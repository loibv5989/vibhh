<?php

if (!defined('ABSPATH')) exit;

class BbZodiac_Calc {

    private static function getData(): array {
        return require BB_ZODIAC_PLUGIN_DIR . 'data/zodiac.php';
    }

    public static function calculate(string $dob): array {
        [$day, $month, $year] = self::parseDob($dob);
        self::validateDate($day, $month, $year);

        $data = self::getData();
        $md = sprintf('%02d-%02d', $month, $day);

        $foundSignKey = 'aries';
        foreach ($data['signs'] as $key => $sign) {
            if (self::isDateInRange($md, $sign['date_range']['start'], $sign['date_range']['end'])) {
                $foundSignKey = $key;
                break;
            }
        }
        $signData = $data['signs'][$foundSignKey];

        $foundDecan = 1;
        foreach ($signData['decans'] as $idx => $decan) {
            if (self::isDateInRange($md, $decan['days'][0], $decan['days'][1])) {
                $foundDecan = $idx;
                break;
            }
        }
        $decanData = $signData['decans'][$foundDecan];
        $cuspData = null;
        foreach ($data['cusps'] as $key => $cusp) {
            if (self::isDateInRange($md, $cusp['date_range']['start'], $cusp['date_range']['end'])) {
                $cuspData = $cusp;
                break;
            }
        }

        return [
            'id'            => $signData['id'],
            'name'          => $signData['name'],
            'symbol'        => $signData['symbol'],
            'element'       => $signData['element'],
            'planet'        => $signData['planet'],
            'quality'       => $signData['quality'],
            'polarity'      => $signData['polarity'],
            'keywords'      => $signData['keywords'],
            'decan'         => $foundDecan,
            'sub_ruler'     => $decanData['ruler'],
            'decan_vibe'    => self::spinText($decanData['vibe'] ?? ''),
            'has_cusp'      => $cuspData !== null,
            'cusp_name'     => $cuspData['name']  ?? '',
            'cusp_blend'    => $cuspData['blend'] ?? '',
            'cusp_vibe'     => self::spinText($cuspData['vibe']  ?? ''),
            'compatibility' => $signData['compatibility'] ?? [],
            'easter_eggs'   => self::calculateEasterEggs($day, $month, $year),
            'horoscope_life'=> $signData['horoscope_life'] ?? '',
            'personality'   => $signData['personality'] ?? [],
        ];
    }

    private static function calculateEasterEggs(int $day, int $month, int $year): array {
        $easter_eggs = [];
        $dob = DateTime::createFromFormat('Y-m-d', "$year-$month-$day");
        $now = new DateTime();

        if ($dob && $dob > $now) {
            $maxAllowed = (clone $now)->modify('+5 years');
            if ($dob > $maxAllowed) {
                $easter_eggs[] = ['type' => 'future'];
            }
        } else if ($dob) {
            $age = $dob->diff($now)->y;
            if ($age < 18) $easter_eggs[] = ['type' => 'under18', 'age' => $age];
            if ($age > 100) $easter_eggs[] = ['type' => 'over100', 'age' => $age];
        }

        return $easter_eggs;
    }

    private static function isDateInRange(string $date, string $start, string $end): bool {
        if ($start <= $end) {
            return $date >= $start && $date <= $end;
        } else {
            return $date >= $start || $date <= $end;
        }
    }

    private static function parseDob(string $dob): array {
        $dob = trim($dob);
        if (!preg_match('/^(\d{1,2})[\/.\-\s](\d{1,2})[\/.\-\s](\d{4})$/', $dob, $m)) {
            throw new InvalidArgumentException("Định dạng ngày sinh không hợp lệ. Ví dụ: 15/12/1999.");
        }
        return [(int)$m[1], (int)$m[2], (int)$m[3]];
    }

    private static function validateDate(int $day, int $month, int $year): void {
        if (!checkdate($month, $day, $year)) {
            throw new InvalidArgumentException("Ngày sinh không hợp lệ: {$day}/{$month}/{$year}.");
        }
    }

    public static function parseResponse(string $raw): array {
        $hints = [];
        $tabs  = ['chi_tiet' => '', 'cach_tinh' => ''];
        $raw_html = self::markdownToHtml(trim($raw));

        $hasZdcHtml = false;
        if (preg_match('/\[ZDC_HTML\](.*?)\[\/ZDC_HTML\]/s', $raw, $m)) {
            $hasZdcHtml = true;
            $content = trim($m[1]);
            
            // Remove guidance text that starts with (Gợi ý: or similar
            $content = preg_replace('/^\(.*?\)\s*/s', '', $content);
            $content = preg_replace('/^\(.*$/s', '', $content);
            $content = trim($content);
            
            $tabs['chi_tiet'] = self::markdownToHtml($content);
        } else {
            // Fallback: if no ZDC_HTML tags, treat entire response as content
            $hasZdcHtml = true;
            $cleanContent = trim($raw);
            
            // Remove any meta content or guidance text
            $cleanContent = preg_replace('/^\(.*?\)\s*/s', '', $cleanContent);
            $cleanContent = preg_replace('/^\[.*?\]\s*/s', '', $cleanContent);
            $cleanContent = trim($cleanContent);
            
            // If content is not empty after cleaning, use it
            if (!empty($cleanContent)) {
                $tabs['chi_tiet'] = self::markdownToHtml($cleanContent);
            }
        }

        return [
            'hints' => $hints,
            'tabs' => $tabs,
            'raw_html' => $raw_html,
            'has_zdc_html' => $hasZdcHtml,
        ];
    }

    public static function markdownToHtml(string $md): string {
        $md = preg_replace('/^[\-]{3,}$/m', '', $md);
        $md = preg_replace('/^\*{3,}$/m', '', $md);
        $md = preg_replace('/^_{3,}$/m', '', $md);

        if (!class_exists('Parsedown')) {
            require_once BB_ZODIAC_PLUGIN_DIR . 'lib/Parsedown.php';
        }

        $Parsedown = new Parsedown();
        return $Parsedown->text($md);
    }

    public static function normalizeDob(string $dob): string {
        $dob  = trim($dob);
        $d    = DateTime::createFromFormat('Y-m-d', $dob);
        if ($d && $d->format('Y-m-d') === $dob) return $d->format('d/m/Y');
        $norm = preg_replace('/[\-\.\s]+/', '/', $dob);
        foreach (['d/m/Y', 'j/n/Y'] as $fmt) {
            $d = DateTime::createFromFormat($fmt, $norm);
            if ($d) return $d->format('d/m/Y');
        }
        return $dob;
    }

    public static function calculateLoveProfile(string $name1, string $dob1, string $name2, string $dob2): array {
        $name1 = self::normalizePersonName($name1);
        $name2 = self::normalizePersonName($name2);

        $sign1 = self::calculate($dob1);
        $sign2 = self::calculate($dob2);

        $analysis = self::calculateDeepLove($sign1, $sign2);
        $base_percent = $analysis['score'];

        $blocks = [];
        $d1Obj = DateTime::createFromFormat('!d/m/Y', $dob1);
        $d2Obj = DateTime::createFromFormat('!d/m/Y', $dob2);
        $now = new DateTime();

        $age1 = $d1Obj ? $d1Obj->diff($now)->y : 0;
        $age2 = $d2Obj ? $d2Obj->diff($now)->y : 0;

        foreach([['n'=>$name1, 'd'=>$d1Obj, 'age'=>$age1], ['n'=>$name2, 'd'=>$d2Obj, 'age'=>$age2]] as $u) {
            if (!$u['d'] || $u['d'] > $now) {
                $blocks[] = ['type'=>'future', 'name'=>$u['n']];
            } else {
                if ($u['age'] <= 3) $blocks[] = ['type'=>'infant', 'name'=>$u['n']];
                elseif ($u['age'] < 14) $blocks[] = ['type'=>'under14', 'name'=>$u['n']];
                elseif ($u['age'] > 90) $blocks[] = ['type'=>'over90', 'name'=>$u['n']];
            }
        }

        $norm1 = self::normalizeNameStr($name1);
        $norm2 = self::normalizeNameStr($name2);
        if ($norm1 === $norm2) {
            $blocks[] = ['type'=>'same_name'];
        }

        $diff = $age1 - $age2;
        $absDiff = abs($diff);
        $penalty = 0;
        $age_gap_msg = '';

        if ($diff > 0) {
            if ($absDiff >= 10 && $absDiff <= 15) { $penalty = 5; $age_gap_msg = ""; }
            elseif ($absDiff >= 16 && $absDiff <= 25) { $penalty = 10; $age_gap_msg = ""; }
            elseif ($absDiff >= 26 && $absDiff <= 30) { $penalty = 15; $age_gap_msg = "Thử thách, trở ngại rào cản, khoảng cách"; }
            elseif ($absDiff >= 31 && $absDiff <= 40) { $penalty = 25; $age_gap_msg = "Thử thách, trở ngại rào cản, khoảng cách lớn"; }
            elseif ($absDiff >= 41 && $absDiff <= 50) { $penalty = 35; $age_gap_msg = "Rào cản rất lớn"; }
            elseif ($absDiff > 50) { $penalty = 50; $age_gap_msg = "Có khoảng cách và rào cản rất lớn"; }
        } elseif ($diff < 0) {
            if ($absDiff >= 4 && $absDiff <= 9) { $penalty = 5; $age_gap_msg = ""; }
            elseif ($absDiff >= 10 && $absDiff <= 15) { $penalty = 12; $age_gap_msg = ""; }
            elseif ($absDiff >= 16 && $absDiff <= 20) { $penalty = 20; $age_gap_msg = "Trở ngại rào cản, khoảng cách"; }
            elseif ($absDiff >= 21 && $absDiff <= 30) { $penalty = 30; $age_gap_msg = "Trở ngại rào cản, khoảng cách lớn"; }
            elseif ($absDiff >= 31 && $absDiff <= 40) { $penalty = 40; $age_gap_msg = "Rào cản rất lớn"; }
            elseif ($absDiff > 40) { $penalty = 50; $age_gap_msg = "Có khoảng cách và rào cản rất lớn"; }
        }

        $final_percent = max(1, $base_percent - $penalty);
        $analysis['score'] = $final_percent;

        return [
            'name1'      => $name1,
            'name2'      => $name2,
            'dob1'       => $dob1,
            'dob2'       => $dob2,
            'sign1_name' => (string)($sign1['name'] ?? '') . ' ' . (string)($sign1['symbol'] ?? ''),
            'sign2_name' => (string)($sign2['name'] ?? '') . ' ' . (string)($sign2['symbol'] ?? ''),
            'sign1'      => [
                'id'           => (string)($sign1['id'] ?? ''),
                'name'         => (string)($sign1['name'] ?? ''),
                'symbol'       => (string)($sign1['symbol'] ?? ''),
                'element'      => (string)($sign1['element'] ?? ''),
                'planet'       => (string)($sign1['planet'] ?? ''),
                'quality'      => (string)($sign1['quality'] ?? ''),
                'polarity'     => (string)($sign1['polarity'] ?? ''),
                'keywords'     => (string)($sign1['keywords'] ?? ''),
                'decan'        => (int)($sign1['decan'] ?? 0),
                'sub_ruler'    => (string)($sign1['sub_ruler'] ?? ''),
                'compatibility'=> (array)($sign1['compatibility'] ?? []),
            ],
            'sign2'      => [
                'id'           => (string)($sign2['id'] ?? ''),
                'name'         => (string)($sign2['name'] ?? ''),
                'symbol'       => (string)($sign2['symbol'] ?? ''),
                'element'      => (string)($sign2['element'] ?? ''),
                'planet'       => (string)($sign2['planet'] ?? ''),
                'quality'      => (string)($sign2['quality'] ?? ''),
                'polarity'     => (string)($sign2['polarity'] ?? ''),
                'keywords'     => (string)($sign2['keywords'] ?? ''),
                'decan'        => (int)($sign2['decan'] ?? 0),
                'sub_ruler'    => (string)($sign2['sub_ruler'] ?? ''),
                'compatibility'=> (array)($sign2['compatibility'] ?? []),
            ],
            'element1'        => (string)($sign1['element'] ?? ''),
            'element2'        => (string)($sign2['element'] ?? ''),
            'base_percent'    => $base_percent,
            'final_percent'   => $final_percent,
            'penalty_percent' => $penalty,
            'age_gap_msg'     => $age_gap_msg,
            'percent'         => $final_percent,
            'analysis'        => $analysis,
            'blocks'          => $blocks,
        ];
    }

    public static function parseLoveResponse(string $raw): array {
        $tabs = ['phan_tich' => ''];
        $hasZdcHtml = false;

        if (preg_match('/\[ZDC_HTML\](.*?)\[\/ZDC_HTML\]/s', $raw, $m)) {
            $hasZdcHtml = true;
            $tabs['phan_tich'] = self::markdownToHtml(trim($m[1]));
        }

        return [
            'tabs' => $tabs,
            'has_zdc_html' => $hasZdcHtml,
        ];
    }

    public static function normalizePersonName(string $name): string {
        $cleaned = preg_replace('/\s+/u', ' ', trim($name));
        if ($cleaned === '') return '';

        $words = preg_split('/\s+/u', mb_strtolower($cleaned, 'UTF-8')) ?: [];
        $normalizedWords = [];

        foreach ($words as $word) {
            $parts = explode('-', $word);
            $normalizedParts = [];

            foreach ($parts as $part) {
                if ($part === '') {
                    continue;
                }
                $first = mb_substr($part, 0, 1, 'UTF-8');
                $rest = mb_substr($part, 1, null, 'UTF-8');
                $normalizedParts[] = mb_strtoupper($first, 'UTF-8') . $rest;
            }

            if (!empty($normalizedParts)) {
                $normalizedWords[] = implode('-', $normalizedParts);
            }
        }

        return implode(' ', $normalizedWords);
    }

    private static function calculateDeepLove(array $sign1, array $sign2): array {
        $score = 60;
        $strengths = [];
        $challenges = [];

        $id1 = $sign1['id'] ?? ''; $id2 = $sign2['id'] ?? '';
        $compat1 = $sign1['compatibility'] ?? [];
        $isBest = in_array($id2, $compat1['best_match'] ?? []);
        $isWorst = in_array($id2, $compat1['worst_match'] ?? []);
        $isKarmic = in_array($id2, $compat1['karmic_match'] ?? []);

        $aspectLabel = 'Góc chiếu trung tính';
        $aspectHint = '— Phát triển tình cảm dựa trên nỗ lực.';
        if ($isBest) {
            $score += 15; $aspectLabel = 'Tam hợp / Lục hợp (Hòa hợp)'; $aspectHint = '— Tần số tương hợp tự nhiên rất cao.';
            $strengths[] = 'Góc chiếu lý tưởng, dễ thu hút và nâng đỡ nhau trong cuộc sống.';
        } elseif ($isWorst) {
            $score -= 10; $aspectLabel = 'Vuông góc / Lệch (Thử thách)'; $aspectHint = '— Cần vượt qua khác biệt cốt lõi.';
            $challenges[] = 'Dễ phát sinh xung đột do khác biệt sâu sắc về nhu cầu cảm xúc.';
        } elseif ($isKarmic) {
            $score += 5; $aspectLabel = 'Đối đỉnh (Duyên nghiệp)'; $aspectHint = '— Lực hút nam châm mãnh liệt.';
            $strengths[] = 'Liên kết duyên nghiệp mang lại lực hút mạnh và những bài học trưởng thành cho cả hai.';
        } elseif ($id1 === $id2) {
            $score += 0; $aspectLabel = 'Trùng tụ (Giống hệt nhau)'; $aspectHint = '— Như soi gương.';
            $challenges[] = 'Hiểu nhau như soi gương, nhưng khi giận dữ dễ phóng đại khuyết điểm của nhau.';
        }

        $e1 = $sign1['element'] ?? ''; $e2 = $sign2['element'] ?? '';
        $elementMap = ['Lửa' => 'Khí', 'Khí' => 'Lửa', 'Đất' => 'Nước', 'Nước' => 'Đất'];
        $elementHint = '';
        if ($e1 === $e2) {
            $score += 10; $elementHint = '— Chia sẻ chung nhịp sống';
            $strengths[] = "Cùng nguyên tố $e1 giúp hai bạn chia sẻ lý tưởng và cách biểu đạt tình cảm tương đồng.";
        } elseif (($elementMap[$e1] ?? '') === $e2) {
            $score += 5; $elementHint = '— Bổ trợ và truyền cảm hứng';
            $strengths[] = "Nguyên tố tương sinh ($e1 - $e2): Bổ trợ hoàn hảo, người này tiếp thêm sức mạnh cho người kia.";
        } else {
            $score -= 10; $elementHint = '— Cần dung hòa hệ giá trị';
            $challenges[] = "Nguyên tố tương khắc ($e1 - $e2): Cần nhiều sự thấu hiểu để dung hòa sự khác biệt cốt lõi.";
        }

        $mod1 = trim(explode('(', $sign1['quality'] ?? '')[0]);
        $mod2 = trim(explode('(', $sign2['quality'] ?? '')[0]);
        $modHint = '';
        if ($mod1 === $mod2) {
            $score -= 5; $modHint = '— Dễ xảy ra tranh chấp';
            if ($mod1 === 'Thống Lĩnh') $challenges[] = 'Cùng nhóm Thống Lĩnh: Cả hai đều muốn làm chủ, dễ xảy ra tranh giành quyền quyết định.';
            if ($mod1 === 'Kiên Định') $challenges[] = 'Cùng nhóm Kiên Định: Rất chung thủy nhưng đều bướng bỉnh, khó ai chịu nhường ai.';
            if ($mod1 === 'Linh Hoạt') $challenges[] = 'Cùng nhóm Linh Hoạt: Ở bên nhau vui vẻ nhưng có thể thiếu đi sự cam kết vững chắc.';
        } else {
            $score += 5; $modHint = '— Bù trừ phong cách hành động';
            $strengths[] = "Đặc tính bổ trợ ($mod1 - $mod2): Người tung kẻ hứng, người vạch kế hoạch người thực thi.";
        }

        $pol1 = explode(' ', $sign1['polarity'] ?? '')[0] ?? '';
        $pol2 = explode(' ', $sign2['polarity'] ?? '')[0] ?? '';
        $polHint = '';
        if ($pol1 === $pol2) {
            $score += 5; $polHint = '— Đồng điệu năng lượng';
            if ($pol1 === 'Dương') $strengths[] = 'Cùng hệ Dương: Mối quan hệ nồng cháy, hướng ngoại và đầy nhiệt huyết.';
            if ($pol1 === 'Âm') $strengths[] = 'Cùng hệ Âm: Mối quan hệ sâu sắc, hướng nội và mang tính gắn kết, nuôi dưỡng cao.';
        } else {
            $score += 5; $polHint = '— Bù trừ hoàn hảo';
            $strengths[] = 'Khác biệt Âm/Dương: Tạo ra sức hút nam châm mãnh liệt, người này bù đắp phần khuyết thiếu của người kia.';
        }

        $p1 = $sign1['planet'] ?? ''; $sr1 = $sign1['sub_ruler'] ?? '';
        $p2 = $sign2['planet'] ?? ''; $sr2 = $sign2['sub_ruler'] ?? '';
        $hasPlanetMatch = ($p1 === $p2 || $sr1 === $sr2 || $p1 === $sr2 || $sr1 === $p2);
        $planetHint = $hasPlanetMatch ? '— Tâm giao tiềm thức' : '— Tôn trọng không gian riêng';
        if ($hasPlanetMatch) {
            $score += 10;
            $strengths[] = 'Có liên kết Hành tinh/Decan: Sự đồng điệu đáng kinh ngạc ở tầng sâu tâm hồn và tiềm thức.';
        }

        $score = max(35, min(99, $score));

        return [
            'score' => $score,
            'aspect_label' => $aspectLabel,
            'aspect_hint' => $aspectHint,
            'element_hint' => $elementHint,
            'mod_1' => $mod1,
            'mod_2' => $mod2,
            'mod_hint' => $modHint,
            'pol_1' => $pol1,
            'pol_2' => $pol2,
            'pol_hint' => $polHint,
            'planet_match' => $hasPlanetMatch,
            'planet_hint' => $planetHint,
            'strengths' => $strengths,
            'challenges' => $challenges
        ];
    }

    public static function getHoroscopeProfile(string $signId, string $period): array {
        $data = self::getData();
        if (!isset($data['signs'][$signId])) {
            throw new InvalidArgumentException("Cung hoàng đạo không hợp lệ.");
        }

        $sign = $data['signs'][$signId];

        return [
            'id'            => $sign['id'] ?? $signId,
            'name'          => $sign['name'] ?? '',
            'symbol'        => $sign['symbol'] ?? '',
            'element'       => $sign['element'] ?? '',
            'planet'        => $sign['planet'] ?? '',
            'quality'       => $sign['quality'] ?? '',
            'polarity'      => $sign['polarity'] ?? '',
            'keywords'      => $sign['keywords'] ?? '',
            'compatibility' => $sign['compatibility'] ?? [],
            'horoscope_life'=> $sign['horoscope_life'] ?? 'Bản mệnh mang năng lượng mạnh mẽ, không ngừng phát triển và khám phá các giá trị cốt lõi của bản thân trong suốt hành trình cuộc đời.',
            'period'        => $period,
        ];
    }

    public static function getStaticHoroscopeLines(string $signId, string $period, string $avoidDomain = ''): array {
        $tuviData = include BB_ZODIAC_PLUGIN_DIR . 'data/tu-vi.php';
        if (!isset($tuviData[$period])) return ['lines' => [], 'primary' => ''];

        $elementKey = $tuviData['__zodiac_map'][$signId]['element'] ?? 'fire';

        $timeContext = date('Y-m-d');
        if ($period === 'weekly') $timeContext = date('Y-\WW');
        if ($period === 'monthly') $timeContext = date('Y-m');

        $seed = crc32($signId . $period . $timeContext);
        mt_srand($seed);

        $pool = $tuviData[$period][$elementKey] ?? [];
        if (empty($pool)) {
            mt_srand();
            return ['lines' => [], 'primary' => ''];
        }

        $domains = ['career', 'love', 'money'];

        $domainLabels = [
            'career' => 'Công việc',
            'love'   => 'Tình cảm',
            'money'  => 'Tài chính'
        ];

        for ($i = count($domains) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            $temp = $domains[$i];
            $domains[$i] = $domains[$j];
            $domains[$j] = $temp;
        }

        if ($domains[0] === $avoidDomain) {
            $temp = $domains[0];
            $domains[0] = $domains[1];
            $domains[1] = $domains[2];
            $domains[2] = $temp;
        }

        $primaryDomain   = $domains[0];
        $secondaryDomain = $domains[1];
        $tertiaryDomain  = $domains[2];

        $vars = [
            '{month}' => date('n'),
            '{today}' => date('j/n'),
        ];

        $anchors = $tuviData['anchors'][$period][$elementKey] ?? "";
        $anchorText = self::spinText(str_replace(array_keys($vars), array_values($vars), $anchors));

        $processNode = function($domain, $isPrimary, $isTertiary) use ($pool, $vars) {
            if (empty($pool[$domain])) return "";

            $keys = array_keys($pool[$domain]);
            $randomKey = $keys[mt_rand(0, count($keys) - 1)];
            $node = $pool[$domain][$randomKey];

            if (is_array($node)) {
                $headline    = self::spinText(str_replace(array_keys($vars), array_values($vars), $node['headline'] ?? ''));
                $explanation = self::spinText(str_replace(array_keys($vars), array_values($vars), $node['explanation'] ?? ''));
                $action      = self::spinText(str_replace(array_keys($vars), array_values($vars), $node['action'] ?? ''));
                $text = $isPrimary ? "$headline $explanation $action" : ($isTertiary ? "$headline" : "$headline $explanation");
            } else {
                $text = self::spinText(str_replace(array_keys($vars), array_values($vars), $node));
            }
            return $text;
        };

        $results = [];

        $p1Text = $processNode($primaryDomain, true, false);
        if ($p1Text) {
            $line = trim($anchorText . " " . $p1Text);
            $results[] = [
                'label' => $domainLabels[$primaryDomain],
                'text'  => str_replace(array_keys($vars), array_values($vars), $line)
            ];
        }

        $p2Text = $processNode($secondaryDomain, false, false);
        if ($p2Text) {
            $results[] = [
                'label' => $domainLabels[$secondaryDomain],
                'text'  => str_replace(array_keys($vars), array_values($vars), $p2Text)
            ];
        }

        $p3Text = $processNode($tertiaryDomain, false, true);
        if ($p3Text) {
            $results[] = [
                'label' => $domainLabels[$tertiaryDomain],
                'text'  => str_replace(array_keys($vars), array_values($vars), $p3Text)
            ];
        }

        mt_srand();
        return [
            'lines' => $results,
            'primary' => $primaryDomain
        ];
    }

    private static function normalizeNameStr(string $str): string {
        $map = ['à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ'=>'a','è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ'=>'e','ì|í|ị|ỉ|ĩ'=>'i','ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ'=>'o','ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ'=>'u','ỳ|ý|ỵ|ỷ|ỹ'=>'y','đ'=>'d'];
        foreach($map as $v => $r) $str = preg_replace("/($v)/i", $r, $str);
        return strtoupper(preg_replace('/[^A-Za-z]/', '', $str));
    }

    public static function spinText(string $text): string {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            function ($matches) {
                $parts = explode('|', $matches[1]);
                return self::spinText($parts[array_rand($parts)]);
            },
            $text
        );
    }
}