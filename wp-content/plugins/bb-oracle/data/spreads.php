<?php
if (!defined('ABSPATH')) exit;

return [
    '1_card' => [
        'name'  => 'Rút 1 lá (Daily Card)',
        'count' => 1,
        'positions' => [
            'message' => 'Thông Điệp Hôm Nay',
        ],
    ],
    '2_cards' => [
        'name'  => 'Rút 2 lá',
        'count' => 2,
        'positions' => [
            'situation' => 'Tình Huống',
            'guidance'  => 'Hướng Dẫn',
        ],
    ],
    '3_cards' => [
        'name'  => 'Rút 3 lá',
        'count' => 3,
        'positions' => [
            'mind'   => 'Tâm Trí',
            'heart'  => 'Trái Tim',
            'spirit' => 'Linh Hồn',
        ],
    ],
];
