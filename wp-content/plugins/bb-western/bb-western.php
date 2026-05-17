<?php
/**
 * Plugin Name: Bói Bài Tây
 * Plugin URI: https://nbblo.com
 * Description: Plugin bói bài Tây (52 lá) online
 * Version: 26.5.17
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: bb-western
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('BB_WESTERN_VERSION', time());
define('BB_WESTERN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BB_WESTERN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BB_WESTERN_RATE_LIMIT', 10);

require_once BB_WESTERN_PLUGIN_DIR . 'includes/settings.php';
require_once BB_WESTERN_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once BB_WESTERN_PLUGIN_DIR . 'admin/admin.php';
    new BBW_Admin(__FILE__);
}
