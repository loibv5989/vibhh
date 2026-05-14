<?php

if (!defined('ABSPATH')) exit;

class IChing_AI_Settings {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {}

    public function geminiModel() {
        return get_option('iching_ai_model', 'gemini-flash-latest');
    }

    public function groqModel() {
        return get_option('iching_groq_model', 'llama-3.3-70b-versatile');
    }

    public function mistralModel() {
        return get_option('iching_mistral_model', 'mistral-small-latest');
    }

    public function geminiKeysArray() {
        $keys = get_option('iching_gemini_key', '');
        $keys = explode("\n", $keys);
        $keys = array_map(function($k){
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }

    public function groqKeysArray() {
        $keys = get_option('iching_groq_key', '');
        $keys = explode("\n", $keys);
        $keys = array_map(function($k){
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }

    public function mistralKeysArray() {
        $keys = get_option('iching_mistral_key', '');
        $keys = explode("\n", $keys);
        $keys = array_map(function($k){
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }
}

IChing_AI_Settings::get_instance();
