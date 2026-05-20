<?php
/**
 * Plugin Name: Love Compatibility - Numerology
 * Description: Plugin analyzing love compatibility through Numerology.
 * Version: 26.4.20
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Text Domain: tsh-love
 */

if (!defined('ABSPATH')) exit;

define('TSH_LOVE_VERSION', '26.4.20');
define('TSH_LOVE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TSH_LOVE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TSH_LOVE_RATE_LIMIT', 3);

require_once TSH_LOVE_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once TSH_LOVE_PLUGIN_DIR . 'admin/admin.php';
    new TshLove_Admin(__FILE__);
}
