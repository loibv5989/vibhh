<?php

if (!defined('ABSPATH')) {
    exit;
}

class TR_Admin {
    private $plugin_file;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu', [$this, 'register_settings_page'], 99);
        add_action('admin_init', [$this, 'save_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_bb_tarot_test_provider', [$this, 'test_provider']);
        add_action('wp_ajax_bb_tarot_create_pages', [$this, 'create_pages']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settings_link']);
    }

    public function enqueue_admin_assets($hook) {
        if (!str_contains($hook, 'bb-tarot-settings')) return;

        wp_enqueue_style('bb-tarot', TAROT_PLUGIN_URL . 'admin/assets/css/tarot.css', [], TAROT_VERSION);
        wp_enqueue_script('bb-tarot', TAROT_PLUGIN_URL . 'admin/assets/js/tarot.js', ['jquery'], TAROT_VERSION, true);

        wp_localize_script('bb-tarot', 'bbTarotAdmin', [
            'nonce' => wp_create_nonce('bb_tarot_test_nonce'),
            'create_pages_nonce' => wp_create_nonce('bb_tarot_create_pages_nonce'),
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    public function settings_link($links) {
        $settings_link = '<a href="admin.php?page=bb-tarot-settings">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings_page() {
        add_submenu_page(
                'fortune-settings',
                'Tarot Reading',
                'Tarot Reading',
                'manage_options',
                'bb-tarot-settings',
                [$this, 'render_settings_page']
        );
    }

    public function render_settings_page() {

        $model      = get_option('tarot_ai_model', 'gemini-flash-latest');
        $provider   = get_option('tarot_ai_provider', 'gemini');
        $groq_model = get_option('tarot_groq_model', 'llama-3.3-70b-versatile');
        $gemini_key = get_option('tarot_gemini_key', '');
        $groq_key   = get_option('tarot_groq_key', '');
        $mistral_key = get_option('tarot_mistral_key', '');
        $mistral_model = get_option('tarot_mistral_model', 'mistral-small-latest');

        $gatekeeper_order = get_option('tarot_gatekeeper_order', 'groq,mistral,gemini');
        $analysis_order = get_option('tarot_analysis_order', 'gemini,mistral,groq');
        $allow_ai = get_option('tarot_allow_ai', '0');

        ?>

        <div class="wrap">
            <h1>Tarot Reading Settings</h1>
            <form method="post">
                <?php wp_nonce_field('bb_tarot_settings_form'); ?>
                
                <!-- SECTION 0: AI Enable/Disable -->
                <h2>🤖 AI Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable AI</th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_ai" value="1" <?php checked($allow_ai, '1'); ?>>
                                Enable AI features (Gatekeeper + Card Interpretation)
                            </label>
                            <p class="description" style="color: #d63638;">
                                <strong>⚠️ Note:</strong> When disabled, all AI features will be deactivated. Users will not be able to request a Tarot reading interpretation.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <hr style="margin: 30px 0;">
                
                <h2>⚙️ Model Priority Order (Fallback)</h2>
                <p style="margin-top: 0; color: #666;">The system will automatically fall back to the next model if the primary one fails.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <strong>Step 1:</strong> Question Validation
                            <p style="font-weight: normal; color: #666; margin: 5px 0 0 0;">Decides whether to proceed to Step 2</p>
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
                            <strong>Step 2:</strong> Card Interpretation
                            <p style="font-weight: normal; color: #666; margin: 5px 0 0 0;">Analysis and response</p>
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
                <h2>🔑 API Keys & Models Configuration</h2>
                
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
                            <textarea name="gemini_key" rows="4" cols="60" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($gemini_key); ?></textarea>
                            <p class="description">Enter multiple keys to enable automatic rotation when rate limits are hit.</p>
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
                            <textarea name="groq_key" rows="4" cols="60" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($groq_key); ?></textarea>
                            <p class="description">Enter multiple keys to enable automatic rotation when rate limits are hit.</p>
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
                            <textarea name="mistral_key" rows="4" cols="60" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($mistral_key); ?></textarea>
                            <p class="description">Enter multiple keys to enable automatic rotation when rate limits are hit.</p>
                        </td>
                    </tr>
                </table>
                
                <h3 style="margin-top: 30px;">Test Connection</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Select provider to test</th>
                        <td>
                            <select name="provider" id="bb_tarot_provider_select" style="min-width: 250px;">
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
                <h2>📄 Create Pages</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Create Pages</th>
                        <td>
                            <button type="button" id="create-pages-btn" class="button button-primary">Create Pages</button>
                            <div id="create-pages-results" style="margin-top: 10px;"></div>
                            <p class="description">
                                Creates 6 pages with a hierarchy structure for the Tarot Reading plugin:<br>
                                • Free Online Tarot Reading (LV 1)<br>
                                • Three-Card Tarot Spread (LV 2)<br>
                                • Five-Card Tarot Spread (LV 2)<br>
                                • Horseshoe Tarot Spread (LV 2)<br>
                                • Celtic Cross Tarot Spread (LV 2)<br>
                                • Tarot Question Reading (LV 2)<br><br>
                                <strong>Pages will be created at:</strong><br>
                                • /tarot-online/ (main page)<br>
                                • /tarot-3-la/ (3-card spread)<br>
                                • /tarot-5-la/ (5-card spread)<br>
                                • /tarot-7-la/ (7-card spread)<br>
                                • /tarot-10-la/ (10-card spread)<br>
                                • /tarot-cau-hoi/ (question reading)<br><br>
                                <em>Note: Pages that already exist will be skipped.</em>
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
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'bb_tarot_settings_form')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['provider'])) {
            update_option('tarot_ai_provider', sanitize_text_field($_POST['provider']));
        }

        if (isset($_POST['model'])) {
            update_option('tarot_ai_model', sanitize_text_field($_POST['model']));
        }

        if (isset($_POST['groq_model'])) {
            update_option('tarot_groq_model', sanitize_text_field($_POST['groq_model']));
        }

        if (isset($_POST['mistral_model'])) {
            update_option('tarot_mistral_model', sanitize_text_field($_POST['mistral_model']));
        }

        if (isset($_POST['gemini_key'])) {
            update_option('tarot_gemini_key', sanitize_textarea_field($_POST['gemini_key']));
        }

        if (isset($_POST['groq_key'])) {
            update_option('tarot_groq_key', sanitize_textarea_field($_POST['groq_key']));
        }

        if (isset($_POST['mistral_key'])) {
            update_option('tarot_mistral_key', sanitize_textarea_field($_POST['mistral_key']));
        }

        update_option('tarot_allow_ai', isset($_POST['allow_ai']) ? '1' : '0');

        if (isset($_POST['gatekeeper_order'])) {
            update_option('tarot_gatekeeper_order', sanitize_text_field($_POST['gatekeeper_order']));
        }

        if (isset($_POST['analysis_order'])) {
            update_option('tarot_analysis_order', sanitize_text_field($_POST['analysis_order']));
        }
    }

    public function test_provider() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'bb_tarot_test_nonce')) {
            error_log('[Tarot Test] Invalid nonce. User=' . (get_current_user_id() ?: 0));
            wp_send_json_error([
                'message' => 'Invalid nonce',
                'debug' => [
                    'user_id' => get_current_user_id(),
                ]
            ]);
        }

        if (!current_user_can('manage_options')) {
            error_log('[Tarot Test] Permission denied. User=' . (get_current_user_id() ?: 0));
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

        if (!class_exists('TR_Gemini')) {
            require_once TAROT_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('TR_Groq')) {
            require_once TAROT_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('TR_Mistral')) {
            require_once TAROT_PLUGIN_DIR . 'includes/mistral.php';
        }

        error_log('[Tarot Test] Start. Provider=' . $provider . ' | User=' . (get_current_user_id() ?: 0));

        try {
            switch ($provider) {
                case 'gemini':
                    $response = TR_Gemini::get_instance()->ftn_gemini_generate($test_prompt, $test_info);
                    break;
                case 'groq':
                    $response = TR_Groq::get_instance()->ftn_groq_generate($test_prompt, $test_info);
                    break;
                case 'mistral':
                    $response = TR_Mistral::get_instance()->ftn_mistral_generate($test_prompt, $test_info);
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
                error_log('[Tarot Test] Failed keys: ' . implode(', ', $test_info['failed_keys']));
            }

            if ($response && !str_starts_with($response, '[Error]')) {
                error_log('[Tarot Test] Success - Provider: ' . $provider . ' | Model: ' . ($test_info['model'] ?? 'N/A') . ' | Key: ' . ($test_info['key'] ?? 'N/A') . ' | Duration: ' . $duration . 'ms');
                wp_send_json_success([
                    'response' => $response,
                    'model' => $test_info['model'] ?? 'N/A',
                    'key' => $test_info['key'] ?? 'N/A',
                    'duration_ms' => $duration,
                    'failed_keys' => $test_info['failed_keys'] ?? [],
                ]);
            } else {
                error_log('[Tarot Test] Failed - Provider: ' . $provider . ' | Error: ' . ($response ?: 'No response') . ' | Duration: ' . $duration . 'ms');
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
            error_log('[Tarot Test] Exception - Provider: ' . $provider . ' | ' . $e->getMessage());
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
        check_ajax_referer('bb_tarot_create_pages_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $pages_config = [
            [
                'title' => 'Free Online Tarot Reading',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[tarot_form mode="hub"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'boi-bai-tarot-online',
                'parent' => 0
            ],

            [
                'title' => 'Three-Card Tarot Spread',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[tarot_form mode="topic" spread="3_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tarot-3-la',
                'parent' => 'boi-bai-tarot-online'
            ],
            [
                'title' => 'Five-Card Tarot Spread',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[tarot_form mode="topic" spread="5_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tarot-5-la',
                'parent' => 'boi-bai-tarot-online'
            ],
            [
                'title' => 'Horseshoe Tarot Spread',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[tarot_form mode="topic" spread="7_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tarot-7-la',
                'parent' => 'boi-bai-tarot-online'
            ],
            [
                'title' => 'Celtic Cross Tarot Spread',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[tarot_form mode="topic" spread="10_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tarot-10-la',
                'parent' => 'boi-bai-tarot-online'
            ],
            [
                'title' => 'Tarot Question Reading',
                'content' => '<!-- wp:shortcode -->' . "\n" . '[tarot_form mode="question" spread="3_cards"]' . "\n" . '<!-- /wp:shortcode -->',
                'slug' => 'tarot-cau-hoi',
                'parent' => 'boi-bai-tarot-online'
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
                $parent_ids[$page_config['slug']] = $existing[0]->ID;
                $created_pages[] = [
                        'title'    => $page_config['title'],
                        'edit_url' => get_edit_post_link($existing[0]->ID),
                        'status'   => 'already exists (skipped)',
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
                        'status'   => 'created',
                ];
            }
        }

        if (!empty($created_pages)) {
            wp_send_json_success([
                    'message' => 'Processed ' . count($created_pages) . ' pages',
                    'pages'   => $created_pages,
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to create pages']);
        }
    }
}
