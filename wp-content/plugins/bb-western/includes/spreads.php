<?php
/**
 * Western Spreads Configuration
 */

if (!defined('ABSPATH')) exit;

$spreads = [
    '3_cards' => [
        'name'     => 'Trải bài 3 lá',
        'count'    => 3,
        'template' => 'default',
        'positions' => [
            'past'    => 'Quá khứ',
            'present' => 'Hiện tại',
            'future'  => 'Tương lai'
        ]
    ],
    '5_cards' => [
        'name'     => 'Trải bài 5 lá (Chữ thập)',
        'count'    => 5,
        'template' => 'default',
        'positions' => [
            'situation' => 'Hiện tại',
            'challenge' => 'Thử thách',
            'advice'    => 'Lời khuyên',
            'external'  => 'Tác động ngoài',
            'outcome'   => 'Kết quả'
        ]
    ],
    '7_cards' => [
        'name'     => 'Trải bài 7 lá (Móng ngựa)',
        'count'    => 7,
        'template' => 'default',
        'positions' => [
            'past'        => 'Quá khứ',
            'present'     => 'Hiện tại',
            'hidden'      => 'Yếu tố ẩn',
            'obstacle'    => 'Trở ngại',
            'environment' => 'Môi trường',
            'advice'      => 'Lời khuyên',
            'outcome'     => 'Kết quả'
        ]
    ],
];

return $spreads;
