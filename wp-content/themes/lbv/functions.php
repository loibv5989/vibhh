<?php

defined( 'ABSPATH' ) || exit;

define( 'LBV_THEME_VERSION', '26.5.20' );
define( 'LBV_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'LBV_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );

define( 'LBV_SOCIAL_ENABLE',  0 );
define( 'LBV_SOCIAL_FACEBOOK',  '' );
define( 'LBV_SOCIAL_X',         '' );
define( 'LBV_SOCIAL_TIKTOK',    '' );
define( 'LBV_SOCIAL_YOUTUBE',   '' );
define( 'LBV_SOCIAL_THREADS',   '' );
define( 'LBV_SOCIAL_INSTAGRAM', '' );

define( 'LBV_POSTS_PER_PAGE',        15 );
define( 'LBV_HOME_POSTS_PER_PAGE',   10 );
define( 'LBV_TAG_LOAD_MORE',         0 );
define( 'LBV_CATEGORY_LOAD_MORE',    0 );
define( 'LBV_SEARCH_LOAD_MORE',      0 );

function lbv_theme_setup() {
    register_nav_menus(array(
        'primary'       => __('Primary Menu', 'lbv'),
        'footer-menu-1' => __('Footer Menu 1', 'lbv'),
        'footer-menu-2' => __('Footer Menu 2', 'lbv')
    ));

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails', array('post', 'page'));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));

    load_theme_textdomain(
        'lbv',
        get_template_directory() . '/languages'
    );
}
add_action('after_setup_theme', 'lbv_theme_setup');

function lbv_theme_activated() {
    require_once get_template_directory() . '/includes/contact-table.php';
    LBV_Contact_Installer::install();
}
add_action('after_switch_theme', 'lbv_theme_activated');

function enqueue_scripts() {
    wp_enqueue_style('lbv', LBV_THEME_URI . 'assets/css/main.css', array(), LBV_THEME_VERSION, 'all');
    wp_enqueue_script('lbv', LBV_THEME_URI . 'assets/js/main.min.js', array('jquery'), LBV_THEME_VERSION, true);
    wp_localize_script('lbv', 'lbvMain', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'login_url' => site_url('/wp-login.php'),
        'is_user_logged_in' => is_user_logged_in()
    ));

    if (is_singular(['idol', 'group', 'photo', 'actor', 'v_star', 'post'])) {
        wp_enqueue_style('lbv-post', LBV_THEME_URI . 'assets/css/post.css', array(), LBV_THEME_VERSION, 'all');
        wp_enqueue_script('lbv-post', LBV_THEME_URI . 'assets/js/post.min.js', array('jquery'), LBV_THEME_VERSION, true);

        $current_user = wp_get_current_user();

        wp_localize_script('lbv-post', 'lbvPost', array(
            'is_admin' => current_user_can('manage_options'),
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_logged_in' => is_user_logged_in(),
            'current_user' => [
                'name' => $current_user->display_name,
                'avatar' => get_avatar_url($current_user->ID, ['size' => 32]),
                'id' => $current_user->ID
            ]
        ));

        wp_enqueue_script('lbv-popup', LBV_THEME_URI . 'assets/js/popup.min.js', array('jquery'), LBV_THEME_VERSION, true);

    }

    if ((is_singular('page') && !is_front_page()) || is_404()) {
        wp_enqueue_style('lbv-page', LBV_THEME_URI . 'assets/css/page.css', array(), LBV_THEME_VERSION, 'all');

        if (is_singular('page') && !is_front_page()) {
            $current_user = wp_get_current_user();
            wp_enqueue_script('lbv-page', LBV_THEME_URI . 'assets/js/page.min.js', array('jquery'), LBV_THEME_VERSION, true);
            wp_localize_script('lbv-page', 'lbvPost', array(
                'is_admin'    => current_user_can('manage_options'),
                'ajax_url'    => admin_url('admin-ajax.php'),
                'is_logged_in' => is_user_logged_in(),
                'current_user' => array(
                    'name'   => $current_user->display_name,
                    'avatar' => get_avatar_url($current_user->ID, array('size' => 32)),
                    'id'     => $current_user->ID,
                ),
            ));
        }
    }

    if (is_author()) {
        wp_enqueue_style('lbv-author', LBV_THEME_URI . 'assets/css/author.css', array(), LBV_THEME_VERSION);
    }

    if (!is_singular(['post']) && !is_author() && !is_front_page() && !is_page()) {
        wp_enqueue_script('lbv-archive', LBV_THEME_URI . 'assets/js/archive.min.js', array('jquery'), LBV_THEME_VERSION, true);

        wp_localize_script('lbv-archive', 'lbvArchive', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_scripts', 10);

function lbv_load_theme_files() {
    static $loaded = false;

    if ($loaded) return;

    $base_dir = get_template_directory();

    $files = [
        'includes/theme-settings.php',
        'includes/menu-header.php',
        'includes/menu-footer.php',
        'includes/toc-nav.php',
        'includes/walker-comment.php',
        'templates/ajax/load-posts.php',
        'templates/ajax/live-search.php',
        'templates/ajax/load-sidebar.php',
        'templates/ajax/comments.php',
        'templates/contact.php',
        'templates/login.php',
        'templates/login/github.php',
        'templates/login/google.php',
        'templates/profile.php',
        'backend/login.php',
        'backend/users.php',
        'includes/robots.php',
        'includes/svg.php',
        'backend/cron/remove-image-size-150.php',
        'backend/lock-modified-date.php'
    ];

    foreach ($files as $file) {
        $path = $base_dir . '/' . ltrim($file, '/');
        if (file_exists($path)) {
            require_once $path;
        } elseif (WP_DEBUG) {
            error_log("LBV Theme: Missing file - {$file}");
        }
    }

    $loaded = true;
}

lbv_load_theme_files();

if (is_admin()) {
    $admin_files = [
        'backend/admin.php',
        'backend/settings.php',
        'backend/contact.php',
        'backend/thumbnail.php',
        'backend/post-cleaner.php',
    ];

    foreach ($admin_files as $file) {
        $path = get_template_directory() . '/' . $file;
        if (file_exists($path)) {
            require_once $path;
        }
    }

    if (class_exists('LBV_Admin')) {
        new LBV_Admin();
    }
}
