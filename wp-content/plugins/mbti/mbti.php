<?php

/**
 * Plugin Name:   MBTI
 * Plugin URI:    https://nbblo.com
 * Description:   MBTI Personality Test Plugin
 * Version:       26.4.21
 * Author:        Loibv
 * Requires PHP: 8.2 or higher
 * Author URI:    https://nbblo.com
 * Text Domain:   mbti
 */

if (!defined('ABSPATH')) exit;

define('MBTI_NAME', 'MBTI');
define('MBTI_VERSION', '26.4.21');
define('MBTI_PLUGIN_FILE', __FILE__);
define('MBTI_PLUGIN_BASE', plugin_basename(MBTI_PLUGIN_FILE));
define('MBTI_PLUGIN_DIR',  plugin_dir_path(MBTI_PLUGIN_FILE));
define('MBTI_PLUGIN_URL',  plugin_dir_url(MBTI_PLUGIN_FILE));
define('MBTI_RATE_LIMIT', 3);

require_once MBTI_PLUGIN_DIR . 'includes/settings.php';
require_once MBTI_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once MBTI_PLUGIN_DIR . 'admin/admin.php';
    new MBTI_Admin(MBTI_PLUGIN_FILE);
}