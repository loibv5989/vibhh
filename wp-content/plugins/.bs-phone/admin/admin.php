<?php
if (!defined('ABSPATH')) exit;

/**
 * BS_Phone_Admin — PhoneSync v2
 */
class BS_Phone_Admin {

    private string $plugin_file;

    public function __construct(string $plugin_file = '') {
        $this->plugin_file = $plugin_file ?: BS_PHONE_PLUGIN_FILE;

        add_action('admin_menu',            [$this, 'registerSettingsPage'], 99);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_bs_phone_create_pages', [$this, 'createPages']);
        add_action('wp_ajax_bs_phone_clear_cache',  [$this, 'clearCache']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settingsLink']);
    }

    public function enqueueAssets(string $hook): void {
        if (!str_contains($hook, 'bs-phone-settings')) return;
        wp_enqueue_style('bs-phone-admin',  BS_PHONE_PLUGIN_URL . 'admin/assets/css/admin.css', [], BS_PHONE_VERSION);
        wp_enqueue_script('bs-phone-admin', BS_PHONE_PLUGIN_URL . 'admin/assets/js/admin.js',  ['jquery'], BS_PHONE_VERSION, true);
        wp_localize_script('bs-phone-admin', 'bsPhoneAdmin', [
            'create_pages_nonce' => wp_create_nonce('bs_phone_create_pages_nonce'),
            'clear_cache_nonce'  => wp_create_nonce('bs_phone_clear_cache_nonce'),
            'ajax_url'           => admin_url('admin-ajax.php'),
        ]);
    }

    public function settingsLink(array $links): array {
        array_unshift($links, '<a href="admin.php?page=bs-phone-settings">Settings</a>');
        return $links;
    }

    public function registerSettingsPage(): void {
        add_submenu_page(
            'fortune-settings',
            'Giải Mã Số Điện Thoại',
            'Giải Mã Số Điện Thoại',
            'manage_options',
            'bs-phone-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void {
        $upload_dir  = wp_upload_dir();
        $cache_dir   = $upload_dir['basedir'] . '/bs-phone';
        $cache_count = 0;
        if (is_dir($cache_dir)) {
            $cache_count = count(glob($cache_dir . '/*.json') ?: []);
        }

        $this->loadDependencies();
        ?>

        <div class="wrap">
            <h1>Giải Mã Số Điện Thoại — PhoneSync</h1>
            <p style="color:#666;">Version <?= esc_html(BS_PHONE_VERSION) ?> | Logic phân tích tích hợp sẵn, không cần API key.</p>

            <hr style="margin:20px 0;">

            <h2>📄 Tạo trang</h2>
            <p>Tự động tạo trang WordPress với shortcode <code>[phone_form]</code>.</p>
            <button type="button" id="bs-phone-create-pages" class="button button-primary">Tạo Trang Mẫu</button>
            <div id="bs-phone-pages-result" style="margin-top:10px;"></div>

            <hr style="margin:20px 0;">

            <h2>🗑️ Cache</h2>
            <p>Kết quả phân tích được cache trong <code><?= esc_html($cache_dir) ?></code>.</p>
            <p>Hiện có <strong><?= esc_html($cache_count) ?></strong> file cache.</p>
            <button type="button" id="bs-phone-clear-cache" class="button button-secondary">Xóa Toàn Bộ Cache</button>
            <div id="bs-phone-cache-result" style="margin-top:10px;"></div>

            <hr style="margin:20px 0;">

            <h2>ℹ️ Các plugin phụ thuộc</h2>
            <table class="widefat" style="max-width:600px;">
                <thead><tr><th>Plugin</th><th>Trạng thái</th><th>Ghi chú</th></tr></thead>
                <tbody>
                    <tr>
                        <td>Thần Số Học</td>
                        <td><?= class_exists('ThanSoHoc_Calc') ? '<span style="color:green">✓ Đã load</span>' : '<span style="color:#888">Chưa load (dùng fallback)</span>' ?></td>
                        <td>Dùng để tính Life Path Number</td>
                    </tr>
                    <tr>
                        <td>Cung Hoàng Đạo</td>
                        <td><?= class_exists('BbZodiac_Calc') ? '<span style="color:green">✓ Đã load</span>' : '<span style="color:#888">Chưa load (bỏ qua)</span>' ?></td>
                        <td>Dùng để xác định cung & nguyên tố</td>
                    </tr>
                    <tr>
                        <td>Tử Vi</td>
                        <td><?= class_exists('TuVi_Engine') ? '<span style="color:green">✓ Đã load</span>' : '<span style="color:#888">Chưa dùng</span>' ?></td>
                        <td>Chưa tích hợp trong v2</td>
                    </tr>
                </tbody>
            </table>

            <hr style="margin:20px 0;">

            <h2>📝 Shortcode</h2>
            <code>[phone_form]</code>
        </div>

        <?php
    }

    public function createPages(): void {
        if (!check_ajax_referer('bs_phone_create_pages_nonce', 'nonce', false) || !current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $pages = [
            [
                'title'   => 'Giải Mã Số Điện Thoại',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[phone_form]' . "\n" . '<!-- /wp:shortcode -->',
                'slug'    => 'giai-ma-so-dien-thoai',
            ],
        ];

        $created = [];
        foreach ($pages as $page) {
            if (get_page_by_path($page['slug'])) continue;
            $id = wp_insert_post([
                'post_title'   => $page['title'],
                'post_content' => $page['content'],
                'post_name'    => $page['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ]);
            if (!is_wp_error($id)) {
                $created[] = '<a href="' . get_permalink($id) . '" target="_blank">' . esc_html($page['title']) . '</a>';
            }
        }

        if (empty($created)) {
            wp_send_json_success(['message' => 'Trang đã tồn tại, không tạo lại.']);
        }
        wp_send_json_success(['message' => 'Đã tạo: ' . implode(', ', $created)]);
    }

    public function clearCache(): void {
        if (!check_ajax_referer('bs_phone_clear_cache_nonce', 'nonce', false) || !current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . '/bs-phone';
        $deleted    = 0;

        if (is_dir($cache_dir)) {
            foreach (glob($cache_dir . '/*.json') ?: [] as $file) {
                @unlink($file);
                $deleted++;
            }
        }

        wp_send_json_success(['message' => "Đã xóa $deleted file cache."]);
    }

    private function loadDependencies(): void {
        if (!class_exists('BS_Phone_Calc')) {
            require_once BS_PHONE_PLUGIN_DIR . 'includes/calc.php';
        }
        // Chỉ load plugin phụ thuộc nếu constant của chúng đã được define
        if (!class_exists('ThanSoHoc_Calc') && defined('THAN_SO_HOC_PLUGIN_DIR')) {
            require_once THAN_SO_HOC_PLUGIN_DIR . 'includes/calc.php';
        }
        if (!class_exists('BbZodiac_Calc') && defined('BB_ZODIAC_PLUGIN_DIR')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/calc.php';
        }
        if (!class_exists('TuVi_Engine') && defined('TUVI_PLUGIN_DIR')) {
            require_once TUVI_PLUGIN_DIR . 'includes/laso.php';
        }
    }
}
