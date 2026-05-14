<?php
if (!defined("ABSPATH")) exit();

class BatTu_Engine {

    public static function lap_bat_tu($input) {
        $data = BatTu_Data::load("all");

        if (empty($input["ngay_sinh"]) || empty($input["gio_sinh"])) {
            return ["error" => "Vui lòng nhập đầy đủ ngày và giờ sinh."];
        }

        $normalized = self::chuan_hoa_dau_vao($input, $data["gio_dia_chi"]);
        if (isset($normalized["error"])) {
            return $normalized;
        }

        $thoi_gian_cung = self::tinh_toan_thien_van(
            $normalized,
            $data["tiet_khi"],
            $data["thang_tiet_khi"]
        );

        $tu_tru = self::lap_tu_tru($normalized, $thoi_gian_cung, $data);
        $tu_tru = self::tinh_thap_than( $tu_tru, $data["thap_than"], $data["thien_can"] );
        $tu_tru = self::tinh_truong_sinh_than_sat( $tu_tru, $data["truong_sinh_12"], $data["than_sat"] );
        $dai_van = self::tinh_dai_van(
            $tu_tru["nam"]["can"],
            $normalized["gioi_tinh"],
            $tu_tru["thang"],
            $thoi_gian_cung,
            $data,
            $normalized["ngay_sinh"],
            $tu_tru["ngay"]["can"]
        );

        $thien_can_data = $data["thien_can"];
        $nhat_can_element = $thien_can_data[$tu_tru["ngay"]["can"]]["element"];

        $vuong_suy = self::tinh_vuong_suy($nhat_can_element, $tu_tru["thang"]["chi"], $data);

        // Tính Thân vượng/nhược và Dụng Thần
        $than_info = self::tinh_than_vuong_nhuoc($tu_tru, $data);
        $dung_than_info = self::tinh_dung_than($tu_tru, $than_info, $data);

        $is_thuan_nam = ($thien_can_data[$tu_tru["nam"]["can"]]["polarity"] === "+" && $normalized["gioi_tinh"] === "nam") ||
            ($thien_can_data[$tu_tru["nam"]["can"]]["polarity"] === "-" && $normalized["gioi_tinh"] === "nu");

        $tieu_van = self::tinh_tieu_van($tu_tru["gio"]["can"], $tu_tru["gio"]["chi"], $is_thuan_nam, $dai_van["tuoi_khoi_van"], $data["can_chi_60"]);

        $am_lich = self::format_lunar_date($normalized["ngay_sinh"]);

        return [
            "thong_tin" => array_merge($normalized, [
                'am_lich' => !empty($am_lich) ? $am_lich : '',
                'tiet_khi_hien_tai' => !empty($thoi_gian_cung['tiet_khi_hien_tai']) ? $thoi_gian_cung['tiet_khi_hien_tai'] : '',
                'tiet_start' => !empty($thoi_gian_cung['tiet_start']) ? $thoi_gian_cung['tiet_start'] : '',
                'tiet_end' => !empty($thoi_gian_cung['tiet_end']) ? $thoi_gian_cung['tiet_end'] : ''
            ]),
            "tu_tru" => $tu_tru,
            "dai_van" => $dai_van,
            "tieu_van" => $tieu_van,
            "vuong_suy_nhat_chu" => $vuong_suy,
            "than_vuong_nhuoc" => $than_info,
            "dung_than" => $dung_than_info
        ];
    }

    private static function chuan_hoa_dau_vao($input, $gio_dia_chi) {
        $ngay_sinh = trim($input["ngay_sinh"]);
        $gio_sinh = trim($input["gio_sinh"]);
        $gioi_tinh = $input["gioi_tinh"] ?? "nam";
        $ho_ten = $input["ho_ten"] ?? "Đương Số";

        $gio_val = (int) str_replace(":", "", $gio_sinh);
        $gio_id = "ty";
        $is_late_night = false;

        foreach ($gio_dia_chi as $khung_gio) {
            $start = (int) str_replace(":", "", $khung_gio["start"]);
            $end = (int) str_replace(":", "", $khung_gio["end"]);

            if ($start > $end) {
                if ($gio_val >= $start || $gio_val <= $end) {
                    $gio_id = $khung_gio["id"];
                    $is_late_night = $khung_gio["is_late_night"] ?? false;
                    break;
                }
            } else {
                if ($gio_val >= $start && $gio_val <= $end) {
                    $gio_id = $khung_gio["id"];
                    $is_late_night = $khung_gio["is_late_night"] ?? false;
                    break;
                }
            }
        }

        return [
            "ngay_sinh" => $ngay_sinh,
            "gio_sinh" => $gio_sinh,
            "gio_id" => $gio_id,
            "is_late_night" => $is_late_night,
            "gioi_tinh" => $gioi_tinh,
            "ho_ten" => $ho_ten,
        ];
    }

    private static function tinh_toan_thien_van( $normalized, $tiet_khi, $thang_tiet_khi ) {
        $ngay_sinh = $normalized["ngay_sinh"];
        $gio_sinh = $normalized["gio_sinh"] ?? '00:00';
        $parts = explode("-", $ngay_sinh);
        $year = (int) $parts[0];
        $month = (int) $parts[1];
        $day = (int) $parts[2];
        $gio_parts = explode(':', $gio_sinh);
        $hour_fraction = ((int)($gio_parts[0] ?? 0) * 60 + (int)($gio_parts[1] ?? 0)) / 1440.0;
        $utc_offset = 7.0 / 24.0;

        $jdn_int = self::gregorian_to_jdn($year, $month, $day);
        $jdn = $jdn_int - 0.5 + $hour_fraction - $utc_offset;

        $can_keys = [ "giap", "at", "binh", "dinh", "mau", "ky", "canh", "tan", "nham", "quy", ];
        $chi_keys = [ "ty", "suu", "dan", "mao", "thin", "ti", "ngo", "mui", "than", "dau", "tuat", "hoi", ];

        $can_ngay_idx = ($jdn_int + 9) % 10;
        $chi_ngay_idx = ($jdn_int + 1) % 12;
        $can_ngay = $can_keys[$can_ngay_idx];
        $chi_ngay = $chi_keys[$chi_ngay_idx];

        $nam_tiet = $year;
        $lap_xuan_jdn = self::tinh_jdn_tiet_khi($year, 315);
        if ($jdn < $lap_xuan_jdn) {
            $nam_tiet = $year - 1;
        }
        $can_nam_idx = ($nam_tiet - 4) % 10;
        if ($can_nam_idx < 0) {
            $can_nam_idx += 10;
        }
        $chi_nam_idx = ($nam_tiet - 4) % 12;
        if ($chi_nam_idx < 0) {
            $chi_nam_idx += 12;
        }
        $can_nam = $can_keys[$can_nam_idx];
        $chi_nam = $chi_keys[$chi_nam_idx];

        $tiet_lon_map = [];
        foreach ($tiet_khi as $tk) {
            if ($tk["type"] === "tiet") {
                $tiet_lon_map[$tk["id"]] = $tk["solar_longitude"];
            }
        }

        $all_tiets = [];
        foreach ([$year - 1, $year, $year + 1] as $y) {
            foreach ($tiet_lon_map as $id => $lon) {
                $t_jdn = self::tinh_jdn_tiet_khi($y, $lon);
                $all_tiets[] = [
                    "id" => $id,
                    "lon" => $lon,
                    "jdn" => $t_jdn,
                    "year" => $y,
                ];
            }
        }
        usort($all_tiets, fn($a, $b) => $a["jdn"] <=> $b["jdn"]);

        $start_term_to_month = [];
        foreach ($thang_tiet_khi as $ttk) {
            $start_term_to_month[$ttk["start_term"]] = $ttk["month_branch"];
        }

        $chi_thang = "suu";
        $tiet_truoc_jdn = $lap_xuan_jdn;
        $tiet_tiep_jdn = $lap_xuan_jdn;
        $tiet_khi_hien_tai = '';

        for ($i = 0; $i < count($all_tiets) - 1; $i++) {
            $cur = $all_tiets[$i];
            $nxt = $all_tiets[$i + 1];
            if ($jdn >= $cur["jdn"] && $jdn < $nxt["jdn"]) {
                if (isset($start_term_to_month[$cur["id"]])) {
                    $chi_thang = $start_term_to_month[$cur["id"]];
                }
                $tiet_truoc_jdn = $cur["jdn"];
                $tiet_tiep_jdn = $nxt["jdn"];

                foreach ($tiet_khi as $tk) {
                    if ($tk['id'] === $cur['id']) {
                        $tiet_khi_hien_tai = $tk['name'];
                        break;
                    }
                }
                break;
            }
        }

        $so_ngay_den_tiet_tiep_theo = $tiet_tiep_jdn - $jdn;
        $so_ngay_tu_tiet_truoc = $jdn - $tiet_truoc_jdn;

        if ($so_ngay_den_tiet_tiep_theo < 0) {
            $so_ngay_den_tiet_tiep_theo = 0;
        }
        if ($so_ngay_tu_tiet_truoc < 0) {
            $so_ngay_tu_tiet_truoc = 0;
        }

        return [
            "can_nam" => $can_nam,
            "chi_nam" => $chi_nam,
            "chi_thang" => $chi_thang,
            "can_ngay" => $can_ngay,
            "chi_ngay" => $chi_ngay,
            "so_ngay_den_tiet_tiep_theo" => $so_ngay_den_tiet_tiep_theo,
            "so_ngay_tu_tiet_truoc" => $so_ngay_tu_tiet_truoc,
            "tiet_khi_hien_tai" => $tiet_khi_hien_tai ?: 'Không xác định',
            "tiet_start" => self::jdn_to_date_string($tiet_truoc_jdn),
            "tiet_end" => self::jdn_to_date_string($tiet_tiep_jdn),
            "tiet_truoc_jdn_float" => $tiet_truoc_jdn,
            "jdn_float" => $jdn,
        ];
    }

    private static function jdn_to_date_string($jdn) {
        $jdn = (int) floor($jdn);
        $l = $jdn + 68569;
        $n = intdiv(4 * $l, 146097);
        $l = $l - intdiv(146097 * $n + 3, 4);
        $i = intdiv(4000 * ($l + 1), 1461001);
        $l = $l - intdiv(1461 * $i, 4) + 31;
        $j = intdiv(80 * $l, 2447);
        $d = $l - intdiv(2447 * $j, 80);
        $l = intdiv($j, 11);
        $m = $j + 2 - (12 * $l);
        $y = 100 * ($n - 49) + $i + $l;
        return sprintf("%02d/%02d/%04d", $d, $m, $y);
    }

    private static function gregorian_to_jdn($year, $month, $day){
        $a = intdiv(14 - $month, 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;
        return $day + intdiv(153 * $m + 2, 5) + 365 * $y + intdiv($y, 4) - intdiv($y, 100) + intdiv($y, 400) - 32045;
    }

    private static function tinh_jdn_tiet_khi($year, $target_longitude) {
        $days_per_degree = 365.2422 / 360.0;
        $jan1_jdn = self::gregorian_to_jdn($year, 1, 1);
        $jan1_lon = self::solar_longitude($jan1_jdn);

        $delta = $target_longitude - $jan1_lon;
        if ($delta < 0) {
            $delta += 360;
        }

        $est_jdn = $jan1_jdn + $delta * $days_per_degree;

        for ($i = 0; $i < 20; $i++) {
            $lon = self::solar_longitude($est_jdn);
            $diff = $target_longitude - $lon;
            if ($diff > 180) {
                $diff -= 360;
            }
            if ($diff < -180) {
                $diff += 360;
            }
            if (abs($diff) < 0.0001) {
                break;
            }
            $est_jdn += $diff * $days_per_degree;
        }

        return $est_jdn;
    }

    private static function solar_longitude($jdn){
        $T = ($jdn - 2451545.0) / 36525.0;
        $L0 = 280.46646 + 36000.76983 * $T + 0.0003032 * $T * $T;
        $M = 357.52911 + 35999.05029 * $T - 0.0001537 * $T * $T;
        $M_rad = deg2rad($M);
        $C =
            (1.914602 - 0.004817 * $T - 0.000014 * $T * $T) * sin($M_rad) +
            (0.019993 - 0.000101 * $T) * sin(2 * $M_rad) +
            0.000289 * sin(3 * $M_rad);
        $theta = $L0 + $C;
        $omega = 125.04 - 1934.136 * $T;
        $lambda = $theta - 0.00569 - 0.00478 * sin(deg2rad($omega));
        $lambda = fmod($lambda, 360.0);
        if ($lambda < 0) {
            $lambda += 360.0;
        }
        return $lambda;
    }

    private static function lap_tu_tru($normalized, $thoi_gian, $data) {
        $gio_chi_key = str_replace("da_", "", $normalized["gio_id"]);
        $tru = [
            "nam" => [
                "can" => $thoi_gian["can_nam"],
                "chi" => $thoi_gian["chi_nam"],
            ],
            "thang" => ["chi" => $thoi_gian["chi_thang"]],
            "ngay" => [
                "can" => $thoi_gian["can_ngay"],
                "chi" => $thoi_gian["chi_ngay"],
            ],
            "gio" => [
                "chi" => $data["dia_chi"][$gio_chi_key]["name"] ?? $gio_chi_key,
            ],
        ];

        $can_nam_id = $thoi_gian["can_nam"];
        $can_thang_dau = $data["nam_thang_can_quy_tac"][$can_nam_id];

        $chi_thang_index = array_search(
            $thoi_gian["chi_thang"],
            array_keys($data["dia_chi"])
        );
        $chi_dan_index = array_search("dan", array_keys($data["dia_chi"]));
        $offset_thang = $chi_thang_index - $chi_dan_index;
        if ($offset_thang < 0) {
            $offset_thang += 12;
        }

        $can_keys = array_keys($data["thien_can"]);
        $can_thang_dau_index = array_search($can_thang_dau, $can_keys);
        $can_thang_index = ($can_thang_dau_index + $offset_thang) % 10;
        $tru["thang"]["can"] = $can_keys[$can_thang_index];

        $can_ngay_tinh_gio = $thoi_gian["can_ngay"];
        if ($normalized["is_late_night"]) {
            $can_ngay_index = array_search($can_ngay_tinh_gio, $can_keys);
            $can_ngay_tinh_gio = $can_keys[($can_ngay_index + 1) % 10];
        }

        $can_gio_dau = $data["gio_can_quy_tac"][$can_ngay_tinh_gio];
        $chi_gio_index = array_search(
            str_replace("da_", "", $normalized["gio_id"]),
            array_keys($data["dia_chi"])
        );
        $chi_ty_index = array_search("ty", array_keys($data["dia_chi"]));
        $offset_gio = $chi_gio_index - $chi_ty_index;
        if ($offset_gio < 0) {
            $offset_gio += 12;
        }

        $can_gio_dau_index = array_search($can_gio_dau, $can_keys);
        $can_gio_index = ($can_gio_dau_index + $offset_gio) % 10;
        $tru["gio"]["can"] = $can_keys[$can_gio_index];
        $tru["gio"]["chi"] = str_replace("da_", "", $normalized["gio_id"]);

        return $tru;
    }

    private static function tinh_thap_than( $tu_tru, $thap_than_data, $thien_can_data ) {
        $nhat_can = $tu_tru["ngay"]["can"];
        $nhat_can_element = $thien_can_data[$nhat_can]["element"];
        $nhat_can_polarity = $thien_can_data[$nhat_can]["polarity"];

        foreach (["nam", "thang", "gio"] as $tru) {
            $can_xet = $tu_tru[$tru]["can"];
            $element_xet = $thien_can_data[$can_xet]["element"];
            $polarity_xet = $thien_can_data[$can_xet]["polarity"];

            $polarity_type =
                $nhat_can_polarity === $polarity_xet ? "same" : "diff";
            $relation_type = self::xac_dinh_sinh_khac(
                $nhat_can_element,
                $element_xet
            );

            foreach ($thap_than_data as $tt) {
                if (
                    $tt["type"] === $relation_type &&
                    $tt["polarity"] === $polarity_type
                ) {
                    $tu_tru[$tru]["thap_than_can"] = $tt["id"];
                    break;
                }
            }
        }
        $tu_tru["ngay"]["thap_than_can"] = "nhat_chu";
        return $tu_tru;
    }

    public static function tinh_thap_than_can( $nhat_can, $can_xet, $thien_can_data, $thap_than_data ) {
        $nhat_can_el = $thien_can_data[$nhat_can]["element"];
        $nhat_can_pol = $thien_can_data[$nhat_can]["polarity"];
        $xet_el = $thien_can_data[$can_xet]["element"];
        $xet_pol = $thien_can_data[$can_xet]["polarity"];

        $pol_type = $nhat_can_pol === $xet_pol ? "same" : "diff";
        $rel_type = self::xac_dinh_sinh_khac($nhat_can_el, $xet_el);

        foreach ($thap_than_data as $tt) {
            if ($tt["type"] === $rel_type && $tt["polarity"] === $pol_type) {
                return $tt["id"];
            }
        }
        return "";
    }

    private static function xac_dinh_sinh_khac($nhat_can_el, $target_el) {
        if ($nhat_can_el === $target_el) {
            return "same_element";
        }
        $sinh_map = [ "kim" => "thuy", "thuy" => "moc", "moc" => "hoa", "hoa" => "tho", "tho" => "kim", ];
        $khac_map = [ "kim" => "moc", "moc" => "tho", "tho" => "thuy", "thuy" => "hoa", "hoa" => "kim", ];

        if (($sinh_map[$nhat_can_el] ?? "") === $target_el) {
            return "generate";
        }
        if (($sinh_map[$target_el] ?? "") === $nhat_can_el) {
            return "generated_by";
        }
        if (($khac_map[$nhat_can_el] ?? "") === $target_el) {
            return "control";
        }
        if (($khac_map[$target_el] ?? "") === $nhat_can_el) {
            return "controlled_by";
        }
        return "unknown";
    }

    private static function tinh_truong_sinh_than_sat( $tu_tru, $truong_sinh_data, $than_sat_data ) {
        $nhat_can = $tu_tru["ngay"]["can"];
        $nhat_kien_chi = $tu_tru["ngay"]["chi"];
        $nguyet_kien_chi = $tu_tru["thang"]["chi"];
        $can_nam = $tu_tru["nam"]["can"];
        $chi_nam = $tu_tru["nam"]["chi"];

        foreach (["nam", "thang", "ngay", "gio"] as $tru) {
            $can = $tu_tru[$tru]["can"];
            $chi = $tu_tru[$tru]["chi"];

            // Trường Sinh: tra theo Nhật Can đối với Địa Chi mỗi trụ
            $chi_index = array_search($chi, $truong_sinh_data["map"][$nhat_can]);
            $tu_tru[$tru]["truong_sinh"] =
                $truong_sinh_data["states"][$chi_index];

            // Nhật Kiến
            $nk_index = array_search(
                $nhat_kien_chi,
                $truong_sinh_data["map"][$can]
            );
            $tu_tru[$tru]["nhat_kien"] = $truong_sinh_data["states"][$nk_index];

            // Nguyệt Kiến
            $mk_index = array_search(
                $nguyet_kien_chi,
                $truong_sinh_data["map"][$can]
            );
            $tu_tru[$tru]["nguyet_kien"] =
                $truong_sinh_data["states"][$mk_index];

            $tu_tru[$tru]["than_sat"] = [];
            foreach ($than_sat_data as $id => $ts) {
                $base = $ts["base"] ?? '';
                $map = $ts["map"] ?? [];
                $matched = false;

                if ($base === "can_ngay" || $base === "can_ngay_nam") {
                    if (isset($map[$nhat_can])) {
                        $val = $map[$nhat_can];
                        $matched = (is_array($val) && in_array($chi, $val)) || $val === $chi;
                    }
                } elseif ($base === "chi_ngay_nam") {
                    if (isset($map[$chi_nam])) {
                        $val = $map[$chi_nam];
                        $matched = (is_array($val) && in_array($chi, $val)) || $val === $chi;
                    }
                } elseif ($base === "can_chi_ngay_nam") {
                    $can_chi_nam = $can_nam . "_" . $chi_nam;
                    if (isset($map[$can_chi_nam])) {
                        $val = $map[$can_chi_nam];
                        $matched = (is_array($val) && in_array($chi, $val)) || $val === $chi;
                    }
                } elseif ($base === "chi_nam") {
                    if (isset($map[$chi_nam])) {
                        $val = $map[$chi_nam];
                        $matched = (is_array($val) && in_array($chi, $val)) || $val === $chi;
                    }
                } elseif ($base === "thang_chi") {
                    if (isset($map[$nguyet_kien_chi])) {
                        $val = $map[$nguyet_kien_chi];
                        $matched = (is_array($val) && in_array($can, $val)) || $val === $can;
                    }
                }

                if ($matched) {
                    $tu_tru[$tru]["than_sat"][] = $id;
                }
            }
        }
        return $tu_tru;
    }

    private static function tinh_dai_van( $can_nam, $gioi_tinh, $tru_thang, $thoi_gian, $data, $ngay_sinh, $nhat_can ) {
        $thien_can_data = $data["thien_can"];
        $can_chi_60 = $data["can_chi_60"];
        $thap_than_data = $data["thap_than"];

        $polarity = $thien_can_data[$can_nam]["polarity"];
        $is_thuan =
            ($polarity === "+" && $gioi_tinh === "nam") ||
            ($polarity === "-" && $gioi_tinh === "nu");

        $so_ngay = $is_thuan
            ? $thoi_gian["so_ngay_den_tiet_tiep_theo"]
            : $thoi_gian["so_ngay_tu_tiet_truoc"];


        $nam_khoi = (int) floor($so_ngay / 3);
        $thang_khoi = (int) round(($so_ngay / 3 - $nam_khoi) * 12);

        if ($nam_khoi < 1 && $thang_khoi == 0) {
            $nam_khoi = 1;
        }

        $thoi_gian_khoi_van = $nam_khoi . ' tuổi ' . $thang_khoi . ' tháng';
        $tuoi_khoi_van = $thang_khoi >= 6 ? $nam_khoi + 1 : $nam_khoi;

        $nam_sinh_dl = (int) explode("-", $ngay_sinh)[0];
        $can_chi_thang = $tru_thang["can"] . "_" . $tru_thang["chi"];
        $thang_index = array_search(
            $can_chi_thang,
            array_column($can_chi_60, "id")
        );

        $dai_van = [];
        $step = $is_thuan ? 1 : -1;

        $can_keys = [
            1 => "giap",
            2 => "at",
            3 => "binh",
            4 => "dinh",
            5 => "mau",
            6 => "ky",
            7 => "canh",
            8 => "tan",
            9 => "nham",
            10 => "quy",
        ];
        $chi_keys = [
            1 => "ty",
            2 => "suu",
            3 => "dan",
            4 => "mao",
            5 => "thin",
            6 => "ti",
            7 => "ngo",
            8 => "mui",
            9 => "than",
            10 => "dau",
            11 => "tuat",
            12 => "hoi",
        ];

        for ($i = 1; $i <= 10; $i++) {
            $thang_index += $step;
            if ($thang_index > 59) {
                $thang_index -= 60;
            }
            if ($thang_index < 0) {
                $thang_index += 60;
            }

            $tuoi_dv = $tuoi_khoi_van + ($i - 1) * 10;
            $nam_bat_dau = $nam_sinh_dl + $nam_khoi + ($i - 1) * 10;

            $dv_can = $can_chi_60[$thang_index]["can"];
            $dv_chi = $can_chi_60[$thang_index]["chi"];

            $tieu_van = [];
            for ($j = 0; $j < 10; $j++) {
                $nam_ln = $nam_bat_dau + $j;
                $can_ln_idx = ($nam_ln - 3) % 10;
                if ($can_ln_idx <= 0) {
                    $can_ln_idx += 10;
                }
                $chi_ln_idx = ($nam_ln - 3) % 12;
                if ($chi_ln_idx <= 0) {
                    $chi_ln_idx += 12;
                }

                $can_ln = $can_keys[$can_ln_idx];
                $chi_ln = $chi_keys[$chi_ln_idx];

                $tt_ln_id = self::tinh_thap_than_can(
                    $nhat_can,
                    $can_ln,
                    $thien_can_data,
                    $thap_than_data
                );

                $tieu_van[] = [
                    "nam" => $nam_ln,
                    "can" => $can_ln,
                    "chi" => $chi_ln,
                    "thap_than" => $tt_ln_id,
                ];
            }

            $dai_van[] = [
                "tuoi" => $tuoi_dv,
                "nam_bat_dau" => $nam_bat_dau,
                "can" => $dv_can,
                "chi" => $dv_chi,
                "tieu_van" => $tieu_van,
            ];
        }

        return [
            "tuoi_khoi_van" => $tuoi_khoi_van,
            "thoi_gian_khoi_van" => $thoi_gian_khoi_van,
            "chieu_hanh_van" => $is_thuan ? "thuận" : "nghịch",
            "van_trinh" => $dai_van,
        ];
    }

    private static function jd_from_date(int $dd, int $mm, int $yy): int {
        $a = intdiv(14 - $mm, 12);
        $y = $yy + 4800 - $a;
        $m = $mm + 12 * $a - 3;
        $jd =
            $dd +
            intdiv(153 * $m + 2, 5) +
            365 * $y +
            intdiv($y, 4) -
            intdiv($y, 100) +
            intdiv($y, 400) -
            32045;
        if ($jd < 2299161) {
            $jd =
                $dd +
                intdiv(153 * $m + 2, 5) +
                365 * $y +
                intdiv($y, 4) -
                32083;
        }
        return (int) $jd;
    }

    private static function new_moon(int $k): int {
        $T = $k / 1236.85;
        $T2 = $T * $T;
        $T3 = $T2 * $T;
        $dr = M_PI / 180.0;

        $Jd1 = 2415020.75933 + 29.53058868 * $k + 0.0001178 * $T2 - 0.000000155 * $T3;
        $Jd1 += 0.00033 * sin((166.56 + 132.87 * $T - 0.009173 * $T2) * $dr);
        $M = 359.2242 + 29.10535608 * $k - 0.0000333 * $T2 - 0.00000347 * $T3;
        $Mpr = 306.0253 + 385.81691806 * $k + 0.0107306 * $T2 + 0.00001236 * $T3;
        $F = 21.2964 + 390.67050646 * $k - 0.0016528 * $T2 - 0.00000239 * $T3;

        $C1 =
            (0.1734 - 0.000393 * $T) * sin($M * $dr) +
            0.0021 * sin(2 * $dr * $M) -
            0.4068 * sin($Mpr * $dr) +
            0.0161 * sin(2 * $dr * $Mpr) -
            0.0004 * sin(3 * $dr * $Mpr) +
            0.0104 * sin(2 * $dr * $F) -
            0.0051 * sin(($M + $Mpr) * $dr) -
            0.0074 * sin(($M - $Mpr) * $dr) +
            0.0004 * sin((2 * $F + $M) * $dr) -
            0.0004 * sin((2 * $F - $M) * $dr) -
            0.0006 * sin((2 * $F + $Mpr) * $dr) +
            0.0010 * sin((2 * $F - $Mpr) * $dr) +
            0.0005 * sin((2 * $Mpr + $M) * $dr);

        if ($T < -11) {
            $deltaT = 0.001 + 0.000839 * $T + 0.0002261 * $T2 - 0.00000845 * $T3 - 0.000000081 * $T * $T3;
        } else {
            $deltaT = -0.000278 + 0.000265 * $T + 0.000262 * $T2;
        }

        return (int) floor($Jd1 + $C1 - $deltaT);
    }

    private static function sun_longitude(int $jdn): int {
        $T = ($jdn - 2451545.0) / 36525.0;
        $T2 = $T * $T;
        $dr = M_PI / 180.0;

        $M = 357.52910 + 35999.05030 * $T - 0.0001559 * $T2 - 0.00000048 * $T * $T2;
        $L0 = 280.46645 + 36000.76983 * $T + 0.0003032 * $T2;
        $DL =
            (1.914600 - 0.004817 * $T - 0.000014 * $T2) * sin($M * $dr) +
            (0.019993 - 0.000101 * $T) * sin(2 * $dr * $M) +
            0.000290 * sin(3 * $dr * $M);

        $L = $L0 + $DL;
        $L = $L - 360.0 * floor($L / 360.0);

        return (int) floor($L / 30.0);
    }

    private static function get_new_moon_day(int $k, float $timeZone): int{
        return (int) floor(self::new_moon($k) + 0.5 + $timeZone / 24.0);
    }

    private static function get_sun_longitude(int $dayNumber, float $timeZone): int{
        return self::sun_longitude((int) floor($dayNumber - 0.5 - $timeZone / 24.0));
    }

    private static function get_lunar_month11(int $yy, float $timeZone): int{
        $off = self::jd_from_date(31, 12, $yy) - 2415021;
        $k = (int) floor($off / 29.530588853);
        $nm = self::get_new_moon_day($k, $timeZone);
        $sunLong = self::get_sun_longitude($nm, $timeZone);
        if ($sunLong >= 9) {
            $nm = self::get_new_moon_day($k - 1, $timeZone);
        }
        return $nm;
    }

    private static function get_leap_month_offset(int $a11, float $timeZone): int{
        $k = (int) floor(0.5 + ($a11 - 2415021.076998695) / 29.530588853);
        $last = 0;
        $i = 1;
        $arc = self::get_sun_longitude(self::get_new_moon_day($k + $i, $timeZone), $timeZone);
        do {
            $last = $arc;
            $i++;
            $arc = self::get_sun_longitude(self::get_new_moon_day($k + $i, $timeZone), $timeZone);
        } while ($arc !== $last && $i < 14);
        return $i - 1;
    }

    private static function solar_to_lunar(int $dd, int $mm, int $yy, float $timeZone = 7.0): array{
        $dayNumber = self::jd_from_date($dd, $mm, $yy);
        $k = (int) floor(($dayNumber - 2415021.076998695) / 29.530588853);
        $monthStart = self::get_new_moon_day($k + 1, $timeZone);
        if ($monthStart > $dayNumber) {
            $monthStart = self::get_new_moon_day($k, $timeZone);
        }

        $a11 = self::get_lunar_month11($yy, $timeZone);
        $b11 = $a11;
        if ($a11 >= $monthStart) {
            $lunarYear = $yy;
            $a11 = self::get_lunar_month11($yy - 1, $timeZone);
        } else {
            $lunarYear = $yy + 1;
            $b11 = self::get_lunar_month11($yy + 1, $timeZone);
        }

        $lunarDay = $dayNumber - $monthStart;
        $diff = (int) floor(($monthStart - $a11) / 29);
        $lunarLeap = 0;
        $lunarMonth = $diff + 11;

        if ($b11 - $a11 > 365) {
            $leapMonthDiff = self::get_leap_month_offset($a11, $timeZone);
            if ($diff >= $leapMonthDiff) {
                $lunarMonth = $diff + 10;
                if ($diff === $leapMonthDiff) {
                    $lunarLeap = 1;
                }
            }
        }

        if ($lunarMonth > 12) {
            $lunarMonth -= 12;
        }
        if ($lunarMonth >= 11 && $diff < 4) {
            $lunarYear -= 1;
        }

        return [$lunarDay, $lunarMonth, $lunarYear, $lunarLeap];
    }

    public static function format_lunar_date(string $solarDate): string{
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $solarDate, $matches)) {
            return '';
        }
        [$day, $month, $year] = self::solar_to_lunar(
            (int) $matches[3],
            (int) $matches[2],
            (int) $matches[1]
        );
        return sprintf('%02d/%02d/%04d', $day, $month, $year);
    }

    private static function tinh_tieu_van($can_gio, $chi_gio, $is_thuan, $tuoi_khoi_van, $can_chi_60){
        $can_chi_id = $can_gio . '_' . $chi_gio;
        $gio_idx = array_search($can_chi_id, array_column($can_chi_60, 'id'));
        if ($gio_idx === false) $gio_idx = 0;

        $step = $is_thuan ? 1 : -1;
        $tieu_van = [];

        for ($i = 1; $i <= $tuoi_khoi_van; $i++) {
            $gio_idx += $step;
            if ($gio_idx > 59) $gio_idx -= 60;
            if ($gio_idx < 0) $gio_idx += 60;

            $tieu_van[] = [
                'tuoi' => $i,
                'can' => $can_chi_60[$gio_idx]['can'],
                'chi' => $can_chi_60[$gio_idx]['chi'],
                'name' => $can_chi_60[$gio_idx]['name']
            ];
        }
        return $tieu_van;
    }

    private static function tinh_vuong_suy($nhat_can_element, $chi_thang, $data){
        $mua_sinh = '';
        foreach ($data['mua_chi'] as $mua => $cac_chi) {
            if (in_array($chi_thang, $cac_chi)) {
                $mua_sinh = $mua;
                break;
            }
        }

        if (!$mua_sinh) return 'Không xác định';

        $trang_thai = $data['vuong_suy'][$mua_sinh][$nhat_can_element] ?? '';
        $map_trang_thai = [
            'vuong' => 'Vượng', 'tuong' => 'Tướng',
            'huu' => 'Hưu', 'tu' => 'Tù', 'tu_state' => 'Tử'
        ];

        return $map_trang_thai[$trang_thai] ?? $trang_thai;
    }

    public static function tinh_than_vuong_nhuoc($tu_tru, $data) {
        $thien_can = $data['thien_can'];
        $dia_chi = $data['dia_chi'];
        $truong_sinh = $data['truong_sinh_12'];
        $ngu_hanh = $data['ngu_hanh'];

        $nhat_can = $tu_tru['ngay']['can'];
        $nhat_can_info = $thien_can[$nhat_can];
        $nhat_can_el = $nhat_can_info['element'];
        $nhat_can_pol = $nhat_can_info['polarity'];
        $is_duong_can = ($nhat_can_pol === '+');

        $chi_thang = $tu_tru['thang']['chi'];

        $diem = 0;
        $chi_tiet = [];

        // 1. ĐƯỢC LỆNH (lệnh tháng) - 40 điểm
        $mua_sinh = '';
        foreach ($data['mua_chi'] as $mua => $cac_chi) {
            if (in_array($chi_thang, $cac_chi)) {
                $mua_sinh = $mua;
                break;
            }
        }
        $vuong_suy_state = $data['vuong_suy'][$mua_sinh][$nhat_can_el] ?? '';
        if ($vuong_suy_state === 'vuong') {
            $diem += 40;
            $chi_tiet['duoc_lenh'] = ['co' => true, 'diem' => 40, 'mo_ta' => 'Vượng tại lệnh tháng'];
        } elseif ($vuong_suy_state === 'tuong') {
            $diem += 30;
            $chi_tiet['duoc_lenh'] = ['co' => true, 'diem' => 30, 'mo_ta' => 'Tướng tại lệnh tháng'];
        } else {
            $chi_tiet['duoc_lenh'] = ['co' => false, 'diem' => 0, 'mo_ta' => 'Thất lệnh'];
        }

        // 2. ĐẮC ĐỊA - 20 điểm
        // Kiểm tra Nhật Can có Lộc, Đế Vượng, Trường Sinh, Mộ trong 4 địa chi
        $dac_dia_states = $is_duong_can
            ? ['lam_quan', 'de_vuong', 'truong_sinh', 'mo']  // Dương can: Lộc=Lâm quan, Đế vượng, Trường sinh, Mộ
            : ['lam_quan', 'de_vuong', 'mo'];                 // Âm can: Lộc=Lâm quan, Đế vượng, Mộ (không tính Trường sinh)

        $dac_dia_count = 0;
        $ts_map = $truong_sinh['map'][$nhat_can] ?? [];
        foreach (['nam', 'thang', 'ngay', 'gio'] as $tru_key) {
            $chi = $tu_tru[$tru_key]['chi'];
            $chi_idx = array_search($chi, $ts_map);
            if ($chi_idx !== false) {
                $state = $truong_sinh['states'][$chi_idx] ?? '';
                if (in_array($state, $dac_dia_states)) {
                    $dac_dia_count++;
                }
            }
        }
        if ($dac_dia_count >= 2) {
            $diem += 20;
            $chi_tiet['dac_dia'] = ['co' => true, 'diem' => 20, 'mo_ta' => "Đắc địa tại {$dac_dia_count} trụ"];
        } elseif ($dac_dia_count === 1) {
            $diem += 10;
            $chi_tiet['dac_dia'] = ['co' => true, 'diem' => 10, 'mo_ta' => 'Đắc địa tại 1 trụ'];
        } else {
            $chi_tiet['dac_dia'] = ['co' => false, 'diem' => 0, 'mo_ta' => 'Không đắc địa'];
        }

        // 3. ĐƯỢC SINH (Ấn Tinh) - 20 điểm
        // Hành sinh cho Nhật Chủ
        $hanh_sinh = $ngu_hanh[$nhat_can_el]['sinh'] ?? '';
        $hanh_sinh_cho_nhat = '';
        foreach ($ngu_hanh as $h_id => $h_info) {
            if (($h_info['sinh'] ?? '') === $nhat_can_el) {
                $hanh_sinh_cho_nhat = $h_id;
                break;
            }
        }

        $sinh_count = 0;
        // Đếm Thiên Can sinh Nhật Chủ (trừ trụ ngày)
        foreach (['nam', 'thang', 'gio'] as $tru_key) {
            $can = $tu_tru[$tru_key]['can'];
            $can_el = $thien_can[$can]['element'] ?? '';
            if ($can_el === $hanh_sinh_cho_nhat) {
                $sinh_count++;
            }
        }
        // Đếm Tàng Can sinh Nhật Chủ trong 4 địa chi
        foreach (['nam', 'thang', 'ngay', 'gio'] as $tru_key) {
            $chi = $tu_tru[$tru_key]['chi'];
            $tang_can = $dia_chi[$chi]['tang_can'] ?? [];
            foreach ($tang_can as $tc_id => $tc_pct) {
                $tc_el = $thien_can[$tc_id]['element'] ?? '';
                if ($tc_el === $hanh_sinh_cho_nhat && $tc_pct >= 30) {
                    $sinh_count++;
                }
            }
        }
        if ($sinh_count >= 3) {
            $diem += 20;
            $chi_tiet['duoc_sinh'] = ['co' => true, 'diem' => 20, 'mo_ta' => "Có {$sinh_count} Ấn Tinh"];
        } elseif ($sinh_count >= 1) {
            $diem += 10;
            $chi_tiet['duoc_sinh'] = ['co' => true, 'diem' => 10, 'mo_ta' => "Có {$sinh_count} Ấn Tinh"];
        } else {
            $chi_tiet['duoc_sinh'] = ['co' => false, 'diem' => 0, 'mo_ta' => 'Không có Ấn Tinh'];
        }

        // 4. ĐƯỢC TRỢ (Tỷ Kiếp) - 20 điểm
        $tro_count = 0;
        // Đếm Thiên Can cùng hành với Nhật Chủ (trừ trụ ngày)
        foreach (['nam', 'thang', 'gio'] as $tru_key) {
            $can = $tu_tru[$tru_key]['can'];
            $can_el = $thien_can[$can]['element'] ?? '';
            if ($can_el === $nhat_can_el) {
                $tro_count++;
            }
        }
        // Đếm Tàng Can cùng hành trong 4 địa chi
        foreach (['nam', 'thang', 'ngay', 'gio'] as $tru_key) {
            $chi = $tu_tru[$tru_key]['chi'];
            $tang_can = $dia_chi[$chi]['tang_can'] ?? [];
            foreach ($tang_can as $tc_id => $tc_pct) {
                $tc_el = $thien_can[$tc_id]['element'] ?? '';
                if ($tc_el === $nhat_can_el && $tc_pct >= 30) {
                    $tro_count++;
                }
            }
        }
        if ($tro_count >= 3) {
            $diem += 20;
            $chi_tiet['duoc_tro'] = ['co' => true, 'diem' => 20, 'mo_ta' => "Có {$tro_count} Tỷ Kiếp"];
        } elseif ($tro_count >= 1) {
            $diem += 10;
            $chi_tiet['duoc_tro'] = ['co' => true, 'diem' => 10, 'mo_ta' => "Có {$tro_count} Tỷ Kiếp"];
        } else {
            $chi_tiet['duoc_tro'] = ['co' => false, 'diem' => 0, 'mo_ta' => 'Không có Tỷ Kiếp'];
        }

        // Phán định Thân vượng/nhược
        // Tổng điểm tối đa: 100
        // >= 50: Thân vượng, < 50: Thân nhược
        $than_vuong = $diem >= 50;
        $muc_do = '';
        if ($diem >= 70) $muc_do = 'cực vượng';
        elseif ($diem >= 50) $muc_do = 'vượng';
        elseif ($diem >= 30) $muc_do = 'nhược';
        else $muc_do = 'cực nhược';

        return [
            'than_vuong' => $than_vuong,
            'muc_do' => $muc_do,
            'diem' => $diem,
            'chi_tiet' => $chi_tiet,
        ];
    }

    public static function tinh_dung_than($tu_tru, $than_info, $data) {
        $thien_can = $data['thien_can'];
        $ngu_hanh = $data['ngu_hanh'];
        $sinh_vuong_xuat_tu = $data['sinh_vuong_xuat_tu'];

        $nhat_can = $tu_tru['ngay']['can'];
        $nhat_can_el = $thien_can[$nhat_can]['element'];
        $than_vuong = $than_info['than_vuong'];

        $hanh_map = $sinh_vuong_xuat_tu[$nhat_can_el] ?? [];
        // sinh: hành sinh ra Nhật Chủ (Ấn)
        // vuong: hành cùng Nhật Chủ (Tỷ Kiếp)
        // xuat: hành Nhật Chủ sinh ra (Thực Thương)
        // tu: hành khắc Nhật Chủ (Quan Sát)
        // hao: hành Nhật Chủ khắc (Tài Tinh)

        $dung_than = [];
        $hy_than = [];
        $ky_than = [];

        if ($than_vuong) {
            // Thân vượng: cần tiết khí, khắc chế
            // Dụng Thần: Thực Thương (xuat), Tài Tinh (hao), Quan Sát (tu)
            // Kỵ Thần: Ấn Tinh (sinh), Tỷ Kiếp (vuong)
            $dung_than = [$hanh_map['xuat'], $hanh_map['hao']];
            $hy_than = [$hanh_map['tu']];
            $ky_than = [$hanh_map['sinh'], $hanh_map['vuong']];
        } else {
            // Thân nhược: cần sinh phù, trợ giúp
            // Dụng Thần: Ấn Tinh (sinh), Tỷ Kiếp (vuong)
            // Kỵ Thần: Quan Sát (tu), Tài Tinh (hao), Thực Thương (xuat)
            $dung_than = [$hanh_map['sinh'], $hanh_map['vuong']];
            $hy_than = [];
            $ky_than = [$hanh_map['tu'], $hanh_map['hao'], $hanh_map['xuat']];
        }

        // Chuyển đổi sang tên tiếng Việt
        $hanh_names = [];
        foreach ($ngu_hanh as $h_id => $h_info) {
            $hanh_names[$h_id] = $h_info['name'];
        }

        return [
            'dung_than' => array_map(fn($h) => $hanh_names[$h] ?? $h, $dung_than),
            'hy_than' => array_map(fn($h) => $hanh_names[$h] ?? $h, $hy_than),
            'ky_than' => array_map(fn($h) => $hanh_names[$h] ?? $h, $ky_than),
            'dung_than_ids' => $dung_than,
            'ky_than_ids' => $ky_than,
        ];
    }
}
