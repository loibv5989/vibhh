<?php
if (!defined('ABSPATH')) exit;

/**
 * TuVi_HopTuoi — Engine so sánh hợp tuổi 2 người
 * Cấu trúc tương tự TuVi_NTX: tách biệt engine / data / template / form
 * Phân tích: Ngũ Hành Nạp Âm, Thiên Can, Địa Chi, Cung Mệnh (Bát Trạch)
 * Ứng dụng: Tình duyên, Hôn nhân, Hợp tác làm ăn
 */
class TuVi_HopTuoi {

    /**
     * So sánh hợp tuổi 2 người
     * @param array $input [
     *   'ngay_sinh_a', 'gio_sinh_a', 'gioi_tinh_a', 'ten_a',
     *   'ngay_sinh_b', 'gio_sinh_b', 'gioi_tinh_b', 'ten_b',
     *   'muc_dich' => 'tinh_duyen' | 'hon_nhan' | 'hop_tac'
     * ]
     */
    public static function evaluate(array $input): array
    {
        $muc_dich = self::normalizeMucDich($input['muc_dich'] ?? 'hon_nhan');

        $profileA = self::buildProfile([
            'ngay_sinh'  => $input['ngay_sinh_a'] ?? '',
            'gio_sinh'   => $input['gio_sinh_a']  ?? '12:00',
            'gioi_tinh'  => $input['gioi_tinh_a'] ?? 'nam',
            'ten'        => $input['ten_a']        ?? 'Người A',
        ]);

        $profileB = self::buildProfile([
            'ngay_sinh'  => $input['ngay_sinh_b'] ?? '',
            'gio_sinh'   => $input['gio_sinh_b']  ?? '12:00',
            'gioi_tinh'  => $input['gioi_tinh_b'] ?? 'nu',
            'ten'        => $input['ten_b']        ?? 'Người B',
        ]);

        if (!$profileA['valid']) {
            return ['success' => false, 'message' => 'Ngày sinh người thứ nhất không hợp lệ.'];
        }
        if (!$profileB['valid']) {
            return ['success' => false, 'message' => 'Ngày sinh người thứ hai không hợp lệ.'];
        }

        $rules = self::loadRules();

        // Chạy 4 tầng phân tích
        $evalNguHanh = self::evalNguHanh($profileA, $profileB, $rules);
        $evalCan     = self::evalThienCan($profileA, $profileB, $rules);
        $evalChi     = self::evalDiaChi($profileA, $profileB, $rules);
        $evalCung    = self::evalCungMenh($profileA, $profileB, $rules);

        // Tổng điểm (thang 10): NH(3) + Can(2) + Chi(2) + Cung(3)
        $weights = $rules['thang_diem'] ?? ['ngu_hanh' => 3, 'thien_can' => 2, 'dia_chi' => 2, 'cung_menh' => 3];
        $maxScore = array_sum($weights);

        $rawScore = $evalNguHanh['score'] + $evalCan['score'] + $evalChi['score'] + $evalCung['score'];
        $totalScore = round(max(0, min($maxScore, $rawScore)), 1);
        $percent = (int)round(($totalScore / $maxScore) * 100);

        $level   = self::determineLevel($percent, $rules);
        $remedies = self::generateRemedies($evalNguHanh, $evalCan, $evalChi, $evalCung, $profileA, $profileB);
        $conclusion = self::generateConclusion($evalNguHanh, $evalCan, $evalChi, $evalCung, $muc_dich, $profileA, $profileB, $percent);

        return [
            'success'    => true,
            'muc_dich'   => $muc_dich,
            'profiles'   => ['A' => $profileA, 'B' => $profileB],
            'score'      => $totalScore,
            'max_score'  => $maxScore,
            'percent'    => $percent,
            'level'      => $level,
            'details'    => [
                'ngu_hanh'  => $evalNguHanh,
                'thien_can' => $evalCan,
                'dia_chi'   => $evalChi,
                'cung_menh' => $evalCung,
            ],
            'conclusion' => $conclusion,
            'remedies'   => $remedies,
        ];
    }

    // -------------------------------------------------------------------------
    // BUILD PROFILE — Xây dựng hồ sơ từng người
    // -------------------------------------------------------------------------

    private static function buildProfile(array $input): array
    {
        $ngay_sinh = trim($input['ngay_sinh'] ?? '');
        if ($ngay_sinh === '') return ['valid' => false];

        $date = null;
        foreach (['Y-m-d', 'd-m-Y', 'd/m/Y'] as $fmt) {
            $d = DateTimeImmutable::createFromFormat($fmt, $ngay_sinh);
            if ($d instanceof DateTimeImmutable) { $date = $d; break; }
        }
        if ($date === null) {
            try { $date = new DateTimeImmutable($ngay_sinh); } catch (\Throwable $e) { return ['valid' => false]; }
        }

        $gio_sinh  = $input['gio_sinh']  ?? '12:00';
        $gioi_tinh = $input['gioi_tinh'] ?? 'nam';
        $ten       = sanitize_text_field($input['ten'] ?? '');

        $timeParts = explode(':', $gio_sinh);
        $hour = (int)($timeParts[0] ?? 12);

        $day   = (int)$date->format('j');
        $month = (int)$date->format('n');
        $year  = (int)$date->format('Y');

        $amlich    = self::getAmLich();
        $tz        = self::getTimezone();
        $lunar     = $amlich->convertSolar2Lunar($day, $month, $year, $tz);
        $lunarYear = (int)($lunar[2] ?? $year);

        // Năm sinh → Can Chi năm → Nạp Âm bản mệnh
        $yearStem = self::getYearStem($lunarYear);
        $yearChi  = self::getYearChi($lunarYear);
        $napAmKey = self::normalizeKey($yearStem) . '_' . self::normalizeKey($yearChi);

        // Giờ Tý vắt ngày — chỉ ảnh hưởng Nhật Chủ
        $dateForDay = $date;
        if ($hour >= 23) $dateForDay = $date->modify('+1 day');
        $jdn        = self::getJdn((int)$dateForDay->format('j'), (int)$dateForDay->format('n'), (int)$dateForDay->format('Y'));
        $dayCanChi  = self::getCanChiByJdn($jdn);

        // Cung Mệnh Bát Trạch
        $cungMenh = self::calcCungMenh($lunarYear, $gioi_tinh);

        // Ngũ Hành bản mệnh (Nạp Âm năm sinh)
        $rules    = self::loadRules();
        $nguHanh  = $rules['nap_am_ngu_hanh'][$napAmKey] ?? 'tho';
        $nguHanhVn = self::mapNguHanhVn($nguHanh);

        return [
            'valid'       => true,
            'ten'         => $ten !== '' ? $ten : ($gioi_tinh === 'nam' ? 'Người Nam' : 'Người Nữ'),
            'gioi_tinh'   => $gioi_tinh,
            'solar'       => "$day/$month/$year",
            'lunar_year'  => $lunarYear,
            'year_can'    => $yearStem,
            'year_chi'    => $yearChi,
            'can_chi_nam' => $yearStem . ' ' . $yearChi,
            'day_can'     => $dayCanChi['can'],
            'day_chi'     => $dayCanChi['chi'],
            'ngu_hanh'    => $nguHanh,
            'ngu_hanh_vn' => $nguHanhVn,
            'cung_menh'   => $cungMenh,
            'nap_am_key'  => $napAmKey,
        ];
    }

    // -------------------------------------------------------------------------
    // TẦNG 1 — NGŨ HÀNH NẠP ÂM
    // -------------------------------------------------------------------------

    private static function evalNguHanh(array $pA, array $pB, array $rules): array
    {
        // ── Cố định hệ quy chiếu theo giới tính ──────────────────────────────
        // Luôn lấy người Nam làm trụ cột (Nhật Chủ) để xét sinh/khắc,
        // bất kể thứ tự nhập. Nếu cả hai cùng giới hoặc không xác định,
        // giữ nguyên thứ tự A/B mà không có nghĩa đặc biệt.
        if ($pA['gioi_tinh'] === 'nu' && $pB['gioi_tinh'] === 'nam') {
            // Hoán vị nội bộ: pNam = B, pNu = A
            $pNam = $pB;
            $pNu  = $pA;
        } else {
            // A là Nam (hoặc cả hai cùng giới) → giữ nguyên
            $pNam = $pA;
            $pNu  = $pB;
        }

        $nhNam = $pNam['ngu_hanh'];
        $nhNu  = $pNu['ngu_hanh'];

        $sinh = ['kim' => 'thuy', 'thuy' => 'moc', 'moc' => 'hoa', 'hoa' => 'tho', 'tho' => 'kim'];
        $khac = ['kim' => 'moc',  'moc'  => 'tho',  'tho' => 'thuy', 'thuy' => 'hoa', 'hoa' => 'kim'];

        // Xét từ góc nhìn người Nam (trụ cột):
        // sinh_nhap  = Nữ sinh Nam   → đại cát (điểm cao nhất)
        // sinh_xuat  = Nam sinh Nữ   → bình, có hy sinh
        // ty_hoa     = cùng mệnh     → bình hòa
        // khac_xuat  = Nam khắc Nữ   → tiểu hung (vẫn có điểm, chủ động)
        // khac_nhap  = Nữ khắc Nam   → đại hung (điểm 0)
        if ($nhNam === $nhNu) {
            $relKey = 'ty_hoa';
        } elseif (($sinh[$nhNu] ?? '') === $nhNam) {
            $relKey = 'sinh_nhap';   // Nữ sinh Nam
        } elseif (($sinh[$nhNam] ?? '') === $nhNu) {
            $relKey = 'sinh_xuat';   // Nam sinh Nữ
        } elseif (($khac[$nhNam] ?? '') === $nhNu) {
            $relKey = 'khac_xuat';   // Nam khắc Nữ
        } elseif (($khac[$nhNu] ?? '') === $nhNam) {
            $relKey = 'khac_nhap';   // Nữ khắc Nam
        } else {
            $relKey = 'ty_hoa';
        }

        $data    = $rules['ngu_hanh_quan_he'][$relKey] ?? ['score' => 1, 'desc' => 'Bình Hòa - Không sinh không khắc.'];
        $weights = $rules['thang_diem'] ?? ['ngu_hanh' => 3];
        $maxNH   = $weights['ngu_hanh'] ?? 3;

        // Scale điểm gốc (0–3) sang maxNH
        $rawMax = 3;
        $score  = round(($data['score'] / $rawMax) * $maxNH, 2);
        $score  = max(0, min($maxNH, $score));

        $descParts = explode(' - ', $data['desc'], 2);
        $status = $descParts[0] ?? $relKey;
        $desc   = $descParts[1] ?? $data['desc'];

        // Luận giải chi tiết (dùng tên và mệnh theo vai trò Nam/Nữ)
        $nhNamVn = self::mapNguHanhVn($nhNam);
        $nhNuVn  = self::mapNguHanhVn($nhNu);
        $detail  = self::buildNguHanhDetail($relKey, $nhNamVn, $nhNuVn, $pNam['ten'], $pNu['ten']);

        // Trả về A/B vẫn theo thứ tự hiển thị gốc (pA, pB) để template không bị vỡ
        $nhAVn = self::mapNguHanhVn($pA['ngu_hanh']);
        $nhBVn = self::mapNguHanhVn($pB['ngu_hanh']);

        return [
            'label'   => 'Ngũ Hành Bản Mệnh (Nạp Âm)',
            'A'       => $nhAVn,
            'B'       => $nhBVn,
            'rel_key' => $relKey,
            'status'  => $status,
            'desc'    => $desc,
            'detail'  => $detail,
            'score'   => $score,
            'max'     => $maxNH,
        ];
    }

    private static function buildNguHanhDetail(string $rel, string $nhNam, string $nhNu, string $tenNam, string $tenNu): string
    {
        switch ($rel) {
            case 'sinh_nhap':
                return "$nhNu sinh $nhNam — {$tenNu} là nguồn sinh khí, nâng đỡ và bồi dưỡng khí vận cho {$tenNam}. Đây là cặp tương sinh đại cát theo hệ quy chiếu phong thủy, bền lâu và phát triển.";
            case 'sinh_xuat':
                return "$nhNam sinh $nhNu — {$tenNam} tiêu hao nguyên khí để nuôi dưỡng {$tenNu}. Tình cảm chân thực, nhưng {$tenNam} cần chú ý giữ gìn sức lực và không hy sinh thái quá.";
            case 'ty_hoa':
                return "$nhNam — $nhNu đồng hành cùng bản mệnh. Hai người cùng nhịp suy nghĩ, dễ đồng thuận, ít va chạm lớn nhưng thiếu lực kéo bổ sung cho nhau.";
            case 'khac_xuat':
                return "$nhNam khắc $nhNu — {$tenNam} có xu hướng chủ động và kiểm soát. Gia đạo rõ vai trò, tránh áp đặt để quan hệ bền vững.";
            case 'khac_nhap':
                return "$nhNu khắc $nhNam — {$tenNu} vô tình cản trở khí vận của {$tenNam}. Cần chú ý hướng đi lại, nơi ở và màu sắc phong thủy để hóa giải sát khí.";
            default:
                return 'Quan hệ ngũ hành bình hòa, không đặc biệt tốt cũng không xung khắc.';
        }
    }

    // -------------------------------------------------------------------------
    // TẦNG 2 — THIÊN CAN
    // -------------------------------------------------------------------------

    private static function evalThienCan(array $pA, array $pB, array $rules): array
    {
        $canA = $pA['year_can'];
        $canB = $pB['year_can'];
        $keyAB = self::normalizeKey($canA) . '_' . self::normalizeKey($canB);
        $keyBA = self::normalizeKey($canB) . '_' . self::normalizeKey($canA);

        $weights = $rules['thang_diem'] ?? [];
        $maxCan  = $weights['thien_can'] ?? 2;

        $hopList = $rules['can_quan_he']['hop'] ?? [];
        $phaList = $rules['can_quan_he']['pha'] ?? [];

        if (in_array($keyAB, $hopList, true) || in_array($keyBA, $hopList, true)) {
            $score  = $maxCan;
            $status = 'Thiên Can Tương Hợp';
            $desc   = "$canA hợp $canB — Tính cách hòa thuận, dễ đồng thuận trong công việc và cuộc sống. Hai người bổ trợ nhau trong quyết định.";
        } elseif (in_array($keyAB, $phaList, true) || in_array($keyBA, $phaList, true)) {
            $score  = 0;
            $status = 'Thiên Can Tương Khắc';
            $desc   = "$canA và $canB có ngũ hành tương khắc — Dễ bất đồng quan điểm, tranh luận, khắc khẩu. Cần luyện kỹ năng lắng nghe, tránh áp đặt.";
        } else {
            $score  = round($maxCan * 0.5, 1);
            $status = 'Thiên Can Bình Hòa';
            $desc   = "$canA và $canB không tương hợp cũng không tương khắc — Mỗi người giữ quan điểm độc lập, ít phụ thuộc, cần xây dựng điểm chung.";
        }

        return [
            'label'  => 'Thiên Can (Can năm sinh)',
            'A'      => $canA,
            'B'      => $canB,
            'status' => $status,
            'desc'   => $desc,
            'score'  => $score,
            'max'    => $maxCan,
        ];
    }

    // -------------------------------------------------------------------------
    // TẦNG 3 — ĐỊA CHI
    // -------------------------------------------------------------------------

    private static function evalDiaChi(array $pA, array $pB, array $rules): array
    {
        $chiA  = $pA['year_chi'];
        $chiB  = $pB['year_chi'];
        $keyAB = self::normalizeKey($chiA) . '_' . self::normalizeKey($chiB);
        $keyBA = self::normalizeKey($chiB) . '_' . self::normalizeKey($chiA);

        $weights = $rules['thang_diem'] ?? [];
        $maxChi  = $weights['dia_chi'] ?? 2;

        $hopList     = $rules['chi_quan_he']['hop']      ?? [];
        $xungHaiList = $rules['chi_quan_he']['xung_hai'] ?? [];

        // Phân loại chi tiết hơn
        $tamHopPairs = ['than_ty', 'ty_than', 'ty_thin', 'thin_ty', 'than_thin', 'thin_than', 'dan_ngo', 'ngo_dan', 'ngo_tuat', 'tuat_ngo', 'dan_tuat', 'tuat_dan', 'hoi_mao', 'mao_hoi', 'mao_mui', 'mui_mao', 'hoi_mui', 'mui_hoi', 'ti_dau', 'dau_ti', 'dau_suu', 'suu_dau', 'ti_suu', 'suu_ti'];
        $lucHopPairs = ['ty_suu', 'suu_ty', 'dan_hoi', 'hoi_dan', 'mao_tuat', 'tuat_mao', 'thin_dau', 'dau_thin', 'ti_than', 'than_ti', 'ngo_mui', 'mui_ngo'];
        $xungPairs   = ['ty_ngo', 'ngo_ty', 'suu_mui', 'mui_suu', 'dan_than', 'than_dan', 'mao_dau', 'dau_mao', 'thin_tuat', 'tuat_thin', 'ti_hoi', 'hoi_ti'];
        $haiPairs    = ['ty_mui', 'mui_ty', 'suu_ngo', 'ngo_suu', 'dan_ti', 'ti_dan', 'mao_thin', 'thin_mao', 'than_hoi', 'hoi_than', 'dau_tuat', 'tuat_dau'];

        if (in_array($keyAB, $tamHopPairs, true) || in_array($keyBA, $tamHopPairs, true)) {
            $score  = $maxChi;
            $status = 'Tam Hợp (Đại Cát)';
            $desc   = "$chiA và $chiB thuộc bộ Tam Hợp — Tâm giao sâu sắc, hỗ trợ nhau mạnh mẽ, hậu vận phát triển khi ở bên nhau.";
        } elseif (in_array($keyAB, $lucHopPairs, true) || in_array($keyBA, $lucHopPairs, true)) {
            $score  = round($maxChi * 0.8, 1);
            $status = 'Lục Hợp (Cát)';
            $desc   = "$chiA hợp $chiB theo Lục Hợp — Gia đạo êm ấm, tình cảm ổn định, dễ thông cảm và tha thứ cho nhau.";
        } elseif (in_array($keyAB, $xungPairs, true) || in_array($keyBA, $xungPairs, true)) {
            $score  = 0;
            $status = 'Lục Xung (Hung)';
            $desc   = "$chiA xung $chiB — Dễ dẫn đến đổ vỡ hoặc cản trở nhau trên con đường tài lộc, sự nghiệp. Cần hóa giải phong thủy.";
        } elseif (in_array($keyAB, $haiPairs, true) || in_array($keyBA, $haiPairs, true)) {
            $score  = round($maxChi * 0.15, 1);
            $status = 'Lục Hại (Tiểu Hung)';
            $desc   = "$chiA hại $chiB — Dễ gây hiểu nhầm, sóng gió nhỏ, tổn hao. Cần kiên nhẫn và xây dựng niềm tin lâu dài.";
        } else {
            $score  = round($maxChi * 0.5, 1);
            $status = 'Bình Hòa';
            $desc   = "$chiA và $chiB không xung không hợp — Quan hệ bình thường, an ổn, không có lực kéo đặc biệt.";
        }

        return [
            'label'  => 'Địa Chi (Chi năm sinh)',
            'A'      => $chiA,
            'B'      => $chiB,
            'status' => $status,
            'desc'   => $desc,
            'score'  => $score,
            'max'    => $maxChi,
        ];
    }

    // -------------------------------------------------------------------------
    // TẦNG 4 — CUNG MỆNH BÁT TRẠCH
    // -------------------------------------------------------------------------

    private static function evalCungMenh(array $pA, array $pB, array $rules): array
    {
        $cungA    = $pA['cung_menh'];
        $cungB    = $pB['cung_menh'];
        $matTran  = $rules['cung_phi_ma_tran'] ?? [];
        $yNghia   = $rules['cung_phi_y_nghia'] ?? [];
        $weights  = $rules['thang_diem']       ?? [];
        $maxCung  = $weights['cung_menh']       ?? 3;

        // Tra ma trận 2 chiều A→B và B→A, lấy kết quả bất lợi hơn làm chủ
        $resultAB = $matTran[$cungA][$cungB] ?? 'Phục Vị';
        $resultBA = $matTran[$cungB][$cungA] ?? 'Phục Vị';

        $dataAB   = $yNghia[$resultAB] ?? ['score' => 1.5, 'type' => 'Cát', 'desc' => 'Bình yên, ổn định.'];
        $dataBA   = $yNghia[$resultBA] ?? ['score' => 1.5, 'type' => 'Cát', 'desc' => 'Bình yên, ổn định.'];

        // Lấy chiều bất lợi hơn làm điểm đại diện (conservative)
        $usedResult = $dataAB['score'] <= $dataBA['score'] ? $resultAB : $resultBA;
        $usedData   = $dataAB['score'] <= $dataBA['score'] ? $dataAB   : $dataBA;

        // Scale: score gốc tối đa là 3 (Sinh Khí) → map sang maxCung
        $scoreRawMax = 3;
        $score = round(($usedData['score'] / $scoreRawMax) * $maxCung, 2);
        $score = max(0, min($maxCung, $score));

        $status = $usedResult . ' (' . ($usedData['type'] ?? '') . ')';

        // Luận giải AB và BA
        $detailAB = "{$pA['ten']} ({$cungA}) → {$pB['ten']} ({$cungB}): $resultAB — " . ($dataAB['desc'] ?? '');
        $detailBA = "{$pB['ten']} ({$cungB}) → {$pA['ten']} ({$cungA}): $resultBA — " . ($dataBA['desc'] ?? '');

        return [
            'label'     => 'Cung Mệnh (Bát Trạch Phong Thủy)',
            'A'         => $cungA,
            'B'         => $cungB,
            'result_AB' => $resultAB,
            'result_BA' => $resultBA,
            'status'    => $status,
            'desc'      => $usedData['desc'] ?? '',
            'detail_AB' => $detailAB,
            'detail_BA' => $detailBA,
            'score'     => $score,
            'max'       => $maxCung,
        ];
    }

    // -------------------------------------------------------------------------
    // KẾT LUẬN & HÓA GIẢI
    // -------------------------------------------------------------------------

    private static function generateConclusion(
        array $evalNH, array $evalCan, array $evalChi, array $evalCung,
        array $muc_dich, array $pA, array $pB, int $percent
    ): string {
        $tenA = $pA['ten'];
        $tenB = $pB['ten'];

        $intro = match(true) {
            $percent >= 80 => "Tổng hợp các yếu tố Can Chi, Ngũ Hành và Cung Mệnh cho thấy $tenA và $tenB có sự tương hợp rất tốt",
            $percent >= 65 => "$tenA và $tenB có mức độ hòa hợp khá, phần lớn các yếu tố thuận lợi",
            $percent >= 45 => "$tenA và $tenB ở mức trung bình, có điểm hợp và điểm cần cải thiện",
            default        => "$tenA và $tenB có khá nhiều điểm xung khắc cần chú ý",
        };

        $mdNote = match($muc_dich['key']) {
            'tinh_duyen' => "trong quan hệ tình cảm và đời sống đôi lứa.",
            'hon_nhan'   => "cho hôn nhân lâu dài và xây dựng gia đình.",
            'hop_tac'    => "trong hợp tác làm ăn, kinh doanh và sự nghiệp chung.",
            default      => "trong các mối quan hệ chung.",
        };

        $points = [];
        if ($evalNH['score'] >= $evalNH['max'] * 0.7) {
            $points[] = "ngũ hành tương sinh hỗ trợ nhau ({$evalNH['A']} — {$evalNH['B']})";
        }
        if ($evalChi['score'] >= $evalChi['max'] * 0.7) {
            $points[] = "địa chi {$evalChi['status']}";
        }
        if ($evalCan['score'] >= $evalCan['max'] * 0.7) {
            $points[] = "thiên can tương hợp ({$evalCan['A']} — {$evalCan['B']})";
        }
        // Kiểm tra cả hai chiều AB và BA — lấy chiều tốt hơn để đưa vào điểm mạnh
        if ($evalCung['score'] >= $evalCung['max'] * 0.7) {
            $catCung = ['Sinh Khí', 'Diên Niên', 'Thiên Y', 'Phục Vị'];
            $bestResult = null;
            if (in_array($evalCung['result_AB'], $catCung, true)) {
                $bestResult = $evalCung['result_AB'];
            }
            if (in_array($evalCung['result_BA'], $catCung, true)) {
                // Ưu tiên result có score cao hơn nếu cả hai đều cát
                if ($bestResult === null) {
                    $bestResult = $evalCung['result_BA'];
                }
            }
            $points[] = "cung mệnh " . ($bestResult ?? $evalCung['result_AB']);
        }

        $pointsStr = !empty($points) ? ' Điểm mạnh: ' . implode(', ', $points) . '.' : '';

        return "$intro $mdNote$pointsStr";
    }

    private static function generateRemedies(
        array $evalNH, array $evalCan, array $evalChi, array $evalCung,
        array $pA, array $pB
    ): array {
        $remedies = [];

        // Hóa giải Ngũ Hành
        if (in_array($evalNH['rel_key'], ['khac_nhap', 'khac_xuat'], true)) {
            $nhA = $pA['ngu_hanh'];
            $nhB = $pB['ngu_hanh'];
            // Tìm mệnh trung gian (bắc cầu tương sinh)
            $bridge = self::findNguHanhBridge($nhA, $nhB);
            $bridgeVn = $bridge ? self::mapNguHanhVn($bridge) : null;
            $r = '<strong>Hóa giải Ngũ Hành:</strong> ';
            if ($bridgeVn) {
                $r .= "Mệnh {$pA['ngu_hanh_vn']} và {$pB['ngu_hanh_vn']} xung khắc. Nên bài trí vật phẩm mang ngũ hành <em>$bridgeVn</em> (màu sắc, chất liệu) trong không gian chung để bắc cầu tương sinh. ";
            }
            $r .= "Tránh chọn ngày xung với mệnh khi ký kết hoặc tiến hành đại sự chung.";
            $remedies[] = $r;
        }

        // Hóa giải Thiên Can xung phá
        if ($evalCan['score'] === 0) {
            $remedies[] = '<strong>Hóa giải Thiên Can:</strong> Hai Thiên Can tương phá dễ gây tranh luận. Nên phân công rõ vai trò: mỗi người phụ trách một lĩnh vực không chồng chéo. Tránh ra quyết định chung vào ngày Can xung.';
        }

        // Hóa giải Địa Chi xung
        if (str_contains($evalChi['status'] ?? '', 'Xung')) {
            $remedies[] = '<strong>Hóa giải Địa Chi:</strong> Phạm Lục Xung — Đeo vật phẩm phong thủy thuộc con giáp Tam Hợp với tuổi mình để giảm sát khí. Tránh chung vốn lớn vào các năm tuổi của một trong hai người.';
        } elseif (str_contains($evalChi['status'] ?? '', 'Hại')) {
            $remedies[] = '<strong>Hóa giải Lục Hại:</strong> Nên sử dụng vật phẩm màu vàng/nâu (Thổ) hoặc vàng (Kim) để hóa giải. Xây dựng thói quen giao tiếp rõ ràng, tránh để hiểu nhầm tích lũy.';
        }

        // Hóa giải Cung Mệnh hung
        $hungCung = ['Tuyệt Mệnh', 'Ngũ Quỷ', 'Lục Sát', 'Họa Hại'];
        if (in_array($evalCung['result_AB'], $hungCung, true) || in_array($evalCung['result_BA'], $hungCung, true)) {
            $remedies[] = '<strong>Hóa giải Cung Mệnh:</strong> Điều hướng cửa chính, hướng giường ngủ và hướng bếp về cung Sinh Khí hoặc Thiên Y của người trụ cột gia đình. Nên nhờ chuyên gia phong thủy xem bố cục nhà ở để trấn áp hung khí tổng thể.';
        }

        if (empty($remedies)) {
            $remedies[] = 'Hai người khá hợp nhau, không cần can thiệp hóa giải phong thủy phức tạp. Duy trì giao tiếp cởi mở và tôn trọng lẫn nhau là đủ.';
        }

        return $remedies;
    }

    /**
     * Tìm ngũ hành trung gian để bắc cầu tương sinh giữa 2 mệnh xung khắc
     */
    private static function findNguHanhBridge(string $nhA, string $nhB): ?string
    {
        $sinh = ['kim' => 'thuy', 'thuy' => 'moc', 'moc' => 'hoa', 'hoa' => 'tho', 'tho' => 'kim'];
        foreach ($sinh as $source => $target) {
            // Tìm mệnh X mà nhA sinh X và X sinh nhB (hoặc ngược lại)
            if (($sinh[$nhA] ?? '') === $source && ($sinh[$source] ?? '') === $nhB) return $source;
            if (($sinh[$nhB] ?? '') === $source && ($sinh[$source] ?? '') === $nhA) return $source;
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // TÍNH CUNG MỆNH BÁT TRẠCH
    // -------------------------------------------------------------------------

    /**
     * Công thức chuẩn Bát Trạch:
     * Nam: (11 - tổng chữ số năm âm lịch) % 9, nếu = 0 → 9
     * Nữ:  (4  + tổng chữ số năm âm lịch) % 9, nếu = 0 → 9
     * Cung 5 (Trung Cung): Nam → Khôn, Nữ → Cấn
     */
    private static function calcCungMenh(int $lunarYear, string $gender): string
    {
        $sum = 0;
        $temp = abs($lunarYear);
        while ($temp > 0) {
            $sum += $temp % 10;
            $temp = (int)($temp / 10);
        }
        while ($sum > 9) {
            $s2 = 0;
            $tmp = $sum;
            while ($tmp > 0) { $s2 += $tmp % 10; $tmp = (int)($tmp / 10); }
            $sum = $s2;
        }

        if ($gender === 'nam') {
            $num = (11 - $sum) % 9;
            if ($num === 0) $num = 9;
        } else {
            $num = (4 + $sum) % 9;
            if ($num === 0) $num = 9;
        }

        $cungMap = [
            1 => 'Khảm', 2 => 'Khôn', 3 => 'Chấn', 4 => 'Tốn',
            5 => ($gender === 'nam') ? 'Khôn' : 'Cấn',
            6 => 'Càn',  7 => 'Đoài', 8 => 'Cấn',  9 => 'Ly',
        ];

        return $cungMap[$num] ?? 'Khảm';
    }

    // -------------------------------------------------------------------------
    // ĐÁNH GIÁ TỔNG
    // -------------------------------------------------------------------------

    private static function determineLevel(int $percent, array $rules): array
    {
        $bands = $rules['score_bands'] ?? [
            ['min' => 0,  'max' => 24,  'label' => 'Rất xung'],
            ['min' => 25, 'max' => 44,  'label' => 'Xung nhẹ'],
            ['min' => 45, 'max' => 64,  'label' => 'Trung bình'],
            ['min' => 65, 'max' => 84,  'label' => 'Khá hợp'],
            ['min' => 85, 'max' => 100, 'label' => 'Rất hợp'],
        ];

        foreach ($bands as $band) {
            if ($percent >= $band['min'] && $percent <= $band['max']) {
                return [
                    'label'   => $band['label'],
                    'percent' => $percent,
                    'class'   => self::levelClass($band['label']),
                ];
            }
        }

        return ['label' => 'Trung bình', 'percent' => $percent, 'class' => 'tuvi-result-neutral'];
    }

    private static function levelClass(string $label): string
    {
        if (str_contains($label, 'Rất hợp') || str_contains($label, 'Khá hợp')) return 'tuvi-result-good';
        if (str_contains($label, 'Rất xung') || str_contains($label, 'Xung nhẹ')) return 'tuvi-result-bad';
        return 'tuvi-result-neutral';
    }

    private static function normalizeMucDich($val): array
    {
        $map = [
            'tinh_duyen' => ['label' => 'Tình duyên, Đôi lứa', 'key' => 'tinh_duyen'],
            'hon_nhan'   => ['label' => 'Hôn nhân, Gia đạo',   'key' => 'hon_nhan'],
            'hop_tac'    => ['label' => 'Hợp tác, Làm ăn',     'key' => 'hop_tac'],
        ];
        $key = is_string($val) ? trim($val) : 'hon_nhan';
        return $map[$key] ?? $map['hon_nhan'];
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private static function loadRules(): array
    {
        return TuVi_Data::load('all');
    }

    private static function getAmLich(): Tuvi_AmLich
    {
        static $instance = null;
        if ($instance instanceof Tuvi_AmLich) return $instance;
        if (!class_exists('Tuvi_AmLich')) {
            $path = defined('TUVI_PLUGIN_DIR') ? TUVI_PLUGIN_DIR . 'data/amlich.php' : dirname(__DIR__) . '/data/amlich.php';
            if (is_file($path)) require_once $path;
        }
        $instance = new Tuvi_AmLich();
        return $instance;
    }

    private static function getTimezone(): int
    {
        if (class_exists('TuVi_Settings')) {
            return TuVi_Settings::get_instance()->getTimezone();
        }
        return 7;
    }

    private static function getJdn(int $day, int $month, int $year): int
    {
        return (int)self::getAmLich()->jdFromDate($day, $month, $year);
    }

    private static function getCanChiByJdn(int $jdn): array
    {
        $canList = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
        $chiList = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];
        return [
            'can' => $canList[($jdn + 9) % 10] ?? 'Giáp',
            'chi' => $chiList[($jdn + 1) % 12] ?? 'Tý',
        ];
    }

    private static function getYearChi(int $year): string
    {
        $chiList = ['Tý', 'Sửu', 'Dần', 'Mão', 'Thìn', 'Tỵ', 'Ngọ', 'Mùi', 'Thân', 'Dậu', 'Tuất', 'Hợi'];
        return $chiList[($year + 8) % 12] ?? 'Tý';
    }

    private static function getYearStem(int $year): string
    {
        $canList = ['Giáp', 'Ất', 'Bính', 'Đinh', 'Mậu', 'Kỷ', 'Canh', 'Tân', 'Nhâm', 'Quý'];
        return $canList[($year + 6) % 10] ?? 'Giáp';
    }

    private static function mapNguHanhVn(string $key): string
    {
        return ['kim' => 'Kim', 'thuy' => 'Thủy', 'moc' => 'Mộc', 'hoa' => 'Hỏa', 'tho' => 'Thổ'][$key] ?? 'Thổ';
    }

    private static function normalizeKey(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = str_replace('tỵ', 'ti', $value);
        $map = [
            'á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
            'â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
            'ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y','đ'=>'d',
        ];
        return strtr($value, $map);
    }
}