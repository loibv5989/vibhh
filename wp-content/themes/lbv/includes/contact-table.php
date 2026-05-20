<?php

if (!defined('ABSPATH')) exit;

class LBV_Contact_Installer {

    public static function install() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'lbv_contact';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            content text NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'pending',
            replied_at datetime NULL,
            PRIMARY KEY  (id),
            KEY status_idx (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        self::update_table_schema();

        update_option('lbv_contact_db_version', '1.1');
    }

    public static function update_table_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'lbv_contact';

        // Check column status
        $column_exists = $wpdb->get_results(
            $wpdb->prepare(
                "SHOW COLUMNS FROM `{$table_name}` LIKE %s",
                'status'
            )
        );

        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE `{$table_name}` ADD COLUMN status varchar(20) DEFAULT 'pending' AFTER date");
        }

        // Check column replied_at
        $replied_exists = $wpdb->get_results(
            $wpdb->prepare(
                "SHOW COLUMNS FROM `{$table_name}` LIKE %s",
                'replied_at'
            )
        );

        if (empty($replied_exists)) {
            $wpdb->query("ALTER TABLE `{$table_name}` ADD COLUMN replied_at datetime NULL AFTER status");
        }
    }
}
