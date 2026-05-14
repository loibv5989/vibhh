<?php
/**
 * Plugin Name: Oracle Cards Online
 * Plugin URI: https://nbblo.com
 * Description: Plugin rút bài Oracle online
 * Version: 26.5.10
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Author URI: https://nbblo.com
 * Text Domain: bb-oracle
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('BB_ORACLE_VERSION', '26.5.10');
define('BB_ORACLE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BB_ORACLE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BB_ORACLE_RATE_LIMIT', 5);

require_once BB_ORACLE_PLUGIN_DIR . 'includes/settings.php';
require_once BB_ORACLE_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once BB_ORACLE_PLUGIN_DIR . 'admin/admin.php';
    new BbOracle_Admin(__FILE__);
}
