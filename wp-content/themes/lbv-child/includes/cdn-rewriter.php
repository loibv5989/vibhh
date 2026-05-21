<?php

defined('ABSPATH') || exit;

class LBV_CDN_Rewriter {

    private static $instance = null;

    private $site_url = '';

    private $cdn_url  = '';

    private $includes = ['wp-content', 'wp-includes'];

    private $excludes = ['.php', 'logo.svg', 'logo_dark.svg', '.ico', 'vibhh-dark', 'vibhh-light', 'favicon', 'cropped-favicon'];

    private $excluded_thumbs = [];

    private function __construct() {
        add_action('template_redirect', [$this, 'init'], 0);
        add_filter('wpseo_xml_sitemap_img_src', [$this, 'rewrite_sitemap_img_src'], 10, 2);
        add_filter('wpseo_schema_person', [$this, 'lbv_remove_cdn_from_person_schema'], 10);
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        if (defined('REST_REQUEST') && REST_REQUEST) return;

        $this->site_url = rtrim(get_option('siteurl'), '/');
        $this->cdn_url  = LBV_CDN_URL;

        $author_id = 0;
        if (is_singular()) {
            $author_id = get_post_field('post_author', get_queried_object_id());
        } elseif (is_author()) {
            $author_id = get_queried_object_id();
        }

        if ($author_id) {
            $avatar_url = get_user_meta($author_id, 'wpseo_user_avatar', true);
            if (empty($avatar_url)) {
                $avatar_url = get_avatar_url($author_id);
            }

            if (!empty($avatar_url)) {
                $avatar_file = basename(parse_url($avatar_url, PHP_URL_PATH));
                $avatar_file = preg_replace('/-\d+x\d+(?=\.[a-zA-Z0-9]+$)/', '', $avatar_file);

                if (!empty($avatar_file)) {
                    $this->excludes[] = $avatar_file;
                }
            }
        }

        $this->start_buffer();
    }

    public function start_buffer() {
        ob_start(array($this, 'rewrite_html'));
    }

    public function rewrite_html($html) {
        if (empty($html)) {
            return $html;
        }

        $dirs_pattern = implode('|', array_map('preg_quote', $this->includes));
        $site         = preg_quote($this->site_url, '#');

        $pattern = '#(?<=["\'\s(,=])' . $site . '/(?:' . $dirs_pattern . ')/\S+?(?=["\'\s),>])#';
        $html = preg_replace_callback($pattern, array($this, 'maybe_rewrite_url'), $html);

        $site_esc    = preg_quote(str_replace('/', '\/', $this->site_url), '#');
        $pattern_esc = '#' . $site_esc . '\\\\/(?:' . $dirs_pattern . ')\\\\/[^"]+#';
        $html = preg_replace_callback($pattern_esc, array($this, 'maybe_rewrite_url_escaped'), $html);

        return $html;
    }

    private function maybe_rewrite_url($matches) {
        $url = $matches[0];

        foreach ($this->excludes as $exclude) {
            if (stripos($url, $exclude) !== false) {
                return $url;
            }
        }

        if (!empty($this->excluded_thumbs)) {
            $filename = basename(parse_url($url, PHP_URL_PATH));
            if (in_array($filename, $this->excluded_thumbs, true)) {
                return $url;
            }
        }

        return str_replace($this->site_url, $this->cdn_url, $url);
    }

    private function maybe_rewrite_url_escaped($matches) {
        $url_escaped = $matches[0];
        $url = str_replace('\/', '/', $url_escaped);

        foreach ($this->excludes as $exclude) {
            if (stripos($url, $exclude) !== false) {
                return $url_escaped;
            }
        }

        if (!empty($this->excluded_thumbs)) {
            $filename = basename(parse_url($url, PHP_URL_PATH));
            if (in_array($filename, $this->excluded_thumbs, true)) {
                return $url_escaped;
            }
        }

        $rewritten = str_replace($this->site_url, $this->cdn_url, $url);
        return str_replace('/', '\/', $rewritten);
    }

    public function rewrite_sitemap_img_src($src, $post = null) {
        if (empty($src)) {
            return $src;
        }

        $this->site_url = rtrim(home_url(), '/');
        $this->cdn_url  = rtrim(LBV_CDN_URL, '/');

        if (!empty($this->excludes)) {
            foreach ($this->excludes as $exclude) {
                if (stripos($src, $exclude) !== false) {
                    return $src;
                }
            }
        }

        $site_host = wp_parse_url($this->site_url, PHP_URL_HOST);
        $cdn_host  = wp_parse_url($this->cdn_url, PHP_URL_HOST);

        if ($site_host && $cdn_host) {
            return str_replace('://' . $site_host, '://' . $cdn_host, $src);
        }

        return str_replace($this->site_url, $this->cdn_url, $src);
    }

    public function lbv_remove_cdn_from_person_schema( $data ) {
        if ( isset( $data['image']['url'] ) ) {
            $site_url = rtrim( home_url(), '/' );
            $cdn_url = rtrim( LBV_CDN_URL, '/' );


            $data['image']['url']        = str_replace( $cdn_url, $site_url, $data['image']['url'] );
            $data['image']['contentUrl'] = str_replace( $cdn_url, $site_url, $data['image']['contentUrl'] );
        }

        return $data;
    }
}

LBV_CDN_Rewriter::get_instance();