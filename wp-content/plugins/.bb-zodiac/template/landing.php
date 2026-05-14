<?php
/**
 * Template: Landing Page – Cung Hoàng Đạo
 * @package BbZodiac
 */
if (!defined('ABSPATH')) exit;

$signs = [
    ['id' => 'aries',       'name' => 'Bạch Dương', 'symbol' => '♈', 'date' => '21/3 – 19/4',  'element' => 'Lửa',  'keywords' => 'Quả quyết · Độc lập · Bốc đồng'],
    ['id' => 'taurus',      'name' => 'Kim Ngưu',   'symbol' => '♉', 'date' => '20/4 – 20/5',  'element' => 'Đất',  'keywords' => 'Bền bỉ · Hưởng thụ · Cứng đầu'],
    ['id' => 'gemini',      'name' => 'Song Tử',    'symbol' => '♊', 'date' => '21/5 – 20/6',  'element' => 'Khí',  'keywords' => 'Thông minh · Linh hoạt · Hai mặt'],
    ['id' => 'cancer',      'name' => 'Cự Giải',    'symbol' => '♋', 'date' => '21/6 – 22/7',  'element' => 'Nước', 'keywords' => 'Nhạy cảm · Yêu gia đình · Thù dai'],
    ['id' => 'leo',         'name' => 'Sư Tử',      'symbol' => '♌', 'date' => '23/7 – 22/8',  'element' => 'Lửa',  'keywords' => 'Kiêu hãnh · Hào phóng · Sĩ diện'],
    ['id' => 'virgo',       'name' => 'Xử Nữ',      'symbol' => '♍', 'date' => '23/8 – 22/9',  'element' => 'Đất',  'keywords' => 'Cầu toàn · Kỹ tính · Tận tụy'],
    ['id' => 'libra',       'name' => 'Thiên Bình', 'symbol' => '♎', 'date' => '23/9 – 22/10', 'element' => 'Khí',  'keywords' => 'Hòa nhã · Công bằng · Ba phải'],
    ['id' => 'scorpio',     'name' => 'Thiên Yết',  'symbol' => '♏', 'date' => '23/10 – 21/11','element' => 'Nước', 'keywords' => 'Bí ẩn · Đam mê · Đa nghi'],
    ['id' => 'sagittarius', 'name' => 'Nhân Mã',    'symbol' => '♐', 'date' => '22/11 – 21/12','element' => 'Lửa',  'keywords' => 'Lạc quan · Tự do · Vô tâm'],
    ['id' => 'capricorn',   'name' => 'Ma Kết',     'symbol' => '♑', 'date' => '22/12 – 19/1', 'element' => 'Đất',  'keywords' => 'Tham vọng · Kỷ luật · Lạnh lùng'],
    ['id' => 'aquarius',    'name' => 'Bảo Bình',   'symbol' => '♒', 'date' => '20/1 – 18/2',  'element' => 'Khí',  'keywords' => 'Sáng tạo · Độc lập · Bướng bỉnh'],
    ['id' => 'pisces',      'name' => 'Song Ngư',   'symbol' => '♓', 'date' => '19/2 – 20/3',  'element' => 'Nước', 'keywords' => 'Mơ mộng · Lãng mạn · Trốn tránh'],
];

$elementColors = [
    'Lửa'  => '#ef4444',
    'Đất'  => '#84cc16',
    'Khí'  => '#06b6d4',
    'Nước' => '#6366f1',
];

$faqs = [
    ['q' => 'Kết quả có chính xác không?',
     'a' => 'Kết quả được tính toán dựa trên hệ thống chiêm tinh học phương Tây, kết hợp phân tích Decan và vị trí các thiên thể. Nội dung phản ánh các đặc điểm và xu hướng theo phương pháp này — không phải cơ sở khẳng định tuyệt đối về số phận.'],
    ['q' => 'Sử dụng có mất phí không?',
     'a' => 'Hoàn toàn miễn phí. Chúng tôi không thu phí dưới bất kỳ hình thức nào, không bán khóa học và không cung cấp dịch vụ xem bói tính tiền.'],
    ['q' => 'Chiêm tinh học có phải mê tín không?',
     'a' => 'Chiêm tinh học là hệ thống biểu tượng và niềm tin có lịch sử hơn 2.500 năm. Chúng tôi xem đây là công cụ chiêm nghiệm cá nhân — giúp bạn nhìn lại bản thân và định hướng tư duy, không phải phương tiện phán xét hay tiên đoán số phận.'],
    ['q' => 'Decan là gì? Tại sao lại phân tích thêm?',
     'a' => 'Mỗi cung hoàng đạo được chia thành 3 Decan (mỗi Decan ~10 ngày), mỗi Decan chịu ảnh hưởng của một hành tinh phụ khác nhau. Điều này giải thích tại sao hai người cùng cung nhưng lại có tính cách khá khác biệt.'],
    ['q' => 'Giao đỉnh (Cusp) là gì?',
     'a' => 'Giao đỉnh là khoảng thời gian chuyển tiếp giữa hai cung (~5–7 ngày). Người sinh vào giai đoạn này mang năng lượng pha trộn của cả hai cung liền kề, tạo nên tính cách phức tạp và đa dạng hơn.'],
    ['q' => 'Dữ liệu có được lưu không?',
     'a' => 'Thông tin được xử lý trực tiếp và không được lưu trữ trên hệ thống. Không có dữ liệu cá nhân nào được ghi lại hoặc sử dụng cho mục đích khác.'],
];
?>

<div class="zdc-lp fortune-page" id="zdc-landing-wrapper">
    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">♈ Khám Phá Bản Thân</div>
        <h1 class="ftn-hero-title">Giải Mã <span>Cung Hoàng Đạo</span><br>Của Bạn</h1>
        <p>Nhập ngày sinh để khám phá bản đồ sao cá nhân: tính cách, điểm mạnh, điểm yếu, tình cảm, sự nghiệp — được phân tích chuyên sâu theo Cung Mặt Trời, Decan và sự tương tác của các hành tinh.</p>        <div class="zdc-hero-actions">
            <button class="zdc-btn-primary" onclick="document.getElementById('zdc-tools-section').scrollIntoView({behavior:'smooth'})">Khám phá</button>
            <button class="zdc-btn-ghost" onclick="document.getElementById('zdc-about-section').scrollIntoView({behavior:'smooth'})">Tìm hiểu thêm</button>
        </div>
    </section>
    <section id="zdc-about-section" class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <h2 class="zdc-section-title">Chiêm tinh học là gì?</h2>
            <p class="zdc-section-desc">Chiêm tinh học phương Tây là hệ thống nghiên cứu mối liên hệ giữa vị trí các thiên thể lúc bạn chào đời và đặc điểm tính cách, xu hướng hành vi của bạn. Khác với tử vi đơn giản chỉ dùng 12 cung, hệ thống này còn đào sâu vào Decan, Giao đỉnh và Thần Số Học để tạo ra bức chân dung cực kỳ cá nhân hóa.</p>
            <div class="zdc-intro-grid">
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">🌟</div>
                        <h3>Cung Mặt Trời</h3>
                    </div>
                    <p>Xác định theo ngày sinh, phản ánh bản ngã cốt lõi, cách bạn thể hiện bản thân với thế giới và những gì bạn khao khát trở thành.</p>
                </div>
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">🔬</div>
                        <h3>Decan – Biến thể 10 ngày</h3>
                    </div>
                    <p>Mỗi cung chia thành 3 Decan, mỗi Decan chịu ảnh hưởng hành tinh phụ khác nhau. Giải thích tại sao hai người cùng cung lại khác biệt về tính cách.</p>
                </div>
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">🌒</div>
                        <h3>Mặt Trăng & Cung Mọc</h3>
                    </div>
                    <p>Mặt Trăng đại diện cho thế giới cảm xúc nội tâm, trong khi Cung Mọc (Ascendant) quyết định cách bạn tiếp cận thế giới và ấn tượng đầu tiên bạn để lại cho người khác.</p>
                </div>
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">⚡</div>
                        <h3>Giao Đỉnh (Cusp)</h3>
                    </div>
                    <p>Nếu bạn sinh vào khoảng chuyển giao giữa hai cung, bạn mang năng lượng pha trộn độc đáo — phức tạp hơn nhưng cũng thú vị hơn rất nhiều.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <div class="zdc-section-label">12 Cung Hoàng Đạo</div>
            <h2 class="zdc-section-title">Bạn thuộc cung nào?</h2>
            <p class="zdc-section-desc">Click vào cung của bạn để xem ngày sinh và năng lượng đặc trưng — hoặc nhập ngày sinh bên dưới để nhận phân tích chuyên sâu.</p>
            <div class="zdc-signs-grid">
                <?php foreach ($signs as $s):
                    $color = $elementColors[$s['element']] ?? '#7c3aed';
                ?>
                <div class="zdc-sign-card" data-element="<?= esc_attr($s['element']) ?>" style="--sign-color:<?= $color ?>">
                    <div class="zdc-sign-symbol"><?= $s['symbol'] ?></div>
                    <div class="zdc-sign-name"><?= esc_html($s['name']) ?></div>
                    <div class="zdc-sign-date"><?= esc_html($s['date']) ?></div>
                    <div class="zdc-sign-element"><?= esc_html($s['element']) ?></div>
                    <div class="zdc-sign-keywords"><?= esc_html($s['keywords']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="zdc-element-legend">
                <span class="zdc-el-badge" style="--el-color:#ef4444">🔥 Lửa — Bạch Dương · Sư Tử · Nhân Mã</span>
                <span class="zdc-el-badge" style="--el-color:#84cc16">🌱 Đất — Kim Ngưu · Xử Nữ · Ma Kết</span>
                <span class="zdc-el-badge" style="--el-color:#06b6d4">🌬️ Khí — Song Tử · Thiên Bình · Bảo Bình</span>
                <span class="zdc-el-badge" style="--el-color:#6366f1">💧 Nước — Cự Giải · Thiên Yết · Song Ngư</span>
            </div>
        </div>
    </section>
    <section id="zdc-tools-section" class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <div class="zdc-section-label">Các công cụ</div>
            <h2 class="zdc-section-title">5 Nhóm Chiêm Tinh</h2>
            <p class="zdc-section-desc">Lựa chọn phương thức tiếp cận phù hợp để giải mã các thông điệp từ vũ trụ dành riêng cho bạn.</p>
            <div class="zdc-tools-grid">
                <a href="<?= esc_url(home_url('/cung-hoang-dao/tinh-cach/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">🔮</div>
                        <h3>Tính Cách Con Người Thật Của Bạn</h3>
                    </div>
                    <p>Dựa vào ngày sinh → xác định cung → phân tích tính cách, điểm mạnh, điểm yếu, tình cảm và sự nghiệp của bạn.</p>
                    <div class="zdc-tool-examples">Bạch Dương: năng động, nóng tính · Xử Nữ: cầu toàn, chi tiết</div>
                    <span class="zdc-tool-cta">Bắt đầu →</span>
                </a>

                <a href="<?= esc_url(home_url('/cung-hoang-dao/tinh-yeu/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">💘</div>
                        <h3>Bói Tình Yêu (Hợp – Khắc)</h3>
                    </div>
                    <p>So sánh 2 cung hoàng đạo để xem mức độ hòa hợp tình cảm, điểm chung và những mâu thuẫn tiềm ẩn.</p>
                    <div class="zdc-tool-examples">Sư Tử hợp Nhân Mã · Cự Giải hợp Song Ngư</div>
                    <span class="zdc-tool-cta">Bắt đầu →</span>
                </a>

                <a href="<?= esc_url(home_url('/cung-hoang-dao/tu-vi/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">🗓️</div>
                        <h3>Tử Vi Hàng Ngày</h3>
                    </div>
                    <p>Dự đoán vận mệnh theo ngày / tuần / tháng: công việc, tài chính, tình cảm — cập nhật liên tục theo chu kỳ thiên văn.</p>
                    <div class="zdc-tool-examples">Dự báo theo ngày · Tuần · Tháng</div>
                    <span class="zdc-tool-cta">Bắt đầu →</span>
                </a>

                <a href="<?= esc_url(home_url('/cung-hoang-dao/ban-do-sao/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">🌙</div>
                        <h3>Bản Đồ Sao Cá Nhân</h3>
                    </div>
                    <p>Phân tích chuyên sâu dựa trên ngày sinh, giờ sinh và nơi sinh. Bao gồm Mặt Trời, Mặt Trăng và vị trí các hành tinh.</p>
                    <div class="zdc-tool-examples">Natal Chart · Cung Mặt Trăng · Cung Ascendant</div>
                    <span class="zdc-tool-cta">Bắt đầu →</span>
                </a>
            </div>
        </div>
    </section>
    <section class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <h3 class="zdc-section-title">Câu hỏi thường gặp</h3>
            <div class="zdc-faq-list">
                <?php foreach ($faqs as $faq): ?>
                <div class="zdc-faq-item">
                    <div class="zdc-faq-q">
                        <span><?= esc_html($faq['q']) ?></span>
                        <span class="zdc-faq-chevron">▼</span>
                    </div>
                    <div class="zdc-faq-a"><?= esc_html($faq['a']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>
