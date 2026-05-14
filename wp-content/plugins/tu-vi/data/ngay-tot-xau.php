<?php
if (!defined('ABSPATH')) {
    exit;
}

return [
    'version' => '3.0.1',
    'name'    => 'Tu Vi Ngay Tot Xau Rules',

    /*
    |--------------------------------------------------------------------------
    | 1. TRỌNG SỐ ĐIỂM (WEIGHTS)
    |--------------------------------------------------------------------------
    */
    'weights' => [
        'hoang_dao'           => 10,
        'hac_dao'             => -10,
        'truc_tot'            => 10,
        'truc_xau'            => -10,
        'xung_nhat_chu'       => -30,
        'xung_thai_tue'       => -20,
        'hop_nhat_chu'        => 15,
        'hop_thai_tue'        => 10,
        'tam_hinh_nhat_chu'   => -15,
        'tam_hinh_thai_tue'   => -10,
        'luc_pha_nhat_chu'    => -10,
        'luc_pha_thai_tue'    => -8,
        'tam_hop_nhat_chu'    => 12,
        'tam_hop_thai_tue'    => 8,
        'can_pha_nhat_chu'    => -20,
        'can_pha_thai_tue'    => -10,
        'ngu_hanh_sinh'       => 10,
        'ngu_hanh_sinh_xuat'  => -5,
        'ngu_hanh_khac'       => -15,
        'sao_28_tot'          => 10,
        'sao_28_xau'          => -10,
        'sao_ngay_tot'        => 15,
        'sao_ngay_xau'        => -25,
    ],

    /*
    |--------------------------------------------------------------------------
    | 2. NGU HANH NAP AM - DU 60 HOA GIAP
    |--------------------------------------------------------------------------
    */
    'nap_am_ngu_hanh' => [
        'giap_ty'=>'kim',   'at_suu'=>'kim',
        'nham_than'=>'kim', 'quy_dau'=>'kim',
        'canh_thin'=>'kim', 'tan_ti'=>'kim',
        'giap_ngo'=>'kim',  'at_mui'=>'kim',
        'nham_dan'=>'kim',  'quy_mao'=>'kim',
        'canh_tuat'=>'kim', 'tan_hoi'=>'kim',
        'binh_dan'=>'hoa',  'dinh_mao'=>'hoa',
        'giap_tuat'=>'hoa', 'at_hoi'=>'hoa',
        'mau_ty'=>'hoa',    'ky_suu'=>'hoa',
        'binh_than'=>'hoa', 'dinh_dau'=>'hoa',
        'giap_thin'=>'hoa', 'at_ti'=>'hoa',
        'mau_ngo'=>'hoa',   'ky_mui'=>'hoa',
        'mau_thin'=>'moc',  'ky_ti'=>'moc',
        'nham_ngo'=>'moc',  'quy_mui'=>'moc',
        'canh_dan'=>'moc',  'tan_mao'=>'moc',
        'mau_tuat'=>'moc',  'ky_hoi'=>'moc',
        'nham_ty'=>'moc',   'quy_suu'=>'moc',
        'canh_than'=>'moc', 'tan_dau'=>'moc',
        'binh_ty'=>'thuy',  'dinh_suu'=>'thuy',
        'giap_than'=>'thuy','at_dau'=>'thuy',
        'nham_thin'=>'thuy','quy_ti'=>'thuy',
        'binh_ngo'=>'thuy', 'dinh_mui'=>'thuy',
        'giap_dan'=>'thuy', 'at_mao'=>'thuy',
        'nham_tuat'=>'thuy','quy_hoi'=>'thuy',
        'canh_ngo'=>'tho',  'tan_mui'=>'tho',
        'mau_dan'=>'tho',   'ky_mao'=>'tho',
        'binh_tuat'=>'tho', 'dinh_hoi'=>'tho',
        'canh_ty'=>'tho',   'tan_suu'=>'tho',
        'mau_than'=>'tho',  'ky_dau'=>'tho',
        'binh_thin'=>'tho', 'dinh_ti'=>'tho',
    ],

    /*
    |--------------------------------------------------------------------------
    | 3. THIEN CAN TUONG PHA & DIA CHI XUNG / HAI
    |--------------------------------------------------------------------------
    */
    'can_khac' => [
        'giap'=>'mau', 'at'=>'ky',    'binh'=>'canh', 'dinh'=>'tan', 'mau'=>'nham',
        'ky'=>'quy',   'canh'=>'giap','tan'=>'at',    'nham'=>'binh','quy'=>'dinh'
    ],
    'chi_xung' => [
        'ty'=>'ngo',    'suu'=>'mui',  'dan'=>'than',  'mao'=>'dau',
        'thin'=>'tuat', 'ti'=>'hoi',   'ngo'=>'ty',    'mui'=>'suu',
        'than'=>'dan',  'dau'=>'mao',  'tuat'=>'thin', 'hoi'=>'ti'
    ],
    'chi_hai' => [
        'ty'=>'mui',   'suu'=>'ngo',  'dan'=>'ti',    'mao'=>'thin',
        'thin'=>'mao', 'ti'=>'dan',   'ngo'=>'suu',   'mui'=>'ty',
        'than'=>'hoi', 'dau'=>'tuat', 'tuat'=>'dau',  'hoi'=>'than'
    ],

    /*
    |--------------------------------------------------------------------------
    | 4. TAM HINH
    |--------------------------------------------------------------------------
    */
    'tam_hinh' => [
        'dan'  => ['ti',  'than'],
        'ti'   => ['dan', 'than'],
        'than' => ['dan', 'ti'],
        'suu'  => ['tuat','mui'],
        'tuat' => ['suu', 'mui'],
        'mui'  => ['suu', 'tuat'],
        'ty'   => ['mao'],
        'mao'  => ['ty'],
        'thin' => ['thin'],
        'ngo'  => ['ngo'],
        'dau'  => ['dau'],
        'hoi'  => ['hoi'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 5. LUC PHA
    |--------------------------------------------------------------------------
    */
    'luc_pha' => [
        'ty'   => 'dau',  'dau'  => 'ty',
        'suu'  => 'thin', 'thin' => 'suu',
        'dan'  => 'hoi',  'hoi'  => 'dan',
        'mao'  => 'ngo',  'ngo'  => 'mao',
        'ti'   => 'than', 'than' => 'ti',
        'mui'  => 'tuat', 'tuat' => 'mui',
    ],

    /*
    |--------------------------------------------------------------------------
    | 6. TAM HOP CUC
    |--------------------------------------------------------------------------
    */
    'tam_hop' => [
        'dan'  => ['ngo', 'tuat'],
        'ngo'  => ['dan', 'tuat'],
        'tuat' => ['dan', 'ngo'],
        'than' => ['ty',  'thin'],
        'ty'   => ['than','thin'],
        'thin' => ['than','ty'],
        'hoi'  => ['mao', 'mui'],
        'mao'  => ['hoi', 'mui'],
        'mui'  => ['hoi', 'mao'],
        'ti'   => ['dau', 'suu'],
        'dau'  => ['ti',  'suu'],
        'suu'  => ['ti',  'dau'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 7. LUC HOP
    |--------------------------------------------------------------------------
    */
    'luc_hop' => [
        'ty'   => 'suu',  'suu'  => 'ty',
        'dan'  => 'hoi',  'hoi'  => 'dan',
        'mao'  => 'tuat', 'tuat' => 'mao',
        'thin' => 'dau',  'dau'  => 'thin',
        'ti'   => 'than', 'than' => 'ti',
        'ngo'  => 'mui',  'mui'  => 'ngo',
    ],

    /*
    |--------------------------------------------------------------------------
    | 8. 12 SAO HOANG DAO / HAC DAO
    |--------------------------------------------------------------------------
    */
    'thap_nhi_truc_tinh' => [
        ['name' => 'Thanh Long', 'type' => 'Hoang Dao', 'desc' => 'Tốt mọi việc, đặc biệt xuất hành, cưới hỏi.'],
        ['name' => 'Minh Đường', 'type' => 'Hoang Dao', 'desc' => 'Tốt mọi việc, lợi cho thăng tiến, nhậm chức.'],
        ['name' => 'Thiên Hình', 'type' => 'Hac Dao',   'desc' => 'Kỵ kiện tụng, tranh chấp, nguy cơ thị phi.'],
        ['name' => 'Chu Tước',   'type' => 'Hac Dao',   'desc' => 'Kỵ xuất hành, giao dịch, dễ hao tài.'],
        ['name' => 'Kim Quỹ',    'type' => 'Hoang Dao', 'desc' => 'Tốt cho cưới hỏi, cầu tài lộc, giao dịch.'],
        ['name' => 'Bảo Quang',  'type' => 'Hoang Dao', 'desc' => 'Đại cát, vạn sự hanh thông.'],
        ['name' => 'Bạch Hổ',    'type' => 'Hac Dao',   'desc' => 'Kỵ động thổ, cưới hỏi, rủi ro tai nạn.'],
        ['name' => 'Ngọc Đường', 'type' => 'Hoang Dao', 'desc' => 'Tốt cho xây dựng, nhập trạch, thi cử.'],
        ['name' => 'Thiên Lao',  'type' => 'Hac Dao',   'desc' => 'Xấu mọi việc, cẩn trọng đi lại.'],
        ['name' => 'Huyền Vũ',   'type' => 'Hac Dao',   'desc' => 'Kỵ giao dịch, xuất hành, đề phòng mất mát.'],
        ['name' => 'Tư Mệnh',    'type' => 'Hoang Dao', 'desc' => 'Tốt mọi việc, đặc biệt khởi tạo, sửa chữa.'],
        ['name' => 'Câu Trận',   'type' => 'Hac Dao',   'desc' => 'Kỵ động thổ, an táng, xuất hành.']
    ],
    'khoi_thanh_long' => [
        'dan'=>'ty',   'mao'=>'dan',  'thin'=>'thin', 'ti'=>'ngo',
        'ngo'=>'than', 'mui'=>'tuat', 'than'=>'ty',   'dau'=>'dan',
        'tuat'=>'thin','hoi'=>'ngo',  'ty'=>'than',   'suu'=>'tuat'
    ],

    /*
    |--------------------------------------------------------------------------
    | 9. BANH TO BACH KY
    |--------------------------------------------------------------------------
    */
    'banh_to_bach_ky' => [
        'can' => [
            'giap' => 'Giáp bất khai thương (Kỵ mở kho, xuất tiền)',
            'at'   => 'Ất bất tài chủng (Kỵ gieo trồng, nông nghiệp)',
            'binh' => 'Bính bất tu táo (Kỵ sửa chữa bếp núc)',
            'dinh' => 'Đinh bất thế đầu (Kỵ cắt tóc, chỉnh sửa diện mạo)',
            'mau'  => 'Mậu bất thụ điền (Kỵ nhận đất, mua bán bất động sản)',
            'ky'   => 'Kỷ bất phá khoán (Kỵ phá vỡ hợp đồng, hủy giao ước)',
            'canh' => 'Canh bất kinh lạc (Kỵ dệt lưới, dệt vải)',
            'tan'  => 'Tân bất hợp tương (Kỵ trộn tương, ủ men)',
            'nham' => 'Nhâm bất quyết thủy (Kỵ tháo nước, đắp đập)',
            'quy'  => 'Quý bất từ tụng (Kỵ dính dáng kiện tụng, tranh chấp)'
        ],
        'chi' => [
            'ty'   => 'Tý bất vấn bốc (Kỵ gieo quẻ, hỏi chuyện tâm linh)',
            'suu'  => 'Sửu bất quan đới (Kỵ nhận chức, đội mũ mão)',
            'dan'  => 'Dần bất tế tự (Kỵ tế lễ thần linh, gia tiên)',
            'mao'  => 'Mão bất xuyên tỉnh (Kỵ đào giếng, khơi dòng)',
            'thin' => 'Thìn bất khốc khấp (Kỵ khóc lóc, bi lụy)',
            'ti'   => 'Tỵ bất viễn hành (Kỵ xuất hành đi xa)',
            'ngo'  => 'Ngọ bất thiêm cái (Kỵ lợp mái nhà, dựng xà)',
            'mui'  => 'Mùi bất phục dược (Kỵ bốc thuốc, uống thuốc bệnh)',
            'than' => 'Thân bất an sàng (Kỵ kê giường, dọn phòng ngủ)',
            'dau'  => 'Dậu bất hội khách (Kỵ mở tiệc, đãi khách)',
            'tuat' => 'Tuất bất cật khuyển (Kỵ ăn thịt chó, sát sinh thú vật)',
            'hoi'  => 'Hợi bất giá thú (Kỵ cưới hỏi, ăn hỏi)'
        ],
        'chi_purpose_map' => [
            'ti'   => ['xuat_hanh'],
            'ngo'  => ['dong_tho'],
            'than' => ['nhap_trach'],
            'hoi'  => ['cuoi'],
        ],
        'can_purpose_map' => [
            'giap' => ['khai_truong'],
            'mau'  => ['dong_tho'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 10. MUC DICH (SU KIEN)
    |--------------------------------------------------------------------------
    */
    'muc_dich_rules' => [
        'cuoi' => [
            'label'             => 'Cưới hỏi, Gia đạo',
            'prefer_truc'       => ['thanh', 'dinh', 'man', 'khai', 'binh'],
            'avoid_truc'        => ['pha', 'nguy', 'be'],
            'require_hoang_dao' => true,
        ],
        'khai_truong' => [
            'label'             => 'Khai trương, Cầu tài',
            'prefer_truc'       => ['khai', 'thanh', 'man', 'thu'],
            'avoid_truc'        => ['pha', 'be', 'nguy'],
            'require_hoang_dao' => true,
        ],
        'ky_hop_dong' => [
            'label'       => 'Ký hợp đồng, Giao dịch',
            'prefer_truc' => ['thanh', 'khai', 'dinh', 'thu', 'man'],
            'avoid_truc'  => ['pha', 'be', 'chap', 'tru'],
        ],
        'dong_tho' => [
            'label'             => 'Động thổ, Xây cất',
            'prefer_truc'       => ['binh', 'dinh', 'thanh', 'khai'],
            'avoid_truc'        => ['kien', 'pha', 'nguy', 'be'],
            'require_hoang_dao' => true,
        ],
        'nhap_trach' => [
            'label'             => 'Nhập trạch, Về nhà mới',
            'prefer_truc'       => ['thanh', 'khai', 'dinh'],
            'avoid_truc'        => ['pha', 'be', 'nguy', 'chap'],
            'require_hoang_dao' => true,
        ],
        'xuat_hanh' => [
            'label'       => 'Xuất hành, Đi xa',
            'prefer_truc' => ['kien', 'khai', 'thanh'],
            'avoid_truc'  => ['be', 'pha', 'nguy'],
        ],
        'mua_xe' => [
            'label'       => 'Mua xe, Tài sản lớn',
            'prefer_truc' => ['man', 'thanh', 'khai'],
            'avoid_truc'  => ['pha', 'nguy', 'be'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 11. DIEM 12 TRUC THEO MUC DICH
    |--------------------------------------------------------------------------
    */
    'truc_rules' => [
        'Kien'  => ['score_by_purpose' => ['xuat_hanh'=>2,  'dong_tho'=>-3,'cuoi'=>0,  'khai_truong'=>1,  'ky_hop_dong'=>1,  'nhap_trach'=>-1,'mua_xe'=>0]],
        'Tru'   => ['score_by_purpose' => ['nhap_trach'=>-2,'ky_hop_dong'=>-2,'dong_tho'=>-2,'cuoi'=>-1,   'khai_truong'=>-1,'xuat_hanh'=>-1, 'mua_xe'=>-1]],
        'Man'   => ['score_by_purpose' => ['khai_truong'=>3,'cuoi'=>2,       'ky_hop_dong'=>2,'dong_tho'=>1,'nhap_trach'=>1, 'xuat_hanh'=>1,  'mua_xe'=>3]],
        'Binh'  => ['score_by_purpose' => ['dong_tho'=>2,  'cuoi'=>2,       'ky_hop_dong'=>1,'khai_truong'=>1,'nhap_trach'=>1,'xuat_hanh'=>0, 'mua_xe'=>1]],
        'Dinh'  => ['score_by_purpose' => ['nhap_trach'=>3,'dong_tho'=>2,   'cuoi'=>2,       'khai_truong'=>1,'ky_hop_dong'=>2,'xuat_hanh'=>1,'mua_xe'=>1]],
        'Chap'  => ['score_by_purpose' => ['dong_tho'=>1,  'ky_hop_dong'=>-2,'cuoi'=>0,      'khai_truong'=>0,'nhap_trach'=>-1,'xuat_hanh'=>0,'mua_xe'=>0]],
        'Pha'   => ['score_by_purpose' => ['cuoi'=>-3,     'khai_truong'=>-3,'dong_tho'=>-3, 'nhap_trach'=>-3,'ky_hop_dong'=>-2,'xuat_hanh'=>-2,'mua_xe'=>-3]],
        'Nguy'  => ['score_by_purpose' => ['cuoi'=>-3,     'xuat_hanh'=>-3, 'dong_tho'=>-3, 'khai_truong'=>-2,'nhap_trach'=>-2,'ky_hop_dong'=>-1,'mua_xe'=>-2]],
        'Thanh' => ['score_by_purpose' => ['cuoi'=>3,      'khai_truong'=>3,'nhap_trach'=>3,'dong_tho'=>3,   'ky_hop_dong'=>3, 'xuat_hanh'=>2, 'mua_xe'=>3]],
        'Thu'   => ['score_by_purpose' => ['ky_hop_dong'=>2,'mua_xe'=>2,    'xuat_hanh'=>-2,'cuoi'=>0,       'khai_truong'=>1, 'dong_tho'=>0,  'nhap_trach'=>0]],
        'Khai'  => ['score_by_purpose' => ['khai_truong'=>3,'nhap_trach'=>2,'dong_tho'=>2,  'cuoi'=>2,       'ky_hop_dong'=>2, 'xuat_hanh'=>2, 'mua_xe'=>2]],
        'Be'    => ['score_by_purpose' => ['khai_truong'=>-3,'cuoi'=>-3,    'dong_tho'=>-3, 'xuat_hanh'=>-3, 'nhap_trach'=>-2,'ky_hop_dong'=>-2,'mua_xe'=>-2]],
    ],

    /*
    |--------------------------------------------------------------------------
    | 12. CAT TINH & HUNG TINH NGAY
    |--------------------------------------------------------------------------
    */
    'sao_ngay_dong_cong' => [
        'tho_tu'   => [1=>'tuat',2=>'thin',3=>'hoi',4=>'ti',  5=>'ty',  6=>'ngo', 7=>'suu',8=>'mui',9=>'dan',10=>'than',11=>'mao',12=>'dau'],
        'sat_chu'  => [1=>'ti',  2=>'ty',  3=>'mui',4=>'mao', 5=>'than',6=>'tuat',7=>'hoi', 8=>'suu',9=>'ngo',10=>'dau', 11=>'dan',12=>'thin'],
        'thien_hy' => [1=>'tuat',2=>'hoi', 3=>'ty', 4=>'suu', 5=>'dan', 6=>'mao', 7=>'thin',8=>'ti', 9=>'ngo',10=>'mui', 11=>'than',12=>'dau'],
        'thien_ma_by_year_chi' => [
            'dan'=>'than',  'ngo'=>'than',  'tuat'=>'than',
            'than'=>'dan',  'ty'=>'dan',    'thin'=>'dan',
            'hoi'=>'ti',    'mao'=>'ti',    'mui'=>'ti',
            'ti'=>'hoi',    'dau'=>'hoi',   'suu'=>'hoi',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 13. NGAY KIENG KY CO DINH
    |--------------------------------------------------------------------------
    */
    'ngay_kiem_ky' => [
        'tam_nuong'  => ['03', '07', '13', '18', '22', '27'],
        'nguyet_ky'  => ['05', '14', '23'],
        'duong_cong' => ['01', '10', '20', '30'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 14. NHI THAP BAT TU (28 SAO)
    |--------------------------------------------------------------------------
    */
    'nhi_thap_bat_tu' => [
        0  => ['name'=>'Giác',  'type'=>'Tot', 'desc'=>'Rất tốt cho cưới hỏi, thi cử, nhậm chức.'],
        1  => ['name'=>'Cang',  'type'=>'Xau', 'desc'=>'Xấu mọi việc, đặc biệt kỵ xây cất, cưới hỏi.'],
        2  => ['name'=>'Đê',    'type'=>'Xau', 'desc'=>'Kỵ động thổ, khai trương, xuất hành.'],
        3  => ['name'=>'Phòng', 'type'=>'Tot', 'desc'=>'Đại kiết, khởi tạo trăm việc đều tốt.'],
        4  => ['name'=>'Tâm',   'type'=>'Xau', 'desc'=>'Xấu, kỵ thưa kiện, tranh chấp, cưới hỏi.'],
        5  => ['name'=>'Vĩ',    'type'=>'Tot', 'desc'=>'Tốt cho cưới hỏi, xây cất, khai trương.'],
        6  => ['name'=>'Cơ',    'type'=>'Tot', 'desc'=>'Tốt cho mua bán, xuất hành, thi cử.'],
        7  => ['name'=>'Đẩu',   'type'=>'Tot', 'desc'=>'Rất tốt để xây cất, đào ao, mở kho.'],
        8  => ['name'=>'Ngưu',  'type'=>'Xau', 'desc'=>'Rất kỵ cưới hỏi, xây cất.'],
        9  => ['name'=>'Nữ',    'type'=>'Xau', 'desc'=>'Kỵ tranh kiện, cưới hỏi, xuất hành.'],
        10 => ['name'=>'Hư',    'type'=>'Xau', 'desc'=>'Kỵ cưới hỏi, khai trương, đào đất.'],
        11 => ['name'=>'Nguy',  'type'=>'Xau', 'desc'=>'Xấu mọi việc, đi thuyền cẩn thận.'],
        12 => ['name'=>'Thất',  'type'=>'Tot', 'desc'=>'Khởi tạo, cưới hỏi, xuất hành đều tốt.'],
        13 => ['name'=>'Bích',  'type'=>'Tot', 'desc'=>'Tốt cho khai trương, cưới hỏi, xây cất.'],
        14 => ['name'=>'Khuê',  'type'=>'Xau', 'desc'=>'Kỵ khai trương, động thổ, cưới hỏi.'],
        15 => ['name'=>'Lâu',   'type'=>'Tot', 'desc'=>'Đại cát, trăm việc đều thành.'],
        16 => ['name'=>'Vị',    'type'=>'Tot', 'desc'=>'Tốt cho cưới hỏi, nhậm chức, xây cất.'],
        17 => ['name'=>'Mão',   'type'=>'Xau', 'desc'=>'Kỵ xây cất, cưới hỏi, xuất hành.'],
        18 => ['name'=>'Tất',   'type'=>'Tot', 'desc'=>'Tốt cho xuất hành, khai trương, cưới hỏi.'],
        19 => ['name'=>'Chủy',  'type'=>'Xau', 'desc'=>'Kỵ khai trương, nhậm chức, xuất hành.'],
        20 => ['name'=>'Sâm',   'type'=>'Tot', 'desc'=>'Tốt cho giao dịch, ký kết, cưới hỏi.'],
        21 => ['name'=>'Tỉnh',  'type'=>'Tot', 'desc'=>'Tốt cho khai trương, thi cử, xây nhà.'],
        22 => ['name'=>'Quỷ',   'type'=>'Xau', 'desc'=>'Rất kỵ cưới hỏi, xây cất. Chỉ tốt cho an táng.'],
        23 => ['name'=>'Liễu',  'type'=>'Xau', 'desc'=>'Xấu mọi việc, đặc biệt khai trương.'],
        24 => ['name'=>'Tinh',  'type'=>'Xau', 'desc'=>'Kỵ cưới hỏi, xây cất, xuất hành.'],
        25 => ['name'=>'Trương','type'=>'Tot', 'desc'=>'Tốt cho cưới hỏi, giao dịch, cầu tài.'],
        26 => ['name'=>'Dực',   'type'=>'Tot', 'desc'=>'Tốt cho mọi việc, đặc biệt đào giếng, xây cất.'],
        27 => ['name'=>'Chẩn',  'type'=>'Tot', 'desc'=>'Tốt cho xuất hành, cưới hỏi, nhậm chức.'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 15. GIO HOANG DAO THEO NGAY CHI
    |--------------------------------------------------------------------------
    */
    'gio_hoang_dao_theo_ngay_chi' => [
        'ty'   => ['ty',  'suu', 'mao', 'ngo', 'than', 'dau'],
        'ngo'  => ['ty',  'suu', 'mao', 'ngo', 'than', 'dau'],
        'suu'  => ['dan', 'mao', 'ti',  'than','tuat',  'hoi'],
        'mui'  => ['dan', 'mao', 'ti',  'than','tuat',  'hoi'],
        'dan'  => ['ty',  'suu', 'thin','ti',  'mui',   'tuat'],
        'than' => ['ty',  'suu', 'thin','ti',  'mui',   'tuat'],
        'mao'  => ['dan', 'mao', 'ngo', 'mui', 'dau',   'ty'],
        'dau'  => ['dan', 'mao', 'ngo', 'mui', 'dau',   'ty'],
        'thin' => ['dan', 'thin','ti',  'than','dau',   'hoi'],
        'tuat' => ['dan', 'thin','ti',  'than','dau',   'hoi'],
        'ti'   => ['suu', 'thin','ngo', 'mui', 'tuat',  'hoi'],
        'hoi'  => ['suu', 'thin','ngo', 'mui', 'tuat',  'hoi'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 16. HUONG XUAT HANH (HY THAN & TAI THAN theo Thien Can ngay)
    |--------------------------------------------------------------------------
    */
    'huong_xuat_hanh' => [
        'giap' => ['hy_than' => 'Đông Bắc',  'tai_than' => 'Đông Nam'],
        'ky'   => ['hy_than' => 'Đông Bắc',  'tai_than' => 'Đông Nam'],
        'at'   => ['hy_than' => 'Tây Bắc',   'tai_than' => 'Đông Nam'],
        'canh' => ['hy_than' => 'Tây Bắc',   'tai_than' => 'Tây Nam'],
        'binh' => ['hy_than' => 'Tây Nam',   'tai_than' => 'Chính Đông'],
        'tan'  => ['hy_than' => 'Tây Nam',   'tai_than' => 'Chính Đông'],
        'dinh' => ['hy_than' => 'Chính Nam', 'tai_than' => 'Chính Đông'],
        'nham' => ['hy_than' => 'Chính Nam', 'tai_than' => 'Chính Tây'],
        'mau'  => ['hy_than' => 'Đông Nam',  'tai_than' => 'Chính Bắc'],
        'quy'  => ['hy_than' => 'Đông Nam',  'tai_than' => 'Chính Tây'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 17. GIO DAI HUNG (SAT CHU & THO TU theo Dia Chi ngay)
    |--------------------------------------------------------------------------
    */
    'gio_dai_hung' => [
        'sat_chu' => [
            'ty'=>'mao',  'suu'=>'dan', 'dan'=>'mui', 'mao'=>'thin',
            'thin'=>'suu','ti'=>'than', 'ngo'=>'dau', 'mui'=>'tuat',
            'than'=>'hoi','dau'=>'ty',  'tuat'=>'ti', 'hoi'=>'ngo'
        ],
        'tho_tu' => [
            'ty'=>'suu',  'suu'=>'ngo', 'dan'=>'suu', 'mao'=>'ti',
            'thin'=>'ti', 'ti'=>'tuat', 'ngo'=>'mui', 'mui'=>'ngo',
            'than'=>'mao','dau'=>'ti',  'tuat'=>'mui','hoi'=>'ngo'
        ]
    ],
];