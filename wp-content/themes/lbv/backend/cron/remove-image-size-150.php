<?php

class CleanImage150Handler {

    private $dry_run = false;
    private $errors = array();

    public function __construct( $dry_run = false ) {
        $this->dry_run = $dry_run;
    }

    public function execute() {
        $featured_image_ids = $this->get_all_featured_image_ids();
        $attachments = $this->get_all_attachments();
        $deleted_count = 0;
        $skipped_count = 0;
        $total_found = 0;

        foreach ( $attachments as $attachment_id ) {
            $meta = wp_get_attachment_metadata( $attachment_id );

            if ( ! isset( $meta['sizes'] ) || empty( $meta['sizes'] ) ) {
                continue;
            }

            $is_featured = in_array( $attachment_id, $featured_image_ids );
            $sizes_to_delete = array();

            foreach ( $meta['sizes'] as $size_name => $size_data ) {
                if ( $this->is_150x_pattern( $size_data ) ) {
                    $total_found++;

                    if ( $is_featured ) {
                        $skipped_count++;
                        continue;
                    }

                    $sizes_to_delete[] = array(
                        'size_name' => $size_name,
                        'size_data' => $size_data
                    );
                }
            }

            if ( ! empty( $sizes_to_delete ) ) {
                if ( ! $this->dry_run ) {
                    foreach ( $sizes_to_delete as $size_info ) {
                        $result = $this->delete_single_size( $attachment_id, $size_info['size_name'], $meta );
                        if ( $result ) {
                            $deleted_count++;
                        }
                    }
                } else {
                    $deleted_count += count( $sizes_to_delete );
                }
            }
        }

        $this->log_errors();

        return array(
            'total_found' => $total_found,
            'deleted' => $deleted_count,
            'skipped' => $skipped_count
        );
    }

    public function get_list() {
        $featured_image_ids = $this->get_all_featured_image_ids();
        $attachments = $this->get_all_attachments();
        $results = array();

        foreach ( $attachments as $attachment_id ) {
            $meta = wp_get_attachment_metadata( $attachment_id );

            if ( ! isset( $meta['sizes'] ) || empty( $meta['sizes'] ) ) {
                continue;
            }

            $is_featured = in_array( $attachment_id, $featured_image_ids );

            foreach ( $meta['sizes'] as $size_name => $size_data ) {
                if ( $this->is_150x_pattern( $size_data ) ) {
                    $results[] = array(
                        'attachment_id' => $attachment_id,
                        'size_name'     => $size_name,
                        'dimensions'    => "{$size_data['width']}x{$size_data['height']}",
                        'file'          => $size_data['file'],
                        'featured'      => $is_featured ? '✓ FEATURED' : '',
                    );
                }
            }
        }

        return $results;
    }

    private function get_all_featured_image_ids() {
        global $wpdb;

        $post_types = get_post_types( array( 'public' => true ) );
        $post_types_str = "'" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "'";

        $query = "
            SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_thumbnail_id'
            AND pm.meta_value > 0
            AND p.post_type IN ({$post_types_str})
            AND p.post_status != 'trash'
        ";

        $results = $wpdb->get_col( $query );
        return array_map( 'intval', $results );
    }

    private function is_150x_pattern( $size_data ) {
        $width  = (int) $size_data['width'];
        $height = (int) $size_data['height'];
        return ( $width === 150 || $height === 150 );
    }

    private function get_all_attachments() {
        $attachments = new \WP_Query( array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'post_status'    => 'inherit',
            'fields'         => 'ids',
        ) );

        return $attachments->posts;
    }

    private function delete_single_size( $attachment_id, $size_name, &$meta ) {
        if ( ! isset( $meta['sizes'][ $size_name ] ) ) {
            return false;
        }

        $upload_dir = wp_upload_dir();
        $file_path = $meta['file'];
        $size_file = $meta['sizes'][ $size_name ]['file'];

        $full_path = $upload_dir['basedir'] . '/' . dirname( $file_path ) . '/' . $size_file;

        if ( file_exists( $full_path ) ) {
            wp_delete_file( $full_path );

            if ( file_exists( $full_path ) ) {
                @unlink( $full_path );
            }

            if ( file_exists( $full_path ) ) {
                $this->add_error( "Could not delete file: {$full_path}" );
                return false;
            }
        }

        unset( $meta['sizes'][ $size_name ] );
        wp_update_attachment_metadata( $attachment_id, $meta );

        return true;
    }

    private function add_error( $message ) {
        $this->errors[] = $message;
    }

    private function log_errors() {
        if ( ! empty( $this->errors ) ) {
            foreach ( $this->errors as $error ) {
                error_log( '[Clean Image 150 Error] ' . $error );
            }
        }

        if ( defined( 'WP_CLI' ) && WP_CLI && ! empty( $this->errors ) ) {
            foreach ( $this->errors as $error ) {
                \WP_CLI::warning( $error );
            }
        }
    }
}

class CleanImage150CLI {

    public function delete( $args, $assoc_args ) {
        $dry_run = isset( $assoc_args['dry-run'] );
        $handler = new CleanImage150Handler( $dry_run );
        $result = $handler->execute();

        if ( $dry_run ) {
            \WP_CLI::log( "Dry run completed. Found: {$result['total_found']}, Would delete: {$result['deleted']}, Skipped: {$result['skipped']}" );
            \WP_CLI::log( "Run without --dry-run to actually delete files." );
        } else {
            \WP_CLI::success( "Cleanup completed! Deleted: {$result['deleted']}, Skipped: {$result['skipped']}" );
        }
    }

    public function list_images( $args, $assoc_args ) {
        $handler = new CleanImage150Handler();
        $results = $handler->get_list();

        if ( empty( $results ) ) {
            \WP_CLI::log( "No 150x[x] images found." );
            return;
        }

        \WP_CLI\Utils\format_items( 'table', $results, array( 'attachment_id', 'size_name', 'dimensions', 'file', 'featured' ) );
        \WP_CLI::log( "\nTotal found: " . count( $results ) );

        $featured_count = count( array_filter( $results, function( $item ) {
            return ! empty( $item['featured'] );
        } ) );

        \WP_CLI::log( "Featured images (will be kept): {$featured_count}" );
        \WP_CLI::log( "Non-featured images (can be deleted): " . ( count( $results ) - $featured_count ) );
    }
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    \WP_CLI::add_command( 'clean-image-150', 'CleanImage150CLI' );
}

// Cron scheduling
function clean_image_150_add_cron_schedule( $schedules ) {
    $schedules['daily_midnight'] = array(
        'interval' => 86400,
        'display'  => __( 'Daily at Midnight' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'clean_image_150_add_cron_schedule' );

function clean_image_150_schedule_cron() {
    if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON && isset( $_GET['doing_wp_cron'] ) ) ) {
        if ( ! wp_next_scheduled( 'clean_image_150_daily_task' ) ) {
            $midnight = strtotime( 'tomorrow midnight' );
            wp_schedule_event( $midnight, 'daily_midnight', 'clean_image_150_daily_task' );
        }
    }
}
add_action( 'init', 'clean_image_150_schedule_cron' );

function clean_image_150_run_cleanup() {
    $handler = new CleanImage150Handler( false );
    $result = $handler->execute();
}
add_action( 'clean_image_150_daily_task', 'clean_image_150_run_cleanup' );

function clean_image_150_deactivate_cron() {
    $timestamp = wp_next_scheduled( 'clean_image_150_daily_task' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'clean_image_150_daily_task' );
    }
}
register_deactivation_hook( __FILE__, 'clean_image_150_deactivate_cron' );