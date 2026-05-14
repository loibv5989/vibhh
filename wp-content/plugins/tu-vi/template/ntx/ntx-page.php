<?php

if (!defined("ABSPATH")) {
    exit();
}

$has_result = !empty($result) && !isset($result["error"]);
$is_ajax = $is_ajax ?? false;

$getLevelClass = function (string $level): string {
    $map = [
            "Rat tot"    => "tuvi-result-good",
            "Tot"        => "tuvi-result-good",
            "Khong nen"  => "tuvi-result-bad",
    ];
    return $map[$level] ?? "tuvi-result-neutral";
};

$getLevelLabel = function (string $level): string {
    $map = [
            "Rat tot"   => "RẤT TỐT",
            "Tot"       => "TỐT",
            "Trung binh"=> "TRUNG BÌNH",
            "Khong nen" => "KHÔNG NÊN",
    ];
    return $map[$level] ?? mb_strtoupper($level, 'UTF-8');
};

$gioMap = [
        'Tý'  => '23:00-01:00', 'Sửu'  => '01:00-03:00', 'Dần' => '03:00-05:00',
        'Mão' => '05:00-07:00', 'Thìn' => '07:00-09:00', 'Tỵ'  => '09:00-11:00',
        'Ngọ' => '11:00-13:00', 'Mùi'  => '13:00-15:00', 'Thân'=> '15:00-17:00',
        'Dậu' => '17:00-19:00', 'Tuất' => '19:00-21:00', 'Hợi' => '21:00-23:00',
];

$renderSingleDay = function ($result, $getLevelClass, $getLevelLabel) use ($gioMap) {
    $eval       = $result["evaluation"] ?? [];
    $score      = (int)($eval["score"]  ?? 0);
    $levelClass = $getLevelClass($eval["level"] ?? "");
    $levelLabel = $getLevelLabel($eval["level"] ?? "");

    $formatGio = function ($gio) use ($gioMap) {
        return isset($gioMap[$gio]) ? "{$gio} ({$gioMap[$gio]})" : $gio;
    };

    $hasWarnings = !empty($eval["warnings"]);
    $hasReasons  = !empty($eval["reasons"]);
    ?>
    <div class="tuvi-result-box">
        <?php if (!empty($result['battu'])): ?>
            <div class="tuvi-battu-info">
                Mệnh tuổi: Tuổi <strong><?= esc_html($result['battu']['nam_sinh']) ?></strong>
                | Nhật Chủ <strong class="tuvi-nhat-chu"><?= esc_html($result['battu']['nhat_chu']) ?></strong>
            </div>
        <?php endif; ?>

        <div class="tuvi-result-item tuvi-ntx-header <?= $levelClass ?>">
            <div class="row-wrap">
                <strong class="label">Cát hung:</strong>
                <span class="value"><?= esc_html($levelLabel) ?></span>
            </div>
            <div class="row-wrap">
                <strong class="label">Ngày cần xem:</strong>
                <span class="value">
                    <?= esc_html(date('d/m/Y', strtotime($result['date']))) ?>
                    (Âm lịch: <?= esc_html(
                            str_pad($result['lunar']['day']   ?? '', 2, '0', STR_PAD_LEFT) . '/' .
                            str_pad($result['lunar']['month'] ?? '', 2, '0', STR_PAD_LEFT) . '/' .
                            ($result['lunar']['year'] ?? '') .
                            (!empty($result['lunar']['leap']) ? ' Nhuận' : '')
                    ) ?>)
                </span>
            </div>
            <div class="row-wrap">
                <strong class="label">Sự Kiện:</strong>
                <span class="value"><?= esc_html($result["purpose_label"] ?? "") ?></span>
            </div>
        </div>

        <?php if ($score < 0): ?>
            <div class="tuvi-result-item tuvi-ct-alert">
                <p class="content">Lưu ý: Các chỉ số phong thủy trong ngày không thuận lợi cho sự kiện dự kiến. Tỷ lệ xung khắc cao — nên chọn ngày khác.</p>
            </div>
        <?php elseif ($score >= 12 && $hasWarnings): ?>
            <div class="tuvi-result-item tuvi-ct-alert-mild">
                <p class="content">Ghi chú: Ngày rất tốt nhờ nhiều yếu tố cát lợi hội tụ. Các yếu tố cần cân nhắc bên dưới là những điểm trừ nhỏ đã được bù đắp — không ảnh hưởng đến kết luận chung.</p>
            </div>
        <?php elseif ($score >= 6 && $hasWarnings): ?>
            <div class="tuvi-result-item tuvi-ct-alert-mild">
                <p class="content">Ghi chú: Ngày nhìn chung tốt. Các yếu tố cần cân nhắc bên dưới là điểm trừ nhỏ đã được tính vào tổng thể mức độ cát hung.</p>
            </div>
        <?php endif; ?>

        <div class="tuvi-data-grid tuvi-mt-15 tuvi-mb-20">
            <div class="tuvi-data-card">
                <div class="tuvi-data-card-title">Bát Tự Ngày</div>
                <div class="tuvi-data-card-val tuvi-ct-accent-text"><?= esc_html($result["can_chi"] ?? "") ?></div>
            </div>
            <div class="tuvi-data-card">
                <div class="tuvi-data-card-title">12 Trực</div>
                <div class="tuvi-data-card-val tuvi-ct-accent-text"><?= esc_html($result["truc"] ?? "") ?></div>
            </div>
            <div class="tuvi-data-card">
                <div class="tuvi-data-card-title">Vòng Đạo Tinh</div>
                <div class="tuvi-data-card-val tuvi-ct-accent-text">
                    <?= esc_html(is_array($result["sao_hoang_dao"]) ? ($result["sao_hoang_dao"]["name"] ?? "Hắc Đạo") : "Hắc Đạo") ?>
                </div>
            </div>
        </div>

        <?php if (!empty($result["huong_xuat_hanh"])): ?>
            <div class="tuvi-result-item tuvi-ct-section">
                <strong class="title tuvi-ct-title">HƯỚNG XUẤT HÀNH CÁT LỢI:</strong>
                <div class="content">
                    Hỷ Thần: Hướng <?= esc_html($result["huong_xuat_hanh"]["hy_than"] ?? "") ?>
                    | Tài Thần: Hướng <?= esc_html($result["huong_xuat_hanh"]["tai_than"] ?? "") ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($hasReasons): ?>
            <div class="tuvi-result-item tuvi-ct-section">
                <strong class="title tuvi-ct-title">YẾU TỐ CÁT LỢI:</strong>
                <div class="content">
                    <ul class="tuvi-reason-list">
                        <?php foreach ($eval["reasons"] as $r): ?>
                            <li><?= esc_html($r) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($hasWarnings): ?>
            <div class="tuvi-result-item tuvi-ct-section">
                <strong class="title tuvi-ct-title">
                    <?php if ($score >= 6): ?>YẾU TỐ CẦN CÂN NHẮC:<?php else: ?>YẾU TỐ KHÔNG TỐT:<?php endif; ?>
                </strong>
                <div class="content">
                    <ul class="tuvi-warning-list">
                        <?php foreach ($eval["warnings"] as $w): ?>
                            <li><?= esc_html($w) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($result["gio_tot"])): ?>
            <div class="tuvi-result-item tuvi-ct-section">
                <strong class="title tuvi-ct-title">KHUNG GIỜ HOÀNG ĐẠO:</strong>
                <div class="content">
                    <ul class="tuvi-gio-list">
                        <?php foreach ($result["gio_tot"] as $gio): ?>
                            <li><?= esc_html($formatGio($gio)) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
};

$renderRange = function ($result, $getLevelClass, $getLevelLabel) use ($gioMap) {
    $best      = $result["best"] ?? null;
    if ($best && !in_array($best["level"] ?? "", ["Tot", "Rat tot"])) {
        $best = null;
    }
    $bestClass = $best ? $getLevelClass($best["level"] ?? "") : "";
    ?>
    <div class="tuvi-result-box">
        <div class="tuvi-battu-info">
            <?php if (!empty($result['battu'])): ?>
                Mệnh tuổi: Tuổi <strong><?= esc_html($result['battu']['nam_sinh']) ?></strong>
                | Nhật Chủ <strong class="tuvi-nhat-chu"><?= esc_html($result['battu']['nhat_chu']) ?></strong><br>
            <?php endif; ?>
            Sự Kiện: <strong><?= esc_html($result["purpose_label"] ?? "") ?></strong><br>
            Tra cứu từ: <strong><?= esc_html(date('d/m/Y', strtotime($result['range']['start'] ?? ""))) ?></strong> đến <strong><?= esc_html(date('d/m/Y', strtotime($result['range']['end'] ?? ""))) ?></strong>
        </div>

        <?php if ($best): ?>
            <div class="tuvi-best-day-banner tuvi-result-item tuvi-ntx-header <?= $bestClass ?>">
                <h3 class="tuvi-best-day-title">Thời điểm cát lợi nhất</h3>
                <div class="tuvi-best-day-date"><?= esc_html(date('d/m/Y', strtotime($best["date"] ?? ""))) ?></div>
                <div><strong>Đánh Giá:</strong> <span class="value"><?= esc_html($getLevelLabel($best["level"] ?? "")) ?></span></div>
            </div>
        <?php endif; ?>

        <div class="tuvi-range-grid">
            <?php foreach ($result["results"] as $row):
                if ($best && ($row["date"] ?? '') === ($best["date"] ?? '')) continue;

                $rowClass = $getLevelClass($row["level"] ?? "");
                $saoHD    = $row["sao_hoang_dao"] ?? null;
                $isHD     = is_array($saoHD) ? ($saoHD["type"] === "Hoang Dao") : (bool)($row["hoang_dao"] ?? false);
                $hdLabel  = is_array($saoHD) ? ($saoHD["name"] ?? ($isHD ? "Hoàng Đạo" : "Hắc Đạo")) : ($isHD ? "Hoàng Đạo" : "Hắc Đạo");
                $isDC     = ($row["score"] ?? 0) >= 15;
                $rowScore = (int)($row["score"] ?? 0);
                $hasRowWarn = !empty($row["warnings"]);
                ?>
                <div class="tuvi-range-card">
                    <div class="tuvi-rc-head">
                        <div class="tuvi-rc-date"><?= esc_html(date('d/m/Y', strtotime($row["date"]))) ?></div>
                        <div class="tuvi-rc-tags">
                            <span class="tuvi-tag-level <?= $rowClass ?>"><?= esc_html($getLevelLabel($row["level"] ?? "")) ?></span>
                            <?php if ($isDC): ?><span class="tuvi-tag-badge tuvi-tag-dc">Đại Cát</span><?php endif; ?>
                            <span class="tuvi-tag-badge <?= $isHD ? 'tuvi-tag-hd' : 'tuvi-tag-hac' ?>"><?= esc_html($hdLabel) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($row["reasons"])): ?>
                        <strong class="tuvi-reason-title">Yếu tố vượng khí:</strong>
                        <ul class="tuvi-reason-list tuvi-reason-list-compact">
                            <?php foreach ($row["reasons"] as $reason): ?>
                                <li><?= esc_html($reason) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($hasRowWarn): ?>
                        <strong class="tuvi-reason-title tuvi-title-accent">
                            <?= $rowScore >= 6 ? 'Yếu tố cần cân nhắc:' : 'Yếu tố cản trở:' ?>
                        </strong>
                        <ul class="tuvi-warning-list tuvi-reason-list-compact">
                            <?php foreach ($row["warnings"] as $warning): ?>
                                <li><?= esc_html($warning) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (!empty($row["gio_tot"])): ?>
                        <div class="tuvi-rc-section">
                            <strong>Khung giờ cát lợi:</strong>
                            <div class="tuvi-gio-tags">
                                <?php foreach ($row["gio_tot"] as $gio): ?>
                                    <span class="tuvi-gio-tag" title="<?= esc_html($gioMap[$gio] ?? '') ?>"><?= esc_html($gio) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
};

if ($is_ajax && $has_result) {
    if (isset($result["date"])) {
        $renderSingleDay($result, $getLevelClass, $getLevelLabel);
    } elseif (isset($result["results"])) {
        $renderRange($result, $getLevelClass, $getLevelLabel);
    }
    return;
}
?>

<div class="tuvi-tabs<?= !$has_result ? ' tuvi-tabs-hidden' : '' ?>" role="tablist">
    <button class="tuvi-tab active" data-tab="chi-tiet" role="tab">Chi tiết</button>
    <button class="tuvi-tab" data-tab="cach-tinh" role="tab">Cơ sở</button>
</div>

<div class="tuvi-tab-pane active" id="tuvi-tab-chi-tiet">
    <?php if ($has_result && isset($result["date"])): ?>
        <?php $renderSingleDay($result, $getLevelClass, $getLevelLabel); ?>
    <?php elseif ($has_result && isset($result["results"])): ?>
        <?php $renderRange($result, $getLevelClass, $getLevelLabel); ?>
    <?php else: ?>
        <div id="ntx-result" class="ntx-result-container"></div>
    <?php endif; ?>
</div>

<div class="tuvi-tab-pane" id="tuvi-tab-cach-tinh">
    <div class="tuvi-ct-wrapper">
        <div class="tuvi-ct-alert">
            Hệ thống xem ngày tốt xấu (Trạch Nhật) được đúc kết từ thuật toán đối chiếu giữa Thiên văn cổ học và Bát tự cá nhân.
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">I. Cấu Trúc 3 Tầng Tuyển Trạch</h3>
            <p class="tuvi-text-secondary tuvi-mb-12">Hệ thống phân tích cát hung dựa trên 3 lớp dữ liệu độc lập:</p>

            <div class="tuvi-ct-dict-grid">
                <div class="tuvi-ct-dict-item">
                    <strong>1. Tầng Thần Sát (Cơ bản)</strong>
                    Phân tích quỹ đạo 12 Sao Hoàng Đạo / Hắc Đạo, 12 Trực hành kiển và hệ thống Cát Tinh, Hung Tinh theo Ngọc Hạp Thông Thư (Sát Chủ, Thọ Tử, Thiên Mã, Thiên Hỷ...).
                </div>
                <div class="tuvi-ct-dict-item">
                    <strong>2. Tầng Thiên Văn (Mở rộng)</strong>
                    Tính toán sự hiện diện của Nhị Thập Bát Tú (28 chòm sao) chiếu tới trong ngày, xét tính chất thuận/nghịch theo từng loại sự kiện cụ thể.
                </div>
                <div class="tuvi-ct-dict-item">
                    <strong>3. Tầng Bát Tự (Cá nhân hóa)</strong>
                    (Áp dụng khi có Năm sinh). Khởi dựng Ngũ Hành Nạp Âm, Thiên Can, Địa Chi của đương số để đối chiếu tương sinh, tương khắc, Lục Xung, Tam Hình, Lục Phá, Tam Hợp, Lục Hợp với ngày hiện tại.
                </div>
            </div>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">II. Cơ Chế Định Lượng Cát Hung</h3>
            <p class="tuvi-text-secondary tuvi-mb-12">Trạng thái khởi điểm là Bình Hòa (0 điểm). Thuật toán cộng hưởng vượng khí (trợ lực) và chiết giảm sát khí (cản trở).</p>

            <div class="tuvi-data-grid">
                <div class="tuvi-data-card">
                    <div class="tuvi-data-card-title">Nhóm Sinh Khí (Trợ Lực)</div>
                    <ul class="tuvi-reason-list tuvi-list-sm tuvi-mt-8">
                        <li>Ngày Hoàng Đạo, 12 Trực cát lợi.</li>
                        <li>Cát Tinh hội tụ (Thiên Hỷ, Thiên Mã...).</li>
                        <li>Ngũ hành ngày tương sinh, tương hòa với bản mệnh.</li>
                        <li>Địa chi ngày Lục Hợp, Tam Hợp với bản mệnh.</li>
                    </ul>
                </div>
                <div class="tuvi-data-card tuvi-card-accent">
                    <div class="tuvi-data-card-title tuvi-title-accent">Nhóm Sát Khí (Khắc Phạt)</div>
                    <ul class="tuvi-warning-list tuvi-list-sm tuvi-mt-8">
                        <li>Phạm ngày Sát Chủ, Thọ Tử, Dương Công, Nguyệt Kỵ.</li>
                        <li>Lục Xung, Tam Hình, Lục Phá với tuổi đương số.</li>
                        <li>Ngũ hành ngày khắc phạt bản mệnh.</li>
                        <li>Thiên Can ngày tương phá Thiên Can tuổi.</li>
                    </ul>
                </div>
            </div>

            <ul class="tuvi-ct-overview-list tuvi-mt-15">
                <li><strong>Rất Tốt:</strong> Vượng khí đắc lệnh, áp đảo hung tinh. Thích hợp tiến hành đại sự.</li>
                <li><strong>Tốt:</strong> Cát khí sung túc, thuận lợi chiếm ưu thế. Hoàn toàn có thể tiến hành.</li>
                <li><strong>Trung Bình:</strong> Âm Dương bình hòa, cát hung đan xen. Có thể làm việc nhỏ.</li>
                <li><strong>Không Nên:</strong> Sát khí lấn át. Cần tuyệt đối tránh tiến hành sự kiện.</li>
            </ul>

            <p class="tuvi-text-secondary tuvi-mt-12">
                * Khi ngày đạt mức <em>Tốt</em> hoặc <em>Rất tốt</em>, các yếu tố "cần cân nhắc" hiển thị bên dưới là những điểm trừ nhỏ <strong>đã được tính vào điểm tổng</strong> — kết luận cát hung đã phản ánh đầy đủ cả hai chiều. Không có mâu thuẫn giữa kết quả và các yếu tố này.
            </p>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">III. Phép Chọn Giờ Cát Lợi</h3>
            <p class="tuvi-text-secondary tuvi-mb-12">Khung giờ tốt được chắt lọc qua 2 bước nghiêm ngặt:</p>
            <ul class="tuvi-ct-overview-list">
                <li><strong>Bước 1 (Tránh Hung Sát):</strong> Loại bỏ giờ Hoàng Đạo phạm Sát Chủ hoặc Thọ Tử trong ngày.</li>
                <li><strong>Bước 2 (Bảo Vệ Bản Mệnh):</strong> (Áp dụng khi có Năm sinh). Loại bỏ khung giờ phạm Lục Xung hoặc Lục Hại với Địa chi tuổi đương số.</li>
            </ul>
        </div>
    </div>
</div>