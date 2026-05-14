<?php
if (!defined('ABSPATH')) {
    exit;
}

final class AM2FA_Plugin {
    private static $instance = null;
    public $settings;
    public $login;
    public $mailer;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->settings = new AM2FA_Settings();
        $this->mailer   = new AM2FA_Mailer();
        $this->login    = new AM2FA_Login($this->settings, $this->mailer);
    }

    public static function activate() {
        $defaults = self::default_settings();
        $current  = get_option('am2fa_settings', array());
        if (!is_array($current)) {
            $current = array();
        }

        update_option('am2fa_settings', wp_parse_args($current, $defaults), false);
    }

    public static function default_settings() {
        return array(
            'enabled'          => 1,
            'ttl_minutes'      => 10,
            'code_length'      => 6,
            'subject_template'  => __('[{site_name}] Login Authentication Code', 'lbv-2fa'),
            'body_template'     => __("Hello {display_name},

Your login code for {site_name} is: {code}

This code expires in {ttl_minutes} minutes.

If you did not try to log in, ignore this message.
", 'lbv-2fa'),
            'from_name'        => get_bloginfo('name'),
            'from_email'       => get_option('admin_email'),
        );
    }

    public static function get_settings() {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $settings = get_option('am2fa_settings', array());
        if (!is_array($settings)) {
            $settings = array();
        }

        $cache = wp_parse_args($settings, self::default_settings());
        return $cache;
    }
}