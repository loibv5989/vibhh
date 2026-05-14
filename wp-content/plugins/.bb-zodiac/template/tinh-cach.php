<?php
/**
 * Template: Bói Tính Cách Theo Cung Hoàng Đạo
 * @package BbZodiac
 */
if (!defined('ABSPATH')) exit;
?>
<div class="fortune-page" id="zdc-tinh-cach-wrapper">
    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">Bói Tính Cách</div>
        <h1 class="ftn-hero-title">Giải Mã <span>Tính Cách</span> của bạn<br> theo Cung Hoàng Đạo</h1>
        <p>Dựa trên ngày sinh, hệ thống sẽ phân tích tính cách, điểm mạnh, điểm yếu, đời sống tình cảm và sự nghiệp của bạn theo Cung Hoàng Đạo và Decan.</p>
    </section>
    <div class="ftn-calc-card zdc-lp-toggle">
        <form id="zdc-form" novalidate>
            <input type="text" id="zdc-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-dob">Ngày / tháng / năm sinh (Dương lịch)</label>
                    <input type="text" id="zdc-dob" class="ftn-input" placeholder="Ví dụ: 15/12/1999" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-dob"></span>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">Phân tích</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Đang phân tích...</span>
            </button>
        </form>
    </div>
    <div class="ftn-result" id="zdc-result"></div>
</div>
