<?php
if (!defined('ABSPATH')) exit;
?>
<div class="ich-detail-panel" data-panel="logic">
    <div class="ich-ritual-steps">

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9776;</span>
                <span class="ich-ritual-label">Cung Quẻ &amp; Cốt Lõi</span>
                <span class="ich-ritual-sub">Kim tiền Lục Hào — thông tin lập quẻ</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-logic-grid ich-logic-grid-2">
                    <div class="ich-logic-item"><strong>Cung</strong><span><?= esc_html($luchao_data['cung'] ?? '') ?></span></div>
                    <div class="ich-logic-item"><strong>Hành cung</strong><span><?= esc_html($luchao_data['hanh_cung'] ?? '') ?></span></div>
                    <div class="ich-logic-item"><strong>Hào Thế</strong><span>Hào <?= esc_html((string) ($luchao_data['the'] ?? '')) ?></span></div>
                    <div class="ich-logic-item"><strong>Hào Ứng</strong><span>Hào <?= esc_html((string) ($luchao_data['ung'] ?? '')) ?></span></div>
                </div>
            </div>
        </div>

        <?php if (!empty($tosses)): ?>
            <div class="ich-ritual-step">
                <div class="ich-ritual-step-head">
                    <span class="ich-ritual-gua">&#9778;</span>
                    <span class="ich-ritual-label">Lục Độ Kim Tiền</span>
                    <span class="ich-ritual-sub">Sáu lần gieo — tổng điểm từng hào</span>
                </div>
                <div class="ich-ritual-body">
                    <div class="ich-toss-score-row">
                        <?php foreach ($tosses as $idx => $toss): ?>
                            <?php $is_dynamic = in_array($idx + 1, $changing_lines ?? []); ?>
                            <div class="ich-toss-score-cell <?= $is_dynamic ? 'ich-toss-score-dynamic' : '' ?>">
                                <span class="ich-toss-score-hao">Hào <?= esc_html((string) ($idx + 1)) ?></span>
                                <span class="ich-toss-score-val"><?= esc_html((string) $toss) ?></span>
                                <?php if ($is_dynamic): ?><span class="ich-toss-score-mark">●</span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="ich-ritual-step">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9775;</span>
                <span class="ich-ritual-label">Lục Hào Quẻ Chủ</span>
                <span class="ich-ritual-sub">Lục thân · Can chi · Ngũ hành · Lục thú</span>
            </div>
            <div class="ich-ritual-body">
                <div class="ich-hao-table">
                    <?php
                    $chu_lines = $luchao_data['lines'] ?? [];
                    $sorted_keys = array_keys($chu_lines);
                    rsort($sorted_keys);
                    $mid = count($sorted_keys) > 3 ? 3 : -1;
                    foreach ($sorted_keys as $pos => $idx):
                        $line = $chu_lines[$idx];
                        $is_the = !empty($line['is_the']);
                        $is_ung = !empty($line['is_ung']);
                        $is_dyn = in_array($idx, $changing_lines ?? []);
                        $hanh = strtolower($line['hanh'] ?? '');
                        if ($pos === $mid): ?>
                            <div class="ich-hao-divider"><span>— Thượng Quái / Hạ Quái —</span></div>
                        <?php endif; ?>
                        <div class="ich-hao-row <?= $is_dyn ? 'ich-hao-row-dynamic' : '' ?>">
                            <span class="ich-hao-num">Hào <?= esc_html((string) $idx) ?></span>
                            <span class="ich-hao-than"><?= esc_html($line['luc_than'] ?? '') ?></span>
                            <span class="ich-hao-chi"><?= esc_html(($line['chi'] ?? '') . ' ' . ($line['hanh'] ?? '')) ?></span>
                            <span class="ich-hao-thu"><?= esc_html($line['luc_thu'] ?? '') ?></span>
                            <span class="ich-hao-flags">
                                                    <?php if ($is_the): ?><span class="ich-flag-the">Thế</span><?php endif; ?>
                                <?php if ($is_ung): ?><span class="ich-flag-ung">Ứng</span><?php endif; ?>
                                <?php if ($is_dyn): ?><span class="ich-flag-dyn">Động</span><?php endif; ?>
                                                </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (is_array($luchao_bien_data)): ?>
            <div class="ich-ritual-step">
                <div class="ich-ritual-step-head">
                    <span class="ich-ritual-gua">&#9774;</span>
                    <span class="ich-ritual-label">Lục Hào Quẻ Biến</span>
                    <span class="ich-ritual-sub">Sau khi các hào động chuyển hóa</span>
                </div>
                <div class="ich-ritual-body">
                    <div class="ich-hao-table ich-hao-table-bien">
                        <?php
                        $bien_lines = $luchao_bien_data['lines'] ?? [];
                        $sorted_bien = array_keys($bien_lines);
                        rsort($sorted_bien);
                        foreach ($sorted_bien as $pos => $idx):
                            $line = $bien_lines[$idx];
                            $is_the = !empty($line['is_the']);
                            $is_ung = !empty($line['is_ung']);
                            if ($pos === $mid): ?>
                                <div class="ich-hao-divider"><span>— Thượng Quái / Hạ Quái —</span></div>
                            <?php endif; ?>
                            <div class="ich-hao-row">
                                <span class="ich-hao-num">Hào <?= esc_html((string) $idx) ?></span>
                                <span class="ich-hao-than"><?= esc_html($line['luc_than'] ?? '') ?></span>
                                <span class="ich-hao-chi"><?= esc_html(($line['chi'] ?? '') . ' ' . ($line['hanh'] ?? '')) ?></span>
                                <span class="ich-hao-thu"><?= esc_html($line['luc_thu'] ?? '') ?></span>
                                <span class="ich-hao-flags">
                                                    <?php if ($is_the): ?><span class="ich-flag-the">Thế</span><?php endif; ?>
                                    <?php if ($is_ung): ?><span class="ich-flag-ung">Ứng</span><?php endif; ?>
                                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="ich-ritual-step ich-ritual-step-final">
            <div class="ich-ritual-step-head">
                <span class="ich-ritual-gua">&#9790;</span>
                <span class="ich-ritual-label">Hiển Lộ Thiên Mệnh</span>
                <span class="ich-ritual-sub">Các dữ liệu lõi để luận quẻ</span>
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
                    <div class="ich-ritual-result-item">
                        <span class="ich-ritual-result-role">Hào động</span>
                        <span class="ich-ritual-result-name"><?= !empty($changing_lines) ? esc_html('Hào ' . implode(', ', array_map('intval', $changing_lines))) : 'Không có' ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
