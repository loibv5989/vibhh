<?php
/**
 * Template for Question Input Steps
 * Includes spread selection and question input
 */
?>

<div class="trt-step <?= ($mode === 'question') ? 'active' : '' ?>" id="trt-step-spread-b">
    <div class="trt-step-header">
        <a href="/tarot-online/" class="trt-back-btn">← Tarot Home</a>
        <span class="trt-step-label">✍️ Ask Tarot</span>
    </div>
    <h1 class="trt-hero-title">Ask Tarot a Question</h1>
    <div class="trt-input-section">
        <p class="trt-label">How many cards do you want to draw from the 78-card deck?</p>
        <div class="trt-mode-grid trt-grid-spreads">
            <div class="trt-mode-card trt-spread-btn" data-spread="3_cards" data-count="3">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🃏</div>
                    <div class="trt-mode-title">3-Card Spread</div>
                </div>
                <div class="trt-mode-desc">View: Past - Present - Future.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="5_cards" data-count="5">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🔮</div>
                    <div class="trt-mode-title">5-Card Spread</div>
                </div>
                <div class="trt-mode-desc">Deeper insight with advice and challenges.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="7_cards" data-count="7">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🧲</div>
                    <div class="trt-mode-title">7-Card Spread</div>
                </div>
                <div class="trt-mode-desc">Dive into root causes and hidden factors.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="10_cards" data-count="10">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">✡️</div>
                    <div class="trt-mode-title">10-Card Celtic Cross</div>
                </div>
                <div class="trt-mode-desc">The most detailed full picture for a major issue.</div>
            </div>
        </div>
    </div>
</div>

<div class="trt-step" id="trt-step-input-b">
    <div class="trt-step-header">
        <button class="trt-back-btn" data-back="spread-b">← Back to spread selection</button>
        <span class="trt-step-label">✍️ Enter Question</span>
    </div>
    <div class="trt-input-section">
        <label class="trt-label" for="trt-question">What is your question?</label>
        <textarea id="trt-question" class="trt-input trt-textarea" placeholder="Example: Where is this relationship headed?..." maxlength="300" rows="3"></textarea>
        <div class="trt-char-count"><span id="trt-q-count">0</span>/300</div>
        <span class="trt-error" id="trt-err-question"></span>

        <div class="trt-chips">
            <button type="button" class="trt-chip" data-q="How will my love life unfold soon?">How will my love life unfold soon?</button>
            <button type="button" class="trt-chip" data-q="Will my work go smoothly?">Will my work go smoothly?</button>
            <button type="button" class="trt-chip" data-q="Should I make a change?">Should I make a change?</button>
            <button type="button" class="trt-chip" data-q="How will my finances look in the near future?">How will my finances look in the near future?</button>
            <button type="button" class="trt-chip" data-q="What is blocking me right now?">What is blocking me right now?</button>
            <button type="button" class="trt-chip" data-q="What should I focus on at this moment?">What should I focus on at this moment?</button>
        </div>

        <div class="trt-question-tips">
            <div><strong>💡 Good questions for Tarot:</strong></div>
            <div>• Write a question that is <strong>focused and clear</strong> about your issue.</div>
            <div>• <strong>For yourself:</strong> "Will my work go smoothly in 2026?"</div>
            <div>• <strong>About others:</strong> Clarify the relationship (friend, family, colleague...)</div>
        </div>
    </div>
    <div class="trt-user-question" aria-hidden="true">
        <label for="trt-user-label">Enter an answer?</label>
        <input type="text" id="trt-user-question-trap" name="trt-user-question" tabindex="-1" autocomplete="off">
    </div>
    <button class="trt-submit-btn" id="trt-btn-submit-b">
        <span class="trt-btn-text">Shuffle Cards</span>
        <span class="trt-btn-loading" style="display:none;"><span class="trt-spinner"></span> Shuffling...</span>
    </button>
</div>
