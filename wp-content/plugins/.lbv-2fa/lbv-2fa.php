<?php
/**
 * Plugin Name: LBV 2FA
 * Plugin URI: https://omgidol.com/
 * Description: Email-based two-factor authentication for administrator logins.
 * Version: 1.0.4
 * Author: Loibv
 * Text Domain: lbv-2fa
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AM2FA_VERSION', '1.0.4');
define('AM2FA_FILE', __FILE__);
define('AM2FA_PATH', plugin_dir_path(__FILE__));
define('AM2FA_URL', plugin_dir_url(__FILE__));
define('AM2FA_BASENAME', plugin_basename(__FILE__));

require_once AM2FA_PATH . 'includes/plugin.php';
require_once AM2FA_PATH . 'includes/settings.php';
require_once AM2FA_PATH . 'includes/login.php';
require_once AM2FA_PATH . 'includes/mailer.php';

register_activation_hook(__FILE__, array('AM2FA_Plugin', 'activate'));

add_action('plugins_loaded', static function () {
    AM2FA_Plugin::instance();
});