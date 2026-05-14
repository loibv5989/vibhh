<?php
/**
 * Template for Topic Selection Step
 * Displays 6 topic options for Tarot reading
 */
?>
<div class="trt-step <?= ($mode === 'topic') ? 'active' : '' ?>" id="trt-step-input-a">
    <div class="trt-step-header">
        <a href="/tarot-online/" class="trt-back-btn">← Trang chủ Tarot</a>
        <span class="trt-step-label">🃏 <?= esc_html($current_spread['name']) ?></span>
    </div>
    <h1 class="trt-hero-title">Trải bài Tarot <span><?= esc_html($total_cards) ?> lá</span></h1>
    <div class="trt-topic-section">
        <p class="trt-label">Chọn chủ đề bạn muốn hỏi</p>
        <p class="trt-topic-hint">✦ Nhắm mắt, nghĩ về vấn đề đang vướng mắc, rồi chọn chủ đề</p>
        <div class="trt-topic-grid">
            <?php
            $topics = [
                'love' => ['icon' => '❤️', 'label' => 'Tình Yêu'],
                'career' => ['icon' => '💼', 'label' => 'Công Việc'],
                'finance' => ['icon' => '💰', 'label' => 'Tài Chính'],
                'study' => ['icon' => '📚', 'label' => 'Học Tập'],
                'health' => ['icon' => '🌿', 'label' => 'Sức Khỏe'],
                'future' => ['icon' => '🔮', 'label' => 'Tương Lai'],
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
