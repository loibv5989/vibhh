<?php
if (!defined('ABSPATH')) exit;

class MBTI_Handler {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('mbti_form', [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts',   [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_action('rest_api_init',         [$this, 'registerRestRoutes']);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
    }

    private static function loadData(): void {
        require_once MBTI_PLUGIN_DIR . 'includes/data.php';
    }

    private static function loadCalc(): void {
        self::loadData();
        require_once MBTI_PLUGIN_DIR . 'includes/calc.php';
    }

    private static function loadRender(): void {
        require_once MBTI_PLUGIN_DIR . 'templates/render.php';
    }

    private static function loadPrompt(): void {
        require_once MBTI_PLUGIN_DIR . 'includes/prompt.php';
    }

    private static function loadAIProviders(): void {
        require_once MBTI_PLUGIN_DIR . 'includes/gemini.php';
        require_once MBTI_PLUGIN_DIR . 'includes/groq.php';
        require_once MBTI_PLUGIN_DIR . 'includes/mistral.php';
    }

    private static function loadNumerologyCalc(): void {
        if (!class_exists('ThanSoHoc_Calc')) {
            $tsh_dir = WP_PLUGIN_DIR . '/than-so-hoc/';
            require_once $tsh_dir . 'includes/calc.php';
        }
    }

    private static function loadZodiacCalc(): void {
        if (!class_exists('BbZodiac_Calc')) {
            $zdc_dir = WP_PLUGIN_DIR . '/bb-zodiac/';
            require_once $zdc_dir . 'includes/calc.php';
        }
    }

    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        if (!has_shortcode($post->post_content, 'mbti_form')) return;

        wp_enqueue_style('bb-mbti',  MBTI_PLUGIN_URL . 'assets/mbti.css',  [], MBTI_VERSION);
        wp_enqueue_script('bb-mbti', MBTI_PLUGIN_URL . 'assets/mbti.min.js', ['jquery'], MBTI_VERSION, true);
        wp_localize_script('bb-mbti', 'MbtiRest', [
            'rest_url' => rest_url('mbti/v1'),
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        if (!has_shortcode($post->post_content, 'mbti_form')) return;
        echo '<link rel="preload" href="' . esc_url(MBTI_PLUGIN_URL . 'assets/mbti.css' . '?ver=' . MBTI_VERSION) . '" as="style">' . "\n";
    }

    public function registerRestRoutes(): void {
        register_rest_route('mbti/v1', '/calculate', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestCalc'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('mbti/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestAnalyze'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function renderShortcode(): string {
        self::loadData();

        $questions = MBTI_Data::getQuestions();
        $chunks    = array_chunk($questions, 8);

        ob_start();
        include MBTI_PLUGIN_DIR . 'templates/landing.php';
        return ob_get_clean();
    }

    private function mbti_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'mbti_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);

        if ($count >= MBTI_RATE_LIMIT) return false;

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

    private function validate_logged_in() {
        return (bool) self::get_cookie_user();
    }

    public function handleRestCalc(WP_REST_Request $request): WP_REST_Response {
        self::loadCalc();
        self::loadRender();

        $raw_answers = $request->get_param('answers');
        if (!is_array($raw_answers)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid data.'], 200);
        }

        $answers = [];
        foreach ($raw_answers as $key => $val) {
            $k = sanitize_key($key);
            $v = (int) $val;
            if ($k && $v >= 1 && $v <= 5) {
                $answers[$k] = $v;
            }
        }

        $data = MBTI_Calc::calculate($answers);

        return new WP_REST_Response([
            'success' => true,
            'html'    => MBTI_Render::resultStatic($data),
        ], 200);
    }

    public function handleRestAnalyze(WP_REST_Request $request): WP_REST_Response {
        if (!$this->validate_logged_in()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please log in to use this feature.'], 200);
        }

        if (get_option('mbti_allow_ai', '0') !== '1') {
            return new WP_REST_Response(['success' => false, 'message' => 'This feature is currently disabled.'], 200);
        }

        if (!$this->mbti_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => "You've reached today's analysis limit. Please try again tomorrow."], 200);
        }

        $name = sanitize_text_field($request->get_param('full_name') ?? '');
        $dob  = sanitize_text_field($request->get_param('dob') ?? '');

        if (empty($name) || empty($dob)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please fill in all required fields.'], 200);
        }

        // Validate date of birth is not in the future
        $dobNormalized = self::normalizeDob($dob);
        $dobDate = DateTime::createFromFormat('d/m/Y', $dobNormalized);
        $today = new DateTime('today');
        if ($dobDate && $dobDate > $today) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please enter a valid date of birth.'], 200);
        }

        $raw_answers = $request->get_param('answers');
        if (!is_array($raw_answers)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid data.'], 200);
        }

        $answers = [];
        foreach ($raw_answers as $key => $val) {
            $k = sanitize_key($key);
            $v = (int) $val;
            if ($k && $v >= 1 && $v <= 5) {
                $answers[$k] = $v;
            }
        }

        self::loadCalc();

        $dobDisplay = self::normalizeDob($dob);
        $data       = MBTI_Calc::calculate($answers);
        $mbtiType   = $data['type'];

        $cacheFile = $this->getCacheFilePath($name, $dobDisplay, $mbtiType);

        if (file_exists($cacheFile)) {
            if (time() - filemtime($cacheFile) > 365 * DAY_IN_SECONDS) {
                @unlink($cacheFile);
            } else {
                $cached = json_decode(file_get_contents($cacheFile), true);
                if (!empty($cached)) {
                    return new WP_REST_Response(array_merge($cached, ['success' => true, 'is_cached' => true]), 200);
                }
            }
        }

        self::loadNumerologyCalc();
        self::loadZodiacCalc();
        self::loadPrompt();
        self::loadAIProviders();

        $tshData = [];
        $zodiacData = [];

        if (class_exists('ThanSoHoc_Calc')) {
            $tshData = ThanSoHoc_Calc::calculate($name, $dobDisplay);
        }
        if (class_exists('BbZodiac_Calc')) {
            $zodiacData = BbZodiac_Calc::calculate($dobDisplay);
        }

        $prompt = MBTI_Prompt::build($name, $dobDisplay, $data, $tshData, $zodiacData);

        $analysis_order_str = get_option('mbti_analysis_order', 'gemini,mistral,groq');
        $analysis_order = array_map('trim', explode(',', $analysis_order_str));

        $providers = [
            'gemini'  => fn($p) => MBTI_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => MBTI_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => MBTI_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $rawResponse = '';
        $successful_provider = '';

        foreach ($analysis_order as $current_provider) {
            if (!isset($providers[$current_provider])) continue;
            try {
                $res = $providers[$current_provider]($prompt);
                if (!empty($res) && !str_starts_with($res, '[Error]')) {
                    $rawResponse = $res;
                    $successful_provider = $current_provider;
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        if (empty($rawResponse)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Could not connect to AI. Please try again.'], 200);
        }

        $parsed = MBTI_Calc::parseResponse($rawResponse);

        self::loadRender();
        $payload = [
            'html' => MBTI_Render::resultAI($parsed['tabs']),
        ];

        if (!empty(trim($parsed['tabs']['mbti_result'] ?? '')) && $successful_provider === 'gemini') {
            file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE), LOCK_EX);
        }

        return new WP_REST_Response(array_merge($payload, ['success' => true, 'is_cached' => false]), 200);
    }

    private function getCacheFilePath(string $name, string $dobDisplay, string $mbtiType): string {
        static $target_dir = null;

        if ($target_dir === null) {
            $upload_dir = wp_upload_dir();
            $target_dir = $upload_dir['basedir'] . '/mbti';

            if (!file_exists($target_dir)) {
                wp_mkdir_p($target_dir);
                file_put_contents($target_dir . '/.htaccess', "Deny from all\n");
                file_put_contents($target_dir . '/index.php',  '<?php // Silence is golden');
            }
        }

        $hash = md5(mb_strtolower(trim($name)) . '|' . $dobDisplay . '|' . $mbtiType);
        return trailingslashit($target_dir) . $hash . '.json';
    }

    private static function normalizeDob(string $dob): string {
        $dob = trim($dob);

        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if ($d && $d->format('Y-m-d') === $dob) return $d->format('d/m/Y');

        $normalized = preg_replace('/[\-\.\s]+/', '/', $dob);
        foreach (['d/m/Y', 'j/n/Y'] as $fmt) {
            $d = DateTime::createFromFormat($fmt, $normalized);
            if ($d) return $d->format('d/m/Y');
        }

        return $dob;
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && has_shortcode($post->post_content, 'mbti_form')) {
            return '';
        }
        return $title;
    }
}

MBTI_Handler::get_instance();
