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
            <a href="/oracle-cards-online/" class="trt-back-btn">← Trang chủ Oracle</a>
            <span class="trt-step-label">🔮 <?= esc_html($current_spread['name']) ?></span>
        </div>
        <h1 class="trt-hero-title">Trải bài Oracle <span><?= esc_html($total_cards) ?> lá</span></h1>
        <div class="trt-topic-section">
            <p class="trt-label">Chọn chủ đề năng lượng bạn muốn khám phá</p>
            <p class="trt-topic-hint">✦ Hít thở sâu, đặt tay lên trái tim, nghĩ về điều đang chiếm trọn tâm trí bạn</p>
            <div class="trt-topic-grid">
                <?php
                $topics = [
                        'love'    => ['icon' => '💗', 'label' => 'Tình Yêu'],
                        'career'  => ['icon' => '💼', 'label' => 'Sự Nghiệp'],
                        'finance' => ['icon' => '💰', 'label' => 'Tài Chính'],
                        'study'   => ['icon' => '📚', 'label' => 'Học Tập'],
                        'health'  => ['icon' => '🌿', 'label' => 'Sức Khỏe'],
                        'future'  => ['icon' => '🌌', 'label' => 'Tương Lai'],
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
            <a href="/oracle-cards-online/" class="trt-back-btn">← Trang chủ Oracle</a>
            <span class="trt-step-label">✍️ Hỏi Oracle</span>
        </div>
        <h1 class="trt-hero-title">Hỏi Oracle Cards Online</h1>
        <div class="trt-input-section trt-margin-b-24">
            <p class="trt-label">Câu hỏi của bạn</p>
            <textarea id="trt-question" class="trt-input trt-textarea" placeholder="Ví dụ: Tôi cần tập trung vào điều gì lúc này?" maxlength="300" rows="3"></textarea>
            <div class="trt-char-count"><span id="trt-q-count">0</span>/300</div>
            <span class="trt-error" id="trt-err-question"></span>
        </div>
        <div class="trt-user-question" aria-hidden="true">
            <label for="trt-user-label">Nhập câu trả lời?</label>
            <input type="text" id="trt-user-question-trap" name="trt-user-question" tabindex="-1" autocomplete="off">
        </div>
        <div class="trt-input-section">
            <p class="trt-label">Bạn muốn rút bao nhiêu lá?</p>
            <div class="trt-mode-grid trt-grid-spreads">
                <div class="trt-mode-card trt-spread-btn" data-spread="1_card" data-count="1">
                    <div class="trt-spread-header"><div class="trt-mode-icon">🌟</div><div class="trt-mode-title">1 Lá</div></div>
                    <div class="trt-mode-desc">Một thông điệp súc tích, tập trung.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="2_cards" data-count="2">
                    <div class="trt-spread-header"><div class="trt-mode-icon">✨</div><div class="trt-mode-title">2 Lá</div></div>
                    <div class="trt-mode-desc">Tình huống và hướng dẫn.</div>
                </div>
                <div class="trt-mode-card trt-spread-btn" data-spread="3_cards" data-count="3">
                    <div class="trt-spread-header"><div class="trt-mode-icon">🔮</div><div class="trt-mode-title">3 Lá</div></div>
                    <div class="trt-mode-desc">Tâm Trí · Trái Tim · Linh Hồn.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="trt-step" id="trt-step-deck">
        <p class="trt-deck-instruction" id="trt-deck-instruction">✦ Hít thở sâu, tập trung vào câu hỏi và chọn lá bài</p>
        <div class="trt-deck-wrap" id="trt-deck-wrap"></div>
        <div class="trt-selected-slots" id="trt-dynamic-slots"></div>
        <div class="trt-deck-counter">Đã chọn: <span id="trt-selected-count">0</span>/<span id="trt-target-count">0</span></div>
    </div>

    <div id="trt-result-box"></div>
</div>