<?php

if (!defined('ABSPATH')) {
    exit;
}

class IChing_Admin {

    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action('admin_init', [$this, 'save_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_iching_test_provider', [$this, 'test_provider']);
        add_action('wp_ajax_iching_create_pages', [$this, 'create_pages']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settings_link']);
    }

    public function enqueue_admin_assets($hook) {
        if (!str_contains($hook, 'iching-settings')) return;

        wp_enqueue_style( 'iching-admin-css', ICHING_PLUGIN_URL . 'admin/assets/css/iching-admin.css', [], ICHING_PLUGIN_VERSION );
        wp_enqueue_script( 'iching-admin-js', ICHING_PLUGIN_URL . 'admin/assets/js/iching-admin.js', ['jquery'], ICHING_PLUGIN_VERSION, true );

        wp_localize_script('iching-admin-js', 'ichingAdmin', [
            'nonce' => wp_create_nonce('iching_test_nonce'),
            'create_pages_nonce' => wp_create_nonce('iching_create_pages_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    public function settings_link($links) {
        $settings_link = '<a href="admin.php?page=iching-settings">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings_page() {
        add_submenu_page(
                'fortune-settings',
                'Quẻ Kinh Dịch',
                'Quẻ Kinh Dịch',
                'manage_options',
                'iching-settings',
                [$this, 'render_settings_page']
        );
    }

    public function render_settings_page() {

        $model      = get_option('iching_ai_model', 'gemini-flash-latest');
        $provider   = get_option('iching_ai_provider', 'gemini');
        $groq_model = get_option('iching_groq_model', 'llama-3.3-70b-versatile');
        $gemini_key = get_option('iching_gemini_key', '');
        $groq_key   = get_option('iching_groq_key', '');
        $mistral_key = get_option('iching_mistral_key', '');
        $mistral_model = get_option('iching_mistral_model', 'mistral-small-latest');
        
        $gatekeeper_order = get_option('iching_gatekeeper_order', 'groq,mistral,gemini');
        $analysis_order = get_option('iching_analysis_order', 'gemini,mistral,groq');
        $allow_ai = get_option('iching_allow_ai', '0');

        ?>

        <div class="wrap">
            <h1>Cấu hình Kinh Dịch</h1>
            <form method="post">
                <?php wp_nonce_field('iching_settings_form'); ?>

                <!-- SECTION 0: AI Enable/Disable -->
                <h2>🤖 Bật/Tắt AI</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Cho phép sử dụng AI</th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_ai" value="1" <?php checked($allow_ai, '1'); ?>>
                                Bật chức năng AI (Gatekeeper + Luận quẻ)
                            </label>
                            <p class="description" style="color: #d63638;">
                                <strong>⚠️ Lưu ý:</strong> Nếu tắt, tất cả chức năng AI sẽ bị vô hiệu hóa.
                            </p>
                        </td>
                    </tr>
                </table>

                <hr style="margin: 30px 0;">
                
                <!-- SECTION 1: Model Priority -->
                <h2>⚙️ Thứ tự ưu tiên Model (Fallback)</h2>
                <p style="margin-top: 0; color: #666;">Hệ thống sẽ tự động chuyển sang model dự phòng nếu model chính gặp lỗi.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <strong>Step 1:</strong> Kiểm duyệt câu hỏi
                            <p style="font-weight: normal; color: #666; margin: 5px 0 0 0;">Xác định Dụng Thần</p>
                        </th>
                        <td>
                            <select name="gatekeeper_order" style="min-width: 250px;">
                                <option value="groq,mistral,gemini" <?php selected($gatekeeper_order, 'groq,mistral,gemini'); ?>>groq → mistral → gemini</option>
                                <option value="groq,gemini,mistral" <?php selected($gatekeeper_order, 'groq,gemini,mistral'); ?>>groq → gemini → mistral</option>
                                <option value="mistral,groq,gemini" <?php selected($gatekeeper_order, 'mistral,groq,gemini'); ?>>mistral → groq → gemini</option>
                                <option value="mistral,gemini,groq" <?php selected($gatekeeper_order, 'mistral,gemini,groq'); ?>>mistral → gemini → groq</option>
                                <option value="gemini,groq,mistral" <?php selected($gatekeeper_order, 'gemini,groq,mistral'); ?>>gemini → groq → mistral</option>
                                <option value="gemini,mistral,groq" <?php selected($gatekeeper_order, 'gemini,mistral,groq'); ?>>gemini → mistral → groq</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <strong>Step 2:</strong> Luận quẻ chính
                            <p style="font-weight: normal; color: #666; margin: 5px 0 0 0;">Phân tích và giải đáp</p>
                        </th>
                        <td>
                            <select name="analysis_order" style="min-width: 250px;">
                                <option value="gemini,mistral,groq" <?php selected($analysis_order, 'gemini,mistral,groq'); ?>>gemini → mistral → groq</option>
                                <option value="gemini,groq,mistral" <?php selected($analysis_order, 'gemini,groq,mistral'); ?>>gemini → groq → mistral</option>
                                <option value="groq,gemini,mistral" <?php selected($analysis_order, 'groq,gemini,mistral'); ?>>groq → gemini → mistral</option>
                                <option value="groq,mistral,gemini" <?php selected($analysis_order, 'groq,mistral,gemini'); ?>>groq → mistral → gemini</option>
                                <option value="mistral,gemini,groq" <?php selected($analysis_order, 'mistral,gemini,groq'); ?>>mistral → gemini → groq</option>
                                <option value="mistral,groq,gemini" <?php selected($analysis_order, 'mistral,groq,gemini'); ?>>mistral → groq → gemini</option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <hr style="margin: 30px 0;">
                
                <!-- SECTION 2: API Configuration -->
                <h2>🔑 Cấu hình API Keys & Models</h2>
                
                <h3 style="margin-top: 20px;">Google Gemini</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Gemini Model</th>
                        <td>
                            <select name="model" style="min-width: 250px;">
                                <option value="gemini-flash-latest" <?php selected($model, 'gemini-flash-latest'); ?>>gemini-flash-latest</option>
                                <option value="gemini-2.5-flash" <?php selected($model, 'gemini-2.5-flash'); ?>>gemini-2.5-flash</option>
                                <option value="gemini-3-flash-preview" <?php selected($model, 'gemini-3-flash-preview'); ?>>gemini-3-flash-preview</option>
                                <option value="gemini-2.5-flash-lite" <?php selected($model, 'gemini-2.5-flash-lite'); ?>>gemini-2.5-flash-lite</option>
                                <option value="gemini-flash-lite-latest" <?php selected($model, 'gemini-flash-lite-latest'); ?>>gemini-flash-lite-latest</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Gemini API Keys</th>
                        <td>
                            <textarea name="gemini_key" rows="4" cols="60" placeholder="Nhập API keys (mỗi key một dòng)"><?php echo esc_textarea($gemini_key); ?></textarea>
                            <p class="description">Nhập nhiều keys để hệ thống tự động rotate khi gặp rate limit.</p>
                        </td>
                    </tr>
                </table>
                
                <h3 style="margin-top: 30px;">Groq</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Groq Model</th>
                        <td>
                            <select name="groq_model" style="min-width: 250px;">
                                <option value="llama-3.3-70b-versatile" <?php selected($groq_model, 'llama-3.3-70b-versatile'); ?>>llama-3.3-70b-versatile</option>
                                <option value="qwen/qwen3-32b" <?php selected($groq_model, 'qwen/qwen3-32b'); ?>>qwen/qwen3-32b</option>
                                <option value="openai/gpt-oss-120b" <?php selected($groq_model, 'openai/gpt-oss-120b'); ?>>openai/gpt-oss-120b</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Groq API Keys</th>
                        <td>
                            <textarea name="groq_key" rows="4" cols="60" placeholder="Nhập API keys (mỗi key một dòng)"><?php echo esc_textarea($groq_key); ?></textarea>
                            <p class="description">Nhập nhiều keys để hệ thống tự động rotate khi gặp rate limit.</p>
                        </td>
                    </tr>
                </table>
                
                <h3 style="margin-top: 30px;">Mistral AI</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Mistral Model</th>
                        <td>
                            <select name="mistral_model" style="min-width: 250px;">
                                <option value="mistral-small-latest" <?php selected($mistral_model, 'mistral-small-latest'); ?>>mistral-small-latest</option>
                                <option value="mistral-large-2411" <?php selected($mistral_model, 'mistral-large-2411'); ?>>mistral-large-2411</option>
                                <option value="mistral-medium-latest" <?php selected($mistral_model, 'mistral-medium-latest'); ?>>mistral-medium-latest</option>
                                <option value="mistral-medium-2505" <?php selected($mistral_model, 'mistral-medium-2505'); ?>>mistral-medium-2505</option>
                                <option value="mistral-medium-2508" <?php selected($mistral_model, 'mistral-medium-2508'); ?>>mistral-medium-2508</option>
                                <option value="mistral-large-latest" <?php selected($mistral_model, 'mistral-large-latest'); ?>>mistral-large-latest</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Mistral API Keys</th>
                        <td>
                            <textarea name="mistral_key" rows="4" cols="60" placeholder="Nhập API keys (mỗi key một dòng)"><?php echo esc_textarea($mistral_key); ?></textarea>
                            <p class="description">Nhập nhiều keys để hệ thống tự động rotate khi gặp rate limit.</p>
                        </td>
                    </tr>
                </table>
                
                <h3 style="margin-top: 30px;">Test Connection</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Chọn provider để test</th>
                        <td>
                            <select name="provider" id="iching_provider_select" style="min-width: 250px;">
                                <option value="gemini" <?php selected($provider, 'gemini'); ?>>Google Gemini</option>
                                <option value="groq" <?php selected($provider, 'groq'); ?>>Groq</option>
                                <option value="mistral" <?php selected($provider, 'mistral'); ?>>Mistral AI</option>
                            </select>
                            <button type="button" id="test-provider" class="button" style="margin-left: 10px;">Test Connection</button>
                            <div id="test-results" style="margin-top: 10px;"></div>
                        </td>
                    </tr>
                </table>
                
                <hr style="margin: 30px 0;">
                
                <!-- SECTION 3: Page Creation -->
                <h2>📄 Tạo trang</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Tạo trang</th>
                        <td>
                            <button type="button" id="create-pages-btn" class="button button-primary">Tạo trang</button>
                            <div id="create-pages-results" style="margin-top: 10px;"></div>
                            <p class="description">
                                Tạo 6 trang với cấu trúc hierarchy:<br>
                                • Gieo Quẻ Kinh Dịch Online, Luận Giải Mai Hoa – Lục Hào (LV 1)<br>
                                • Gieo Quẻ Lục Hào Nạp Giáp – Kinh Dịch Quẻ (LV 2)<br>
                                • Gieo Quẻ Mai Hoa Dịch Số – Kinh Dịch Quẻ (LV 2)<br>
                                • Gieo Quẻ Mai Hoa Theo Con Số – Luận Giải Kinh Dịch Quẻ (LV 2)<br>
                                • Gieo Quẻ Mai Hoa Theo Ngoại Tượng – Kinh Dịch Quẻ (LV 2)<br>
                                • Gieo Quẻ Mai Hoa Theo Thời Gian (LV 2)<br><br>
                                <strong>URLs sẽ được tạo:</strong><br>
                                • /que-kinh-dich/ (trang chính)<br>
                                • /que-kinh-dich/luc-hao/ (Lục Hào)<br>
                                • /que-kinh-dich/mai-hoa/ (Mai Hoa)<br>
                                • /que-kinh-dich/con-so/ (Con Số)<br>
                                • /que-kinh-dich/ngoai-tuong/ (Ngoại Tượng)<br><br>
                                <em>Lưu ý: Trang đã tồn tại sẽ được bỏ qua.</em>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>

        <?php
    }

    public function save_settings() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'iching_settings_form')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['provider'])) {
            update_option('iching_ai_provider', sanitize_text_field($_POST['provider']));
        }

        if (isset($_POST['model'])) {
            update_option('iching_ai_model', sanitize_text_field($_POST['model']));
        }

        if (isset($_POST['groq_model'])) {
            update_option('iching_groq_model', sanitize_text_field($_POST['groq_model']));
        }

        if (isset($_POST['mistral_model'])) {
            update_option('iching_mistral_model', sanitize_text_field($_POST['mistral_model']));
        }

        if (isset($_POST['gemini_key'])) {
            update_option('iching_gemini_key', sanitize_textarea_field($_POST['gemini_key']));
        }

        if (isset($_POST['groq_key'])) {
            update_option('iching_groq_key', sanitize_textarea_field($_POST['groq_key']));
        }

        if (isset($_POST['mistral_key'])) {
            update_option('iching_mistral_key', sanitize_textarea_field($_POST['mistral_key']));
        }

        update_option('iching_allow_ai', isset($_POST['allow_ai']) ? '1' : '0');
        
        if (isset($_POST['gatekeeper_order'])) {
            update_option('iching_gatekeeper_order', sanitize_text_field($_POST['gatekeeper_order']));
        }
        
        if (isset($_POST['analysis_order'])) {
            update_option('iching_analysis_order', sanitize_text_field($_POST['analysis_order']));
        }
    }

    public function test_provider() {
        check_ajax_referer('iching_test_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        if (get_option('iching_allow_ai', '0') !== '1') {
            wp_send_json_error(['message' => 'AI hiện đang tắt trong phần cấu hình.']);
        }

        $provider = sanitize_text_field($_POST['provider']);
        $test_prompt = "Test connection - respond with 'OK'";

        if (!class_exists('IChing_Gemini')) {
            require_once ICHING_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('IChing_Groq')) {
            require_once ICHING_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('IChing_Mistral')) {
            require_once ICHING_PLUGIN_DIR . 'includes/mistral.php';
        }

        try {
            switch ($provider) {
                case 'gemini':
                    $response = IChing_Gemini::get_instance()->ftn_gemini_generate($test_prompt);
                    break;
                case 'groq':
                    $response = IChing_Groq::get_instance()->ftn_groq_generate($test_prompt);
                    break;
                case 'mistral':
                    $response = IChing_Mistral::get_instance()->ftn_mistral_generate($test_prompt);
                    break;
                default:
                    wp_send_json_error(['message' => 'Invalid provider']);
            }

            if ($response && !str_starts_with($response, '[Error]')) {
                wp_send_json_success();
            } else {
                wp_send_json_error(['message' => $response ?: 'No response received']);
            }
        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function create_pages() {
        check_ajax_referer('iching_create_pages_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $pages_config = [
            [
                'title' => 'Gieo Quẻ Kinh Dịch Online, Luận Giải Mai Hoa – Lục Hào',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[iching_landing]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'que-kinh-dich',
                'parent' => 0
            ],

            [
                'title' => 'Gieo Quẻ Lục Hào Nạp Giáp – Kinh Dịch Quẻ',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[iching_luchao]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'luc-hao',
                'parent' => 'que-kinh-dich'
            ],
            [
                'title' => 'Gieo Quẻ Mai Hoa Dịch Số – Kinh Dịch Quẻ',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[iching_maihoa]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'mai-hoa',
                'parent' => 'que-kinh-dich'
            ],
            [
                'title' => 'Gieo Quẻ Mai Hoa Theo Con Số – Kinh Dịch Quẻ',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[iching_maihoa method="maihoa_number"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'con-so',
                'parent' => 'que-kinh-dich'
            ],
            [
                'title' => 'Gieo Quẻ Mai Hoa Theo Ngoại Tượng – Kinh Dịch Quẻ',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[iching_maihoa method="maihoa_object"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'ngoai-tuong',
                'parent' => 'que-kinh-dich'
            ]
        ];

        $created_pages = [];
        $parent_ids = [];

        foreach ($pages_config as $page_config) {

            $parent_id = 0;
            if ($page_config['parent'] !== 0) {
                if (isset($parent_ids[$page_config['parent']])) {
                    $parent_id = $parent_ids[$page_config['parent']];
                } else {
                    $parent_page = get_page_by_path($page_config['parent']);
                    if ($parent_page) {
                        $parent_id = $parent_page->ID;
                    }
                }
            }

            $existing = get_posts([
                    'post_type'   => 'page',
                    'name'        => $page_config['slug'],
                    'post_parent' => $parent_id,
                    'post_status' => 'any',
                    'numberposts' => 1,
            ]);

            if (!empty($existing)) {
                $parent_ids[$page_config['slug']] = $existing[0]->ID; // vẫn cần lưu để các trang con dùng
                $created_pages[] = [
                        'title'    => $page_config['title'],
                        'edit_url' => get_edit_post_link($existing[0]->ID),
                        'status'   => 'đã tồn tại (bỏ qua)',
                ];
                continue;
            }

            $post_data = [
                    'post_title'   => $page_config['title'],
                    'post_content' => $page_config['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $page_config['slug'],
                    'post_parent'  => $parent_id,
            ];

            $page_id = wp_insert_post($post_data);

            if ($page_id && !is_wp_error($page_id)) {
                $parent_ids[$page_config['slug']] = $page_id;
                $created_pages[] = [
                        'title'    => $page_config['title'],
                        'edit_url' => get_edit_post_link($page_id),
                        'status'   => 'đã tạo',
                ];
            }
        }

        if (!empty($created_pages)) {
            wp_send_json_success([
                    'message' => 'Đã xử lý ' . count($created_pages) . ' trang',
                    'pages'   => $created_pages,
            ]);
        } else {
            wp_send_json_error(['message' => 'Không thể tạo trang']);
        }
    }
}
