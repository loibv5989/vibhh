<?php

if (!defined('ABSPATH')) exit;

class BbOracle_Gemini {

    public $helpers = null;

    private const BASE_URL = "https://generativelanguage.googleapis.com/v1beta/models/";

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {}

    private function sendAdminNotification($subject, $message) {
        $admin_email = get_option('admin_email');
        if (!$admin_email) return;
        wp_mail($admin_email, '[BB-Oracle] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
    }

    private function helpers() {
        if ($this->helpers === null) {
            $this->helpers = BbOracle_AI_Settings::get_instance();
        }
        return $this->helpers;
    }

    public function geminiConfig($prompt) {
        return [
            "contents" => [
                [
                    "role"  => "user",
                    "parts" => [["text" => $prompt]],
                ]
            ],
            "generationConfig" => [
                "thinkingConfig" => ["thinkingBudget" => -1],
            ],
        ];
    }

    public function ftn_gemini_generate($prompt) {
        $helpers      = $this->helpers();
        $gemini_keys  = $helpers->geminiKeysArray();
        $gemini_model = $helpers->geminiModel();
        $gemini_model = apply_filters('bb_oracle_gemini_model', $gemini_model);
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
            $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError    = curl_error($ch);

            if ($curlError) { $result = "[Error] cURL: $curlError"; break; }

            $response = json_decode($response_raw, true);

            if ($httpCode == 429 || (isset($response['error']['code']) && $response['error']['code'] == 429)) {
                $result = "[Error] Key limited (429): $gemini_key";
                unset($gemini_keys[array_search($gemini_key, $gemini_keys)]);
                $this->sendAdminNotification("Gemini API Key Limited", "API Key: $gemini_key\nError: 429\nTime: " . current_time("Y-m-d H:i:s"));
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
                $result = ($finishReason === 'STOP') ? "no information available" : "[Error] Key: $gemini_key - Empty content, finishReason: $finishReason";
                break;
            }

            $result = "[Error] Key: $gemini_key - Unexpected response structure.";
            break;
        }

        if ($result === null) $result = "[Error] All keys are limited (429).";
        return $result;
    }
}

BbOracle_Gemini::get_instance();
