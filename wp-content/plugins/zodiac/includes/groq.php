<?php

if (!defined('ABSPATH')) exit;

class Zodiac_Groq {

    private $helpers = null;
    private const BASE_URL = "https://api.groq.com/openai/v1/chat/completions";
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {}

    private function helpers() {
        if ($this->helpers === null) $this->helpers = Zodiac_AI_Settings::get_instance();
        return $this->helpers;
    }

    private function sendAdminNotification($subject, $message) {
        $admin_email = get_option('admin_email');
        if (!$admin_email) return;
        wp_mail($admin_email, '[Zodiac] ' . $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
    }

    public function ftn_groq_generate($prompt) {
        $helpers   = $this->helpers();
        $groq_keys = $helpers->groqKeysArray();
        $result    = null;

        while (!empty($groq_keys)) {
            $key = $groq_keys[array_rand($groq_keys)];
            $ch  = curl_init(self::BASE_URL);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode([
                    "model"       => $helpers->groqModel(),
                    "messages"    => [["role" => "user", "content" => $prompt]],
                    "temperature" => 1,
                    "max_tokens"  => 4096,
                    "top_p"       => 1,
                    "stream"      => false,
                    "stop"        => null,
                ]),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $key],
                CURLOPT_TIMEOUT        => 50,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $raw      = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);

            if ($curlErr) { $result = "[Error] cURL: $curlErr"; break; }
            $response = json_decode($raw, true);

            if ($httpCode === 429 || ($response['error']['code'] ?? '') === 'rate_limit_exceeded') {
                unset($groq_keys[array_search($key, $groq_keys)]);
                $this->sendAdminNotification("Groq Key Limited", "Key: $key\nTime: " . current_time("Y-m-d H:i:s"));
                $result = "[Error] Key limited (429): $key"; continue;
            }
            if ($httpCode === 498 || $httpCode === 503) {
                unset($groq_keys[array_search($key, $groq_keys)]); continue;
            }
            if (isset($response['error'])) {
                $result = "[Error] Key: $key - Code {$response['error']['code']}: {$response['error']['message']}"; break;
            }
            if (isset($response['choices'][0]['message']['content'])) {
                $result = $response['choices'][0]['message']['content']; break;
            }
            $finish = $response['choices'][0]['finish_reason'] ?? 'UNKNOWN';
            $result = ($finish === 'stop') ? "no information available" : "[Error] Key: $key - Unexpected response.";
            break;
        }
        if ($result === null) $result = "[Error] All Groq keys are limited (429).";
        return $result;
    }
}

Zodiac_Groq::get_instance();
