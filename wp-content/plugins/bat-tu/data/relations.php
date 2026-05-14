<?php
if (!defined('ABSPATH')) exit;

return [
    'can_hop' => [
        'giap_ky'   => ['result' => 'tho', 'month_req' => ['thin', 'tuat', 'suu', 'mui', 'ti', 'ngo']],
        'at_canh'   => ['result' => 'kim', 'month_req' => ['than', 'dau', 'thin', 'tuat', 'suu', 'mui']],
        'binh_tan'  => ['result' => 'thuy', 'month_req' => ['hoi', 'ty', 'than', 'dau', 'thin']],
        'dinh_nham' => ['result' => 'moc', 'month_req' => ['dan', 'mao', 'hoi', 'ty']],
        'mau_quy'   => ['result' => 'hoa', 'month_req' => ['ti', 'ngo', 'dan', 'mao']],
    ],
    'can_xung' => [
        'giap_canh',
        'at_tan',
        'binh_nham',
        'dinh_quy',
    ],
    'chi_luc_hop' => [
        'ty_suu' => 'tho',
        'dan_hoi' => 'moc',
        'mao_tuat' => 'hoa',
        'thin_dau' => 'kim',
        'ti_than' => 'thuy',
        'ngo_mui' => 'tho',
    ],
    'chi_tam_hop' => [
        'dan_ngo_tuat' => 'hoa',
        'than_ty_thin' => 'thuy',
        'hoi_mao_mui' => 'moc',
        'ti_dau_suu' => 'kim',
    ],
    'chi_tam_hoi' => [
        'dan_mao_thin' => 'moc',
        'ti_ngo_mui' => 'hoa',
        'than_dau_tuat' => 'kim',
        'hoi_ty_suu' => 'thuy',
    ],
    'chi_luc_xung' => [
        'ty_ngo',
        'suu_mui',
        'dan_than',
        'mao_dau',
        'thin_tuat',
        'ti_hoi',
    ],
    'chi_tuong_hinh' => [
        'tam_hinh_tri_the' => [
            'dan',
            'ti',
            'than',
        ],
        'tam_hinh_vo_an' => [
            'suu',
            'tuat',
            'mui',
        ],
        'nhi_hinh_vo_le' => [
            'ty',
            'mao',
        ],
        'tu_hinh' => [
            'thin',
            'ngo',
            'dau',
            'hoi',
        ],
    ],
    'chi_tuong_hai' => [
        'ty_mui',
        'suu_ngo',
        'dan_ti',
        'mao_thin',
        'than_hoi',
        'dau_tuat',
    ],
    'chi_tuong_pha' => [
        'ty_dau',
        'mao_ngo',
        'thin_suu',
        'tuat_mui',
        'dan_hoi',
        'ti_than',
    ],
    'luc_hop_nhat' => [
        'ty' => 'suu',
        'dan' => 'hoi',
        'mao' => 'tuat',
        'thin' => 'dau',
        'ti' => 'than',
        'ngo' => 'mui',
    ],
    'chi_ban_hop' => [
        'dan_ngo'  => 'hoa',
        'than_ty'  => 'thuy',
        'hoi_mao'  => 'moc',
        'ti_dau'   => 'kim',
        'ngo_tuat' => 'hoa',
        'ty_thin'  => 'thuy',
        'mao_mui'  => 'moc',
        'dau_suu'  => 'kim',
    ],
];
