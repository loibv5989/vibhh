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
    <h1 class="trt-hero-title">Tarot Reading by Question</h1>
    <div class="trt-input-section">
        <p class="trt-label">How many cards would you like to draw from the 78-card deck?</p>
        <div class="trt-mode-grid trt-grid-spreads">
            <div class="trt-mode-card trt-spread-btn" data-spread="3_cards" data-count="3">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🃏</div>
                    <div class="trt-mode-title">3-Card Spread</div>
                </div>
                <div class="trt-mode-desc">Past – Present – Future.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="5_cards" data-count="5">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🔮</div>
                    <div class="trt-mode-title">5-Card Spread</div>
                </div>
                <div class="trt-mode-desc">Goes deeper, with advice and challenges included.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="7_cards" data-count="7">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🧲</div>
                    <div class="trt-mode-title">7-Card Spread</div>
                </div>
                <div class="trt-mode-desc">Digs into root causes and hidden factors.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="10_cards" data-count="10">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">✡️</div>
                    <div class="trt-mode-title">10-Card Spread (Celtic)</div>
                </div>
                <div class="trt-mode-desc">The most detailed full picture for a complex situation.</div>
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
            <button type="button" class="trt-chip" data-q="What does my love life look like in the near future?">What does my love life look like ahead?</button>
            <button type="button" class="trt-chip" data-q="Will my career go smoothly?">Will my career go smoothly?</button>
            <button type="button" class="trt-chip" data-q="Should I make a change?">Should I make a change?</button>
            <button type="button" class="trt-chip" data-q="What does my financial situation look like ahead?">What does my financial situation look like ahead?</button>
            <button type="button" class="trt-chip" data-q="What is holding me back right now?">What is holding me back?</button>
            <button type="button" class="trt-chip" data-q="What should I focus on right now?">What should I focus on right now?</button>
        </div>

        <div class="trt-question-tips">
            <div><strong>💡 Tarot works well for:</strong></div>
            <div>• Write a question that is <strong>clear and focused</strong> on your situation.</div>
            <div>• <strong>Asking for yourself:</strong> "Will my career go well in 2026?"</div>
            <div>• <strong>Asking about someone else:</strong> Include your relationship to them (friend, family, colleague...)</div>
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