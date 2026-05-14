<?php

if (!defined('ABSPATH')) exit;

class Zodiac_Handle {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private static array $shortcodes = [
        'zodiac_form',
        'zodiac_personality',
        'zodiac_love',
        'zodiac_horoscope',
    ];

    private const ALLOWED_CACHE_PROVIDERS = ['gemini'];

    private function __construct() {
        foreach (self::$shortcodes as $tag) {
            add_shortcode($tag, [$this, 'renderShortcode']);
        }
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_action('rest_api_init', [$this, 'registerRoutes']);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
    }

    private static function loadAIProviders(): void {
        if (!class_exists('Zodiac_Gemini')) {
            require_once ZODIAC_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('Zodiac_Groq')) {
            require_once ZODIAC_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('Zodiac_Mistral')) {
            require_once ZODIAC_PLUGIN_DIR . 'includes/mistral.php';
        }
    }

    private static function loadPrompt(): void {
        if (!class_exists('Zodiac_Prompt')) {
            require_once ZODIAC_PLUGIN_DIR . 'includes/prompt.php';
        }
    }

    private static function loadRender(): void {
        if (!class_exists('Zodiac_Render')) {
            require_once ZODIAC_PLUGIN_DIR . 'template/render.php';
        }
    }

    public function registerRoutes(): void {
        register_rest_route('zdc/v1', '/calc', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handleCalc'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('zdc/v1', '/love', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handleLove'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('zdc/v1', '/love-analyze', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handleLoveAnalyze'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('zdc/v1', '/tuvi', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleTuVi'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function enqueueAssets(): void {
        if (!is_singular()) return;
        global $post;
        if (empty($post->post_content)) return;
        $has = false;
        foreach (self::$shortcodes as $tag) {
            if (has_shortcode($post->post_content, $tag)) { $has = true; break; }
        }
        if (!$has) return;

        wp_enqueue_style('zodiac',   ZODIAC_PLUGIN_URL . 'assets/zodiac.css',  [], ZODIAC_VERSION);
        wp_enqueue_script('zodiac', ZODIAC_PLUGIN_URL . 'assets/zodiac.js', ['jquery'], ZODIAC_VERSION, true);
        wp_localize_script('zodiac', 'ZodiacAjax', [
            'api_url'  => rest_url('zdc/v1/'),
        ]);
    }

    public function preloadAssets(): void {
        if (!is_singular()) return;
        global $post;
        if (empty($post->post_content)) return;
        $has = false;
        foreach (self::$shortcodes as $tag) {
            if (has_shortcode($post->post_content, $tag)) { $has = true; break; }
        }
        if (!$has) return;
        echo '<link rel="preload" href="' . esc_url(ZODIAC_PLUGIN_URL . 'assets/zodiac.css' . '?ver=' . ZODIAC_VERSION) . '" as="style">' . "\n";
    }

    public function renderShortcode(array $atts, string $content = '', string $tag = 'zodiac_form'): string {
        $template_map = [
            'zodiac_form'         => 'landing.php',
            'zodiac_personality'    => 'personality.php',
            'zodiac_love'     => 'love.php',
            'zodiac_horoscope'        => 'horoscope.php',
        ];
        $tpl = ZODIAC_PLUGIN_DIR . 'template/' . ($template_map[$tag] ?? 'landing.php');
        ob_start();
        include $tpl;
        return ob_get_clean();
    }

    public function handleCalc(WP_REST_Request $request): WP_REST_Response {
        $contactLine = sanitize_text_field((string)$request->get_param('zdc_cbsp'));
        if ($contactLine !== '') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid request.']], 200);
        }

        $dob = sanitize_text_field((string)$request->get_param('dob'));
        if (empty($dob)) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Please enter your date of birth.']], 200);
        }

        $dobDisplay = Zodiac_Calc::normalizeDob($dob);

        try {
            $signData = Zodiac_Calc::calculate($dobDisplay);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => $e->getMessage()]], 200);
        }

        $easter_eggs = $signData['easter_eggs'] ?? [];
        $hasFuture = array_filter($easter_eggs, fn($e) => $e['type'] === 'future');
        if (!empty($hasFuture)) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => "This age hasn't been born yet. Wait until they're born."]], 200);
        }

        $signData['dob'] = $dobDisplay;

        self::loadRender();

        $staticHtml = Zodiac_Render::buildStaticAnalyze($signData);

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html'      => Zodiac_Render::indexes($signData),
                'zdc_html'  => $staticHtml,
                'sign_data' => $signData,
                'dob'       => $dobDisplay,
            ],
        ], 200);
    }

    public function handleLove(WP_REST_Request $request): WP_REST_Response {
        $contactLine = sanitize_text_field((string)$request->get_param('zdc_cbsp'));
        if ($contactLine !== '') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid request.']], 200);
        }

        $nameA = Zodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_a')));
        $nameB = Zodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_b')));
        $dobA  = sanitize_text_field((string)$request->get_param('dob_a'));
        $dobB  = sanitize_text_field((string)$request->get_param('dob_b'));

        if ($nameA === '' || $nameB === '' || $dobA === '' || $dobB === '') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Please enter full name and date of birth for both people.']], 200);
        }

        $dobDisplayA = Zodiac_Calc::normalizeDob($dobA);
        $dobDisplayB = Zodiac_Calc::normalizeDob($dobB);

        try {
            $loveData = Zodiac_Calc::calculateLoveProfile($nameA, $dobDisplayA, $nameB, $dobDisplayB);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => $e->getMessage()]], 200);
        }

        self::loadRender();

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html' => Zodiac_Render::loveResult($loveData),
                'calc_data' => $loveData,
                'is_complete' => true,
            ],
        ], 200);
    }

    private function zodiac_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'zodiac_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);
        if ($count >= ZODIAC_RATE_LIMIT) return false;
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

    public function handleLoveAnalyze(WP_REST_Request $request): WP_REST_Response {
        $contactLine = sanitize_text_field((string)$request->get_param('zdc_cbsp'));
        if ($contactLine !== '') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid request.']], 200);
        }

        if (!$this->validate_logged_in()) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Please log in to use this feature.']], 200);
        }

        if (get_option('zodiac_allow_ai', '0') !== '1') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'This feature is temporarily unavailable. Please check back later.']], 200);
        }

        if (!$this->zodiac_quota()) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Daily analysis limit reached. Please come back tomorrow.']], 200);
        }

        $nameA = Zodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_a')));
        $dobA  = Zodiac_Calc::normalizeDob(sanitize_text_field((string)$request->get_param('dob_a')));
        $nameB = Zodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_b')));
        $dobB  = Zodiac_Calc::normalizeDob(sanitize_text_field((string)$request->get_param('dob_b')));

        if ($nameA !== '' && $dobA !== '' && $nameB !== '' && $dobB !== '') {
            $cacheFile = $this->getLoveCacheFilePath($nameA, $dobA, $nameB, $dobB);
            if (file_exists($cacheFile)) {
                if (time() - filemtime($cacheFile) > DAY_IN_SECONDS) {
                    @unlink($cacheFile);
                } else {
                    $cached = json_decode(file_get_contents($cacheFile), true);
                    if (!empty($cached)) {
                        return new WP_REST_Response([
                            'success' => true,
                            'data' => array_merge($cached, ['is_cached' => true]),
                        ], 200);
                    }
                }
            }
        }

        $calcData = $request->get_param('calc_data') ?? [];
        if (empty($calcData) || !is_array($calcData)) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid data.']], 200);
        }

        if (!empty($calcData['blocks'])) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid data.']], 200);
        }

        if (isset($calcData['name1'])) {
            $calcData['name1'] = Zodiac_Calc::normalizePersonName((string)$calcData['name1']);
        }
        if (isset($calcData['name2'])) {
            $calcData['name2'] = Zodiac_Calc::normalizePersonName((string)$calcData['name2']);
        }

        self::loadPrompt();
        self::loadAIProviders();

        $prompt = Zodiac_Prompt::buildLove($calcData);
        $provider = get_option('zodiac_ai_provider', 'gemini');

        $providers = [
            'gemini'  => fn($p) => Zodiac_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => Zodiac_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => Zodiac_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $rawResponse = ($providers[$provider] ?? $providers['gemini'])($prompt);
        if (str_starts_with($rawResponse, '[Error]')) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Connection failed, please try again.']], 200);
        }

        $parsed = Zodiac_Calc::parseLoveResponse($rawResponse);
        $isValidData = !empty($parsed['has_zdc_html']) && !empty(trim((string)($parsed['tabs']['phan_tich'] ?? '')));

        self::loadRender();

        $payload = [
            'tabs_html' => Zodiac_Render::loveTabs($parsed['tabs']),
        ];

        if (!empty($isValidData) && isset($cacheFile) && $this->isCacheAllowed($provider)) {
            file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => array_merge($payload, ['is_cached' => false]),
        ], 200);
    }

    private function isCacheAllowed(string $provider): bool {
        return in_array($provider, self::ALLOWED_CACHE_PROVIDERS, true);
    }

    private function getLoveCacheFilePath(string $nameA, string $dobA, string $nameB, string $dobB): string {
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/zodiac/tinh-yeu';
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
            file_put_contents($target_dir . '/.htaccess', "Deny from all\n");
            file_put_contents($target_dir . '/index.php', "<?php // silence\n");
        }

        $p1 = mb_strtolower(trim($nameA), 'UTF-8') . '|' . $dobA;
        $p2 = mb_strtolower(trim($nameB), 'UTF-8') . '|' . $dobB;

        $combo = $p1 . '||' . $p2;

        return trailingslashit($target_dir) . md5($combo) . '.json';
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if (!$post) return $title;
        foreach (self::$shortcodes as $tag) {
            if (has_shortcode($post->post_content, $tag)) return '';
        }
        return $title;
    }

    public function handleTuVi(WP_REST_Request $request): WP_REST_Response {
        $contactLine = sanitize_text_field((string)$request->get_param('zdc_cbsp'));
        if ($contactLine !== '') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid request.']], 200);
        }

        $signId = sanitize_text_field((string)$request->get_param('sign'));
        $period = sanitize_text_field((string)$request->get_param('period'));
        $avoidDomain = sanitize_text_field((string)$request->get_param('avoid_domain'));

        try {
            $signData = Zodiac_Calc::getHoroscopeProfile($signId, $period);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => $e->getMessage()]], 200);
        }

        self::loadRender();

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html'   => Zodiac_Render::tuViIndexes($signData, $period, $avoidDomain),
                'sign'   => $signId,
                'period' => $period
            ],
        ], 200);
    }
}

Zodiac_Handle::get_instance();