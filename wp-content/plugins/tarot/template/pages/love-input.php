<?php
/**
 * Template for Love Spread Input
 */
if (!defined('ABSPATH')) exit;
?>
<div class="trt-step <?= ($mode === 'love') ? 'active' : '' ?>" id="trt-step-spread-love">
    <div class="trt-step-header">
        <a href="/" class="trt-back-btn">← Tarot Home</a>
        <span class="trt-step-label">💖 Love Reading</span>
    </div>
    <h1 class="trt-hero-title">Love Tarot Reading</h1>
    <div class="trt-input-section">
        <p class="trt-label">Choose how many cards to draw for your love reading</p>

        <div class="trt-mode-grid trt-grid-spreads">
            <div class="trt-mode-card trt-spread-btn" data-spread="love_3_cards" data-count="3" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">💞</div>
                    <div class="trt-mode-title">3 Cards</div>
                </div>
                <div class="trt-mode-desc">You – Your partner – The relationship.</div>
            </div>
            <div class="trt-mode-card trt-spread-btn" data-spread="love_5_cards" data-count="5" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">⚔️</div>
                    <div class="trt-mode-title">5 Cards</div>
                </div>
                <div class="trt-mode-desc">Insight into the thoughts and feelings of both people.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="love_7_cards" data-count="7" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🧲</div>
                    <div class="trt-mode-title">7 Cards</div>
                </div>
                <div class="trt-mode-desc">Deeper look at issues and the path forward.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="love_9_cards" data-count="9" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🔮</div>
                    <div class="trt-mode-title">9 Cards</div>
                </div>
                <div class="trt-mode-desc">A clear view of the current situation and near future.</div>
            </div>
        </div>
    </div>
</div>

<div class="trt-step" id="trt-step-input-love">
    <div class="trt-step-header">
        <button class="trt-back-btn" type="button"
                onclick="document.querySelector('.trt-step.active').classList.remove('active'); document.getElementById('trt-step-spread-love').classList.add('active');">
            ← Back to card count
        </button>
        <span class="trt-step-label">💖 Ask a Question</span>
    </div>

    <h2 class="trt-hero-title">What do you want to ask about your love life?</h2>
    <div class="trt-input-section">
        <p class="trt-label"> Enter your question or a brief description of your situation. </p>
        <textarea id="trt-question-love" class="trt-input trt-textarea" rows="4" placeholder="Enter your question or situation..." maxlength="300" ></textarea>
        <div class="trt-char-count"><span id="trt-q-count-love">0</span>/300</div>
        <span class="trt-error" id="trt-err-question-love"></span>

        <div class="trt-chips">
            <button type="button" class="trt-chip" data-q="What is my partner thinking about me right now?">What is my partner thinking about me?</button>
            <button type="button" class="trt-chip" data-q="Where is this relationship headed in the future?">Where is this relationship headed?</button>
            <button type="button" class="trt-chip" data-q="Do we have a chance to reconcile and heal?">Do we have a chance to reconcile?</button>
            <button type="button" class="trt-chip" data-q="How can I resolve the current conflict in my relationship?">How can I resolve this conflict?</button>
        </div>

        <div class="trt-question-tips">
            <div><strong>💡 Love Tarot is best for:</strong></div>
            <div>• Questions about <strong>romantic relationships</strong>, couples, marriage.</div>
            <div>• <strong>Example:</strong> "Where is my relationship with my partner headed?"</div>
        </div>
    </div>

    <button class="trt-submit-btn" id="trt-btn-submit-love">Draw Cards →</button>
</div>