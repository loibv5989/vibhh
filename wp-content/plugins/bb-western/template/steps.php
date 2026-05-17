<?php
if (!defined('ABSPATH')) exit;

/** @var string $mode */
/** @var string $spread_key */
/** @var int $total_cards */
/** @var array $spreads_config */
/** @var array $current_spread */

?>
<div class="trt-wrap" id="trt-wrap">
    <div id="trt-app-config" data-mode="<?= esc_attr($mode) ?>" data-spread="<?= esc_attr($spread_key) ?>" data-count="<?= esc_attr($total_cards) ?>" style="display:none;"></div>
    <script>
        window.WESTERN_SPREADS = <?= json_encode($spreads_config, JSON_UNESCAPED_UNICODE) ?>;
    </script>

    <div class="trt-step <?= ($mode === 'topic') ? 'active' : '' ?>" id="trt-step-input-a">
        <div class="trt-step-header">
            <a href="/boi-bai-tay/" class="trt-back-btn">← Card Reading Home</a>
            <span class="trt-step-label">🃏 <?= esc_html($current_spread['name']) ?></span>
        </div>
        <div class="trt-topic-section">
            <p class="trt-label">Choose a topic for your reading</p>
            <p class="trt-topic-hint">✦ Close your eyes, think about what's on your mind, then pick a topic</p>
            <div class="trt-topic-grid">
                <?php
                $topics = [
                        'love'    => ['icon' => '❤️', 'label' => 'Love'],
                        'career'  => ['icon' => '💼', 'label' => 'Career'],
                        'finance' => ['icon' => '💰', 'label' => 'Finance'],
                        'study'   => ['icon' => '📚', 'label' => 'Studies'],
                        'health'  => ['icon' => '🌿', 'label' => 'Health'],
                        'future'  => ['icon' => '🔮', 'label' => 'Future'],
                ];
                foreach ($topics as $val => $t): ?>
                    <button class="trt-topic-card" data-topic="<?= $val ?>">
                        <div class="trt-topic-card-back">
                            <div class="trt-topic-card-inner">
                                <span class="trt-topic-icon"><?= $t['icon'] ?></span>
                                <span class="trt-topic-label"><?= $t['label'] ?></span>
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
            <a href="/boi-bai-tay/" class="trt-back-btn">← Card Reading Home</a>
            <span class="trt-step-label">✍️ Ask the Cards</span>
        </div>
        <div class="trt-input-section">
            <p class="trt-label">How many cards do you want to draw?</p>
            <div class="trt-mode-grid trt-grid-spreads">
                <div class="trt-mode-card trt-spread-btn" data-spread="3_cards" data-count="3">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">🃏</div>
                        <div class="trt-mode-title">3-Card Spread</div>
                    </div>
                    <div class="trt-mode-desc">Past, Present, and Future.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="5_cards" data-count="5">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">🔮</div>
                        <div class="trt-mode-title">5-Card Spread</div>
                    </div>
                    <div class="trt-mode-desc">Breaks down the situation, challenges, and outcome.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="7_cards" data-count="7">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">🧲</div>
                        <div class="trt-mode-title">7-Card Spread</div>
                    </div>
                    <div class="trt-mode-desc">Full picture: root causes, blocks, and direction.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="trt-step" id="trt-step-input-b">
        <div class="trt-step-header">
            <button class="trt-back-btn" data-back="spread-b">← Back to spread selection</button>
            <span class="trt-step-label">✍️ Enter Your Question</span>
        </div>
        <div class="trt-input-section">
            <label class="trt-label" for="trt-question">What is your question?</label>
            <textarea id="trt-question" class="trt-input trt-textarea" placeholder="Example: Where is this relationship heading?..." maxlength="300" rows="3"></textarea>
            <div class="trt-char-count"><span id="trt-q-count">0</span>/300</div>
            <span class="trt-error" id="trt-err-question"></span>

            <div class="trt-chips">
                <button type="button" class="trt-chip" data-q="What does my love life look like ahead?">What does my love life look like ahead?</button>
                <button type="button" class="trt-chip" data-q="Will things go smoothly at work?">Will things go smoothly at work?</button>
                <button type="button" class="trt-chip" data-q="Should I make a change?">Should I make a change?</button>
                <button type="button" class="trt-chip" data-q="What does my financial situation look like soon?">What does my financial situation look like soon?</button>
                <button type="button" class="trt-chip" data-q="What is holding me back?">What is holding me back?</button>
                <button type="button" class="trt-chip" data-q="What should I focus on right now?">What should I focus on right now?</button>
            </div>
        </div>
        <div class="trt-user-question" aria-hidden="true">
            <label for="trt-user-label">Enter your answer?</label>
            <input type="text" id="trt-user-question-trap" name="trt-user-question" tabindex="-1" autocomplete="off">
        </div>
        <button class="trt-submit-btn" id="trt-btn-submit-b">
            <span class="trt-btn-text">Shuffle Cards</span>
            <span class="trt-btn-loading" style="display:none;"><span class="trt-spinner"></span> Shuffling...</span>
        </button>
    </div>

    <div class="trt-step" id="trt-step-deck">
        <p class="trt-deck-instruction" id="trt-deck-instruction">✦ Focus on your question and choose your cards</p>
        <div class="trt-deck-wrap" id="trt-deck-wrap"></div>
        <div class="trt-selected-slots" id="trt-dynamic-slots"></div>
        <div class="trt-deck-counter">Selected: <span id="trt-selected-count">0</span>/<span id="trt-target-count">0</span></div>
    </div>

    <div id="trt-result-box" style="display:none"></div>

    <div id="trt-card-modal" class="trt-card-modal" style="display:none">
        <div class="trt-card-modal-backdrop"></div>
        <div class="trt-card-modal-body" id="trt-modal-body">
            <div class="trt-card-modal-overlay"></div>
            <button class="trt-card-modal-close" aria-label="Close">&times;</button>
            <div class="trt-card-modal-content" id="trt-modal-content"></div>
        </div>
    </div>
</div>