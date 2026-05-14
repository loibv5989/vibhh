<?php

class LBV_Admin_Login {

    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
        $this->setup_cron();
    }

    private function init_hooks() {
        add_filter('show_admin_bar', array($this, 'remove_admin_bar_for_non_admins'), PHP_INT_MAX);

        add_filter('login_display_language_dropdown', '__return_false');
        add_action('login_enqueue_scripts', array($this, 'customize_logo'), 999);
        add_filter('login_headerurl', array($this, 'customize_logo_url'));
        add_filter('login_headertext', array($this, 'customize_logo_title'));
        add_action('login_form', array($this, 'add_social_login_buttons'));
        add_action('register_form', array($this, 'add_social_login_buttons'));
        add_action('login_enqueue_scripts', array($this, 'enqueue_login_styles'));

        add_filter('registration_errors', array($this, 'validate_registration'), 10, 3);

        add_action('user_register', array($this, 'add_user_verify_meta'), 10, 1);

        add_action('login_form_rp', array($this, 'set_user_verify_when_confirm'));
        add_action('login_form_resetpass', array($this, 'set_user_verify_when_confirm'));

        add_action('lbv_google_oauth_user_created', array($this, 'set_oauth_user_verified'), 10, 2);
        add_action('lbv_github_oauth_user_created', array($this, 'set_oauth_user_verified'), 10, 2);

        add_action('login_footer', array($this, 'add_login_wrapper_script'));
    }

    private function setup_cron() {
        if (!wp_next_scheduled('lbv_delete_unverified_users_cron')) {
            $midnight = strtotime('tomorrow 00:00', current_time('timestamp'));
            wp_schedule_event($midnight, 'daily', 'lbv_delete_unverified_users_cron');
        }

        add_action('lbv_delete_unverified_users_cron', array($this, 'delete_unverified_users'));
    }

    public function remove_admin_bar_for_non_admins($show_admin_bar) {
        if (!current_user_can('administrator')) {
            return false;
        }
        return $show_admin_bar;
    }

    public function customize_logo() {
        $logo_url = $this->get_logo_url();

        if (empty($logo_url)) {
            return;
        }
        ?>
        <style>#login h1 a, .login h1 a {background-image: url(<?php echo esc_url($logo_url); ?>);}</style>
        <?php
    }

    public function add_social_login_buttons() {
        LBV_Social_Login::get_instance()->admin_social_login_buttons();

        if (!isset($_GET['action']) || $_GET['action'] !== 'register') {
            return;
        }

        $num1 = rand(1, 5);
        $num2 = rand(1, 5);

        $question_key = 'math_question_' . wp_generate_password(20, false);
        set_transient($question_key, $num1 + $num2, 300);

        ?>
        <p class="math-question-field">
            <label for="math_question">
                <?php echo sprintf(__('Question: %d + %d = ?', 'lbv'), $num1, $num2); ?>
                <span class="required">*</span>
            </label>
            <input type="number" name="math_question" id="math_question" class="input" value="" size="25" required
                   autocomplete="off" placeholder="<?php _e('Enter your answer', 'lbv'); ?>" />
            <input type="hidden" name="math_question_key" value="<?php echo esc_attr($question_key); ?>" />
        </p>
        <?php
    }

    private function get_logo_url() {
        if (!class_exists('LBV_Theme_Settings')) {
            return '';
        }

        $main_logo = LBV_Theme_Settings::get_instance()->get_option('main_logo');

        if (empty($main_logo)) {
            return '';
        }

        return wp_get_attachment_url($main_logo);
    }

    public function customize_logo_url($url) {
        return home_url();
    }

    public function customize_logo_title($title) {
        return get_bloginfo('name');
    }

    public function enqueue_login_styles() {
        wp_enqueue_style('login-screen', LBV_THEME_URI . 'backend/assets/css/login.css', array(), LBV_THEME_VERSION);
    }

    public function validate_registration($errors, $sanitized_user_login, $user_email) {

        $has_username = !empty($sanitized_user_login);
        $has_email = !empty($user_email);

        if ($has_username) {
            $errors = $this->validate_username($errors, $sanitized_user_login);
        }

        if ($has_email) {
            $errors = $this->validate_email_domain($errors, $user_email);
        }

        if ($has_username && $has_email && !$errors->has_errors()) {
            $errors = $this->validate_math_question($errors);
        }

        return $errors;
    }

    private function validate_math_question($errors) {
        if (empty($_POST['math_question_key'])) {
            $errors->add('math_question_missing', '<strong>Error</strong>: Answer the question first.');
            return $errors;
        }

        $question_key = sanitize_text_field($_POST['math_question_key']);
        $correct_answer = get_transient($question_key);

        if ($correct_answer === false) {
            $errors->add('math_question_expired', '<strong>Error</strong>: Question expired. Please refresh and try again.');
            return $errors;
        }

        if (empty($_POST['math_question'])) {
            delete_transient($question_key);
            $errors->add('math_question_empty', '<strong>Error</strong>: Please answer the question.');
            return $errors;
        }

        $user_answer = intval($_POST['math_question']);

        if ($user_answer !== intval($correct_answer)) {
            delete_transient($question_key);
            $errors->add('math_question_wrong', '<strong>Error</strong>: Incorrect answer. Please try again.');
            return $errors;
        }

        delete_transient($question_key);

        return $errors;
    }

    private function validate_username($errors, $sanitized_user_login) {
        if (preg_match('/\s/', $sanitized_user_login)) {
            $errors->add('username_whitespace', '<strong>Error</strong>: Username cannot contain spaces.');
        }

        if (strlen($sanitized_user_login) < 3) {
            $errors->add('username_length_min', '<strong>Error</strong>: Username must be at least 3 characters long.');
        }

        if (strlen($sanitized_user_login) > 15) {
            $errors->add('username_length_max', '<strong>Error</strong>: Username cannot exceed 15 characters.');
        }

        if (preg_match('/[A-Z]/', $sanitized_user_login)) {
            $errors->add('username_uppercase', '<strong>Error</strong>: Username must contain only lowercase letters.');
        }

        return $errors;
    }

    private function validate_email_domain($errors, $user_email) {
        $domain = substr(strrchr($user_email, "@"), 1);

        if (!$domain) {
            return $errors;
        }

        $status = $this->check_domain_status($domain);

        if ($status !== 200) {
            $errors->add('email_blocked', '<strong>Error</strong>: This email is not allowed.');
        }

        return $errors;
    }

    private function check_domain_status($domain) {
        $allowed_providers = array(
                "gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "icloud.com",
                "protonmail.com", "yandex.ru", "yandex.com", "zoho.com", "aol.com",
                "mail.ru", "gmx.com", "tutanota.com", "fastmail.com", "mail.com", "seznam.cz"
        );

        if (in_array($domain, $allowed_providers)) {
            return 200;
        }

        if (preg_match('/\.ru$/', $domain) && !in_array($domain, ['mail.ru', 'yandex.ru'])) {
            return 999;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
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
        ));

        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $http_code ?: 999;
    }

    public function add_user_verify_meta($user_id) {
        if (is_admin() && current_user_can('create_users')) {
            add_user_meta($user_id, 'user_verify', 1, true);
        } else {
            add_user_meta($user_id, 'user_verify', 0, true);
            add_user_meta($user_id, 'user_verify_time', current_time('timestamp'));
        }
    }

    public function set_user_verify_when_confirm() {
        if (isset($_GET['login'])) {
            $user = get_user_by('login', sanitize_text_field($_GET['login']));
            if ($user && get_user_meta($user->ID, 'user_verify', true) == 0) {
                update_user_meta($user->ID, 'user_verify', 1);
            }
        }
    }

    public function set_oauth_user_verified($user_id, $user_info) {
        update_user_meta($user_id, 'user_verify', 1);
    }

    public function delete_unverified_users() {
        $args = array(
                'meta_query' => array(
                        array(
                                'key'     => 'user_verify',
                                'value'   => 0,
                                'compare' => '='
                        ),
                        array(
                                'key'     => 'user_verify_time',
                                'value'   => current_time('timestamp') - 86400,
                                'compare' => '<'
                        )
                ),
                'fields' => 'ID'
        );

        $users = get_users($args);
        global $wpdb;

        foreach ($users as $user_id) {
            $wpdb->delete($wpdb->posts, ['post_author' => $user_id]);
            $wpdb->delete($wpdb->usermeta, ['user_id' => $user_id]);
            $wpdb->delete($wpdb->users, ['ID' => $user_id]);
            error_log("🚮 LBV: Deleted unverified user ID: $user_id");
        }
    }

    public function add_login_wrapper_script() {
        ?>
        <script>
            (function() {
                var loginDiv = document.getElementById('login');
                if (loginDiv) {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'login-wrapper';
                    loginDiv.parentNode.insertBefore(wrapper, loginDiv);
                    wrapper.appendChild(loginDiv);
                }
            })();
        </script>
        <?php
    }
}

LBV_Admin_Login::get_instance();
