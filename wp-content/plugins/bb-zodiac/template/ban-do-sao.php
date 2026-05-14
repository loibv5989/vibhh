<?php
if (!defined('ABSPATH')) exit;
?>
<div class="fortune-page" id="zdc-ban-do-sao-wrapper">

    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">🌙 Bản Đồ Sao</div>
        <h1 class="ftn-hero-title">Bản Đồ Sao <span>Cá Nhân</span></h1>
        <p>Phân tích dựa trên ngày sinh, giờ sinh và nơi sinh. Bao gồm Cung Mặt Trời, Mặt Trăng, Ascendant và sự kết hợp năng lượng (Big 3).</p>
    </section>

    <div class="ftn-calc-card zdc-lp-toggle">
        <form id="zdc-natal-form" novalidate>
            <input type="text" id="zdc-natal-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="on" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-natal-dob">Ngày sinh (Dương lịch)</label>
                    <input type="text" id="zdc-natal-dob" class="ftn-input" placeholder="Ví dụ: 15/12/1999" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-natal-dob"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-natal-tob">Giờ sinh (nếu biết)</label>
                    <input type="text" id="zdc-natal-tob" class="ftn-input" placeholder="Ví dụ: 14:30">
                    <span class="ftn-error" id="zdc-error-natal-tob"></span>
                </div>
            </div>
            <div class="ftn-form-group" style="margin-top:12px">
                <label for="zdc-natal-pob">Nơi sinh (Tỉnh/Thành phố)</label>
                <div class="zdc-pob-wrap">
                    <input type="text" id="zdc-natal-pob" class="ftn-input" placeholder="Gõ tên tỉnh/thành phố..." autocomplete="off">
                    <div id="zdc-pob-dropdown" class="zdc-pob-dropdown" role="listbox" aria-label="Gợi ý nơi sinh"></div>
                </div>
                <span class="ftn-error" id="zdc-error-natal-pob"></span>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-natal-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">Trích xuất Bản đồ sao</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang đo quỹ đạo...</span>
            </button>
            <p id="zdc-error-natal" style="color:var(--lbv-color-2); text-align:center; margin-top:10px; font-size:14px;"></p>
        </form>
    </div>

    <div class="ftn-result" id="zdc-natal-result" style="display:none;"></div>
</div>