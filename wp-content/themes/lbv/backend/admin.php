<?php

defined('ABSPATH') ||  exit;

class LBV_Admin {

    private static $instance = null;
    private $lbv_settings;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 99);
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'), 99);
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('wp_generate_attachment_metadata', array($this, 'add_svg_dimensions'), 10, 2);
    }
    public function enqueue_block_editor_assets() {
        wp_enqueue_style('lbv-main', LBV_THEME_URI . 'backend/assets/css/editor.css', array(), LBV_THEME_VERSION);
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function lbv_settings() {
        if ($this->lbv_settings === null) {
            $this->lbv_settings = LBV_Settings::get_instance();
        }
        return $this->lbv_settings;
    }

    public function add_svg_dimensions($data, $id) {
        $attachment = get_post($id);
        $mime_type = $attachment->post_mime_type;

        if ($mime_type == 'image/svg+xml') {
            $svg_path = get_attached_file($id);
            $svg = simplexml_load_file($svg_path);

            if ($svg !== false) {
                $attributes = $svg->attributes();
                $width = (string) $attributes->width;
                $height = (string) $attributes->height;

                if (empty($width) || empty($height)) {
                    $viewBox = explode(' ', $attributes->viewBox);
                    $width = isset($viewBox[2]) ? $viewBox[2] : null;
                    $height = isset($viewBox[3]) ? $viewBox[3] : null;
                }

                $data['width'] = intval($width);
                $data['height'] = intval($height);
            }
        }

        return $data;
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'appearance_page_lbv-options') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('lbv-main', LBV_THEME_URI . 'backend/assets/css/main.css', array(), LBV_THEME_VERSION);
        wp_enqueue_script('lbv-main', LBV_THEME_URI . 'backend/assets/js/main.js', array('jquery'), LBV_THEME_VERSION, true);

        wp_localize_script('lbv-main', 'lbvAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('lbv_admin_nonce'),
        ));
    }

    public function add_admin_menu() {
        add_submenu_page(
                'themes.php',
                'LBV Options',
                'LBV theme',
                'manage_options',
                'lbv-options',
                array($this, 'render_admin_page')
        );
    }

    public function register_settings() {
        register_setting('lbv_options', 'lbv_options');
    }

    public function render_admin_page() {
        $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'lbv-options';

        if ($current_page === 'lbv-options') {
            $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'theme-options';
        } else {
            $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';
        }

        $current_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'default-logos';
        ?>
        <div class="lbv-admin-wrapper">
            <?php $this->render_top_navigation($current_page, $current_tab); ?>
            <div class="lbv-content-wrapper">
                <?php
                if ($current_tab === 'theme-options') {
                    $this->render_sidebar($current_page, $current_tab, $current_section);
                }
                ?>
                <div class="lbv-main-content">
                    <?php $this->render_content($current_page, $current_tab, $current_section); ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_top_navigation($current_page, $current_tab) {
        $tabs = $this->get_top_tabs();
        ?>
        <div class="lbv-top-nav">
            <div class="lbv-top-tabs">
                <?php foreach ($tabs as $tab_key => $tab): ?>
                    <a href="<?php echo esc_url(add_query_arg(array('page' => $current_page, 'tab' => $tab_key))); ?>"
                       class="lbv-tab <?php echo $current_tab === $tab_key ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-<?php echo esc_attr($tab['icon']); ?>"></span>
                        <?php echo esc_html($tab['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    private function get_top_tabs() {
        return array(
                'theme-options' => array(
                        'label' => 'Theme Options',
                        'icon' => 'admin-settings',
                ),
                'system-info' => array(
                        'label' => 'System Info',
                        'icon' => 'info',
                ),
        );
    }

    private function render_sidebar($current_page, $current_tab, $current_section) {
        $sections = $this->get_sidebar_sections();
        ?>
        <div class="lbv-sidebar">
            <?php foreach ($sections as $section_key => $section): ?>
                <div class="lbv-sidebar-section">
                    <div class="lbv-section-title" data-section="<?php echo esc_attr($section_key); ?>">
                        <span class="dashicons dashicons-<?php echo esc_attr($section['icon']); ?>"></span>
                        <?php echo esc_html($section['label']); ?>
                    </div>
                    <?php if (!empty($section['subsections'])): ?>
                        <div class="lbv-submenu <?php echo $section_key === 'logo' ? 'active' : ''; ?>">
                            <?php foreach ($section['subsections'] as $subsection_key => $subsection): ?>
                                <a href="<?php echo esc_url(add_query_arg(array(
                                        'page' => $current_page,
                                        'tab' => $current_tab,
                                        'section' => $subsection_key
                                ))); ?>"
                                   class="<?php echo $current_section === $subsection_key ? 'active' : ''; ?>">
                                    <span class="dashicons dashicons-<?php echo esc_attr($subsection['icon']); ?>"></span>
                                    <?php echo esc_html($subsection['label']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function get_sidebar_sections() {
        return array(
                'logo' => array(
                        'label' => 'Logo',
                        'icon' => 'format-image',
                        'subsections' => array(
                                'default-logos' => array(
                                        'label' => 'Default Logos',
                                        'icon' => 'images-alt2',
                                ),
                                'mobile-logos' => array(
                                        'label' => 'Mobile Logos',
                                        'icon' => 'smartphone',
                                ),
                        ),
                ),
                'oauth' => array(
                        'label' => 'User Login',
                        'icon' => 'admin-network',
                        'subsections' => array(
                                'oauth-credentials' => array(
                                        'label' => 'OAuth Credentials',
                                        'icon' => 'lock',
                                ),
                        ),
                ),
        );
    }

    private function render_content($current_page, $current_tab, $current_section) {
        if ($current_tab === 'theme-options') {
            switch ($current_section) {
                case 'default-logos':
                    $this->lbv_settings()->render_logo_settings();
                    break;

                case 'mobile-logos':
                    $this->lbv_settings()->render_mobile_logo_settings();
                    break;

                case 'oauth-credentials':
                    $this->lbv_settings()->render_oauth_settings();
                    break;

                default:
                    $this->render_welcome_screen();
                    break;
            }
        } elseif ($current_tab === 'system-info') {
            $this->lbv_settings()->render_system_info();
        } else {
            $this->render_welcome_screen();
        }
    }

    private function render_welcome_screen() {
        ?>
        <div class="lbv-content-box">
            <h1>Welcome to Lbv Theme Options</h1>
            <p>Select an option from the sidebar to get started.</p>
        </div>
        <?php
    }
}

LBV_Admin::get_instance();