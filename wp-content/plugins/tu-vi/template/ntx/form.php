<?php
if (!defined('ABSPATH')) exit;

try {
    $timezone = TuVi_Settings::get_instance()->getTimezone();
    $tz = new DateTimeZone($timezone);
} catch (Exception $e) {
    $tz = wp_timezone();
}

$dateObj = new DateTime('now', $tz);
$today = $dateObj->format('d/m/Y');

$dateObj->modify('+1 month');
$end_date = $dateObj->format('d/m/Y');

?>
<form method="post" action="<?= esc_url($_SERVER['REQUEST_URI']); ?>" class="tuvi-ntx-form">
    <div class="tuvi-form-header">
        <div class="tuvi-form-ornament">
            <div class="tuvi-form-ornament-line"></div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M8 1L9.2 6.2L14.5 8L9.2 9.8L8 15L6.8 9.8L1.5 8L6.8 6.2Z"
                      stroke="var(--tuvi-color-1)" stroke-width="0.8"
                      fill="color-mix(in srgb, var(--tuvi-color-1) 8%, transparent)"/>
            </svg>
            <div class="tuvi-form-ornament-line right"></div>
        </div>
        <h2 class="tuvi-form-h1">Trạch Nhật Cát Hung</h2>
        <p class="tuvi-form-sub">Bát Tự &amp; Đổng Công Tuyển Trạch</p>
        <a href="/tu-vi-12-con-giap/" class="tuvi-back-link">← Quay lại</a>

    </div>
    <div class="tuvi-field">
        <label for="tuvi_mode">Tra Cứu</label>
        <select name="tuvi_mode" id="tuvi_mode">
            <option value="single" <?php selected($mode ?? 'single', 'single'); ?>>Theo ngày</option>
            <option value="range"  <?php selected($mode ?? 'single', 'range'); ?>>Khoảng ngày</option>
        </select>
    </div>

    <div class="tuvi-field">
        <label for="tuvi_purpose">Sự Kiện</label>
        <select name="tuvi_purpose" id="tuvi_purpose">
            <option value="cuoi"        <?php selected($purpose ?? 'cuoi', 'cuoi'); ?>>Cưới hỏi, Gia đạo</option>
            <option value="dong_tho"    <?php selected($purpose ?? 'cuoi', 'dong_tho'); ?>>Động thổ, Xây cất</option>
            <option value="nhap_trach"  <?php selected($purpose ?? 'cuoi', 'nhap_trach'); ?>>Nhập trạch, Về nhà mới</option>
            <option value="khai_truong" <?php selected($purpose ?? 'cuoi', 'khai_truong'); ?>>Khai trương, Cầu tài</option>
            <option value="ky_hop_dong" <?php selected($purpose ?? 'cuoi', 'ky_hop_dong'); ?>>Ký hợp đồng, Giao dịch</option>
            <option value="mua_xe"      <?php selected($purpose ?? 'cuoi', 'mua_xe'); ?>>Mua xe, Tài sản lớn</option>
            <option value="xuat_hanh"   <?php selected($purpose ?? 'cuoi', 'xuat_hanh'); ?>>Xuất hành, Đi xa</option>
        </select>
    </div>

    <div class="tuvi-field tuvi-single-field">
        <label for="tuvi_date">Ngày Cần Xem (Dương Lịch)</label>
        <input type="text" name="tuvi_date" id="tuvi_date" class="tuvi-input-date" autocomplete="on"
               placeholder="VD: <?= esc_attr($today); ?>" maxlength="10" value="<?= esc_attr($date ?? ''); ?>">
    </div>

    <div class="tuvi-range-fields" style="<?= ($mode ?? 'single') === 'range' ? '' : 'display:none;'; ?>">
        <div class="tuvi-field">
            <label for="tuvi_start">Từ Ngày</label>
            <input type="text" name="tuvi_start" id="tuvi_start" class="tuvi-input-date" autocomplete="on"
                   placeholder="VD: <?= esc_attr($today); ?>" maxlength="10" value="<?= esc_attr($start ?? ''); ?>">
        </div>
        <div class="tuvi-field">
            <label for="tuvi_end">Đến Ngày</label>
            <input type="text" name="tuvi_end" id="tuvi_end" class="tuvi-input-date" autocomplete="on"
                   placeholder="VD: <?= esc_attr($end_date)?>" maxlength="10" value="<?= esc_attr($end ?? ''); ?>">
        </div>
        <div class="tuvi-field">
            <label for="tuvi_limit">Số ngày cần xem</label>
            <input type="number" name="tuvi_limit" id="tuvi_limit" min="1" max="50"
                   value="<?= esc_attr(($limit ?? 30) > 0 ? ($limit ?? 30) : 30); ?>" required>
        </div>
    </div>

    <div class="tuvi-battu-fields">
        <div class="tuvi-field">
            <label for="tuvi_ngay_sinh">Ngày/Tháng/Năm Sinh (DL) <span class="tuvi-required">*</span></label>
            <input type="text" name="tuvi_ngay_sinh" id="tuvi_ngay_sinh" class="tuvi-input-date"
                   placeholder="VD: 15/8/1990" maxlength="10"
                   value="<?= esc_attr($ngay_sinh ?? ''); ?>" required>
        </div>

        <div class="tuvi-field">
            <label for="tuvi_gio_sinh">Giờ sinh <span class="tuvi-hint">(24h)</span></label>
            <input type="text" class="tuvi-input-time" name="tuvi_gio_sinh" id="tuvi_gio_sinh"
                   value="<?= esc_attr($gio_sinh ?? ''); ?>"
                   placeholder="VD: 14:30" maxlength="5" pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                   title="Vui lòng nhập giờ định dạng 24h (VD: 14:30)" autocomplete="on">
        </div>

        <div class="tuvi-field">
            <label for="tuvi_gioi_tinh">Giới Tính <span class="tuvi-required">*</span></label>
            <select name="tuvi_gioi_tinh" id="tuvi_gioi_tinh" required>
                <option value="" <?php selected($gioi_tinh ?? '', ''); ?>>--Giới tính--</option>
                <option value="nam" <?php selected($gioi_tinh ?? '', 'nam'); ?>>Nam</option>
                <option value="nu" <?php selected($gioi_tinh ?? '', 'nu'); ?>>Nữ</option>
            </select>
        </div>
    </div>
    <div class="tuvi-field tuvi-submit-field">
        <button type="submit" name="tuvi_ntx_submit" value="1">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="0.9"/>
                <path d="M6 3.5v2.8l1.8 1.2" stroke="currentColor" stroke-width="0.9" stroke-linecap="round"/>
            </svg><span class="tuvi-btn-text">Tra cứu</span>
        </button>
    </div>
</form>