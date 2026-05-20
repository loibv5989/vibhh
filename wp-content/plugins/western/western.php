<?php

/**
 * Plugin Name: Western Card Reading
 * Plugin URI: https://nbblo.com
 * Description: Online 52-card Western playing card reading plugin
 * Version: 26.5.20
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: western
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('WESTERN_VERSION', '26.5.20');
define('WESTERN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WESTERN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WESTERN_RATE_LIMIT', 6);

require_once WESTERN_PLUGIN_DIR . 'includes/settings.php';
require_once WESTERN_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once WESTERN_PLUGIN_DIR . 'admin/admin.php';
    new WESTERN_Admin(__FILE__);
}
