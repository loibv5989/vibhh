<?php

if (!defined('ABSPATH')) exit;

class LBV_Ajax_Load_Post {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_lbv_load_posts', array($this, 'load_posts'));
        add_action('wp_ajax_nopriv_lbv_load_posts', array($this, 'load_posts'));
        add_action('wp_ajax_lbv_get_nonce', array($this, 'get_nonce'));
        add_action('wp_ajax_nopriv_lbv_get_nonce', array($this, 'get_nonce'));
    }

    public function get_nonce() {
        wp_send_json_success(array(
            'nonce' => wp_create_nonce('archive_nonce'),
            'timestamp' => time()
        ));
    }

    public function load_posts() {
        if (!check_ajax_referer('archive_nonce', 'archive_nonce', false)) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
        }

        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        $archive_id = isset($_POST['archive_id']) ? intval($_POST['archive_id']) : 0;
        $context = isset($_POST['context']) ? sanitize_text_field($_POST['context']) : 'category';
        $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
        $is_home = isset($_POST['is_home']) ? intval($_POST['is_home']) : 0;

        $posts_per_page = LBV_Theme_Settings::get_instance()->lbv_posts_per_page();
        $uncat_id = get_category_by_slug('uncategorized')->term_id ?? 0;

        $offset = ($paged - 1) * $posts_per_page;

        $post_type_param = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';

        $post_types = ['idol', 'group', 'photo', 'actor', 'v_star'];

        if ($post_type_param === 'post') {
            $post_types = ['post'];
        }

        $args = [
            'post_type'      => $post_types,
            'posts_per_page' => $posts_per_page + 1,
            'offset'         => $offset,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'category__not_in' => [$uncat_id],

            'no_found_rows'  => true,

            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
        ];

        switch ($context) {
            case 'search':
                if (!empty($search_query)) {
                    $args['s'] = $search_query;
                    $args['post_type'][] = 'post';
                }
                break;

            case 'tag':
                $args['tag_id'] = $archive_id;
                $args['post_type'][] = 'post';
                break;

            case 'category':
                $args['cat'] = $archive_id;
                $args['post_type'][] = 'post';
                break;
        }

        $query = new WP_Query($args);

        $post_count = $query->post_count;
        $has_more = ($post_count > $posts_per_page);

        if ($has_more) {
            array_pop($query->posts);
            $query->post_count = $posts_per_page;
        }

        $response = array(
            'posts'     => $this->get_posts_html($query, $is_home),
            'has_more'  => $has_more,
            'max_pages' => 999,
            'current_page' => $paged,
            'post_count' => $query->post_count,
        );

        if (!$has_more) {
            $response['notice'] = $this->end_list();
        }

        wp_reset_postdata();
        wp_send_json_success($response);
    }

    private function get_posts_html($query, $is_home = false) {
        $output = '';

        while ($query->have_posts()) {
            $query->the_post();

            ob_start();
            get_template_part('templates/single/post-item', 'home', array(
                'is_home' => $is_home
            ));

            $output .= ob_get_clean();
        }

        return $output;
    }


    private function end_list() {
        $output = '<div class="end-list"><span>';
        $output .= __( 'You\'ve reached the end of the list!', 'lbv' );
        $output .= '</span></div>';

        return $output;
    }
}

LBV_Ajax_Load_Post::get_instance();