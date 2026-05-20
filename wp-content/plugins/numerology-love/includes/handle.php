<?php
if (!defined('ABSPATH')) exit;

class TshLove_Handle {
    private static ?TshLove_Handle $instance = null;

    private function __construct() {
        add_shortcode('tsh_love_form', [$this, 'renderShortcode']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
    }

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private static function loadData(): void {
        if (!class_exists('TshLove_Data')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/data.php';
        }
    }

    private static function loadCalc(): void {
        if (!class_exists('TshLove_Calc')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/calc.php';
        }
    }

    private static function loadRender(): void {
        if (!class_exists('TshLove_Render')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'template/render.php';
        }
    }

    private static function loadPrompt(): void {
        if (!class_exists('TshLove_Prompt')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/prompt.php';
        }
    }

    private static function loadAIProviders(): void {
        if (!class_exists('TshLove_Helpers')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/helpers.php';
        }
        if (!class_exists('TshLove_Gemini')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('TshLove_Groq')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('TshLove_Mistral')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'includes/mistral.php';
        }
    }


    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        if (!has_shortcode($post->post_content, 'tsh_love_form')) return;

        wp_enqueue_style('tsh-love', TSH_LOVE_PLUGIN_URL . 'assets/tsh-love.css', [], TSH_LOVE_VERSION);
        wp_enqueue_script('tsh-love', TSH_LOVE_PLUGIN_URL . 'assets/tsh-love.min.js', ['jquery'], TSH_LOVE_VERSION, true);
        wp_localize_script('tsh-love', 'ThsLove', [
                'rest_url' => rest_url('tsh-love/v1')
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        if (!has_shortcode($post->post_content, 'tsh_love_form')) return;
        echo '<link rel="preload" href="' . esc_url(TSH_LOVE_PLUGIN_URL . 'assets/tsh-love.css' . '?ver=' . TSH_LOVE_VERSION) . '" as="style">' . "\n";
    }

    public function registerRestRoutes(): void {
        register_rest_route('tsh-love/v1', '/calculate', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestCalc'],
            'permission_callback' => '__return_true',
            'args'                => [
                'name1' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'dob1' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'name2' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'dob2' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);

        register_rest_route('tsh-love/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestAnalyze'],
            'permission_callback' => '__return_true',
            'args'                => [
                'name1' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'dob1' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'name2' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'dob2' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);
    }

    private static function get_cookie_user() {
        $cookie = $_COOKIE[LOGGED_IN_COOKIE] ?? '';
        $parsed = false;

        if ($cookie) {
            $parsed = wp_parse_auth_cookie($cookie, 'logged_in');
        }
        return $parsed ?: false;
    }

    private function tshlove_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $today = wp_date('Y-m-d');

        $key = 'tshlove_quota_ai_' . $user['username'] . '_' . $today;

        $count = (int) get_transient($key);
        if ($count >= TSH_LOVE_RATE_LIMIT) return false;

        $ttl = strtotime('tomorrow', current_time('timestamp')) - current_time('timestamp');

        set_transient($key, $count + 1, $ttl);
        return true;
    }

    public function validate_logged_in() {
        return (bool) self::get_cookie_user();
    }

    public function handleRestCalc(WP_REST_Request $request): WP_REST_Response {
        self::loadCalc();
        self::loadRender();
        self::loadData();

        $n1 = $request->get_param('name1');
        $dob1 = $request->get_param('dob1');
        $n2 = $request->get_param('name2');
        $dob2 = $request->get_param('dob2');

        if (!self::isValidName($n1) || !self::isValidName($n2)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Name can only contain letters and spaces.'], 200);
        }

        $dob1_norm = self::normalizeDob($dob1);
        $dob2_norm = self::normalizeDob($dob2);

        if (substr_count($dob1_norm, '/') !== 2 || substr_count($dob2_norm, '/') !== 2) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid date of birth format.'], 200);
        }

        try {
            $data = TshLove_Calc::calculate($n1, $dob1_norm, $n2, $dob2_norm);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'message' => $e->getMessage()], 200);
        }

        return new WP_REST_Response([
            'success'       => true,
            'html'          => TshLove_Render::indexes($data),
            'calc_data'     => $data,
            'is_complete'   => true,
        ], 200);
    }

    private function getCacheFilePath(string $n1, string $dob1, string $n2, string $dob2): string {
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/ths-love';
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
            file_put_contents($target_dir . '/.htaccess', "Deny from all\n");
            file_put_contents($target_dir . '/index.php', '<?php // Silence is golden');
        }

        $p1 = mb_strtolower(trim($n1)) . '|' . $dob1;
        $p2 = mb_strtolower(trim($n2)) . '|' . $dob2;

        $combo = $p1 . '||' . $p2;

        $hash = md5($combo);
        return trailingslashit($target_dir) . $hash . '.json';
    }

    public function renderShortcode(array $atts): string {
        ob_start();
        require_once TSH_LOVE_PLUGIN_DIR . 'template/form.php';
        return ob_get_clean();
    }

    public function handleRestAnalyze(WP_REST_Request $request): WP_REST_Response {
        if (!$this->validate_logged_in()) {
            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Please log in to use this feature.'
                ], 200);
        }

        if (get_option('bty_tsh_allow_ai', '0') !== '1') {
            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'This feature is currently disabled.'
                ], 200);
        }

        if (!$this->tshlove_quota()) {
            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Daily analysis limit reached. Please come back tomorrow.'
                ], 200);
        }

        $n1 = $request->get_param('name1');
        $dob1 = self::normalizeDob($request->get_param('dob1'));
        $n2 = $request->get_param('name2');
        $dob2 = self::normalizeDob($request->get_param('dob2'));

        if (!self::isValidName($n1) || !self::isValidName($n2)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Name can only contain letters and spaces.'], 200);
        }

        if (substr_count($dob1, '/') !== 2 || substr_count($dob2, '/') !== 2) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid date of birth format.'], 200);
        }

        if (!empty($n1) && !empty($dob1) && !empty($n2) && !empty($dob2)) {
            $cacheFile = $this->getCacheFilePath($n1, $dob1, $n2, $dob2);
            if (file_exists($cacheFile)) {
                if (time() - filemtime($cacheFile) > 1 * DAY_IN_SECONDS) {
                    @unlink($cacheFile);
                } else {
                    $cached = json_decode(file_get_contents($cacheFile), true);
                    if (!empty($cached)) {
                        return new WP_REST_Response(array_merge($cached, ['success' => true, 'is_cached' => true]), 200);
                    }
                }
            }
        }

        self::loadPrompt();
        self::loadAIProviders();
        self::loadRender();
        self::loadCalc();
        self::loadData();

        $data = TshLove_Calc::calculate($n1, $dob1, $n2, $dob2);

        if (empty($data)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid data.'], 200);
        }

        if (!empty($data['blocks']) && is_array($data['blocks'])) {
            foreach ($data['blocks'] as $block) {
                $type = $block['type'] ?? '';
                $name = $block['name'] ?? '';
                if ($type === 'future') {
                    $msg = $name ? "Come back when {$name} is born." : "Come back when they're born.";
                    return new WP_REST_Response(['success' => false, 'message' => $msg], 200);
                } elseif ($type === 'infant') {
                    $msg = $name ? "Baby {$name} is still in diapers. Let them grow up naturally first!" : "Still in diapers? Love compatibility can wait!";
                    return new WP_REST_Response(['success' => false, 'message' => $msg], 200);
                } elseif ($type === 'under14') {
                    $msg = $name ? "Focus on school first, love later, {$name}." : "Focus on school first, love later!";
                    return new WP_REST_Response(['success' => false, 'message' => $msg], 200);
                } elseif ($type === 'over90') {
                    $msg = $name ? "{$name}, love knows no age limit." : "Love knows no age limit.";
                    return new WP_REST_Response(['success' => false, 'message' => $msg], 200);
                } elseif ($type === 'same_name') {
                    return new WP_REST_Response(['success' => false, 'message' => 'Both people have the same name.'], 200);
                }
            }
            return new WP_REST_Response(['success' => false, 'message' => 'Invalid data.'], 200);
        }

        $prompt = TshLove_Prompt::build($data);

        $providers = [
            'gemini'  => fn($p) => TshLove_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => TshLove_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => TshLove_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $analysis_order_str = get_option('bty_tsh_analysis_order', 'gemini,mistral,groq');
        $analysis_order = array_map('trim', explode(',', $analysis_order_str));

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
            return new WP_REST_Response(['success' => false, 'message' => 'Connection failed. Please try again.'], 200);
        }

        $parsed = self::parseResponse($rawResponse);

        $is_valid_data = true;

        $required_tabs = ['analysis'];
        foreach ($required_tabs as $tab_key) {
            if (empty(trim($parsed['tabs'][$tab_key] ?? ''))) {
                $is_valid_data = false;
                break;
            }
        }

        $payload = [
            'tabs_html' => TshLove_Render::tabs($parsed['tabs'])
        ];

        if ($is_valid_data && $successful_provider === 'gemini' && isset($cacheFile)) {
            file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        return new WP_REST_Response(array_merge($payload, ['success' => true, 'is_cached' => false]), 200);
    }

    public static function parseResponse(string $raw): array {
        $tabs = ['analysis' => ''];
        if (preg_match('/\[TAB_RESULT\](.*?)\[\/TAB_RESULT\]/is', $raw, $matches)) {
            $tabs['analysis'] = self::markdownToHtml(trim($matches[1]));
        }
        return ['tabs' => $tabs];
    }

    public static function markdownToHtml(string $md): string {
        if (str_contains($md, '[AST_RESULT]') && str_contains($md, '[/AST_RESULT]')) {
            preg_match('/\[AST_RESULT\]([\s\S]*?)\[\/AST_RESULT\]/', $md, $matches);
            if (!empty($matches[1])) {
                $md = trim($matches[1]);
            }
        }

        $md = preg_replace('/^[\-]{3,}$/m', '', $md);
        $md = preg_replace('/^\*{3,}$/m', '', $md);
        $md = preg_replace('/^_{3,}$/m', '', $md);

        if (!class_exists('Parsedown')) {
            require_once TSH_LOVE_PLUGIN_DIR . 'lib/Parsedown.php';
        }
        $parsedown = new Parsedown();

        return $parsedown->text($md);
    }

    private static function normalizeDob(string $dob): string {
        $dob = trim($dob);

        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if ($d && $d->format('Y-m-d') === $dob) {
            return $d->format('d/m/Y');
        }

        $normalized = preg_replace('/[\-\.\s]+/', '/', $dob);
        $parts = explode('/', $normalized);

        if (count($parts) === 3) {
            $day = (int)$parts[0];
            $month = (int)$parts[1];
            $year = (int)$parts[2];

            if (checkdate($month, $day, $year)) {
                return sprintf('%02d/%02d/%04d', $day, $month, $year);
            }
        }

        return '';
    }

    private static function isValidName(string $name): bool {
        $name = trim($name);
        if (empty($name)) return false;
        return preg_match('/^[\p{L}\s\-]+$/u', $name) === 1;
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && has_shortcode($post->post_content, 'tsh_love_form')) {
            return '';
        }
        return $title;
    }
}

TshLove_Handle::get_instance();
