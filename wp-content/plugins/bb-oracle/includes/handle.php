<?php

if (!defined('ABSPATH')) exit;

class BbOracle_Handle {

    private static ?self $instance = null;
    private ?string $cache_base_dir = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('oracle_form', [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
    }

    private static function loadCalc(): void {
        if (!class_exists('BbOracle_Calc')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/calc.php';
        }
    }

    private static function loadAIProviders(): void {
        if (!class_exists('BbOracle_Gemini')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('BbOracle_Groq')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('BbOracle_Mistral')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'includes/mistral.php';
        }
    }

    public function registerRestRoutes(): void {
        register_rest_route('oracle/v1', '/draw', [
            'methods'             => 'POST',
            'callback'            => [$this, 'apiDraw'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('oracle/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'apiAnalyze'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'oracle_form')) return;
        wp_enqueue_style('bb-oracle',   BB_ORACLE_PLUGIN_URL . 'assets/oracle.css',  [], BB_ORACLE_VERSION);
        wp_enqueue_script('bb-oracle', BB_ORACLE_PLUGIN_URL . 'assets/oracle.js', ['jquery'], BB_ORACLE_VERSION, true);
        wp_localize_script('bb-oracle', 'OracleAjax', [
            'api_url'   => rest_url('oracle/v1')
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'oracle_form')) return;
        echo '<link rel="preload" href="' . esc_url(BB_ORACLE_PLUGIN_URL . 'assets/oracle.css' . '?ver=' . BB_ORACLE_VERSION) . '" as="style">' . "\n";
    }

    public function renderShortcode($atts): string {
        $atts = shortcode_atts([
            'mode'   => 'hub',
            'spread' => '3_cards',
        ], $atts);

        $mode       = $atts['mode'];
        $spread_key = $atts['spread'];

        self::loadCalc();
        $spreads    = BbOracle_Calc::getSpreads();

        $current_spread = $spreads[$spread_key] ?? $spreads['3_cards'];
        $total_cards    = $current_spread['count'];

        ob_start();
        if ($mode == 'hub') {
            include_once BB_ORACLE_PLUGIN_DIR . 'template/landing.php';
        } else {
            include_once BB_ORACLE_PLUGIN_DIR . 'template/step_1.php';
        }
        return ob_get_clean();
    }

    public static function render(
        string $name,
        string $topic,
        array  $cards,
        string $mode       = 'topic',
        string $question   = '',
        string $spread_key = '3_cards'
    ): string {
        self::loadCalc();
        $spreads       = BbOracle_Calc::getSpreads();
        $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions     = $spread_config['positions'];
        $allow_ai      = get_option('bb_oracle_allow_ai', '0');

        $element_labels = [
            'earth' => 'Earth 🌍', 'water' => 'Water 💧', 'fire'  => 'Fire 🔥',
            'air'   => 'Air 🌬', 'wind'  => 'Air 🌬',  'wood'  => 'Wood 🌿',
            'light' => 'Light ✨', 'moon' => 'Moon 🌙',
            'sun'   => 'Sun ☀️', 'ether' => 'Ether 🌌', 'heart' => 'Heart 💜',
        ];
        $topic_labels = [
            'love' => 'Love', 'career' => 'Career', 'finance' => 'Finance',
            'study' => 'Study', 'health' => 'Health', 'future' => 'Future',
        ];
        $colors_palette = ['#8b5cf6', '#d4af37', '#10b981', '#f43f5e', '#0ea5e9'];

        $count      = $spread_config['count'];
        $intro_text = ($mode === 'question' && !empty($question))
            ? 'Method: ' . $spread_config['name']
            : 'Topic: ' . ($topic_labels[$topic] ?? $topic) . ' · ' . $count . ' cards drawn:';

        $lines = [
            ['type' => 'greeting', 'text' => $intro_text],
            ['type' => 'intro',    'text' => ''],
            ['type' => 'divider',  'text' => ''],
        ];

        $color_idx = 0;
        foreach ($positions as $pos_key => $pos_label) {
            if (!isset($cards[$pos_key])) continue;
            $c = $cards[$pos_key];
            $lines[] = [
                'type'  => 'index',
                'key'   => $pos_key,
                'label' => 'Card',
                'value' => $c['name'] . ' (' . $pos_label . ')',
                'color' => $colors_palette[$color_idx % count($colors_palette)],
            ];
            $color_idx++;
        }
        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines_json = json_encode($lines, JSON_UNESCAPED_UNICODE);

        ob_start(); ?>

        <?php if ($mode === 'question' && !empty($question)): ?>
            <div class="trt-context-badge">
                <span class="trt-context-icon">Question » </span>
                <span class="trt-context-text"><?= esc_html(mb_substr($question, 0, 120)) ?></span>
            </div>
        <?php elseif (!empty($topic)): ?>
            <div class="trt-context-badge">
                <span class="trt-context-icon">» </span>
                <span class="trt-context-text">Topic: <?= esc_html($topic_labels[$topic] ?? $topic) ?></span>
            </div>
        <?php endif; ?>

        <?php
        include_once BB_ORACLE_PLUGIN_DIR . 'template/step_2.php';
        return ob_get_clean();
    }

    public function apiDraw(WP_REST_Request $request): WP_REST_Response {
        $params = $request->get_json_params();

        self::loadCalc();
        $spread_key = sanitize_text_field($params['spread'] ?? '3_cards');
        if (!BbOracle_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $mode     = sanitize_text_field($params['mode']     ?? 'topic');
        $topic    = sanitize_text_field($params['topic']    ?? '');
        $question = sanitize_text_field($params['question'] ?? '');

        $liteCards  = BbOracle_Calc::drawLite($spread_key);
        $fullCards  = BbOracle_Calc::hydrate($liteCards);

        $renderHTML = self::render('', $mode === 'topic' ? $topic : '', $fullCards, $mode, $question, $spread_key);

        return new WP_REST_Response(['html' => $renderHTML, 'cards' => $liteCards], 200);
    }

    private function oracle_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'oracle_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);

        if ($count >= BB_ORACLE_RATE_LIMIT) return false;

        $ttl = strtotime('tomorrow', current_time('timestamp')) - current_time('timestamp');
        set_transient($key, $count + 1, $ttl);
        return true;
    }

    private static function get_cookie_user() {
        $cookie = $_COOKIE[LOGGED_IN_COOKIE] ?? '';
        $parsed = false;

        if ($cookie) {
            $parsed = wp_parse_auth_cookie($cookie, 'logged_in');
        }
        return $parsed ?: false;
    }

    public function validate_logged_in() {
        return (bool) self::get_cookie_user();
    }

    public function apiAnalyze(WP_REST_Request $request): WP_REST_Response {
        if (!$this->validate_logged_in()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please log in to use this feature.'], 200);
        }

        if (get_option('bb_oracle_allow_ai', '0') !== '1') {
            return new WP_REST_Response(['success' => false, 'message' => 'This feature is temporarily unavailable. Please check back later.'], 200);
        }

        if (!$this->oracle_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Daily analysis limit reached. Please come back tomorrow.'], 200);
        }

        $params = $request->get_json_params();
        $hp_trap = $params['hp_trap'] ?? '';

        if (!empty($hp_trap)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Connection refused.'], 200);
        }

        $name       = mb_substr(sanitize_text_field($params['full_name']    ?? ''), 0, 60);
        $mode       = sanitize_text_field($params['mode']                   ?? 'topic');
        $topic      = sanitize_text_field($params['topic']                  ?? '');
        $question   = mb_substr(sanitize_textarea_field($params['question'] ?? ''), 0, 500);
        $spread_key = sanitize_text_field($params['spread']                 ?? '3_cards');

        self::loadCalc();
        if (!BbOracle_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        if (empty($name)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please enter your name.'], 200);
        }
        if ($mode === 'question' && empty($question)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please enter a question before analyzing.'], 200);
        }

        $liteCards = $params['cards'] ?? [];

        if (!is_array($liteCards) || empty($liteCards) || count($liteCards) > 10) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card data.'], 403);
        }

        foreach ($liteCards as $c) {
            if (!is_array($c) || empty($c['key'])) {
                return new WP_REST_Response(['success' => false, 'message' => 'Card structure is malformed.'], 200);
            }
        }

        $fullCards = BbOracle_Calc::hydrate($liteCards);

        self::loadAIProviders();

        $providers = [
            'gemini'  => fn($p) => BbOracle_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => BbOracle_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => BbOracle_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        if ($mode === 'question') {
            $gatekeeper_file = BB_ORACLE_PLUGIN_DIR . 'includes/gatekeeper.php';
            if (file_exists($gatekeeper_file)) {
                require_once $gatekeeper_file;

                if (function_exists('bb_oracle_build_gatekeeper_prompt')) {
                    $gatekeeper_prompt = bb_oracle_build_gatekeeper_prompt($question);
                    $gk_response = '';

                    $gatekeeper_order_str = get_option('bb_oracle_gatekeeper_order', 'groq,mistral,gemini');
                    $gatekeeper_order = array_map('trim', explode(',', $gatekeeper_order_str));

                    foreach ($gatekeeper_order as $current_provider) {
                        if (!isset($providers[$current_provider])) continue;
                        try {
                            $res = $providers[$current_provider]($gatekeeper_prompt);
                            if (!empty($res) && !str_starts_with($res, '[Error]')) {
                                $gk_response = trim(mb_strtoupper($res, 'UTF-8'));
                                break;
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }

                    if (strpos($gk_response, 'NO') !== false || empty($gk_response)) {
                        $html_fallback = '<br><span class="ast-reload" onclick="window.location.reload()">Please rephrase your question</span> with more detail and clarity.';

                        return new WP_REST_Response([
                            'success'   => true,
                            'is_cached' => false,
                            'html'      => $html_fallback
                        ], 200);
                    }
                }
            }
        }

        $card_keys = implode(',', array_column($liteCards, 'key'));
        $cache_str = implode('|', [
            $name,
            $mode,
            $spread_key,
            $mode === 'question' ? md5($question) : $topic,
            $card_keys,
        ]);
        $cache_key = md5($cache_str);

        $cached = $this->getCache($cache_key);
        if ($cached !== null) {
            $parsed               = BbOracle_Calc::parseResponse($cached);
            $parsed['is_cached']  = true;
            return new WP_REST_Response($parsed, 200);
        }

        require_once BB_ORACLE_PLUGIN_DIR . 'includes/prompt.php';
        $prompt = ($mode === 'question') ? prompt_question($name, $question, $fullCards, $spread_key)
            : prompt_topic($name, $topic, $fullCards, $spread_key);

        $analysis_order_str = get_option('bb_oracle_analysis_order', 'gemini,mistral,groq');
        $analysis_order = array_map('trim', explode(',', $analysis_order_str));

        $response = null;
        $last_error = null;
        $successful_provider = '';

        foreach ($analysis_order as $current_provider) {
            if (!isset($providers[$current_provider])) continue;
            try {
                $res = $providers[$current_provider]($prompt);
                if (!empty($res) && !str_starts_with($res, '[Error]')) {
                    $response = $res;
                    $successful_provider = $current_provider;
                    break;
                }
            } catch (Exception $e) {
                $last_error = $e->getMessage();
                continue;
            }
        }

        if ($response !== null) {
            if ($successful_provider === 'gemini') {
                $this->saveCache($cache_key, $response);
            }
            $parsed              = BbOracle_Calc::parseResponse($response);
            $parsed['is_cached'] = false;
            return new WP_REST_Response($parsed, 200);
        } else {
            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => $last_error ?: 'Connection error. Please try again later.'
                ], 200);
        }
    }

    private function getCacheDir(): string {
        if ($this->cache_base_dir === null) {
            $upload_dir           = wp_upload_dir();
            $dir                  = $upload_dir['basedir'] . '/bb-oracle';
            $this->cache_base_dir = $dir;
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
                chmod($dir, 0755);
                file_put_contents($dir . '/.htaccess', "Deny from all\n");
                file_put_contents($dir . '/index.php', "<?php // silence\n");
            }
        }
        return $this->cache_base_dir;
    }

    private function getCacheFilePath(string $key): string {
        return $this->getCacheDir() . '/' . $key . '.php';
    }

    private function getCache(string $key): ?string {
        $file = $this->getCacheFilePath($key);
        if (!file_exists($file)) return null;
        if (time() - filemtime($file) > 365 * DAY_IN_SECONDS) {
            @unlink($file);
            return null;
        }
        $raw  = file_get_contents($file);
        $json = substr($raw, strlen('<?php exit; ?>'));
        $data = json_decode($json, true);
        return $data['response'] ?? null;
    }

    private function saveCache(string $key, string $response): void {
        $content = '<?php exit; ?>' . json_encode(['response' => $response, 'created' => time()]);
        file_put_contents($this->getCacheFilePath($key), $content, LOCK_EX);
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && has_shortcode($post->post_content, 'oracle_form')) {
            return '';
        }
        return $title;
    }
}

BbOracle_Handle::get_instance();