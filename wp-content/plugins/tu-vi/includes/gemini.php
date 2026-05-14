<?php

if (!defined('ABSPATH')) exit;

class Tuvi_Gemini {

    public $settings = null;

    private const BASE_URL = "https://generativelanguage.googleapis.com/v1beta/models/";

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
        $subject = '[Tu vi] ' . $subject;
        
        wp_mail($admin_email, $subject, $message, $headers);
    }

    private function settings() {

        if ($this->settings === null) {
            $this->settings = TuVi_Settings::get_instance();
        }

        return $this->settings;
    }

    public function geminiConfig($prompt) {
        return [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        [
                            "text" => $prompt
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "thinkingConfig" => [
                    "thinkingBudget" => -1,
                ],
            ],
        ];
    }

    public function ftn_gemini_generate($prompt) {

        $settings = $this->settings();
        $gemini_keys  = $settings->geminiKeysArray();
        $gemini_model = $settings->geminiModel();
        $gemini_model = apply_filters('tuvi_gemini_model', $gemini_model);
        $result = null;

        while (!empty($gemini_keys)) {

            $gemini_key = $gemini_keys[array_rand($gemini_keys)];
            $url = self::BASE_URL . $gemini_model . ':generateContent?key=' . $gemini_key;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->geminiConfig($prompt)));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $response_raw = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            if ($curlError) {
                $result = "[Error] cURL: $curlError";
                break;
            }

            $response = json_decode($response_raw, true);

            if ($httpCode == 429 || (isset($response['error']['code']) && $response['error']['code'] == 429)) {
                $result = "[Error] Key limited (429): $gemini_key";
                unset($gemini_keys[array_search($gemini_key, $gemini_keys)]);
                $this->sendAdminNotification("Gemini API Key Limited", "API Key: $gemini_key\nError: 429 - Rate limit exceeded\nTime: " . current_time("Y-m-d H:i:s"));
                continue;
            }

            if ($httpCode == 503 || (isset($response['error']['code']) && $response['error']['code'] == 503)) {
                $result = "[Error] Key: $gemini_key - Code 503: Model overloaded.";
                unset($gemini_keys[array_search($gemini_key, $gemini_keys)]);
                continue;
            }

            if (isset($response['error'])) {
                $result = "[Error] Key: $gemini_key - Code {$response['error']['code']}: {$response['error']['message']}";
                break;
            }

            if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $result = $response['candidates'][0]['content']['parts'][0]['text'];
                break;
            }

            if (isset($response['candidates'][0]['content']) && empty($response['candidates'][0]['content']['parts'])) {
                $finishReason = $response['candidates'][0]['finishReason'] ?? 'UNKNOWN';
                if ($finishReason === 'STOP') {
                    $result = "no information available";
                } else {
                    $result = "[Error] Key: $gemini_key - Empty content, finishReason: $finishReason";
                }
                break;
            }

            $error_info = [
                'finishReason' => $response['candidates'][0]['finishReason'] ?? 'N/A',
                'hasContent' => isset($response['candidates'][0]['content']),
                'hasParts' => isset($response['candidates'][0]['content']['parts']),
                'usageMetadata' => $response['usageMetadata'] ?? []
            ];

            $result = "[Error] Key: $gemini_key - Response structure: " . json_encode($error_info);
            break;
        }

        if ($result === null) {
            $result = "[Error] All keys are limited (429).";
        }

        return $result;
    }
}

Tuvi_Gemini::get_instance();
