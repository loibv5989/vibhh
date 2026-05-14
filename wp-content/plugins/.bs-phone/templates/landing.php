<?php
if (!defined('ABSPATH')) exit;
?>
<div class="fortune-page">
    <section class="ftn-hero">
        <div class="ftn-hero-badge">📱 Giải Mã Số Điện Thoại</div>
        <h1 class="ftn-hero-title">Ý nghĩa <span>và tần số năng lượng</span><br> Số điện thoại của bạn</h1>
        <p>Theo thuyết Synchronicity của Carl Jung, những lựa chọn của bạn có thể phản ánh trạng thái vô thức hiện tại. Số điện thoại bạn chọn có thể liên quan đến năng lượng bạn đang thu hút hoặc tìm kiếm.</p>
    </section>

    <div class="ftn-calc-card">
        <form id="phone-form" novalidate>
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="p-name">Họ và tên</label>
                    <input type="text" id="p-name" class="ftn-input" placeholder="Ví dụ: Nguyễn Văn A" autocomplete="on">
                    <span class="ftn-error" id="p-error-name"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="p-dob">Ngày sinh (Dương lịch)</label>
                    <input type="text" id="p-dob" class="ftn-input" placeholder="Ví dụ: 15/12/1989">
                    <span class="ftn-error" id="p-error-dob"></span>
                </div>
            </div>
            <div class="ftn-form-group" style="width:100%; margin-top:12px;">
                <label for="p-phone">Số điện thoại</label>
                <input type="tel" id="p-phone" class="ftn-input ftn-input-phone" placeholder="0987654321" autocomplete="on">
                <span class="ftn-error" id="p-error-phone"></span>
            </div>

            <div class="phone-nav-buttons" style="margin-top:20px;">
                <button type="submit" id="phone-submit" class="ftn-btn-submit">
                    <span class="ftn-btn-text">Phân tích</span>
                    <span class="ftn-btn-loading" style="display:none;"><span class="ftn-spinner"></span> Đang phân tích...</span>
                </button>
            </div>
            <div id="phone-error-msg" class="ftn-error" style="text-align:center; margin-top:10px; display:none;"></div>
        </form>
    </div>

    <div class="ftn-result" id="phone-result" style="display:none;"></div>
</div>
