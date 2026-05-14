<?php

if (!defined('ABSPATH')) exit;

class TuVi_Engine {

    public static function lap_la_so($input, $timeZone = 7) {
        $data = TuVi_Data::load('all');
        $am_lich = new Tuvi_AmLich();

        if (empty($input['ngay_sinh']) || empty($input['gio_sinh'])) return ['error' => 'Vui lòng nhập đầy đủ ngày và giờ sinh.'];

        $date_parts = explode('-', $input['ngay_sinh']);
        if (count($date_parts) !== 3) return ['error' => 'Ngày sinh không hợp lệ.'];

        $nam = (int)$date_parts[0]; $thang = (int)$date_parts[1]; $ngay = (int)$date_parts[2];
        $current_year = (int)date('Y');
        $ho_ten = (!empty($input['ho_ten']) && trim($input['ho_ten']) !== '') ? trim($input['ho_ten']) : 'Đương Số';

        if (!checkdate($thang, $ngay, $nam)) {
            return ['error' => 'Ngày sinh không tồn tại, vui lòng kiểm tra lại!'];
        }

        $egg_message = '';
        if ($nam < 1900) {
            $egg_message = "Năm quá khứ: {$nam}";
        } elseif ($nam > $current_year) {
            $egg_message = "Năm tương lai: {$nam}";
        }

        $gio_str = $input['gio_sinh'];
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $gio_str)) {
            return ['error' => 'Giờ sinh không đúng định dạng HH:mm (VD: 14:30)'];
        }
        $gioi_tinh = $input['gioi_tinh'] ?? 'nam';
        $nam_xem = !empty($input['nam_xem']) ? (int)$input['nam_xem'] : (int)date('Y');

        $normalized_input = self::step_1_normalize_input($data, $ngay, $thang, $nam, $gio_str, $gioi_tinh, $ho_ten);
        $calendar_data = self::step_2_convert_calendar($am_lich, $normalized_input['ngay'], $normalized_input['thang'], $normalized_input['nam'], $normalized_input['gio'], $timeZone);

        $can_nam_xem_idx = ($nam_xem - 3) % 10; if ($can_nam_xem_idx <= 0) $can_nam_xem_idx += 10;
        $chi_nam_xem_idx = ($nam_xem - 3) % 12; if ($chi_nam_xem_idx <= 0) $chi_nam_xem_idx += 12;
        $calendar_data['nam_xem'] = $nam_xem;
        $calendar_data['can_nam_xem_idx'] = $can_nam_xem_idx;
        $calendar_data['chi_nam_xem_idx'] = $chi_nam_xem_idx;

        $cung_data = self::step_3_an_cung($data, $calendar_data, $gioi_tinh);
        $laso = self::step_4_an_sao($data, $calendar_data, $cung_data, $gioi_tinh);

        return self::step_5_format_output($data, $normalized_input, $calendar_data, $cung_data, $laso, $egg_message, $gio_str);
    }

    private static function step_1_normalize_input($data, $ngay, $thang, $nam, $gio_str, $gioi_tinh, $ho_ten) {
        $ngay_goc = (int)$ngay;
        $thang_goc = (int)$thang;
        $nam_goc = (int)$nam;

        $gio_index = 1;
        if (!empty($gio_str)) {
            $time_val = (int)str_replace(':', '', $gio_str);
            if ($time_val >= 2300) {
                $gio_index = 1; // Giờ Tý
                // Đẻ sau 23h đêm -> Tính sang ngày hôm sau
                $date_string = "$nam-$thang-$ngay +1 day";
                $new_date = strtotime($date_string);
                $ngay = (int)date('d', $new_date);
                $thang = (int)date('m', $new_date);
                $nam = (int)date('Y', $new_date);
            } elseif ($time_val < 100) {
                $gio_index = 1; // Giờ Tý (Đầu giờ sáng, không cộng ngày)
            } else {
                $idx = 1;
                if (isset($data['gio_12'])) {
                    foreach ($data['gio_12'] as $key => $val) {
                        if ($idx > 1) {
                            $range = explode('-', $val['range']);
                            if (count($range) == 2) {
                                $start = (int)str_replace(':', '', trim($range[0]));
                                $end = (int)str_replace(':', '', trim($range[1]));
                                if ($time_val >= $start && $time_val < $end) {
                                    $gio_index = $idx; break;
                                }
                            }
                        }
                        $idx++;
                    }
                }
            }
        }

        return [
            'ngay' => (int)$ngay,
            'thang' => (int)$thang,
            'nam' => (int)$nam,
            'ngay_goc' => $ngay_goc,
            'thang_goc' => $thang_goc,
            'nam_goc' => $nam_goc,
            'gio' => $gio_index,
            'gio_str' => $gio_str,
            'gioi_tinh' => $gioi_tinh,
            'ho_ten' => $ho_ten
        ];
    }

    private static function step_2_convert_calendar($am_lich, $ngay, $thang, $nam, $gio_index, $timeZone) {
        $al = $am_lich->convertSolar2Lunar($ngay, $thang, $nam, $timeZone);
        $can_nam_idx = ($al[2] - 3) % 10; if ($can_nam_idx <= 0) $can_nam_idx += 10;
        $chi_nam_idx = ($al[2] - 3) % 12; if ($chi_nam_idx <= 0) $chi_nam_idx += 12;
        return [
            'am_lich' => ['ngay' => $al[0], 'thang' => $al[1], 'nam' => $al[2], 'nhuan' => $al[3]],
            'can_nam_idx' => $can_nam_idx, 'chi_nam_idx' => $chi_nam_idx, 'gio_idx' => $gio_index
        ];
    }

    private static function step_3_an_cung($data, $calendar_data, $gioi_tinh) {
        $thang = $calendar_data['am_lich']['thang']; $gio = $calendar_data['gio_idx']; $can_nam_idx = $calendar_data['can_nam_idx'];
        $rules = $data['an_sao_rules']; $khoi_diem = $rules['an_menh_than']['khoi_diem'];

        $menh_idx = self::wrap_12($khoi_diem + ($thang - 1) * $rules['an_menh_than']['menh']['thang'] + ($gio - 1) * $rules['an_menh_than']['menh']['gio']);
        $than_idx = self::wrap_12($khoi_diem + ($thang - 1) * $rules['an_menh_than']['than']['thang'] + ($gio - 1) * $rules['an_menh_than']['than']['gio']);

        $can_id_nam = $data['thien_can'][$can_nam_idx]['id'];
        $can_dan_id = $rules['ngu_ho_don'][$can_id_nam];
        $can_dan_idx = 1; foreach ($data['thien_can'] as $idx => $can) { if ($can['id'] === $can_dan_id) { $can_dan_idx = $idx; break; } }

        $can_menh_idx = self::wrap_10($can_dan_idx + ($menh_idx - 3));
        $can_val = $rules['tinh_cuc_ngu_hanh']['can_index'][$data['thien_can'][$can_menh_idx]['id']];
        $chi_val = $rules['tinh_cuc_ngu_hanh']['chi_index'][$data['dia_chi'][$menh_idx]['id']];
        $cuc_val = $rules['tinh_cuc_ngu_hanh']['cuc_mapping'][self::wrap_5($can_val + $chi_val)];

        $can_12_cung = [];
        for ($i = 0; $i < 12; $i++) {
            $chi_idx = self::wrap_12(3 + $i);
            $can_12_cung[$chi_idx] = $data['thien_can'][self::wrap_10($can_dan_idx + $i)];
        }

        return ['menh_idx' => $menh_idx, 'than_idx' => $than_idx, 'cuc' => $cuc_val, 'can_cung' => $can_12_cung];
    }

    private static function step_4_an_sao($data, $calendar_data, $cung_data, $gioi_tinh) {
        $ngay = $calendar_data['am_lich']['ngay']; $thang = $calendar_data['am_lich']['thang'];
        $gio = $calendar_data['gio_idx']; $can_nam_idx = $calendar_data['can_nam_idx'];
        $chi_nam_idx = $calendar_data['chi_nam_idx']; $cuc = $cung_data['cuc'];
        $chi_nam_xem_idx = $calendar_data['chi_nam_xem_idx'];

        $can_nam_id = $data['thien_can'][$can_nam_idx]['id'];
        $chi_nam_id = $data['dia_chi'][$chi_nam_idx]['id'];
        $step = (($data['thien_can'][$can_nam_idx]['polarity'] === '+') === ($gioi_tinh === 'nam')) ? 1 : -1;

        $laso = [];
        for ($i = 1; $i <= 12; $i++) {
            $laso[$i] = ['dia_chi' => $data['dia_chi'][$i]['name'], 'chinh_tinh' => [], 'phu_tinh' => [], 'vong_sao' => [], 'tuan_triet' => [], 'sao_luu' => []];
        }

        // 1. Tử Vi & Thiên Phủ
        $tuvi_idx = $data['an_sao_rules']['an_tu_vi'][$cuc][$ngay] ?? self::tinh_vi_tri_tu_vi($ngay, $cuc);
        $thienphu_idx = self::wrap_12(18 - $tuvi_idx);
        $laso[$tuvi_idx]['chinh_tinh'][] = 'tu_vi';
        foreach ($data['an_sao_rules']['an_chinh_tinh']['chom_tu_vi_offset'] as $sao => $offset) $laso[self::wrap_12($tuvi_idx + $offset)]['chinh_tinh'][] = $sao;
        $laso[$thienphu_idx]['chinh_tinh'][] = 'thien_phu';
        foreach ($data['an_sao_rules']['an_chinh_tinh']['chom_thien_phu_offset'] as $sao => $offset) $laso[self::wrap_12($thienphu_idx + $offset)]['chinh_tinh'][] = $sao;

        // 2. Tuần Triệt
        foreach ($data['an_sao_rules']['triet_khong'][$can_nam_id] ?? [] as $chi_id) $laso[self::get_chi_index($data, $chi_id)]['tuan_triet'][] = 'Triệt';
        $hoa_giap_id = self::get_hoa_giap_head($can_nam_idx, $chi_nam_idx);
        foreach ($data['an_sao_rules']['tuan_khong'][$hoa_giap_id] ?? [] as $chi_id) $laso[self::get_chi_index($data, $chi_id)]['tuan_triet'][] = 'Tuần';

        // 3. Phụ tinh Mapping Tĩnh
        foreach ($data['an_sao_rules']['an_sao_mapping']['theo_thang'] as $sao => $vong) $laso[$vong[$thang - 1]]['phu_tinh'][] = $sao;
        foreach ($data['an_sao_rules']['an_sao_mapping']['theo_gio'] as $sao => $vong) $laso[$vong[$gio - 1]]['phu_tinh'][] = $sao;
        foreach ($data['an_sao_rules']['an_sao_mapping']['theo_can_nam'] as $sao => $vong) $laso[$vong[$can_nam_idx - 1]]['phu_tinh'][] = $sao;

        // 4. Bàng tinh & Toán học
        $laso[self::wrap_12($chi_nam_idx + 1)]['phu_tinh'][] = 'thien_khong';
        $laso[self::wrap_12(7 - ($chi_nam_idx - 1))]['phu_tinh'][] = 'thien_khoc';
        $laso[self::wrap_12(7 + ($chi_nam_idx - 1))]['phu_tinh'][] = 'thien_hu';
        $laso[self::wrap_12($chi_nam_xem_idx - ($thang - 1) + ($gio - 1))]['phu_tinh'][] = 'dau_quan';

        $laso[self::wrap_12(5 + ($chi_nam_idx - 1))]['phu_tinh'][] = 'long_tri';
        $phuong_cac_idx = self::wrap_12(11 - ($chi_nam_idx - 1));
        $laso[$phuong_cac_idx]['phu_tinh'][] = 'phuong_cac';
        $laso[$phuong_cac_idx]['phu_tinh'][] = 'giai_than';

        $laso[self::wrap_12(2 + ($thang - 1))]['phu_tinh'][] = 'thien_rieu';
        $laso[self::wrap_12(2 + ($thang - 1))]['phu_tinh'][] = 'thien_y';
        $thien_tru_map = [1=>6, 2=>7, 3=>1, 4=>6, 5=>7, 6=>9, 7=>3, 8=>7, 9=>10, 10=>11];
        if(isset($thien_tru_map[$can_nam_idx])) $laso[$thien_tru_map[$can_nam_idx]]['phu_tinh'][] = 'thien_tru';

        $menh = $cung_data['menh_idx']; $than = $cung_data['than_idx'];
        $laso[self::wrap_12($menh + ($chi_nam_idx - 1))]['phu_tinh'][] = 'thien_tai';
        $laso[self::wrap_12($than + ($chi_nam_idx - 1))]['phu_tinh'][] = 'thien_tho';
        $laso[self::wrap_12($menh + 5)]['phu_tinh'][] = 'thien_thuong';
        $laso[self::wrap_12($menh + 7)]['phu_tinh'][] = 'thien_su';
        $laso[5]['phu_tinh'][] = 'thien_la';
        $laso[11]['phu_tinh'][] = 'dia_vong';

        $vx = $data['an_sao_rules']['an_sao_mapping']['theo_gio']['van_xuong'][$gio - 1];
        $vk = $data['an_sao_rules']['an_sao_mapping']['theo_gio']['van_khuc'][$gio - 1];
        $tp = $data['an_sao_rules']['an_sao_mapping']['theo_thang']['ta_phu'][$thang - 1];
        $hb = $data['an_sao_rules']['an_sao_mapping']['theo_thang']['huu_bat'][$thang - 1];

        $laso[self::wrap_12($vk + 2)]['phu_tinh'][] = 'thai_phu';
        $laso[self::wrap_12($vk - 2)]['phu_tinh'][] = 'phong_cao';
        $laso[self::wrap_12($vx + $ngay - 2)]['phu_tinh'][] = 'an_quang';
        $laso[self::wrap_12($vk - $ngay + 2)]['phu_tinh'][] = 'thien_quy';
        $laso[self::wrap_12($tp + $ngay - 1)]['phu_tinh'][] = 'tam_thai';
        $laso[self::wrap_12($hb - ($ngay - 1))]['phu_tinh'][] = 'bat_toa';

        $laso[self::wrap_12(10 + ($thang - 1))]['phu_tinh'][] = 'thien_hinh';

        $thien_giai_map = [1=>9, 2=>9, 3=>10, 4=>10, 5=>11, 6=>11, 7=>12, 8=>12, 9=>7, 10=>7, 11=>8, 12=>8];
        $laso[$thien_giai_map[$thang]]['phu_tinh'][] = 'thien_giai';
        $laso[self::wrap_12(8 + ($thang - 1))]['phu_tinh'][] = 'dia_giai';

        $laso[self::wrap_12(10 + ($chi_nam_idx - 1))]['phu_tinh'][] = 'thien_duc';
        $laso[self::wrap_12(6 + ($chi_nam_idx - 1))]['phu_tinh'][] = 'nguyet_duc';

        // Hoa Cái, Kiếp Sát (Theo Tam Hợp)
        $tam_hop_chi = ['dan'=>1, 'ngo'=>1, 'tuat'=>1, 'than'=>2, 'ty'=>2, 'thin'=>2, 'ti'=>3, 'dau'=>3, 'suu'=>3, 'hoi'=>4, 'mao'=>4, 'mui'=>4];
        $th_group = $tam_hop_chi[$chi_nam_id];

        $hoa_cai_map = [1=>11, 2=>5, 3=>2, 4=>8];
        $laso[$hoa_cai_map[$th_group]]['phu_tinh'][] = 'hoa_cai';

        $kiep_sat_map = [1=>12, 2=>6, 3=>3, 4=>9];
        $laso[$kiep_sat_map[$th_group]]['phu_tinh'][] = 'kiep_sat';

        // Đường Phù, Quốc Ấn, Lưu Niên Văn Tinh (Theo Can)
        $duong_phu_map = ['giap'=>8, 'at'=>9, 'binh'=>10, 'dinh'=>12, 'mau'=>10, 'ky'=>12, 'canh'=>1, 'tan'=>3, 'nham'=>4, 'quy'=>6];
        if(isset($duong_phu_map[$can_nam_id])) $laso[$duong_phu_map[$can_nam_id]]['phu_tinh'][] = 'duong_phu';

        $quoc_an_map = ['giap'=>11, 'at'=>12, 'binh'=>2, 'dinh'=>3, 'mau'=>2, 'ky'=>3, 'canh'=>5, 'tan'=>6, 'nham'=>8, 'quy'=>9];
        if(isset($quoc_an_map[$can_nam_id])) $laso[$quoc_an_map[$can_nam_id]]['phu_tinh'][] = 'quoc_an';

        $ln_van_tinh_map = ['giap'=>6, 'at'=>7, 'binh'=>9, 'dinh'=>10, 'mau'=>9, 'ky'=>10, 'canh'=>12, 'tan'=>1, 'nham'=>3, 'quy'=>4];
        if(isset($ln_van_tinh_map[$can_nam_id])) $laso[$ln_van_tinh_map[$can_nam_id]]['phu_tinh'][] = 'ln_van_tinh';

        // 5. Vòng Lộc Tồn & Hỏa Linh
        if (isset($data['an_sao_rules']['loc_ton_by_can'][$can_nam_id])) {
            $loc_ton_idx = self::get_chi_index($data, $data['an_sao_rules']['loc_ton_by_can'][$can_nam_id]);
            $laso[$loc_ton_idx]['phu_tinh'][] = 'loc_ton';
            $laso[self::wrap_12($loc_ton_idx + 1)]['phu_tinh'][] = 'kinh_duong';
            $laso[self::wrap_12($loc_ton_idx - 1)]['phu_tinh'][] = 'da_la';
            for ($i = 0; $i < 12; $i++) $laso[self::wrap_12($loc_ton_idx + ($i * $step))]['vong_sao'][] = $data['an_sao_rules']['vong_sao']['vong_loc_ton'][$i];
        }

        $bt_rules = $data['an_sao_rules']['bang_tinh_khac'] ?? [];
        if (!empty($bt_rules)) {
            foreach ($bt_rules['hoa_linh'] as $group => $hl) {
                if (strpos($group, $chi_nam_id) !== false) {
                    $laso[self::wrap_12(self::get_chi_index($data, $hl['hoa']) + $step * ($gio - 1))]['phu_tinh'][] = 'hoa_tinh';
                    $laso[self::wrap_12(self::get_chi_index($data, $hl['linh']) - $step * ($gio - 1))]['phu_tinh'][] = 'linh_tinh';
                    break;
                }
            }
            if(isset($bt_rules['luu_ha'][$can_nam_id])) $laso[self::get_chi_index($data, $bt_rules['luu_ha'][$can_nam_id])]['phu_tinh'][] = 'luu_ha';
            if(isset($bt_rules['thien_quan'][$can_nam_id])) $laso[self::get_chi_index($data, $bt_rules['thien_quan'][$can_nam_id])]['phu_tinh'][] = 'thien_quan';
            if(isset($bt_rules['thien_phuc'][$can_nam_id])) $laso[self::get_chi_index($data, $bt_rules['thien_phuc'][$can_nam_id])]['phu_tinh'][] = 'thien_phuc';
            foreach ($bt_rules['co_qua'] as $group => $cq) {
                if (strpos($group, $chi_nam_id) !== false) {
                    $laso[self::get_chi_index($data, $cq['co_than'])]['phu_tinh'][] = 'co_than';
                    $laso[self::get_chi_index($data, $cq['qua_tu'])]['phu_tinh'][] = 'qua_tu';
                    break;
                }
            }
            foreach ($bt_rules['pha_toai'] as $group => $pt) {
                if (strpos($group, $chi_nam_id) !== false) { $laso[self::get_chi_index($data, $pt)]['phu_tinh'][] = 'pha_toai'; break; }
            }
        }

        // 6. Đào Hoa, Thiên Mã, Hóa
        foreach ($data['an_sao_rules']['dao_hoa_by_nam_chi_group'] as $group_key => $stars) {
            if (strpos($group_key, $chi_nam_id) !== false) {
                // Chỉ lấy Đào Hoa từ group (Hồng Loan & Thiên Hỷ tính riêng bên dưới)
                $laso[self::get_chi_index($data, $stars['dao_hoa'])]['phu_tinh'][] = 'dao_hoa';
                break;
            }
        }

        $hong_loan_idx = self::wrap_12(5 - $chi_nam_idx);
        $thien_hy_idx  = self::wrap_12($hong_loan_idx + 6);
        $laso[$hong_loan_idx]['phu_tinh'][] = 'hong_loan';
        $laso[$thien_hy_idx]['phu_tinh'][]  = 'thien_hy';
        foreach ($data['an_sao_rules']['thien_ma_by_tam_hop'] as $rule) {
            if (in_array($chi_nam_id, $rule['group'])) { $laso[self::get_chi_index($data, $rule['ma'])]['phu_tinh'][] = 'thien_ma'; break; }
        }
        foreach ($data['an_sao_rules']['hoa_by_can'][$can_nam_id] ?? [] as $hoa => $sao) {
            for ($i = 1; $i <= 12; $i++) {
                if (in_array($sao, $laso[$i]['chinh_tinh']) || in_array($sao, $laso[$i]['phu_tinh'])) { $laso[$i]['phu_tinh'][] = 'hoa_'.$hoa; break; }
            }
        }

        // 7. Vòng Thái Tuế & Tràng Sinh
        for ($i = 0; $i < 12; $i++) $laso[self::wrap_12($chi_nam_idx + $i)]['vong_sao'][] = $data['an_sao_rules']['vong_sao']['vong_thai_tue'][$i];
        $trang_sinh_start = [2 => 9, 3 => 12, 4 => 6, 5 => 9, 6 => 3][$cuc] ?? 1;
        for ($i = 0; $i < 12; $i++) $laso[self::wrap_12($trang_sinh_start + ($i * $step))]['vong_sao'][] = $data['an_sao_rules']['vong_sao']['vong_trang_sinh'][$i];

        // 8. TÍNH SAO LƯU NIÊN (L.Thái Tuế, L.Lộc, L.Kình Đà, L.Mã...)
        $can_nx_id = $data['thien_can'][$calendar_data['can_nam_xem_idx']]['id'];
        $chi_nx_id = $data['dia_chi'][$calendar_data['chi_nam_xem_idx']]['id'];
        $chi_nx_idx = $calendar_data['chi_nam_xem_idx'];

        $laso[$chi_nx_idx]['sao_luu'][] = 'L.Thái Tuế';
        $laso[self::wrap_12($chi_nx_idx + 2)]['sao_luu'][] = 'L.Tang Môn';
        $laso[self::wrap_12($chi_nx_idx + 8)]['sao_luu'][] = 'L.Bạch Hổ';
        $laso[self::wrap_12(7 - ($chi_nx_idx - 1))]['sao_luu'][] = 'L.Thiên Khốc';
        $laso[self::wrap_12(7 + ($chi_nx_idx - 1))]['sao_luu'][] = 'L.Thiên Hư';

        if (isset($data['an_sao_rules']['loc_ton_by_can'][$can_nx_id])) {
            $l_loc_idx = self::get_chi_index($data, $data['an_sao_rules']['loc_ton_by_can'][$can_nx_id]);
            $laso[$l_loc_idx]['sao_luu'][] = 'L.Lộc Tồn';
            $laso[self::wrap_12($l_loc_idx + 1)]['sao_luu'][] = 'L.Kình Dương';
            $laso[self::wrap_12($l_loc_idx - 1)]['sao_luu'][] = 'L.Đà La';
        }
        foreach ($data['an_sao_rules']['thien_ma_by_tam_hop'] as $rule) {
            if (in_array($chi_nx_id, $rule['group'])) { $laso[self::get_chi_index($data, $rule['ma'])]['sao_luu'][] = 'L.Thiên Mã'; break; }
        }
        foreach ($data['an_sao_rules']['hoa_by_can'][$can_nx_id] ?? [] as $hoa => $sao) {
            for ($i = 1; $i <= 12; $i++) {
                if (in_array($sao, $laso[$i]['chinh_tinh']) || in_array($sao, $laso[$i]['phu_tinh'])) {
                    $ten_hoa = ['loc' => 'Hóa Lộc', 'quyen' => 'Hóa Quyền', 'khoa' => 'Hóa Khoa', 'ky' => 'Hóa Kỵ'][$hoa];
                    $laso[$i]['sao_luu'][] = 'L.'.$ten_hoa; break;
                }
            }
        }

        return $laso;
    }

    private static function step_5_format_output($data, $normalized, $calendar, $cung, $laso, $egg_message, $gio_str) {
        $can_nam_id = $data['thien_can'][$calendar['can_nam_idx']]['id'];
        $chi_nam_id = $data['dia_chi'][$calendar['chi_nam_idx']]['id'];
        $chi_menh_id = $data['dia_chi'][$cung['menh_idx']]['id'];
        $hoa_giap_key = null; foreach ($data['can_chi_60'] as $key => $hc) { if ($hc['can'] === $can_nam_id && $hc['chi'] === $chi_nam_id) { $hoa_giap_key = $key; break; } }
        $nam_nap_am = $hoa_giap_key ? $data['can_chi_60'][$hoa_giap_key]['nap_am'] : 'Đang cập nhật';

        $is_duong_year = ($data['thien_can'][$calendar['can_nam_idx']]['polarity'] === '+');
        $is_nam = ($normalized['gioi_tinh'] === 'nam');

        $menh_hanh_str = '';
        if (strpos($nam_nap_am, 'Kim') !== false) $menh_hanh_str = 'kim'; elseif (strpos($nam_nap_am, 'Mộc') !== false) $menh_hanh_str = 'moc'; elseif (strpos($nam_nap_am, 'Thủy') !== false) $menh_hanh_str = 'thuy'; elseif (strpos($nam_nap_am, 'Hỏa') !== false) $menh_hanh_str = 'hoa'; elseif (strpos($nam_nap_am, 'Thổ') !== false) $menh_hanh_str = 'tho';

        $cuc_hanh_str = $data['ngu_hanh_cuc'][$cung['cuc']]['hanh'];
        if ($menh_hanh_str == $cuc_hanh_str) $menh_cuc_ly = "Mệnh Cục bình hòa"; elseif ($data['ngu_hanh'][$menh_hanh_str]['sinh'] == $cuc_hanh_str) $menh_cuc_ly = "Mệnh sinh Cục"; elseif ($data['ngu_hanh'][$cuc_hanh_str]['sinh'] == $menh_hanh_str) $menh_cuc_ly = "Cục sinh Mệnh"; elseif ($data['ngu_hanh'][$menh_hanh_str]['khac'] == $cuc_hanh_str) $menh_cuc_ly = "Mệnh khắc Cục"; else $menh_cuc_ly = "Cục khắc Mệnh";

        $chu_menh_map = ['ty' => 'Tham Lang', 'suu' => 'Cự Môn', 'hoi' => 'Cự Môn', 'dan' => 'Lộc Tồn', 'tuat' => 'Lộc Tồn', 'mao' => 'Văn Khúc', 'dau' => 'Văn Khúc', 'thin' => 'Liêm Trinh', 'than' => 'Liêm Trinh', 'ti' => 'Vũ Khúc', 'mui' => 'Vũ Khúc', 'ngo' => 'Phá Quân'];
        $chu_than_map = ['ty' => 'Hỏa Tinh', 'ngo' => 'Hỏa Tinh', 'suu' => 'Thiên Tướng', 'mui' => 'Thiên Tướng', 'dan' => 'Thiên Lương', 'than' => 'Thiên Lương', 'mao' => 'Thiên Đồng', 'dau' => 'Thiên Đồng', 'thin' => 'Văn Xương', 'tuat' => 'Văn Xương', 'ti' => 'Thiên Cơ', 'hoi' => 'Thiên Cơ'];

        $nam_xem_calc = (int)$calendar['nam_xem'];
        $tuoi_am = $nam_xem_calc - $calendar['am_lich']['nam'] + 1;
        if ($tuoi_am < 1) $tuoi_am = 1;

        $is_menh_duong = ($data['dia_chi'][$cung['menh_idx']]['polarity'] === '+');
        $thuan_nghich_ly = ($is_duong_year === $is_menh_duong) ? "Âm Dương Thuận Lý" : "Âm Dương Nghịch Lý";
        $dai_han_hanh = ($is_duong_year === $is_nam) ? "Đại Hạn Thuận Hành" : "Đại Hạn Nghịch Hành";

        $tu_hoa_nam_xem = [];
        $can_nx_id_for_thongtin = $data['thien_can'][$calendar['can_nam_xem_idx']]['id'];
        $hoa_labels = ['loc' => 'Hóa Lộc', 'quyen' => 'Hóa Quyền', 'khoa' => 'Hóa Khoa', 'ky' => 'Hóa Kỵ'];
        $hoa_icons  = ['loc' => '🟢', 'quyen' => '🔵', 'khoa' => '🟡', 'ky' => '🔴'];
        $hoa_notes  = [
            'loc'   => 'Tăng lộc, thuận lợi, thu nhập',
            'quyen' => 'Quyền lực, chủ động, quyết đoán',
            'khoa'  => 'Danh tiếng, bằng cấp, thanh danh',
            'ky'    => 'Vướng mắc, cần thận trọng'
        ];
        if (isset($data['an_sao_rules']['hoa_by_can'][$can_nx_id_for_thongtin])) {
            foreach ($data['an_sao_rules']['hoa_by_can'][$can_nx_id_for_thongtin] as $hoa_type => $sao_id) {
                $sao_name = '';
                $sao_element = '';
                $sao_cung_name = '';
                $sao_cung_chi = '';

                if (isset($data['chinh_tinh'][$sao_id])) {
                    $sao_name = $data['chinh_tinh'][$sao_id]['name'];
                } else {
                    $sao_name = self::get_star_name($data, $sao_id);
                }
                $sao_element = self::get_star_element($data, $sao_id);

                foreach ($laso as $cung_idx => $cung_data_item) {
                    if (in_array($sao_id, $cung_data_item['chinh_tinh']) || in_array($sao_id, $cung_data_item['phu_tinh'])) {
                        $offset = $cung_idx - $cung['menh_idx'];
                        if ($offset < 0) $offset += 12;
                        $cung_chuc_nang_arr = [0 => 'Mệnh', 1 => 'Phụ Mẫu', 2 => 'Phúc Đức', 3 => 'Điền Trạch', 4 => 'Quan Lộc', 5 => 'Nô Bộc', 6 => 'Thiên Di', 7 => 'Tật Ách', 8 => 'Tài Bạch', 9 => 'Tử Tức', 10 => 'Phu Thê', 11 => 'Huynh Đệ'];
                        $sao_cung_name = $cung_chuc_nang_arr[$offset] ?? '';
                        $sao_cung_chi  = $data['dia_chi'][$cung_idx]['name'] ?? '';
                        break;
                    }
                }
                $tu_hoa_nam_xem[] = [
                    'type'      => $hoa_type,
                    'label'     => $hoa_labels[$hoa_type] ?? $hoa_type,
                    'icon'      => $hoa_icons[$hoa_type] ?? '',
                    'note'      => $hoa_notes[$hoa_type] ?? '',
                    'sao_name'  => $sao_name,
                    'sao_el'    => $sao_element,
                    'cung_name' => $sao_cung_name,
                    'cung_chi'  => $sao_cung_chi,
                ];
            }
        }

        $thong_tin = [
            'ho_ten'          => $normalized['ho_ten'],
            'tuoi'            => $tuoi_am,
            'ngay_duong'      => sprintf('%02d/%02d/%04d', $normalized['ngay_goc'], $normalized['thang_goc'], $normalized['nam_goc']),
            'gio_duong'       => $normalized['gio_str'],
            'ngay_am'         => sprintf('%02d/%02d/%04d', $calendar['am_lich']['ngay'], $calendar['am_lich']['thang'], $calendar['am_lich']['nam']),
            'gio_am'          => $data['dia_chi'][$calendar['gio_idx']]['name'],
            'nam_can_chi'     => $data['thien_can'][$calendar['can_nam_idx']]['name'] . ' ' . $data['dia_chi'][$calendar['chi_nam_idx']]['name'],
            'nam_nap_am'      => $nam_nap_am,
            'nam_xem'         => $calendar['nam_xem'] . ' (' . $data['thien_can'][$calendar['can_nam_xem_idx']]['name'] . ' ' . $data['dia_chi'][$calendar['chi_nam_xem_idx']]['name'] . ')',
            'nam_xem_raw'     => (int)$calendar['nam_xem'],
            'can_nam_xem_ten' => $data['thien_can'][$calendar['can_nam_xem_idx']]['name'],
            'gioi_tinh'       => ($is_duong_year ? "Dương " : "Âm ") . ($is_nam ? "Nam" : "Nữ"),
            'cuc_name'        => $data['ngu_hanh_cuc'][$cung['cuc']]['name'] ?? 'Không rõ Cục',
            'am_duong_ly'     => $thuan_nghich_ly . ' - ' . $dai_han_hanh,
            'menh_cuc_ly'     => $menh_cuc_ly,
            'chu_menh'        => $chu_menh_map[$chi_menh_id] ?? '',
            'chu_than'        => $chu_than_map[$chi_nam_id] ?? '',
            'than_cu'         => '',
            'egg_message'     => $egg_message,
            'gio_sinh'        => $data['dia_chi'][$calendar['gio_idx']]['hour'],
            'tu_hoa_nam_xem'  => $tu_hoa_nam_xem,
        ];

        $cung_chuc_nang = [0 => 'Mệnh', 1 => 'Phụ Mẫu', 2 => 'Phúc Đức', 3 => 'Điền Trạch', 4 => 'Quan Lộc', 5 => 'Nô Bộc', 6 => 'Thiên Di', 7 => 'Tật Ách', 8 => 'Tài Bạch', 9 => 'Tử Tức', 10 => 'Phu Thê', 11 => 'Huynh Đệ'];
        $hung_sat_keys = ['kinh_duong', 'da_la', 'hoa_tinh', 'linh_tinh', 'dia_khong', 'dia_kiep', 'thien_khong', 'thien_khoc', 'thien_hu', 'thien_hinh', 'luu_ha', 'pha_toai', 'kiep_sat', 'co_than', 'qua_tu', 'dia_vong', 'thien_la', 'dau_quan', 'thien_thuong', 'thien_su'];

        $chieu_dai_van = ($is_duong_year === $is_nam) ? 1 : -1;
        $chi_sinh_id = $data['dia_chi'][$calendar['chi_nam_idx']]['id'];
        $tieu_han_khoi = 1;
        $dan_ngo_tuat = ['dan', 'ngo', 'tuat'];
        $than_ty_thin = ['than', 'ty', 'thin'];
        $ti_dau_suu   = ['ti', 'dau', 'suu'];
        $hoi_mao_mui  = ['hoi', 'mao', 'mui'];
        if (in_array($chi_sinh_id, $dan_ngo_tuat)) $tieu_han_khoi = 5;
        elseif (in_array($chi_sinh_id, $than_ty_thin)) $tieu_han_khoi = 11;
        elseif (in_array($chi_sinh_id, $ti_dau_suu)) $tieu_han_khoi = 8;
        elseif (in_array($chi_sinh_id, $hoi_mao_mui)) $tieu_han_khoi = 2;

        $tuoi_tinh = (int)$calendar['nam_xem'] - $calendar['am_lich']['nam'] + 1;
        if ($tuoi_tinh < 1) $tuoi_tinh = 1;

        $chieu_tieu_han = $is_nam ? 1 : -1;
        $tieu_han_idx = self::wrap_12($tieu_han_khoi + ($tuoi_tinh - 1) * $chieu_tieu_han);

        $thang_sinh = $calendar['am_lich']['thang'];
        $gio_sinh_idx = $calendar['gio_idx'];

        $luu_thai_tue_idx = $calendar['chi_nam_xem_idx'];

        $tmp = $luu_thai_tue_idx;
        for ($i = 1; $i < $thang_sinh; $i++) {
            $tmp = self::wrap_12($tmp - 1);
        }
        for ($i = 1; $i < $gio_sinh_idx; $i++) {
            $tmp = self::wrap_12($tmp + 1);
        }
        $nguyet_han_thang1_idx = $tmp;

        $nguyet_han = [];
        for ($m = 1; $m <= 12; $m++) {
            $nguyet_han[$m] = self::wrap_12($nguyet_han_thang1_idx + ($m - 1));
        }

        $thong_tin['tieu_han_idx'] = $tieu_han_idx;
        $thong_tin['tieu_han_chi'] = $data['dia_chi'][$tieu_han_idx]['name'];
        $thong_tin['nguyet_han'] = $nguyet_han;

        $la_so_formatted = [];

        for ($i = 1; $i <= 12; $i++) {
            $offset = $i - $cung['menh_idx']; if ($offset < 0) $offset += 12;
            $cung_name = $cung_chuc_nang[$offset];
            if ($i === $cung['than_idx']) { $thong_tin['than_cu'] = "Thân cư " . $cung_name; if ($cung_name !== 'Mệnh') $cung_name .= ' / Thân'; }

            $step_from_menh = ($i - $cung['menh_idx']) * $chieu_dai_van; if ($step_from_menh < 0) $step_from_menh += 12;

            $chinh_tinh_arr = []; foreach ($laso[$i]['chinh_tinh'] as $ct_id) $chinh_tinh_arr[] = ['name' => $data['chinh_tinh'][$ct_id]['name'] ?? ucfirst(str_replace('_', ' ', $ct_id)), 'do_sang' => $data['do_sang'][$ct_id][$i] ?? '', 'element' => self::get_star_element($data, $ct_id)];

            $phu_cat = []; $phu_hung = [];
            foreach ($laso[$i]['phu_tinh'] as $pt_id) {
                $p_data = ['name' => self::get_star_name($data, $pt_id), 'do_sang' => $data['do_sang'][$pt_id][$i] ?? '', 'element' => self::get_star_element($data, $pt_id)];
                if (in_array($pt_id, $hung_sat_keys)) $phu_hung[] = $p_data; else $phu_cat[] = $p_data;
            }

            $vong_sao_arr = []; $trang_sinh_name = 'Tràng Sinh';
            foreach ($laso[$i]['vong_sao'] as $vs_id) {
                $name = self::get_star_name($data, $vs_id);
                if (in_array($vs_id, $data['an_sao_rules']['vong_sao']['vong_trang_sinh'])) $trang_sinh_name = $name; else $vong_sao_arr[] = ['name' => $name, 'element' => self::get_star_element($data, $vs_id)];
            }

            $thang_trong_cung = [];
            foreach ($nguyet_han as $thang_so => $cung_idx) {
                if ($cung_idx === $i) $thang_trong_cung[] = $thang_so;
            }

            $la_so_formatted[$i] = [
                'cung_name' => $cung_name, 'chi_name' => $laso[$i]['dia_chi'], 'can_name' => $cung['can_cung'][$i]['name'],
                'dai_van' => $cung['cuc'] + ($step_from_menh * 10), 'chinh_tinh' => $chinh_tinh_arr,
                'tuan_triet' => $laso[$i]['tuan_triet'], 'phu_cat' => $phu_cat, 'phu_hung' => $phu_hung,
                'vong_sao' => $vong_sao_arr, 'trang_sinh' => $trang_sinh_name, 'sao_luu' => $laso[$i]['sao_luu'],
                'is_tieu_han' => ($i === $tieu_han_idx),
                'thang_trong_cung' => $thang_trong_cung,
            ];
        }
        return ['thong_tin' => $thong_tin, 'la_so' => $la_so_formatted];
    }

    private static function get_star_name($data, $id) {
        if (isset($data['phu_tinh'])) foreach ($data['phu_tinh'] as $group) if (isset($group[$id])) return $group[$id]['name'];
        return mb_convert_case(str_replace('_', ' ', $id), MB_CASE_TITLE, "UTF-8");
    }

    private static function get_star_element($data, $id) {
        if (isset($data['chinh_tinh'][$id]['element'])) return $data['chinh_tinh'][$id]['element'];
        if (isset($data['phu_tinh'])) foreach ($data['phu_tinh'] as $group) if (isset($group[$id]['element'])) return $group[$id]['element'];
        $fallback = [
            'kim' => ['vu_khuc','that_sat','kinh_duong','da_la','thien_khoc','quan_phu','quan_phu2','thai_phu','phong_cao','tuong_quan','phi_liem','thien_thuong'],
            'moc' => ['thien_co','tham_lang','hoa_khoa','thien_khong','long_tri','phuong_cac','giai_than','thanh_long','moc_duc','hoa_ky','thien_su','thien_tho'],
            'hoa' => ['thai_duong','liem_trinh','hoa_tinh','linh_tinh','thien_viet','thien_khoi','hoa_quyen','thien_ma','dao_hoa','hong_loan','thien_hy','thai_tue','tang_mon','bach_ho','dieu_khach','dia_giai','thien_giai'],
            'tho' => ['tu_vi','thien_phu','thien_luong','loc_ton','hoa_loc','ta_phu','van_xuong','an_quang','thien_quy','thien_tru','dau_quan','co_than','qua_tu','bat_toa','thien_tai','thien_la','dia_vong'],
        ];
        foreach ($fallback as $element => $stars) if (in_array($id, $stars)) return $element;
        return 'thuy';
    }

    private static function wrap_12($val) {
        $val = $val % 12; return $val <= 0 ? $val + 12 : $val;
    }

    private static function wrap_10($val) {
        $val = $val % 10; return $val <= 0 ? $val + 10 : $val;
    }

    private static function wrap_5($val) {
        $val = $val % 5; return $val <= 0 ? 5 : $val;
    }

    private static function get_chi_index($data, $chi_id) {
        foreach ($data['dia_chi'] as $idx => $chi) {
            if ($chi['id'] === $chi_id) return $idx;
        }
        return 1;
    }

    private static function get_hoa_giap_head($can_idx, $chi_idx) {
        $chi_keys = ['ty', 'suu', 'dan', 'mao', 'thin', 'ti', 'ngo', 'mui', 'than', 'dau', 'tuat', 'hoi'];
        $offset = $chi_idx - $can_idx; if ($offset < 0) $offset += 12; return 'giap_' . $chi_keys[$offset];
    }

    private static function tinh_vi_tri_tu_vi($ngay, $cuc) {
        $thuong = intdiv($ngay, (int)$cuc);
        $du = $ngay % (int)$cuc;
        $cuc_add = ($du != 0) ? ((int)$cuc - $du) : 0; $thuong += ($du != 0) ? 1 : 0;
        $tu_vi_pos = 3 + $thuong - 1;
        $tu_vi_pos += ($cuc_add % 2 != 0) ? -$cuc_add : $cuc_add;
        return self::wrap_12($tu_vi_pos);
    }
}