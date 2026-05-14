<?php
if (!defined('ABSPATH')) exit;

class MBTI_Groq {

    private const BASE_URL = 'https://api.groq.com/openai/v1/chat/completions';

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
            'model'       => $this->settings()->groqModel(),
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 1,
            'max_tokens'  => 4096,
            'top_p'       => 1,
            'stream'      => false,
            'stop'        => null,
        ];
    }

    public function ftn_groq_generate(string $prompt): string {
        $keys = $this->settings()->groqKeysArray();

        if (empty($keys)) {
            return '[Error] Chưa cấu hình Groq API key.';
        }

        $result = null;

        while (!empty($keys)) {
            $key = $keys[array_rand($keys)];

            $ch = curl_init(self::BASE_URL);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($this->buildPayload($prompt)),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $key,
                ],
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

            // Rate limit / capacity / overload — thử key khác
            if (in_array($httpCode, [429, 498, 503], true)
                || ($response['error']['code'] ?? '') === 'rate_limit_exceeded') {
                $this->notifyAdmin('Groq API Key Limited', "Key: {$key}\nHTTP: {$httpCode}\nTime: " . current_time('Y-m-d H:i:s'));
                $keys = array_values(array_diff($keys, [$key]));
                continue;
            }

            if (isset($response['error'])) {
                $code = $response['error']['code']    ?? $httpCode;
                $msg  = $response['error']['message'] ?? 'Unknown error';
                return "[Error] Groq {$code}: {$msg}";
            }

            $text = $response['choices'][0]['message']['content'] ?? null;
            if ($text !== null) {
                return $text;
            }

            $finishReason = $response['choices'][0]['finish_reason'] ?? 'UNKNOWN';
            $result = ($finishReason === 'stop')
                ? 'no information available'
                : '[Error] Groq unexpected response, finishReason: ' . $finishReason;
            break;
        }

        return $result ?? '[Error] All Groq keys are rate-limited.';
    }

    private function notifyAdmin(string $subject, string $message): void {
        $email = get_option('admin_email');
        if ($email) {
            wp_mail($email, '[MBTI] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
        }
    }
}

MBTI_Groq::get_instance();
