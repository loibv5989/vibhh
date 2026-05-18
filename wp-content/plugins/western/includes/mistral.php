<?php

if (!defined('ABSPATH')) exit;

class WESTERN_Mistral {

    public $helpers = null;

    private const BASE_URL = "https://api.mistral.ai/v1/chat/completions";

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {}

    private function sendAdminNotification($subject, $message) {
        $admin_email = get_option('admin_email');
        if (!$admin_email) return;

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $subject = '[Fortune Tools] ' . $subject;

        wp_mail($admin_email, $subject, $message, $headers);
    }

    private function helpers() {
        if ($this->helpers === null) {
            $this->helpers = WESTERN_Settings::get_instance();
        }
        return $this->helpers;
    }

    public function ftn_mistral_generate($prompt) {

        $helpers       = $this->helpers();
        $mistral_keys  = $helpers->mistralKeysArray();
        $mistral_model = $helpers->mistralModel();
        $result        = null;

        $body = [
            "model"       => $mistral_model,
            "messages"    => [
                [
                    "role"    => "user",
                    "content" => $prompt,
                ]
            ],
            "temperature" => 0.5,
            "max_tokens"  => 2048,
            "top_p"       => 0.5,
            "stream"      => false,
        ];

        while (!empty($mistral_keys)) {

            $mistral_key = $mistral_keys[array_rand($mistral_keys)];

            $ch = curl_init(self::BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $mistral_key,
            ]);

            $response_raw = curl_exec($ch);
            $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $response = json_decode($response_raw, true);

            // 429 - Rate limit
            if ($httpCode === 429) {
                $result = "[Error] Key limited (429): $mistral_key";
                unset($mistral_keys[array_search($mistral_key, $mistral_keys)]);
                $this->sendAdminNotification(
                    "Mistral API Key Limited",
                    "API Key: $mistral_key\nError: 429 - Rate limit exceeded\nTime: " . current_time("Y-m-d H:i:s")
                );
                continue;
            }

            // 503 - Service Unavailable
            if ($httpCode === 503) {
                $result = "[Error] Key: $mistral_key - Code 503: Service unavailable.";
                unset($mistral_keys[array_search($mistral_key, $mistral_keys)]);
                continue;
            }

            // Other errors
            if (isset($response['error'])) {
                $code    = $response['error']['code']    ?? $httpCode;
                $message = $response['error']['message'] ?? 'Unknown error';
                $result  = "[Error] Key: $mistral_key - Code $code: $message";
                break;
            }

            // Success — response format giống OpenAI/Groq
            if (isset($response['choices'][0]['message']['content'])) {
                $result = $response['choices'][0]['message']['content'];
                break;
            }

            // Unexpected structure
            $finish_reason = $response['choices'][0]['finish_reason'] ?? 'UNKNOWN';
            if ($finish_reason === 'stop') {
                $result = "no information available";
            } else {
                $error_info = [
                    'finish_reason' => $finish_reason,
                    'hasChoices'    => isset($response['choices']),
                    'hasMessage'    => isset($response['choices'][0]['message']),
                    'usage'         => $response['usage'] ?? [],
                ];
                $result = "[Error] Key: $mistral_key - Response structure: " . json_encode($error_info);
            }
            break;
        }

        if ($result === null) {
            $result = "[Error] All Mistral keys are limited (429).";
        }

        return $result;
    }
}

WESTERN_Mistral::get_instance();
