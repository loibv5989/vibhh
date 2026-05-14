<?php
if (!defined('ABSPATH')) exit;

class TshLove_Calc {
    private const LETTER_MAP = [
        'A'=>1,'J'=>1,'S'=>1, 'B'=>2,'K'=>2,'T'=>2, 'C'=>3,'L'=>3,'U'=>3,
        'D'=>4,'M'=>4,'V'=>4, 'E'=>5,'N'=>5,'W'=>5, 'F'=>6,'O'=>6,'X'=>6,
        'G'=>7,'P'=>7,'Y'=>7, 'H'=>8,'Q'=>8,'Z'=>8, 'I'=>9,'R'=>9
    ];

    private static function formatNumber(int $num): string {
        if (in_array($num, [11, 22, 33])) {
            $single = array_sum(str_split((string)$num));
            return "$num/$single";
        }
        return (string)$num;
    }

    public static function calculate(string $name1, string $dob1, string $name2, string $dob2): array {
        $n1_display = mb_convert_case(trim($name1), MB_CASE_TITLE, 'UTF-8');
        $n2_display = mb_convert_case(trim($name2), MB_CASE_TITLE, 'UTF-8');

        $lp1_res = self::calculateLifePathDetailed($dob1);
        $lp2_res = self::calculateLifePathDetailed($dob2);

        $lp1 = $lp1_res['number'];
        $lp2 = $lp2_res['number'];

        $base_percent = self::calculateMatchPercent($lp1, $lp2, $n1_display, $n2_display, $dob1, $dob2);

        $d1 = DateTime::createFromFormat('!d/m/Y', $dob1);
        $d2 = DateTime::createFromFormat('!d/m/Y', $dob2);
        $now = new DateTime();

        $age1 = $d1 ? $d1->diff($now)->y : 0;
        $age2 = $d2 ? $d2->diff($now)->y : 0;

        $diff = $age1 - $age2;
        $absDiff = abs($diff);
        $penalty = 0;
        $age_gap_msg = '';

        if ($diff > 0) {
            if ($absDiff >= 10 && $absDiff <= 15) { $penalty = 5; $age_gap_msg = "Nam hơn Nữ $absDiff tuổi"; }
            elseif ($absDiff >= 16 && $absDiff <= 25) { $penalty = 10; $age_gap_msg = "Nam hơn Nữ $absDiff tuổi"; }
            elseif ($absDiff >= 26 && $absDiff <= 30) { $penalty = 15; $age_gap_msg = "Nam hơn Nữ $absDiff tuổi (Khoảng cách thế hệ)"; }
            elseif ($absDiff >= 31 && $absDiff <= 40) { $penalty = 25; $age_gap_msg = "Nam hơn Nữ $absDiff tuổi (Khoảng cách lớn)"; }
            elseif ($absDiff >= 41 && $absDiff <= 50) { $penalty = 35; $age_gap_msg = "Nam hơn Nữ $absDiff tuổi (Rào cản rất lớn)"; }
            elseif ($absDiff > 50) { $penalty = 50; $age_gap_msg = "Nam hơn Nữ $absDiff tuổi (Không phù hợp)"; }
        } elseif ($diff < 0) {
            if ($absDiff >= 4 && $absDiff <= 9) { $penalty = 5; $age_gap_msg = "Nữ hơn Nam $absDiff tuổi"; }
            elseif ($absDiff >= 10 && $absDiff <= 15) { $penalty = 12; $age_gap_msg = "Nữ hơn Nam $absDiff tuổi"; }
            elseif ($absDiff >= 16 && $absDiff <= 20) { $penalty = 20; $age_gap_msg = "Nữ hơn Nam $absDiff tuổi (Rào cản lớn)"; }
            elseif ($absDiff >= 21 && $absDiff <= 30) { $penalty = 30; $age_gap_msg = "Nữ hơn Nam $absDiff tuổi (Khoảng cách lớn)"; }
            elseif ($absDiff >= 31 && $absDiff <= 40) { $penalty = 40; $age_gap_msg = "Nữ hơn Nam $absDiff tuổi (Rào cản rất lớn)"; }
            elseif ($absDiff > 40) { $penalty = 50; $age_gap_msg = "Nữ hơn Nam $absDiff tuổi (Không phù hợp)"; }
        }

        $final_percent = max(0, $base_percent - $penalty);

        $match_data = TshLove_Data::getCompatibilityAnalysis($lp1, $lp2, $final_percent);

        $soul1 = self::calculateSoulUrge($name1);
        $soul2 = self::calculateSoulUrge($name2);
        $att1 = self::calculateAttitude($dob1);
        $att2 = self::calculateAttitude($dob2);

        $rel_number = self::reduceNumber($lp1 + $lp2, false);
        $blocks = self::checkBlocks($n1_display, $dob1, $n2_display, $dob2);

        return [
            'name1'   => $n1_display,
            'dob1'    => $dob1,
            'lp1'     => self::formatNumber($lp1),
            'calc1'   => $lp1_res['formula'],
            'desc1'   => TshLove_Data::getLifePathDetail($lp1),
            'soul1'   => self::formatNumber($soul1),
            'att1'    => self::formatNumber($att1),

            'name2'   => $n2_display,
            'dob2'    => $dob2,
            'lp2'     => self::formatNumber($lp2),
            'calc2'   => $lp2_res['formula'],
            'desc2'   => TshLove_Data::getLifePathDetail($lp2),
            'soul2'   => self::formatNumber($soul2),
            'att2'    => self::formatNumber($att2),

            'match_summary' => $match_data['summary'],
            'pros'          => $match_data['pros'],
            'cons'          => $match_data['cons'],
            'advice1'       => $match_data['advice1'],
            'advice2'       => $match_data['advice2'],

            'percent'         => $final_percent,
            'base_percent'    => $base_percent,
            'penalty_percent' => $penalty,
            'final_percent'   => $final_percent,
            'age_gap_msg'     => $age_gap_msg,

            'rel_num'       => $rel_number,
            'blocks'        => $blocks,
            'hints'         => [
                'lp1'   => TshLove_Data::getLifePathHint($lp1),
                'lp2'   => TshLove_Data::getLifePathHint($lp2),
                'soul1' => TshLove_Data::getSoulUrgeHint($soul1),
                'soul2' => TshLove_Data::getSoulUrgeHint($soul2),
                'att1'  => TshLove_Data::getAttitudeHint($att1),
                'att2'  => TshLove_Data::getAttitudeHint($att2),
                'rel'   => TshLove_Data::getRelationshipHint($rel_number),
                'match' => TshLove_Data::getMatchHint($final_percent),
            ]
        ];
    }

    private static function calculateLifePathDetailed(string $dob): array {
        $digits = preg_replace('/[^0-9]/', '', $dob);
        $arr = str_split($digits);
        $total = array_sum($arr);
        $formula = implode(' + ', $arr) . " = $total";
        $final = $total;
        while ($final > 9 && !in_array($final, [11, 22, 33])) {
            $parts = str_split((string)$final);
            $final = array_sum($parts);
            $formula .= " &rarr; " . implode(' + ', $parts) . " = $final";
        }
        return ['number' => $final, 'formula' => $formula];
    }

    private static function calculateMatchPercent(int $lp1, int $lp2, string $n1, string $n2, string $dob1, string $dob2): int {
        $matchMatrix = [
            1  => [1, 5, 7, 3, 9], 2 => [2, 4, 8, 6, 11, 22], 3 => [3, 6, 9, 1, 5],
            4  => [4, 2, 8, 7, 22], 5 => [5, 1, 3, 7, 9], 6 => [6, 2, 8, 3, 9, 33],
            7  => [7, 1, 5, 4], 8 => [8, 2, 4, 6, 22], 9 => [9, 3, 6, 1, 5],
            11 => [2, 4, 6, 8, 11], 22 => [4, 2, 8, 6, 22], 33 => [6, 3, 9, 2, 33]
        ];
        if ($lp1 === $lp2) {
            $base = 90;
        } elseif (isset($matchMatrix[$lp1]) && in_array($lp2, $matchMatrix[$lp1])) {
            $base = 85;
        } elseif (isset($matchMatrix[$lp2]) && in_array($lp1, $matchMatrix[$lp2])) {
            $base = 85;
        } else {
            $base = 55;
        }
        $hashStr = $n1 . $dob1 . $n2 . $dob2;
        $hash = crc32($hashStr);
        return min($base + (abs($hash) % 10), 99);
    }

    private static function calculateSoulUrge(string $name): int {
        $str = self::normalizeNameStr($name);
        $sum = 0; $vowels = ['A', 'E', 'I', 'O', 'U', 'Y'];
        for ($i = 0; $i < strlen($str); $i++) { if (in_array($str[$i], $vowels)) $sum += self::LETTER_MAP[$str[$i]] ?? 0; }
        return self::reduceNumber($sum, true);
    }

    private static function calculateAttitude(string $dob): int {
        [$day, $month] = explode('/', $dob);
        $sum = self::reduceNumber((int)$day, false) + self::reduceNumber((int)$month, false);
        return self::reduceNumber($sum, false);
    }

    private static function normalizeNameStr(string $str): string {
        $map = ['à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ'=>'a','è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ'=>'e','ì|í|ị|ỉ|ĩ'=>'i','ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ'=>'o','ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ'=>'u','ỳ|ý|ỵ|ỷ|ỹ'=>'y','đ'=>'d'];
        foreach($map as $v => $r) $str = preg_replace("/($v)/i", $r, $str);
        return strtoupper(preg_replace('/[^A-Za-z]/', '', $str));
    }

    private static function reduceNumber(int $num, bool $keepMaster = true): int {
        while ($num > 9) {
            if ($keepMaster && in_array($num, [11, 22, 33])) break;
            $num = array_sum(str_split((string)$num));
        }
        return $num;
    }

    private static function checkBlocks(string $n1, string $dob1, string $n2, string $dob2): array {
        $blocks = [];
        $d1 = DateTime::createFromFormat('!d/m/Y', $dob1);
        $d2 = DateTime::createFromFormat('!d/m/Y', $dob2);
        $now = new DateTime();

        foreach([['n'=>$n1, 'd'=>$d1], ['n'=>$n2, 'd'=>$d2]] as $u) {
            if (!$u['d'] || $u['d'] > $now) {
                $blocks[] = ['type'=>'future', 'name'=>$u['n']];
            } else {
                $age = $u['d']->diff($now)->y;

                if ($age <= 3) $blocks[] = ['type'=>'infant', 'name'=>$u['n']];
                elseif ($age < 14) $blocks[] = ['type'=>'under14', 'name'=>$u['n']];
                elseif ($age > 90) $blocks[] = ['type'=>'over90', 'name'=>$u['n']];
            }
        }

        $norm1 = self::normalizeNameStr($n1);
        $norm2 = self::normalizeNameStr($n2);
        if ($norm1 === $norm2) {
            $blocks[] = ['type'=>'same_name'];
        }

        return $blocks;
    }
}