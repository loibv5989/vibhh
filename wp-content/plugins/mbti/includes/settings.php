<?php

if (!defined('ABSPATH')) exit;


class MBTI_Settings {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {}

    public function geminiModel() {
        return get_option('mbti_ai_model', 'gemini-flash-latest');
    }

    public function groqModel() {
        return get_option('mbti_groq_model', 'llama-3.3-70b-versatile');
    }

    public function mistralModel() {
        return get_option('mbti_mistral_model', 'mistral-medium-latest');
    }

    public function geminiKeysArray() {
        $keys = get_option('mbti_gemini_key', '');
        $keys = explode("\n", $keys);
        $keys = array_map(function($k) {
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }

    public function groqKeysArray() {
        $keys = get_option('mbti_groq_key', '');
        $keys = explode("\n", $keys);
        $keys = array_map(function($k) {
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }

    public function mistralKeysArray() {
        $keys = get_option('mbti_mistral_key', '');
        $keys = explode("\n", $keys);
        $keys = array_map(function($k) {
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }

    public function allowAI(): bool {
        return get_option('mbti_allow_ai', '1') === '1';
    }

    public function analysisOrder(): array {
        $order = get_option('mbti_analysis_order', 'gemini,mistral,groq');
        return array_map('trim', explode(',', $order));
    }
}

MBTI_Settings::get_instance();
