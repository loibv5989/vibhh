<?php

if (!defined('ABSPATH')) exit;

class Battu_Settings {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {}

    public function allowAI() {
        return get_option('battu_allow_ai', '0');
    }

    public function provider() {
        return get_option('battu_ai_provider', 'gemini');
    }

    public function geminiModel() {
        return get_option('battu_gemini_model', 'gemini-flash-latest');
    }

    public function groqModel() {
        return get_option('battu_groq_model', 'llama-3.3-70b-versatile');
    }

    public function mistralModel() {
        return get_option('battu_mistral_model', 'mistral-small-latest');
    }

    public function analysisOrder() {
        return get_option('battu_analysis_order', 'gemini,mistral,groq');
    }

    public function gatekeeperOrder() {
        return get_option('battu_gatekeeper_order', 'groq,mistral,gemini');
    }

    public function geminiKeysArray() {
        return $this->keysArrayFromOption('battu_gemini_key');
    }

    public function groqKeysArray() {
        return $this->keysArrayFromOption('battu_groq_key');
    }

    public function mistralKeysArray() {
        return $this->keysArrayFromOption('battu_mistral_key');
    }

    private function keysArrayFromOption($option_name) {
        $keys = get_option($option_name, '');
        $keys = explode("\n", (string) $keys);
        $keys = array_map(function ($k) {
            return trim($k, " \t\n\r\0\x0B,");
        }, $keys);
        $keys = array_filter($keys);
        return array_values($keys);
    }

    public function getTimezone(): float {
        $gmt_offset = get_option('gmt_offset');
        if ($gmt_offset !== '') {
            return (float)$gmt_offset;
        }

        return 7.0;
    }
}

Battu_Settings::get_instance();
