<?php

if (!defined('ABSPATH')) exit;

class BbZodiac_Gemini {

    private $helpers = null;
    private const BASE_URL = "https://generativelanguage.googleapis.com/v1beta/models/";
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {}

    private function helpers() {
        if ($this->helpers === null) $this->helpers = BbZodiac_AI_Settings::get_instance();
        return $this->helpers;
    }

    private function sendAdminNotification($subject, $message) {
        $admin_email = get_option('admin_email');
        if (!$admin_email) return;
        wp_mail($admin_email, '[BB-Zodiac] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
    }

    public function ftn_gemini_generate($prompt) {
        $helpers      = $this->helpers();
        $gemini_keys  = $helpers->geminiKeysArray();
        $gemini_model = apply_filters('bb_zodiac_gemini_model', $helpers->geminiModel());
        $result = null;

        while (!empty($gemini_keys)) {
            $key = $gemini_keys[array_rand($gemini_keys)];
            $url = self::BASE_URL . $gemini_model . ':generateContent?key=' . $key;
            $ch  = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode([
                    "contents"         => [["role" => "user", "parts" => [["text" => $prompt]]]],
                    "generationConfig" => ["thinkingConfig" => ["thinkingBudget" => -1]],
                ]),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 50,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $raw      = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);

            if ($curlErr) { $result = "[Error] cURL: $curlErr"; break; }
            $response = json_decode($raw, true);

            if ($httpCode == 429 || ($response['error']['code'] ?? 0) == 429) {
                unset($gemini_keys[array_search($key, $gemini_keys)]);
                $this->sendAdminNotification("Gemini Key Limited", "Key: $key\nTime: " . current_time("Y-m-d H:i:s"));
                $result = "[Error] Key limited (429): $key";
                continue;
            }
            if ($httpCode == 503 || ($response['error']['code'] ?? 0) == 503) {
                unset($gemini_keys[array_search($key, $gemini_keys)]);
                $result = "[Error] Key: $key - 503 overloaded."; continue;
            }
            if (isset($response['error'])) {
                $result = "[Error] Key: $key - Code {$response['error']['code']}: {$response['error']['message']}"; break;
            }
            if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $result = $response['candidates'][0]['content']['parts'][0]['text']; break;
            }
            $finishReason = $response['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            $result = ($finishReason === 'STOP') ? "no information available" : "[Error] Key: $key - finishReason: $finishReason";
            break;
        }
        if ($result === null) $result = "[Error] All keys are limited (429).";
        return $result;
    }
}

BbZodiac_Gemini::get_instance();
