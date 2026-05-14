<?php

if (!defined('ABSPATH')) exit;

class Iching_Calc {

    private static function getData(): array {
        $base_dir = dirname(__DIR__);

        $part1 = require $base_dir . '/hexagram/01_16.php';
        $part2 = require $base_dir . '/hexagram/17_32.php';
        $part3 = require $base_dir . '/hexagram/33_48.php';
        $part4 = require $base_dir . '/hexagram/49_64.php';

        return $part1 + $part2 + $part3 + $part4;
    }

    public static function drawLite(string $mode = 'luchao', array $args = []): array {
        $iching_data = self::getData();

        // --- XỬ LÝ HỆ MAI HOA ---
        if (strpos($mode, 'maihoa') === 0) {
            $n1 = 0; $n2 = 0; $n_total = 0;
            $tz = wp_timezone();
            if (!empty($args['time'])) {
                $dt = new DateTime($args['time'], $tz);
                $ts = $dt->getTimestamp();
            } else {
                $ts = time();
            }

            $rawNumStr = ''; $numStr = ''; $len = 0; $half = 0; $part1 = ''; $part2 = '';
            $obj1 = 0; $obj2 = 0;

            if ($mode === 'maihoa_time') {
                $cc = Iching_Calendar::get($ts);
                $chi_map = ['Tý'=>1,'Sửu'=>2,'Dần'=>3,'Mão'=>4,'Thìn'=>5,'Tỵ'=>6,'Ngọ'=>7,'Mùi'=>8,'Thân'=>9,'Dậu'=>10,'Tuất'=>11,'Hợi'=>12];

                $dd = (int) wp_date('j', $ts);
                $mm = (int) wp_date('n', $ts);
                $yy = (int) wp_date('Y', $ts);

                $am_lich = new Iching_AmLich();
                $lunar_date = $am_lich->convertSolar2Lunar($dd, $mm, $yy, 7.0);

                $ngay_am  = $lunar_date[0];
                $thang_am = $lunar_date[1];
                $nam_am   = $lunar_date[2];

                $nam_am_chi = ($nam_am - 3) % 12;
                if ($nam_am_chi === 0) $nam_am_chi = 12;

                $gio = $chi_map[explode(' ', $cc['gio'])[1]] ?? 1;

                $n1 = $nam_am_chi + $thang_am + $ngay_am;
                $n2 = $n1 + $gio;
                $n_total = $n2;
            }
            elseif ($mode === 'maihoa_number') {
                $rawNumStr = (string)($args['number'] ?? '');
                $numStr = preg_replace('/[^0-9]/', '', $rawNumStr);
                if (empty($numStr)) $numStr = '1234';

                $len = strlen($numStr);

                if ($len === 1) {
                    $half = 1;
                    $part1 = $numStr;
                    $part2 = $numStr;
                    $n1 = (int)$numStr;
                    $n2 = (int)$numStr;
                    $n_total = (int)$numStr;
                } else {
                    $half = (int)floor($len / 2);
                    $part1 = substr($numStr, 0, $half);
                    $part2 = substr($numStr, $half);

                    $n1 = 0; for ($i=0; $i<strlen($part1); $i++) $n1 += (int)$part1[$i];
                    $n2 = 0; for ($i=0; $i<strlen($part2); $i++) $n2 += (int)$part2[$i];
                    $n_total = $n1 + $n2;
                }
            }
            elseif ($mode === 'maihoa_object') {
                $obj1 = (int)($args['obj1'] ?? 1);
                $obj2 = (int)($args['obj2'] ?? 1);
                $n1 = $obj1;
                $n2 = $obj2;
                $n_total = $n1 + $n2;
            }

            // ---------------------------------------------------------
            // ĐOẠN NÀY LÀ LOGIC CHUNG CHO MỌI PHƯƠNG PHÁP MAI HOA
            // (Chỉ chạy 1 lần duy nhất, không lặp lại)
            // ---------------------------------------------------------
            $thuong_mod = $n1 % 8;      if ($thuong_mod === 0) $thuong_mod = 8;
            $ha_mod     = $n2 % 8;      if ($ha_mod === 0) $ha_mod = 8;
            $hao_mod    = $n_total % 6; if ($hao_mod === 0) $hao_mod = 6;

            $bagua_names = [ 1=>'Càn', 2=>'Đoài', 3=>'Ly', 4=>'Chấn', 5=>'Tốn', 6=>'Khảm', 7=>'Cấn', 8=>'Khôn' ];
            $bagua = [ 1=>'111', 2=>'110', 3=>'101', 4=>'100', 5=>'011', 6=>'010', 7=>'001', 8=>'000' ];

            $thuong_bin = $bagua[$thuong_mod];
            $ha_bin     = $bagua[$ha_mod];
            $chu_bin = $ha_bin . $thuong_bin;

            $changing_line = $hao_mod;
            $bien_bin = $chu_bin;
            $bien_bin[$changing_line - 1] = $chu_bin[$changing_line - 1] === '1' ? '0' : '1';

            // Quẻ Hỗ được sinh ra duy nhất ở đây!
            $ho_bin = $chu_bin[1] . $chu_bin[2] . $chu_bin[3] . $chu_bin[2] . $chu_bin[3] . $chu_bin[4];

            // Ráp data giao diện cho riêng hàm Number
            if ($mode === 'maihoa_number') {
                $number_meta = [
                    'raw' => $rawNumStr,
                    'sanitized' => $numStr,
                    'length' => $len,
                    'half' => $half,
                    'part1' => $part1,
                    'part2' => $part2,
                    'sum1' => $n1,
                    'sum2' => $n2,
                    'n1' => $n1,
                    'n2' => $n2,
                    'n_total' => $n_total,
                    'thuong_mod' => $thuong_mod,
                    'ha_mod' => $ha_mod,
                    'hao_mod' => $hao_mod,
                    'thuong_name' => $bagua_names[$thuong_mod],
                    'ha_name'     => $bagua_names[$ha_mod],
                    'changing_line' => $changing_line
                ];
            } elseif ($mode === 'maihoa_object') {
                $object_meta = [
                    'obj1' => $obj1,
                    'obj2' => $obj2,
                    'n1' => $n1,
                    'n2' => $n2,
                    'n_total' => $n_total,
                    'thuong_mod' => $thuong_mod,
                    'ha_mod' => $ha_mod,
                    'hao_mod' => $hao_mod,
                    'thuong_name' => $bagua_names[$thuong_mod],
                    'ha_name' => $bagua_names[$ha_mod],
                    'changing_line' => $changing_line,
                ];
            }

            $result = [
                'mode'           => 'maihoa',
                'chu_key'        => $chu_bin,
                'ho_key'         => $ho_bin,
                'bien_key'       => $bien_bin,
                'changing_line'  => $changing_line,
                'toss_time'      => wp_date('Y-m-d H:i:s', $ts),
                'names'          => [
                    'chu'  => $iching_data[$chu_bin]['name_vi']  ?? '',
                    'ho'   => $iching_data[$ho_bin]['name_vi']   ?? '',
                    'bien' => $iching_data[$bien_bin]['name_vi'] ?? '',
                ],
            ];

            if ($mode === 'maihoa_number') {
                $result['number_meta'] = $number_meta;
            } elseif ($mode === 'maihoa_object') {
                $result['object_meta'] = $object_meta;
            } elseif ($mode === 'maihoa_time') {
                $chi_names = [1=>'Tý',2=>'Sửu',3=>'Dần',4=>'Mão',5=>'Thìn',6=>'Tỵ',7=>'Ngọ',8=>'Mùi',9=>'Thân',10=>'Dậu',11=>'Tuất',12=>'Hợi'];
                $result['time_meta'] = [
                    'nam_chi'      => $nam_am_chi,
                    'nam_chi_name' => $chi_names[$nam_am_chi] ?? '',
                    'thang_am'     => $thang_am,
                    'ngay_am'      => $ngay_am,
                    'gio_chi'      => $gio,
                    'gio_chi_name' => $chi_names[$gio] ?? '',
                    'n1'           => $n1,
                    'n2'           => $n2,
                    'n_total'      => $n_total,
                    'thuong_mod'   => $thuong_mod,
                    'ha_mod'       => $ha_mod,
                    'hao_mod'      => $hao_mod,
                    'thuong_name'  => $bagua_names[$thuong_mod],
                    'ha_name'      => $bagua_names[$ha_mod],
                    'changing_line' => $changing_line,
                ];
            }

            return $result;
        }

        // --- XỬ LÝ HỆ LỤC HÀO ---
        $tosses         = [];
        $chu_bin        = '';
        $bien_bin       = '';
        $changing_lines = [];

        for ($i = 0; $i < 6; $i++) {
            $sum      = rand(2, 3) + rand(2, 3) + rand(2, 3);
            $tosses[] = $sum;

            if ($sum === 6) {
                $chu_bin  .= '0';
                $bien_bin .= '1';
                $changing_lines[] = $i + 1;
            } elseif ($sum === 7) {
                $chu_bin  .= '1';
                $bien_bin .= '1';
            } elseif ($sum === 8) {
                $chu_bin  .= '0';
                $bien_bin .= '0';
            } elseif ($sum === 9) {
                $chu_bin  .= '1';
                $bien_bin .= '0';
                $changing_lines[] = $i + 1;
            }
        }

        $ho_bin = $chu_bin[1] . $chu_bin[2] . $chu_bin[3]
            . $chu_bin[2] . $chu_bin[3] . $chu_bin[4];

        return [
            'mode'           => 'luchao',
            'tosses'         => $tosses,
            'chu_key'        => $chu_bin,
            'ho_key'         => $ho_bin,
            'bien_key'       => $bien_bin,
            'changing_lines' => $changing_lines,
            'toss_time'      => current_time('mysql'),
            'names'          => [
                'chu'  => $iching_data[$chu_bin]['name_vi']  ?? '',
                'ho'   => $iching_data[$ho_bin]['name_vi']   ?? '',
                'bien' => ($chu_bin !== $bien_bin)
                    ? ($iching_data[$bien_bin]['name_vi'] ?? '')
                    : 'Không có Hào Động',
            ],
        ];
    }

    public static function validateLite(array $lite): bool {
        if (!isset($lite['chu_key'], $lite['ho_key'], $lite['bien_key'])) return false;

        foreach (['chu_key', 'ho_key', 'bien_key'] as $k) {
            if (!preg_match('/^[01]{6}$/', $lite[$k])) return false;
        }

        if (isset($lite['mode']) && strpos($lite['mode'], 'maihoa') === 0) {
            if (!isset($lite['changing_line'])) return false;
            $cl = $lite['changing_line'];
            if (!is_int($cl) || $cl < 1 || $cl > 6) return false;

            if (($lite['mode'] ?? '') === 'maihoa' && isset($lite['number_meta']) && !is_array($lite['number_meta'])) {
                return false;
            }
        } else {
            if (!isset($lite['tosses'])) return false;
        }

        return true;
    }

    public static function hydrate(array $liteData): array {
        $iching_data = self::getData();

        $fullData = [
            'chu'            => $iching_data[$liteData['chu_key']]  ?? null,
            'ho'             => $iching_data[$liteData['ho_key']]   ?? null,
            'bien'           => null,
            'number_meta'    => $liteData['number_meta'] ?? null,
            'object_meta'    => $liteData['object_meta'] ?? null,
            'time_meta'      => $liteData['time_meta'] ?? null,
            'tosses'         => $liteData['tosses'] ?? [],
            'changing_lines' => $liteData['changing_lines'] ?? [],
            'changing_line'  => $liteData['changing_line'] ?? 0,
            'toss_time'      => $liteData['toss_time'] ?? current_time('mysql'),
            'chu_key'        => $liteData['chu_key'],
            'ho_key'         => $liteData['ho_key'],
            'bien_key'       => $liteData['bien_key'],
        ];

        if ($liteData['chu_key'] !== $liteData['bien_key']) {
            $fullData['bien'] = $iching_data[$liteData['bien_key']] ?? null;
        }

        return $fullData;
    }
}