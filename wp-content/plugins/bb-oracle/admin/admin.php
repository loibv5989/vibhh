<?php

if (!defined('ABSPATH')) exit;

class BbOracle_Admin {

    private string $plugin_file;

    public function __construct(string $plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', [$this, 'registerSettingsPage'], 99);
        add_action('admin_init', [$this, 'saveSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_bb_oracle_test_provider', [$this, 'testProvider']);
        add_action('wp_ajax_bb_oracle_create_pages',  [$this, 'createPages']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settingsLink']);
    }

    public function enqueueAssets(string $hook): void {
        if (!str_contains($hook, 'bb-oracle-settings')) return;

        wp_enqueue_style('bb-oracle-admin-css', BB_ORACLE_PLUGIN_URL . 'admin/assets/css/oracle-admin.css', [], BB_ORACLE_VERSION);
        wp_enqueue_script('bb-oracle-admin-js', BB_ORACLE_PLUGIN_URL . 'admin/assets/js/oracle-admin.js', ['jquery'], BB_ORACLE_VERSION, true);
        wp_localize_script('bb-oracle-admin-js', 'bbOracleAdmin', [
            'ajax_url'            => admin_url('admin-ajax.php'),
            'test_provider_nonce' => wp_create_nonce('bb_oracle_test_nonce'),
            'create_pages_nonce'  => wp_create_nonce('bb_oracle_create_pages_nonce'),
        ]);
    }

    public function settingsLink(array $links): array {
        array_unshift($links, '<a href="admin.php?page=bb-oracle-settings">Settings</a>');
        return $links;
    }

    public function registerSettingsPage(): void {
        add_submenu_page(
            'fortune-settings',
            'Oracle Cards',
            'Oracle Cards',
            'manage_options',
            'bb-oracle-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void {
        $model         = get_option('bb_oracle_ai_model',       'gemini-flash-latest');
        $provider      = get_option('bb_oracle_ai_provider',    'gemini');
        $groq_model    = get_option('bb_oracle_groq_model',     'llama-3.3-70b-versatile');
        $gemini_key    = get_option('bb_oracle_gemini_key',     '');
        $groq_key      = get_option('bb_oracle_groq_key',       '');
        $mistral_key   = get_option('bb_oracle_mistral_key',    '');
        $mistral_model = get_option('bb_oracle_mistral_model',  'mistral-small-latest');
        $allow_ai      = get_option('bb_oracle_allow_ai',       '0');
        $gatekeeper_order = get_option('bb_oracle_gatekeeper_order', 'groq,mistral,gemini');
        $analysis_order   = get_option('bb_oracle_analysis_order',   'gemini,mistral,groq');
        ?>
        <div class="wrap">
            <h1>Cấu hình | Oracle Cards</h1>
            <form method="post">
                <?php wp_nonce_field('bb_oracle_settings_form'); ?>

                <h2>🤖 Bật/Tắt AI</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Cho phép sử dụng AI</th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_ai" value="1" <?php checked($allow_ai, '1'); ?>>
                                Bật chức năng AI (Luận giải lá bài)
                            </label>
                            <p class="description" style="color:#d63638;">
                                <strong>⚠️ Lưu ý:</strong> Nếu tắt, toàn bộ chức năng AI sẽ bị vô hiệu hóa.
                            </p>
                        </td>
                    </tr>
                </table>

                <hr style="margin:30px 0;">

                <h2>⚙️ Thứ tự ưu tiên Model (Fallback)</h2>
                <p style="margin-top:0;color:#666;">Hệ thống sẽ tự động chuyển sang model dự phòng nếu model chính gặp lỗi.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <strong>Step 1:</strong> Kiểm duyệt câu hỏi
                            <p style="font-weight:normal;color:#666;margin:5px 0 0 0;">Quyết định có tiếp tục Step 2 không</p>
                        </th>
                        <td>
                            <select name="gatekeeper_order" style="min-width:250px;">
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
                            <p style="font-weight:normal;color:#666;margin:5px 0 0 0;">Phân tích và giải đáp</p>
                        </th>
                        <td>
                            <select name="analysis_order" style="min-width:250px;">
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

                <hr style="margin:30px 0;">

                <h2>🔑 Cấu hình API Keys &amp; Models</h2>

                <h3 style="margin-top:20px;">Google Gemini</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Gemini Model</th>
                        <td>
                            <select name="model" style="min-width:250px;">
                                <option value="gemini-flash-latest"   <?php selected($model, 'gemini-flash-latest');   ?>>gemini-flash-latest</option>
                                <option value="gemini-2.5-flash"      <?php selected($model, 'gemini-2.5-flash');      ?>>gemini-2.5-flash</option>
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

                <h3 style="margin-top:30px;">Groq</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Groq Model</th>
                        <td>
                            <select name="groq_model" style="min-width:250px;">
                                <option value="llama-3.3-70b-versatile" <?php selected($groq_model, 'llama-3.3-70b-versatile'); ?>>llama-3.3-70b-versatile</option>
                                <option value="qwen/qwen3-32b"          <?php selected($groq_model, 'qwen/qwen3-32b');          ?>>qwen/qwen3-32b</option>
                                <option value="openai/gpt-oss-120b"     <?php selected($groq_model, 'openai/gpt-oss-120b');     ?>>openai/gpt-oss-120b</option>
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

                <h3 style="margin-top:30px;">Mistral AI</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Mistral Model</th>
                        <td>
                            <select name="mistral_model" style="min-width:250px;">
                                <option value="mistral-small-latest"  <?php selected($mistral_model, 'mistral-small-latest');  ?>>mistral-small-latest</option>
                                <option value="mistral-large-2411"    <?php selected($mistral_model, 'mistral-large-2411');    ?>>mistral-large-2411</option>
                                <option value="mistral-medium-latest" <?php selected($mistral_model, 'mistral-medium-latest'); ?>>mistral-medium-latest</option>
                                <option value="mistral-medium-2505"   <?php selected($mistral_model, 'mistral-medium-2505');   ?>>mistral-medium-2505</option>
                                <option value="mistral-large-latest"  <?php selected($mistral_model, 'mistral-large-latest');  ?>>mistral-large-latest</option>
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

                <h3 style="margin-top:30px;">Test Connection</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Chọn provider để test</th>
                        <td>
                            <select id="bb_oracle_test_provider_select" style="min-width:250px;">
                                <option value="gemini">Google Gemini</option>
                                <option value="groq">Groq</option>
                                <option value="mistral">Mistral AI</option>
                            </select>
                            <button type="button" id="bb-oracle-test-provider" class="button" style="margin-left:10px;">Test Connection</button>
                            <div id="bb-oracle-test-results" style="margin-top:10px;"></div>
                        </td>
                    </tr>
                </table>

                <hr style="margin:30px 0;">

                <h2>📄 Tạo trang</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Tạo trang Oracle</th>
                        <td>
                            <button type="button" id="bb-oracle-create-pages" class="button button-primary">Tạo trang</button>
                            <div id="bb-oracle-create-pages-results" style="margin-top:10px;"></div>
                            <p class="description">
                                Tạo các trang Oracle với shortcode tương ứng:<br>
                                • <strong>/oracle-cards-online/</strong> — Hub trang chủ <code>[oracle_form mode="hub"]</code><br>
                                • <strong>/oracle-1-la/</strong> — Daily Card 1 lá <code>[oracle_form mode="topic" spread="1_card"]</code><br>
                                • <strong>/oracle-2-la/</strong> — Oracle 2 lá <code>[oracle_form mode="topic" spread="2_cards"]</code><br>
                                • <strong>/oracle-3-la/</strong> — Oracle 3 lá <code>[oracle_form mode="topic" spread="3_cards"]</code><br>
                                • <strong>/oracle-cau-hoi/</strong> — Hỏi Oracle <code>[oracle_form mode="question" spread="3_cards"]</code><br>
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

    public function saveSettings(): void {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'bb_oracle_settings_form')) return;
        if (!current_user_can('manage_options')) return;

        if (isset($_POST['provider']))      update_option('bb_oracle_ai_provider',   sanitize_text_field($_POST['provider']));
        if (isset($_POST['model']))         update_option('bb_oracle_ai_model',       sanitize_text_field($_POST['model']));
        if (isset($_POST['groq_model']))    update_option('bb_oracle_groq_model',     sanitize_text_field($_POST['groq_model']));
        if (isset($_POST['mistral_model'])) update_option('bb_oracle_mistral_model',  sanitize_text_field($_POST['mistral_model']));
        if (isset($_POST['gemini_key']))    update_option('bb_oracle_gemini_key',     sanitize_textarea_field($_POST['gemini_key']));
        if (isset($_POST['groq_key']))      update_option('bb_oracle_groq_key',       sanitize_textarea_field($_POST['groq_key']));
        if (isset($_POST['mistral_key']))   update_option('bb_oracle_mistral_key',    sanitize_textarea_field($_POST['mistral_key']));

        update_option('bb_oracle_allow_ai', isset($_POST['allow_ai']) ? '1' : '0');

        if (isset($_POST['gatekeeper_order'])) {
            update_option('bb_oracle_gatekeeper_order', sanitize_text_field($_POST['gatekeeper_order']));
        }

        if (isset($_POST['analysis_order'])) {
            update_option('bb_oracle_analysis_order', sanitize_text_field($_POST['analysis_order']));
        }
    }

    public function testProvider(): void {
        if (!check_ajax_referer('bb_oracle_test_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Nonce không hợp lệ.']);
            return;
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }
        if (get_option('bb_oracle_allow_ai', '0') !== '1') {
            wp_send_json_error(['message' => 'AI hiện đang tắt trong phần cấu hình.']);
            return;
        }

        $provider    = sanitize_text_field($_POST['provider'] ?? 'gemini');
        $test_prompt = "Test connection - respond with 'OK'";

        if (!class_exists('BbOracle_Gemini')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('BbOracle_Groq')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('BbOracle_Mistral')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/mistral.php';
        }

        try {
            switch ($provider) {
                case 'gemini':
                    $response = BbOracle_Gemini::get_instance()->ftn_gemini_generate($test_prompt);
                    break;
                case 'groq':
                    $response = BbOracle_Groq::get_instance()->ftn_groq_generate($test_prompt);
                    break;
                case 'mistral':
                    $response = BbOracle_Mistral::get_instance()->ftn_mistral_generate($test_prompt);
                    break;
                default:
                    wp_send_json_error(['message' => 'Invalid provider']);
                    return;
            }

            if ($response && !str_starts_with($response, '[Error]')) {
                wp_send_json_success(['message' => 'OK: ' . mb_substr($response, 0, 100)]);
            } else {
                wp_send_json_error(['message' => $response ?: 'No response received']);
            }
        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function createPages(): void {
        if (!check_ajax_referer('bb_oracle_create_pages_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Nonce không hợp lệ.']);
            return;
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $pages_config = [
            [
                'title'   => 'Oracle Cards Online – Rút Bài Oracle Miễn Phí',
                'content' => "<!-- wp:shortcode -->\n[oracle_form mode=\"hub\"]\n<!-- /wp:shortcode -->",
                'slug'    => 'oracle-cards-online',
                'parent'  => 0,
            ],
            [
                'title'   => 'Oracle 1 Lá – Daily Card Hôm Nay',
                'content' => "<!-- wp:shortcode -->\n[oracle_form mode=\"topic\" spread=\"1_card\"]\n<!-- /wp:shortcode -->",
                'slug'    => 'oracle-1-la',
                'parent'  => 0,
            ],
            [
                'title'   => 'Oracle 2 Lá – Tình Huống và Hướng Dẫn',
                'content' => "<!-- wp:shortcode -->\n[oracle_form mode=\"topic\" spread=\"2_cards\"]\n<!-- /wp:shortcode -->",
                'slug'    => 'oracle-2-la',
                'parent'  => 0,
            ],
            [
                'title'   => 'Oracle 3 Lá – Tâm Trí Trái Tim Linh Hồn',
                'content' => "<!-- wp:shortcode -->\n[oracle_form mode=\"topic\" spread=\"3_cards\"]\n<!-- /wp:shortcode -->",
                'slug'    => 'oracle-3-la',
                'parent'  => 0,
            ],
            [
                'title'   => 'Hỏi Oracle – Đặt Câu Hỏi Nhận Thông Điệp',
                'content' => "<!-- wp:shortcode -->\n[oracle_form mode=\"question\" spread=\"3_cards\"]\n<!-- /wp:shortcode -->",
                'slug'    => 'oracle-cau-hoi',
                'parent'  => 0,
            ],
        ];

        $created_pages = [];

        foreach ($pages_config as $page_config) {
            $existing = get_posts([
                'post_type'   => 'page',
                'name'        => $page_config['slug'],
                'post_status' => 'any',
                'numberposts' => 1,
            ]);

            if (!empty($existing)) {
                $created_pages[] = [
                    'title'    => $page_config['title'],
                    'edit_url' => get_edit_post_link($existing[0]->ID),
                    'status'   => 'đã tồn tại (bỏ qua)',
                ];
                continue;
            }

            $page_id = wp_insert_post([
                'post_title'   => $page_config['title'],
                'post_content' => $page_config['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => $page_config['slug'],
                'post_parent'  => $page_config['parent'],
            ]);

            if ($page_id && !is_wp_error($page_id)) {
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
