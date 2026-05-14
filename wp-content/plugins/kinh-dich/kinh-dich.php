<?php
/**
 * Plugin Name: Quẻ Kinh Dịch
 * Plugin URI: https://nbblo.com
 * Description: Plugin gieo và luận giải Kinh Dịch (Mai Hoa & Lục Hào).
 * Version: 26.5.3
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: kinh-dich
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('ICHING_PLUGIN_VERSION', '26.5.3');
define('ICHING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ICHING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ICHING_RATE_LIMIT', 10);

require_once ICHING_PLUGIN_DIR . 'includes/amlich.php';
require_once ICHING_PLUGIN_DIR . 'includes/calc.php';
require_once ICHING_PLUGIN_DIR . 'includes/calendar.php';
require_once ICHING_PLUGIN_DIR . 'includes/luchao.php';

require_once ICHING_PLUGIN_DIR . 'includes/settings.php';
require_once ICHING_PLUGIN_DIR . 'includes/handle.php';
require_once ICHING_PLUGIN_DIR . 'includes/svg.php';

if (is_admin()) {
    require_once ICHING_PLUGIN_DIR . 'admin/admin.php';
    new IChing_Admin(__FILE__);
}
