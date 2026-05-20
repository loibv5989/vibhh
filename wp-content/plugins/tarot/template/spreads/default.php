<?php

if (!defined('ABSPATH')) exit;

function tarot_default(string $topic, array $cards, string $mode = 'topic', string $question = '', string $spread_key = '3_cards'): string {
    $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $orient_symbol = ['upright' => '↑', 'reversed' => '↓'];
    $orient_label  = ['upright' => 'Upright', 'reversed' => 'Reversed'];
    $element_colors = ['fire' => '#ef4444', 'water' => '#3b82f6', 'air' => '#f59e0b', 'earth' => '#10b981'];
    $topic_labels  = ['love' => 'Love', 'career' => 'Career', 'finance' => 'Finance', 'study' => 'Study', 'health' => 'Health', 'future' => 'Future'];
    $colors_palette = ['#8b5cf6', '#d4af37', '#10b981', '#f43f5e', '#0ea5e9', '#f59e0b', '#ec4899', '#84cc16', '#14b8a6', '#6366f1'];

    if (($mode === 'question' || $mode === 'love') && !empty($question)) {
        $intro_text = 'Spread: ' . $spread_config['name'];
    } else {
        $intro_text = $spread_config['name'] . ':';
    }

    $lines = [
            ['type' => 'greeting', 'text' => $intro_text],
            ['type' => 'intro',    'text' => ''],
            ['type' => 'divider',  'text' => '']
    ];

    $color_idx = 0;
    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c  = $cards[$pos_key];
        $os = $orient_symbol[$c['orientation']] ?? '';
        $ol = $orient_label[$c['orientation']]  ?? '';
        $c_color = $colors_palette[$color_idx % count($colors_palette)];

        $lines[] = [
                'type'  => 'index',
                'key'   => $pos_key,
                'label' => $pos_label,
                'value' => $c['name'] . ' ' . $os . ' ' . $ol,
                'color' => $c_color,
        ];
        $color_idx++;
    }

    $lines[] = ['type' => 'divider',  'text' => ''];
    $lines_json = json_encode($lines, JSON_UNESCAPED_UNICODE);

    ob_start(); ?>

    <?php if (($mode === 'question' || $mode === 'love') && !empty($question)): ?>
        <div class="trt-context-badge">
            <span class="trt-context-icon">Question » </span>
            <span class="trt-context-text"><?= esc_html(mb_substr($question, 0, 120)) ?></span>
        </div>
    <?php elseif (!empty($topic)): ?>
        <div class="trt-context-badge">
            <span class="trt-context-icon">» </span>
            <span class="trt-context-text">Theme: <?= esc_html($topic_labels[$topic] ?? $topic) ?></span>
        </div>
    <?php endif; ?>

    <div class="trt-chat-wrap" id="trt-chat-wrap">
        <div class="trt-oracle-vision">
            <div class="trt-oracle-content">
                <div class="trt-oracle-header">
                    <span class="trt-moon">✦</span>
                    <span class="trt-oracle-title">Spread Overview</span>
                </div>
                <div class="ast-chat-body" id="ast-chat-body" data-lines="<?= esc_attr($lines_json) ?>">
                    <span class="ast-cursor">|</span>
                </div>
            </div>
        </div>
    </div>

    <div id="trt-detail-container" style="display:none">
        <p style="text-align: center; margin: 20px 0; font-size: 14px; color: #666;">Click a card to reveal its reading.</p>
        <div class="trt-cards-grid">
        <?php
        $color_idx = 0;
        foreach ($positions as $pos_key => $pos_label):
            if (!isset($cards[$pos_key])) continue;
            $c = $cards[$pos_key];
            $orient = $c['orientation'];
            $pcolor = $colors_palette[$color_idx % count($colors_palette)];
            $kw = implode(', ', $c['keywords'] ?? []);
            $is_major = $c['arcana'] === 'major';
            $element_symbols = ['fire' => '🔥', 'water' => '🌊', 'air' => '🌬️', 'earth' => '🌿'];
            $suit_symbols = ['wands' => '🕯️', 'cups' => '🏆', 'swords' => '⚔️', 'pentacles' => '⭐'];
            $el_symbol = $element_symbols[$c['element']] ?? '✧';
            ?>
            <article class="trt-tarot-card">
                <div class="trt-card-frame">
                    <div class="trt-card-corner trt-corner-tl"></div>
                    <div class="trt-card-corner trt-corner-tr"></div>
                    <div class="trt-card-corner trt-corner-bl"></div>
                    <div class="trt-card-corner trt-corner-br"></div>
                    
                    <header class="trt-card-top">
                        <div class="trt-card-position">
                            <span class="trt-pos-num"><?= esc_html($color_idx + 1) ?></span>
                            <span class="trt-pos-label"><?= esc_html($pos_label) ?></span>
                        </div>
                        <div class="trt-card-arcana <?= $is_major ? 'is-major' : 'is-minor' ?>">
                            <?= $is_major ? '★ Major Arcana' : '☆ Minor Arcana' ?>
                        </div>
                    </header>

                    <div class="trt-card-identity">
                        <div class="trt-card-numeral"><?= esc_html($c['number'] ?? '0') ?></div>
                        <h3 class="trt-card-name"><?= esc_html($c['name']) ?></h3>
                        <p class="trt-card-name-en"><?= esc_html($c['name']) ?></p>
                        <div class="trt-card-orientation <?= esc_attr($orient) ?>">
                            <span class="trt-orient-arrow"><?= $orient === 'upright' ? '↑' : '↓' ?></span>
                            <span><?= $orient === 'upright' ? 'Upright' : 'Reversed' ?></span>
                        </div>
                    </div>

                    <div class="trt-card-symbols">
                        <span class="trt-symbol" title="<?= esc_attr(ucfirst($c['element'])) ?>"><?= $el_symbol ?></span>
                        <?php if (!empty($c['astro_name'])): ?>
                        <span class="trt-symbol" title="<?= esc_attr($c['astro_name']) ?>">✨</span>
                        <?php endif; ?>
                        <?php if (!empty($c['suit'])): ?>
                        <span class="trt-symbol" title="<?= esc_attr(ucfirst($c['suit'])) ?>"><?= $suit_symbols[$c['suit']] ?? '✦' ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="trt-card-wisdom">
                        <div class="trt-wisdom-scroll">
                            <p><?= esc_html(!empty($c['description']) ? $c['description'] : ($c['meaning'] ?? '')) ?></p>
                        </div>
                        <?php if (!empty($kw)): ?>
                        <div class="trt-card-keywords">
                            <span class="trt-kw-icon">✦</span>
                            <span><?= esc_html($kw) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <footer class="trt-card-bottom">
                        <div class="trt-card-meta-row">
                            <?php if (!empty($c['timing'])): ?>
                            <div class="trt-meta-tag"><span>⏳</span><?= esc_html($c['timing']) ?></div>
                            <?php endif; ?>
                            <div class="trt-meta-tag"><span><?= $el_symbol ?></span><?= esc_html(ucfirst($c['element'])) ?></div>
                            <?php if (!empty($c['astro_name'])): ?>
                            <div class="trt-meta-tag"><span>✨</span><?= esc_html($c['astro_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($c['related_cards'])): ?>
                        <div class="trt-related">
                            <span class="trt-related-icon">🔗</span>
                            <span><?= esc_html(implode(' • ', array_map(function($card) { return ucwords(str_replace('_', ' ', $card)); }, $c['related_cards']))) ?></span>
                        </div>
                        <?php endif; ?>
                    </footer>
                </div>
            </article>
            <?php $color_idx++; endforeach; ?>
        </div>

        <?php if (get_option('tarot_allow_ai', '0') === '1'): ?>
        <div id="trt-deep-analyze-form">
            <h3>Interpret the Spread</h3>
            <p class="analyze-desc">A detailed interpretation of each card and their connections within the spread.</p>
            <div class="trt-input-section">
                <div class="trt-input-trap" aria-hidden="true">
                    <input type="text" id="trt-deep-trap" name="trt-deep-trap" tabindex="-1" autocomplete="off">
                </div>
                <input type="text" id="trt-deep-name" class="trt-input" placeholder="Your name..." maxlength="40">
                <span class="trt-error" id="trt-err-deep-name"></span>
            </div>
            <button class="trt-submit-btn" id="trt-btn-deep-analyze">
                <span class="trt-btn-text">Read the Cards</span>
                <span class="trt-btn-loading"><span class="trt-spinner"></span> Reading the cards...</span>
            </button>
            <span class="trt-error trt-error-analyze" id="trt-err-analyze"></span>
        </div>

        <div id="ast-analysis-wrap" style="display:none;">
            <div id="ast-final-result"></div>
        </div>
        <?php endif; ?>

        <div class="ast-action-footer" style="display:none;">
            <span id="ast-btn-comment" class="ast-btn-comment">Discussion</span>
            <span class="ast-reload" onclick="window.location.reload()">↺ New Reading</span>
        </div>

        <p class="trt-disclaimer" id="trt-disclaimer" style="display:none;">
            ✦ This reading is for guidance only. The choices you make and the efforts you put forth are entirely your own.
        </p>

    </div>
    <?php return ob_get_clean();
}