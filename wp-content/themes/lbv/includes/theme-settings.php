<?php

defined('ABSPATH') || exit;

class LBV_Theme_Settings {

    private static $instance = null;

    private $lbv_options = null;

    private $lbv_option_key = 'lbv_options';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('upload_mimes', array($this, 'mime_types'), 1, 1);
        add_action('pre_get_posts', array($this, 'modify_main_query'));
    }

    public function mime_types($mime_types) {
        $mime_types['svg'] = 'image/svg+xml';
        $mime_types['ico'] = 'image/x-icon';
        $mime_types['webp'] = 'image/webp';
        $mime_types['zip'] = 'application/zip';
        $mime_types['rar'] = 'application/x-rar-compressed';

        return $mime_types;
    }

    public function modify_main_query($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if (is_category() || is_tag() || is_author() || is_archive() || is_search()) {
            $query->set('posts_per_page', $this->lbv_posts_per_page());
        }

        if (is_front_page()) {
            $query->set('posts_per_page', $this->lbv_home_post_per_page());
        }

        if ($query->is_search() || is_author()) {
            $query->set('post_type', $this->post_type());
        }
        if ($query->is_search() || is_author() || is_category() || is_tag()) {
            $excluded_pages = $this->get_excluded_page_ids();
            if (!empty($excluded_pages)) {
                $query->set('post__not_in', $excluded_pages);
            }
        }
    }

    public function get_option($key, $default = '') {
        if (null === $this->lbv_options) {
            $this->lbv_options = get_option($this->lbv_option_key, array());
        }

        return isset($this->lbv_options[$key]) ? $this->lbv_options[$key] : $default;
    }

    private function post_type(){
        $post_type = [ 'post', 'page'];
        return $post_type;
    }

    public function get_excluded_page_ids() {
        return array_filter(array_unique([
            51556, 36621, 1481, 36630, 36636, 36641, 51505
        ]));
    }

    private function lbv_const( $name, $default ) {
        return defined($name) ? constant($name) : $default;
    }

    public function lbv_posts_per_page() {
        return $this->lbv_const('LBV_POSTS_PER_PAGE', 15);
    }

    public function lbv_home_post_per_page() {
        return $this->lbv_const('LBV_HOME_POSTS_PER_PAGE', 10);
    }

    public function lbv_tag_load_more() {
        return $this->lbv_const('LBV_TAG_LOAD_MORE', 0);
    }

    public function lbv_category_load_more() {
        return $this->lbv_const('LBV_CATEGORY_LOAD_MORE', 0);
    }

    public function lbv_search_load_more() {
        return $this->lbv_const('LBV_SEARCH_LOAD_MORE', 0);
    }

    public function lbv_logout_url(){
        $current_url = home_url(add_query_arg([]));
        return wp_logout_url($current_url);
    }

    public function lbv_git_client_id() {
        return $this->get_option('git_client_id');
    }

    public function lbv_git_client_secret() {
        return $this->get_option('git_client_secret');
    }

    public function lbv_google_client_id() {
        return $this->get_option('google_client_id');
    }

    public function lbv_google_client_secret() {
        return $this->get_option('google_client_secret');
    }

    public function lbv_site_logo(){
        $main_logo = $this->get_option('main_logo');
        $dark_logo = $this->get_option('dark_logo');
        $logo_alt  =  get_bloginfo('name') . ' Logo';
        ?><a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="<?php echo esc_attr($logo_alt); ?>">
        <?php if ($main_logo || $dark_logo) : ?>
            <?php if ($main_logo) : ?><?php echo wp_get_attachment_image($main_logo, 'full', false, array(
                    'alt' => $logo_alt,
                    'class' => 'logo-light'
                ));
                ?>
            <?php endif; ?>

            <?php if ($dark_logo) : ?>
            <?php echo wp_get_attachment_image($dark_logo, 'full', false, array(
                'alt' => $logo_alt,
                'class' => 'logo-dark'
            ));
            ?><?php endif; ?><?php else : ?>
            <span class="logo-text"><?php bloginfo('name'); ?></span><?php endif; ?></a>
        <?php
    }

    public function is_profile($category) {
        $is_profile = false;
        $current_term = $category;

        while ($current_term && !is_wp_error($current_term)) {
            if ($current_term->slug === 'profiles') {
                $is_profile = true;
                break;
            }
            $current_term = ($current_term->parent) ? get_term($current_term->parent, 'category') : null;
        }

        return $is_profile;
    }
}

LBV_Theme_Settings::get_instance();
