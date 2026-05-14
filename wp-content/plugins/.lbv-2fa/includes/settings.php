<?php
if (!defined('ABSPATH')) {
    exit;
}

final class AM2FA_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'menu_2fa'));
        add_action('admin_init', array($this, 'register_2fa'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_2fa'));
    }

    public function menu_2fa() {
        add_options_page(
            __('LBV 2FA', 'lbv-2fa'),
            __('LBV 2FA', 'lbv-2fa'),
            'manage_options',
            'am2fa-settings',
            array($this, 'render_am2fa')
        );
    }

    public function register_2fa() {
        register_setting('am2fa_settings_group', 'am2fa_settings', array($this, 'sanitize'));

        add_settings_section(
            'am2fa_main',
            __('Cấu hình chính', 'lbv-2fa'),
            '__return_false',
            'am2fa-settings'
        );

        add_settings_field(
            'enabled',
            __('Kích hoạt', 'lbv-2fa'),
            array($this, 'field_enabled'),
            'am2fa-settings',
            'am2fa_main'
        );

        add_settings_field(
            'ttl_minutes',
            __('Thời gian hiệu lực mã', 'lbv-2fa'),
            array($this, 'field_ttl'),
            'am2fa-settings',
            'am2fa_main'
        );

        add_settings_field(
            'code_length',
            __('Độ dài mã', 'lbv-2fa'),
            array($this, 'field_code_length'),
            'am2fa-settings',
            'am2fa_main'
        );

        add_settings_field(
            'subject_template',
            __('Tiêu đề email', 'lbv-2fa'),
            array($this, 'field_subject'),
            'am2fa-settings',
            'am2fa_main'
        );

        add_settings_field(
            'body_template',
            __('Nội dung email', 'lbv-2fa'),
            array($this, 'field_body'),
            'am2fa-settings',
            'am2fa_main'
        );

        add_settings_field(
            'from_name',
            __('Tên người gửi', 'lbv-2fa'),
            array($this, 'field_from_name'),
            'am2fa-settings',
            'am2fa_main'
        );

        add_settings_field(
            'from_email',
            __('Email người gửi', 'lbv-2fa'),
            array($this, 'field_from_email'),
            'am2fa-settings',
            'am2fa_main'
        );
    }

    public function enqueue_2fa($hook) {
        if ($hook !== 'settings_page_am2fa-settings') {
            return;
        }

        wp_enqueue_style('am2fa', AM2FA_URL . 'assets/css/am2fa.css', array(), AM2FA_VERSION);
    }

    public function sanitize($input) {
        $defaults = AM2FA_Plugin::default_settings();
        $out = array();

        $out['enabled'] = empty($input['enabled']) ? 0 : 1;

        $ttl = isset($input['ttl_minutes']) ? absint($input['ttl_minutes']) : $defaults['ttl_minutes'];
        $out['ttl_minutes'] = max(1, min(60, $ttl));

        $len = isset($input['code_length']) ? absint($input['code_length']) : $defaults['code_length'];
        $out['code_length'] = max(4, min(10, $len));

        $out['subject_template'] = isset($input['subject_template']) ? sanitize_text_field(wp_unslash($input['subject_template'])) : $defaults['subject_template'];
        $out['body_template']    = isset($input['body_template']) ? sanitize_textarea_field(wp_unslash($input['body_template'])) : $defaults['body_template'];
        $out['from_name']        = isset($input['from_name']) ? sanitize_text_field(wp_unslash($input['from_name'])) : $defaults['from_name'];
        $out['from_email']       = isset($input['from_email']) ? sanitize_email(wp_unslash($input['from_email'])) : $defaults['from_email'];

        if (empty($out['from_email']) || !is_email($out['from_email'])) {
            $out['from_email'] = get_option('admin_email');
        }

        return $out;
    }

    private function settings() {
        return AM2FA_Plugin::get_settings();
    }

    public function render_am2fa() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap am2fa-wrap">
            <h1><?php echo esc_html__('Admin Mail 2FA', 'lbv-2fa'); ?></h1>
            <p><?php echo esc_html__('Plugin áp dụng cho tất cả tài khoản có quyền, ngoại trừ role Subscriber. Hệ thống bắt buộc xác minh mỗi khi mở trình duyệt (KHÔNG lưu phiên lâu dài).', 'lbv-2fa'); ?></p>
            <form method="post" action="options.php">
                <?php
                settings_fields('am2fa_settings_group');
                do_settings_sections('am2fa-settings');
                submit_button();
                ?>
            </form>
            ...
        </div>
        <?php
    }

    public function field_enabled() {
        $s = $this->settings();
        ?>
        <label>
            <input type="checkbox" name="am2fa_settings[enabled]" value="1" <?php checked(1, (int) $s['enabled']); ?>>
            <?php echo esc_html__('Kích hoạt xác thực email 2 bước (Trừ role Subscriber)', 'lbv-2fa'); ?>
        </label>
        <?php
    }

    public function field_ttl() {
        $s = $this->settings();
        ?>
        <input type="number" min="1" max="60" step="1" name="am2fa_settings[ttl_minutes]" value="<?php echo esc_attr((int) $s['ttl_minutes']); ?>">
        <p class="description"><?php echo esc_html__('Số phút mã còn hiệu lực.', 'lbv-2fa'); ?></p>
        <?php
    }

    public function field_code_length() {
        $s = $this->settings();
        ?>
        <input type="number" min="4" max="10" step="1" name="am2fa_settings[code_length]" value="<?php echo esc_attr((int) $s['code_length']); ?>">
        <p class="description"><?php echo esc_html__('Mặc định nên dùng 6 chữ số.', 'lbv-2fa'); ?></p>
        <?php
    }

    public function field_subject() {
        $s = $this->settings();
        ?>
        <input type="text" class="regular-text" name="am2fa_settings[subject_template]" value="<?php echo esc_attr($s['subject_template']); ?>">
        <?php
    }

    public function field_body() {
        $s = $this->settings();
        ?>
        <textarea class="large-text code" rows="8" name="am2fa_settings[body_template]"><?php echo esc_textarea($s['body_template']); ?></textarea>
        <?php
    }

    public function field_from_name() {
        $s = $this->settings();
        ?>
        <input type="text" class="regular-text" name="am2fa_settings[from_name]" value="<?php echo esc_attr($s['from_name']); ?>">
        <?php
    }

    public function field_from_email() {
        $s = $this->settings();
        ?>
        <input type="email" class="regular-text" name="am2fa_settings[from_email]" value="<?php echo esc_attr($s['from_email']); ?>">
        <?php
    }
}
