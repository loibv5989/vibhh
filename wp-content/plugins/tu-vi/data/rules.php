<?php

return [
    "an_sao_rules" => [
        "an_menh" => [
            "thuận" => "Dương nam, âm nữ thường an thuận",
            "nghich" => "Âm nam, dương nữ thường an nghịch",
            "note" => "Một số phái có biến thể theo giờ, tháng và cục, nên lưu rule theo cấu hình.",
        ],

        "triet_khong" => [
            "giap" => ["than", "dau"], "ky"   => ["than", "dau"],
            "at"   => ["ngo", "mui"],  "canh" => ["ngo", "mui"],
            "binh" => ["thin", "ti"],  "tan"  => ["thin", "ti"],
            "dinh" => ["dan", "mao"],  "nham" => ["dan", "mao"],
            "mau"  => ["ty", "suu"],   "quy"  => ["ty", "suu"],
        ],
        "tuan_khong" => [
            "giap_ty"   => ["tuat", "hoi"],
            "giap_tuat" => ["than", "dau"],
            "giap_than" => ["ngo", "mui"],
            "giap_ngo"  => ["thin", "ti"],
            "giap_thin" => ["dan", "mao"],
            "giap_dan"  => ["ty", "suu"],
        ],

        "an_menh_than" => [
            "khoi_diem" => 3,
            "menh" => ["thang" => 1, "gio" => -1],
            "than" => ["thang" => 1, "gio" => 1],
        ],

        "ngu_ho_don" => [
            "giap" => "binh", "ky"   => "binh", "at"   => "mau", "canh" => "mau",
            "binh" => "canh", "tan"  => "canh", "dinh" => "nham", "nham" => "nham",
            "mau"  => "giap", "quy"  => "giap",
        ],

        "tinh_cuc_ngu_hanh" => [
            "can_index" => [
                "giap" => 1, "at" => 1, "binh" => 2, "dinh" => 2, "mau" => 3,
                "ky" => 3, "canh" => 4, "tan" => 4, "nham" => 5, "quy" => 5,
            ],
            "chi_index" => [
                "ty" => 0, "suu" => 0, "ngo" => 0, "mui" => 0,
                "dan" => 1, "mao" => 1, "than" => 1, "dau" => 1,
                "thin" => 2, "ti" => 2, "tuat" => 2, "hoi" => 2,
            ],
            "cuc_mapping" => [
                1 => 4, // Kim Tứ
                2 => 2, // Thủy Nhị
                3 => 6, // Hỏa Lục
                4 => 5, // Thổ Ngũ
                5 => 3, // Mộc Tam
            ],
        ],

        "an_chinh_tinh" => [
            "chom_tu_vi_offset" => [
                "thien_co" => -1, "thai_duong" => -3, "vu_khuc" => -4, "thien_dong" => -5, "liem_trinh" => -8,
            ],
            "thien_phu_axis" => 18,
            "chom_thien_phu_offset" => [
                "thai_am" => 1, "tham_lang" => 2, "cu_mon" => 3, "thien_tuong" => 4,
                "thien_luong" => 5, "that_sat" => 6, "pha_quan" => 10,
            ],
        ],

        "an_tu_vi" => [
            2 => [1=>2, 2=>3, 3=>3, 4=>4, 5=>4, 6=>5, 7=>5, 8=>6, 9=>6, 10=>7, 11=>7, 12=>8, 13=>8, 14=>9, 15=>9, 16=>10, 17=>10, 18=>11, 19=>11, 20=>12, 21=>12, 22=>1, 23=>1, 24=>2, 25=>2, 26=>3, 27=>3, 28=>4, 29=>4, 30=>5],
            3 => [1=>5, 2=>2, 3=>3, 4=>6, 5=>3, 6=>4, 7=>7, 8=>4, 9=>5, 10=>8, 11=>5, 12=>6, 13=>9, 14=>6, 15=>7, 16=>10, 17=>7, 18=>8, 19=>11, 20=>8, 21=>9, 22=>12, 23=>9, 24=>10, 25=>1, 26=>10, 27=>11, 28=>2, 29=>11, 30=>12],
            4 => [1=>12, 2=>5, 3=>2, 4=>3, 5=>1, 6=>6, 7=>3, 8=>4, 9=>2, 10=>7, 11=>4, 12=>5, 13=>3, 14=>8, 15=>5, 16=>6, 17=>4, 18=>9, 19=>6, 20=>7, 21=>5, 22=>10, 23=>7, 24=>8, 25=>6, 26=>11, 27=>8, 28=>9, 29=>7, 30=>12],
            5 => [1=>7, 2=>12, 3=>5, 4=>2, 5=>3, 6=>8, 7=>1, 8=>6, 9=>3, 10=>4, 11=>9, 12=>2, 13=>7, 14=>4, 15=>5, 16=>10, 17=>3, 18=>8, 19=>5, 20=>6, 21=>11, 22=>4, 23=>9, 24=>6, 25=>7, 26=>12, 27=>5, 28=>10, 29=>7, 30=>8],
            6 => [1=>10, 2=>7, 3=>12, 4=>5, 5=>2, 6=>3, 7=>11, 8=>8, 9=>1, 10=>6, 11=>3, 12=>4, 13=>12, 14=>9, 15=>2, 16=>7, 17=>4, 18=>5, 19=>1, 20=>10, 21=>3, 22=>8, 23=>5, 24=>6, 25=>2, 26=>11, 27=>4, 28=>9, 29=>6, 30=>7],
        ],

        "an_sao_mapping" => [
            "theo_thang" => [
                "ta_phu"  => [5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3, 4],
                "huu_bat" => [11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 12],
            ],
            "theo_gio" => [
                "van_xuong" => [11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 12],
                "van_khuc"  => [5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3, 4],
                "dia_khong" => [12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
                "dia_kiep"  => [12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            "theo_can_nam" => [
                "thien_khoi" => [2, 1, 12, 10, 2, 1, 2, 7, 4, 4],
                "thien_viet" => [8, 9, 10, 12, 8, 9, 8, 3, 6, 6],
            ],
        ],

        "bang_tinh_khac" => [
            "hoa_linh" => [
                "dan_ngo_tuat" => ["hoa" => "suu", "linh" => "mao"],
                "than_ty_thin" => ["hoa" => "dan", "linh" => "tuat"],
                "ti_dau_suu"   => ["hoa" => "mao", "linh" => "tuat"],
                "hoi_mao_mui"  => ["hoa" => "dau", "linh" => "tuat"],
            ],
            "luu_ha" => [
                "giap" => "dau", "at" => "tuat", "binh" => "mui", "dinh" => "than", "mau" => "ti",
                "ky" => "ngo", "canh" => "thin", "tan" => "mao", "nham" => "hoi", "quy" => "dan"
            ],
            "thien_quan" => [
                "giap" => "mui", "at" => "thin", "binh" => "ti", "dinh" => "dan", "mau" => "mao",
                "ky" => "dau", "canh" => "hoi", "tan" => "dau", "nham" => "tuat", "quy" => "ngo"
            ],
            "thien_phuc" => [
                "giap" => "dau", "at" => "than", "binh" => "ty", "dinh" => "hoi", "mau" => "mao",
                "ky" => "dan", "canh" => "ngo", "tan" => "ti", "nham" => "ngo", "quy" => "ti"
            ],
            "co_qua" => [
                "dan_mao_thin" => ["co_than" => "ti", "qua_tu" => "suu"],
                "ti_ngo_mui"   => ["co_than" => "than", "qua_tu" => "thin"],
                "than_dau_tuat"=> ["co_than" => "hoi", "qua_tu" => "mui"],
                "hoi_ty_suu"   => ["co_than" => "dan", "qua_tu" => "tuat"],
            ],
            "pha_toai" => [
                "ty_ngo_mao_dau"     => "ti",
                "dan_than_ti_hoi"    => "dau",
                "thin_tuat_suu_mui"  => "suu"
            ]
        ],

        "vong_sao" => [
            "vong_thai_tue" => [
                "thai_tue", "thieu_duong", "tang_mon", "thieu_am", "quan_phu2", "tu_phu",
                "tue_pha", "long_duc", "bach_ho", "phuc_duc", "dieu_khach", "truc_phu"
            ],
            "vong_loc_ton" => [
                "bac_si", "luc_si", "thanh_long", "tieu_hao", "tuong_quan", "tau_thu",
                "phi_liem", "hy_than", "benh_phu", "dai_hao", "phuc_binh", "quan_phu"
            ],
            "vong_trang_sinh" => [
                "truong_sinh", "moc_duc", "quan_doi", "lam_quan", "de_vuong", "suy",
                "benh", "tu", "mo", "tuyet", "thai", "duong"
            ],
        ],

        "loc_ton_by_can" => [
            "giap" => "dan", "at" => "mao", "binh" => "ti", "dinh" => "ngo", "mau" => "ti",
            "ky" => "ngo", "canh" => "than", "tan" => "dau", "nham" => "hoi", "quy" => "ty",
        ],
        "hoa_by_can" => [
            "giap" => ["loc" => "liem_trinh", "quyen" => "pha_quan", "khoa" => "vu_khuc", "ky" => "thai_duong"],
            "at"   => ["loc" => "thien_co", "quyen" => "thien_luong", "khoa" => "tu_vi", "ky" => "thai_am"],
            "binh" => ["loc" => "thien_dong", "quyen" => "thien_co", "khoa" => "van_xuong", "ky" => "liem_trinh"],
            "dinh" => ["loc" => "thai_am", "quyen" => "thien_dong", "khoa" => "thien_co", "ky" => "cu_mon"],
            "mau"  => ["loc" => "tham_lang", "quyen" => "thai_am", "khoa" => "huu_bat", "ky" => "thien_co"],
            "ky"   => ["loc" => "vu_khuc", "quyen" => "tham_lang", "khoa" => "thien_luong", "ky" => "van_khuc"],
            "canh" => ["loc" => "thai_duong", "quyen" => "vu_khuc", "khoa" => "thai_am", "ky" => "thien_dong"],
            "tan"  => ["loc" => "cu_mon", "quyen" => "thai_duong", "khoa" => "van_khuc", "ky" => "van_xuong"],
            "nham" => ["loc" => "thien_luong", "quyen" => "tu_vi", "khoa" => "ta_phu", "ky" => "vu_khuc"],
            "quy"  => ["loc" => "pha_quan", "quyen" => "cu_mon", "khoa" => "thien_co", "ky" => "thai_duong"],
        ],
        "thien_ma_by_tam_hop" => [
            "than_ty_than" => ["group" => ["dan", "ngo", "tuat"], "ma" => "than"],
            "ty_than_thin" => ["group" => ["than", "ty", "thin"], "ma" => "dan"],
            "hoi_mao_mui"  => ["group" => ["hoi", "mao", "mui"], "ma" => "ti"],
            "ti_dau_suu"   => ["group" => ["ti", "dau", "suu"], "ma" => "hoi"],
        ],
        "dao_hoa_by_nam_chi_group" => [
            "dan_ngo_tuat" => ["dao_hoa" => "mao", "hong_loan" => "dau", "thien_hy" => "thin"],
            "than_ty_thin" => ["dao_hoa" => "dau", "hong_loan" => "mao", "thien_hy" => "tuat"],
            "hoi_mao_mui"  => ["dao_hoa" => "ty", "hong_loan" => "hoi", "thien_hy" => "suu"],
            "ti_dau_suu"   => ["dao_hoa" => "ngo", "hong_loan" => "suu", "thien_hy" => "ty"],
        ],
    ],
];