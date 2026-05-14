<?php
if (!defined('ABSPATH')) exit;

return [
    'meta' => [
        'schema_version' => '1.0-addon',
        'module'         => 'hop_tuoi',
        'purpose'        => 'Du lieu so sanh 2 nguoi theo can, chi, ngu hanh, nap am va cung menh',
    ],

    // Trọng số tổng quát nếu bạn muốn quy đổi về điểm cuối
    'thang_diem' => [
        'ngu_hanh' => 3,
        'thien_can' => 2,
        'dia_chi'   => 2,
        'cung_menh' => 3,
    ],

    // Thang phân loại tổng điểm 100
    'score_bands' => [
        ['min' => 0,  'max' => 24,  'label' => 'Rất xung'],
        ['min' => 25, 'max' => 44,  'label' => 'Xung nhẹ'],
        ['min' => 45, 'max' => 64,  'label' => 'Trung bình'],
        ['min' => 65, 'max' => 84,  'label' => 'Khá hợp'],
        ['min' => 85, 'max' => 100, 'label' => 'Rất hợp'],
    ],

    // 1. NGŨ HÀNH TƯƠNG SINH TƯƠNG KHẮC (Luận Mệnh Nạp Âm)
    'ngu_hanh_quan_he' => [
        'sinh_nhap' => [
            'score' => 3,
            'desc'  => 'Tương Sinh (Đại Cát) - Nuôi dưỡng, hỗ trợ nhau phát triển.',
        ],
        'sinh_xuat' => [
            'score' => 2,
            'desc'  => 'Tương Sinh (Bình) - Có sự hỗ trợ nhưng một bên phải hy sinh nhiều hơn.',
        ],
        'ty_hoa' => [
            'score' => 1.5,
            'desc'  => 'Tỷ Hòa (Bình Hòa) - Đồng lứa, bình đẳng, không sinh không khắc.',
        ],
        'khac_xuat' => [
            'score' => 0.5,
            'desc'  => 'Tương Khắc (Tiểu Hung) - Chế ngự được đối phương, gia đạo hay cãi vã nhưng không vỡ.',
        ],
        'khac_nhap' => [
            'score' => 0,
            'desc'  => 'Tương Khắc (Đại Hung) - Khắc phạt mạnh, cản trở tài lộc và sức khỏe.',
        ],
    ],


    // 2. THIÊN CAN TƯƠNG TÁC
    'can_quan_he' => [
        'hop' => [
            'giap_ky', 'ky_giap', 'at_canh', 'canh_at', 'binh_tan', 'tan_binh',
            'dinh_nham', 'nham_dinh', 'mau_quy', 'quy_mau',
        ],
        'pha' => [
            'giap_mau', 'mau_giap', 'at_ky', 'ky_at', 'binh_canh', 'canh_binh',
            'dinh_tan', 'tan_dinh', 'mau_nham', 'nham_mau', 'ky_quy', 'quy_ky',
            'canh_giap', 'giap_canh', 'tan_at', 'at_tan', 'nham_binh', 'binh_nham',
            'quy_dinh', 'dinh_quy',
        ],
    ],


    // 3. ĐỊA CHI TƯƠNG TÁC
    'chi_quan_he' => [
        'hop' => [
            'ty_suu', 'suu_ty', 'dan_hoi', 'hoi_dan', 'mao_tuat', 'tuat_mao', 'thin_dau', 'dau_thin',
            'ti_than', 'than_ti', 'ngo_mui', 'mui_ngo', // Lục hợp
            'than_ty', 'ty_than', 'ty_thin', 'thin_ty', 'than_thin', 'thin_than', // Thân - Tý - Thìn
            'dan_ngo', 'ngo_dan', 'ngo_tuat', 'tuat_ngo', 'dan_tuat', 'tuat_dan', // Dần - Ngọ - Tuất
            'hoi_mao', 'mao_hoi', 'mao_mui', 'mui_mao', 'hoi_mui', 'mui_hoi', // Hợi - Mão - Mùi
            'ti_dau', 'dau_ti', 'dau_suu', 'suu_dau', 'ti_suu', 'suu_ti', // Tỵ - Dậu - Sửu
        ],
        'xung_hai' => [
            'ty_ngo', 'ngo_ty', 'suu_mui', 'mui_suu', 'dan_than', 'than_dan', 'mao_dau', 'dau_mao',
            'thin_tuat', 'tuat_thin', 'ti_hoi', 'hoi_ti', // Lục xung
            'ty_mui', 'mui_ty', 'suu_ngo', 'ngo_suu', 'dan_ti', 'ti_dan', 'mao_thin', 'thin_mao',
            'than_hoi', 'hoi_than', 'dau_tuat', 'tuat_dau', // Lục hại
        ],
    ],

    // 4. BÁT TRẠCH CUNG PHI (Ma trận 8x8)
    'cung_phi_ma_tran' => [
        'Càn'  => ['Càn' => 'Phục Vị', 'Khảm' => 'Lục Sát', 'Cấn' => 'Thiên Y', 'Chấn' => 'Ngũ Quỷ', 'Tốn' => 'Họa Hại', 'Ly' => 'Tuyệt Mệnh', 'Khôn' => 'Diên Niên', 'Đoài' => 'Sinh Khí'],
        'Khảm' => ['Càn' => 'Lục Sát', 'Khảm' => 'Phục Vị', 'Cấn' => 'Ngũ Quỷ', 'Chấn' => 'Thiên Y', 'Tốn' => 'Sinh Khí', 'Ly' => 'Diên Niên', 'Khôn' => 'Tuyệt Mệnh', 'Đoài' => 'Họa Hại'],
        'Cấn'  => ['Càn' => 'Thiên Y', 'Khảm' => 'Ngũ Quỷ', 'Cấn' => 'Phục Vị', 'Chấn' => 'Lục Sát', 'Tốn' => 'Tuyệt Mệnh', 'Ly' => 'Họa Hại', 'Khôn' => 'Sinh Khí', 'Đoài' => 'Diên Niên'],
        'Chấn' => ['Càn' => 'Ngũ Quỷ', 'Khảm' => 'Thiên Y', 'Cấn' => 'Lục Sát', 'Chấn' => 'Phục Vị', 'Tốn' => 'Diên Niên', 'Ly' => 'Sinh Khí', 'Khôn' => 'Họa Hại', 'Đoài' => 'Tuyệt Mệnh'],
        'Tốn'  => ['Càn' => 'Họa Hại', 'Khảm' => 'Sinh Khí', 'Cấn' => 'Tuyệt Mệnh', 'Chấn' => 'Diên Niên', 'Tốn' => 'Phục Vị', 'Ly' => 'Thiên Y', 'Khôn' => 'Ngũ Quỷ', 'Đoài' => 'Lục Sát'],
        'Ly'   => ['Càn' => 'Tuyệt Mệnh', 'Khảm' => 'Diên Niên', 'Cấn' => 'Họa Hại', 'Chấn' => 'Sinh Khí', 'Tốn' => 'Thiên Y', 'Ly' => 'Phục Vị', 'Khôn' => 'Lục Sát', 'Đoài' => 'Ngũ Quỷ'],
        'Khôn' => ['Càn' => 'Diên Niên', 'Khảm' => 'Tuyệt Mệnh', 'Cấn' => 'Sinh Khí', 'Chấn' => 'Họa Hại', 'Tốn' => 'Ngũ Quỷ', 'Ly' => 'Lục Sát', 'Khôn' => 'Phục Vị', 'Đoài' => 'Thiên Y'],
        'Đoài' => ['Càn' => 'Sinh Khí', 'Khảm' => 'Họa Hại', 'Cấn' => 'Diên Niên', 'Chấn' => 'Tuyệt Mệnh', 'Tốn' => 'Lục Sát', 'Ly' => 'Ngũ Quỷ', 'Khôn' => 'Thiên Y', 'Đoài' => 'Phục Vị'],
    ],

    // Giải nghĩa Bát Trạch & Điểm
    'cung_phi_y_nghia' => [
        'Sinh Khí'   => ['score' => 3,   'type' => 'Đại Cát',  'desc' => 'Thu hút tài lộc, danh tiếng, thăng quan phát tài.'],
        'Diên Niên'  => ['score' => 2.5, 'type' => 'Cát',      'desc' => 'Gia đạo êm ấm, các mối quan hệ bền chặt, hòa thuận.'],
        'Thiên Y'    => ['score' => 2,   'type' => 'Cát',      'desc' => 'Cải thiện sức khỏe, trường thọ, có quý nhân phù trợ.'],
        'Phục Vị'    => ['score' => 1.5, 'type' => 'Cát',      'desc' => 'Bình yên, củng cố sức mạnh tinh thần, may mắn trong thi cử.'],
        'Họa Hại'    => ['score' => 0.5, 'type' => 'Hung',     'desc' => 'Dễ gặp thị phi, thất bại, sóng gió nhỏ.'],
        'Lục Sát'    => ['score' => 0,   'type' => 'Hung',     'desc' => 'Sát khí, trục trặc tình cảm, tai nạn nước, vướng pháp lý.'],
        'Ngũ Quỷ'    => ['score' => 0,   'type' => 'Đại Hung', 'desc' => 'Mất nguồn thu nhập, cãi vã triền miên, tai họa ập đến.'],
        'Tuyệt Mệnh' => ['score' => 0,   'type' => 'Đại Hung', 'desc' => 'Phá sản, bệnh tật chết người, đường con cái khó khăn.'],
    ],

    // 5. NẠP ÂM NHÓM SCORE
    'nap_am_group_score' => [
        'same_element'  => 10,
        'generated'     => 7,
        'generated_by'  => 5,
        'controlled'    => -6,
        'controlled_by' => -4,
    ],

    // 6. MẪU KẾT LUẬN
    'relationship_templates' => [
        'tot' => [
            'label'   => 'Hợp',
            'summary' => 'Dễ đồng thuận, hỗ trợ nhau, ít xung đột lớn.',
        ],
        'can_bang' => [
            'label'   => 'Cân bằng',
            'summary' => 'Có điểm hợp và điểm khắc, cần quy ước rõ ràng.',
        ],
        'can_nhac' => [
            'label'   => 'Cần cân nhắc',
            'summary' => 'Có lực hút nhưng phải xử lý khác biệt chủ đạo.',
        ],
        'xung' => [
            'label'   => 'Xung mạnh',
            'summary' => 'Dễ va chạm, nên xem thêm mệnh và mục tiêu chung.',
        ],
    ],

    'notes' => [
        'Module nay co the dung cho SEO hop tuoi va API tinh diem nhanh.',
        'Diem so co the dieu chinh theo phien ban luan giai va quy tac noi bo.',
        'Neu can, co the bo sung them mapping ngay sinh -> can chi -> ngu hanh -> cung menh.',
    ],
];