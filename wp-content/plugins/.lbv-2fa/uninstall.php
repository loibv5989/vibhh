<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('am2fa_settings');
delete_site_option('am2fa_settings');
