<?php

defined('ABSPATH') || exit;

class LBV_Google_OAuth {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('login_init', array($this, 'handle_google_login'));
    }

    private function theme_setting() {
        return LBV_Theme_Settings::get_instance();
    }

    private function client_id() {
        return $this->theme_setting()->lbv_google_client_id();
    }

    private function redirect_uri() {
        return site_url('/wp-login.php?loginSocial=google');
    }

    private function client_secret() {
        return $this->theme_setting()->lbv_google_client_secret();
    }

    public function handle_google_login() {
        if (!isset($_GET['loginSocial']) || $_GET['loginSocial'] !== 'google') {
            return;
        }

        if (isset($_GET['code']) && isset($_GET['state'])) {
            $this->process_google_callback($_GET['code'], $_GET['state']);
        } else {
            $this->redirect_to_google();
        }
    }

    private function redirect_to_google() {
        $state = wp_generate_password(32, false, false);

        $redirect_to = home_url();
        if (!empty($_GET['redirect_to'])) {
            $redirect_to = esc_url_raw($_GET['redirect_to']);
        } elseif (!empty($_REQUEST['redirect_to'])) {
            $redirect_to = esc_url_raw($_REQUEST['redirect_to']);
        } elseif ($ref = wp_get_referer()) {
            if (strpos($ref, 'wp-login.php') === false && strpos($ref, 'wp-admin') === false) {
                $redirect_to = $ref;
            }
        }

        $state_data = array(
                'created'     => time(),
                'redirect_to' => $redirect_to
        );
        set_transient('lbv_oauth_state_' . $state, $state_data, 600);

        $params = array(
                'client_id'     => $this->client_id(),
                'redirect_uri'  => $this->redirect_uri(),
                'response_type' => 'code',
                'scope'         => 'openid email profile',
                'access_type'   => 'online',
                'prompt'        => 'select_account',
                'state'         => $state
        );

        wp_redirect('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
        exit;
    }

    private function process_google_callback($code, $state) {
        $state_data = get_transient('lbv_oauth_state_' . $state);

        if (!$state_data) {
            error_log('LBV OAuth Error: Invalid or expired state parameter');
            wp_safe_redirect(add_query_arg('login', 'error', wp_login_url()));
            exit;
        }

        delete_transient('lbv_oauth_state_' . $state);

        $token = $this->get_access_token($code);
        if (!$token) {
            wp_safe_redirect(add_query_arg('login', 'error', wp_login_url()));
            exit;
        }

        $user_info = $this->get_user_info($token);
        if (!$user_info) {
            wp_safe_redirect(add_query_arg('login', 'error', wp_login_url()));
            exit;
        }

        $user = $this->authenticate_user($user_info);
        if (is_wp_error($user)) {
            error_log('LBV OAuth Error: ' . $user->get_error_message());
            wp_safe_redirect(add_query_arg('login', 'error', wp_login_url()));
            exit;
        }

        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);

        $redirect_to = isset($state_data['redirect_to']) ? $state_data['redirect_to'] : home_url();
        $redirect_to = apply_filters('login_redirect', $redirect_to, $redirect_to, $user);

        $redirect_to = add_query_arg('login', 'success', $redirect_to);

        wp_safe_redirect($redirect_to);
        exit;
    }

    private function get_access_token($code) {
        $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
                'timeout' => 30,
                'headers' => array(
                        'Content-Type' => 'application/x-www-form-urlencoded'
                ),
                'body' => array(
                        'code'          => $code,
                        'client_id'     => $this->client_id(),
                        'client_secret' => $this->client_secret(),
                        'redirect_uri'  => $this->redirect_uri(),
                        'grant_type'    => 'authorization_code'
                )
        ));

        if (is_wp_error($response)) {
            error_log('LBV OAuth Token Error: ' . $response->get_error_message());
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('LBV OAuth Token HTTP Error: ' . $status_code);
            $body = wp_remote_retrieve_body($response);
            error_log('LBV OAuth Token Response: ' . $body);
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return isset($body['access_token']) ? $body['access_token'] : false;
    }

    private function get_user_info($access_token) {
        $response = wp_remote_get('https://www.googleapis.com/oauth2/v2/userinfo', array(
                'timeout' => 30,
                'headers' => array(
                        'Authorization' => 'Bearer ' . $access_token
                )
        ));

        if (is_wp_error($response)) {
            error_log('LBV OAuth User Info Error: ' . $response->get_error_message());
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('LBV OAuth User Info HTTP Error: ' . $status_code);
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    private function authenticate_user($user_info) {
        $email = sanitize_email($user_info['email']);

        if (!is_email($email)) {
            return new WP_Error('invalid_email', __('Invalid email address format.', 'lbv-user'));
        }

        if (isset($user_info['email_verified']) && !$user_info['email_verified']) {
            return new WP_Error('email_not_verified', __('Email address is not verified by Google.', 'lbv-user'));
        }

        $user = get_user_by('email', $email);
        if ($user) {
            $this->update_user_google_data($user->ID, $user_info);
            return $user;
        }

        if (!get_option('users_can_register')) {
            return new WP_Error('registration_disabled', __('User registration is currently not allowed.', 'lbv-user'));
        }

        return $this->create_new_user($user_info);
    }

    private function update_user_google_data($user_id, $user_info) {
        if (isset($user_info['id']) && !get_user_meta($user_id, 'lbv_google_id', true)) {
            update_user_meta($user_id, 'lbv_google_id', sanitize_text_field($user_info['id']));
        }

        if (isset($user_info['picture'])) {
            update_user_meta($user_id, 'lbv_google_avatar', esc_url_raw($user_info['picture']));
        }
    }

    private function create_new_user($user_info) {
        $email = sanitize_email($user_info['email']);

        $email_parts = explode('@', $email);
        $base_username = sanitize_user($email_parts[0], true);

        if (empty($base_username)) {
            $base_username = 'user';
        }

        if (!validate_username($base_username)) {
            return new WP_Error('invalid_username_format', __('Username format is invalid.', 'lbv-user'));
        }

        $username = $this->generate_unique_username($base_username);

        if (is_wp_error($username)) {
            return $username;
        }

        $user_data = array(
                'user_login'    => $username,
                'user_email'    => $email,
                'display_name'  => isset($user_info['name']) ? sanitize_text_field($user_info['name']) : $username,
                'first_name'    => isset($user_info['given_name']) ? sanitize_text_field($user_info['given_name']) : '',
                'last_name'     => isset($user_info['family_name']) ? sanitize_text_field($user_info['family_name']) : '',
                'user_pass'     => wp_generate_password(20, true, true),
                'role'          => get_option('default_role')
        );

        $user_id = wp_insert_user($user_data);

        if (is_wp_error($user_id)) {
            error_log('LBV OAuth Create User Error: ' . $user_id->get_error_message());
            return $user_id;
        }

        if (isset($user_info['id'])) {
            update_user_meta($user_id, 'lbv_google_id', sanitize_text_field($user_info['id']));
        }
        if (isset($user_info['picture'])) {
            update_user_meta($user_id, 'lbv_google_avatar', esc_url_raw($user_info['picture']));
        }

        do_action('lbv_google_oauth_user_created', $user_id, $user_info);

        return get_user_by('id', $user_id);
    }

    private function generate_unique_username($base_username) {
        $username = $base_username;
        $counter = 1;

        while (username_exists($username) && $counter < 1000) {
            $username = $base_username . $counter;
            $counter++;
        }

        if (username_exists($username)) {
            return new WP_Error('username_generation_failed', __('Unable to generate unique username.', 'lbv-user'));
        }

        return $username;
    }
}

LBV_Google_OAuth::get_instance();