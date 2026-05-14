<?php

/**
 * @var array|null $tu_tru     Kết quả tứ trụ từ engine (tuỳ chọn)
 * @var array      $battu_data Toàn bộ data từ BatTu_Data::load('all')
 */

if (!defined('ABSPATH')) exit;

$nh_colors = [
    'kim'  => ['hex' => '#c9a227', 'name' => 'Kim',  'icon' => '⚙'],
    'moc'  => ['hex' => '#16a34a', 'name' => 'Mộc',  'icon' => '🌿'],
    'thuy' => ['hex' => '#2563eb', 'name' => 'Thủy', 'icon' => '💧'],
    'hoa'  => ['hex' => '#dc2626', 'name' => 'Hỏa',  'icon' => '🔥'],
    'tho'  => ['hex' => '#92400e', 'name' => 'Thổ',  'icon' => '🪨'],
];

function bt_pol($p) { return $p === '+' ? '⊕ Dương' : '⊖ Âm'; }

$truong_sinh_labels = [
    'truong_sinh' => ['Trường Sinh', '#16a34a'],
    'moc_duc'     => ['Mộc Dục',    '#65a30d'],
    'quan_doi'    => ['Quan Đới',   '#0284c7'],
    'lam_quan'    => ['Lâm Quan',   '#6366f1'],
    'de_vuong'    => ['Đế Vượng',   '#7c3aed'],
    'suy'         => ['Suy',        '#b45309'],
    'benh'        => ['Bệnh',       '#d97706'],
    'tu'          => ['Tử',         '#dc2626'],
    'mo'          => ['Mộ',         '#9f1239'],
    'tuyet'       => ['Tuyệt',      '#6b7280'],
    'thai'        => ['Thai',       '#0891b2'],
    'duong'       => ['Dưỡng',      '#059669'],
];

$thien_can = $battu_data['thien_can'] ?? [];
$dia_chi   = $battu_data['dia_chi']   ?? [];
$ts_map    = $battu_data['truong_sinh_12']['map']    ?? [];
$ts_states = $battu_data['truong_sinh_12']['states'] ?? [];
$can_hop   = $battu_data['can_hop']   ?? [];
$can_xung  = $battu_data['can_xung']  ?? [];
$chi_luc_hop  = $battu_data['chi_luc_hop']  ?? [];
$chi_tam_hop  = $battu_data['chi_tam_hop']  ?? [];
$chi_luc_xung = $battu_data['chi_luc_xung'] ?? [];
$chi_hinh  = $battu_data['chi_tuong_hinh'] ?? [];
$chi_hai   = $battu_data['chi_tuong_hai']  ?? [];
$chi_pha   = $battu_data['chi_tuong_pha']  ?? [];
$ngu_hanh  = $battu_data['ngu_hanh'] ?? [];

$thap_than_desc = [
    'ty_kien'    => ['Tỷ Kiên',    '比肩', 'Tỷ Kiếp', '#ef4444',
        'Cùng hành, cùng âm dương với Nhật Chủ. Đại diện anh em, bạn bè đồng cấp, đồng nghiệp cạnh tranh. Khi nhiều: dễ cứng đầu, không nhờ vả được người khác.'],
    'kiep_tai'   => ['Kiếp Tài',   '劫財', 'Tỷ Kiếp', '#ef4444',
        'Cùng hành, khác âm dương. Hung hơn Tỷ Kiên: tranh đoạt tài lộc, quan hệ phức tạp. Nhưng nếu Nhật Chủ nhược, Kiếp Tài là trợ thủ tốt.'],
    'thuc_than'  => ['Thực Thần',  '食神', 'Thực Thương', '#f59e0b',
        'Nhật Chủ sinh ra, cùng âm dương. Tài năng sáng tạo, ăn phúc, duyên nghệ thuật. Nữ mệnh: con cái khỏe mạnh. Thực Thần chế Thất Sát là cách cục quý.'],
    'thuong_quan'=> ['Thương Quan', '傷官', 'Thực Thương', '#f59e0b',
        'Nhật Chủ sinh ra, khác âm dương. Thông minh xuất chúng, cá tính mạnh, phá cách quan. Nếu không có Tài Tinh đi kèm: dễ bị cô lập.'],
    'thien_tai'  => ['Thiên Tài',  '偏財', 'Tài Tinh', '#10b981',
        'Nhật Chủ khắc, cùng âm dương. Tài lộc bôn ba, đào hoa, đại diện cha (với nam). Vượng địa: buôn bán giỏi, thích giao du rộng rãi.'],
    'chinh_tai'  => ['Chính Tài',  '正財', 'Tài Tinh', '#10b981',
        'Nhật Chủ khắc, khác âm dương. Tài lộc ổn định, chăm chỉ tích lũy. Nam mệnh: vợ. Cần Thân Vượng mới giữ được Chính Tài lâu dài.'],
    'thien_quan' => ['Thất Sát',   '七殺', 'Quan Sát', '#6366f1',
        'Khắc Nhật Chủ, cùng âm dương (còn gọi Thiên Quan). Áp lực, thách thức, quyền lực phi chính thống. Chế hóa đúng cách thành cách cục lập nghiệp phi thường.'],
    'chinh_quan' => ['Chính Quan', '正官', 'Quan Sát', '#6366f1',
        'Khắc Nhật Chủ, khác âm dương. Kỷ luật, danh dự, pháp luật, địa vị xã hội. Nữ mệnh: chồng. Quan tinh vượng thì tiến thân chính đạo.'],
    'thien_an'   => ['Thiên Ấn',  '偏印', 'Ấn Tinh', '#06b6d4',
        'Sinh Nhật Chủ, cùng âm dương (còn gọi Kiêu Thần). Trực giác, học thuật đặc biệt, nghệ thuật tâm linh. Nhiều quá: trì hoãn, cô lập, mẹ kế hoặc dì.'],
    'chinh_an'   => ['Chính Ấn',  '正印', 'Ấn Tinh', '#06b6d4',
        'Sinh Nhật Chủ, khác âm dương. Học vấn chính thống, quý nhân, mẹ ruột. Ấn vượng giúp Nhật Chủ nhược đứng vững. Nếu lá số có quan tinh + ấn tinh: cách cục công danh.'],
];

$ts_desc = [
    'truong_sinh' => 'Vừa ra đời, tràn đầy sinh lực. Thời kỳ khởi đầu thuận lợi, được quý nhân hỗ trợ.',
    'moc_duc'     => 'Tuổi thiếu niên hiếu động. Dễ mắc sai lầm do bốc đồng, cần rèn giũa.',
    'quan_doi'    => 'Đội mũ ra đời, bước vào xã hội. Bắt đầu định hình sự nghiệp.',
    'lam_quan'    => 'Đỉnh thời làm quan, sự nghiệp thăng hoa, uy tín cao.',
    'de_vuong'    => 'Đỉnh cao quyền lực và tài lực. Hành này tại đây mạnh nhất.',
    'suy'         => 'Bắt đầu suy giảm, năng lực chậm lại. Nên giữ gìn hơn là xông pha.',
    'benh'        => 'Sức lực yếu ớt, dễ gặp bệnh tật và trở ngại.',
    'tu'          => 'Hành khí tắt lịm tại chi này. Sao tại cung này bị hao tổn đáng kể.',
    'mo'          => 'Vào kho, thu tàng. Không hẳn xấu - Mộ đôi khi là tích lũy ngầm.',
    'tuyet'       => 'Hoàn toàn tuyệt khí, trước lúc tái sinh. Yếu nhất trong vòng 12.',
    'thai'        => 'Hình thành trong bào thai. Còn ở dạng tiềm năng, chưa phát lộ.',
    'duong'       => 'Được nuôi dưỡng, chuẩn bị chào đời. Năng lượng đang dần phục hồi.',
];

$can_hop_desc = [
    'giap_ky'   => 'Giáp (Mộc+) hợp Kỷ (Thổ−) → hóa Thổ. Hợp hóa xảy ra khi tháng sinh thuận lợi.',
    'at_canh'   => 'Ất (Mộc−) hợp Canh (Kim+) → hóa Kim. Đây là cặp hợp mang tính cương nhu bổ sung.',
    'binh_tan'  => 'Bính (Hỏa+) hợp Tân (Kim−) → hóa Thủy. Hỏa khắc Kim nhưng vẫn hợp - tình cảm phức tạp.',
    'dinh_nham' => 'Đinh (Hỏa−) hợp Nhâm (Thủy+) → hóa Mộc. Thủy khắc Hỏa nhưng sinh ra tình ý.',
    'mau_quy'   => 'Mậu (Thổ+) hợp Quý (Thủy−) → hóa Hỏa. Thổ khắc Thủy nhưng giao tình bền chặt.',
];

$chi_tam_hop_desc = [
    'dan_ngo_tuat' => 'Tam hợp Hỏa cục - năng lượng nhiệt huyết, hành động, lãnh đạo.',
    'than_ty_thin' => 'Tam hợp Thủy cục - trí tuệ, linh động, học thuật, lưu chuyển.',
    'hoi_mao_mui'  => 'Tam hợp Mộc cục - nhân từ, phát triển, sáng tạo, bền bỉ.',
    'ti_dau_suu'   => 'Tam hợp Kim cục - cương nghị, quyết đoán, tích lũy tài sản.',
];

$luc_hop_desc = [
    'ty_suu'  => 'Tý − Sửu → hóa Thổ. Cặp hợp âm dương, tương trợ lẫn nhau.',
    'dan_hoi' => 'Dần − Hợi → hóa Mộc. Thú và cá tương duyên.',
    'mao_tuat'=> 'Mão − Tuất → hóa Hỏa. Mèo và Chó vốn xung nhưng vẫn hợp kỳ lạ.',
    'thin_dau'=> 'Thìn − Dậu → hóa Kim. Rồng và Gà - hợp thành kho báu.',
    'ti_than' => 'Tỵ − Thân → hóa Thủy. Rắn và Khỉ - tương hỗ linh động.',
    'ngo_mui' => 'Ngọ − Mùi → hóa Thổ. Ngựa và Dê - gần gũi bình hòa.',
];
?>

<div class="battu-ct-wrapper">
    <div class="tuvi-ct-alert">
        📖 Bảng này giải thích các khái niệm nền tảng của Bát Tự: Thiên Can, Địa Chi, quan hệ sinh khắc và vòng Trường Sinh 12 cung.
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">1. Thiên Can (天干) - 10 Can</h3>
        <p class="tuvi-ct-desc">
            Thiên Can là 10 yếu tố Âm Dương biểu thị lực lượng trên trời - mỗi Can mang một hành và một cực tính.
            Trong Bát Tự, <strong>Thiên Can Nhật Trụ là Nhật Chủ</strong>, trung tâm của toàn bộ lá số.
        </p>
        <div class="battu-ct-can-grid">
            <?php foreach ($thien_can as $id => $can):
                $nh  = $nh_colors[$can['element']] ?? ['hex' => '#888', 'name' => '?', 'icon' => ''];
                $pol = $can['polarity'] === '+' ? 'Dương' : 'Âm';
                $pol_class = $can['polarity'] === '+' ? 'battu-ct-pol-duong' : 'battu-ct-pol-am';
                ?>
                <div class="battu-ct-can-card" style="--can-color: <?= $nh['hex'] ?>;">
                    <div class="battu-ct-can-name"><?= $can['name'] ?></div>
                    <div class="battu-ct-can-icon"><?= $nh['icon'] ?></div>
                    <div class="battu-ct-can-hanh" style="color: <?= $nh['hex'] ?>;"><?= $nh['name'] ?></div>
                    <div class="battu-ct-can-pol <?= $pol_class ?>"><?= $pol ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="battu-ct-note">
            💡 <strong>Dương Can</strong> (Giáp · Bính · Mậu · Canh · Nhâm): chủ động, hướng ngoại, cứng cáp.
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Âm Can</strong> (Ất · Đinh · Kỷ · Tân · Quý): nhu mì, linh hoạt, thích ứng giỏi.
        </div>
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">2. Địa Chi (地支) - 12 Chi & Tàng Can</h3>
        <p class="tuvi-ct-desc">
            Địa Chi là 12 yếu tố ẩn chứa bên dưới - mỗi Chi không chỉ mang một hành chính mà còn
            <strong>tàng chứa 1–3 Thiên Can bên trong</strong> (Tàng Can / Nhân Nguyên Tư Trụ).
            Tàng Can quyết định thực lực thực sự của Địa Chi trong lá số.
        </p>
        <div class="battu-ct-chi-grid">
            <?php foreach ($dia_chi as $id => $chi):
                $nh = $nh_colors[$chi['element']] ?? ['hex' => '#888', 'name' => '?'];
                $pol_label = $chi['polarity'] === '+' ? 'Dương' : 'Âm';
                $pol_class = $chi['polarity'] === '+' ? 'battu-ct-pol-duong' : 'battu-ct-pol-am';
                ?>
                <div class="battu-ct-chi-card" style="--chi-color: <?= $nh['hex'] ?>;">
                    <div class="battu-ct-chi-header">
                        <span class="battu-ct-chi-name"><?= $chi['name'] ?></span>
                        <span class="battu-ct-chi-pol <?= $pol_class ?>"><?= $pol_label ?></span>
                    </div>
                    <div class="battu-ct-chi-hanh" style="color: <?= $nh['hex'] ?>;"><?= $nh['name'] ?></div>
                    <div class="battu-ct-chi-tang">
                        <span class="battu-ct-tang-label">Tàng can:</span>
                        <?php
                        $tangs = $chi['tang_can'] ?? [];
                        $parts = [];
                        foreach ($tangs as $can_id => $pct) {
                            $cn = $thien_can[$can_id]['name'] ?? $can_id;
                            $ce = $thien_can[$can_id]['element'] ?? 'tho';
                            $cc = $nh_colors[$ce]['hex'] ?? '#888';
                            $parts[] = "<span style='color:{$cc}; font-weight:600;'>{$cn}</span><small style='color:var(--text-secondary);'>({$pct}%)</small>";
                        }
                        echo implode(' · ', $parts);
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="battu-ct-note">
            💡 Chi có 1 Tàng Can (như Tý, Mão, Dậu) là <strong>Chi thuần khiết</strong>, lực mạnh tập trung.
            Chi có 3 Tàng Can (như Dần, Tỵ, Thân, Hợi) là <strong>Chi phức hợp</strong>, lực đa dạng, phong phú.
        </div>
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">3. Thập Thần (十神) - 10 Thần Tương Quan</h3>
        <p class="tuvi-ct-desc">
            Thập Thần là quan hệ của từng Can trong lá số đối với <strong>Nhật Chủ</strong>, xác định bằng tương sinh/khắc và âm dương.
            Mọi luận đoán về tài, quan, hôn nhân, con cái đều qua Thập Thần.
        </p>
        <div class="battu-ct-thapthan-grid">
            <?php foreach ($thap_than_desc as $id => $tt):
                [$ten, $symbol, $group, $color, $mo_ta] = $tt;
                ?>
                <div class="battu-ct-tt-card" style="--tt-color: <?= $color ?>;">
                    <div class="battu-ct-tt-header">
                        <span class="battu-ct-tt-symbol"><?= $symbol ?></span>
                        <span class="battu-ct-tt-name"><?= $ten ?></span>
                        <span class="battu-ct-tt-group" style="color:<?= $color ?>;"><?= $group ?></span>
                    </div>
                    <p class="battu-ct-tt-desc"><?= $mo_ta ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="battu-ct-note">
            💡 <strong>Cách xác định Thập Thần:</strong> Lấy hành của Can đang xét → so với Nhật Chủ theo sinh/khắc → xem cùng hay khác âm dương → tra bảng 10 Thần.
        </div>
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">4. Quan Hệ Thiên Can - Hợp & Xung</h3>
        <div class="battu-ct-legend-grid">

            <div class="tuvi-ct-legend-box">
                <h4>Ngũ Hợp Thiên Can</h4>
                <p class="battu-ct-legend-intro">Khi 2 Can hợp nhau, có thể hóa thành hành mới nếu tháng sinh thuận lợi. Hợp = thân thiết, bị ràng buộc.</p>
                <ul>
                    <?php foreach ($can_hop as $key => $hop):
                        $pair = explode('_', $key);
                        $c1 = $thien_can[$pair[0]]['name'] ?? $pair[0];
                        $c2 = $thien_can[$pair[1]]['name'] ?? $pair[1];
                        $nh_hoa = $nh_colors[$hop['result']] ?? ['name'=>'?','hex'=>'#888'];
                        $desc_text = $can_hop_desc[$key] ?? '';
                        ?>
                        <li>
                        <span class="battu-ct-pair-badge" style="background: color-mix(in srgb, <?= $nh_hoa['hex'] ?> 15%, transparent); border-color: <?= $nh_hoa['hex'] ?>; color: <?= $nh_hoa['hex'] ?>;">
                            <?= $c1 ?> ✦ <?= $c2 ?> → <?= $nh_hoa['name'] ?>
                        </span>
                            <span class="battu-ct-pair-desc"><?= $desc_text ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tuvi-ct-legend-box">
                <h4>Tứ Xung Thiên Can</h4>
                <p class="battu-ct-legend-intro">Can xung nhau = đối kháng, tranh giành. Trong lá số dễ sinh xung đột nội tâm hoặc quan hệ đối nghịch.</p>
                <ul>
                    <?php foreach ($can_xung as $pair_str):
                        $p = explode('_', $pair_str);
                        $c1 = $thien_can[$p[0]]['name'] ?? $p[0];
                        $c2 = $thien_can[$p[1]]['name'] ?? $p[1];
                        $e1 = $thien_can[$p[0]]['element'] ?? 'tho';
                        $color_xung = $nh_colors[$e1]['hex'] ?? '#888';
                        ?>
                        <li>
                        <span class="battu-ct-pair-badge battu-ct-xung" style="border-color:<?= $color_xung ?>; color:<?= $color_xung ?>;">
                            <?= $c1 ?> ✕ <?= $c2 ?>
                        </span>
                            <span class="battu-ct-pair-desc"><?= $c1 ?> và <?= $c2 ?> cùng hành nhưng đối cực âm dương, kình địch lẫn nhau.</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">5. Quan Hệ Địa Chi - Hợp · Xung · Hình · Hại · Phá</h3>
        <p class="tuvi-ct-desc">
            Địa Chi tương tác với nhau tạo ra các hiện tượng phức tạp hơn Thiên Can.
            Trong lá số, khi 2 cột (trụ) chứa Chi có quan hệ đặc biệt, tính chất của lá số thay đổi đáng kể.
        </p>
        <div class="battu-ct-chi-rel-grid">
            <div class="battu-ct-chi-rel-box">
                <h4 class="battu-ct-rel-title battu-ct-rel-hop">🤝 Lục Hợp (Hợp đôi)</h4>
                <p class="battu-ct-rel-intro">6 cặp Chi hợp nhau - tăng cường lực lượng và hóa thành hành mới.</p>
                <div class="battu-ct-rel-list">
                    <?php foreach ($luc_hop_desc as $pair => $desc):
                        $p = explode('_', $pair);
                        $ch1 = $dia_chi[$p[0]]['name'] ?? $p[0];
                        $ch2 = $dia_chi[$p[1]]['name'] ?? $p[1];
                        $hop_hanh = $chi_luc_hop[$pair] ?? '';
                        $hh_color = $hop_hanh ? ($nh_colors[$hop_hanh]['hex'] ?? '#888') : '#888';
                        $hh_name  = $hop_hanh ? ($nh_colors[$hop_hanh]['name'] ?? '') : '';
                        ?>
                        <div class="battu-ct-rel-item">
                        <span class="battu-ct-chi-pair" style="--rel-color:<?= $hh_color ?>;">
                            <?= $ch1 ?> + <?= $ch2 ?>
                            <?php if ($hh_name): ?><em>→ <?= $hh_name ?></em><?php endif; ?>
                        </span>
                            <span class="battu-ct-rel-desc"><?= $desc ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="battu-ct-chi-rel-box">
                <h4 class="battu-ct-rel-title battu-ct-rel-hop">🔺 Tam Hợp (Hợp ba)</h4>
                <p class="battu-ct-rel-intro">3 Chi hội tụ thành một hành mạnh mẽ - cách cục vượng địa.</p>
                <div class="battu-ct-rel-list">
                    <?php foreach ($chi_tam_hop as $key => $hanh):
                        $p = explode('_', $key);
                        $names = array_map(fn($x) => $dia_chi[$x]['name'] ?? $x, $p);
                        $hcolor = $nh_colors[$hanh]['hex'] ?? '#888';
                        $hname  = $nh_colors[$hanh]['name'] ?? $hanh;
                        $desc_text = $chi_tam_hop_desc[$key] ?? '';
                        ?>
                        <div class="battu-ct-rel-item">
                        <span class="battu-ct-chi-pair" style="--rel-color:<?= $hcolor ?>;">
                            <?= implode(' + ', $names) ?> <em>→ <?= $hname ?></em>
                        </span>
                            <span class="battu-ct-rel-desc"><?= $desc_text ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="battu-ct-chi-rel-box">
                <h4 class="battu-ct-rel-title battu-ct-rel-xung">⚡ Lục Xung (6 cặp xung)</h4>
                <p class="battu-ct-rel-intro">Chi xung nhau = va chạm, biến động, phá vỡ ổn định. Dịch chuyển, thay đổi môi trường sống và làm việc.</p>
                <div class="battu-ct-rel-list">
                    <?php foreach ($chi_luc_xung as $pair_str):
                        $p = explode('_', $pair_str);
                        $ch1 = $dia_chi[$p[0]]['name'] ?? $p[0];
                        $ch2 = $dia_chi[$p[1]]['name'] ?? $p[1];
                        $e1  = $dia_chi[$p[0]]['element'] ?? 'tho';
                        $xc  = $nh_colors[$e1]['hex'] ?? '#888';
                        ?>
                        <div class="battu-ct-rel-item">
                        <span class="battu-ct-chi-pair battu-ct-chi-xung" style="--rel-color:<?= $xc ?>;">
                            <?= $ch1 ?> ✕ <?= $ch2 ?>
                        </span>
                            <span class="battu-ct-rel-desc"><?= $ch1 ?> và <?= $ch2 ?> đối đỉnh 180°, xung phá lẫn nhau.</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="battu-ct-chi-rel-box">
                <h4 class="battu-ct-rel-title battu-ct-rel-hinh">⚠ Tương Hình (Hình phạt)</h4>
                <p class="battu-ct-rel-intro">Hình mang tính pháp lý, bệnh tật, tai nạn. Có 3 loại hình và tự hình.</p>
                <div class="battu-ct-rel-list">
                    <?php
                    $hinh_map = [
                        'Tam Hình Vô Ân (Ỷ lại)'  => $chi_hinh['tam_hinh_tri_the']  ?? [],
                        'Tam Hình Vô Lễ (Tự cao)'  => $chi_hinh['tam_hinh_vo_an']    ?? [],
                        'Nhị Hình Vô Lễ'            => $chi_hinh['nhi_hinh_vo_le']    ?? [],
                        'Tự Hình (xung bản thân)'   => $chi_hinh['tu_hinh']           ?? [],
                    ];
                    foreach ($hinh_map as $label => $chis):
                        $names = array_map(fn($x) => $dia_chi[$x]['name'] ?? $x, $chis);
                        ?>
                        <div class="battu-ct-rel-item">
                        <span class="battu-ct-chi-pair battu-ct-chi-hinh">
                            <?= implode(' · ', $names) ?>
                        </span>
                            <span class="battu-ct-rel-desc"><?= $label ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="battu-ct-chi-rel-box">
                <h4 class="battu-ct-rel-title battu-ct-rel-hai">🩸 Tương Hại (6 cặp hại)</h4>
                <p class="battu-ct-rel-intro">Hại gây tổn thương ngầm, đứt gãy quan hệ, phá vỡ từ bên trong - âm thầm hơn Xung.</p>
                <div class="battu-ct-rel-list">
                    <?php foreach ($chi_hai as $pair_str):
                        $p = explode('_', $pair_str);
                        $ch1 = $dia_chi[$p[0]]['name'] ?? $p[0];
                        $ch2 = $dia_chi[$p[1]]['name'] ?? $p[1];
                        ?>
                        <div class="battu-ct-rel-item">
                        <span class="battu-ct-chi-pair battu-ct-chi-hai">
                            <?= $ch1 ?> ⚔ <?= $ch2 ?>
                        </span>
                            <span class="battu-ct-rel-desc"><?= $ch1 ?> và <?= $ch2 ?> tương hại nhau - dễ gặp tiểu nhân, phản trắc.</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="battu-ct-chi-rel-box">
                <h4 class="battu-ct-rel-title battu-ct-rel-pha">🔨 Tương Phá (6 cặp phá)</h4>
                <p class="battu-ct-rel-intro">Phá làm phân tán, không trọn vẹn. Kế hoạch nửa chừng, hợp tác dang dở.</p>
                <div class="battu-ct-rel-list">
                    <?php foreach ($chi_pha as $pair_str):
                        $p = explode('_', $pair_str);
                        $ch1 = $dia_chi[$p[0]]['name'] ?? $p[0];
                        $ch2 = $dia_chi[$p[1]]['name'] ?? $p[1];
                        ?>
                        <div class="battu-ct-rel-item">
                        <span class="battu-ct-chi-pair battu-ct-chi-pha">
                            <?= $ch1 ?> ✦ <?= $ch2 ?>
                        </span>
                            <span class="battu-ct-rel-desc"><?= $ch1 ?> và <?= $ch2 ?> tương phá - làm hỏng những gì đang tốt.</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">6. Vòng Trường Sinh 12 Cung</h3>
        <p class="tuvi-ct-desc">
            Mỗi Thiên Can trải qua vòng đời 12 trạng thái tại 12 Địa Chi - từ Trường Sinh (ra đời) đến Dưỡng (tái sinh).
            Trạng thái này xác định độ mạnh/yếu của từng Can tại vị trí của nó trong Tứ Trụ.
        </p>
        <div class="battu-ct-ts-states">
            <?php foreach ($truong_sinh_labels as $id => [$label, $color]):
                $desc_text = $ts_desc[$id] ?? '';
                ?>
                <div class="battu-ct-ts-state-item" style="--ts-color: <?= $color ?>;">
                    <span class="battu-ct-ts-badge"><?= $label ?></span>
                    <span class="battu-ct-ts-desc"><?= $desc_text ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="battu-ct-ts-table-wrap">
            <table class="battu-ct-ts-table">
                <thead>
                <tr>
                    <th>Can \ Chi</th>
                    <?php foreach ($dia_chi as $chi): ?>
                        <th style="color: <?= $nh_colors[$chi['element']]['hex'] ?? '#888' ?>;"><?= $chi['name'] ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ts_map as $can_id => $chi_seq):
                    $can_info = $thien_can[$can_id] ?? [];
                    $can_color = $nh_colors[$can_info['element'] ?? 'tho']['hex'] ?? '#888';

                    $state_of_chi = [];
                    foreach ($chi_seq as $i => $chi_id) {
                        $state_of_chi[$chi_id] = $ts_states[$i] ?? '';
                    }
                    ?>
                    <tr>
                        <td class="battu-ct-ts-can" style="color: <?= $can_color ?>; font-weight: 700;"><?= $can_info['name'] ?? $can_id ?></td>
                        <?php foreach ($dia_chi as $chi_id => $chi_data):
                            $state_id = $state_of_chi[$chi_id] ?? '';
                            [$state_label, $state_color] = $truong_sinh_labels[$state_id] ?? ['?', '#888'];
                            ?>
                            <td>
                                <span class="battu-ct-ts-cell" style="color: <?= $state_color ?>;" title="<?= $ts_desc[$state_id] ?? '' ?>"><?= $state_label ?></span>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="battu-ct-note">
            💡 Di chuột / nhấn vào từng ô để xem mô tả trạng thái. <strong>Đế Vượng</strong> và <strong>Trường Sinh</strong> là 2 trạng thái mạnh nhất; <strong>Tuyệt</strong> và <strong>Tử</strong> là yếu nhất.
        </div>
    </div>
    <div class="tuvi-ct-section">
        <h3 class="tuvi-ct-title">7. Bảng 60 Hoa Giáp - Can Chi Kết Hợp</h3>
        <p class="tuvi-ct-desc">
            Can và Chi ghép theo quy tắc: <strong>Dương Can đi với Dương Chi, Âm Can đi với Âm Chi</strong> → tạo ra 60 tổ hợp (Lục Thập Hoa Giáp) tuần hoàn mỗi 60 năm.
        </p>
        <div class="battu-ct-60-grid">
            <?php foreach ($data['can_chi_60'] ?? [] as $cc):
                $can_info = $thien_can[$cc['can']] ?? [];
                $chi_info = $dia_chi[$cc['chi']] ?? [];
                $can_color = $nh_colors[$can_info['element'] ?? 'tho']['hex'] ?? '#888';
                $chi_color = $nh_colors[$chi_info['element'] ?? 'tho']['hex'] ?? '#888';
                ?>
                <div class="battu-ct-60-item">
                    <span class="battu-ct-60-no"><?= str_pad($cc['index_1_based'], 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="battu-ct-60-can" style="color: <?= $can_color ?>;"><?= $can_info['name'] ?? '' ?></span>
                    <span class="battu-ct-60-chi" style="color: <?= $chi_color ?>;"><?= $chi_info['name'] ?? '' ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="battu-ct-note">
            💡 Mỗi Năm / Tháng / Ngày / Giờ trong lá số Bát Tự đều tương ứng với một trong 60 tổ hợp này.
        </div>
    </div>
</div>