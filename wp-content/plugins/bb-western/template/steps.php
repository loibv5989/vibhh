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
            <a href="/boi-bai-tay/" class="trt-back-btn">← Trang chủ Bài Tây</a>
            <span class="trt-step-label">🃏 <?= esc_html($current_spread['name']) ?></span>
        </div>
        <div class="trt-topic-section">
            <p class="trt-label">Chọn chủ đề bạn muốn hỏi</p>
            <p class="trt-topic-hint">✦ Nhắm mắt, nghĩ về vấn đề đang vướng mắc, rồi chọn chủ đề</p>
            <div class="trt-topic-grid">
                <?php
                $topics = [
                    'love'    => ['icon' => '❤️', 'label' => 'Tình Yêu'],
                    'career'  => ['icon' => '💼', 'label' => 'Công Việc'],
                    'finance' => ['icon' => '💰', 'label' => 'Tài Chính'],
                    'study'   => ['icon' => '📚', 'label' => 'Học Tập'],
                    'health'  => ['icon' => '🌿', 'label' => 'Sức Khỏe'],
                    'future'  => ['icon' => '🔮', 'label' => 'Tương Lai'],
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
            <a href="/boi-bai-tay/" class="trt-back-btn">← Trang chủ Bài Tây</a>
            <span class="trt-step-label">✍️ Hỏi Bài Tây</span>
        </div>
        <div class="trt-input-section">
            <p class="trt-label">Bạn muốn trải bao nhiêu lá?</p>
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
                    <div class="trt-mode-desc">Phân tích tình huống, thử thách và kết quả.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="7_cards" data-count="7">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">🧲</div>
                        <div class="trt-mode-title">Trải 7 Lá</div>
                    </div>
                    <div class="trt-mode-desc">Phân tích toàn diện, nguyên nhân và hướng đi.</div>
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

    <div class="trt-step" id="trt-step-deck">
        <p class="trt-deck-instruction" id="trt-deck-instruction">✦ Tập trung vào câu hỏi của bạn và chọn bài</p>
        <div class="trt-deck-wrap" id="trt-deck-wrap"></div>
        <div class="trt-selected-slots" id="trt-dynamic-slots"></div>
        <div class="trt-deck-counter">Đã chọn: <span id="trt-selected-count">0</span>/<span id="trt-target-count">0</span></div>
    </div>

    <div id="trt-result-box" style="display:none"></div>

    <div id="trt-card-modal" class="trt-card-modal" style="display:none">
        <div class="trt-card-modal-backdrop"></div>
        <div class="trt-card-modal-body" id="trt-modal-body">
            <div class="trt-card-modal-overlay"></div>
            <button class="trt-card-modal-close" aria-label="Đóng">&times;</button>
            <div class="trt-card-modal-content" id="trt-modal-content"></div>
        </div>
    </div>
</div>
