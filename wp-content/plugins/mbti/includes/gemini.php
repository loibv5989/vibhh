<?php
if (!defined('ABSPATH')) exit;

class MBTI_Gemini {

    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';

    private static ?self $instance = null;
    private ?MBTI_Settings $settings = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {}

    private function settings(): MBTI_Settings {
        if ($this->settings === null) {
            $this->settings = MBTI_Settings::get_instance();
        }
        return $this->settings;
    }

    private function buildPayload(string $prompt): array {
        return [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'thinkingConfig' => ['thinkingBudget' => -1],
            ],
        ];
    }

    public function ftn_gemini_generate(string $prompt): string {
        $settings = $this->settings();
        $keys     = $settings->geminiKeysArray();
        $model    = $settings->geminiModel();
        $model    = apply_filters('mbti_gemini_model', $model);

        if (empty($keys)) {
            return '[Error] Chưa cấu hình Gemini API key.';
        }

        $result = null;

        while (!empty($keys)) {
            $key = $keys[array_rand($keys)];
            $url = self::BASE_URL . $model . ':generateContent?key=' . $key;

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($this->buildPayload($prompt)),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 50,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);

            $raw      = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            if ($curlErr) {
                return '[Error] cURL: ' . $curlErr;
            }

            $response = json_decode($raw, true);
            $errCode  = $response['error']['code'] ?? null;

            // Rate limit hoặc overload — thử key khác
            if ($httpCode === 429 || $errCode === 429 || $httpCode === 503 || $errCode === 503) {
                $this->notifyAdmin(
                    'Gemini API Key Limited',
                    "Key: {$key}\nHTTP: {$httpCode}\nTime: " . current_time('Y-m-d H:i:s')
                );
                $keys = array_values(array_diff($keys, [$key]));
                continue;
            }

            if ($errCode !== null) {
                return '[Error] Gemini ' . $httpCode . ': ' . ($response['error']['message'] ?? 'Unknown error');
            }

            $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text !== null) {
                return $text;
            }

            $finishReason = $response['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            $result = ($finishReason === 'STOP')
                ? 'no information available'
                : '[Error] Gemini empty content, finishReason: ' . $finishReason;
            break;
        }

        return $result ?? '[Error] All Gemini keys are rate-limited.';
    }

    private function notifyAdmin(string $subject, string $message): void {
        $email = get_option('admin_email');
        if ($email) {
            wp_mail($email, '[MBTI] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
        }
    }
}

MBTI_Gemini::get_instance();
