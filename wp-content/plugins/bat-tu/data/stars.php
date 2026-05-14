<?php
if (!defined('ABSPATH')) exit;

return [
    'thap_than' => [
        'ty_kien' => [
            'id' => 'ty_kien',
            'name' => 'Tỷ Kiên',
            'type' => 'same_element',
            'polarity' => 'same',
        ],
        'kiep_tai' => [
            'id' => 'kiep_tai',
            'name' => 'Kiếp Tài',
            'type' => 'same_element',
            'polarity' => 'diff',
        ],
        'thuc_than' => [
            'id' => 'thuc_than',
            'name' => 'Thực Thần',
            'type' => 'generate',
            'polarity' => 'same',
        ],
        'thuong_quan' => [
            'id' => 'thuong_quan',
            'name' => 'Thương Quan',
            'type' => 'generate',
            'polarity' => 'diff',
        ],
        'thien_tai' => [
            'id' => 'thien_tai',
            'name' => 'Thiên Tài',
            'type' => 'control',
            'polarity' => 'same',
        ],
        'chinh_tai' => [
            'id' => 'chinh_tai',
            'name' => 'Chính Tài',
            'type' => 'control',
            'polarity' => 'diff',
        ],
        'that_sat' => [
            'id' => 'that_sat',
            'name' => 'Thất Sát',
            'type' => 'controlled_by',
            'polarity' => 'same',
        ],
        'chinh_quan' => [
            'id' => 'chinh_quan',
            'name' => 'Chính Quan',
            'type' => 'controlled_by',
            'polarity' => 'diff',
        ],
        'thien_an' => [
            'id' => 'thien_an',
            'name' => 'Thiên Ấn',
            'type' => 'generated_by',
            'polarity' => 'same',
        ],
        'chinh_an' => [
            'id' => 'chinh_an',
            'name' => 'Chính Ấn',
            'type' => 'generated_by',
            'polarity' => 'diff',
        ],
    ],
    'than_sat' => [
        'thien_at_quy_nhan' => [
            'name' => 'Thiên Ất Quý Nhân',
            'base' => 'can_ngay_nam',
            'map' => [
                'giap' => [
                    'suu',
                    'mui',
                ],
                'mau' => [
                    'suu',
                    'mui',
                ],
                'canh' => [
                    'suu',
                    'mui',
                ],
                'at' => [
                    'ty',
                    'than',
                ],
                'ky' => [
                    'ty',
                    'than',
                ],
                'binh' => [
                    'dau',
                    'hoi',
                ],
                'dinh' => [
                    'dau',
                    'hoi',
                ],
                'nham' => [
                    'mao',
                    'ti',
                ],
                'quy' => [
                    'mao',
                    'ti',
                ],
                'tan' => [
                    'dan',
                    'ngo',
                ],
            ],
        ],
        'loc_than' => [
            'name' => 'Lộc Thần',
            'base' => 'can_ngay',
            'map' => [
                'giap' => 'dan',
                'at' => 'mao',
                'binh' => 'ti',
                'dinh' => 'ngo',
                'mau' => 'ti',
                'ky' => 'ngo',
                'canh' => 'than',
                'tan' => 'dau',
                'nham' => 'hoi',
                'quy' => 'ty',
            ],
        ],
        'kinh_duong' => [
            'name' => 'Kình Dương',
            'base' => 'can_ngay',
            'map' => [
                'giap' => 'mao',
                'at' => 'thin',
                'binh' => 'ngo',
                'dinh' => 'mui',
                'mau' => 'ngo',
                'ky' => 'mui',
                'canh' => 'dau',
                'tan' => 'tuat',
                'nham' => 'ty',
                'quy' => 'suu',
            ],
        ],
        'dich_ma' => [
            'name' => 'Dịch Mã',
            'base' => 'chi_ngay_nam',
            'map' => [
                'than' => 'dan',
                'ty' => 'dan',
                'thin' => 'dan',
                'dan' => 'than',
                'ngo' => 'than',
                'tuat' => 'than',
                'ti' => 'hoi',
                'dau' => 'hoi',
                'suu' => 'hoi',
                'hoi' => 'ti',
                'mao' => 'ti',
                'mui' => 'ti',
            ],
        ],
        'dao_hoa' => [
            'name' => 'Đào Hoa',
            'base' => 'chi_ngay_nam',
            'map' => [
                'than' => 'dau',
                'ty' => 'dau',
                'thin' => 'dau',
                'dan' => 'mao',
                'ngo' => 'mao',
                'tuat' => 'mao',
                'ti' => 'ngo',
                'dau' => 'ngo',
                'suu' => 'ngo',
                'hoi' => 'ty',
                'mao' => 'ty',
                'mui' => 'ty',
            ],
        ],
        'hoa_cai' => [
            'name' => 'Hoa Cái',
            'base' => 'chi_ngay_nam',
            'map' => [
                'dan' => 'tuat',
                'ngo' => 'tuat',
                'tuat' => 'tuat',
                'than' => 'thin',
                'ty' => 'thin',
                'thin' => 'thin',
                'ti' => 'suu',
                'dau' => 'suu',
                'suu' => 'suu',
                'hoi' => 'mui',
                'mao' => 'mui',
                'mui' => 'mui',
            ],
        ],
        'khong_vong' => [
            'name' => 'Không Vong',
            'base' => 'can_chi_ngay_nam',
            'map' => [
                'giap_ty' => [
                    'tuat',
                    'hoi',
                ],
                'at_suu' => [
                    'tuat',
                    'hoi',
                ],
                'binh_dan' => [
                    'tuat',
                    'hoi',
                ],
                'dinh_mao' => [
                    'tuat',
                    'hoi',
                ],
                'mau_thin' => [
                    'tuat',
                    'hoi',
                ],
                'ky_ti' => [
                    'tuat',
                    'hoi',
                ],
                'canh_ngo' => [
                    'tuat',
                    'hoi',
                ],
                'tan_mui' => [
                    'tuat',
                    'hoi',
                ],
                'nham_than' => [
                    'tuat',
                    'hoi',
                ],
                'quy_dau' => [
                    'tuat',
                    'hoi',
                ],
                'giap_tuat' => [
                    'than',
                    'dau',
                ],
                'at_hoi' => [
                    'than',
                    'dau',
                ],
                'binh_ty' => [
                    'than',
                    'dau',
                ],
                'dinh_suu' => [
                    'than',
                    'dau',
                ],
                'mau_dan' => [
                    'than',
                    'dau',
                ],
                'ky_mao' => [
                    'than',
                    'dau',
                ],
                'canh_thin' => [
                    'than',
                    'dau',
                ],
                'tan_ti' => [
                    'than',
                    'dau',
                ],
                'nham_ngo' => [
                    'than',
                    'dau',
                ],
                'quy_mui' => [
                    'than',
                    'dau',
                ],
                'giap_than' => [
                    'ngo',
                    'mui',
                ],
                'at_dau' => [
                    'ngo',
                    'mui',
                ],
                'binh_tuat' => [
                    'ngo',
                    'mui',
                ],
                'dinh_hoi' => [
                    'ngo',
                    'mui',
                ],
                'mau_ty' => [
                    'ngo',
                    'mui',
                ],
                'ky_suu' => [
                    'ngo',
                    'mui',
                ],
                'canh_dan' => [
                    'ngo',
                    'mui',
                ],
                'tan_mao' => [
                    'ngo',
                    'mui',
                ],
                'nham_thin' => [
                    'ngo',
                    'mui',
                ],
                'quy_ti' => [
                    'ngo',
                    'mui',
                ],
                'giap_ngo' => [
                    'thin',
                    'ti',
                ],
                'at_mui' => [
                    'thin',
                    'ti',
                ],
                'binh_than' => [
                    'thin',
                    'ti',
                ],
                'dinh_dau' => [
                    'thin',
                    'ti',
                ],
                'mau_tuat' => [
                    'thin',
                    'ti',
                ],
                'ky_hoi' => [
                    'thin',
                    'ti',
                ],
                'canh_ty' => [
                    'thin',
                    'ti',
                ],
                'tan_suu' => [
                    'thin',
                    'ti',
                ],
                'nham_dan' => [
                    'thin',
                    'ti',
                ],
                'quy_mao' => [
                    'thin',
                    'ti',
                ],
                'giap_thin' => [
                    'dan',
                    'mao',
                ],
                'at_ti' => [
                    'dan',
                    'mao',
                ],
                'binh_ngo' => [
                    'dan',
                    'mao',
                ],
                'dinh_mui' => [
                    'dan',
                    'mao',
                ],
                'mau_than' => [
                    'dan',
                    'mao',
                ],
                'ky_dau' => [
                    'dan',
                    'mao',
                ],
                'canh_tuat' => [
                    'dan',
                    'mao',
                ],
                'tan_hoi' => [
                    'dan',
                    'mao',
                ],
                'nham_ty' => [
                    'dan',
                    'mao',
                ],
                'quy_suu' => [
                    'dan',
                    'mao',
                ],
                'giap_dan' => [
                    'ty',
                    'suu',
                ],
                'at_mao' => [
                    'ty',
                    'suu',
                ],
                'binh_thin' => [
                    'ty',
                    'suu',
                ],
                'dinh_ti' => [
                    'ty',
                    'suu',
                ],
                'mau_ngo' => [
                    'ty',
                    'suu',
                ],
                'ky_mui' => [
                    'ty',
                    'suu',
                ],
                'canh_than' => [
                    'ty',
                    'suu',
                ],
                'tan_dau' => [
                    'ty',
                    'suu',
                ],
                'nham_tuat' => [
                    'ty',
                    'suu',
                ],
                'quy_hoi' => [
                    'ty',
                    'suu',
                ],
            ],
        ],
        'hong_loan' => [
            'name' => 'Hồng Loan',
            'base' => 'chi_nam',
            'map' => [
                'ty' => 'mao',
                'suu' => 'dan',
                'dan' => 'suu',
                'mao' => 'ty',
                'thin' => 'hoi',
                'ti' => 'tuat',
                'ngo' => 'dau',
                'mui' => 'than',
                'than' => 'mui',
                'dau' => 'ngo',
                'tuat' => 'ti',
                'hoi' => 'thin',
            ],
        ],

        'thien_hy' => [
            'name' => 'Thiên Hỷ',
            'base' => 'chi_nam',
            'map' => [
                'ty' => 'dau',
                'suu' => 'than',
                'dan' => 'mui',
                'mao' => 'ngo',
                'thin' => 'ti',
                'ti' => 'tuat',
                'ngo' => 'mao',
                'mui' => 'dan',
                'than' => 'suu',
                'dau' => 'ty',
                'tuat' => 'hoi',
                'hoi' => 'thin',
            ],
        ],
        'nguyet_duc' => [
            'name' => 'Nguyệt Đức',
            'base' => 'thang_chi',
            'map' => [
                'dan' => [
                    'dinh',
                ],
                'mao' => [
                    'giap',
                ],
                'thin' => [
                    'quy',
                ],
                'ti' => [
                    'canh',
                ],
                'ngo' => [
                    'at',
                ],
                'mui' => [
                    'nham',
                ],
                'than' => [
                    'binh',
                ],
                'dau' => [
                    'tan',
                ],
                'tuat' => [
                    'mau',
                ],
                'hoi' => [
                    'ky',
                ],
                'ty' => [
                    'giap',
                ],
                'suu' => [
                    'tan',
                ],
            ],
        ],
        'thien_duc' => [
            'name' => 'Thiên Đức',
            'base' => 'thang_chi',
            'map' => [
                'dan' => [
                    'binh',
                ],
                'mao' => [
                    'quy',
                ],
                'thin' => [
                    'canh',
                ],
                'ti' => [
                    'at',
                ],
                'ngo' => [
                    'nham',
                ],
                'mui' => [
                    'giap',
                ],
                'than' => [
                    'dinh',
                ],
                'dau' => [
                    'mau',
                ],
                'tuat' => [
                    'tan',
                ],
                'hoi' => [
                    'canh',
                ],
                'ty' => [
                    'dinh',
                ],
                'suu' => [
                    'quy',
                ],
            ],
        ],
        'van_xuong' => [
            'name' => 'Văn Xương',
            'base' => 'can_ngay',
            'map' => [
                'giap' => 'ty',
                'at' => 'ngo',
                'binh' => 'than',
                'dinh' => 'dau',
                'mau' => 'than',
                'ky' => 'dau',
                'canh' => 'hoi',
                'tan' => 'ty',
                'nham' => 'dan',
                'quy' => 'mao',
            ],
        ],
        'kim_du' => [
            'name' => 'Kim Dư',
            'base' => 'can_ngay',
            'map' => [
                'giap' => 'mui',
                'at' => 'than',
                'binh' => 'dau',
                'dinh' => 'hoi',
                'mau' => 'mui',
                'ky' => 'than',
                'canh' => 'dau',
                'tan' => 'hoi',
                'nham' => 'mui',
                'quy' => 'than',
            ],
        ],
    ],
];
