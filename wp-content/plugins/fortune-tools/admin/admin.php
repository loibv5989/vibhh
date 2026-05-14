<?php

if (!defined('ABSPATH')) {
    exit;
}

class Fortune_Admin {

    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settings_link']);
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_fortune-settings' !== $hook) {
            return;
        }

        wp_enqueue_style( 'ftn', FORTUNE_TOOLS_PLUGIN_URL . 'admin/assets/css/ftn.css', [], FORTUNE_TOOLS_VERSION );
        wp_enqueue_script( 'ftn', FORTUNE_TOOLS_PLUGIN_URL . 'admin/assets/js/ftn.js', ['jquery'], FORTUNE_TOOLS_VERSION, true );

        wp_localize_script('ftn', 'ftnAdmin', [
            'nonce' => wp_create_nonce('fortune_test_nonce')
        ]);
    }

    public function settings_link($links) {
        $settings_link = '<a href="admin.php?page=fortune-settings">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings_page() {
        add_menu_page(
                'Fortune Tools',
                'Fortune Tools',
                'manage_options',
                'fortune-settings',
                [$this, 'render_settings_page'],
                'dashicons-admin-generic',
                60
        );
    }

    public function render_settings_page() {

        ?>
        <div class="wrap">
            <h1>Fortune Manager</h1>
        </div>

        <?php
    }
}
