<?php
/**
 * Template: System Information
 *
 * @package LBV
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// PHP Info
$php_version = phpversion();
$memory_limit = ini_get('memory_limit');
$max_input_vars = ini_get('max_input_vars');
$post_max_size = ini_get('post_max_size');
$upload_max_size = ini_get('upload_max_filesize');
$max_execution_time = ini_get('max_execution_time');
$zip_archive = class_exists('ZipArchive') ? 'Yes' : 'No';

// Server Info
$server_software = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';
$server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'Unknown';
$server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown';
$server_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'Unknown';
$document_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Unknown';

// Detect web server type
$web_server = 'Unknown';
if (stripos($server_software, 'nginx') !== false) {
    $web_server = 'Nginx';
} elseif (stripos($server_software, 'apache') !== false) {
    $web_server = 'Apache';
} elseif (stripos($server_software, 'litespeed') !== false || stripos($server_software, 'lsws') !== false) {
    $web_server = 'LiteSpeed';
} elseif (stripos($server_software, 'openlitespeed') !== false) {
    $web_server = 'OpenLiteSpeed';
} elseif (stripos($server_software, 'microsoft-iis') !== false) {
    $web_server = 'IIS';
}

// Database Info
$db_version = $wpdb->db_version();

// WordPress Info
$wp_version = get_bloginfo('version');
$wp_debug = defined('WP_DEBUG') && WP_DEBUG ? 'Enabled' : 'Disabled';
$wp_debug_log = defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Enabled' : 'Disabled';
$wp_memory_limit = WP_MEMORY_LIMIT;
$wp_max_memory_limit = WP_MAX_MEMORY_LIMIT;

// Theme Info
$theme = wp_get_theme();
$theme_name = $theme->get('Name');
$theme_version = $theme->get('Version');
$theme_author = $theme->get('Author');
?>

<div class="lbv-system-info-wrapper">
    <div class="lbv-system-info-header">
        <div class="lbv-system-info-icon">
            <span class="dashicons dashicons-database"></span>
        </div>
        <div class="lbv-system-info-title">
            <h1><?php _e('System Information', 'lbv'); ?></h1>
            <p><?php _e('Lbv theme can operate on nearly all servers. However, we recommend following the server settings below if you encounter any red values', 'lbv'); ?></p>
        </div>
    </div>

    <!-- Server Information -->
    <div class="lbv-info-section">
        <h3 class="lbv-section-title">
            <span class="dashicons dashicons-admin-site"></span>
            <?php _e('Server Information', 'lbv'); ?>
        </h3>

        <table class="lbv-info-table">
            <tr>
                <td class="lbv-info-label"><?php _e('Web Server', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($web_server); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Server Software', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html($server_software); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Server Protocol', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html($server_protocol); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Server Name', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html($server_name); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Server IP', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html($server_ip); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Document Root', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html($document_root); ?></td>
            </tr>
        </table>
    </div>

    <!-- PHP Information -->
    <div class="lbv-info-section">
        <h3 class="lbv-section-title">
            <span class="dashicons dashicons-editor-code"></span>
            <?php _e('PHP Information', 'lbv'); ?>
        </h3>

        <table class="lbv-info-table">
            <tr>
                <td class="lbv-info-label"><?php _e('PHP Version', 'lbv'); ?></td>
                <td class="lbv-info-value <?php echo version_compare($php_version, '8.0', '>=') ? 'success' : 'warning'; ?>">
                    <?php echo esc_html($php_version); ?>
                </td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Memory Limit', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($memory_limit); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Max Execution Time', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($max_execution_time); ?>s</td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Max Input Vars', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($max_input_vars); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Post Max Size', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($post_max_size); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Max Upload Size', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($upload_max_size); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('ZipArchive Support', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($zip_archive); ?></td>
            </tr>
        </table>
    </div>

    <!-- Database Info -->
    <div class="lbv-info-section">
        <h3 class="lbv-section-title">
            <span class="dashicons dashicons-database"></span>
            <?php _e('Database Information', 'lbv'); ?>
        </h3>

        <table class="lbv-info-table">
            <tr>
                <td class="lbv-info-label"><?php _e('Database Type', 'lbv'); ?></td>
                <td class="lbv-info-value">MySQL/MariaDB</td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Database Version', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($db_version); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Database Host', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html(DB_HOST); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Database Name', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html(DB_NAME); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Database Charset', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html(DB_CHARSET); ?></td>
            </tr>
        </table>
    </div>

    <!-- WordPress Info -->
    <div class="lbv-info-section">
        <h3 class="lbv-section-title">
            <span class="dashicons dashicons-wordpress"></span>
            <?php _e('WordPress Info', 'lbv'); ?>
        </h3>

        <table class="lbv-info-table">
            <tr>
                <td class="lbv-info-label"><?php _e('WordPress Version', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($wp_version); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Site URL', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html(get_site_url()); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Home URL', 'lbv'); ?></td>
                <td class="lbv-info-value"><?php echo esc_html(get_home_url()); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('WP Memory Limit', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($wp_memory_limit); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('WP Max Memory Limit', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($wp_max_memory_limit); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Debug Mode', 'lbv'); ?></td>
                <td class="lbv-info-value <?php echo $wp_debug === 'Disabled' ? 'success' : 'warning'; ?>">
                    <?php echo esc_html($wp_debug); ?>
                </td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Debug Log', 'lbv'); ?></td>
                <td class="lbv-info-value <?php echo $wp_debug_log === 'Disabled' ? 'success' : 'warning'; ?>">
                    <?php echo esc_html($wp_debug_log); ?>
                </td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Theme Name', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($theme_name); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Theme Version', 'lbv'); ?></td>
                <td class="lbv-info-value success"><?php echo esc_html($theme_version); ?></td>
            </tr>
            <tr>
                <td class="lbv-info-label"><?php _e('Theme Author', 'lbv'); ?></td>
                <td class="lbv-info-value">
                    <a href="<?php echo esc_url($theme->get('AuthorURI')); ?>" target="_blank">
                        <?php echo esc_html($theme_author); ?>
                    </a>
                </td>
            </tr>
        </table>
    </div>
</div>
