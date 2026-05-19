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
    <h1 class="trt-hero-title">Love Spread</h1>
    <div class="trt-input-section">
        <p class="trt-label">Choose how many cards to read for your relationship</p>

        <div class="trt-mode-grid trt-grid-spreads">
            <div class="trt-mode-card trt-spread-btn" data-spread="love_3_cards" data-count="3" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">💞</div>
                    <div class="trt-mode-title">3 Cards</div>
                </div>
                <div class="trt-mode-desc">You – Them – The relationship.</div>
            </div>
            <div class="trt-mode-card trt-spread-btn" data-spread="love_5_cards" data-count="5" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">⚔️</div>
                    <div class="trt-mode-title">5 Cards</div>
                </div>
                <div class="trt-mode-desc">See the thoughts and feelings on both sides.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="love_7_cards" data-count="7" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🧲</div>
                    <div class="trt-mode-title">7 Cards</div>
                </div>
                <div class="trt-mode-desc">Uncover deeper issues and where things are headed.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="love_9_cards" data-count="9" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🔮</div>
                    <div class="trt-mode-title">9 Cards</div>
                </div>
                <div class="trt-mode-desc">A clear look at the current situation and what's coming next.</div>
            </div>
        </div>
    </div>
</div>

<div class="trt-step" id="trt-step-input-love">
    <div class="trt-step-header">
        <button class="trt-back-btn" type="button"
                onclick="document.querySelector('.trt-step.active').classList.remove('active'); document.getElementById('trt-step-spread-love').classList.add('active');">
            ← Change card count
        </button>
        <span class="trt-step-label">💖 Ask your question</span>
    </div>

    <h2 class="trt-hero-title">What do you want to know about your love life?</h2>
    <div class="trt-input-section">
        <p class="trt-label">Enter your question or a brief description of your situation.</p>
        <textarea id="trt-question-love" class="trt-input trt-textarea" rows="4" placeholder="Enter your question or situation..." maxlength="300" ></textarea>
        <div class="trt-char-count"><span id="trt-q-count-love">0</span>/300</div>
        <span class="trt-error" id="trt-err-question-love"></span>

        <div class="trt-chips">
            <button type="button" class="trt-chip" data-q="What is this person currently thinking about me and our relationship?">What are they thinking about me?</button>
            <button type="button" class="trt-chip" data-q="What direction will this romantic relationship develop in the future?">Where is this relationship going?</button>
            <button type="button" class="trt-chip" data-q="Do we have a chance to get back together and reconnect?">Is there a chance we'll get back together?</button>
            <button type="button" class="trt-chip" data-q="How can I resolve the current conflict in this relationship?">How do I handle this conflict?</button>
        </div>

        <div class="trt-question-tips">
            <div><strong>💡 Love Tarot works best for:</strong></div>
            <div>• Questions about <strong>romantic relationships</strong>, partnerships, and marriage.</div>
            <div>• <strong>Example:</strong> "Where is my relationship with this person heading?"</div>
        </div>
    </div>

    <button class="trt-submit-btn" id="trt-btn-submit-love">Draw Cards →</button>
</div>