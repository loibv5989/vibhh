<?php

if (!defined('ABSPATH')) exit;

class BbZodiac_Admin {

    private string $plugin_file;

    public function __construct(string $plugin_file) {
        $this->plugin_file = $plugin_file;

        add_action('admin_menu',            [$this, 'registerSettingsPage'], 99);
        add_action('admin_init',            [$this, 'saveSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_bb_zodiac_test_provider', [$this, 'testProvider']);
        add_action('wp_ajax_bb_zodiac_create_pages',  [$this, 'createPages']);
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_file), [$this, 'settingsLink']);
    }

    public function enqueueAssets(string $hook): void {
        if (!str_contains($hook, 'bb-zodiac-settings')) return;

        wp_enqueue_style('bb-zodiac-admin-css', BB_ZODIAC_PLUGIN_URL . 'admin/assets/css/zodiac-admin.css', [], BB_ZODIAC_VERSION);
        wp_enqueue_script('bb-zodiac-admin-js', BB_ZODIAC_PLUGIN_URL . 'admin/assets/js/zodiac-admin.js', ['jquery'], BB_ZODIAC_VERSION, true);
        wp_localize_script('bb-zodiac-admin-js', 'bbZodiacAdmin', [
            'ajax_url'            => admin_url('admin-ajax.php'),
            'test_provider_nonce' => wp_create_nonce('bb_zodiac_test_nonce'),
            'create_pages_nonce'  => wp_create_nonce('bb_zodiac_create_pages_nonce'),
        ]);
    }

    public function settingsLink(array $links): array {
        array_unshift($links, '<a href="admin.php?page=bb-zodiac-settings">Settings</a>');
        return $links;
    }

    public function registerSettingsPage(): void {
        add_submenu_page(
            'fortune-settings',
            'Zodiac',
            'Zodiac',
            'manage_options',
            'bb-zodiac-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void {
        $model         = get_option('bb_zodiac_ai_model',      'gemini-flash-latest');
        $provider      = get_option('bb_zodiac_ai_provider',   'gemini');
        $groq_model    = get_option('bb_zodiac_groq_model',    'llama-3.3-70b-versatile');
        $gemini_key    = get_option('bb_zodiac_gemini_key',    '');
        $groq_key      = get_option('bb_zodiac_groq_key',      '');
        $mistral_key   = get_option('bb_zodiac_mistral_key',   '');
        $mistral_model = get_option('bb_zodiac_mistral_model', 'mistral-small-latest');
        $allow_ai      = get_option('bb_zodiac_allow_ai',      '0');
        ?>
        <div class="wrap">
            <h1>Settings | Zodiac</h1>
            <form method="post">
                <?php wp_nonce_field('bb_zodiac_settings_form'); ?>

                <h2>🤖 Enable / Disable AI</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Allow AI usage</th>
                        <td>
                            <label>
                                <input type="checkbox" name="allow_ai" value="1" <?php checked($allow_ai, '1'); ?>>
                                Enable AI function (Zodiac analysis)
                            </label>
                            <p class="description" style="color:#d63638;">
                                <strong>⚠️ Note:</strong> If turned off, all AI functions will be disabled.
                            </p>
                        </td>
                    </tr>
                </table>

                <hr style="margin:30px 0;">

                <h2>⚙️ AI Provider</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Primary Provider</th>
                        <td>
                            <select name="provider" style="min-width:250px;">
                                <option value="gemini"  <?php selected($provider, 'gemini');  ?>>Google Gemini</option>
                                <option value="groq"    <?php selected($provider, 'groq');    ?>>Groq</option>
                                <option value="mistral" <?php selected($provider, 'mistral'); ?>>Mistral AI</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <hr style="margin:30px 0;">

                <h2>🔑 API Keys &amp; Models Configuration</h2>

                <h3 style="margin-top:20px;">Google Gemini</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Gemini Model</th>
                        <td>
                            <select name="model" style="min-width:250px;">
                                <option value="gemini-flash-latest"      <?php selected($model, 'gemini-flash-latest');      ?>>gemini-flash-latest</option>
                                <option value="gemini-2.5-flash"         <?php selected($model, 'gemini-2.5-flash');         ?>>gemini-2.5-flash</option>
                                <option value="gemini-3-flash-preview"   <?php selected($model, 'gemini-3-flash-preview');   ?>>gemini-3-flash-preview</option>
                                <option value="gemini-2.5-flash-lite"    <?php selected($model, 'gemini-2.5-flash-lite');    ?>>gemini-2.5-flash-lite</option>
                                <option value="gemini-flash-lite-latest" <?php selected($model, 'gemini-flash-lite-latest'); ?>>gemini-flash-lite-latest</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Gemini API Keys</th>
                        <td>
                            <textarea name="gemini_key" rows="4" cols="60" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($gemini_key); ?></textarea>
                            <p class="description">Enter multiple keys so the system can auto-rotate when hitting rate limits.</p>
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
                            <textarea name="groq_key" rows="4" cols="60" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($groq_key); ?></textarea>
                            <p class="description">Enter multiple keys so the system can auto-rotate when hitting rate limits.</p>
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
                            <textarea name="mistral_key" rows="4" cols="60" placeholder="Enter API keys (one per line)"><?php echo esc_textarea($mistral_key); ?></textarea>
                            <p class="description">Enter multiple keys so the system can auto-rotate when hitting rate limits.</p>
                        </td>
                    </tr>
                </table>

                <h3 style="margin-top:30px;">Test Connection</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">Select provider to test</th>
                        <td>
                            <select id="bb_zodiac_test_provider_select" style="min-width:250px;">
                                <option value="gemini">Google Gemini</option>
                                <option value="groq">Groq</option>
                                <option value="mistral">Mistral AI</option>
                            </select>
                            <button type="button" id="bb-zodiac-test-provider" class="button" style="margin-left:10px;">Test Connection</button>
                            <div id="bb-zodiac-test-results" style="margin-top:10px;"></div>
                        </td>
                    </tr>
                </table>

                <hr style="margin:30px 0;">

                <h2>📄 Create Pages</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Create Zodiac Pages</th>
                        <td>
                            <button type="button" id="bb-zodiac-create-pages" class="button button-primary">Create Pages</button>
                            <div id="bb-zodiac-create-pages-results" style="margin-top:10px;"></div>
                            <p class="description">
                                Create page with shortcode <code>[zodiac_form]</code>:<br>
                                • <strong>/cung-hoang-dao/</strong> — Online zodiac decoding<br>
                                <em>Note: Existing pages will be skipped.</em>
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
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'bb_zodiac_settings_form')) return;
        if (!current_user_can('manage_options')) return;

        if (isset($_POST['provider']))      update_option('bb_zodiac_ai_provider',   sanitize_text_field($_POST['provider']));
        if (isset($_POST['model']))         update_option('bb_zodiac_ai_model',       sanitize_text_field($_POST['model']));
        if (isset($_POST['groq_model']))    update_option('bb_zodiac_groq_model',     sanitize_text_field($_POST['groq_model']));
        if (isset($_POST['mistral_model'])) update_option('bb_zodiac_mistral_model',  sanitize_text_field($_POST['mistral_model']));
        if (isset($_POST['gemini_key']))    update_option('bb_zodiac_gemini_key',     sanitize_textarea_field($_POST['gemini_key']));
        if (isset($_POST['groq_key']))      update_option('bb_zodiac_groq_key',       sanitize_textarea_field($_POST['groq_key']));
        if (isset($_POST['mistral_key']))   update_option('bb_zodiac_mistral_key',    sanitize_textarea_field($_POST['mistral_key']));

        update_option('bb_zodiac_allow_ai', isset($_POST['allow_ai']) ? '1' : '0');
    }

    public function testProvider(): void {
        if (!check_ajax_referer('bb_zodiac_test_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce.']);
            return;
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }
        if (get_option('bb_zodiac_allow_ai', '0') !== '1') {
            wp_send_json_error(['message' => 'AI is currently turned off in settings.']);
            return;
        }

        $provider    = sanitize_text_field($_POST['provider'] ?? 'gemini');
        $test_prompt = "Test connection - respond with 'OK'";

        if (!class_exists('BbZodiac_Gemini')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('BbZodiac_Groq')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('BbZodiac_Mistral')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/mistral.php';
        }

        try {
            switch ($provider) {
                case 'gemini':
                    $response = BbZodiac_Gemini::get_instance()->ftn_gemini_generate($test_prompt);
                    break;
                case 'groq':
                    $response = BbZodiac_Groq::get_instance()->ftn_groq_generate($test_prompt);
                    break;
                case 'mistral':
                    $response = BbZodiac_Mistral::get_instance()->ftn_mistral_generate($test_prompt);
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
        if (!check_ajax_referer('bb_zodiac_create_pages_nonce', 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce.']);
            return;
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        $pages_config = [
            [
                'title'   => 'Decode Your Zodiac Sign Online – 12 Astrological Signs',
                'content' => "<!-- wp:shortcode -->\n[zodiac_form]\n<!-- /wp:shortcode -->",
                'slug'    => 'cung-hoang-dao',
                'parent'  => 0,
            ],
            [
                'title'   => 'Zodiac Personality Analysis',
                'content' => "<!-- wp:shortcode -->\n[zodiac_tinh_cach]\n<!-- /wp:shortcode -->",
                'slug'    => 'tinh-cach',
                'parent'  => 'cung-hoang-dao',
            ],
            [
                'title'   => 'Love Match – Zodiac Compatibility',
                'content' => "<!-- wp:shortcode -->\n[zodiac_tinh_yeu]\n<!-- /wp:shortcode -->",
                'slug'    => 'tinh-yeu',
                'parent'  => 'cung-hoang-dao',
            ],
            [
                'title'   => 'Daily Horoscope by Zodiac Sign',
                'content' => "<!-- wp:shortcode -->\n[zodiac_tu_vi]\n<!-- /wp:shortcode -->",
                'slug'    => 'tu-vi',
                'parent'  => 'cung-hoang-dao',
            ]
        ];

        $created_pages  = [];
        $parent_id_map  = [];

        foreach ($pages_config as $page_config) {
            $existing = get_posts([
                'post_type'   => 'page',
                'name'        => $page_config['slug'],
                'post_status' => 'any',
                'numberposts' => 1,
            ]);

            if (!empty($existing)) {
                $pid = $existing[0]->ID;
                $parent_id_map[$page_config['slug']] = $pid;
                $created_pages[] = [
                    'title'    => $page_config['title'],
                    'edit_url' => get_edit_post_link($pid),
                    'status'   => 'already exists (skipped)',
                ];
                continue;
            }

            $parent_id = 0;
            if (!empty($page_config['parent']) && isset($parent_id_map[$page_config['parent']])) {
                $parent_id = $parent_id_map[$page_config['parent']];
            }

            $page_id = wp_insert_post([
                'post_title'   => $page_config['title'],
                'post_content' => $page_config['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => $page_config['slug'],
                'post_parent'  => $parent_id,
            ]);

            if ($page_id && !is_wp_error($page_id)) {
                $parent_id_map[$page_config['slug']] = $page_id;
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
            wp_send_json_error(['message' => 'Unable to create pages']);
        }
    }
}
