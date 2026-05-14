<?php
if (!defined('ABSPATH')) exit;
?>
<?php
$gio_sinh_options = [
        '00:00' => 'Giờ Tý Sáng (00:00 - 00:59)',
        '02:00' => 'Giờ Sửu (01:00 - 02:59)',
        '04:00' => 'Giờ Dần (03:00 - 04:59)',
        '06:00' => 'Giờ Mão (05:00 - 06:59)',
        '08:00' => 'Giờ Thìn (07:00 - 08:59)',
        '10:00' => 'Giờ Tỵ (09:00 - 10:59)',
        '12:00' => 'Giờ Ngọ (11:00 - 12:59)',
        '14:00' => 'Giờ Mùi (13:00 - 14:59)',
        '16:00' => 'Giờ Thân (15:00 - 16:59)',
        '18:00' => 'Giờ Dậu (17:00 - 18:59)',
        '20:00' => 'Giờ Tuất (19:00 - 20:59)',
        '22:00' => 'Giờ Hợi (21:00 - 22:59)',
        '23:00' => 'Giờ Tý Đêm (23:00 - 23:59)'
];
$selected_gio = $_POST['tuvi_gio_sinh'] ?? '';
if (!$result || isset($result['error'])): ?>
    <div class="tuvi-calc-card">
        <h1 class="tuvi-form-title">Lập <span>Lá Số Tử Vi</span><br>Luận Giải Đẩu Số</h1>
        <p class="tuvi-landing-subtitle">
            Lá số Tử Vi được lập từ ngày giờ sinh, luận giải dựa trên dữ liệu cổ học trong Tử Vi Đẩu Số Toàn Thư của Trần Đoàn (Trần Hi Di), chú giải truyền thừa Lâm Canh Phàm.
            Nội dung phản ánh vận mệnh, tính cách và các giai đoạn quan trọng như sự nghiệp, tài chính, tình duyên và vận hạn, không pha trộn với các trường phái chiêm tinh hoặc huyền học khác ngoài Tử Vi cổ học.
        </p>
        <a href="/tu-vi-12-con-giap/" class="tuvi-back-link">← Quay lại</a>
        <form method="post" class="tuvi-form-inline">
            <div>
                <label class="tuvi-label-inline">Họ và tên</label>
                <input type="text" class="tuvi-input-inline tuvi-input-name" name="tuvi_ho_ten" placeholder="Vd: Bùi Duy Anh" value="<?= esc_attr($_POST['tuvi_ho_ten'] ?? '') ?>" pattern="[a-zA-ZÀ-ỹ\s]*" />
            </div>
            <div>
                <label class="tuvi-label-inline">Ngày/tháng/năm <span>(Dương lịch)</span></label>
                <input type="text" class="tuvi-input-inline tuvi-input-date" name="tuvi_ngay_sinh" placeholder="VD: 14/10/2000" maxlength="10" value="<?= esc_attr($_POST['tuvi_ngay_sinh'] ?? '') ?>" required />
            </div>
            <div>
                <label class="tuvi-label-inline">Giờ sinh <span>(12 Con Giáp)</span></label>
                <select name="tuvi_gio_sinh" class="tuvi-select-inline" required>
                    <option value="" disabled <?= empty($selected_gio) ? 'selected' : '' ?>>-- Chọn giờ sinh --</option>
                    <?php foreach ($gio_sinh_options as $val => $label): ?>
                        <option value="<?= esc_attr($val) ?>" <?= $selected_gio === $val ? 'selected' : '' ?>>
                            <?= esc_html($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="tuvi-label-inline">Giới tính</label>
                <select name="tuvi_gioi_tinh" class="tuvi-select-inline" required>
                    <option value="" disabled <?= !isset($_POST['tuvi_gioi_tinh']) ? 'selected' : '' ?>>-- Chọn --</option>
                    <option value="nam" <?= (isset($_POST['tuvi_gioi_tinh']) && $_POST['tuvi_gioi_tinh'] == 'nam') ? 'selected' : '' ?>>Nam</option>
                    <option value="nu" <?= (isset($_POST['tuvi_gioi_tinh']) && $_POST['tuvi_gioi_tinh'] == 'nu') ? 'selected' : '' ?>>Nữ</option>
                </select>
            </div>
            <div>
                <label class="tuvi-label-inline">Năm xem</label>
                <input type="number" class="tuvi-input-inline tuvi-input-year" name="tuvi_nam_xem" value="<?= esc_attr($_POST['tuvi_nam_xem'] ?? date('Y')) ?>" placeholder="<?= date('Y') ?>" />
            </div>
            <div class="tuvi-form-group-inline">
                <button type="submit" class="tuvi-btn-submit-inline">Lập Lá Số</button>
            </div>
        </form>
        <?php if(isset($result['error'])): ?>
            <div class="tuvi-error-inline"><?= $result['error'] ?></div>
        <?php endif; ?>
        <div class="tuvi-box-fo">
            <ul class="tuvi-landing-benefits">
                <li>Hiểu rõ <strong>tính cách và năng lực</strong> thực sự để biết đâu là thế mạnh ưu tiên và điểm yếu khắc phục.</li>
                <li>Đưa ra <strong>định hướng công việc</strong> phù hợp cùng thời điểm thuận lợi để phát triển.</li>
                <li>Xem xét khách quan về <strong>tình duyên và gia đạo</strong>, đời sống hôn nhân cũng như các mối quan hệ người thân.</li>
                <li>Chỉ ra những <strong>biến động trong năm</strong> để bạn nắm bắt cơ hội tốt và chủ động phòng tránh rủi ro.</li>
                <li>Nắm được <strong>nhịp độ thời vận</strong>, biết lúc nào vận lên để tiến tới và lúc nào vận xuống để chậm lại.</li>
                <li>Đưa ra <strong>gợi ý cân bằng</strong> thực tế giúp bạn điều chỉnh quyết định và hành động để cuộc sống diễn ra suôn sẻ hơn.</li>
            </ul>
        </div>
    </div>
<?php endif; ?>
