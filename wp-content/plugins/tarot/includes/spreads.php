<?php
if (!defined('ABSPATH')) exit;

$spreads = [
    '1_card' => [
        'name'     => '1-Card Spread',
        'count'    => 1,
        'template' => 'one-card',
        'positions' => [
            'answer' => 'The Card'
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
        'name'     => '5-Card Cross Spread',
        'count'    => 5,
        'template' => 'cross',
        'positions' => [
            'situation' => 'Current Situation',
            'challenge' => 'Challenge',
            'advice'    => 'Advice',
            'external'  => 'External Influences',
            'outcome'   => 'Outcome'
        ]
    ],
    '7_cards' => [
        'name'     => '7-Card Horseshoe Spread',
        'count'    => 7,
        'template' => 'horseshoe',
        'positions' => [
            'past'        => 'Past',
            'present'     => 'Present',
            'hidden'      => 'Hidden Factors',
            'obstacle'    => 'Obstacles',
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
        'name'     => 'Love 3-Card Spread',
        'count'    => 3,
        'template' => 'three-cards',
        'positions' => [
            'you'        => 'You (Energy & Position)',
            'partner'    => 'Partner (Thoughts & Feelings)',
            'connection' => 'Connection (Future & Advice)'
        ]
    ],
    'love_5_cards' => [
        'name'     => 'Love 5-Card Contrast Spread',
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
        'name'     => 'Love 7-Card Horseshoe Spread',
        'count'    => 7,
        'template' => 'horseshoe',
        'positions' => [
            'past_love'      => 'Past Love',
            'present_status' => 'Present Status',
            'your_desire'    => 'Your Desire',
            'their_desire'   => 'Their Desire',
            'hidden_factors' => 'Hidden Blocks',
            'advice'         => 'Advice',
            'future_outcome' => 'Future Outcome'
        ]
    ],
    'love_9_cards' => [
        'name'     => 'Love 9-Card Overview',
        'count'    => 9,
        'template' => 'love-nine',
        'positions' => [
            'you_now'       => 'You Now',
            'them_now'      => 'Them Now',
            'core_issue'    => 'Core Issue',
            'past_bond'     => 'Shared Past',
            'your_hope'     => 'Your Hope',
            'their_hope'    => 'Their Hope',
            'challenge'     => 'Upcoming Challenge',
            'advice'        => 'Advice from the Universe',
            'final_outcome' => 'Final Outcome'
        ]
    ],
];

// Alias
$spreads['celtic_cross'] = $spreads['10_cards'];

return $spreads;