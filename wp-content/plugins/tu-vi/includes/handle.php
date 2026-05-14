<?php

if (!defined('ABSPATH')) exit;

class TuVi_Handle {
    const TUVI_CACHE = '/tuvi';

    private static ?TuVi_Handle $instance = null;

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_shortcode('tuvi_landing', [$this, 'shortcode_landing']);
        add_shortcode('tuvi_lap_la_so', [$this, 'shortcode_lap_la_so']);
        add_shortcode('tuvi_ngay_tot_xau', [$this, 'shortcode_ntx']);
        add_shortcode('tuvi_hop_tuoi', [$this, 'shortcode_hop_tuoi']);

        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_filter('cron_schedules', [$this, 'addMonthlyCronSchedule']);
        add_action('tuvi_monthly_cache_reset', [$this, 'resetCacheDirectory']);

        if (!wp_next_scheduled('tuvi_monthly_cache_reset')) {
            $next_run = mktime(0, 0, 0, (int)date('n') + 1, 1, (int)date('Y'));
            wp_schedule_event($next_run, 'monthly_first', 'tuvi_monthly_cache_reset');
        }
    }

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private static function normalizeDate(?string $date): ?string {
        if (empty($date)) return $date;
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return $date;
        $normalized = preg_replace('/[-\.\s]+/', '/', $date);
        foreach (['d/m/Y', 'j/n/Y'] as $fmt) {
            $d = DateTime::createFromFormat($fmt, $normalized);
            if ($d) return $d->format('Y-m-d');
        }
        return $date;
    }

    private static function validateDate(?string $param, bool $allowEmpty = false): bool {
        if (empty($param)) return $allowEmpty;

        $year = 1990;

        // Check định dạng YYYY-MM-DD
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $param, $matches)) {
            if (!checkdate((int)$matches[2], (int)$matches[3], (int)$matches[1])) return false;
            $year = (int)$matches[1];
        }
        // Check định dạng DD/MM/YYYY (hoặc dấu -, ., khoảng trắng)
        elseif (preg_match('/^(\d{1,2})[\/\-.\s](\d{1,2})[\/\-.\s](\d{4})$/', $param, $matches)) {
            if (!checkdate((int)$matches[2], (int)$matches[1], (int)$matches[3])) return false;
            $year = (int)$matches[3];
        } else {
            return false;
        }

        if ($year < 1900 || $year > 2100) {
            return false;
        }

        return true;
    }

    private static function loadData(): void {
        if (!class_exists('TuVi_Data')) {
            require_once TUVI_PLUGIN_DIR . 'includes/data.php';
        }
    }

    private static function loadAmLich(): void {
        if (!class_exists('Tuvi_AmLich')) {
            require_once TUVI_PLUGIN_DIR . 'data/amlich.php';
        }
    }

    private static function loadEngine(): void {
        self::loadData();
        self::loadAmLich();
        if (!class_exists('TuVi_Engine')) {
            require_once TUVI_PLUGIN_DIR . 'includes/laso.php';
        }
    }

    private static function loadNTX(): void {
        self::loadData();
        if (!class_exists('TuVi_NTX')) {
            require_once TUVI_PLUGIN_DIR . 'includes/ntx.php';
        }
    }

    private static function loadHopTuoi(): void {
        self::loadData();
        if (!class_exists('TuVi_HopTuoi')) {
            require_once TUVI_PLUGIN_DIR . 'includes/hop-tuoi.php';
        }
    }

    private static function loadRender(): void {
        if (!function_exists('tuvi_render_landing')) {
            require_once TUVI_PLUGIN_DIR . 'template/render.php';
        }
    }

    private static function loadPrompt(): void {
        if (!class_exists('TuVi_Prompt')) {
            require_once TUVI_PLUGIN_DIR . 'includes/prompt.php';
        }
    }

    private static function loadAIProviders(): void {
        if (!class_exists('Tuvi_Gemini')) {
            require_once TUVI_PLUGIN_DIR . 'includes/gemini.php';
        }
        if (!class_exists('Tuvi_Groq')) {
            require_once TUVI_PLUGIN_DIR . 'includes/groq.php';
        }
        if (!class_exists('Tuvi_Mistral')) {
            require_once TUVI_PLUGIN_DIR . 'includes/mistral.php';
        }
    }

    public function addMonthlyCronSchedule(array $schedules): array {
        $schedules['monthly_first'] = [
            'interval' => 30 * DAY_IN_SECONDS,
            'display'  => __('Monthly (approx)', 'tu-vi'),
        ];
        return $schedules;
    }

    public function resetCacheDirectory(): void {
        $upload_dir = wp_upload_dir();
        $cache_dir  = $upload_dir['basedir'] . self::TUVI_CACHE;

        if (!is_dir($cache_dir)) return;

        $files = glob($cache_dir . '/*.json');
        if (empty($files)) return;

        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && @unlink($file)) {
                $deleted++;
            }
        }
    }

    private function getCacheFilePath(array $input): string {
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . self::TUVI_CACHE;
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
            file_put_contents($target_dir . '/.htaccess', "Deny from all\n");
            file_put_contents($target_dir . '/index.php', '<?php // Silence is golden');
        }
        $hash = md5(mb_strtolower(trim($input['ho_ten'])) . '|' . $input['ngay_sinh'] . '|' . $input['gio_sinh'] . '|' . $input['gioi_tinh'] . '|' . $input['nam_xem']);
        return trailingslashit($target_dir) . $hash . '.json';
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);

        if (!$post) {
            return $title;
        }

        $shortcodes = [
            'tuvi_lap_la_so',
            'tuvi_landing',
            'tuvi_ngay_tot_xau',
            'tuvi_hop_tuoi'
        ];

        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return '';
            }
        }

        return $title;
    }
    public function enqueue_assets() {
        if (is_admin()) return;

        global $post;
        if (empty($post) || empty($post->post_content)) {
            return;
        }

        $shortcodes = [ 'tuvi_lap_la_so', 'tuvi_landing', 'tuvi_ngay_tot_xau', 'tuvi_hop_tuoi' ];
        $has_shortcode = false;

        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                $has_shortcode = true;
                break;
            }
        }

        if (!$has_shortcode) {
            return;
        }

        wp_enqueue_style( 'tuvi', TUVI_PLUGIN_URL . 'assets/tuvi.min.css', [], TUVI_PLUGIN_VERSION );
        wp_enqueue_script( 'tuvi', TUVI_PLUGIN_URL . 'assets/tuvi.min.js', ['jquery'], TUVI_PLUGIN_VERSION, true );
        wp_localize_script('tuvi', 'tuvi', [
            'api_url' => esc_url_raw(rest_url('tuvi/v1/')),
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        $shortcodes = ['tuvi_landing', 'tuvi_lap_la_so', 'tuvi_ngay_tot_xau', 'tuvi_hop_tuoi'];
        $has_shortcode = false;
        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                $has_shortcode = true;
                break;
            }
        }
        if (!$has_shortcode) return;
        echo '<link rel="preload" href="' . esc_url(TUVI_PLUGIN_URL . 'assets/tuvi.min.css' . '?ver=' . TUVI_PLUGIN_VERSION) . '" as="style">' . "\n";
    }

    public function shortcode_landing($atts = []) {
        self::loadRender();
        return tuvi_render_landing();
    }

    public function shortcode_lap_la_so($atts = []) {
        self::loadRender();
        return tuvi_render_lap_la_so_form();
    }

    public function shortcode_ntx($atts = []){
        self::loadRender();
        return tuvi_ntx();
    }

    public function shortcode_hop_tuoi($atts = []){
        self::loadRender();
        return tuvi_hop_tuoi();
    }

    public function registerRestRoutes(): void {
        $validateName = function($param) {
            return empty($param) || (strlen($param) <= 50 && preg_match('/^[a-zA-ZÀ-ỹ\s\-]+$/u', $param));
        };

        register_rest_route('tuvi/v1', '/calculate', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestCalc'],
            'permission_callback' => '__return_true',
            'args'                => [
                'ho_ten' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => $validateName
                ],
                'ngay_sinh' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p, true)
                ],
                'gio_sinh' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return empty($param) || preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $param);
                    }
                ],
                'gioi_tinh' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return empty($param) || in_array($param, ['nam', 'nu']);
                    }
                ],
                'nam_xem' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return empty($param) || (preg_match('/^\d{4}$/', $param) && $param >= 1900 && $param <= 2100);
                    }
                ],
            ]
        ]);

        register_rest_route('tuvi/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestAnalyze'],
            'permission_callback' => '__return_true',
            'args'                => [
                'ho_ten' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'ngay_sinh' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p, true)
                ],
                'gio_sinh' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'gioi_tinh' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'nam_xem' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'tuvi_hp_tname' => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'user_question' => ['required' => false, 'sanitize_callback' => 'sanitize_textarea_field'],
                'is_qa_mode'    => ['required' => false, 'sanitize_callback' => 'rest_sanitize_boolean']
            ]
        ]);

        register_rest_route('tuvi/v1', '/ntx', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestNgayTotXau'],
            'permission_callback' => '__return_true',
            'args'                => [
                'mode' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return in_array($param, ['single', 'range']);
                    }
                ],
                'purpose' => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
                'date' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p, true)
                ],
                'start' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p, true)
                ],
                'end' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p, true)
                ],
                'limit' => [
                    'required'          => false,
                    'sanitize_callback' => 'absint'
                ],
                'ngay_sinh' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p)
                ],
                'gio_sinh' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return empty($param) || preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $param);
                    }
                ],
                'gioi_tinh' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return in_array($param, ['nam', 'nu']);
                    }
                ],
            ]
        ]);

        register_rest_route('tuvi/v1', '/hoptuoi', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestHopTuoi'],
            'permission_callback' => '__return_true',
            'args'                => [
                'muc_dich'    => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
                'ten_a'       => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => $validateName
                ],
                'ngay_sinh_a' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p)
                ],
                'gio_sinh_a'  => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return empty($param) || preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $param);
                    }
                ],
                'gioi_tinh_a' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return in_array($param, ['nam', 'nu'], true);
                    }
                ],

                'ten_b'       => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => $validateName
                ],
                'ngay_sinh_b' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => fn($p) => self::validateDate($p)
                ],
                'gio_sinh_b'  => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return empty($param) || preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $param);
                    }
                ],
                'gioi_tinh_b' => [
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return in_array($param, ['nam', 'nu'], true);
                    }
                ],
            ]
        ]);
    }

    public function handleRestNgayTotXau(WP_REST_Request $request): WP_REST_Response {
        self::loadNTX();

        $mode = $request->get_param('mode');
        $purpose = $request->get_param('purpose') ?: 'cuoi';
        $date = self::normalizeDate($request->get_param('date'));

        $start = self::normalizeDate($request->get_param('start'));
        $end = self::normalizeDate($request->get_param('end'));
        $ngay_sinh = self::normalizeDate($request->get_param('ngay_sinh'));
        $gio_sinh = $request->get_param('gio_sinh');
        $gioi_tinh = $request->get_param('gioi_tinh');
        $limit = intval($request->get_param('limit') ?: 10);
        $timezone = TuVi_Settings::get_instance()->getTimezone();

        try {
            $todayObj = new DateTimeImmutable('today', new DateTimeZone($timezone));
        } catch (Exception $e) {
            $todayObj = new DateTimeImmutable('today');
        }
        $today = $todayObj->format('Y-m-d');

        if ($mode === 'range') {
            if (empty($start) || empty($end)) {
                return new WP_REST_Response(
                    ['success' => false,
                        'message' => 'Bạn cần nhập đủ ngày bắt đầu và ngày kết thúc.'
                    ], 200);
            }

            if ($start < $today || $end < $today) {
                return new WP_REST_Response(
                    ['success' => false,
                        'message' => 'Khoảng ngày tra cứu không được chứa ngày nằm trong quá khứ.'
                    ], 200);
            }

            $input = [
                'tu_ngay' => $start, 'den_ngay' => $end, 'muc_dich' => $purpose,
                'timezone' => $timezone, 'limit' => $limit > 0 ? $limit : 10,
                'ngay_sinh' => $ngay_sinh, 'gio_sinh' => $gio_sinh, 'gioi_tinh' => $gioi_tinh
            ];
            $result = TuVi_NTX::tim_ngay_tot($input);
        } else {
            if (empty($date)) {
                return new WP_REST_Response(
                    ['success' => false,
                        'message' => 'Bạn cần chọn một ngày cụ thể.'],
                    200);
            }

            if ($date < $today) {
                return new WP_REST_Response(
                    ['success' => false,
                        'message' => 'Ngày cần xem không được nằm trong quá khứ.'],
                    200);
            }

            $input = [
                'ngay' => $date, 'muc_dich' => $purpose, 'timezone' => $timezone,
                'ngay_sinh' => $ngay_sinh, 'gio_sinh' => $gio_sinh, 'gioi_tinh' => $gioi_tinh
            ];
            $result = TuVi_NTX::xem_ngay($input);
        }

        if (isset($result['error'])) {
            return new WP_REST_Response(['success' => false, 'message' => $result['error']], 200);
        }

        $html = $this->renderNtxHtml($result, $mode);
        return new WP_REST_Response([
            'success' => true,
            'html'    => $html,
            'mode'    => $mode
        ], 200);
    }

    public function handleRestHopTuoi(WP_REST_Request $request): WP_REST_Response {
        $input = [
            'muc_dich'    => $request->get_param('muc_dich') ?: 'hon_nhan',

            'ten_a'       => $request->get_param('ten_a'),
            'ngay_sinh_a' => self::normalizeDate($request->get_param('ngay_sinh_a')),
            'gio_sinh_a'  => $request->get_param('gio_sinh_a') ?: '12:00',
            'gioi_tinh_a' => $request->get_param('gioi_tinh_a'),

            'ten_b'       => $request->get_param('ten_b'),
            'ngay_sinh_b' => self::normalizeDate($request->get_param('ngay_sinh_b')),
            'gio_sinh_b'  => $request->get_param('gio_sinh_b') ?: '12:00',
            'gioi_tinh_b' => $request->get_param('gioi_tinh_b'),
        ];

        self::loadHopTuoi();

        $result = TuVi_HopTuoi::evaluate($input);

        if (isset($result['success']) && $result['success'] === false) {
            return new WP_REST_Response(['success' => false, 'message' => $result['message'] ?? 'Có lỗi xảy ra.'], 200);
        }

        $is_ajax = true;
        ob_start();
        include TUVI_PLUGIN_DIR . 'template/hop-tuoi/ht-page.php';
        $html = ob_get_clean();

        return new WP_REST_Response([
            'success' => true,
            'html'    => $html
        ], 200);
    }

    private function renderNtxHtml(array $result, string $mode): string {
        $is_ajax = true;
        ob_start();
        include TUVI_PLUGIN_DIR . 'template/ntx/ntx-page.php';
        return ob_get_clean();
    }

    public function handleRestCalc(WP_REST_Request $request): WP_REST_Response {
        $ho_ten    = $request->get_param('ho_ten');
        $ngay_sinh = self::normalizeDate($request->get_param('ngay_sinh'));
        $gio_sinh  = $request->get_param('gio_sinh');
        $gioi_tinh = $request->get_param('gioi_tinh');
        $nam_xem   = $request->get_param('nam_xem') ?: date('Y');

        if (empty($ho_ten) || empty($ngay_sinh) || empty($gio_sinh) || empty($gioi_tinh)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin.'], 200);
        }

        $input = [
            'ho_ten'    => $ho_ten,
            'ngay_sinh' => $ngay_sinh,
            'gio_sinh'  => $gio_sinh,
            'gioi_tinh' => $gioi_tinh,
            'nam_xem'   => $nam_xem,
        ];

        self::loadEngine();

        $result = TuVi_Engine::lap_la_so($input);

        if (isset($result['error'])) {
            return new WP_REST_Response(['success' => false, 'message' => $result['error']], 200);
        }

        $thong_tin = $result['thong_tin'];
        $la_so     = $result['la_so'];
        $skip_ai = !empty($thong_tin['egg_message']);
        $html = $this->renderLaSoHtml($thong_tin, $la_so);

        return new WP_REST_Response([
            'success'      => true,
            'html'         => $html,
            'thong_tin'    => $thong_tin,
            'la_so'        => $la_so,
            'input'        => $input,
            'skip_ai'      => $skip_ai,
            'is_logged_in' => is_user_logged_in(),
        ], 200);
    }

    private function renderLaSoHtml(array $thong_tin, array $la_so): string {
        $grid_classes = [
            6 => 'cung-ti',  7 => 'cung-ngo',  8 => 'cung-mui',  9 => 'cung-than',
            5 => 'cung-thin',                                    10 => 'cung-dau',
            4 => 'cung-mao',                                     11 => 'cung-tuat',
            3 => 'cung-dan', 2 => 'cung-suu',  1 => 'cung-ty',   12 => 'cung-hoi'
        ];
        $anchor_map = [5=>6, 3=>4, 10=>9, 12=>11, 8=>7, 1=>2];

        ob_start();
        include TUVI_PLUGIN_DIR . 'template/la-so/ls-page.php';
        return ob_get_clean();
    }

    private function tuvi_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user['username'])) return false;

        $date_format = get_option('date_format');
        $today = wp_date($date_format);

        $key = 'tuvi_quota_ai_' . $user['username'] . '_' . $today;
        $count = (int) get_transient($key);

        if ($count >= TUVI_RATE_LIMIT) return false;

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

        if (!TuVi_Settings::get_instance()->allowAI()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Chức năng đang tạm ngưng. Vui lòng quay lại sau.'], 200);
        }

        if (!$this->tuvi_quota()) {
            return new WP_REST_Response(['success' => false, 'message' => 'Đã đạt giới hạn phân tích trong ngày. Vui lòng quay lại vào ngày mai.'], 200);
        }

        $hp_trap = $request->get_param('tuvi_hp_tname');
        if (!empty($hp_trap)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'], 403);
        }

        $user_question = sanitize_textarea_field($request->get_param('user_question') ?? '');
        $is_qa_mode = rest_sanitize_boolean($request->get_param('is_qa_mode'));

        if ($is_qa_mode && mb_strlen($user_question) < 10) {
            return new WP_REST_Response(['success' => false, 'message' => 'Vui lòng đặt câu hỏi rõ ràng hơn.'], 200);
        }

        $ho_ten = $request->get_param('ho_ten') ?: "Đương Số";
        $ngay_sinh = self::normalizeDate($request->get_param('ngay_sinh'));
        $gio_sinh = $request->get_param('gio_sinh');
        $gioi_tinh = $request->get_param('gioi_tinh') ?: 'nam';
        $nam_xem = $request->get_param('nam_xem') ?: date('Y');

        $input = [
            'ho_ten' => $ho_ten, 'ngay_sinh' => $ngay_sinh, 'gio_sinh' => $gio_sinh,
            'gioi_tinh' => $gioi_tinh, 'nam_xem' => $nam_xem
        ];

        $cacheFile = $this->getCacheFilePath($input);
        if (!$is_qa_mode && file_exists($cacheFile)) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if (!empty($cached) && isset($cached['tuvi_html'])) {
                return new WP_REST_Response(['success' => true, 'tuvi_html' => $cached['tuvi_html'], 'is_cached' => true], 200);
            }
        }

        self::loadEngine();
        $result = TuVi_Engine::lap_la_so($input);
        if (isset($result['error'])) {
            return new WP_REST_Response(['success' => false, 'message' => 'Lỗi kết nối, vui lòng thực hiện lại.'], 200);
        }

        self::loadPrompt();
        self::loadAIProviders();

        $providers = [
            'gemini'  => fn($p) => Tuvi_Gemini::get_instance()->ftn_gemini_generate($p),
            'groq'    => fn($p) => Tuvi_Groq::get_instance()->ftn_groq_generate($p),
            'mistral' => fn($p) => Tuvi_Mistral::get_instance()->ftn_mistral_generate($p),
        ];

        $prompt = '';

        if ($is_qa_mode) {
            if (mb_strlen($user_question, 'UTF-8') > 500) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Câu hỏi quá dài.'
                ], 200);
            }

            $gatekeeper_prompt = TuVi_Prompt::gatekeeper($user_question);
            $gatekeeper_order_str = TuVi_Settings::get_instance()->gatekeeperOrder() ?: 'groq,mistral,gemini';
            $gk_execution_order = array_map('trim', explode(',', $gatekeeper_order_str));

            $gk_response = '';
            foreach ($gk_execution_order as $provider) {
                if (isset($providers[$provider])) {
                    $raw_gk = $providers[$provider]($gatekeeper_prompt);
                    if (!str_starts_with($raw_gk, '[Error]') && !empty(trim($raw_gk))) {
                        $gk_clean = remove_accents(trim($raw_gk));
                        $gk_response = strtoupper(preg_replace('/[^A-Z]/', '', $gk_clean));
                        break;
                    }
                }
            }

            if (strpos($gk_response, 'KHONG') !== false || empty($gk_response)) {
                return new WP_REST_Response(['success' => true, 'message' => 'Vui lòng đặt câu hỏi rõ ràng hơn về vấn đề của bạn nhé!'], 200);
            }

            $valid_categories = [
                'TONGQUAN', 'TINHCACH', 'CONGVIEC', 'TAILOC',
                'TINHCAM',  'SUKKHOE',  'GIADAO',   'VANHAN',
                'XUATHANH', 'QUANHE',   'PHAPLY',
            ];

            if (!in_array($gk_response, $valid_categories)) {
                return new WP_REST_Response(['success' => false, 'message' => 'Vui lòng đặt câu hỏi rõ ràng hơn.'], 200);
            }

            $prompt = TuVi_Prompt::build_qa($result['thong_tin'], $result['la_so'], $user_question, $gk_response);
        } else {
            $prompt = TuVi_Prompt::build($result['thong_tin'], $result['la_so']);
        }

        file_put_contents(plugin_dir_path(__FILE__) . 'prompt.log', $prompt);

        $analysis_order_str = TuVi_Settings::get_instance()->analysisOrder() ?: 'gemini,mistral,groq';
        $execution_order = array_map('trim', explode(',', $analysis_order_str));
        $execution_order = array_filter($execution_order, fn($p) => isset($providers[$p]));
        foreach (array_keys($providers) as $key) {
            if (!in_array($key, $execution_order, true)) $execution_order[] = $key;
        }

        $rawResponse = '';
        $successful_provider = '';

        foreach ($execution_order as $current_provider) {
            if (!isset($providers[$current_provider])) continue;
            $rawResponse = $providers[$current_provider]($prompt);
            if (!str_starts_with($rawResponse, '[Error]') && !empty($rawResponse)) {
                $successful_provider = $current_provider;
                break;
            }
        }

        file_put_contents(plugin_dir_path(__FILE__) . 'rawResponse.log', $rawResponse);

        $markdownToHtml = self::markdownToHtml($rawResponse);

        if (!$is_qa_mode && $successful_provider === 'gemini' &&
            str_contains($rawResponse, '[AST_RESULT]') && str_contains($rawResponse, '[/AST_RESULT]')) {
            $payload = ['tuvi_html' => $markdownToHtml];
            file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        return new WP_REST_Response([
            'success'   => true,
            'tuvi_html' => $markdownToHtml,
            'is_cached' => false,
        ], 200);
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
            require_once TUVI_PLUGIN_DIR . 'lib/Parsedown.php';
        }

        $Parsedown = new Parsedown();
        return $Parsedown->text($md);
    }
}

TuVi_Handle::get_instance();