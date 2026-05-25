<?php

defined('ABSPATH') || exit;

class LBV_Settings {

    private static $instance = null;

    private $options;

    public function __construct() {
        $this->options = get_option('lbv_options', array());
        add_action('wp_ajax_lbv_save_logo', array($this, 'save_logo_settings'));
        add_action('wp_ajax_lbv_remove_logo', array($this, 'remove_logo'));
        add_action('wp_ajax_lbv_save_oauth', array($this, 'save_oauth_settings'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_option($key, $default = '') {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    public function render_logo_settings() {
        ?>
        <div class="lbv-settings-header">
            <div class="lbv-search-box">
                <input type="text" placeholder="<?php _e('Search for options...', 'lbv'); ?>">
            </div>
            <div class="lbv-header-actions">
                <button class="lbv-btn lbv-btn-primary" id="lbv-save-settings">
                    <?php _e('Save Changes', 'lbv'); ?>
                </button>
            </div>
        </div>

        <div class="lbv-content-box">
            <h2>
                <span class="dashicons dashicons-desktop"></span>
                <?php _e('Default Logos', 'lbv'); ?>
            </h2>
            <p><?php _e('Upload logos for you website.', 'lbv'); ?></p>

            <?php $this->render_info_box('blue', __('Ensure that dark mode logos are configured when enabling dark mode for your site.', 'lbv')); ?>
            <?php
            $this->render_logo_field(
                'main_logo',
                __('Main Logo', 'lbv'),
                __('Select or upload the main logo for your site.', 'lbv'),
                __('For optimal display, use a retina-ready logo with a recommended height of 120px, which is twice the height of its wrapper.', 'lbv')
            );
            ?>
            <?php
            $this->render_logo_field(
                'dark_logo',
                __('Dark Mode - Main Logo', 'lbv'),
                __('Select or upload the logo for your site\'s dark mode.', 'lbv'),
                __('This logo should match the main logo but with colors adjusted to contrast well with a dark mode header background.', 'lbv')
            );
            ?>
        </div>
        <?php
    }

    public function render_mobile_logo_settings() {
        ?>
        <div class="lbv-settings-header">
            <div class="lbv-search-box">
                <input type="text" placeholder="<?php _e('Search for options...', 'lbv'); ?>">
            </div>
            <div class="lbv-header-actions">
                <button class="lbv-btn lbv-btn-primary" id="lbv-save-settings">
                    <?php _e('Save Changes', 'lbv'); ?>
                </button>
            </div>
        </div>

        <div class="lbv-content-box">
            <h2>
                <span class="dashicons dashicons-smartphone"></span>
                <?php _e('Mobile Logos', 'lbv'); ?>
            </h2>
            <p><?php _e('Upload logos specifically optimized for mobile devices.', 'lbv'); ?></p>

            <?php $this->render_info_box('blue', __('Mobile logos will be displayed on devices with screen widths less than 768px.', 'lbv')); ?>

            <?php $this->render_info_box('orange', __('If mobile logos are not set, the main logos will be used instead.', 'lbv')); ?>

            <?php
            $this->render_logo_field(
                'mobile_logo',
                __('Mobile Logo', 'lbv'),
                __('Select or upload the logo for mobile devices.', 'lbv'),
                __('For optimal mobile display, use a logo with a recommended height of 80px.', 'lbv')
            );
            ?>

            <?php
            $this->render_logo_field(
                'mobile_dark_logo',
                __('Dark Mode - Mobile Logo', 'lbv'),
                __('Select or upload the dark mode logo for mobile devices.', 'lbv'),
                __('This logo will be displayed on mobile devices when dark mode is enabled.', 'lbv')
            );
            ?>
        </div>
        <?php
    }

    private function render_info_box($type, $message) {
        ?>
        <div class="lbv-info-box lbv-info-<?php echo esc_attr($type); ?>">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <?php
    }

    private function render_logo_field($field_key, $label, $description, $note = '') {
        $logo_id = $this->get_option($field_key);
        $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
        ?>
        <div class="lbv-field-wrapper" data-field="<?php echo esc_attr($field_key); ?>">
            <div class="lbv-field-left">
                <label class="lbv-field-label"><?php echo esc_html($label); ?></label>
                <p class="lbv-field-desc"><?php echo esc_html($description); ?></p>
            </div>
            <div class="lbv-field-right">
                <input type="text" class="lbv-logo-url-display" value="<?php echo esc_url($logo_url); ?>" readonly placeholder="No logo selected">
                <div class="lbv-logo-preview-box">
                    <?php if ($logo_url): ?>
                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($label); ?>" class="lbv-logo-image">
                    <?php endif; ?>
                </div>
                <div class="lbv-field-actions">
                    <button type="button" class="lbv-btn lbv-btn-success lbv-upload-logo" data-field="<?php echo esc_attr($field_key); ?>">
                        <?php _e('Upload', 'lbv'); ?>
                    </button>
                    <button type="button" class="lbv-btn lbv-btn-danger lbv-remove-logo" data-field="<?php echo esc_attr($field_key); ?>" <?php echo !$logo_id ? 'disabled' : ''; ?>>
                        <?php _e('Remove', 'lbv'); ?>
                    </button>
                </div>
                <?php if ($note): ?>
                    <p class="lbv-field-note">
                        <?php echo esc_html($note); ?>
                    </p>
                <?php endif; ?>
                <input type="hidden" class="lbv-logo-id" value="<?php echo esc_attr($logo_id); ?>">
            </div>
        </div>
        <?php
    }

    public function save_logo_settings() {
        check_ajax_referer('lbv_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'lbv')));
        }

        $field = isset($_POST['field']) ? sanitize_text_field($_POST['field']) : '';
        $id = isset($_POST['id']) ? absint($_POST['id']) : 0;

        if ($field && $id) {
            $options = get_option('lbv_options', array());
            $options[$field] = $id;
            update_option('lbv_options', $options);
            wp_send_json_success(array(
                'message' => __('Logo saved successfully', 'lbv'),
                'id' => $id
            ));
        }

        wp_send_json_error(array('message' => __('Invalid data', 'lbv')));
    }

    public function remove_logo() {
        check_ajax_referer('lbv_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'lbv')));
        }

        $field = isset($_POST['field']) ? sanitize_text_field($_POST['field']) : '';
        if ($field) {
            $options = get_option('lbv_options', array());
            unset($options[$field]);
            update_option('lbv_options', $options);
            wp_send_json_success(array('message' => __('Logo removed successfully', 'lbv')));
        }
        wp_send_json_error(array('message' => __('Invalid field', 'lbv')));
    }

    public function save_oauth_settings() {
        check_ajax_referer('lbv_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'lbv')));
        }

        $oauth_fields = array('git_client_id', 'git_client_secret', 'google_client_id', 'google_client_secret');
        $options = get_option('lbv_options', array());

        foreach ($oauth_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);

                if (!empty($value)) {
                    $options[$field] = $value;
                } elseif (isset($_POST['remove_' . $field])) {
                    unset($options[$field]);
                }
            }
        }

        update_option('lbv_options', $options);
        wp_send_json_success(array('message' => __('OAuth settings saved successfully', 'lbv')));
    }

    public function render_system_info() {
        $this->load_template('system-info');
    }

    public function render_oauth_settings() {
        ?>
        <div class="lbv-settings-header">
            <div class="lbv-search-box">
                <input type="text" placeholder="<?php _e('Search for options...', 'lbv'); ?>">
            </div>
            <div class="lbv-header-actions">
                <button class="lbv-btn lbv-btn-primary" id="lbv-save-oauth-settings">
                    <?php _e('Save Changes', 'lbv'); ?>
                </button>
            </div>
        </div>

        <div class="lbv-content-box">
            <h2>
                <span class="dashicons dashicons-admin-network"></span>
                <?php _e('Login OAuth Settings', 'lbv'); ?>
            </h2>
            <p><?php _e('Configure OAuth credentials for third-party authentication services.', 'lbv'); ?></p>

            <?php $this->render_info_box('orange', __('Keep your OAuth credentials secure. Never share them publicly or commit them to version control.', 'lbv')); ?>

            <!-- GitHub OAuth -->
            <div class="lbv-field-group">
                <h3>
                    <span class="dashicons dashicons-admin-site"></span>
                    <?php _e('GitHub OAuth', 'lbv'); ?>
                </h3>
                <?php
                $this->render_text_field(
                    'git_client_id',
                    __('GitHub Client ID', 'lbv'),
                    __('Enter your GitHub OAuth application Client ID.', 'lbv')
                );
                ?>
                <?php
                $this->render_password_field(
                    'git_client_secret',
                    __('GitHub Client Secret', 'lbv'),
                    __('Enter your GitHub OAuth application Client Secret.', 'lbv')
                );
                ?>
            </div>

            <!-- Google OAuth -->
            <div class="lbv-field-group">
                <h3>
                    <span class="dashicons dashicons-google"></span>
                    <?php _e('Google OAuth', 'lbv'); ?>
                </h3>
                <?php
                $this->render_text_field('google_client_id', __('Google Client ID', 'lbv'), __('Enter your Google OAuth application Client ID.', 'lbv'));
                ?>
                <?php
                $this->render_password_field(
                    'google_client_secret',
                    __('Google Client Secret', 'lbv'),
                    __('Enter your Google OAuth application Client Secret.', 'lbv')
                );
                ?>
            </div>
        </div>
        <?php
    }

    private function render_text_field($field_key, $label, $description) {
        $value = $this->get_option($field_key, '');
        ?>
        <div class="lbv-field-wrapper" data-field="<?php echo esc_attr($field_key); ?>">
            <div class="lbv-field-left">
                <label class="lbv-field-label"><?php echo esc_html($label); ?></label>
                <p class="lbv-field-desc"><?php echo esc_html($description); ?></p>
            </div>
            <div class="lbv-field-right">
                <input type="text" class="lbv-text-input" name="lbv_options[<?php echo esc_attr($field_key); ?>]"
                       value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($label); ?>">
            </div>
        </div>
        <p>Authorized redirect URIs: <?= home_url() ?>/wp-login.php?loginSocial=google</p>
        <?php
    }

    private function render_password_field($field_key, $label, $description) {
        $value = $this->get_option($field_key, '');
        $masked_value = !empty($value) ? '••••••••••••••••' : '';
        ?>
        <div class="lbv-field-wrapper" data-field="<?php echo esc_attr($field_key); ?>">
            <div class="lbv-field-left">
                <label class="lbv-field-label"><?php echo esc_html($label); ?></label>
                <p class="lbv-field-desc"><?php echo esc_html($description); ?></p>
            </div>
            <div class="lbv-field-right">
                <input type="password"
                       class="lbv-password-input"
                       name="lbv_options[<?php echo esc_attr($field_key); ?>]"
                       value="<?php echo esc_attr($value); ?>"
                       placeholder="<?php echo empty($value) ? esc_attr($label) : $masked_value; ?>">
                <button type="button" class="lbv-btn lbv-btn-secondary lbv-toggle-password">
                    <span class="dashicons dashicons-visibility"></span>
                </button>
            </div>
        </div>
        <?php
    }

    private function load_template($template, $args = array()) {
        $template_path = get_template_directory() . '/backend/templates/' . $template . '.php';
        if (file_exists($template_path)) {
            if (!empty($args)) {
                extract($args);
            }
            include $template_path;
        } else {
            echo '<div class="notice notice-error"><p>' . sprintf(__('Template file "%s" not found!', 'lbv'), $template) . '</p></div>';
        }
    }
}

LBV_Settings::get_instance();