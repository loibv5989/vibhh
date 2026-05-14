<?php
if (!defined('ABSPATH')) exit;

class Iching_LucHao {

    // Ngũ hành của 12 Địa chi
    const CHI_HANH = [
        'Tý' => 'Thủy', 'Sửu' => 'Thổ', 'Dần' => 'Mộc', 'Mão' => 'Mộc',
        'Thìn' => 'Thổ', 'Tỵ' => 'Hỏa', 'Ngọ' => 'Hỏa', 'Mùi' => 'Thổ',
        'Thân' => 'Kim', 'Dậu' => 'Kim', 'Tuất' => 'Thổ', 'Hợi' => 'Thủy'
    ];

    // Nạp Can cho 8 Quái: [Nội quái, Ngoại quái]
    const NAP_CAN = [
        '111' => ['Giáp', 'Nhâm'], // Càn
        '000' => ['Ất',   'Quý'],  // Khôn
        '100' => ['Canh', 'Canh'], // Chấn
        '011' => ['Tân',  'Tân'],  // Tốn
        '010' => ['Mậu',  'Mậu'],  // Khảm
        '101' => ['Kỷ',   'Kỷ'],   // Ly
        '001' => ['Bính', 'Bính'], // Cấn
        '110' => ['Đinh', 'Đinh'], // Đoài
    ];

    // Nạp chi cho 8 Quái (đọc từ Hào 1 lên Hào 6)
    const NAP_CHI = [
        '111' => ['Tý','Dần','Thìn','Ngọ','Thân','Tuất'], // Càn
        '100' => ['Tý','Dần','Thìn','Ngọ','Thân','Tuất'], // Chấn
        '010' => ['Dần','Thìn','Ngọ','Thân','Tuất','Tý'], // Khảm
        '001' => ['Thìn','Ngọ','Thân','Tuất','Tý','Dần'], // Cấn
        '000' => ['Mùi','Tỵ','Mão','Sửu','Hợi','Dậu'],    // Khôn
        '011' => ['Sửu','Hợi','Dậu','Mùi','Tỵ','Mão'],    // Tốn
        '101' => ['Mão','Sửu','Hợi','Dậu','Mùi','Tỵ'],    // Ly
        '110' => ['Tỵ','Mão','Sửu','Hợi','Dậu','Mùi'],    // Đoài
    ];

    public static function getLucThan($hanh_cung, $hanh_hao): string {
        if ($hanh_cung === $hanh_hao) return 'Huynh Đệ';

        $sinh = ['Kim'=>'Thủy', 'Thủy'=>'Mộc', 'Mộc'=>'Hỏa', 'Hỏa'=>'Thổ', 'Thổ'=>'Kim'];
        $khac = ['Kim'=>'Mộc', 'Mộc'=>'Thổ', 'Thổ'=>'Thủy', 'Thủy'=>'Hỏa', 'Hỏa'=>'Kim'];

        if ($sinh[$hanh_cung] === $hanh_hao) return 'Tử Tôn';
        if ($sinh[$hanh_hao] === $hanh_cung) return 'Phụ Mẫu';
        if ($khac[$hanh_cung] === $hanh_hao) return 'Thê Tài';
        if ($khac[$hanh_hao] === $hanh_cung) return 'Quan Quỷ';

        return 'Không rõ';
    }

    public static function getLucThu($can_ngay_str, $hao_index): string {
        $can_map = ['Giáp'=>0, 'Ất'=>0, 'Bính'=>1, 'Đinh'=>1, 'Mậu'=>2, 'Kỷ'=>3, 'Canh'=>4, 'Tân'=>4, 'Nhâm'=>5, 'Quý'=>5];
        $start = $can_map[$can_ngay_str] ?? 0;
        $thu_list = ['Thanh Long', 'Chu Tước', 'Câu Trần', 'Đằng Xà', 'Bạch Hổ', 'Huyền Vũ'];
        return $thu_list[($start + $hao_index) % 6];
    }

    // Thuật toán xác định Cung và Hào Thế dựa trên giao điểm Nội/Ngoại quái
    public static function getCungAndThe($chu_bin): array {
        $quai_hanh = [
            '111'=>['Càn','Kim'], '110'=>['Đoài','Kim'], '101'=>['Ly','Hỏa'], '100'=>['Chấn','Mộc'],
            '011'=>['Tốn','Mộc'], '010'=>['Khảm','Thủy'], '001'=>['Cấn','Thổ'], '000'=>['Khôn','Thổ']
        ];

        $B = substr($chu_bin, 0, 3);
        $T = substr($chu_bin, 3, 3);

        // Loại bỏ hoàn toàn toán tử ^ (XOR) để tránh lỗi ép kiểu của PHP
        // So sánh trực tiếp từng hào để tìm điểm giao
        $X = '';
        $X .= ($B[0] === $T[0]) ? '0' : '1';
        $X .= ($B[1] === $T[1]) ? '0' : '1';
        $X .= ($B[2] === $T[2]) ? '0' : '1';

        // Đảo quái an toàn tuyệt đối bằng chuỗi
        $invertB = '';
        $invertB .= ($B[0] === '1') ? '0' : '1';
        $invertB .= ($B[1] === '1') ? '0' : '1';
        $invertB .= ($B[2] === '1') ? '0' : '1';

        $the = 0; $cung_bin = '';
        if     ($X === '000') { $the = 6; $cung_bin = $T; }
        elseif ($X === '100') { $the = 1; $cung_bin = $T; }
        elseif ($X === '110') { $the = 2; $cung_bin = $T; }
        elseif ($X === '111') { $the = 3; $cung_bin = $T; }
        elseif ($X === '011') { $the = 4; $cung_bin = $invertB; }
        elseif ($X === '001') { $the = 5; $cung_bin = $invertB; }
        elseif ($X === '101') { $the = 4; $cung_bin = $invertB; } // Du Hồn
        elseif ($X === '010') { $the = 3; $cung_bin = $B; }        // Quy Hồn
        else {
            $the = 1; $cung_bin = '111';
        }

        $ung = ($the + 2) % 6 + 1; // 6->3, 1->4, 2->5, 3->6, 4->1, 5->2

        return [
            'the'       => $the,
            'ung'       => $ung,
            'cung_ten'  => $quai_hanh[$cung_bin][0],
            'cung_hanh' => $quai_hanh[$cung_bin][1]
        ];
    }

    public static function parse($chu_bin, $can_ngay, $override_hanh_cung = null): array {
        $cung_the = self::getCungAndThe($chu_bin);
        $noi_quai = substr($chu_bin, 0, 3);
        $ngoai_quai = substr($chu_bin, 3, 3);

        $final_hanh_cung = $override_hanh_cung ?? $cung_the['cung_hanh'];

        $lines = [];
        for ($i = 0; $i < 6; $i++) {
            $quai_truy_xuat = ($i < 3) ? $noi_quai : $ngoai_quai;
            $chi = self::NAP_CHI[$quai_truy_xuat][$i];
            $hanh_chi = self::CHI_HANH[$chi];

            $luc_than = self::getLucThan($final_hanh_cung, $hanh_chi);
            $luc_thu = self::getLucThu($can_ngay, $i);
            $is_noi = ($i < 3);
            $can = self::NAP_CAN[$quai_truy_xuat][$is_noi ? 0 : 1];

            $lines[$i + 1] = [
                'can' => $can,
                'chi' => $chi,
                'hanh' => $hanh_chi,
                'luc_than' => $luc_than,
                'luc_thu' => $luc_thu,
                'is_the' => ($i + 1 == $cung_the['the']),
                'is_ung' => ($i + 1 == $cung_the['ung'])
            ];
        }

        return [
            'cung' => $cung_the['cung_ten'],
            'hanh_cung' => $final_hanh_cung,
            'the' => $cung_the['the'],
            'ung' => $cung_the['ung'],
            'lines' => $lines
        ];
    }

    public static function getPhucThan(string $cung_ten, string $dung_than_target) {
        $quai_to_bin = [
            'Càn' => '111', 'Đoài' => '110', 'Ly' => '101', 'Chấn' => '100',
            'Tốn' => '011', 'Khảm' => '010', 'Cấn' => '001', 'Khôn' => '000'
        ];

        $quai_hanh_map = [
            'Càn' => 'Kim', 'Đoài' => 'Kim', 'Ly' => 'Hỏa', 'Chấn' => 'Mộc',
            'Tốn' => 'Mộc', 'Khảm' => 'Thủy', 'Cấn' => 'Thổ', 'Khôn' => 'Thổ'
        ];

        if (!isset($quai_to_bin[$cung_ten])) return null;

        $bin = $quai_to_bin[$cung_ten];
        $hanh_cung = $quai_hanh_map[$cung_ten];

        for ($i = 0; $i < 6; $i++) {
            $chi = self::NAP_CHI[$bin][$i];
            $hanh_chi = self::CHI_HANH[$chi];
            $luc_than = self::getLucThan($hanh_cung, $hanh_chi);

            if ($luc_than === $dung_than_target) {
                return [
                    'chi'        => $chi,
                    'hanh'       => $hanh_chi,
                    'luc_than'   => $luc_than,
                    'under_line' => $i + 1
                ];
            }
        }

        return null;
    }
}