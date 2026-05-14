<?php

defined( 'ABSPATH' ) || exit;

class LBV_Ajax_Search {
    private static $instance = null;
    private $tag_limit = 4;
    private $live_search_limit = 5;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_ajax_search', array($this, 'live_search'));
        add_action('wp_ajax_nopriv_ajax_search', array($this, 'live_search'));
    }

    private function theme_settings(){
        $settings = LBV_Theme_Settings::get_instance();
        return $settings;
    }

    private function get_search_query_args($search_term, $paged = 1, $posts_per_page = null) {
        if (null === $posts_per_page) {
            $posts_per_page = $this->theme_settings()->lbv_posts_per_page();
        }

        $excluded_pages = $this->theme_settings()->get_excluded_page_ids();

        $args = array(
            's'                   => sanitize_text_field($search_term),
            'post_type'           => ['page', 'post'],
            'post_status'         => 'publish',
            'posts_per_page'      => intval($posts_per_page),
            'paged'               => intval($paged),
            'ignore_sticky_posts' => true
        );

        if (!empty($excluded_pages)) {
            $args['post__not_in'] = $excluded_pages;
        }

        return $args;
    }

    public function live_search() {
        $search_query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

        if (empty($search_query)) {
            wp_send_json_error(array('message' => __('Empty search query', 'lbv')));
            wp_die();
        }

        $args = $this->get_search_query_args($search_query, 1, $this->live_search_limit);
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<div class="search-results-list">';

            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail');
                ?>
                <div class="p-wrap">
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="h-result-item">
                        <?php if ($thumbnail) : ?>
                            <img src="<?php echo esc_url($thumbnail); ?>"
                                 alt="<?php echo esc_attr(get_the_title()); ?>"
                                 class="result-thumbnail">
                        <?php endif; ?>
                        <div class="result-content">
                            <span class="r-post-title"><?php the_title(); ?></span>
                        </div>
                    </a>
                </div>
                <?php
            }

            echo '</div>';

            $search_url = esc_url(home_url('/?s=' . urlencode($search_query)));
            echo '<div class="live-search-link">';
            echo '<a class="is-btn" href="' . $search_url . '">' . __('More Results', 'lbv') . '</a>';
            echo '</div>';

            wp_reset_postdata();
        } else {
            echo '<div class="no-results">' . __('No results found', 'lbv') . '</div>';
        }

        wp_die();
    }
}

LBV_Ajax_Search::get_instance();