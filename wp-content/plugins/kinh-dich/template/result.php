<?php
if (!defined('ABSPATH')) exit;

require_once ICHING_PLUGIN_DIR . 'includes/calendar.php';

function iching_render_result(string $question, array $fullData, string $mode = 'luchao'): string {
    $chu = $fullData['chu'];
    $ho  = $fullData['ho'];
    $bien = $fullData['bien'];
    $number_meta = $fullData['number_meta'] ?? null;
    $object_meta = $fullData['object_meta'] ?? null;
    $time_meta   = $fullData['time_meta'] ?? null;
    $tosses = $fullData['tosses'] ?? [];
    $changing_lines = $fullData['changing_lines'] ?? [];
    $changing_line = $fullData['changing_line'] ?? 0;

    $tz = wp_timezone();
    if (isset($fullData['toss_time'])) {
        $dt = new DateTime($fullData['toss_time'], $tz);
        $timestamp = $dt->getTimestamp();
    } else {
        $timestamp = time();
    }
    $time_string  = wp_date('H:i, d/m/Y', $timestamp);

    $dd = (int) wp_date('j', $timestamp);
    $mm = (int) wp_date('n', $timestamp);
    $yy = (int) wp_date('Y', $timestamp);

    $am_lich = new Iching_AmLich();
    $lunar_date = $am_lich->convertSolar2Lunar($dd, $mm, $yy, 7.0);

    $ngay_am  = str_pad($lunar_date[0], 2, '0', STR_PAD_LEFT);
    $thang_am = str_pad($lunar_date[1], 2, '0', STR_PAD_LEFT);
    $nam_am   = $lunar_date[2];
    $lunar_str = "{$ngay_am}/{$thang_am}/{$nam_am}";

    $cc           = Iching_Calendar::get($timestamp);
    $method_name = 'Kim Tiền Lục Hào (Nạp Giáp)';
    $luchao_data = null;
    $luchao_bien_data = null;

    if ($mode === 'luchao') {
        $can_ngay_str = explode(' ', $cc['ngay'])[0];
        $luchao_data = Iching_LucHao::parse($fullData['chu_key'], $can_ngay_str);
        if (!empty($fullData['bien_key']) && $fullData['chu_key'] !== $fullData['bien_key']) {
            $luchao_bien_data = Iching_LucHao::parse($fullData['bien_key'], $can_ngay_str, $luchao_data['hanh_cung']);
        }
    }

    if ($mode === 'maihoa_time') {
        $method_name = 'Mai Hoa Dịch Số (Thời Gian Động Tâm)';
    } elseif ($mode === 'maihoa_number') {
        $method_name = 'Mai Hoa Dịch Số (Con Số Động Tâm)';
    } elseif ($mode === 'maihoa_object') {
        $method_name = 'Mai Hoa Dịch Số (Ngoại Tượng)';
    } elseif ($mode === 'maihoa') {
        $method_name = 'Mai Hoa Dịch Số';
    }

    $lines = [
            ['type' => 'intro', 'text' => 'Phương pháp:'],
            ['type' => 'greeting', 'text' => $method_name],
            ['type' => 'intro', 'text' => "Giờ động tâm: Giờ {$cc['gio']}, ngày {$lunar_str} Âm lịch (Ngày dương: {$time_string})"],
            ['type' => 'intro', 'text' => "Can chi: Giờ {$cc['gio']} · Ngày {$cc['ngay']} · Tháng {$cc['thang']} · Năm {$cc['nam']}"],
    ];

    if ($mode === 'luchao') {
        $lines[] = ['type' => 'intro', 'text' => "Nguyệt Lệnh: {$cc['hanh_thang']} (Chi {$cc['nguyet_lenh']})"];
        $lines[] = ['type' => 'intro', 'text' => "Nhật Kiến: {$cc['hanh_ngay']} (Chi {$cc['nhat_kien']})"];
    }

    $lines[] = ['type' => 'divider',  'text' => ''];

    ob_start(); ?>
    <div class="ich-context-badge">Gieo Quẻ Kinh Dịch » <?= esc_html($question) ?></div>

    <div class="ich-chat-wrap">
        <h3 class="ich-section-title ich-collapsible-header" id="ich-detail-header">
            <span>☯ Kết quả lập quẻ</span>
        </h3>

        <div class="ast-chat-bubble">
            <div class="ast-chat-body" id="ast-chat-body" data-lines='<?= json_encode($lines, JSON_UNESCAPED_UNICODE) ?>'>
                <span class="ast-cursor">|</span>
            </div>
            <div id="ich-detail-content" class="ich-detail-content">
                <?php $has_number_logic_tab = ($mode === 'maihoa_number' && is_array($number_meta)); ?>
                <?php $has_object_logic_tab = ($mode === 'maihoa_object' && is_array($object_meta)); ?>
                <?php $has_time_logic_tab   = ($mode === 'maihoa_time' && is_array($time_meta)); ?>
                <?php $has_luchao_logic_tab = ($mode === 'luchao' && is_array($luchao_data)); ?>

                <?php if ($has_number_logic_tab || $has_object_logic_tab || $has_luchao_logic_tab || $has_time_logic_tab): ?>
                    <div class="ich-detail-tabs">
                        <button type="button" class="ich-detail-tab active" data-tab="result"><span>☯</span> Kết quả</button>
                        <button type="button" class="ich-detail-tab" data-tab="logic">📄Chi tiết</button>
                        <button type="button" class="ich-detail-tab" data-tab="lapque">☰ Lập Quẻ</button>
                    </div>
                <?php endif; ?>

                <div class="ich-detail-panel active" data-panel="result">
                    <?php
                    $hexagrams = ['Quẻ Chủ (Hiện Tại)' => $chu, 'Quẻ Hỗ (Tiềm Ẩn)' => $ho];
                    if ($bien) $hexagrams['Quẻ Biến (Tương Lai)'] = $bien;

                    foreach ($hexagrams as $role => $hex):
                        $el_color = $element_colors[$hex['element']] ?? '#d4af37';
                        ?>
                        <div class="ich-card-detail">
                            <div class="ich-cd-header" style="border-left-color: <?= esc_attr($el_color) ?>">
                                <span class="ich-cd-pos" style="color:<?= esc_attr($el_color) ?>"><?= esc_html($role) ?></span>
                                <span class="ich-cd-name"><?= esc_html($hex['name_vi']) ?> <small>(<?= esc_html($hex['name']) ?>)</small></span>

                                <?php if (strpos($mode, 'maihoa') !== 0): ?>
                                    <span class="ich-badge-element ich-el-<?= strtolower($hex['element']) ?>">
                                Ngũ Hành Cung: <?= esc_html($hex['element']) ?>
                            </span>
                                <?php endif; ?>

                            </div>
                            <div class="ich-cd-body">
                                <p><?= esc_html($hex['meaning']) ?></p>
                                <?php if (!empty($hex['keywords'])): ?>
                                    <p class="ich-cd-mindset">
                                        ✦ <strong>Tâm thế:</strong> <em><?= esc_html(implode(' · ', $hex['keywords'])) ?></em>
                                    </p>
                                <?php endif; ?>
                                <p class="ich-cd-judgment">
                                    <strong>Thoán từ:</strong> <?= esc_html($hex['judgment']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!$bien && $mode === 'luchao'): ?>
                        <div class="ich-card-detail">
                            <div class="ich-cd-header" style="border-left-color: #8b5cf6">
                                <span class="ich-cd-pos" style="color:#8b5cf6">Quẻ Biến (Tương Lai)</span>
                                <span class="ich-cd-name">Quẻ Tĩnh <small>(Không có hào động)</small></span>
                                <span class="ich-badge-element" style="background: #8b5cf6; color: #fff; border:none;">
                            Giữ nguyên Quẻ Chủ
                        </span>
                            </div>
                            <div class="ich-cd-body">
                                <p>Sự việc không có biến động lớn, mọi yếu tố đều được giữ nguyên như trạng thái của Quẻ Chủ. Trong trường hợp này, hãy tập trung vào Thoán từ và ý nghĩa của Quẻ Chủ để tìm hướng đi.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (strpos($mode, 'maihoa') === 0 && $changing_line > 0): ?>
                        <div class="ich-card-detail ich-dynamic-lines">
                            <div class="ich-cd-header">
                                <span class="ich-cd-pos">HÀO ĐỘNG</span>
                                <span class="ich-cd-name">Hào <?= $changing_line ?> - Hào Từ</span>
                            </div>
                            <div class="ich-cd-body">
                                <p><?= esc_html($chu['lines'][$changing_line] ?? '') ?></p>
                            </div>
                        </div>
                    <?php elseif ($mode === 'luchao' && !empty($changing_lines)): ?>
                        <div class="ich-card-detail ich-dynamic-lines">
                            <div class="ich-cd-header">
                                <span class="ich-cd-pos">HÀO ĐỘNG</span>
                                <span class="ich-cd-name">- Hào Từ</span>
                            </div>
                            <div class="ich-cd-body">
                                <ul class="ich-line-list">
                                    <?php foreach ($changing_lines as $ln): ?>
                                        <li><?= esc_html($chu['lines'][$ln]) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($has_number_logic_tab): ?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/maihoa/number.php'?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/maihoa/lapque.php'?>
                <?php elseif ($has_object_logic_tab): ?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/maihoa/object.php'?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/maihoa/lapque.php'?>
                <?php elseif ($has_time_logic_tab): ?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/maihoa/time.php'?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/maihoa/lapque.php'?>
                <?php elseif ($has_luchao_logic_tab): ?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/luchao/luchao.php'?>
                    <?php include_once ICHING_PLUGIN_DIR . 'template/tab/luchao/lapque.php'?>
                <?php endif; ?>
            </div>

            <?php if (get_option('iching_allow_ai', '0') === '1'): ?>
                <div id="ich-deep-analysis-form" class="ich-form-deep">
                    <?php if ($mode === 'luchao'): ?>
                        <p>💡 Để <strong>luận giải</strong> chi tiết hơn, hãy nhập họ và tên và giới tính của bạn bên dưới.</p>
                        <div class="ich-deep-form-row">
                            <input type="text" id="ich-name" class="ich-input ich-flex-2" placeholder="Họ và tên người hỏi...">
                            <select id="ich-gender" class="ich-input ich-gender-select">
                                <option value="" disabled selected>Giới tính</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <span class="ich-error" id="ich-err-info"></span>
                    <?php endif; ?>

                    <button class="ich-submit-btn" id="ich-btn-deep-analyze">
                        <span class="ich-btn-text">Luận giải quẻ này</span>
                        <span class="ich-btn-loading"><span class="ich-spinner"></span> Đang luận giải...</span>
                    </button>
                    <span class="ich-error ich-error-analyze" id="ich-err-analyze"></span>
                </div>

                <div id="ast-analysis-wrap" class="ich-analysis-result-wrap">
                    <div class="ich-analysis-header" style="display: none;">
                        <span class="ich-analysis-title">☯ Quẻ & diễn giải</span>
                    </div>
                    <div id="ast-final-result" class="ich-analysis-content">
                        <div class="ast-skeleton ast-sk-title"></div>
                        <div class="ast-skeleton ast-sk-line"></div>
                        <div class="ast-skeleton ast-sk-line ast-sk-short"></div>
                    </div>
                    <p class="ich-disclaimer" id="ich-disclaimer" style="display:none;">
                        ✦ Đây là kết quả tham khảo theo hệ thống Dịch học. Mọi hành động và hướng đi tiếp theo nằm ở sự lựa chọn sáng suốt cũng như nỗ lực của bản thân.
                    </p>
                </div>
            <?php endif; ?>

            <div class="ich-action-footer">
                <span class="ich-reload" onclick="window.location.reload()" style="display:none;">↺ Gieo và luận giải quẻ khác</span>
                <button type="button" id="ich-btn-comment" class="ich-btn-comment" style="display:none;">Thảo Luận</button>
            </div>

        </div>
    </div>

    <div id="ich-detail-container" class="ich-detail-wrapper"></div>
    <?php return ob_get_clean();
}