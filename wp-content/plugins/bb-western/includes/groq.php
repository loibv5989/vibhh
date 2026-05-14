<?php

if (!defined('ABSPATH')) exit;

class BBW_Groq {

    public $helpers = null;

    private const BASE_URL = "https://api.groq.com/openai/v1/chat/completions";

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
            $this->helpers = BBW_Settings::get_instance();
        }
        return $this->helpers;
    }

    public function groqConfig($prompt) {

        return [
            "model"               => $this->helpers()->groqModel(),
            "messages"            => [
                [
                    "role"    => "user",
                    "content" => $prompt,
                ]
            ],
            "temperature"         => 0.5,
            "max_tokens"          => 4096,
            "top_p"               => 0.5,
            "stream"              => false,
            "stop"                => null,
        ];
    }

    public function ftn_groq_generate($prompt) {

        $helpers   = $this->helpers();
        $groq_keys = $helpers->groqKeysArray();
        $result    = null;

        while (!empty($groq_keys)) {

            $groq_key = $groq_keys[array_rand($groq_keys)];

            $ch = curl_init(self::BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->groqConfig($prompt)));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $groq_key,
            ]);

            $response_raw = curl_exec($ch);
            $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $response = json_decode($response_raw, true);

            if ($httpCode === 429 || (isset($response['error']['code']) && $response['error']['code'] === 'rate_limit_exceeded')) {
                $result = "[Error] Key limited (429): $groq_key";
                unset($groq_keys[array_search($groq_key, $groq_keys)]);
                $this->sendAdminNotification(
                    "Groq API Key Limited",
                    "API Key: $groq_key\nError: 429 - Rate limit exceeded\nTime: " . current_time("Y-m-d H:i:s")
                );
                continue;
            }

            if ($httpCode === 498) {
                $result = "[Error] Key: $groq_key - Code 498: Flex Tier Capacity Exceeded.";
                unset($groq_keys[array_search($groq_key, $groq_keys)]);
                continue;
            }

            if ($httpCode === 503 || (isset($response['error']['code']) && $response['error']['code'] === 503)) {
                $result = "[Error] Key: $groq_key - Code 503: Service unavailable.";
                unset($groq_keys[array_search($groq_key, $groq_keys)]);
                continue;
            }

            if (isset($response['error'])) {
                $code    = $response['error']['code']    ?? $httpCode;
                $message = $response['error']['message'] ?? 'Unknown error';
                $result  = "[Error] Key: $groq_key - Code $code: $message";
                break;
            }

            if (isset($response['choices'][0]['message']['content'])) {
                $result = $response['choices'][0]['message']['content'];
                break;
            }

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
                $result = "[Error] Key: $groq_key - Response structure: " . json_encode($error_info);
            }
            break;
        }

        if ($result === null) {
            $result = "[Error] All Groq keys are limited (429).";
        }

        return $result;
    }
}

BBW_Groq::get_instance();