<?php
if (!defined('ABSPATH')) exit;

/** @var string $mode */
/** @var string $spread_key */
/** @var int $total_cards */
/** @var array $spreads */
/** @var array $current_spread */

?>
<div class="trt-wrap" id="trt-wrap">
    <div id="trt-app-config"
         data-mode="<?= esc_attr($mode) ?>"
         data-spread="<?= esc_attr($spread_key) ?>"
         data-count="<?= esc_attr($total_cards) ?>"
         data-action-draw="bb_oracle_draw"
         data-action-analyze="bb_oracle_analyze"></div>
    <script>window.ORACLE_SPREADS = <?= json_encode($spreads, JSON_UNESCAPED_UNICODE) ?>;</script>

    <div class="trt-step <?= ($mode === 'topic') ? 'active' : '' ?>" id="trt-step-input-a">
        <div class="trt-step-header">
            <a href="/oracle-cards-online/" class="trt-back-btn">← Oracle Home</a>
            <span class="trt-step-label">🔮 <?= esc_html($current_spread['name']) ?></span>
        </div>
        <h1 class="trt-hero-title">Oracle <span><?= esc_html($total_cards) ?> Card</span> Spread</h1>
        <div class="trt-topic-section">
            <p class="trt-label">Choose a theme to explore</p>
            <p class="trt-topic-hint">✦ Take a deep breath, place your hand on your heart, and think about what's on your mind</p>
            <div class="trt-topic-grid">
                <?php
                $topics = [
                        'love'    => ['icon' => '💗', 'label' => 'Love'],
                        'career'  => ['icon' => '💼', 'label' => 'Career'],
                        'finance' => ['icon' => '💰', 'label' => 'Finance'],
                        'study'   => ['icon' => '📚', 'label' => 'Study'],
                        'health'  => ['icon' => '🌿', 'label' => 'Health'],
                        'future'  => ['icon' => '🌌', 'label' => 'Future'],
                ];
                foreach ($topics as $val => $t): ?>
                    <button class="trt-topic-card" data-topic="<?= esc_attr($val) ?>">
                        <div class="trt-topic-card-back">
                            <div class="trt-topic-card-inner">
                                <span class="trt-topic-icon"><?= esc_html($t['icon']) ?></span>
                                <span class="trt-topic-label"><?= esc_html($t['label']) ?></span>
                            </div>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
            <span class="trt-error" id="trt-err-topic-a"></span>
        </div>
        <input type="hidden" id="trt-topic-val" value="">
    </div>

    <div class="trt-step <?= ($mode === 'question') ? 'active' : '' ?>" id="trt-step-spread-b">
        <div class="trt-step-header">
            <a href="/oracle-cards-online/" class="trt-back-btn">← Oracle Home</a>
            <span class="trt-step-label">✍️ Ask Oracle</span>
        </div>
        <h1 class="trt-hero-title">Ask Oracle Cards Online</h1>
        <div class="trt-input-section trt-margin-b-24">
            <p class="trt-label">Your question</p>
            <textarea id="trt-question" class="trt-input trt-textarea" placeholder="e.g. What should I focus on right now?" maxlength="300" rows="3"></textarea>
            <div class="trt-char-count"><span id="trt-q-count">0</span>/300</div>
            <span class="trt-error" id="trt-err-question"></span>
        </div>
        <div class="trt-user-question" aria-hidden="true">
            <label for="trt-user-label">Enter answer?</label>
            <input type="text" id="trt-user-question-trap" name="trt-user-question" tabindex="-1" autocomplete="off">
        </div>
        <div class="trt-input-section">
            <p class="trt-label">How many cards would you like to draw?</p>
            <div class="trt-mode-grid trt-grid-spreads">
                <div class="trt-mode-card trt-spread-btn" data-spread="1_card" data-count="1">
                    <div class="trt-spread-header"><div class="trt-mode-icon">🌟</div><div class="trt-mode-title">1 Card</div></div>
                    <div class="trt-mode-desc">One focused, concise message.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="2_cards" data-count="2">
                    <div class="trt-spread-header"><div class="trt-mode-icon">✨</div><div class="trt-mode-title">2 Cards</div></div>
                    <div class="trt-mode-desc">Situation and guidance.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="3_cards" data-count="3">
                    <div class="trt-spread-header"><div class="trt-mode-icon">🔮</div><div class="trt-mode-title">3 Cards</div></div>
                    <div class="trt-mode-desc">Mind · Heart · Spirit.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="trt-step" id="trt-step-deck">
        <p class="trt-deck-instruction" id="trt-deck-instruction">✦ Take a deep breath, focus on your question, and choose your cards</p>
        <div class="trt-deck-wrap" id="trt-deck-wrap"></div>
        <div class="trt-selected-slots" id="trt-dynamic-slots"></div>
        <div class="trt-deck-counter">Selected: <span id="trt-selected-count">0</span>/<span id="trt-target-count">0</span></div>
    </div>

    <div id="trt-result-box"></div>
</div>