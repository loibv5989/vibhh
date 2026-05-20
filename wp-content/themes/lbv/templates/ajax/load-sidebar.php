<?php

defined( 'ABSPATH' ) || exit;

class LBV_Popular_Posts {

    private static $instance = null;

    private $nonce_action = 'lbv_popular_posts_nonce';

    private $posts_per_page = 5;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_load_popular_posts', array($this, 'load_posts'));
        add_action('wp_ajax_nopriv_load_popular_posts', array($this, 'load_posts'));
        add_action('wp_ajax_lbv_get_popular_nonce', array($this, 'get_nonce'));
        add_action('wp_ajax_nopriv_lbv_get_popular_nonce', array($this, 'get_nonce'));
    }

    public function get_nonce() {
        $this->clean_output_buffer();
        wp_send_json_success(array(
            'nonce' => wp_create_nonce($this->nonce_action)
        ));
    }

    private function verify_nonce() {
        if (!check_ajax_referer($this->nonce_action, 'nonce', false)) {
            wp_send_json_error(array('message' => __('Invalid security token', 'lbv')));
            exit;
        }
    }

    private function clean_output_buffer() {
        if (ob_get_length()) {
            ob_end_clean();
        }
    }

    private function get_reference_post_id($provided_id = 0, $post_type = 'post') {
        if ($provided_id > 0) {
            return $provided_id;
        }

        global $wpdb;

        $sql = $wpdb->prepare("
            SELECT p.ID
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
            WHERE p.post_type = %s AND p.post_status = 'publish'
            GROUP BY p.ID
            ORDER BY COUNT(c.comment_ID) DESC
            LIMIT 1
        ", $post_type);

        return intval($wpdb->get_var($sql));
    }

    private function get_posts_by_tags($reference_post_id, $offset, $limit, $post_type = 'post') {
        global $wpdb;

        $post_tags = wp_get_post_tags($reference_post_id);

        if (empty($post_tags)) {
            return array('posts' => array(), 'total' => 0);
        }

        $tag_ids = array_map(function($tag) {
            return intval($tag->term_id);
        }, $post_tags);

        $tag_ids_str = implode(',', $tag_ids);

        $sql = $wpdb->prepare("
            SELECT SQL_CALC_FOUND_ROWS DISTINCT p.*, COUNT(c.comment_ID) as comment_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE p.post_type = %s
            AND p.post_status = 'publish'
            AND p.ID != %d
            AND tt.term_id IN ($tag_ids_str)
            AND tt.taxonomy = 'post_tag'
            GROUP BY p.ID
            ORDER BY comment_count DESC
            LIMIT %d, %d
        ", $post_type, $reference_post_id, $offset, $limit);

        $posts = $wpdb->get_results($sql);
        $total = intval($wpdb->get_var("SELECT FOUND_ROWS()"));

        return array('posts' => $posts, 'total' => $total);
    }

    private function get_posts_by_categories($reference_post_id, $offset, $limit, $post_type = 'post') {
        global $wpdb;

        $post_categories = wp_get_post_categories($reference_post_id);

        if (empty($post_categories)) {
            return array('posts' => array(), 'total' => 0);
        }

        $cat_ids_str = implode(',', array_map('intval', $post_categories));

        // SỬA: Thêm %s cho post_type
        $sql = $wpdb->prepare("
            SELECT SQL_CALC_FOUND_ROWS DISTINCT p.*, COUNT(c.comment_ID) as comment_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE p.post_type = %s
            AND p.post_status = 'publish'
            AND p.ID != %d
            AND tt.term_id IN ($cat_ids_str)
            AND tt.taxonomy = 'category'
            GROUP BY p.ID
            ORDER BY comment_count DESC
            LIMIT %d, %d
        ", $post_type, $reference_post_id, $offset, $limit);

        $posts = $wpdb->get_results($sql);
        $total = intval($wpdb->get_var("SELECT FOUND_ROWS()"));

        return array('posts' => $posts, 'total' => $total);
    }

    private function get_all_popular_posts($offset, $limit, $post_type = 'post') {
        global $wpdb;

        $sql = $wpdb->prepare("
            SELECT SQL_CALC_FOUND_ROWS p.*, COUNT(c.comment_ID) as comment_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->comments} c ON p.ID = c.comment_post_ID
            WHERE p.post_type = %s
            AND p.post_status = 'publish'
            GROUP BY p.ID
            ORDER BY comment_count DESC
            LIMIT %d, %d
        ", $post_type, $offset, $limit);

        $posts = $wpdb->get_results($sql);
        $total = intval($wpdb->get_var("SELECT FOUND_ROWS()"));

        return array('posts' => $posts, 'total' => $total);
    }

    private function fetch_posts($reference_post_id, $offset, $limit, $post_type = 'post') {
        $result = $this->get_posts_by_tags($reference_post_id, $offset, $limit, $post_type);

        if (!empty($result['posts'])) {
            return array_merge($result, array('query_type' => 'tag'));
        }

        $result = $this->get_posts_by_categories($reference_post_id, $offset, $limit, $post_type);

        if (!empty($result['posts'])) {
            return array_merge($result, array('query_type' => 'category'));
        }

        $result = $this->get_all_popular_posts($offset, $limit, $post_type);

        return array_merge($result, array('query_type' => 'all'));
    }

    private function render_post_item($post) {
        $post_id   = isset($post->ID) ? (int) $post->ID : 0;
        $permalink = esc_url(get_permalink($post_id));

        $thumbnail = '';
        if (has_post_thumbnail($post_id)) {
            $thumb_html = get_the_post_thumbnail($post_id, 'medium');
            $thumbnail = '<div class="post-thumbnail"><a href="' . $permalink . '">' . $thumb_html . '</a></div>';
        }

        $title = esc_html(get_the_title($post_id));

        $date = apply_filters('lbv_modified_date', get_the_modified_date('', $post_id), $post_id);
        $extra_html = '<div class="post-meta"><span class="post-date">' . esc_html($date) . '</span></div>';

        return <<<HTML
<div class="popular-post-item">
    {$thumbnail}
    <div class="post-info">
        <div class="post-list-title">
            <a href="{$permalink}">{$title}</a>
        </div>
        {$extra_html}
    </div>
</div>
HTML;
    }


    public function load_posts() {
        $this->clean_output_buffer();
        $this->verify_nonce();

        $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = ($paged - 1) * $this->posts_per_page;

        $reference_post_id = isset($_POST['reference_post_id']) ? intval($_POST['reference_post_id']) : 0;
        $post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : 'post';

        if (!post_type_exists($post_type)) {
            $post_type = 'post';
        }

        $reference_post_id = $this->get_reference_post_id($reference_post_id, $post_type);

        $result = $this->fetch_posts($reference_post_id, $offset, $this->posts_per_page, $post_type);

        $posts = $result['posts'];
        $total_posts = $result['total'];
        $query_type = $result['query_type'];
        $max_pages = ceil($total_posts / $this->posts_per_page);

        $output = '';

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $output .= $this->render_post_item($post);
            }
        } else {
            $output = '<p>' . sprintf(__('No posts found for page %d', 'lbv'), $paged) . '</p>';
        }

        wp_send_json_success(array(
            'html'              => $output,
            'current_page'      => $paged,
            'max_pages'         => $max_pages,
            'post_count'        => count($posts),
            'found_posts'       => $total_posts,
            'reference_post_id' => $reference_post_id,
            'query_type'        => $query_type
        ));
    }

}

LBV_Popular_Posts::get_instance();
