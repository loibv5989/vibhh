<?php

if (!defined('ABSPATH')) {
    exit;
}

/** @var array $tu_tru */

$ngu_hanh_class = [
    'kim' => 'e-kim',
    'moc' => 'e-moc',
    'thuy' => 'e-thuy',
    'hoa' => 'e-hoa',
    'tho' => 'e-tho',
];

$solar = preg_split('/-/', (string) ($thong_tin['ngay_sinh'] ?? ''));
$dl_year = $solar[0] ?? '';
$dl_month = $solar[1] ?? '';
$dl_day = $solar[2] ?? '';
$dl_time = trim((string) ($thong_tin['gio_sinh'] ?? ''));
$lunar_date = trim((string) ($thong_tin['am_lich'] ?? ''));
$birth_year = (int) preg_replace('/\D+/', '', (string) $dl_year);

$format_date = static function (string $date): string {
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt instanceof DateTime ? $dt->format('d/m/Y') : $date;
};

$solar_display = trim($format_date((string) ($thong_tin['ngay_sinh'] ?? '')) . ' (' . $dl_time . ')');
$lunar_display = trim($format_date((string) $lunar_date) . ' (' . $dl_time . ')');
$current_year = (int) wp_date('Y');

$render_hidden_row = static function (array $items, string $mode) use ($ngu_hanh_class): string {
    if (empty($items)) {
        return '<div class="nh-empty-cell"></div>';
    }

    $html = '<div class="nh-hidden-grid">';
    foreach ($items as $item) {
        $html .= '<div class="nh-hidden-item">';
        $cls = $ngu_hanh_class[$item['element'] ?? ''] ?? '';
        if ($mode === 'tang-can') {
            $html .= '<div class="nh-hidden-top ' . esc_attr($cls) . '">' . esc_html($item['can'] ?? '') . '</div>';
            if (!empty($item['thap_than_short'])) {
                $html .= '<div class="nh-hidden-mid">' . esc_html($item['thap_than_short']) . '</div>';
            }
            if (!empty($item['truong_sinh'])) {
                $html .= '<div class="nh-hidden-bot">' . esc_html($item['truong_sinh']) . '</div>';
            }
        } else {
            $html .= '<div class="nh-hidden-top ' . esc_attr($cls) . '">' . esc_html($item['thap_than_short'] ?? '') . '</div>';
            if (!empty($item['truong_sinh'])) {
                $html .= '<div class="nh-hidden-bot">' . esc_html($item['truong_sinh']) . '</div>';
            }
        }
        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;
};

$render_van_cell = static function (array $van) use ($ngu_hanh_class): string {
    $vanCanClass = $ngu_hanh_class[$van['can_element'] ?? ''] ?? '';
    $vanChiClass = $ngu_hanh_class[$van['chi_element'] ?? ''] ?? '';

    return '
        <div class="nh-van-cell">
            <div class="nh-van-can ' . esc_attr($vanCanClass) . '">' . esc_html(mb_convert_case((string) ($van['can_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) . '</div>
            <div class="nh-van-chi ' . esc_attr($vanChiClass) . '">' . esc_html(mb_convert_case((string) ($van['chi_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) . '</div>
            <div class="nh-van-age">' . esc_html(((int) ($van['tuoi'] ?? 0)) + 1) . '-' . esc_html(((int) ($van['tuoi'] ?? 0)) + 10) . 't</div>
            <div class="nh-van-year">' . esc_html($van['nam_bat_dau'] ?? '') . '</div>
        </div>
    ';
};

$render_year_cell = static function (array $tv) use ($ngu_hanh_class, $birth_year, $current_year): string {
    $tvCanClass = $ngu_hanh_class[$tv['can_element'] ?? ''] ?? '';
    $tvChiClass = $ngu_hanh_class[$tv['chi_element'] ?? ''] ?? '';
    $year = (int) ($tv['nam'] ?? 0);
    $age = $year > 0 && $birth_year > 0 ? $year - $birth_year + 1 : '';
    $current = $year === $current_year ? ' is-current' : '';

    return '
        <div class="nh-year-cell' . $current . '">
            <div class="nh-year-can ' . esc_attr($tvCanClass) . '">' . esc_html(mb_convert_case((string) ($tv['can_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) . '</div>
            <div class="nh-year-chi ' . esc_attr($tvChiClass) . '">' . esc_html(mb_convert_case((string) ($tv['chi_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) . '</div>
            <div class="nh-year-num">' . esc_html($year) . '</div>
            <div class="nh-year-age">' . esc_html($age) . 't</div>
        </div>
    ';
};

$rows = [
    [
        'label_lines' => ['DƯƠNG', 'LỊCH'],
            'cells' => [$dl_year, $dl_month, $dl_day, $dl_time],
            'type' => 'plain',
    ],
    [
        'label_lines' => ['CHỦ', 'TINH'],
        'cells' => array_map(static fn($k) => $tu_tru[$k]['thap_than_short'] ?? '', ['nam', 'thang', 'ngay', 'gio']),
        'type' => 'plain',
    ],
    [
        'label_lines' => ['BÁT TỰ'],
        'cells' => array_map(static fn($k) => $tu_tru[$k], ['nam', 'thang', 'ngay', 'gio']),
        'type' => 'bazi',
    ],
    [
        'label_lines' => ['TÀNG', 'ẨN'],
        'cells' => array_map(static fn($k) => $tu_tru[$k]['tang_can'] ?? [], ['nam', 'thang', 'ngay', 'gio']),
        'type' => 'tang-can',
    ],
    [
        'label_lines' => ['PHÓ', 'TINH'],
        'cells' => array_map(static fn($k) => $tu_tru[$k]['tang_can'] ?? [], ['nam', 'thang', 'ngay', 'gio']),
        'type' => 'pho-tinh',
    ],
    [
        'label_lines' => ['THẦN', 'SÁT'],
        'cells' => array_map(static fn($k) => $tu_tru[$k]['than_sat'] ?? [], ['nam', 'thang', 'ngay', 'gio']),
        'type' => 'than-sat',
    ],
    [
        'label_lines' => ['NẠP', 'ÂM'],
        'cells' => array_map(static fn($k) => $tu_tru[$k]['nap_am'] ?? '', ['nam', 'thang', 'ngay', 'gio']),
        'type' => 'plain',
    ],
];
?>

<div class="battu-tabs" role="tablist">
    <button class="battu-tab active" data-tab="la-so" role="tab">Lá Số Tứ Trụ</button>
    <button class="battu-tab" data-tab="tu-dien" role="tab">Từ Điển Can Chi</button>
</div>

<div class="battu-tab-pane active" id="battu-tab-la-so">
    <div class="nh-result-scale-wrap">
        <div class="battu-result-wrap nh-result battu-capture-wrap">
            <div class="nh-corner nh-corner-tl"></div>
            <div class="nh-corner nh-corner-br"></div>
            <div class="nh-shell">
                <div class="nh-header">
                    <div class="nh-brand-block">
                        <div class="nh-logo" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256">
                                <path d="M113.9 15.2c0 .2-.2 1.3-.5 2.5L113 20h31v-5h-15c-8.2 0-15 .1-15.1.2M113.4 26.2c.3 2.3.6 2.3 15.4 2.6l15.2.3V24h-31zM113.4 35.2c.3 2.3.5 2.3 15.2 2.3s14.9 0 15.2-2.3l.3-2.2H113zM49 46.6C43.5 52 39 57.1 39 58.1c0 .9.7 2.2 1.5 2.9 1.3 1.1 3.1-.3 11.6-8.6 8-7.9 9.9-10.4 9.7-12.3-.2-1.3-.9-2.6-1.6-2.9-.7-.2-5.8 4-11.2 9.4M194.3 39.6c-.4 1.1 2.5 4.7 9.8 12 5.7 5.7 10.8 10.3 11.3 10.1s1.4-1.1 2-2c.9-1.4-.6-3.4-8.9-11.7-9.7-9.7-12.9-11.7-14.2-8.4M54.5 54c-8.3 8.3-9.8 10.3-8.9 11.7.6.9 1.5 1.8 2 2s5.6-4.4 11.3-10.1c7.3-7.3 10.2-10.9 9.8-12-1.3-3.3-4.5-1.3-14.2 8.4M188.7 44.7c-1.8 1.7-.1 4.4 6.6 10.8 13.6 13.2 13.5 13.1 15.4 11.2 1.6-1.6 1.2-2.3-8.7-12.2-10-10-11.8-11.4-13.3-9.8M146.7 50.3c1.8.7 3.2 1.6 3 2.2-.1.5.8 1.1 2.1 1.3 3.5.5 10 7.6 13.2 14.3 7.4 15.7 3.5 36.6-9 48.3-7.8 7.3-15.6 10.5-28.4 11.6-8.6.7-11.9 1.5-17.1 4-8.2 3.9-15 10.8-18.9 19-2.8 5.8-3.1 7.5-3.1 15.5s.3 9.7 3.1 15.6c5.1 10.7 13.8 18.7 23.7 21.9 2.1.7 3.7 1.4 3.4 1.6-.2.2-2.6-.3-5.3-1.1s-5.8-1.5-6.9-1.5c-3.7 0-21.9-9.8-28.7-15.4-11.3-9.4-20-22.4-25.3-37.8-1.3-4-2.5-6.6-2.5-5.7 0 2.4 3.9 13.8 6.7 19.4 9.1 17.9 24.9 32.3 42.9 39.1 30.2 11.4 63.4 4.3 85.8-18.1 13.3-13.3 21.7-32.3 22.9-52l.6-9-1.4 9.5c-4.7 30.9-19.2 52.1-44.2 64.9-6.7 3.4-6.4 2.4.3-1.3 20.7-11.1 34.8-28.9 40.5-51.1 2.6-10.2 2.9-29.5.5-40.5-5.1-23.9-24.9-44.4-51.8-53.6-7.1-2.4-12.3-3.3-6.1-1.1m-12.4 101.1c1.8.7 4.7 3 6.5 5 2.7 3.1 3.2 4.4 3.2 8.9-.1 11.1-6.5 18.1-16.7 18.1-8.5 0-13.6-4.9-15.5-15.1-1.1-5.9-1-6.4 1.5-10.1 4.5-6.9 13.7-9.9 21-6.8"/>
                                <path d="M120.5 153.8c-1.6 1.1-3.7 2.7-4.5 3.8-.8 1 0 .7 1.8-.8 6.3-5.2 13.9-4.9 20.5 1.1 3.3 2.8 3.8 3.1 2.6 1.1-4.2-6.6-14-9.1-20.4-5.2M60.5 60c-8.3 8.3-9.8 10.3-8.9 11.7.6.9 1.5 1.8 2 2s5.6-4.4 11.3-10.1c7.3-7.3 10.2-10.9 9.8-12-1.3-3.3-4.5-1.3-14.2 8.4M182.7 50.7c-.4.3-.7 1.4-.7 2.2 0 1.9 18.9 21.1 20.7 21.1.7 0 1.7-.8 2.2-1.8.8-1.6-.7-3.5-9.2-12-9.8-9.8-11.5-11-13-9.5M72.3 70.8c-3.9 3.9-8.2 9.5-10 13.1-4.4 8.3-4.1 8.7.8 1.5 2.2-3.2 6.8-8.7 10.2-12.2 3.4-3.4 5.8-6.2 5.3-6.2s-.3-.7.4-1.5c3.2-3.8-.6-.8-6.7 5.3m5.7-3.5c0 .2-1.5 1.6-3.2 3.3l-3.3 2.9 2.9-3.3c2.8-3 3.6-3.7 3.6-2.9M122 74.1c-5.4 2.2-10 8.4-10 13.7 0 1.6 1.1 4.9 2.3 7.5 1.3 2.5 2 4.4 1.5 4.1-.4-.3-.8-.3-.8 0s1.7 1.7 3.7 3.1c2.8 1.9 5 2.5 8.8 2.5 5.4 0 10.2-1.9 9.7-3.9-.1-.7.2-1 .7-.7s1.5-.2 2.2-1.1c1.2-1.4 1.2-1.6-.1-.8-1.1.6-1.1.4.2-1.1.9-1.1 2.2-4 2.9-6.5 1.1-4.2 1-4.9-1.2-9-4-7.3-12.7-10.7-19.9-7.8M16.2 127.2c.3 14.5.4 15.3 2.3 15.3s2-.8 2.3-15.3l.3-15.2h-5.2zM25 127.6v15.5l2.3-.3c2.2-.3 2.2-.6 2.5-15.6l.3-15.2H25zM34 112.4c0 .3-.1 7.2-.1 15.3 0 14.4 0 14.8 2.1 14.8s2.1-.4 2.1-15c0-14.4-.1-15-2-15.3-1.2-.2-2.1-.1-2.1.2M219 128.1c0 14.6.1 15 2 14.7 2-.3 2.1-1 2.3-15.1.2-14.6.2-14.7-2-14.7-2.3 0-2.3.1-2.3 15.1M227 128v15h5v-30h-5zM236 127.9c0 12.7.2 15 1.6 15.5.9.3 2 .4 2.5 0 .5-.3.9-7.2.9-15.5V113h-5zM49.1 140.6c0 1.1.3 1.4.6.6.3-.7.2-1.6-.1-1.9-.3-.4-.6.2-.5 1.3M52.7 180.7c-1.8 1.8.1 4.6 9.2 13.7 6.9 6.9 10.4 9.7 11.5 9.3 3.2-1.3 1.4-4.5-8.2-14-9.3-9.3-11-10.5-12.5-9M191.8 190.7c-9.6 9.6-11.4 12.7-8.2 14 2.3.8 21.6-18.4 21.2-21.2-.6-4-3.5-2.4-13 7.2M46 187.6c-1.2 1.3.1 3 9.1 12 10.1 10.2 13 11.8 13.7 8 .3-1.3-2.7-4.9-9.4-11.7-5.4-5.5-10.4-9.9-11-9.9s-1.7.7-2.4 1.6M197.9 196.7c-9.8 9.5-11.1 11.7-8.7 14.1 1 1 3.5-1.1 12-9.5 9-8.9 10.5-10.9 9.7-12.5-.5-1-1.5-1.8-2.1-1.8s-5.5 4.4-10.9 9.7M40.2 193.2c-.7.7-1.2 1.7-1.2 2.3 0 1.8 19.4 20.5 21.3 20.5 1 0 2-.7 2.4-1.6.4-1.1-2.5-4.7-9.8-12C47.2 196.7 42.3 192 42 192s-1.1.5-1.8 1.2M203.5 204c-8.3 8.3-9.8 10.3-8.9 11.7.6.9 1.5 1.8 2 2s5.6-4.4 11.3-10.1c7.3-7.3 10.2-10.9 9.8-12-1.3-3.3-4.5-1.3-14.2 8.4M113.4 217.5c-1.6 4-.1 4.5 15 4.5h14.4l.4-2.5c.3-1.3.3-2.7 0-3s-7-.5-14.9-.5c-11.8 0-14.5.3-14.9 1.5M113.2 227.6l.3 2.6 14.5-.1c7.9-.1 14.5-.3 14.6-.4s.4-1.1.6-2.2c.3-1.9-.3-2-15-2.3l-15.3-.3zM113 236.5v2.5h30v-5h-30z"/>
                            </svg>
                        </div>
                        <div class="nh-brand-text">
                            <div class="nh-title-stack">
                                <span>Lá số bát tự</span>
                            </div>
                            <div class="nh-brand-name">nbblo.com</div>
                        </div>
                    </div>
                    <div class="nh-meta-grid">
                        <div class="nh-meta-row"><span>Họ và tên:</span><strong><?= esc_html($thong_tin['ho_ten'] ?? 'Đương số') ?></strong></div>
                        <div class="nh-meta-row"><span>Giới tính:</span><strong><?= esc_html($thong_tin['gioi_tinh'] ?? '') ?></strong></div>
                        <div class="nh-meta-row"><span>Dương lịch:</span><strong class="nh-red"><?= esc_html($solar_display) ?></strong></div>
                        <div class="nh-meta-row"><span>Âm lịch:</span><strong class="nh-green"><?= esc_html($thong_tin['am_lich'] ?? '') ?> (<?= esc_html($dl_time) ?>)</strong></div>
                        <?php
                        $than_info = $than_vuong_nhuoc ?? [];
                        $dung_info = $dung_than ?? [];
                        $than_ket_qua = $than_info['ket_qua'] ?? '';
                        $than_muc_do = $than_info['muc_do'] ?? '';
                        $dung_list = $dung_info['dung_than'] ?? [];
                        $ky_list = $dung_info['ky_than'] ?? [];
                        ?>
                        <div class="nh-meta-row"><span>Thân:</span><strong><?= esc_html($than_ket_qua) ?><?= $than_muc_do ? ' (' . esc_html($than_muc_do) . ')' : '' ?></strong></div>
                        <div class="nh-meta-row"><span>Dụng Thần:</span><strong class="nh-dung-than"><?= esc_html(implode(', ', $dung_list)) ?></strong></div>
                        <div class="nh-meta-row"><span>Kỵ Thần:</span><strong class="nh-ky-than"><?= esc_html(implode(', ', $ky_list)) ?></strong></div>
                    </div>
                </div>

                <div class="nh-table-wrap">
                    <table class="nh-chart-table">
                        <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <th class="nh-row-label">
                                    <span><?= esc_html(implode(' ', $row['label_lines'])) ?></span>
                                </th>
                                <?php foreach ($row['cells'] as $cell): ?>
                                    <td>
                                        <?php if ($row['type'] === 'plain'): ?>
                                            <div class="nh-plain-cell"><?= esc_html($cell) ?></div>
                                        <?php elseif ($row['type'] === 'bazi'): ?>
                                            <?php $clsCan = $ngu_hanh_class[$cell['can_element'] ?? ''] ?? ''; ?>
                                            <?php $clsChi = $ngu_hanh_class[$cell['chi_element'] ?? ''] ?? ''; ?>
                                            <div class="nh-bazi-cell">
                                                <div class="nh-bazi-can <?= esc_attr($clsCan) ?>"><?= esc_html(mb_strtoupper($cell['can_name'] ?? '', 'UTF-8')) ?></div>
                                                <div class="nh-bazi-chi <?= esc_attr($clsChi) ?>"><?= esc_html(mb_strtoupper($cell['chi_name'] ?? '', 'UTF-8')) ?></div>
                                            </div>
                                        <?php elseif ($row['type'] === 'tang-can'): ?>
                                            <?= $render_hidden_row($cell, 'tang-can') ?>
                                        <?php elseif ($row['type'] === 'pho-tinh'): ?>
                                            <?= $render_hidden_row($cell, 'pho-tinh') ?>
                                        <?php elseif ($row['type'] === 'than-sat'): ?>
                                            <div class="nh-than-sat-cell">
                                                <?php foreach ($cell as $ts): ?>
                                                    <div><?= esc_html($ts) ?></div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="nh-van-section">
                    <div class="nh-van-row nh-van-row-first">
                        <div class="nh-van-side">ĐẠI VẬN</div>
                        <div class="nh-van-body">
                            <div class="nh-van-header-text">
                                <strong>Đại vận và lưu niên</strong>
                                <div class="nh-space-bottom">
                                    Hành vận theo chiều <strong><?= esc_html($dai_van['chieu_hanh_van'] ?? '') ?></strong>.
                                </div>
                                <div class="nh-space-bottom">
                                    Thời điểm bắt đầu nhập Đại vận: <strong class="nh-highlight"><?= esc_html($dai_van['thoi_gian_khoi_van'] ?? '') ?></strong> (năm <strong class="nh-highlight"><?= esc_html($dai_van['van_trinh'][0]['nam_bat_dau'] ?? '') ?></strong>)
                                </div>
                                <div class="nh-tiet-khi-note">
                                    Nguyệt lệnh: <strong><?= esc_html($thong_tin['tiet_khi_hien_tai'] ?? '') ?></strong>
                                    <span>
                                        <?php if (!empty($thong_tin['tiet_start']) && !empty($thong_tin['tiet_end'])): ?>
                                            (Từ <?= esc_html($thong_tin['tiet_start']) ?> đến <?= esc_html($thong_tin['tiet_end']) ?>)
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="nh-van-grid nh-van-grid-10">
                                <?php foreach (($dai_van['van_trinh'] ?? []) as $idx => $van):
                                    $vanCanClass = $ngu_hanh_class[$van['can_element'] ?? ''] ?? '';
                                    $vanChiClass = $ngu_hanh_class[$van['chi_element'] ?? ''] ?? '';
                                    $nam_bat_dau = (int)($van['nam_bat_dau'] ?? 0);
                                    $is_current_van = ($current_year >= $nam_bat_dau && $current_year < $nam_bat_dau + 10);
                                    ?>
                                    <div class="nh-van-cell nh-van-cell-split<?= $is_current_van ? ' is-current' : '' ?>">
                                        <div class="nh-van-cell-top">
                                            <div class="nh-van-can <?= esc_attr($vanCanClass) ?>"><?= esc_html(mb_convert_case((string) ($van['can_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) ?></div>
                                            <div class="nh-van-chi <?= esc_attr($vanChiClass) ?>"><?= esc_html(mb_convert_case((string) ($van['chi_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) ?></div>
                                        </div>
                                        <div class="nh-van-cell-bot">
                                            <div class="nh-van-age"><?= esc_html(((int) ($van['tuoi'] ?? 0))) ?>-<?= esc_html(((int) ($van['tuoi'] ?? 0)) + 9) ?>t</div>
                                            <div class="nh-van-year"><?= esc_html($van['nam_bat_dau'] ?? '') ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Logic tìm Đại Vận hiện tại
                    $van_trinh = $dai_van['van_trinh'] ?? [];
                    $total_van = count($van_trinh);
                    $current_van_idx = 0;

                    foreach ($van_trinh as $idx => $van) {
                        $nam_bat_dau = (int)($van['nam_bat_dau'] ?? 0);
                        if ($current_year >= $nam_bat_dau && $current_year <= $nam_bat_dau + 9) {
                            $current_van_idx = $idx;
                            break;
                        } elseif ($current_year < $nam_bat_dau && $idx === 0) {
                            $current_van_idx = 0;
                            break;
                        }
                    }

                    $idx_luu_nien_1 = $current_van_idx;
                    $idx_luu_nien_2 = ($current_van_idx + 1 < $total_van) ? $current_van_idx + 1 : -1;
                    ?>

                    <div class="nh-van-row">
                        <div class="nh-van-side">LƯU NIÊN</div>
                        <div class="nh-van-body">
                            <div class="nh-van-grid nh-van-grid-years nh-van-grid-10">
                                <?php foreach (($van_trinh[$idx_luu_nien_1]['tieu_van'] ?? []) as $tv): ?>
                                    <?php
                                    $tvCanClass = $ngu_hanh_class[$tv['can_element'] ?? ''] ?? '';
                                    $tvChiClass = $ngu_hanh_class[$tv['chi_element'] ?? ''] ?? '';
                                    $year = (int) ($tv['nam'] ?? 0);
                                    $age = $year > 0 && $birth_year > 0 ? $year - $birth_year + 1 : '';
                                    $current = $year === $current_year ? ' is-current' : '';
                                    ?>
                                    <div class="nh-year-cell nh-year-cell-split<?= $current ?>">
                                        <div class="nh-year-cell-top">
                                            <div class="nh-year-can <?= esc_attr($tvCanClass) ?>"><?= esc_html(mb_convert_case((string) ($tv['can_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) ?></div>
                                            <div class="nh-year-chi <?= esc_attr($tvChiClass) ?>"><?= esc_html(mb_convert_case((string) ($tv['chi_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) ?></div>
                                        </div>
                                        <div class="nh-year-cell-bot">
                                            <div class="nh-year-num"><?= esc_html($year) ?></div>
                                            <div class="nh-year-age"><?= esc_html($age) ?>t</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($idx_luu_nien_2 !== -1): ?>
                        <div class="nh-van-row">
                            <div class="nh-van-side">LƯU NIÊN</div>
                            <div class="nh-van-body">
                                <div class="nh-van-grid nh-van-grid-years nh-van-grid-10">
                                    <?php foreach (($van_trinh[$idx_luu_nien_2]['tieu_van'] ?? []) as $tv): ?>
                                        <?php
                                        $tvCanClass = $ngu_hanh_class[$tv['can_element'] ?? ''] ?? '';
                                        $tvChiClass = $ngu_hanh_class[$tv['chi_element'] ?? ''] ?? '';
                                        $year = (int) ($tv['nam'] ?? 0);
                                        $age = $year > 0 && $birth_year > 0 ? $year - $birth_year + 1 : '';
                                        $current = $year === $current_year ? ' is-current' : '';
                                        ?>
                                        <div class="nh-year-cell nh-year-cell-split<?= $current ?>">
                                            <div class="nh-year-cell-top">
                                                <div class="nh-year-can <?= esc_attr($tvCanClass) ?>"><?= esc_html(mb_convert_case((string) ($tv['can_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) ?></div>
                                                <div class="nh-year-chi <?= esc_attr($tvChiClass) ?>"><?= esc_html(mb_convert_case((string) ($tv['chi_name'] ?? ''), MB_CASE_TITLE, 'UTF-8')) ?></div>
                                            </div>
                                            <div class="nh-year-cell-bot">
                                                <div class="nh-year-num"><?= esc_html($year) ?></div>
                                                <div class="nh-year-age"><?= esc_html($age) ?>t</div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="nh-footer">
                    <div class="nh-footer-text">Tra cứu và luận giải miễn phí lá số bát tự tại
                        <span><?= parse_url(get_bloginfo('url'), PHP_URL_HOST) ?></span>
                    </div>
                    <div class="nh-legend">
                        <span><i class="nh-dot nh-kim"></i>Kim</span>
                        <span><i class="nh-dot nh-thuy"></i>Thủy</span>
                        <span><i class="nh-dot nh-moc"></i>Mộc</span>
                        <span><i class="nh-dot nh-hoa"></i>Hỏa</span>
                        <span><i class="nh-dot nh-tho"></i>Thổ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="battu-tab-pane" id="battu-tab-tu-dien">
    <?php include BATTU_PLUGIN_DIR . 'template/tab-tu-dien.php'; ?>
</div>

<div class="battu-action-controls">
    <button type="button" class="battu-btn-action" id="battu-download-btn">↓ Lưu lá số</button>
    <button type="button" class="battu-btn-action" id="battu-new-calc-btn">↺ Xem lá số khác</button>
</div>

<?php if (!empty($is_ajax)): ?>
    <div class="battu-section" id="battu-actions">
        <p class="battu-note">💡 Luận giải chi tiết lá số hoặc đặt câu hỏi trực tiếp trên Bát Tự của bạn.</p>
        <div id="battu-qa-input-area" class="battu-qa-area" style="display: none;">
            <textarea id="battu-user-question" placeholder="VD: Định hướng công việc nào phù hợp với lá số của tôi?" rows="3" class="battu-textarea" maxlength="500"></textarea>
            <div class="battu-suggested-questions">
                <p class="sq-label">Gợi ý hỏi:</p>
                <div class="sq-list">
                    <button type="button" class="sq-btn" data-q="Lá số này có phải tình duyên lận đận không?"> Tình duyên lận đận </button>
                    <button type="button" class="sq-btn" data-q="Bát Tự này có hợp tự kinh doanh hay làm ổn định hơn?"> Hợp kinh doanh </button>
                    <button type="button" class="sq-btn" data-q="Lá số này có dễ gặp cảnh hao tài hoặc khó giữ tiền không?"> Có khó giữ tiền </button>
                    <button type="button" class="sq-btn" data-q="Bát Tự này có phải là người khá nóng tính và cứng đầu không?"> Tính cách </button>
                    <button type="button" class="sq-btn" data-q="Lá số này có dễ kết hôn muộn hoặc trải qua đổ vỡ tình cảm không?"> Có số kết hôn muộn </button>
                    <button type="button" class="sq-btn" data-q="Bát Tự này có phải càng về hậu vận càng ổn định và dễ có tài lộc hơn không?"> Hậu vận có khá hơn </button>
                </div>
            </div>
        </div>
        <div class="battu-btn-group">
            <button class="battu-btn-submit" id="battu-btn-deep-analyze">
                <span class="battu-btn-text">Luận giải Bát Tự</span>
                <span class="battu-btn-loading" style="display: none;"><span class="battu-spinner"></span> Đang phân tích...</span>
            </button>
            <button class="battu-btn-submit battu-btn-secondary" id="battu-btn-qa-analyze">
                <span class="battu-btn-text">Hỏi Bát Tự</span>
                <span class="battu-btn-loading" style="display: none;"><span class="battu-spinner"></span> Đang xử lý...</span>
            </button>
            <?php
            $support = 0;
            if ($support): ?>
            <button class="battu-btn-submit battu-btn-secondary" id="battu-btn-support">
                <span class="battu-btn-text">Hỗ trợ luận giải</span>
                <span class="battu-btn-loading" style="display: none;"><span class="battu-spinner"></span> Đang gửi...</span>
            </button>
            <?php endif; ?>
            <button class="battu-btn-submit battu-btn-cancel" id="battu-btn-qa-cancel" style="display: none;">
                <span class="battu-btn-text">← Quay lại</span>
            </button>
            <button class="battu-btn-submit battu-btn-cancel" id="battu-btn-support-cancel" style="display: none;">
                <span class="battu-btn-text">← Quay lại</span>
            </button>
        </div>
        <span class="battu-error-inline battu-err-analyze" style="display: none; margin-top:10px;"></span>
    </div>

    <div id="battu-support-area" class="battu-qa-input-area battu-support-area">
        <p class="battu-support-note">
            Mình là <a href="/author/loibv/" target="_blank">Loibv</a>, không phải "thầy bói", mình nghiên cứu bát tự (tứ trụ) dựa trên bát tự cổ học của Thiệu Vĩ Hoa (邵伟华/Shào Wěihuá), mọi qui tắc cung vận đều căn cứ vào dữ liệu tứ trụ cổ học gốc. Nếu bạn cần hỗ trợ luận giải, hãy ghi rõ câu hỏi.
        </p>
        <textarea id="battu-support-question" name="battu_support_question"
                  rows="6" class="battu-textarea battu-w-100" maxlength="650"></textarea>
        <div class="battu-support-btn-wrap">
            <button class="battu-btn-submit battu-btn-primary" id="battu-btn-support-submit">
                <span class="battu-btn-text">Gửi câu hỏi</span>
                <span class="battu-btn-loading"><span class="battu-spinner"></span> Đang gửi...</span>
            </button>
        </div>
        <span class="battu-error-inline battu-err-support"></span>
    </div>
    <span class="battu-success-inline battu-success-support" style="display: none;"></span>

    <div id="battu-support-modal" class="battu-modal">
        <div class="battu-modal-content">
            <h3>Xác nhận gửi câu hỏi</h3>
            <p>Vui lòng kiểm tra lại thông tin trước khi gửi:</p>
            <textarea id="battu-modal-textarea" readonly class="battu-modal-textarea"></textarea>
            <div class="battu-modal-buttons">
                <button id="battu-modal-cancel" class="battu-btn-submit battu-btn-cancel battu-modal-cancel">Đóng</button>
                <button id="battu-modal-ok" class="battu-btn-submit battu-btn-primary">OK</button>
            </div>
        </div>
    </div>

    </div>
    <div id="battu-luan-giai" class="battu-luan-giai" style="display: none;"></div>
<?php endif; ?>