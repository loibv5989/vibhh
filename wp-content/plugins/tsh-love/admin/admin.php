<?php
if (!defined('ABSPATH')) exit;

class TshLove_Admin {

    private string $plugin_file;

    public function __construct(string $plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu',            [$this, 'registerSettingsPage']);
        add_action('admin_init',            [$this, 'saveSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_bty_tsh_test_provider', [$this, 'testProvider']);
        add_action('wp_ajax_bty_tsh_create_pages',  [$this, 'createPages']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settingsLink']);

    }

    public function enqueueAssets(string $hook): void {
        if ('toplevel_page_bty-tsh-settings' !== $hook) return;
        wp_enqueue_style('bty-tsh',  TSH_LOVE_PLUGIN_URL . 'admin/assets/css/bty-tsh.css', [], TSH_LOVE_VERSION);
        wp_enqueue_script('bty-tsh',  TSH_LOVE_PLUGIN_URL . 'admin/assets/js/bty-tsh.js',  ['jquery'], TSH_LOVE_VERSION, true);
        wp_localize_script('bty-tsh', 'btyTshAdmin', [
            'test_provider_nonce' => wp_create_nonce('bty_tsh_test_nonce'),
            'create_pages_nonce' => wp_create_nonce('bty_tsh_create_pages_nonce'),
        ]);
    }

    public function settingsLink(array $links): array {
        array_unshift($links, '<a href="admin.php?page=bty-tsh-settings">Settings</a>');
        return $links;
    }

    public function registerSettingsPage(): void {
        add_submenu_page(
                'fortune-settings',
                'Bói Tình Yêu - THS',
                'Bói Tình Yêu - THS',
                'manage_options',
                'bty-tsh-settings',
                [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void {
        $model         = get_option('bty_tsh_ai_model', 'gemini-flash-latest');
        $provider      = get_option('bty_tsh_ai_provider', 'gemini');
        $groq_model    = get_option('bty_tsh_groq_model', 'llama-3.3-70b-versatile');
        $gemini_key    = get_option('bty_tsh_gemini_key', '');
        $groq_key      = get_option('bty_tsh_groq_key', '');
        $mistral_key   = get_option('bty_tsh_mistral_key', '');
        $mistral_model = get_option('bty_tsh_mistral_model', 'mistral-small-latest');
        $allow_ai      = get_option('bty_tsh_allow_ai', '1');
        $analysis_order = get_option('bty_tsh_analysis_order', 'gemini,mistral,groq');
        ?>
        <div class="wrap">
            <h1>Cấu hình | Bói Tình Yêu - Thần Số Học</h1>
            <form method="post">
                <?php wp_nonce_field('bty_tsh_settings_form'); ?>

                <h2>🤖 Bật/Tắt AI</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Cho phép sử dụng AI</th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_ai" value="1" <?php checked($allow_ai, '1'); ?>>
                                Bật chức năng AI
                            </label>
                            <p class="description" style="color: #d63638;">
                                <strong>⚠️ Lưu ý:</strong> Nếu tắt, toàn bộ chức năng AI sẽ bị vô hiệu hóa.
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
                            <strong>Analysis order</strong>
                            <p style="font-weight: normal; color: #666; margin: 5px 0 0 0;">Phân tích và giải đáp Bói Tình Yêu</p>
                        </th>
                        <td>
                            <select name="analysis_order" style="min-width: 260px;">
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

                <h2>🔑 Cấu hình API Keys &amp; Models</h2>

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
                            <select name="provider" id="bty_tsh_provider_select" style="min-width: 250px;">
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

                <h2>📄 Tạo trang</h2>
                <p>Tự động tạo trang WordPress với shortcode <code>[tsh_love_form]</code>.</p>
                <button type="button" id="bty-tsh-create-pages" class="button button-secondary">Tạo Trang Mẫu</button>
                <div id="bty-tsh-pages-result" style="margin-top:10px;"></div>

                <hr>
                <?php submit_button('Lưu Cấu Hình'); ?>
            </form>
        </div>
        <?php
    }

    public function saveSettings(): void {
        if (
            !isset($_POST['_wpnonce']) ||
            !wp_verify_nonce($_POST['_wpnonce'], 'bty_tsh_settings_form') ||
            !current_user_can('manage_options')
        ) return;

        if (isset($_POST['provider'])) {
            update_option('bty_tsh_ai_provider', sanitize_text_field($_POST['provider']));
        }

        if (isset($_POST['model'])) {
            update_option('bty_tsh_ai_model', sanitize_text_field($_POST['model']));
        }

        if (isset($_POST['groq_model'])) {
            update_option('bty_tsh_groq_model', sanitize_text_field($_POST['groq_model']));
        }

        if (isset($_POST['mistral_model'])) {
            update_option('bty_tsh_mistral_model', sanitize_text_field($_POST['mistral_model']));
        }

        update_option('bty_tsh_allow_ai', isset($_POST['allow_ai']) ? '1' : '0');

        if (isset($_POST['analysis_order'])) {
            update_option('bty_tsh_analysis_order', sanitize_text_field($_POST['analysis_order']));
        }

        if (isset($_POST['gemini_key'])) {
            update_option('bty_tsh_gemini_key', sanitize_textarea_field($_POST['gemini_key']));
        }

        if (isset($_POST['groq_key'])) {
            update_option('bty_tsh_groq_key', sanitize_textarea_field($_POST['groq_key']));
        }

        if (isset($_POST['mistral_key'])) {
            update_option('bty_tsh_mistral_key', sanitize_textarea_field($_POST['mistral_key']));
        }
    }

    public function testProvider(): void {
        check_ajax_referer('bty_tsh_test_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $provider = sanitize_text_field($_POST['provider']);
        $test_prompt = "Test connection - respond with 'OK'";

        if (!class_exists('TshLove_Helpers')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/helpers.php';
        }
        if (!class_exists('TshLove_Gemini')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('TshLove_Groq')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('TshLove_Mistral')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/mistral.php';
        }

        try {
            switch ($provider) {
                case 'gemini':
                    $response = TshLove_Gemini::get_instance()->ftn_gemini_generate($test_prompt);
                    break;
                case 'groq':
                    $response = TshLove_Groq::get_instance()->ftn_groq_generate($test_prompt);
                    break;
                case 'mistral':
                    $response = TshLove_Mistral::get_instance()->ftn_mistral_generate($test_prompt);
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

    public function createPages(): void {
        if (!check_ajax_referer('bty_tsh_create_pages_nonce', 'nonce', false) || !current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $pages   = [
            [
                    'title' => 'Bói Tình Yêu Thần Số Học',
                    'content' => '<!-- wp:shortcode -->' . "\n" . '[tsh_love_form]' . "\n" . '<!-- /wp:shortcode -->',
                    'slug' => 'boi-tinh-yeu-than-so-hoc'
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
            wp_send_json_success(['message' => 'Trang đã tồn tại.']);
        }
        wp_send_json_success(['message' => 'Đã tạo: ' . implode(', ', $created)]);
    }
}
