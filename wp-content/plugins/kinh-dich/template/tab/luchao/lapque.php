
<?php
if (!defined('ABSPATH')) exit;

/** @var string $time_string */
/** @var string $question */
/** @var array $cc */
/** @var string $lunar_str */
/** @var array $chu */
/** @var array $luchao_data */
/** @var array $luchao_bien_data */
/** @var array $fullData */
/** @var array $ho */
/** @var array $bien */
/** @var array $changing_lines */

?>
<div class="ich-detail-panel" data-panel="lapque">
    <?php
    // ─── Lấy Timestamps & Thời gian ──────────────────────────────
    $tz = wp_timezone();
    if (isset($fullData['toss_time'])) {
        $dt = new DateTime($fullData['toss_time'], $tz);
        $timestamp = $dt->getTimestamp();
    } else {
        $timestamp = time();
    }

    $solar_m = (int)wp_date('n', $timestamp);
    $solar_d = (int)wp_date('j', $timestamp);
    $solar_y = (int)wp_date('Y', $timestamp);

    // Tính chính xác Tiết Khí
    $tiet_khi_names = [
            1 => 'Tiểu Hàn', 2 => 'Lập Xuân', 3 => 'Kinh Trập', 4 => 'Thanh Minh',
            5 => 'Lập Hạ', 6 => 'Mang Chủng', 7 => 'Tiểu Thử', 8 => 'Lập Thu',
            9 => 'Bạch Lộ', 10 => 'Hàn Lộ', 11 => 'Lập Đông', 12 => 'Đại Tuyết'
    ];
    $tiet_day = Iching_Calendar::getTietDay($solar_m, $solar_y);
    $tk_idx = ($solar_d >= $tiet_day) ? $solar_m : ($solar_m - 1);
    if ($tk_idx == 0) $tk_idx = 12;
    $tiet_khi_str = $tiet_khi_names[$tk_idx] ?? 'Đang xác định';

    // ─── Dữ liệu cơ bản ───────────────────────────────────────
    $_parts_ngay = explode(' ', $cc['ngay']);
    $_can_ngay = $_parts_ngay[0] ?? '';
    $_chi_ngay = $_parts_ngay[1] ?? '';

    $_tuan_khong = Iching_Calendar::getTuanKhong($cc['ngay']);
    $_loc_than = Iching_Calendar::getLoc($_can_ngay);
    $_dich_ma = Iching_Calendar::getMa($_chi_ngay);
    $_quy_nhan = Iching_Calendar::getQuyNhan($_can_ngay);
    $_dao_hoa = Iching_Calendar::getDaoHoa($_chi_ngay);

    $_chu_lines = $luchao_data['lines'] ?? [];
    $_bien_lines = $luchao_bien_data['lines'] ?? [];
    $_has_bien = !empty($luchao_bien_data) && ($luchao_bien_data !== $luchao_data);
    if (!$_has_bien) $_bien_lines = $_chu_lines;

    // ─── Phục Thần ────────────────────────────────────────────
    $_co_lt = array_column($_chu_lines, 'luc_than');
    $_lt_std = ['Quan Quỷ', 'Phụ Mẫu', 'Huynh Đệ', 'Tử Tôn', 'Thê Tài'];
    $_khuyet = array_diff($_lt_std, $_co_lt);
    $_phuc_list = [];
    if (!empty($_khuyet)) {
        $_cung = $luchao_data['cung'] ?? '';
        foreach ($_khuyet as $_lt) {
            $_pt = Iching_LucHao::getPhucThan($_cung, $_lt);
            if ($_pt) $_phuc_list[$_pt['under_line']] = $_pt;
        }
    }

    // ─── Lấy binary keys ĐỂ VẼ HÀO ─────────────────────────────
    $_chu_bin = $fullData['chu_key'] ?? '111111';
    $_bien_bin = $fullData['bien_key'] ?? $_chu_bin;
    $_ho_bin = !empty($fullData['ho_key']) ? $fullData['ho_key'] : '111111';

    // ─── Helper: nét hào (inline) ──────────────────────────────
    // SỬA LỖI 2: Hàm này giờ lấy biến $bit ('0' hoặc '1') của Quẻ để quyết định Âm/Dương, không lấy Địa Chi nữa.
    $_hao = function (string $bit, bool $is_dong = false): string {
        $color = $is_dong ? 'blue' : 'red';
        if ($bit === '0') {
            return '<span class="lhq-bar yin ' . $color . '"><span></span><span></span></span>';
        }
        return '<span class="lhq-bar yang ' . $color . '"><span></span></span>';
    };

    // ─── Helper: hình quẻ 6 nét (block) ────────────────────────
    $_hexfig = function (string $bin, array $changing = []) {
        $out = '<div class="lhq-hexfig">';
        for ($i = 5; $i >= 0; $i--) {
            $is_dong = in_array($i + 1, $changing);
            $color = $is_dong ? 'blue' : 'red';
            $bit = $bin[$i] ?? '1';
            if ($bit === '1') {
                $out .= '<div class="lhq-hline yang ' . $color . '"><span></span></div>';
            } else {
                $out .= '<div class="lhq-hline yin ' . $color . '"><span></span><span></span></div>';
            }
        }
        $out .= '</div>';
        return $out;
    };

    // ─── get chi cuối chuỗi ───────────────────────────────────
    $_gchi = function (string $s): string {
        $p = explode(' ', trim($s));
        return end($p);
    };
    ?>

    <div class="lhq-wrap">
        <div class="lhq-header">
            <div class="lhq-avatar">☯</div>
            <div class="lhq-meta">
                <p>Phương pháp lập quẻ: <strong>Kim Tiền Lục Hào (Nạp Giáp)</strong></p>
                <p>Việc cần xem: <strong><?= esc_html($question) ?></strong></p>
                <p>Thời gian lập quẻ (GĐT): Giờ <?= esc_html($cc['gio']) ?>, ngày <?= esc_html($lunar_str) ?> Âm
                    lịch (Ngày dương: <?= esc_html($time_string) ?>)</p>
                <p>Can chi: Giờ <?= esc_html($cc['gio']) ?>, ngày <?= esc_html($cc['ngay']) ?>,
                    tháng <?= esc_html($cc['thang']) ?>, năm <?= esc_html($cc['nam']) ?></p>
                <div class="lhq-meta-row">
                    <span>Tiết Khí: <span class="lhq-accent"><?= esc_html($tiet_khi_str) ?></span></span>
                    <span>Nhật thần: <span
                                class="lhq-accent"><?= esc_html($cc['nhat_kien'] . '-' . $cc['hanh_ngay']) ?></span></span>
                    <span>Nguyệt lệnh: <span
                                class="lhq-accent"><?= esc_html($cc['nguyet_lenh'] . '-' . $cc['hanh_thang']) ?></span></span>
                </div>
            </div>
        </div>
        <div class="lhq-tbl-container">
            <table class="lhq-tbl">

                <tr>
                    <td colspan="12" class="lhq-td-top-wrap">
                        <div class="lhq-flex-container">
                            <div class="lhq-flex-col lhq-col-bordered">
                                <div class="lhq-td-hexname no-border">
                                    <?= mb_strtoupper(esc_html($chu['name_vi']), 'UTF-8') ?><br>
                                    <small class="lhq-hexname-note">(Quẻ Chủ)</small>
                                </div>
                                <div class="lhq-td-heximg no-border flex-grow">
                                    <?= $_hexfig($_chu_bin, $changing_lines) ?>
                                    <span class="lhq-hexfig-label">Họ <?= esc_html($luchao_data['cung'] ?? '') ?></span>
                                </div>
                                <div class="lhq-td-cungtitle no-border top-bordered">
                                    <?= mb_strtoupper(esc_html($chu['name']), 'UTF-8') ?>
                                </div>
                            </div>

                            <div class="lhq-flex-col lhq-col-bordered">
                                <div class="lhq-td-hexname no-border">
                                    <?= mb_strtoupper(esc_html($ho['name_vi']), 'UTF-8') ?><br>
                                    <small class="lhq-hexname-note">(Quẻ Hỗ)</small>
                                </div>
                                <div class="lhq-td-heximg no-border flex-grow">
                                    <?= $_hexfig($_ho_bin, []) ?>
                                    <span class="lhq-hexfig-label">&nbsp;</span>
                                </div>
                                <div class="lhq-td-cungtitle no-border top-bordered">
                                    <?= mb_strtoupper(esc_html($ho['name']), 'UTF-8') ?>
                                </div>
                            </div>

                            <div class="lhq-flex-col">
                                <div class="lhq-td-hexname no-border">
                                    <?php if ($_has_bien): ?>
                                        <?= mb_strtoupper(esc_html($bien['name_vi'] ?? ''), 'UTF-8') ?><br>
                                        <small class="lhq-hexname-note">(Quẻ Biến)</small>
                                    <?php else: ?>
                                        QUẺ TĨNH<br>
                                        <small class="lhq-hexname-note">(Không Biến)</small>
                                    <?php endif; ?>
                                </div>
                                <div class="lhq-td-heximg no-border flex-grow">
                                    <?= $_hexfig($_has_bien ? $_bien_bin : $_chu_bin, []) ?>
                                    <span class="lhq-hexfig-label">
                                        <?php if ($_has_bien): ?>
                                            Họ <?= esc_html($luchao_bien_data['cung'] ?? '') ?>
                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="lhq-td-cungtitle no-border top-bordered">
                                    <?= mb_strtoupper(esc_html($_has_bien ? ($bien['name'] ?? '') : 'TĨNH'), 'UTF-8') ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="lhq-th">Hào</td>
                    <td class="lhq-th">T/Ư</td>
                    <td class="lhq-th">Lục Thú</td>
                    <td class="lhq-th">Lục Thân</td>
                    <td class="lhq-th">Can Chi</td>
                    <td class="lhq-th">Phục Thần</td>
                    <td class="lhq-th lhq-divr">TK</td>

                    <td class="lhq-th">Hào</td>
                    <td class="lhq-th">Lục Thân</td>
                    <td class="lhq-th">Can Chi</td>
                    <td class="lhq-th">Vượng Suy</td>
                    <td class="lhq-th">TK</td>
                </tr>

                <?php for ($_i = 6; $_i >= 1; $_i--):
                    $_c = $_chu_lines[$_i] ?? [];
                    $_b = $_bien_lines[$_i] ?? [];
                    $_dong = in_array($_i, $changing_lines ?? []);

                    $_tu = '';
                    if (!empty($_c['is_the'])) $_tu = 'T';
                    elseif (!empty($_c['is_ung'])) $_tu = 'Ư';

                    $_pt_txt = '';
                    if (!empty($_phuc_list[$_i])) {
                        $_p = $_phuc_list[$_i];
                        $_pt_txt = esc_html($_p['luc_than']) . ' - ' . esc_html((!empty($_p['can']) ? $_p['can'] . ' ' : '') . ($_p['chi'] ?? ''));
                    }

                    $_tk_c = in_array($_c['chi'] ?? '', $_tuan_khong) ? 'K' : '';
                    $_tk_b = ($_has_bien && in_array($_b['chi'] ?? '', $_tuan_khong)) ? 'K' : '';

                    $_can_c = !empty($_c['can']) ? $_c['can'] . ' ' : '';
                    $_can_b = !empty($_b['can']) ? $_b['can'] . ' ' : '';

                    $_cc_c = esc_html($_can_c . ($_c['chi'] ?? '') . ' - ' . ($_c['hanh'] ?? ''));
                    $_cc_b = $_has_bien ? esc_html($_can_b . ($_b['chi'] ?? '') . ' - ' . ($_b['hanh'] ?? '')) : '';

                    $_vs_b = $_has_bien ? Iching_Calendar::getVuongSuy($_b['hanh'] ?? '', $cc['hanh_thang']) : '';

                    $_color_cls = $_dong ? 'lhq-blue' : 'lhq-red';
                    ?>
                    <tr>
                        <td><?= $_hao($_chu_bin[$_i - 1], $_dong) ?></td>
                        <td class="lhq-bold"><?= esc_html($_tu) ?></td>
                        <td class="<?= $_color_cls ?>"><?= esc_html($_c['luc_thu'] ?? '') ?></td> <td class="<?= $_color_cls ?>"><?= esc_html($_c['luc_than'] ?? '') ?></td>
                        <td class="<?= $_color_cls ?>"><?= $_cc_c ?></td>
                        <td class="lhq-pt"><?= $_pt_txt ? wp_kses_post($_pt_txt) : '' ?></td>
                        <td class="lhq-bold lhq-divr"><?= $_tk_c ?></td>

                        <td><?= $_has_bien ? $_hao($_bien_bin[$_i - 1], $_dong) : '' ?></td>
                        <td class="<?= $_color_cls ?>"><?= $_has_bien ? esc_html($_b['luc_than'] ?? '') : '' ?></td>
                        <td class="<?= $_color_cls ?>"><?= $_cc_b ?></td>
                        <td class="<?= $_color_cls ?>"><?= esc_html($_vs_b) ?></td>
                        <td class="lhq-bold"><?= $_tk_b ?></td>
                    </tr>
                <?php endfor; ?>

                <tr>
                    <td colspan="2" class="lhq-th2">Can Chi Hào</td>
                    <td class="lhq-th2">Vượng Suy</td>
                    <td class="lhq-th2">Lộc</td>
                    <td class="lhq-th2">Mã</td>
                    <td class="lhq-th2">Quý</td>
                    <td class="lhq-th2 lhq-divr">Đào</td>

                    <td colspan="2" class="lhq-th2">Can Chi Biến</td>
                    <td class="lhq-th2">Lộc</td>
                    <td class="lhq-th2">Mã</td>
                    <td class="lhq-th2">Quý/Đào</td>
                </tr>

                <?php for ($_i = 6; $_i >= 1; $_i--):
                    $_c = $_chu_lines[$_i] ?? [];
                    $_b = $_bien_lines[$_i] ?? [];
                    $_dong = in_array($_i, $changing_lines ?? []);

                    $_vs_c = Iching_Calendar::getVuongSuy($_c['hanh'] ?? '', $cc['hanh_thang']);

                    $_xc = $_gchi($_c['chi'] ?? '');
                    $_xb = $_gchi($_b['chi'] ?? '');

                    $_loc_c = ($_xc === $_loc_than) ? 'L' : '-';
                    $_ma_c = ($_xc === $_dich_ma) ? 'M' : '-';
                    $_quy_c = in_array($_xc, $_quy_nhan) ? 'Q' : '-';
                    $_dao_c = ($_xc === $_dao_hoa) ? 'Đ' : '-';

                    $_loc_b = ($_has_bien && $_xb === $_loc_than) ? 'L' : '-';
                    $_ma_b = ($_has_bien && $_xb === $_dich_ma) ? 'M' : '-';
                    $_quy_b = ($_has_bien && in_array($_xb, $_quy_nhan)) ? 'Q' : '-';
                    $_dao_b = ($_has_bien && $_xb === $_dao_hoa) ? 'Đ' : '-';
                    $_quy_dao_b = ($_quy_b !== '-' ? $_quy_b : ($_dao_b !== '-' ? $_dao_b : '-')); // Gộp Quý/Đào cho quẻ biến để tiết kiệm diện tích

                    $_can_chi_full_c = (!empty($_c['can']) ? $_c['can'] . ' ' : '') . ($_c['chi'] ?? '');
                    $_can_chi_full_b = $_has_bien ? ((!empty($_b['can']) ? $_b['can'] . ' ' : '') . ($_b['chi'] ?? '')) : '';

                    $_color_cls = $_dong ? 'lhq-blue' : 'lhq-red';
                    ?>
                    <tr>
                        <td colspan="2" class="<?= $_color_cls ?>"><?= esc_html($_can_chi_full_c) ?></td>
                        <td class="<?= $_color_cls ?>"><?= esc_html($_vs_c) ?></td>
                        <td class="lhq-bold"><?= $_loc_c ?></td>
                        <td class="lhq-bold"><?= $_ma_c ?></td>
                        <td class="lhq-bold"><?= $_quy_c ?></td>
                        <td class="lhq-bold lhq-divr"><?= $_dao_c ?></td>

                        <td colspan="2" class="<?= $_color_cls ?>"><?= esc_html($_can_chi_full_b) ?></td>
                        <td class="lhq-bold"><?= $_loc_b ?></td>
                        <td class="lhq-bold"><?= $_ma_b ?></td>
                        <td class="lhq-bold"><?= $_quy_dao_b ?></td>
                    </tr>
                <?php endfor; ?>
            </table>
        </div>
    </div>
</div>
<div class="lhq-save-row">
    <button type="button" class="lhq-btn-save" id="lhq-btn-save" data-action="save-image">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Lưu Quẻ
    </button>
</div>