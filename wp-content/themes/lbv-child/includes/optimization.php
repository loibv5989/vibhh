<?php

/**
 * WordPress Optimization & Cleanup
 */

if (!defined('ABSPATH')) exit;

// ============================================================================
// 1. REMOVE UNNECESSARY WP HEAD TAGS
// ============================================================================

remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');


// ============================================================================
// 2. DISABLE REST API LINKS & HEADERS (BUT ALLOW CUSTOM ENDPOINTS)
// ============================================================================

remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);

add_filter('rest_output_link_header', '__return_false', 11);
add_filter('rest_output_link_wp_head', '__return_false', 11);

add_filter('rest_authentication_errors', function ($result) {

    if (!empty($result)) {
        return $result;
    }

    $current_route = '';
    if (isset($_SERVER['REQUEST_URI'])) {
        $current_route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    $allowed_routes = [
        '/wp-json/wp/v2/',
        '/wp-json/nrgy/v1/calculate',
        '/wp-json/nrgy/v1/analyze',
        '/wp-json/zdc/v1/calc',
        '/wp-json/zdc/v1/love',
        '/wp-json/zdc/v1/love-analyze',
        '/wp-json/zdc/v1/tuvi',
        '/wp-json/zdc/v1/tuvi-analyze',
        '/wp-json/zdc/v1/natal',
        '/wp-json/zdc/v1/natal-analyze',
        '/wp-json/iching/v1/draw',
        '/wp-json/iching/v1/analyze',
        '/wp-json/tarot/v1/draw',
        '/wp-json/tarot/v1/analyze',
        '/wp-json/tarot/v1/reveal',
        '/wp-json/tuvi/v1/calculate',
        '/wp-json/tuvi/v1/analyze',
        '/wp-json/tuvi/v1/ntx',
        '/wp-json/tuvi/v1/hoptuoi',
        '/wp-json/western/v1/draw',
        '/wp-json/western/v1/analyze',
        '/wp-json/western/v1/reveal',
        '/wp-json/oracle/v1/draw',
        '/wp-json/oracle/v1/analyze',
        '/wp-json/tsh-love/v1/calculate',
        '/wp-json/tsh-love/v1/analyze',
        '/wp-json/mbti/v1/calculate',
        '/wp-json/mbti/v1/analyze',
        '/wp-json/tsh-phone/v1/calculate',
        '/wp-json/tsh-phone/v1/analyze',
        '/wp-json/battu/v1/calculate',
        '/wp-json/battu/v1/analyze',
        '/wp-json/battu/v1/send-support'
    ];

    foreach ($allowed_routes as $allowed) {
        if (strpos($current_route, $allowed) !== false) {
            return $result;
        }
    }

    if (!is_user_logged_in()) {
        return new WP_Error('rest_disabled', __('REST API is disabled.', 'lbv'),
            ['status' => 403]
        );
    }

    return $result;
});


// ============================================================================
// 3. OPTIMIZE EMBEDS
// ============================================================================

remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

add_filter('embed_oembed_html', function($html, $url, $attr, $post_id) {
    if (strpos($html, '<iframe') !== false) {
        $html = str_replace('<iframe', '<iframe loading="lazy"', $html);
    }
    return $html;
}, 10, 4);


// ============================================================================
// 4. DISABLE SHORTLINKS
// ============================================================================

remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('template_redirect', 'wp_shortlink_header');
add_filter('pre_get_shortlink', '__return_empty_string');


// ============================================================================
// 5. DISABLE XML-RPC
// ============================================================================

add_filter('xmlrpc_enabled', '__return_false');
add_filter('pings_open', '__return_false');
add_filter('pre_update_option_default_pingback_flag', '__return_false');

add_filter('xmlrpc_methods', function($methods) {
    unset($methods['pingback.ping']);
    return $methods;
});

add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});


// ============================================================================
// 6. DISABLE EMOJIS
// ============================================================================

add_action('init', 'lbv_disable_emojis');

function lbv_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'lbv_disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'lbv_disable_emojis_remove_dns_prefetch', 10, 2);
}

function lbv_disable_emojis_tinymce($plugins) {
    return is_array($plugins) ? array_diff($plugins, array('wpemoji')) : array();
}

function lbv_disable_emojis_remove_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' == $relation_type) {
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
        $urls = array_diff($urls, array($emoji_svg_url));
    }
    return $urls;
}


// ============================================================================
// 7. DISABLE ALL BLOCK STYLES
// ============================================================================

add_action('init', 'lbv_remove_global_styles_actions', 1);
function lbv_remove_global_styles_actions() {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
}

function lbv_remove_global_styles_complete() {
    if (is_admin() || is_customize_preview()) return;

    $wp69_blocks = [
        'wp-block-gallery','wp-block-heading','wp-block-image','wp-block-paragraph',
        'wp-block-archives','wp-block-audio','wp-block-breadcrumbs','wp-block-button',
        'wp-block-calendar','wp-block-categories','wp-block-code','wp-block-columns',
        'wp-block-avatar','wp-block-details','wp-block-embed','wp-block-file',
        'wp-block-post-comments','wp-block-comments','wp-block-comment-template'
    ];

    foreach ($wp69_blocks as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }

    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('core-block-supports');

    $core_styles = ['global-styles','wp-block-library','wp-block-library-theme',
        'classic-theme-styles','core-block-supports'];

    foreach ($core_styles as $handle) {
        wp_deregister_style($handle);
    }

    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
        wp_deregister_style('dashicons');
    }

    if (!current_user_can('administrator')) {
        wp_deregister_script('wp-i18n');
        wp_dequeue_script('wp-i18n');
        remove_filter('script_loader_tag', 'wp_get_script_translations', 20);
    }
}

add_action('wp_enqueue_scripts','lbv_remove_global_styles_complete',999);
add_action('wp_footer','lbv_remove_global_styles_complete',999);


// ============================================================================
// 8. HTML BUFFER CLEANUP
// ============================================================================

add_action('template_redirect', 'lbv_html_cleanup_buffer');
function lbv_html_cleanup_buffer() {
    if (is_admin() || is_404() || wp_doing_ajax()) return;
    ob_start('lbv_remove_block_css');
}

function lbv_remove_block_css($html) {
    $html = preg_replace('/<style id=[\'"]?(global-styles|core-block-supports|wp-block-library)-inline-css[\'"]?[^>]*>.*?<\/style>/is', '', $html);
    $html = preg_replace('/<link[^>]*wp-block-[^>]*\/>/i', '', $html);
    $html = preg_replace('/<link[^>]*id=[\'"]?(wp-block-library|global-styles|classic-theme-styles)[\'"]?[^>]*\/>/i', '', $html);
    return $html;
}


// ============================================================================
// 9. OPTIMIZE SCRIPTS & STYLES LOADING
// ============================================================================

add_filter('wp_default_scripts', 'lbv_remove_jquery_migrate');

function lbv_remove_jquery_migrate($scripts) {
    if (empty($scripts->registered['jquery']) || is_admin()) {
        return;
    }
    $deps = &$scripts->registered['jquery']->deps;
    $deps = array_diff($deps, ['jquery-migrate']);
}

add_action('wp_enqueue_scripts', 'lbv_delay_all_scripts', 999);

function lbv_delay_all_scripts() {

    if (is_admin() || current_user_can('administrator')) return;

    add_filter('script_loader_tag', function($tag, $handle, $src) {
        if (is_admin()) return $tag;
        $excluded_handles = ['adsense', 'analytics'];
        if (in_array($handle, $excluded_handles)) {
            return $tag;
        }
        if (strpos($tag, ' defer') === false && strpos($tag, ' src=') !== false) {
            $tag = str_replace('<script ', '<script defer ', $tag);
        }
        return $tag;

    }, 10, 3);
}


// ============================================================================
// 10. OPTIMIZE INLINE STYLES
// ============================================================================

add_filter('should_load_block_supports_inline_styles', '__return_false');
add_filter('styles_inline_size_limit', '__return_zero');

// ============================================================================
// 11. CLEANUP BLOCK HTML OUTPUT
// ============================================================================

add_filter('render_block', 'lbv_aggressive_html_cleanup', 10, 2);

function lbv_aggressive_html_cleanup($block_content, $block) {
    if (empty($block['blockName']) || strpos($block['blockName'], 'core/') !== 0) {
        return $block_content;
    }

    $block_content = trim($block_content);
    $block_content = preg_replace('/>\s+</', '><', $block_content);
    return $block_content;
}

add_filter('rank_math/researches/tests', function ($tests, $type) {
    unset($tests['hasContentAI']);
    return $tests;
}, 10, 2);


// ============================================================================
// 12. DISABLE ALL FEEDS
// ============================================================================

add_action('init', function () {
    $hooks = ['do_feed','do_feed_rdf','do_feed_rss','do_feed_rss2','do_feed_atom','do_feed_comments_rss2','do_feed_atom_comments'];
    foreach ($hooks as $hook) {
        add_action($hook, 'lbv_kill_feed', 0);
    }
}, 0);

function lbv_kill_feed() {
    if (!headers_sent()) {
        status_header(410);
        nocache_headers();
    }
    wp_die('Feed disabled', 'Feed disabled', ['response' => 410]);
}
