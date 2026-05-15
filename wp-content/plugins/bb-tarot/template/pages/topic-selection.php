<?php
/**
 * Template for Topic Selection Step
 * Displays 6 topic options for Tarot reading
 */
?>
<div class="trt-step <?= ($mode === 'topic') ? 'active' : '' ?>" id="trt-step-input-a">
    <div class="trt-step-header">
        <a href="/tarot-online/" class="trt-back-btn">← Tarot Home</a>
        <span class="trt-step-label">🃏 <?= esc_html($current_spread['name']) ?></span>
    </div>
    <h1 class="trt-hero-title">Tarot Reading <span><?= esc_html($total_cards) ?> Cards</span></h1>
    <div class="trt-topic-section">
        <p class="trt-label">Choose the topic you want to ask about</p>
        <p class="trt-topic-hint">✦ Close your eyes, think about the issue troubling you, then choose a topic</p>
        <div class="trt-topic-grid">
            <?php
            $topics = [
                'love' => ['icon' => '❤️', 'label' => 'Love'],
                'career' => ['icon' => '💼', 'label' => 'Career'],
                'finance' => ['icon' => '💰', 'label' => 'Finance'],
                'study' => ['icon' => '📚', 'label' => 'Study'],
                'health' => ['icon' => '🌿', 'label' => 'Health'],
                'future' => ['icon' => '🔮', 'label' => 'Future'],
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
