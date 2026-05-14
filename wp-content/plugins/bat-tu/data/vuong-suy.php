<?php
if (!defined('ABSPATH')) exit;

return [
    'vuong_suy' => [
        'xuan'   => ['moc' => 'vuong', 'hoa' => 'tuong', 'thuy' => 'huu', 'kim' => 'tu', 'tho' => 'tu_state'],
        'ha'     => ['hoa' => 'vuong', 'tho' => 'tuong', 'moc' => 'huu', 'thuy' => 'tu', 'kim' => 'tu_state'],
        'thu'    => ['kim' => 'vuong', 'thuy' => 'tuong', 'tho' => 'huu', 'hoa' => 'tu', 'moc' => 'tu_state'],
        'dong'   => ['thuy' => 'vuong', 'moc' => 'tuong', 'kim' => 'huu', 'tho' => 'tu', 'hoa' => 'tu_state'],
        'tu_quy' => ['tho' => 'vuong', 'kim' => 'tuong', 'hoa' => 'huu', 'moc' => 'tu', 'thuy' => 'tu_state'],
    ],
    'mua_chi' => [
        'xuan'   => ['dan', 'mao'],
        'ha'     => ['ti', 'ngo'],
        'thu'    => ['than', 'dau'],
        'dong'   => ['hoi', 'ty'],
        'tu_quy' => ['thin', 'tuat', 'suu', 'mui']
    ],
    'sinh_vuong_xuat_tu' => [
        'moc' => [
            'sinh' => 'thuy',
            'vuong' => 'moc',
            'xuat' => 'hoa',
            'tu' => 'kim',
            'hao' => 'tho',
        ],
        'hoa' => [
            'sinh' => 'moc',
            'vuong' => 'hoa',
            'xuat' => 'tho',
            'tu' => 'thuy',
            'hao' => 'kim',
        ],
        'tho' => [
            'sinh' => 'hoa',
            'vuong' => 'tho',
            'xuat' => 'kim',
            'tu' => 'moc',
            'hao' => 'thuy',
        ],
        'kim' => [
            'sinh' => 'tho',
            'vuong' => 'kim',
            'xuat' => 'thuy',
            'tu' => 'hoa',
            'hao' => 'moc',
        ],
        'thuy' => [
            'sinh' => 'kim',
            'vuong' => 'thuy',
            'xuat' => 'moc',
            'tu' => 'tho',
            'hao' => 'hoa',
        ],
    ],
];
