<?php
if (!defined('ABSPATH')) exit;

class MBTI_Calc {
    private const VALID_AXES = ['EI', 'SN', 'TF', 'JP'];

    public static function calculate(array $answers): array {
        $questions = MBTI_Data::getQuestions();

        $scores = ['E' => 0, 'I' => 0, 'S' => 0, 'N' => 0, 'T' => 0, 'F' => 0, 'J' => 0, 'P' => 0];

        foreach ($questions as $q) {
            if (!isset($answers[$q['id']])) continue;
            $val        = max(1, min(5, (int) $answers[$q['id']]));
            $val_mapped = $val - 1;  // 0–4

            if (!in_array($q['axis'], self::VALID_AXES, true)) continue;

            [$pos, $neg] = str_split($q['axis']);

            if ($q['dir'] > 0) {
                $scores[$pos] += $val_mapped;
                $scores[$neg] += (4 - $val_mapped);
            } else {
                $scores[$pos] += (4 - $val_mapped);
                $scores[$neg] += $val_mapped;
            }
        }

        $type = ($scores['E'] >= $scores['I'] ? 'E' : 'I')
              . ($scores['S'] >= $scores['N'] ? 'S' : 'N')
              . ($scores['T'] >= $scores['F'] ? 'T' : 'F')
              . ($scores['J'] >= $scores['P'] ? 'J' : 'P');

        $pct        = [];
        $borderline = [];
        $axes = ['EI' => ['E','I'], 'SN' => ['S','N'], 'TF' => ['T','F'], 'JP' => ['J','P']];

        foreach ($axes as $axis => [$p, $n]) {
            $total = $scores[$p] + $scores[$n];
            $pPos  = $total > 0 ? round($scores[$p] / $total * 100) : 50;
            $pct[$axis] = [$p => $pPos, $n => 100 - $pPos];

            if (abs($pPos - 50) <= 5) $borderline[] = $axis;
        }

        $profiles = MBTI_Data::getProfiles();
        $profile  = $profiles[$type] ?? [
            'title'     => 'The Enigma',
            'overview' => 'No information available.',
            'career'   => 'No information available.',
            'love'     => 'No information available.',
        ];

        return [
            'type'       => $type,
            'scores'     => $scores,
            'pct'        => $pct,
            'borderline' => $borderline,
            'profile'    => $profile,
        ];
    }

    public static function parseResponse(string $raw): array {
        $tabs = ['mbti_result' => ''];

        if (preg_match('/\[TAB_RESULT\](.*?)\[\/TAB_RESULT\]/is', $raw, $m)) {
            $tabs['mbti_result'] = self::markdownToHtml(trim($m[1]));
        }

        return ['tabs' => $tabs];
    }

    public static function markdownToHtml(string $md): string {
        $md = preg_replace('/^[\-]{3,}$/m', '', $md);
        $md = preg_replace('/^\*{3,}$/m', '', $md);
        $md = preg_replace('/^_{3,}$/m', '',  $md);

        if (!class_exists('Parsedown')) {
            require_once MBTI_PLUGIN_DIR . 'lib/Parsedown.php';
        }

        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);

        return $Parsedown->text($md);
    }
}
