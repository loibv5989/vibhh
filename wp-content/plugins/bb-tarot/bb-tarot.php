<?php
/**
 * Plugin Name: Bói Bài Tarot
 * Plugin URI: https://nbblo.com
 * Description: Plugin bói bài Tarot online
 * Version: 26.5.10
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: bb-tarot
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('TAROT_VERSION', '26.5.10');
define('TAROT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TAROT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TAROT_RATE_LIMIT', 10);

require_once TAROT_PLUGIN_DIR . 'includes/settings.php';
require_once TAROT_PLUGIN_DIR . 'includes/calc.php';
require_once TAROT_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once TAROT_PLUGIN_DIR . 'admin/admin.php';
    new TR_Admin(__FILE__);
}
