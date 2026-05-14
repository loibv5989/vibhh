<?php
if (!defined('ABSPATH')) exit;
?>
<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>#tuvi-ht-app" class="tuvi-ht-form">
    <div class="tuvi-form-header">
        <div class="tuvi-form-ornament">
            <div class="tuvi-form-ornament-line"></div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M8 2C8 2 5 5.5 5 7.5C5 9.2 6.3 10.5 8 10.5C9.7 10.5 11 9.2 11 7.5C11 5.5 8 2 8 2Z"
                      stroke="var(--tuvi-color-1)" stroke-width="0.8"
                      fill="color-mix(in srgb, var(--tuvi-color-1) 8%, transparent)"/>
            </svg>
            <div class="tuvi-form-ornament-line right"></div>
        </div>
        <h2 class="tuvi-form-h1">Xem Hợp Tuổi</h2>
        <p class="tuvi-form-sub">Can Chi · Ngũ Hành · Cung Mệnh Bát Trạch</p>
        <p class="tuvi-form-desc">Xem tử vi tình duyên, bói tuổi vợ chồng và tình yêu đôi lứa; phân tích tuổi hợp tác làm ăn, kết giao và định hướng sự nghiệp theo phong thủy, dựa trên Can Chi, Ngũ Hành và Bát Trạch.</p>
        <a href="/tu-vi-12-con-giap/" class="tuvi-back-link">← Quay lại</a>
    </div>

    <div class="tuvi-field">
        <label for="tuvi_ht_muc_dich">Chủ đề</label>
        <select name="tuvi_ht_muc_dich" id="tuvi_ht_muc_dich">
            <option value="hon_nhan"   <?php selected($muc_dich ?? 'hon_nhan', 'hon_nhan'); ?>>Hôn nhân, Gia đạo</option>
            <option value="tinh_duyen" <?php selected($muc_dich ?? 'hon_nhan', 'tinh_duyen'); ?>>Tình duyên, Đôi lứa</option>
            <option value="hop_tac"    <?php selected($muc_dich ?? 'hon_nhan', 'hop_tac'); ?>>Hợp tác, Làm ăn</option>
        </select>
    </div>

    <div class="tuvi-ht-persons-wrapper">
        <div class="tuvi-ht-person-block">
            <div class="tuvi-ht-person-label">
                <span class="tuvi-ht-badge tuvi-ht-badge-a">Người 1</span>
            </div>

            <div class="tuvi-field tuvi-field-full">
                <label for="tuvi_ht_ten_a">Họ Và Tên <span class="tuvi-required">*</span></label>
                <input type="text" name="tuvi_ht_ten_a" id="tuvi_ht_ten_a"
                       value="<?php echo esc_attr($ten_a ?? ''); ?>"
                       placeholder="VD: Nguyễn Văn An" maxlength="50" required>
            </div>

            <div class="tuvi-ht-row">
                <div class="tuvi-field tuvi-field-full">
                    <label for="tuvi_ht_ngay_sinh_a">Ngày Sinh (Dương Lịch) <span class="tuvi-required">*</span></label>
                    <input type="text" name="tuvi_ht_ngay_sinh_a" id="tuvi_ht_ngay_sinh_a" class="tuvi-input-date"
                           placeholder="VD: 15/8/1990" maxlength="10"
                           value="<?php echo esc_attr($ngay_sinh_a ?? ''); ?>" required>
                </div>

                <div class="tuvi-field">
                    <label for="tuvi_ht_gio_sinh_a">Giờ Sinh</label>
                    <input type="text" class="tuvi-input-time" name="tuvi_ht_gio_sinh_a" id="tuvi_ht_gio_sinh_a"
                           value="<?php echo esc_attr($gio_sinh_a ?? ''); ?>"
                           placeholder="VD: 14:30" maxlength="5" pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                           title="Giờ định dạng 24h (VD: 14:30)" autocomplete="off">
                </div>

                <div class="tuvi-field">
                    <label for="tuvi_ht_gioi_tinh_a">Giới Tính <span class="tuvi-required">*</span></label>
                    <select name="tuvi_ht_gioi_tinh_a" id="tuvi_ht_gioi_tinh_a" required>
                        <option value="nam" <?php selected($gioi_tinh_a ?? 'nam', 'nam'); ?>>Nam</option>
                        <option value="nu"  <?php selected($gioi_tinh_a ?? 'nam', 'nu'); ?>>Nữ</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="tuvi-ht-person-block">
            <div class="tuvi-ht-person-label">
                <span class="tuvi-ht-badge tuvi-ht-badge-b">Người 2</span>
            </div>

            <div class="tuvi-field tuvi-field-full">
                <label for="tuvi_ht_ten_b">Họ Và Tên <span class="tuvi-required">*</span></label>
                <input type="text" name="tuvi_ht_ten_b" id="tuvi_ht_ten_b"
                       value="<?php echo esc_attr($ten_b ?? ''); ?>"
                       placeholder="VD: Trần Thị Bình" maxlength="50" required>
            </div>

            <div class="tuvi-ht-row">
                <div class="tuvi-field tuvi-field-full">
                    <label for="tuvi_ht_ngay_sinh_b">Ngày Sinh (Dương Lịch) <span class="tuvi-required">*</span></label>
                    <input type="text" name="tuvi_ht_ngay_sinh_b" id="tuvi_ht_ngay_sinh_b" class="tuvi-input-date"
                           placeholder="VD: 15/8/1990" maxlength="10"
                           value="<?php echo esc_attr($ngay_sinh_b ?? ''); ?>" required>
                </div>

                <div class="tuvi-field">
                    <label for="tuvi_ht_gio_sinh_b">Giờ Sinh</label>
                    <input type="text" class="tuvi-input-time" name="tuvi_ht_gio_sinh_b" id="tuvi_ht_gio_sinh_b"
                           value="<?php echo esc_attr($gio_sinh_b ?? ''); ?>"
                           placeholder="VD: 09:00" maxlength="5" pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                           title="Giờ định dạng 24h (VD: 09:00)" autocomplete="off">
                </div>

                <div class="tuvi-field">
                    <label for="tuvi_ht_gioi_tinh_b">Giới Tính <span class="tuvi-required">*</span></label>
                    <select name="tuvi_ht_gioi_tinh_b" id="tuvi_ht_gioi_tinh_b" required>
                        <option value="nu"  <?php selected($gioi_tinh_b ?? 'nu', 'nu'); ?>>Nữ</option>
                        <option value="nam" <?php selected($gioi_tinh_b ?? 'nu', 'nam'); ?>>Nam</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="tuvi-field tuvi-submit-field">
        <button type="submit" name="tuvi_ht_submit" value="1">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                <path d="M6 1.5C6 1.5 3.5 4 3.5 5.8C3.5 7.2 4.6 8.2 6 8.2C7.4 8.2 8.5 7.2 8.5 5.8C8.5 4 6 1.5 6 1.5Z"
                      stroke="currentColor" stroke-width="0.9" stroke-linejoin="round"/>
                <path d="M3.5 10.5h5" stroke="currentColor" stroke-width="0.9" stroke-linecap="round"/>
            </svg>
            <span class="tuvi-btn-text">Xem Hợp Tuổi</span>
        </button>
    </div>
</form>