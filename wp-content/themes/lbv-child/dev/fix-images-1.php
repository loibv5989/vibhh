<?php
/**
 * WP-CLI Image Fixer - SAFE VERSION
 *
 * wp image-fixer scan
 * wp image-fixer fix-metadata
 * wp image-fixer import-orphans
 * wp image-fixer fix-all
 * wp image-fixer reimport-missing
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

class LBV_Image_Fixer_CLI {

    public function scan($args, $assoc_args) {
        WP_CLI::line('🔍 Scanning for image issues...');
        WP_CLI::line('');

        global $wpdb;

        $posts = $wpdb->get_results(
            "SELECT ID, post_title, post_content 
             FROM {$wpdb->posts} 
             WHERE post_type IN ('post', 'page') 
             AND post_status = 'publish'
             AND post_content LIKE '%<img%'"
        );

        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $base_dir = $upload_dir['basedir'];

        $missing_dimensions = 0;
        $orphaned_images = 0;
        $missing_files = 0;
        $posts_with_issues = 0;

        $total = count($posts);
        $progress = \WP_CLI\Utils\make_progress_bar('Scanning', $total);

        foreach ($posts as $post) {
            $progress->tick();

            preg_match_all('/<img([^>]+)>/i', $post->post_content, $matches);

            if (empty($matches[0])) {
                continue;
            }

            $post_has_issues = false;

            foreach ($matches[0] as $img_tag) {
                if (!preg_match('/src=["\']([^"\']+)["\']/i', $img_tag, $src_match)) {
                    continue;
                }

                $img_url = $src_match[1];

                if (strpos($img_url, $base_url) === false) {
                    continue;
                }

                $filename = basename($img_url);

                $has_width = preg_match('/width=["\']([^"\']+)["\']/i', $img_tag);
                $has_height = preg_match('/height=["\']([^"\']+)["\']/i', $img_tag);

                if (!$has_width || !$has_height) {
                    if (!$post_has_issues) {
                        $post_has_issues = true;
                        $posts_with_issues++;
                    }
                    $missing_dimensions++;
                }

                $in_db = $wpdb->get_var($wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} 
                     WHERE guid LIKE %s 
                     AND post_type = 'attachment'",
                    '%' . $wpdb->esc_like($filename)
                ));

                $file_path = str_replace($base_url, $base_dir, $img_url);
                $file_exists = file_exists($file_path);

                if ($file_exists && !$in_db) {
                    if (!$post_has_issues) {
                        $post_has_issues = true;
                        $posts_with_issues++;
                    }
                    $orphaned_images++;
                }

                if (!$file_exists) {
                    if (!$post_has_issues) {
                        $post_has_issues = true;
                        $posts_with_issues++;
                    }
                    $missing_files++;
                }
            }
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('Scan completed!');
        WP_CLI::line('');
        WP_CLI::line('📊 Results:');
        WP_CLI::line("  • Posts scanned: $total");
        WP_CLI::line("  • Posts with issues: $posts_with_issues");
        WP_CLI::line("  • Missing dimensions: $missing_dimensions");
        WP_CLI::line("  • Orphaned images: $orphaned_images");
        WP_CLI::line("  • Missing files: $missing_files");

        if ($missing_dimensions > 0) {
            WP_CLI::line('');
            WP_CLI::line('💡 To fix missing dimensions, add filter to functions.php');
        }

        if ($orphaned_images > 0) {
            WP_CLI::line('');
            WP_CLI::line('💡 To import orphaned images:');
            WP_CLI::line('   wp image-fixer import-orphans --allow-root');
        }

        if ($missing_files > 0) {
            WP_CLI::line('');
            WP_CLI::warning('⚠️  Some image files are missing! Restore from backup first.');
        }
    }

    public function fix_metadata($args, $assoc_args) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Tắt plugins hooks để chạy nhanh
        error_reporting(E_ERROR | E_PARSE);

        WP_CLI::line('🔧 Fixing image metadata...');
        WP_CLI::line('');

        global $wpdb;

        $images = $wpdb->get_col(
            "SELECT ID FROM {$wpdb->posts} 
             WHERE post_type = 'attachment' 
             AND post_mime_type LIKE 'image/%'"
        );

        $total = count($images);
        $fixed = 0;
        $skipped = 0;

        WP_CLI::line("Total images: $total");
        WP_CLI::line('---');

        $progress = \WP_CLI\Utils\make_progress_bar('Processing', $total);

        foreach ($images as $id) {
            $progress->tick();

            $file = get_attached_file($id);

            if (!file_exists($file)) {
                $skipped++;
                continue;
            }

            $meta = wp_get_attachment_metadata($id);

            if (empty($meta['width']) || empty($meta['height'])) {
                $new_meta = wp_generate_attachment_metadata($id, $file);

                if ($new_meta && !empty($new_meta['width']) && !empty($new_meta['height'])) {
                    wp_update_attachment_metadata($id, $new_meta);
                    $fixed++;
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('Metadata fix completed!');
        WP_CLI::line('');
        WP_CLI::line('📊 Results:');
        WP_CLI::line("  • Total: $total");
        WP_CLI::line("  • Fixed: $fixed");
        WP_CLI::line("  • Skipped: $skipped");
    }

    public function import_orphans($args, $assoc_args) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Tắt plugins hooks
        error_reporting(E_ERROR | E_PARSE);

        WP_CLI::line('📦 Importing orphaned images...');
        WP_CLI::line('');

        global $wpdb;

        $posts = $wpdb->get_results(
            "SELECT ID, post_content, post_author 
             FROM {$wpdb->posts} 
             WHERE post_type IN ('post', 'page') 
             AND post_status = 'publish'
             AND post_content LIKE '%<img%'"
        );

        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $base_dir = $upload_dir['basedir'];

        $orphaned_files = array();

        foreach ($posts as $post) {
            preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post->post_content, $matches);

            if (empty($matches[1])) {
                continue;
            }

            foreach ($matches[1] as $img_url) {
                if (strpos($img_url, $base_url) === false) {
                    continue;
                }

                $filename = basename($img_url);
                $file_path = str_replace($base_url, $base_dir, $img_url);

                if (!file_exists($file_path)) {
                    continue;
                }

                // ✅ Tìm xem đã có attachment với filename này chưa
                $existing = $wpdb->get_var($wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} 
                     WHERE post_type = 'attachment'
                     AND guid LIKE %s
                     LIMIT 1",
                    '%' . $wpdb->esc_like($filename)
                ));

                // ✅ Chỉ import nếu THỰC SỰ chưa có
                if (!$existing) {
                    $orphaned_files[$file_path] = array(
                        'path' => $file_path,
                        'url' => $img_url,
                        'filename' => $filename,
                        'post_id' => $post->ID,
                        'post_author' => $post->post_author
                    );
                }
            }
        }

        $total = count($orphaned_files);

        if ($total === 0) {
            WP_CLI::success('No orphaned images found!');
            return;
        }

        WP_CLI::line("Found $total orphaned images");
        WP_CLI::line('---');

        $progress = \WP_CLI\Utils\make_progress_bar('Importing', $total);

        $imported = 0;
        $failed = 0;

        foreach ($orphaned_files as $file_data) {
            $progress->tick();

            $file_path = $file_data['path'];
            $filename = $file_data['filename'];

            $filetype = wp_check_filetype($filename, null);
            $relative_path = str_replace($base_dir . '/', '', $file_path);

            // ✅ Thêm post_parent và post_author
            $attachment = array(
                'guid'           => $base_url . '/' . str_replace('\\', '/', $relative_path),
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
                'post_content'   => '',
                'post_status'    => 'inherit',
                'post_parent'    => $file_data['post_id'],
                'post_author'    => $file_data['post_author']
            );

            $attachment_id = wp_insert_attachment($attachment, $file_path, $file_data['post_id']);

            if (is_wp_error($attachment_id)) {
                $failed++;
                continue;
            }

            $metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $metadata);

            $imported++;
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('Import completed!');
        WP_CLI::line('');
        WP_CLI::line('📊 Results:');
        WP_CLI::line("  • Total: $total");
        WP_CLI::line("  • Imported: $imported");
        WP_CLI::line("  • Failed: $failed");
    }

    public function reimport_missing($args, $assoc_args) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Tắt warnings
        error_reporting(E_ERROR | E_PARSE);

        WP_CLI::line('🔧 Re-importing missing attachments...');
        WP_CLI::line('');

        global $wpdb;

        $posts = $wpdb->get_results(
            "SELECT ID, post_content, post_title, post_author FROM {$wpdb->posts} 
             WHERE post_type IN ('post', 'page') 
             AND post_status = 'publish'
             AND post_content LIKE '%wp:image%'"
        );

        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $base_url = $upload_dir['baseurl'];

        $imported = 0;
        $reused = 0;
        $fixed_posts = 0;
        $id_map = array();

        foreach ($posts as $post) {
            $content = $post->post_content;
            $updated = false;

            preg_match_all('/<!-- wp:image \{"id":(\d+)[^}]*\} -->.*?<img[^>]+src="([^"]+)"[^>]*class="[^"]*wp-image-(\d+)/s', $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $block_id = $match[1];
                $img_url = $match[2];
                $class_id = $match[3];

                // Kiểm tra attachment có tồn tại không
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} WHERE ID = %d AND post_type = 'attachment'",
                    $block_id
                ));

                if (!$exists) {
                    if (isset($id_map[$block_id])) {
                        $new_id = $id_map[$block_id];
                    } else {
                        $file_path = str_replace($base_url, $base_dir, $img_url);
                        $filename = basename($file_path);

                        if (!file_exists($file_path)) {
                            continue;
                        }

                        // ✅ TÌM ATTACHMENT ĐÃ CÓ TRƯỚC
                        $existing_att = $wpdb->get_var($wpdb->prepare(
                            "SELECT ID FROM {$wpdb->posts} 
                             WHERE post_type = 'attachment' 
                             AND guid LIKE %s 
                             ORDER BY ID DESC LIMIT 1",
                            '%' . $wpdb->esc_like($filename)
                        ));

                        if ($existing_att) {
                            // ✅ DÙNG LẠI
                            $new_id = $existing_att;
                            $reused++;

                            // Update post_parent nếu chưa có
                            $current_parent = $wpdb->get_var($wpdb->prepare(
                                "SELECT post_parent FROM {$wpdb->posts} WHERE ID = %d",
                                $new_id
                            ));

                            if ($current_parent == 0) {
                                $wpdb->update(
                                    $wpdb->posts,
                                    array(
                                        'post_parent' => $post->ID,
                                        'post_author' => $post->post_author
                                    ),
                                    array('ID' => $new_id),
                                    array('%d', '%d'),
                                    array('%d')
                                );
                            }
                        } else {
                            // ✅ TẠO MỚI
                            $filetype = wp_check_filetype($filename, null);
                            $title = sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME));

                            $attachment = array(
                                'guid'           => $img_url,
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => $title,
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                                'post_parent'    => $post->ID,
                                'post_author'    => $post->post_author
                            );

                            $new_id = wp_insert_attachment($attachment, $file_path, $post->ID);

                            if (is_wp_error($new_id)) {
                                continue;
                            }

                            $metadata = wp_generate_attachment_metadata($new_id, $file_path);
                            wp_update_attachment_metadata($new_id, $metadata);

                            $imported++;
                        }

                        $id_map[$block_id] = $new_id;
                    }

                    // Replace trong content
                    $content = str_replace('"id":'.$block_id, '"id":'.$new_id, $content);
                    $content = str_replace('wp-image-'.$class_id, 'wp-image-'.$new_id, $content);
                    $updated = true;
                }
            }

            if ($updated) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'post_content' => $content
                ));
                $fixed_posts++;
            }
        }

        WP_CLI::line('');
        WP_CLI::success("Import completed!");
        WP_CLI::line("New imports: {$imported} attachments");
        WP_CLI::line("Reused existing: {$reused} attachments");
        WP_CLI::line("Fixed: {$fixed_posts} posts");
    }

    public function fix_all($args, $assoc_args) {
        WP_CLI::line('🚀 Running complete fix...');
        WP_CLI::line('');

        WP_CLI::line('═══════════════════════════════════════');
        WP_CLI::line('STEP 1: Importing orphaned images');
        WP_CLI::line('═══════════════════════════════════════');
        $this->import_orphans($args, $assoc_args);

        WP_CLI::line('');
        WP_CLI::line('═══════════════════════════════════════');
        WP_CLI::line('STEP 2: Fixing metadata');
        WP_CLI::line('═══════════════════════════════════════');
        $this->fix_metadata($args, $assoc_args);

        WP_CLI::line('');
        WP_CLI::line('═══════════════════════════════════════');
        WP_CLI::line('STEP 3: Re-importing missing references');
        WP_CLI::line('═══════════════════════════════════════');
        $this->reimport_missing($args, $assoc_args);

        WP_CLI::line('');
        WP_CLI::line('═══════════════════════════════════════');
        WP_CLI::line('STEP 4: Final scan');
        WP_CLI::line('═══════════════════════════════════════');
        $this->scan($args, $assoc_args);

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::success('🎉 All fixes completed!');
    }

    public function cleanup_unused($args, $assoc_args) {
        WP_CLI::line('🧹 Finding unused images...');
        WP_CLI::line('');

        // ⚠️ Dry-run mode mặc định
        $dry_run = !isset($assoc_args['force']);

        if ($dry_run) {
            WP_CLI::warning('DRY-RUN MODE: No files will be deleted. Use --force to actually delete.');
            WP_CLI::line('');
        }

        global $wpdb;

        // Tìm tất cả attachments
        $attachments = $wpdb->get_results(
            "SELECT ID, post_title, guid, post_parent 
         FROM {$wpdb->posts} 
         WHERE post_type = 'attachment' 
         AND post_mime_type LIKE 'image/%'
         ORDER BY ID DESC"
        );

        $total = count($attachments);
        $unused_count = 0;
        $protected_count = 0;
        $in_use_count = 0;
        $deleted_count = 0;
        $deleted_size = 0;

        WP_CLI::line("Checking $total attachments...");
        WP_CLI::line('---');

        $progress = \WP_CLI\Utils\make_progress_bar('Scanning', $total);

        foreach ($attachments as $att) {
            $progress->tick();

            $filename = basename($att->guid);
            $att_id = $att->ID;

            // ✅ LOẠI TRỪ: logo, banner, avatar (case-insensitive, anywhere in filename)
            $protected_patterns = array(
                '/logo/i',
                '/banner/i',
                '/avatar/i',
                '/favicon/i',
            );

            $is_protected = false;
            foreach ($protected_patterns as $pattern) {
                if (preg_match($pattern, $filename)) {
                    $is_protected = true;
                    break;
                }
            }

            if ($is_protected) {
                $protected_count++;
                continue;
            }

            // ✅ LẤY BASE FILENAME (không kể -WIDTHxHEIGHT)
            // VD: "3C-All-in-One-Toolbox-APK-Screenshort-15-1-150x150.jpg"
            //  -> "3C-All-in-One-Toolbox-APK-Screenshort-15-1"
            $base_filename = preg_replace('/-\d+x\d+(\.(jpg|jpeg|png|gif|webp))?$/i', '', $filename);
            // Remove extension
            $base_filename = preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '', $base_filename);

            // Check 1: Có trong post content không? (search base filename)
            $in_content = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} 
             WHERE post_type IN ('post', 'page') 
             AND post_status IN ('publish', 'draft', 'pending', 'future')
             AND post_content LIKE %s",
                '%' . $wpdb->esc_like($base_filename) . '%'
            ));

            // Check 2: Là featured image không?
            $is_featured = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->postmeta} 
             WHERE meta_key = '_thumbnail_id' 
             AND meta_value = %d",
                $att_id
            ));

            // Check 3: Có post_parent không?
            $has_parent = (int)$att->post_parent;

            // ✅ Check post_parent có tồn tại không (post có thể đã bị xóa)
            if ($has_parent > 0) {
                $parent_exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->posts} 
                 WHERE ID = %d 
                 AND post_status IN ('publish', 'draft', 'pending', 'future')",
                    $has_parent
                ));

                // Nếu parent post đã xóa -> coi như orphaned
                if ($parent_exists == 0) {
                    $has_parent = 0;
                }
            }

            // Check 4: Trong widget/menu/customizer không?
            $in_options = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->options} 
             WHERE option_value LIKE %s",
                '%' . $wpdb->esc_like($base_filename) . '%'
            ));

            // Check 5: Trong ACF/custom fields không?
            $in_postmeta = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->postmeta} 
             WHERE meta_key != '_thumbnail_id'
             AND meta_value LIKE %s",
                '%' . $wpdb->esc_like($base_filename) . '%'
            ));

            // ✅ XÁC ĐỊNH KHÔNG DÙNG
            $is_unused = (
                $in_content == 0 &&
                $is_featured == 0 &&
                $has_parent == 0 &&
                $in_options == 0 &&
                $in_postmeta == 0
            );

            if ($is_unused) {
                $unused_count++;

                $file_path = get_attached_file($att_id);
                $file_size = 0;

                if (file_exists($file_path)) {
                    $file_size = filesize($file_path);
                    $deleted_size += $file_size;
                }

                // Đếm cả thumbnails
                $meta = wp_get_attachment_metadata($att_id);
                $thumbnail_count = 0;
                if (!empty($meta['sizes'])) {
                    $thumbnail_count = count($meta['sizes']);

                    // Cộng size của thumbnails
                    foreach ($meta['sizes'] as $size_data) {
                        $thumb_path = dirname($file_path) . '/' . $size_data['file'];
                        if (file_exists($thumb_path)) {
                            $deleted_size += filesize($thumb_path);
                        }
                    }
                }

                WP_CLI::line('');
                WP_CLI::line("❌ Unused: {$att->post_title}");
                WP_CLI::line("   ID: {$att_id}");
                WP_CLI::line("   File: {$filename}");
                WP_CLI::line("   Thumbnails: {$thumbnail_count}");
                WP_CLI::line("   Total size: " . size_format($file_size));

                if (!$dry_run) {
                    // ✅ XÓA THẬT (cả file gốc + thumbnails)
                    $deleted = wp_delete_attachment($att_id, true);
                    if ($deleted) {
                        $deleted_count++;
                        WP_CLI::success("   → Deleted (+ {$thumbnail_count} thumbnails)!");
                    } else {
                        WP_CLI::error("   → Failed to delete");
                    }
                } else {
                    WP_CLI::line("   → Would be deleted (use --force)");
                }
            } else {
                $in_use_count++;
            }
        }

        $progress->finish();

        WP_CLI::line('');
        WP_CLI::line('');
        WP_CLI::line('📊 Summary:');
        WP_CLI::line("  • Total attachments: $total");
        WP_CLI::line("  • In use: $in_use_count");
        WP_CLI::line("  • Protected (logo/banner/avatar): $protected_count");
        WP_CLI::line("  • Unused: $unused_count");

        if ($dry_run) {
            WP_CLI::line("  • Would free: " . size_format($deleted_size));
            WP_CLI::line('');
            WP_CLI::warning('This was a DRY-RUN. No files deleted.');
            WP_CLI::line('');
            WP_CLI::line('To actually delete, run:');
            WP_CLI::line('  wp image-fixer cleanup-unused --force --allow-root');
        } else {
            WP_CLI::line("  • Deleted: $deleted_count attachments");
            WP_CLI::line("  • Freed space: " . size_format($deleted_size));
            WP_CLI::line('');
            WP_CLI::success('Cleanup completed!');
        }
    }


}

WP_CLI::add_command('image-fixer', 'LBV_Image_Fixer_CLI');
WP_CLI::add_command('image-fixer cleanup-unused', ['LBV_Image_Fixer_CLI', 'cleanup_unused']);
