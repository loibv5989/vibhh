<?php
if (!defined('ABSPATH')) exit;
/**
 * @var array $chunks
 */
?>
<div class="fortune-page">
    <section class="ftn-hero">
        <div class="ftn-hero-badge">🧠 Trắc Nghiệm Tính Cách Miễn Phí Online</div>
        <h1 class="ftn-hero-title">Đọc vị bản thân<br>qua <span>Bản đồ tâm lý MBTI</span></h1>
        <p>Nhận diện nhóm tính cách cốt lõi qua 32 câu hỏi. Khám phá phong cách làm việc, xu hướng tình cảm và định hướng phát triển dành riêng cho bạn.</p>
    </section>

    <div class="ftn-mbti-progress">
        <div class="ftn-mbti-bar" id="mbti-progress-bar" style="width: 25%;">25%</div>
    </div>

    <form id="mbti-form" class="ftn-calc-card">
        <?php foreach ($chunks as $index => $chunk): $stepNum = $index + 1; ?>
            <div class="mbti-step <?= $stepNum === 1 ? 'active' : '' ?>" data-step="<?= $stepNum ?>" style="<?= $stepNum === 1 ? '' : 'display:none;' ?>">
                <?php foreach ($chunk as $q): ?>
                    <div class="mbti-question-box">
                        <p class="mbti-q-text"><?= esc_html($q['text']) ?></p>
                        <div class="mbti-options">
                            <span class="mbti-lbl-disagree">Không đồng ý</span>
                            <div class="mbti-radios">
                                <label><input type="radio" name="<?= esc_attr($q['id']) ?>" value="1" class="mbti-rad-big"></label>
                                <label><input type="radio" name="<?= esc_attr($q['id']) ?>" value="2" class="mbti-rad-med"></label>
                                <label><input type="radio" name="<?= esc_attr($q['id']) ?>" value="3" class="mbti-rad-small"></label>
                                <label><input type="radio" name="<?= esc_attr($q['id']) ?>" value="4" class="mbti-rad-med"></label>
                                <label><input type="radio" name="<?= esc_attr($q['id']) ?>" value="5" class="mbti-rad-big"></label>
                            </div>
                            <span class="mbti-lbl-agree">Đồng ý</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="mbti-nav-buttons">
            <button type="button" id="mbti-prev" class="ftn-btn-outline" style="display:none;">Quay lại</button>
            <button type="button" id="mbti-next" class="ftn-btn-submit">Tiếp tục</button>
            <button type="submit" id="mbti-submit" class="ftn-btn-submit" style="display:none;">
                <span class="ftn-btn-text">Xem kết quả</span>
                <span class="ftn-btn-loading" style="display:none;"><span class="ftn-spinner"></span> Đang xử lý...</span>
            </button>
        </div>
        <div id="mbti-error-msg" class="ftn-error" style="text-align:center; margin-top:10px; display:none;"></div>
    </form>

    <div class="ftn-result" id="mbti-result" style="display:none;"></div>
</div>
