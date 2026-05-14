<?php

defined('ABSPATH') || exit;

class LBV_GitHub_OAuth {

    private static $instance = null;

    const AUTH_URL  = 'https://github.com/login/oauth/authorize';
    const TOKEN_URL = 'https://github.com/login/oauth/access_token';
    const API_URL   = 'https://api.github.com';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('login_init', array($this, 'handle_github_login'));
    }

    private function theme_setting() {
        return LBV_Theme_Settings::get_instance();
    }

    private function client_id() {
        return $this->theme_setting()->lbv_git_client_id();
    }

    private function client_secret() {
        return $this->theme_setting()->lbv_git_client_secret();
    }

    private function redirect_uri() {
        return site_url('/wp-login.php?loginSocial=github');
    }

    public function handle_github_login() {
        if (!isset($_GET['loginSocial']) || $_GET['loginSocial'] !== 'github') {
            return;
        }

        if (isset($_GET['code']) && isset($_GET['state'])) {
            $this->process_github_callback($_GET['code'], $_GET['state']);
        } else {
            $this->redirect_to_github();
        }
    }

    private function redirect_to_github() {
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
                'scope'         => 'user:email',
                'state'         => $state,
                'allow_signup'  => 'true'
        );

        $auth_url = self::AUTH_URL . '?' . http_build_query($params);
        wp_redirect($auth_url);
        exit;
    }

    private function process_github_callback($code, $state) {
        $state_data = get_transient('lbv_oauth_state_' . $state);

        if (!$state_data || (time() - $state_data['created'] > 600)) {
            error_log('LBV GitHub OAuth Error: Invalid or expired state parameter');
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
            error_log('LBV GitHub OAuth Error: ' . $user->get_error_message());
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
        $response = wp_remote_post(self::TOKEN_URL, array(
                'timeout' => 30,
                'headers' => array('Accept' => 'application/json'),
                'body' => array(
                        'client_id'     => $this->client_id(),
                        'client_secret' => $this->client_secret(),
                        'code'          => $code,
                        'redirect_uri'  => $this->redirect_uri()
                )
        ));

        if (is_wp_error($response)) {
            error_log('LBV GitHub OAuth Token Error: ' . $response->get_error_message());
            return false;
        }

        $status = wp_remote_retrieve_response_code($response);
        if ($status !== 200) {
            error_log('LBV GitHub OAuth Token HTTP Error: ' . $status);
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['error'])) {
            error_log('LBV GitHub OAuth Token Error: ' . ($body['error_description'] ?? $body['error']));
            return false;
        }

        return $body['access_token'] ?? false;
    }

    private function get_user_info($access_token) {
        $headers = array(
                'Authorization' => 'Bearer ' . $access_token,
                'Accept'        => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28'
        );

        $response = wp_remote_get(self::API_URL . '/user', array(
                'timeout' => 30,
                'headers' => $headers
        ));

        if (is_wp_error($response)) {
            error_log('LBV GitHub OAuth User Info Error: ' . $response->get_error_message());
            return false;
        }

        $status = wp_remote_retrieve_response_code($response);
        if ($status !== 200) {
            error_log('LBV GitHub OAuth User Info HTTP Error: ' . $status);
            return false;
        }

        $user_data = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($user_data['email'])) {
            $email_response = wp_remote_get(self::API_URL . '/user/emails', array(
                    'timeout' => 30,
                    'headers' => $headers
            ));

            if (!is_wp_error($email_response) && wp_remote_retrieve_response_code($email_response) === 200) {
                $emails = json_decode(wp_remote_retrieve_body($email_response), true);
                foreach ($emails as $email) {
                    if (!empty($email['verified'])) {
                        $user_data['email'] = $email['email'];
                        if (!empty($email['primary'])) break;
                    }
                }
            }
        }

        return $user_data;
    }

    private function authenticate_user($user_info) {
        if (empty($user_info['email'])) {
            return new WP_Error('no_email', __('GitHub account does not have a verified email address.', 'lbv-user'));
        }

        $email = sanitize_email($user_info['email']);
        if (!is_email($email)) {
            return new WP_Error('invalid_email', __('Invalid email address format.', 'lbv-user'));
        }

        $user = get_user_by('email', $email);
        if ($user) {
            $this->update_user_github_data($user->ID, $user_info);
            return $user;
        }

        if (!get_option('users_can_register')) {
            return new WP_Error('registration_disabled', __('User registration is currently not allowed.', 'lbv-user'));
        }

        return $this->create_new_user($user_info);
    }

    private function update_user_github_data($user_id, $user_info) {
        if (!empty($user_info['id'])) {
            update_user_meta($user_id, 'lbv_github_id', sanitize_text_field($user_info['id']));
        }

        if (!empty($user_info['login'])) {
            update_user_meta($user_id, 'lbv_github_login', sanitize_text_field($user_info['login']));
        }

        if (!empty($user_info['avatar_url'])) {
            update_user_meta($user_id, 'lbv_github_avatar', esc_url_raw($user_info['avatar_url']));
        }

        if (!empty($user_info['html_url'])) {
            update_user_meta($user_id, 'lbv_github_profile', esc_url_raw($user_info['html_url']));
        }
    }

    private function create_new_user($user_info) {
        $email = sanitize_email($user_info['email']);
        $base_username = sanitize_user($user_info['login'], true);

        if (empty($base_username) || !validate_username($base_username)) {
            $base_username = 'github_user';
        }

        $username = $this->generate_unique_username($base_username);
        if (is_wp_error($username)) {
            return $username;
        }

        $display_name = !empty($user_info['name']) ? sanitize_text_field($user_info['name']) : $username;
        $parts = explode(' ', $display_name, 2);

        $user_data = array(
                'user_login'   => $username,
                'user_email'   => $email,
                'display_name' => $display_name,
                'first_name'   => $parts[0] ?? '',
                'last_name'    => $parts[1] ?? '',
                'user_pass'    => wp_generate_password(20, true, true),
                'role'         => get_option('default_role')
        );

        $user_id = wp_insert_user($user_data);
        if (is_wp_error($user_id)) {
            error_log('LBV GitHub OAuth Create User Error: ' . $user_id->get_error_message());
            return $user_id;
        }

        $this->update_user_github_data($user_id, $user_info);

        do_action('lbv_github_oauth_user_created', $user_id, $user_info);

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

LBV_GitHub_OAuth::get_instance();