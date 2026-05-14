<?php

if (!defined('ABSPATH')) exit;

require_once dirname(__DIR__) . '/includes/calendar.php';
require_once dirname(__DIR__) . '/includes/luchao.php';

function iching_gatekeeper(string $question, string $gender): string {
    return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây là một câu hỏi kiến thức thông thường hay là câu hỏi có ý nghĩa xem bói, luận quẻ, dự đoán sự việc, xem hung cát liên quan đến quẻ kinh dịch.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi chứa nội dung nhạy cảm, bạo lực, phi pháp, xúc phạm hoặc nguy hiểm.
- Không được từ chối trả lời dưới bất kỳ hình thức nào (ví dụ: không được nói “tôi không thể hỗ trợ”).
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại theo quy tắc bên dưới.

Câu hỏi: {$question}
Giới tính: {$gender}

Quy tắc phân loại:
Nếu câu hỏi mang tính tìm hiểu kiến thức, định nghĩa, cách dùng, hoặc nói chuyện phiếm, hoặc một chuỗi ký tự không rõ ràng, thử bot 
 (Ví dụ: "Lục Hào là gì?", "Mai hoa hợp với gì?", "Cách lập quẻ", "Chào bạn")
 → Trả về: KHONG

Nếu câu hỏi có ý nghĩa xem bói, luận quẻ, dự đoán sự việc, xem hung cát, xác định Dụng Thần:
- Công việc / Sự nghiệp / Kiện tụng / Rắc rối / Bạo lực / Hại người / Tù tội / Hình sự → Quan Quỷ
- Tiền bạc / Tài lộc / Đầu tư / Kinh doanh / Mua bán / Tìm đồ vật thất lạc → Thê Tài
- Học hành / Thi cử / Giấy tờ / Hợp đồng / Xin Visa / Mua nhà cửa, đất đai, xe cộ → Phụ Mẫu
- Tình duyên / Hôn nhân / Tình yêu / Vợ chồng (Nam hỏi) → Thê Tài
- Tình duyên / Hôn nhân / Tình yêu / Vợ chồng (Nữ hỏi) → Quan Quỷ
- Sức khỏe / Bệnh hiểm nghèo / Tính mạng / Tự sát / Tìm người / Bình an / Vận hạn tổng quan / Con cái / Mang thai / Sinh đẻ → Tử Tôn

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất sau khi đã phân loại:
KHONG / Quan Quỷ / Thê Tài / Phụ Mẫu / Tử Tôn
Không giải thích, CTA, phân tích nội bộ.
Không thêm bất kỳ nội dung nào khác.
TXT;
}

function iching_build_prompt(
    string $name,
    string $gender,
    string $topic,
    string $question,
    array  $fullData,
    string $mode = 'luchao',
    string $api_dung_than = ''
): string {

    $smart_topic_label = _iching_get_smart_topic($question, $topic, $gender);

    if (strpos($mode, 'maihoa') === 0) {
        return iching_build_prompt_maihoa($smart_topic_label, $question, $fullData, $mode);
    }

    // ── 1. XÁC ĐỊNH DỤNG THẦN TỪ API GÁC CỔNG ──────────────────
    if (!empty($api_dung_than)) {
        $dung_than = $api_dung_than;
        $dt_reason = "Xác định tự động từ AI phân tích ngữ cảnh câu hỏi.";
    } else {
        // Fallback dùng PHP nếu API Gác cổng lỗi
        $dt_result = _iching_detect_dung_than($question, $gender);
        $dung_than = $dt_result['dung_than'];
        $dt_reason = $dt_result['reason'];

        if ($dt_result['source'] === 'topic_fallback') {
            $dung_than_map = [
                'career'     => 'Quan Quỷ',
                'finance'    => 'Thê Tài',
                'love'       => ($gender === 'Nam') ? 'Thê Tài' : 'Quan Quỷ',
                'health'     => 'Tử Tôn',
                'education'  => 'Phụ Mẫu',
                'lost_found' => 'Thê Tài',
                'general'    => 'Tử Tôn',
            ];

            $dung_than = $dung_than_map[$topic] ?? 'Tử Tôn';
            $dt_reason = "Câu hỏi không rõ chủ đề → fallback theo nhóm [{$smart_topic_label}]";
        }
    }

    // ── 2. LẤY DỮ LIỆU QUẺ ────────────────────────────────────
    $bien           = $fullData['bien'];
    $changing_lines = $fullData['changing_lines'] ?? [];

    $toss_time    = $fullData['toss_time'] ?? current_time('mysql');
    $tz           = wp_timezone();
    $dt           = new DateTime($toss_time, $tz);
    $timestamp    = $dt->getTimestamp();
    $cc           = Iching_Calendar::get($timestamp);
    $calendar_str = Iching_Calendar::toPromptString($cc);

    $dd = (int) wp_date('j', $timestamp);
    $mm = (int) wp_date('n', $timestamp);
    $yy = (int) wp_date('Y', $timestamp);

    $am_lich = new Iching_AmLich();
    $lunar_date = $am_lich->convertSolar2Lunar($dd, $mm, $yy, 7.0);

    $ngay_am  = str_pad($lunar_date[0], 2, '0', STR_PAD_LEFT);
    $thang_am = str_pad($lunar_date[1], 2, '0', STR_PAD_LEFT);
    $nam_am   = $lunar_date[2];
    $lunar_str = "{$ngay_am}/{$thang_am}/{$nam_am}";

    $chu_bin      = $fullData['chu_key'];
    $can_ngay_str = explode(' ', $cc['ngay'])[0];

    $luchao_data   = Iching_LucHao::parse($chu_bin, $can_ngay_str);
    $goc_hanh_cung = $luchao_data['hanh_cung'];

    $luchao_bien = null;
    if ($bien) {
        $bien_bin    = $fullData['bien_key'];
        $luchao_bien = Iching_LucHao::parse($bien_bin, $can_ngay_str, $goc_hanh_cung);
    }

    // ── 3. XÁC ĐỊNH DỤNG THẦN TRONG 6 HÀO ────────────────────
    $is_dt_present = false;
    $dt_hanh       = '';
    $dt_chi        = '';
    $dt_all_lines  = [];

    foreach ($luchao_data['lines'] as $idx => $line) {
        if ($line['luc_than'] === $dung_than) {
            $is_dt_present = true;
            $dt_all_lines[] = [
                'index'   => $idx,
                'chi'     => $line['chi'],
                'hanh'    => $line['hanh'],
                'luc_thu' => $line['luc_thu'],
                'is_the'  => $line['is_the'],
                'is_ung'  => $line['is_ung'],
            ];
            if (!$dt_hanh) {
                $dt_hanh = $line['hanh'];
                $dt_chi  = $line['chi'];
            }
        }
    }

    // ── 4. PHỤC THẦN (nếu DT không lộ trên quẻ) ──────────────
    $phuc_than_block = '';
    $phuc_than_data  = null;
    $phi_chi         = '';
    $tuan_khong_arr = Iching_Calendar::getTuanKhong($cc['ngay']);

    if (!$is_dt_present) {
        $pt = Iching_LucHao::getPhucThan($luchao_data['cung'], $dung_than);
        if ($pt) {
            $phuc_than_data = $pt;
            $dt_hanh        = $pt['hanh'];
            $dt_chi         = $pt['chi'];

            $phi_than     = $luchao_data['lines'][$pt['under_line']];
            $phi_hanh     = $phi_than['hanh'];
            $phi_chi      = $phi_than['chi'];
            $phi_luc_than = $phi_than['luc_than'];
            $phi_luc_thu  = $phi_than['luc_thu'];

            $phi_phuc_rel = _iching_phi_phuc_relation($phi_hanh, $pt['hanh']);

            $hop_tru_nhat   = _iching_is_luc_hop($cc['nhat_kien'], $pt['chi'])
                ? "Nhật Kiến {$cc['nhat_kien']} HỢP {$pt['chi']} → HỢP TRỤ Phục Thần (bị nhốt, không lộ được trong ngày này)"
                : '';
            $hop_tru_nguyet = _iching_is_luc_hop($cc['nguyet_lenh'], $pt['chi'])
                ? "Nguyệt Lệnh {$cc['nguyet_lenh']} HỢP {$pt['chi']} → HỢP TRỤ Phục Thần (bị nhốt suốt tháng)"
                : '';
            $xung_phi_nhat   = _iching_is_luc_xung($cc['nhat_kien'], $phi_chi)
                ? "Nhật Kiến {$cc['nhat_kien']} XUNG Phi {$phi_chi} → Phi bị phá, Phục có khả năng lộ hôm nay"
                : '';
            $xung_phi_nguyet = _iching_is_luc_xung($cc['nguyet_lenh'], $phi_chi)
                ? "Nguyệt Lệnh {$cc['nguyet_lenh']} XUNG Phi {$phi_chi} → Phi bị phá trong tháng, Phục dễ lộ"
                : '';
            $is_pt_tk = in_array($pt['chi'], $tuan_khong_arr) ? ' [TUẦN KHÔNG]' : '';

            $phuc_than_block  = "  - PHỤC THẦN: {$pt['chi']} {$pt['hanh']} ({$pt['luc_than']}) ẩn dưới Hào {$pt['under_line']}\n";
            $phuc_than_block .= "  - PHI THẦN (hào đè): {$phi_chi} {$phi_hanh} ({$phi_luc_than}) [{$phi_luc_thu}]\n";
            $phuc_than_block .= "    → Quan hệ Phi/Phục: {$phi_phuc_rel}\n";
            if ($hop_tru_nhat)    $phuc_than_block .= "    → {$hop_tru_nhat}\n";
            if ($hop_tru_nguyet)  $phuc_than_block .= "    → {$hop_tru_nguyet}\n";
            if ($xung_phi_nhat)   $phuc_than_block .= "    → {$xung_phi_nhat}\n";
            if ($xung_phi_nguyet) $phuc_than_block .= "    → {$xung_phi_nguyet}\n";
            if (!$hop_tru_nhat && !$hop_tru_nguyet && !$xung_phi_nhat && !$xung_phi_nguyet) {
                $phuc_than_block .= "    → Nhật/Nguyệt không xung Phi, không hợp trụ Phục: trạng thái giữ nguyên\n";
            }
        }
    }

    // ── 5. VƯỢNG SUY & NHẬT KIẾN CỦA DỤNG THẦN ───────────────
    $dt_vuong_suy  = $dt_hanh ? Iching_Calendar::getVuongSuy($dt_hanh, $cc['hanh_thang']) : '';
    $dt_nhat_kien  = $dt_hanh ? Iching_Calendar::getNhatKienComment($dt_hanh, $cc['hanh_ngay'], 'Dụng Thần') : '';

    $dt_nhat_xung   = ($dt_chi && _iching_is_luc_xung($cc['nhat_kien'], $dt_chi))
        ? "Nhật Kiến {$cc['nhat_kien']} XUNG {$dt_chi} → kích động Dụng Thần (mạnh: bứt phá | yếu: bị phá)"
        : '';
    $dt_nhat_hop    = ($dt_chi && _iching_is_luc_hop($cc['nhat_kien'], $dt_chi))
        ? "Nhật Kiến {$cc['nhat_kien']} HỢP {$dt_chi} → HỢP TRỤ Dụng Thần (dù mạnh cũng bị trói, không phát)"
        : '';
    $dt_nguyet_xung = ($dt_chi && _iching_is_luc_xung($cc['nguyet_lenh'], $dt_chi))
        ? "Nguyệt Lệnh {$cc['nguyet_lenh']} XUNG {$dt_chi} → Dụng Thần bị nguyệt phá"
        : '';
    $dt_nguyet_hop  = ($dt_chi && _iching_is_luc_hop($cc['nguyet_lenh'], $dt_chi))
        ? "Nguyệt Lệnh {$cc['nguyet_lenh']} HỢP {$dt_chi} → Dụng Thần bị nguyệt hợp trụ suốt tháng"
        : '';

    // ── 6. TÓM TẮT VỊ TRÍ DỤNG THẦN ──────────────────────────
    $dt_lines_summary = '';
    if (count($dt_all_lines) > 1) {
        $dt_lines_summary = "  │ XUẤT HIỆN TẠI " . count($dt_all_lines) . " HÀO — phân tích mục (c) phải liệt kê ĐỦ:\n";
        foreach ($dt_all_lines as $dl) {
            $pos = $dl['is_the'] ? ' (Thế)' : ($dl['is_ung'] ? ' (Ứng)' : '');
            $dt_lines_summary .= "  │   • Hào {$dl['index']}: {$dl['chi']} {$dl['hanh']} | Lục Thú: {$dl['luc_thu']}{$pos}\n";
        }
    } elseif (count($dt_all_lines) === 1) {
        $dl  = $dt_all_lines[0];
        $pos = $dl['is_the'] ? ' (Thế)' : ($dl['is_ung'] ? ' (Ứng)' : '');
        $dt_lines_summary = "  │ Vị trí: Hào {$dl['index']} | Lục Thú: {$dl['luc_thu']}{$pos}\n";
    }

    // ── 7. THỜI ĐIỂM THUẬN ────────────────────────────────────
    $xung_map = [
        'Tý'=>'Ngọ','Ngọ'=>'Tý','Sửu'=>'Mùi','Mùi'=>'Sửu',
        'Dần'=>'Thân','Thân'=>'Dần','Mão'=>'Dậu','Dậu'=>'Mão',
        'Thìn'=>'Tuất','Tuất'=>'Thìn','Tỵ'=>'Hợi','Hợi'=>'Tỵ',
    ];

    $hop_tru_chis = [];
    if ($dt_chi && _iching_is_luc_hop($cc['nhat_kien'], $dt_chi))   $hop_tru_chis[] = $cc['nhat_kien'];
    if ($dt_chi && _iching_is_luc_hop($cc['nguyet_lenh'], $dt_chi)) $hop_tru_chis[] = $cc['nguyet_lenh'];
    foreach ($changing_lines as $cl) {
        if (!$luchao_bien) continue;
        $ld = $luchao_data['lines'][$cl];
        $lb = $luchao_bien['lines'][$cl];
        if ($dt_chi && _iching_is_luc_hop($ld['chi'], $dt_chi)) $hop_tru_chis[] = $ld['chi'];
        if ($dt_chi && _iching_is_luc_hop($lb['chi'], $dt_chi)) $hop_tru_chis[] = $lb['chi'];
    }
    $hop_tru_chis = array_unique($hop_tru_chis);

    $thoi_diem_block = '';
    if (!empty($hop_tru_chis)) {
        $thoi_diem_block .= "  │ THỜI ĐIỂM THUẬN — ĐÃ TÍNH SẴN:\n";
        foreach ($hop_tru_chis as $ht_chi) {
            $giai_chi = $xung_map[$ht_chi] ?? '';
            if ($giai_chi) {
                $thoi_diem_block .= "  │   DT bị {$ht_chi} hợp trụ → cần xung {$ht_chi}: ngày/tháng {$giai_chi} sẽ giải phóng\n";
            }
        }
        $thoi_diem_block .= "  │   (KHÔNG dùng ngày xung DT để giải hợp trụ — sai cơ chế)\n";
    } else {
        $thoi_diem_block .= "  │ THỜI ĐIỂM THUẬN: DT không bị hợp trụ. Thuận vào ngày/tháng hành {$dt_hanh} hoặc hành sinh {$dt_hanh}.\n";
    }

    // ── 8. BUILD DATA BLOCK ────────────────────────────────────
    $time_solar = wp_date('d/m/Y', $timestamp);
    $time_hour  = wp_date('H:i', $timestamp);
    $data_text  = "THỜI GIAN GIEO QUẺ: Giờ {$time_hour}, ngày {$lunar_str} Âm lịch (Dương lịch: {$time_solar})\n";
    $data_text .= "CAN CHI: {$calendar_str}\n";

    $data_text .= "  - Nguyệt Lệnh: {$cc['hanh_thang']} (Chi {$cc['nguyet_lenh']})\n";
    $data_text .= "  - Nhật Kiến:   {$cc['hanh_ngay']} (Chi {$cc['nhat_kien']})\n\n";

    $data_text .= $bien
        ? "TRẠNG THÁI: Có hào động — tồn tại Quẻ Biến.\n\n"
        : "TRẠNG THÁI: Quẻ Tĩnh — không có hào động, không có Quẻ Biến.\n\n";

    $data_text .= "LỤC HÀO NẠP GIÁP:\n";
    $data_text .= "  - Cung: {$luchao_data['cung']} (Ngũ hành Cung: {$luchao_data['hanh_cung']})\n\n";

    if ($dt_hanh) {
        $dt_is_tk = ($dt_chi && in_array($dt_chi, $tuan_khong_arr)) ? ' [LÂM TUẦN KHÔNG]' : '';
        $data_text .= "  ┌─ DỤNG THẦN [{$dung_than}] ─┐\n";
        $data_text .= "  │ Lý do chọn: {$dt_reason}\n";
        $data_text .= "  │ Ngũ hành: {$dt_hanh} | Địa chi đại diện: {$dt_chi}{$dt_is_tk}\n";
        $data_text .= $dt_lines_summary;
        $data_text .= "  │ Vượng/Suy Nguyệt Lệnh: {$dt_vuong_suy}\n";
        $data_text .= "  │ Nhật Kiến tác động: {$dt_nhat_kien}\n";
        if ($dt_nhat_xung)   $data_text .= "  │ {$dt_nhat_xung}\n";
        if ($dt_nhat_hop)    $data_text .= "  │ {$dt_nhat_hop}\n";
        if ($dt_nguyet_xung) $data_text .= "  │ {$dt_nguyet_xung}\n";
        if ($dt_nguyet_hop)  $data_text .= "  │ {$dt_nguyet_hop}\n";
        if (!$dt_nhat_xung && !$dt_nhat_hop && !$dt_nguyet_xung && !$dt_nguyet_hop) {
            $data_text .= "  │ Nhật/Nguyệt không xung/hợp chi Dụng Thần\n";
        }
        $data_text .= $thoi_diem_block;
        $data_text .= "  └──────────────────────────────────────────────────────────────┘\n\n";
    }

    if ($phuc_than_block) {
        $data_text .= "  ┌─ PHỤC THẦN ─┐\n";
        $data_text .= $phuc_than_block;
        $data_text .= "  └─────────────┘\n\n";
    }

    $tuan_khong_arr = Iching_Calendar::getTuanKhong($cc['ngay']);
    $loc_than = Iching_Calendar::getLoc(explode(' ', $cc['ngay'])[0]);
    $dich_ma = Iching_Calendar::getMa($cc['nhat_kien']);
    $quy_nhan = Iching_Calendar::getQuyNhan(explode(' ', $cc['ngay'])[0]);
    $dao_hoa = Iching_Calendar::getDaoHoa($cc['nhat_kien']);

    $data_text .= "  CHI TIẾT 6 HÀO (Hào 6 → Hào 1):\n";
    for ($i = 6; $i >= 1; $i--) {
        $ld    = $luchao_data['lines'][$i];
        $pos   = $ld['is_the'] ? ' [Thế]' : ($ld['is_ung'] ? ' [Ứng]' : '');
        $is_dt = ($ld['luc_than'] === $dung_than) ? ' ★DỤNG THẦN★' : '';

        $vs_hao = Iching_Calendar::getVuongSuy($ld['hanh'], $cc['hanh_thang']);
        $is_tk = in_array($ld['chi'], $tuan_khong_arr) ? ' [TUẦN KHÔNG]' : '';

        $than_sat = [];
        if ($ld['chi'] === $loc_than) $than_sat[] = 'Lộc';
        if ($ld['chi'] === $dich_ma)  $than_sat[] = 'Mã';
        if (in_array($ld['chi'], $quy_nhan)) $than_sat[] = 'Quý Nhân';
        if ($ld['chi'] === $dao_hoa)  $than_sat[] = 'Đào Hoa';
        $ts_str = !empty($than_sat) ? ' | Thần Sát: ' . implode(', ', $than_sat) : '';

        $line_str = "  Hào {$i}: {$ld['chi']} {$ld['hanh']} (Vượng Suy: {$vs_hao})"
            . " | Lục Thân: {$ld['luc_than']}"
            . " | Lục Thú: {$ld['luc_thu']}"
            . $ts_str
            . "{$pos}{$is_dt}{$is_tk}";

        if (in_array($i, $changing_lines) && $luchao_bien) {
            $lb         = $luchao_bien['lines'][$i];
            $hoi_dau    = _iching_hoi_dau($lb['hanh'], $ld['hanh']);
            $dong_vs_dt = $dt_chi
                ? _iching_dong_vs_dt($ld['chi'], $ld['hanh'], $dt_chi, $dt_hanh)
                : 'Chưa xác định Dụng Thần';
            $bien_vs_dt = $dt_chi
                ? _iching_dong_vs_dt($lb['chi'], $lb['hanh'], $dt_chi, $dt_hanh)
                : 'Chưa xác định Dụng Thần';

            $is_bien_tk = in_array($lb['chi'], $tuan_khong_arr) ? ' [TUẦN KHÔNG]' : '';

            $line_str .= " [ĐỘNG]";
            $line_str .= "\n    ├ Biến thành: {$lb['chi']} {$lb['hanh']} ({$lb['luc_than']}){$is_bien_tk}";
            $line_str .= "\n    ├ Hồi đầu (Biến→Động): {$hoi_dau}";
            $line_str .= "\n    ├ Hào động ({$ld['chi']}) vs DT ({$dt_chi}): {$dong_vs_dt}";
            $line_str .= "\n    └ Hào biến ({$lb['chi']}) vs DT ({$dt_chi}): {$bien_vs_dt}";
        }

        $data_text .= $line_str . "\n";
    }

    // ── 9. SENSITIVE & DIRECTIVE ───────────────────────────────
    $has_phuc_than = ($phuc_than_data !== null);
    $has_dong_hao  = !empty($changing_lines);
    $dt_count      = count($dt_all_lines);
    $sensitive     = _iching_detect_sensitive($question);
    $directive     = _iching_build_directive($sensitive['is_sensitive'], $sensitive['category'], $question);

    // ── 10. ASSEMBLE PROMPT ────────────────────────────────────
    $output_structure = _iching_output_structure(
        $name, $dung_than, $dt_reason, $dt_vuong_suy, $dt_nhat_kien,
        $has_phuc_than, $has_dong_hao, $dt_count,
        $smart_topic_label,
        $question
    );

    $rules = _iching_rules($name, $dung_than, $sensitive['is_sensitive']);

    return <<<TXT
Nhiệm vụ: Luận Quẻ Kinh Dịch theo phương pháp Lục Hào Nạp Giáp (không gieo quẻ).
Chỉ luận giải quẻ theo thông tin ĐÃ TÍNH SẴN, CHỈ ĐỌC, KHÔNG TÍNH LẠI.
{$directive}
DỤNG THẦN: {$dung_than}

THÔNG TIN NGƯỜI HỎI:
- Tên: {$name}
- Giới tính: {$gender}
- Câu hỏi: {$question}

DỮ LIỆU QUẺ:
{$data_text}
{$output_structure}
{$rules}
TXT;
}

function _iching_output_structure(
    string $name,
    string $dung_than,
    string $dt_reason,
    string $dt_vuong_suy,
    string $dt_nhat_kien,
    bool   $has_phuc_than,
    bool   $has_dong_hao,
    int    $dt_count = 1,
    string $smart_topic_label = '',
    string $question = ''
): string {
    $phuc_block = $has_phuc_than
        ? "Dụng Thần là PHỤC THẦN (ẩn). Đọc dữ liệu Phi/Phục đã cho:\n"
        . "  - Nêu quan hệ Phi/Phục (đã tính sẵn) → DT được nuôi / bị kìm / tiết khí như thế nào.\n"
        . "  - Nêu trạng thái hợp trụ / xung Phi của Nhật/Nguyệt (đã tính sẵn).\n"
        . "  - KẾT LUẬN RÕ: Phục đang LỘ hay ẨN? Điều kiện nào khiến nó lộ?"
        : ($dt_count > 1
            ? "Dụng Thần [{$dung_than}] xuất hiện tại {$dt_count} hào (xem danh sách trong block Dụng Thần).\n"
            . "  BẮT BUỘC liệt kê và nhận xét TẤT CẢ {$dt_count} hào: vị trí, Lục Thú, tốt/xấu cho Dụng Thần.\n"
            . "  KHÔNG bỏ qua hào nào."
            : "DT [{$dung_than}] xuất hiện tại 1 hào (xem block DT). Nêu Lục Thú, vị trí Thế/Ứng và ý nghĩa.");

    $dong_block = $has_dong_hao
        ? "Với MỖI hào động (đọc đúng thứ tự từ dữ liệu):\n"
        . "  1. Lục Thú: tên và tính chất.\n"
        . "  2. Hồi đầu: chỉ đọc giá trị đã tính sẵn, diễn giải ý nghĩa, KHÔNG tính lại.\n"
        . "     ★ Hồi đầu KHẮC = hào động bị chế — KHÔNG phải Dụng Thần phản công.\n"
        . "  3. Tác động lên Dụng Thần: đọc giá trị đã tính sẵn (cả hào động lẫn hào biến), diễn giải.\n"
        . "  4. Kết luận: hào này có lợi / bất lợi / trung tính cho Dụng Thần?"
        : "Quẻ Tĩnh — không có hào động. Luận thuần vào vượng/suy Dụng Thần và trạng thái Phục Thần (nếu có).";

    $is_special = false;
    $sensitive_note = '';
    $practical_advice = '';

    switch ($smart_topic_label) {
        case 'Sức khỏe nghiêm trọng / Tính mạng':
            $is_special = true;
            $sensitive_note = "Câu hỏi liên quan đến sức khỏe nghiêm trọng / tính mạng.";
            $practical_advice = "Tình trạng sức khỏe nghiêm trọng cần được theo dõi bởi đội ngũ y tế — bác sĩ điều trị là người có đủ thông tin nhất để đưa ra phán đoán chính xác.";
            break;
        case 'Tâm lý khủng hoảng / Tự hại':
            $is_special = true;
            $sensitive_note = "Câu hỏi liên quan đến tâm lý khủng hoảng.";
            $practical_advice = "Nếu đang trải qua giai đoạn khó khăn về tâm lý, hãy chia sẻ với người thân hoặc chuyên gia tâm lý — quẻ dịch có thể soi chiếu xu hướng nhưng không thay thế được sự đồng hành của con người.";
            break;
        case 'Nhạy cảm / Xung đột / Gây hại':
        case 'Nhạy cảm / Hành vi bạo lực, phi pháp':
            $is_special = true;
            $sensitive_note = "Câu hỏi chứa yếu tố xung đột, bạo lực hoặc rủi ro pháp lý.";
            $practical_advice = "Nếu có mâu thuẫn hoặc ý định vi phạm pháp luật, hãy dừng lại và tìm đến sự tư vấn từ cơ quan chức năng. Hành vi bạo lực/phi pháp luôn dẫn đến hậu quả tai hại.";
            break;
        case 'Chính trị / Xã hội':
            $is_special = true;
            $sensitive_note = "Câu hỏi liên quan đến chính trị, xã hội vĩ mô.";
            $practical_advice = "Quẻ Dịch phản ánh xu hướng thời thế khách quan, không đại diện cho lập trường chính trị cá nhân hay tổ chức nào.";
            break;
        case 'Câu hỏi vui / Không nghiêm túc':
            $is_special = true;
            $sensitive_note = "Câu hỏi mang tính chất giải trí, giả định.";
            $practical_advice = "Đây là phần luận giải mang tính chất tham khảo vui vẻ dựa trên cơ chế ngũ hành và quy luật Dịch lý.";
            break;
    }

    if ($is_special) {
        $this_conclusion = "### Quan sát từ Quẻ\n"
            . "{$sensitive_note} Quẻ Dịch soi chiếu xu hướng — không phán quyết kết quả cụ thể.\n"
            . "Dựa trên phân tích Phần 1, mô tả trung lập xu hướng hiện tại:\n"
            . "  - Dụng Thần đang ở trạng thái nào (mạnh/yếu/bị cản/bị tiết)?\n"
            . "  - Quẻ Biến cho thấy chiều hướng chuyển động như thế nào?\n"
            . "KHÔNG đưa ra kết luận TỐT / TRUNG BÌNH / XẤU.\n"
            . "KHÔNG dùng ngôn ngữ khẳng định kết quả (ví dụ: sẽ thành công, sẽ thoát tội, sẽ xảy ra...).";

        $this_guidance = "### Lời nhắn\n"
            . "* **Từ góc độ quẻ:** (1 quan sát xu hướng — dùng ngôn ngữ mềm: \"quẻ cho thấy...\", \"xu hướng...\")\n"
            . "* **Thực tế:** {$practical_advice}\n"
            . "* **Thời điểm chú ý:** (đọc từ block THỜI ĐIỂM THUẬN trong dữ liệu, diễn giải nhẹ nhàng)";
    } else {
        $this_conclusion = "### KẾT LUẬN: [chọn đúng 1 trong 3: TỐT / TRUNG BÌNH / XẤU]\n"
            . "1-2 câu phản ánh ĐÚNG tổng trạng thái — bao gồm cả lực cản nếu có.\n"
            . "KHÔNG kết luận TỐT khi DT đang bị ẩn / hợp trụ / hào động gây áp lực đồng thời.";

        $this_guidance = "### Chỉ dẫn\n"
            . "* **Nên làm:** (1 hành động, đọc trực tiếp từ trạng thái sinh/khắc/xung/hợp — KHÔNG từ ý nghĩa Lục Thú hay Lục Thân)\n"
            . "* **Nên tránh:** (1 điều, đọc trực tiếp từ lực cản đang tác động lên DT — KHÔNG suy từ tính chất Lục Thú)\n"
            . "* **Thời điểm thuận:** (đọc đúng từ block THỜI ĐIỂM THUẬN trong dữ liệu, chỉ nêu hành và các chi tương ứng)";
    }

    return <<<TXT

YÊU CẦU OUTPUT:
1. TUYỆT ĐỐI KHÔNG dùng các gạch đầu dòng phân mục như a), b), c), d), e), f) hay 1, 2, 3, 4 để làm tiêu đề trong bài viết.
2. CÁC ĐOẠN (Tư duy ngầm)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ LUẬN GIẢI, không được đưa vào nội dung OUTPUT.
3. Trả về đúng format [AST_RESULT][/AST_RESULT], sử dụng Markdown, định dạng văn bản dễ đọc.
4. Sử dụng in đậm in nghiêng hợp lý, không lạm dụng, (không sử dụng ---, ***, ___, thẻ hr).
5. KHÔNG in tiêu đề phụ máy móc. không in những hướng dẫn, suy nghĩ nội bộ vào nội dung.
6. Cấm suy diễn, mở rộng, hoặc bổ sung dữ kiện ngoài các trường/dữ liệu đã nêu; chỉ được diễn giải lại đúng ý nghĩa trực tiếp của dữ liệu có sẵn.

[AST_RESULT]

### Phần 1 — Luận giải Quẻ
a) Dụng Thần: {$dung_than}.
(Tư duy ngầm - KHÔNG in ra bài): Tự kiểm tra lại câu hỏi, đối chiếu bảng chuẩn trong R0:
  - Nếu ĐÚNG → Viết: "{$dung_than} (Câu hỏi của {$name} về [chủ đề])".
  - Nếu SAI  → Xác định Dụng Thần đúng, viết theo format trên, dùng Dụng Thần mới cho toàn bộ phân tích.
KHÔNG viết: "Xác nhận đúng", "Hệ thống gợi ý", "lý do:", hay bất kỳ câu quy trình nào.

b) Vượng/Suy Dụng Thần*:
(Tư duy ngầm - KHÔNG in ra bài): Dùng đúng hai giá trị đã tính sẵn:
  - Nguyệt Lệnh: {$dt_vuong_suy}
  - Nhật Kiến: {$dt_nhat_kien}
KHÔNG tự nâng/hạ cấp (Tướng ≠ cực vượng | Tử ≠ hơi suy).
Nêu: Nhật/Nguyệt có xung hay hợp trụ chi Dụng Thần không?
→ Tổng hợp: Dụng Thần mạnh / trung / yếu — có bị ràng buộc không?

c) Trạng thái Dụng Thần:
{$phuc_block}
(Tư duy ngầm): BẮT BUỘC kiểm tra xem Dụng Thần có bị [TUẦN KHÔNG] không? Nếu có, phải kết luận rõ là sự việc đang bị đình trệ/ảo ảnh, chưa thể xảy ra ngay. Thần Sát của Dụng Thần là gì (nếu có)?

d) Hào động — tác động lên Dụng Thần
{$dong_block}

e) Thế – Ứng
(Tư duy ngầm - KHÔNG in ra bài): Hào Thế đại diện cho {$name}, hào Ứng đại diện cho đối phương / sự việc bên ngoài.
Nêu theo thứ tự:
  1. Thế: chi, ngũ hành, Lục Thân, Lục Thú — vượng/suy theo Nguyệt Lệnh đã cho.
  2. Ứng: chi, ngũ hành, Lục Thân, Lục Thú — vượng/suy theo Nguyệt Lệnh đã cho.
  3. Quan hệ Thế – Ứng: sinh/khắc/tỷ hòa giữa hai hành.
  4. Kết luận: Thế mạnh hay Ứng mạnh hơn — xét cả ngũ hành lẫn vượng/suy đồng thời.
NGHIÊM CẤM:
  ✗ Kết luận "Thế kiểm soát được Ứng" chỉ vì Thế khắc Ứng — phải xét vượng/suy trước.
  ✗ Dùng số thứ tự hào (1–6) để đánh giá tôn/ti (ví dụ: "hào 5 tôn quý").
  ✗ Gán danh tính cụ thể cho Ứng — chỉ được nêu Lục Thân.

f) Tổng hợp lực tác động
(Tư duy ngầm - KHÔNG in ra bài):
Liệt kê TẤT CẢ lực tác động (sinh, khắc, hợp trụ, xung) lên Dụng Thần.
Nếu vừa có lực sinh vừa có lực cản → nêu RÕ CẢ HAI.
KHÔNG chỉ lấy mặt tích cực. KHÔNG che giấu lực cản.

### Phần 2 — Tổng Luận

(Tư duy ngầm từ câu hỏi — KHÔNG in ra bài):
  Lấy kết quả đã phân tích ở Phần 1 làm gốc.
  Đối chiếu xem trạng thái Dụng Thần có thực sự ứng nghiệm với nội dung câu hỏi hay không.
  KHÔNG điều chỉnh ý nghĩa quẻ để khớp câu hỏi.
  Nếu quẻ không ứng trực tiếp → trình bày đúng thực trạng quẻ, không bịa thêm.

(Nguyên lý tối thượng): DỮ LIỆU QUẺ LÀ GỐC.
  Chỉ dùng kết quả vượng/suy, sinh/khắc, xung/hợp, hào động đã xác định ở Phần 1 để soi chiếu câu hỏi.
  KHÔNG bóp méo, khiên cưỡng hay "chế" thêm ý nghĩa để chiều theo câu hỏi.

(Viết Tổng Luận):
  - Trình bày thành các đoạn văn tự nhiên, trôi chảy, liền mạch.
  - Đi thẳng vào trạng thái và xu hướng theo dữ liệu quẻ (sinh/khắc, vượng/suy, xung/hợp).
  - KHÔNG dịch sinh/khắc thành kịch bản đời thực (ví dụ: "tiền bị chiếm dụng", "đối phương không thành thật").
  - KHÔNG dùng từ "tượng" để mô tả kết hợp lục thân/lục thú.
  - KHÔNG dùng tiêu đề phụ máy móc ("Vận hạn:", "Xu hướng:", "Thời điểm:"...).
  - KHÔNG trả lời theo dạng có/không, chắc chắn/không chắc.
  - Chuyển thành mô tả trạng thái và diễn biến: thuận/nghịch, mạnh/yếu, bế/thông, tiến/dừng...
  - Cấm lặp lại nguyên văn các từ ngữ bạo lực, nhạy cảm từ câu hỏi.

### Xu hướng — Hào Động và Thời Điểm
Xu hướng xét từ: trạng thái Dụng Thần + tác động hào động (nếu có) + chiều chuyển biến.
KHÔNG dùng khái niệm "Quẻ Biến phản ánh..." — trong Lục Hào, xu hướng đọc từ hào động, không từ tượng quẻ biến.
Thời điểm thuận: đọc đúng từ block THỜI ĐIỂM THUẬN trong dữ liệu.
  - Nếu data ghi "thuận vào hành X hoặc sinh X" → chỉ được liệt kê các chi thuộc hành X và hành sinh X.
  - KHÔNG thêm bất kỳ tháng/ngày/chi nào ngoài phạm vi đó.
  - KHÔNG tự tính thêm, KHÔNG mở rộng suy luận.

{$this_conclusion}

{$this_guidance}

[/AST_RESULT]

TXT;
}


function _iching_rules(string $name, string $dung_than, bool $is_sensitive = false): string {
    $r12 = $is_sensitive ? <<<BLOCK

▌R12. CÂU HỎI NHẠY CẢM — CHẾ ĐỘ QUAN SÁT
Câu hỏi này thuộc nhóm nhạy cảm (sức khỏe nghiêm trọng / tính mạng / tâm lý khủng hoảng).
Áp dụng toàn bộ R1–R11 cho phần phân tích quẻ như bình thường.
Riêng phần KẾT LUẬN và CHỈ DẪN: tuân thủ nghiêm ngặt template "Quan sát từ Quẻ" + "Lời nhắn".
  ✗ KHÔNG dùng: "sẽ", "chắc chắn", "qua khỏi", "không qua được", "sắp mất", "còn sống"
  ✗ KHÔNG đưa ra tiên lượng dưới bất kỳ hình thức nào — kể cả ngụ ý
  ✓ Dùng: "quẻ cho thấy...", "xu hướng...", "cục diện hiện tại..."
  ✓ Luôn kết thúc bằng lời nhắn thực tế đã cung cấp sẵn
BLOCK
        : '';

    return <<<TXT
QUY TẮC BẮT BUỘC — VI PHẠM = KẾT QUẢ SAI CHUYÊN MÔN

▌R0. DỤNG THẦN — TỰ XÁC ĐỊNH TRƯỚC KHI PHÂN TÍCH
Bảng Dụng Thần chuẩn:
  Công việc / sự nghiệp / chức vụ        → Quan Quỷ
  Học hành / thi cử / giấy tờ / hợp đồng → Phụ Mẫu
  Tiền bạc / đầu tư / tài lộc / đồ vật   → Thê Tài
  Tình cảm / Vợ chồng (người hỏi là Nam)  → Thê Tài
  Tình cảm / Vợ chồng (người hỏi là Nữ)  → Quan Quỷ
  Con cái / mang thai / sinh đẻ          → Tử Tôn
  Sức khỏe / bệnh tật / an nguy          → Tử Tôn
  Tìm người / mất tích / sinh mệnh       → Tử Tôn
  Vận hạn tổng quan / không rõ           → Tử Tôn

Quy trình kiểm tra (nội bộ — KHÔNG viết ra output):
  1. Đọc câu hỏi → xác định chủ đề CHÍNH.
  2. Đối chiếu bảng → xác định DT đúng.
  3. Khớp gợi ý → dùng gợi ý.
  4. Không khớp → tự xác định DT đúng, dùng cho TOÀN BỘ phân tích.

FORMAT OUTPUT: "[tên Dụng Thần] (Câu hỏi của [tên ngắn] liên quan đến [chủ đề])".
NGHIÊM CẤM: "Xác nhận đúng", "Hệ thống gợi ý", "lý do:", hay bất kỳ câu quy trình nào.

Lưu ý đặc biệt:
  - Hỏi về con cái, đường con cái, mang thai -> CHẮC CHẮN dùng Tử Tôn.
  - "con / gia đình / người thân" + "tìm kiếm / mất tích / ở đâu / còn sống" → Tử Tôn
  - Hỏi về sức khỏe/an nguy của người khác → Tử Tôn
  - Hỏi về tình cảm vợ chồng (dù có từ "gia đình") → Thê Tài / Quan Quỷ

▌R1. DỤNG THẦN = {$dung_than}
Toàn bộ phân tích xoay quanh Dụng Thần. Các yếu tố khác chỉ có giá trị khi TÁC ĐỘNG lên DT.

▌R2. NGŨ HÀNH — BẮT BUỘC ĐÚNG
Tương sinh: Kim→Thủy→Mộc→Hỏa→Thổ→Kim
Tương khắc: Kim→Mộc→Thổ→Thủy→Hỏa→Kim

▌R3. VƯỢNG SUY — KHÔNG TỰ TÍNH LẠI, KHÔNG THAY ĐỔI CẤP ĐỘ
Dữ liệu đã tính sẵn. Dùng đúng: Vượng / Tướng / Hưu / Tù / Tử.
NGHIÊM CẤM: "Tướng" → "cực vượng" | "Tử" → "hơi suy" | bất kỳ biến thể nào.

▌R4. HỒI ĐẦU — CHIỀU ĐÚNG: BIẾN → ĐỘNG
Hồi đầu = hào BIẾN tác động ngược lên hào ĐỘNG. (KHÔNG phải Động → Biến)
Dữ liệu đã tính sẵn. CHỈ diễn giải, KHÔNG tính lại.
Ví dụ:
  Dần Mộc động → biến Dậu Kim: "Hồi đầu KHẮC — hào động bị chế"
  → Diễn giải đúng: "hào động mất lực" — KHÔNG phải "DT phản công"

▌R5. TÁC ĐỘNG HÀO ĐỘNG LÊN DT — ĐÃ TÍNH SẴN
Dữ liệu ghi rõ tác động của hào động VÀ hào biến lên DT.
CHỈ đọc và diễn giải. KHÔNG tính lại.

▌R6. HỢP TRỤ / XUNG — KHÔNG TỰ THÊM, KHÔNG TỰ BỎ
Chỉ dùng các hợp/xung đã ghi trong dữ liệu.
NGHIÊM CẤM tự tạo khái niệm như "nhị Thìn hợp Dậu", "tam hợp"...

▌R7. LỤC THÚ — BẮT BUỘC ĐỀ CẬP KHI PHÂN TÍCH HÀO ĐỘNG
  Thanh Long → cát tường, cơ hội, quý nhân
  Chu Tước   → khẩu thiệt, tranh tụng, văn thư
  Câu Trần   → trì trệ, cản trở, quan liêu
  Đằng Xà    → bất an, hư hao, biến ảo khó lường
  Bạch Hổ    → biến động mạnh, thương tổn, xung đột
  Huyền Vũ   → gian lận, tiểu nhân, mất mát âm thầm

▌R8. PHỤC THẦN — CHỈ DÙNG DỮ LIỆU ĐÃ TÍNH SẴN
Điều kiện lộ/ẩn đã tính sẵn. KHÔNG thêm điều kiện ngoài dữ liệu.

▌R9. KẾT LUẬN — PHẢN ÁNH ĐÚNG THỰC TRẠNG
Chỉ chọn 1: TỐT / TRUNG BÌNH / XẤU
  TỐT        → DT Vượng/Tướng, được sinh, không bị hợp trụ, không bị ẩn
  TRUNG BÌNH → DT có cả sinh lẫn cản, hoặc mạnh nhưng bị ẩn/hợp trụ
  XẤU        → DT Tử/Tù, bị khắc nặng, Phục bị kìm không lộ được
NGHIÊM CẤM kết luận TỐT khi DT đang bị ẩn + hợp trụ + hào động gây áp lực.

▌R10. KHÔNG TỰ THÊM THÔNG TIN NGOÀI DỮ LIỆU
Chỉ dùng những gì trong "DỮ LIỆU QUẺ". KHÔNG suy đoán, KHÔNG tạo khái niệm mới.

▌R11. NGHIÊM CẤM SUY DIỄN — CHỈ ĐƯỢC DIỄN GIẢI
Suy diễn = lấy 1 dữ kiện rồi tự rút kết luận thứ cấp không có trong dữ liệu.
Diễn giải = đọc giá trị đã tính sẵn rồi giải thích ý nghĩa.

Ví dụ CẤM:
  ✗ "Hồi đầu SINH → lực khắc của hào động lên DT càng mạnh"
  ✗ "DT mạnh → chắc chắn phát huy trong tháng tới"
  ✗ "Bạch Hổ → có người cạnh tranh tên X"

Ví dụ ĐÚNG:
  ✓ "Hồi đầu SINH → hào động được bổ khí, không bị suy"
  ✓ "DT trạng thái Tướng → nội lực tương đối mạnh trong tháng này"
  ✓ "Bạch Hổ tại hào động → biến cố mang tính áp lực, xung đột"

Kiểm tra trước khi viết mỗi câu:
  → Câu này có cơ sở trực tiếp từ dữ liệu đã cho không?
  → Nếu KHÔNG → xóa câu đó.
{$r12}

▌R13. NGHIÊM CẤM SUY DIỄN TÂM LÝ — GIỮ BẢN CHẤT KINH DỊCH
Kinh Dịch = phân tích KHÁCH QUAN qua vượng/suy, sinh/khắc, xung/hợp.

CẤM viết:
  ✗ "đang trong trạng thái chiến tranh lạnh"
  ✗ "không bùng nổ nhưng không ngừng gây sức ép"
  ✗ "nước ngấm sâu vào đất, không ồn ào mà dai dẳng"
  ✗ Bất kỳ ẩn dụ văn chương nào không có trong dữ liệu

ĐÚNG viết:
  ✓ "DT Tù — ngũ hành bị kìm trong tháng này"
  ✓ "Nhật Kiến khắc DT → thêm áp lực trong ngày"
  ✓ "Vượng suy Tử — DT yếu, không có lực phát"

Nguyên tắc:
  - Chỉ nói về NGŨ HÀNH, VƯỢNG/SUY, SINH/KHẮC, XUNG/HỢP
  - KHÔNG diễn giải thành cảm xúc, tâm lý, trạng thái tinh thần
  - KHÔNG đánh giá chủ quan (quẻ đẹp/xấu, tình huống bi thảm/may mắn...)
  - KHÔNG dùng ẩn dụ văn học

▌R14. TỔNG LUẬN — DỮ LIỆU QUẺ LÀ GỐC, KHÔNG CHIỀU THEO CÂU HỎI
Nguyên tắc cốt lõi: Phân tích Phần 1 xong mới soi vào câu hỏi.
  - Nếu trạng thái DT ứng với câu hỏi → trình bày theo đúng thực trạng.
  - Nếu không ứng trực tiếp → vẫn trình bày đúng thực trạng quẻ, KHÔNG bịa thêm.
NGHIÊM CẤM bóp méo, khiên cưỡng ý nghĩa quẻ để khớp với câu hỏi.
KHÔNG trả lời theo dạng có/không, chắc chắn/không chắc.
Mô tả bằng: thuận/nghịch, mạnh/yếu, bế/thông, tiến/dừng, hợp/khắc...

CẤM SUY DIỄN THỰC TẾ từ kết quả sinh/khắc:
  ✗ "tiền đã bị tiêu hết" (chỉ có dữ kiện: Huynh khắc Tài)
  ✗ "đối phương không có tiền trả" (chỉ có dữ kiện: Ứng khắc DT)
  ✗ "quan hệ sẽ rạn nứt" (chỉ có dữ kiện: Bạch Hổ tại hào)
  ✗ "đối phương không trốn tránh" / "có thiện chí" (chỉ có dữ kiện: Ứng sinh DT)
  ✗ "đối phương gặp khó khăn tài chính" (chỉ có dữ kiện: Hồi đầu khắc hào Ứng)
  ✓ ĐÚNG: "DT bị Huynh khắc — bị giữ / bị cản, khó thu hồi"
  ✓ ĐÚNG: "Ứng sinh DT — có lực tác động thuận lên DT"
  ✓ ĐÚNG: "Ứng bị Hồi đầu khắc — lực sinh của Ứng lên DT bị suy giảm"
  ✓ ĐÚNG: "Bạch Hổ tại hào động — biến cố mang tính áp lực, xung đột"

CẤM DỊCH LỤC THÚ THÀNH LỜI KHUYÊN CHIẾN THUẬT:
  ✗ "Chu Tước → nên dùng lời nói/giấy tờ để đòi tiền"
  ✗ "Huyền Vũ → đừng tin lời hứa"
  ✗ "Bạch Hổ → tránh dùng biện pháp mạnh"
  ✓ ĐÚNG ở Chỉ dẫn: chỉ được nêu trạng thái sinh/khắc/xung/hợp và để người đọc tự suy luận hành động.

▌R15. NGHIÊM CẤM DÙNG TƯỢNG QUẺ ĐỂ LUẬN SỰ VIỆC
Lục Hào Nạp Giáp KHÔNG dùng ý nghĩa, tên gọi hay tượng của quẻ để phán đoán.
CẤM TUYỆT ĐỐI:
  ✗ "Quẻ Sơn Hỏa Bí có nghĩa là trang sức, vẻ bề ngoài hào nhoáng..."
  ✗ "Quẻ Thủy Hỏa Ký Tế → việc đã hoàn thành..."
  ✗ Bất kỳ diễn giải nào dựa trên tên quẻ, Thoán từ, Hào từ, hay tượng quẻ
Tên quẻ trong dữ liệu chỉ để nhận dạng — KHÔNG dùng để luận sự việc.
Toàn bộ luận giải chỉ được dựa vào: Nạp Giáp + Lục Thân + Lục Thú + Vượng/Suy + Sinh/Khắc + Xung/Hợp + Tuần Không + Thần Sát.

▌R16. LỤC THÚ — GIỚI HẠN MỨC ĐỘ DIỄN GIẢI
Lục Thú chỉ cung cấp "tính chất" của hào — KHÔNG được suy ra hành vi cụ thể.
Mức cho phép:
  ✓ Câu Trần → "trì trệ, chậm trễ, vướng mắc"
  ✓ Bạch Hổ → "áp lực lớn, xung đột, tổn thất"
  ✓ Huyền Vũ → "mất mát âm thầm, gian lận"
Mức CẤM (suy diễn hành vi):
  ✗ Câu Trần → "hứa hẹn nhưng không thực hiện, kéo dài thời gian"
  ✗ Bạch Hổ → "nếu thúc ép sẽ rạn nứt quan hệ"
  ✗ Huyền Vũ → "đối phương đang lừa dối có chủ đích"

▌R17. VỊ TRÍ HÀO — KHÔNG DÙNG KHÁI NIỆM "TÔN QUÝ" / "CAO THẤP"
Trong Lục Hào Nạp Giáp, vị trí hào chỉ dùng để xác định Thế/Ứng.
CẤM TUYỆT ĐỐI:
  ✗ "Hào 5 là hào tôn quý" (khái niệm thuộc Kinh Dịch tượng học)
  ✗ "Hào thượng là hào cực" / "Hào 1 là hào thấp nhất"
  ✗ Bất kỳ đánh giá vị trí hào theo thứ bậc cao thấp
  ✓ ĐÚNG: "Hào 5 lâm Bạch Hổ — mang tính chất áp lực, xung đột"

▌R18. THỜI ĐIỂM — CHỈ ĐỌC TỪ BLOCK "THỜI ĐIỂM THUẬN", KHÔNG TỰ SUY
Dữ liệu đã tính sẵn hành cần thiết. Chỉ được diễn giải hành đó thành các chi tương ứng.
CẤM:
  ✗ Tự liệt kê tháng cụ thể không có trong dữ liệu (ví dụ: "Tháng Hợi, Tý, Thân, Dậu")
  ✗ Tự tính thêm can chi ngoài block đã cho
ĐÚNG:
  ✓ Data ghi "thuận vào hành Thủy hoặc sinh Thủy"
     → Diễn giải: "ngày/tháng mang hành Thủy (Hợi, Tý) hoặc hành Kim sinh Thủy (Thân, Dậu)"
     → Không thêm gì khác ngoài đây.

▌R19. TUẦN KHÔNG — QUY TẮC PHỦ QUYẾT:
Nếu Dụng Thần hoặc Hào Động có chữ [TUẦN KHÔNG], mọi năng lực sinh/khắc bị đình trệ. Việc tốt chưa thể thành, việc xấu chưa thể họa, phải đợi đến ngày/tháng "xuất Không" (trùng với chi của hào đó) mới ứng nghiệm. BẮT BUỘC phải nhấn mạnh điều này trong bài phân tích.

▌R20. THẦN SÁT — CHẤT XÚC TÁC:
Chỉ dùng Thần Sát (Lộc, Mã, Quý Nhân, Đào Hoa) để thêm chi tiết cho phần Hướng dẫn. (Ví dụ có Mã thì khuyên dịch chuyển, có Lộc thì thuận lợi tiền bạc...).

PHONG CÁCH:
  Xưng hô: Trích xuất tên ví dụ: Nguyễn Thị Lan -> Xưng hô Lan (hoặc xưng hô Bạn), đảm bảo đồng nhất. CẤM dùng: anh, chị hay anh/chị, họ, em, mày.
  Ngôn ngữ tự nhiên, chuyên môn sâu — KHÔNG hoa mỹ, KHÔNG sáo rỗng
  Không chào hỏi | Không CTA | Không gợi ý thêm cuối bài
  Trả về Markdown chuẩn, sử dụng in đậm, in nghiêng hợp lý, không lạm dụng.
  Chỉ trả về đúng [AST_RESULT]...[/AST_RESULT]
TXT;
}

function _iching_build_directive(bool $is_sensitive, string $category, string $question): string {
    $q = mb_strtolower($question, 'UTF-8');

    $is_troll_meme = false;
    $troll_signals = ['meme', 'troll', 'haha', 'lol', ':v', '😂', 'ngáo', 'phá đám', 'thử xem'];
    foreach ($troll_signals as $s) {
        if (mb_strpos($q, $s, 0, 'UTF-8') !== false) { $is_troll_meme = true; break; }
    }

    $is_political = false;
    $political_signals = ['chính trị', 'chiến tranh', 'bầu cử', 'đảng', 'tổng thống', 'thủ tướng', 'quân đội', 'xâm lược', 'cách mạng'];
    foreach ($political_signals as $s) {
        if (mb_strpos($q, $s, 0, 'UTF-8') !== false) { $is_political = true; break; }
    }

    $is_violent = false;
    $violent_signals = [
        'giết', 'chém', 'bắn', 'hiếp', 'hãm hiếp', 'tra tấn', 'bạo lực', 'đánh chết',
        'cướp', 'trộm', 'lừa đảo', 'tống tiền', 'buôn lậu', 'ma túy', 'chất cấm',
        'rửa tiền', 'làm giả', 'hack', 'tấn công mạng',
    ];
    foreach ($violent_signals as $s) {
        if (mb_strpos($q, $s, 0, 'UTF-8') !== false) { $is_violent = true; break; }
    }

    $is_special = $is_sensitive || $is_troll_meme || $is_political || $is_violent;

    $lines = [];

    $lines[] = "ĐIỀU KIỆN TIẾP TỤC";
    $lines[] = "- Nếu câu hỏi có ý nghĩa và có mục đích rõ ràng bao gồm tất cả chủ đề, hỏi troll, nhạy cảm, chính trị, bạo lực, thô tục, nhưng CÓ mang ý nghĩa XEM BÓI bằng quẻ Kinh Dịch, là một lời thỉnh quẻ, hoặc mong muốn dự đoán tương lai cho một sự việc cụ thể.";
    $lines[] = "- NGUYÊN TẮC BẤT BIẾN: KHÔNG ĐƯỢC TỪ CHỐI LUẬN QUẺ DÙ CÂU HỎI CÓ TÍNH CHẤT GÌ.";
    $lines[] = "- Luôn luôn trả về đúng format [AST_RESULT][/AST_RESULT]";
    $lines[] = "";

    if (!$is_special) {
        $lines[] = "LUẬN GIẢI HOÀN TOÀN DỰA VÀO DỮ LIỆU QUẺ";
        return implode("\n", $lines) . "\n";
    }

    $lines[] = "2. [NHÓM ĐẶC BIỆT] VÀ NGUYÊN TẮC CHUYỂN HƯỚNG:";
    $lines[] = "Nếu câu hỏi thuộc các nhóm dưới đây, TUYỆT ĐỐI CẤM phân tích tính khả thi, CẤM đưa lời khuyên thực hiện. Chuyển hướng cách luận như sau:";

    if ($is_sensitive && $category === 'life_death') {
        $lines[] = "- Sức khỏe nghiêm trọng / tính mạng: NGUYÊN TẮC CHUYỂN HƯỚNG -> KHÔNG phán đoán sống/chết, không dùng những câu từ mang tính chất 'tuyên án' hay 'kết thúc'. Chỉ luận 'vận hạn, trạng thái phục hồi hoặc suy yếu'.";
        $lines[] = "    Có thể viết 1 câu nhẹ nhàng ngắn, lời khuyên/an ủi tâm lý nhưng đảm bảo không biến chất 'kinh dịch quẻ', không dạy đời, đạo lý.";
    } elseif ($is_sensitive && $category === 'self_harm') {
        $lines[] = "- Tâm lý khủng hoảng / tự hại: NGUYÊN TẮC CHUYỂN HƯỚNG -> Không nhắc lại hành động tự hại. Chỉ luận 'giai đoạn khó khăn tâm lý, sự bế tắc hoặc lối thoát'.";
        $lines[] = "    Có thể viết 1 câu nhẹ nhàng ngắn, lời khuyên/an ủi tâm lý nhưng đảm bảo không biến chất 'kinh dịch quẻ', không dạy đời, đạo lý.";
    } elseif ($is_sensitive && $category === 'harm_others') {
        $lines[] = "- Gây hại người khác / pháp lý (VD: giết, đánh, cướp): NGUYÊN TẮC CHUYỂN HƯỚNG -> Chỉ luận về 'Tai họa, rủi ro, sự bế tắc và hậu quả pháp lý' dựa trên các yếu tố khắc/suy của quẻ. Tuyệt đối không khuyên 'chờ thời cơ' hay 'lên kế hoạch'.";
    } elseif ($is_violent) {
        $lines[] = "- Hành vi phi pháp / bạo lực: NGUYÊN TẮC CHUYỂN HƯỚNG -> Chỉ luận về 'Tai họa, rủi ro, sự bế tắc và hậu quả pháp lý' dựa trên các yếu tố khắc/suy của quẻ. Tuyệt đối không khuyên 'chờ thời cơ' hay 'lên kế hoạch'.";
    } elseif ($is_political) {
        $lines[] = "- Chính trị / chiến tranh: NGUYÊN TẮC CHUYỂN HƯỚNG -> Không đưa ra lập trường. Chỉ luận về 'Thời thế, sự bất ổn, xu hướng biến động chung' theo quẻ một cách trung lập. Không gắn với cá nhân lãnh đạo cụ thể.";
    } elseif ($is_troll_meme) {
        $lines[] = "- Câu hỏi vui / meme / troll / linh tinh: NGUYÊN TẮC CHUYỂN HƯỚNG -> Vẫn luận quẻ nghiêm túc dựa trên ngũ hành, không sử dụng cách diễn đạt thiếu nghiêm túc.";
    }

    $lines[] = "";
    return implode("\n", $lines) . "\n";
}

function _iching_detect_sensitive(string $question): array {
    $q = mb_strtolower($question, 'UTF-8');

    $life_death = [
        'qua khỏi', 'sống được bao lâu', 'chết không', 'chết chưa', 'bao giờ chết', 'năm nào chết',
        'tuổi thọ', 'tử vong', 'tuyệt mệnh', 'lâm chung', 'qua đời', 'đột tử', 'hấp hối',
        'ung thư', 'di căn', 'giai đoạn cuối', 'giai đoạn 3', 'giai đoạn 4',
        'nguy kịch', 'hôn mê', 'cấp cứu', 'tai biến', 'đột quỵ',
        'ung bướu', 'khối u ác', 'hạch ác'
    ];
    $harm_others = [
        'trả thù', 'hại người', 'giết người', 'đâm chết', 'chém chết', 'bắn chết', 'thuê giang hồ',
        'tù tội', 'bị bắt', 'tống tiền', 'đi tù', 'án mạng'
    ];
    $self_harm = [
        'tự tử', 'tự sát', 'quyên sinh', 'không muốn sống nữa', 'chán sống', 'kết thúc cuộc đời',
        'tự kết liễu', 'trầm cảm nặng', 'tuyệt vọng'
    ];

    foreach ($life_death as $kw) {
        if (mb_strpos($q, $kw, 0, 'UTF-8') !== false) return ['is_sensitive' => true, 'category' => 'life_death'];
    }
    foreach ($harm_others as $kw) {
        if (mb_strpos($q, $kw, 0, 'UTF-8') !== false) return ['is_sensitive' => true, 'category' => 'harm_others'];
    }
    foreach ($self_harm as $kw) {
        if (mb_strpos($q, $kw, 0, 'UTF-8') !== false) return ['is_sensitive' => true, 'category' => 'self_harm'];
    }

    return ['is_sensitive' => false, 'category' => ''];
}

function _iching_detect_dung_than(string $question, string $gender): array {
    $q = mb_strtolower($question, 'UTF-8');

    $keywords = [
        'career' => [
            'công việc', 'việc làm', 'thăng chức', 'thăng tiến', 'sự nghiệp',
            'công danh', 'phỏng vấn', 'xin việc', 'ứng tuyển', 'dự án',
            'sếp', 'lãnh đạo', 'quản lý', 'đồng nghiệp', 'cấp trên',
            'công ty', 'doanh nghiệp', 'kinh doanh', 'm mở công ty',
            'khởi nghiệp', 'startup', 'chức vụ', 'bổ nhiệm', 'sa thải',
            'nghỉ việc', 'chuyển việc', 'đổi việc',
        ],
        'finance' => [
            'tiền', 'tài chính', 'tài lộc', 'thu nhập', 'lương',
            'đầu tư', 'cổ phiếu', 'chứng khoán', 'crypto', 'tiền ảo',
            'vay', 'nợ', 'trả nợ', 'vay tiền', 'cho vay',
            'mua nhà', 'mua đất', 'bất động sản', 'mua xe',
            'kiếm tiền', 'làm giàu', 'phát tài', 'buôn bán',
            'lợi nhuận', 'doanh thu', 'thua lỗ', 'phá sản',
        ],
        'love' => [
            'tình cảm', 'tình yêu', 'người yêu', 'bạn trai', 'bạn gái',
            'yêu đương', 'hẹn hò', 'kết hôn', 'cưới', 'hôn nhân',
            'chia tay', 'ly hôn', 'li hôn', 'ngoại tình',
            'crush', 'thích ai', 'có duyên', 'duyên số',
            'gia đạo', 'vợ chồng', 'vợ', 'chồng',
            'con cái', 'sinh con', 'mang thai', 'con trai', 'con gái',
            'cha mẹ', 'bố mẹ', 'gia đình',
        ],
        'health' => [
            'sức khỏe', 'bệnh', 'bệnh tật', 'ốm', 'đau',
            'phẫu thuật', 'mổ', 'nhập viện', 'xuất viện',
            'chữa bệnh', 'điều trị', 'thuốc', 'khỏi bệnh',
            'tai nạn', 'thương tích', 'hồi phục',
            'sức đề kháng', 'miễn dịch', 'thể lực',
        ],
        'education' => [
            'học tập', 'học hành', 'thi cử', 'thi đỗ', 'thi trượt', 'điểm số',
            'tốt nghiệp', 'du học', 'bằng cấp', 'luận văn', 'bảo vệ', 'chứng chỉ',
            'trường học', 'đại học', 'giáo dục', 'giấy tờ', 'hợp đồng', 'ký kết', 'thủ tục', 'visa'
        ],
        'lost_found' => [
            'mất đồ', 'tìm đồ', 'rơi ví', 'mất điện thoại', 'tìm người', 'mất tích',
            'thất lạc', 'bỏ nhà', 'tìm chó', 'mất chó', 'mất mèo', 'rơi mất', 'để quên'
        ],
        'knowledge' => [
            'là gì', 'nghĩa là gì', 'phù hợp với', 'giải thích', 'cách dùng',
            'phương pháp', 'kiến thức', 'tại sao', 'làm sao', 'hướng dẫn'
        ]
    ];

    $scores = ['career' => 0, 'finance' => 0, 'love' => 0, 'health' => 0, 'education' => 0, 'lost_found' => 0, 'knowledge' => 0];
    foreach ($keywords as $topic => $words) {
        foreach ($words as $word) {
            if (mb_strpos($q, $word, 0, 'UTF-8') !== false) {
                $scores[$topic] += mb_strlen($word, 'UTF-8') >= 6 ? 2 : 1;
            }
        }
    }

    $max_score = max($scores);

    if ($max_score === 0) {
        return [
            'dung_than'      => 'Tử Tôn',
            'source'         => 'topic_fallback',
            'detected_topic' => '',
            'reason'         => 'Câu hỏi không rõ chủ đề',
        ];
    }

    $detected_topic = array_search($max_score, $scores);
    $tied = array_filter($scores, fn($s) => $s === $max_score);
    if (count($tied) > 1) {
        return [
            'dung_than'      => 'Quan Quỷ',
            'source'         => 'topic_fallback',
            'detected_topic' => '',
            'reason'         => 'Câu hỏi có nhiều chủ đề (không rõ chủ đề chính)',
        ];
    }

    $dung_than_map = [
        'career'     => 'Quan Quỷ',
        'finance'    => 'Thê Tài',
        'love'       => ($gender === 'Nam') ? 'Thê Tài' : 'Quan Quỷ',
        'health'     => 'Tử Tôn',
        'education'  => 'Phụ Mẫu',
        'lost_found' => 'Thê Tài',
        'knowledge'  => 'Không xét',
    ];
    $topic_label_map = [
        'career'     => 'Công danh / Sự nghiệp',
        'finance'    => 'Tài lộc / Tiền bạc',
        'love'       => 'Tình duyên / Gia đạo',
        'health'     => 'Sức khỏe / Bình an',
        'education'  => 'Học hành / Giấy tờ',
        'lost_found' => 'Tìm kiếm / Thất lạc',
        'knowledge'  => 'Tìm hiểu kiến thức',
    ];

    return [
        'dung_than'      => $dung_than_map[$detected_topic],
        'source'         => 'question',
        'detected_topic' => $detected_topic,
        'reason'         => "Câu hỏi liên quan đến [{$topic_label_map[$detected_topic]}] (điểm: {$max_score})",
    ];
}

function _iching_hoi_dau(string $hanh_bien, string $hanh_dong): string {
    $sinh = ['Kim'=>'Thủy','Thủy'=>'Mộc','Mộc'=>'Hỏa','Hỏa'=>'Thổ','Thổ'=>'Kim'];
    $khac = ['Kim'=>'Mộc', 'Mộc'=>'Thổ', 'Thổ'=>'Thủy','Thủy'=>'Hỏa','Hỏa'=>'Kim'];

    if ($hanh_bien === $hanh_dong)               return 'Tỷ hòa — hào động không bị ảnh hưởng (trung tính)';
    if (($sinh[$hanh_bien]??'') === $hanh_dong)  return 'Hồi đầu SINH — hào biến sinh hào động (hào động được bổ khí)';
    if (($sinh[$hanh_dong]??'') === $hanh_bien)  return 'Hồi đầu TIẾT — hào động tiết khí cho hào biến (hào động suy nhẹ)';
    if (($khac[$hanh_bien]??'') === $hanh_dong)  return 'Hồi đầu KHẮC — hào biến khắc hào động (hào động bị chế, KHÔNG phải DT phản công)';
    if (($khac[$hanh_dong]??'') === $hanh_bien)  return 'Hồi đầu BỊ KHẮC — hào động chế hào biến (hào biến bị phá, trung tính)';
    return 'Không xác định';
}

function _iching_phi_phuc_relation(string $hanh_phi, string $hanh_phuc): string {
    $sinh = ['Kim'=>'Thủy','Thủy'=>'Mộc','Mộc'=>'Hỏa','Hỏa'=>'Thổ','Thổ'=>'Kim'];
    $khac = ['Kim'=>'Mộc', 'Mộc'=>'Thổ', 'Thổ'=>'Thủy','Thủy'=>'Hỏa','Hỏa'=>'Kim'];

    if ($hanh_phi === $hanh_phuc)                return 'Phi Phục đồng hành — trung tính';
    if (($sinh[$hanh_phi]??'') === $hanh_phuc)   return 'Phi SINH Phục — DT được nuôi dưỡng, có nền nhưng còn ẩn';
    if (($sinh[$hanh_phuc]??'') === $hanh_phi)   return 'Phục TIẾT Phi — DT bị tiết khí qua Phi (hơi suy)';
    if (($khac[$hanh_phi]??'') === $hanh_phuc)   return 'Phi KHẮC Phục — DT bị kìm kẹp nặng (rất khó lộ)';
    if (($khac[$hanh_phuc]??'') === $hanh_phi)   return 'Phục KHẮC Phi — DT chế Phi, xu hướng lộ nếu được kích';
    return 'Không xác định';
}

function _iching_dong_vs_dt(string $chi_hao, string $hanh_hao, string $chi_dt, string $hanh_dt): string {
    $sinh = ['Kim'=>'Thủy','Thủy'=>'Mộc','Mộc'=>'Hỏa','Hỏa'=>'Thổ','Thổ'=>'Kim'];
    $khac = ['Kim'=>'Mộc', 'Mộc'=>'Thổ', 'Thổ'=>'Thủy','Thủy'=>'Hỏa','Hỏa'=>'Kim'];

    $results = [];

    if ($hanh_hao === $hanh_dt) {
        $results[] = "cùng hành {$hanh_dt} → tỷ hòa, hỗ trợ nhẹ";
    } elseif (($sinh[$hanh_hao]??'') === $hanh_dt) {
        $results[] = "{$hanh_hao} sinh {$hanh_dt} → SINH DT (tốt)";
    } elseif (($sinh[$hanh_dt]??'') === $hanh_hao) {
        $results[] = "{$hanh_dt} sinh {$hanh_hao} → DT bị TIẾT khí";
    } elseif (($khac[$hanh_hao]??'') === $hanh_dt) {
        $results[] = "{$hanh_hao} khắc {$hanh_dt} → KHẮC DT (xấu)";
    } elseif (($khac[$hanh_dt]??'') === $hanh_hao) {
        $results[] = "{$hanh_dt} khắc {$hanh_hao} → DT CHẾ hào (DT có lực)";
    }

    if ($chi_hao === $chi_dt) {
        $results[] = "trùng chi {$chi_dt} → phục ngâm (không tiến không lùi)";
    } elseif (_iching_is_luc_xung($chi_hao, $chi_dt)) {
        $results[] = "{$chi_hao} XUNG {$chi_dt} → kích động DT (mạnh: bứt phá | yếu: bị phá)";
    } elseif (_iching_is_luc_hop($chi_hao, $chi_dt)) {
        $results[] = "{$chi_hao} HỢP {$chi_dt} → HỢP TRỤ DT (bị nhốt, dù mạnh cũng không phát)";
    }

    return $results ? implode(' | ', $results) : 'Không có tác động trực tiếp';
}

function _iching_is_luc_xung(string $a, string $b): bool {
    $xung = [
        'Tý'=>'Ngọ','Ngọ'=>'Tý','Sửu'=>'Mùi','Mùi'=>'Sửu',
        'Dần'=>'Thân','Thân'=>'Dần','Mão'=>'Dậu','Dậu'=>'Mão',
        'Thìn'=>'Tuất','Tuất'=>'Thìn','Tỵ'=>'Hợi','Hợi'=>'Tỵ',
    ];
    return ($xung[$a] ?? '') === $b;
}

function _iching_is_luc_hop(string $a, string $b): bool {
    $hop = [
        'Tý'=>'Sửu','Sửu'=>'Tý','Dần'=>'Hợi','Hợi'=>'Dần',
        'Mão'=>'Tuất','Tuất'=>'Mão','Thìn'=>'Dậu','Dậu'=>'Thìn',
        'Tỵ'=>'Thân','Thân'=>'Tỵ','Ngọ'=>'Mùi','Mùi'=>'Ngọ',
    ];
    return ($hop[$a] ?? '') === $b;
}

function _iching_get_smart_topic(string $question, string $raw_topic, string $gender): string {
    $q = mb_strtolower($question, 'UTF-8');

    $troll_signals = ['meme', 'troll', 'haha', 'lol', ':v', '😂', 'ngáo', 'phá đám', 'thử xem', 'kiếnkiến', 'kiến'];
    foreach ($troll_signals as $s) {
        if (mb_strpos($q, $s, 0, 'UTF-8') !== false) return 'Câu hỏi vui / Không nghiêm túc';
    }

    $violent_signals = [
        'giết', 'chém', 'bắn', 'hiếp', 'hãm hiếp', 'tra tấn', 'bạo lực', 'đánh chết', 'tiêu diệt',
        'cướp', 'trộm', 'lừa đảo', 'lừa', 'bị lừa', 'tống tiền', 'buôn lậu', 'ma túy', 'chất cấm',
        'rửa tiền', 'làm giả', 'hack', 'tấn công mạng', 'đập đá', 'bắt cóc'
    ];

    foreach ($violent_signals as $s) {
        if (mb_strpos($q, $s, 0, 'UTF-8') !== false) return 'Nhạy cảm / Hành vi bạo lực, phi pháp';
    }

    $political_signals = ['chính trị', 'chiến tranh', 'bầu cử', 'đảng', 'tổng thống', 'thủ tướng', 'quân đội', 'xâm lược', 'cách mạng', 'trump', 'biden', 'putin'];
    foreach ($political_signals as $s) {
        if (mb_strpos($q, $s, 0, 'UTF-8') !== false) return 'Chính trị / Xã hội';
    }

    $sensitive = _iching_detect_sensitive($question);
    if ($sensitive['is_sensitive']) {
        if ($sensitive['category'] === 'life_death') return 'Sức khỏe nghiêm trọng / Tính mạng';
        if ($sensitive['category'] === 'self_harm') return 'Tâm lý khủng hoảng / Tự hại';
        if ($sensitive['category'] === 'harm_others') return 'Nhạy cảm / Xung đột / Gây hại';
    }

    if ($raw_topic === 'general' || empty($raw_topic)) {
        $dt_result = _iching_detect_dung_than($question, $gender);
        if ($dt_result['source'] !== 'topic_fallback') {
            $topic_label_map = [
                'career'     => 'Công danh / Sự nghiệp',
                'finance'    => 'Tài lộc / Tiền bạc',
                'love'       => 'Tình duyên / Gia đạo',
                'health'     => 'Sức khỏe / Bình an',
                'education'  => 'Học hành / Giấy tờ',
                'lost_found' => 'Tìm kiếm / Thất lạc',
                'knowledge'  => 'Tìm hiểu kiến thức',
            ];
            return $topic_label_map[$dt_result['detected_topic']] ?? 'Tổng quan';
        }
    }

    $label_map = [
        'career'     => 'Công danh / Sự nghiệp',
        'finance'    => 'Tài lộc / Tiền bạc',
        'love'       => 'Tình duyên / Gia đạo',
        'health'     => 'Sức khỏe / Bình an',
        'education'  => 'Học hành / Giấy tờ',
        'lost_found' => 'Tìm kiếm / Thất lạc',
        'knowledge'  => 'Tìm hiểu kiến thức',
        'general'    => 'Tổng quan'
    ];

    return $label_map[$raw_topic] ?? 'Tổng quan';
}