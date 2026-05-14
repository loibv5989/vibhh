<?php

/**
 * Template: Landing Page – Bát Tự (Tứ Trụ)
 * @package BbTuVi
 */
if (!defined('ABSPATH')) exit;

$tu_tru = [
    [
        'id'       => 'nien_tru',
        'name'     => 'Niên Trụ',
        'symbol'   => '年',
        'group'    => 'Tứ Trụ',
        'label'    => 'Năm sinh',
        'keywords' => 'Tổ tiên · Thời thơ ấu · Phúc phần',
        'desc'     => 'Phản ánh nền tảng gia đình, gốc rễ tổ tiên và môi trường thời thơ ấu.',
    ],
    [
        'id'       => 'nguyet_tru',
        'name'     => 'Nguyệt Trụ',
        'symbol'   => '月',
        'group'    => 'Tứ Trụ',
        'label'    => 'Tháng sinh',
        'keywords' => 'Cha mẹ · Tuổi thanh xuân · Sự nghiệp',
        'desc'     => 'Liên quan tới cha mẹ, tuổi niên thiếu và xu hướng nghề nghiệp.',
    ],
    [
        'id'       => 'nhat_tru',
        'name'     => 'Nhật Trụ',
        'symbol'   => '日',
        'group'    => 'Tứ Trụ',
        'label'    => 'Ngày sinh',
        'keywords' => 'Nhật chủ · Bản ngã · Hôn nhân',
        'desc'     => 'Trụ quan trọng nhất — Thiên Can ngày sinh chính là Nhật Chủ, đại diện bản thân.',
    ],
    [
        'id'       => 'thoi_tru',
        'name'     => 'Thời Trụ',
        'symbol'   => '時',
        'group'    => 'Tứ Trụ',
        'label'    => 'Giờ sinh',
        'keywords' => 'Con cái · Cuối đời · Kết quả',
        'desc'     => 'Phản ánh con cái, sự nghiệp hậu vận và thành quả cuối đời.',
    ],
];

$thap_than = [
    ['ten' => 'Tỷ Kiên',   'symbol' => '比肩', 'am_duong' => 'Dương', 'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Bạn bè · Anh em · Cạnh tranh',   'group' => 'Tỷ Kiếp'],
    ['ten' => 'Kiếp Tài',  'symbol' => '劫財', 'am_duong' => 'Âm',   'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Tài lộc bấp bênh · Tranh đoạt',  'group' => 'Tỷ Kiếp'],
    ['ten' => 'Thực Thần', 'symbol' => '食神', 'am_duong' => 'Dương', 'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Tài năng · Thực phúc · Sáng tạo', 'group' => 'Thực Thương'],
    ['ten' => 'Thương Quan','symbol' => '傷官', 'am_duong' => 'Âm',   'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Thông minh · Phá cách · Cá tính',  'group' => 'Thực Thương'],
    ['ten' => 'Thiên Tài',  'symbol' => '偏財', 'am_duong' => 'Dương', 'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Tài lộc · Cha · Đào hoa',         'group' => 'Tài Tinh'],
    ['ten' => 'Chính Tài',  'symbol' => '正財', 'am_duong' => 'Âm',   'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Vợ · Lao động · Tích lũy',        'group' => 'Tài Tinh'],
    ['ten' => 'Thiên Quan', 'symbol' => '七殺', 'am_duong' => 'Dương', 'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Thất Sát · Quyền uy · Áp lực',   'group' => 'Quan Sát'],
    ['ten' => 'Chính Quan', 'symbol' => '正官', 'am_duong' => 'Âm',   'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Chồng · Pháp luật · Danh dự',     'group' => 'Quan Sát'],
    ['ten' => 'Thiên Ấn',  'symbol' => '偏印', 'am_duong' => 'Dương', 'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Học thuật · Trực giác · Mẹ kế',   'group' => 'Ấn Tinh'],
    ['ten' => 'Chính Ấn',  'symbol' => '正印', 'am_duong' => 'Âm',   'vu_hanh' => 'Kim/Mộc/Thủy/Hỏa/Thổ', 'y_nghia' => 'Mẹ · Học vấn · Quý nhân',         'group' => 'Ấn Tinh'],
];

$groupColors = [
    'Tỷ Kiếp'    => '#ef4444',
    'Thực Thương' => '#f59e0b',
    'Tài Tinh'    => '#10b981',
    'Quan Sát'    => '#6366f1',
    'Ấn Tinh'     => '#06b6d4',
];

$faqs = [
    [
        'q' => 'Bát Tự (Tứ trụ) khác Tử Vi Đẩu Số như thế nào?',
        'a' => 'Tử Vi Đẩu Số an sao vào 12 cung cố định dựa theo Âm lịch và giờ sinh, thiên về mô tả vận mệnh từng cung. Bát Tự (Tứ Trụ) phân tích mối quan hệ Can-Chi qua lăng kính Ngũ Hành và Thập Thần — thiên về cân bằng nguyên tố và cấu trúc nhân cách hơn là sơ đồ sao bàn.',
    ],
    [
        'q' => 'Nhật Chủ là gì và tại sao quan trọng?',
        'a' => 'Nhật Chủ (日主) là Thiên Can của Nhật Trụ (ngày sinh). Đây là "cái tôi" trung tâm của lá số — mọi Can Chi còn lại được luận giải theo quan hệ sinh/khắc với Nhật Chủ để xác định Thập Thần, từ đó phân tích tính cách, tài vận, hôn nhân và sự nghiệp.',
    ],
    [
        'q' => 'Tiết Khí ảnh hưởng thế nào đến Nguyệt Trụ?',
        'a' => 'Trong Bát Tự, tháng không tính theo Âm lịch thông thường mà theo 24 Tiết Khí thiên văn. Ví dụ, tháng Dần bắt đầu từ Lập Xuân (khoảng 4/2), không phải mồng 1 tháng Giêng. Hệ thống của chúng tôi tính toán Nguyệt Trụ chuẩn xác theo tiết khí thiên văn thực tế.',
    ],
    [
        'q' => 'Dụng Thần là gì và cách chọn?',
        'a' => 'Dụng Thần (用神) là hành hoặc Thập Thần có lợi nhất cho Nhật Chủ, giúp cân bằng cục diện lá số. Chọn Dụng Thần đúng là cốt lõi của việc phân tích Bát Tự — từ đó xác định màu sắc, hướng nhà, ngành nghề, và thời điểm Đại Vận thuận lợi.',
    ],
    [
        'q' => 'Đại Vận và Lưu Niên tính thế nào?',
        'a' => 'Đại Vận là chu kỳ 10 năm, khởi từ ngày sinh theo hướng thuận/nghịch Can Chi dựa vào giới tính và Can Năm. Lưu Niên là năm hiện tại, Can Chi của năm đó tác động lên lá số. Hệ thống tự động tính tuổi khởi đại vận và liệt kê từng vận theo thứ tự thời gian.',
    ],
    [
        'q' => 'Thần Sát có vai trò gì trong Bát Tự?',
        'a' => 'Thần Sát là các sao phụ (Thiên Ất Quý Nhân, Văn Xương, Đào Hoa, Dịch Mã, Kiếp Sát, Bạch Hổ...) được xác định dựa trên Can/Chi của các trụ. Chúng bổ sung thêm màu sắc luận giải về quý nhân, đào hoa, tai ương hay biến động — không thay thế phân tích Thập Thần cơ bản.',
    ]
];

$ngu_hanh_colors = [
    'Kim'  => '#c9a227',
    'Mộc'  => '#16a34a',
    'Thủy' => '#2563eb',
    'Hỏa'  => '#dc2626',
    'Thổ'  => '#92400e',
];
?>

<div class="battu-page" id="battu-landing-wrapper">
    <section class="battu-hero battu-lp-toggle">
        <div class="battu-hero-badge">☯ Giải Mã Tứ Trụ <br> Ngũ Hành · Thập Thần</div>
        <h1 class="battu-hero-title">Phân Tích <span>Lá Số Bát Tự</span><br>Tứ Trụ Mệnh Lý</h1>
        <p>Lập Tứ Trụ theo tiết khí thiên văn kết hợp luận giải Thập Thần, Dụng Thần, Thần Sát và Đại Vận dựa trên tri thức cổ truyền phương Đông giúp mỗi người hiểu bản thân và chủ động nắm bắt vận mệnh.</p>
    </section>
    <section class="battu-calc-card">
        <h2 class="battu-form-title">Lập Lá Số Bát Tự (Tứ Trụ)</h2>
        <p class="battu-form-subtitle">
            Nhập ngày giờ sinh dương lịch để an lập Tứ Trụ theo tiết khí thiên văn và xem phân tích chi tiết về ngũ hành, thập thần, dụng thần cùng các chu kỳ đại vận.
        </p>
        <form method="post" class="battu-form-inline" id="battu-form">
            <div>
                <label class="battu-label-inline">Họ và tên</label>
                <input type="text" class="battu-input-inline battu-input-name" name="battu_ho_ten" placeholder="Vd: Bùi Minh Anh" pattern="[a-zA-ZÀ-ỹ\s\-]*" />
            </div>
            <div>
                <label class="battu-label-inline">Ngày/tháng/năm <span>(Dương lịch)</span></label>
                <input type="text" class="battu-input-inline battu-input-date" name="battu_ngay_sinh" placeholder="Vd: 15/8/2001" maxlength="10" required />
            </div>
            <div>
                <label class="battu-label-inline">Giờ sinh <span>(Định dạng 24h)</span></label>
                <input type="text" class="battu-input-inline battu-input-time" name="battu_gio_sinh" placeholder="Vd: 14:30" maxlength="5" required />
            </div>
            <div>
                <label class="battu-label-inline">Giới tính</label>
                <select name="battu_gioi_tinh" class="battu-select-inline" required>
                    <option value="" disabled selected>-- Chọn --</option>
                    <option value="nam">Nam</option>
                    <option value="nu">Nữ</option>
                </select>
            </div>
            <div class="battu-form-group-inline">
                <button type="submit" class="battu-btn-submit justify-center">
                    <span class="battu-btn-text">Lập Lá Số</span>
                    <span class="battu-btn-loading" style="display: none;"><span class="battu-spinner"></span> Đang xử lý...</span>
                </button>
            </div>
        </form>

        <div id="battu-error" class="battu-error-inline" style="display:none;"></div>
        <div id="battu-result"></div>
    </section>
<!--    ẩn từ đây -->
    <section id="battu-about-section" class="battu-lp-section battu-lp-toggle">
        <div class="battu-lp-container">
            <h2 class="battu-section-title">Bát Tự (Tứ Trụ) là gì?</h2>
            <p class="battu-section-desc">Bát Tự (八字) hay Tứ Trụ (四柱) là bộ môn mệnh lý cổ phương Đông, sử dụng 8 ký tự Can Chi của bốn trụ Năm – Tháng – Ngày – Giờ sinh để phác họa bức tranh toàn cảnh về tính cách, tài vận, hôn nhân, sự nghiệp và các chu kỳ vận hạn trong cuộc đời.</p>
            <div class="battu-intro-grid">
                <div class="battu-intro-card">
                    <div class="battu-intro-card-header">
                        <div class="battu-intro-icon">🌿</div>
                        <h3>Ngũ Hành Cân Bằng</h3>
                    </div>
                    <p>Kim – Mộc – Thủy – Hỏa – Thổ tương sinh tương khắc tạo nên cấu trúc lá số. Xác định hành vượng, hành suy và Dụng Thần là bước cốt lõi của mọi luận giải.</p>
                </div>
                <div class="battu-intro-card">
                    <div class="battu-intro-card-header">
                        <div class="battu-intro-icon">🏛️</div>
                        <h3>Tứ Trụ & Tàng Can</h3>
                    </div>
                    <p>Mỗi Địa Chi chứa 1–3 Thiên Can ẩn bên trong (Tàng Can), tạo ra bức tranh Can Chi phong phú hơn 8 chữ bề mặt — đây là nơi ẩn chứa năng lượng tiềm ẩn của lá số.</p>
                </div>
                <div class="battu-intro-card">
                    <div class="battu-intro-card-header">
                        <div class="battu-intro-icon">⚖️</div>
                        <h3>Thập Thần Luận</h3>
                    </div>
                    <p>10 thần tương quan (Tỷ Kiên, Kiếp Tài, Thực Thần, Thương Quan, Thiên Tài, Chính Tài, Thất Sát, Chính Quan, Thiên Ấn, Chính Ấn) phân tích mối quan hệ của từng Can với Nhật Chủ.</p>
                </div>
                <div class="battu-intro-card">
                    <div class="battu-intro-card-header">
                        <div class="battu-intro-icon">⏳</div>
                        <h3>Đại Vận & Lưu Niên</h3>
                    </div>
                    <p>Chu kỳ Đại Vận 10 năm xác định giai đoạn vận khí tổng thể; Lưu Niên (năm hiện tại) và Lưu Nguyệt (tháng hiện tại) kích hoạt các sự kiện cụ thể trong năm.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="battu-lp-section battu-lp-toggle">
        <div class="battu-lp-container">
            <div class="battu-section-label">Cấu Trúc Lá Số</div>
            <h2 class="battu-section-title">Tứ Trụ — Bốn Trụ Mệnh</h2>
            <p class="battu-section-desc">Mỗi trụ là một cặp Thiên Can – Địa Chi, đại diện cho một giai đoạn và một khía cạnh của cuộc đời. Tương tác giữa bốn trụ tạo nên cách cục và hình thái lá số.</p>

            <div class="battu-tutru-grid">
                <?php foreach ($tu_tru as $tru): ?>
                    <div class="battu-tutru-card">
                        <div class="battu-tutru-symbol"><?= $tru['symbol'] ?></div>
                        <div class="battu-tutru-label"><?= esc_html($tru['label']) ?></div>
                        <div class="battu-tutru-name"><?= esc_html($tru['name']) ?></div>
                        <div class="battu-tutru-keywords"><?= esc_html($tru['keywords']) ?></div>
                        <p class="battu-tutru-desc"><?= esc_html($tru['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="battu-sample-chart" aria-label="Ví dụ minh họa Tứ Trụ">
                <div class="battu-sample-label">Ví dụ minh họa · Bát Tự (8 chữ)</div>
                <div class="battu-sample-row">
                    <?php
                    $sample = [
                        ['tru' => 'Niên',   'can' => 'Giáp', 'chi' => 'Tý',  'hanh_can' => 'Mộc',  'hanh_chi' => 'Thủy'],
                        ['tru' => 'Nguyệt', 'can' => 'Bính', 'chi' => 'Dần', 'hanh_can' => 'Hỏa',  'hanh_chi' => 'Mộc'],
                        ['tru' => 'Nhật',   'can' => 'Mậu',  'chi' => 'Ngọ', 'hanh_can' => 'Thổ',  'hanh_chi' => 'Hỏa'],
                        ['tru' => 'Thời',   'can' => 'Canh', 'chi' => 'Thân','hanh_can' => 'Kim',   'hanh_chi' => 'Kim'],
                    ];
                    foreach ($sample as $i => $s):
                        $cc = $ngu_hanh_colors[$s['hanh_can']] ?? '#c9a227';
                        $ci = $ngu_hanh_colors[$s['hanh_chi']] ?? '#c9a227';
                        ?>
                        <div class="battu-sample-col <?= $i === 2 ? 'is-nhat-tru' : '' ?>">
                            <div class="battu-sample-tru"><?= esc_html($s['tru']) ?></div>
                            <div class="battu-sample-can" style="color:<?= $cc ?>;"><?= esc_html($s['can']) ?></div>
                            <div class="battu-sample-divider"></div>
                            <div class="battu-sample-chi" style="color:<?= $ci ?>;"><?= esc_html($s['chi']) ?></div>
                            <div class="battu-sample-hanh">
                                <span style="color:<?= $cc ?>;"><?= esc_html($s['hanh_can']) ?></span>
                                <span style="color:<?= $ci ?>;"><?= esc_html($s['hanh_chi']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="battu-sample-note">★ Nhật Trụ (Mậu Ngọ) — Thiên Can ngày sinh là <strong>Nhật Chủ</strong>, đại diện bản thân chủ mệnh</div>
            </div>
        </div>
    </section>
    <section class="battu-lp-section battu-lp-toggle">
        <div class="battu-lp-container">
            <div class="battu-section-label">Thập Thần</div>
            <h2 class="battu-section-title">10 Thần Tương Quan</h2>
            <p class="battu-section-desc">Thập Thần xác định quan hệ sinh khắc giữa từng Can Chi trong lá số với Nhật Chủ, là nền tảng để luận đoán tài vận, quan lộc, hôn nhân và các mối quan hệ.</p>

            <div class="battu-thapthan-grid">
                <?php foreach ($thap_than as $tt):
                    $color = $groupColors[$tt['group']] ?? '#c9a227';
                    ?>
                    <div class="battu-tt-card" style="--tt-color: <?= $color ?>;">
                        <div class="battu-tt-header">
                            <span class="battu-tt-symbol"><?= $tt['symbol'] ?></span>
                            <span class="battu-tt-name"><?= esc_html($tt['ten']) ?></span>
                        </div>
                        <div class="battu-tt-group" style="color:<?= $color ?>;"><?= esc_html($tt['group']) ?></div>
                        <div class="battu-tt-keywords"><?= esc_html($tt['y_nghia']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="battu-thapthan-legend">
                <?php foreach ($groupColors as $grp => $col): ?>
                    <span class="battu-tt-badge" style="--tt-color:<?= $col ?>;"><?= esc_html($grp) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="battu-lp-section battu-lp-toggle">
        <div class="battu-lp-container">
            <div class="battu-section-label">Ngũ Hành</div>
            <h2 class="battu-section-title">Vòng Sinh Khắc Ngũ Hành</h2>
            <p class="battu-section-desc">Mọi Can Chi đều mang một hành. Quan hệ tương sinh (hỗ trợ) và tương khắc (kiềm chế) giữa các hành quyết định chất lượng của từng Thập Thần trong lá số.</p>

            <div class="battu-wuxing-wrap">
                <div class="battu-wuxing-grid">
                    <?php
                    $elements = [
                        ['hanh' => 'Kim',  'color' => '#c9a227', 'icon' => '⚙', 'can' => 'Canh · Tân', 'chi' => 'Thân · Dậu', 'tinh_cach' => 'Cương nghị · Chính trực · Quyết đoán'],
                        ['hanh' => 'Mộc',  'color' => '#16a34a', 'icon' => '🌿', 'can' => 'Giáp · Ất', 'chi' => 'Dần · Mão',  'tinh_cach' => 'Nhân từ · Phát triển · Sáng tạo'],
                        ['hanh' => 'Thủy', 'color' => '#2563eb', 'icon' => '💧', 'can' => 'Nhâm · Quý', 'chi' => 'Tý · Hợi',  'tinh_cach' => 'Thông minh · Linh hoạt · Học thuật'],
                        ['hanh' => 'Hỏa',  'color' => '#dc2626', 'icon' => '🔥', 'can' => 'Bính · Đinh', 'chi' => 'Ngọ · Tỵ', 'tinh_cach' => 'Nhiệt huyết · Biểu đạt · Năng động'],
                        ['hanh' => 'Thổ',  'color' => '#92400e', 'icon' => '🪨', 'can' => 'Mậu · Kỷ',  'chi' => 'Thìn · Tuất · Sửu · Mùi', 'tinh_cach' => 'Bền vững · Trung tín · Thực tế'],
                    ];
                    foreach ($elements as $el): ?>
                        <div class="battu-wuxing-card" style="--wux-color:<?= $el['color'] ?>;">
                            <div class="battu-wux-icon"><?= $el['icon'] ?></div>
                            <div class="battu-wux-name" style="color:<?= $el['color'] ?>;"><?= esc_html($el['hanh']) ?></div>
                            <div class="battu-wux-can"><strong>Thiên Can:</strong> <?= esc_html($el['can']) ?></div>
                            <div class="battu-wux-chi"><strong>Địa Chi:</strong> <?= esc_html($el['chi']) ?></div>
                            <div class="battu-wux-tinh"><?= esc_html($el['tinh_cach']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="battu-wuxing-cycle">
                    <div class="battu-wux-cycle-row">
                        <span class="battu-wux-arrow sinh">Tương Sinh →</span>
                        <span class="battu-wux-cycle-text">Mộc → Hỏa → Thổ → Kim → Thủy → Mộc</span>
                    </div>
                    <div class="battu-wux-cycle-row">
                        <span class="battu-wux-arrow khac">Tương Khắc →</span>
                        <span class="battu-wux-cycle-text">Mộc → Thổ → Thủy → Hỏa → Kim → Mộc</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="battu-lp-section battu-lp-toggle">
        <div class="battu-lp-container">
            <div class="battu-section-label">Quy Trình</div>
            <h2 class="battu-section-title">Cách Đọc Lá Số Bát Tự</h2>
            <p class="battu-section-desc">Luận giải Bát Tự theo trình tự chuẩn từ nền tảng đến chuyên sâu, đảm bảo không bỏ sót yếu tố quan trọng.</p>

            <div class="battu-steps-list">
                <?php
                $steps = [
                    ['no' => '01', 'title' => 'Xác định Nhật Chủ',       'desc' => 'Xem Thiên Can của Nhật Trụ. Đây là "cái tôi" của lá số, mọi phân tích xoay quanh điểm này.'],
                    ['no' => '02', 'title' => 'Phân tích Thân Vượng/Nhược', 'desc' => 'Đánh giá tổng lực của Nhật Chủ qua sự hỗ trợ của Tỷ Kiếp và Ấn Tinh trong cả 4 trụ và Tàng Can.'],
                    ['no' => '03', 'title' => 'Xác định Dụng Thần',       'desc' => 'Chọn hành hoặc Thập Thần cân bằng lá số, đây là "thuốc" của lá số, chỉ ra hướng phát triển.'],
                    ['no' => '04', 'title' => 'Luận Thập Thần',           'desc' => 'Phân tích từng Thập Thần xuất hiện trong 4 trụ, xem xét thế lực và cách cục hình thành.'],
                    ['no' => '05', 'title' => 'Đọc Thần Sát',             'desc' => 'Bổ sung luận giải qua các Thần Sát: Quý Nhân, Đào Hoa, Dịch Mã, Kiếp Sát, Hoa Cái...'],
                    ['no' => '06', 'title' => 'Xem Đại Vận & Lưu Niên',  'desc' => 'Đặt Dụng/Kỵ Thần vào Đại Vận để dự báo giai đoạn thuận/nghịch, kết hợp Lưu Niên cho năm cụ thể.'],
                ];
                foreach ($steps as $s): ?>
                    <div class="battu-step-item">
                        <div class="battu-step-no"><?= $s['no'] ?></div>
                        <div class="battu-step-body">
                            <div class="battu-step-title"><?= esc_html($s['title']) ?></div>
                            <div class="battu-step-desc"><?= esc_html($s['desc']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="battu-lp-section battu-lp-toggle">
        <div class="battu-lp-container">
            <h3 class="battu-section-title battu-section-label">Câu hỏi thường gặp</h3>
            <div class="battu-faq-list">
                <?php foreach ($faqs as $faq): ?>
                    <div class="battu-faq-item">
                        <div class="battu-faq-q">
                            <span><?= esc_html($faq['q']) ?></span>
                            <span class="battu-faq-chevron">▼</span>
                        </div>
                        <div class="battu-faq-a"><?= esc_html($faq['a']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>
