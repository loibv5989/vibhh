<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Fortune_Installer {

    public static function install() {
        self::create_page();
    }

    private static function create_page() {
        $slug = 'fortune-tools';

        if ( get_page_by_path( $slug ) ) return;

        $page_id = wp_insert_post( [
            'post_title'   => __( 'Xem Vận Mệnh & Tình Duyên', 'fortune-tools' ),
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ] );

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            update_post_meta( $page_id, '_wp_page_template', 'fortune-tools-page.php' );
            update_option( 'fortune_tools_page_id', $page_id );
        }
    }
}
