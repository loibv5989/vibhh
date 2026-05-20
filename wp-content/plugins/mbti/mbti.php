<?php

/**
 * Plugin Name:   MBTI
 * Plugin URI:    https://nbblo.com
 * Description:   Plugin trắc nghiệm tính cách MBTI
 * Version:       26.4.20
 * Author:        Loibv
 * Requires PHP: 8.2 or higher
 * Author URI:    https://nbblo.com
 * Text Domain:   mbti
 */

if (!defined('ABSPATH')) exit;

define('MBTI_NAME', 'MBTI');
define('MBTI_VERSION', '26.4.20');
define('MBTI_PLUGIN_FILE', __FILE__);
define('MBTI_PLUGIN_BASE', plugin_basename(MBTI_PLUGIN_FILE));
define('MBTI_PLUGIN_DIR',  plugin_dir_path(MBTI_PLUGIN_FILE));
define('MBTI_PLUGIN_URL',  plugin_dir_url(MBTI_PLUGIN_FILE));
define('MBTI_RATE_LIMIT', 5);

require_once MBTI_PLUGIN_DIR . 'includes/settings.php';
require_once MBTI_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once MBTI_PLUGIN_DIR . 'admin/admin.php';
    new MBTI_Admin(MBTI_PLUGIN_FILE);
}