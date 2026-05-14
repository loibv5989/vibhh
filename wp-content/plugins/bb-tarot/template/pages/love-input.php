<?php
/**
 * Template for Love Spread Input
 */
if (!defined('ABSPATH')) exit;
?>
<div class="trt-step <?= ($mode === 'love') ? 'active' : '' ?>" id="trt-step-spread-love">
    <div class="trt-step-header">
        <a href="/" class="trt-back-btn">← Trang chủ Tarot</a>
        <span class="trt-step-label">💖 Bói Tình Yêu</span>
    </div>
    <h1 class="trt-hero-title">Trải Bài Tình Yêu</h1>
    <div class="trt-input-section">
        <p class="trt-label">Chọn số lá để xem chuyện tình cảm</p>

        <div class="trt-mode-grid trt-grid-spreads">
            <div class="trt-mode-card trt-spread-btn" data-spread="love_3_cards" data-count="3" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">💞</div>
                    <div class="trt-mode-title">3 Lá</div>
                </div>
                <div class="trt-mode-desc">Bạn – Người ấy – Mối quan hệ.</div>
            </div>
            <div class="trt-mode-card trt-spread-btn" data-spread="love_5_cards" data-count="5" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">⚔️</div>
                    <div class="trt-mode-title">5 Lá</div>
                </div>
                <div class="trt-mode-desc">Xem rõ suy nghĩ và cảm xúc của hai người.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="love_7_cards" data-count="7" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🧲</div>
                    <div class="trt-mode-title">7 Lá</div>
                </div>
                <div class="trt-mode-desc">Xem thêm vấn đề và hướng đi tiếp theo.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="love_9_cards" data-count="9" data-next="input-love">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🔮</div>
                    <div class="trt-mode-title">9 Lá</div>
                </div>
                <div class="trt-mode-desc">Xem rõ tình hình hiện tại và tương lai gần.</div>
            </div>
        </div>
    </div>
</div>

<div class="trt-step" id="trt-step-input-love">
    <div class="trt-step-header">
        <button class="trt-back-btn" type="button"
                onclick="document.querySelector('.trt-step.active').classList.remove('active'); document.getElementById('trt-step-spread-love').classList.add('active');">
            ← Chọn lại số lá
        </button>
        <span class="trt-step-label">💖 Đặt câu hỏi</span>
    </div>

    <h2 class="trt-hero-title">Bạn muốn hỏi gì về tình cảm?</h2>
    <div class="trt-input-section">
        <p class="trt-label"> Nhập câu hỏi hoặc mô tả ngắn về tình huống của bạn. </p>
        <textarea id="trt-question-love" class="trt-input trt-textarea" rows="4" placeholder="Nhập câu hỏi hoặc tình huống của bạn..." maxlength="300" ></textarea>
        <div class="trt-char-count"><span id="trt-q-count-love">0</span>/300</div>
        <span class="trt-error" id="trt-err-question-love"></span>

        <div class="trt-chips">
            <button type="button" class="trt-chip" data-q="Người ấy hiện đang nghĩ gì về tôi trong mối quan hệ này?">Người ấy hiện đang nghĩ gì về tôi?</button>
            <button type="button" class="trt-chip" data-q="Mối quan hệ tình cảm này trong tương lai sẽ phát triển theo hướng nào?">Mối quan hệ này sẽ đi về đâu?</button>
            <button type="button" class="trt-chip" data-q="Chúng tôi có cơ hội quay lại và hàn gắn tình cảm không?">Có cơ hội quay lại không?</button>
            <button type="button" class="trt-chip" data-q="Làm sao để giải quyết mâu thuẫn hiện tại trong chuyện tình cảm?">Giải quyết mâu thuẫn thế nào?</button>
        </div>

        <div class="trt-question-tips">
            <div><strong>💡 Tarot Tình Yêu phù hợp với:</strong></div>
            <div>• Câu hỏi về <strong>tình cảm đôi lứa</strong>, mối quan hệ nam nữ, hôn nhân.</div>
            <div>• <strong>Ví dụ:</strong> "Mối quan hệ của tôi và người ấy sẽ đi về đâu?"</div>
        </div>
    </div>

    <button class="trt-submit-btn" id="trt-btn-submit-love">Rút Bài →</button>
</div>