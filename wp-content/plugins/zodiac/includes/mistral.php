<?php

if (!defined('ABSPATH')) exit;

class Zodiac_Mistral {

    private static ?self $instance = null;
    private ?Zodiac_AI_Settings $helpers = null;
    private const BASE_URL = "https://api.mistral.ai/v1/conversations";

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {}

    private function helpers(): Zodiac_AI_Settings {
        if ($this->helpers === null) $this->helpers = Zodiac_AI_Settings::get_instance();
        return $this->helpers;
    }

    private function sendAdminNotification(string $subject, string $message): void {
        $admin_email = get_option('admin_email');
        if (!$admin_email) return;
        wp_mail($admin_email, '[Zodiac] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
    }

    private function parseContent(array $response): ?string {
        foreach ($response['outputs'] ?? [] as $output) {
            if (($output['type'] ?? '') === 'message.output' && ($output['role'] ?? '') === 'assistant' && !empty($output['content'])) {
                return $output['content'];
            }
        }
        return null;
    }

    public function ftn_mistral_generate(string $prompt): string {
        $keys = $this->helpers()->mistralKeysArray();
        $body = [
            'model'           => $this->helpers()->mistralModel(),
            'inputs'          => [['role' => 'user', 'content' => $prompt]],
            'tools'           => [],
            'completion_args' => ['temperature' => 0.2, 'max_tokens' => 4096, 'top_p' => 1],
            'instructions'    => '',
            'store'           => false,
        ];

        while (!empty($keys)) {
            $key = $keys[array_rand($keys)];
            $ch  = curl_init(self::BASE_URL);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($body),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $key],
                CURLOPT_TIMEOUT        => 50,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $raw      = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);

            if ($curlErr) return "[Error] cURL: $curlErr";
            $response = json_decode($raw, true);

            if ($httpCode === 429) {
                unset($keys[array_search($key, $keys)]);
                $this->sendAdminNotification('Mistral Key Limited', "Key: $key\nTime: " . current_time('Y-m-d H:i:s'));
                continue;
            }
            if ($httpCode === 503) { unset($keys[array_search($key, $keys)]); continue; }
            if (isset($response['error'])) {
                return "[Error] Key: $key - Code {$response['error']['code']}: {$response['error']['message']}";
            }
            $content = $this->parseContent($response);
            if ($content !== null) return $content;
            return "[Error] Key: $key - Unexpected response.";
        }
        return '[Error] All Mistral keys are limited (429).';
    }
}

Zodiac_Mistral::get_instance();
