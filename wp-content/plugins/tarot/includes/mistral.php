<?php

if (!defined('ABSPATH')) exit;

class TR_Mistral {

    private static ?self $instance = null;
    private ?TR_Settings $helpers = null;

    private const BASE_URL = "https://api.mistral.ai/v1/conversations";

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {}

    private function helpers(): TR_Settings {
        if ($this->helpers === null) {
            $this->helpers = TR_Settings::get_instance();
        }
        return $this->helpers;
    }

    private function buildBody(string $prompt): array {
        return [
            'model'           => $this->helpers()->mistralModel(),
            'inputs'          => [
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ]
            ],
            'tools'           => [],
            'completion_args' => [
                'temperature' => 0.2,
                'max_tokens'  => 4096,
                'top_p'       => 1,
            ],
            'instructions'    => '',
            'store'           => false,
        ];
    }

    private function buildHeaders(string $api_key): array {
        return [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ];
    }

    private function sendRequest(string $api_key, array $body): array {
        $ch = curl_init(self::BASE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeaders($api_key));
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $raw       = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        return [
            'http_code'  => $httpCode,
            'response'   => json_decode($raw, true),
            'curl_error' => $curlError,
        ];
    }

    private function parseContent(array $response): ?string {
        if (empty($response['outputs']) || !is_array($response['outputs'])) {
            return null;
        }
        foreach ($response['outputs'] as $output) {
            if (($output['type'] ?? '') === 'message.output' &&
                ($output['role'] ?? '') === 'assistant' &&
                !empty($output['content'])) {
                return $output['content'];
            }
        }
        return null;
    }

    private function sendAdminNotification(string $subject, string $message): void {
        $admin_email = get_option('admin_email');
        if (!$admin_email) return;
        wp_mail(
            $admin_email,
            '[Tarot] ' . $subject,
            $message,
            ['Content-Type: text/plain; charset=UTF-8']
        );
    }

    public function ftn_mistral_generate(string $prompt, ?array &$info = null): string {
        $keys = $this->helpers()->mistralKeysArray();
        $model = $this->helpers()->mistralModel();
        $body = $this->buildBody($prompt);
        $successful_key = null;
        
        if ($info !== null) {
            $info['model'] = $model;
            $info['failed_keys'] = [];
        }

        while (!empty($keys)) {
            $key    = $keys[array_rand($keys)];
            $result = $this->sendRequest($key, $body);

            $httpCode  = $result['http_code'];
            $response  = $result['response'];
            $curlError = $result['curl_error'] ?? '';

            if ($curlError) {
                return "[Error] cURL: $curlError";
            }

            if ($httpCode === 429) {
                if ($info !== null) {
                    $info['failed_keys'][] = $this->maskKey($key) . ' (429)';
                }
                unset($keys[array_search($key, $keys)]);
                $this->sendAdminNotification(
                    'Mistral API Key Limited',
                    "API Key: $key\nError: 429 - Rate limit exceeded\nTime: " . current_time('Y-m-d H:i:s')
                );
                continue;
            }

            if ($httpCode === 503) {
                if ($info !== null) {
                    $info['failed_keys'][] = $this->maskKey($key) . ' (503)';
                }
                unset($keys[array_search($key, $keys)]);
                continue;
            }

            if (isset($response['error'])) {
                $code    = $response['error']['code']    ?? $httpCode;
                $message = $response['error']['message'] ?? 'Unknown error';
                return "[Error] Key: $key - Code $code: $message";
            }

            $content = $this->parseContent($response);
            if ($content !== null) {
                $successful_key = $key;
                if ($successful_key && $info !== null) {
                    $info['key'] = $this->maskKey($successful_key);
                }
                return $content;
            }

            return "[Error] Key: $key - Unexpected response: " . json_encode([
                    'object'  => $response['object']  ?? null,
                    'outputs' => $response['outputs'] ?? [],
                    'usage'   => $response['usage']   ?? [],
                ]);
        }

        return '[Error] All Mistral keys are limited (429).';
    }

    private function maskKey(string $key): string {
        if (strlen($key) <= 8) return '***';
        return substr($key, 0, 4) . '...' . substr($key, -4);
    }
}

TR_Mistral::get_instance();