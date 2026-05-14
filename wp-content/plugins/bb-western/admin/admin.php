<?php

if (!defined('ABSPATH')) {
    exit;
}

class BBW_Admin {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', [$this, 'register_settings_page'], 99);
        add_action('admin_init', [$this, 'save_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_bb_western_test_provider', [$this, 'test_provider']);
        add_action('wp_ajax_bb_western_create_pages', [$this, 'create_pages']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settings_link']);
    }

    public function enqueue_admin_assets($hook) {
        if (!str_contains($hook, 'bb-western-settings')) return;

        wp_enqueue_style('bb-western', BB_WESTERN_PLUGIN_URL . 'admin/assets/css/western.css', [], TAROT_VERSION);
        wp_enqueue_script('bb-western', BB_WESTERN_PLUGIN_URL . 'admin/assets/js/western.js', ['jquery'], TAROT_VERSION, true);

        wp_localize_script('bb-western', 'bbWesternAdmin', [
            'nonce' => wp_create_nonce('bb_western_test_nonce'),
            'create_pages_nonce' => wp_create_nonce('bb_western_create_pages_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    public function settings_link($links) {
        $settings_link = '<a href="admin.php?page=bb-western-settings">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings_page() {
        add_submenu_page(
                'fortune-settings',
                'Bói Bài Tây',
                'Bói Bài Tây',
                'manage_options',
                'bb-western-settings',
                [$this, 'render_settings_page']
        );
    }

    public function render_settings_page() {

        $model      = get_option('western_ai_model', 'gemini-flash-latest');
        $provider   = get_option('western_ai_provider', 'gemini');
        $groq_model = get_option('western_groq_model', 'llama-3.3-70b-versatile');
        $gemini_key = get_option('western_gemini_key', '');
        $groq_key   = get_option('western_groq_key', '');
        $mistral_key = get_option('western_mistral_key', '');
        $mistral_model = get_option('western_mistral_model', 'mistral-small-latest');

        $gatekeeper_order = get_option('western_gatekeeper_order', 'groq,mistral,gemini');
        $analysis_order = get_option('western_analysis_order', 'gemini,mistral,groq');
        $allow_ai = get_option('western_allow_ai', '0');

        ?>

        <div class="wrap">
            <h1>Cấu hình Bói Bài Tây</h1>
            <form method="post">
                <?php wp_nonce_field('bb_western_settings_form'); ?>
                
                <!-- SECTION 0: AI Enable/Disable -->
                <h2>🤖 Bật/Tắt AI</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Cho phép sử dụng AI</th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_ai" value="1" <?php checked($allow_ai, '1'); ?>>
                                Bật chức năng AI (Gatekeeper + Giải bài Tây)
                            </label>
                            <p class="description" style="color: #d63638;">
                                <strong>⚠️ Lưu ý:</strong> Nếu tắt, tất cả chức năng AI sẽ bị vô hiệu hóa. Người dùng sẽ không thể sử dụng tính năng giải bài Tây.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <hr style="margin: 30px 0;">
                
                <h2>⚙️ Thứ tự ưu tiên Model (Fallback)</h2>
                <p style="margin-top: 0; color: #666;">Hệ thống sẽ tự động chuyển sang model dự phòng nếu model chính gặp lỗi.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <strong>Step 1:</strong> Kiểm duyệt câu hỏi
                            <p style="font-weight: normal; color: #666; margin: 5px 0 0 0;">Quyết định có tiếp tục Step 2 không</p>
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
                            <strong>Step 2:</strong> Giải lá bài
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
                            <select name="provider" id="bb_western_provider_select" style="min-width: 250px;">
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
                                Tạo 6 trang với cấu trúc hierarchy cho Bói Bài Tây:<br>
                                • Bói Bài Tây Online Miễn Phí (LV 1)<br>
                                • Bói Bài Tây 3 Lá (LV 2)<br>
                                • Bói Bài Tây 5 Lá (LV 2)<br>
                                • Bói Bài Móng Ngựa Tây 7 Lá (LV 2)<br>
                                • Bói Bài Tây 10 Lá (LV 2)<br>
                                • Bói Bài Tây Theo Câu Hỏi (LV 2)<br><br>
                                <strong>URLs sẽ được tạo:</strong><br>
                                • /boi-bai-tay/ (trang chính)<br>
                                • /tay-3-la/ (Tây 3 lá)<br>
                                • /tay-5-la/ (Tây 5 lá)<br>
                                • /tay-7-la/ (Tây 7 lá)<br>
                                • /tay-10-la/ (Tây 10 lá)<br>
                                • /tay-cau-hoi/ (Tây câu hỏi)<br><br>
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
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'bb_western_settings_form')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['provider'])) {
            update_option('western_ai_provider', sanitize_text_field($_POST['provider']));
        }

        if (isset($_POST['model'])) {
            update_option('western_ai_model', sanitize_text_field($_POST['model']));
        }

        if (isset($_POST['groq_model'])) {
            update_option('western_groq_model', sanitize_text_field($_POST['groq_model']));
        }

        if (isset($_POST['mistral_model'])) {
            update_option('western_mistral_model', sanitize_text_field($_POST['mistral_model']));
        }

        if (isset($_POST['gemini_key'])) {
            update_option('western_gemini_key', sanitize_textarea_field($_POST['gemini_key']));
        }

        if (isset($_POST['groq_key'])) {
            update_option('western_groq_key', sanitize_textarea_field($_POST['groq_key']));
        }

        if (isset($_POST['mistral_key'])) {
            update_option('western_mistral_key', sanitize_textarea_field($_POST['mistral_key']));
        }

        update_option('western_allow_ai', isset($_POST['allow_ai']) ? '1' : '0');

        if (isset($_POST['gatekeeper_order'])) {
            update_option('western_gatekeeper_order', sanitize_text_field($_POST['gatekeeper_order']));
        }

        if (isset($_POST['analysis_order'])) {
            update_option('western_analysis_order', sanitize_text_field($_POST['analysis_order']));
        }
    }

    public function test_provider() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'bb_western_test_nonce')) {
            error_log('[Western Test] Invalid nonce. User=' . (get_current_user_id() ?: 0));
            wp_send_json_error([
                'message' => 'Invalid nonce',
                'debug' => [
                    'user_id' => get_current_user_id(),
                ]
            ]);
        }

        if (!current_user_can('manage_options')) {
            error_log('[Western Test] Permission denied. User=' . (get_current_user_id() ?: 0));
            wp_send_json_error([
                'message' => 'Permission denied',
                'debug' => [
                    'user_id' => get_current_user_id(),
                ]
            ]);
        }

        $provider = isset($_POST['provider']) ? sanitize_text_field(wp_unslash($_POST['provider'])) : '';
        $test_prompt = "Test connection - respond with 'OK'";
        $start_time = microtime(true);
        $test_info = [];

        if (!class_exists('BBW_Gemini')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('BBW_Groq')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('BBW_Mistral')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/mistral.php';
        }

        error_log('[Western Test] Start. Provider=' . $provider . ' | User=' . (get_current_user_id() ?: 0));

        try {
            switch ($provider) {
                case 'gemini':
                    $response = BBW_Gemini::get_instance()->ftn_gemini_generate($test_prompt, $test_info);
                    break;
                case 'groq':
                    $response = BBW_Groq::get_instance()->ftn_groq_generate($test_prompt, $test_info);
                    break;
                case 'mistral':
                    $response = BBW_Mistral::get_instance()->ftn_mistral_generate($test_prompt, $test_info);
                    break;
                default:
                    wp_send_json_error([
                        'message' => 'Invalid provider',
                        'debug' => [
                            'provider' => $provider,
                        ]
                    ]);
            }

            $duration = round((microtime(true) - $start_time) * 1000);

            // Debug log failed keys
            if (!empty($test_info['failed_keys'])) {
                error_log('[Western Test] Failed keys: ' . implode(', ', $test_info['failed_keys']));
            }

            if ($response && !str_starts_with($response, '[Error]')) {
                error_log('[Western Test] Success - Provider: ' . $provider . ' | Model: ' . ($test_info['model'] ?? 'N/A') . ' | Key: ' . ($test_info['key'] ?? 'N/A') . ' | Duration: ' . $duration . 'ms');
                wp_send_json_success([
                    'response' => $response,
                    'model' => $test_info['model'] ?? 'N/A',
                    'key' => $test_info['key'] ?? 'N/A',
                    'duration_ms' => $duration,
                    'failed_keys' => $test_info['failed_keys'] ?? [],
                ]);
            } else {
                error_log('[Western Test] Failed - Provider: ' . $provider . ' | Error: ' . ($response ?: 'No response') . ' | Duration: ' . $duration . 'ms');
                wp_send_json_error([
                    'message' => $response ?: 'No response received',
                    'duration_ms' => $duration,
                    'failed_keys' => $test_info['failed_keys'] ?? [],
                    'debug' => [
                        'provider' => $provider,
                        'model' => $test_info['model'] ?? 'N/A',
                        'key' => $test_info['key'] ?? 'N/A',
                        'response_snippet' => is_string($response) ? mb_substr($response, 0, 500) : '',
                    ]
                ]);
            }
        } catch (Throwable $e) {
            $duration = round((microtime(true) - $start_time) * 1000);
            error_log('[Western Test] Exception - Provider: ' . $provider . ' | ' . $e->getMessage());
            wp_send_json_error([
                'message' => $e->getMessage(),
                'duration_ms' => $duration,
                'debug' => [
                    'provider' => $provider,
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ]);
        }
    }

    public function create_pages() {
        check_ajax_referer('bb_western_create_pages_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $pages_config = [
            [
                'title' => 'Bói Bài Tây Online Miễn Phí',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[western_form mode="hub"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'boi-bai-tay',
                'parent' => 0
            ],

            [
                'title' => 'Bói Bài Tây 3 Lá',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[western_form mode="topic" spread="3_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tay-3-la',
                'parent' => 'boi-bai-tay'
            ],
            [
                'title' => 'Bói Bài Tây 5 Lá',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[western_form mode="topic" spread="5_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tay-5-la',
                'parent' => 'boi-bai-tay'
            ],
            [
                'title' => 'Bói Bài Móng Ngựa Tây 7 Lá',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[western_form mode="topic" spread="7_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tay-7-la',
                'parent' => 'boi-bai-tay'
            ],
            [
                'title' => 'Bói Bài Tây 10 Lá',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[western_form mode="topic" spread="10_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tay-10-la',
                'parent' => 'boi-bai-tay'
            ],
            [
                'title' => 'Bói Bài Tây Theo Câu Hỏi',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[western_form mode="question" spread="3_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tay-cau-hoi',
                'parent' => 'boi-bai-tay'
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
