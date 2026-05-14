<?php
if (!defined('ABSPATH')) exit;

class Numerology_Render {

    private const INDEX_COLORS = [
            'life_path'      => ['gradient' => 'linear-gradient(135deg,#8b5cf6,#3b82f6)', 'glow' => 'rgba(139,92,246,.35)'],
            'destiny'        => ['gradient' => 'linear-gradient(135deg,#a855f7,#ec4899)', 'glow' => 'rgba(168,85,247,.35)'],
            'attitude'       => ['gradient' => 'linear-gradient(135deg,#ec4899,#f43f5e)', 'glow' => 'rgba(236,72,153,.35)'],
            'birthday'       => ['gradient' => 'linear-gradient(135deg,#f97316,#eab308)', 'glow' => 'rgba(249,115,22,.35)'],
            'soul_urge'      => ['gradient' => 'linear-gradient(135deg,#10b981,#06b6d4)', 'glow' => 'rgba(16,185,129,.35)'],
            'personality'    => ['gradient' => 'linear-gradient(135deg,#06b6d4,#3b82f6)', 'glow' => 'rgba(6,182,212,.35)'],
            'maturity'       => ['gradient' => 'linear-gradient(135deg,#84cc16,#10b981)', 'glow' => 'rgba(132,204,22,.35)'],
            'balance'        => ['gradient' => 'linear-gradient(135deg,#14b8a6,#0ea5e9)', 'glow' => 'rgba(20,184,166,.35)'],
            'karmic_lessons' => ['gradient' => 'linear-gradient(135deg,#ef4444,#f97316)', 'glow' => 'rgba(239,68,68,.35)'],
            'karmic_debt'    => ['gradient' => 'linear-gradient(135deg,#b91c1c,#dc2626)', 'glow' => 'rgba(185,28,28,.35)'],
            'pinnacles'      => ['gradient' => 'linear-gradient(135deg,#eab308,#f59e0b)', 'glow' => 'rgba(234,179,8,.35)'],
            'challenges'     => ['gradient' => 'linear-gradient(135deg,#64748b,#94a3b8)', 'glow' => 'rgba(100,116,139,.35)'],
            'personal_year'  => ['gradient' => 'linear-gradient(135deg,#f59e0b,#ef4444)', 'glow' => 'rgba(245,158,11,.35)'],
            'personal_month' => ['gradient' => 'linear-gradient(135deg,#0ea5e9,#6366f1)', 'glow' => 'rgba(14,165,233,.35)'],
            'personal_day'   => ['gradient' => 'linear-gradient(135deg,#3b82f6,#8b5cf6)', 'glow' => 'rgba(59,130,246,.35)'],
    ];

    private const INDEX_LABELS = [
            'life_path'      => 'Life Path',
            'destiny'        => 'Destiny',
            'attitude'       => 'Attitude',
            'birthday'       => 'Birth Day',
            'soul_urge'      => 'Soul Urge',
            'personality'    => 'Personality',
            'maturity'       => 'Maturity',
            'balance'        => 'Balance',
            'karmic_lessons' => 'Missing Lessons',
            'karmic_debt'    => 'Karmic Debt',
            'pinnacles'      => '4 Pinnacles',
            'challenges'     => '4 Challenges',
            'personal_year'  => 'Personal Year',
            'personal_month' => 'Personal Month',
            'personal_day'   => 'Personal Day',
    ];

    public static function color(string $key): array {
        return self::INDEX_COLORS[$key] ?? self::INDEX_COLORS['life_path'];
    }

    private static function renderBlockOnly(string $fatalMessage, string $messageType, string $name, string $dob): string {
        $lines = [
                ['type' => 'greeting', 'text' => ''],
                ['type' => 'divider',  'text' => ''],
                ['type' => $messageType, 'text' => $fatalMessage],
        ];

        ob_start(); ?>
        <div class="nrgy-chat-wrap">
            <div class="nrgy-chat-bubble">
                <div class="nrgy-chat-body" id="nrgy-chat-body"
                     data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>"
                     data-name="<?= esc_attr($name) ?>"
                     data-dob="<?= esc_attr($dob) ?>">
                    <span class="nrgy-cursor">|</span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function result(array $indexes, array $tabs, string $name, string $dob): string {
        $blocks = $indexes['easter_eggs'] ?? [];

        if (!empty($blocks)) {
            foreach ($blocks as $block) {
                $blockName = esc_html($block['name'] ?? '');
                if ($block['type'] === 'future') {
                    $fatalMessage = "<strong>{$blockName}</strong> - Come back after you're born.";
                    return self::renderBlockOnly($fatalMessage, 'block', $name, $dob);
                }
                if ($block['type'] === 'over120') {
                    $fatalMessage = "👑 <strong>{$blockName}</strong> - Age of fulfillment. Your destiny has already been proven by reality!";
                    return self::renderBlockOnly($fatalMessage, 'easter', $name, $dob);
                }
            }
        }

        $labels       = self::INDEX_LABELS;
        $labels['personal_year']  .= ' ' . gmdate('Y');
        $labels['personal_month'] .= ' ' . gmdate('F Y');
        $labels['personal_day']   .= ' ' . gmdate('F j, Y');

        $lines = [];
        if (!empty($blocks)) {
            foreach ($blocks as $egg) {
                if ($egg['type'] === 'over100') {
                    $eggName = esc_html($egg['name'] ?? '');
                    $lines[] = ['type' => 'easter', 'text' => '👑 ' . $eggName . ' — Age of fulfillment. Your destiny has already been proven by reality!'];
                    $lines[] = ['type' => 'divider', 'text' => ''];
                }
            }
        }

        $lines[] = ['type' => 'greeting', 'text' => 'Numerology results for ' . esc_html($name) . ':'];
        $lines[] = ['type' => 'divider', 'text' => ''];

        if (isset($indexes['expression'])) { $indexes['destiny'] = $indexes['expression']; }

        foreach ($labels as $key => $label) {
            $val = $indexes[$key] ?? '?';
            $displayVal = $val;
            if (is_numeric($val) && (int)$val > 9 && !in_array($key, ['personal_year', 'personal_month', 'personal_day'])) {
                $num = (int)$val;
                $root = $num;
                while ($root > 9) $root = array_sum(str_split((string)$root));
                if ($num !== $root) $displayVal = "$num/$root";
            }

            $hintStr = Numerology_Calc::getHint($key, (string)$val);
            $lines[] = [
                    'type'  => 'index',
                    'key'   => $key,
                    'label' => $label,
                    'value' => $displayVal,
                    'hint'  => $hintStr ? '- ' . $hintStr : ''
            ];
        }

        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines[] = ['type' => 'closing', 'text' => 'Detailed analysis...'];

        ob_start(); ?>
        <div class="nrgy-analysis-wrap" id="nrgy-analysis-wrap">
            <div class="nrgy-tabs" role="tablist">
                <button class="nrgy-tab active" data-tab="detail" role="tab">Details</button>
                <button class="nrgy-tab" data-tab="calculation" role="tab">Calculation</button>
            </div>
            <div class="nrgy-tab-pane active" id="nrgy-tab-detail">
                <div id="nrgy-step-1-wrap">
                    <div class="nrgy-step1-grid">
                        <div class="nrgy-chat-wrap">
                            <div class="nrgy-chat-bubble">
                                <div class="nrgy-chat-body" id="nrgy-chat-body"
                                     data-name="<?= esc_attr($name) ?>" data-dob="<?= esc_attr($dob) ?>"
                                     data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>">
                                    <span class="nrgy-cursor">|</span>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($indexes['chart_html'])) : ?>
                            <div class="nrgy-chart-col">
                                <?= $indexes['chart_html'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div id="nrgy-action-next" class="nrgy-action-next">
                        <button type="button" id="btn-show-analysis" class="nrgy-btn-submit">
                            <span class="nrgy-btn-text">✦ More Details</span>
                            <span class="nrgy-btn-loading">
                                <span class="nrgy-spinner"></span> Analyzing...
                            </span>
                        </button>
                        <div id="nrgy-error-api" class="nrgy-error nrgy-error-api"></div>
                    </div>
                </div>
                <div id="nrgy-step-2-wrap" class="nrgy-step-2-wrap">
                    <div id="nrgy-analysis-display"></div>
                </div>
            </div>
            <div class="nrgy-tab-pane" id="nrgy-tab-calculation">
                <?= wp_kses_post($tabs['calculation'] ?? '') ?>
            </div>
        </div>

        <div class="nrgy-btn-right">
            <span class="nrgy-btn-reset">← Go Back</span>
        </div>

        <p class="nrgy-disclaimer" id="nrgy-disclaimer">
            ✦ This is a reference result based on the Pythagorean numerology system. All actions and future directions depend on your wise choices and personal efforts.
        </p>

        <div class="nrgy-action-footer" id="nrgy-action-footer">
            <button type="button" id="nrgy-btn-comment" class="nrgy-btn-comment">✦ Discussion</button>
        </div>
        <?php
        return ob_get_clean();
    }
}