<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LBV_USER_Profile {

    private $errors = array();
    private static $instance = null;

    public function __construct() {
        add_shortcode( 'user_profile', array( $this, 'render_profile_form' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 12 );
        add_action( 'template_redirect', array( $this, 'handle_profile_update' ) );

        add_filter( 'user_profile_picture_description', '__return_false' );

        add_action( 'show_user_profile', array( $this, 'add_admin_custom_field' ), 5 );
        add_action( 'edit_user_profile', array( $this, 'add_admin_custom_field' ), 5 );

        add_action( 'personal_options_update', array( $this, 'save_admin_custom_field' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_admin_custom_field' ) );

        add_action( 'user_edit_form_tag', array( $this, 'add_admin_form_enctype' ) );

        add_filter( 'get_avatar', array( $this, 'custom_avatar' ), 99, 5 );
        add_filter( 'get_avatar_url', array( $this, 'custom_avatar_url' ), 99, 3 );

        add_action( 'delete_user', array( $this, 'delete_user_avatar' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        add_action( 'user_register', array($this, 'user_meta'));
        add_filter( 'manage_users_columns', array( $this, 'add_custom_user_columns' ) );
        add_filter( 'manage_users_custom_column', array( $this, 'show_custom_user_column_content' ), 10, 3 );
        add_filter( 'manage_users_sortable_columns', array( $this, 'make_registration_columns_sortable' ) );
        add_action( 'pre_get_users', array($this, 'sort_registration_columns') );

        add_action('lbv_google_oauth_user_created', array($this, 'save_google_registration_source'), 10, 2);
        add_action('lbv_github_oauth_user_created', array($this, 'save_github_registration_source'), 10, 2);

        add_action( 'wp_ajax_fup_delete_account', array( $this, 'ajax_delete_account' ) );
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function save_google_registration_source($user_id, $user_info) {
        update_user_meta($user_id, 'registration_source', 'google');

        if (isset($user_info['id'])) {
            update_user_meta($user_id, 'oauth_provider_id', sanitize_text_field($user_info['id']));
        }
    }

    public function save_github_registration_source($user_id, $user_info) {
        update_user_meta($user_id, 'registration_source', 'github');

        if (isset($user_info['id'])) {
            update_user_meta($user_id, 'oauth_provider_id', sanitize_text_field($user_info['id']));
        }
    }
    public function user_meta($user_id) {
        update_user_meta($user_id, 'newsletter', 1);

        $ip_data = $this->get_client_ip();
        update_user_meta($user_id, 'registration_ip', $ip_data['primary']);

        if (!empty($ip_data['ipv4'])) {
            update_user_meta($user_id, 'registration_ipv4', $ip_data['ipv4']);
        }
        if (!empty($ip_data['ipv6'])) {
            update_user_meta($user_id, 'registration_ipv6', $ip_data['ipv6']);
        }

        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        update_user_meta($user_id, 'registration_browser', sanitize_text_field($user_agent));

        $registration_time = current_time('mysql');
        update_user_meta($user_id, 'registration_time', $registration_time);

        $existing_source = get_user_meta($user_id, 'registration_source', true);
        if (empty($existing_source)) {
            if (is_admin() && current_user_can('create_users')) {
                update_user_meta($user_id, 'registration_source', 'admin_created');
            } else {
                update_user_meta($user_id, 'registration_source', 'wordpress_default');
            }
        }
    }

    private function get_client_ip() {
        $ip_keys = array(
                'HTTP_CF_CONNECTING_IP',
                'HTTP_X_REAL_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_CLIENT_IP',
                'REMOTE_ADDR'
        );

        $ipv4 = '';
        $ipv6 = '';

        foreach ($ip_keys as $key) {
            if (!isset($_SERVER[$key])) {
                continue;
            }

            $ip_list = $_SERVER[$key];

            if (strpos($ip_list, ',') !== false) {
                $ip_list = explode(',', $ip_list);
                $ip_list = trim($ip_list[0]);
            } else {
                $ip_list = trim($ip_list);
            }

            if (filter_var($ip_list, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipv4 = $ip_list;
                break;
            } elseif (filter_var($ip_list, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                if (empty($ipv6)) {
                    $ipv6 = $ip_list;
                }
            }
        }

        if (!empty($ipv4)) {
            $primary = $ipv4;
        } elseif (!empty($ipv6)) {
            $primary = $ipv6;
        } else {
            $primary = 'Unknown';
        }

        return array(
                'primary' => $primary,
                'ipv4'    => $ipv4,
                'ipv6'    => $ipv6
        );
    }

    public function add_custom_user_columns( $columns ) {
        $columns['registration_ip'] = 'IP';
        $columns['registration_browser'] = 'Browser';
        $columns['registration_source'] = 'Source';
        $columns['registration_time'] = 'Date';
        return $columns;
    }

    public function show_custom_user_column_content($value, $column_name, $user_id) {
        switch ($column_name) {
            case 'registration_source':
                $source = get_user_meta($user_id, 'registration_source', true);

                switch ($source) {
                    case 'google':
                        return '<span>Sign up with Google</span>';
                    case 'github':
                        return '<span>Sign up with GitHub</span>';
                    case 'wordpress_default':
                        return '<span>Sign up with WordPress</span>';
                    case 'admin_created':
                        return '<span>Create by Admin</span>';
                    default:
                        return '<span>—</span>';
                }

            case 'registration_ip':
                $primary_ip = get_user_meta($user_id, 'registration_ip', true);
                $ipv4 = get_user_meta($user_id, 'registration_ipv4', true);
                $ipv6 = get_user_meta($user_id, 'registration_ipv6', true);

                if (empty($primary_ip)) {
                    return '—';
                }

                if (!empty($ipv4) && !empty($ipv6)) {
                    return sprintf(
                            '<span title="IPv4: %s&#10;IPv6: %s" style="cursor: help; border-bottom: 1px dotted #999;">%s</span>',
                            esc_attr($ipv4),
                            esc_attr($ipv6),
                            esc_html($primary_ip)
                    );
                }

                return esc_html($primary_ip);

            case 'registration_browser':
                $user_agent = get_user_meta($user_id, 'registration_browser', true);

                if (empty($user_agent)) {
                    return '—';
                }

                return sprintf(
                        '<div style="max-width: 400px; word-break: break-all; font-size: 11px; font-family: monospace; line-height: 1.4;">%s</div>',
                        esc_html($user_agent)
                );

            case 'registration_time':
                $time = get_user_meta($user_id, 'registration_time', true);
                return $time ? date('d/m/Y H:i', strtotime($time)) : '—';
        }
        return $value;
    }

    private function parse_browser_name($user_agent) {
        $is_mobile = (strpos($user_agent, 'Mobile') !== false || strpos($user_agent, 'Android') !== false);
        $device = $is_mobile ? ' 📱' : ' 💻';

        if (strpos($user_agent, 'Edg') !== false || strpos($user_agent, 'Edge') !== false) {
            return 'Edge' . $device;
        } elseif (strpos($user_agent, 'Chrome') !== false && strpos($user_agent, 'Safari') !== false) {
            return 'Chrome' . $device;
        } elseif (strpos($user_agent, 'Firefox') !== false) {
            return 'Firefox' . $device;
        } elseif (strpos($user_agent, 'Safari') !== false) {
            return 'Safari' . $device;
        } elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR') !== false) {
            return 'Opera' . $device;
        } else {
            return 'Other' . $device;
        }
    }

    public function make_registration_columns_sortable( $columns ) {
        $columns['registration_source'] = 'registration_source';
        $columns['registration_time'] = 'registration_time';
        $columns['registration_ip'] = 'registration_ip';
        return $columns;
    }

    public function sort_registration_columns($query) {
        if (!is_admin()) {
            return;
        }

        $orderby = $query->get('orderby');

        if ('registration_source' === $orderby) {
            $query->set('meta_key', 'registration_source');
            $query->set('orderby', 'meta_value');
        }

        if ('registration_time' === $orderby) {
            $query->set('meta_key', 'registration_time');
            $query->set('orderby', 'meta_value');
        }

        if ('registration_ip' === $orderby) {
            $query->set('meta_key', 'registration_ip');
            $query->set('orderby', 'meta_value');
        }
    }

    public function add_admin_form_enctype() {
        echo ' enctype="multipart/form-data"';
    }

    public function add_admin_custom_field($user) {
        $avatar_id = get_user_meta($user->ID, 'lbv_avatar_attachment_id', true);
        $google_avatar = get_user_meta($user->ID, 'lbv_google_avatar', true);
        $github_avatar = get_user_meta($user->ID, 'lbv_github_avatar', true);
        $newsletter = get_user_meta($user->ID, 'lbv_newsletter', true);
        $lbv_post_editor = get_user_meta( $user->ID, 'lbv_post_editor', true );

        // CHỈ admin mới thấy thông tin registration
        $is_admin_viewing = current_user_can('edit_users');

        // Lấy thông tin registration (chỉ khi admin xem)
        if ($is_admin_viewing) {
            $reg_source = get_user_meta($user->ID, 'registration_source', true);
            $reg_ip = get_user_meta($user->ID, 'registration_ip', true);
            $reg_ipv4 = get_user_meta($user->ID, 'registration_ipv4', true);
            $reg_ipv6 = get_user_meta($user->ID, 'registration_ipv6', true);
            $reg_browser = get_user_meta($user->ID, 'registration_browser', true);
            $reg_time = get_user_meta($user->ID, 'registration_time', true);
        }
        ?>
        <style>
            .user-profile-picture {
                display: none !important;
            }
            <?php if ($is_admin_viewing) : ?>
            .lbv-reg-info-table {
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .lbv-reg-info-table th {
                width: 200px;
                font-weight: 600;
            }
            .lbv-reg-info-table code {
                background: #fff;
                padding: 2px 6px;
                border: 1px solid #ddd;
                border-radius: 3px;
                font-size: 12px;
            }
            .lbv-reg-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 4px;
                font-weight: 600;
                font-size: 13px;
            }
            .lbv-reg-badge.google {
                background: #e8f0fe;
                color: #1967d2;
            }
            .lbv-reg-badge.github {
                background: #f3f4f6;
                color: #24292f;
            }
            .lbv-reg-badge.wordpress {
                background: #e5f3ff;
                color: #0073aa;
            }
            .lbv-reg-badge.admin {
                background: #f0f0f1;
                color: #50575e;
            }
            <?php endif; ?>
        </style>

        <!-- Profile Picture Section -->
        <table class="form-table lbv-profile-picture-table" role="presentation">
            <tbody>
            <tr>
                <th><label for="profile_avatar"><?php _e('Profile Picture', 'lbv'); ?></label></th>
                <td>
                    <div class="lbv-admin-avatar-wrapper">
                        <div class="lbv-avatar-preview" style="margin-bottom: 15px;">
                            <?php
                            if ($avatar_id) {
                                $avatar_url = wp_get_attachment_image_url($avatar_id, array(96, 96));
                                if ($avatar_url) {
                                    echo '<img src="' . esc_url($avatar_url) . '" width="96" height="96" class="lbv-current-avatar" style="border-radius: 50%; object-fit: cover;">';
                                    echo '<p class="description" style="margin-top: 8px; color: #2271b1;">';
                                    echo '<strong>' . __('Custom uploaded avatar is active', 'lbv') . '</strong>';
                                    echo '</p>';
                                }
                            } elseif ($google_avatar) {
                                echo '<img src="' . esc_url($google_avatar) . '" width="96" height="96" class="lbv-current-avatar" style="border-radius: 50%; object-fit: cover;">';
                                echo '<p class="description" style="margin-top: 8px; color: #2271b1;">';
                                echo '<strong>' . __('Google avatar is active', 'lbv') . '</strong>';
                                echo '</p>';
                            } elseif ($github_avatar) {
                                echo '<img src="' . esc_url($github_avatar) . '" width="96" height="96" class="lbv-current-avatar" style="border-radius: 50%; object-fit: cover;">';
                                echo '<p class="description" style="margin-top: 8px; color: #2271b1;">';
                                echo '<strong>' . __('GitHub avatar is active', 'lbv') . '</strong>';
                                echo '</p>';
                            } else {
                                echo get_avatar($user->ID, 96);
                                echo '<p class="description" style="margin-top: 8px;">';
                                echo __('Using Gravatar (based on email)', 'lbv');
                                echo '</p>';
                            }
                            ?>
                        </div>

                        <div class="lbv-avatar-upload">
                            <input type="file" name="profile_avatar" id="profile_avatar" accept="image/*" style="display: block; margin-bottom: 8px;">
                            <p class="description">
                                <?php _e('Maximum file size: 2MB. Allowed types: JPG, PNG, GIF, WEBP, SVG', 'lbv'); ?>
                            </p>
                        </div>

                        <?php if ($avatar_id) : ?>
                            <div style="margin-top: 12px;">
                                <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="remove_avatar" value="1" style="margin: 0 8px 0 0;">
                                    <span><?php _e('Remove custom avatar', 'lbv'); ?></span>
                                </label>
                                <?php if ($google_avatar || $github_avatar) : ?>
                                    <p class="description" style="margin-top: 8px;">
                                        <?php
                                        if ($google_avatar) {
                                            _e('Google avatar will be shown after removal', 'lbv');
                                        } else {
                                            _e('GitHub avatar will be shown after removal', 'lbv');
                                        }
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($google_avatar && !$avatar_id) : ?>
                            <div style="margin-top: 12px; padding: 10px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                                <p style="margin: 0;">
                                    <?php
                                    printf(
                                            __('Upload a custom avatar to override your Google profile picture, or <a href="%s" target="_blank">update it on Google</a>.', 'lbv'),
                                            'https://myaccount.google.com/personal-info'
                                    );
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if ($github_avatar && !$avatar_id) : ?>
                            <div style="margin-top: 12px; padding: 10px; background: #f0f6fc; border-left: 4px solid #2271b1;">
                                <p style="margin: 0;">
                                    <?php
                                    printf(
                                            __('Upload a custom avatar to override your GitHub profile picture, or <a href="%s" target="_blank">update it on GitHub</a>.', 'lbv'),
                                            'https://github.com/settings/profile'
                                    );
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Registration Information Section -->
        <?php if ($is_admin_viewing && ($reg_source || $reg_ip || $reg_browser)) : ?>
            <h2><?php _e('Registration Information', 'lbv'); ?> <span style="color: #d63638; font-size: 13px;">(Admin Only)</span></h2>
            <table class="form-table lbv-reg-info-table" role="presentation">
                <tbody>
                <?php if ($reg_source) : ?>
                    <tr>
                        <th><?php _e('Registration Method', 'lbv'); ?></th>
                        <td>
                            <?php
                            switch ($reg_source) {
                                case 'google':
                                    echo '<span class="lbv-reg-badge google">🔵 Google OAuth</span>';
                                    break;
                                case 'github':
                                    echo '<span class="lbv-reg-badge github">⚫ GitHub OAuth</span>';
                                    break;
                                case 'wordpress_default':
                                    echo '<span class="lbv-reg-badge wordpress">🔷 WordPress Form</span>';
                                    break;
                                case 'admin_created':
                                    echo '<span class="lbv-reg-badge admin">👤 Created by Admin</span>';
                                    break;
                                default:
                                    echo '<span style="color: #999;">❓ Unknown</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if ($reg_time) : ?>
                    <tr>
                        <th><?php _e('Registration Date', 'lbv'); ?></th>
                        <td>
                            <code><?php echo date('d/m/Y H:i:s', strtotime($reg_time)); ?></code>
                            <span style="color: #666; margin-left: 10px;">
                        (<?php echo human_time_diff(strtotime($reg_time), current_time('timestamp')); ?> ago)
                    </span>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if ($reg_ip) : ?>
                    <tr>
                        <th><?php _e('IP Address', 'lbv'); ?></th>
                        <td>
                            <code><?php echo esc_html($reg_ip); ?></code>
                            <?php if ($reg_ipv4 && $reg_ipv6) : ?>
                                <br><small style="color: #666; margin-top: 5px; display: block;">
                                    <strong>IPv4:</strong> <code><?php echo esc_html($reg_ipv4); ?></code>
                                    &nbsp;|&nbsp;
                                    <strong>IPv6:</strong> <code><?php echo esc_html($reg_ipv6); ?></code>
                                </small>
                            <?php elseif ($reg_ipv4) : ?>
                                <br><small style="color: #666; margin-top: 5px; display: block;">
                                    <strong>Type:</strong> IPv4
                                </small>
                            <?php elseif ($reg_ipv6) : ?>
                                <br><small style="color: #666; margin-top: 5px; display: block;">
                                    <strong>Type:</strong> IPv6
                                </small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if ($reg_browser) : ?>
                    <tr>
                        <th><?php _e('User Agent', 'lbv'); ?></th>
                        <td>
                            <textarea readonly style="width: 100%; max-width: 600px; height: 70px; font-family: 'Courier New', monospace; font-size: 11px; resize: vertical; padding: 8px; background: #fff; border: 1px solid #ddd; border-radius: 3px;"><?php echo esc_textarea($reg_browser); ?></textarea>
                            <p class="description" style="margin-top: 8px;">
                                <?php
                                $browser_info = $this->parse_browser_name($reg_browser);
                                echo sprintf(__('Detected: %s', 'lbv'), '<strong>' . esc_html($browser_info) . '</strong>');
                                ?>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2><?php _e('Notifications', 'lbv'); ?></h2>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th>
                    <label for="user_newsletter"><?php esc_html_e('Newsletter', 'lbv'); ?></label>
                </th>
                <td>
                    <label for="user_newsletter" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="user_newsletter" id="user_newsletter" value="1"
                               <?php checked($newsletter, 1); ?>style="margin: 0;">
                        <span><?php esc_html_e('Sign up for our newsletter', 'lbv'); ?></span>
                    </label>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="user_newsletter"><?php esc_html_e('Post Editor', 'lbv'); ?></label>
                </th>
                <td>
                    <label for="lbv_post_editor" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="lbv_post_editor" id="lbv_post_editor" value="1"
                               <?php checked($lbv_post_editor, 1); ?>style="margin: 0;">
                        <span><?php esc_html_e('Disable edit notification emails', 'lbv'); ?></span>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>

        <?php
    }

    public function save_admin_custom_field( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        if ( isset( $_POST['user_newsletter'] ) && $_POST['user_newsletter'] == '1' ) {
            update_user_meta( $user_id, 'lbv_newsletter', 1 );
        } else {
            update_user_meta( $user_id, 'lbv_newsletter', 0 );
        }

        if ( isset( $_POST['lbv_post_editor'] ) && $_POST['lbv_post_editor'] == '1' ) {
            update_user_meta( $user_id, 'lbv_post_editor', 1 );
        } else {
            update_user_meta( $user_id, 'lbv_post_editor', 0 );
        }

        if ( isset( $_POST['remove_avatar'] ) && $_POST['remove_avatar'] == '1' ) {
            $this->remove_user_avatar( $user_id );
        }

        if ( ! empty( $_FILES['profile_avatar']['name'] ) ) {
            $avatar_result = $this->handle_avatar_upload( $user_id );

            if ( is_wp_error( $avatar_result ) ) {
                add_settings_error(
                    'lbv_avatar',
                    'avatar_upload_error',
                    $avatar_result->get_error_message(),
                    'error'
                );
            } else {
                add_settings_error(
                    'lbv_avatar',
                    'avatar_upload_success',
                    __( 'Profile picture updated successfully!', 'lbv' ),
                    'success'
                );
            }
        }
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( $hook !== 'profile.php' && $hook !== 'user-edit.php' ) {
            return;
        }

        wp_add_inline_script( 'jquery', '
            jQuery(document).ready(function($) {
                var lbvSection = $("#lbv-profile-picture-heading, .lbv-profile-picture-table");
                var aboutSection = $("#your-profile h2:contains(\"About Yourself\"), #your-profile h2:contains(\"About You\")").first();
                
                if (lbvSection.length && aboutSection.length) {
                    lbvSection.insertBefore(aboutSection);
                }
                
                $("#profile_avatar").on("change", function() {
                    var file = this.files[0];
                    if (file) {
     
                        if (file.size > 2097152) {
                            alert("' . esc_js( __( 'File size must be less than 2MB', 'lbv' ) ) . '");
                            $(this).val("");
                            return;
                        }
                        
                        var allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif", "image/svg", "image/webp"];
                        if (allowedTypes.indexOf(file.type) === -1) {
                            alert("' . esc_js( __( 'Only JPG, PNG, and GIF files are allowed', 'lbv' ) ) . '");
                            $(this).val("");
                            return;
                        }
                        
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $(".lbv-current-avatar").attr("src", e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $("input[name=remove_avatar]").on("change", function() {
                    if ($(this).is(":checked")) {
                        if (!confirm("' . esc_js( __( 'Are you sure you want to remove your custom avatar?', 'lbv' ) ) . '")) {
                            $(this).prop("checked", false);
                        }
                    }
                });
            });
        ' );
    }

    public function enqueue_assets() {
        if ( is_page( 'my-account' ) || is_page( 'tai-khoan' )) {
            wp_enqueue_style('lbv-profile', LBV_THEME_URI . 'assets/css/profile.css', array(), LBV_THEME_VERSION);
            wp_enqueue_script('lbv-profile', LBV_THEME_URI . 'assets/js/profile.min.js', array('jquery'), LBV_THEME_VERSION, true);
            wp_localize_script('lbv-profile', 'fup_ajax',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('fup_delete_account'),
                )
            );
        }
    }

    public function ajax_delete_account() {
        check_ajax_referer('fup_delete_account', 'nonce');

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array(
                    'message' => __( 'You must be logged in.', 'lbv' )
            ) );
        }

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $admin_email = get_option( 'admin_email' );

        if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
            wp_send_json_error( array(
                'message' => sprintf(__( 'Please contact %s for support.', 'lbv' ), $admin_email)
            ) );
        }

        $input_username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
        if ($input_username !== $current_user->user_login) {
            wp_send_json_error( array(
                    'message' => __( 'Username does not match.', 'lbv' )
            ) );
        }

        try {
            $this->delete_user_avatar( $user_id );
            $meta_keys = array(
                    'lbv_avatar_attachment_id',
                    'lbv_google_avatar',
                    'lbv_github_avatar',
                    'lbv_newsletter',
                    'lbv_post_editor',
                    'lbv_loved_reads',
                    'registration_source',
                    'registration_ip',
                    'registration_ipv4',
                    'registration_ipv6',
                    'registration_browser',
                    'registration_time',
                    'oauth_provider_id'
            );

            foreach ( $meta_keys as $key ) {
                delete_user_meta( $user_id, $key );
            }

            global $wpdb;
            $wpdb->update(
                    $wpdb->comments,
                    array(
                            'comment_author_email' => '',
                            'comment_author_IP' => '',
                            'comment_author_url' => '',
                    ),
                    array('user_id' => $user_id),
                    array('%s', '%s', '%s'),
                    array('%d')
            );

            require_once( ABSPATH . 'wp-admin/includes/user.php' );
            $result = wp_delete_user( $user_id, 2 );

            if ( $result ) {
                wp_clear_auth_cookie();
                wp_send_json_success( array(
                        'message' => __( 'Goodbye!.', 'lbv' )
                ) );
            } else {
                wp_send_json_error( array(
                        'message' => __( 'Failed to delete account.', 'lbv' )
                ) );
            }

        } catch ( Exception $e ) {
            wp_send_json_error( array(
                    'message' => __( 'An error occurred.', 'lbv' )
            ) );
        }
    }

    public function handle_profile_update() {
        if ( ! isset( $_POST['fup_update_profile'] ) ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        $current_user = wp_get_current_user();

        if ( ! isset( $_POST['fup_nonce'] ) || ! wp_verify_nonce( $_POST['fup_nonce'], 'fup_update_profile_' . $current_user->ID ) ) {
            $this->errors[] = __( 'Security check failed.', 'lbv' );
            return;
        }

        if ( ! empty( $_FILES['profile_avatar']['name'] ) ) {
            $avatar_result = $this->handle_avatar_upload( $current_user->ID );

            if ( is_wp_error( $avatar_result ) ) {
                $this->errors[] = $avatar_result->get_error_message();
            }
        }

        if ( isset( $_POST['remove_avatar'] ) && $_POST['remove_avatar'] == '1' ) {
            $this->remove_user_avatar( $current_user->ID );
        }

        if ( ! empty( $_POST['pass1'] ) && ! empty( $_POST['pass2'] ) ) {
            if ( $_POST['pass1'] === $_POST['pass2'] ) {
                wp_update_user( array(
                    'ID' => $current_user->ID,
                    'user_pass' => $_POST['pass1']
                ) );
            } else {
                $this->errors[] = __( 'Passwords do not match.', 'lbv' );
            }
        }

        if ( ! empty( $_POST['email'] ) ) {
            $email = sanitize_email( $_POST['email'] );

            if ( ! is_email( $email ) ) {
                $this->errors[] = __( 'Invalid email address.', 'lbv' );
            } else {
                $email_exists = email_exists( $email );
                if ( $email_exists && $email_exists != $current_user->ID ) {
                    $this->errors[] = __( 'Email address is already in use.', 'lbv' );
                } else {
                    wp_update_user( array(
                        'ID' => $current_user->ID,
                        'user_email' => $email
                    ) );
                }
            }
        }

        if ( isset( $_POST['url'] ) ) {
            wp_update_user( array(
                'ID' => $current_user->ID,
                'user_url' => esc_url_raw( $_POST['url'] )
            ) );
        }

        $meta_fields = array(
            'first_name' => 'first-name',
            'last_name' => 'last-name',
            'nickname' => 'nickname',
            'description' => 'description',
        );

        foreach ( $meta_fields as $meta_key => $post_key ) {
            if ( isset( $_POST[ $post_key ] ) ) {
                update_user_meta( $current_user->ID, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
            }
        }

        if ( ! empty( $_POST['display_name'] ) ) {
            wp_update_user( array(
                'ID' => $current_user->ID,
                'display_name' => sanitize_text_field( $_POST['display_name'] )
            ) );
        }

        if ( isset( $_POST['user_newsletter'] ) && $_POST['user_newsletter'] == '1' ) {
            update_user_meta( $current_user->ID, 'lbv_newsletter', 1 );
        } else {
            update_user_meta( $current_user->ID, 'lbv_newsletter', 0 );
        }

        if ( isset( $_POST['lbv_post_editor'] ) && $_POST['lbv_post_editor'] == '1' ) {
            update_user_meta( $current_user->ID, 'lbv_post_editor', 1 );
        } else {
            update_user_meta( $current_user->ID, 'lbv_post_editor', 0 );
        }

        if ( empty( $this->errors ) ) {
            do_action( 'fup_profile_updated', $current_user->ID );
            wp_safe_redirect( add_query_arg( 'profile_updated', 'true', wp_get_referer() ) );
            exit;
        }
    }

    public function render_profile_form( $atts ) {
        if ( ! is_user_logged_in() ) {
            return '<p class="must-log-in">'
                . sprintf(
                    __( 'You must be <a rel="nofollow" href="%s">logged in</a> to view this page.', 'lbv' ),
                    esc_url( wp_login_url( get_permalink() ) )
                )
                . '</p>';
        }

        ob_start();
        include LBV_THEME_DIR . 'templates/profile/form.php';
        return ob_get_clean();
    }

    public function get_display_name_options( $user ) {
        $public_display = array();
        $public_display['display_nickname'] = $user->nickname;
        $public_display['display_username'] = $user->user_login;

        if ( ! empty( $user->first_name ) ) {
            $public_display['display_firstname'] = $user->first_name;
        }

        if ( ! empty( $user->last_name ) ) {
            $public_display['display_lastname'] = $user->last_name;
        }

        if ( ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {
            $public_display['display_firstlast'] = $user->first_name . ' ' . $user->last_name;
            $public_display['display_lastfirst'] = $user->last_name . ' ' . $user->first_name;
        }

        if ( ! in_array( $user->display_name, $public_display ) ) {
            $public_display = array( 'display_displayname' => $user->display_name ) + $public_display;
        }

        return array_unique( array_map( 'trim', $public_display ) );
    }

    private function handle_avatar_upload( $user_id ) {
        if ( ! isset( $_FILES['profile_avatar'] ) || $_FILES['profile_avatar']['error'] !== UPLOAD_ERR_OK ) {
            return new WP_Error( 'upload_error', __( 'File upload failed.', 'lbv' ) );
        }

        $file = $_FILES['profile_avatar'];

        $max_size = 2 * 1024 * 1024;
        if ( $file['size'] > $max_size ) {
            return new WP_Error( 'file_too_large', __( 'File size must be less than 2MB.', 'lbv' ) );
        }

        $allowed_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml' );

        if ( ! in_array( $file['type'], $allowed_types ) ) {
            return new WP_Error( 'invalid_file_type', __( 'Only JPG, PNG, Webp, and GIF files are allowed.', 'lbv' ) );
        }

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        $upload_overrides = array(
            'test_form' => false,
            'mimes'     => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif'          => 'image/gif',
                'png'          => 'image/png',
                'webp'         => 'image/webp',
                'svg'         =>  'image/svg+xml',
            )
        );

        $uploaded_file = wp_handle_upload( $file, $upload_overrides );

        if ( isset( $uploaded_file['error'] ) ) {
            return new WP_Error( 'upload_error', $uploaded_file['error'] );
        }

        $attachment = array(
            'post_mime_type' => $uploaded_file['type'],
            'post_title'     => sanitize_file_name( pathinfo( $uploaded_file['file'], PATHINFO_FILENAME ) ),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_author'    => $user_id
        );

        $attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );

        if ( is_wp_error( $attach_id ) ) {
            return $attach_id;
        }

        $attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        $old_avatar_id = get_user_meta( $user_id, 'lbv_avatar_attachment_id', true );
        if ( $old_avatar_id ) {
            wp_delete_attachment( $old_avatar_id, true );
        }

        update_user_meta( $user_id, 'lbv_avatar_attachment_id', $attach_id );

        return $attach_id;
    }

    private function remove_user_avatar( $user_id ) {
        $avatar_id = get_user_meta( $user_id, 'lbv_avatar_attachment_id', true );
        if ( $avatar_id ) {
            wp_delete_attachment( $avatar_id, true );
            delete_user_meta( $user_id, 'lbv_avatar_attachment_id' );
        }
    }

    public function custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
        $user_id = 0;

        // Kiểm tra object TRƯỚC, tránh truyền object vào is_email()
        if ( is_object( $id_or_email ) ) {
            if ( $id_or_email instanceof WP_Comment ) {
                // Comment từ user đã đăng ký
                if ( ! empty( $id_or_email->user_id ) ) {
                    $user_id = (int) $id_or_email->user_id;
                }
                // Comment từ guest không có user_id, return avatar gốc
            } elseif ( $id_or_email instanceof WP_User ) {
                $user_id = $id_or_email->ID;
            } elseif ( $id_or_email instanceof WP_Post ) {
                $user_id = $id_or_email->post_author;
            }
        } elseif ( is_numeric( $id_or_email ) ) {
            $user_id = (int) $id_or_email;
        } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
            // CHỈ gọi is_email() khi đã chắc chắn là string
            $user = get_user_by( 'email', $id_or_email );
            $user_id = $user ? $user->ID : 0;
        }

        if ( ! $user_id ) {
            return $avatar;
        }

        $avatar_id = get_user_meta( $user_id, 'lbv_avatar_attachment_id', true );

        if ( $avatar_id ) {
            $avatar_url = wp_get_attachment_image_url( $avatar_id, array( $size, $size ) );

            if ( $avatar_url ) {
                return sprintf(
                    '<img src="%s" alt="%s" width="%d" height="%d" class="avatar avatar-custom" style="border-radius: 50%%; object-fit: cover;" />',
                    esc_url( $avatar_url ),
                    esc_attr( $alt ),
                    (int) $size,
                    (int) $size
                );
            }
        }

        $google_avatar = get_user_meta( $user_id, 'lbv_google_avatar', true );

        if ( $google_avatar ) {
            return sprintf(
                '<img src="%s" alt="%s" width="%d" height="%d" class="avatar avatar-google" style="border-radius: 50%%; object-fit: cover;" />',
                esc_url( $google_avatar ),
                esc_attr( $alt ),
                (int) $size,
                (int) $size
            );
        }

        return $avatar;
    }

    public function custom_avatar_url( $url, $id_or_email, $args ) {
        $user_id = 0;

        // Xử lý đầy đủ các kiểu dữ liệu
        if ( is_object( $id_or_email ) ) {
            if ( $id_or_email instanceof WP_Comment ) {
                if ( ! empty( $id_or_email->user_id ) ) {
                    $user_id = (int) $id_or_email->user_id;
                }
                // Comment từ guest không có user_id, bỏ qua
            } elseif ( $id_or_email instanceof WP_User ) {
                $user_id = $id_or_email->ID;
            } elseif ( $id_or_email instanceof WP_Post ) {
                $user_id = $id_or_email->post_author;
            }
        } elseif ( is_numeric( $id_or_email ) ) {
            $user_id = (int) $id_or_email;
        } elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
            $user = get_user_by( 'email', $id_or_email );
            $user_id = $user ? $user->ID : 0;
        }

        if ( ! $user_id ) {
            return $url;
        }

        $avatar_id = get_user_meta( $user_id, 'lbv_avatar_attachment_id', true );

        if ( $avatar_id ) {
            $size = isset( $args['size'] ) ? $args['size'] : 96;
            $avatar_url = wp_get_attachment_image_url( $avatar_id, array( $size, $size ) );

            if ( $avatar_url ) {
                return esc_url( $avatar_url );
            }
        }

        $google_avatar = get_user_meta( $user_id, 'lbv_google_avatar', true );

        return $google_avatar ? esc_url( $google_avatar ) : $url;
    }

    public function delete_user_avatar( $user_id ) {
        $avatar_id = get_user_meta( $user_id, 'lbv_avatar_attachment_id', true );
        if ( $avatar_id ) {
            wp_delete_attachment( $avatar_id, true );
        }
    }
}

LBV_USER_Profile::get_instance();
