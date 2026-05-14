<?php

if (!defined('ABSPATH')) exit;

function western_render(string $name, string $topic, array $cards, string $mode = 'topic', string $question = '', string $spread_key = '3_cards'): string {
    $spreads        = Western_Calc::getSpreads();
    $spread_config  = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions      = $spread_config['positions'];

    $suit_labels  = ['hearts' => 'Cơ ♥', 'diamonds' => 'Rô ♦', 'clubs' => 'Chuồn ♣', 'spades' => 'Bích ♠'];
    $suit_colors  = ['hearts' => '#ef4444', 'diamonds' => '#f59e0b', 'clubs' => '#10b981', 'spades' => '#6366f1'];
    $topic_labels = ['love' => 'Tình yêu', 'career' => 'Công việc', 'finance' => 'Tài chính', 'study' => 'Học tập', 'health' => 'Sức khỏe', 'future' => 'Tương lai'];
    $colors_palette = ['#8b5cf6', '#d4af37', '#10b981', '#f43f5e', '#0ea5e9', '#f59e0b', '#ec4899'];

    $intro_text = ($mode === 'question' && !empty($question))
        ? 'Phương pháp: ' . $spread_config['name']
        : 'Chủ đề: ' . ($topic_labels[$topic] ?? $topic) . ' · ' . $spread_config['name'] . ':';

    $lines = [
            ['type' => 'greeting', 'text' => 'Thông điệp từ các lá bài:'],
            ['type' => 'divider',  'text' => ''],
    ];

    $color_idx = 0;
    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $hint_text = !empty($c['keywords']) ? implode(', ', $c['keywords']) : '';
        $lines[] = [
            'type'  => 'index',
            'key'   => $pos_key,
            'label' => $pos_label,
            'value' => $c['name_vi'],
            'color' => $colors_palette[$color_idx % count($colors_palette)],
            'hint'  => $hint_text
        ];
        $color_idx++;
    }
    $lines[] = ['type' => 'divider', 'text' => ''];
    $lines_json = json_encode($lines, JSON_UNESCAPED_UNICODE);

    ob_start(); ?>

    <?php if ($mode === 'question' && !empty($question)): ?>
        <div class="trt-context-badge">
            <span class="trt-context-icon">» </span>
            <span class="trt-context-text"><?= esc_html(mb_substr($question, 0, 120)) ?></span>
        </div>
    <?php elseif (!empty($topic)): ?>
        <div class="trt-context-badge">
            <span class="trt-context-icon">» </span>
            <span class="trt-context-text">Chủ đề: <?= esc_html($topic_labels[$topic] ?? $topic) ?></span>
        </div>
    <?php endif; ?>

    <div class="trt-chat-wrap" id="trt-chat-wrap">
        <div class="ast-chat-bubble">
            <div class="ast-chat-body" id="ast-chat-body" data-lines="<?= esc_attr($lines_json) ?>">
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
            $suit   = $c['suit'] ?? '';
            $pcolor = $suit_colors[$suit] ?? '#888';
            $kw     = implode(', ', $c['keywords'] ?? []);
            $suit   = $c['suit'] ?? '';
            ?>
            <div class="trt-card-detail">
                <div class="trt-cd-header" style="border-left:3px solid <?= esc_attr($pcolor) ?>">
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
            <?php $color_idx++; endforeach; ?>


        <?php $allow_ai = get_option('western_allow_ai', '0');
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