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
        $html .= '<h4 class="zdc-st-title">Định hướng & Cốt lõi</h4>';
        $html .= '<p>' . $horoscopeLife . ' ' . $core . '</p>';
        if ($decanOverlay) $html .= '<p><strong>Ảnh hưởng của Decan ' . $decanNum . ':</strong> ' . $decanOverlay . '</p>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-grid">';
        $html .= '<div class="zdc-st-col"><h4 class="zdc-st-title">Điểm mạnh</h4><ul class="zdc-st-list">' . $strHtml . '</ul></div>';
        $html .= '<div class="zdc-st-col"><h4 class="zdc-st-title">Điểm yếu</h4><ul class="zdc-st-list">' . $weakHtml . '</ul></div>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Tình cảm & Sự nghiệp</h4>';
        $html .= '<p><strong>Tình cảm:</strong> ' . $love . '</p>';
        $html .= '<p><strong>Sự nghiệp:</strong> ' . $career . '</p>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Các lớp năng lượng</h4>';
        $html .= '<ul class="zdc-st-list">';
        if($layerElem) $html .= '<li>' . $layerElem . '</li>';
        if($layerPlan) $html .= '<li>' . $layerPlan . '</li>';
        if($layerQual) $html .= '<li>' . $layerQual . '</li>';
        if($layerPol)  $html .= '<li>' . $layerPol . '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        $html .= '<div class="zdc-st-section">';
        $html .= '<h4 class="zdc-st-title">Góc khuất</h4>';
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
        $lines[] = ['type' => 'greeting', 'text' => 'Xin chào'];
        $lines[] = ['type' => 'intro',    'text' => 'Đây là bản đồ tính cách của bạn:'];

        foreach ($easter_eggs as $egg) {
            if ($egg['type'] === 'under18') {
                $lines[] = ['type' => 'easter', 'text' => 'Bạn còn rất trẻ (' . $egg['age'] . ' tuổi) — tương lai rực rỡ đang chờ phía trước!'];
            }
        }

        $lines[] = ['type' => 'divider',  'text' => ''];
        $lines[] = ['type' => 'section', 'text' => ''];

        $lines[] = ['type' => 'index', 'key' => 'sign_name', 'label' => 'Cung Hoàng Đạo', 'value' => $name . ' ' . $symbol, 'hint' => '— ' . ($sign['keywords'] ?? '')];
        $lines[] = ['type' => 'index', 'key' => 'element', 'label' => 'Nguyên Tố', 'value' => $sign['element'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'planet', 'label' => 'Hành Tinh', 'value' => $sign['planet'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'quality', 'label' => 'Tính Chất', 'value' => $sign['quality'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'polarity', 'label' => 'Phân Cực', 'value' => $sign['polarity'] ?? '?', 'hint' => ''];

        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines[] = ['type' => 'section', 'text' => 'Decan (Phân Cung)'];

        $lines[] = ['type' => 'index', 'key' => 'decan', 'label' => 'Decan', 'value' => 'Decan ' . ($sign['decan'] ?? '?'), 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'sub_ruler', 'label' => 'Hành Tinh Phụ', 'value' => $sign['sub_ruler'] ?? '?', 'hint' => ''];
        $lines[] = ['type' => 'index', 'key' => 'decan_vibe', 'label' => 'Sắc Thái', 'value' => $sign['decan_vibe'] ?? '?', 'hint' => ''];

        $compat = $sign['compatibility'] ?? [];
        if (!empty($compat)) {
            $lines[] = ['type' => 'divider', 'text' => ''];
            $lines[] = ['type' => 'section', 'text' => 'Tương Hợp'];

            if (!empty($compat['best_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_best', 'label' => 'Hợp Nhất', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['best_match'])), 'hint' => ''];
            }
            if (!empty($compat['karmic_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_karmic', 'label' => 'Duyên Nghiệp', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['karmic_match'])), 'hint' => ''];
            }
            if (!empty($compat['worst_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_worst', 'label' => 'Khắc Nhất', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['worst_match'])), 'hint' => ''];
            }
        }

        if (!empty($sign['has_cusp'])) {
            $lines[] = ['type' => 'divider', 'text' => ''];
            $lines[] = ['type' => 'section', 'text' => 'Giao Đỉnh (Cusp)'];
            $lines[] = ['type' => 'index', 'key' => 'cusp_name', 'label' => 'Tên Giao Đỉnh', 'value' => $sign['cusp_name'] ?? '?', 'hint' => ''];
            $lines[] = ['type' => 'index', 'key' => 'cusp_blend', 'label' => 'Pha Trộn', 'value' => $sign['cusp_blend'] ?? '?', 'hint' => ''];
            $lines[] = ['type' => 'index', 'key' => 'cusp_vibe', 'label' => 'Năng Lượng', 'value' => $sign['cusp_vibe'] ?? '?', 'hint' => ''];
        }

        $lines[] = ['type' => 'divider', 'text' => ''];
        $lines[] = ['type' => 'closing', 'text' => 'Đang phân tích...'];

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="zdc-chi-tiet" role="tab">Chi Tiết</button>
                <button class="ftn-tab" data-tab="zdc-cach-tinh" role="tab">Cách Tính</button>
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
            <span class="ftn-btn-reset zdc-btn-reset">← Quay lại</span>
            <button type="button" id="zdc-btn-comment" class="zdc-btn-comment">Thảo Luận</button>
        </div>
        <p class="zdc-disclaimer" id="zdc-disclaimer" style="display:none;">
            ✦ Đây là kết quả tham khảo theo hệ thống Chiêm Tinh học. Mọi hành động và hướng đi tiếp theo nằm ở sự lựa chọn sáng suốt cũng như nỗ lực của bản thân.
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
                ob_start(); ?>
                <div class="ftn-analysis-wrap">
                    <div class="ftn-chat-wrap"><div class="ftn-chat-bubble"><div class="ftn-chat-body" id="zdc-love-chat-body" data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>"></div></div></div>
                </div>
                <div class="zdc-action-footer" style="display:none;">
                    <span class="ftn-btn-reset zdc-love-btn-reset">← Nhập lại</span>
                </div>
                <?php return ob_get_clean();
            }
        }

        $name1Safe = esc_html($love['name1'] ?? 'Bạn');
        $name2Safe = esc_html($love['name2'] ?? 'Người ấy');
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
            $lines[] = ['type' => 'block', 'text' => "» Có Troll troll VN không vây?"];
        }

        $matchHint = '';
        if (!empty($love['age_gap_msg'])) {
            $matchHint = "<strong style='color:#ef4444;'>{$love['age_gap_msg']}</strong>";
        }

        $lines = array_merge($lines, [
                ['type' => 'greeting', 'text' => 'Bản đồ chiêm tinh của ' . $name1Safe . ' và ' . $name2Safe . ':'],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'index', 'key' => 'sign1', 'label' => $name1Safe, 'value' => 'Cung ' . ($love['sign1_name'] ?? '') . ' (' . ($sign1['element'] ?? '') . ')', 'hint' => ''],
                ['type' => 'index', 'key' => 'sign2', 'label' => $name2Safe, 'value' => 'Cung ' . ($love['sign2_name'] ?? '') . ' (' . ($sign2['element'] ?? '') . ')', 'hint' => ''],
                ['type' => 'divider', 'text' => ''],

                ['type' => 'index', 'key' => 'pair_aspect', 'label' => '1. Khoảng cách (Góc chiếu)', 'value' => $analysis['aspect_label'] ?? '', 'hint' => $analysis['aspect_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_element', 'label' => '2. Nguyên tố', 'value' => ($sign1['element'] ?? '') . ' × ' . ($sign2['element'] ?? ''), 'hint' => $analysis['element_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_quality', 'label' => '3. Đặc tính', 'value' => ($analysis['mod_1'] ?? '') . ' × ' . ($analysis['mod_2'] ?? ''), 'hint' => $analysis['mod_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_polarity', 'label' => '4. Phân cực', 'value' => ($analysis['pol_1'] ?? '') . ' × ' . ($analysis['pol_2'] ?? ''), 'hint' => $analysis['pol_hint'] ?? ''],
                ['type' => 'index', 'key' => 'pair_planet', 'label' => '5. Hành tinh & Decan', 'value' => !empty($analysis['planet_match']) ? 'Có liên kết sâu' : 'Độc lập', 'hint' => $analysis['planet_hint'] ?? ''],
                ['type' => 'divider', 'text' => ''],
                ['type' => 'index', 'key' => 'match', 'label' => 'Độ tương hợp', 'value' => ($love['final_percent'] ?? $analysis['score'] ?? 0) . '%', 'hint' => $matchHint],
                ['type' => 'divider', 'text' => '']
        ]);

        foreach ($analysis['strengths'] ?? [] as $s) { $lines[] = ['type' => 'block', 'text' => '✅ ' . $s]; }
        foreach ($analysis['challenges'] ?? [] as $c) { $lines[] = ['type' => 'block', 'text' => '⚠️ ' . $c]; }
        $lines[] = ['type' => 'closing', 'text' => 'Đang phân tích...'];

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-love-analysis-wrap">
            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="chat" role="tab">Kết Quả</button>
                <button class="ftn-tab" data-tab="co-so-luan-giai" role="tab">Cách Tính</button>
            </div>
            <div class="ftn-tab-pane active" id="zdc-love-tab-chat">
                <div class="ftn-chat-wrap">
                    <div class="ftn-chat-bubble">
                        <div class="ftn-chat-body" id="zdc-love-chat-body" data-lines="<?= esc_attr(json_encode($lines, JSON_UNESCAPED_UNICODE)) ?>"></div>
                    </div>
                </div>
                <?php if (get_option('bb_zodiac_allow_ai', '0') === '1' && $showAiBtn): ?>
                    <div id="zdc-love-deep-analysis-form" class="ftn-form-deep" style="display:none;">
                        <p>Tiếp tục phân tích <strong>để hiểu sâu hơn</strong> về sự kết nối và tiềm năng của hai người.</p>
                        <input type="text" id="zdc-love-cbsp-deep" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button class="ftn-btn-submit" id="zdc-love-btn-deep-analyze">
                            <span class="ftn-btn-text">Phân tích</span>
                            <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang phân tích...</span>
                        </button>
                        <span class="zdc-error zdc-err-analyze"></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ftn-tab-pane" id="zdc-love-tab-co-so-luan-giai">
                <div class="zdc-calc-breakdown">
                    <h4>Cơ sở phân tích 5 lớp (Synastry)</h4>
                    <p>Hệ thống không đánh giá cảm quan mà sử dụng 5 phương pháp nền tảng trong chiêm tinh học để tính toán mức độ hòa hợp:</p>
                    <ul style="padding-left: 20px; line-height: 1.6;">
                        <li><strong>1. Góc chiếu (Aspects):</strong> Tính khoảng cách giữa 2 cung trên vòng tròn 360 độ để xác định chúng tạo thành góc Tam hợp (tốt), Vuông góc (căng thẳng) hay Đối đỉnh (bù trừ).</li>
                        <li><strong>2. Nhóm Nguyên Tố (Elements):</strong> Xét phản ứng hóa học giữa Lửa - Đất - Khí - Nước. Tương sinh sẽ được cộng điểm, tương khắc sẽ bị trừ điểm cơ bản.</li>
                        <li><strong>3. Đặc Tính (Modalities):</strong> Xét phong cách hành động (Thống Lĩnh, Kiên Định, Linh Hoạt). Cùng đặc tính dễ tạo ra xung đột, khác đặc tính dễ bề bù trừ.</li>
                        <li><strong>4. Hệ Âm - Dương (Polarity):</strong> Xét dòng chảy năng lượng. Cùng hệ (Dương-Dương hoặc Âm-Âm) dễ đồng cảm hơn việc lệch hệ.</li>
                        <li><strong>5. Hành Tinh & Phân Cung (Rulers & Decans):</strong> Đây là bước phân tích sâu. Hệ thống kiểm tra Hành tinh chủ quản và Hành tinh phụ (Decan theo ngày sinh cụ thể) xem có sự liên kết ngầm nào với đối phương hay không.</li>
                    </ul>

                    <h4>Xác định Cung Hoàng Đạo (<?= $name1Safe ?>)</h4>
                    <p>Dựa vào ngày sinh <strong><?= esc_html($dob1Safe) ?></strong>, hệ thống đối chiếu với bảng <strong>12 cung hoàng đạo Tây phương</strong> (Tropical Zodiac) để xác định cung phù hợp với ngày/tháng sinh.</p>
                    <p><strong>Cách tính:</strong> Hệ thống lấy tháng và ngày sinh, sau đó đối chiếu với khoảng ngày tiêu chuẩn của 12 cung (Bạch Dương: 21/3-19/4, Kim Ngưu: 20/4-20/5, Xử Nữ: 23/8-22/9, v.v.). Nếu ngày sinh nằm trong khoảng của cung nào, người đó thuộc cung đó.</p>
                    <p>→ Kết quả: <strong><?= esc_html((string)($love['sign1_name'] ?? '')) ?></strong></p>

                    <h4>Xác định Cung Hoàng Đạo (<?= $name2Safe ?>)</h4>
                    <p>Dựa vào ngày sinh <strong><?= esc_html($dob2Safe) ?></strong>, hệ thống đối chiếu với bảng <strong>12 cung hoàng đạo Tây phương</strong> (Tropical Zodiac) để xác định cung phù hợp với ngày/tháng sinh.</p>
                    <p><strong>Cách tính:</strong> Tương tự như trên, hệ thống lấy tháng và ngày sinh và đối chiếu với khoảng ngày tiêu chuẩn của 12 cung để xác định cung hoàng đạo tương ứng.</p>
                    <p>→ Kết quả: <strong><?= esc_html((string)($love['sign2_name'] ?? '')) ?></strong></p>

                    <h4>Đối chiếu Nguyên Tố</h4>
                    <p>Hai cung được đặt vào nhóm nguyên tố tương ứng: <strong><?= esc_html((string)($love['element1'] ?? '?')) ?></strong> và <strong><?= esc_html((string)($love['element2'] ?? '?')) ?></strong>.</p>
                    <p>Hệ thống đánh giá mức độ bổ trợ/xung khắc từ tương tác nguyên tố (Lửa - Đất - Khí - Nước) để tạo trục nền cho phân tích tình cảm.</p>

                    <h4>Đối chiếu Tính Chất & Phân Cực</h4>
                    <p>Tính chất của hai cung: <strong><?= esc_html((string)($sign1['quality'] ?? '?')) ?></strong> × <strong><?= esc_html((string)($sign2['quality'] ?? '?')) ?></strong>.</p>
                    <p>Phân cực năng lượng: <strong><?= esc_html((string)($sign1['polarity'] ?? '?')) ?></strong> × <strong><?= esc_html((string)($sign2['polarity'] ?? '?')) ?></strong>.</p>

                    <h4>Tổng hợp độ tương hợp</h4>
                    <p>Từ các lớp dữ liệu trên, tổng hợp tỷ lệ tương hợp: <strong style="color: red"><?= esc_html((string)($analysis['score'] ?? 0)) ?>%</strong>.</p>
                </div>
            </div>
        </div>
        <div class="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-love-btn-reset">← Quay lại</span>
            <button type="button" id="zdc-btn-comment" class="zdc-btn-comment">Thảo Luận</button>
        </div>
        <p class="zdc-disclaimer" id="zdc-disclaimer" style="display:none;">
            ✦ Đây là kết quả tham khảo theo hệ thống Chiêm Tinh học. Mọi hành động và hướng đi tiếp theo nằm ở sự lựa chọn sáng suốt cũng như nỗ lực của bản thân.
        </p>
        <?php
        return ob_get_clean();
    }

    private static function signName(string $id): string {
        $map = ['aries' => 'Bạch Dương ♈', 'taurus' => 'Kim Ngưu ♉', 'gemini' => 'Song Tử ♊', 'cancer' => 'Cự Giải ♋', 'leo' => 'Sư Tử ♌', 'virgo' => 'Xử Nữ ♍', 'libra' => 'Thiên Bình ♎', 'scorpio' => 'Thiên Yết ♏', 'sagittarius' => 'Nhân Mã ♐', 'capricorn' => 'Ma Kết ♑', 'aquarius' => 'Bảo Bình ♒', 'pisces' => 'Song Ngư ♓'];
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
            <h4>1. Phép tính Cung Hoàng Đạo</h4>
            <ul style="padding-left: 20px; line-height: 1.6; margin-bottom: 15px;">
                <li><strong>Ngày sinh:</strong> <?= esc_html($dob) ?></li>
                <li><strong>Trích xuất:</strong> Ngày = <?= esc_html($day) ?>, Tháng = <?= esc_html($month) ?> (Không tính năm sinh)</li>
                <li><strong>Đối chiếu:</strong> Tọa độ [<?= esc_html($day) ?>/<?= esc_html($month) ?>] khớp với dải định mức từ [<?= esc_html($startDisp) ?>] đến [<?= esc_html($endDisp) ?>]</li>
                <li><strong>Kết quả:</strong> &#10142; <strong><?= esc_html($signName) ?></strong></li>
            </ul>

            <h4>2. Phép tính Decan (Phân cung)</h4>
            <ul style="padding-left: 20px; line-height: 1.6; margin-bottom: 15px;">
                <li><strong>Khung chia:</strong> Chu kỳ 30 ngày của <?= esc_html($signName) ?> được chia làm 3 khoảng (mỗi khoảng 10 ngày).</li>
                <li><strong>Định vị:</strong> Tọa độ [<?= esc_html($day) ?>/<?= esc_html($month) ?>] rơi vào khoảng 10 ngày thứ <?= esc_html((string)$decan) ?>.</li>
                <li><strong>Kết quả:</strong> &#10142; <strong>Decan <?= esc_html((string)$decan) ?></strong></li>
            </ul>

            <?php if ($hasCusp): ?>
                <h4>3. Kiểm tra Giao Đỉnh (Cusp)</h4>
                <ul style="padding-left: 20px; line-height: 1.6;">
                    <li><strong>Điều kiện biên:</strong> [<?= esc_html($day) ?>/<?= esc_html($month) ?>] nằm trong sai số 3-5 ngày chuyển giao giữa 2 chòm sao.</li>
                    <li><strong>Trạng thái:</strong> TRUE (Kích hoạt pha trộn năng lượng)</li>
                    <li><strong>Kết quả:</strong> &#10142; <strong>Giao đỉnh <?= esc_html($sign['cusp_name'] ?? '') ?></strong></li>
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
        $lines[] = ['type' => 'greeting', 'text' => 'Cung hoàng đạo của ' . $name . ':'];
        $lines[] = ['type' => 'index', 'key' => 'sign_name', 'label' => 'Cung chủ quản', 'value' => $name . ' ' . $symbol, 'hint' => '— ' . ($sign['keywords'] ?? '')];
        $lines[] = ['type' => 'index', 'key' => 'element', 'label' => 'Nguyên tố', 'value' => $sign['element'] ?? ''];
        $lines[] = ['type' => 'index', 'key' => 'planet', 'label' => 'Hành tinh', 'value' => $sign['planet'] ?? ''];
        $lines[] = ['type' => 'index', 'key' => 'quality', 'label' => 'Tính chất', 'value' => $sign['quality'] ?? ''];
        $lines[] = ['type' => 'index', 'key' => 'polarity', 'label' => 'Phân cực', 'value' => $sign['polarity'] ?? ''];

        $compat = $sign['compatibility'] ?? [];
        if (!empty($compat)) {
            $lines[] = ['type' => 'divider', 'text' => ''];
            $lines[] = ['type' => 'section', 'text' => 'Tương Hợp Năng Lượng'];

            if (!empty($compat['best_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_best', 'label' => 'Hợp Nhất', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['best_match'])), 'hint' => ''];
            }
            if (!empty($compat['karmic_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_karmic', 'label' => 'Duyên Nghiệp', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['karmic_match'])), 'hint' => ''];
            }
            if (!empty($compat['worst_match'])) {
                $lines[] = ['type' => 'index', 'key' => 'compat_worst', 'label' => 'Khắc Nhất', 'value' => implode(', ', array_map([self::class, 'signName'], $compat['worst_match'])), 'hint' => ''];
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
                        <div class="zdc-hero-badge">Thông điệp chính</div>
                        <?php foreach ($horoscopeParagraphs as $item): ?>
                            <p class="zdc-hero-text"><strong><?= esc_html($item['label']) ?> — </strong><?= $item['text'] ?></p>
                        <?php endforeach; ?>
                    </div>

                    <div class="zdc-energy-feedback">
                        <p class="zdc-feedback-title">Tần số này có tương xứng với bạn?</p>
                        <div class="zdc-feedback-options" data-primary="<?= esc_attr($primaryDomain) ?>"
                             data-sign="<?= esc_attr($signId) ?>" data-period="<?= esc_attr($period) ?>">
                            <button class="zdc-energy-btn" data-type="high">Chạm đến tâm thức</button>
                            <button class="zdc-energy-btn" data-type="mid">Đang chiêm nghiệm</button>
                            <button class="zdc-energy-btn" data-type="low">Chưa chung tần số</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-tuvi-btn-reset">← Đổi chòm sao khác</span>
        </div>
        <p class="zdc-disclaimer" style="display:none;">
            ✦ Nội dung luận giải mang tính tham khảo để bạn có thêm góc nhìn. Lựa chọn và quyết định thực tế luôn thuộc về chính bạn.
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
        $domElem = $natalChart['dominant_element'] ?? 'Lửa';

        // Ngôn ngữ luận giải học thuật
        $elemDesc = [
                'Lửa'  => 'thiên hướng hành động, nhiệt huyết, khao khát khẳng định bản thân và tính tiên phong.',
                'Đất'  => 'tư duy thực tế, sự kiên nhẫn, nhu cầu xây dựng nền tảng vững chắc và hướng tới sự ổn định.',
                'Khí'  => 'tư duy lý trí, khách quan, khát khao tự do và nhu cầu không ngừng kết nối, trao đổi thông tin.',
                'Nước' => 'trực giác nhạy bén, chiều sâu cảm xúc thầm kín, sự thấu cảm và nhu cầu gắn kết tinh thần.'
        ];

        $allow_ai = get_option('bb_zodiac_allow_ai', '0') === '1';

        $lines = [
                ['type' => 'greeting', 'text' => 'Bản đồ sao cá nhân đã được khởi tạo.'],
                ['type' => 'intro', 'text' => "Cơ sở dữ liệu thiên văn tại tọa độ <strong>" . esc_html( mb_convert_case($data['pob'], MB_CASE_TITLE, "UTF-8") ) . "</strong> vào ngày <strong>" . esc_html($data['dob']) . "</strong>" . ($data['tob'] ? ' (' . esc_html($data['tob']) . ')' : '') . ":"],
                ['type' => 'divider', 'text' => ''],

                ['type' => 'section', 'text' => 'PHÂN TÍCH CẤU TRÚC CỐT LÕI (BIG 3)'],

                ['type' => 'block', 'text' => "<strong>Mặt Trời {$sun['sign']} (Sun Sign - Ý thức & Bản ngã):</strong><br>Mang đặc tính của nguyên tố {$sun['element']}. Định hình tính cách cốt lõi và lăng kính bạn nhìn nhận thế giới qua sự: <em>" . ($sun['keywords'] ?? '') . "</em>."],

                ['type' => 'block', 'text' => "<strong>Mặt Trăng {$moon['sign']} (Moon Sign - Tiềm thức & Cảm xúc):</strong><br>Chi phối bởi nguyên tố {$moon['element']}. Quyết định bản năng phản ứng, thế giới nội tâm và nhu cầu an toàn cảm xúc thông qua sự: <em>" . ($moon['keywords'] ?? '') . "</em>."],

                ['type' => 'block', 'text' => "<strong>Cung Mọc {$ascendant['sign']} (Ascendant - Vị thế cá nhân):</strong><br>Năng lượng {$ascendant['element']} dẫn dắt. Đây là lớp mặt nạ xã hội, phong cách tiếp cận vấn đề và hình ảnh đầu tiên bạn kiến tạo trước tập thể: <em>" . ($ascendant['keywords'] ?? '') . "</em>."],

                ['type' => 'block', 'text' => "<strong>Nguyên Tố Chủ Đạo: {$domElem}</strong><br>Chiếm tỷ trọng lớn nhất trong biểu đồ sao cá nhân, hệ {$domElem} khiến cấu trúc tâm lý của bạn thiên về {$elemDesc[$domElem]}"]
        ];

        ob_start(); ?>
        <div class="ftn-analysis-wrap" id="zdc-natal-analysis-wrap">

            <div class="ftn-tabs" role="tablist">
                <button class="ftn-tab active" data-tab="zdc-natal-reading" role="tab">Tổng Quan</button>
                <button class="ftn-tab" data-tab="zdc-natal-wheel" role="tab">Biểu Đồ</button>
                <button class="ftn-tab" data-tab="zdc-natal-data" role="tab">Tọa Độ</button>
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
                        <p>💡 Để hiểu rõ hơn về bản thân, hãy tiếp tục khám phá sự tương tác giữa các hành tinh và giải mã vận mệnh.</p>
                        <input type="text" id="zdc-natal-cbsp-deep" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button class="ftn-btn-submit" id="zdc-natal-btn-analyze">
                            <span class="ftn-btn-text">Giải mã bản đồ</span>
                            <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang xử lý...</span>
                        </button>
                        <span class="zdc-error zdc-err-analyze"></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ftn-tab-pane" id="ftn-tab-zdc-natal-wheel">
                <p class="zdc-wheel-intro">
                    <strong>Cấu trúc biểu đồ:</strong> Vòng ngoài biểu thị 12 cung hoàng đạo, vòng trong biểu thị 12 cung địa bàn (Houses). Các đường nối tại tâm thể hiện góc chiếu (Aspects) cấu thành nên cấu trúc tâm lý cá nhân.
                </p>
                <div class="zdc-wheel-wrap">
                    <canvas id="zdc-natal-canvas" width="460" height="460"></canvas>
                    <div id="zdc-wheel-tooltip" class="zdc-wheel-tooltip"></div>
                </div>
                <div class="zdc-wheel-legend">
                    <div class="zdc-wheel-legend-elements">
                        <span><i class="zdc-wheel-legend-dot zdc-bg-fire"></i> Lửa</span>
                        <span><i class="zdc-wheel-legend-dot zdc-bg-earth"></i> Đất</span>
                        <span><i class="zdc-wheel-legend-dot zdc-bg-air"></i> Khí</span>
                        <span><i class="zdc-wheel-legend-dot zdc-bg-water"></i> Nước</span>
                    </div>
                    <div class="zdc-wheel-legend-planets">
                        <span>Sun</span><span>Moon</span><span>Mercury</span><span>Venus</span><span>Mars</span>
                        <span>Jupiter</span><span>Saturn</span><span>Uranus</span><span>Neptune</span><span>Pluto</span>
                        <br>
                        <span class="zdc-text-asc">ASC</span> (Cung Mọc) &nbsp;|&nbsp;
                        <span class="zdc-text-mc">MC</span> (Thiên Đỉnh)
                    </div>
                </div>
            </div>

            <div class="ftn-tab-pane" id="ftn-tab-zdc-natal-data">

                <h4 class="zdc-data-heading">Hành Tinh Chiêm Tinh (Planets)</h4>
                <div class="zdc-data-grid">
                    <?php foreach ($planets as $key => $p): ?>
                        <div class="zdc-data-card">
                            <div class="zdc-data-card-title"><?= $p['symbol'] ?> <?= $p['name'] ?></div>
                            <div class="zdc-data-card-val"><?= $p['sign'] ?> <?= $p['degree_formatted'] ?></div>
                            <div class="zdc-data-card-meta"><?= $p['element'] ?> • <?= $p['modality'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h4 class="zdc-data-heading zdc-mt-4">Các Điểm Quan Trọng (Angles)</h4>
                <div class="zdc-data-grid">
                    <div class="zdc-data-card zdc-border-asc">
                        <div class="zdc-data-card-title">Ascendant (Cung Mọc)</div>
                        <div class="zdc-data-card-val"><?= $ascendant['sign'] ?> <?= $ascendant['degree_formatted'] ?></div>
                        <div class="zdc-data-card-meta">Vỏ bọc & Giao tiếp xã hội</div>
                    </div>
                    <div class="zdc-data-card zdc-border-mc">
                        <div class="zdc-data-card-title">Midheaven (Thiên Đỉnh)</div>
                        <div class="zdc-data-card-val"><?= $midheaven['sign'] ?> <?= $midheaven['degree_formatted'] ?></div>
                        <div class="zdc-data-card-meta">Sự nghiệp & Danh tiếng</div>
                    </div>
                </div>

                <div class="zdc-data-grid-2 zdc-mt-4">
                    <div>
                        <h4 class="zdc-data-heading">Phân Bổ Nguyên Tố</h4>
                        <ul class="zdc-data-list">
                            <?php
                            $total = array_sum($elementDist);
                            foreach ($elementDist as $el => $cnt):
                                $pct = $total > 0 ? round(($cnt / $total) * 100) : 0;
                                ?>
                                <li><strong><?= $el ?>:</strong> <?= $cnt ?> hành tinh (<?= $pct ?>%)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="zdc-data-heading">Phân Bổ Đặc Tính</h4>
                        <ul class="zdc-data-list">
                            <?php
                            $modNames = ['Cardinal' => 'Thống Lĩnh', 'Fixed' => 'Kiên Định', 'Mutable' => 'Linh Hoạt'];
                            foreach ($modalityDist as $mod => $cnt):
                                $pct = $total > 0 ? round(($cnt / $total) * 100) : 0;
                                ?>
                                <li><strong><?= $modNames[$mod] ?? $mod ?>:</strong> <?= $cnt ?> hành tinh (<?= $pct ?>%)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

        <div class="zdc-action-footer" style="display:none;">
            <span class="ftn-btn-reset zdc-natal-btn-reset">← Nhập lại dữ liệu</span>
        </div>

        <p class="zdc-disclaimer" style="display:none;">
            * Lưu ý: Tọa độ Cung Mọc (Ascendant) và phân bổ Cung Nhà (Houses) có thể sai lệch đáng kể nếu giờ sinh cung cấp không chính xác.
        </p>
        <?php return ob_get_clean();
    }
}