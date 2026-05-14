<?php
/**
 * WP-CLI Image Converter - Convert JPG/PNG to WebP
 *
 * wp image-convert scan
 * wp image-convert process --quality=75
 * wp image-convert batch-convert --quality=75
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

class LBV_Image_Converter_CLI {

    private $webp_quality = 75;
    private $base_dir;
    private $base_url;

    public function __construct() {
        $upload_dir = wp_upload_dir();
        $this->base_dir = $upload_dir['basedir'];
        $this->base_url = $upload_dir['baseurl'];
    }

    public function scan($args, $assoc_args) {
        WP_CLI::line('🔍 Scanning for JPG/PNG images...');
        WP_CLI::line('');

        global $wpdb;

        $images = $wpdb->get_results(
            "SELECT ID, post_title, guid, post_mime_type
             FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type IN ('image/jpeg', 'image/jpg', 'image/png')
             ORDER BY ID DESC"
        );

        $total = count($images);
        $already_has_webp = 0;
        $can_convert = 0;

        WP_CLI::line("Total JPG/PNG images: $total");
        WP_CLI::line('---');

        $progress = \WP_CLI\Utils\make_progress_bar('Scanning', $total);

        foreach ($images as $image) {
            $progress->tick();

            $file_path = get_attached_file($image->ID);

            if (!file_exists($file_path)) {
                continue;
            }

            $webp_file = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);

            if (file_exists($webp_file)) {
                $already_has_webp++;
            } else {
                $can_convert++;
            }
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('Scan completed!');
        WP_CLI::line('');
        WP_CLI::line('📊 Results:');
        WP_CLI::line("  • Total images: $total");
        WP_CLI::line("  • Already WebP: $already_has_webp");
        WP_CLI::line("  • Can convert: $can_convert");
        WP_CLI::line('');
        WP_CLI::line('To convert all, run:');
        WP_CLI::line('  wp image-convert process --quality=75 --allow-root');
    }

    public function process($args, $assoc_args) {
        $quality = isset($assoc_args['quality']) ? (int)$assoc_args['quality'] : $this->webp_quality;

        WP_CLI::line('🔄 Converting JPG/PNG to WebP...');
        WP_CLI::line("Quality: $quality%");
        WP_CLI::line('');

        global $wpdb;

        $images = $wpdb->get_results(
            "SELECT ID, post_title, guid
             FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type IN ('image/jpeg', 'image/jpg', 'image/png')
             ORDER BY ID"
        );

        $total = count($images);
        $converted = 0;
        $skipped = 0;
        $failed = 0;

        WP_CLI::line("Processing $total images...");
        WP_CLI::line('---');

        $progress = \WP_CLI\Utils\make_progress_bar('Converting', $total);

        foreach ($images as $image) {
            $progress->tick();

            $file_path = get_attached_file($image->ID);

            if (!file_exists($file_path)) {
                $skipped++;
                continue;
            }

            $webp_file = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);

            if (file_exists($webp_file)) {
                $skipped++;
                continue;
            }

            $mime_type = get_post_mime_type($image->ID);
            $result = $this->convert_to_webp($file_path, $webp_file, $mime_type, $quality);

            if ($result) {
                $converted++;
            } else {
                $failed++;
            }

            // Convert thumbnails
            $meta = wp_get_attachment_metadata($image->ID);
            if (!empty($meta['sizes'])) {
                foreach ($meta['sizes'] as $size_data) {
                    $thumb_path = dirname($file_path) . '/' . $size_data['file'];
                    if (file_exists($thumb_path)) {
                        $thumb_webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $thumb_path);
                        if (!file_exists($thumb_webp)) {
                            $this->convert_to_webp($thumb_path, $thumb_webp, $mime_type, $quality);
                        }
                    }
                }
            }
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('Conversion completed!');
        WP_CLI::line('');
        WP_CLI::line('📊 Results:');
        WP_CLI::line("  • Total: $total");
        WP_CLI::line("  • Converted: $converted");
        WP_CLI::line("  • Skipped: $skipped");
        WP_CLI::line("  • Failed: $failed");
    }

    public function batch_convert($args, $assoc_args) {
        $quality = isset($assoc_args['quality']) ? (int)$assoc_args['quality'] : $this->webp_quality;
        $batch_size = isset($assoc_args['batch-size']) ? (int)$assoc_args['batch-size'] : 5;

        WP_CLI::line('🚀 Batch converting JPG/PNG to WebP...');
        WP_CLI::line("Quality: $quality%, Batch size: $batch_size");
        WP_CLI::line('');

        global $wpdb;

        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type IN ('image/jpeg', 'image/jpg', 'image/png')"
        );

        $offset = 0;
        $total_converted = 0;
        $total_failed = 0;

        while ($offset < $total) {
            $images = $wpdb->get_results($wpdb->prepare(
                "SELECT ID, post_title, guid
                 FROM {$wpdb->posts}
                 WHERE post_type = 'attachment'
                 AND post_mime_type IN ('image/jpeg', 'image/jpg', 'image/png')
                 ORDER BY ID
                 LIMIT %d OFFSET %d",
                $batch_size,
                $offset
            ));

            $batch_converted = 0;
            $batch_failed = 0;

            foreach ($images as $image) {
                $file_path = get_attached_file($image->ID);

                if (!file_exists($file_path)) {
                    continue;
                }

                $webp_file = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);

                if (file_exists($webp_file)) {
                    continue;
                }

                $mime_type = get_post_mime_type($image->ID);
                $result = $this->convert_to_webp($file_path, $webp_file, $mime_type, $quality);

                if ($result) {
                    $batch_converted++;
                    $total_converted++;
                } else {
                    $batch_failed++;
                    $total_failed++;
                }
            }

            $offset += $batch_size;
            $progress_pct = round(($offset / $total) * 100);

            WP_CLI::line("Progress: $progress_pct% ($offset/$total) | Batch: +$batch_converted converted");

            usleep(100000); // 0.1 second
        }

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('Batch conversion completed!');
        WP_CLI::line('');
        WP_CLI::line('📊 Results:');
        WP_CLI::line("  • Total converted: $total_converted");
        WP_CLI::line("  • Failed: $total_failed");
    }

    private function convert_to_webp($source_file, $webp_file, $mime_type, $quality) {
        if (!file_exists($source_file)) {
            return false;
        }

        if (extension_loaded('imagick')) {
            try {
                $image = new Imagick($source_file);
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($quality);

                if ($mime_type === 'image/png') {
                    $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
                    $image->setBackgroundColor(new ImagickPixel('transparent'));
                }

                $image->writeImage($webp_file);
                $image->clear();
                $image->destroy();
                return true;
            } catch (Exception $e) {
                error_log('Image convert error (Imagick): ' . $e->getMessage());
            }
        }

        return $this->convert_to_webp_gd($source_file, $webp_file, $mime_type, $quality);
    }

    private function convert_to_webp_gd($source_file, $webp_file, $mime_type, $quality) {
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
            $result = imagewebp($image, $webp_file, $quality);
            imagedestroy($image);
            return $result;
        }

        return false;
    }

    public function list_images($args, $assoc_args) {
        WP_CLI::line('📋 Image WebP Status');
        WP_CLI::line('');

        global $wpdb;

        $images = $wpdb->get_results(
            "SELECT ID, post_title, guid, post_mime_type
             FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
             AND post_mime_type IN ('image/jpeg', 'image/jpg', 'image/png')
             ORDER BY ID DESC
             LIMIT 50"
        );

        $rows = array();

        foreach ($images as $image) {
            $file_path = get_attached_file($image->ID);
            $webp_file = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);

            $has_webp = file_exists($webp_file) ? '✅' : '❌';
            $file_size = file_exists($file_path) ? size_format(filesize($file_path)) : 'N/A';
            $webp_size = file_exists($webp_file) ? size_format(filesize($webp_file)) : '-';

            $rows[] = array(
                'ID' => $image->ID,
                'Title' => substr($image->post_title, 0, 30),
                'Type' => str_replace('image/', '', $image->post_mime_type),
                'Size' => $file_size,
                'WebP' => $has_webp,
                'WebP Size' => $webp_size
            );
        }

        \WP_CLI\Utils\format_items('table', $rows, array('ID', 'Title', 'Type', 'Size', 'WebP', 'WebP Size'));
    }
}

WP_CLI::add_command('image-convert', 'LBV_Image_Converter_CLI');
WP_CLI::add_command('image-convert scan', ['LBV_Image_Converter_CLI', 'scan']);
WP_CLI::add_command('image-convert process', ['LBV_Image_Converter_CLI', 'process']);
WP_CLI::add_command('image-convert batch-convert', ['LBV_Image_Converter_CLI', 'batch_convert']);
WP_CLI::add_command('image-convert list', ['LBV_Image_Converter_CLI', 'list_images']);
