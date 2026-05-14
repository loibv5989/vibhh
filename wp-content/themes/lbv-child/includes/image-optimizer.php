<?php

if (!defined('ABSPATH')) {
    exit;
}

class LBV_Image_Optimizer {

    private $webp_quality = 75;

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
        add_filter('wp_img_tag_add_decoding_attr', '__return_false');
        add_filter('wp_editor_set_quality', array($this, 'set_webp_quality'), 10, 2);
        add_filter('wp_generate_attachment_metadata', array($this, 'process_images_on_upload'), 10, 2);
        add_filter('wp_get_attachment_image_attributes', array($this, 'lbv_optimize_listing_images'), 12, 3);
        add_filter('wp_calculate_image_srcset', array($this, 'lbv_calculate_image_srcset'), 10, 5);
        add_filter('image_resize_dimensions', array($this, 'no_crop_portrait_for_any_150_width'), 10, 6 );
        add_filter('the_content', array($this, 'lbv_optimize_single_post_images'), 12);
    }

    public function lbv_optimize_single_post_images($content) {

        if (!is_singular(['idol', 'group', 'photo', 'actor', 'v_star', 'post']) || empty($content)) {
            return $content;
        }

        $image_count = 0;
        $content = preg_replace_callback(
            '/<img([^>]+)>/i',
            function($matches) use (&$image_count) {
                $image_count++;
                $img_tag = $matches[0];

                if (strpos($img_tag, 'sizes=') !== false) {
                    $img_tag = preg_replace(
                        '/sizes=["\'][^"\']*["\']/i',
                        'sizes="(max-width: 480px) 375px, (max-width: 768px) 480px, (max-width: 1200px) 768px, 1024px"',
                        $img_tag
                    );
                } else {
                    $img_tag = str_replace('<img', '<img sizes="(max-width: 480px) 375px, (max-width: 768px) 480px, (max-width: 1200px) 768px, 1024px"', $img_tag);
                }

                if ($image_count === 1) {
                    if (strpos($img_tag, 'loading=') !== false) {
                        $img_tag = preg_replace('/loading=["\']lazy["\']/i', 'loading="eager"', $img_tag);
                    } else {
                        $img_tag = str_replace('<img', '<img loading="eager"', $img_tag);
                    }

                    if (strpos($img_tag, 'fetchpriority=') !== false) {
                        $img_tag = preg_replace('/fetchpriority=["\'][^"\']*["\']/i', 'fetchpriority="high"', $img_tag);
                    } else {
                        $img_tag = str_replace('<img', '<img fetchpriority="high"', $img_tag);
                    }
                }

                return $img_tag;
            },
            $content
        );

        return $content;
    }

    public function lbv_calculate_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {

        if (is_singular(['idol', 'group', 'actor', 'v_star', 'post'])) {
            unset($sources[150]);
            return $sources;
        }

        return $sources;
    }

    public function lbv_optimize_listing_images($attr, $attachment, $size) {

        if ($size === 'medium' || $size === 'thumbnail') {
            unset($attr['srcset'], $attr['sizes']);
        }

//        if (is_singular(['idol', 'group', 'actor', 'photo', 'v_star', 'post'])) {
//            return $attr;
//        }

        if ($size === 'medium') {
            static $thumbnail_counter = 0;
            $thumbnail_counter++;

            if ($thumbnail_counter <= 6) {
                $attr['loading'] = 'eager';
                $attr['fetchpriority'] = 'high';
            } else {
                $attr['loading'] = 'lazy';
            }
        }

        return $attr;
    }

    public function set_webp_quality($quality, $mime_type) {
        if ($mime_type === 'image/webp') {
            return $this->webp_quality;
        }
        return $quality;
    }

    public function process_images_on_upload($metadata, $attachment_id) {
        $file = get_attached_file($attachment_id);
        $mime_type = get_post_mime_type($attachment_id);
        $path_info = pathinfo($file);

        if (!in_array($mime_type, array('image/jpeg', 'image/jpg', 'image/png'))) {
            return $metadata;
        }

        $webp_file = $path_info['dirname'] . '/' . $path_info['filename'] . '.webp';
        $this->convert_to_webp($file, $webp_file, $mime_type);

        if (!empty($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumbnail_path = $path_info['dirname'] . '/' . $size_data['file'];
                $thumbnail_info = pathinfo($thumbnail_path);
                $thumbnail_webp = $thumbnail_info['dirname'] . '/' . $thumbnail_info['filename'] . '.webp';

                if (file_exists($thumbnail_path)) {
                    $this->convert_to_webp($thumbnail_path, $thumbnail_webp, $mime_type);
                }
            }
        }

        return $metadata;
    }

    private function convert_to_webp($source_file, $webp_file, $mime_type) {
        if (!file_exists($source_file)) {
            return false;
        }

        if (extension_loaded('imagick')) {
            try {
                $image = new Imagick($source_file);
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($this->webp_quality);

                if ($mime_type === 'image/png') {
                    $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
                    $image->setBackgroundColor(new ImagickPixel('transparent'));
                }

                $image->writeImage($webp_file);
                $image->clear();
                $image->destroy();
                return true;
            } catch (Exception $e) {
                error_log('LBV Image Optimizer - Imagick conversion failed: ' . $e->getMessage());
            }
        }

        return $this->convert_to_webp_gd($source_file, $webp_file, $mime_type);
    }

    private function convert_to_webp_gd($source_file, $webp_file, $mime_type) {
        if (!function_exists('imagewebp')) {
            return false;
        }

        $image = null;

        if (in_array($mime_type, array('image/jpeg', 'image/jpg'))) {
            $image = imagecreatefromjpeg($source_file);
        } elseif ($mime_type === 'image/png') {
            $image = imagecreatefrompng($source_file);

            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        if ($image) {
            $result = imagewebp($image, $webp_file, $this->webp_quality);
            imagedestroy($image);
            return $result;
        }

        return false;
    }

    public function no_crop_portrait_for_any_150_width( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ) {
        if ( $new_w == 150 ) {

            $ratio = $orig_w / $orig_h;
            if ( $ratio < 0.7 ) {
                return false;
            }
        }

        return $default;
    }
}

LBV_Image_Optimizer::get_instance();
