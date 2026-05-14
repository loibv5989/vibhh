<?php

/**
 * Plugin Name:   Fortune Tools
 * Plugin URI:    https://omgidol.com
 * Description:   Plugin cung cấp các công cụ xem bói (numerology, zodiac, tarot).
 * Version:       26.4.15
 * Author:        Loibv
 * Author URI:    https://omgidol.com
 * Text Domain:   fortune-tools
 * Domain Path:   /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FORTUNE_TOOLS_NAME', 'Fortune Tools' );
define( 'FORTUNE_TOOLS_VERSION', '26.4.15');
define( 'FORTUNE_TOOLS_PLUGIN_FILE', __FILE__ );
define( 'FORTUNE_TOOLS_PLUGIN_BASE', plugin_basename( FORTUNE_TOOLS_PLUGIN_FILE ) );
define( 'FORTUNE_TOOLS_PLUGIN_DIR',  plugin_dir_path( FORTUNE_TOOLS_PLUGIN_FILE ) );
define( 'FORTUNE_TOOLS_PLUGIN_URL',  plugin_dir_url( FORTUNE_TOOLS_PLUGIN_FILE ) );

class Fortune_Tools {

    private $admin;
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    private function init() {
        $this->load_directory( FORTUNE_TOOLS_PLUGIN_DIR . 'includes' );
        $this->load_directory( FORTUNE_TOOLS_PLUGIN_DIR . 'admin' );

        if ( is_admin() && class_exists( 'Fortune_Admin' ) ) {
            $this->admin = new Fortune_Admin( FORTUNE_TOOLS_PLUGIN_FILE );
        }
    }

    private function load_directory( $dir, $recursive = false ) {
        if ( ! is_dir( $dir ) ) return;

        if ( $recursive ) {
            $dir_iter = new RecursiveDirectoryIterator( $dir );
            $iterator = new RecursiveIteratorIterator( $dir_iter );
            $iterator->setMaxDepth( 1 );
            foreach ( $iterator as $file ) {
                if ( $file->isFile() && $file->getExtension() === 'php' ) {
                    require_once $file->getPathname();
                }
            }
        } else {
            foreach ( (array) glob( $dir . '/*.php' ) as $file ) {
                if ( is_file( $file ) ) require_once $file;
            }
        }
    }
}

require_once FORTUNE_TOOLS_PLUGIN_DIR . 'includes/installer.php';

register_activation_hook( FORTUNE_TOOLS_PLUGIN_FILE, [ 'Fortune_Installer', 'install' ] );

Fortune_Tools::get_instance();