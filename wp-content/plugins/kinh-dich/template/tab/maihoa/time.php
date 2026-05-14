<?php
if (!defined('ABSPATH')) exit;
?>
<div class="ich-detail-panel" data-panel="logic">
    <div class="ich-ritual-steps">
        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9784;</span>
                <span class="ich-ritual-label">Thiên Thời Động Tâm</span>
                <span class="ich-ritual-sub">Can chi thời điểm khởi quẻ</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Năm âm lịch (chi)</strong><span><?= esc_html((string) ($time_meta['nam_chi_name'] ?? $time_meta['nam_chi'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Tháng âm lịch</strong><span><?= esc_html((string) ($time_meta['thang_am'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Ngày âm lịch</strong><span><?= esc_html((string) ($time_meta['ngay_am'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Giờ (chi)</strong><span><?= esc_html((string) ($time_meta['gio_chi_name'] ?? $time_meta['gio_chi'] ?? '')) ?></span></div>
                </div>
            </div>
        </div>

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9778;</span>
                <span class="ich-ritual-label">Tổng Hợp Âm Dương</span>
                <span class="ich-ritual-sub">Cộng can chi để định thượng hạ quái</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Thượng quái (năm+tháng+ngày)</strong><span><?= esc_html((string) ($time_meta['n1'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Hạ quái (năm+tháng+ngày+giờ)</strong><span><?= esc_html((string) ($time_meta['n2'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Hào động (tổng mod 6)</strong><span><?= esc_html((string) ($time_meta['n_total'] ?? '')) ?> → Hào <?= esc_html((string) ($time_meta['hao_mod'] ?? '')) ?></span></div>
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
                    <div class="ich-logic-item"><strong>Thượng quái (trên)</strong><span><?= esc_html($time_meta['thuong_name'] ?? '') ?> <em class="ich-ritual-em">mod <?= esc_html((string) ($time_meta['thuong_mod'] ?? '')) ?></em></span></div>
                    <div class="ich-logic-item"><strong>Hạ quái (dưới)</strong><span><?= esc_html($time_meta['ha_name'] ?? '') ?> <em class="ich-ritual-em">mod <?= esc_html((string) ($time_meta['ha_mod'] ?? '')) ?></em></span></div>
                    <div class="ich-logic-item ich-logic-item-dynamic"><strong>Hào động</strong><span>Hào <?= esc_html((string) ($time_meta['changing_line'] ?? '')) ?></span></div>
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
