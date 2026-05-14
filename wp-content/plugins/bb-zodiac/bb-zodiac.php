<?php
/**
 * Plugin Name: Cung Hoàng Đạo
 * Plugin URI: https://nbblo.com
 * Description: Plugin giải mã cung hoàng đạo online
 * Version: 26.4.15
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: bb-zodiac
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('BB_ZODIAC_VERSION', '26.4.15');
define('BB_ZODIAC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BB_ZODIAC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BB_ZODIAC_RATE_LIMIT', 10);

require_once BB_ZODIAC_PLUGIN_DIR . 'includes/settings.php';
require_once BB_ZODIAC_PLUGIN_DIR . 'includes/calc.php';
require_once BB_ZODIAC_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once BB_ZODIAC_PLUGIN_DIR . 'admin/admin.php';
    new BbZodiac_Admin(__FILE__);
}
