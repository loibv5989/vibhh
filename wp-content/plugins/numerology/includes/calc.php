<?php

if (!defined('ABSPATH')) exit;

class Numerology_Calc {

    private static ?array $nrgy_data = null;

    private const LETTER_MAP = [
        'A'=>1,'J'=>1,'S'=>1, 'B'=>2,'K'=>2,'T'=>2, 'C'=>3,'L'=>3,'U'=>3,
        'D'=>4,'M'=>4,'V'=>4, 'E'=>5,'N'=>5,'W'=>5, 'F'=>6,'O'=>6,'X'=>6,
        'G'=>7,'P'=>7,'Y'=>7, 'H'=>8,'Q'=>8,'Z'=>8, 'I'=>9,'R'=>9
    ];

    private const VOWELS = ['A','E','I','O','U'];

    private const MASTER = [11, 22, 33];

    private const KARMIC = [13, 14, 16, 19];

    private static function checkKarmic(int $num): int {
        if (in_array($num, self::KARMIC)) return $num;
        while ($num > 9) {
            $num = array_sum(str_split((string)$num));
            if (in_array($num, self::KARMIC)) return $num;
        }
        return 0;
    }

    private static function loadData(): array {
        if (self::$nrgy_data === null) {
            self::$nrgy_data = require_once NUMEROLOGY_PLUGIN_DIR . 'includes/data.php';
        }
        return self::$nrgy_data;
    }

    public static function calculate(string $name, string $dob): array {
        [$day, $month, $year] = self::parseDob($dob);
        self::validateDate($day, $month, $year);

        $clean = self::normalizeName($name);
        self::validateName($clean);

        $letters = str_split(str_replace(' ', '', $clean));
        $freq = array_fill(1, 9, 0);
        $exp_raw = 0; $su_raw = 0; $per_raw = 0;

        foreach ($letters as $l) {
            $val = self::LETTER_MAP[$l] ?? 0;
            if ($val > 0) {
                $freq[$val]++;
                $exp_raw += $val;
                if (in_array($l, self::VOWELS, true)) {
                    $su_raw += $val;
                } else {
                    $per_raw += $val;
                }
            }
        }

        $karmic_lessons = [];
        foreach ($freq as $num => $count) { if ($count === 0) $karmic_lessons[] = $num; }

        $max_freq = max($freq);
        $hidden_passion = [];
        if ($max_freq > 0) {
            foreach ($freq as $num => $count) { if ($count === $max_freq) $hidden_passion[] = $num; }
        }

        $subconscious_self = 9 - count($karmic_lessons);
        $words = explode(' ', $clean);
        $balance_sum = 0;
        foreach ($words as $w) { if (!empty($w)) $balance_sum += self::LETTER_MAP[$w[0]] ?? 0; }

        $dateStr = $day . $month . $year;
        $lp_raw = array_sum(str_split($dateStr));

        $life_path   = self::reduce($lp_raw);
        $expression  = self::reduce($exp_raw);
        $soul_urge   = self::reduce($su_raw);
        $personality = self::reduce($per_raw);
        $maturity    = self::reduce($life_path + $expression);

        $currentYear = (int) gmdate('Y');
        $py_raw = array_sum(str_split($day . $month . $currentYear));
        $personal_year  = self::reduceToSingle($py_raw);
        $personal_month = self::reduceToSingle($personal_year + (int)gmdate('n'));
        $personal_day   = self::reduceToSingle($personal_month + (int)gmdate('j'));

        $pinnacles = self::calculatePinnacles($day, $month, $year, $life_path);
        $challenges = self::calculateChallenges($day, $month, $year);

        $k_day = self::checkKarmic($day);
        $k_lp  = self::checkKarmic($lp_raw);
        $k_exp = self::checkKarmic($exp_raw);
        $k_su  = self::checkKarmic($su_raw);
        $k_per = self::checkKarmic($per_raw);

        $k_debt_display = [];
        if ($k_day > 0) $k_debt_display[] = $k_day;
        if ($k_lp > 0)  $k_debt_display[] = $k_lp;
        if ($k_exp > 0) $k_debt_display[] = $k_exp;
        if ($k_su > 0)  $k_debt_display[] = $k_su;
        if ($k_per > 0) $k_debt_display[] = $k_per;

        $k_debt_str = empty($k_debt_display) ? 'None' : implode(', ', array_unique($k_debt_display));
        $k_less_str = empty($karmic_lessons) ? 'None' : implode(', ', $karmic_lessons);
        $att_raw = $day + $month;

        $dateDigits = str_split($day . $month . $year);
        $counts = array_fill(1, 9, 0);
        foreach ($dateDigits as $d) {
            $v = (int)$d;
            if ($v >= 1 && $v <= 9) $counts[$v]++;
        }

        return [
            'life_path'        => $life_path,
            'destiny'          => $expression,
            'attitude'         => self::reduce($att_raw),
            'birthday'         => $day,
            'soul_urge'        => $soul_urge,
            'expression'       => $expression,
            'personality'      => $personality,
            'maturity'         => $maturity,
            'balance'          => self::reduce($balance_sum),
            'karmic_lessons'   => $k_less_str,
            'karmic_debt'      => $k_debt_str,
            'karmic_lessons_arr'=> $karmic_lessons,
            'pinnacles'        => implode('-', $pinnacles['peaks']),
            'challenges'       => implode('-', $challenges),
            'personal_year'    => $personal_year,
            'personal_month'   => $personal_month,
            'personal_day'     => $personal_day,
            'rational_thought' => self::reduce((self::LETTER_MAP[mb_substr(ltrim($clean), 0, 1)] ?? 0) + $day),
            'subconscious_self'=> $subconscious_self,
            'hidden_passion'   => $hidden_passion,
            'pinnacle_details' => $pinnacles,
            'karmic_debt_arr'  => [
                'birthday'    => ($k_day > 0) ? (string)$k_day : '',
                'life_path'   => ($k_lp > 0)  ? "$k_lp/$life_path" : '',
                'expression'  => ($k_exp > 0) ? "$k_exp/$expression" : '',
                'soul_urge'   => ($k_su > 0)  ? "$k_su/$soul_urge" : '',
                'personality' => ($k_per > 0) ? "$k_per/$personality" : '',
            ],
            'master' => [
                'life_path'  => in_array($life_path,  self::MASTER) ? (string)$life_path  : '',
                'expression' => in_array($expression, self::MASTER) ? (string)$expression : '',
                'soul_urge'  => in_array($soul_urge,  self::MASTER) ? (string)$soul_urge  : '',
                'attitude'   => in_array(self::reduce($att_raw), self::MASTER) ? (string)self::reduce($att_raw) : '',
                'personality'=> in_array($personality,self::MASTER) ? (string)$personality: '',
                'maturity'   => in_array($maturity,   self::MASTER) ? (string)$maturity   : '',
                'balance'    => in_array(self::reduce($balance_sum), self::MASTER) ? (string)self::reduce($balance_sum) : '',
            ],
            'easter_eggs'     => self::calculateEasterEggs($name, $dob),
            'chart_html'      => self::buildChart($dob),
            'arrows'          => self::buildArrows($counts),
        ];
    }

    private static function calculateEasterEggs(string $name, string $dob): array {
        $easter_eggs = [];

        $dob_obj = DateTime::createFromFormat('d/m/Y', $dob);
        if (!$dob_obj) return $easter_eggs;

        $now         = new DateTime('now', new DateTimeZone('UTC'));
        $currentYear = (int) $now->format('Y');
        $birthYear   = (int) $dob_obj->format('Y');
        $birthMonth  = (int) $dob_obj->format('n');
        $birthDay    = (int) $dob_obj->format('j');

        $currentMonth = (int) $now->format('n');
        $currentDay   = (int) $now->format('j');

        $isFuture = false;
        if ($birthYear > $currentYear) {
            $isFuture = true;
        } elseif ($birthYear === $currentYear) {
            if ($birthMonth > $currentMonth) {
                $isFuture = true;
            } elseif ($birthMonth === $currentMonth && $birthDay > $currentDay) {
                $isFuture = true;
            }
        }

        if ($isFuture) {
            $maxAllowed = (clone $now)->modify('+5 years');
            if ($dob_obj > $maxAllowed) {
                $easter_eggs[] = ['type' => 'future', 'name' => $name];
                return $easter_eggs;
            }
            return $easter_eggs;
        }

        $ageByYear = $currentYear - $birthYear;

        if ($ageByYear > 120) {
            $easter_eggs[] = ['type' => 'over120', 'name' => $name];
            return $easter_eggs;
        }

        if ($ageByYear >= 100) {
            $easter_eggs[] = ['type' => 'over100', 'name' => $name];
        }

        return $easter_eggs;
    }

    private static function getReductionLines(int $raw): array {
        $lines = [];
        $current = $raw;
        while ($current > 9 && !in_array($current, self::MASTER, true)) {
            $digits = str_split((string) $current);
            $sum = array_sum($digits);
            $lines[] = "* {$current} → " . implode('+', $digits) . " = {$sum}";
            $current = $sum;
        }
        if (in_array($current, self::MASTER, true)) {
            $single = self::reduceToSingle($current);
            $lines[] = "* Result: {$current}/{$single}";
        }
        return $lines;
    }

    private static function formatVal(int $val): string {
        if (in_array($val, self::MASTER, true)) {
            return $val . '/' . self::reduceToSingle($val);
        }
        return (string)self::reduceToSingle($val);
    }

    public static function spinText(string $text, ?callable $rand = null): string {
        if (strpos($text, '{') === false) return $text;
        if ($rand === null) $rand = fn($min, $max) => mt_rand($min, $max);

        static $history = [];

        return preg_replace_callback('/\{([^{}]+)\}/', function($matches) use ($rand, &$history) {
            $parts = explode('|', $matches[1]);
            $count = count($parts);
            if ($count <= 1) return $parts[0] ?? '';

            $hash = md5($matches[1]);
            if (!isset($history[$hash])) {
                $history[$hash] = [];
            }

            $available = array_diff(range(0, $count - 1), $history[$hash]);

            if (empty($available)) {
                $history[$hash] = [];
                $available = range(0, $count - 1);
            }

            $available = array_values($available);
            $pickIndex = $available[$rand(0, count($available) - 1)];

            $history[$hash][] = $pickIndex;

            return $parts[$pickIndex];
        }, $text);
    }

    public static function getHint(string $key, string $value): string {
        $dict = self::loadData();

        if (empty($dict) || !isset($dict['hint']) || $value === '' || $value === '?' || strpos($value, 'None') !== false || strpos($value, 'Không') !== false) {
            return '';
        }

        $contextMap = [
            'life_path'      => ['prefix' => 'Life Path',             'type' => 'core'],
            'destiny'        => ['prefix' => 'Destiny',               'type' => 'core'],
            'attitude'       => ['prefix' => 'First Reaction',        'type' => 'action'],
            'birthday'       => ['prefix' => 'Natural Energy',        'type' => 'action'],
            'personality'    => ['prefix' => 'Outward Demeanor',      'type' => 'action'],
            'soul_urge'      => ['prefix' => 'Inner Desire',          'type' => 'inner'],
            'balance'        => ['prefix' => 'Way to Rebalance',      'type' => 'advice'],
            'maturity'       => ['prefix' => 'Maturity Goal',         'type' => 'advice'],
            'karmic_lessons' => ['prefix' => 'Lessons to Develop',    'type' => 'lesson'],
            'karmic_debt'    => ['prefix' => 'Karmic Debt to Resolve','type' => 'advice'],
            'pinnacles'      => ['prefix' => 'Pinnacle',              'type' => 'period'],
            'challenges'     => ['prefix' => 'Challenge',             'type' => 'negative'],
            'personal_year'  => ['prefix' => 'Year Theme',            'type' => 'period'],
            'personal_month' => ['prefix' => 'Month Focus',           'type' => 'period'],
            'personal_day'   => ['prefix' => 'Today Energy',          'type' => 'period'],
        ];

        if (!isset($contextMap[$key])) return '';

        $type   = $contextMap[$key]['type'];
        $prefix = $contextMap[$key]['prefix'];

        $noMasterKeys = ['personal_year', 'personal_month', 'personal_day'];
        $allowMaster  = !in_array($key, $noMasterKeys);
        $isShortUI    = ($key === 'pinnacles' || $key === 'challenges');

        $resolveNumber = function($raw, $allowMaster) use ($dict) {
            $numStr = preg_replace('/\D/', '', explode('/', $raw)[0]);
            if ($numStr === '') return null;
            $num = (int)$numStr;

            if (!$allowMaster) {
                while ($num > 9) $num = array_sum(str_split((string)$num));
                return $num;
            }

            while (!isset($dict['hint'][$num]) && $num > 9 && !in_array($num, [11, 22, 33, 13, 14, 16, 19, 15])) {
                $num = array_sum(str_split((string)$num));
            }
            return $num;
        };

        $getText = function($num, $type, $isShortUI) use ($dict, $key) {
            if (!isset($dict['hint'][$num])) return '';
            $h = $dict['hint'][$num];

            if ($key === 'karmic_debt') {
                $text = $h['advice'] ?? $h['negative'] ?? '';
            } elseif ($key === 'karmic_lessons') {
                $text = $h['lesson'] ?? $h['core'] ?? '';
            } else {
                $text = $h[$type] ?? $h['core'] ?? $h['action'] ?? $h['advice'] ?? $h['negative'] ?? '';
            }

            if ($text === '') return '';

            if ($isShortUI) {
                $sk = 'short_' . $type;
                if (isset($h[$sk])) {
                    $text = $h[$sk];
                } else {
                    $spunText = Numerology_Calc::spinText($text);
                    $text = trim(explode(',', $spunText)[0]);
                }
            }

            return Numerology_Calc::spinText($text);
        };

        $buildText = function($numbers, $separator, $type) use ($resolveNumber, $getText, $allowMaster, $isShortUI) {
            $results = [];
            foreach ($numbers as $n) {
                $num = $resolveNumber(trim($n), $allowMaster);
                if ($num !== null && ($txt = $getText($num, $type, $isShortUI))) {
                    $results[] = $txt;
                }
            }
            return implode($separator, $results);
        };

        if ($key === 'pinnacles' || $key === 'challenges') {
            $nums = explode('-', $value);
            $baseText = $buildText($nums, ' → ', $type);
        } elseif ($key === 'karmic_lessons' || $key === 'karmic_debt') {
            $nums = explode(',', $value);
            $baseText = $buildText($nums, '; ', $type);
        } else {
            $num = $resolveNumber($value, $allowMaster);
            $baseText = $num !== null ? $getText($num, $type, false) : '';
        }

        if ($baseText === '' || $baseText === '?') return '';

        $firstChar = mb_substr($baseText, 0, 1, 'UTF-8');
        $secondChar = mb_substr($baseText, 1, 1, 'UTF-8');
        if (mb_strtoupper($secondChar, 'UTF-8') !== $secondChar || trim($secondChar) === '') {
            $firstChar = mb_strtolower($firstChar, 'UTF-8');
        }
        return "{$prefix}: {$firstChar}" . mb_substr($baseText, 1, null, 'UTF-8');
    }

    public static function generateStaticAnalysis(array $indexes, string $name, string $dob): string {
        $data = self::loadData();
        $seed = crc32($name . $dob . gmdate('Y-m-d'));
        $seededRand = (function() use ($seed) {
            $state = $seed & 0x7FFFFFFF;
            return function(int $min, int $max) use (&$state): int {
                $state = ($state * 1103515245 + 12345) & 0x7FFFFFFF;
                $randVal = $state >> 16;
                return $min + ($randVal % ($max - $min + 1));
            };
        })();

        $getText = function($category, $number) use ($data, $seededRand) {
            return isset($data[$category][$number]) ? self::spinText($data[$category][$number], $seededRand) : '';
        };

        $safeName = esc_html($name);
        $safeDob  = esc_html($dob);

        $html = '<div class="nrgy-prose">';

        $lp = $indexes['life_path'] ?? 0;
        $des = $indexes['destiny'] ?? $indexes['expression'] ?? 0;
        $html .= '<div class="nrgy-section"><h3 class="nrgy-title">1. Life Path and Destiny</h3>';
        if ($txt = $getText('life_path', $lp)) $html .= '<p><strong>Life Path (Number ' . $lp . '):</strong> ' . $txt . '</p>';
        if ($txt = $getText('destiny', $des)) $html .= '<p><strong>Destiny (Number ' . $des . '):</strong> ' . $txt . '</p>';
        if (isset($data['pair_matrix'][$lp . '_' . $des])) {
            $html .= '<div class="nrgy-matrix"><strong>💡 Connection:</strong> ' . self::spinText($data['pair_matrix'][$lp . '_' . $des]) . '</div>';
        }
        $html .= '</div>';

        $att = $indexes['attitude'] ?? 0;
        $dobNum = $indexes['birthday'] ?? 0;
        $html .= '<div class="nrgy-section"><h3 class="nrgy-title">2. Attitude and Birthday</h3>';
        if ($txt = $getText('attitude', $att)) $html .= '<p><strong>Attitude (Number ' . $att . '):</strong> ' . $txt . '</p>';
        if ($txt = $getText('day_of_birth', $dobNum)) $html .= '<p><strong>Day of Birth (Number ' . $dobNum . '):</strong> ' . $txt . '</p>';
        $html .= '</div>';

        $soul = $indexes['soul_urge'] ?? 0;
        $per = $indexes['personality'] ?? 0;
        $rat = $indexes['rational_thought'] ?? 0;
        $html .= '<div class="nrgy-section"><h3 class="nrgy-title">3. Inner Numbers</h3>';
        if ($txt = $getText('soul', $soul)) $html .= '<p><strong>Soul Urge (Number ' . $soul . '):</strong> ' . $txt . '</p>';
        if ($txt = $getText('personality', $per)) $html .= '<p><strong>Personality (Number ' . $per . '):</strong> ' . $txt . '</p>';
        if ($rat && ($txt = $getText('rational_thought', $rat))) $html .= '<p><strong>Rational Thought (Number ' . $rat . '):</strong> ' . $txt . '</p>';
        if (isset($indexes['subconscious_self']) && ($txt = $getText('subconscious_self', $indexes['subconscious_self']))) $html .= '<p><strong>Subconscious Self (Number ' . $indexes['subconscious_self'] . '):</strong> ' . $txt . '</p>';

        if (!empty($indexes['hidden_passion'])) {
            $hpTexts = [];
            foreach ($indexes['hidden_passion'] as $hp) {
                if ($txt = $getText('hidden_passion', $hp)) $hpTexts[] = "<strong>Number $hp:</strong> " . $txt;
            }
            if (!empty($hpTexts)) $html .= '<p><strong class="hidden-passion">Hidden Passion:</strong><br>' . implode('<br>', $hpTexts) . '</p>';
        }
        $html .= '</div>';

        $mat = $indexes['maturity'] ?? 0;
        $bal = $indexes['balance'] ?? 0;
        $html .= '<div class="nrgy-section"><h3 class="nrgy-title">4. Maturity and Balance</h3>';
        if ($txt = $getText('maturity', $mat)) $html .= '<p><strong>Maturity (Number ' . $mat . '):</strong> ' . $txt . '</p>';
        if ($txt = $getText('balance', $bal)) $html .= '<p><strong>Balance (Number ' . $bal . '):</strong> ' . $txt . '</p>';
        $html .= '</div>';

        $hasMissing = !empty($indexes['karmic_lessons_arr']);
        $kd_list = [];
        if (!empty($indexes['karmic_debt_arr'])) {
            foreach ($indexes['karmic_debt_arr'] as $kd_str) {
                if (empty($kd_str)) continue;
                $k_num = (int)explode('/', $kd_str)[0];
                if (in_array($k_num, [13,14,16,19])) $kd_list[$k_num] = $k_num;
            }
        }
        if ($hasMissing || !empty($kd_list)) {
            $html .= '<div class="nrgy-section"><h3 class="nrgy-title">5. Karmic Debt and Missing Lessons</h3>';
            if (!empty($kd_list)) {
                $html .= '<p><strong>Karmic Debt for '.$safeName.':</strong></p><ul class="nrgy-list-disc">';
                foreach ($kd_list as $k) {
                    if ($txt = $getText('karmic', $k)) $html .= '<li class="nrgy-item"><strong>Number ' . $k . ':</strong> ' . $txt . '</li>';
                }
                $html .= '</ul>';
            }
            if ($hasMissing) {
                $html .= '<p><strong>Missing Lessons:</strong></p><ul class="nrgy-list-disc">';
                foreach ($indexes['karmic_lessons_arr'] as $m) {
                    if ($txt = $getText('missing', $m)) $html .= '<li class="nrgy-item"><strong>Number ' . $m . ':</strong> ' . $txt . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="nrgy-section"><h3 class="nrgy-title">6. Pinnacles and Challenges</h3>';
        $peaks = explode('-', $indexes['pinnacles'] ?? '');
        if (!empty($peaks) && count($peaks) === 4) {
            $html .= '<p><strong>4 Life Pinnacles:</strong></p><ol class="nrgy-list-dec">';
            $age_base = $indexes['pinnacle_details']['age_peak_1'] ?? 36;
            $ages = [$age_base, $age_base + 9, $age_base + 18, $age_base + 27];
            foreach ($peaks as $i => $peak) {
                if ($txt = $getText('peak', (int)$peak)) {
                    $html .= '<li class="nrgy-item"><strong>Pinnacle ' . ($i+1) . ' (Age ' . $ages[$i] . ' - Number ' . $peak . '):</strong> ' . $txt . '</li>';
                }
            }
            $html .= '</ol>';
        }

        $challenges = explode('-', $indexes['challenges'] ?? '');
        if (!empty($challenges) && count($challenges) === 4) {
            $html .= '<p><strong>4 Challenges:</strong></p><ol class="nrgy-list-dec">';
            foreach ($challenges as $i => $chal) {
                if ($txt = $getText('challenge', (int)$chal)) {
                    $html .= '<li class="nrgy-item"><strong>Challenge ' . ($i+1) . ' (Number ' . $chal . '):</strong> ' . $txt . '</li>';
                }
            }
            $html .= '</ol>';
        }

        $html .= '<div class="nrgy-section"><h3 class="nrgy-title">7. Arrow Structure</h3>';
        $arrows = $indexes['arrows'] ?? ['present' => [], 'missing' => []];

        $clean_dob = preg_replace('/[^0-9]/', '', $dob);
        $dob_digits = str_split($clean_dob);
        $counts = array_fill(1, 9, 0);
        foreach ($dob_digits as $d) {
            $v = (int)$d;
            if ($v >= 1 && $v <= 9) $counts[$v]++;
        }

        $arrow_info = $data['arrows'] ?? [];

        if (!empty($arrows['present'])) {
            $html .= '<p><strong>Natural Strengths:</strong></p><ul class="nrgy-list-disc">';
            foreach ($arrows['present'] as $arrName) {
                if (isset($arrow_info[$arrName])) {
                    $html .= '<li class="nrgy-item nrgy-mb-10">';
                    $html .= '<strong>' . $arrName . ' (' . $arrow_info[$arrName]['nums'] . ')</strong><br>';
                    $html .= $arrow_info[$arrName]['present'];
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';
        }

        if (!empty($arrows['missing'])) {
            $html .= '<p><strong>Missing Arrows:</strong></p><ul class="nrgy-list-disc">';
            foreach ($arrows['missing'] as $arrName) {
                if (isset($arrow_info[$arrName])) {
                    $html .= '<li class="nrgy-item nrgy-mb-10">';
                    $html .= '<strong>' . $arrName . ' (' . $arrow_info[$arrName]['nums'] . ')</strong><br>';
                    $html .= $arrow_info[$arrName]['missing'];
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';
        }

        if (empty($arrows['present']) && empty($arrows['missing'])) {
            $html .= '<p class="nrgy-arrow-flexible">The chart shows no clear arrows, indicating a flexible personality that adapts easily to different situations.</p>';
        }

        $html .= '</div>';

        $py = $indexes['personal_year'] ?? 0;
        $pm = $indexes['personal_month'] ?? 0;
        $pd = $indexes['personal_day'] ?? 0;

        $year = gmdate('Y');

        $html .= '<div class="nrgy-msg-card">';
        $html .= '<div class="nrgy-msg-badge">Insights for '.$year.'</div>';
        if ($txt = $getText('personal_year', $py)) $html .= '<p class="nrgy-msg-text"><strong>Personal Year ' . gmdate('Y') . ' (Number ' . $py . ') - </strong>' . $txt . '</p>';
        if ($txt = $getText('personal_month', $pm)) $html .= '<p class="nrgy-msg-text"><strong>Personal Month ' . gmdate('F Y') . ' (Number ' . $pm . ') - </strong>' . $txt . '</p>';
        $today = gmdate('F j, Y');
        if ($txt = $getText('personal_day', $pd)) $html .= '<p class="nrgy-msg-text"><strong>Personal Day Today (' . $today . ') - Number ' . $pd . ' - </strong>' . $txt . '</p>';
        $html .= '</div></div></div>';

        return $html;
    }

    public static function buildCalculation(string $name, string $dob, array $numbers): string {
        [$day, $month, $year] = self::parseDob($dob);
        $clean = self::normalizeName($name);
        $clean = esc_html($clean);
        $currentYear = (int) gmdate('Y');

        $lp_disp  = self::formatVal($numbers['life_path']);
        $exp_disp = self::formatVal($numbers['expression']);
        $su_disp  = self::formatVal($numbers['soul_urge']);

        $letters = str_split(str_replace(' ', '', $clean));
        $suParts = []; $suSum = 0; $expParts = []; $expSum = 0; $perParts = []; $perSum = 0;

        foreach ($letters as $l) {
            $val = self::LETTER_MAP[$l] ?? 0;
            if ($val === 0) continue;
            $expParts[] = "{$l}({$val})"; $expSum += $val;
            if (in_array($l, self::VOWELS, true)) {
                $suParts[] = "{$l}({$val})"; $suSum += $val;
            } else {
                $perParts[] = "{$l}({$val})"; $perSum += $val;
            }
        }

        $lines = ["**Normalized Name:** {$clean}", ""];

        // ① Life Path Number
        $lpRaw = array_sum(str_split($day . $month . $year));
        $lines[] = "**① Life Path Number {$lp_disp}**";
        $lines[] = "* Birth date: {$day}/{$month}/{$year} → " . implode('+', str_split($day . $month . $year)) . " = {$lpRaw}";
        $lines = array_merge($lines, self::getReductionLines($lpRaw));
        $lines[] = "";

        // ② Attitude Number
        $attTotal = $day + $month;
        $lines[] = "**② Attitude Number ({$numbers['attitude']})**";
        $lines[] = "* Day + Month: {$day} + {$month} = {$attTotal}";
        $lines = array_merge($lines, self::getReductionLines($attTotal));
        $lines[] = "";

        // ③ Birthday Number
        $lines[] = "**③ Birthday Number ({$numbers['birthday']})**";
        $lines[] = "* Keep birth day: {$day}";
        $lines[] = "";

        // ④ Soul Urge Number
        $lines[] = "**④ Soul Urge Number {$su_disp}**";
        $lines[] = "* Vowels: " . (empty($suParts) ? '(none)' : implode(' + ', $suParts) . " = {$suSum}");
        $lines = array_merge($lines, self::getReductionLines($suSum));
        $lines[] = "";

        // ⑤ Expression / Destiny Number
        $lines[] = "**⑤ Expression / Destiny Number {$exp_disp}**";
        $lines[] = "* All letters: " . implode(' + ', $expParts) . " = {$expSum}";
        $lines = array_merge($lines, self::getReductionLines($expSum));
        $lines[] = "";

        // ⑥ Personality Number
        $lines[] = "**⑥ Personality Number ".self::formatVal($numbers['personality'])."** — consonants total";
        $lines[] = "* Consonants: " . (empty($perParts) ? '(none)' : implode(' + ', $perParts) . " = {$perSum}");
        $lines = array_merge($lines, self::getReductionLines($perSum));
        $lines[] = "";

        // ⑦ Maturity Number
        $matRaw = $numbers['life_path'] + $numbers['expression'];
        $lines[] = "**⑦ Maturity Number ".self::formatVal($numbers['maturity'])."**";
        $lines[] = "* Life Path({$numbers['life_path']}) + Expression({$numbers['expression']}) = {$matRaw}";
        $lines = array_merge($lines, self::getReductionLines($matRaw));
        $lines[] = "";

        // ⑧ Personal Year
        $py_disp = self::formatVal($numbers['personal_year']);
        $pyRaw = array_sum(str_split($day . $month . $currentYear));
        $lines[] = "**⑧ Personal Year {$currentYear} ({$py_disp})**";
        $lines[] = "* {$day}/{$month}/{$currentYear} → " . implode('+', str_split($day . $month . $currentYear)) . " = {$pyRaw}";
        $cur = $pyRaw;
        while ($cur > 9) {
            $digits = str_split((string)$cur);
            $sum = array_sum($digits);
            $lines[] = "* {$cur} → " . implode('+', $digits) . " = {$sum}";
            $cur = $sum;
        }
        $lines[] = "";

        // ⑨ Four Pinnacles
        [$rDay, $rMonth, $rYear, $p1, $p2, $p3, $p4] = $numbers['pinnacle_details']['calc_data'];
        $lines[] = "**⑨ Four Pinnacles ({$numbers['pinnacles']})**";
        $lines[] = "* Reduce: Day {$day}→{$rDay}, Month {$month}→{$rMonth}, Year {$year}→{$rYear}";
        $lines[] = "* Pinnacle 1: Month({$rMonth}) + Day({$rDay}) = {$p1}";
        $lines[] = "* Pinnacle 2: Day({$rDay}) + Year({$rYear}) = {$p2}";
        $lines[] = "* Pinnacle 3: Pinnacle 1 + Pinnacle 2 = {$p3}";
        $lines[] = "* Pinnacle 4: Month({$rMonth}) + Year({$rYear}) = {$p4}";
        $lines[] = "";

        // ⑩ Four Challenges
        $cData = explode('-', $numbers['challenges']);
        $lines[] = "**⑩ Four Challenges ({$numbers['challenges']})**";
        $lines[] = "* Challenge 1: |Month({$rMonth}) - Day({$rDay})| = {$cData[0]}";
        $lines[] = "* Challenge 2: |Day({$rDay}) - Year({$rYear})| = {$cData[1]}";
        $lines[] = "* Challenge 3: |C1 - C2| = {$cData[2]}";
        $lines[] = "* Challenge 4: |Month({$rMonth}) - Year({$rYear})| = {$cData[3]}";
        $lines[] = "";

        // ⑪ Balance Number
        $bal = $numbers['balance'];
        $lines[] = "**⑪ Balance Number ({$bal})**";
        $words = explode(' ', $clean);
        $firstLetters = [];
        foreach ($words as $w) { if (!empty($w)) $firstLetters[] = $w[0]; }
        $balParts = [];
        foreach ($firstLetters as $l) {
            $v = self::LETTER_MAP[$l] ?? 0;
            if ($v > 0) $balParts[] = "{$l}({$v})";
        }
        $lines[] = "* First letters: " . (empty($balParts) ? '(none)' : implode(' + ', $balParts));
        $lines[] = "";

        // ⑫ Hidden Passion & Subconscious Self
        $lines[] = "**⑫ Hidden Passion & Subconscious Self**";
        if (!empty($numbers['hidden_passion'])) {
            $lines[] = "* Passion: " . implode(', ', $numbers['hidden_passion']) . " (appears most frequently)";
        } else {
            $lines[] = "* Passion: Unclear";
        }
        $lines[] = "* Subconscious Self: {$numbers['subconscious_self']} (9 - number of karmic lessons)";
        $lines[] = "";

        // ⑬ Karmic Debt & Karmic Lessons
        $kDebt = $numbers['karmic_debt'];
        $kLess = $numbers['karmic_lessons'];
        $lines[] = "**⑬ Karmic Debt & Lessons**";
        $lines[] = "* Karmic Debt: {$kDebt}";
        if (!empty($numbers['karmic_debt_arr'])) {
            foreach ($numbers['karmic_debt_arr'] as $key => $val) {
                if (!empty($val)) {
                    $lines[] = "  - {$key}: {$val}";
                }
            }
        }
        $lines[] = "* Missing Lessons: {$kLess}";

        return self::calcToHtml(implode("\n", $lines));
    }

    private static function calculatePinnacles(int $day, int $month, int $year, int $life_path): array {
        $rDay = self::reduce($day); $rMonth = self::reduce($month);
        $rYear = self::reduce(array_sum(str_split((string)$year)));
        $p1 = self::reduce($rMonth + $rDay); $p2 = self::reduce($rDay + $rYear);
        $p3 = self::reduce($p1 + $p2); $p4 = self::reduce($rMonth + $rYear);
        return [
            'peaks' => [$p1, $p2, $p3, $p4],
            'age_peak_1' => (36 - self::reduceToSingle($life_path)),
            'calc_data' => [$rDay, $rMonth, $rYear, $p1, $p2, $p3, $p4]
        ];
    }

    private static function calculateChallenges(int $day, int $month, int $year): array {
        $rDay = self::reduce($day); $rMonth = self::reduce($month);
        $rYear = self::reduce(array_sum(str_split((string)$year)));
        return [abs($rMonth - $rDay), abs($rDay - $rYear), abs(abs($rMonth - $rDay) - abs($rDay - $rYear)), abs($rMonth - $rYear)];
    }

    private static function parseDob(string $dob): array {
        if (!preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', trim($dob), $m)) throw new InvalidArgumentException("Invalid date of birth.");
        return [(int)$m[1], (int)$m[2], (int)$m[3]];
    }

    private static function validateDate($d, $m, $y): void {
        if (!checkdate($m, $d, $y)) {
            throw new InvalidArgumentException("Date {$y}-{$m}-{$d} does not exist.");
        }
    }

    private static function validateName($n): void {
        if (strlen(preg_replace('/[^A-Z]/', '', $n)) < 2) {
            throw new InvalidArgumentException("Name is too short.");
        }
    }

    private static function normalizeName(string $name): string {
        $str = strtolower($name);
        return strtoupper(preg_replace('/\s+/', ' ', trim(preg_replace('/[^a-z ]/i', '', $str))));
    }

    private static function reduce(int $num): int {
        while ($num > 9 && !in_array($num, self::MASTER, true)) { $num = array_sum(str_split((string) $num)); }
        return $num;
    }

    private static function reduceToSingle(int $num): int {
        while ($num > 9) { $num = array_sum(str_split((string) $num)); }
        return $num;
    }

    public static function calcToHtml(string $raw): string {
        $raw = str_replace('->', '→', preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $raw));
        $html = ''; $groupLines = [];
        foreach (explode("\n", $raw) as $line) {
            $trimmed = rtrim($line);
            if ($trimmed === '') { if (!empty($groupLines)) { $html .= '<div class="nrgy-calc-group">' . implode('', $groupLines) . '</div>'; $groupLines = []; } continue; }
            $stripped = ltrim($trimmed);
            $indent = strlen($trimmed) - strlen($stripped);
            if (preg_match('/^\*\s+(.+)$/', $stripped, $m)) {
                $cls = $indent >= 2 ? ' class="nrgy-calc-sub"' : '';
                $groupLines[] = '<p' . $cls . '>▸ ' . $m[1] . '</p>';
            } else { $groupLines[] = '<p>' . $stripped . '</p>'; }
        }
        if (!empty($groupLines)) $html .= '<div class="nrgy-calc-group">' . implode('', $groupLines) . '</div>';
        return $html;
    }

    public static function buildChart(string $dob): string {
        try {
            [$day, $month, $year] = self::parseDob($dob);
        } catch (\Exception $e) {
            return '';
        }

        $digits = str_split($day . $month . $year);
        $counts = array_fill(1, 9, 0);
        foreach ($digits as $d) {
            $v = (int)$d;
            if ($v >= 1 && $v <= 9) $counts[$v]++;
        }

        $labels = [
            3 => 'Intellect', 6 => 'Creativity', 9 => 'Idealism',
            2 => 'Intuition', 5 => 'Emotion', 8 => 'Willpower',
            1 => 'Leadership', 4 => 'Practicality', 7 => 'Philosophy'
        ];

        $html = '<div class="nrgy-chart-container">';
        $html .= '<div class="nrgy-grid-3x3">';

        $positions = [3, 6, 9, 2, 5, 8, 1, 4, 7];
        foreach ($positions as $num) {
            $c = $counts[$num];
            $cls = $c > 0 ? 'is-filled' : 'is-empty';
            $html .= "<div class='nrgy-cell {$cls}'>";
            $html .= "<span class='nrgy-cell-num'>{$num}</span>";
            if ($c > 0) {
                $displayString = str_repeat((string)$num, $c);
                $html .= "<span class='nrgy-cell-count'>{$displayString}</span>";
                $html .= "<span class='nrgy-cell-times'>×{$c}</span>";
            }
            $html .= "<span class='nrgy-cell-label'>{$labels[$num]}</span>";
            $html .= "</div>";
        }

        $html .= '<svg class="nrgy-chart-svg" width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">';
        $html .= '<g class="nrgy-chart-base-lines">';
        $html .= '<line x1="40" y1="40" x2="260" y2="40" />';
        $html .= '<line x1="40" y1="150" x2="260" y2="150" />';
        $html .= '<line x1="40" y1="260" x2="260" y2="260" />';
        $html .= '<line x1="40" y1="40" x2="40" y2="260" />';
        $html .= '<line x1="150" y1="40" x2="150" y2="260" />';
        $html .= '<line x1="260" y1="40" x2="260" y2="260" />';
        $html .= '<line x1="40" y1="260" x2="260" y2="40" />';
        $html .= '<line x1="40" y1="40" x2="260" y2="260" />';
        $html .= '</g>';

        $arrows = [
            'r1' => [$counts[3], $counts[6], $counts[9]],
            'r2' => [$counts[2], $counts[5], $counts[8]],
            'r3' => [$counts[1], $counts[4], $counts[7]],
            'c1' => [$counts[1], $counts[2], $counts[3]],
            'c2' => [$counts[4], $counts[5], $counts[6]],
            'c3' => [$counts[7], $counts[8], $counts[9]],
            'd1' => [$counts[1], $counts[5], $counts[9]],
            'd2' => [$counts[3], $counts[5], $counts[7]],
        ];

        $html .= '<defs><marker id="nrgy-arrowhead" class="nrgy-arrowhead" markerWidth="10" markerHeight="7" refX="8" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" /></marker><marker id="nrgy-arrowhead-missing" class="nrgy-arrowhead-missing" markerWidth="10" markerHeight="7" refX="8" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" /></marker></defs>';

        $drawArrow = function($x1, $y1, $x2, $y2, $missing = false) {
            if ($missing) {
                return "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" class=\"nrgy-arrow-missing\" />";
            }
            return "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" />";
        };

        // Present arrows (solid)
        $html .= '<g class="nrgy-chart-arrows">';
        if (min($arrows['r1']) > 0) $html .= $drawArrow(40, 40, 260, 40);
        if (min($arrows['r2']) > 0) $html .= $drawArrow(40, 150, 260, 150);
        if (min($arrows['r3']) > 0) $html .= $drawArrow(40, 260, 260, 260);
        if (min($arrows['c1']) > 0) $html .= $drawArrow(40, 260, 40, 40);
        if (min($arrows['c2']) > 0) $html .= $drawArrow(150, 260, 150, 40);
        if (min($arrows['c3']) > 0) $html .= $drawArrow(260, 260, 260, 40);
        if (min($arrows['d1']) > 0) $html .= $drawArrow(40, 260, 260, 40);
        if (min($arrows['d2']) > 0) $html .= $drawArrow(40, 40, 260, 260);
        $html .= '</g>';

        // Missing arrows (dashed, all 3 cells are zero)
        $html .= '<g class="nrgy-chart-arrows-missing">';
        if (max($arrows['r1']) === 0) $html .= $drawArrow(40, 40, 260, 40, true);
        if (max($arrows['r2']) === 0) $html .= $drawArrow(40, 150, 260, 150, true);
        if (max($arrows['r3']) === 0) $html .= $drawArrow(40, 260, 260, 260, true);
        if (max($arrows['c1']) === 0) $html .= $drawArrow(40, 260, 40, 40, true);
        if (max($arrows['c2']) === 0) $html .= $drawArrow(150, 260, 150, 40, true);
        if (max($arrows['c3']) === 0) $html .= $drawArrow(260, 260, 260, 40, true);
        if (max($arrows['d1']) === 0) $html .= $drawArrow(40, 260, 260, 40, true);
        if (max($arrows['d2']) === 0) $html .= $drawArrow(40, 40, 260, 260, true);
        $html .= '</g>';
        $html .= '</svg>';
        $html .= '</div></div>';

        $html .= '<div class="nrgy-chart-legend">';
        $html .= '<span class="nrgy-legend-item"><span class="nrgy-legend-line nrgy-legend-solid"></span> Full Arrow</span>';
        $html .= '<span class="nrgy-legend-item"><span class="nrgy-legend-line nrgy-legend-dashed"></span> Missing Arrow (all 3 cells empty)</span>';
        $html .= '</div>';

        return $html;
    }

    private static function buildArrows(array $counts): array {
        $defs = [
            ['key'=>'intellect',    'name'=>'Intellect',     'nums'=>[3,6,9]],
            ['key'=>'emotion',      'name'=>'Emotion',       'nums'=>[2,5,8]],
            ['key'=>'action',       'name'=>'Action',        'nums'=>[1,4,7]],
            ['key'=>'planning',     'name'=>'Planning',      'nums'=>[3,2,1]],
            ['key'=>'willpower',    'name'=>'Willpower',     'nums'=>[6,5,4]],
            ['key'=>'result',       'name'=>'Result',        'nums'=>[9,8,7]],
            ['key'=>'determination','name'=>'Determination', 'nums'=>[3,5,7]],
            ['key'=>'sensitivity',  'name'=>'Sensitivity',   'nums'=>[1,5,9]],
        ];

        $present = [];
        $missing = [];
        $partial = []; // incomplete arrow: 1-2 cells have numbers, the rest are empty

        foreach ($defs as $d) {
            $filledCount = 0;
            $missingNums = [];

            foreach ($d['nums'] as $n) {
                if ($counts[$n] > 0) {
                    $filledCount++;
                } else {
                    $missingNums[] = $n;
                }
            }

            if ($filledCount === 3) {
                $present[] = $d['name'];
            } elseif ($filledCount === 0) {
                $missing[] = $d['name'];
            } else {
                // partial: store with info about missing cells
                $partial[] = [
                    'name'        => $d['name'],
                    'filled'      => $filledCount,
                    'missing_nums'=> $missingNums,
                ];
            }
        }

        return [
            'present' => $present,
            'missing' => $missing,
            'partial' => $partial,
            'count'   => count($present),
        ];
    }
}