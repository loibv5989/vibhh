<?php

if (!defined('ABSPATH')) exit;

class Batu_Admin {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', [$this, 'register_menu'], 20);
        add_action('admin_init', [$this, 'save_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_battu_test_provider', [$this, 'test_provider']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settings_link']);
    }

    public function settings_link($links) {
        $settings_link = '<a href="admin.php?page=bat-tu">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_menu() {
        add_submenu_page(
                'fortune-settings',
                'Bát Tự (Tứ Trụ)',
                'Bát Tự (Tứ Trụ)',
                'manage_options',
                'bat-tu',
                [$this, 'render_settings_page']
        );
    }

    public function enqueue_admin_assets($hook) {
        if (!str_contains($hook, 'bat-tu')) return;

        wp_enqueue_style(
            'battu-admin-css',
            BATTU_PLUGIN_URL . 'admin/assets/css/battu-admin.css',
            [],
            BATTU_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'battu-admin-js',
            BATTU_PLUGIN_URL . 'admin/assets/js/battu-admin.js',
            ['jquery'],
            BATTU_PLUGIN_VERSION,
            true
        );

        wp_localize_script('battu-admin-js', 'battuAdmin', [
                'ajax_url' => admin_url('admin-ajax.php'),
            'test_provider_nonce' => wp_create_nonce('battu_test_nonce'),
        ]);
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $allow_ai = get_option('battu_allow_ai', '0');
        $provider = get_option('battu_ai_provider', 'gemini');

        $gemini_model = get_option('battu_gemini_model', 'gemini-flash-latest');
        $groq_model = get_option('battu_groq_model', 'llama-3.3-70b-versatile');
        $mistral_model = get_option('battu_mistral_model', 'mistral-small-latest');

        $gemini_key = get_option('battu_gemini_key', '');
        $groq_key = get_option('battu_groq_key', '');
        $mistral_key = get_option('battu_mistral_key', '');

        $gatekeeper_order = get_option('battu_gatekeeper_order', 'groq,mistral,gemini');
        $analysis_order = get_option('battu_analysis_order', 'gemini,mistral,groq');

        ?>
        <div class="wrap">
            <h1>Cấu hình | Bát Tự</h1>
            <form method="post">
                <?php wp_nonce_field('battu_settings_form'); ?>

                <h2>Bật/Tắt AI</h2>
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

                <hr>

                <table class="form-table">
                    <tr>
                        <th scope="row">AI Provider</th>
                        <td>
                            <select name="provider" id="battu_provider_select">
                                <option value="gemini" <?php selected($provider, 'gemini'); ?>>Google Gemini</option>
                                <option value="groq" <?php selected($provider, 'groq'); ?>>Groq</option>
                                <option value="mistral" <?php selected($provider, 'mistral'); ?>>Mistral AI</option>
                            </select>
                            <button type="button" id="battu-test-provider" class="button" style="margin-left: 10px;">Test Connection</button>
                            <div id="battu-test-results" style="margin-top: 10px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">AI Model</th>
                        <td>
                            <div id="battu-gemini-models" style="<?php echo $provider === 'gemini' ? '' : 'display:none'; ?>">
                                <select name="gemini_model">
                                    <option value="gemini-flash-latest" <?php selected($gemini_model, 'gemini-flash-latest'); ?>>gemini-flash-latest</option>
                                    <option value="gemini-2.5-flash" <?php selected($gemini_model, 'gemini-2.5-flash'); ?>>Gemini 2.5 Flash</option>
                                    <option value="gemini-3-flash-preview" <?php selected($gemini_model, 'gemini-3-flash-preview'); ?>>Gemini 3 Flash preview</option>
                                    <option value="gemini-2.5-flash-lite" <?php selected($gemini_model, 'gemini-2.5-flash-lite'); ?>>gemini-2.5-flash-lite</option>
                                    <option value="gemini-flash-lite-latest" <?php selected($gemini_model, 'gemini-flash-lite-latest'); ?>>gemini-flash-lite-latest</option>
                                </select>
                            </div>
                            <div id="battu-groq-models" style="<?php echo $provider === 'groq' ? '' : 'display:none'; ?>">
                                <select name="groq_model">
                                    <option value="llama-3.3-70b-versatile" <?php selected($groq_model, 'llama-3.3-70b-versatile'); ?>>Llama 3.3 70B Versatile</option>
                                    <option value="qwen/qwen3-32b" <?php selected($groq_model, 'qwen/qwen3-32b'); ?>>Qwen3 32B</option>
                                    <option value="openai/gpt-oss-120b" <?php selected($groq_model, 'openai/gpt-oss-120b'); ?>>openai/gpt-oss-120b</option>
                                </select>
                            </div>
                            <div id="battu-mistral-models" style="<?php echo $provider === 'mistral' ? '' : 'display:none'; ?>">
                                <select name="mistral_model">
                                    <option value="mistral-small-latest" <?php selected($mistral_model, 'mistral-small-latest'); ?>>mistral-small-latest</option>
                                    <option value="mistral-large-2411" <?php selected($mistral_model, 'mistral-large-2411'); ?>>mistral-large-2411</option>
                                    <option value="mistral-medium-latest" <?php selected($mistral_model, 'mistral-medium-latest'); ?>>mistral-medium-latest</option>
                                    <option value="mistral-medium-2505" <?php selected($mistral_model, 'mistral-medium-2505'); ?>>mistral-medium-2505</option>
                                    <option value="mistral-medium-2508" <?php selected($mistral_model, 'mistral-medium-2508'); ?>>mistral-medium-2508</option>
                                    <option value="mistral-large-latest" <?php selected($mistral_model, 'mistral-large-latest'); ?>>mistral-large-latest</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>

                <div id="battu-gemini-settings" style="<?php echo $provider === 'gemini' ? '' : 'display:none'; ?>">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Gemini API Keys</th>
                            <td>
                                <textarea name="gemini_key" rows="5" cols="50" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($gemini_key); ?></textarea>
                                <p class="description">Enter multiple Gemini API keys, one per line. The system will rotate through them automatically.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="battu-groq-settings" style="<?php echo $provider === 'groq' ? '' : 'display:none'; ?>">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Groq API Keys</th>
                            <td>
                                <textarea name="groq_key" rows="5" cols="50" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($groq_key); ?></textarea>
                                <p class="description">Enter multiple Groq API keys, one per line. The system will rotate through them automatically.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="battu-mistral-settings" style="<?php echo $provider === 'mistral' ? '' : 'display:none'; ?>">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Mistral API Keys</th>
                            <td>
                                <textarea name="mistral_key" rows="5" cols="50" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($mistral_key); ?></textarea>
                                <p class="description">Enter multiple Mistral API keys, one per line. The system will rotate through them automatically.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <hr>
                <h2>Thứ tự ưu tiên (Fallback)</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Gatekeeper order</th>
                        <td>
                            <select name="gatekeeper_order" style="min-width: 260px;">
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
                        <th scope="row">Analysis order</th>
                        <td>
                            <select name="analysis_order" style="min-width: 260px;">
                                <option value="gemini,mistral,groq" <?php selected($analysis_order, 'gemini,mistral,groq'); ?>>gemini → mistral → groq</option>
                                <option value="gemini,groq,mistral" <?php selected($analysis_order, 'gemini,groq,mistral'); ?>>gemini → groq → mistral</option>
                                <option value="mistral,gemini,groq" <?php selected($analysis_order, 'mistral,gemini,groq'); ?>>mistral → gemini → groq</option>
                                <option value="mistral,groq,gemini" <?php selected($analysis_order, 'mistral,groq,gemini'); ?>>mistral → groq → gemini</option>
                                <option value="groq,gemini,mistral" <?php selected($analysis_order, 'groq,gemini,mistral'); ?>>groq → gemini → mistral</option>
                                <option value="groq,mistral,gemini" <?php selected($analysis_order, 'groq,mistral,gemini'); ?>>groq → mistral → gemini</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <hr>
                <?php submit_button('Lưu Cấu Hình'); ?>
            </form>
        </div>
        <?php
    }

    public function test_provider() {
        check_ajax_referer('battu_test_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        if (get_option('battu_allow_ai', '0') !== '1') {
            wp_send_json_error(['message' => 'AI hiện đang tắt trong phần cấu hình.']);
        }

        $provider = isset($_POST['provider']) ? sanitize_text_field(wp_unslash($_POST['provider'])) : '';
        $test_prompt = "Test connection - respond with 'OK'";

        if (!class_exists('Battu_Gemini')) {
            require_once BATTU_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('Battu_Groq')) {
            require_once BATTU_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('Battu_Mistral')) {
            require_once BATTU_PLUGIN_DIR . 'includes/mistral.php';
        }

        try {
            switch ($provider) {
                case 'gemini':
                    $response = Battu_Gemini::get_instance()->ftn_battu_gemini_generate($test_prompt);
                    break;
                case 'groq':
                    $response = Battu_Groq::get_instance()->ftn_battu_groq_generate($test_prompt);
                    break;
                case 'mistral':
                    $response = Battu_Mistral::get_instance()->ftn_battu_mistral_generate($test_prompt);
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

    public function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }
        if (!isset($_POST['_wpnonce'])) {
            return;
        }
        if (!wp_verify_nonce($_POST['_wpnonce'], 'battu_settings_form')) {
            return;
        }

        $allow_ai = isset($_POST['allow_ai']) ? '1' : '0';
        update_option('battu_allow_ai', $allow_ai);

        $provider = isset($_POST['provider']) ? sanitize_text_field(wp_unslash($_POST['provider'])) : 'gemini';
        if (!in_array($provider, ['gemini', 'groq', 'mistral'], true)) {
            $provider = 'gemini';
        }
        update_option('battu_ai_provider', $provider);

        $gatekeeper_order = isset($_POST['gatekeeper_order']) ? sanitize_text_field(wp_unslash($_POST['gatekeeper_order'])) : 'groq,mistral,gemini';
        update_option('battu_gatekeeper_order', $gatekeeper_order);

        $analysis_order = isset($_POST['analysis_order']) ? sanitize_text_field(wp_unslash($_POST['analysis_order'])) : 'gemini,mistral,groq';
        update_option('battu_analysis_order', $analysis_order);

        $gemini_model = isset($_POST['gemini_model']) ? sanitize_text_field(wp_unslash($_POST['gemini_model'])) : 'gemini-flash-latest';
        $groq_model = isset($_POST['groq_model']) ? sanitize_text_field(wp_unslash($_POST['groq_model'])) : 'llama-3.3-70b-versatile';
        $mistral_model = isset($_POST['mistral_model']) ? sanitize_text_field(wp_unslash($_POST['mistral_model'])) : 'mistral-small-latest';
        update_option('battu_gemini_model', $gemini_model);
        update_option('battu_groq_model', $groq_model);
        update_option('battu_mistral_model', $mistral_model);

        $gemini_key = isset($_POST['gemini_key']) ? sanitize_textarea_field(wp_unslash($_POST['gemini_key'])) : '';
        $groq_key = isset($_POST['groq_key']) ? sanitize_textarea_field(wp_unslash($_POST['groq_key'])) : '';
        $mistral_key = isset($_POST['mistral_key']) ? sanitize_textarea_field(wp_unslash($_POST['mistral_key'])) : '';
        update_option('battu_gemini_key', $gemini_key);
        update_option('battu_groq_key', $groq_key);
        update_option('battu_mistral_key', $mistral_key);
    }
}
