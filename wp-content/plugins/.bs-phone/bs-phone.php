<?php

/**
 * Plugin Name:   BS Phone
 * Plugin URI:    https://nbblo.com
 * Description:   Giải mã cộng số điện thoại
 * Version:       2.0
 * Author:        Loibv
 * Author URI:    https://nbblo.com
 * Text Domain:   bs-phone
 */

if (!defined('ABSPATH')) exit;

define('BS_PHONE_NAME', 'BS Phone');
define('BS_PHONE_VERSION', time());
define('BS_PHONE_PLUGIN_FILE', __FILE__);
define('BS_PHONE_PLUGIN_BASE', plugin_basename(BS_PHONE_PLUGIN_FILE));
define('BS_PHONE_PLUGIN_DIR',  plugin_dir_path(BS_PHONE_PLUGIN_FILE));
define('BS_PHONE_PLUGIN_URL',  plugin_dir_url(BS_PHONE_PLUGIN_FILE));

require_once BS_PHONE_PLUGIN_DIR . 'includes/handle.php';

if (is_admin()) {
    require_once BS_PHONE_PLUGIN_DIR . 'admin/admin.php';
    new BS_Phone_Admin(BS_PHONE_PLUGIN_FILE);
}
