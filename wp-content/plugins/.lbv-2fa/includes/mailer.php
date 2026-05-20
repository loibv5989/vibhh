<?php
if (!defined('ABSPATH')) {
    exit;
}

final class AM2FA_Mailer {
    public function send_code(WP_User $user, $code) {
        $settings = AM2FA_Plugin::get_settings();

        $subject = $this->replace_tags($settings['subject_template'], $user, $code);
        $body    = $this->replace_tags($settings['body_template'], $user, $code);

        $headers = array();
        if (!empty($settings['from_name']) || !empty($settings['from_email'])) {
            $from_name  = !empty($settings['from_name']) ? $settings['from_name'] : get_bloginfo('name');
            $from_email = !empty($settings['from_email']) ? $settings['from_email'] : get_option('admin_email');
            $headers[] = sprintf('From: %s <%s>', wp_specialchars_decode($from_name, ENT_QUOTES), sanitize_email($from_email));
        }

        return wp_mail($user->user_email, $subject, $body, $headers);
    }

    private function replace_tags($template, WP_User $user, $code) {
        $settings = AM2FA_Plugin::get_settings();

        $replacements = array(
            '{site_name}'    => wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES),
            '{display_name}' => $user->display_name ? $user->display_name : $user->user_login,
            '{user_login}'   => $user->user_login,
            '{code}'         => $code,
            '{ttl_minutes}'  => (string) absint($settings['ttl_minutes']),
            '{login_url}'    => wp_login_url(),
            '{ip_address}'   => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
        );

        return strtr($template, $replacements);
    }
}
