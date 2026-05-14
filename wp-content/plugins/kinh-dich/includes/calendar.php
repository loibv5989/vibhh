<?php
if (!defined('ABSPATH')) exit;

class Iching_Calendar {

    const CAN = ['Giáp','Ất','Bính','Đinh','Mậu','Kỷ','Canh','Tân','Nhâm','Quý'];
    const CHI = ['Tý','Sửu','Dần','Mão','Thìn','Tỵ','Ngọ','Mùi','Thân','Dậu','Tuất','Hợi'];

    const CHI_HANH = [
        'Tý'  => 'Thủy', 'Sửu' => 'Thổ',  'Dần' => 'Mộc', 'Mão'  => 'Mộc',
        'Thìn'=> 'Thổ',  'Tỵ'  => 'Hỏa',  'Ngọ' => 'Hỏa', 'Mùi'  => 'Thổ',
        'Thân'=> 'Kim',  'Dậu' => 'Kim',   'Tuất'=> 'Thổ', 'Hợi'  => 'Thủy',
    ];

    const DUOC_SINH = [
        'Mộc' => 'Thủy', 'Hỏa' => 'Mộc', 'Thổ' => 'Hỏa',
        'Kim' => 'Thổ',  'Thủy'=> 'Kim',
    ];

    const BI_KHAC = [
        'Mộc' => 'Kim',  'Hỏa' => 'Thủy', 'Thổ' => 'Mộc',
        'Kim' => 'Hỏa',  'Thủy'=> 'Thổ',
    ];

    const THANG_CAN_BASE = [0 => 2, 1 => 4, 2 => 6, 3 => 8, 4 => 0];

    public static function get(int $timestamp): array {
        $hour = (int) wp_date('G', $timestamp);

        $ts_ngay = ($hour >= 23) ? $timestamp + 86400 : $timestamp;

        $year  = (int) wp_date('Y', $ts_ngay);
        $month = (int) wp_date('n', $ts_ngay);
        $day   = (int) wp_date('j', $ts_ngay);

        $nam   = self::canChiNam($year);
        $thang = self::canChiThang($year, $month, $day);
        $ngay  = self::canChiNgay($year, $month, $day);
        $gio   = self::canChiGio($year, $month, $day, $hour);

        $chi_ngay  = explode(' ', $ngay)[1];
        $chi_thang = explode(' ', $thang)[1];

        return [
            'nam'         => $nam,
            'thang'       => $thang,
            'ngay'        => $ngay,
            'gio'         => $gio,
            'hanh_ngay'   => self::CHI_HANH[$chi_ngay]  ?? '',
            'hanh_thang'  => self::CHI_HANH[$chi_thang] ?? '',
            'nguyet_lenh' => $chi_thang,
            'nhat_kien'   => $chi_ngay,
        ];
    }

    public static function toPromptString(array $cc): string {
        return "Giờ {$cc['gio']}, ngày {$cc['ngay']}, tháng {$cc['thang']}, năm {$cc['nam']}";
    }

    public static function canChiNam(int $year, int $month = 0, int $day = 0): string {
        $y = $year;
        // Kiểm tra nếu chưa qua Lập Xuân (tháng 2 dương lịch) thì phải lùi lại 1 năm
        if ($month > 0 && $day > 0) {
            if ($month == 1) {
                $y--; // Tháng 1 chắc chắn thuộc năm cũ của Tiết Khí
            } elseif ($month == 2) {
                $lap_xuan_day = self::getTietDay(2, $year);
                if ($day < $lap_xuan_day) {
                    $y--; // Tháng 2 nhưng chưa tới ngày Lập Xuân
                }
            }
        }

        $can = ($y - 4) % 10;
        $chi = ($y - 4) % 12;
        if ($can < 0) $can += 10;
        if ($chi < 0) $chi += 12;
        return self::CAN[$can] . ' ' . self::CHI[$chi];
    }

    public static function canChiNgay(int $year, int $month, int $day): string {
        $jd  = self::julianDay($year, $month, $day);
        $can = ($jd + 49) % 10;
        $chi = ($jd + 61) % 12;
        return self::CAN[$can] . ' ' . self::CHI[$chi];
    }

    public static function canChiThang(int $year, int $month, int $day): string {
        $y          = $year;
        $thang_tiet = $month;

        $tiet_day = self::getTietDay($month, $year);

        if ($day < $tiet_day) {
            $thang_tiet = $month - 1;
            if ($thang_tiet === 0) { $thang_tiet = 12; $y--; }
        }

        $chi_idx  = $thang_tiet % 12;
        $can_nam  = ($y - 4) % 10;
        if ($can_nam < 0) $can_nam += 10;
        $can_base = self::THANG_CAN_BASE[$can_nam % 5];
        $delta    = ($chi_idx - 2 + 12) % 12;
        $can_idx  = ($can_base + $delta) % 10;

        return self::CAN[$can_idx] . ' ' . self::CHI[$chi_idx];
    }

    public static function canChiGio(int $year, int $month, int $day, int $hour): string {
        $chi_gio    = (int)(($hour + 1) / 2) % 12;
        $jd         = self::julianDay($year, $month, $day);
        $can_ngay   = ($jd + 49) % 10;
        $can_ty_map = [0, 2, 4, 6, 8, 0, 2, 4, 6, 8];
        $can_gio    = ($can_ty_map[$can_ngay] + $chi_gio) % 10;
        return self::CAN[$can_gio] . ' ' . self::CHI[$chi_gio];
    }
    public static function julianDay(int $y, int $m, int $d): int {
        if ($m <= 2) { $y--; $m += 12; }
        $A = (int)($y / 100);
        $B = 2 - $A + (int)($A / 4);
        return (int)(365.25 * ($y + 4716))
            + (int)(30.6001 * ($m + 1))
            + $d + $B - 1524;
    }

    public static function getVuongSuy(string $hanh_que, string $hanh_thang): string {
        if ($hanh_thang === $hanh_que) return 'Vượng';

        if ((self::DUOC_SINH[$hanh_que]   ?? '') === $hanh_thang)  return 'Tướng';
        if ((self::DUOC_SINH[$hanh_thang] ?? '') === $hanh_que)    return 'Hưu';

        if ((self::BI_KHAC[$hanh_thang]   ?? '') === $hanh_que)    return 'Tù';

        if ((self::BI_KHAC[$hanh_que]     ?? '') === $hanh_thang)  return 'Tử';

        return 'Bình';
    }

    public static function getNhatKienComment(string $hanh_que, string $hanh_ngay, string $label = 'quẻ'): string {
        if ($hanh_ngay === $hanh_que)                              return "Nhật Kiến cùng hành {$label} — củng cố thêm";
        if ((self::DUOC_SINH[$hanh_que]  ?? '') === $hanh_ngay)   return "Nhật Kiến sinh {$label} — có trợ lực";
        if ((self::DUOC_SINH[$hanh_ngay] ?? '') === $hanh_que)    return "Nhật Kiến bị {$label} sinh — tiết khí";
        if ((self::BI_KHAC[$hanh_que]    ?? '') === $hanh_ngay)   return "Nhật Kiến khắc {$label} — thêm áp lực";
        if ((self::BI_KHAC[$hanh_ngay]   ?? '') === $hanh_que)    return "Nhật Kiến bị {$label} khắc — {$label} có uy";
        return '';
    }

    /**
     * Thuật toán tính chính xác ngày bắt đầu Tiết Khí (đổi tháng) trong thế kỷ 21
     * Công thức: [Y * D + C] - L
     */
    public static function getTietDay(int $m, int $y): int {
        $yy = $y % 100;

        // Hằng số C cho 12 Tiết Lệnh (Tháng 1 -> 12 Dương Lịch) thế kỷ 21
        $C = [
            1 => 5.4055,  // Tiểu Hàn (Đổi sang tháng Sửu)
            2 => 3.87,    // Lập Xuân (Đổi sang tháng Dần)
            3 => 5.63,    // Kinh Trập (Đổi sang tháng Mão)
            4 => 4.81,    // Thanh Minh (Đổi sang tháng Thìn)
            5 => 5.52,    // Lập Hạ (Đổi sang tháng Tỵ)
            6 => 5.678,   // Mang Chủng (Đổi sang tháng Ngọ)
            7 => 7.108,   // Tiểu Thử (Đổi sang tháng Mùi)
            8 => 7.5,     // Lập Thu (Đổi sang tháng Thân)
            9 => 7.646,   // Bạch Lộ (Đổi sang tháng Dậu)
            10 => 8.318,  // Hàn Lộ (Đổi sang tháng Tuất)
            11 => 7.438,  // Lập Đông (Đổi sang tháng Hợi)
            12 => 7.18    // Đại Tuyết (Đổi sang tháng Tý)
        ];

        if (!isset($C[$m])) return 6; // Fallback an toàn

        $day = floor($yy * 0.2422 + $C[$m]) - floor($yy / 4);

        // Các năm ngoại lệ của thuật toán trong thế kỷ 21 (Cộng thêm 1 ngày)
        if ($y == 2082 && $m == 1) $day += 1;
        if ($y == 2084 && $m == 3) $day += 1;
        if ($y == 2008 && $m == 5) $day += 1;
        if ($y == 2002 && $m == 6) $day += 1;
        if ($y == 2016 && $m == 7) $day += 1;
        if ($y == 2002 && $m == 8) $day += 1;
        if ($y == 2089 && $m == 11) $day += 1;

        return (int)$day;
    }

    public static function getTuanKhong(string $can_chi_ngay): array {
        $can_arr = ['Giáp'=>1,'Ất'=>2,'Bính'=>3,'Đinh'=>4,'Mậu'=>5,'Kỷ'=>6,'Canh'=>7,'Tân'=>8,'Nhâm'=>9,'Quý'=>10];
        $chi_arr = ['Tý'=>1,'Sửu'=>2,'Dần'=>3,'Mão'=>4,'Thìn'=>5,'Tỵ'=>6,'Ngọ'=>7,'Mùi'=>8,'Thân'=>9,'Dậu'=>10,'Tuất'=>11,'Hợi'=>12];
        $parts = explode(' ', $can_chi_ngay);
        if (count($parts) != 2) return [];
        $delta = $chi_arr[$parts[1]] - $can_arr[$parts[0]];
        if ($delta < 0) $delta += 12;
        $tk_map = [0 => ['Tuất', 'Hợi'], 10 => ['Thân', 'Dậu'], 8 => ['Ngọ', 'Mùi'], 6 => ['Thìn', 'Tỵ'], 4 => ['Dần', 'Mão'], 2 => ['Tý', 'Sửu']];
        return $tk_map[$delta] ?? [];
    }

    public static function getLoc(string $can_ngay): string {
        $map = ['Giáp'=>'Dần','Ất'=>'Mão','Bính'=>'Tỵ','Mậu'=>'Tỵ','Đinh'=>'Ngọ','Kỷ'=>'Ngọ','Canh'=>'Thân','Tân'=>'Dậu','Nhâm'=>'Hợi','Quý'=>'Tý'];
        return $map[$can_ngay] ?? '';
    }

    public static function getMa(string $chi_ngay): string {
        $map = ['Thân'=>'Dần','Tý'=>'Dần','Thìn'=>'Dần', 'Dần'=>'Thân','Ngọ'=>'Thân','Tuất'=>'Thân', 'Tỵ'=>'Hợi','Dậu'=>'Hợi','Sửu'=>'Hợi', 'Hợi'=>'Tỵ','Mão'=>'Tỵ','Mùi'=>'Tỵ'];
        return $map[$chi_ngay] ?? '';
    }

    public static function getQuyNhan(string $can_ngay): array {
        $map = ['Giáp'=>['Sửu','Mùi'],'Mậu'=>['Sửu','Mùi'],'Canh'=>['Sửu','Mùi'], 'Ất'=>['Tý','Thân'],'Kỷ'=>['Tý','Thân'], 'Bính'=>['Hợi','Dậu'],'Đinh'=>['Hợi','Dậu'], 'Nhâm'=>['Tỵ','Mão'],'Quý'=>['Tỵ','Mão'], 'Tân'=>['Ngọ','Dần']];
        return $map[$can_ngay] ?? [];
    }

    public static function getDaoHoa(string $chi_ngay): string {
        $map = ['Thân'=>'Dậu','Tý'=>'Dậu','Thìn'=>'Dậu', 'Dần'=>'Mão','Ngọ'=>'Mão','Tuất'=>'Mão', 'Tỵ'=>'Ngọ','Dậu'=>'Ngọ','Sửu'=>'Ngọ', 'Hợi'=>'Tý','Mão'=>'Tý','Mùi'=>'Tý'];
        return $map[$chi_ngay] ?? '';
    }
}