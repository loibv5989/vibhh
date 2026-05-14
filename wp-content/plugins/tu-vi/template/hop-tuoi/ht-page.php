<?php
if (!defined('ABSPATH')) exit;

$has_result = !empty($result) && !isset($result['error']) && ($result['success'] ?? false);
$is_ajax = $is_ajax ?? false;

$renderHopTuoi = function (array $result) {
    $level    = $result['level']    ?? [];
    $profiles = $result['profiles'] ?? [];
    $details  = $result['details']  ?? [];
    $pA       = $profiles['A']      ?? [];
    $pB       = $profiles['B']      ?? [];
    $percent  = $result['percent']  ?? 0;
    $muc_dich = $result['muc_dich'] ?? [];
    $levelClass = $level['class']   ?? 'tuvi-result-neutral';

    $barColor = match(true) {
        $percent >= 65 => 'var(--tuvi-color-1)',
        $percent <= 44 => 'var(--accent-color)',
        default        => '#e0a800',
    };

    $iconCung = [
            'Khảm' => '☵', 'Khôn' => '☷', 'Chấn' => '☳', 'Tốn' => '☴',
            'Càn'  => '☰', 'Đoài' => '☱', 'Cấn'  => '☶', 'Ly'   => '☲',
    ];
    ?>
    <div class="tuvi-result-box tuvi-ht-result">
        <div class="tuvi-result-item tuvi-ntx-header <?= esc_attr($levelClass) ?>">
            <div class="row-wrap">
                <strong class="label">Mục Đích:</strong>
                <span class="value"><?= esc_html($muc_dich['label'] ?? '') ?></span>
            </div>
            <div class="row-wrap">
                <strong class="label">Kết Quả:</strong>
                <span class="value" style="font-size:1.15em;"><?= esc_html($level['label'] ?? '') ?></span>
            </div>
            <div class="tuvi-ht-progress-wrap">
                <div class="tuvi-ht-progress-label">
                    <span>Mức độ hòa hợp</span>
                    <strong style="color:<?= $barColor ?>;"><?= $percent ?>%</strong>
                </div>
                <div class="tuvi-ht-progress-track">
                    <div class="tuvi-ht-progress-fill" style="width:<?= $percent ?>%; background:<?= $barColor ?>;"></div>
                </div>
            </div>
        </div>

        <div class="tuvi-data-grid">
            <?php foreach ([['A', $pA, 'tuvi-ht-badge-a'], ['B', $pB, 'tuvi-ht-badge-b']] as [$key, $p, $badgeClass]): ?>
                <div class="tuvi-data-card">
                    <div class="tuvi-data-card-title">
                        <span class="tuvi-ht-badge <?= $badgeClass ?>"><?= esc_html($p['ten'] ?? "Người $key") ?></span>
                    </div>
                    <div class="tuvi-data-card-meta">
                        <div>
                            <strong class="tuvi-data-card-label">Sinh năm:</strong>
                            <?= esc_html($p['can_chi_nam'] ?? '') ?> (<?= esc_html($p['lunar_year'] ?? '') ?>)
                        </div>
                        <div>
                            <strong class="tuvi-data-card-label">Bản mệnh:</strong>
                            <span class="tuvi-ct-accent-text"><?= esc_html($p['ngu_hanh_vn'] ?? '') ?></span>
                        </div>
                        <div>
                            <strong class="tuvi-data-card-label">Cung Mệnh:</strong>
                            <span class="tuvi-ct-accent-text">
                                <?= esc_html($iconCung[$p['cung_menh'] ?? ''] ?? '') ?>
                                <?= esc_html($p['cung_menh'] ?? '') ?>
                            </span>
                        </div>
                        <div>
                            <strong class="tuvi-data-card-label">Nhật Chủ:</strong>
                            <?= esc_html(($p['day_can'] ?? '') . ' ' . ($p['day_chi'] ?? '')) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($result['conclusion'])): ?>
            <div class="tuvi-result-item tuvi-ct-section tuvi-ht-conclusion">
                <strong class="title tuvi-ct-title">NHẬN ĐỊNH TỔNG QUAN:</strong>
                <div class="content"><?= esc_html($result['conclusion']) ?></div>
            </div>
        <?php endif; ?>

        <?php
        $detailSections = [
                'ngu_hanh'  => ['icon' => '🔥', 'title' => 'NGŨ HÀNH BẢN MỆNH (NẠP ÂM)'],
                'thien_can' => ['icon' => '☯',  'title' => 'THIÊN CAN TƯƠNG TÁC'],
                'dia_chi'   => ['icon' => '🐉',  'title' => 'ĐỊA CHI TƯƠNG TÁC'],
                'cung_menh' => ['icon' => '🏯',  'title' => 'CUNG MỆNH BÁT TRẠCH'],
        ];
        foreach ($detailSections as $key => $meta):
            $d = $details[$key] ?? [];
            if (empty($d)) continue;

            $isGood = ($d['score'] ?? 0) >= ($d['max'] ?? 1) * 0.6;
            $isBad  = ($d['score'] ?? 0) < ($d['max'] ?? 1) * 0.3;
            $statusColor = $isGood ? 'var(--tuvi-color-1)' : ($isBad ? 'var(--accent-color)' : '#e0a800');
            ?>
            <div class="tuvi-result-item tuvi-ct-section tuvi-ht-detail-section">
                <strong class="title tuvi-ct-title">
                    <?= $meta['icon'] ?> <?= $meta['title'] ?>
                </strong>
                <div class="tuvi-ht-detail-inner">
                    <div class="tuvi-ht-person-row">
                        <div class="tuvi-ht-person-a">
                            <div class="tuvi-ht-person-name"><?= esc_html($pA['ten'] ?? 'Người 1') ?></div>
                            <div class="tuvi-ht-person-value-a">
                                <?php if ($key === 'cung_menh'): ?>
                                    <?= esc_html(($iconCung[$d['A'] ?? ''] ?? '') . ' ' . ($d['A'] ?? '')) ?>
                                <?php else: ?>
                                    <?= esc_html($d['A'] ?? '') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tuvi-ht-arrow">⇄</div>
                        <div class="tuvi-ht-person-b">
                            <div class="tuvi-ht-person-name"><?= esc_html($pB['ten'] ?? 'Người 2') ?></div>
                            <div class="tuvi-ht-person-value-b">
                                <?php if ($key === 'cung_menh'): ?>
                                    <?= esc_html(($iconCung[$d['B'] ?? ''] ?? '') . ' ' . ($d['B'] ?? '')) ?>
                                <?php else: ?>
                                    <?= esc_html($d['B'] ?? '') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tuvi-ht-status-row">
                        <span class="tuvi-ht-status-badge" style="border:1px solid <?= $statusColor ?>; color:<?= $statusColor ?>;">
                            <?= esc_html($d['status'] ?? '') ?>
                        </span>
                        <span class="tuvi-ht-score-text">
                            Điểm: <strong style="color:<?= $statusColor ?>;"><?= esc_html($d['score'] ?? 0) ?></strong> / <?= esc_html($d['max'] ?? 0) ?>
                        </span>
                    </div>

                    <div class="tuvi-ht-desc"><?= esc_html($d['desc'] ?? '') ?></div>

                    <?php if (!empty($d['detail'])): ?>
                        <div class="tuvi-ht-detail-note"><?= esc_html($d['detail']) ?></div>
                    <?php endif; ?>

                    <?php if ($key === 'cung_menh' && !empty($d['detail_AB'])): ?>
                        <div class="tuvi-ht-cung-detail">
                            <div class="tuvi-ht-cung-pin">📌 <?= esc_html($d['detail_AB']) ?></div>
                            <?php if (!empty($d['detail_BA'])): ?>
                                <div class="tuvi-ht-cung-pin">📌 <?= esc_html($d['detail_BA']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($result['remedies'])): ?>
            <div class="tuvi-result-item tuvi-ct-section tuvi-ht-remedies">
                <strong class="title tuvi-ct-title tuvi-ht-remedies-title">💡 GỢI Ý HÓA GIẢI</strong>
                <div class="tuvi-ht-remedies-content">
                    <ul class="tuvi-reason-list">
                        <?php foreach ($result['remedies'] as $remedy): ?>
                            <li><?= wp_kses($remedy, ['strong' => [], 'em' => []]) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
};

if ($is_ajax && $has_result) {
    $renderHopTuoi($result);
    return;
}
?>

<div class="tuvi-tabs" role="tablist"<?= !$has_result ? ' style="display:none;"' : '' ?>>
    <button class="tuvi-tab active" data-tab="chi-tiet" role="tab">Kết quả</button>
    <button class="tuvi-tab" data-tab="cach-tinh" role="tab">Cơ sở</button>
</div>

<div class="tuvi-tab-pane active" id="tuvi-tab-chi-tiet">
    <?php if ($has_result): ?>
        <?php $renderHopTuoi($result); ?>
    <?php else: ?>
        <div id="ht-result" class="ntx-result-container"></div>
    <?php endif; ?>
</div>

<div class="tuvi-tab-pane" id="tuvi-tab-cach-tinh">
    <div class="tuvi-ct-wrapper">
        <div class="tuvi-ct-alert">
            Hệ thống Xem Hợp Tuổi phân tích mức độ tương sinh tương khắc giữa hai người dựa trên Bát Tự và Bát Trạch Phong Thủy cổ học.
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">I. Cấu Trúc 4 Tầng Phân Tích</h3>
            <div class="tuvi-ct-dict-grid">
                <div class="tuvi-ct-dict-item">
                    <strong>1. Ngũ Hành Bản Mệnh (Nạp Âm)</strong>
                    Lấy từ Can Chi năm sinh (âm lịch) để xác định ngũ hành nạp âm. Xét tương sinh, tương khắc, tỷ hòa giữa mệnh hai người.
                </div>
                <div class="tuvi-ct-dict-item">
                    <strong>2. Thiên Can Tương Tác</strong>
                    Xét Can năm sinh: Ngũ hợp (Giáp–Kỷ, Ất–Canh...) hoặc Tương Phá. Phản ánh tính cách, tư duy và khả năng đồng thuận.
                </div>
                <div class="tuvi-ct-dict-item">
                    <strong>3. Địa Chi Tương Tác</strong>
                    Xét Chi năm sinh: Tam Hợp, Lục Hợp (tốt) hoặc Lục Xung, Lục Hại (xấu). Phản ánh tình cảm, gia đạo, tài lộc chung.
                </div>
                <div class="tuvi-ct-dict-item">
                    <strong>4. Cung Mệnh (Bát Trạch)</strong>
                    Tính cung phi từ năm âm lịch và giới tính. Tra ma trận 8×8 để xác định Sinh Khí, Diên Niên, Thiên Y... hay Tuyệt Mệnh, Ngũ Quỷ.
                </div>
            </div>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">II. Thang Điểm &amp; Phân Loại</h3>
            <ul class="tuvi-ct-overview-list">
                <li><strong>85–100% — Rất hợp:</strong> Hội tụ nhiều yếu tố tương sinh, ít xung khắc. Duyên lành, bền vững.</li>
                <li><strong>65–84% — Khá hợp:</strong> Phần lớn thuận lợi, có thể có một vài điểm cần lưu ý nhỏ.</li>
                <li><strong>45–64% — Trung bình:</strong> Có điểm hợp và điểm cần cải thiện. Cần nỗ lực từ cả hai phía.</li>
                <li><strong>25–44% — Xung nhẹ:</strong> Có khắc chế đáng kể, nên tham khảo hóa giải phong thủy.</li>
                <li><strong>0–24% — Rất xung:</strong> Nhiều điểm xung khắc chủ đạo. Cần cân nhắc kỹ và áp dụng hóa giải.</li>
            </ul>
            <p class="tuvi-ct-footnote">
                * Đây là luận giải theo cổ học phong thủy phương Đông. Kết quả mang tính tham khảo, không phải định mệnh tuyệt đối.
            </p>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">III. Cung Mệnh Bát Trạch</h3>
            <div class="tuvi-ct-dict-grid">
                <div class="tuvi-ct-dict-item tuvi-ct-group-dong">
                    <strong>Nhóm Đông Tứ Mệnh</strong>
                    Khảm ☵ · Ly ☲ · Chấn ☳ · Tốn ☴<br>
                    Hợp với nhau và với hướng Đông.
                </div>
                <div class="tuvi-ct-dict-item tuvi-ct-group-tay">
                    <strong>Nhóm Tây Tứ Mệnh</strong>
                    Càn ☰ · Khôn ☷ · Cấn ☶ · Đoài ☱<br>
                    Hợp với nhau và với hướng Tây.
                </div>
            </div>
            <p class="tuvi-ct-cung-note">
                Hai người cùng nhóm (Đông–Đông hoặc Tây–Tây) thường có kết quả Cung Mệnh tốt (Sinh Khí, Diên Niên, Thiên Y). Khác nhóm có thể gặp Tuyệt Mệnh hoặc Ngũ Quỷ — cần điều chỉnh hướng nhà, hướng giường.
            </p>
        </div>
    </div>
</div>