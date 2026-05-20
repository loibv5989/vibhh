<?php
/**
 * Template: 3 Cards Spread
 * Layout: Horizontal
 */

if (!defined('ABSPATH')) exit;

function tarot_three_cards(string $topic, array $cards, string $mode = 'topic', string $question = '', string $spread_key = '3_cards'): string {
    $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $orient_symbol = ['upright' => '↑', 'reversed' => '↓'];
    $orient_label  = ['upright' => 'Upright', 'reversed' => 'Reversed'];
    $topic_labels  = ['love' => 'Love', 'career' => 'Career', 'finance' => 'Finance', 'study' => 'Study', 'health' => 'Health', 'future' => 'Future'];
    $colors_palette = ['#8b5cf6', '#d4af37', '#10b981'];

    $element_symbols = ['fire' => '🔥', 'water' => '🌊', 'air' => '🌬️', 'earth' => '🌿'];
    $suit_symbols = ['wands' => '🕯️', 'cups' => '🏆', 'swords' => '⚔️', 'pentacles' => '⭐'];

    $intro_text = (($mode === 'question' || $mode === 'love') && !empty($question))
        ? 'Spread: ' . $spread_config['name']
        : $spread_config['name'] . ':';

    $lines = [
        ['type' => 'greeting', 'text' => $intro_text],
        ['type' => 'intro',    'text' => ''],
        ['type' => 'divider',  'text' => '']
    ];

    $color_idx = 0;
    $cards_data = [];
    $idx = 0;

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

        $cards_data[] = [
            'idx'         => $idx,
            'pos_key'     => $pos_key,
            'pos_label'   => $pos_label,
            'card'        => $c,
            'el_symbol'   => $element_symbols[$c['element'] ?? ''] ?? '✧',
            'suit_symbol' => $suit_symbols[$c['suit'] ?? ''] ?? '✦',
            'is_major'    => ($c['arcana'] ?? '') === 'major',
            'kw'          => implode(', ', $c['keywords'] ?? []),
        ];

        $color_idx++;
        $idx++;
    }

    $lines[] = ['type' => 'divider',  'text' => ''];
    $lines_json = json_encode($lines, JSON_UNESCAPED_UNICODE);

    ob_start();
    ?>

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

    <div class="trt-chat-wrap trt-cc-chat" id="trt-chat-wrap">
        <div class="trt-oracle-vision">
            <div class="trt-oracle-stars"></div>
            <div class="trt-oracle-content">
                <div class="trt-oracle-divider"><span></span><span class="trt-star">✦</span><span></span></div>
                <div class="ast-chat-body" id="ast-chat-body" data-lines="<?= esc_attr($lines_json) ?>">
                    <span class="ast-cursor">|</span>
                </div>
            </div>
        </div>
    </div>

    <div id="trt-detail-container" style="display:none">
        <p style="text-align: center; margin: 20px 0; font-size: 14px; color: #666;">Click a card to reveal its reading.</p>
        <div class="trt-3cards-area">
            <?php foreach ($cards_data as $i => $data):
                $c = $data['card'];
                $slot_num = $i + 1;
                $orient = $c['orientation'];
                $el_class = 'trt-el-' . ($c['element'] ?? 'earth');
                ?>
                <div class="trt-3c-slot trt-3c-slot-<?= $slot_num ?>">
                    <article class="trt-cc-card <?= $el_class ?>" data-card-idx="<?= $data['idx'] ?>">
                        <div class="trt-card-frame <?= $orient === 'reversed' ? 'is-reversed' : '' ?>">
                            <img src="<?= esc_url($c['image_url'] ?? '') ?>" alt="<?= esc_attr($c['name']) ?>" class="trt-card-image" loading="lazy">
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
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

    <div class="trt-overlay" id="trtOverlay">
        <div class="trt-modal" id="trtModal">
            <button class="trt-modal-close" id="trtModalClose">✕</button>
            <div class="trt-modal-head">
                <div class="trt-modal-symbol" id="trtMSym"></div>
                <div class="trt-modal-titles">
                    <div class="trt-modal-pos" id="trtMPos"></div>
                    <div class="trt-modal-name" id="trtMName"></div>
                    <div class="trt-modal-name-vi" id="trtMNameVi"></div>
                </div>
            </div>
            <div class="trt-modal-dir-row">
                <span class="trt-modal-dir" id="trtMDir"></span>
                <span class="trt-modal-kw" id="trtMKw"></span>
            </div>
            <div class="trt-modal-rule"></div>
            <div class="trt-modal-body">
                <div class="trt-modal-timing" id="trtMTiming"></div>
                <div class="trt-modal-desc" id="trtMDesc"></div>
                <div class="trt-modal-grid" id="trtMGrid"></div>
                <div id="trtMLinks"></div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const TRT_CARDS_DATA = <?= json_encode(array_map(function($d) {
                $c = $d['card'];
                return [
                    'pos' => sprintf('%02d · %s', $d['idx'] + 1, $d['pos_label']),
                    'sym' => $d['el_symbol'],
                    'name' => $c['name'],
                    'dir' => $c['orientation'] === 'upright' ? '↑ Upright' : '↓ Reversed',
                    'dirCls' => $c['orientation'],
                    'kw' => $d['kw'],
                    'timing' => $c['timing'] ?? '',
                    'desc' => $c['description'] ?? $c['meaning'] ?? '',
                    'element' => ucfirst($c['element'] ?? '') . ' ' . $d['el_symbol'],
                    'planet' => $c['astro_name'] ?? '',
                    'arcana' => ($c['arcana'] ?? '') === 'major' ? 'Major Arcana' : 'Minor Arcana',
                    'themes' => implode(', ', $c['themes'] ?? []),
                    'links' => $c['related_cards'] ?? [],
                ];
            }, $cards_data), JSON_UNESCAPED_UNICODE) ?>;

            function trtOpenModal(idx) {
                const c = TRT_CARDS_DATA[idx];
                if (!c) return;

                document.getElementById('trtMSym').textContent = c.sym;
                document.getElementById('trtMPos').textContent = c.pos;
                document.getElementById('trtMName').textContent = c.name;
                document.getElementById('trtMNameVi').textContent = '';

                const dirEl = document.getElementById('trtMDir');
                dirEl.textContent = c.dir;
                dirEl.className = 'trt-modal-dir trt-card-orientation ' + c.dirCls;

                document.getElementById('trtMKw').textContent = '✦ ' + c.kw;
                document.getElementById('trtMTiming').textContent = c.timing ? '⏳ ' + c.timing : '';
                document.getElementById('trtMTiming').style.display = c.timing ? '' : 'none';
                document.getElementById('trtMDesc').textContent = c.desc;

                document.getElementById('trtMGrid').innerHTML = `
                <div class="trt-modal-info"><div class="trt-modal-info-label">Element</div><div class="trt-modal-info-val">${c.element}</div></div>
                <div class="trt-modal-info"><div class="trt-modal-info-label">Planet / Sign</div><div class="trt-modal-info-val">${c.planet || '—'}</div></div>
                <div class="trt-modal-info"><div class="trt-modal-info-label">Arcana</div><div class="trt-modal-info-val">${c.arcana}</div></div>
                ${c.themes ? `<div class="trt-modal-info"><div class="trt-modal-info-label">Themes</div><div class="trt-modal-info-val">${c.themes}</div></div>` : ''}
                `;

                if (c.links && c.links.length) {
                    document.getElementById('trtMLinks').innerHTML = `
                    <div class="trt-modal-links-label">Related Cards</div>
                    <div class="trt-modal-link-tags">${c.links.map(l => `<span class="trt-modal-link-tag">${l.replace(/_/g, ' ')}</span>`).join('')}</div>
                    `;
                } else {
                    document.getElementById('trtMLinks').innerHTML = '';
                }

                document.getElementById('trtOverlay').classList.add('trt-active');
                document.body.style.overflow = 'hidden';
            }

            function trtCloseModal() {
                document.getElementById('trtOverlay').classList.remove('trt-active');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('.trt-cc-card').forEach(el => {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.cardIdx, 10);
                    trtOpenModal(idx);
                });
            });

            document.getElementById('trtModalClose').addEventListener('click', trtCloseModal);
            document.getElementById('trtOverlay').addEventListener('click', function(e) {
                if (e.target === this) trtCloseModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') trtCloseModal();
            });
        })();
    </script>

    <?php return ob_get_clean();
}