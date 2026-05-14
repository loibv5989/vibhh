<?php
if (!defined('ABSPATH')) exit;
?>
<div class="ich-detail-panel" data-panel="logic">
    <div class="ich-ritual-steps">
        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9776;</span>
                <span class="ich-ritual-label">Thiên Cơ Khởi Số</span>
                <span class="ich-ritual-sub">Con số mang cơ duyên</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-ritual-highlight"><?= esc_html($number_meta['sanitized'] ?? '') ?></div>
                <div class="ich-logic-grid ich-logic-grid-2" style="margin-top:12px">
                    <div class="ich-logic-item"><strong>Tổng chữ số</strong><span><?= esc_html((string) ($number_meta['length'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Điểm phân chia</strong><span><?= esc_html((string) ($number_meta['half'] ?? '')) ?></span></div>
                </div>
            </div>
        </div>

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9778;</span>
                <span class="ich-ritual-label">Phân Chia Âm Dương</span>
                <span class="ich-ritual-sub">Tách đôi để định thượng hạ quái</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Phần thứ nhất</strong><span><?= esc_html((string) ($number_meta['part1'] ?? '')) ?> <em class="ich-ritual-em">→ tổng <?= esc_html((string) ($number_meta['sum1'] ?? '')) ?></em></span></div>
                    <div class="ich-logic-item"><strong>Phần thứ hai</strong><span><?= esc_html((string) ($number_meta['part2'] ?? '')) ?> <em class="ich-ritual-em">→ tổng <?= esc_html((string) ($number_meta['sum2'] ?? '')) ?></em></span></div>
                </div>
            </div>
        </div>

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9775;</span>
                <span class="ich-ritual-label">Định Vị Cung Quẻ</span>
                <span class="ich-ritual-sub">Quy về bát quái &amp; xác định hào động</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid">
                    <div class="ich-logic-item"><strong>Thượng quái (trên)</strong><span><?= esc_html($number_meta['thuong_name'] ?? '') ?> <em class="ich-ritual-em">mod <?= esc_html((string) ($number_meta['thuong_mod'] ?? '')) ?></em></span></div>
                    <div class="ich-logic-item"><strong>Hạ quái (dưới)</strong><span><?= esc_html($number_meta['ha_name'] ?? '') ?> <em class="ich-ritual-em">mod <?= esc_html((string) ($number_meta['ha_mod'] ?? '')) ?></em></span></div>
                    <div class="ich-logic-item ich-logic-item-dynamic"><strong>Hào động</strong><span>Hào <?= esc_html((string) ($number_meta['changing_line'] ?? '')) ?></span></div>
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