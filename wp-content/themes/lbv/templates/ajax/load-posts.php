<?php

if (!defined('ABSPATH')) exit;

class LBV_Load_Post {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('lbv/v1', '/load-posts', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array($this, 'load_posts'),
            'permission_callback' => '__return_true'
        ));
    }

    public function load_posts(WP_REST_Request $request) {
        $paged        = $request->get_param('paged') ? intval($request->get_param('paged')) : 1;
        $archive_id   = $request->get_param('archive_id') ? intval($request->get_param('archive_id')) : 0;
        $context      = $request->get_param('context') ? sanitize_text_field($request->get_param('context')) : 'category';
        $search_query = $request->get_param('search_query') ? sanitize_text_field($request->get_param('search_query')) : '';
        $is_home      = $request->get_param('is_home') ? intval($request->get_param('is_home')) : 0;
        $post_type_param = $request->get_param('post_type') ? sanitize_text_field($request->get_param('post_type')) : '';

        $posts_per_page = LBV_Theme_Settings::get_instance()->lbv_posts_per_page();
        $uncat_id = get_category_by_slug('uncategorized')->term_id ?? 0;

        $offset = ($paged - 1) * $posts_per_page;

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

        $response_data = array(
            'posts'        => $this->get_posts_html($query, $is_home),
            'has_more'     => $has_more,
            'max_pages'    => 999999,
            'current_page' => $paged,
            'post_count'   => $query->post_count,
        );

        if (!$has_more) {
            $response_data['notice'] = $this->end_list();
        }

        wp_reset_postdata();

        return rest_ensure_response($response_data);
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

LBV_Load_Post::get_instance();