<?php
/**
 * Template: Landing Page – Tử Vi Đẩu Số
 * @package BbTuVi
 */
if (!defined('ABSPATH')) exit;

$cung_vi = [
        ['id' => 'menh',       'name' => 'Mệnh & Thân', 'symbol' => '命', 'group' => 'Mệnh Tài Quan', 'keywords' => 'Bản ngã · Tính cách · Cốt lõi'],
        ['id' => 'phu_mau',    'name' => 'Phụ Mẫu',     'symbol' => '父', 'group' => 'Lục Thân',      'keywords' => 'Cha mẹ · Bề trên · Cấp trên'],
        ['id' => 'phuc_duc',   'name' => 'Phúc Đức',    'symbol' => '福', 'group' => 'Phúc Phối Di',  'keywords' => 'Phúc trạch · Tổ tiên · Tinh thần'],
        ['id' => 'dien_trach', 'name' => 'Điền Trạch',  'symbol' => '田', 'group' => 'Khác',          'keywords' => 'Nhà cửa · Đất đai · Nơi ở'],
        ['id' => 'quan_loc',   'name' => 'Quan Lộc',    'symbol' => '官', 'group' => 'Mệnh Tài Quan', 'keywords' => 'Sự nghiệp · Công danh · Học hành'],
        ['id' => 'giao_huu',   'name' => 'Giao Hữu',    'symbol' => '友', 'group' => 'Lục Thân',      'keywords' => 'Bạn bè · Đồng nghiệp · Cấp dưới'],
        ['id' => 'thien_di',   'name' => 'Thiên Di',    'symbol' => '移', 'group' => 'Phúc Phối Di',  'keywords' => 'Ra ngoài · Xã hội · Biến động'],
        ['id' => 'tat_ach',    'name' => 'Tật Ách',     'symbol' => '疾', 'group' => 'Khác',          'keywords' => 'Sức khỏe · Bệnh tật · Tai ương'],
        ['id' => 'tai_bach',   'name' => 'Tài Bạch',    'symbol' => '財', 'group' => 'Mệnh Tài Quan', 'keywords' => 'Tiền tài · Kiếm tiền · Tài sản'],
        ['id' => 'tu_tuc',     'name' => 'Tử Tức',      'symbol' => '子', 'group' => 'Lục Thân',      'keywords' => 'Con cái · Hậu bối · Sinh sản'],
        ['id' => 'phu_the',    'name' => 'Phu Thê',     'symbol' => '夫', 'group' => 'Phúc Phối Di',  'keywords' => 'Vợ chồng · Hôn nhân · Tình duyên'],
        ['id' => 'huynh_de',   'name' => 'Huynh Đệ',    'symbol' => '兄', 'group' => 'Lục Thân',      'keywords' => 'Anh em · Bạn tri kỷ · Đối tác'],
];

$groupColors = [
        'Mệnh Tài Quan' => '#ef4444', // Đỏ
        'Phúc Phối Di'  => '#f59e0b', // Vàng
        'Lục Thân'      => '#06b6d4', // Xanh dương
        'Khác'          => '#84cc16', // Xanh lục
];

$faqs = [
        ['q' => 'Kết quả luận giải có chính xác không?',
                'a' => 'Hệ thống an sao và luận giải được xây dựng dựa trên các cổ thư Tử Vi Đẩu Số truyền thống (như Tử Vi Đẩu Số Toàn Thư, Tử Vi Nghiệm Lý). Kết quả mang tính chất tham khảo, giúp bạn hiểu rõ thiên hướng bẩm sinh và chu kỳ vận hạn, không phải là định mệnh tuyệt đối.'],
        ['q' => 'Xem lá số Tử Vi có mất phí không?',
                'a' => 'Các tính năng lập lá số và xem tổng quan bản mệnh là hoàn toàn miễn phí. Chúng tôi mong muốn mang đến một công cụ chuẩn xác và dễ tiếp cận cho cộng đồng yêu thích huyền học.'],
        ['q' => 'Tại sao cần nhập chính xác giờ sinh?',
                'a' => 'Trong Tử Vi, giờ sinh quyết định trực tiếp đến việc an Mệnh, Thân và 14 Chính Tinh. Lệch một mốc giờ (ví dụ từ Tý sang Sửu) sẽ cho ra một lá số hoàn toàn khác biệt với số phận khác nhau.'],
        ['q' => 'Tháng nhuận trong Âm lịch được tính thế nào?',
                'a' => 'Hệ thống của chúng tôi hỗ trợ tự động quy đổi Dương lịch sang Âm lịch và xử lý chuẩn xác các tháng nhuận theo quy tắc lịch pháp thiên văn, đảm bảo an sao không bị sai lệch.'],
        ['q' => 'Tuần Triệt, Hóa Kỵ có phải luôn luôn xấu?',
                'a' => 'Không hẳn. Tùy thuộc vào vị trí cung, bản mệnh và các sao đồng cung. Ví dụ, Sát tinh đắc địa gặp Hóa Kỵ đôi khi lại bộc phát mạnh mẽ, hoặc Tuần Triệt có thể giúp hóa giải bớt hung hiểm của Không Kiếp.'],
        ['q' => 'Dữ liệu ngày sinh của tôi có được lưu trữ không?',
                'a' => 'Mọi dữ liệu bạn nhập vào chỉ được xử lý tạm thời trên trình duyệt hoặc phiên làm việc để tính toán lá số. Chúng tôi không lưu trữ thông tin cá nhân của bạn.'],
];
?>

<div class="tuvi-page" id="tuvi-landing-wrapper">

    <section class="tuvi-hero tuvi-lp-toggle">
        <div class="tuvi-hero-badge">☯ Khám Phá Thiên Bàn Cuộc Đời</div>
        <h1 class="tuvi-hero-title">Giải Mã <span>Lá Số Tử Vi</span><br>Của Bạn</h1>
        <p>Hệ thống an sao chuẩn xác, luận giải chi tiết 12 cung bản mệnh, vận hạn năm và đại vận. Nắm bắt thiên thời, địa lợi, nhân hòa để kiến tạo cuộc đời.</p>
        <div class="tuvi-hero-actions">
            <button class="tuvi-btn-primary" onclick="document.getElementById('tuvi-tools-section').scrollIntoView({behavior:'smooth'})">Lập Lá Số</button>
            <button class="tuvi-btn-ghost" onclick="document.getElementById('tuvi-about-section').scrollIntoView({behavior:'smooth'})">Tìm hiểu thêm</button>
        </div>
    </section>

    <section id="tuvi-about-section" class="tuvi-lp-section tuvi-lp-toggle">
        <div class="tuvi-lp-container">
            <h2 class="tuvi-section-title">Tử Vi Đẩu Số là gì?</h2>
            <p class="tuvi-section-desc">Tử Vi là một bộ môn khoa học cổ phương Đông, dựa trên triết lý Kinh Dịch và Âm Dương Ngũ Hành. Bằng cách thiết lập một bản đồ sao (lá số) tại thời điểm bạn chào đời, Tử Vi giúp phác họa bức tranh toàn cảnh về cuộc đời, các mối quan hệ và sự thăng trầm qua từng giai đoạn.</p>
            <div class="tuvi-intro-grid">
                <div class="tuvi-intro-card">
                    <div class="tuvi-intro-icon">☯</div>
                    <h3>Âm Dương Ngũ Hành</h3>
                    <p>Nền tảng sinh khắc giữa bản Mệnh và Cục, giữa các sao và cung vị, quyết định mức độ thuận lợi hay trắc trở trong cuộc sống.</p>
                </div>
                <div class="tuvi-intro-card">
                    <div class="tuvi-intro-icon">🌟</div>
                    <h3>14 Chính Tinh</h3>
                    <p>Các vì sao chủ đạo như Tử Vi, Thiên Phủ, Sát Phá Tham... đóng vai trò như nòng cốt hình thành nên tính cách và xu hướng hành động.</p>
                </div>
                <div class="tuvi-intro-card">
                    <div class="tuvi-intro-icon">📜</div>
                    <h3>12 Cung Vị</h3>
                    <p>Đại diện cho 12 khía cạnh của cuộc đời từ Bản Mệnh, Tiền Bạc, Sự Nghiệp đến Gia Đình, Vợ Chồng, Con Cái và Phúc Đức.</p>
                </div>
                <div class="tuvi-intro-card">
                    <div class="tuvi-intro-icon">⏳</div>
                    <h3>Tứ Hóa & Vận Hạn</h3>
                    <p>Sự biến đổi năng lượng theo thời gian (Hóa Lộc, Hóa Quyền, Hóa Khoa, Hóa Kỵ) kích hoạt các sự kiện tốt xấu qua từng năm.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="tuvi-lp-section tuvi-lp-toggle">
        <div class="tuvi-lp-container">
            <div class="tuvi-section-label">Cấu Trúc Lá Số</div>
            <h2 class="tuvi-section-title">12 Cung Trên Thiên Bàn</h2>
            <p class="tuvi-section-desc">Mỗi cung đại diện cho một mảng lớn trong đời người. Tương tác giữa các cường cung và nhược cung sẽ tạo nên các cách cục đặc trưng.</p>

            <div class="tuvi-signs-grid">
                <?php foreach ($cung_vi as $cung):
                    $color = $groupColors[$cung['group']] ?? '#D4AF37';
                    ?>
                    <div class="tuvi-sign-card" data-group="<?= esc_attr($cung['group']) ?>" style="--sign-color:<?= $color ?>">
                        <div class="tuvi-sign-symbol"><?= $cung['symbol'] ?></div>
                        <div class="tuvi-sign-name"><?= esc_html($cung['name']) ?></div>
                        <div class="tuvi-sign-date"><?= esc_html($cung['group']) ?></div>
                        <div class="tuvi-sign-element" style="color: <?= $color ?>; border-color: <?= $color ?>;"><?= esc_html($cung['keywords']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="tuvi-element-legend">
                <span class="tuvi-el-badge" style="--el-color:#ef4444">🔴 Mệnh Tài Quan (Bản thể & Sự nghiệp)</span>
                <span class="tuvi-el-badge" style="--el-color:#f59e0b">🟡 Phúc Phối Di (Hoàn cảnh & Hôn nhân)</span>
                <span class="tuvi-el-badge" style="--el-color:#06b6d4">🔵 Lục Thân (Các mối quan hệ huyết thống)</span>
                <span class="tuvi-el-badge" style="--el-color:#84cc16">🟢 Khác (Tài sản & Sức khỏe)</span>
            </div>
        </div>
    </section>

    <section id="tuvi-tools-section" class="tuvi-lp-section tuvi-lp-toggle">
        <div class="tuvi-lp-container">
            <h2 class="tuvi-section-title tuvi-section-label">Nhóm Công Cụ Phân Tích</h2>
            <p class="tuvi-section-desc">Từ lập lá số cơ bản đến phân tích vận hạn chi tiết — hãy chọn chức năng bạn cần.</p>

            <div class="tuvi-tools-grid">
                <a href="<?= esc_url(home_url('/tu-vi-12-con-giap/lap-la-so/')) ?>" class="tuvi-tool-card">
                    <div class="tuvi-tool-header">
                        <div class="tuvi-tool-icon">🔮</div>
                        <h3>Lá Số Tử Vi</h3>
                    </div>
                    <p>An sao theo giờ sinh, hiển thị 12 cung, can chi, nạp âm, Trường Sinh, Thái Tuế.</p>
                    <p><strong>Xem Vận Mệnh:</strong> Tính cách, sự nghiệp, tài lộc, tình duyên.</p>
                    <p><strong>Xem Vận Hạn:</strong> Phân tích tiểu hạn, lưu niên, dự báo tiền bạc, tình cảm, công việc.</p>
                    <span class="tuvi-tool-cta">Bắt đầu →</span>
                </a>

                <a href="<?= esc_url(home_url('/tu-vi-12-con-giap/xem-ngay-tot-xau/')) ?>" class="tuvi-tool-card">
                    <div class="tuvi-tool-header">
                        <div class="tuvi-tool-icon">📅</div>
                        <h3>Xem Ngày Tốt Xấu</h3>
                    </div>
                    <p>Chọn ngày hoàng đạo, tránh hắc đạo. Tra cứu nhanh theo lịch âm dương, can chi, ngũ hành.</p>
                    <p><strong>Xem ngày:</strong> Cưới hỏi, khai trương, động thổ, xuất hành, ký kết.</p>
                    <p><strong>Luận giải:</strong> Nhị thập bát tú, trực ngày, sao tốt xấu, giờ hoàng đạo.</p>
                    <span class="tuvi-tool-cta">Bắt đầu →</span>
                </a>
                <a href="<?= esc_url(home_url('/tu-vi-12-con-giap/xem-hop-tuoi/')) ?>" class="tuvi-tool-card">
                    <div class="tuvi-tool-header">
                        <div class="tuvi-tool-icon">💘</div>
                        <h3>Xem Hợp Tuổi</h3>
                    </div>
                    <p>So sánh Thiên Can, Địa Chi, ngũ hành, cung mệnh để đánh giá mức độ hòa hợp.</p>
                    <p><strong>Xem bói:</strong> Tình duyên, hôn nhân, hợp tác làm ăn.</p>
                    <p><strong>Phân tích:</strong> Bát Trạch, mệnh cung, xung hợp, hóa giải.</p>
                    <span class="tuvi-tool-cta">Bắt đầu →</span>
                </a>
            </div>
        </div>
    </section>

    <section class="tuvi-lp-section tuvi-lp-toggle">
        <div class="tuvi-lp-container">
            <h3 class="tuvi-section-title tuvi-section-label">Câu hỏi thường gặp</h3>
            <div class="tuvi-faq-list">
                <?php foreach ($faqs as $faq): ?>
                    <div class="tuvi-faq-item">
                        <div class="tuvi-faq-q">
                            <span><?= esc_html($faq['q']) ?></span>
                            <span class="tuvi-faq-chevron">▼</span>
                        </div>
                        <div class="tuvi-faq-a"><?= esc_html($faq['a']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</div>