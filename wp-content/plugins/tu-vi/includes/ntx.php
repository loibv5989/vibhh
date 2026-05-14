<?php
if (!defined('ABSPATH')) exit;

class TuVi_NTX {

    private static function settings() {
        return TuVi_Settings::get_instance();
    }

    public static function xem_ngay(array $input = []): array
    {
        $date = self::normalizeDate($input['ngay'] ?? $input['date'] ?? null);
        if ($date === null) {
            return ['success' => false, 'message' => 'Ngay khong hop le.'];
        }

        $purpose = self::normalizePurpose($input['muc_dich'] ?? $input['purpose'] ?? 'cuoi');
        $tz = self::settings()->getTimezone();

        $context = self::buildDayContext($date, $tz);
        $birthInfo = self::buildBirthInfo($input);

        if (!empty($birthInfo['enabled'])) {
            $rules = self::loadRules();
            $context['gio_tot'] = self::filterGoodHours($context['gio_tot'], $birthInfo, $rules);
        }

        $evaluation = self::evaluateDay($context, $purpose, $birthInfo);

        return [
            'success'         => true,
            'purpose_key'     => $purpose['key'],
            'purpose_label'   => $purpose['label'],
            'battu'           => $birthInfo['enabled'] ? [
                'nhat_chu' => $birthInfo['day_can'] . ' ' . $birthInfo['day_chi'],
                'nam_sinh' => $birthInfo['year_can'] . ' ' . $birthInfo['year_chi']
            ] : null,
            'date'            => $context['solar']['ymd'],
            'lunar'           => $context['lunar'],
            'can_chi'         => $context['can_chi'],
            'truc'            => $context['truc'],
            'hoang_dao'       => $context['hoang_dao'],
            'sao_hoang_dao'   => $context['sao_hoang_dao'] ?? null,
            'banh_to'         => $context['banh_to'] ?? [],
            'huong_xuat_hanh' => $context['huong_xuat_hanh'] ?? [],
            'gio_tot'         => $context['gio_tot'],
            'gio_xau'         => $context['gio_xau'] ?? [],
            'nhi_thap_bat_tu' => $context['nhi_thap_bat_tu'] ?? null,
            'sao_ngay_tot'    => $context['sao_ngay_tot'] ?? [],
            'sao_ngay_xau'    => $context['sao_ngay_xau'] ?? [],
            'evaluation'      => $evaluation,
        ];
    }

    public static function tim_ngay_tot(array $input = []): array
    {
        $start = self::normalizeDate($input['tu_ngay'] ?? $input['start'] ?? null);
        $end   = self::normalizeDate($input['den_ngay'] ?? $input['end'] ?? null);

        if ($start === null || $end === null) {
            return ['success' => false, 'message' => 'Khoang ngay khong hop le.'];
        }

        if ($end < $start) {
            [$start, $end] = [$end, $start];
        }

        $purpose   = self::normalizePurpose($input['muc_dich'] ?? $input['purpose'] ?? 'cuoi');
        $tz        = self::settings()->getTimezone();
        $birthInfo = self::buildBirthInfo($input);
        $limit     = max(1, min(50, (int)($input['limit'] ?? 10)));

        $results  = [];
        $period   = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));
        $maxDays  = 365;
        $dayCount = 0;

        foreach ($period as $date) {
            if ($dayCount >= $maxDays) break;
            $dayCount++;

            $context = self::buildDayContext($date, $tz);

            if (!empty($birthInfo['enabled'])) {
                $rules = self::loadRules();
                $context['gio_tot'] = self::filterGoodHours($context['gio_tot'], $birthInfo, $rules);
            }

            $evaluation = self::evaluateDay($context, $purpose, $birthInfo);

            $results[] = [
                'date'          => $context['solar']['ymd'],
                'score'         => $evaluation['score'],
                'level'         => $evaluation['level'],
                'summary'       => $evaluation['summary'] ?? '',
                'reasons'       => $evaluation['reasons'],
                'warnings'      => $evaluation['warnings'],
                'can_chi'       => $context['can_chi'],
                'truc'          => $context['truc'],
                'hoang_dao'     => $context['hoang_dao'],
                'sao_hoang_dao' => $context['sao_hoang_dao'] ?? null,
                'gio_tot'       => $context['gio_tot'],
            ];
        }

        usort($results, static function ($a, $b) {
            if ($a['score'] === $b['score']) return strcmp($a['date'], $b['date']);
            return $b['score'] <=> $a['score'];
        });

        return [
            'success'       => true,
            'purpose_key'   => $purpose['key'],
            'purpose_label' => $purpose['label'],
            'battu'         => $birthInfo['enabled'] ? [
                'nhat_chu' => $birthInfo['day_can'] . ' ' . $birthInfo['day_chi'],
                'nam_sinh' => $birthInfo['year_can'] . ' ' . $birthInfo['year_chi']
            ] : null,
            'range'         => [
                'start' => $start->format('Y-m-d'),
                'end'   => $end->format('Y-m-d'),
            ],
            'count'         => count($results),
            'best'          => $results[0] ?? null,
            'results'       => array_slice($results, 0, $limit),
        ];
    }

    // =========================================================================
    // CORE EVALUATION
    // =========================================================================

    private static function evaluateDay(array $context, array $purpose, array $birthInfo): array
    {
        $rules      = self::loadRules();
        $purposeKey = $purpose['key'];

        $score    = 0;
        $reasons  = [];
        $warnings = [];
        $isFatal  = false;

        $dayChi   = $context['chi'];
        $dayStem  = $context['can'];

        // ------------------------------------------------------------------
        // 1. 12 SAO HOANG DAO / HAC DAO
        // ------------------------------------------------------------------
        $saoHD = $context['sao_hoang_dao'] ?? null;
        if (is_array($saoHD)) {
            if ($saoHD['type'] === 'Hoang Dao') {
                $score += (int)($rules['weights']['hoang_dao'] ?? 10);
                $reasons['hoang_dao'] = "Ngày Hoàng Đạo ({$saoHD['name']}): {$saoHD['desc']}";
            } else {
                $score += (int)($rules['weights']['hac_dao'] ?? -10);
                $warnings['hac_dao'] = "Ngày Hắc Đạo ({$saoHD['name']}): {$saoHD['desc']}";
            }
        }

        // ------------------------------------------------------------------
        // 2. TRUC NGAY
        // ------------------------------------------------------------------
        $trucScore = self::scoreTruc($context['truc'], $purposeKey);
        $score += $trucScore['score'];
        if ($trucScore['reason'] !== '') {
            if ($trucScore['score'] >= 0) {
                $reasons[] = $trucScore['reason'];
            } else {
                $warnings[] = $trucScore['reason'];
            }
        }

        // ------------------------------------------------------------------
        // 3. NGAY KY DAN GIAN
        // ------------------------------------------------------------------
        $dayKiKyPenalty = self::scoreDayKiKy($context['lunar']['day'], $rules);
        if ($dayKiKyPenalty !== 0) {
            $score += $dayKiKyPenalty;
            $warnings[] = 'Phạm ngày kiêng kỵ dân gian (Tam Nương, Nguyệt Kỵ, Dương Công).';
        }

        // ------------------------------------------------------------------
        // 4. NHI THAP BAT TU
        // ------------------------------------------------------------------
        $sao28 = $context['nhi_thap_bat_tu'] ?? null;
        if (is_array($sao28)) {
            if (($sao28['type'] ?? '') === 'Tot') {
                $score += (int)($rules['weights']['sao_28_tot'] ?? 10);
                $reasons[] = "Tọa tinh {$sao28['name']} (Cát tinh): {$sao28['desc']}";
            } elseif (($sao28['type'] ?? '') === 'Xau') {
                $score += (int)($rules['weights']['sao_28_xau'] ?? -10);
                $warnings[] = "Tọa tinh {$sao28['name']} (Hung tinh): {$sao28['desc']}";
            }
        }

        // ------------------------------------------------------------------
        // 5. CAT TINH / HUNG TINH NGAY (Dong Cong)
        // ------------------------------------------------------------------
        $sao_ngay_xau = $context['sao_ngay_xau'] ?? [];
        $sao_ngay_tot = $context['sao_ngay_tot'] ?? [];

        if (!empty($sao_ngay_xau)) {
            $score += (int)($rules['weights']['sao_ngay_xau'] ?? -25);
            $warnings[] = 'Phạm đại hung tinh chiếu chu kỳ: ' . implode(', ', $sao_ngay_xau) . '.';
            $isFatal = true;
        }
        if (!empty($sao_ngay_tot)) {
            $score += (int)($rules['weights']['sao_ngay_tot'] ?? 15);
            $reasons[] = 'Được quý tụ bởi các cát tinh: ' . implode(', ', $sao_ngay_tot) . '.';
        }

        // ------------------------------------------------------------------
        // 6. BAT TU (khi co nam sinh)
        // ------------------------------------------------------------------
        if (!empty($birthInfo['enabled'])) {
            $birthYearChi  = $birthInfo['year_chi'];
            $birthYearStem = $birthInfo['year_can'];
            $birthDayChi   = $birthInfo['day_chi'];
            $birthDayStem  = $birthInfo['day_can'];

            $birthYearStemKey = self::normalizeKey($birthYearStem);
            $birthDayStemKey  = self::normalizeKey($birthDayStem);
            $dayStemKey       = self::normalizeKey($dayStem);
            $dayChiKey        = self::normalizeKey($dayChi);
            $birthDayChiKey   = self::normalizeKey($birthDayChi);
            $birthYearChiKey  = self::normalizeKey($birthYearChi);

            // A. Ngu Hanh Nap Am (So sanh Nap Am Ngay va Nap Am Nam Sinh - Menh Tuoi)
            $dayCanChiKey       = $dayStemKey . '_' . $dayChiKey;
            $birthYearCanChiKey = $birthYearStemKey . '_' . $birthYearChiKey;

            $dayNguHanh   = $rules['nap_am_ngu_hanh'][$dayCanChiKey]   ?? null;
            $birthNguHanh = $rules['nap_am_ngu_hanh'][$birthYearCanChiKey] ?? null;

            if ($dayNguHanh && $birthNguHanh) {
                $relation = self::getNguHanhRelation($dayNguHanh, $birthNguHanh);
                $mapNH = ['kim'=>'Kim','thuy'=>'Thủy','moc'=>'Mộc','hoa'=>'Hỏa','tho'=>'Thổ'];
                $dNH = $mapNH[$dayNguHanh];
                $bNH = $mapNH[$birthNguHanh];

                if ($relation === 'sinh_nhap' || $relation === 'ty_hoa') {
                    $score += (int)($rules['weights']['ngu_hanh_sinh'] ?? 10);
                    $reasons[] = "Bản mệnh (Nạp Âm năm sinh - $bNH) được ngũ hành ngày ($dNH) tương sinh/tương hòa, vượng khí nạp tài.";
                } elseif ($relation === 'sinh_xuat') {
                    $score += (int)($rules['weights']['ngu_hanh_sinh_xuat'] ?? -5);
                    $warnings[] = "Bản mệnh (Nạp Âm năm sinh - $bNH) sinh xuất ngũ hành ngày ($dNH), đương số tiêu hao tinh lực.";
                } elseif ($relation === 'khac_nhap') {
                    $score += (int)($rules['weights']['ngu_hanh_khac'] ?? -15);
                    $warnings[] = "Ngũ hành ngày ($dNH) khắc phạt Bản mệnh (Nạp Âm năm sinh - $bNH), hao tổn sinh khí.";
                    $isFatal = true;
                } elseif ($relation === 'khac_xuat') {
                    $score += 5;
                    $reasons['khac_xuat'] = "Bản mệnh (Nạp Âm năm sinh - $bNH) khắc chế được ngũ hành ngày ($dNH) (Khắc Xuất), đương số nắm quyền chủ động.";
                }
            }

            // B. Thien Can phat (Tránh Double Penalty bằng elseif)
            $canKhac = $rules['can_khac'] ?? [];
            if (($canKhac[$dayStemKey] ?? '') === $birthDayStemKey) {
                $score += (int)($rules['weights']['can_pha_nhat_chu'] ?? -20);
                $warnings[] = "Thiên can ngày ($dayStem) phá NHẬT CHỦ ($birthDayStem), đại kỵ tiến hành đại sự.";
                $isFatal = true;
            } elseif (($canKhac[$dayStemKey] ?? '') === $birthYearStemKey) {
                $score += (int)($rules['weights']['can_pha_thai_tue'] ?? -10);
                $warnings[] = "Thiên can ngày ($dayStem) phá THÁI TUẾ ($birthYearStem), mưu sự dễ gặp cản trở.";
                $isFatal = true;
            }

            // C. Dia Chi — Luc Xung
            $isXungNhatChu = self::isXung($dayChi, $birthDayChi);
            $isXungThaiTue = self::isXung($dayChi, $birthYearChi);

            if ($isXungNhatChu) {
                $score += (int)($rules['weights']['xung_nhat_chu'] ?? -30);
                $warnings[] = "Ngày sinh ($birthDayChi) xung khắc trực diện với ngày ($dayChi) (Phạm Nhật Phá), rất hung hiểm.";
                $isFatal = true;
            }
            elseif ($isXungThaiTue) {
                $score += (int)($rules['weights']['xung_thai_tue'] ?? -20);
                $warnings[] = "Năm sinh ($birthYearChi) xung khắc với ngày ($dayChi) (Phạm Tuế Phá), biến động bất lợi.";
                $isFatal = true;
            }

            // D. Dia Chi — Tam Hinh
            $tamHinhMap = $rules['tam_hinh'] ?? [];
            $dayChiHinhList = $tamHinhMap[$dayChiKey] ?? [];

            if (!$isXungNhatChu && in_array($birthDayChiKey, $dayChiHinhList, true)) {
                if ($dayChiKey === $birthDayChiKey) {
                    $score -= 3;
                    $warnings[] = "Ngày ($dayChi) và Nhật Chi ($birthDayChi) phạm Tự Hình, nội tâm dễ vướng bận, cần đề phòng tiểu tiết.";
                } else {
                    $score += (int)($rules['weights']['tam_hinh_nhat_chu'] ?? -15);
                    $warnings[] = "Ngày ($dayChi) và Nhật Chi ($birthDayChi) phạm Tam Hình, hung sát phát động, khuyên trì hoãn.";
                    $isFatal = true;
                }
            }
            if (!$isXungThaiTue && in_array($birthYearChiKey, $dayChiHinhList, true)) {
                if ($dayChiKey === $birthYearChiKey) {
                    $score -= 3;
                    $warnings[] = "Ngày ($dayChi) và Thái Tuế ($birthYearChi) phạm Tự Hình, mưu sự dễ lặp lại trắc trở nhỏ.";
                } else {
                    $score += (int)($rules['weights']['tam_hinh_thai_tue'] ?? -10);
                    $warnings[] = "Ngày ($dayChi) và Thái Tuế ($birthYearChi) phạm Tam Hình, biến động bất ổn.";
                    $isFatal = true;
                }
            }

            // E. Dia Chi — Luc Pha
            $lucPhaMap = $rules['luc_pha'] ?? [];
            if (($lucPhaMap[$dayChiKey] ?? '') === $birthDayChiKey) {
                $score += (int)($rules['weights']['luc_pha_nhat_chu'] ?? -10);
                $warnings[] = "Ngày ($dayChi) phạm Lục Phá với Nhật Chi ($birthDayChi), công việc dễ bị gián đoạn.";
            }
            if (($lucPhaMap[$dayChiKey] ?? '') === $birthYearChiKey) {
                $score += (int)($rules['weights']['luc_pha_thai_tue'] ?? -8);
                $warnings[] = "Ngày ($dayChi) phạm Lục Phá với Thái Tuế ($birthYearChi), cần đề phòng tiêu hao.";
            }

            // F. Dia Chi — Tam Hop
            $tamHopMap = $rules['tam_hop'] ?? [];
            $dayHopList = $tamHopMap[$dayChiKey] ?? [];
            if (!empty($dayHopList) && in_array($birthDayChiKey, $dayHopList, true)) {
                $score += (int)($rules['weights']['tam_hop_nhat_chu'] ?? 12);
                $reasons[] = "Ngày ($dayChi) và Nhật Chi ($birthDayChi) cùng nhóm Tam Hợp, đại cát phát vượng.";
            }
            if (!empty($dayHopList) && in_array($birthYearChiKey, $dayHopList, true)) {
                $score += (int)($rules['weights']['tam_hop_thai_tue'] ?? 8);
                $reasons[] = "Ngày ($dayChi) và Thái Tuế ($birthYearChi) tương ứng Tam Hợp cục, vận thế hanh thông.";
            }

            // G. Dia Chi — Luc Hop
            if (self::isLucHop($dayChi, $birthDayChi)) {
                $score += (int)($rules['weights']['hop_nhat_chu'] ?? 15);
                $reasons[] = "Ngày sinh ($birthDayChi) tương hợp với ngày ($dayChi), tinh thần hanh thông.";
            }
            if (self::isLucHop($dayChi, $birthYearChi)) {
                $score += (int)($rules['weights']['hop_thai_tue'] ?? 10);
                $reasons[] = "Năm sinh ($birthYearChi) tương hợp với ngày ($dayChi), vận thế bình ổn.";
            }
        }

        // ------------------------------------------------------------------
        // 7. DIEU CHINH THEO MUC DICH CU THE
        // ------------------------------------------------------------------
        $finalAdjust = self::purposeSpecificAdjust($context, $purpose, $birthInfo);
        $score += $finalAdjust['score'];

        if (!empty($finalAdjust['is_fatal'])) {
            $isFatal = true;
        }

        $reasons  = array_merge($reasons, $finalAdjust['reasons']);
        $warnings = array_merge($warnings, $finalAdjust['warnings']);

        // ------------------------------------------------------------------
        // 8. HAU XU LY SCORE AM
        // ------------------------------------------------------------------
        if ($isFatal) {
            $score = min(-1, $score);
        }
        $score = max(-20, min(20, $score));

        // Neu score am: hien thi nguyen nhan do Hung sat ap dao, KHONG xoa $reasons
        if ($score < 0) {
            if (isset($reasons['hoang_dao'])) {
                $tenSao = $context['sao_hoang_dao']['name'] ?? '';
                $warnings['hac_note'] = "Lưu ý: Ngày Hoàng Đạo ($tenSao) vốn mang sinh khí tốt nhưng bị yếu tố Hung sát / Xung khắc lấn át hoàn toàn, nên cân nhắc lại.";
            }
            if (isset($reasons['khac_xuat'])) {
                $warnings[] = "Bản mệnh khắc chế Ngũ hành ngày, nhưng do ngày mang đại hung nên mưu sự vẫn vất vả, khó thành.";
            }
        }

        // Neu score >= 4 va co cat tinh: lam mem canh bao Hac Dao
        if ($score >= 4) {
            foreach ($warnings as $k => $v) {
                if (mb_strpos((string)$v, 'Hắc Đạo') !== false && !empty($sao_ngay_tot)) {
                    $warnings[$k] = "Ngày Hắc Đạo ({$context['sao_hoang_dao']['name']}): Vốn có hung khí, nhưng đã được cát tinh (" . implode(', ', $sao_ngay_tot) . ") chế ngự và hóa giải, biến hung thành cát.";
                }
            }
        }

        $reasons  = self::uniqueLines(array_values($reasons));
        $warnings = self::uniqueLines(array_values($warnings));
        $level    = self::scoreToLevel($score);

        return [
            'score'    => $score,
            'level'    => $level,
            'reasons'  => $reasons,
            'warnings' => $warnings,
        ];
    }

    private static function getTrucLabel(string $truc): string
    {
        $map = [
            'Kien' => 'Kiến', 'Tru'  => 'Trừ',  'Man'   => 'Mãn',   'Binh' => 'Bình',
            'Dinh' => 'Định', 'Chap' => 'Chấp', 'Pha'   => 'Phá',   'Nguy' => 'Nguy',
            'Thanh'=> 'Thành','Thu'  => 'Thu',  'Khai'  => 'Khai',  'Be'   => 'Bế',
        ];
        return $map[$truc] ?? $truc;
    }

    private static function purposeSpecificAdjust(array $context, array $purpose, array $birthInfo): array
    {
        $rules       = self::loadRules();
        $purposeRules = $rules['muc_dich_rules'][$purpose['key']] ?? [];
        $purposeKey  = $purpose['key'];

        $score    = 0;
        $reasons  = [];
        $warnings = [];
        $isFatal  = false;

        $trucHienTai = self::normalizeKey($context['truc'] ?? '');
        $trucLabel   = self::getTrucLabel($context['truc'] ?? '');

        // 1. Truc uu tien
        $preferTruc = $purposeRules['prefer_truc'] ?? [];
        if (!empty($preferTruc) && in_array($trucHienTai, $preferTruc, true)) {
            $score += 3;
            $reasons[] = "Trực {$trucLabel} thuộc nhóm hành khiển đặc biệt cát lợi cho việc {$purpose['label']}.";
        }

        // 2. Truc kieng ky
        $avoidTruc = $purposeRules['avoid_truc'] ?? [];
        if (!empty($avoidTruc) && in_array($trucHienTai, $avoidTruc, true)) {
            $score -= 3;
            $warnings[] = "Trực {$trucLabel} thuộc nhóm hành khiển bất lợi cho việc {$purpose['label']}, nên lưu ý.";
        }

        // 3. Uu tien Hoang Dao
        if (!empty($purposeRules['require_hoang_dao']) && empty($context['hoang_dao'])) {
            $score -= 2;
            $warnings[] = "Sự kiện {$purpose['label']} ưu tiên ngày Hoàng Đạo, ngày này là Hắc Đạo nên cân nhắc thêm.";
        }

        // 4. Ky dac thu theo muc dich
        $dayChiKey  = self::normalizeKey($context['chi'] ?? '');
        $lunarDay   = (int)($context['lunar']['day']   ?? 0);
        $lunarMonth = (int)($context['lunar']['month'] ?? 0);

        if ($purposeKey === 'cuoi') {
            if ($dayChiKey === 'hoi') {
                $score -= 5;
                $warnings[] = "Phạm Hợi Bất Giá Thú (ngày Hợi kỵ cưới hỏi theo Bành Tổ Bách Kỵ), đại kỵ dựng vợ gả chồng.";
                $isFatal = true;
            }
            if ($dayChiKey === 'ngo' && $lunarMonth === 6) {
                $score -= 3;
                $warnings[] = "Tháng 6 ngày Ngọ phạm Thiên Địa Trùng Tang, kỵ cưới hỏi.";
                $isFatal = true;
            }
        } elseif ($purposeKey === 'dong_tho') {
            if ($dayChiKey === 'thin' && $lunarMonth === 3) {
                $score -= 3;
                $warnings[] = "Tháng 3 ngày Thìn phạm Thiên Địa Trùng Sát, động thổ xây cất cần thận trọng.";
            }
        } elseif ($purposeKey === 'nhap_trach') {
            if (in_array($lunarMonth, [3, 6, 9, 12], true) && $lunarDay > 12 && $lunarDay <= 18) {
                $score -= 2;
                $warnings[] = "Giai đoạn Thổ Vương Dụng Sự, hạn chế động thổ và nhập trạch.";
            }
        } elseif ($purposeKey === 'xuat_hanh') {
            if ($dayChiKey === 'ti') {
                $score -= 4;
                $warnings[] = "Phạm Tỵ Bất Viễn Hành (ngày Tỵ kỵ đi xa theo Bành Tổ Bách Kỵ), bất lợi cho xuất hành.";
            }
        } elseif ($purposeKey === 'khai_truong') {
            if ($lunarDay === 1) {
                $score -= 1;
                $warnings[] = "Mùng 1 âm lịch: khí vận mới chưa ổn định, mở hàng nên chọn ngày từ mùng 2 trở đi.";
            }
        }

        // 5. Banh To Bach Ky
        $banhToData   = $rules['banh_to_bach_ky'] ?? [];
        $canKey       = self::normalizeKey($context['can'] ?? '');
        $canPurposeMap = $banhToData['can_purpose_map'] ?? [];
        $chiPurposeMap = $banhToData['chi_purpose_map'] ?? [];

        if (!empty($canPurposeMap[$canKey]) && in_array($purposeKey, $canPurposeMap[$canKey], true)) {
            $score -= 3;
            $banhToCan = $banhToData['can'][$canKey] ?? '';
            if ($banhToCan) {
                $warnings[] = "Bành Tổ Bách Kỵ: $banhToCan — ảnh hưởng tiêu cực đến {$purpose['label']}.";
            }
        }
        if (!empty($chiPurposeMap[$dayChiKey]) && in_array($purposeKey, $chiPurposeMap[$dayChiKey], true)) {
            if ($dayChiKey !== 'hoi' || $purposeKey !== 'cuoi') { // Hoi-cuoi da xu ly rieng tren
                $score -= 3;
                $banhToChi = $banhToData['chi'][$dayChiKey] ?? '';
                if ($banhToChi) {
                    $warnings[] = "Bành Tổ Bách Kỵ: $banhToChi — ảnh hưởng tiêu cực đến {$purpose['label']}.";
                }
            }
        }

        // 6. Quet tu khoa hung tinh (chan triet de Sao ky Su kien)
        $kyMap = [
            'cuoi'        => ['cuoi hoi', 'gia thu', 'cuoi'],
            'dong_tho'    => ['xay cat', 'dong tho', 'dao dat'],
            'khai_truong' => ['khai truong', 'giao dich'],
            'xuat_hanh'   => ['xuat hanh', 'di xa'],
            'nhap_trach'  => ['nhap trach', 've nha moi'],
            'ky_hop_dong' => ['giao dich', 'ky ket'],
        ];

        $keywords = $kyMap[$purposeKey] ?? [];
        if (!empty($keywords)) {
            // Su dung self::normalizeKey() de xoa toan bo dau tieng Viet, dam bao khop chinh xac voi array $kyMap
            $sao28 = $context['nhi_thap_bat_tu'] ?? null;
            if (is_array($sao28) && ($sao28['type'] ?? '') === 'Xau') {
                $desc28 = self::normalizeKey($sao28['desc']);
                foreach ($keywords as $kw) {
                    if (mb_strpos($desc28, $kw) !== false) { $isFatal = true; break; }
                }
            }

            $saoHD = $context['sao_hoang_dao'] ?? null;
            if (is_array($saoHD) && ($saoHD['type'] ?? '') === 'Hac Dao') {
                $descHD = self::normalizeKey($saoHD['desc']);
                foreach ($keywords as $kw) {
                    if (mb_strpos($descHD, $kw) !== false) { $isFatal = true; break; }
                }
            }
        }

        return [
            'score'    => $score,
            'reasons'  => $reasons,
            'warnings' => $warnings,
            'is_fatal' => $isFatal,
        ];
    }

    // =========================================================================
    // BUILD DAY CONTEXT
    // =========================================================================

    private static function buildDayContext(DateTimeImmutable $date, int $tz): array
    {
        $day   = (int)$date->format('j');
        $month = (int)$date->format('n');
        $year  = (int)$date->format('Y');

        $amlich     = self::getAmLich();
        $lunar      = $amlich->convertSolar2Lunar($day, $month, $year, $tz);
        $lunarDay   = (int)($lunar[0] ?? 1);
        $lunarMonth = (int)($lunar[1] ?? 1);
        $lunarYear  = (int)($lunar[2] ?? $year);

        $jdn     = self::getJdn($day, $month, $year);
        $canChi  = self::getCanChiByJdn($jdn);

        $monthChi = self::getLunarMonthChi($lunarMonth);
        $truc     = self::getTruc($monthChi, $canChi['chi']);

        $rules = self::loadRules();

        $monthChiKey = self::normalizeKey($monthChi);
        $chiKey      = self::normalizeKey($canChi['chi']);
        $canKey      = self::normalizeKey($canChi['can']);

        // 1. 12 SAO HOANG DAO / HAC DAO
        $thanhLongStart = self::normalizeKey($rules['khoi_thanh_long'][$monthChiKey] ?? 'ty');
        $chiList        = ['ty','suu','dan','mao','thin','ti','ngo','mui','than','dau','tuat','hoi'];
        $startIndex     = array_search($thanhLongStart, $chiList, true) ?: 0;
        $dayIndex       = array_search($chiKey, $chiList, true) ?: 0;
        $offset         = ($dayIndex - $startIndex + 12) % 12;

        $saoHoangDao = $rules['thap_nhi_truc_tinh'][$offset] ?? null;
        $hoangDao    = ($saoHoangDao && $saoHoangDao['type'] === 'Hoang Dao');

        // 2. BANH TO BACH KY
        $banhToCan = $rules['banh_to_bach_ky']['can'][$canKey] ?? '';
        $banhToChi = $rules['banh_to_bach_ky']['chi'][$chiKey] ?? '';
        $banhTo    = array_values(array_filter([$banhToCan, $banhToChi]));

        // 3. HUONG XUAT HANH
        $huongXuatHanh = $rules['huong_xuat_hanh'][$canKey] ?? [];

        // 4. LOC GIO TOT
        $gioSatChu  = self::normalizeKey($rules['gio_dai_hung']['sat_chu'][$chiKey] ?? '');
        $gioThoTu   = self::normalizeKey($rules['gio_dai_hung']['tho_tu'][$chiKey]  ?? '');
        $chiXungNgay = self::normalizeKey($rules['chi_xung'][$chiKey] ?? '');
        $gioDaiHung  = array_filter([$gioSatChu, $gioThoTu, $chiXungNgay]);

        $gioTotGoc = self::getGoodHours($canChi['chi'], $canChi['can']);
        $gioTot    = array_values(array_filter($gioTotGoc, static function ($g) use ($gioDaiHung) {
            return !in_array(self::normalizeKey($g), $gioDaiHung, true);
        }));
        $gioXau = self::getBadHours($canChi['chi'], $canChi['can']);

        // 5. NHI THAP BAT TU
        $sao28Index = ($jdn + 17) % 28;
        $sao28      = $rules['nhi_thap_bat_tu'][$sao28Index] ?? null;

        // 6. SAO NGAY (Dong Cong)
        $saoNgayTot = [];
        $saoNgayXau = [];
        $dongCong   = $rules['sao_ngay_dong_cong'] ?? [];

        if (($dongCong['tho_tu'][$lunarMonth]  ?? '') === $chiKey) $saoNgayXau[] = 'Thọ Tử';
        if (($dongCong['sat_chu'][$lunarMonth] ?? '') === $chiKey) $saoNgayXau[] = 'Sát Chủ';
        if (($dongCong['thien_hy'][$lunarMonth]?? '') === $chiKey) $saoNgayTot[] = 'Thiên Hỷ';

        $yearChiNamXem = self::normalizeKey(self::getYearChi($lunarYear));
        $thienmaByChi  = $dongCong['thien_ma_by_year_chi'] ?? [];
        if (!empty($thienmaByChi[$yearChiNamXem]) && $thienmaByChi[$yearChiNamXem] === $chiKey) {
            $saoNgayTot[] = 'Thiên Mã';
        }

        return [
            'solar'           => ['date'=>$date,'ymd'=>$date->format('Y-m-d'),'day'=>$day,'month'=>$month,'year'=>$year],
            'lunar'           => ['day'=>$lunarDay,'month'=>$lunarMonth,'year'=>$lunarYear,'leap'=>(bool)($lunar[3]??false)],
            'jdn'             => $jdn,
            'can'             => $canChi['can'],
            'chi'             => $canChi['chi'],
            'can_index'       => $canChi['can_index'],
            'chi_index'       => $canChi['chi_index'],
            'can_chi'         => $canChi['can'] . ' ' . $canChi['chi'],
            'truc'            => $truc,
            'hoang_dao'       => $hoangDao,
            'sao_hoang_dao'   => $saoHoangDao,
            'banh_to'         => $banhTo,
            'huong_xuat_hanh' => $huongXuatHanh,
            'gio_tot'         => $gioTot,
            'gio_xau'         => $gioXau,
            'nhi_thap_bat_tu' => $sao28,
            'sao_ngay_tot'    => $saoNgayTot,
            'sao_ngay_xau'    => $saoNgayXau,
        ];
    }

    private static function isXung(string $a, string $b): bool
    {
        $rules   = self::loadRules();
        $chiXung = $rules['chi_xung'] ?? [];
        $aKey    = self::normalizeKey($a);
        $bKey    = self::normalizeKey($b);
        return ($chiXung[$aKey] ?? '') === $bKey;
    }

    private static function isLucHop(string $a, string $b): bool
    {
        $rules  = self::loadRules();
        $lucHop = $rules['luc_hop'] ?? [];
        $aKey   = self::normalizeKey($a);
        $bKey   = self::normalizeKey($b);
        return ($lucHop[$aKey] ?? '') === $bKey;
    }

    private static function normalizeDate($value): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeInterface) {
            return (new DateTimeImmutable($value->format('Y-m-d'), $value->getTimezone()))->setTime(0,0,0);
        }
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') return null;
            $tz = new DateTimeZone(date_default_timezone_get());
            foreach (['Y-m-d','d-m-Y','d/m/Y','Y/m/d'] as $format) {
                $dt = DateTimeImmutable::createFromFormat($format, $value, $tz);
                if ($dt instanceof DateTimeImmutable) return $dt->setTime(0,0,0);
            }
            try { return (new DateTimeImmutable($value, $tz))->setTime(0,0,0); } catch (Throwable $e) { return null; }
        }
        return null;
    }

    private static function normalizePurpose($purpose): array
    {
        $raw = is_string($purpose) ? trim($purpose) : 'cuoi';
        $map = [
            'cuoi'        => ['cuoi','cuoi hoi'],
            'khai_truong' => ['khai truong','mo hang','mo cua'],
            'ky_hop_dong' => ['ky hop dong','hop dong','ky ket','ky'],
            'dong_tho'    => ['dong tho','xay nha','xay cat'],
            'nhap_trach'  => ['nhap trach','ve nha moi'],
            'xuat_hanh'   => ['xuat hanh','di xa'],
            'mua_xe'      => ['mua xe','tai san'],
        ];
        if (isset($map[$raw])) return self::purposeMeta($raw);
        $raw = str_replace('_',' ', self::normalizeKey($raw));
        foreach ($map as $key => $aliases) {
            foreach ($aliases as $alias) {
                if ($raw === $alias || mb_strpos($raw, $alias) !== false) return self::purposeMeta($key);
            }
        }
        return self::purposeMeta('cuoi');
    }

    private static function purposeMeta(string $key): array
    {
        $rules = self::loadRules();
        $item  = $rules['muc_dich_rules'][$key] ?? [];
        return [
            'key'               => $key,
            'label'             => $item['label'] ?? $key,
            'prefer_truc'       => $item['prefer_truc'] ?? [],
            'avoid_truc'        => $item['avoid_truc']  ?? [],
            'require_hoang_dao' => (bool)($item['require_hoang_dao'] ?? false),
            'score_adjust'      => $item['score_adjust'] ?? [],
        ];
    }

    private static function normalizeKey(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value, 'UTF-8');

        // Bắt chính xác Tý/Tí và Tỵ/Tị trước khi bị xóa dấu để tránh nhầm lẫn con giáp
        $value = str_replace(['tý', 'tí'], 'ty', $value);
        $value = str_replace(['tỵ', 'tị'], 'ti', $value);

        $map   = [
            'a'=>'a','á'=>'a','à'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ằ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
            'e'=>'e','é'=>'e','è'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
            'i'=>'i','í'=>'i','ì'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
            'o'=>'o','ó'=>'o','ò'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ớ'=>'o','ờ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
            'u'=>'u','ú'=>'u','ù'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
            'y'=>'y','ý'=>'y','ỳ'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
            'd'=>'d','đ'=>'d',
        ];
        $value = strtr($value, $map);
        $value = preg_replace('/\s+/',' ', $value);
        return trim($value);
    }

    private static function uniqueLines(array $lines): array
    {
        $out = [];
        foreach ($lines as $line) {
            $line = trim((string)$line);
            if ($line === '') continue;
            $out[$line] = true;
        }
        return array_keys($out);
    }

    private static function loadRules(): array
    {
        return TuVi_Data::load('all');
    }

    private static function buildBirthInfo(array $input): array
    {
        if (empty($input['ngay_sinh'])) return ['enabled' => false];
        $dt = self::normalizeDate($input['ngay_sinh']);
        if ($dt === null) return ['enabled' => false];

        $originalDay   = (int)$dt->format('j');
        $originalMonth = (int)$dt->format('n');
        $originalYear  = (int)$dt->format('Y');
        $tz            = self::settings()->getTimezone();

        $amlich    = self::getAmLich();
        $lunar     = $amlich->convertSolar2Lunar($originalDay, $originalMonth, $originalYear, $tz);
        $lunarYear = (int)($lunar[2] ?? $originalYear);
        $yearChi   = self::getYearChi($lunarYear);
        $yearStem  = self::getYearStem($lunarYear);

        $gio_sinh  = $input['gio_sinh'] ?? '12:00';
        $timeParts = explode(':', $gio_sinh);
        $hour      = (int)($timeParts[0] ?? 12);

        if ($hour >= 23) {
            $dt = $dt->modify('+1 day');
            $originalDay   = (int)$dt->format('j');
            $originalMonth = (int)$dt->format('n');
            $originalYear  = (int)$dt->format('Y');
        }

        $jdn       = self::getJdn($originalDay, $originalMonth, $originalYear);
        $dayCanChi = self::getCanChiByJdn($jdn);

        return [
            'enabled'  => true,
            'year_can' => $yearStem,
            'year_chi' => $yearChi,
            'day_can'  => $dayCanChi['can'],
            'day_chi'  => $dayCanChi['chi'],
            'gender'   => $input['gioi_tinh'] ?? 'nam',
        ];
    }

    private static function getAmLich(): Tuvi_AmLich
    {
        static $instance = null;
        if ($instance instanceof Tuvi_AmLich) return $instance;
        if (!class_exists('Tuvi_AmLich')) {
            $baseDir = defined('TUVI_PLUGIN_DIR') ? rtrim(TUVI_PLUGIN_DIR,'/\\') : dirname(__DIR__);
            $path    = $baseDir . '/data/amlich.php';
            if (is_file($path)) require_once $path;
        }
        $instance = new Tuvi_AmLich();
        return $instance;
    }

    private static function getJdn(int $day, int $month, int $year): int
    {
        return (int)self::getAmLich()->jdFromDate($day, $month, $year);
    }

    private static function getCanChiByJdn(int $jdn): array
    {
        $canIndex = ($jdn + 9)  % 10;
        $chiIndex = ($jdn + 1)  % 12;
        $canList  = ['Giáp','Ất','Bính','Đinh','Mậu','Kỷ','Canh','Tân','Nhâm','Quý'];
        $chiList  = ['Tý','Sửu','Dần','Mão','Thìn','Tỵ','Ngọ','Mùi','Thân','Dậu','Tuất','Hợi'];
        return [
            'can_index' => $canIndex,
            'chi_index' => $chiIndex,
            'can'       => $canList[$canIndex] ?? 'Giáp',
            'chi'       => $chiList[$chiIndex] ?? 'Tý',
        ];
    }

    private static function getLunarMonthChi(int $lunarMonth): string
    {
        $chiList = ['Tý','Sửu','Dần','Mão','Thìn','Tỵ','Ngọ','Mùi','Thân','Dậu','Tuất','Hợi'];
        $idx     = ($lunarMonth + 1) % 12;
        return $chiList[$idx] ?? 'Dần';
    }

    private static function getYearChi(int $year): string
    {
        $chiList = ['Tý','Sửu','Dần','Mão','Thìn','Tỵ','Ngọ','Mùi','Thân','Dậu','Tuất','Hợi'];
        $idx     = ($year + 8) % 12;
        return $chiList[$idx] ?? 'Tý';
    }

    private static function getYearStem(int $year): string
    {
        $canList = ['Giáp','Ất','Bính','Đinh','Mậu','Kỷ','Canh','Tân','Nhâm','Quý'];
        $idx     = ($year + 6) % 10;
        return $canList[$idx] ?? 'Giáp';
    }

    private static function getTruc(string $monthChi, string $dayChi): string
    {
        $chiList   = ['Tý','Sửu','Dần','Mão','Thìn','Tỵ','Ngọ','Mùi','Thân','Dậu','Tuất','Hợi'];
        $trucOrder = ['Kien','Tru','Man','Binh','Dinh','Chap','Pha','Nguy','Thanh','Thu','Khai','Be'];

        $mIdx = array_search($monthChi, $chiList, true);
        $dIdx = array_search($dayChi,   $chiList, true);

        if ($mIdx === false || $dIdx === false) return 'Kien';

        $offset = ($dIdx - $mIdx + 12) % 12;
        return $trucOrder[$offset];
    }

    private static function filterGoodHours(array $gio_hoang_dao, array $birthInfo, array $rules): array
    {
        if (empty($birthInfo['enabled'])) return $gio_hoang_dao;

        $tuoiChiKey  = self::normalizeKey($birthInfo['year_chi']);
        $ngayChiKey  = self::normalizeKey($birthInfo['day_chi']);

        $chiXungTuoi = self::normalizeKey($rules['chi_xung'][$tuoiChiKey] ?? '');
        $chiHaiTuoi  = self::normalizeKey($rules['chi_hai'][$tuoiChiKey]  ?? '');
        $chiXungNgay = self::normalizeKey($rules['chi_xung'][$ngayChiKey] ?? '');

        $gio_loc = [];
        foreach ($gio_hoang_dao as $gio) {
            $gioKey = self::normalizeKey($gio);
            if ($chiXungTuoi !== '' && $gioKey === $chiXungTuoi) continue;
            if ($chiHaiTuoi  !== '' && $gioKey === $chiHaiTuoi)  continue;
            if ($chiXungNgay !== '' && $gioKey === $chiXungNgay) continue;
            $gio_loc[] = $gio;
        }
        return $gio_loc;
    }

    private static function getNguHanhRelation(string $dayNH, string $birthNH): string
    {
        if ($dayNH === $birthNH) return 'ty_hoa';
        $sinh = ['kim'=>'thuy','thuy'=>'moc','moc'=>'hoa','hoa'=>'tho','tho'=>'kim'];
        $khac = ['kim'=>'moc', 'moc'=>'tho', 'tho'=>'thuy','thuy'=>'hoa','hoa'=>'kim'];

        if (($sinh[$dayNH]   ?? '') === $birthNH) return 'sinh_nhap';
        if (($sinh[$birthNH] ?? '') === $dayNH)   return 'sinh_xuat';
        if (($khac[$dayNH]   ?? '') === $birthNH) return 'khac_nhap';
        if (($khac[$birthNH] ?? '') === $dayNH)   return 'khac_xuat';
        return 'binh_thuong';
    }

    private static function scoreTruc(string $truc, string $purposeKey): array
    {
        $rules        = self::loadRules();
        $item         = $rules['truc_rules'][$truc] ?? null;
        if (!is_array($item)) return ['score' => 0, 'reason' => ''];

        $purposeRules = $rules['muc_dich_rules'][$purposeKey] ?? [];
        $trucKey      = self::normalizeKey($truc);
        $preferTruc   = $purposeRules['prefer_truc'] ?? [];
        $avoidTruc    = $purposeRules['avoid_truc']  ?? [];

        if (in_array($trucKey, $preferTruc, true) || in_array($trucKey, $avoidTruc, true)) {
            return ['score' => 0, 'reason' => ''];
        }

        $score = 0; $reason = '';
        $purposeScore = (int)($item['score_by_purpose'][$purposeKey] ?? 0);
        $trucLabel = self::getTrucLabel($truc);

        if ($purposeScore > 0) {
            $score   = $purposeScore;
            $reason  = "Trực $trucLabel tương ứng với mục đích hiện tại, mang lại cát lợi.";
        } elseif ($purposeScore < 0) {
            $score   = $purposeScore;
            $reason  = "Trực $trucLabel không thực sự phù hợp cho mục đích này.";
        }

        return ['score' => $score, 'reason' => $reason];
    }

    private static function scoreDayKiKy(int $lunarDay, array $rules): int
    {
        $day        = str_pad((string)$lunarDay, 2, '0', STR_PAD_LEFT);
        $ngayKiemKy = $rules['ngay_kiem_ky'] ?? [];
        if (in_array($day, $ngayKiemKy['tam_nuong']  ?? [], true)) return -2;
        if (in_array($day, $ngayKiemKy['nguyet_ky']  ?? [], true)) return -2;
        if (in_array($day, $ngayKiemKy['duong_cong'] ?? [], true)) return -1;
        return 0;
    }

    private static function scoreToLevel(int $score): string
    {
        if ($score >= 12) return 'Rat tot';
        if ($score >= 6)  return 'Tot';
        if ($score >= 0)  return 'Trung binh';
        return 'Khong nen';
    }

    private static function getGoodHours(string $dayChi, string $dayStem): array
    {
        $rules      = self::loadRules();
        $dayChiKey  = self::normalizeKey($dayChi);
        $dataGio    = $rules['gio_hoang_dao_theo_ngay_chi'][$dayChiKey] ?? null;
        $chiDisplay = [
            'ty'=>'Tý','suu'=>'Sửu','dan'=>'Dần','mao'=>'Mão',
            'thin'=>'Thìn','ti'=>'Tỵ','ngo'=>'Ngọ','mui'=>'Mùi',
            'than'=>'Thân','dau'=>'Dậu','tuat'=>'Tuất','hoi'=>'Hợi',
        ];
        if (is_array($dataGio) && !empty($dataGio)) {
            $good = array_values(array_filter(array_map(
                static fn($k) => $chiDisplay[$k] ?? null, $dataGio
            )));
            return $good ?: self::defaultGoodHoursByChi($dayChi);
        }
        return self::defaultGoodHoursByChi($dayChi);
    }

    private static function getBadHours(string $dayChi, string $dayStem): array
    {
        $all  = ['Tý','Sửu','Dần','Mão','Thìn','Tỵ','Ngọ','Mùi','Thân','Dậu','Tuất','Hợi'];
        $good = self::getGoodHours($dayChi, $dayStem);
        return array_values(array_diff($all, $good));
    }

    private static function defaultGoodHoursByChi(string $dayChi): array
    {
        $fallback = [
            'Tý'  => ['Tý','Sửu','Mão','Ngọ','Thân','Dậu'],
            'Ngọ' => ['Tý','Sửu','Mão','Ngọ','Thân','Dậu'],
            'Sửu' => ['Dần','Mão','Tỵ','Thân','Tuất','Hợi'],
            'Mùi' => ['Dần','Mão','Tỵ','Thân','Tuất','Hợi'],
            'Dần' => ['Tý','Sửu','Thìn','Tỵ','Mùi','Tuất'],
            'Thân'=> ['Tý','Sửu','Thìn','Tỵ','Mùi','Tuất'],
            'Mão' => ['Dần','Mão','Ngọ','Mùi','Dậu','Tý'],
            'Dậu' => ['Dần','Mão','Ngọ','Mùi','Dậu','Tý'],
            'Thìn'=> ['Dần','Thìn','Tỵ','Thân','Dậu','Hợi'],
            'Tuất'=> ['Dần','Thìn','Tỵ','Thân','Dậu','Hợi'],
            'Tỵ'  => ['Sửu','Thìn','Ngọ','Mùi','Tuất','Hợi'],
            'Hợi' => ['Sửu','Thìn','Ngọ','Mùi','Tuất','Hợi'],
        ];
        return $fallback[$dayChi] ?? ['Tý','Sửu','Dần','Mão','Thìn','Tỵ'];
    }
}