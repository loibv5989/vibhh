<?php

class LBV_USER_Form {

    private static $instance = null;

    public function __construct() {

        add_shortcode( 'lbv_contact', [$this, 'lbv_contact'] );
        add_action('wp_ajax_lbv_contact', [$this, 'lbv_contact_cb']);
        add_action('wp_ajax_nopriv_lbv_contact', [$this, 'lbv_contact_cb']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 12);
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function enqueue_scripts(){
        if (is_page( 'contact-us' ) || is_page( 'lien-he' ) || is_page( 'contact' )) {
            wp_enqueue_style('lbv-contact', LBV_THEME_URI . 'assets/css/contact.css', [], LBV_THEME_VERSION);
            wp_enqueue_script('lbv-contact', LBV_THEME_URI . 'assets/js/contact.min.js', ['jquery'], LBV_THEME_VERSION, true);
            wp_localize_script('lbv-contact', 'lbvCT', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce('lbv_contact_nonce'),
            ]);
        }
    }

    public function lbv_contact() {
        ob_start();
        $template = LBV_THEME_DIR . 'templates/contact/form.php';
        if ( file_exists( $template ) ) {
            include $template;
        }
        return ob_get_clean();
    }

    public function lbv_contact_cb() {
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'lbv_contact_nonce' ) ) {
            wp_send_json_error( __('Invalid security token', 'lbv') );
        }

        $name     = isset($_POST['fullname']) ? sanitize_text_field($_POST['fullname']) : '';
        $email    = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $subject  = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
        $content  = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';

        if ( empty($name) || empty($email) || empty($subject) || empty($content) ) {
            wp_send_json_error( __('Please fill in all the fields.', 'lbv') );
        }

        if (strlen($name) < 2) {
            wp_send_json_error( __('Name must be at least 2 characters long.', 'lbv') );
        }

        if (strlen($name) > 50) {
            wp_send_json_error( __('Name must be less than 50 characters.', 'lbv') );
        }

        if ( ! is_email($email) ) {
            wp_send_json_error( __('Invalid email address.', 'lbv') );
        }

        $domain = substr( strrchr( $email, "@" ), 1 );
        $status = $this->check_domain_status( $domain );

        if ( $status !== 200 ) {
            wp_send_json_error( __('Email domain is not allowed.', 'lbv') );
        }

        if (strlen($subject) < 5) {
            wp_send_json_error( __('Subject must be at least 5 characters long.', 'lbv') );
        }

        if (strlen($subject) > 100) {
            wp_send_json_error( __('Subject must be less than 100 characters.', 'lbv') );
        }

        if (strlen($content) < 10) {
            wp_send_json_error( __('Message must be at least 10 characters long.', 'lbv') );
        }

        if (strlen($content) > 250) {
            wp_send_json_error( __('Message must be less than 250 characters.', 'lbv') );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'lbv_contact';

        $result = $wpdb->insert( $table, [
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'content' => $content,
            'date'    => current_time('mysql'),
        ], [ '%s', '%s', '%s', '%s', '%s' ] );

        if ( $result ) {
            $this->sendmail_admin($name, $email, $subject, $content);

            wp_send_json_success([
                'message' => __('Contact submitted successfully.', 'lbv'),
                'html' => $this->get_success_html()
            ]);
        } else {
            wp_send_json_error( __('Failed to submit contact, please try again.', 'lbv') );
        }
    }

    private function get_success_html() {
        ob_start();
        ?>
        <div style="background: #d4edda; color: #155724; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
            <h3>🎉 <?php _e('Thank you for contacting us!', 'lbv'); ?></h3>
            <p><?php _e('We will get back to you as soon as possible.', 'lbv'); ?></p>
            <p>
                <?php _e('Or chat with us on Facebook:', 'lbv'); ?>
                <a href="<?= LBV_SOCIAL_FACEBOOK ?>" target="_blank" style="color: #1877f2;">
                    <?= LBV_SOCIAL_FACEBOOK ?>
                </a>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    private function sendmail_admin($name, $email, $subject, $content){
        $admin_email   = get_option('admin_email');
        $email_subject = sprintf( __('📩 New Contact Submission: %s', 'lbv'), $subject );

        $email_message  = "==============================\n";
        $email_message .= sprintf( __('   New Contact Submission', 'lbv') ) . "\n";
        $email_message .= "==============================\n\n";
        $email_message .= sprintf( __('Name    : %s', 'lbv'), $name ) . "\n";
        $email_message .= sprintf( __('Email   : %s', 'lbv'), $email ) . "\n";
        $email_message .= sprintf( __('Subject : %s', 'lbv'), $subject ) . "\n\n";
        $email_message .= __('Message:', 'lbv') . "\n";
        $email_message .= "---------------------------------\n";
        $email_message .= "{$content}\n";
        $email_message .= "---------------------------------\n\n";
        $email_message .= sprintf( __('Sent from : %s', 'lbv'), get_bloginfo('name') ) . "\n";
        $email_message .= sprintf( __('Date      : %s', 'lbv'), date('Y-m-d H:i:s') ) . "\n";
        $email_message .= "=================================\n";

        wp_mail($admin_email, $email_subject, $email_message);
    }

    private function check_domain_status( $domain ) {
        $domainProviders = array(
            "gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "icloud.com",
            "protonmail.com", "yandex.ru", "yandex.com", "zoho.com", "aol.com",
            "mail.ru", "gmx.com", "tutanota.com", "fastmail.com", "mail.com", "seznam.cz"
        );

        if ( in_array( $domain, $domainProviders ) ) {
            return 200;
        }

        if ( preg_match( '/\.ru$/', $domain ) && !in_array( $domain, ['mail.ru', 'yandex.ru'] ) ) {
            return 999;
        }

        $ch = curl_init();
        curl_setopt_array( $ch, array(
            CURLOPT_URL            => 'https://' . $domain,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 2,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ) );

        curl_exec( $ch );
        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close( $ch );

        return $http_code ?: 999;
    }
}

LBV_USER_Form::get_instance();
