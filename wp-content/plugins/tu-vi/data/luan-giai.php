<?php
return [
    "meta" => [
        "schema_version" => "1.0-addon",
        "module" => "luan_giai",
        "purpose" => "Template va quy tac phan giai sao/cung cho tong quan va cac cung chinh",
    ],

    "palace_templates" => [
        "menh" => [
            "label" => "Mệnh",
            "focus" => ["tinh_cach", "bản_ngã", "cách_hành_động"],
            "questions" => ["Bạn là người như thế nào?", "Điểm mạnh lõi là gì?", "Điểm yếu lặp lại là gì?"],
        ],
        "than" => [
            "label" => "Thân",
            "focus" => ["diễn_biến_sau_nay", "môi_trường", "vai_trò_xã_hội"],
            "questions" => ["Về sau phát triển theo hướng nào?", "Cơ hội đến từ đâu?"],
        ],
        "quan_loc" => [
            "label" => "Quan Lộc",
            "focus" => ["sự_nghiệp", "chức_vụ", "đường_thăng_tiến"],
            "questions" => ["Hợp nghề gì?", "Công việc nào dễ bật lên?"],
        ],
        "tai_bach" => [
            "label" => "Tài Bạch",
            "focus" => ["tiền_bạc", "dòng_tiền", "cách_tích_lũy"],
            "questions" => ["Kiếm tiền kiểu nào?", "Tiền đến rồi giữ được không?"],
        ],
        "phu_the" => [
            "label" => "Phu Thê",
            "focus" => ["tình_duyên", "hôn_nhân", "cách_đồng_hành"],
            "questions" => ["Mẫu người phù hợp?", "Cơ chế yêu và cưới?"],
        ],
        "phuc_duc" => [
            "label" => "Phúc Đức",
            "focus" => ["nền_tảng_gia_đình", "tinh_thần", "độ_bền"],
            "questions" => ["Nền gốc gia đạo ra sao?", "Khả năng hồi phục thế nào?"],
        ],
        "tat_ach" => [
            "label" => "Tật Ách",
            "focus" => ["sức_khỏe", "stress", "điểm_đứt_gãy"],
            "questions" => ["Dễ mệt ở đâu?", "Nguy cơ nào cần tránh?"],
        ],
    ],

    "star_quality" => [
        "cat" => ["tu_vi", "thien_phu", "thien_tuong", "thien_luong", "van_xuong", "van_khuc", "ta_phu", "huu_bat", "thien_khoi", "thien_viet", "loc_ton", "hoa_loc", "hoa_quyen", "hoa_khoa"],
        "trung_tinh" => ["thien_co", "thai_am", "thai_duong", "vu_khuc", "thien_dong", "cu_mon", "linh_tinh", "hoa_tinh", "thien_ma"],
        "hung" => ["that_sat", "pha_quan", "kinh_duong", "da_la", "dia_khong", "dia_kiep", "hoa_ky"],
    ],

    "combo_rules" => [
        "tu_vi_phu" => [
            "positive" => ["quyen_the", "to_chuc", "vi_the"],
            "negative" => ["cung_nhac", "qua_tu_ton"],
        ],
        "co_nguyet_dong_luong" => [
            "positive" => ["phuc_khi", "tri_thuc", "bao_tro"],
            "negative" => ["chau_au", "cam_xuc_hoa"],
        ],
        "sat_pha_liem_tham" => [
            "positive" => ["doi_moi", "quyet_doan", "canh_tranh"],
            "negative" => ["bao_dong", "dong_luc_cuc_doan"],
        ],
        "cu_nhat" => [
            "positive" => ["ly_tri", "tu_duy", "nghien_cuu"],
            "negative" => ["co_lap", "thit_phi"],
        ],
    ],

    "output_style" => [
        "short_summary" => true,
        "bullet_points" => true,
        "score_explanation" => true,
        "risk_warning_if_low_confidence" => true,
    ],

    "keywords_to_text" => [
        "tinh_cach" => [
            "linh_hoat" => "Linh hoạt, thích nghi nhanh.",
            "ky_luat" => "Kỷ luật, bền bỉ, có nhịp làm việc rõ.",
            "sang_tao" => "Thiên về ý tưởng và cách làm mới.",
        ],
        "su_nghiep" => [
            "quan_he" => "Mạnh về quan hệ, phối hợp, kết nối.",
            "quan_ly" => "Hợp vai trò quản lý, điều phối.",
            "chuyen_mon" => "Hợp nghề đòi hỏi chuyên môn sâu.",
        ],
        "tai_bach" => [
            "thu_nhap_on_dinh" => "Tiền đến đều, hợp tích lũy dài hạn.",
            "co_coi_song" => "Dòng tiền biến động, có cơ hội nhưng phải kiểm soát.",
            "giu_tien" => "Kiếm tốt nhưng cần kỷ luật chi tiêu.",
        ],
        "phu_the" => [
            "on_dinh" => "Thiên về gắn bó và bền.",
            "cam_xuc_manh" => "Tình cảm mạnh, cần học cách hòa nhịp.",
            "muon_muon" => "Duyên đến chậm, cần kiên nhẫn.",
        ],
    ],

    "notes" => [
        "Day la lop data mau cho engine luan giai noi dung.",
        "Co the tiep tuc mo rong bang cac template cho tung cung va tung cap sao.",
    ],
];
