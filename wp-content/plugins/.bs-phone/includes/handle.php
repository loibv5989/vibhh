<?php
// FILE: handle.php
if (!defined('ABSPATH')) exit;

class BS_Phone_Handler {

    private static ?self $instance = null;

    public static function get_instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('phone_form',      [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 12);
        add_action('rest_api_init',      [$this, 'registerRestRoutes']);
        add_filter('lbv_header_title',   [$this, 'removePageTitle'], 10, 2);
    }

    public function enqueueAssets(): void {
        global $post;
        if (!is_a($post, 'WP_Post')) return;
        if (!has_shortcode($post->post_content, 'phone_form')) return;

        wp_enqueue_style('bs-phone',  BS_PHONE_PLUGIN_URL . 'assets/phone.css', [], BS_PHONE_VERSION);
        wp_enqueue_script('bs-phone', BS_PHONE_PLUGIN_URL . 'assets/phone.js', ['jquery'], BS_PHONE_VERSION, true);
        wp_localize_script('bs-phone', 'PhoneRest', [
            'rest_url' => rest_url('bs-phone/v1'),
        ]);
    }

    public function registerRestRoutes(): void {
        register_rest_route('bs-phone/v1', '/calculate', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRestCalc'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function renderShortcode(): string {
        ob_start();
        include BS_PHONE_PLUGIN_DIR . 'templates/landing.php';
        return ob_get_clean();
    }

    private static function loadClasses(): void {
        if (!class_exists('BS_Phone_Calc'))   require_once BS_PHONE_PLUGIN_DIR . 'includes/calc.php';
        if (!class_exists('BS_Phone_Render')) require_once BS_PHONE_PLUGIN_DIR . 'templates/render.php';
    }

    public function handleRestCalc(WP_REST_Request $request): WP_REST_Response {
        $name  = sanitize_text_field($request->get_param('full_name') ?? '');
        $dob   = sanitize_text_field($request->get_param('dob')       ?? '');
        $phone = sanitize_text_field($request->get_param('phone')     ?? '');

        // Validate phone server-side: 10 số, bắt đầu bằng 0, không nhận +84
        $phone_clean = preg_replace('/\D/', '', $phone);
        if (!preg_match('/^0[1-9][0-9]{8}$/', $phone_clean)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Số điện thoại không hợp lệ (phải có 10 số, bắt đầu bằng 0).',
            ], 200);
        }

        if (empty($name) || empty($dob)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin.',
            ], 200);
        }

        self::loadClasses();

        try {
            $calc_data = BS_Phone_Calc::calculate($name, $dob, $phone_clean);
        } catch (Exception $e) {
            return new WP_REST_Response(['success' => false, 'message' => $e->getMessage()], 200);
        }

        $narrative = BS_Phone_Calc::build_narrative($calc_data);

        $html      = BS_Phone_Render::indexes($calc_data, $narrative['block4'] ?? '');
        $tabs_html = BS_Phone_Render::narrative_tabs($narrative);

        return new WP_REST_Response([
            'success'   => true,
            'html'      => $html,
            'tabs_html' => $tabs_html,
        ], 200);
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if ($post && has_shortcode($post->post_content, 'phone_form')) return '';
        return $title;
    }
}

BS_Phone_Handler::get_instance();
