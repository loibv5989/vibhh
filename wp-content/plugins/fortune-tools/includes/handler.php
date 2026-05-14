<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Fortune_Frontend {

    const PAGE_TEMPLATE = 'ftn-layout.php';

    public function __construct() {
        add_filter( 'theme_page_templates', [ $this, 'register_template' ] );
        add_filter( 'template_include',     [ $this, 'load_template' ], 99 );
        add_action( 'wp_enqueue_scripts',   [ $this, 'enqueue_assets' ], 12 );
        add_action( 'wp_head',              [ $this, 'preload_assets' ], 0 );
    }

    public function enqueue_assets() {
        if ( ! is_page_template( self::PAGE_TEMPLATE ) ) return;
        wp_enqueue_style(
            'ftn',
            FORTUNE_TOOLS_PLUGIN_URL . 'assets/css/ftn.css',
            [],
            FORTUNE_TOOLS_VERSION
        );
    }

    public function preload_assets() {
        if ( ! is_page_template( self::PAGE_TEMPLATE ) ) return;
        echo '<link rel="preload" href="' . esc_url( FORTUNE_TOOLS_PLUGIN_URL . 'assets/css/ftn.css' . '?ver=' . FORTUNE_TOOLS_VERSION ) . '" as="style">' . "\n";
    }

    public function register_template( $templates ) {
        $templates[ self::PAGE_TEMPLATE ] = __( 'Fortune Tools', 'fortune-tools' );
        return $templates;
    }

    public function load_template( $template ) {
        if ( ! is_page() ) return $template;

        $assigned = get_post_meta( get_the_ID(), '_wp_page_template', true );

        if ( self::PAGE_TEMPLATE === $assigned ) {
            $plugin_tpl = FORTUNE_TOOLS_PLUGIN_DIR . 'templates/ftn-layout.php';
            if ( file_exists( $plugin_tpl ) ) {
                return $plugin_tpl;
            }
        }

        return $template;
    }
}

new Fortune_Frontend();
