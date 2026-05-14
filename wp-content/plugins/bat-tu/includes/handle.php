<?php

if (!defined("ABSPATH")) exit();

class Battu_Handle {

    const BATTU_CACHE = "/battu";

    private static ?Battu_Handle $instance = null;

    public function __construct() {
        add_action("wp_enqueue_scripts", [$this, "enqueue_assets"], 12);
        add_action("wp_head", [$this, "preloadAssets"], 0);
        add_shortcode("battu_lap_la_so", [$this, "shortcode_lap_la_so"]);

        add_filter("lbv_header_title", [$this, "removePageTitle"], 10, 2);
        add_action("rest_api_init", [$this, "registerRestRoutes"]);
        add_filter("cron_schedules", [$this, "addMonthlyCronSchedule"]);
        add_action("battu_daily_cache_reset", [$this, "resetCacheDirectory"]);

        if (!wp_next_scheduled("battu_daily_cache_reset")) {
            $next_run = strtotime("tomorrow midnight");
            wp_schedule_event($next_run,"daily","battu_daily_cache_reset");
        }
    }

    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static function normalizeDate(?string $date): ?string {
        if (empty($date)) {
            return $date;
        }
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        $normalized = preg_replace("/[-\.\s]+/", "/", $date);
        foreach (["d/m/Y", "j/n/Y"] as $fmt) {
            $d = DateTime::createFromFormat($fmt, $normalized);
            if ($d) {
                return $d->format("Y-m-d");
            }
        }
        return $date;
    }

    private static function validateDate( ?string $param, bool $allowEmpty = false ): bool {
        if (empty($param)) {
            return $allowEmpty;
        }

        $year = 1990;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $param, $matches)) {
            if ( !checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1] ) ) {
                return false;
            }
            $year = (int) $matches[1];
        } elseif (
            preg_match('/^(\d{1,2})[\/\-.\s](\d{1,2})[\/\-.\s](\d{4})$/',$param,$matches)
        ) {
            if ( !checkdate( (int) $matches[2], (int) $matches[1], (int) $matches[3] ) ) {
                return false;
            }
            $year = (int) $matches[3];
        } else {
            return false;
        }
        if ($year < 1900 || $year > 2100) {
            return false;
        }
        return true;
    }

    private static function loadData(): void {
        if (!class_exists("BatTu_Data")) {
            require_once BATTU_PLUGIN_DIR . "includes/data.php";
        }
    }

    private static function loadEngine(): void {
        self::loadData();
        if (!class_exists("BatTu_Engine")) {
            require_once BATTU_PLUGIN_DIR . "includes/engine.php";
        }
    }

    private static function loadAIProviders(): void {
        if (!class_exists("Battu_Gemini")) {
            require_once BATTU_PLUGIN_DIR . "includes/gemini.php";
        }
        if (!class_exists("Battu_Groq")) {
            require_once BATTU_PLUGIN_DIR . "includes/groq.php";
        }
        if (!class_exists("Battu_Mistral")) {
            require_once BATTU_PLUGIN_DIR . "includes/mistral.php";
        }
        if (!class_exists("Battu_Prompt")) {
            require_once BATTU_PLUGIN_DIR . "includes/prompt.php";
        }
    }

    public function addMonthlyCronSchedule(array $schedules): array {
        $schedules["monthly_first_battu"] = [
            "interval" => 30 * DAY_IN_SECONDS,
            "display" => __("Monthly (approx)", "bat-tu"),
        ];
        return $schedules;
    }

    public function resetCacheDirectory(): void {
        $upload_dir = wp_upload_dir();
        $cache_dir = $upload_dir["basedir"] . self::BATTU_CACHE;
        if (!is_dir($cache_dir)) {
            return;
        }
        $files = glob($cache_dir . "/*.json");
        if (empty($files)) {
            return;
        }
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function getCacheFilePath(array $input): string {
        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir["basedir"] . self::BATTU_CACHE;
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
            file_put_contents($target_dir . "/.htaccess", "Deny from all\n");
            file_put_contents(
                $target_dir . "/index.php",
                "<?php // Silence is golden"
            );
        }
        $hash = md5( mb_strtolower(trim($input["ho_ten"])) . "|" . $input["ngay_sinh"] . "|" . $input["gio_sinh"] . "|" . $input["gioi_tinh"] );
        return trailingslashit($target_dir) . $hash . ".json";
    }

    public function removePageTitle($title, $post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return $title;
        }
        $shortcodes = ["battu_lap_la_so"];
        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return "";
            }
        }
        return $title;
    }

    public function enqueue_assets() {
        if (is_admin()) {
            return;
        }
        global $post;
        if (empty($post) || empty($post->post_content)) {
            return;
        }
        $has_shortcode = false;
        if (has_shortcode($post->post_content, "battu_lap_la_so")) {
            $has_shortcode = true;
        }
        if (!$has_shortcode) {
            return;
        }

        wp_enqueue_style( "battu", BATTU_PLUGIN_URL . "assets/battu.css", [], BATTU_PLUGIN_VERSION );
        wp_enqueue_script( "battu", BATTU_PLUGIN_URL . "assets/battu.min.js", ["jquery"], BATTU_PLUGIN_VERSION, true );
        wp_localize_script("battu", "battu", [
            "api_url" => esc_url_raw(rest_url("battu/v1/")),
        ]);
    }

    public function preloadAssets(): void {
        global $post;
        if (!is_a($post, "WP_Post")) {
            return;
        }
        if (has_shortcode($post->post_content, "battu_lap_la_so")) {
            echo '<link rel="preload" href="' . esc_url( BATTU_PLUGIN_URL . "assets/battu.css?ver=" . BATTU_PLUGIN_VERSION ) . '" as="style">' . "\n";
        }
    }

    public function shortcode_lap_la_so($atts = []) {
        ob_start();
        include BATTU_PLUGIN_DIR . "template/landing.php";
        return ob_get_clean();
    }

    public function registerRestRoutes(): void {
        $validateName = function ($param) {
            return empty($param) || (strlen($param) <= 50 && preg_match('/^[a-zA-ZÀ-ỹ\s\-]+$/u', $param));
        };

        register_rest_route("battu/v1", "/calculate", [
            "methods" => "POST",
            "callback" => [$this, "handleRestCalc"],
            "permission_callback" => "__return_true",
            "args" => [
                "ho_ten" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                    "validate_callback" => $validateName,
                ],
                "ngay_sinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                    "validate_callback" => fn($p) => self::validateDate(
                        $p,
                        true
                    ),
                ],
                "gio_sinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                    "validate_callback" => function ($param) {
                        return empty($param) ||
                            preg_match(
                                '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                                $param
                            );
                    },
                ],
                "gioi_tinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                    "validate_callback" => function ($param) {
                        return empty($param) || in_array($param, ["nam", "nu"]);
                    },
                ],
            ],
        ]);

        register_rest_route("battu/v1", "/analyze", [
            "methods" => "POST",
            "callback" => [$this, "handleRestAnalyze"],
            "permission_callback" => "__return_true",
            "args" => [
                "ho_ten" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "ngay_sinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                    "validate_callback" => fn($p) => self::validateDate(
                        $p,
                        true
                    ),
                ],
                "gio_sinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "gioi_tinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "battu_hp_tname" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "user_question" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_textarea_field",
                ],
                "is_qa_mode" => [
                    "required" => false,
                    "sanitize_callback" => "rest_sanitize_boolean",
                ],
            ],
        ]);

        register_rest_route("battu/v1", "/send-support", [
            "methods" => "POST",
            "callback" => [$this, "handleRestSendSupport"],
            "permission_callback" => "__return_true",
            "args" => [
                "ho_ten" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "ngay_sinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "gio_sinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "gioi_tinh" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_text_field",
                ],
                "email" => [
                    "required" => false,
                    "sanitize_callback" => "sanitize_email",
                ],
                "question" => [
                    "required" => true,
                    "sanitize_callback" => "sanitize_textarea_field",
                ],
            ],
        ]);
    }

    public function handleRestSendSupport(WP_REST_Request $request): WP_REST_Response {
        $user = self::get_cookie_user();
        if (!$user || empty($user['username'])) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Bạn cần đăng nhập để gửi câu hỏi."],
                200
            );
        }

        $user_obj = get_user_by('login', $user['username']);
        if (!$user_obj) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Không tìm thấy người dùng."],
                200
            );
        }

        $today = date('Y-m-d');
        $count_key = "battu_support_count_{$today}";
        $count = get_user_meta($user_obj->ID, $count_key, true);
        $count = $count ? intval($count) : 0;

        if ($count >= 30) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Bạn đã gửi 3 mail hôm nay rồi. Vui lòng chờ phản hồi nhé."],
                200
            );
        }

        $ho_ten = $request->get_param("ho_ten");
        $question = $request->get_param("question");

        if (empty($question) || mb_strlen($question, "UTF-8") < 10) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Vui lòng đặt câu hỏi rõ nghĩa hơn."],
                200
            );
        }

        if (mb_strlen($question, "UTF-8") > 650) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Câu hỏi quá dài. Vui lòng ngắn gọn dưới 650 ký tự."],
                200
            );
        }

        $wordCount = count(preg_split('/\s+/', trim($question), -1, PREG_SPLIT_NO_EMPTY));
        if ($wordCount < 20) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Vui lòng ghi rõ câu hỏi của bạn."],
                200
            );
        }

        $user_name = $user_obj->display_name ?? '';

        $lines = explode("\n", $question);
        $parsed = [];
        $actual_question = '';
        foreach ($lines as $line) {
            if (preg_match('/^Họ tên:\s*(.+)$/', $line, $m)) {
                $parsed['ho_ten'] = trim($m[1]);
            } elseif (preg_match('/^Ngày sinh:\s*(.+)$/', $line, $m)) {
                $parsed['ngay_sinh'] = self::normalizeDate(trim($m[1]));
            } elseif (preg_match('/^Giờ sinh:\s*(.+)$/', $line, $m)) {
                $parsed['gio_sinh'] = trim($m[1]);
            } elseif (preg_match('/^Giới tính:\s*(.+)$/', $line, $m)) {
                $parsed['gioi_tinh'] = trim($m[1]);
            } elseif (preg_match('/^Email:\s*(.+)$/', $line, $m)) {
                $parsed['email'] = trim($m[1]);
            } elseif (preg_match('/^Câu hỏi:\s*(.*)$/', $line, $m)) {
                $actual_question = trim($m[1]);
            } elseif (!empty(trim($line)) && !preg_match('/^(Họ tên|Ngày sinh|Giờ sinh|Giới tính|Email|Câu hỏi):/', $line)) {
                $actual_question .= ($actual_question ? ' ' : '') . trim($line);
            }
        }

        $battu_input = [
            "ho_ten" => $parsed['ho_ten'] ?? $ho_ten ?: $user_name,
            "ngay_sinh" => $parsed['ngay_sinh'] ?? '',
            "gio_sinh" => $parsed['gio_sinh'] ?? '',
            "gioi_tinh" => $parsed['gioi_tinh'] ?? 'nam',
        ];

        if (empty($battu_input['ngay_sinh']) || empty($battu_input['gio_sinh'])) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Không tìm thấy thông tin ngày giờ sinh. Vui lòng nhập đầy đủ."],
                200
            );
        }

        self::loadEngine();
        $result = BatTu_Engine::lap_bat_tu($battu_input);

        $formatted = self::format_output($result, $battu_input);

        require_once BATTU_PLUGIN_DIR . 'includes/prompt.php';
        $prompt = Battu_Prompt::build_qa($formatted, $actual_question, '');

        $admin_email = get_option('admin_email');

        $subject = "[Hỗ trợ luận giải Bát Tự] Câu hỏi từ {$battu_input['ho_ten']}";
        $message = "--- THÔNG TIN BÁT TỬ ---\n\n{$prompt}\n\n";

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $sent = wp_mail($admin_email, $subject, $message, $headers);

        if ($sent) {
            update_user_meta($user_obj->ID, $count_key, $count + 1);
            return new WP_REST_Response(
                ["success" => true, "message" => "Đã nhận yêu cầu, Mình sẽ phản hồi sớm nhất có thể."],
                200
            );
        } else {
            return new WP_REST_Response(
                ["success" => false, "message" => "Không gửi được email. Vui lòng thử lại sau."],
                200
            );
        }
    }

    public function handleRestCalc(WP_REST_Request $request): WP_REST_Response {
        $ho_ten = $request->get_param("ho_ten");
        $ngay_sinh = self::normalizeDate($request->get_param("ngay_sinh"));
        $gio_sinh = $request->get_param("gio_sinh");
        $gioi_tinh = $request->get_param("gioi_tinh");

        if ( empty($ho_ten) || empty($ngay_sinh) || empty($gio_sinh) || empty($gioi_tinh) ) {
            return new WP_REST_Response(
                [
                    "success" => false,
                    "message" => "Vui lòng nhập đầy đủ thông tin.",
                ],
                200
            );
        }

        $input = [
            "ho_ten" => $ho_ten,
            "ngay_sinh" => $ngay_sinh,
            "gio_sinh" => $gio_sinh,
            "gioi_tinh" => $gioi_tinh,
        ];

        self::loadEngine();
        $result = BatTu_Engine::lap_bat_tu($input);

        if (isset($result["error"])) {
            return new WP_REST_Response(
                ["success" => false, "message" => $result["error"]],
                200
            );
        }

        $formatted = self::format_output($result, $input);
        $html = $this->renderLaSoHtml($formatted);

        return new WP_REST_Response(
            [
                "success" => true,
                "html" => $html,
                "thong_tin" => $formatted["thong_tin"],
                "tu_tru" => $formatted["tu_tru"],
                "dai_van" => $formatted["dai_van"],
                "input" => $input,
                "is_logged_in" => is_user_logged_in(),
            ],
            200
        );
    }

    private static function format_output(array $result, array $input): array {
        $data = BatTu_Data::load("all");
        $thong_tin = $result["thong_tin"];
        $tu_tru = $result["tu_tru"];
        $dai_van = $result["dai_van"];
        $than_vuong_nhuoc = $result["than_vuong_nhuoc"] ?? [];
        $dung_than = $result["dung_than"] ?? [];

        $thien_can = $data["thien_can"];
        $dia_chi = $data["dia_chi"];
        $can_chi_60 = $data["can_chi_60"];
        $ngu_hanh = $data["ngu_hanh"];
        $thap_than = $data["thap_than"];

        $truong_sinh_names = [
            "truong_sinh" => "Trường sinh",
            "moc_duc" => "Mộc dục",
            "quan_doi" => "Quan đới",
            "lam_quan" => "Lâm quan",
            "de_vuong" => "Đế vượng",
            "suy" => "Suy",
            "benh" => "Bệnh",
            "tu" => "Tử",
            "mo" => "Mộ",
            "tuyet" => "Tuyệt",
            "thai" => "Thai",
            "duong" => "Dưỡng",
        ];

        $short_tt = [
            "Thực Thần" => "Thực",
            "Thương Quan" => "Thương",
            "Thiên Tài" => "T.Tài",
            "Chính Tài" => "C.Tài",
            "Thất Sát" => "Sát",
            "Chính Quan" => "Quan",
            "Thiên Ấn" => "Kiêu",
            "Chính Ấn" => "Ấn",
            "Tỷ Kiên" => "Tỷ",
            "Kiếp Tài" => "Kiếp",
        ];

        $can_ngay = $tu_tru["ngay"]["can"];
        $chi_ngay = $tu_tru["ngay"]["chi"];
        $nhat_chu = $thien_can[$can_ngay] ?? [];
        $nhat_chu_name = $nhat_chu["name"] ?? "";
        $nhat_tru_name =
            ($nhat_chu["name"] ?? "") .
            " " .
            ($dia_chi[$chi_ngay]["name"] ?? "");

        // Nạp Âm năm sinh (theo lục thập hoa giáp)
        $nam_nap_am_id =
            ($tu_tru["nam"]["can"] ?? "") . "_" . ($tu_tru["nam"]["chi"] ?? "");
        $nap_am_nam = $data["nap_am_60"][$nam_nap_am_id] ?? "";

        $tu_tru_formatted = [];
        foreach (["nam", "thang", "ngay", "gio"] as $tru_key) {
            $tru_data = $tu_tru[$tru_key];
            $can_id = $tru_data["can"];
            $chi_id = $tru_data["chi"];

            $can_info = $thien_can[$can_id] ?? [];
            $chi_info = $dia_chi[$chi_id] ?? [];

            $thap_than_id = $tru_data["thap_than_can"] ?? "";
            if ($thap_than_id === "nhat_chu") {
                $thap_than_name = "Nhật Chủ";
                $thap_than_short = "NHẬT CHỦ";
            } elseif (isset($thap_than[$thap_than_id])) {
                $thap_than_name = $thap_than[$thap_than_id]["name"];
                $thap_than_short =
                    $short_tt[$thap_than_name] ?? $thap_than_name;
            } else {
                $thap_than_name = "";
                $thap_than_short = "";
            }

            $nap_am_id = $can_id . "_" . $chi_id;
            $nap_am = $data["nap_am_60"][$nap_am_id] ?? "";

            $tang_can_formatted = [];
            if (isset($chi_info["tang_can"])) {
                foreach ($chi_info["tang_can"] as $tc_id => $tc_pct) {
                    $tc_info = $thien_can[$tc_id] ?? [];

                    if ($tc_id === $can_ngay) {
                        $tc_thap_than_name = "Tỷ Kiên";
                    } else {
                        $tc_tt_id = BatTu_Engine::tinh_thap_than_can(
                            $can_ngay,
                            $tc_id,
                            $thien_can,
                            $thap_than
                        );
                        $tc_thap_than_name =
                            $thap_than[$tc_tt_id]["name"] ?? "";
                    }
                    $tc_thap_than_short =
                        $short_tt[$tc_thap_than_name] ?? $tc_thap_than_name;

                    $ts_idx = array_search(
                        $chi_id,
                        $data["truong_sinh_12"]["map"][$tc_id]
                    );
                    $tc_truong_sinh =
                        $truong_sinh_names[
                        $data["truong_sinh_12"]["states"][$ts_idx]
                        ] ?? "";

                    $tang_can_formatted[] = [
                        "can" => $tc_info["name"] ?? $tc_id,
                        "element" => $tc_info["element"] ?? "",
                        "pct" => $tc_pct,
                        "thap_than_short" => $tc_thap_than_short,
                        "truong_sinh" => $tc_truong_sinh,
                    ];
                }
            }

            $than_sat_formatted = [];
            if (!empty($tru_data["than_sat"])) {
                $than_sat_data = $data["than_sat"] ?? [];
                foreach ($tru_data["than_sat"] as $ts_id) {
                    $than_sat_formatted[] =
                        $than_sat_data[$ts_id]["name"] ?? $ts_id;
                }
            }

            $tu_tru_formatted[$tru_key] = [
                "can_name" => $can_info["name"] ?? $can_id,
                "can_element" => $can_info["element"] ?? "",
                "chi_name" => $chi_info["name"] ?? $chi_id,
                "chi_element" => $chi_info["element"] ?? "",
                "thap_than_short" => $thap_than_short,
                "truong_sinh" =>
                    $truong_sinh_names[$tru_data["truong_sinh"]] ?? "",
                "nhat_kien" => $truong_sinh_names[$tru_data["nhat_kien"]] ?? "",
                "nguyet_kien" =>
                    $truong_sinh_names[$tru_data["nguyet_kien"]] ?? "",
                "nap_am" => $nap_am,
                "tang_can" => $tang_can_formatted,
                "than_sat" => $than_sat_formatted,
            ];
        }

        $dai_van_formatted = [];
        foreach ($dai_van["van_trinh"] as $dv) {
            $dv_can = $thien_can[$dv["can"]] ?? [];
            $dv_chi = $dia_chi[$dv["chi"]] ?? [];

            $dv_tt_id = BatTu_Engine::tinh_thap_than_can(
                $can_ngay,
                $dv["can"],
                $thien_can,
                $thap_than
            );
            $dv_tt_name = $thap_than[$dv_tt_id]["name"] ?? "";
            $short_dv_tt = $short_tt[$dv_tt_name] ?? $dv_tt_name;

            $tieu_van_formatted = [];
            foreach ($dv["tieu_van"] as $tv) {
                $tv_can_name = $thien_can[$tv["can"]]["name"];
                $tv_chi_name = $dia_chi[$tv["chi"]]["name"];
                $tv_tt_name = $thap_than[$tv["thap_than"]]["name"] ?? "";
                $tieu_van_formatted[] = [
                    "nam" => $tv["nam"],
                    "can_name" => $tv_can_name,
                    "chi_name" => $tv_chi_name,
                    "thap_than_short" => $short_tt[$tv_tt_name] ?? $tv_tt_name,
                ];
            }

            $dai_van_formatted[] = [
                "tuoi" => $dv["tuoi"],
                "nam_bat_dau" => $dv["nam_bat_dau"],
                "can_name" => $dv_can["name"] ?? $dv["can"],
                "chi_name" => $dv_chi["name"] ?? $dv["chi"],
                "can_element" => $dv_can["element"] ?? "",
                "chi_element" => $dv_chi["element"] ?? "",
                "thap_than_short" => $short_dv_tt,
                "tieu_van" => $tieu_van_formatted,
            ];
        }

        $mua_chi = $data["mua_chi"] ?? [];
        $vuong_suy = $data["vuong_suy"] ?? [];
        $chi_thang = $tu_tru["thang"]["chi"];
        $mua_hien_tai = "tu_quy";
        foreach ($mua_chi as $mua => $chi_arr) {
            if (in_array($chi_thang, $chi_arr)) {
                $mua_hien_tai = $mua;
                break;
            }
        }
        $vuong_suy_nhat_chu =
            $vuong_suy[$mua_hien_tai][$nhat_chu["element"] ?? ""] ?? "";
        $vuong_suy_names = [
            "vuong" => "Vượng",
            "tuong" => "Tướng",
            "huu" => "Hưu",
            "tu" => "Tù",
            "tu_state" => "Tử",
        ];

        return [
            "thong_tin" => [
                "ho_ten" => $thong_tin["ho_ten"],
                "ngay_sinh" => $thong_tin["ngay_sinh"],
                "gio_sinh" => $thong_tin["gio_sinh"],
                "am_lich" => BatTu_Engine::format_lunar_date($thong_tin["ngay_sinh"]),
                "gioi_tinh" =>
                    (($thien_can[$tu_tru["nam"]["can"]] ?? [])["polarity"] ?? "+") === "+"
                        ? ($thong_tin["gioi_tinh"] === "nam" ? "Dương nam" : "Dương nữ")
                        : ($thong_tin["gioi_tinh"] === "nam" ? "Âm nam" : "Âm nữ"),
                "nhat_chu" => $nhat_chu_name,
                "nhat_tru" => $nhat_tru_name,
                "nhat_chu_hanh" =>
                    $ngu_hanh[$nhat_chu["element"] ?? ""]["name"] ?? "",
                "nap_am_nam" => $nap_am_nam,
                "vuong_suy" =>
                    $vuong_suy_names[$vuong_suy_nhat_chu] ??
                    $vuong_suy_nhat_chu,
                "mua" => $mua_hien_tai,
                "tiet_khi_hien_tai" => $thong_tin["tiet_khi_hien_tai"] ?? "",
                "tiet_start" => $thong_tin["tiet_start"] ?? "",
                "tiet_end" => $thong_tin["tiet_end"] ?? "",
            ],
            "tu_tru" => $tu_tru_formatted,
            "dai_van" => [
                "tuoi_khoi_van" => $dai_van["tuoi_khoi_van"],
                "chieu_hanh_van" => $dai_van["chieu_hanh_van"],
                "thoi_gian_khoi_van" => $dai_van["thoi_gian_khoi_van"] ?? "",
                "van_trinh" => $dai_van_formatted,
            ],
            "than_vuong_nhuoc" => [
                "ket_qua" => $than_vuong_nhuoc["than_vuong"] ?? false ? "Thân vượng" : "Thân nhược",
                "muc_do" => $than_vuong_nhuoc["muc_do"] ?? "",
                "diem" => $than_vuong_nhuoc["diem"] ?? 0,
                "chi_tiet" => $than_vuong_nhuoc["chi_tiet"] ?? [],
            ],
            "dung_than" => [
                "dung_than" => $dung_than["dung_than"] ?? [],
                "hy_than" => $dung_than["hy_than"] ?? [],
                "ky_than" => $dung_than["ky_than"] ?? [],
            ],
            "data" => $data,
        ];
    }

    private function renderLaSoHtml(array $formatted): string {
        $thong_tin = $formatted["thong_tin"];
        $tu_tru = $formatted["tu_tru"];
        $dai_van = $formatted["dai_van"];
        $than_vuong_nhuoc = $formatted["than_vuong_nhuoc"] ?? [];
        $dung_than = $formatted["dung_than"] ?? [];
        $battu_data = $formatted["data"];
        $is_ajax = true;
        ob_start();
        include BATTU_PLUGIN_DIR . "template/result.php";
        return ob_get_clean();
    }

    private function battu_quota(): bool {
        $user = self::get_cookie_user();
        if (empty($user["username"])) {
            return false;
        }
        $key = "battu_quota_ai_" . $user["username"] . "_" . wp_date(get_option("date_format"));
        $count = (int) get_transient($key);
        if ($count >= BATTU_RATE_LIMIT) {
            return false;
        }
        set_transient( $key, $count + 1, strtotime("tomorrow", current_time("timestamp")) - current_time("timestamp") );
        return true;
    }

    private static function get_cookie_user() {
        $cookie = $_COOKIE[LOGGED_IN_COOKIE] ?? "";
        return $cookie ? wp_parse_auth_cookie($cookie, "logged_in") : false;
    }

    public function validate_logged_in() {
        return (bool) self::get_cookie_user();
    }

    public function handleRestAnalyze(WP_REST_Request $request): WP_REST_Response {
        if (!$this->validate_logged_in()) {
            return new WP_REST_Response( [ "success" => false, "message" => "Vui lòng đăng nhập để sử dụng tính năng này.", ], 200 );
        }
        if (!Battu_Settings::get_instance()->allowAI()) {
            return new WP_REST_Response( [ "success" => false, "message" => "Chức năng đang tạm ngưng. Vui lòng quay lại sau.", ], 200 );
        }
        if (!$this->battu_quota()) {
            return new WP_REST_Response( [ "success" => false, "message" => "Đã đạt giới hạn phân tích trong ngày.", ], 200 );
        }
        if (!empty($request->get_param("battu_hp_tname"))) {
            return new WP_REST_Response( ["success" => false, "message" => "Yêu cầu không hợp lệ."], 403 );
        }

        $user_question = sanitize_textarea_field($request->get_param("user_question") ?? "");
        $is_qa_mode = rest_sanitize_boolean($request->get_param("is_qa_mode"));

        if ($is_qa_mode && mb_strlen($user_question) < 10) {
            return new WP_REST_Response( [ "success" => false, "message" => "Vui lòng đặt câu hỏi rõ ràng hơn.", ], 200 );
        }

        $input = [
            "ho_ten" => $request->get_param("ho_ten") ?: "Đương Số",
            "ngay_sinh" => self::normalizeDate(
                $request->get_param("ngay_sinh")
            ),
            "gio_sinh" => $request->get_param("gio_sinh"),
            "gioi_tinh" => $request->get_param("gioi_tinh") ?: "nam",
        ];

        $cacheFile = $this->getCacheFilePath($input);
        if (!$is_qa_mode && file_exists($cacheFile)) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if (!empty($cached["battu_html"]) && ($cached["cache_type"] ?? "") === "full_analysis") {
                return new WP_REST_Response( [ "success" => true, "battu_html" => $cached["battu_html"], "is_cached" => true, ], 200 );
            }
        }

        self::loadEngine();
        $result = BatTu_Engine::lap_bat_tu($input);
        if (isset($result["error"])) {
            return new WP_REST_Response(
                ["success" => false, "message" => "Lỗi kết nối."],
                200
            );
        }

        self::loadAIProviders();
        $providers = [
            "gemini" => fn( $p ) => Battu_Gemini::get_instance()->ftn_battu_gemini_generate($p),
            "groq" => fn( $p ) => Battu_Groq::get_instance()->ftn_battu_groq_generate($p),
            "mistral" => fn( $p ) => Battu_Mistral::get_instance()->ftn_battu_mistral_generate($p),
        ];

        $formatted = self::format_output($result, $input);

        if ($is_qa_mode) {
            if (mb_strlen($user_question, "UTF-8") > 500) {
                return new WP_REST_Response(
                    ["success" => false, "message" => "Câu hỏi quá dài."],
                    200
                );
            }
            $gatekeeper_prompt = Battu_Prompt::gatekeeper($user_question);
            $gatekeeper_order_str = Battu_Settings::get_instance()->gatekeeperOrder() ?: "groq,mistral,gemini";
            $execution_order = array_filter(
                array_map("trim", explode(",", $gatekeeper_order_str)),
                fn($p) => isset($providers[$p])
            );
            foreach (array_keys($providers) as $key) {
                if (!in_array($key, $execution_order, true)) {
                    $execution_order[] = $key;
                }
            }
            $category = "";
            foreach ($execution_order as $current_provider) {
                if (!isset($providers[$current_provider])) {
                    continue;
                }
                $category = $providers[$current_provider]($gatekeeper_prompt);
                if (!empty($category) && !str_starts_with($category, "[Error]")) {
                    break;
                }
            }
            $valid_categories = [
                'TONGQUAN', 'TINHCACH', 'CONGVIEC', 'TAILOC',
                'TINHCAM',  'SUKKHOE',  'GIADAO',   'VANHAN',
                'XUATHANH', 'QUANHE',   'PHAPLY',   'TAMLINH',
            ];

            if (!in_array($category, $valid_categories)) {
                return new WP_REST_Response(
                    ["success" => false, "message" => "Vui lòng đặt câu hỏi rõ ràng hơn."],
                    200
                );
            }

            $prompt = Battu_Prompt::build_qa($formatted, $user_question, $category);
        } else {
            $prompt = Battu_Prompt::build($formatted, "");
        }

        $analysis_order_str = Battu_Settings::get_instance()->analysisOrder() ?: "gemini,mistral,groq";
        $execution_order = array_filter(
            array_map("trim", explode(",", $analysis_order_str)),
            fn($p) => isset($providers[$p])
        );
        foreach (array_keys($providers) as $key) {
            if (!in_array($key, $execution_order, true)) {
                $execution_order[] = $key;
            }
        }

        $rawResponse = "";
        $successful_provider = "";
        foreach ($execution_order as $current_provider) {
            if (!isset($providers[$current_provider])) {
                continue;
            }
            $rawResponse = $providers[$current_provider]($prompt);
            if (
                !str_starts_with($rawResponse, "[Error]") &&
                !empty($rawResponse)
            ) {
                $successful_provider = $current_provider;
                break;
            }
        }

        $markdownToHtml = self::markdownToHtml($rawResponse);
        if ( !$is_qa_mode && $successful_provider === "gemini" && str_contains($rawResponse, "[AST_RESULT]") ) {
            file_put_contents( $cacheFile, json_encode( ["battu_html" => $markdownToHtml, "cache_type" => "full_analysis"], JSON_UNESCAPED_UNICODE ) );
        }

        return new WP_REST_Response( [ "success" => true, "battu_html" => $markdownToHtml, "is_cached" => false, ], 200 );
    }

    public static function markdownToHtml(string $md): string{
        if ( str_contains($md, "[AST_RESULT]") && str_contains($md, "[/AST_RESULT]") ) {
            preg_match("/\[AST_RESULT\]([\s\S]*?)\[\/AST_RESULT\]/",$md,$matches);
            if (!empty($matches[1])) {
                $md = trim($matches[1]);
            }
        }
        $md = preg_replace('/^[\-]{3,}$/m', "", $md);
        $md = preg_replace('/^\*{3,}$/m', "", $md);
        $md = preg_replace('/^_{3,}$/m', "", $md);

        if (!class_exists("Parsedown")) {
            require_once BATTU_PLUGIN_DIR . "lib/Parsedown.php";
        }

        return (new Parsedown())->text($md);
    }
}
Battu_Handle::get_instance();
