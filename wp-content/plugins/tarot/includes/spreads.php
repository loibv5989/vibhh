<?php
if (!defined('ABSPATH')) exit;

$spreads = [
    '1_card' => [
        'name'     => '1-Card Spread',
        'count'    => 1,
        'template' => 'one-card',
        'positions' => [
            'answer' => 'Card'
        ]
    ],
    '3_cards' => [
        'name'     => '3-Card Spread',
        'count'    => 3,
        'template' => 'three-cards',
        'positions' => [
            'past'    => 'Past',
            'present' => 'Present',
            'future'  => 'Future'
        ]
    ],
    '5_cards' => [
        'name'     => '5-Card Spread (Cross)',
        'count'    => 5,
        'template' => 'cross',
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
        'template' => 'horseshoe',
        'positions' => [
            'past'        => 'Past',
            'present'     => 'Present',
            'hidden'      => 'Hidden Factor',
            'obstacle'    => 'Obstacle',
            'environment' => 'Environment',
            'advice'      => 'Advice',
            'outcome'     => 'Outcome'
        ]
    ],
    '10_cards' => [
        'name'     => 'Celtic Cross (10 Cards)',
        'count'    => 10,
        'template' => 'celtic-cross',
        'positions' => [
            'heart'       => 'Heart of the Matter',
            'challenge'   => 'Challenge',
            'root'        => 'Root Cause',
            'past'        => 'Past',
            'goal'        => 'Goal',
            'future'      => 'Near Future',
            'self'        => 'Self',
            'environment' => 'Environment',
            'hopes_fears' => 'Hopes / Fears',
            'outcome'     => 'Outcome'
        ]
    ],

    'love_3_cards' => [
        'name'     => 'Love — 3 Cards',
        'count'    => 3,
        'template' => 'three-cards',
        'positions' => [
            'you'        => 'You (Energy & Position)',
            'partner'    => 'Them (Thoughts & Feelings)',
            'connection' => 'Connection (Future & Advice)'
        ]
    ],
    'love_5_cards' => [
        'name'     => 'Love — 5 Cards (Mirror)',
        'count'    => 5,
        'template' => 'cross',
        'positions' => [
            'your_mind'   => 'Your Mind',
            'their_mind'  => 'Their Mind',
            'your_heart'  => 'Your Heart',
            'their_heart' => 'Their Heart',
            'outcome'     => 'Outcome / Advice'
        ]
    ],
    'love_7_cards' => [
        'name'     => 'Love — 7 Cards (Horseshoe)',
        'count'    => 7,
        'template' => 'horseshoe',
        'positions' => [
            'past_love'      => 'Romantic Past',
            'present_status' => 'Current Status',
            'your_desire'    => 'Your Desire',
            'their_desire'   => 'Their Desire',
            'hidden_factors' => 'Blocking Factors',
            'advice'         => 'Advice',
            'future_outcome' => 'Future Outcome'
        ]
    ],
    'love_9_cards' => [
        'name'     => 'Love — 9 Cards (Full Picture)',
        'count'    => 9,
        'template' => 'love-nine',
        'positions' => [
            'you_now'       => 'You Right Now',
            'them_now'      => 'Them Right Now',
            'core_issue'    => 'Core Issue',
            'past_bond'     => 'Shared Past',
            'your_hope'     => 'Your Hopes',
            'their_hope'    => 'Their Hopes',
            'challenge'     => 'Upcoming Challenge',
            'advice'        => 'Advice from the Universe',
            'final_outcome' => 'Final Outcome'
        ]
    ],
];

$spreads['celtic_cross'] = $spreads['10_cards'];

return $spreads;