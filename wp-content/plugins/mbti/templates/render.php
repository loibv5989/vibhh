<?php
if (!defined('ABSPATH')) exit;

class MBTI_Render {
    public static function resultStatic(array $data): string {
        $p   = $data['profile'];
        $pct = $data['pct'];
        ob_start(); ?>
        <div class="ftn-result-box">
            <div class="ftn-res-header">
                <div class="ftn-res-type"><?= esc_html($data['type']) ?></div>
                <div class="ftn-res-title"><?= esc_html($p['title']) ?></div>
                <div class="ftn-res-stats">
                    <span class="ftn-stat-badge">E <?= $pct['EI']['E'] ?>% / I <?= $pct['EI']['I'] ?>%</span>
                    <span class="ftn-stat-badge">S <?= $pct['SN']['S'] ?>% / N <?= $pct['SN']['N'] ?>%</span>
                    <span class="ftn-stat-badge">T <?= $pct['TF']['T'] ?>% / F <?= $pct['TF']['F'] ?>%</span>
                    <span class="ftn-stat-badge">J <?= $pct['JP']['J'] ?>% / P <?= $pct['JP']['P'] ?>%</span>
                </div>
            </div>

            <?php if (!empty($data['borderline'])):
                $borderline_desc = [
                        'EI' => 'Hướng ngoại / Hướng nội',
                        'SN' => 'Giác quan / Trực giác',
                        'TF' => 'Lý trí / Cảm xúc',
                        'JP' => 'Nguyên tắc / Linh hoạt',
                ];
                $count_border = count($data['borderline']);
                if ($count_border === 1) {
                    $bot_text = 'Bạn không nghiêng hẳn về phía nào ở khía cạnh này. Điều đó không phải vấn đề, chỉ là kết quả chưa đủ rõ.';
                } elseif ($count_border === 2) {
                    $bot_text = 'Bạn cân bằng ở khá nhiều khía cạnh. Điều này giúp bạn thích nghi tốt, nhưng đôi khi cũng khiến bạn khó chọn khi phải nghiêng về một phía.';
                } else {
                    $bot_text = 'Kết quả của bạn không nghiêng rõ về nhóm nào. Làm lại bài test sau một thời gian có thể cho kết quả rõ hơn.';
                }
                ?>
                <div class="mbti-borderline-box">
                    <strong>Kết quả của bạn khá cân bằng ở:</strong><br>
                    <?php foreach ($data['borderline'] as $ax): ?>
                        - <strong><?= $borderline_desc[$ax] ?? $ax ?></strong><br>
                    <?php endforeach; ?>
                    <em class="mbti-borderline-tip">💡 <?= $bot_text ?></em>
                </div>
            <?php endif; ?>

            <div class="ftn-result-profile">
                <p><?= nl2br(esc_html($p['tong_quan'])) ?></p>
                <p><?= nl2br(esc_html($p['su_nghiep'])) ?></p>
                <p><?= nl2br(esc_html($p['tinh_yeu'])) ?></p>

                <?php if (!empty($p['diem_manh']) && is_array($p['diem_manh'])): ?>
                    <div class="mbti-strengths">
                        <strong>Điểm mạnh</strong>
                        <ul>
                            <?php foreach ($p['diem_manh'] as $item): ?>
                                <li><?= esc_html($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($p['diem_yeu']) && is_array($p['diem_yeu'])): ?>
                    <div class="mbti-weaknesses">
                        <strong>Điểm yếu</strong>
                        <ul>
                            <?php foreach ($p['diem_yeu'] as $item): ?>
                                <li><?= esc_html($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (get_option('mbti_allow_ai', '0') === '1'): ?>
                <div class="ftn-upsell-box">
                    <p class="ftn-upsell-title">Phân tích thêm về tính cách của bạn</p>
                    <p>MBTI của bạn là <strong style="color: red"><?= esc_html($data['type']) ?></strong>. Kết hợp thêm <a href="/than-so-hoc/" target="_blank">tên và ngày sinh</a> hoặc <a href="/cung-hoang-dao/" target="_blank">cung hoàng đạo</a> để xem thêm các góc nhìn khác.</p>

                    <form id="mbti-ai-form">
                        <div class="ftn-form-group">
                            <label for="ai-name" class="ftn-form-label">Họ và tên</label>
                            <input type="text" id="ai-name" class="ftn-input" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>
                        <div class="ftn-form-group">
                            <label for="ai-dob" class="ftn-form-label">Ngày sinh (Dương lịch)</label>
                            <input type="text" id="ai-dob" class="ftn-input" placeholder="VD: 15/12/1999" required>
                        </div>
                        <button type="submit" class="ftn-btn-submit" id="ai-submit-btn">
                            <span class="ftn-btn-text">Tiếp tục phân tích</span>
                            <span class="ftn-btn-loading" style="display:none;"><span class="ftn-spinner"></span> Đang xử lý...</span>
                        </button>
                    </form>

                    <div id="ai-error-msg" class="ftn-error" style="display:none;"></div>
                    <div id="ai-final-result" class="ftn-ai-final-result" style="display:none;"></div>
                </div>
            <?php endif; ?>
        </div>
        <div class="ast-action-footer" style="display:none;">
            <span id="ast-btn-comment" class="ast-btn-comment">Thảo Luận</span>
            <span class="ast-reload" onclick="window.location.reload()">↺ Làm lại</span>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function resultAI(array $tabs): string {
        return '<div class="ftn-ai-final-wrap">' . ($tabs['ai_tong_hop'] ?? '') . '</div>';
    }
}