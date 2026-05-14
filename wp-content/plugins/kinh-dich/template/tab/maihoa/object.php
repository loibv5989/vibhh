<?php
if (!defined('ABSPATH')) exit;
?>
<div class="ich-detail-panel" data-panel="logic">
    <div class="ich-ritual-steps">
        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9776;</span>
                <span class="ich-ritual-label">Thiên Cơ Ngoại Tượng</span>
                <span class="ich-ritual-sub">Hai dấu hiệu mang cơ duyên</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Tượng thứ nhất (→ Thượng Quái)</strong><span><?= esc_html((string) ($object_meta['obj1'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Tượng thứ hai (→ Hạ Quái)</strong><span><?= esc_html((string) ($object_meta['obj2'] ?? '')) ?></span></div>
                </div>
            </div>
        </div>

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9775;</span>
                <span class="ich-ritual-label">Định Vị Cung Quẻ</span>
                <span class="ich-ritual-sub">Quy về thượng hạ quái trong bát quái</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Thượng quái (trên)</strong><span><?= esc_html($object_meta['thuong_name'] ?? '') ?> <em class="ich-ritual-em">mod <?= esc_html((string) ($object_meta['thuong_mod'] ?? '')) ?></em></span></div>
                    <div class="ich-logic-item"><strong>Hạ quái (dưới)</strong><span><?= esc_html($object_meta['ha_name'] ?? '') ?> <em class="ich-ritual-em">mod <?= esc_html((string) ($object_meta['ha_mod'] ?? '')) ?></em></span></div>
                </div>
            </div>
        </div>

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9778;</span>
                <span class="ich-ritual-label">Điểm Biến Chuyển</span>
                <span class="ich-ritual-sub">Xác định hào động của quẻ</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Tổng hai tượng</strong><span><?= esc_html((string) ($object_meta['n_total'] ?? '')) ?></span></div>
                    <div class="ich-logic-item ich-logic-item-dynamic"><strong>Hào động</strong><span>Hào <?= esc_html((string) ($object_meta['changing_line'] ?? '')) ?></span></div>
                </div>
            </div>
        </div>

        <div class="ich-ritual-step ich-ritual-step-final">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9790;</span>
                <span class="ich-ritual-label">Hiển Lộ Thiên Mệnh</span>
                <span class="ich-ritual-sub">Ba lớp quẻ được hình thành</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-ritual-result-row">
                    <div class="ich-ritual-result-item">
                        <span class="ich-ritual-result-role">Quẻ Chủ</span>
                        <span class="ich-ritual-result-name"><?= esc_html($chu['name_vi'] ?? '') ?></span>
                    </div>
                    <div class="ich-ritual-result-item">
                        <span class="ich-ritual-result-role">Quẻ Hỗ</span>
                        <span class="ich-ritual-result-name"><?= esc_html($ho['name_vi'] ?? '') ?></span>
                    </div>
                    <div class="ich-ritual-result-item">
                        <span class="ich-ritual-result-role">Quẻ Biến</span>
                        <span class="ich-ritual-result-name"><?= esc_html($bien['name_vi'] ?? 'Quẻ Tĩnh') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>