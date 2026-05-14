<?php

/**
 * Plugin Name: Tử Vi
 * Description: Plugin lập lá số và luận giải Tử Vi.
 * Version: 26.5.11
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * Text Domain: tu-vi
 */

if (!defined('ABSPATH')) exit;

define('TUVI_PLUGIN_VERSION', '26.5.11');
define('TUVI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TUVI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TUVI_RATE_LIMIT', 5);

require_once TUVI_PLUGIN_DIR . 'includes/settings.php';
require_once TUVI_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once TUVI_PLUGIN_DIR . 'admin/admin.php';
    new TuVi_Admin(__FILE__);
}
