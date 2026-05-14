<?php
if (!defined('ABSPATH')) exit;

$signs = [
        'aries'       => '♈ Bạch Dương (21/3 - 19/4)',
        'taurus'      => '♉ Kim Ngưu (20/4 - 20/5)',
        'gemini'      => '♊ Song Tử (21/5 - 20/6)',
        'cancer'      => '♋ Cự Giải (21/6 - 22/7)',
        'leo'         => '♌ Sư Tử (23/7 - 22/8)',
        'virgo'       => '♍ Xử Nữ (23/8 - 22/9)',
        'libra'       => '♎ Thiên Bình (23/9 - 22/10)',
        'scorpio'     => '♏ Thiên Yết (23/10 - 21/11)',
        'sagittarius' => '♐ Nhân Mã (22/11 - 21/12)',
        'capricorn'   => '♑ Ma Kết (22/12 - 19/1)',
        'aquarius'    => '♒ Bảo Bình (20/1 - 18/2)',
        'pisces'      => '♓ Song Ngư (19/2 - 20/3)'
];
?>
<div class="fortune-page" id="zdc-tu-vi-wrapper">

    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">🗓️ Tử Vi</div>
        <h1 class="ftn-hero-title">Tử Vi <span>Hàng Ngày</span></h1>
        <p>Dự đoán vận mệnh theo ngày / tuần / tháng: công việc, tài chính, tình cảm theo cung hoàng đạo của bạn.</p>
    </section>
    <div class="ftn-calc-card zdc-lp-toggle">
        <form id="zdc-tuvi-form" novalidate>
            <input type="text" id="zdc-tuvi-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-tuvi-sign">Cung hoàng đạo của bạn</label>
                    <select id="zdc-tuvi-sign" class="ftn-input">
                        <option value="" disabled selected>-- Chọn cung hoàng đạo --</option>
                        <?php foreach ($signs as $id => $label): ?>
                            <option value="<?= esc_attr($id) ?>"><?= esc_html($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-tuvi-period">Xem dự báo cho</label>
                    <select id="zdc-tuvi-period" class="ftn-input">
                        <option value="daily">Hôm nay</option>
                        <option value="weekly">Tuần này</option>
                        <option value="monthly">Tháng này</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-tuvi-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">Xem tử vi</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang kết nối...</span>
            </button>
            <p id="zdc-error-tuvi" style="color:var(--lbv-color-2); text-align:center; margin-top:10px; font-size:14px;"></p>
        </form>
    </div>
    <div class="ftn-result" id="zdc-tuvi-result" style="display:none;"></div>
    <div class="ftn-btn-right">
        <span class="ftn-btn-reset zdc-tuvi-btn-reset" style="display:none;">← Đổi cung khác</span>
    </div>
</div>