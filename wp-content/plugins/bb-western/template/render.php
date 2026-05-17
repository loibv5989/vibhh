<?php

if (!defined('ABSPATH')) exit;

function western_render(string $name, string $topic, array $cards, string $mode = 'topic', string $question = '', string $spread_key = '3_cards'): string {
    $spreads        = Western_Calc::getSpreads();
    $spread_config  = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions      = $spread_config['positions'];

    $suit_labels  = ['hearts' => 'Cơ ♥', 'diamonds' => 'Rô ♦', 'clubs' => 'Chuồn ♣', 'spades' => 'Bích ♠'];
    $suit_colors  = ['hearts' => '#dc2626', 'diamonds' => '#dc2626', 'clubs' => '#1f2937', 'spades' => '#1f2937'];
    $suit_symbols = ['hearts' => '♥', 'diamonds' => '♦', 'clubs' => '♣', 'spades' => '♠'];
    $topic_labels = ['love' => 'Tình yêu', 'career' => 'Công việc', 'finance' => 'Tài chính', 'study' => 'Học tập', 'health' => 'Sức khỏe', 'future' => 'Tương lai'];

    ob_start(); ?>

    <div id="trt-detail-container" style="display:none">

        <div class="trt-card-spread">
        <?php
        $color_idx = 0;
        $rotations = [-4, 0, 4, -2, 2, -3, 3];
        $idx = 0;
        foreach ($positions as $pos_key => $pos_label):
            if (!isset($cards[$pos_key])) continue;
            $c      = $cards[$pos_key];
            $suit   = $c['suit'] ?? '';
            $pcolor = $suit_colors[$suit] ?? '#888';
            $kw     = implode(', ', $c['keywords'] ?? []);
            $rot    = $rotations[$idx % count($rotations)];
            $idx++;
            ?>
            <?php $sym = $suit_symbols[$suit] ?? ''; $rank = $c['rank'] ?? ''; ?>
            <div class="trt-card-detail" data-card-name="<?= esc_attr($c['name_vi']) ?>">
                <div class="trt-cd-visual trt-cd-<?= esc_attr($suit) ?> trt-cd-rank-<?= esc_attr($rank) ?>" role="button" tabindex="0" data-suit="<?= esc_attr($suit) ?>" style="--card-color:<?= esc_attr($pcolor) ?>;--card-rot:<?= $rot ?>deg">
                    <div class="trt-cv-corner trt-cv-corner-top">
                        <span class="trt-cv-rank"><?= esc_html($rank) ?></span>
                        <span class="trt-cv-suit-top"><?= esc_html($sym) ?></span>
                    </div>
                    <span class="trt-cv-suit-big"><?= esc_html($sym) ?></span>
                    <div class="trt-cv-corner trt-cv-corner-bot">
                        <span class="trt-cv-suit-bot"><?= esc_html($sym) ?></span>
                    </div>
                </div>
                <span class="trt-cd-pos-label"><?= esc_html($pos_label) ?></span>
                <div class="trt-cd-content">
                    <div class="trt-cd-header">
                        <span class="trt-cd-pos" style="color:<?= esc_attr($pcolor) ?>"><?= esc_html($pos_label) ?></span>
                        <span class="trt-cd-name"><?= esc_html($c['name_vi']) ?> <small style="font-weight:400">(<?= esc_html($c['name']) ?>)</small></span>
                        <?php if (!empty($suit)): ?>
                            <span class="trt-badge-minor" style="color:<?= esc_attr($suit_colors[$suit] ?? 'inherit') ?>"><?= esc_html($suit_labels[$suit] ?? $suit) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="trt-cd-body">
                        <p><?= esc_html($c['meaning'] ?? '') ?></p>
                        <?php if (!empty($kw)): ?><p style="font-size:.8rem">✦ Thông điệp: <em><?= esc_html($kw) ?></em></p><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php $color_idx++; endforeach; ?>
        </div>


        <?php
        $allow_ai = get_option('western_allow_ai', '0');
        if ($allow_ai === '1'):  ?>
        <div id="trt-deep-analyze-form">
            <h3>Luận giải các lá bài</h3>
            <p style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:20px;">Tiếp tục luận giải chi tiết ý nghĩa, sự liên kết giữa các lá bài.</p>
            <div class="trt-input-section">
                <div class="trt-input-trap" aria-hidden="true">
                    <input type="text" id="trt-deep-trap" name="trt-deep-trap" tabindex="-1" autocomplete="off">
                </div>
                <input type="text" id="trt-deep-name" class="trt-input" placeholder="Họ và tên của bạn..." maxlength="40">
                <span class="trt-error" id="trt-err-deep-name"></span>
            </div>
            <button class="trt-submit-btn" id="trt-btn-deep-analyze">
                <span class="trt-btn-text">Giải mã</span>
                <span class="trt-btn-loading"><span class="trt-spinner"></span> Đang giải mã...</span>
            </button>
            <span class="trt-error trt-error-analyze" id="trt-err-analyze"></span>
        </div>
        <?php endif; ?>

        <div id="ast-analysis-wrap" style="display:none;">
            <div id="ast-final-result" style="margin-top:20px;">
                <div class="ast-skeleton ast-sk-title"></div>
                <div class="ast-skeleton ast-sk-line"></div>
                <div class="ast-skeleton ast-sk-line ast-sk-short"></div>
                <div class="ast-skeleton ast-sk-line"></div>
            </div>
        </div>
    </div>
    <div class="ast-action-footer" style="display:none;">
        <span id="ast-btn-comment" class="ast-btn-comment">Thảo Luận</span>
        <span class="ast-reload" onclick="window.location.reload()">↺ Trải bài khác</span>
    </div>

    <p class="trt-disclaimer" id="trt-disclaimer" style="display:none;">
        ✦ Đây là kết quả tham khảo theo hệ thống Western Oracle. Mọi hành động và hướng đi tiếp theo nằm ở sự lựa chọn sáng suốt cũng như nỗ lực của bản thân.
    </p>

    <?php return ob_get_clean();
}