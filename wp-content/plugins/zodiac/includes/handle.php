<?php

if (!defined('ABSPATH')) exit;

class BbZodiac_Handle {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private static array $shortcodes = [
        'zodiac_form',
        'zodiac_tinh_cach',
        'zodiac_tinh_yeu',
        'zodiac_tu_vi',
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
        if (!class_exists('BbZodiac_Gemini')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('BbZodiac_Groq')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('BbZodiac_Mistral')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/mistral.php';
        }
    }

    private static function loadPrompt(): void {
        if (!class_exists('BbZodiac_Prompt')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/prompt.php';
        }
    }

    private static function loadRender(): void {
        if (!class_exists('BbZodiac_Render')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'template/render.php';
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

        register_rest_route('zdc/v1', '/natal', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleNatal'],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route('zdc/v1', '/natal-analyze', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleNatalAnalyze'],
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

        wp_enqueue_style('bb-zodiac',   BB_ZODIAC_PLUGIN_URL . 'assets/zodiac.css',  [], BB_ZODIAC_VERSION);
        wp_enqueue_script('bb-zodiac', BB_ZODIAC_PLUGIN_URL . 'assets/zodiac.js', ['jquery'], BB_ZODIAC_VERSION, true);
        wp_localize_script('bb-zodiac', 'ZodiacAjax', [
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
        echo '<link rel="preload" href="' . esc_url(BB_ZODIAC_PLUGIN_URL . 'assets/zodiac.css' . '?ver=' . BB_ZODIAC_VERSION) . '" as="style">' . "\n";
    }

    public function renderShortcode(array $atts, string $content = '', string $tag = 'zodiac_form'): string {
        $template_map = [
            'zodiac_form'         => 'landing.php',
            'zodiac_tinh_cach'    => 'personality.php',
            'zodiac_tinh_yeu'     => 'love.php',
            'zodiac_tu_vi'        => 'horoscope.php',
        ];
        $tpl = BB_ZODIAC_PLUGIN_DIR . 'template/' . ($template_map[$tag] ?? 'landing.php');
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

        $dobDisplay = BbZodiac_Calc::normalizeDob($dob);

        try {
            $signData = BbZodiac_Calc::calculate($dobDisplay);
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

        $staticHtml = BbZodiac_Render::buildStaticAnalyze($signData);

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html'      => BbZodiac_Render::indexes($signData),
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

        $nameA = BbZodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_a')));
        $nameB = BbZodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_b')));
        $dobA  = sanitize_text_field((string)$request->get_param('dob_a'));
        $dobB  = sanitize_text_field((string)$request->get_param('dob_b'));

        if ($nameA === '' || $nameB === '' || $dobA === '' || $dobB === '') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Please enter full name and date of birth for both people.']], 200);
        }

        $dobDisplayA = BbZodiac_Calc::normalizeDob($dobA);
        $dobDisplayB = BbZodiac_Calc::normalizeDob($dobB);

        try {
            $loveData = BbZodiac_Calc::calculateLoveProfile($nameA, $dobDisplayA, $nameB, $dobDisplayB);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => $e->getMessage()]], 200);
        }

        self::loadRender();

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html' => BbZodiac_Render::loveResult($loveData),
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
        if ($count >= BB_ZODIAC_RATE_LIMIT) return false;
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

        if (get_option('bb_zodiac_allow_ai', '0') !== '1') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'This feature is temporarily unavailable. Please check back later.']], 200);
        }

        if (!$this->zodiac_quota()) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Daily analysis limit reached. Please come back tomorrow.']], 200);
        }

        $nameA = BbZodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_a')));
        $dobA  = BbZodiac_Calc::normalizeDob(sanitize_text_field((string)$request->get_param('dob_a')));
        $nameB = BbZodiac_Calc::normalizePersonName(sanitize_text_field((string)$request->get_param('name_b')));
        $dobB  = BbZodiac_Calc::normalizeDob(sanitize_text_field((string)$request->get_param('dob_b')));

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
            $calcData['name1'] = BbZodiac_Calc::normalizePersonName((string)$calcData['name1']);
        }
        if (isset($calcData['name2'])) {
            $calcData['name2'] = BbZodiac_Calc::normalizePersonName((string)$calcData['name2']);
        }

        self::loadPrompt();
        self::loadAIProviders();

        $prompt = BbZodiac_Prompt::buildLove($calcData);
        $provider = get_option('bb_zodiac_ai_provider', 'gemini');

        $providers = [
            'gemini'  => fn($p) => BbZodiac_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => BbZodiac_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => BbZodiac_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $rawResponse = ($providers[$provider] ?? $providers['gemini'])($prompt);
        if (str_starts_with($rawResponse, '[Error]')) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Connection failed, please try again.']], 200);
        }

        $parsed = BbZodiac_Calc::parseLoveResponse($rawResponse);
        $isValidData = !empty($parsed['has_zdc_html']) && !empty(trim((string)($parsed['tabs']['phan_tich'] ?? '')));

        self::loadRender();

        $payload = [
            'tabs_html' => BbZodiac_Render::loveTabs($parsed['tabs']),
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
        $target_dir = $upload_dir['basedir'] . '/bb-zodiac/tinh-yeu';
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
            $signData = BbZodiac_Calc::getHoroscopeProfile($signId, $period);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => $e->getMessage()]], 200);
        }

        self::loadRender();

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html'   => BbZodiac_Render::tuViIndexes($signData, $period, $avoidDomain),
                'sign'   => $signId,
                'period' => $period
            ],
        ], 200);
    }

    public function handleNatal(WP_REST_Request $request): WP_REST_Response {
        $contactLine = sanitize_text_field((string)$request->get_param('zdc_cbsp'));
        if ($contactLine !== '') return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid request.']], 200);

        $dob = sanitize_text_field((string)$request->get_param('dob'));
        $tob = sanitize_text_field((string)$request->get_param('tob'));
        $pob = sanitize_text_field((string)$request->get_param('pob'));

        if (empty($dob) || empty($pob)) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Please enter Date and Place of birth.']], 200);
        }

        $dobDisplay = BbZodiac_Calc::normalizeDob($dob);

        try {
            require_once BB_ZODIAC_PLUGIN_DIR . 'includes/sweph.php';
            $natalChart = BbZodiac_Sweph::calculateNatalChart($dobDisplay, $tob, $pob);
        } catch (Exception $e) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Astronomy error: ' . $e->getMessage()]], 200);
        }

        self::loadRender();

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'html' => BbZodiac_Render::natalResult(['dob' => $dobDisplay, 'tob' => $tob, 'pob' => $pob], $natalChart),
                'natal_chart' => $natalChart,
                'dob'  => $dobDisplay,
                'tob'  => $tob,
                'pob'  => $pob
            ]
        ], 200);
    }

    public function handleNatalAnalyze(WP_REST_Request $request): WP_REST_Response {
        $contactLine = sanitize_text_field((string)$request->get_param('zdc_cbsp'));
        if ($contactLine !== '') return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Invalid request.']], 200);

        if (!$this->validate_logged_in()) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Please log in to use this feature.']], 200);
        }

        if (get_option('bb_zodiac_allow_ai', '0') !== '1') {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Feature under maintenance.']], 200);
        }

        if (!$this->zodiac_quota()) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Daily analysis limit reached. Please come back tomorrow.']], 200);
        }

        $dob = sanitize_text_field((string)$request->get_param('dob'));
        $tob = sanitize_text_field((string)$request->get_param('tob'));
        $pob = sanitize_text_field((string)$request->get_param('pob'));

        $natalChart = $request->get_param('natal_chart') ?? [];
        $cacheHash = md5($dob . $tob . mb_strtolower($pob, 'UTF-8'));
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/bb-zodiac/natal';

        if (!file_exists($target_dir)) wp_mkdir_p($target_dir);

        $cacheFile = trailingslashit($target_dir) . "{$cacheHash}.json";

        if (file_exists($cacheFile)) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if (!empty($cached)) return new WP_REST_Response(['success' => true, 'data' => array_merge($cached, ['is_cached' => true])], 200);
        }

        self::loadPrompt();
        self::loadAIProviders();

        $prompt = BbZodiac_Prompt::buildNatal($dob, $tob, $pob, $natalChart);
        $provider = get_option('bb_zodiac_ai_provider', 'gemini');
        $providers = [
            'gemini'  => fn($p) => BbZodiac_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => BbZodiac_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => BbZodiac_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $rawResponse = ($providers[$provider] ?? $providers['gemini'])($prompt);
        if (str_starts_with($rawResponse, '[Error]')) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'System is busy, please try again in a few minutes.']], 200);
        }

        $parsed = BbZodiac_Calc::parseResponse($rawResponse);
        if (!$parsed['has_zdc_html'] || empty(trim($parsed['tabs']['chi_tiet']))) {
            return new WP_REST_Response(['success' => false, 'data' => ['message' => 'Decoding failed, please try again.']], 200);
        }

        $payload = ['zdc_html' => $parsed['tabs']['chi_tiet']];
        if ($this->isCacheAllowed($provider)) {
            file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        return new WP_REST_Response(['success' => true, 'data' => array_merge($payload, ['is_cached' => false])], 200);
    }
}

BbZodiac_Handle::get_instance();