<?php
if (!defined('ABSPATH')) exit;

$spreads = [
    '1_card' => [
        'name'     => 'Trải bài 1 lá',
        'count'    => 1,
        'template' => 'one-card',
        'positions' => [
            'answer' => 'Lá bài'
        ]
    ],
    '3_cards' => [
        'name'     => 'Trải bài 3 lá',
        'count'    => 3,
        'template' => 'three-cards',
        'positions' => [
            'past'    => 'Quá khứ',
            'present' => 'Hiện tại',
            'future'  => 'Tương lai'
        ]
    ],
    '5_cards' => [
        'name'     => 'Trải bài 5 lá (Chữ thập)',
        'count'    => 5,
        'template' => 'cross',
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
        'template' => 'horseshoe',
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
    '10_cards' => [
        'name'     => 'Trải bài Celtic Cross (10 lá)',
        'count'    => 10,
        'template' => 'celtic-cross',
        'positions' => [
            'heart'       => 'Trọng tâm',
            'challenge'   => 'Thử thách',
            'root'        => 'Gốc rễ',
            'past'        => 'Quá khứ',
            'goal'        => 'Mục tiêu',
            'future'      => 'Tương lai gần',
            'self'        => 'Bản thân',
            'environment' => 'Môi trường',
            'hopes_fears' => 'Hy vọng / Sợ hãi',
            'outcome'     => 'Kết quả'
        ]
    ],

    'love_3_cards' => [
        'name'     => 'Tình Yêu 3 Lá',
        'count'    => 3,
        'template' => 'three-cards',
        'positions' => [
            'you'        => 'Bạn (Năng lượng & Vị thế)',
            'partner'    => 'Người ấy (Suy nghĩ & Cảm xúc)',
            'connection' => 'Kết nối (Tương lai & Lời khuyên)'
        ]
    ],
    'love_5_cards' => [
        'name'     => 'Tình Yêu 5 Lá (Đối chiếu)',
        'count'    => 5,
        'template' => 'cross',
        'positions' => [
            'your_mind'   => 'Lý trí của Bạn',
            'their_mind'  => 'Lý trí của Họ',
            'your_heart'  => 'Trái tim của Bạn',
            'their_heart' => 'Trái tim của Họ',
            'outcome'     => 'Kết quả / Lời khuyên'
        ]
    ],
    'love_7_cards' => [
        'name'     => 'Tình Yêu 7 Lá (Móng ngựa)',
        'count'    => 7,
        'template' => 'horseshoe',
        'positions' => [
            'past_love'      => 'Quá khứ tình cảm',
            'present_status' => 'Tình trạng hiện tại',
            'your_desire'    => 'Mong muốn của Bạn',
            'their_desire'   => 'Mong muốn của Họ',
            'hidden_factors' => 'Yếu tố cản trở',
            'advice'         => 'Lời khuyên',
            'future_outcome' => 'Kết quả tương lai'
        ]
    ],
    'love_9_cards' => [
        'name'     => 'Tình Yêu 9 Lá (Toàn cảnh)',
        'count'    => 9,
        'template' => 'love-nine',
        'positions' => [
            'you_now'       => 'Bạn ở hiện tại',
            'them_now'      => 'Họ ở hiện tại',
            'core_issue'    => 'Vấn đề cốt lõi',
            'past_bond'     => 'Quá khứ chung',
            'your_hope'     => 'Kỳ vọng của Bạn',
            'their_hope'    => 'Kỳ vọng của Họ',
            'challenge'     => 'Thử thách sắp tới',
            'advice'        => 'Lời khuyên từ vũ trụ',
            'final_outcome' => 'Kết quả cuối cùng'
        ]
    ],
];

// Alias
$spreads['celtic_cross'] = $spreads['10_cards'];

return $spreads;