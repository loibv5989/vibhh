<?php

if (!defined('ABSPATH')) exit;

function tuvi_render_landing() {
    ob_start();
    include TUVI_PLUGIN_DIR . 'template/landing.php';
    return ob_get_clean();
}

function tuvi_render_lap_la_so_form() {
    ob_start();

    $result = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tuvi_ngay_sinh'])) {
        $input = [
                'ho_ten'    => sanitize_text_field($_POST['tuvi_ho_ten'] ?? ''),
                'ngay_sinh' => sanitize_text_field($_POST['tuvi_ngay_sinh']),
                'gio_sinh'  => sanitize_text_field($_POST['tuvi_gio_sinh']),
                'gioi_tinh' => sanitize_text_field($_POST['tuvi_gioi_tinh']),
                'nam_xem'   => sanitize_text_field($_POST['tuvi_nam_xem'] ?? date('Y')),
        ];
        $result = TuVi_Engine::lap_la_so($input);
    }
    ?>

    <div class="tuvi tuvi-form-wrap<?= ($result && !isset($result['error'])) ? ' has-results' : '' ?>">

        <?php include TUVI_PLUGIN_DIR . 'template/la-so/form.php'; ?>

        <?php if ($result && !isset($result['error'])):
            $thong_tin = $result['thong_tin'];
            $la_so = $result['la_so'];

            $grid_classes = [
                    6 => 'cung-ti',  7 => 'cung-ngo',  8 => 'cung-mui',  9 => 'cung-than',
                    5 => 'cung-thin',                                    10 => 'cung-dau',
                    4 => 'cung-mao',                                     11 => 'cung-tuat',
                    3 => 'cung-dan', 2 => 'cung-suu',  1 => 'cung-ty',   12 => 'cung-hoi'
            ];
            $anchor_map = [5=>6, 3=>4, 10=>9, 12=>11, 8=>7, 1=>2];
            ?>
            <?php include TUVI_PLUGIN_DIR . 'template/la-so/ls-page.php'; ?>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function tuvi_ntx(array $atts = []): string {
    $result = null;
    $mode = 'single';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tuvi_ntx_submit'])) {
        $mode = sanitize_text_field($_POST['tuvi_mode'] ?? 'single');
        $input = [
            'muc_dich' => sanitize_text_field($_POST['tuvi_purpose'] ?? 'cuoi'),
        ];

        if ($mode === 'range') {
            $input['tu_ngay'] = sanitize_text_field($_POST['tuvi_start'] ?? '');
            $input['den_ngay'] = sanitize_text_field($_POST['tuvi_end'] ?? '');
            $input['limit'] = (int)($_POST['tuvi_limit'] ?? 30);
            if (!empty($_POST['tuvi_ngay_sinh'])) {
                $input['ngay_sinh'] = sanitize_text_field($_POST['tuvi_ngay_sinh']);
                $input['gio_sinh']  = sanitize_text_field($_POST['tuvi_gio_sinh'] ?? '');
                $input['gioi_tinh'] = sanitize_text_field($_POST['tuvi_gioi_tinh'] ?? 'nam');
            }
            $result = TuVi_NTX::tim_ngay_tot($input);
        } else {
            $input['ngay'] = sanitize_text_field($_POST['tuvi_date'] ?? '');
            if (!empty($_POST['tuvi_ngay_sinh'])) {
                $input['ngay_sinh'] = sanitize_text_field($_POST['tuvi_ngay_sinh']);
                $input['gio_sinh']  = sanitize_text_field($_POST['tuvi_gio_sinh'] ?? '');
                $input['gioi_tinh'] = sanitize_text_field($_POST['tuvi_gioi_tinh'] ?? 'nam');
            }
            $result = TuVi_NTX::xem_ngay($input);
        }
    }

    ob_start();
    ?>
    <div class="tuvi-ntx-wrap<?= ($result && !isset($result['error'])) ? ' has-results' : '' ?>" id="tuvi-ntx-app">
        <?php include TUVI_PLUGIN_DIR . 'template/ntx/form.php'; ?>
        <?php include TUVI_PLUGIN_DIR . 'template/ntx/ntx-page.php'; ?>
    </div>
    <?php

    return ob_get_clean();
}

function tuvi_hop_tuoi(array $atts = []): string {
    $result = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tuvi_ht_submit'])) {
        $input = [
                'muc_dich'    => sanitize_text_field($_POST['tuvi_ht_muc_dich'] ?? 'hon_nhan'),
            // Người A
                'ten_a'       => sanitize_text_field($_POST['tuvi_ht_ten_a'] ?? ''),
                'ngay_sinh_a' => sanitize_text_field($_POST['tuvi_ht_ngay_sinh_a'] ?? ''),
                'gio_sinh_a'  => sanitize_text_field($_POST['tuvi_ht_gio_sinh_a'] ?? ''),
                'gioi_tinh_a' => sanitize_text_field($_POST['tuvi_ht_gioi_tinh_a'] ?? 'nam'),
            // Người B
                'ten_b'       => sanitize_text_field($_POST['tuvi_ht_ten_b'] ?? ''),
                'ngay_sinh_b' => sanitize_text_field($_POST['tuvi_ht_ngay_sinh_b'] ?? ''),
                'gio_sinh_b'  => sanitize_text_field($_POST['tuvi_ht_gio_sinh_b'] ?? ''),
                'gioi_tinh_b' => sanitize_text_field($_POST['tuvi_ht_gioi_tinh_b'] ?? 'nu'),
        ];

        $result = TuVi_HopTuoi::evaluate($input);
    }

    ob_start();
    ?>
    <div class="tuvi-ht-wrap<?= ($result && !isset($result['error'])) ? ' has-results' : '' ?>" id="tuvi-ht-app">
        <?php include TUVI_PLUGIN_DIR . 'template/hop-tuoi/form.php'; ?>

        <?php if ($result && isset($result['success']) && $result['success'] === false): ?>
            <div class="tuvi-result-box tuvi-result-bad" style="margin-top:20px;"><?= esc_html($result['message']) ?></div>
        <?php endif; ?>

        <?php include TUVI_PLUGIN_DIR . 'template/hop-tuoi/ht-page.php'; ?>
    </div>
    <?php

    return ob_get_clean();
}