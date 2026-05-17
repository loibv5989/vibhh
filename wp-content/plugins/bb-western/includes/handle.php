<?php
if (!defined('ABSPATH')) exit;

class BB_Western_Handle {
    private static ?BB_Western_Handle $instance = null;
    private bool $cache_dir_ready = false;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('western_form', [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
    }

    private static function loadCalc(): void {
        if (!class_exists('Western_Calc')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/calc.php';
        }
    }

    private static function loadAIProviders(): void {
        if (!class_exists('BBW_Gemini')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('BBW_Groq')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('BBW_Mistral')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/mistral.php';
        }
    }

    private static function loadPrompt(): void {
        if (!function_exists('western_build_prompt_topic')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/prompt.php';
        }
    }

    private static function loadRender(): void {
        if (!function_exists('western_render')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'template/render.php';
        }
    }

    public function registerRestRoutes(): void {
        register_rest_route('western/v1', '/draw', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestDraw'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('western/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestAnalyze'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'western_form')) return;
        wp_enqueue_style('western',  BB_WESTERN_PLUGIN_URL . 'assets/western.css',  [], BB_WESTERN_VERSION);
        wp_enqueue_script('western', BB_WESTERN_PLUGIN_URL . 'assets/western.js', ['jquery'], BB_WESTERN_VERSION, true);
        wp_localize_script('western', 'WesternAjax', [
                'api_url' => esc_url_raw(rest_url('western/v1/')),
                'nonce'    => wp_create_nonce('wp_rest'),
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'western_form')) return;
        echo '<link rel="preload" href="' . esc_url(BB_WESTERN_PLUGIN_URL . 'assets/western.css' . '?ver=' . BB_WESTERN_VERSION) . '" as="style">' . "\n";
    }

    public function renderShortcode($atts): string {
        $atts = shortcode_atts([
                'mode'   => 'hub',
                'spread' => '3_cards'
        ], $atts);

        $mode       = $atts['mode'];
        $spread_key = $atts['spread'];

        self::loadCalc();
        $spreads_config  = Western_Calc::getSpreads();
        $current_spread  = $spreads_config[$spread_key] ?? $spreads_config['3_cards'];
        $total_cards     = $current_spread['count'];

        ob_start();
        if ($mode === 'hub') {
            include_once BB_WESTERN_PLUGIN_DIR . 'template/landing.php';
        } else {
            include_once BB_WESTERN_PLUGIN_DIR . 'template/steps.php';
        }
        return ob_get_clean();
    }

    public function handleRestDraw(WP_REST_Request $request): WP_REST_Response {
        $name       = sanitize_text_field($request->get_param('full_name') ?? '');
        $mode       = sanitize_text_field($request->get_param('mode')      ?? 'topic');
        $topic      = sanitize_text_field($request->get_param('topic')     ?? '');
        $question   = sanitize_text_field($request->get_param('question')  ?? '');
        $spread_key = sanitize_text_field($request->get_param('spread')    ?? '3_cards');

        self::loadCalc();
        if (!Western_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $liteCards  = Western_Calc::drawLite($spread_key);
        $fullCards  = Western_Calc::hydrate($liteCards);
        
        self::loadRender();
        $renderHTML = western_render($name, $mode === 'topic' ? $topic : '', $fullCards, $mode, $question, $spread_key);

        return new WP_REST_Response([
            'success' => true,
            'html'    => $renderHTML,
            'cards'   => $liteCards,
        ], 200);
    }

    private function western_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'western_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);

        if ($count >= BB_WESTERN_RATE_LIMIT) return false;

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

    public function handleRestAnalyze(WP_REST_Request $request): WP_REST_Response {
        if (!$this->validate_logged_in()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng tính năng này.'], 200);
        }

        $allow_ai = get_option('western_allow_ai', '0');
        if ($allow_ai !== '1') {
            return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Chức năng hiện đang tạm ngưng. Vui lòng quay lại sau.'
            ], 200);
        }

        if (!$this->western_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Đã đạt giới hạn phân tích trong ngày. Vui lòng quay lại vào ngày mai.'], 200);
        }

        $name       = mb_substr(sanitize_text_field($request->get_param('full_name')  ?? ''), 0, 60);
        $mode       = sanitize_text_field($request->get_param('mode')                 ?? 'topic');
        $topic      = sanitize_text_field($request->get_param('topic')                ?? '');
        $question   = mb_substr(sanitize_textarea_field($request->get_param('question') ?? ''), 0, 500);
        $spread_key = sanitize_text_field($request->get_param('spread')               ?? '3_cards');
        
        self::loadCalc();
        if (!Western_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $hp_trap = $request->get_param('hp_trap') ?? '';
        if (!empty($hp_trap)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Dữ liệu bài không hợp lệ.'], 403);
        }

        if (empty($name)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Vui lòng nhập họ và tên.'], 200);
        }
        if ($mode === 'question' && empty($question)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Vui lòng nhập câu hỏi trước khi giải mã.'], 200);
        }

        $cardsLiteRaw = $request->get_param('cards');
        $liteCards    = is_string($cardsLiteRaw) ? json_decode(wp_unslash($cardsLiteRaw), true) : $cardsLiteRaw;

        if (!is_array($liteCards) || empty($liteCards) || count($liteCards) > 10) {
            return new WP_REST_Response(['success' => false, 'message' => 'Dữ liệu bài không hợp lệ.'], 200);
        }

        foreach ($liteCards as $pos => $c) {
            if (!is_array($c) || empty($c['key'])) {
                return new WP_REST_Response(['success' => false, 'message' => 'Cấu trúc lá bài bị sai lệch.'], 200);
            }
        }

        $fullCards = Western_Calc::hydrate($liteCards);

        self::loadAIProviders();

        $providers = [
                'gemini'  => fn($p) => BBW_Gemini::get_instance()->ftn_gemini_generate($p),
                'groq'    => fn($p) => BBW_Groq::get_instance()->ftn_groq_generate($p),
                'mistral' => fn($p) => BBW_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        if ($mode === 'question') {
            require_once BB_WESTERN_PLUGIN_DIR . 'includes/gatekeeper.php';
            $gatekeeper_prompt = western_build_gatekeeper_prompt($question, $mode);
            $gk_response = '';

            $gatekeeper_order_str = get_option('western_gatekeeper_order', 'groq,mistral,gemini');
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

            if (strpos($gk_response, 'KHÔNG') !== false || strpos($gk_response, 'KH') !== false || empty($gk_response)) {
                $html_fallback = '<br><span class="ast-reload" onclick="window.location.reload()">Hãy đặt lại câu hỏi</span> cụ thể, chi tiết và đúng trọng tâm bói toán hơn nhé!';

                return new WP_REST_Response([
                    'success'   => true,
                    'is_cached' => false,
                    'html'      => $html_fallback
                ], 200);
            }
        }

        $cache_str = mb_strtolower(trim($name)) . $mode . $spread_key . ($mode === 'question' ? md5($question) : $topic);
        foreach ($liteCards as $pos => $c) {
            $cache_str .= $c['key'];
        }
        $cache_key = md5($cache_str);

        $cached = $this->getCache($cache_key);
        if ($cached !== null) {
            $parsed = Western_Calc::parseResponse($cached);
            $parsed['success'] = true;
            $parsed['is_cached'] = true;
            return new WP_REST_Response($parsed, 200);
        }

        self::loadPrompt();

        $prompt = ($mode === 'question')
                ? western_build_prompt_question($name, $question, $fullCards, $spread_key)
                : western_build_prompt_topic($name, $topic, $fullCards, $spread_key);

        $analysis_order_str = get_option('western_analysis_order', get_option('western_ai_provider', 'gemini'));
        $analysis_order = array_map('trim', explode(',', $analysis_order_str));

        $response = '';
        foreach ($analysis_order as $current_provider) {
            if (!isset($providers[$current_provider])) continue;
            try {
                $res = $providers[$current_provider]($prompt);
                if (!empty($res) && !str_starts_with($res, '[Error]') &&
                    str_contains($res, '[AST_RESULT]') && str_contains($res, '[/AST_RESULT]')) {
                    $response = $res;
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        if (empty($response)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Lỗi kết nối. Vui lòng thử lại sau.'], 200);
        }

        if (preg_match('/\[AST_RESULT\]([\s\S]*?)\[\/AST_RESULT\]/', $response, $matches)) {
            $response = '[AST_RESULT]' . trim($matches[1]) . '[/AST_RESULT]';
        }

        $this->saveCache($cache_key, $response);
        $parsed = Western_Calc::parseResponse($response);
        $parsed['success'] = true;
        $parsed['is_cached'] = false;
        return new WP_REST_Response($parsed, 200);
    }

    private function ensureCacheDir(): void {
        if ($this->cache_dir_ready) return;
        $upload_dir = wp_upload_dir();
        $dir = $upload_dir['basedir'] . '/western';
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
            chmod($dir, 0755);
            file_put_contents($dir . '/.htaccess', "Deny from all\n");
            file_put_contents($dir . '/index.php', "<?php // silence\n");
        }
        $this->cache_dir_ready = true;
    }

    private function getCacheFilePath(string $key): string {
        $this->ensureCacheDir();
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/western/' . $key . '.json';
    }

    private function getCache(string $key): ?string {
        $file = $this->getCacheFilePath($key);
        if (file_exists($file)) {
            if (time() - filemtime($file) > 365 * DAY_IN_SECONDS) {
                @unlink($file);
                return null;
            }
            $data = json_decode(file_get_contents($file), true);
            return $data['response'] ?? null;
        }
        return null;
    }

    private function saveCache(string $key, string $response): void {
        file_put_contents($this->getCacheFilePath($key), json_encode(['response' => $response, 'created' => time()]));
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && has_shortcode($post->post_content, 'western_form')) {
            return '';
        }
        return $title;
    }
}
BB_Western_Handle::get_instance();