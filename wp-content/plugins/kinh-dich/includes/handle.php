<?php
if (!defined('ABSPATH')) exit;

class IChing_Handle {
    private static ?IChing_Handle $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('iching_landing', [$this, 'renderLanding']);
        add_shortcode('iching_maihoa', [$this, 'renderMaiHoa']);
        add_shortcode('iching_luchao', [$this, 'renderLucHao']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);

        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
        add_filter('body_class', [$this, 'addBodyClass']);
        add_action('wp_footer', [$this, 'addHeroSvg'], 99);
    }

    public function addHeroSvg () {
        global $post;
        if ($post && (
                has_shortcode($post->post_content, 'iching_landing') ||
                has_shortcode($post->post_content, 'iching_maihoa') ||
                has_shortcode($post->post_content, 'iching_luchao') ||
                has_shortcode($post->post_content, 'maihoa_object')
            )) {
            echo iching_render_hero_svg();
        }
    }

    private static function loadAIProviders(): void {
        if (!class_exists('IChing_Gemini')) {
            require_once ICHING_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('IChing_Groq')) {
            require_once ICHING_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('IChing_Mistral')) {
            require_once ICHING_PLUGIN_DIR . 'includes/mistral.php';
        }
    }

    private static function loadPromptLucHao(): void {
        if (!function_exists('iching_gatekeeper')) {
            require_once ICHING_PLUGIN_DIR . 'prompt/luchao.php';
        }
    }

    private static function loadPromptMaiHoa(): void {
        if (!function_exists('iching_build_prompt_maihoa')) {
            require_once ICHING_PLUGIN_DIR . 'prompt/maihoa.php';
        }
    }

    private static function loadForm(): void {
        if (!function_exists('iching_render_form')) {
            require_once ICHING_PLUGIN_DIR . 'template/form.php';
        }
    }

    private static function loadResult(): void {
        if (!function_exists('iching_render_result')) {
            require_once ICHING_PLUGIN_DIR . 'template/result.php';
        }
    }

    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;

        $has_shortcode = has_shortcode($post->post_content, 'iching_landing')
            || has_shortcode($post->post_content, 'iching_maihoa')
            || has_shortcode($post->post_content, 'iching_luchao')
            || has_shortcode($post->post_content, 'maihoa_object');

        if (!$has_shortcode) return;

        wp_enqueue_style('ftn-iching', ICHING_PLUGIN_URL . 'assets/iching.min.css', [], ICHING_PLUGIN_VERSION);
        wp_enqueue_script('ftn-iching', ICHING_PLUGIN_URL . 'assets/iching.min.js', ['jquery'], ICHING_PLUGIN_VERSION, true);

        wp_localize_script('ftn-iching', 'ichingData', [
            'api_url' => esc_url_raw(rest_url('iching/v1/'))
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        $has_shortcode = has_shortcode($post->post_content, 'iching_landing')
            || has_shortcode($post->post_content, 'iching_maihoa')
            || has_shortcode($post->post_content, 'iching_luchao')
            || has_shortcode($post->post_content, 'maihoa_object');
        if (!$has_shortcode) return;
        echo '<link rel="preload" href="' . esc_url(ICHING_PLUGIN_URL . 'assets/iching.min.css' . '?ver=' . ICHING_PLUGIN_VERSION) . '" as="style">' . "\n";
    }

    public function renderLanding($atts): string {
        ob_start();
        require_once ICHING_PLUGIN_DIR . 'template/landing.php';
        return ob_get_clean();
    }

    public function renderMaiHoa($atts): string {
        self::loadForm();
        $method = (is_array($atts) && !empty($atts['method'])) ? $atts : 'maihoa';
        return iching_render_form($method);
    }

    public function renderLucHao($atts): string {
        self::loadForm();
        return iching_render_form('luchao');
    }

    public function registerRestRoutes(): void {
        register_rest_route('iching/v1', '/draw', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestDraw'],
            'permission_callback' => '__return_true',
            'args'                => [
                'question' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_textarea_field'
                ],
                'mode' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'default'           => 'luchao'
                ],
                'time' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'number' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'obj1' => [
                    'required'          => false,
                    'sanitize_callback' => 'absint'
                ],
                'obj2' => [
                    'required'          => false,
                    'sanitize_callback' => 'absint'
                ],
                'hp_trap' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);

        register_rest_route('iching/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestAnalyze'],
            'permission_callback' => '__return_true',
            'args'                => [
                'name' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'gender' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'mode' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'default'           => 'luchao'
                ],
                'topic' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'default'           => 'general'
                ],
                'question' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_textarea_field'
                ],
                'lite' => [
                    'required'          => true,
                    'validate_callback' => function($param) {
                        return is_array($param) && Iching_Calc::validateLite($param);
                    }
                ]
            ]
        ]);
    }

    public function handleRestDraw(WP_REST_Request $request): WP_REST_Response {
        $hp_trap = $request->get_param('hp_trap');
        if (!empty($hp_trap)) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Yêu cầu không hợp lệ.'
            ], 400);
        }

        $question = $request->get_param('question');
        if (empty($question)) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Vui lòng nhập câu hỏi.'
            ], 200);
        }

        if (mb_strlen($question, 'UTF-8') > 500) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Câu hỏi quá dài, hãy mô tả trọng tâm vào vấn đề chính của bạn.'
            ], 200);
        }

        $mode = $request->get_param('mode') ?? 'luchao';
        $maihoa_time = $request->get_param('time');

        if ($mode === 'maihoa_time' && !empty($maihoa_time)) {
            $wp_tz = wp_timezone();
            $parsed_time = DateTime::createFromFormat('d/m/Y H:i', $maihoa_time, $wp_tz);
            $time_errors = DateTime::getLastErrors();

            if (!$parsed_time || !empty($time_errors['warning_count']) || !empty($time_errors['error_count'])) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Thời gian không đúng.'
                ], 200);
            }

            if ($parsed_time > new DateTime()) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Thời gian không đúng.'
                ], 200);
            }

            $limit_date = new DateTime('-24 hours');

            if ($parsed_time < $limit_date) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Đã qua 1 ngày: ý niệm ban đầu có thể đã biến, quẻ không còn phản ánh đúng động tâm lúc đầu. Chỉ nên lập quẻ khi tâm còn giữ nguyên, chưa bị tác động hay thay đổi quyết định.'
                ], 200);
            }

            $maihoa_time = $parsed_time->format('Y-m-d H:i:s');
        }

        $args = [
            'time'   => $maihoa_time,
            'number' => $request->get_param('number'),
            'obj1'   => $request->get_param('obj1'),
            'obj2'   => $request->get_param('obj2'),
        ];

        if ($mode === 'maihoa_number') {
            $number = preg_replace('/[^0-9]/', '', (string) ($args['number'] ?? ''));
            if (strlen($number) > 50) {
                return new WP_REST_Response([
                    'success' => true,
                    'message' => 'Dãy số quá dài. Vui lòng nhập tối đa 50 chữ số.'
                ], 200);
            }
        }

        $liteData = Iching_Calc::drawLite($mode, $args);

        $fullData = Iching_Calc::hydrate($liteData);
        $fullData['toss_time'] = $liteData['toss_time'];

        self::loadResult();
        $html_content = iching_render_result($question, $fullData, $mode);

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'lite' => $liteData,
                'html' => $html_content
            ],
            'is_logged_in' => is_user_logged_in(),
        ], 200);
    }

    private function iching_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'iching_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);

        if ($count >= ICHING_RATE_LIMIT) return false;

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

        $allow_ai = get_option('iching_allow_ai', '0');
        if ($allow_ai !== '1') {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Chức năng đang tạm ngưng. Vui lòng quay lại sau.'
            ], 200);
        }

        if (!$this->iching_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Đã đạt giới hạn phân tích trong ngày. Vui lòng quay lại vào ngày mai.'], 200);
        }

        $name = $request->get_param('name') ?: 'Bạn';
        $gender = $request->get_param('gender') ?: '';
        $mode = $request->get_param('mode') ?? 'luchao';
        $topic = $request->get_param('topic') ?? 'general';
        $question = $request->get_param('question');

        if (empty($question)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Vui lòng nhập câu hỏi.'
            ], 200);
        }

        if (mb_strlen($question, 'UTF-8') > 500) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Câu hỏi quá dài.'
            ], 200);
        }

        $lite = $request->get_param('lite');

        if (!$lite || !is_array($lite) || !Iching_Calc::validateLite($lite)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.'
            ], 200);
        }

        $fullData = Iching_Calc::hydrate($lite);
        $fullData['toss_time'] = sanitize_text_field($lite['toss_time'] ?? current_time('mysql'));

        self::loadAIProviders();
        self::loadPromptLucHao();
        self::loadPromptMaiHoa();

        $providers = [
            'gemini'  => fn($p) => IChing_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => IChing_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => IChing_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $gatekeeper_prompt = iching_gatekeeper($question, $gender);
        $gk_response = '';

        $gatekeeper_order_str = get_option('iching_gatekeeper_order', 'groq,mistral,gemini');
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

        if (strpos($gk_response, 'KHONG') !== false || empty($gk_response)) {
            $html_fallback = '<br><br>Vui lòng thực hiện lại hành động. <span class="ich-reload" onclick="window.location.reload()">Gieo quẻ</span> và đặt câu hỏi rõ ràng hơn về vấn đề của bạn nhé!';
            return new WP_REST_Response([
                'success' => true,
                'data' => [ 'html' => $html_fallback ]
            ], 200);
        }

        $prompt = '';

        if ($mode === 'luchao') {
            $api_dung_than = '';
            $valid_dts = ['QUAN QUỶ' => 'Quan Quỷ', 'THÊ TÀI' => 'Thê Tài', 'PHỤ MẪU' => 'Phụ Mẫu', 'TỬ TÔN' => 'Tử Tôn', 'HUYNH ĐỆ' => 'Huynh Đệ'];
            foreach ($valid_dts as $key => $val) {
                if (strpos($gk_response, $key) !== false) {
                    $api_dung_than = $val;
                    break;
                }
            }
            $prompt = iching_build_prompt($name, $gender, $topic, $question, $fullData, $mode, $api_dung_than);
        } else {
            $smart_topic_label = function_exists('_iching_get_smart_topic')
                ? _iching_get_smart_topic($question, $topic, $gender)
                : $topic;

            $prompt = iching_build_prompt_maihoa($smart_topic_label, $question, $fullData, $mode);
        }

        $rawResponse = '';
        $is_valid    = false;

        $analysis_order_str = get_option('iching_analysis_order', 'gemini,mistral,groq');
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
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Hệ thống đang quá tải. Vui lòng thử lại sau giây lát.'
            ], 200);
        }

        try {
            $parsed = self::parseResponse($rawResponse);
            $parsed['html'] = wp_kses_post($parsed['html']);

            return new WP_REST_Response([
                'success' => true,
                'data' => $parsed
            ], 200);
        } catch (Exception $e) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Lỗi xử lý dữ liệu luận giải. Vui lòng thử lại sau.'
            ], 200);
        }
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && (
                has_shortcode($post->post_content, 'iching_landing') ||
                has_shortcode($post->post_content, 'iching_maihoa') ||
                has_shortcode($post->post_content, 'iching_luchao') ||
                has_shortcode($post->post_content, 'maihoa_object')
            )) {
            return '';
        }
        return $title;
    }

    public function addBodyClass(array $classes): array {
        global $post;
        if (!is_a($post, 'WP_Post')) return $classes;

        if (
            has_shortcode($post->post_content, 'iching_maihoa') ||
            has_shortcode($post->post_content, 'iching_luchao') ||
            has_shortcode($post->post_content, 'maihoa_object')
        ) {
            $classes[] = 'ich-tool-page';
        }

        return $classes;
    }

    public static function parseResponse(string $raw): array {
        $hints = [];
        $html  = '';

        if (preg_match('/\[AST_RESULT\](.*?)\[\/AST_RESULT\]/s', $raw, $m)) {
            $html = self::markdownToHtml(trim($m[1]));
        }

        return ['hints' => $hints, 'html' => $html];
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

        require_once ICHING_PLUGIN_DIR . 'lib/Parsedown.php';

        $Parsedown = new Parsedown();
        return $Parsedown->text($md);
    }
}

IChing_Handle::get_instance();