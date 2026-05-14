<?php

defined('ABSPATH') || exit;

class LBV_Social_Login {

    private static $instance = null;
    private $enabled_providers = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_enabled_providers();

        add_action('admin_init', array($this, 'lbv_redirect_subscriber_from_admin'));
        add_action('init', array($this, 'lbv_redirect_logged_in_user_from_login'));

        add_action('wp_ajax_lbv_user_nonce', array($this, 'lbv_user_nonce'));
        add_action('wp_ajax_nopriv_lbv_user_nonce', array($this, 'lbv_user_nonce'));

        add_action('wp_footer', array($this, 'render_popup_placeholder'));
    }

    private function init_enabled_providers() {
        $default_providers = array(
                'google' => true,
                'github' => false,
                'facebook' => false,
                'twitter' => false,
        );

        $saved_providers = get_option('lbv_social_login_providers', array());
        $providers = wp_parse_args($saved_providers, $default_providers);

        $this->enabled_providers = apply_filters('lbv_enabled_social_providers', $providers);
    }

    public function is_provider_enabled($provider) {
        return isset($this->enabled_providers[$provider]) && $this->enabled_providers[$provider] === true;
    }

    public function get_enabled_providers() {
        return array_keys(array_filter($this->enabled_providers));
    }

    private function get_providers_config() {
        return array(
                'google' => array(
                        'name' => __('Google', 'lbv'),
                        'label' => __('Continue with Google', 'lbv'),
                        'icon_width' => 20,
                        'icon_height' => 20,
                        'class' => 'google-signin-btn',
                        'container_class' => 'google-signin-container',
                ),
                'github' => array(
                        'name' => __('GitHub', 'lbv'),
                        'label' => __('Continue with GitHub', 'lbv'),
                        'icon_width' => 20,
                        'icon_height' => 20,
                        'class' => 'github-signin-btn',
                        'container_class' => 'github-signin-container',
                ),
                'facebook' => array(
                        'name' => __('Facebook', 'lbv'),
                        'label' => __('Continue with Facebook', 'lbv'),
                        'icon_width' => 20,
                        'icon_height' => 20,
                        'class' => 'facebook-signin-btn',
                        'container_class' => 'facebook-signin-container',
                ),
        );
    }

    private function get_social_icon($provider = 'google', $width = 20, $height = 20) {
        $icons = array(
                'google' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M20.64 12.2045c0-.6381-.0573-1.2518-.1636-1.8409H12v3.4814h4.8436c-.2086 1.125-.8427 2.0782-1.7959 2.7164v2.2581h2.9087c1.7018-1.5668 2.6836-3.874 2.6836-6.615z"></path>
                <path fill="#34A853" d="M12 21c2.43 0 4.4673-.806 5.9564-2.1805l-2.9087-2.2581c-.8059.54-1.8368.859-3.0477.859-2.344 0-4.3282-1.5831-5.036-3.7104H3.9574v2.3318C5.4382 18.9832 8.4818 21 12 21z"></path>
                <path fill="#FBBC05" d="M6.964 13.71c-.18-.54-.2822-1.1168-.2822-1.71s.1023-1.17.2823-1.71V7.9582H3.9573A8.9965 8.9965 0 0 0 3 12c0 1.4523.3477 2.8268.9573 4.0418L6.964 13.71z"></path>
                <path fill="#EA4335" d="M12 6.5795c1.3214 0 2.5077.4541 3.4405 1.346l2.5813-2.5814C16.4632 3.8918 14.426 3 12 3 8.4818 3 5.4382 5.0168 3.9573 7.9582L6.964 10.29C7.6718 8.1627 9.6559 6.5795 12 6.5795z"></path>
            </svg>',
                'github' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>',
                'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>',
        );

        $icon = isset($icons[$provider]) ? $icons[$provider] : '';

        return apply_filters('lbv_social_icon', $icon, $provider, $width, $height);
    }

    private function render_social_button($provider, $redirect_url, $context = 'popup') {
        if (!$this->is_provider_enabled($provider)) return;

        $config = $this->get_providers_config();
        if (!isset($config[$provider])) return;

        $provider_config = $config[$provider];

        $login_url = add_query_arg(array(
                'loginSocial' => $provider,
                'redirect_to' => urlencode($redirect_url),
        ), wp_login_url());

        $btn_class = ($context === 'popup') ? $provider_config['class'] . ' oauth-link' : $provider_config['class'];
        if ($context === 'admin') $btn_class = 'lbv-admin-social-btn';

        ?>
        <div class="<?php echo esc_attr($provider_config['container_class']); ?>">
            <a rel="nofollow" href="<?php echo esc_url($login_url); ?>" class="<?php echo esc_attr($btn_class); ?>">
                <?php echo $this->get_social_icon($provider, ($context === 'popup' ? 18 : 20)); ?>
                <span>
                    <?php echo ($context === 'popup') ? sprintf(__('Continue with %s', 'lbv'), '<b>' . esc_html($provider_config['name']) . '</b>') : esc_html($provider_config['label']); ?>
                </span>
            </a>
        </div>
        <?php
    }

    private function render_social_buttons($redirect_url, $context = 'popup') {
        $enabled_providers = $this->get_enabled_providers();
        if (empty($enabled_providers)) return;

        foreach ($enabled_providers as $provider) {
            $this->render_social_button($provider, $redirect_url, $context);
        }
    }

    public function lbv_user_nonce() {
        wp_send_json_success(array(
                'nonce' => wp_create_nonce('lbv_user_nonce'),
                'timestamp' => time()
        ));
    }

    public function lbv_redirect_subscriber_from_admin() {
        if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            return;
        }

        $current_user = wp_get_current_user();
        if (in_array('subscriber', (array) $current_user->roles)) {
            wp_safe_redirect(home_url());
            exit;
        }
    }

    public function lbv_redirect_logged_in_user_from_login() {
        if (current_user_can('manage_options')) {
            return;
        }

        if (!isset($GLOBALS['pagenow']) || $GLOBALS['pagenow'] !== 'wp-login.php') {
            return;
        }

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $allowed_actions = array('logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register');

        if (in_array($action, $allowed_actions)) {
            return;
        }

        if (is_user_logged_in()) {
            wp_safe_redirect(home_url());
            exit;
        }
    }

    public function admin_social_login_buttons() {
        $enabled_providers = $this->get_enabled_providers();

        if (empty($enabled_providers)) {
            return;
        }

        $redirect_url = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : admin_url();
        ?>
        <div class="social-login-separator">
            <span><?php echo __('Or continue with', 'lbv'); ?></span>
        </div>

        <div class="lbv-admin-social-login">
            <?php $this->render_social_buttons($redirect_url, '', 'admin'); ?>
        </div>
        <?php
    }

    public function render_popup_placeholder() {
        if (is_user_logged_in()) return;

        $enabled_providers = $this->get_enabled_providers();
        if (empty($enabled_providers)) return;

        $redirect_url = home_url(add_query_arg([], $_SERVER['REQUEST_URI']));

        ?>
        <div id="lbv-user-popup-form" class="lbv-user-popup-form" style="display: none;">
            <div class="login-modal-overlay"></div>
            <div class="logo-popup-outer">
                <div class="logo-popup">
                    <span class="close-popup-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </span>
                    <div class="login-popup-header">
                        <div class="logo-popup-logo">
                            <?php LBV_Theme_Settings::get_instance()->lbv_site_logo(); ?>
                        </div>
                        <span class="logo-popup-heading h3"><?php _e('Welcome Back!', 'lbv'); ?></span>
                        <p class="logo-popup-description is-meta"><?php _e('SIGN IN TO YOUR ACCOUNT', 'lbv'); ?></p>
                    </div>
                    <div class="user-login-form can-register">
                        <?php
                        wp_login_form(array(
                                'echo' => true,
                                'redirect' => $redirect_url,
                                'form_id' => 'popup-form',
                        ));
                        $this->render_social_buttons($redirect_url, 'popup');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

LBV_Social_Login::get_instance();
