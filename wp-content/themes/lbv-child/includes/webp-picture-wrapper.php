<?php

if (!defined('ABSPATH')) {
    exit;
}

class LBV_WebP_Picture_Wrapper {

    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_filter('the_content', array($this, 'wrap_content_images'), 999);
    }

    public function wrap_content_images($content) {
        if (!is_singular(['idol', 'group', 'photo', 'actor', 'v_star', 'post'])) {
            return $content;
        }

        return preg_replace_callback(
            '/<img([^>]+)>/i',
            array($this, 'create_picture_element'),
            $content
        );
    }

    private function create_picture_element($matches) {
        $img_tag = $matches[0];

        $src = $this->extract_attribute($img_tag, 'src');
        $srcset = $this->extract_attribute($img_tag, 'srcset');
        $sizes = $this->extract_attribute($img_tag, 'sizes');

        if (empty($src)) {
            return $img_tag;
        }

        if ($this->is_webp_url($src)) {
            return $img_tag;
        }

        $has_webp = $this->check_webp_exists($src);

        if (!$has_webp) {
            return $img_tag;
        }

        $webp_src = $this->convert_url_to_webp($src);
        $webp_srcset = $this->convert_srcset_to_webp($srcset);

        $picture = '<picture>';

        $picture .= '<source type="image/webp"';
        if (!empty($webp_srcset)) {
            $picture .= ' srcset="' . esc_attr($webp_srcset) . '"';
        } else {
            $picture .= ' srcset="' . esc_attr($webp_src) . '"';
        }
        if (!empty($sizes)) {
            $picture .= ' sizes="' . esc_attr($sizes) . '"';
        }
        $picture .= '>';
        $picture .= $img_tag;
        $picture .= '</picture>';

        return $picture;
    }

    private function extract_attribute($img_tag, $attribute) {
        if (preg_match('/' . $attribute . '=["\']([^"\']+)["\']/i', $img_tag, $match)) {
            return $match[1];
        }
        return '';
    }

    private function is_webp_url($url) {
        return preg_match('/\.webp$/i', $url);
    }

    private function convert_url_to_webp($url) {
        return preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $url);
    }

    private function convert_srcset_to_webp($srcset) {
        if (empty($srcset)) {
            return '';
        }

        if (preg_match('/\.webp/i', $srcset)) {
            return '';
        }

        return preg_replace('/\.(jpg|jpeg|png)(\s|,|$)/i', '.webp$2', $srcset);
    }

    private function check_webp_exists($url) {
        if ($this->is_webp_url($url)) {
            return false;
        }

        static $cache = array();

        if (isset($cache[$url])) {
            return $cache[$url];
        }

        $webp_url = $this->convert_url_to_webp($url);

        $upload_dir = wp_upload_dir();
        $webp_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url);

        $exists = file_exists($webp_path);
        $cache[$url] = $exists;

        return $exists;
    }
}

LBV_WebP_Picture_Wrapper::get_instance();