<?php
/**
 * Plugin Name: Numerology
 * Plugin URI: https://nbblo.com
 * Description: Numerology Lookup - Destiny
 * Version: 26.5.20
 * Author: Loibv
 * Requires PHP: 8.2 or higher
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) exit;

define('NUMEROLOGY_VERSION', '26.5.20');
define('NUMEROLOGY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NUMEROLOGY_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once NUMEROLOGY_PLUGIN_DIR . 'includes/handle.php';