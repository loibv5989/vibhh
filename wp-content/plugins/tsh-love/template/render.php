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
                    <th class="ftn-th-lbl">Chỉ số</th>
                    <th class="ftn-th-n1">' . $n1 . '</th>
                    <th class="ftn-th-n2">' . $n2 . '</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ftn-td-lbl"><strong>Chủ Đạo</strong><br><span>(Đường đời)</span></td>
                    <td class="ftn-td-n1" data-name="' . $n1 . '">
                        <span class="ftn-num-val">Số ' . $data['lp1'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['lp1']) . '</div>
                    </td>
                    <td class="ftn-td-n2" data-name="' . $n2 . '">
                        <span class="ftn-num-val">Số ' . $data['lp2'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['lp2']) . '</div>
                    </td>
                </tr>
                <tr>
                    <td class="ftn-td-lbl"><strong>Linh Hồn</strong><br><span>(Động lực gốc)</span></td>
                    <td class="ftn-td-n1" data-name="' . $n1 . '">
                        <span class="ftn-num-val">Số ' . $data['soul1'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['soul1']) . '</div>
                    </td>
                    <td class="ftn-td-n2" data-name="' . $n2 . '">
                        <span class="ftn-num-val">Số ' . $data['soul2'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['soul2']) . '</div>
                    </td>
                </tr>
                <tr>
                    <td class="ftn-td-lbl"><strong>Thái Độ</strong><br><span>(Vỏ bọc tự vệ)</span></td>
                    <td class="ftn-td-n1" data-name="' . $n1 . '">
                        <span class="ftn-num-val">Số ' . $data['att1'] . '</span>
                        <div class="ftn-num-hint">' . esc_html($data['hints']['att1']) . '</div>
                    </td>
                    <td class="ftn-td-n2" data-name="' . $n2 . '">
                        <span class="ftn-num-val">Số ' . $data['att2'] . '</span>
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
                    $fatalMessage = "» <strong>{$blockName}</strong> — Khi nào ra đời rồi tính tiếp.";
                    break;
                } elseif ($block['type'] === 'infant') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Bé còn đang bú bình, tính toán tình duyên gì tầm này!";
                    break;
                } elseif ($block['type'] === 'under14') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Hãy tập trung học trước đã rồi yêu sau nhé!";
                    break;
                } elseif ($block['type'] === 'over90') {
                    $fatalMessage = "» <strong>{$blockName}</strong> — Tuổi cụ là tình yêu bao la.";
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
            $lines[] = ['type' => 'block', 'text' => "» Có Troll troll VN không vây?"];
        }

        $lines = array_merge($lines, [
                ['type' => 'greeting', 'text' => 'Mức độ hòa hợp của 2 người dựa trên năng lượng Ngày sinh và Họ tên:'],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'text', 'text' => '<strong>Tính cách & Phản ứng</strong>'],
                ['type' => 'block', 'text' => self::buildComparisonTable($data)],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'text', 'text' => "<strong>Quỹ đạo chung</strong>"],
                ['type' => 'text', 'text' => $data['match_summary']],
                ['type' => 'text', 'text' => '<strong>→ Điểm cộng (Sức hút):</strong>'],
                ['type' => 'text', 'text' => $data['pros']],
                ['type' => 'text', 'text' => '<strong>→ Thách thức (Điểm cọ xát):</strong>'],
                ['type' => 'text', 'text' => $data['cons']],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'text', 'text' => '<strong>Năng lượng</strong>'],
                ['type' => 'text', 'text' => "<strong>• {$data['name1']}:</strong> {$data['advice1']}"],
                ['type' => 'text', 'text' => "<strong>• {$data['name2']}:</strong> {$data['advice2']}"],
                ['type' => 'divider', 'text' => ''],
        ]);

        $data['percent_display'] = isset($data['final_percent']) ? $data['final_percent'] : ($data['percent'] ?? 0);
        $lines[] = ['type' => 'index', 'key' => 'match', 'label' => 'Độ Tương Hợp', 'value' => '💗 '. $data['percent_display'] . '%', 'hint' => $data['hints']['match'] ?? ''];
        return self::render_layout($lines, $data);
    }

    private static function render_layout(array $lines, array $data, bool $isFatal = false): string {
        ob_start();  ?>
        <div class="ftn-analysis-wrap" id="numm-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="phan-tich" role="tab">Kết quả</button>
                <?php if (!$isFatal): ?>
                    <button class="ftn-tab" data-tab="nguyen-ly" role="tab">Cơ Sở</button>
                <?php endif; ?>
            </div>

            <div class="ftn-tab-pane active" id="numm-tab-phan-tich">
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
                <div class="ftn-tab-pane" id="numm-tab-nguyen-ly">
                    <div class="ftn-calc-block">
                        <div class="thsl-lp-section">
                            <h4 class="thsl-lp-title">Số Chủ Đạo:</h4>

                            <div class="thsl-lp-box thsl-lp-box-p1">
                                <strong class="thsl-lp-name thsl-lp-name-p1"><?= esc_html($data['name1']) ?></strong> (<?= esc_html($data['dob1']) ?>)<br>
                                <div class="thsl-lp-detail">Cách tính: <?= wp_kses_post($data['calc1']) ?></div>
                                <div class="thsl-lp-detail">Con số Chủ đạo: <strong class="thsl-lp-num"><?= esc_html($data['lp1']) ?></strong></div>
                            </div>

                            <div class="thsl-lp-box thsl-lp-box-p2">
                                <strong class="thsl-lp-name thsl-lp-name-p2"><?= esc_html($data['name2']) ?></strong> (<?= esc_html($data['dob2']) ?>)<br>
                                <div class="thsl-lp-detail">Cách tính: <?= wp_kses_post($data['calc2']) ?></div>
                                <div class="thsl-lp-detail">Con số Chủ đạo: <strong class="thsl-lp-num"><?= esc_html($data['lp2']) ?></strong></div>
                            </div>
                        </div>

                        <p>Dựa trên <a href="/than-so-hoc/" target="_blank">Thần số học Pitago</a>, hệ thống phân tích dựa trên các lớp năng lượng:</p>
                        <ul>
                            <li><strong>Số Chủ Đạo (Life Path):</strong> Lõi năng lượng gốc, quyết định 70% khả năng đồng hành đường dài.</li>
                            <li><strong>Số Linh Hồn (Soul Urge):</strong> Động lực tiềm thức, giải thích kỳ vọng thực sự trong tình yêu.</li>
                            <li><strong>Số Thái Độ (Attitude):</strong> Lớp vỏ tự vệ, quyết định cách hai người phản ứng khi xảy ra mâu thuẫn.</li>
                        </ul>
                        <p>Tỷ lệ <strong><?= (int)($data['percent_display'] ?? 0) ?>%</strong> là chỉ số phản ánh mức độ tương đồng về tần số rung động tự nhiên giữa hai người.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if (!$isFatal && get_option('bty_tsh_allow_ai', '0') === '1'): ?>
                <div class="ftn-analyze-btn-wrap" id="numm-analyze-btn-wrap" style="display:none;">
                    <button type="button" class="ftn-analyze-btn" id="numm-analyze-btn">
                        <span class="ftn-analyze-text">Phân tích thêm</span>
                        <span class="ftn-analyze-loading"><span class="ftn-spinner"></span> Đang phân tích...</span>
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
        <div id="numm-ai-content"><?= wp_kses_post($tabs['phan_tich'] ?? '') ?></div>
        <?php
        return ob_get_clean();
    }
}