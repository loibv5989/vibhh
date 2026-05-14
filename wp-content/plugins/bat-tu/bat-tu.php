<?php

/**
 * Plugin Name: Bát tự (Tứ trụ)
 * Description: Plugin lập lá số và luận giải Bát tự tứ trụ.
 * Version: 26.5.11
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Text Domain: bat-tu
 */

if (!defined('ABSPATH')) exit;

define('BATTU_PLUGIN_VERSION', '26.5.11');
define('BATTU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BATTU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BATTU_RATE_LIMIT', 5);

require_once BATTU_PLUGIN_DIR . 'includes/settings.php';
require_once BATTU_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once BATTU_PLUGIN_DIR . 'admin/admin.php';
    new Batu_Admin(__FILE__);
}
