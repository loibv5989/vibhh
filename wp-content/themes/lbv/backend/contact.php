<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class LBV_Contact {

    public $helpers;
    public $settings;
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array($this, 'add_admin_menu'));
        add_action( 'admin_init', array($this, 'delete_contact'));
        add_action( 'admin_notices', array($this, 'show_pending_contact_notice'));
        add_filter( 'add_menu_classes', array($this, 'add_pending_badge_to_users_menu'));
    }

    public function add_admin_menu() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';
        $pending_count = $wpdb->get_var(
                "SELECT COUNT(*) FROM {$table_name} WHERE status = 'pending'"
        );

        $menu_title = __('Contacts', 'lbv');

        if ($pending_count > 0) {
            $menu_title .= sprintf(
                    ' <span class="awaiting-mod">%d</span>',
                    $pending_count
            );
        }

        add_submenu_page(
                'users.php',
                __('Contacts', 'lbv'),
                $menu_title,
                'manage_options',
                'lbv',
                array($this, 'admin_page_callback')
        );
    }


    public function add_pending_badge_to_users_menu($menu) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';
        $pending_count = $wpdb->get_var(
                "SELECT COUNT(*) FROM {$table_name} WHERE status = 'pending'"
        );

        if ($pending_count > 0) {
            foreach ($menu as $key => $item) {
                if ($item[2] === 'users.php') {
                    $menu[$key][0] .= sprintf(
                            ' <span class="awaiting-mod count-%d"><span class="pending-count">%d</span></span>',
                            $pending_count,
                            $pending_count
                    );
                    break;
                }
            }
        }

        return $menu;
    }


    public function admin_page_callback() {
        require_once get_template_directory() . '/backend/templates/contact/table.php';

        $table = new LBV_Contact_Table();
        if (isset($_GET['action']) && $_GET['action'] === 'reply' && isset($_GET['contact'])) {
            $contact_id = absint($_GET['contact']);
            $table->display_reply_form($contact_id);
            return;
        }

        $table->prepare_items();

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Contact Messages', 'lbv'); ?></h1>
            <hr class="wp-header-end">

            <?php

            settings_errors('lbv_contact_messages');

            if (isset($_GET['message'])) {
                $message = sanitize_text_field($_GET['message']);
                if ($message === 'sent') {
                    echo '<div class="notice notice-success is-dismissible"><p>' .
                        __('Reply sent successfully!', 'lbv') . '</p></div>';
                } elseif ($message === 'error') {
                    echo '<div class="notice notice-error is-dismissible"><p>' .
                        __('Failed to send reply. Please check your email settings.', 'lbv') . '</p></div>';
                } elseif ($message === 'deleted') {
                    echo '<div class="notice notice-success is-dismissible"><p>' .
                        __('Contact deleted successfully!', 'lbv') . '</p></div>';
                }
            }
            ?>

            <form id="contacts-filter" method="get">
                <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                <?php
                $table->views();
                $table->search_box(__('Search Contacts', 'lbv'), 'contact');
                $table->display();
                ?>
            </form>
        </div>

        <style> .wp-list-table .column-content { width: 25%; } .wp-list-table .column-actions { width: 120px; } .wp-list-table .column-id { width: 50px; } .wp-list-table .column-status { width: 100px; } .wp-list-table .column-date { width: 140px; } .wp-list-table .button-primary { background: #2271b1; border-color: #2271b1; } .wp-list-table .button-primary:hover { background: #135e96; border-color: #135e96; } .subsubsub { margin-bottom: 15px; } .tablenav { margin: 20px 0; } .card { padding: 20px; background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #2271b1; box-shadow: 0 1px 1px rgba(0,0,0,.04); } .card h2 { margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #dcdcde; } .form-table th { padding: 15px 10px 15px 0; } .form-table td { padding: 15px 10px; } .required { color: #d63638; } </style>        <?php
    }

    public function delete_contact() {
        if (isset($_GET['page']) && $_GET['page'] === 'lbv' &&
                isset($_GET['action']) && $_GET['action'] === 'delete' &&
                isset($_GET['contact'])) {

            $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
            $contact_id = absint($_GET['contact']);

            if (wp_verify_nonce($nonce, 'delete_contact_' . $contact_id)) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'lbv_contact';
                $wpdb->delete($table_name, ['id' => $contact_id], ['%d']);

                wp_redirect(admin_url('users.php?page=lbv'));
                exit;
            }
        }

        if (isset($_POST['page']) && $_POST['page'] === 'lbv' &&
                isset($_POST['action']) && $_POST['action'] === 'delete' &&
                isset($_POST['bulk-delete'])) {

            global $wpdb;
            $table_name = $wpdb->prefix . 'lbv_contact';
            $delete_ids = array_map('absint', $_POST['bulk-delete']);

            foreach ($delete_ids as $id) {
                $wpdb->delete($table_name, ['id' => $id], ['%d']);
            }

            wp_redirect(admin_url('users.php?page=lbv'));
            exit;
        }
    }

    public function show_pending_contact_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $screen = get_current_screen();

        if ($screen->id !== 'dashboard') {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';
        $pending_count = $wpdb->get_var(
                "SELECT COUNT(*) FROM {$table_name} WHERE status = 'pending'"
        );

        if ($pending_count == 0) {
            return;
        }

        $contact_url = admin_url('users.php?page=lbv&status=pending');
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php _e('Contact Messages:', 'lbv'); ?></strong>
                <?php
                printf(
                        __('You have %d pending message(s).', 'lbv'),
                        $pending_count
                );
                ?>
                <a href="<?php echo esc_url($contact_url); ?>"><?php _e('View', 'lbv'); ?></a>
            </p>
        </div>
        <?php
    }

}

LBV_Contact::get_instance();