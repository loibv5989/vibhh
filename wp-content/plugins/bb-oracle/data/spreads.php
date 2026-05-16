<?php
if (!defined('ABSPATH')) exit;

return [
    '1_card' => [
        'name'  => '1 Card Draw (Daily Card)',
        'count' => 1,
        'positions' => [
            'message' => "Today's Message",
        ],
    ],
    '2_cards' => [
        'name'  => '2 Card Draw',
        'count' => 2,
        'positions' => [
            'situation' => 'Situation',
            'guidance'  => 'Guidance',
        ],
    ],
    '3_cards' => [
        'name'  => '3 Card Draw',
        'count' => 3,
        'positions' => [
            'mind'   => 'Mind',
            'heart'  => 'Heart',
            'spirit' => 'Spirit',
        ],
    ],
];
