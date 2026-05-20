<?php
if (!defined('ABSPATH')) {
    exit;
}

final class AM2FA_Login {
    const COOKIE_NAME = 'am2fa_token';
    const RESEND_COOLDOWN = 60;

    const ATTEMPTS = 5;

    private $settings;
    private $mailer;

    public function __construct(AM2FA_Settings $settings, AM2FA_Mailer $mailer) {
        $this->settings = $settings;
        $this->mailer   = $mailer;

        add_filter('authenticate', array($this, 'authenticate'), 30, 3);
        add_action('login_init', array($this, 'maybe_process_am2fa'));
        add_action('login_enqueue_scripts', array($this, 'enqueue'));
        add_action('wp_logout', array($this, 'handle_logout'));
        add_filter('shake_error_codes', array($this, 'shake_error_codes'));
        add_filter('login_message', array($this, 'login_message'));
    }

    private function is_enabled() {
        $s = AM2FA_Plugin::get_settings();
        return !empty($s['enabled']);
    }

    private function is_login_screen() {
        return isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php';
    }

    private function current_action() {
        $action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : 'login';
        return $action ? $action : 'login';
    }

    private function get_redirect_to() {
        if (empty($_REQUEST['redirect_to'])) {
            return admin_url();
        }

        return wp_validate_redirect(wp_unslash($_REQUEST['redirect_to']), admin_url());
    }

    private function am2fa_url($args = array()) {
        // Token KHÔNG được đưa lên URL (tránh server log / browser history).
        // Server đọc token từ cookie; chỉ truyền action + redirect_to + error flag.
        $allowed  = array('redirect_to', 'am2fa_error');
        $filtered = array('action' => 'am2fa');
        foreach ($allowed as $key) {
            if (isset($args[$key])) {
                $filtered[$key] = $args[$key];
            }
        }
        return add_query_arg($filtered, wp_login_url());
    }

    private function get_cookie_token() {
        if (empty($_COOKIE[self::COOKIE_NAME])) {
            return '';
        }

        return sanitize_text_field(wp_unslash($_COOKIE[self::COOKIE_NAME]));
    }

    private function set_cookie($token) {
        $expire = time() + DAY_IN_SECONDS;
        $secure = is_ssl();
        $path   = COOKIEPATH ? COOKIEPATH : '/';

        if (PHP_VERSION_ID >= 70300) {
            setcookie(self::COOKIE_NAME, $token, array(
                    'expires'  => $expire,
                    'path'     => $path,
                    'domain'   => COOKIE_DOMAIN,
                    'secure'   => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax',
            ));
        } else {
            setcookie(self::COOKIE_NAME, $token, $expire, $path, COOKIE_DOMAIN, $secure, true);
        }

        $_COOKIE[self::COOKIE_NAME] = $token;
    }

    private function clear_cookie() {
        $secure = is_ssl();
        $path   = COOKIEPATH ? COOKIEPATH : '/';

        if (PHP_VERSION_ID >= 70300) {
            setcookie(self::COOKIE_NAME, '', array(
                    'expires'  => time() - YEAR_IN_SECONDS,
                    'path'     => $path,
                    'domain'   => COOKIE_DOMAIN,
                    'secure'   => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax',
            ));
        } else {
            setcookie(self::COOKIE_NAME, '', time() - YEAR_IN_SECONDS, $path, COOKIE_DOMAIN, $secure, true);
        }

        unset($_COOKIE[self::COOKIE_NAME]);
    }

    private function challenge_key($token) {
        return 'am2fa_challenge_' . $token;
    }

    private function generate_code() {
        $settings = AM2FA_Plugin::get_settings();
        $length   = max(4, min(10, absint($settings['code_length'])));

        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;

        return (string) wp_rand($min, $max);
    }

    private function should_apply_2fa(WP_User $user) {
        return !in_array('subscriber', (array) $user->roles, true);
    }

    private function create_challenge(WP_User $user) {
        $token    = wp_generate_password(32, false, false);
        $code     = $this->generate_code();
        $settings = AM2FA_Plugin::get_settings();
        $ttl      = max(1, absint($settings['ttl_minutes'])) * MINUTE_IN_SECONDS;

        $payload = array(
                'user_id'    => (int) $user->ID,
                'code_hash'  => wp_hash_password($code),
                'created_at' => time(),
                'attempts'   => 0,
        );

        set_transient($this->challenge_key($token), $payload, $ttl);
        $this->set_cookie($token);
        $this->mailer->send_code($user, $code);

        return $token;
    }

    private function get_pending($token) {
        if (empty($token)) {
            return false;
        }

        $pending = get_transient($this->challenge_key($token));
        return is_array($pending) ? $pending : false;
    }

    private function clear_pending($token) {
        if (!empty($token)) {
            delete_transient($this->challenge_key($token));
        }
    }

    private function resend_key($token) {
        return 'am2fa_resend_user_' . $token;
    }

    private function can_resend($token) {
        if (empty($token)) {
            return false;
        }

        $last_sent = (int) get_transient($this->resend_key($token));
        if ($last_sent <= 0) {
            return true;
        }

        return (time() - $last_sent) >= self::RESEND_COOLDOWN;
    }

    private function mark_resend($token) {
        if (!empty($token)) {
            set_transient($this->resend_key($token), time(), self::RESEND_COOLDOWN);
        }
    }

    private function resend_challenge($old_token, array $pending) {
        $user = get_user_by('id', (int) $pending['user_id']);
        if (!$user instanceof WP_User) {
            return false;
        }

        $this->clear_pending($old_token);
        delete_transient($this->resend_key($old_token));

        $new_token = wp_generate_password(32, false, false);
        $code      = $this->generate_code();
        $settings  = AM2FA_Plugin::get_settings();
        $ttl       = max(1, absint($settings['ttl_minutes'])) * MINUTE_IN_SECONDS;

        $new_payload = array(
            'user_id'    => (int) $user->ID,
            'code_hash'  => wp_hash_password($code),
            'created_at' => time(),
            'attempts'   => 0,
        );

        set_transient($this->challenge_key($new_token), $new_payload, $ttl);
        $this->set_cookie($new_token);           // ghi đè cookie với token mới
        $this->mark_resend($new_token);
        $this->mailer->send_code($user, $code);

        return $new_token;
    }

    private function verify_code($code, array $pending) {
        if (empty($pending['code_hash'])) {
            return false;
        }

        if (!empty($pending['attempts']) && (int) $pending['attempts'] >= 5) {
            return false;
        }

        return wp_check_password($code, $pending['code_hash']);
    }

    public function authenticate($user, $username, $password) {
        if (!$this->is_enabled() || !$this->is_login_screen()) {
            return $user;
        }

        if ($this->current_action() !== 'login') {
            return $user;
        }

        if (is_wp_error($user) || !($user instanceof WP_User)) {
            return $user;
        }

        if (!$this->should_apply_2fa($user)) {
            return $user;
        }

        $token   = $this->get_cookie_token();
        $pending = $this->get_pending($token);

        if ($pending && (int) $pending['user_id'] !== (int) $user->ID) {
            $this->clear_cookie();
            $this->clear_pending($token);
            $token   = '';
            $pending = false;
        }

        if ($pending && (int) $pending['user_id'] === (int) $user->ID) {
            $this->redirect_to_am2fa();
            exit;
        }

        $token = $this->create_challenge($user);
        $this->redirect_to_am2fa();
        exit;
    }

    private function redirect_to_am2fa($error = '') {
        $args = array(
                'redirect_to' => $this->get_redirect_to(),
        );

        if (!empty($error)) {
            $args['am2fa_error'] = $error;
        }

        wp_safe_redirect($this->am2fa_url($args));
    }

    private function redirect_to_login($error = '') {
        $args = array();

        if (!empty($error)) {
            $args['am2fa_error'] = $error;
        }

        $login_url = wp_login_url($this->get_redirect_to());
        wp_safe_redirect(add_query_arg($args, $login_url));
    }

    public function handle_logout() {
        if (!$this->is_enabled()) {
            return;
        }

        $token = $this->get_cookie_token();
        if (!empty($token)) {
            $this->clear_pending($token);
        }

        $this->clear_cookie();
    }

    public function maybe_process_am2fa() {
        if (!$this->is_enabled() || !$this->is_login_screen() || $this->current_action() !== 'am2fa') {
            return;
        }

        $token       = $this->get_cookie_token();
        $pending     = $this->get_pending($token);
        $redirect_to = $this->get_redirect_to();

        if ('POST' === strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET')) {
            $post_action = isset($_POST['am2fa_action']) ? sanitize_key(wp_unslash($_POST['am2fa_action'])) : 'verify';

            if ('resend' === $post_action) {
                check_admin_referer('am2fa_resend', '_am2fa_resend_nonce');

                if (!$pending) {
                    $this->clear_cookie();
                    $this->redirect_to_login('expired');
                    exit;
                }

                if (!$this->can_resend($token)) {
                    $this->redirect_to_am2fa('resend_wait');
                    exit;
                }

                $new_token = $this->resend_challenge($token, $pending);
                if (!$new_token) {
                    $this->clear_cookie();
                    $this->redirect_to_login('expired');
                    exit;
                }

                // Cookie đã được set_cookie() với $new_token trong resend_challenge().
                // Redirect không cần token trên URL — browser sẽ gửi cookie mới.
                $this->redirect_to_am2fa('resent');
                exit;
            }

            check_admin_referer('am2fa_verify', '_am2fa_nonce');

            if (!$pending) {
                $this->clear_cookie();
                $this->redirect_to_login('expired');
                exit;
            }

            $code = isset($_POST['am2fa_code']) ? preg_replace('/[^0-9]/', '', (string) wp_unslash($_POST['am2fa_code'])) : '';

            $pending['attempts'] = isset($pending['attempts']) ? (int) $pending['attempts'] + 1 : 1;

            $settings = AM2FA_Plugin::get_settings();
            $ttl      = max(1, absint($settings['ttl_minutes'])) * MINUTE_IN_SECONDS;

            if ($pending['attempts'] >= self::ATTEMPTS) {
                $this->clear_cookie();
                $this->clear_pending($token);
                $this->redirect_to_login('locked');
                exit;
            }

            set_transient($this->challenge_key($token), $pending, $ttl);

            if ($code === '') {
                $this->redirect_to_am2fa('required');
                exit;
            }

            if (!$this->verify_code($code, $pending)) {
                $this->redirect_to_am2fa('invalid');
                exit;
            }

            $user = get_user_by('id', (int) $pending['user_id']);
            if (!$user instanceof WP_User) {
                $this->clear_cookie();
                $this->clear_pending($token);
                $this->redirect_to_login('expired');
                exit;
            }

            $this->clear_cookie();
            $this->clear_pending($token);

            wp_set_current_user((int) $user->ID);
            wp_set_auth_cookie((int) $user->ID, false, is_ssl());
            do_action('wp_login', $user->user_login, $user);

            wp_safe_redirect($redirect_to);
            exit;
        }

        if (!$pending) {
            $this->clear_cookie();
            $this->redirect_to_login('expired');
            exit;
        }

        $this->render_am2fa_page($token, $redirect_to);
        exit;
    }

    private function render_am2fa_page($token, $redirect_to) {
        $error = isset($_GET['am2fa_error']) ? sanitize_key(wp_unslash($_GET['am2fa_error'])) : '';

        nocache_headers();
        status_header(200);

        login_header(__('Xác minh đăng nhập', 'lbv-2fa'));
        ?>
        <div class="am2fa-shell">
            <p class="message am2fa-message"><?php echo esc_html__('Một mã xác minh đã được gửi vào email của bạn. Nhập mã bên dưới để hoàn tất đăng nhập.', 'lbv-2fa'); ?></p>

            <?php if ($error === 'invalid') : ?>
                <p class="message error am2fa-message"><?php echo esc_html__('Mã xác minh không đúng.', 'lbv-2fa'); ?></p>
            <?php elseif ($error === 'required') : ?>
                <p class="message error am2fa-message"><?php echo esc_html__('Bạn phải nhập mã xác minh.', 'lbv-2fa'); ?></p>
            <?php elseif ($error === 'expired') : ?>
                <p class="message error am2fa-message"><?php echo esc_html__('Phiên xác minh đã hết hạn. Hãy đăng nhập lại.', 'lbv-2fa'); ?></p>
            <?php elseif ($error === 'locked') : ?>
                <p class="message error am2fa-message"><?php echo esc_html__('Bạn đã nhập sai quá nhiều lần. Hãy đăng nhập lại để nhận mã mới.', 'lbv-2fa'); ?></p>
            <?php elseif ($error === 'resent') : ?>
                <p class="message updated am2fa-message"><?php echo esc_html__('Mã mới đã được gửi lại vào email của bạn.', 'lbv-2fa'); ?></p>
            <?php elseif ($error === 'resend_wait') : ?>
                <p class="message error am2fa-message"><?php echo esc_html__('Vui lòng chờ một lúc trước khi gửi lại mã.', 'lbv-2fa'); ?></p>
            <?php endif; ?>

            <?php
            $form_action = $this->am2fa_url(array('redirect_to' => $redirect_to));
            ?>
            <form name="am2faform" id="am2faform" action="<?php echo esc_url($form_action); ?>" method="post" autocomplete="off">
                <p>
                    <label for="am2fa_code"><?php echo esc_html__('Mã xác minh', 'lbv-2fa'); ?><br><br>
                        <input type="text" name="am2fa_code" id="am2fa_code" class="input" inputmode="numeric" autocomplete="one-time-code" maxlength="10" placeholder="<?php echo esc_attr__('Nhập mã từ email', 'lbv-2fa'); ?>" autofocus>
                    </label>
                </p>
                <p class="submit">
                    <input type="hidden" name="am2fa_action" value="verify">
                    <input type="submit" class="button button-primary button-large" value="<?php echo esc_attr__('Xác minh', 'lbv-2fa'); ?>">
                </p>
                <?php wp_nonce_field('am2fa_verify', '_am2fa_nonce'); ?>
            </form>

            <div class="am2fa-resend">
                <p class="am2fa-resend-text"><?php echo esc_html__('Bạn không nhận được mã?', 'lbv-2fa'); ?></p>
                <form class="am2fa-resend-form" action="<?php echo esc_url($form_action); ?>" method="post">
                    <input type="hidden" name="am2fa_action" value="resend">
                    <?php wp_nonce_field('am2fa_resend', '_am2fa_resend_nonce'); ?>
                    <button type="submit" class="button button-secondary"><?php echo esc_html__('Gửi lại mã', 'lbv-2fa'); ?></button>
                </form>
            </div>
        </div>
        <?php
        login_footer();
    }

    public function enqueue() {
        if (!$this->is_enabled() || !$this->is_login_screen()) {
            return;
        }

        wp_enqueue_style('am2fa-login', AM2FA_URL . 'assets/css/login.css', array(), AM2FA_VERSION);
    }

    public function login_message($message) {
        $error = isset($_GET['am2fa_error']) ? sanitize_key(wp_unslash($_GET['am2fa_error'])) : '';

        if ($error === 'expired') {
            $notice = '<p class="message error">' . esc_html__('Phiên xác minh đã hết hạn. Hãy đăng nhập lại.', 'lbv-2fa') . '</p>';
            return $notice . $message;
        }

        if ($error === 'locked') {
            $notice = '<p class="message error">' . esc_html__('Bạn đã nhập sai quá nhiều lần. Hãy đăng nhập lại để nhận mã mới.', 'lbv-2fa') . '</p>';
            return $notice . $message;
        }

        return $message;
    }

    public function shake_error_codes($codes) {
        $codes[] = 'am2fa_code_sent';
        $codes[] = 'am2fa_code_required';
        $codes[] = 'am2fa_invalid_code';
        $codes[] = 'am2fa_mismatch';
        $codes[] = 'am2fa_expired';

        return $codes;
    }
}