<?php
if (!defined('ABSPATH')) exit;


/** @var string $lines_json */
/** @var string $spread_key */
/** @var int $total_cards */
/** @var array $positions */
/** @var array $colors_palette */

?>

<div class="trt-chat-wrap" id="trt-chat-wrap">
    <div class="ast-chat-bubble">
        <div class="ast-chat-body" id="ast-chat-body"
             data-lines="<?= esc_attr($lines_json) ?>">
            <span class="ast-cursor">|</span>
        </div>
    </div>
</div>

<div id="trt-detail-container" style="display:none">

    <?php
    $color_idx = 0;
    foreach ($positions as $pos_key => $pos_label):
        if (!isset($cards[$pos_key])) continue;
        $c      = $cards[$pos_key];
        $pcolor = $colors_palette[$color_idx % count($colors_palette)];
        $kw     = implode(' · ', array_slice($c['keywords'] ?? [], 0, 3));
        $element = $c['element'] ?? '';
        ?>
        <div class="trt-card-detail">
            <div class="trt-cd-header" style="border-left:3px solid <?= esc_attr($pcolor) ?>">
                <span class="trt-cd-pos" style="color:<?= esc_attr($pcolor) ?>"><?= esc_html($pos_label) ?></span>
                <span class="trt-cd-name">
                            <?= esc_html($c['name_vi']) ?>
                            <small>(<?= esc_html($c['name_en']) ?>)</small>
                        </span>
                <?php if (!empty($element)): ?>
                    <span class="trt-badge-minor"><?= esc_html($element_labels[$element] ?? $element) ?></span>
                <?php endif; ?>
            </div>
            <div class="trt-cd-body">
                <?php if (!empty($c['light'])): ?>
                    <p><strong>✦ Ánh sáng:</strong> <?= esc_html($c['light']) ?></p>
                <?php endif; ?>
                <?php if (!empty($c['shadow'])): ?>
                    <p><strong>◆ Bóng tối:</strong> <?= esc_html($c['shadow']) ?></p>
                <?php endif; ?>
                <?php if (!empty($kw)): ?>
                    <p style="font-size:.8rem;margin-top:8px"><strong>🔑 Thông điệp: </strong> <?= esc_html($kw) ?></p>
                <?php endif; ?>
                <?php if (!empty($c['mantra'])): ?>
                    <p class="oracle-mantra" style="font-style:italic;margin-top:10px;padding:10px;border-left:2px solid <?= esc_attr($pcolor) ?>;font-size:.85rem">"<?= esc_html($c['mantra']) ?>"</p>
                <?php endif; ?>
            </div>
        </div>
        <?php $color_idx++; endforeach; ?>

    <?php
    $allow_ai  = get_option('bb_oracle_allow_ai', '0');
    if ($allow_ai === '1'): ?>
        <div id="trt-deep-analyze-form">
            <h3>Luận giải thông điệp</h3>
            <p style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:20px;">Tiếp tục phân tích, giải mã ý nghĩa thông điệp của lá bài.</p>
            <div class="trt-input-section">
                <div class="trt-input-trap" aria-hidden="true">
                    <input type="text" id="trt-deep-trap" name="trt-deep-trap" tabindex="-1" autocomplete="off">
                </div>
                <input type="text" id="trt-deep-name" class="trt-input" placeholder="Họ và tên của bạn..." maxlength="40">
                <span class="trt-error" id="trt-err-deep-name"></span>
            </div>
            <button class="trt-submit-btn" id="trt-btn-deep-analyze">
                <span class="trt-btn-text">Giải nghĩa</span>
                <span class="trt-btn-loading"><span class="trt-spinner"></span> Đang kết nối...</span>
            </button>
            <span class="trt-error trt-error-analyze" id="trt-err-analyze"></span>
        </div>
        <div id="ast-analysis-wrap" style="display:none;">
            <div id="ast-final-result" style="margin-top:20px;">
                <div class="ast-skeleton ast-sk-title"></div>
                <div class="ast-skeleton ast-sk-line"></div>
                <div class="ast-skeleton ast-sk-line ast-sk-short"></div>
                <div class="ast-skeleton ast-sk-line"></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="ast-action-footer" style="display:none;">
        <span id="ast-btn-comment" class="ast-btn-comment">Thảo Luận</span>
        <span class="ast-reload" onclick="window.location.reload()">↺ Rút bài khác</span>
    </div>

    <p class="trt-disclaimer" id="trt-disclaimer" style="display:none;">
        ✦ Đây là kết quả tham khảo theo hệ thống Oracle Cards. Mọi hành động và hướng đi tiếp theo nằm ở sự lựa chọn sáng suốt cũng như nỗ lực của bản thân.
    </p>

</div>
