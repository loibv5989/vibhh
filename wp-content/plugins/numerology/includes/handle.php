<?php

if (!defined('ABSPATH')) exit;

class Numerology {

    private static ?Numerology $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private static function loadCalc(): void {
        if (!class_exists('Numerology_Calc')) {
            require_once NUMEROLOGY_PLUGIN_DIR . 'includes/calc.php';
        }
    }

    private static function loadRender(): void {
        if (!class_exists('Numerology_Render')) {
            require_once NUMEROLOGY_PLUGIN_DIR . 'template/render.php';
        }
    }

    private function __construct() {
        add_shortcode('numerology_landing', [$this, 'renderShortcode']);
        add_shortcode('numerology_personal', [$this, 'renderFormShortcode']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('wp_head', [$this, 'preloadAssets'], 0);
        add_filter('lbv_header_title', [$this, 'removePageTitle'], 10, 2);
        add_filter('body_class', [$this, 'addBodyClass']);
    }


    public function enqueueAssets(): void {
        if (!is_singular()) return;
        global $post;
        if (empty($post->post_content)) return;
        if (!has_shortcode($post->post_content, 'numerology_landing') && !has_shortcode($post->post_content, 'numerology_personal')) return;

        wp_enqueue_style('nrgy', NUMEROLOGY_PLUGIN_URL . 'assets/nrgy.css', [], NUMEROLOGY_VERSION);
        wp_enqueue_script('nrgy', NUMEROLOGY_PLUGIN_URL . 'assets/nrgy.min.js', ['jquery'], NUMEROLOGY_VERSION, true);
        wp_localize_script('nrgy', 'nrgy', [
            'api_url' => esc_url_raw(rest_url('nrgy/v1/')),
        ]);
    }

    public function preloadAssets(): void {
        if (!is_singular()) return;
        global $post;
        if (empty($post->post_content)) return;
        if (!has_shortcode($post->post_content, 'numerology_landing') && !has_shortcode($post->post_content, 'numerology_personal')) return;
        echo '<link rel="preload" href="' . esc_url(NUMEROLOGY_PLUGIN_URL . 'assets/nrgy.css' . '?ver=' . NUMEROLOGY_VERSION) . '" as="style">' . "\n";
    }

    public function registerRestRoutes(): void {
        $args = [
            'full_name' => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($param) {
                    if (empty($param) || strlen($param) > 40) return false;
                    return preg_match('/^[a-zA-Z\s\-]+$/u', $param);
                }
            ],
            'dob' => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($param) {
                    return !empty($param) && preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $param);
                }
            ]
        ];

        register_rest_route('nrgy/v1', '/calculate', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestCalc'],
            'permission_callback' => '__return_true',
            'args'                => $args
        ]);

        register_rest_route('nrgy/v1', '/analyze', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestAnalyze'],
            'permission_callback' => '__return_true',
            'args'                => $args
        ]);
    }

    public function renderShortcode(array $atts): string {
        ob_start();
        include_once NUMEROLOGY_PLUGIN_DIR . 'includes/svg.php';
        include_once NUMEROLOGY_PLUGIN_DIR . 'template/landing.php';
        return ob_get_clean();
    }

    public function renderFormShortcode(array $atts): string {
        ob_start();
        include_once NUMEROLOGY_PLUGIN_DIR . 'includes/svg.php';
        include_once NUMEROLOGY_PLUGIN_DIR . 'template/form.php';
        return ob_get_clean();
    }

    private static function classifyEasterEggs(array $easter_eggs): array {
        $hard_block_types = ['future', 'over120'];
        $soft_block_types = ['over100'];

        $hard = array_values(array_filter($easter_eggs, fn($e) => in_array($e['type'], $hard_block_types)));
        $soft = array_values(array_filter($easter_eggs, fn($e) => in_array($e['type'], $soft_block_types)));

        return ['hard' => $hard, 'soft' => $soft];
    }

    public function handleRestCalc(WP_REST_Request $request): WP_REST_Response {
        self::loadCalc();
        self::loadRender();

        $name = $request->get_param('full_name');
        $dob  = $request->get_param('dob');

        if (empty($name) || empty($dob)) {
            return new WP_REST_Response(['success' => false, 'message' => 'Please fill in all required information.'], 200);
        }

        try {
            $dobDisplay = self::normalizeDob($dob);
            $numbers = Numerology_Calc::calculate($name, $dobDisplay);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'message' => $e->getMessage()], 200);
        }

        $easter_eggs = $numbers['easter_eggs'] ?? [];
        ['hard' => $hard_blocks] = self::classifyEasterEggs($easter_eggs);

        if (!empty($hard_blocks)) {
            return new WP_REST_Response([
                'success' => true,
                'html'    => Numerology_Render::result($numbers, [], $name, $dobDisplay),
            ], 200);
        }

        $detail = Numerology_Calc::generateStaticAnalysis($numbers, $name, $dobDisplay);
        $calculation = Numerology_Calc::buildCalculation($name, $dobDisplay, $numbers);

        $tabs = [
            'detail'   => $detail,
            'calculation' => $calculation
        ];

        return new WP_REST_Response([
            'success' => true,
            'html'    => Numerology_Render::result($numbers, $tabs, $name, $dobDisplay)
        ], 200);
    }

    public function handleRestAnalyze(WP_REST_Request $request): WP_REST_Response {
        self::loadCalc();

        $name = $request->get_param('full_name');
        $dob  = $request->get_param('dob');

        if (empty($name) || empty($dob)) {
            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Please fill in all required information.'
                ], 200);
        }

        try {
            $dobDisplay = self::normalizeDob($dob);
            $numbers = Numerology_Calc::calculate($name, $dobDisplay);
        } catch (InvalidArgumentException $e) {
            return new WP_REST_Response(['success' => false, 'message' => $e->getMessage()], 200);
        }

        $detail = Numerology_Calc::generateStaticAnalysis($numbers, $name, $dobDisplay);

        return new WP_REST_Response([
            'success'     => true,
            'html_detail' => $detail,
        ], 200);
    }

    private static function normalizeDob(string $dob): string {
        $dob = trim($dob);

        $d = DateTime::createFromFormat('Y-m-d', $dob);
        if ($d && $d->format('Y-m-d') === $dob) {
            return $d->format('d/m/Y');
        }

        $normalized = preg_replace('/[\-\.\s]+/', '/', $dob);
        $formats = ['d/m/Y', 'j/n/Y', 'm/d/Y', 'n/j/Y'];
        foreach ($formats as $fmt) {
            $d = DateTime::createFromFormat($fmt, $normalized);
            if ($d && $d->format($fmt) === $normalized) {
                return $d->format('d/m/Y');
            }
        }

        throw new InvalidArgumentException('Invalid date format. Please use DD/MM/YYYY.');
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && (has_shortcode($post->post_content, 'numerology_landing') || has_shortcode($post->post_content, 'numerology_personal'))) {
            return '';
        }
        return $title;
    }

    public function addBodyClass(array $classes): array {
        global $post;
        if (!is_a($post, 'WP_Post')) return $classes;

        if (has_shortcode($post->post_content, 'numerology_landing') || has_shortcode($post->post_content, 'numerology_personal')) {
            $classes[] = 'ich-tool-page';
        }

        return $classes;
    }
}

Numerology::get_instance();