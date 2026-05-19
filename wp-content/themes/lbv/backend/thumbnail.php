<?php

if (!defined('ABSPATH')) exit;

class LBV_Thumbnail {

    private static $instance = null;

    private $excluded_posttypes = [];

    public function __construct(){
        if (strpos($_SERVER['REQUEST_URI'], 'post_type=product') === false) {
            add_filter( 'manage_posts_columns', array( $this, 'add_thumbnail_column'), 10, 1 );
            add_action( 'manage_posts_custom_column', array( $this, 'manage_posts_custom_column'), 10, 2 );
            add_filter( 'manage_pages_columns', array( $this, 'add_thumbnail_column'), 10, 1 );
            add_action( 'manage_pages_custom_column', array( $this, 'manage_posts_custom_column'), 10, 2 );
        }
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    private function get_current_admin_post_type() {
        global $post, $typenow, $current_screen;
        return $post && $post->post_type ? $post->post_type : ($typenow ? $typenow : ($current_screen && $current_screen->post_type ? $current_screen->post_type : (isset($_REQUEST['post_type']) ? sanitize_key($_REQUEST['post_type']) : null)));
    }


    private function get_excluded_post_types(){
        if (empty($this->excluded_posttypes)) {
            $this->excluded_posttypes = (array) apply_filters('thumbnail/exclude_posttype', $this->excluded_posttypes);
        }
        return $this->excluded_posttypes;
    }

    public function add_thumbnail_column($columns){
        $new_columns = array();
        $new_columns['thumbnail'] = __('Thumbnail', 'text-domain');
        if (!wp_is_mobile() && !in_array($this->get_current_admin_post_type(), $this->get_excluded_post_types())) {
            $columns = array_merge($new_columns, $columns);
        } else {
            $columns['thumbnail'] = __('Thumbnail', 'text-domain');
        }
        return $columns;
    }


    public function manage_posts_custom_column($column, $post_id){
        if ($column === 'thumbnail' && has_post_thumbnail($post_id) && !in_array($this->get_current_admin_post_type(), $this->get_excluded_post_types())) {
            $thumbnail = get_the_post_thumbnail($post_id, array(120, 9999));

            if (strpos($thumbnail, '.gif') !== false) {
                $thumbnail = '<img width="120" height="60" src="' . get_the_post_thumbnail_url($post_id, 'thumbnail'). '" class="wp-post-image">';
            }
            echo $thumbnail;
        }
    }
}

LBV_Thumbnail::get_instance();