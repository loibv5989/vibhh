<?php
/**
 * Template variables passed from render.php:
 * @var array $thong_tin Thông tin cá nhân
 * @var array $la_so Dữ liệu lá số
 * @var array $grid_classes Mapping CSS
 * @var array $anchor_map Mapping cung đối
 */
?>

<div class="tuvi-tabs" role="tablist">
    <button class="tuvi-tab active" data-tab="la-so" role="tab">Lá Số</button>
    <button class="tuvi-tab" data-tab="chi-tiet" role="tab">Giải Nghĩa</button>
</div>

<?php if (!empty($thong_tin['egg_message'])): ?>
    <div class="tuvi-ct-alert"><?= esc_html($thong_tin['egg_message']) ?></div>
<?php endif; ?>

<div class="tuvi-tab-pane active" id="tuvi-tab-la-so">
    <div class="laso-wrapper" id="tuvi-laso-wrapper">
        <div class="laso-grid">
            <div class="ls-center">
                <div class="ls-logo">LÁ SỐ TỬ VI</div>
                <div class="ls-info-table">
                    <div class="ls-info-col">
                        <div class="ls-info-row"><div class="ls-info-label">Họ tên:</div><div class="ls-info-val"><?= esc_html($thong_tin['ho_ten']) ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Năm sinh:</div><div class="ls-info-val"><?= $thong_tin['nam_can_chi'] ?> (<?= $thong_tin['tuoi'] ?> tuổi ÂL)</div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Dương lịch:</div><div class="ls-info-val"><?= $thong_tin['ngay_duong'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Âm lịch:</div><div class="ls-info-val"><?= $thong_tin['ngay_am'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Giờ sinh:</div><div class="ls-info-val"> Giờ <?= $thong_tin['gio_am'] ?></div></div>
                    </div>
                    <div class="ls-info-col">
                        <div class="ls-info-row"><div class="ls-info-label">Năm Xem:</div><div class="ls-info-val sao-luu"><?= $thong_tin['nam_xem'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Âm Dương:</div><div class="ls-info-val"><?= $thong_tin['gioi_tinh'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Mệnh:</div><div class="ls-info-val"><?= $thong_tin['nam_nap_am'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Cục:</div><div class="ls-info-val"><?= $thong_tin['cuc_name'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Chủ Mệnh:</div><div class="ls-info-val"><?= $thong_tin['chu_menh'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Chủ Thân:</div><div class="ls-info-val"><?= $thong_tin['chu_than'] ?></div></div>
                        <div class="ls-info-row"><div class="ls-info-label">Tiểu Hạn:</div><div class="ls-info-val" style="color:#e53935;font-weight:700;">Cung <?= $thong_tin['tieu_han_chi'] ?? '' ?></div></div>
                    </div>
                </div>
                <div class="ls-center-text">
                    <div><?= $thong_tin['am_duong_ly'] ?></div>
                    <div><?= $thong_tin['menh_cuc_ly'] ?>, <?= $thong_tin['than_cu'] ?></div>
                </div>

                <?php if (!empty($thong_tin['tu_hoa_nam_xem'])): ?>
                <div class="ls-tu-hoa-box">
                    <div class="ls-tu-hoa-title">Tứ Hóa Năm <?= $thong_tin['nam_xem_raw'] ?> - Can <?= $thong_tin['can_nam_xem_ten'] ?></div>
                    <div class="ls-tu-hoa-grid">
                        <?php foreach ($thong_tin['tu_hoa_nam_xem'] as $th): ?>
                        <div class="ls-th-item ls-th-<?= $th['type'] ?>">
                            <div class="ls-th-sao e-<?= $th['sao_el'] ?>"><?= mb_strtoupper($th['sao_name']) ?></div>
                            <div class="ls-th-hoa"><?= $th['label'] ?></div>
                            <div class="ls-th-cung">→ Cung <?= $th['cung_name'] ?> (<?= $th['cung_chi'] ?>)</div>
                            <div class="ls-th-note"><?= $th['note'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="tuvi-lg">An sao - Tử Vi Đẩu Số Toàn Thư • <a href="<?= home_url() ?>"><?= parse_url(home_url(), PHP_URL_HOST) ?></a></div>
            </div>

            <?php foreach ($la_so as $id => $cung): ?>
                <div class="ls-cung <?= $grid_classes[$id] ?><?= $cung['is_tieu_han'] ? ' ls-cung-tieu-han' : '' ?>">
                    <?php
                    $tt_labels = [];
                    if (isset($anchor_map[$id])) {
                        $c2 = $anchor_map[$id];
                        if (in_array('Tuần', $la_so[$id]['tuan_triet']) && in_array('Tuần', $la_so[$c2]['tuan_triet'])) $tt_labels[] = 'Tuần';
                        if (in_array('Triệt', $la_so[$id]['tuan_triet']) && in_array('Triệt', $la_so[$c2]['tuan_triet'])) $tt_labels[] = 'Triệt';
                    }
                    if (!empty($tt_labels)):
                        $tt_class = in_array($id, [5, 3, 10, 12]) ? 'tt-horizontal' : 'tt-vertical';
                        ?>
                        <div class="ls-tuantriet <?= $tt_class ?>"><?= implode("<br>", $tt_labels) ?></div>
                    <?php endif; ?>

                    <div class="ls-header">
                        <div class="ls-can-cung"><?= mb_substr($cung['can_name'], 0, 1, 'UTF-8') . '.' . $cung['chi_name'] ?></div>
                        <div class="ls-chuc-nang"><?= $cung['cung_name'] ?></div>
                        <div class="ls-dai-van"><?= $cung['dai_van'] ?></div>
                    </div>

                    <div class="ls-chinh-tinh">
                        <?php if(empty($cung['chinh_tinh'])): ?>
                            <div class="tuvi-vo-chinh-dieu">VÔ CHÍNH DIỆU</div>
                        <?php else: ?>
                            <?php foreach ($cung['chinh_tinh'] as $sao):
                                $ds_text = $sao['do_sang'] ? " ({$sao['do_sang']})" : "";
                                echo "<div class='e-{$sao['element']}'>".mb_strtoupper($sao['name']).$ds_text."</div>";
                            endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="ls-phu-tinh">
                        <div class="pt-cat">
                            <?php foreach ($cung['phu_cat'] as $sao_phu) {
                                $ds_text = $sao_phu['do_sang'] ? " ({$sao_phu['do_sang']})" : "";
                                echo "<div class='e-{$sao_phu['element']}'>{$sao_phu['name']}{$ds_text}</div>";
                            } ?>
                            <?php foreach ($cung['sao_luu'] as $sao_luu) {
                                echo "<div class='sao-luu'>{$sao_luu}</div>";
                            } ?>
                        </div>
                        <div class="pt-hung">
                            <?php foreach ($cung['phu_hung'] as $sao_phu) {
                                $ds_text = $sao_phu['do_sang'] ? " ({$sao_phu['do_sang']})" : "";
                                echo "<div class='e-{$sao_phu['element']}'>{$sao_phu['name']}{$ds_text}</div>";
                            } ?>
                            <div class="tuvi-vong-sao-margin">
                                <?php foreach ($cung['vong_sao'] as $vong_sao) {
                                    echo "<div class='e-{$vong_sao['element']}'>{$vong_sao['name']}</div>";
                                } ?>
                            </div>
                        </div>
                    </div>

                    <div class="ls-footer">
                        <div class="ls-chi-cung"><?= $cung['chi_name'] ?></div>
                        <div class="ls-trang-sinh"><?= $cung['trang_sinh'] ?></div>
                        <div class="ls-thang"><?php
                            if (!empty($cung['thang_trong_cung'])) {
                                echo 'Th.' . implode(',', $cung['thang_trong_cung']);
                            }
                            if ($cung['is_tieu_han']) {
                                echo '<span class="ls-tieu-han-label"> ★TH</span>';
                            }
                        ?></div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="tuvi-tab-pane" id="tuvi-tab-chi-tiet">
    <div class="tuvi-ct-wrapper">

        <div class="tuvi-ct-alert">
            💡 Bảng này giúp bạn hiểu các thuật ngữ, ký hiệu viết tắt và màu sắc trong lá số tử vi.
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">1. Ký Hiệu Và Chữ Viết Tắt</h3>
            <div class="tuvi-ct-legend-grid">
                <div class="tuvi-ct-legend-box">
                    <h4>Các Chữ Cái Viết Tắt</h4>
                    <ul>
                        <li><span class="tuvi-ct-code">K.Tỵ</span> <strong>Chữ cái đầu góc trái:</strong> Là viết tắt của Thiên Can (Giáp, Ất, Bính, Đinh, Mậu, Kỷ, Canh, Tân, Nhâm, Quý). VD: K.Tỵ là Kỷ Tỵ, M.Thìn là Mậu Thìn.</li>
                        <li><span class="tuvi-ct-code">L.</span> <strong>Sao Lưu Niên (Chữ Đỏ):</strong> Chữ "L." (Vd: L.Hóa Lộc, L.Thái Tuế) là viết tắt của chữ <strong>Lưu</strong>. Đây là các ngôi sao di chuyển theo từng năm để báo hiệu vận hạn cát/hung của năm hiện tại.</li>
                        <li><span class="tuvi-ct-code">L.Hóa Lộc 🟢 / L.Hóa Quyền 🔵</span> Cát tinh năm xem — mang lại thuận lợi và quyền lực cho cung đó.</li>
                        <li><span class="tuvi-ct-code">L.Hóa Khoa 🟡</span> Cát tinh nhẹ — danh tiếng, bằng cấp, giải ách.</li>
                        <li><span class="tuvi-ct-code">L.Hóa Kỵ 🔴</span> Hung tinh năm xem — cần thận trọng với mọi việc liên quan đến cung đó.</li>
                        <li><span class="tuvi-ct-code">★TH</span> Tiểu hạn — Biến động vận hạn trong 12 tháng năm hạn.</li>
                    </ul>
                </div>

                <div class="tuvi-ct-legend-box">
                    <h4>Độ Sáng Của Sao (M/V/Đ/B/H)</h4>
                    <ul>
                        <li><span class="tuvi-ct-code">(M) Miếu / (V) Vượng</span> Sao rất sáng, phát huy tối đa năng lực tốt đẹp.</li>
                        <li><span class="tuvi-ct-code">(Đ) Đắc địa</span> Sao sáng sủa, mang lại sự thuận lợi, hanh thông (Lưu ý: Không phải là viết tắt của từ nhạy cảm).</li>
                        <li><span class="tuvi-ct-code">(B) Bình hòa</span> Trạng thái trung tính, bình thường.</li>
                        <li><span class="tuvi-ct-code">(H) Hãm địa</span> Sao bị mờ tối, mất đi tính tốt, dễ gây ra khó khăn, trắc trở.</li>
                        <li class="tuvi-note-hls">
                            <strong>Lưu ý:</strong> Vũ Khúc tại Tỵ và Hợi là <strong>Hãm (H)</strong> theo chuẩn Tử Vi Đẩu Số Toàn Thư. Thất Sát tại Mùi là <strong>Miếu (M)</strong> do các tài liệu gốc đều xếp Mùi vào vị trí đắc cách của Thất Sát.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">2. Bản Mệnh & Hậu Vận</h3>
            <p class="tuvi-ct-desc">
                Thay vì tra cứu chung 14 chính tinh, dưới đây là phân tích cốt lõi về các sao đóng tại 2 cung quan trọng nhất trên lá số của bạn: <strong>Cung Mệnh</strong> (Bản chất, tiền vận) và <strong>Cung Thân</strong> (Hành động, hậu vận từ sau 30 tuổi).
            </p>

            <div class="tuvi-ct-dict-grid tuvi-dict-single">
                <?php
                $chinh_tinh_dict = [
                        'Tử Vi' => 'Ngôi sao Vua, đại diện cho quyền lực, sự bao dung, năng lực lãnh đạo và tôn quý.',
                        'Thiên Cơ' => 'Ngôi sao Trí Tuệ. Đại diện cho sự thông minh, khéo léo, mưu trí như một vị quân sư.',
                        'Thái Dương' => 'Mặt Trời. Đại diện cho sự quang minh, danh vọng, ý chí vươn lên và người đàn ông trong gia đình.',
                        'Vũ Khúc' => 'Ngôi sao Tài Chính. Chủ về tiền bạc, sự quyết đoán, đôi khi cô độc nhưng kinh doanh nhạy bén.',
                        'Thiên Đồng' => 'Ngôi sao Phúc Đức. Mang năng lượng trẻ con, hiền lành, thích sự an nhàn, hòa bình và hưởng thụ.',
                        'Liêm Trinh' => 'Ngôi sao Pháp Luật & Đào Hoa. Chủ về sự ngay thẳng, liêm khiết, khiếu nghệ thuật nhưng bốc đồng.',
                        'Thiên Phủ' => 'Kho Bạc Hoàng Gia. Đại diện cho tiền tài, đất đai, sự cẩn trọng, ổn định và quản lý tài sản tốt.',
                        'Thái Âm' => 'Mặt Trăng. Đại diện cho điền sản, sự lãng mạn, tinh tế, và người phụ nữ trong gia đình.',
                        'Tham Lang' => 'Ngôi sao Dục Vọng. Đại diện cho sự tham vọng, tài ngoại giao, vui thú vật chất và duyên tâm linh.',
                        'Cự Môn' => 'Ngôi sao Ngôn Ngữ. Đại diện cho cái miệng, tài ăn nói, biện luận, nhưng dễ sinh ra thị phi, cãi vã.',
                        'Thiên Tướng' => 'Vị Tướng Ấn Tín. Quyền lực, trượng nghĩa, thích giúp người, thường có ngoại hình thu hút và chỉn chu.',
                        'Thiên Lương' => 'Ngôi sao Bác Sĩ/Thầy Giáo. Đại diện cho sự thiện lương, tuổi thọ, thích che chở người khác và nguyên tắc.',
                        'Thất Sát' => 'Vị Tướng Tiền Tuyến. Uy dũng, sát phạt, xông pha, hành động quyết liệt nhưng cuộc đời thường nhiều sóng gió.',
                        'Phá Quân' => 'Ngôi sao Tiên Phong. Chủ về sự bứt phá, đập đi xây lại, hao tán, sáng tạo và tính cách nổi loạn.'
                ];

                $menh_html = '';
                $than_html = '';

                foreach ($la_so as $cung) {
                    if ($cung['cung_name'] === 'Mệnh') {
                        $is_than_cu_menh = (strpos($thong_tin['than_cu'], 'Mệnh') !== false);
                        $title = $is_than_cu_menh ? 'Cung Mệnh / Thân (Tại ' . $cung['chi_name'] . ')' : 'Cung Mệnh (Tại ' . $cung['chi_name'] . ')';

                        $menh_html .= '<div class="tuvi-ct-dict-item">';
                        $menh_html .= '<strong class="tuvi-dict-head">' . $title . '</strong>';

                        if (empty($cung['chinh_tinh'])) {
                            $menh_html .= '<div class="tuvi-dict-row"><span class="tuvi-vcd">Vô Chính Diệu:</span> Bản mệnh không có sao chủ. Bạn là người linh hoạt, dễ thích nghi nhưng thời trẻ dễ chông chênh, chịu ảnh hưởng mạnh từ hoàn cảnh.</div>';
                        } else {
                            foreach ($cung['chinh_tinh'] as $sao) {
                                $ten_sao = mb_convert_case($sao['name'], MB_CASE_TITLE, "UTF-8");
                                $y_nghia = $chinh_tinh_dict[$ten_sao] ?? '';
                                $menh_html .= '<div class="tuvi-dict-row"><span class="tuvi-sao-hl">' . $ten_sao . ':</span> ' . $y_nghia . '</div>';
                            }
                        }
                        $menh_html .= '</div>';
                    }

                    if (strpos($cung['cung_name'], 'Thân') !== false && $cung['cung_name'] !== 'Mệnh') {
                        $than_html .= '<div class="tuvi-ct-dict-item">';
                        $than_html .= '<strong class="tuvi-dict-head">Cung ' . $cung['cung_name'] . ' (Tại ' . $cung['chi_name'] . ')</strong>';

                        if (empty($cung['chinh_tinh'])) {
                            $than_html .= '<div class="tuvi-dict-row"><span class="tuvi-vcd">Vô Chính Diệu:</span> Hậu vận của bạn phụ thuộc nhiều vào môi trường và nỗ lực tự thân, ít có sự cố định, dễ thay đổi hướng đi sau tuổi 30.</div>';
                        } else {
                            foreach ($cung['chinh_tinh'] as $sao) {
                                $ten_sao = mb_convert_case($sao['name'], MB_CASE_TITLE, "UTF-8");
                                $y_nghia = $chinh_tinh_dict[$ten_sao] ?? '';
                                $than_html .= '<div class="tuvi-dict-row"><span class="tuvi-sao-hl">' . $ten_sao . ':</span> ' . $y_nghia . '</div>';
                            }
                        }
                        $than_html .= '</div>';
                    }
                }

                echo $menh_html;
                echo $than_html;
                ?>
            </div>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">3. Chìa Khóa Vận Mệnh</h3>
            <ul class="tuvi-ct-overview-list">
                <li>
                    <strong>Âm Dương: <span class="tuvi-ct-accent-text"><?= $thong_tin['am_duong_ly'] ?></span></strong>
                    <em>(Sự hòa hợp giữa tuổi và hoàn cảnh. Thuận lý thường dễ gặp thời, Nghịch lý đòi hỏi nỗ lực vươn lên từ gian khó).</em>
                </li>
                <li>
                    <strong>Mệnh - Cục: <span class="tuvi-ct-accent-text"><?= $thong_tin['menh_cuc_ly'] ?></span></strong>
                    <em>(Mối quan hệ giữa bạn và môi trường xã hội. Tương sinh là được thời thế nâng đỡ, Tương khắc là phải tự lập cánh sinh).</em>
                </li>
                <li>
                    <strong>Hậu Vận: <span class="tuvi-ct-accent-text"><?= $thong_tin['than_cu'] ?></span></strong>
                    <em>(Chi phối mạnh mẽ từ sau 30 tuổi. Ý nghĩa của cung này sẽ quyết định phần lớn sự nghiệp và gia đạo lúc trung niên).</em>
                </li>
            </ul>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">4. Tứ Hóa Năm Xem <?= $thong_tin['nam_xem'] ?></h3>
            <p class="tuvi-ct-desc-sm">
                Tứ Hóa là 4 ngôi sao thay đổi mỗi năm theo Thiên Can năm xem, quyết định luồng năng lượng cát hung chính yếu của năm. Chú ý cung nào nhận Hóa Lộc, Hóa Quyền, Hóa Khoa (cát) và cung nào nhận Hóa Kỵ (hung).
            </p>
            <?php if (!empty($thong_tin['tu_hoa_nam_xem'])): ?>
                <div class="tuvi-ct-tu-hoa-grid">
                    <?php foreach ($thong_tin['tu_hoa_nam_xem'] as $th): ?>
                        <div class="tuvi-ct-th-card tuvi-ct-th-<?= $th['type'] ?>">
                            <div class="tuvi-ct-th-header">
                                <span class="tuvi-ct-th-icon"><?= $th['icon'] ?></span>
                                <span class="tuvi-ct-th-label"><?= $th['label'] ?></span>
                            </div>
                            <div class="tuvi-ct-th-sao e-<?= $th['sao_el'] ?>"><?= mb_strtoupper($th['sao_name']) ?></div>
                            <div class="tuvi-ct-th-cung">Cung <strong><?= $th['cung_name'] ?></strong> (<?= $th['cung_chi'] ?>)</div>
                            <div class="tuvi-ct-th-note"><?= $th['note'] ?></div>
                            <?php
                            $th_desc = [
                                    'loc'   => 'Ngôi sao này tỏa sáng mạnh năm nay, thu hút tài lộc và cơ hội thuận lợi về cung này.',
                                    'quyen' => 'Ngôi sao này được kích hoạt quyền lực, tăng khả năng ra quyết định và ảnh hưởng trong cung này.',
                                    'khoa'  => 'Ngôi sao này đem lại danh tiếng, học thuật, giải ách, là điểm sáng nhẹ nhàng nhưng bền vững của năm.',
                                    'ky'    => 'Ngôi sao này bị giam lại năm nay, cần thận trọng, tránh mạo hiểm liên quan đến cung này.'
                            ];
                            echo '<div class="tuvi-ct-th-desc">' . ($th_desc[$th['type']] ?? '') . '</div>';
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="tuvi-ct-desc-sm">Không có dữ liệu Tứ Hóa cho năm xem.</p>
            <?php endif; ?>
        </div>

        <div class="tuvi-ct-section">
            <h3 class="tuvi-ct-title">5. Phân Loại 12 Cung</h3>
            <div class="tuvi-ct-cung-grid">
                <?php foreach ($la_so as $id => $cung): ?>
                    <div class="tuvi-ct-cung-card">

                        <div class="tuvi-ct-cung-header">
                            <h4 class="tuvi-ct-cung-name">Cung <?= $cung['cung_name'] ?></h4>
                            <span class="tuvi-ct-cung-meta">Đại vận: <?= $cung['dai_van'] ?> tuổi</span>
                        </div>

                        <div class="tuvi-ct-cung-body">
                            <?php if(!empty($cung['tuan_triet'])): ?>
                                <div class="tuvi-ct-tuan-triet">
                                    Bị án ngữ bởi: <strong><?= implode(', ', $cung['tuan_triet']) ?></strong>
                                    <span class="tuvi-ct-tuan-triet-note">(Làm giảm bớt/đảo ngược tính chất của các sao)</span>
                                </div>
                            <?php endif; ?>

                            <div class="tuvi-ct-sao-group">
                                <h5>Chính Tinh (Sao Chủ)</h5>
                                <?php if(empty($cung['chinh_tinh'])): ?>
                                    <p class="tuvi-ct-empty">Vô Chính Diệu (Mượn sao từ cung đối diện)</p>
                                <?php else: ?>
                                    <div class="tuvi-ct-tags">
                                        <?php foreach($cung['chinh_tinh'] as $s): ?>
                                            <span class="e-<?= $s['element'] ?> tuvi-tag-chinh"><?= mb_strtoupper($s['name']) ?><?= $s['do_sang'] ? " ({$s['do_sang']})" : "" ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="tuvi-ct-sao-group">
                                <h5>Cát Tinh (Sao Tốt)</h5>
                                <?php if(empty($cung['phu_cat'])): ?>
                                    <p class="tuvi-ct-empty">Không có</p>
                                <?php else: ?>
                                    <div class="tuvi-ct-tags">
                                        <?php foreach($cung['phu_cat'] as $s): ?>
                                            <span class="e-<?= $s['element'] ?> tuvi-tag-phu"><?= $s['name'] ?><?= $s['do_sang'] ? " ({$s['do_sang']})" : "" ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="tuvi-ct-sao-group">
                                <h5>Hung Sát (Sao Xấu)</h5>
                                <?php if(empty($cung['phu_hung'])): ?>
                                    <p class="tuvi-ct-empty">Không có</p>
                                <?php else: ?>
                                    <div class="tuvi-ct-tags">
                                        <?php foreach($cung['phu_hung'] as $s): ?>
                                            <span class="e-<?= $s['element'] ?> tuvi-tag-phu"><?= $s['name'] ?><?= $s['do_sang'] ? " ({$s['do_sang']})" : "" ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="tuvi-ct-sao-group">
                                <h5>Vòng Sao Phụ</h5>
                                <div class="tuvi-ct-tags">
                                    <span class="tuvi-tag-vong">Tràng Sinh: <?= $cung['trang_sinh'] ?></span>
                                    <?php foreach($cung['vong_sao'] as $s): ?>
                                        <span class="e-<?= $s['element'] ?> tuvi-tag-vong"><?= $s['name'] ?></span>
                                    <?php endforeach; ?>

                                    <?php if(!empty($cung['sao_luu'])): ?>
                                        <div class="tuvi-ct-tags-divider"></div>
                                        <?php foreach($cung['sao_luu'] as $sl): ?>
                                            <span class="tuvi-tag-luu"><?= $sl ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<div class="tuvi-wbtn action-controls">
    <button type="button" class="tuvi-btn-rrrl tuvi-btn-download" id="tuvi-download-btn">↓ Lưu lá số</button>
    <button type="button" class="tuvi-btn-rrrl" id="tuvi-reset-btn">← Lập lá số khác</button>
</div>

<?php
if (TuVi_Settings::get_instance()->allowAI()):
    ?>
    <div id="tuvi-ai-actions" class="tuvi-form-deep tuvi-mt-15">
        <p class="tuvi-kp">
            <strong>Lưu ý:</strong> Các công cụ lập lá số tử vi hiện nay có thể sử dụng các thuật toán và dữ liệu khác nhau,
            kết quả có thể khác nhau giữa <strong>Tử Vi Đẩu Số Toàn Thư của Trần Đoàn (Trần Hi Di)</strong> và các phương pháp cải biên hiện đại của các trường phái khác nhau,
            hoặc có thêm các dữ kiện khác như bát tự, <a href="/cung-hoang-dao/">cung hoàng đạo</a> hoặc <a href="/than-so-hoc/">thần số học</a>.<br>
            Trong đó Lá số tử vi bạn đang xem trên <strong><?= parse_url(home_url(), PHP_URL_HOST) ?></strong> này là dựa trên thuần Tử Vi Đẩu Số Toàn Thư của Trần Đoàn (Trần Hi Di), là tử vi cổ học.
        </p>
        <p class="tuvi-kp">💡 Xem luận giải lá số hoặc đặt câu hỏi cụ thể trên lá số của bạn.</p>
        <div id="tuvi-qa-input-area" class="tuvi-input-group tuvi-mb-15" style="display: none;">
            <textarea id="tuvi-user-question" name="tuvi_user_question" placeholder="VD: Công việc năm nay của tôi có thuận lợi không?"
                      rows="3" class="tuvi-textarea tuvi-w-100" maxlength="500"></textarea>
            <div class="tuvi-suggested-questions">
                <p class="tuvi-sq-label">Gợi ý hỏi (Nếu hỏi cho người khác thì nên nhập trực tiếp thông tin của người cần xem):</p>
                <div class="tuvi-sq-list">
                    <button type="button" class="tuvi-sq-btn" data-q="Lá số này có vận hạn gì trong năm nay?">Hạn năm nay</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Đường tình duyên và gia đạo của tôi sắp tới ra sao?">Tình duyên, gia đạo</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Công việc, ngành nghề nào phù hợp với tôi nhất?">Định hướng sự nghiệp</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Tài lộc năm nay thế nào, có nên mở rộng đầu tư lớn không?">Tài lộc, đầu tư</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Điểm mạnh và điểm yếu lớn nhất trong lá số của tôi là gì?">Điểm mạnh & yếu</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Sức khỏe năm nay của tôi có gì cần chú ý không?">Sức khỏe</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Lá số này nói gì về đường con cái của tôi?">Con cái</button>
                    <button type="button" class="tuvi-sq-btn" data-q="Tính cách con người theo lá số này phù hợp với công việc gì?"> Tính cách </button>
                </div>
            </div>
        </div>
        <div class="tuvi-ai-btn-group">
            <button class="tuvi-btn-submit" id="tuvi-btn-deep-analyze">
                <span class="tuvi-btn-text">Luận giải lá số</span>
                <span class="tuvi-btn-loading" style="display: none;"><span class="tuvi-spinner"></span> Đang luận giải...</span>
            </button>
            <button class="tuvi-btn-submit tuvi-btn-secondary" id="tuvi-btn-qa-analyze">
                <span class="tuvi-btn-text">Hỏi lá số</span>
                <span class="tuvi-btn-loading" style="display: none;"><span class="tuvi-spinner"></span> Đang luận giải...</span>
            </button>
            <button class="tuvi-btn-submit tuvi-btn-cancel" id="tuvi-btn-qa-cancel" style="display: none;">
                <span class="tuvi-btn-text">← Quay lại</span>
            </button>
        </div>
        <span class="tuvi-error tuvi-err-analyze tuvi-err-msg-block" style="display: none;"></span>
    </div>

    <div class="tuvi-luan-giai" id="tuvi-luan-giai" style="display: none;"></div>
    <p class="tuvi-disclaimer" id="tuvi-disclaimer" style="display:none;">
        ✦ Đây là kết quả tham khảo theo hệ thống chiêm tinh tử vi. Mọi hành động và hướng đi tiếp theo nằm ở sự lựa chọn sáng suốt cũng như nỗ lực của bản thân.
    </p>
<?php endif; ?>

<div class="tuvi-action-footer" id="tuvi-action-footer">
    <button type="button" class="tuvi-btn-rrrl tuvi-btn-comment" id="tuvi-btn-comment">Bình Luận</button>
</div>