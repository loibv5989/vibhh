<?php
/**
 * Template for Question Input Steps
 * Includes spread selection and question input
 */
?>

<div class="trt-step <?= ($mode === 'question') ? 'active' : '' ?>" id="trt-step-spread-b">
    <div class="trt-step-header">
        <a href="/tarot-online/" class="trt-back-btn">← Trang chủ Tarot</a>
        <span class="trt-step-label">✍️ Hỏi Tarot</span>
    </div>
    <h1 class="trt-hero-title">Bói Tarot Theo Câu Hỏi</h1>
    <div class="trt-input-section">
        <p class="trt-label">Bạn muốn trải bao nhiêu lá bài trong 78 lá?</p>
        <div class="trt-mode-grid trt-grid-spreads">
            <div class="trt-mode-card trt-spread-btn" data-spread="3_cards" data-count="3">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🃏</div>
                    <div class="trt-mode-title">Trải 3 Lá</div>
                </div>
                <div class="trt-mode-desc">Xem: Quá khứ - Hiện tại - Tương lai.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="5_cards" data-count="5">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🔮</div>
                    <div class="trt-mode-title">Trải 5 Lá</div>
                </div>
                <div class="trt-mode-desc">Sâu hơn, kèm theo lời khuyên và thách thức.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="7_cards" data-count="7">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">🧲</div>
                    <div class="trt-mode-title">Trải 7 Lá</div>
                </div>
                <div class="trt-mode-desc">Đi sâu vào nguyên nhân gốc rễ và yếu tố ẩn.</div>
            </div>

            <div class="trt-mode-card trt-spread-btn" data-spread="10_cards" data-count="10">
                <div class="trt-spread-header">
                    <div class="trt-mode-icon">✡️</div>
                    <div class="trt-mode-title">Trải 10 Lá (Celtic)</div>
                </div>
                <div class="trt-mode-desc">Bức tranh toàn cảnh chi tiết nhất cho một vấn đề lớn.</div>
            </div>
        </div>
    </div>
</div>

<div class="trt-step" id="trt-step-input-b">
    <div class="trt-step-header">
        <button class="trt-back-btn" data-back="spread-b">← Quay lại chọn lá</button>
        <span class="trt-step-label">✍️ Nhập Câu Hỏi</span>
    </div>
    <div class="trt-input-section">
        <label class="trt-label" for="trt-question">Câu hỏi của bạn là gì?</label>
        <textarea id="trt-question" class="trt-input trt-textarea" placeholder="Ví dụ: Mối quan hệ này sẽ đi về đâu?..." maxlength="300" rows="3"></textarea>
        <div class="trt-char-count"><span id="trt-q-count">0</span>/300</div>
        <span class="trt-error" id="trt-err-question"></span>

        <div class="trt-chips">
            <button type="button" class="trt-chip" data-q="Tình cảm của tôi sắp tới sẽ thế nào?">Tình cảm của tôi sắp tới sẽ thế nào?</button>
            <button type="button" class="trt-chip" data-q="Công việc của tôi có thuận lợi không?">Công việc của tôi có thuận lợi không?</button>
            <button type="button" class="trt-chip" data-q="Tôi có nên thay đổi không?">Tôi có nên thay đổi không?</button>
            <button type="button" class="trt-chip" data-q="Tài chính của tôi sắp tới ra sao?">Tài chính của tôi sắp tới ra sao?</button>
            <button type="button" class="trt-chip" data-q="Điều gì đang cản trở tôi?">Điều gì đang cản trở tôi?</button>
            <button type="button" class="trt-chip" data-q="Tôi cần tập trung vào điều gì lúc này?">Tôi cần tập trung vào điều gì lúc này?</button>
        </div>

        <div class="trt-question-tips">
            <div><strong>💡 Tarot phù hợp với:</strong></div>
            <div>• Hãy viết câu hỏi <strong>đủ ý, trọng tâm</strong> vào vấn đề của bạn.</div>
            <div>• <strong>Hỏi cho bản thân:</strong> "Công việc năm 2026 của tôi có thuận lợi không?"</div>
            <div>• <strong>Hỏi cho người khác:</strong> Nên ghi rõ mối quan hệ (bạn bè, người thân, đồng nghiệp...)</div>
        </div>
    </div>
    <div class="trt-user-question" aria-hidden="true">
        <label for="trt-user-label">Nhập câu trả lời?</label>
        <input type="text" id="trt-user-question-trap" name="trt-user-question" tabindex="-1" autocomplete="off">
    </div>
    <button class="trt-submit-btn" id="trt-btn-submit-b">
        <span class="trt-btn-text">Xáo Bài</span>
        <span class="trt-btn-loading" style="display:none;"><span class="trt-spinner"></span> Đang xáo bài...</span>
    </button>
</div>
