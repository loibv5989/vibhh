<?php
if (!defined('ABSPATH')) exit;

class MBTI_Mistral {

    private const BASE_URL = 'https://api.mistral.ai/v1/conversations';

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

    private function buildBody(string $prompt): array {
        return [
            'model'           => $this->settings()->mistralModel(),
            'inputs'          => [['role' => 'user', 'content' => $prompt]],
            'tools'           => [],
            'completion_args' => ['temperature' => 0.2, 'max_tokens' => 4096, 'top_p' => 1],
            'instructions'    => '',
            'store'           => false,
        ];
    }

    private function sendRequest(string $key, array $body): array {
        $ch = curl_init(self::BASE_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
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

        return [
            'http_code'  => $httpCode,
            'response'   => json_decode($raw, true),
            'curl_error' => $curlErr,
        ];
    }

    private function parseContent(array $response): ?string {
        foreach ($response['outputs'] ?? [] as $output) {
            if (($output['type'] ?? '') === 'message.output'
                && ($output['role'] ?? '') === 'assistant'
                && !empty($output['content'])) {
                return $output['content'];
            }
        }
        return null;
    }

    public function ftn_mistral_generate(string $prompt): string {
        $keys = $this->settings()->mistralKeysArray();

        if (empty($keys)) {
            return '[Error] Chưa cấu hình Mistral API key.';
        }

        $body = $this->buildBody($prompt);

        while (!empty($keys)) {
            $key    = $keys[array_rand($keys)];
            $result = $this->sendRequest($key, $body);

            $httpCode = $result['http_code'];
            $response = $result['response'];
            $curlErr  = $result['curl_error'];

            if ($curlErr) {
                return '[Error] cURL: ' . $curlErr;
            }

            if (in_array($httpCode, [429, 503], true)) {
                $this->notifyAdmin('Mistral API Key Limited', "Key: {$key}\nHTTP: {$httpCode}\nTime: " . current_time('Y-m-d H:i:s'));
                $keys = array_values(array_diff($keys, [$key]));
                continue;
            }

            if (isset($response['error'])) {
                $code = $response['error']['code']    ?? $httpCode;
                $msg  = $response['error']['message'] ?? 'Unknown error';
                return "[Error] Mistral {$code}: {$msg}";
            }

            $content = $this->parseContent($response);
            if ($content !== null) return $content;

            return '[Error] Mistral unexpected response structure.';
        }

        return '[Error] All Mistral keys are rate-limited.';
    }

    private function notifyAdmin(string $subject, string $message): void {
        $email = get_option('admin_email');
        if ($email) {
            wp_mail($email, '[MBTI] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
        }
    }
}

MBTI_Mistral::get_instance();
