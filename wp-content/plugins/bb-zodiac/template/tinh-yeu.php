<?php
if (!defined('ABSPATH')) exit;
?>
<div class="fortune-page" id="zdc-tinh-yeu-wrapper">

    <section class="ftn-hero">
        <div class="ftn-hero-badge">⭐ Độ Hợp nhau của chòm sao</div>
        <h1 class="ftn-hero-title">Bói Tình Yêu <span>12 Cung Hoàng Đạo</span></h1>
        <p>Nhập thông tin của bạn và người ấy để hệ thống tự động tìm cung hoàng đạo và khám phá lực hút tự nhiên.</p>
    </section>

    <div class="ftn-calc-card">
        <form id="zdc-love-form" novalidate>
            <input type="text" id="zdc-love-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-love-name1">Họ tên (Bạn nam)</label>
                    <input type="text" id="zdc-love-name1" class="ftn-input" placeholder="Ví dụ: Tuấn Anh" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-name1"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-love-dob1">Ngày sinh (Dương lịch)</label>
                    <input type="text" id="zdc-love-dob1" class="ftn-input" placeholder="Ví dụ: 15/04/1999" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-dob1"></span>
                </div>
            </div>
            <div class="ftn-form-row" style="margin-top:15px;">
                <div class="ftn-form-group">
                    <label for="zdc-love-name2">Họ tên (Bạn nữ)</label>
                    <input type="text" id="zdc-love-name2" class="ftn-input" placeholder="Ví dụ: Phương Trinh" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-name2"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-love-dob2">Ngày sinh (Dương lịch)</label>
                    <input type="text" id="zdc-love-dob2" class="ftn-input" placeholder="Ví dụ: 20/10/2000" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-dob2"></span>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-love-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">Phân tích</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang phân tích...</span>
            </button>
        </form>
    </div>

    <div class="ftn-result" id="zdc-love-result"></div>

    <div class="ftn-btn-right">
        <span class="ftn-btn-reset zdc-love-btn-reset" style="display:none;">← Quay lại</span>
    </div>

</div>
