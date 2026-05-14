<?php
if (!defined('ABSPATH')) exit;

/** @var string $time_string */
/** @var string $question */
/** @var array $cc */
/** @var string $lunar_str */
/** @var array $chu */
/** @var array $ho */
/** @var array $bien */
/** @var array $fullData */
/** @var string $mode */

// ─── 1. THỜI GIAN & TIẾT KHÍ ────────────────────────────────────
$tz = wp_timezone();
if (isset($fullData['toss_time'])) {
    $dt        = new DateTime($fullData['toss_time'], $tz);
    $timestamp = $dt->getTimestamp();
} else {
    $timestamp = time();
}

$solar_m = (int) wp_date('n', $timestamp);
$solar_d = (int) wp_date('j', $timestamp);
$solar_y = (int) wp_date('Y', $timestamp);

$tiet_khi_names = [
        1 => 'Tiểu Hàn', 2 => 'Lập Xuân', 3 => 'Kinh Trập',  4 => 'Thanh Minh',
        5 => 'Lập Hạ',   6 => 'Mang Chủng', 7 => 'Tiểu Thử', 8 => 'Lập Thu',
        9 => 'Bạch Lộ', 10 => 'Hàn Lộ',   11 => 'Lập Đông', 12 => 'Đại Tuyết',
];
$tiet_day = Iching_Calendar::getTietDay($solar_m, $solar_y);
$tk_idx   = ($solar_d >= $tiet_day) ? $solar_m : ($solar_m - 1);
if ($tk_idx == 0) $tk_idx = 12;
$tiet_khi_str = $tiet_khi_names[$tk_idx] ?? '';

// ─── 2. HÀM VẼ ĐỒ HÌNH ──────────────────────────────────────────
$_hexfig = function (string $bin, array $changing = []): string {
    $out = '<div class="lhq-hexfig">';
    for ($i = 5; $i >= 0; $i--) {
        $is_dong = in_array($i + 1, $changing, true);
        $color   = $is_dong ? 'blue' : 'red';
        $bit     = $bin[$i] ?? '1';
        if ($bit === '1') {
            $out .= '<div class="lhq-hline yang ' . $color . '"><span></span></div>';
        } else {
            $out .= '<div class="lhq-hline yin ' . $color . '"><span></span><span></span></div>';
        }
    }
    $out .= '</div>';
    return $out;
};

// ─── 3. BẢNG BÁT QUÁI MỞ RỘNG ───────────────────────────────────
$_bat_quai = [
        'Càn'  => ['hanh' => 'Kim',  'tuong' => 'Trời, Ngọc, Vua, Ngựa, Vàng',    'phuong' => 'Tây Bắc',  'than_the' => 'Đầu',   'gia_dinh' => 'Cha'],
        'Đoài' => ['hanh' => 'Kim',  'tuong' => 'Đầm, Miệng, Dê, Tiền bạc, Vui',  'phuong' => 'Tây',      'than_the' => 'Miệng', 'gia_dinh' => 'Thiếu nữ'],
        'Ly'   => ['hanh' => 'Hỏa', 'tuong' => 'Lửa, Mặt trời, Phượng, Văn thư', 'phuong' => 'Nam',       'than_the' => 'Mắt',   'gia_dinh' => 'Trung nữ'],
        'Chấn' => ['hanh' => 'Mộc', 'tuong' => 'Sấm, Rồng, Cây lớn, Xe cộ',      'phuong' => 'Đông',      'than_the' => 'Chân',  'gia_dinh' => 'Trưởng nam'],
        'Tốn'  => ['hanh' => 'Mộc', 'tuong' => 'Gió, Gỗ, Gà, Dây thừng, Thương', 'phuong' => 'Đông Nam',  'than_the' => 'Đùi',   'gia_dinh' => 'Trưởng nữ'],
        'Khảm' => ['hanh' => 'Thủy','tuong' => 'Nước, Trăng, Heo, Hố sâu, Hiểm', 'phuong' => 'Bắc',       'than_the' => 'Tai',   'gia_dinh' => 'Trung nam'],
        'Cấn'  => ['hanh' => 'Thổ', 'tuong' => 'Núi, Chó, Đá, Cửa, Dừng lại',    'phuong' => 'Đông Bắc',  'than_the' => 'Tay',   'gia_dinh' => 'Thiếu nam'],
        'Khôn' => ['hanh' => 'Thổ', 'tuong' => 'Đất, Bò, Mẹ, Ruộng đồng, Nhu',   'phuong' => 'Tây Nam',   'than_the' => 'Bụng',  'gia_dinh' => 'Mẹ'],
];

$_quai_cell = function (string $ten_quai, string $badge_type = '') use ($_bat_quai): string {
    if ($ten_quai === '') return '<span class="lhq-mh-na">&mdash;</span>';
    $q = $_bat_quai[$ten_quai] ?? null;
    if (!$q) return esc_html($ten_quai);

    $badge = '';
    if ($badge_type === 'the')  $badge = ' <span class="lhq-mh-badge lhq-mh-badge-the">Thể</span>';
    if ($badge_type === 'dung') $badge = ' <span class="lhq-mh-badge lhq-mh-badge-dung">Dụng</span>';

    $hanh_lower = strtolower(iconv('UTF-8','ASCII//TRANSLIT', $q['hanh']));

    return '<div class="lhq-quai-cell">'
            . '<strong class="lhq-quai-name">' . esc_html($ten_quai) . '</strong>'
            . '<span class="lhq-quai-hanh lhq-el-' . esc_attr($hanh_lower) . '">' . esc_html($q['hanh']) . '</span>'
            . $badge
            . '<div class="lhq-quai-detail">'
            . '<span><em>Tượng:</em> ' . esc_html($q['tuong']) . '</span>'
            . '<span><em>Phương:</em> ' . esc_html($q['phuong'])
            . ' &nbsp;·&nbsp; <em>Thân:</em> ' . esc_html($q['than_the'])
            . ' &nbsp;·&nbsp; <em>Nhà:</em> ' . esc_html($q['gia_dinh']) . '</span>'
            . '</div>'
            . '</div>';
};

// ─── 4. CHI NAMES ─────────────────────────────────────────────────
$_chi_names = [1=>'Tý',2=>'Sửu',3=>'Dần',4=>'Mão',5=>'Thìn',6=>'Tỵ',7=>'Ngọ',8=>'Mùi',9=>'Thân',10=>'Dậu',11=>'Tuất',12=>'Hợi'];

// ─── 5. DATA MAI HOA ──────────────────────────────────────────────
$_chu_bin   = $fullData['chu_key']  ?? '111111';
$_ho_bin    = $fullData['ho_key']   ?? '111111';
$_bien_bin  = $fullData['bien_key'] ?? $_chu_bin;
$_dong      = (int) ($fullData['changing_line'] ?? 0);
$_changing_lines = $_dong > 0 ? [$_dong] : [];

$_chu_up    = $chu['upper_trigram']  ?? '';
$_chu_down  = $chu['lower_trigram']  ?? '';
$_ho_up     = $ho['upper_trigram']   ?? '';
$_ho_down   = $ho['lower_trigram']   ?? '';
$_bien_up   = isset($bien) ? ($bien['upper_trigram'] ?? '') : '';
$_bien_down = isset($bien) ? ($bien['lower_trigram'] ?? '') : '';

// ── Thể / Dụng ──
// Hào 1-3 nằm ở hạ quái → Thể = thượng quái (quái KHÔNG chứa hào động)
// Hào 4-6 nằm ở thượng quái → Thể = hạ quái
$_the_override  = $fullData['the_override']  ?? '';
$_dung_override = $fullData['dung_override'] ?? '';

if (is_string($_the_override) && $_the_override !== '' && is_string($_dung_override) && $_dung_override !== '') {
    $_the_quai  = $_the_override;
    $_dung_quai = $_dung_override;
}
else {
    $_the_quai  = ($_dong <= 3) ? $_chu_up   : $_chu_down;
    $_dung_quai = ($_dong <= 3) ? $_chu_down : $_chu_up;
}
$_dung_bien_quai = ($_dong <= 3) ? $_bien_down : $_bien_up;

$_hanh_the       = $_bat_quai[$_the_quai]['hanh']       ?? '';
$_hanh_dung      = $_bat_quai[$_dung_quai]['hanh']      ?? '';
$_hanh_ho_up     = $_bat_quai[$_ho_up]['hanh']          ?? '';
$_hanh_ho_down   = $_bat_quai[$_ho_down]['hanh']        ?? '';
$_hanh_dung_bien = $_bat_quai[$_dung_bien_quai]['hanh'] ?? '';

$_method_label = 'Mai Hoa Dịch Số';
if ($mode === 'maihoa_time')       $_method_label = 'Mai Hoa Dịch Số (Thời Gian Động Tâm)';
elseif ($mode === 'maihoa_number') $_method_label = 'Mai Hoa Dịch Số (Con Số Động Tâm)';
elseif ($mode === 'maihoa_object') $_method_label = 'Mai Hoa Dịch Số (Ngoại Tượng)';

$number_meta = $fullData['number_meta'] ?? null;
$object_meta = $fullData['object_meta'] ?? null;
$time_meta   = $fullData['time_meta']   ?? null;

$_hao_tu = (is_array($chu) && isset($chu['lines'][$_dong])) ? ($chu['lines'][$_dong] ?: '') : '';
?>

<div class="ich-detail-panel" data-panel="lapque">
    <div class="lhq-wrap">
        <div class="lhq-header">
            <div class="lhq-avatar">&#9775;</div>
            <div class="lhq-meta">
                <p>Phương pháp: <strong><?= esc_html($_method_label) ?></strong></p>
                <p>Việc cần xem: <strong><?= esc_html($question) ?></strong></p>
                <p>Thời gian: <?= esc_html($time_string) ?> (Giờ <?= esc_html($cc['gio']) ?>, ngày <?= esc_html($lunar_str) ?> Âm lịch)</p>
                <p>Can chi: Giờ <?= esc_html($cc['gio']) ?> &nbsp;·&nbsp; Ngày <?= esc_html($cc['ngay']) ?> &nbsp;·&nbsp; Tháng <?= esc_html($cc['thang']) ?> &nbsp;·&nbsp; Năm <?= esc_html($cc['nam']) ?></p>
                <div class="lhq-meta-row">
                    <span>Tiết Khí: <span class="lhq-accent"><?= esc_html($tiet_khi_str) ?></span></span>
                    <span>Nhật thần: <span class="lhq-accent"><?= esc_html($cc['nhat_kien'] . '-' . $cc['hanh_ngay']) ?></span></span>
                    <span>Nguyệt lệnh: <span class="lhq-accent"><?= esc_html($cc['nguyet_lenh'] . '-' . $cc['hanh_thang']) ?></span></span>
                </div>
            </div>
        </div>
        <div class="lhq-tbl-container">
            <table class="lhq-tbl">
                <tr>
                    <td colspan="3" class="lhq-td-top-wrap lhq-mh-top-row">
                        <div class="lhq-flex-container">
                            <div class="lhq-flex-col lhq-col-bordered">
                                <div class="lhq-td-hexname no-border">
                                    <?= mb_strtoupper(esc_html($chu['name_vi'] ?? ''), 'UTF-8') ?><br>
                                    <small class="lhq-hexname-note">(Quẻ Chủ)</small>
                                </div>
                                <div class="lhq-td-heximg no-border flex-grow lhq-mh-img-box">
                                    <?= $_hexfig($_chu_bin, $_changing_lines) ?>
                                </div>
                            </div>
                            <div class="lhq-flex-col lhq-col-bordered">
                                <div class="lhq-td-hexname no-border">
                                    <?= mb_strtoupper(esc_html($ho['name_vi'] ?? ''), 'UTF-8') ?><br>
                                    <small class="lhq-hexname-note">(Quẻ Hỗ)</small>
                                </div>
                                <div class="lhq-td-heximg no-border flex-grow lhq-mh-img-box">
                                    <?= $_hexfig($_ho_bin, []) ?>
                                </div>
                            </div>
                            <div class="lhq-flex-col">
                                <div class="lhq-td-hexname no-border">
                                    <?= mb_strtoupper(esc_html($bien['name_vi'] ?? ''), 'UTF-8') ?><br>
                                    <small class="lhq-hexname-note">(Quẻ Biến)</small>
                                </div>
                                <div class="lhq-td-heximg no-border flex-grow lhq-mh-img-box">
                                    <?= $_hexfig($_bien_bin, []) ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
                $chu_kw  = !empty($chu['keywords'])                     ? implode(' · ', $chu['keywords'])  : '';
                $ho_kw   = !empty($ho['keywords'])                      ? implode(' · ', $ho['keywords'])   : '';
                $bien_kw = (!empty($bien) && !empty($bien['keywords'])) ? implode(' · ', $bien['keywords']) : '';
                ?>
                <tr>
                    <td class="lhq-divr">
                        <?php if ($chu_kw): ?><span class="lhq-mh-keywords"><?= esc_html($chu_kw) ?></span><?php endif; ?>
                        <span class="lhq-mh-meaning"><?= esc_html($chu['meaning'] ?? '') ?></span>
                    </td>
                    <td class="lhq-divr">
                        <?php if ($ho_kw): ?><span class="lhq-mh-keywords"><?= esc_html($ho_kw) ?></span><?php endif; ?>
                        <span class="lhq-mh-meaning"><?= esc_html($ho['meaning'] ?? '') ?></span>
                    </td>
                    <td>
                        <?php if ($bien_kw): ?><span class="lhq-mh-keywords"><?= esc_html($bien_kw) ?></span><?php endif; ?>
                        <span class="lhq-mh-meaning"><?= esc_html($bien['meaning'] ?? '') ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="lhq-divr"><?= $_quai_cell($_chu_up, $_the_quai === $_chu_up ? 'the' : ($_dung_quai === $_chu_up ? 'dung' : '')) ?></td>
                    <td class="lhq-divr"><?= $_quai_cell($_ho_up) ?></td>
                    <td><?= $_quai_cell($_bien_up, $_dung_bien_quai === $_bien_up ? 'dung' : '') ?></td>
                </tr>
                <tr>
                    <td class="lhq-divr"><?= $_quai_cell($_chu_down, $_the_quai === $_chu_down ? 'the' : ($_dung_quai === $_chu_down ? 'dung' : '')) ?></td>
                    <td class="lhq-divr"><?= $_quai_cell($_ho_down) ?></td>
                    <td><?= $_quai_cell($_bien_down, $_dung_bien_quai === $_bien_down ? 'dung' : '') ?></td>
                </tr>
                <tr>
                    <td class="lhq-divr">
                        <span class="lhq-mh-the">Thể: <?= esc_html($_the_quai) ?> <em>(<?= esc_html($_hanh_the) ?>)</em></span><br>
                        <span class="lhq-mh-dung">Dụng: <?= esc_html($_dung_quai) ?> <em>(<?= esc_html($_hanh_dung) ?>)</em></span>
                    </td>
                    <td class="lhq-divr">
                        <span class="lhq-mh-the">Thể: <?= esc_html($_the_quai) ?> <em>(<?= esc_html($_hanh_the) ?>)</em></span><br>
                        <span class="lhq-mh-dung">T.Hỗ: <?= esc_html($_ho_up) ?> <em>(<?= esc_html($_hanh_ho_up) ?>)</em></span><br>
                        <span class="lhq-mh-dung">H.Hỗ: <?= esc_html($_ho_down) ?> <em>(<?= esc_html($_hanh_ho_down) ?>)</em></span>
                    </td>
                    <td>
                        <span class="lhq-mh-the">Thể: <?= esc_html($_the_quai) ?> <em>(<?= esc_html($_hanh_the) ?>)</em></span><br>
                        <span class="lhq-mh-dung">Biến: <?= esc_html($_dung_bien_quai) ?> <em>(<?= esc_html($_hanh_dung_bien) ?>)</em></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="lhq-save-row">
    <button type="button" class="lhq-btn-save" id="lhq-btn-save" data-action="save-image">↓ Lưu Quẻ</button>
</div>