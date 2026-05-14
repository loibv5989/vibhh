<?php
// FILE: calc.php
if (!defined('ABSPATH')) exit;

class BS_Phone_Calc {

    private static function reduce(int $n): int {
        while ($n > 9 && !in_array($n, [11, 22], true)) {
            $n = array_sum(str_split((string) $n));
        }
        return $n;
    }

    private static function loadData(): array {
        static $data = null;
        if ($data === null) {
            $data = require BS_PHONE_PLUGIN_DIR . 'includes/data.php';
        }
        return is_array($data) ? $data : [];
    }

    public static function normalizeDob(string $dob): string {
        $dob = trim($dob);
        $normalized = preg_replace('/[\-\.\s]+/', '/', $dob);
        $parts = explode('/', $normalized);

        if (count($parts) === 3) {
            $d = (int)$parts[0];
            $m = (int)$parts[1];
            $y = (int)$parts[2];

            if (checkdate($m, $d, $y)) {
                $dateObj = DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $y, $m, $d));
                if ($dateObj) return $dateObj->format('d/m/Y');
            }
        }
        throw new Exception('Ngày sinh không hợp lệ. Vui lòng kiểm tra lại (định dạng: dd/mm/yyyy).');
    }

    public static function get_life_path(string $dob): int {
        $parts = explode('/', $dob);
        if (count($parts) !== 3) return 0;
        $sum = array_sum(str_split($parts[0] . $parts[1] . $parts[2]));
        return self::reduce($sum);
    }

    public static function get_core_number(string $phone): int {
        $digits = preg_replace('/\D/', '', $phone);
        $sum = array_sum(str_split($digits));
        return self::reduce($sum);
    }

    public static function get_zodiac(string $dob): string {
        $parts = explode('/', $dob);
        if (count($parts) !== 3) return '';
        $d = (int)$parts[0];
        $m = (int)$parts[1];

        if (($m == 3 && $d >= 21) || ($m == 4 && $d <= 19)) return 'aries';
        if (($m == 4 && $d >= 20) || ($m == 5 && $d <= 20)) return 'taurus';
        if (($m == 5 && $d >= 21) || ($m == 6 && $d <= 21)) return 'gemini';
        if (($m == 6 && $d >= 22) || ($m == 7 && $d <= 22)) return 'cancer';
        if (($m == 7 && $d >= 23) || ($m == 8 && $d <= 22)) return 'leo';
        if (($m == 8 && $d >= 23) || ($m == 9 && $d <= 22)) return 'virgo';
        if (($m == 9 && $d >= 23) || ($m == 10 && $d <= 23)) return 'libra';
        if (($m == 10 && $d >= 24) || ($m == 11 && $d <= 21)) return 'scorpio';
        if (($m == 11 && $d >= 22) || ($m == 12 && $d <= 21)) return 'sagittarius';
        // Capricorn: 22/12–31/12 và 01/01–19/01
        if (($m == 12 && $d >= 22) || ($m == 1 && $d <= 19)) return 'capricorn';
        if (($m == 1 && $d >= 20) || ($m == 2 && $d <= 18)) return 'aquarius';
        return 'pisces';
    }

    private static function determine_relationship(int $lp, int $phone_core, array $data): string {
        if ($lp === $phone_core) return 'identical';

        $shadows = $data['shadow_pairs'][$lp] ?? [];
        if (in_array($phone_core, $shadows, true)) return 'compensatory';

        $lp_group    = '';
        $phone_group = '';
        foreach ($data['energy_groups'] as $key => $group) {
            if (in_array($lp,         $group['numbers'], true)) $lp_group    = $key;
            if (in_array($phone_core, $group['numbers'], true)) $phone_group = $key;
        }

        if ($lp_group && $lp_group === $phone_group) return 'harmonic';

        return 'neutral';
    }

    public static function detect_patterns(string $phone, array $data): array {
        $found = [];
        $clean = preg_replace('/\D/', '', $phone);

        // 1. VIP Patterns
        if (preg_match('/(\d)\1{5,}$/', $clean)) {
            $found['vip'] = $data['vip_patterns']['ultra'];
        } elseif (preg_match('/(\d)\1{3,4}$/', $clean)) {
            $found['vip'] = $data['vip_patterns']['high'];
        } elseif (preg_match('/(0123|1234|2345|3456|4567|5678|6789)$/', $clean)) {
            $found['vip'] = $data['vip_patterns']['memorable'];
        }

        // 2. Endings — collective_unconscious trước, endings ghi đè nếu trùng key
        $endings = array_merge($data['collective_unconscious'] ?? [], $data['endings'] ?? []);
        foreach ($endings as $pattern => $info) {
            if (str_ends_with($clean, (string) $pattern)) {
                $found['ending'][$pattern] = $info;
            }
        }
        return $found;
    }

    public static function calculate(string $name, string $dob, string $phone): array {
        $data = self::loadData();
        $cleanDob = self::normalizeDob($dob);

        $life_path  = self::get_life_path($cleanDob);
        $phone_core = self::get_core_number($phone);
        $zodiac     = self::get_zodiac($cleanDob);

        $relationship = self::determine_relationship($life_path, $phone_core, $data);
        $patterns     = self::detect_patterns($phone, $data);

        return [
            'name'         => $name,
            'dob'          => $cleanDob,
            'phone'        => $phone,
            'life_path'    => $life_path,
            'phone_core'   => $phone_core,
            'zodiac'       => $zodiac,
            'relationship' => $relationship,
            'patterns'     => $patterns,
        ];
    }

    /**
     * Tạo chat lines cho Typewriter — gom vào đây, render.php không load data.php
     */
    public static function get_chat_lines(array $calc_data): array {
        $data = self::loadData();

        $life_path    = $calc_data['life_path'];
        $phone_core   = $calc_data['phone_core'];
        $zodiac       = $calc_data['zodiac'];
        $relationship = $calc_data['relationship'];
        $phone        = $calc_data['phone'];

        $rel_concept  = $data['lp_phone_map'][$relationship]['jung_concept'] ?? 'Không xác định';
        $zodiac_label = $data['zodiac_archetypes'][$zodiac]['archetype']     ?? ucfirst($zodiac);

        $lines = [];
        $lines[] = ['type' => 'greeting', 'text' => "Số ĐT: {$phone} | {$calc_data['name']} | Ngày sinh: {$calc_data['dob']}"];
        $lines[] = ['type' => 'divider',  'text' => ''];

        $lines[] = ['type' => 'intro', 'text' => 'BẢN NGÃ & CÔNG CỤ'];
        $lines[] = ['type' => 'text',  'text' => '• Số Đường Đời (Bản ngã): Số ' . $life_path];
        $lines[] = ['type' => 'text',  'text' => '• Số Điện Thoại (Vỏ bọc): Số ' . $phone_core];
        $lines[] = ['type' => 'text',  'text' => '• Nguyên mẫu Hoàng đạo: ' . $zodiac_label];

        $lines[] = ['type' => 'divider', 'text' => ''];

        $lines[] = ['type' => 'intro', 'text' => 'KẾT QUẢ ĐỐI CHIẾU SYNCHRONICITY'];
        $lines[] = ['type' => 'index', 'key' => 'sync', 'label' => 'Trạng thái Tâm lý', 'value' => $rel_concept];

        $lines[] = ['type' => 'divider', 'text' => ''];

        return $lines;
    }

    public static function build_narrative(array $calc_data): array {
        $data = self::loadData();

        $life_path    = $calc_data['life_path'];
        $phone_core   = $calc_data['phone_core'];
        $zodiac       = $calc_data['zodiac'];
        $relationship = $calc_data['relationship'];
        $patterns     = $calc_data['patterns'];

        $lp_info     = $data['lifepath'][$life_path]    ?? [];
        $core_info   = $data['phone_core'][$phone_core] ?? [];
        $rel_info    = $data['lp_phone_map'][$relationship]   ?? [];
        $zodiac_info = $data['zodiac_archetypes'][$zodiac]    ?? [];

        // KHỐI 1: BẢN NGÃ (Ý THỨC)
        $b1  = '';
        $b1 .= '<p>Qua ngày sinh, hệ thống xác định <strong>Số Đường Đời (Life Path)</strong> của bạn là <strong>Số ' . esc_html($life_path) . '</strong>. Trong phân tâm học, đây đại diện cho Archetype <em>' . esc_html($lp_info['archetype'] ?? '') . '</em>.</p>';
        $b1 .= '<ul>';
        $b1 .= '<li><strong>Bản chất:</strong> ' . esc_html($lp_info['essence'] ?? '') . '</li>';
        $b1 .= '<li><strong>Góc khuất (Shadow):</strong> ' . esc_html($lp_info['shadow'] ?? '') . '</li>';
        if (!empty($zodiac_info)) {
            $b1 .= '<li><strong>Khí chất hoàng đạo:</strong> Thuộc nhóm nguyên mẫu ' . esc_html($zodiac_info['archetype'] ?? '') . ' - ' . esc_html($zodiac_info['core'] ?? '') . '</li>';
        }
        $b1 .= '</ul>';

        // KHỐI 2: VỎ BỌC VÀ CÔNG CỤ (VÔ THỨC)
        $b2  = '';
        $b2 .= '<p>Số điện thoại bạn chọn rút gọn lại thành <strong>Số Chủ Đạo ' . esc_html($phone_core) . '</strong>. Dãy số này đóng vai trò như một lớp vỏ bọc năng lượng khi bạn tương tác với xã hội.</p>';
        $b2 .= '<ul>';
        $b2 .= '<li><strong>Tín hiệu phát ra:</strong> ' . esc_html($core_info['communication'] ?? '') . '</li>';
        $b2 .= '<li><strong>Nhu cầu vô thức:</strong> ' . esc_html($core_info['unconscious'] ?? '') . '</li>';
        $b2 .= '</ul>';

        if (!empty($patterns['vip']) || !empty($patterns['ending'])) {
            $b2 .= '<p><strong>Dấu ấn Synchronicity trong dãy số:</strong></p><ul>';
            if (!empty($patterns['vip'])) {
                $b2 .= '<li><strong>Cấu trúc VIP:</strong> ' . esc_html($patterns['vip']['signal']) . '</li>';
            }
            if (!empty($patterns['ending'])) {
                foreach ($patterns['ending'] as $num => $info) {
                    $label = $info['amplify'] ?? $info['label'] ?? "Đuôi $num";
                    $desc  = $info['jung']    ?? $info['signal'] ?? '';
                    $b2   .= '<li><strong>[' . esc_html($label) . ']:</strong> ' . esc_html($desc) . '</li>';
                }
            }
            $b2 .= '</ul>';
        }

        // KHỐI 3: PHÂN TÍCH ĐỐI CHIẾU (SYNCHRONICITY)
        $b3  = '';
        $b3 .= '<p>Trạng thái liên kết giữa Bản ngã (Số ' . esc_html($life_path) . ') và Vỏ bọc (Số ' . esc_html($phone_core) . '): <strong>' . esc_html($rel_info['jung_concept'] ?? '') . '</strong>.</p>';
        $b3 .= '<p>' . esc_html($rel_info['description'] ?? '') . '</p>';

        if (!empty($zodiac_info)) {
            if (in_array($phone_core, $zodiac_info['resonant_cores'] ?? [], true)) {
                $b3 .= '<p><em>*Dãy số này hoàn toàn thuận theo dòng chảy tự nhiên của khí chất cung Hoàng đạo hiện tại.</em></p>';
            } elseif (in_array($phone_core, $zodiac_info['tension_cores'] ?? [], true)) {
                $b3 .= '<p><em>*Sự căng thẳng: Dãy số này đi ngược lại khí chất Hoàng đạo bẩm sinh, cho thấy vô thức đang cố tình tạo ra một "môi trường khắc nghiệt" để buộc bản thân phải phá vỡ giới hạn an toàn.</em></p>';
            }
        }

        $b4 = '<blockquote>' . esc_html($lp_info['growth'] ?? '') . '</blockquote>';
        if ($relationship === 'compensatory') {
            $b4 .= '<p>Đừng chối bỏ phần năng lượng đối lập mà SĐT đang mang. Hãy coi nó là tín hiệu để bạn học cách dung hòa phần "Bóng tối" (Shadow) bên trong mình.</p>';
        }

        return ['block1' => $b1, 'block2' => $b2, 'block3' => $b3, 'block4' => $b4];
    }
}
