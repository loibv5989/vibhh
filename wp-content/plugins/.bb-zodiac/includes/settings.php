<?php

if (!defined('ABSPATH')) exit;

class BbZodiac_AI_Settings {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public function __construct() {}

    public function geminiModel() {
        return get_option('bb_zodiac_ai_model', 'gemini-flash-latest');
    }

    public function groqModel() {
        return get_option('bb_zodiac_groq_model', 'llama-3.3-70b-versatile');
    }

    public function mistralModel() {
        return get_option('bb_zodiac_mistral_model', 'mistral-small-latest');
    }

    public function geminiKeysArray() {
        $keys = get_option('bb_zodiac_gemini_key', '');
        $keys = array_filter(array_map(fn($k) => trim($k, " \t\n\r\0\x0B,"), explode("\n", $keys)));
        return array_values($keys);
    }

    public function groqKeysArray() {
        $keys = get_option('bb_zodiac_groq_key', '');
        $keys = array_filter(array_map(fn($k) => trim($k, " \t\n\r\0\x0B,"), explode("\n", $keys)));
        return array_values($keys);
    }

    public function mistralKeysArray() {
        $keys = get_option('bb_zodiac_mistral_key', '');
        $keys = array_filter(array_map(fn($k) => trim($k, " \t\n\r\0\x0B,"), explode("\n", $keys)));
        return array_values($keys);
    }
}

BbZodiac_AI_Settings::get_instance();
