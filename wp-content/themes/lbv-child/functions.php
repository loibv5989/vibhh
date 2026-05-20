<?php

if (!defined('ABSPATH')) exit;

define( 'LBV_CDN_URL', 'https://cdn.vibhh.com');

add_filter('wp_mail_from', function($from_email) {
    return 'contact@vibhh.com';
});

add_filter('wp_mail_from_name', function($from_name) {
    return 'Vibhh';
});

add_filter('wpseo_debug_markers', '__return_false');
add_filter('Yoast\WP\SEO\post_redirect_slug_change', '__return_true');
add_filter('Yoast\WP\SEO\term_redirect_slug_change', '__return_true');

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
        '/includes/cdn-rewriter.php',
        '/includes/webp-picture-wrapper.php',
        '/includes/optimization.php',
        '/includes/analytics.php',
        '/includes/clear-cache.php',
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
    if (is_dev()) return;
    echo '<link rel="manifest" href="/manifest.json">' ."\n";
    echo '<meta name="theme-color" content="#ffffff">' ."\n";
}
add_action('wp_head', 'pwa_manifest');

function register_sw() {
    if (is_dev()) return;
    $bots = ['googlebot', 'google-inspectiontool', 'bingbot', 'yandex', 'baidu', 'slurp', 'duckduckbot',
        'facebookexternalhit', 'twitterbot', 'applebot', 'semrush', 'ahrefs',
        'prerender', 'headless'];

    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

    foreach ($bots as $bot) {
        if (strpos($user_agent, $bot) !== false) {
            return;
        }
    }
    ?>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' });
            });
        }
    </script>
    <?php
}
add_action('wp_footer', 'register_sw');
