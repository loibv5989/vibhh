<?php
/**
 * Plugin Name: Zodiac
 * Plugin URI: https://nbblo.com
 * Description: Plugin for online zodiac decoding
 * Version: 26.4.20
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: zodiac
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('ZODIAC_VERSION', '26.4.20');
define('ZODIAC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ZODIAC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ZODIAC_RATE_LIMIT', 3);

require_once ZODIAC_PLUGIN_DIR . 'includes/settings.php';
require_once ZODIAC_PLUGIN_DIR . 'includes/calc.php';
require_once ZODIAC_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once ZODIAC_PLUGIN_DIR . 'admin/admin.php';
    new Zodiac_Admin(__FILE__);
}
