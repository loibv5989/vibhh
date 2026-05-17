<?php

if (!defined('ABSPATH')) exit;

$spreads = [
    '3_cards' => [
        'name'     => '3-Card Spread',
        'count'    => 3,
        'template' => 'default',
        'positions' => [
            'past'    => 'Past',
            'present' => 'Present',
            'future'  => 'Future'
        ]
    ],
    '5_cards' => [
        'name'     => '5-Card Spread (Cross)',
        'count'    => 5,
        'template' => 'default',
        'positions' => [
            'situation' => 'Present',
            'challenge' => 'Challenge',
            'advice'    => 'Advice',
            'external'  => 'Outside Influence',
            'outcome'   => 'Outcome'
        ]
    ],
    '7_cards' => [
        'name'     => '7-Card Spread (Horseshoe)',
        'count'    => 7,
        'template' => 'default',
        'positions' => [
            'past'        => 'Past',
            'present'     => 'Present',
            'hidden'      => 'Hidden Factor',
            'obstacle'    => 'Obstacle',
            'environment' => 'Surroundings',
            'advice'      => 'Advice',
            'outcome'     => 'Outcome'
        ]
    ],
];

return $spreads;