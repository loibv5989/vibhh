<?php

if (!defined('ABSPATH')) exit;

class TR_Handle {
    private static ?TR_Handle $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('tarot_form', [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
    }

    private static function loadAIProviders(): void {
        require_once TAROT_PLUGIN_DIR . 'includes/gemini.php';
        require_once TAROT_PLUGIN_DIR . 'includes/groq.php';
        require_once TAROT_PLUGIN_DIR . 'includes/mistral.php';
        require_once TAROT_PLUGIN_DIR . 'includes/prompt.php';
    }

    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'tarot_form')) return;
        wp_enqueue_style('tarot', TAROT_PLUGIN_URL . 'assets/tarot.css', [], TAROT_VERSION);
        wp_enqueue_script('tarot', TAROT_PLUGIN_URL . 'assets/tarot.min.js', ['jquery'], TAROT_VERSION, true);
        wp_localize_script('tarot', 'TarotAjax', [
                'api_url' => esc_url_raw(rest_url('tarot/v1/')),
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'tarot_form')) return;
        echo '<link rel="preload" href="' . esc_url(TAROT_PLUGIN_URL . 'assets/tarot.css' . '?ver=' . TAROT_VERSION) . '" as="style">' . "\n";
    }

    private function renderSpread(string $spread_key, string $topic, array $cards, string $mode, string $question): string {
        $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
        $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
        $template = $spread_config['template'] ?? 'render';
        
        $template_file = TAROT_PLUGIN_DIR . 'template/spreads/' . $template . '.php';
        
        if (!file_exists($template_file)) {
            $template_file = TAROT_PLUGIN_DIR . 'template/spreads/default.php';
            $template = 'default';
        }
        
        require_once $template_file;
        
        $func_name = 'tarot_' . str_replace('-', '_', $template);
        if (function_exists($func_name)) {
            return $func_name($topic, $cards, $mode, $question, $spread_key);
        }
        
        return tarot_default($topic, $cards, $mode, $question, $spread_key);
    }

    public function registerRestRoutes(): void {
        register_rest_route('tarot/v1', '/draw', [
                'methods'             => 'POST',
                'callback'            => [$this, 'handleRestDraw'],
                'permission_callback' => '__return_true',
        ]);

        register_rest_route('tarot/v1', '/reveal', [
                'methods'             => 'POST',
                'callback'            => [$this, 'handleRestReveal'],
                'permission_callback' => '__return_true',
        ]);

        register_rest_route('tarot/v1', '/analyze', [
                'methods'             => 'POST',
                'callback'            => [$this, 'handleRestAnalyze'],
                'permission_callback' => '__return_true',
        ]);
    }

    public function renderShortcode($atts): string {
        $atts = shortcode_atts([
                'mode'   => 'hub',      // 'hub', 'topic', 'question', 'love'
                'spread' => '3_cards'   // '3_cards', '5_cards', '7_cards', '10_cards', 'love_3_cards'...
        ], $atts);

        $mode = $atts['mode'];
        $spread_key = $atts['spread'];

        $spreads_config = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
        $current_spread = $spreads_config[$spread_key] ?? $spreads_config['3_cards'];
        $total_cards = $current_spread['count'];

        ob_start(); ?>

        <div class="trt-wrap" id="trt-wrap">
            <div id="trt-app-config" data-mode="<?= esc_attr($mode) ?>" data-spread="<?= esc_attr($spread_key) ?>" data-count="<?= esc_attr($total_cards) ?>" style="display:none;"></div>
            <script>
                window.TAROT_SPREADS = <?= json_encode($spreads_config, JSON_UNESCAPED_UNICODE) ?>;
            </script>

            <?php if ($mode === 'hub'): ?>
                <?php include TAROT_PLUGIN_DIR . 'template/landing.php'; ?>
            <?php endif; ?>

            <?php include TAROT_PLUGIN_DIR . 'template/pages/topic-selection.php'; ?>
            <?php include TAROT_PLUGIN_DIR . 'template/pages/question-input.php'; ?>
            <?php include TAROT_PLUGIN_DIR . 'template/pages/love-input.php'; ?>
            <?php include TAROT_PLUGIN_DIR . 'template/pages/shuffle-step.php'; ?>
            <?php include TAROT_PLUGIN_DIR . 'template/pages/deck-selection.php'; ?>
        </div>
        <?php return ob_get_clean();
    }

    public function handleRestDraw(WP_REST_Request $request): WP_REST_Response {
        $drawResult = Tarot_Calc::drawShuffled();

        return new WP_REST_Response([
                'success'       => true,
                'shuffled_deck' => $drawResult['shuffled_deck'],
        ], 200);
    }

    public function handleRestReveal(WP_REST_Request $request): WP_REST_Response {
        $mode       = sanitize_text_field($request->get_param('mode') ?? 'topic');
        $topic      = sanitize_text_field($request->get_param('topic') ?? '');
        $question   = sanitize_text_field($request->get_param('question') ?? '');
        $spread_key = sanitize_text_field($request->get_param('spread') ?? '3_cards');
        if (!Tarot_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $pickedRaw = $request->get_param('picked');
        $picked    = is_string($pickedRaw) ? json_decode(wp_unslash($pickedRaw), true) : $pickedRaw;

        if (!is_array($picked) || empty($picked)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card selection.'], 200);
        }

        $spreads   = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
        $spread    = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions = array_keys($spread['positions']);

        $liteCards = [];
        foreach ($picked as $i => $card) {
            $pos_key = $positions[$i] ?? 'extra_' . $i;
            $liteCards[$pos_key] = [
                'key'         => $card['key']         ?? '',
                'orientation' => $card['orientation']  ?? 'upright',
                'name'        => $card['name']         ?? '',
            ];
        }

        $fullCards  = Tarot_Calc::hydrate($liteCards);
        $renderHTML = $this->renderSpread($spread_key, $mode === 'topic' ? $topic : '', $fullCards, $mode, $question);

        $html_content = is_array($renderHTML) ? ($renderHTML['html'] ?? '') : $renderHTML;
        $hints = [];
        foreach ($fullCards as $pos_key => $card) {
            $hints[$pos_key] = $card['hint'] ?? 'A hidden message awaits';
        }

        return new WP_REST_Response([
                'success' => true,
                'html'    => $html_content,
                'cards'   => $liteCards,
                'hints'   => $hints,
        ], 200);
    }

    private function tarot_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'tarot_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);

        if ($count >= TAROT_RATE_LIMIT) return false;

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
            return new WP_REST_Response(
                    [
                            'success' => false,
                            'message' => 'Please log in to use this feature.'
                    ], 200);
        }

        $allow_ai = get_option('tarot_allow_ai', '0');

        if ($allow_ai !== '1') {
            return new WP_REST_Response([
                    'success' => false,
                    'message' => 'This feature is temporarily unavailable. Please check back later.'
            ], 200);
        }

        if (!$this->tarot_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => 'You have reached the daily reading limit. Please come back tomorrow.'], 200);
        }

        $name       = mb_substr(sanitize_text_field($request->get_param('full_name') ?? ''), 0, 60);
        $mode       = sanitize_text_field($request->get_param('mode') ?? 'topic');
        $topic      = sanitize_text_field($request->get_param('topic') ?? '');
        $question   = mb_substr(sanitize_textarea_field($request->get_param('question') ?? ''), 0, 500);
        $spread_key = sanitize_text_field($request->get_param('spread') ?? '3_cards');
        if (!Tarot_Calc::isValidSpread($spread_key)) $spread_key = '3_cards';

        $hp_trap = $request->get_param('hp_trap') ?? '';

        if (!empty($hp_trap)) {
            return new WP_REST_Response(['success' => false, 'message' => 'The universe refuses to connect with you.'], 403);
        }

        if (empty($name)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please enter your full name.'], 200);
        }

        if (($mode === 'question' || $mode === 'love') && empty($question)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card data. Please try again.'], 200);
        }

        $cardsLiteRaw = $request->get_param('cards');
        $liteCards = is_string($cardsLiteRaw) ? json_decode(wp_unslash($cardsLiteRaw), true) : $cardsLiteRaw;

        if (!is_array($liteCards) || empty($liteCards) || count($liteCards) > 10) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid card data.'], 200);
        }

        foreach ($liteCards as $pos => $c) {
            if (!is_array($c) || empty($c['key']) || !isset($c['orientation'])) {
                return new WP_REST_Response(['success' => false, 'message' => 'Card structure is invalid.'], 200);
            }
        }

        $fullCards = Tarot_Calc::hydrate($liteCards);

        self::loadAIProviders();

        $static_hints = [];
        foreach ($fullCards as $pos_key => $card) {
            $static_hints[$pos_key] = $card['hint'] ?? 'A hidden message awaits';
        }

        $providers = [
                'gemini'  => fn($p) => TR_Gemini::get_instance()->ftn_gemini_generate($p),
                'groq'    => fn($p) => TR_Groq::get_instance()->ftn_groq_generate($p),
                'mistral' => fn($p) => TR_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        if ($mode === 'question' || $mode === 'love') {
            $gatekeeper_prompt = Tarot_Prompt::tarot_gatekeeper($question, $mode);
            $gk_response = '';

            $gatekeeper_order_str = get_option('tarot_gatekeeper_order', 'groq,mistral,gemini');
            $gatekeeper_order = explode(',', $gatekeeper_order_str);
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
                if ($mode === 'love') {
                    $html_fallback = '<br><br>Please try again. <span class="trt-reload" onclick="window.location.reload()">Ask a question</span> that is more clearly about your relationship or feelings.';
                } else {
                    $html_fallback = '<br><br>Please try again. <span class="trt-reload" onclick="window.location.reload()">Ask a question</span> that is more specific about your situation.';
                }

                return new WP_REST_Response([
                        'success' => true,
                        'html' => $html_fallback,
                        'hints' => $static_hints,
                ], 200);
            }
        }

        $prompt = (($mode === 'question' || $mode === 'love'))
                ? Tarot_Prompt::tarot_question($name, $question, $fullCards, $spread_key, $topic)
                : Tarot_Prompt::tarot_topic($name, $topic, $fullCards, $spread_key);

        try {
            $rawResponse = '';
            $is_valid = false;
            $analysis_order_str = get_option('tarot_analysis_order', 'gemini,mistral,groq');
            $analysis_order = explode(',', $analysis_order_str);

            foreach ($analysis_order as $current_provider) {
                if (!isset($providers[$current_provider])) continue;
                try {
                    $rawResponse = $providers[$current_provider]($prompt);
                    if (!empty($rawResponse) && !str_starts_with($rawResponse, '[Error]') &&
                            str_contains($rawResponse, '[AST_RESULT]') && str_contains($rawResponse, '[/AST_RESULT]')) {
                        $is_valid = true;
                        break;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }


            if (!$is_valid) {
                return new WP_REST_Response(['success' => false, 'message' => 'The system is currently overloaded. Please try again in a moment.'], 200);
            }

            if (preg_match('/\[AST_RESULT\]([\s\S]*?)\[\/AST_RESULT\]/', $rawResponse, $matches)) {
                $rawResponse = '[AST_RESULT]' . trim($matches[1]) . '[/AST_RESULT]';
            }

            $parsed = Tarot_Calc::parseResponse($rawResponse);

            return new WP_REST_Response([
                    'success'   => true,
                    'html'      => $parsed['html'],
                    'hints'     => $static_hints
            ], 200);

        } catch (Exception $e) {
            return new WP_REST_Response(['success' => false, 'message' => 'Connection error. Please try again.'], 200);
        }
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && has_shortcode($post->post_content, 'tarot_form')) {
            return '';
        }
        return $title;
    }
}
TR_Handle::get_instance();