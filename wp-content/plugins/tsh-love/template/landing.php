<?php
if (!defined('ABSPATH')) exit;
?>
<div class="tsh-love-page">
    <section class="ftn-hero">
        <div class="ftn-hero-badge">Bói Tình Yêu - Thần Số Học</div>
        <h1 class="ftn-hero-title">Thần Số Học <span>của 2 Bạn có hợp nhau không?</span></h1>
        <p>Khám phá mức độ đồng điệu về định hướng cuộc đời và giá trị cốt lõi giữa hai người qua Số Chủ Đạo.</p>
    </section>
    <div class="ftn-calc-card">
        <form id="numm-form" novalidate>
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="numm-name1">Họ và tên (bạn nam)</label>
                    <input type="text" id="numm-name1" class="ftn-input" placeholder="Ví dụ: Nguyễn Tuấn Anh">
                    <span class="ftn-error" id="numm-error-name1"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="numm-dob1">Ngày sinh (Dương lịch)</label>
                    <input type="text" id="numm-dob1" class="ftn-input" placeholder="Ví dụ: 15/04/1999">
                    <span class="ftn-error" id="numm-error-dob1"></span>
                </div>
            </div>
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="numm-name2">Họ và tên (bạn nữ)</label>
                    <input type="text" id="numm-name2" class="ftn-input" placeholder="Ví dụ: Trần Phương Trinh">
                    <span class="ftn-error" id="numm-error-name2"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="numm-dob2">Ngày sinh (Dương lịch)</label>
                    <input type="text" id="numm-dob2" class="ftn-input" placeholder="Ví dụ: 20/10/2000">
                    <span class="ftn-error" id="numm-error-dob2"></span>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="numm-submit-btn">
                <span class="ftn-btn-text">Phân Tích</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang tính toán...</span>
            </button>
        </form>
    </div>
    <div class="ftn-result" id="numm-result"></div>
</div>

