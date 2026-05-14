<?php
if (!defined('ABSPATH')) exit;

class MBTI_Render {
    public static function resultStatic(array $data): string {
        $p = $data['profile'];
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
                    'EI' => 'Hướng ngoại / Hướng nội (sự cân bằng giữa nhu cầu giao tiếp và thời gian cho bản thân)',
                    'SN' => 'Giác quan / Trực giác (sự đan xen giữa tư duy thực tế, chi tiết và những ý tưởng bay bổng)',
                    'TF' => 'Lý trí / Cảm xúc (sự cân nhắc liên tục giữa tính hợp lý của logic và sự thấu cảm tình người)',
                    'JP' => 'Nguyên tắc / Linh hoạt (sự cân bằng giữa việc muốn có kế hoạch rõ ràng nhưng lại khao khát tự do)'
                ];
                $count_border = count($data['borderline']);
                if ($count_border === 1) {
                    $bot_text = "Sự cân bằng ở khía cạnh này mang lại cho bạn lợi thế linh hoạt. Tùy thuộc vào môi trường, bạn không bị đóng khung vào một khuôn mẫu cố định.";
                } elseif ($count_border === 2) {
                    $bot_text = "Sự cân bằng ở nhiều khía cạnh cho thấy bạn có một nội tâm phong phú và đa chiều. Tuy nhiên, điều này đôi khi khiến bạn trải qua những mâu thuẫn nội tâm sâu sắc khi phải đưa ra quyết định nghiêng về một thái cực.";
                } else {
                    $bot_text = "Bạn có tính cách khá linh hoạt. Sự cân bằng ở nhiều trục giúp bạn dễ thích nghi với các môi trường khác nhau. Tuy vậy, bạn cũng có thể cần thêm thời gian để hiểu rõ bản thân mình thực sự muốn gì.";
                }
                ?>
                <div class="mbti-borderline-box">
                    <strong>Các đặc điểm cân bằng:</strong><br>
                    Kết quả của bạn cho thấy sự cân bằng khá rõ ở các trục sau:<br>
                    <?php foreach($data['borderline'] as $ax): ?>
                        - <strong><?= $borderline_desc[$ax] ?? $ax ?></strong><br>
                    <?php endforeach; ?>
                    <em class="mbti-borderline-tip">💡 <?= $bot_text ?></em>
                </div>
            <?php endif; ?>

            <ul class="ftn-result-list">
                <li><?= nl2br(esc_html($p['tong_quan'])) ?></li>
                <li><?= nl2br(esc_html($p['su_nghiep'])) ?></li>
                <li><?= nl2br(esc_html($p['tinh_yeu'])) ?></li>
            </ul>

            <?php if (get_option('mbti_allow_ai', '0') === '1'): ?>
            <div class="ftn-upsell-box">
                <p class="ftn-upsell-title">Phân tích sâu hơn tính cách của bạn</p>
                <p>MBTI của bạn là <strong style="color: red"><?= esc_html($data['type']) ?></strong>. Nếu kết hợp thêm <a href="/than-so-hoc/" target="_blank">phân tích tên, ngày sinh (Dương lịch)</a> và <a href="/cung-hoang-dao/" target="_blank">cung hoàng đạo</a>, bạn có thể khám phá thêm nhiều khía cạnh thú vị về bản thân.</p>

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
                        <span class="ftn-btn-loading" style="display:none;"><span class="ftn-spinner"></span> Đang phân tích...</span>
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
