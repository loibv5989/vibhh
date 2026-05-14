<?php
if (!defined('ABSPATH')) exit;

class BbZodiac_Render {

    public static function buildStaticAnalyze(array $sign): string {
        if (empty($sign['personality'])) return '';

        $p = $sign['personality'];
        $decanNum = $sign['decan'] ?? 1;

        $horoscopeLife = BbZodiac_Calc::spinText($sign['horoscope_life'] ?? '');
        $core = BbZodiac_Calc::spinText($p['core'] ?? '');
        $love = BbZodiac_Calc::spinText($p['love'] ?? '');
        $career = BbZodiac_Calc::spinText($p['career'] ?? '');
        $shadow = BbZodiac_Calc::spinText($p['shadow'] ?? '');
        $decanOverlay = BbZodiac_Calc::spinText($p['decan_overlays'][$decanNum] ?? '');

        $layerElem = BbZodiac_Calc::spinText($p['layers']['element'] ?? '');
        $layerPlan = BbZodiac_Calc::spinText($p['layers']['planet'] ?? '');
        $layerQual = BbZodiac_Calc::spinText($p['layers']['quality'] ?? '');
        $layerPol  = BbZodiac_Calc::spinText($p['layers']['polarity'] ?? '');

        $strHtml = '';
        if(!empty($p['strengths'])) foreach($p['strengths'] as $s) $strHtml .= '<li>' . BbZodiac_Calc::spinText($s) . '</li>';

        $weakHtml = '';
        if(!empty($p['weaknesses'])) foreach($p['weaknesses'] as $w) $weakHtml .= '<li>' . BbZodiac_Calc::spinText($w) . '</li>';

        $html = '<div class="zdc-static-content">';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Direction & Core</h4>';
        $html .= '<p>' . $horoscopeLife . ' ' . $core . '</p>';
        if ($decanOverlay) $html .= '<p><strong>Decan ' . $decanNum . ' influence:</strong> ' . $decanOverlay . '</p>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-grid">';
        $html .= '<div class="zdc-st-col"><h4 class="zdc-st-title">Strengths</h4><ul class="zdc-st-list">' . $strHtml . '</ul></div>';
        $html .= '<div class="zdc-st-col"><h4 class="zdc-st-title">Weaknesses</h4><ul class="zdc-st-list">' . $weakHtml . '</ul></div>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Love & Career</h4>';
        $html .= '<p><strong>Love:</strong> ' . $love . '</p>';
        $html .= '<p><strong>Career:</strong> ' . $career . '</p>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Energy Layers</h4>';
        $html .= '<ul class="zdc-st-list">';
        if($layerElem) $html .= '<li>' . $layerElem . '</li>';
        if($layerPlan) $html .= '<li>' . $layerPlan . '</li>';
        if($layerQual) $html .= '<li>' . $layerQual . '</li>';
        if($layerPol)  $html .= '<li>' . $layerPol . '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Shadow Side</h4>';
        $html .= '<p>' . $shadow . '</p>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    public static function indexes(array $sign): string {
        $signId = $sign['id']     ?? 'aries';
        $name   = $sign['name']   ?? '';
        $symbol = $sign['symbol'] ?? '♈';
        $dob    = $sign['dob']    ?? '';
        $easter_eggs = $sign['easter_eggs'] ?? [];

        $lines = [];
        $lines[] = ['type' => 'greeting', 'text' => 'Hello'];
        $lines[] = ['type' => 'intro',    'text' => 'Here is your personality map:'];

        foreach ($easter_eggs as $egg) {
            if ($egg['type'] === 'under18') {
                $lines[] = ['type' => 'easter', 'text' => 'You are very young (' . $egg['age'] . ' years old) — a bright future awaits ahead!'];
            }
        }

        $lines[] = ['type' => 'divider',  'text' => ''];
        $lines[] = ['type' => 'section', 'text' => ''];

        $lines[] = ['type' => 'index', 'key' => 'sign_name', 'label' => 'Zodiac Sign', 'value' => $name . ' ' . $symbol, 'hint' => '— ' . ($sign['keywords'] ?? '')];
        $lines[] = ['type' => 'index', 'key' => 'element', 'label' => 'Element', 'value' => $sign['element'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'planet', 'label' => 'Planet', 'value' => $sign['planet'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'quality', 'label' => 'Quality', 'value' => $sign['quality'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'polarity', 'label' => 'Polarity', 'value' => $sign['polarity'] ?? '?', 'hint' => ''];

        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines[] = ['type' => 'section', 'text' => 'Decan'];

        $lines[] = ['type' => 'index', 'key' => 'decan', 'label' => 'Decan', 'value' => 'Decan ' . ($sign['decan'] ?? '?'), 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'sub_ruler', 'label' => 'Sub-Ruler', 'value' => $sign['sub_ruler'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'decan_vibe', 'label' => 'Vibe', 'value' => $sign['decan_vibe'] ?? '?', 'hint' => ''];

        $compat = $sign['compatibility'] ?? [];
        if (!empty($compat)) {
            $lines[] = ['type' => 'divider', 'text' => ''];
            $lines[] = ['type' => 'section', 'text' => 'Compatibility'];

            if (!empty($compat['best_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_best', 'label' => 'Best Match', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['best_match'])), 'hint' => ''];
            }
            if (!empty($compat['karmic_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_karmic', 'label' => 'Karmic Match', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['karmic_match'])), 'hint' => ''];
            }
            if (!empty($compat['worst_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_worst', 'label' => 'Challenging', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['worst_match'])), 'hint' => ''];
            }
        }

        if (!empty($sign['has_cusp'])) {
            $lines[] = ['type' => 'divider', 'text' => ''];
            $lines[] = ['type' => 'section', 'text' => 'Cusp'];
            $lines[] = ['type' => 'index', 'key' => 'cusp_name', 'label' => 'Cusp Name', 'value' => $sign['cusp_name'] ?? '?', 'hint' => ''];
            $lines[] = ['type' => 'index', 'key' => 'cusp_blend', 'label' => 'Blend', 'value' => $sign['cusp_blend'] ?? '?', 'hint' => ''];
            $lines[] = ['type' => 'index', 'key' => 'cusp_vibe', 'label' => 'Energy', 'value' => $sign['cusp_vibe'] ?? '?', 'hint' => ''];
        }

        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines[] = ['type' => 'closing', 'text' => 'Analyzing...'];

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="zdc-chi-tiet" role="tab">Details</button>
                <button class="ftn-tab" data-tab="zdc-cach-tinh" role="tab">How It Works</button>
            </div>
            <div class="ftn-tab-pane active" id="ftn-tab-zdc-chi-tiet">
                <div class="ftn-chat-wrap">
                    <div class="ftn-chat-bubble">
                        <div class="ftn-chat-body" id="zdc-chat-body"
                             data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>"
                             data-sign="<?= esc_attr($signId) ?>" data-dob="<?= esc_attr($dob) ?>">
                            <span class="ftn-cursor">|</span>
                        </div>
                    </div>
                </div>

                <div id="zdc-tab-chi-tiet-html" class="zdc-html zdc-tab-detail-container" style="display:none;">
                    <?= self::buildStaticAnalyze($sign) ?>
                </div>
            </div>
            <div class="ftn-tab-pane" id="ftn-tab-zdc-cach-tinh"><?= self::calcBreakdown($sign) ?></div>
        </div>
        <div class="zdc-action-footer" id="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-btn-reset">← Back</span>
            <button type="button" id="zdc-btn-comment" class="zdc-btn-comment">Discuss</button>
        </div>
        <p class="zdc-disclaimer" id="zdc-disclaimer" style="display:none;">
            ✦ This is a reference result based on astrological systems. All actions and future directions depend on your wise choices and personal efforts.
        </p>
        <?php
        return ob_get_clean();
    }

    public static function loveResult(array $love): string {
        $blocks = $love['blocks'] ?? [];
        $fatalMessage = '';
        $hasSameName = false;

        if (!empty($blocks)) {
            foreach ($blocks as $block) {
                $blockName = isset($block['name']) ? esc_html($block['name']) : '';

                if ($block['type'] === 'future') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Wait until you're born to calculate.";
                    break;
                } elseif ($block['type'] === 'infant') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Still a baby — no love calculations needed yet!";
                    break;
                } elseif ($block['type'] === 'under14') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Focus on school first, love can wait!";
                    break;
                } elseif ($block['type'] === 'over90') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Ageless love.";
                    break;
                } elseif ($block['type'] === 'same_name') {
                    $hasSameName = true;
                }
            }

            if ($fatalMessage) {
                $lines = [
                        ['type' => 'block', 'text' => $fatalMessage],
                ];
                ob_start(); ?>
                <div class="ftn-analysis-wrap">
                    <div class="ftn-chat-wrap"><div class="ftn-chat-bubble"><div class="ftn-chat-body" id="zdc-love-chat-body" data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>"></div></div></div>
                </div>
                <div class="zdc-action-footer" style="display:none;">
                    <span class="ftn-btn-reset zdc-love-btn-reset">← Start over</span>
                </div>
                <?php return ob_get_clean();
            }
        }

        $name1Safe = esc_html($love['name1'] ?? 'You');
        $name2Safe = esc_html($love['name2'] ?? 'Partner');
        $dob1Safe = esc_html($love['dob1'] ?? '');
        $dob2Safe = esc_html($love['dob2'] ?? '');
        $sign1 = $love['sign1'] ?? []; $sign2 = $love['sign2'] ?? [];
        $analysis = $love['analysis'] ?? [];

        $getYear = function($dob) { return (int)substr($dob, -4); };
        $currentYear = (int)date('Y');
        $age1 = $currentYear - $getYear($dob1Safe);
        $age2 = $currentYear - $getYear($dob2Safe);
        $showAiBtn = ($age1 <= 55 && $age2 <= 55);

        $lines = [];

        if ($hasSameName) {
            $lines[] = ['type' => 'block', 'text' => "» Are you trolling?"];
        }

        $matchHint = '';
        if (!empty($love['age_gap_msg'])) {
            $matchHint = "<strong style='color:#ef4444;'>{$love['age_gap_msg']}</strong>";
        }

        $lines = array_merge($lines, [
                ['type' => 'greeting', 'text' => 'Astrological chart of ' . $name1Safe . ' and ' . $name2Safe . ':'],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'index', 'key' => 'sign1', 'label' => $name1Safe, 'value' => 'Sign ' . ($love['sign1_name'] ?? '') . ' (' . ($sign1['element'] ?? '') . ')', 'hint' => ''],
                ['type' => 'index', 'key' => 'sign2', 'label' => $name2Safe, 'value' => 'Sign ' . ($love['sign2_name'] ?? '') . ' (' . ($sign2['element'] ?? '') . ')', 'hint' => ''],
                ['type' => 'divider', 'text' => ''],

                ['type' => 'index', 'key' => 'pair_aspect', 'label' => '1. Distance (Aspect)', 'value' => $analysis['aspect_label'] ?? '', 'hint' => $analysis['aspect_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_element', 'label' => '2. Element', 'value' => ($sign1['element'] ?? '') . ' × ' . ($sign2['element'] ?? ''), 'hint' => $analysis['element_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_quality', 'label' => '3. Quality', 'value' => ($analysis['mod_1'] ?? '') . ' × ' . ($analysis['mod_2'] ?? ''), 'hint' => $analysis['mod_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_polarity', 'label' => '4. Polarity', 'value' => ($analysis['pol_1'] ?? '') . ' × ' . ($analysis['pol_2'] ?? ''), 'hint' => $analysis['pol_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_planet', 'label' => '5. Planet & Decan', 'value' => !empty($analysis['planet_match']) ? 'Deep connection' : 'Independent', 'hint' => $analysis['planet_hint'] ?? ''],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'index', 'key' => 'match', 'label' => 'Compatibility', 'value' => ($love['final_percent'] ?? $analysis['score'] ?? 0) . '%', 'hint' => $matchHint],
                ['type' => 'divider', 'text' => '']
        ]);

        foreach ($analysis['strengths'] ?? [] as $s) { $lines[] = ['type' => 'block', 'text' => '✅ ' . $s]; }
        foreach ($analysis['challenges'] ?? [] as $c) { $lines[] = ['type' => 'block', 'text' => '⚠️ ' . $c]; }
        $lines[] = ['type' => 'closing', 'text' => 'Analyzing...'];

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-love-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="chat" role="tab">Result</button>
                <button class="ftn-tab" data-tab="co-so-luan-giai" role="tab">How It Works</button>
            </div>
            <div class="ftn-tab-pane active" id="zdc-love-tab-chat">
                <div class="ftn-chat-wrap">
                    <div class="ftn-chat-bubble">
                        <div class="ftn-chat-body" id="zdc-love-chat-body" data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>"></div>
                    </div>
                </div>
                <?php if (get_option('bb_zodiac_allow_ai', '0') === '1' && $showAiBtn): ?>
                    <div id="zdc-love-deep-analysis-form" class="ftn-form-deep" style="display:none;">
                        <p>Continue analyzing <strong>to understand deeper</strong> the connection and potential of both people.</p>
                        <input type="text" id="zdc-love-cbsp-deep" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button class="ftn-btn-submit" id="zdc-love-btn-deep-analyze">
                            <span class="ftn-btn-text">Analyze</span>
                            <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Analyzing...</span>
                        </button>
                        <span class="zdc-error zdc-err-analyze"></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ftn-tab-pane" id="zdc-love-tab-co-so-luan-giai">
                <div class="zdc-calc-breakdown">
                    <h4>5-Layer Analysis Foundation (Synastry)</h4>
                    <p>The system does not rely on intuition but uses 5 foundational astrological methods to calculate compatibility:</p>
                    <ul style="padding-left: 20px; line-height: 1.6;">
                        <li><strong>1. Aspects:</strong> Calculate the distance between 2 signs on the 360° wheel to determine if they form a Trine (favorable), Square (tense), or Opposition (complementary).</li>
                        <li><strong>2. Element Groups:</strong> Examine the chemical reaction between Fire - Earth - Air - Water. Complementary elements gain points, conflicting ones lose base points.</li>
                        <li><strong>3. Modalities:</strong> Examine action style (Cardinal, Fixed, Mutable). Same modality tends to create conflict; different modalities tend to balance each other.</li>
                        <li><strong>4. Polarity:</strong> Examine energy flow. Same polarity (Yang-Yang or Yin-Yin) tends to empathize more than mixed polarity.</li>
                        <li><strong>5. Planets & Decans (Rulers & Decans):</strong> This is the deep analysis step. The system checks the ruling planet and sub-ruling planet (Decan based on specific birth date) for hidden connections with the partner.</li>
                    </ul>

                    <h4>Zodiac Sign Determination (<?= $name1Safe ?>)</h4>
                    <p>Based on date of birth <strong><?= esc_html($dob1Safe) ?></strong>, the system cross-references with the <strong>12 Western zodiac signs</strong> (Tropical Zodiac) to determine the matching sign.</p>
                    <p><strong>Calculation:</strong> The system takes the month and day of birth, then cross-references with the standard date ranges of the 12 signs (Aries: 21/3-19/4, Taurus: 20/4-20/5, Virgo: 23/8-22/9, etc.). If the birth date falls within a sign's range, that person belongs to that sign.</p>
                    <p>→ Result: <strong><?= esc_html((string)($love['sign1_name'] ?? '')) ?></strong></p>

                    <h4>Zodiac Sign Determination (<?= $name2Safe ?>)</h4>
                    <p>Based on date of birth <strong><?= esc_html($dob2Safe) ?></strong>, the system cross-references with the <strong>12 Western zodiac signs</strong> (Tropical Zodiac) to determine the matching sign.</p>
                    <p><strong>Calculation:</strong> Similar to above, the system takes the month and day of birth and cross-references with the standard date ranges of the 12 signs to determine the corresponding zodiac sign.</p>
                    <p>→ Result: <strong><?= esc_html((string)($love['sign2_name'] ?? '')) ?></strong></p>

                    <h4>Element Comparison</h4>
                    <p>Both signs are placed into their corresponding element groups: <strong><?= esc_html((string)($love['element1'] ?? '?')) ?></strong> and <strong><?= esc_html((string)($love['element2'] ?? '?')) ?></strong>.</p>
                    <p>The system evaluates the degree of complementarity/conflict from the element interaction (Fire - Earth - Air - Water) to create a foundation for the love analysis.</p>

                    <h4>Quality & Polarity Comparison</h4>
                    <p>Qualities of both signs: <strong><?= esc_html((string)($sign1['quality'] ?? '?')) ?></strong> × <strong><?= esc_html((string)($sign2['quality'] ?? '?')) ?></strong>.</p>
                    <p>Energy polarity: <strong><?= esc_html((string)($sign1['polarity'] ?? '?')) ?></strong> × <strong><?= esc_html((string)($sign2['polarity'] ?? '?')) ?></strong>.</p>

                    <h4>Compatibility Summary</h4>
                    <p>From the above data layers, the compatibility score is synthesized: <strong style="color: red"><?= esc_html((string)($analysis['score'] ?? 0)) ?>%</strong>.</p>
                </div>
            </div>
        </div>
        <div class="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-love-btn-reset">← Back</span>
            <button type="button" id="zdc-btn-comment" class="zdc-btn-comment">Discuss</button>
        </div>
        <p class="zdc-disclaimer" id="zdc-disclaimer" style="display:none;">
            ✦ This is a reference result based on astrological systems. All actions and future directions depend on your wise choices and personal efforts.
        </p>
        <?php
        return ob_get_clean();
    }

    private static function signName(string $id): string {
        $map = ['aries' => 'Aries ♈', 'taurus' => 'Taurus ♉', 'gemini' => 'Gemini ♊', 'cancer' => 'Cancer ♋', 'leo' => 'Leo ♌', 'virgo' => 'Virgo ♍', 'libra' => 'Libra ♎', 'scorpio' => 'Scorpio ♏', 'sagittarius' => 'Sagittarius ♐', 'capricorn' => 'Capricorn ♑', 'aquarius' => 'Aquarius ♒', 'pisces' => 'Pisces ♓'];
        return $map[$id] ?? $id;
    }

    public static function tabs(array $tabs): string {
        return '<div id="zdc-tabs-html-payload" style="display:none"><div id="zdc-tab-chi-tiet-html">' . wp_kses_post($tabs['chi_tiet'] ?? '') . '</div></div>';
    }
    public static function loveTabs(array $tabs): string { return '<div id="zdc-love-tabs-html-payload" style="display:none"><div id="zdc-love-tab-chat-ai">' . wp_kses_post($tabs['phan_tich'] ?? '') . '</div></div>'; }

    private static function calcBreakdown(array $sign): string {
        $dob = $sign['dob'] ?? '';
        $signId = $sign['id'] ?? 'aries';
        $signName = ($sign['name'] ?? '') . ' ' . ($sign['symbol'] ?? '');
        $decan = $sign['decan'] ?? '?';
        $hasCusp = !empty($sign['has_cusp']);

        $dateRanges = [
                'aries'       => ['21/03', '19/04'],
                'taurus'      => ['20/04', '20/05'],
                'gemini'      => ['21/05', '20/06'],
                'cancer'      => ['21/06', '22/07'],
                'leo'         => ['23/07', '22/08'],
                'virgo'       => ['23/08', '22/09'],
                'libra'       => ['23/09', '22/10'],
                'scorpio'     => ['23/10', '21/11'],
                'sagittarius' => ['22/11', '21/12'],
                'capricorn'   => ['22/12', '19/01'],
                'aquarius'    => ['20/01', '18/02'],
                'pisces'      => ['19/02', '20/03'],
        ];

        $startDisp = $dateRanges[$signId][0] ?? '??/??';
        $endDisp   = $dateRanges[$signId][1] ?? '??/??';

        $dobParts = explode('/', $dob);
        $day   = $dobParts[0] ?? 'dd';
        $month = $dobParts[1] ?? 'mm';

        ob_start(); ?>
        <div class="zdc-calc-breakdown">
            <h4>1. Zodiac Sign Calculation</h4>
            <ul style="padding-left: 20px; line-height: 1.6; margin-bottom: 15px;">
                <li><strong>Date of birth:</strong> <?= esc_html($dob) ?></li>
                <li><strong>Extract:</strong> Day = <?= esc_html($day) ?>, Month = <?= esc_html($month) ?> (Year not counted)</li>
                <li><strong>Cross-reference:</strong> Coordinate [<?= esc_html($day) ?>/<?= esc_html($month) ?>] matches range [<?= esc_html($startDisp) ?>] to [<?= esc_html($endDisp) ?>]</li>
                <li><strong>Result:</strong> &#10142; <strong><?= esc_html($signName) ?></strong></li>
            </ul>

            <h4>2. Decan Calculation</h4>
            <ul style="padding-left: 20px; line-height: 1.6; margin-bottom: 15px;">
                <li><strong>Division:</strong> The 30-day cycle of <?= esc_html($signName) ?> is split into 3 segments (10 days each).</li>
                <li><strong>Position:</strong> Coordinate [<?= esc_html($day) ?>/<?= esc_html($month) ?>] falls into the <?= esc_html((string)$decan) ?>rd 10-day segment.</li>
                <li><strong>Result:</strong> &#10142; <strong>Decan <?= esc_html((string)$decan) ?></strong></li>
            </ul>

            <?php if ($hasCusp): ?>
                <h4>3. Cusp Check</h4>
                <ul style="padding-left: 20px; line-height: 1.6;">
                    <li><strong>Boundary condition:</strong> [<?= esc_html($day) ?>/<?= esc_html($month) ?>] falls within 3-5 days transition between 2 signs.</li>
                    <li><strong>Status:</strong> TRUE (Blended energy activated)</li>
                    <li><strong>Result:</strong> &#10142; <strong>Cusp <?= esc_html($sign['cusp_name'] ?? '') ?></strong></li>
                </ul>
            <?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }

    public static function tuViIndexes(array $sign, $period, string $avoidDomain = ''): string {
        $signId = $sign['id'] ?? 'aries';
        $name   = $sign['name'] ?? '';
        $symbol = $sign['symbol'] ?? '';

        $lines = [];
        $lines[] = ['type' => 'greeting', 'text' => 'Zodiac sign of ' . $name . ':'];
        $lines[] = ['type' => 'index', 'key' => 'sign_name', 'label' => 'Ruling Sign', 'value' => $name . ' ' . $symbol, 'hint' => '— ' . ($sign['keywords'] ?? '')];
        $lines[] = ['type' => 'index', 'key' => 'element', 'label' => 'Element', 'value' => $sign['element'] ?? ''];
        $lines[] = ['type' => 'index', 'key' => 'planet', 'label' => 'Planet', 'value' => $sign['planet'] ?? ''];
        $lines[] = ['type' => 'index', 'key' => 'quality', 'label' => 'Quality', 'value' => $sign['quality'] ?? ''];
        $lines[] = ['type' => 'index', 'key' => 'polarity', 'label' => 'Polarity', 'value' => $sign['polarity'] ?? ''];

        $compat = $sign['compatibility'] ?? [];
        if (!empty($compat)) {
            $lines[] = ['type' => 'divider', 'text' => ''];
            $lines[] = ['type' => 'section', 'text' => 'Energy Compatibility'];

            if (!empty($compat['best_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_best', 'label' => 'Best Match', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['best_match'])), 'hint' => ''];
            }
            if (!empty($compat['karmic_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_karmic', 'label' => 'Karmic Match', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['karmic_match'])), 'hint' => ''];
            }
            if (!empty($compat['worst_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_worst', 'label' => 'Challenging', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['worst_match'])), 'hint' => ''];
            }
        }

        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines[] = ['type' => 'block', 'text' => BbZodiac_Calc::spinText($sign['horoscope_life'] ?? '') ];

        $horoscopeData = BbZodiac_Calc::getStaticHoroscopeLines($signId, $period, $avoidDomain);
        $horoscopeParagraphs = $horoscopeData['lines'] ?? [];
        $primaryDomain = $horoscopeData['primary'] ?? '';

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-tuvi-analysis-wrap">
            <div class="ftn-chat-wrap">
                <div class="ftn-chat-bubble">
                    <div class="ftn-chat-body" id="zdc-tuvi-chat-body" data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>">
                        <span class="ftn-cursor">|</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($horoscopeParagraphs)): ?>
                <div id="zdc-tuvi-html" class="zdc-tuvi-container" style="display:none;">
                    <div class="zdc-tuvi-hero-card">
                        <div class="zdc-hero-badge">Main Message</div>
                        <?php foreach ($horoscopeParagraphs as $item): ?>
                            <p class="zdc-hero-text"><strong><?= esc_html($item['label']) ?> — </strong><?= $item['text'] ?></p>
                        <?php endforeach; ?>
                    </div>

                    <div class="zdc-energy-feedback">
                        <p class="zdc-feedback-title">Does this frequency resonate with you?</p>
                        <div class="zdc-feedback-options" data-primary="<?= esc_attr($primaryDomain) ?>"
                             data-sign="<?= esc_attr($signId) ?>" data-period="<?= esc_attr($period) ?>">
                            <button class="zdc-energy-btn" data-type="high">Touches my consciousness</button>
                            <button class="zdc-energy-btn" data-type="mid">Contemplating</button>
                            <button class="zdc-energy-btn" data-type="low">Not on the same frequency</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-tuvi-btn-reset">← Change sign</span>
        </div>
        <p class="zdc-disclaimer" style="display:none;">
            ✦ This interpretation is for reference to give you additional perspective. Real choices and decisions always belong to you.
        </p>
        <?php return ob_get_clean();
    }

    public static function natalResult(array $data, array $natalChart): string {
        $planets = $natalChart['planets'];
        $ascendant = $natalChart['ascendant'];
        $midheaven = $natalChart['midheaven'];
        $elementDist = $natalChart['element_distribution'];
        $modalityDist = $natalChart['modality_distribution'];

        $sun = $planets['sun'];
        $moon = $planets['moon'];
        $domElem = $natalChart['dominant_element'] ?? 'Fire';

        // Academic interpretation language
        $elemDesc = [
                'Fire'  => 'action-oriented, passionate, craving self-affirmation and pioneering spirit.',
                'Earth' => 'practical thinking, patience, need to build solid foundations and strive for stability.',
                'Air'   => 'rational thinking, objectivity, desire for freedom and constant need to connect and exchange information.',
                'Water' => 'sharp intuition, hidden emotional depth, empathy and need for spiritual bonding.'
        ];

        $allow_ai = get_option('bb_zodiac_allow_ai', '0') === '1';

        $lines = [
                ['type' => 'greeting', 'text' => 'Your personal star map has been initialized.'],
                ['type' => 'intro', 'text' => "Astronomical database at coordinates <strong>" . esc_html( mb_convert_case($data['pob'], MB_CASE_TITLE, "UTF-8") ) . "</strong> on <strong>" . esc_html($data['dob']) . "</strong>" . ($data['tob'] ? ' (' . esc_html($data['tob']) . ')' : '') . ":"],
                ['type' => 'divider', 'text' => ''],

                ['type' => 'section', 'text' => 'CORE STRUCTURE ANALYSIS (BIG 3)'],

                ['type' => 'block', 'text' => "<strong>Sun {$sun['sign']} (Sun Sign - Consciousness & Identity):</strong><br>Carries the traits of the {$sun['element']} element. Shapes core personality and the lens through which you view the world: <em>" . ($sun['keywords'] ?? '') . "</em>."],

                ['type' => 'block', 'text' => "<strong>Moon {$moon['sign']} (Moon Sign - Subconscious & Emotions):</strong><br>Governed by the {$moon['element']} element. Determines instinctive reactions, inner world and emotional safety needs through: <em>" . ($moon['keywords'] ?? '') . "</em>."],

                ['type' => 'block', 'text' => "<strong>Ascendant {$ascendant['sign']} (Ascendant - Personal Stance):</strong><br>Led by {$ascendant['element']} energy. This is the social mask, problem-solving style and first impression you create before others: <em>" . ($ascendant['keywords'] ?? '') . "</em>."],

                ['type' => 'block', 'text' => "<strong>Dominant Element: {$domElem}</strong><br>Occupying the largest proportion in your personal star chart, the {$domElem} system tilts your psychological structure toward {$elemDesc[$domElem]}"]
        ];

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-natal-analysis-wrap">

            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="zdc-natal-reading" role="tab">Overview</button>
                <button class="ftn-tab" data-tab="zdc-natal-wheel" role="tab">Chart</button>
                <button class="ftn-tab" data-tab="zdc-natal-data" role="tab">Coordinates</button>
            </div>

            <div class="ftn-tab-pane active" id="ftn-tab-zdc-natal-reading">
                <div class="ftn-chat-wrap">
                    <div class="ftn-chat-bubble">
                        <div class="ftn-chat-body" id="zdc-natal-chat-body" data-lines="<?= esc_attr(json_encode($lines)) ?>">
                            <span class="ftn-cursor">|</span>
                        </div>
                    </div>
                </div>
                <div id="zdc-natal-html" class="zdc-html" style="display:none;"></div>

                <?php if ($allow_ai): ?>
                    <div id="zdc-natal-deep-form" class="ftn-form-deep" style="display:none;">
                        <p>💡 To understand yourself better, continue exploring the interactions between planets and decode your destiny.</p>
                        <input type="text" id="zdc-natal-cbsp-deep" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button class="ftn-btn-submit" id="zdc-natal-btn-analyze">
                            <span class="ftn-btn-text">Decode chart</span>
                            <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Processing...</span>
                        </button>
                        <span class="zdc-error zdc-err-analyze"></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ftn-tab-pane" id="ftn-tab-zdc-natal-wheel">
                <p class="zdc-wheel-intro">
                    <strong>Chart structure:</strong> The outer ring represents the 12 zodiac signs, the inner ring represents the 12 houses. The connecting lines at the center show aspects that constitute the personal psychological structure.
                </p>
                <div class="zdc-wheel-wrap">
                    <canvas id="zdc-natal-canvas" width="460" height="460"></canvas>
                    <div id="zdc-wheel-tooltip" class="zdc-wheel-tooltip"></div>
                </div>
                <div class="zdc-wheel-legend">
                    <div class="zdc-wheel-legend-elements">
                        <span><i class="zdc-wheel-legend-dot zdc-bg-fire"></i> Fire</span>
                        <span><i class="zdc-wheel-legend-dot zdc-bg-earth"></i> Earth</span>
                        <span><i class="zdc-wheel-legend-dot zdc-bg-air"></i> Air</span>
                        <span><i class="zdc-wheel-legend-dot zdc-bg-water"></i> Water</span>
                    </div>
                    <div class="zdc-wheel-legend-planets">
                        <span>Sun</span><span>Moon</span><span>Mercury</span><span>Venus</span><span>Mars</span>
                        <span>Jupiter</span><span>Saturn</span><span>Uranus</span><span>Neptune</span><span>Pluto</span>
                        <br>
                        <span class="zdc-text-asc">ASC</span> (Ascendant) &nbsp;|&nbsp;
                        <span class="zdc-text-mc">MC</span> (Midheaven)
                    </div>
                </div>
            </div>

            <div class="ftn-tab-pane" id="ftn-tab-zdc-natal-data">

                <h4 class="zdc-data-heading">Astrological Planets</h4>
                <div class="zdc-data-grid">
                    <?php foreach ($planets as $key => $p): ?>
                        <div class="zdc-data-card">
                            <div class="zdc-data-card-title"><?= $p['symbol'] ?> <?= $p['name'] ?></div>
                            <div class="zdc-data-card-val"><?= $p['sign'] ?> <?= $p['degree_formatted'] ?></div>
                            <div class="zdc-data-card-meta"><?= $p['element'] ?> • <?= $p['modality'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h4 class="zdc-data-heading zdc-mt-4">Key Points (Angles)</h4>
                <div class="zdc-data-grid">
                    <div class="zdc-data-card zdc-border-asc">
                        <div class="zdc-data-card-title">Ascendant</div>
                        <div class="zdc-data-card-val"><?= $ascendant['sign'] ?> <?= $ascendant['degree_formatted'] ?></div>
                        <div class="zdc-data-card-meta">Social mask & Communication</div>
                    </div>
                    <div class="zdc-data-card zdc-border-mc">
                        <div class="zdc-data-card-title">Midheaven</div>
                        <div class="zdc-data-card-val"><?= $midheaven['sign'] ?> <?= $midheaven['degree_formatted'] ?></div>
                        <div class="zdc-data-card-meta">Career & Reputation</div>
                    </div>
                </div>

                <div class="zdc-data-grid-2 zdc-mt-4">
                    <div>
                        <h4 class="zdc-data-heading">Element Distribution</h4>
                        <ul class="zdc-data-list">
                            <?php
                            $total = array_sum($elementDist);
                            foreach ($elementDist as $el => $cnt):
                                $pct = $total > 0 ? round(($cnt / $total) * 100) : 0;
                                ?>
                                <li><strong><?= $el ?>:</strong> <?= $cnt ?> planets (<?= $pct ?>%)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="zdc-data-heading">Modality Distribution</h4>
                        <ul class="zdc-data-list">
                            <?php
                            $modNames = ['Cardinal' => 'Cardinal', 'Fixed' => 'Fixed', 'Mutable' => 'Mutable'];
                            foreach ($modalityDist as $mod => $cnt):
                                $pct = $total > 0 ? round(($cnt / $total) * 100) : 0;
                                ?>
                                <li><strong><?= $modNames[$mod] ?? $mod ?>:</strong> <?= $cnt ?> planets (<?= $pct ?>%)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

        <div class="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-natal-btn-reset">← Re-enter data</span>
        </div>

        <p class="zdc-disclaimer" style="display:none;">
            * Note: Ascendant coordinates and House distributions may differ significantly if the provided birth time is inaccurate.
        </p>
        <?php return ob_get_clean();
    }
}