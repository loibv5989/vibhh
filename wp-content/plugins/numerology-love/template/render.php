<?php
if (!defined('ABSPATH')) exit;

class TshLove_Render {

    private static function buildComparisonTable(array $data): string {
        $n1 = esc_html($data['name1']);
        $n2 = esc_html($data['name2']);

        $html = '
        <table class="ftn-res-table">
            <thead>
                <tr>
                    <th class="ftn-th-lbl">Numbers</th>
                    <th class="ftn-th-n1">' . $n1 . '</th>
                    <th class="ftn-th-n2">' . $n2 . '</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ftn-td-lbl"><strong>Life Path</strong><br><span>(Birth Number)</span></td>
                    <td class="ftn-td-n1" data-name="' . $n1 . '">
                        <span class="ftn-num-val">Number ' . $data['lp1'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['lp1']) . '</div>
                    </td>
                    <td class="ftn-td-n2" data-name="' . $n2 . '">
                        <span class="ftn-num-val">Number ' . $data['lp2'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['lp2']) . '</div>
                    </td>
                </tr>
                <tr>
                    <td class="ftn-td-lbl"><strong>Soul Urge</strong><br><span>(Core Drive)</span></td>
                    <td class="ftn-td-n1" data-name="' . $n1 . '">
                        <span class="ftn-num-val">Number ' . $data['soul1'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['soul1']) . '</div>
                    </td>
                    <td class="ftn-td-n2" data-name="' . $n2 . '">
                        <span class="ftn-num-val">Number ' . $data['soul2'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['soul2']) . '</div>
                    </td>
                </tr>
                <tr>
                    <td class="ftn-td-lbl"><strong>Attitude</strong><br><span>(First Impression)</span></td>
                    <td class="ftn-td-n1" data-name="' . $n1 . '">
                        <span class="ftn-num-val">Number ' . $data['att1'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['att1']) . '</div>
                    </td>
                    <td class="ftn-td-n2" data-name="' . $n2 . '">
                        <span class="ftn-num-val">Number ' . $data['att2'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['att2']) . '</div>
                    </td>
                </tr>
            </tbody>
        </table>';

        return $html;
    }

    public static function indexes(array $data): string {
        $blocks = $data['blocks'] ?? [];
        $lines = [];
        $hasSameName = false;
        $fatalMessage = '';

        if (!empty($blocks)) {
            foreach ($blocks as $block) {
                $blockName = isset($block['name']) ? esc_html($block['name']) : '';
                if ($block['type'] === 'future') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Come back when they're born.";
                    break;
                } elseif ($block['type'] === 'infant') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Still in diapers? Love compatibility can wait!";
                    break;
                } elseif ($block['type'] === 'under14') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Focus on school first, love later!";
                    break;
                } elseif ($block['type'] === 'over90') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Love knows no age limit.";
                    break;
                } elseif ($block['type'] === 'same_name') {
                    $hasSameName = true;
                }
            }

            if ($fatalMessage) {
                $lines = [
                        ['type' => 'block', 'text' => $fatalMessage],
                ];
                return self::render_layout($lines, $data, true);
            }
        }

        if ($hasSameName) {
            $lines[] = ['type' => 'block', 'text' => "» Is someone trolling?"];
        }

        $lines = array_merge($lines, [
                ['type' => 'greeting', 'text' => 'Compatibility level based on Birth Date and Name energy:'],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'text', 'text' => '<strong>Character & Response</strong>'],
                ['type' => 'block', 'text' => self::buildComparisonTable($data)],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'text', 'text' => "<strong>Shared Trajectory</strong>"],
                ['type' => 'text', 'text' => $data['match_summary']],
                ['type' => 'text', 'text' => '<strong>→ Strengths (Attraction):</strong>'],
                ['type' => 'text', 'text' => $data['pros']],
                ['type' => 'text', 'text' => '<strong>→ Challenges (Friction Points):</strong>'],
                ['type' => 'text', 'text' => $data['cons']],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'text', 'text' => '<strong>Energy</strong>'],
                ['type' => 'text', 'text' => "<strong>• {$data['name1']}:</strong> {$data['advice1']}"],
                ['type' => 'text', 'text' => "<strong>• {$data['name2']}:</strong> {$data['advice2']}"],
                ['type' => 'divider', 'text' => ''],
        ]);

        $data['percent_display'] = isset($data['final_percent']) ? $data['final_percent'] : ($data['percent'] ?? 0);
        $lines[] = ['type' => 'index', 'key' => 'match', 'label' => 'Compatibility', 'value' => '💗 '. $data['percent_display'] . '%', 'hint' => $data['hints']['match'] ?? ''];
        return self::render_layout($lines, $data);
    }

    private static function render_layout(array $lines, array $data, bool $isFatal = false): string {
        ob_start();  ?>
        <div class="ftn-analysis-wrap" id="numm-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="analysis" role="tab">Result</button>
                <?php if (!$isFatal): ?>
                    <button class="ftn-tab" data-tab="foundation" role="tab">Foundation</button>
                <?php endif; ?>
            </div>

            <div class="ftn-tab-pane active" id="numm-tab-analysis">
                <div class="ftn-chat-wrap">
                    <div class="ftn-chat-bubble">
                        <div class="ftn-chat-body" id="numm-chat-body"
                             data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>">
                            <span class="ftn-cursor">|</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$isFatal): ?>
                <div class="ftn-tab-pane" id="numm-tab-foundation">
                    <div class="ftn-calc-block">
                        <div class="thsl-lp-section">
                            <h4 class="thsl-lp-title">Life Path Number:</h4>

                            <div class="thsl-lp-box thsl-lp-box-p1">
                                <strong class="thsl-lp-name thsl-lp-name-p1"><?= esc_html($data['name1']) ?></strong> (<?= esc_html($data['dob1']) ?>)<br>
                                <div class="thsl-lp-detail">Calculation: <?= wp_kses_post($data['calc1']) ?></div>
                                <div class="thsl-lp-detail">Life Path Number: <strong class="thsl-lp-num"><?= esc_html($data['lp1']) ?></strong></div>
                            </div>

                            <div class="thsl-lp-box thsl-lp-box-p2">
                                <strong class="thsl-lp-name thsl-lp-name-p2"><?= esc_html($data['name2']) ?></strong> (<?= esc_html($data['dob2']) ?>)<br>
                                <div class="thsl-lp-detail">Calculation: <?= wp_kses_post($data['calc2']) ?></div>
                                <div class="thsl-lp-detail">Life Path Number: <strong class="thsl-lp-num"><?= esc_html($data['lp2']) ?></strong></div>
                            </div>
                        </div>

                        <p>Based on <a href="/numerology/" target="_blank">Pythagorean Numerology</a>, the system analyzes through energy layers:</p>
                        <ul>
                            <li><strong>Life Path Number:</strong> Core life energy, determining 70% of long-term companionship potential.</li>
                            <li><strong>Soul Urge Number:</strong> Subconscious drive, revealing true expectations in love.</li>
                            <li><strong>Attitude Number:</strong> Natural response layer, determining how each person reacts during conflict.</li>
                        </ul>
                        <p>The <strong><?= (int)($data['percent_display'] ?? 0) ?>%</strong> score reflects the natural vibrational frequency similarity between both people.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if (!$isFatal && get_option('bty_tsh_allow_ai', '0') === '1'): ?>
                <div class="ftn-analyze-btn-wrap" id="numm-analyze-btn-wrap" style="display:none;">
                    <button type="button" class="ftn-analyze-btn" id="numm-analyze-btn">
                        <span class="ftn-analyze-text">Analyze More</span>
                        <span class="ftn-analyze-loading"><span class="ftn-spinner"></span> Analyzing...</span>
                    </button>
                    <span class="ftn-error ftn-err-analyze"></span>
                </div>
            <?php endif; ?>
            <div id="numm-ai-content"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function tabs(array $tabs): string {
        ob_start(); ?>
        <div id="numm-ai-content"><?= wp_kses_post($tabs['analysis'] ?? '') ?></div>
        <?php
        return ob_get_clean();
    }
}