
<?php
if (!defined('ABSPATH')) exit;

function iching_render_form($args = 'luchao'): string {
    $method = 'luchao';
    if (is_array($args) && !empty($args['method'])) {
        $method = sanitize_text_field($args['method']);
    }
    elseif (is_string($args) && !empty($args)) {
        $method = sanitize_text_field($args);
    }
    elseif (isset($_GET['method'])) {
        $method = sanitize_text_field($_GET['method']);
    }

    if ($method === 'maihoa') {
        $method = 'maihoa_time';
    }

    $titles = [
            'maihoa_time'   => ['🌸 Mai Hoa Dịch Số', 'Gieo Quẻ Theo Thời Gian', 'Sử dụng năm, tháng, ngày, giờ hiện tại, giờ động tâm để lập quẻ.'],
            'maihoa_number' => ['🌸 Mai Hoa Dịch Số', 'Gieo Quẻ Theo Con Số', 'Nhập dãy số bất kỳ liên quan đến bạn, nhìn thấy hoặc nghĩ đến (VD: số seri tiền, biển số xe...).'],
            'maihoa_object' => ['🌸 Mai Hoa Dịch Số', 'Gieo Quẻ Theo Ngoại Tượng', 'Nhập 2 con số tượng trưng cho sự vật, hiện tượng bạn quan sát được.'],
            'luchao'        => ['☯ Lục Hào Nạp Giáp', 'Gieo Quẻ 3 Đồng Xu', 'Thành tâm gieo 3 đồng xu 6 lần để lập quẻ và luận giải chi tiết.']
    ];

    $info = $titles[$method] ?? $titles['luchao'];
    $badge = $info[0];
    $title = $info[1];
    $subtitle = $info[2];

    ob_start();
    ?>
    <div class="ich-wrap" id="ich-wrap" data-method="<?= esc_attr($method) ?>">
        <div class="ich-step active" id="ich-step-input">
            <div class="ich-hero">
                <div class="ich-hero-svg-wrap">
                    <svg viewBox="0 0 900 400" width="100%" height="100%" preserveAspectRatio="xMidYMid slice">
                        <use href="#ich-hero-symbol"></use>
                    </svg>
                </div>
                <div class="ich-hero-content">
                    <div class="ich-hero-badge"><?= esc_html($badge) ?></div>
                    <h1 class="ich-hero-title"><?= esc_html($title) ?></h1>
                    <p class="ich-hero-sub"><?= esc_html($subtitle) ?></p>
                </div>
            </div>

            <div class="ich-input-section">
                <label class="ich-label">Trình bày rõ câu hỏi của bạn</label>
                <textarea id="ich-question" class="ich-input ich-textarea" placeholder="Ví dụ: Công việc tháng tới của tôi có tiến triển tốt không?" maxlength="500" rows="3"></textarea>
                <span class="ich-error" id="ich-err-question"></span>

                <div class="ich-dynamic-inputs ich-mt-20">
                    <?php if ($method === 'maihoa_number'): ?>
                        <label class="ich-label ich-label-sm">Nhập dãy số của bạn</label>
                        <input type="number" id="ich-number-input" class="ich-input ich-input-number" placeholder="Ví dụ: 56789, 1234..." maxlength="50">
                        <span class="ich-error" id="ich-err-number"></span>
                        <div class="ich-question-tips">
                            <div>💡 <strong>Gợi ý:</strong> Nhập nguyên dãy số bạn vừa nhìn thấy (VD: số seri tiền, biển số xe...). Hệ thống sẽ áp dụng đúng quy tắc Dịch lý để lập quẻ.</div>
                        </div>

                    <?php elseif ($method === 'maihoa_object'): ?>
                        <div class="ich-flex-gap-15">
                            <div class="ich-flex-1">
                                <label class="ich-label ich-label-sm">Số thứ nhất (Thượng Quái)</label>
                                <input type="number" id="ich-object-1" class="ich-input ich-input-object" placeholder="VD: Số lượng, tiếng gõ...">
                            </div>
                            <div class="ich-flex-1">
                                <label class="ich-label ich-label-sm">Số thứ hai (Hạ Quái)</label>
                                <input type="number" id="ich-object-2" class="ich-input ich-input-object" placeholder="VD: Phương vị, số lượng 2...">
                            </div>
                        </div>
                        <span class="ich-error" id="ich-err-object"></span>
                        <div class="ich-question-tips">
                            <div>💡 <strong>Gợi ý:</strong> Số thứ nhất lấy theo số lượng vật tĩnh, tiếng động; Số thứ hai lấy theo phương hướng, thời gian...</div>
                        </div>

                    <?php elseif ($method === 'maihoa_time'): ?>
                        <label class="ich-label ich-label-sm">Thời điểm động tâm</label>
                        <?php $current_datetime = wp_date('d/m/Y H:i'); ?>
                        <input type="text" id="ich-time-input" class="ich-input ich-input-datetime" value="<?= esc_attr($current_datetime) ?>">
                        <span class="ich-error" id="ich-err-time"></span>
                        <div class="ich-question-tips">
                            <div>💡 <strong>Giờ động tâm:</strong><br> Là khoảnh khắc bạn khởi lên ý niệm hoặc thắc mắc trong đầu (giờ động tâm). Mặc định là thời gian hiện tại, bạn có thể chỉnh sửa lại cho đúng.</div>
                        </div>

                    <?php else: ?>
                        <div class="ich-question-tips">
                            <div><strong>💡 Lục Hào phù hợp với:</strong></div>
                            <div>• Hãy viết câu hỏi <strong>đủ ý, trọng tâm</strong> vào vấn đề của bạn.</div>
                            <div>• <strong>Hỏi cho bản thân:</strong> "Công việc năm <?= date("Y") ?> của tôi có thuận lợi không?"</div>
                            <div>• <strong>Hỏi cho người khác:</strong> Nên ghi rõ mối quan hệ (bạn bè, người thân, đồng nghiệp...)</div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <?php wp_nonce_field('iching_nonce', 'ich_nonce'); ?>
            <input type="hidden" id="ich-topic" value="general">
            <input type="hidden" id="ich-hp-trap" name="ich-hp-trap" value="">

            <button class="ich-submit-btn" id="ich-btn-submit-form">
                <span class="ich-btn-text"><?= ($method === 'luchao') ? 'Gieo quẻ' : 'Lập Quẻ' ?></span>
                <span class="ich-btn-loading"><span class="ich-spinner"></span> Đang xử lý...</span>
            </button>

            <div class="ich-back-link-wrap">
                <a href="/que-kinh-dich/" class="ich-back-link">← Trở về trước</a>
            </div>
        </div>

        <div class="ich-step" id="ich-step-toss">
            <div class="ich-step-header">
                <button class="ich-back-btn" data-back="input">← Quay lại</button>
                <span class="ich-step-label">☯ Lập Quẻ Lục Hào</span>
            </div>

            <div class="ich-toss-layout">
                <div class="ich-toss-left">
                    <div class="ich-toss-left-inner">
                        <div class="ich-toss-progress">
                            <span class="ich-toss-step-label">Lần tung</span>
                            <span class="ich-toss-counter">
                                <span id="ich-toss-num">1</span><span class="ich-toss-total"> / 6</span>
                            </span>
                        </div>
                        <div class="ich-coins-container">
                            <div class="ich-coin" id="ich-coin-1"></div>
                            <div class="ich-coin" id="ich-coin-2"></div>
                            <div class="ich-coin" id="ich-coin-3"></div>
                        </div>
                        <div id="ich-toss-result-hint" class="ich-toss-hint"></div>
                        <div id="ich-toss-action">
                            <button class="ich-submit-btn ich-btn-toss-main" id="ich-btn-toss">
                                <span class="ich-btn-text">Tung Lần 1</span>
                                <span class="ich-btn-loading"><span class="ich-spinner"></span> Đang tung...</span>
                            </button>
                        </div>

                        <p class="ich-toss-instruction-small" id="ich-toss-instruction">
                            Tung 3 xu để lập từng hào, từ Hào 1 đến Hào 6
                        </p>

                    </div>
                </div>
                <div class="ich-toss-right">
                    <div class="ich-toss-right-header">
                        <span class="ich-toss-right-title">Lục Hào</span>
                        <span class="ich-toss-right-sub">Hiển thị từ Hào 1 (dưới) → Hào 6 (trên)</span>
                    </div>

                    <div class="ich-hexagram-builder">
                        <?php
                        $hao_names = ['Sơ', 'Nhị', 'Tam', 'Tứ', 'Ngũ', 'Thượng'];
                        for ($i = 5; $i >= 0; $i--):
                            ?>
                            <div class="ich-line-slot" data-index="<?= $i ?>">
                                <span class="ich-line-label">Hào <?= $i + 1 ?> (<?= $hao_names[$i] ?>)</span>
                                <div class="ich-line-draw"></div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="ich-hao-legend">
                        <span class="ich-legend-item ich-legend-yang">— Dương</span>
                        <span class="ich-legend-item ich-legend-yin">— Âm</span>
                        <span class="ich-legend-item ich-legend-change">○ Hào động</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="ich-result-box" class="ich-result-container"></div>
    </div>
    <?php return ob_get_clean();
}
