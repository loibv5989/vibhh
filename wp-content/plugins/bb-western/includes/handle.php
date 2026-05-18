<?php
if (!defined('ABSPATH')) exit;

class BB_Western_Handle {
    private static ?BB_Western_Handle $instance = null;

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
        if (!function_exists('western_build_prompt_question')) {
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
        register_rest_route('western/v1', '/reveal', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestReveal'],
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
                'spread' => '3_cards'
        ], $atts);

        $spread_key = $atts['spread'];

        self::loadCalc();
        $spreads_config  = Western_Calc::getSpreads();

        ob_start();
        include_once BB_WESTERN_PLUGIN_DIR . 'template/landing.php';
        return ob_get_clean();
    }

    public function handleRestDraw(WP_REST_Request $request): WP_REST_Response {
        $topic      = sanitize_text_field($request->get_param('topic')     ?? '');
        $spread_key = sanitize_text_field($request->get_param('spread')    ?? '3_cards');

        self::loadCalc();
        if (!Western_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $drawResult = Western_Calc::drawShuffled($spread_key);
        $liteCards  = $drawResult['cards'];
        $fullCards  = Western_Calc::hydrate($liteCards, $topic);

        self::loadRender();
        $renderHTML = western_render($topic, $fullCards, $spread_key);

        return new WP_REST_Response([
            'success'       => true,
            'html'          => $renderHTML,
            'cards'         => $liteCards,
            'shuffled_deck' => $drawResult['shuffled_deck'],
        ], 200);
    }

    public function handleRestReveal(WP_REST_Request $request): WP_REST_Response {
        $topic      = sanitize_text_field($request->get_param('topic')     ?? '');
        $spread_key = sanitize_text_field($request->get_param('spread')    ?? '3_cards');

        $pickedRaw = $request->get_param('picked');
        $picked    = is_string($pickedRaw) ? json_decode(wp_unslash($pickedRaw), true) : $pickedRaw;

        if (!is_array($picked) || empty($picked)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card selection.'], 200);
        }

        self::loadCalc();
        if (!Western_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $spreads    = Western_Calc::getSpreads();
        $spread     = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions  = array_keys($spread['positions']);

        $liteCards = [];
        foreach ($picked as $i => $card) {
            $pos_key = $positions[$i] ?? 'extra_' . $i;
            $liteCards[$pos_key] = [
                'key'  => $card['key']  ?? '',
                'name' => $card['name'] ?? '',
                'suit' => $card['suit'] ?? '',
            ];
        }

        $fullCards = Western_Calc::hydrate($liteCards, $topic);

        self::loadRender();
        $renderHTML = western_render($topic, $fullCards, $spread_key);

        return new WP_REST_Response([
            'success' => true,
            'html'    => $renderHTML,
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
            return new WP_REST_Response(['success' => false, 'message' => 'Please log in to use this feature.'], 200);
        }

        $allow_ai = get_option('western_allow_ai', '0');
        if ($allow_ai !== '1') {
            return new WP_REST_Response([
                    'success' => false,
                    'message' => 'This feature is currently unavailable. Please try again later.'
            ], 200);
        }

        if (!$this->western_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Daily analysis limit reached. Please come back tomorrow.'], 200);
        }

        $topic      = sanitize_text_field($request->get_param('topic')                ?? '');
        $question   = mb_substr(sanitize_textarea_field($request->get_param('question') ?? ''), 0, 500);
        $spread_key = sanitize_text_field($request->get_param('spread')               ?? '3_cards');
        
        self::loadCalc();
        if (!Western_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $hp_trap = $request->get_param('hp_trap') ?? '';
        if (!empty($hp_trap)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card data.'], 403);
        }

        if (empty($question)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please enter a question before reading.'], 200);
        }

        $cardsLiteRaw = $request->get_param('cards');
        $liteCards    = is_string($cardsLiteRaw) ? json_decode(wp_unslash($cardsLiteRaw), true) : $cardsLiteRaw;

        if (!is_array($liteCards) || empty($liteCards) || count($liteCards) > 10) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card data.'], 200);
        }

        foreach ($liteCards as $pos => $c) {
            if (!is_array($c) || empty($c['key'])) {
                return new WP_REST_Response(['success' => false, 'message' => 'Card data structure is invalid.'], 200);
            }
        }

        $fullCards = Western_Calc::hydrate($liteCards, $topic);

        self::loadAIProviders();

        $providers = [
                'gemini'  => fn($p) => BBW_Gemini::get_instance()->ftn_gemini_generate($p),
                'groq'    => fn($p) => BBW_Groq::get_instance()->ftn_groq_generate($p),
                'mistral' => fn($p) => BBW_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        require_once BB_WESTERN_PLUGIN_DIR . 'includes/gatekeeper.php';
        $gatekeeper_prompt = western_build_gatekeeper_prompt($question);
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

        if (strpos($gk_response, 'NO') !== false || strpos($gk_response, 'No') !== false || empty($gk_response)) {
            $html_fallback = '<br><span class="ast-reload" onclick="window.location.reload()">Please rephrase your question</span> to be more specific, detailed, and focused on the reading.';

            return new WP_REST_Response([
                'success'   => true,
                'html'      => $html_fallback
            ], 200);
        }

        self::loadPrompt();

        $prompt = western_build_prompt_question($question, $fullCards, $spread_key, $topic);

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
            return new WP_REST_Response(['success' => false, 'message' => 'Connection error. Please try again later.'], 200);
        }

        if (preg_match('/\[AST_RESULT\]([\s\S]*?)\[\/AST_RESULT\]/', $response, $matches)) {
            $response = '[AST_RESULT]' . trim($matches[1]) . '[/AST_RESULT]';
        }

        $parsed = Western_Calc::parseResponse($response);
        $parsed['success'] = true;
        return new WP_REST_Response($parsed, 200);
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