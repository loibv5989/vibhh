<?php

if (!defined('ABSPATH')) exit;

define( 'LBV_CDN_URL', 'https://cdn.nbblo.com');

add_filter('wp_mail_from', function($from_email) {
    return 'contact@nbblo.com';
});

add_filter('wp_mail_from_name', function($from_name) {
    return 'nbblo.com';
});

function is_dev() {
    $manager = current_user_can('manage_options');
    $IP = array('127.0.0.1', '127.0.1.1', 'localhost');

    if ($manager || in_array($_SERVER['REMOTE_ADDR'], $IP)) {
        return true;
    }

    return false;
}

function lbv_load_files() {
    static $loaded = false;

    if ($loaded) return;

    $base_dir = get_stylesheet_directory();

    $files = [
        '/includes/image-optimizer.php',
//        '/includes/cdn-rewriter.php',
        '/includes/webp-picture-wrapper.php',
        '/includes/optimization.php',
        '/includes/analytics.php',
//        '/includes/clear-cache.php',
    ];

    foreach ($files as $file) {
        $path = $base_dir . '/' . ltrim($file, '/');

        if (file_exists($path)) {
            require_once $path;
        }
    }

    $loaded = true;
}

lbv_load_files();

add_action('template_redirect', function() {
    if ((is_front_page() || is_home()) && is_paged()) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
}, 1);

function pwa_manifest() {
    if (is_admin()) return;
    echo '<link rel="manifest" href="/manifest.json">' ."\n";
    echo '<meta name="theme-color" content="#ffffff">' ."\n";
}
add_action('wp_head', 'pwa_manifest');

function register_sw() {
    if (is_admin()) return;
    ?>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js?v=nbblo-v1', { scope: '/' });
            });
        }
    </script>
    <?php
}
add_action('wp_footer', 'register_sw');
