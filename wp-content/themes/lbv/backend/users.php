<?php

class LBV_Users {

    private static $instance = null;

    public function __construct() {
        add_action('init', [$this, 'disable_user_registration_rest_api']);
        add_action('init', [$this, 'disable_new_user_notifications']);
        add_action('wp_login', [$this, 'send_admin_login_email'], 10, 2);
        add_filter('wp_new_user_notification_email', [$this, 'custom_wp_new_user_notification_email'], 10, 3);
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function disable_user_registration_rest_api() {
        if (is_admin() || current_user_can('manage_options')) {
            return;
        }

        add_filter('rest_endpoints', function($endpoints) {
            unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
            return $endpoints;
        });
    }

    public function disable_new_user_notifications() {
        remove_action('register_new_user', 'wp_send_new_user_notifications');
        remove_action('edit_user_created_user', 'wp_send_new_user_notifications');
        add_action('register_new_user', [$this, 'send_new_email_notifications']);
        add_action('edit_user_created_user', [$this, 'send_new_email_notifications'], 10, 2);
    }

    public function send_new_email_notifications($user_id, $notify = 'user') {
        if (empty($notify) || $notify == 'admin') {
            return;
        } elseif ($notify == 'both') {
            $notify = 'user';
        }

        wp_send_new_user_notifications($user_id, $notify);
    }

    public function custom_wp_new_user_notification_email($wp_new_user_notification_email, $user, $blogname) {
        $subject = sprintf('[%s] - Registration Info', $user->user_login);
        $headers = "Content-Type: text/plain\r\n";

        $wp_new_user_notification_email['subject'] = $subject;
        $wp_new_user_notification_email['headers'] = $headers;

        return $wp_new_user_notification_email;
    }

    public function send_admin_login_email($user_login, $user) {
        if (user_can($user, 'manage_options')) {
            $admin_email = get_option('admin_email');
            $user_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';
            $user_browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Browser';

            $user_roles = $user->roles;
            $user_role = array_shift($user_roles);

            $subject = 'Admin Login Alert: ' . $user_login . ' [' . $user_role . ']';
            $message = '<p><strong>' . $user_login . '</strong> (' . $user_role . ') just logged in at <strong>' . date('Y-m-d H:i:s') . '</strong>.</p>';
            $message .= '<p><strong>IP Address:</strong> ' . $user_ip . '<br>';
            $message .= '<strong>Browser:</strong> ' . $user_browser . '</p>';

            $headers = ['Content-Type: text/html; charset=UTF-8'];

            wp_mail($admin_email, $subject, $message, $headers);
        }
    }
}

LBV_Users::get_instance();
