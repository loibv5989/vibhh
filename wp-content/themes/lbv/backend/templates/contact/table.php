<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class LBV_Contact_Table extends WP_List_Table {

    private static $instance = null;

    public function __construct() {
        parent::__construct([
            'singular' => __('Contact', 'lbv'),
            'plural'   => __('Contacts', 'lbv'),
            'ajax'     => false
        ]);
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_columns() {
        return [
            'cb'      => '<input type="checkbox" />',
            'id'      => __('ID', 'lbv'),
            'name'    => __('Name', 'lbv'),
            'email'   => __('Email', 'lbv'),
            'subject' => __('Subject', 'lbv'),
            'content' => __('Content', 'lbv'),
            'status'  => __('Status', 'lbv'),
            'date'    => __('Date', 'lbv'),
            'actions' => __('Actions', 'lbv')
        ];
    }

    public function get_sortable_columns() {
        return [
            'id'      => ['id', false],
            'name'    => ['name', false],
            'email'   => ['email', false],
            'subject' => ['subject', false],
            'status'  => ['status', false],
            'date'    => ['date', true]
        ];
    }

    public function get_bulk_actions() {
        return [
            'delete' => __('Delete', 'lbv')
        ];
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']);
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
                return $item['id'];
            case 'name':
                return esc_html($item['name']);
            case 'email':
                return '<a href="mailto:' . esc_attr($item['email']) . '">' . esc_html($item['email']) . '</a>';
            case 'subject':
                return esc_html($item['subject']);
            case 'content':
                return '<div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">' .
                    esc_html(wp_trim_words($item['content'], 10)) . '</div>';
            case 'status':
                return $this->get_status_badge($item);
            case 'date':
                return date('Y-m-d H:i:s', strtotime($item['date']));
            case 'actions':
                return $this->get_action_buttons($item);
            default:
                return print_r($item, true);
        }
    }

    private function get_action_buttons($item) {
        $page = sanitize_text_field($_REQUEST['page']);
        $status = isset($item['status']) ? $item['status'] : 'pending';

        $reply_url = sprintf(
            '?page=%s&action=%s&contact=%s',
            $page, 'reply', $item['id']
        );

        $delete_url = sprintf(
            '?page=%s&action=%s&contact=%s&_wpnonce=%s',
            $page, 'delete', $item['id'], wp_create_nonce('delete_contact_' . $item['id'])
        );

        $buttons = sprintf(
            '<a href="%s" class="button button-small button-primary">%s</a> ',
            esc_url($reply_url),
            $status === 'replied' ? __('Reply Again', 'lbv') : __('Reply', 'lbv')
        );

        $buttons .= sprintf(
            '<a href="%s" class="button button-small" onclick="return confirm(\'%s\')">%s</a>',
            esc_url($delete_url),
            esc_js(__('Are you sure you want to delete this contact?', 'lbv')),
            __('Delete', 'lbv')
        );

        return $buttons;
    }

    private function get_status_badge($item) {
        $status = isset($item['status']) ? $item['status'] : 'pending';

        $badges = [
            'pending' => [
                'label' => __('Pending', 'lbv'),
                'color' => '#d63638',
                'bg' => '#fcf0f1'
            ],
            'replied' => [
                'label' => __('Replied', 'lbv'),
                'color' => '#00a32a',
                'bg' => '#f0f6fc'
            ]
        ];

        $badge = isset($badges[$status]) ? $badges[$status] : $badges['pending'];

        $html = sprintf(
            '<span style="display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: 600; color: %s; background-color: %s;">%s</span>',
            $badge['color'],
            $badge['bg'],
            $badge['label']
        );

        if ($status === 'replied' && !empty($item['replied_at'])) {
            $html .= '<br><small style="color: #646970;">' .
                sprintf(__('on %s', 'lbv'), date('Y-m-d H:i', strtotime($item['replied_at']))) .
                '</small>';
        }

        return $html;
    }

    protected function get_views() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';

        $status_links = [];
        $current = (!empty($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all');

        // Count all
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $class = ($current == 'all' ? ' class="current"' : '');
        $status_links['all'] = sprintf(
            '<a href="?page=%s"%s>%s <span class="count">(%d)</span></a>',
            sanitize_text_field($_REQUEST['page']),
            $class,
            __('All', 'lbv'),
            $total
        );

        // Count pending
        $pending = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
        $class = ($current == 'pending' ? ' class="current"' : '');
        $status_links['pending'] = sprintf(
            '<a href="?page=%s&status=pending"%s>%s <span class="count">(%d)</span></a>',
            sanitize_text_field($_REQUEST['page']),
            $class,
            __('Pending', 'lbv'),
            $pending
        );

        // Count replied
        $replied = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'replied'");
        $class = ($current == 'replied' ? ' class="current"' : '');
        $status_links['replied'] = sprintf(
            '<a href="?page=%s&status=replied"%s>%s <span class="count">(%d)</span></a>',
            sanitize_text_field($_REQUEST['page']),
            $class,
            __('Replied', 'lbv'),
            $replied
        );

        return $status_links;
    }

    public function mark_as_replied($contact_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';

        $wpdb->update(
            $table_name,
            [
                'status' => 'replied',
                'replied_at' => current_time('mysql')
            ],
            ['id' => $contact_id],
            ['%s', '%s'],
            ['%d']
        );
    }

    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';

        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('contacts_per_page', 10);
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $orderby = (!empty($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'id';
        $order = (!empty($_GET['order'])) ? esc_sql($_GET['order']) : 'DESC';

        $search = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
        $status = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : '';

        $where = ' WHERE 1=1';

        // Add status filter
        if (!empty($status) && $status !== 'all') {
            $where .= $wpdb->prepare(" AND status = %s", $status);
        }

        // Add search filter
        if (!empty($search)) {
            $search = '%' . $wpdb->esc_like($search) . '%';
            $where .= $wpdb->prepare(" AND (name LIKE %s OR email LIKE %s OR subject LIKE %s OR content LIKE %s)",
                $search, $search, $search, $search);
        }

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name" . $where);

        $query = $wpdb->prepare("SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT %d OFFSET %d",
            $per_page, $offset);
        $this->items = $wpdb->get_results($query, ARRAY_A);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page
        ]);

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
    }

    public function process_bulk_action() {

    }


    public function search_box($text, $input_id) {
        if (empty($_REQUEST['s']) && !$this->has_items()) {
            return;
        }

        $input_id = $input_id . '-search-input';

        if (!empty($_REQUEST['orderby'])) {
            echo '<input type="hidden" name="orderby" value="' . esc_attr($_REQUEST['orderby']) . '" />';
        }
        if (!empty($_REQUEST['order'])) {
            echo '<input type="hidden" name="order" value="' . esc_attr($_REQUEST['order']) . '" />';
        }
        if (!empty($_REQUEST['status'])) {
            echo '<input type="hidden" name="status" value="' . esc_attr($_REQUEST['status']) . '" />';
        }
        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo esc_attr($input_id); ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button($text, '', '', false, array('id' => 'search-submit')); ?>
        </p>
        <?php
    }

    public function display_reply_form($contact_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';

        $contact = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $contact_id),
            ARRAY_A
        );

        if (!$contact) {
            echo '<div class="notice notice-error"><p>' . __('Contact not found.', 'lbv') . '</p></div>';
            return;
        }

        $this->process_reply();

        ?>
        <div class="wrap">
            <h1><?php _e('Reply to Contact', 'lbv'); ?></h1>

            <?php settings_errors('lbv_contact_messages'); ?>

            <div class="card" style="max-width: 100%; margin: 20px 10px 20px 0;">
                <h2><?php _e('Original Message', 'lbv'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th style="width: 120px;"><?php _e('From', 'lbv'); ?>:</th>
                        <td><strong><?php echo esc_html($contact['name']); ?></strong> &lt;<?php echo esc_html($contact['email']); ?>&gt;</td>
                    </tr>
                    <tr>
                        <th><?php _e('Subject', 'lbv'); ?>:</th>
                        <td><?php echo esc_html($contact['subject']); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Date', 'lbv'); ?>:</th>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($contact['date'])); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Status', 'lbv'); ?>:</th>
                        <td><?php echo $this->get_status_badge($contact); ?></td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top;"><?php _e('Message', 'lbv'); ?>:</th>
                        <td style="white-space: pre-wrap;"><?php echo esc_html($contact['content']); ?></td>
                    </tr>
                </table>
            </div>

            <form method="post" action="">
                <?php wp_nonce_field('lbv_reply_contact', 'lbv_reply_nonce'); ?>
                <input type="hidden" name="contact_id" value="<?php echo esc_attr($contact_id); ?>" />
                <input type="hidden" name="to_email" value="<?php echo esc_attr($contact['email']); ?>" />
                <input type="hidden" name="to_name" value="<?php echo esc_attr($contact['name']); ?>" />

                <h2><?php _e('Compose Reply', 'lbv'); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row" style="width: 120px;">
                            <label for="reply_subject"><?php _e('Subject', 'lbv'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="reply_subject"
                                   name="reply_subject"
                                   value="<?php echo esc_attr('Re: ' . $contact['subject']); ?>"
                                   class="large-text"
                                   required />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" style="vertical-align: top; padding-top: 10px;">
                            <label for="reply_message"><?php _e('Message', 'lbv'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <?php
                            wp_editor('', 'reply_message', array(
                                'textarea_name' => 'reply_message',
                                'textarea_rows' => 12,
                                'media_buttons' => false,
                                'teeny' => false,
                                'quicktags' => true,
                            ));
                            ?>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit"
                           name="send_reply"
                           id="send_reply"
                           class="button button-primary button-large"
                           value="<?php _e('Send Reply', 'lbv'); ?>" />
                    <a href="?page=<?php echo esc_attr($_REQUEST['page']); ?>"
                       class="button button-large"><?php _e('Cancel', 'lbv'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }

    public function process_reply() {
        if (isset($_POST['send_reply']) && check_admin_referer('lbv_reply_contact', 'lbv_reply_nonce')) {
            $to_email = sanitize_email($_POST['to_email']);
            $subject = sanitize_text_field($_POST['reply_subject']);
            $message = wp_kses_post($_POST['reply_message']);

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
            );

            $sent = wp_mail($to_email, $subject, $message, $headers);

            if ($sent) {
                $this->mark_as_replied(absint($_POST['contact_id']));

                add_settings_error(
                    'lbv_contact_messages',
                    'lbv_contact_reply_sent',
                    sprintf(__('Reply sent successfully to %s', 'lbv'), $to_email),
                    'success'
                );
            } else {
                add_settings_error(
                    'lbv_contact_messages',
                    'lbv_contact_reply_error',
                    __('Failed to send reply. Please check your email configuration.', 'lbv'),
                    'error'
                );
            }
        }
    }
}
