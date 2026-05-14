<?php

if (!defined('ABSPATH')) exit;

function tarot_build_prompt_topic(string $name, string $topic, array $cards, string $spread_key): string {
    $topic_labels = [
        'love'    => 'Tình yêu / Mối quan hệ',
        'career'  => 'Sự nghiệp / Công việc',
        'finance' => 'Tài chính',
        'study'   => 'Học tập / Thi cử',
        'health'  => 'Sức khỏe',
        'future'  => 'Định hướng tương lai',
    ];
    
    $topic_contexts = [
        'love'    => 'Tập trung vào tình yêu, mối quan hệ tình cảm, hôn nhân, đôi lứa. Diễn giải các lá bài theo góc độ tình cảm, cảm xúc, sự kết nối giữa hai người.',
        'career'  => 'Tập trung vào sự nghiệp, công việc, thăng tiến, chuyển việc, môi trường làm việc. Diễn giải các lá bài theo góc độ nghề nghiệp, cơ hội phát triển, thử thách trong công việc.',
        'finance' => 'Tập trung vào tài chính, tiền bạc, đầu tư, kinh doanh, thu nhập. Diễn giải các lá bài theo góc độ tài chính, cơ hội kiếm tiền, rủi ro tài chính.',
        'study'   => 'Tập trung vào học tập, thi cử, du học, kiến thức, kỹ năng. Diễn giải các lá bài theo góc độ học vấn, khả năng tiếp thu, kết quả thi cử.',
        'health'  => 'Tập trung vào sức khỏe thể chất, tinh thần, năng lượng, sự cân bằng. Diễn giải các lá bài theo góc độ sức khỏe, cảnh báo, lời khuyên chăm sóc bản thân.',
        'future'  => 'Tập trung vào định hướng tương lai, quyết định quan trọng, con đường phía trước. Diễn giải các lá bài theo góc độ tổng quan, xu hướng, cơ hội và thách thức sắp tới.',
    ];

    $topic_label = $topic_labels[$topic] ?? $topic;
    $topic_context = $topic_contexts[$topic] ?? '';
    
    static $spreads = null;
    if ($spreads === null) {
        $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
    }
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];
    
    $card_lines = '';
    $orient_labels = ['upright' => 'Xuôi ↑', 'reversed' => 'Ngược ↓'];
    
    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $ol = $orient_labels[$c['orientation']] ?? '';
        $kw = implode(', ', $c['keywords']);
        
        $card_lines .= "- [{$pos_label}]: {$c['name_vi']} ({$c['name']}) — {$ol}\n";
        $card_lines .= "  Thông điệp: {$kw}\n";
        if (!empty($c['timing'])) {
            $card_lines .= "  Thời điểm: {$c['timing']}\n";
        }
        $card_lines .= "\n";
    }

    return <<<TXT
Dựa trên toàn bộ các lá bài đã rút, hãy tổng hợp insight và giải nghĩa lá bài về chủ đề "{$topic_label}".

THÔNG TIN:
- Họ và tên: {$name}
- Chủ đề quan tâm: {$topic_label}

CÁC LÁ BÀI:
{$card_lines}

CONTEXT CHỦ ĐỀ:
{$topic_context}

YÊU CẦU QUY TẮC:
- KHÔNG phân tích từng lá bài riêng lẻ  
- KHÔNG giải thích ý nghĩa lá bài  
- Xưng hô “Bạn” hoặc trích xuất tên (ví dụ: “Bùi Văn Lợi” → “Lợi”), dùng nhất quán  
- KHÔNG dùng “Anh/Chị/Em/Họ/Mày/Tao”  
- TỔNG HỢP insight từ toàn bộ lá bài để trả lời câu hỏi  
- KHÔNG suy diễn ngoài dữ liệu lá bài  

YÊU CẦU DIỄN ĐẠT:
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu  
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề  
- Trình bày mạch lạc, không dài dòng  
- Sử dụng **in đậm**, *in nghiêng* hợp lý để nhấn mạnh

YÊU CẦU OUTPUT:
1. TUYỆT ĐỐI KHÔNG dùng các gạch đầu dòng phân mục như a), b), c), d), e), f) hay 1, 2, 3, 4 để làm tiêu đề trong bài viết.
2. CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
3. Trả về đúng format [AST_RESULT][/AST_RESULT], sử dụng Markdown, định dạng văn bản dễ đọc.
4. Sử dụng in đậm in nghiêng hợp lý, không lạm dụng, (không sử dụng ---, ***, ___, thẻ hr).
5. KHÔNG in tiêu đề, chỉ có nội dung. không in những hướng dẫn, suy nghĩ nội bộ vào nội dung.

[AST_RESULT]
(Gợi ý: Nhìn qua toàn bộ các lá bài và vị trí của chúng. Năng lượng chung là gì? (Tích cực, tiêu cực, mâu thuẫn, hay đang chuyển giao?). Xác định một thông điệp cốt lõi nhất bao trùm toàn bộ trải bài để làm câu mở đầu.)
[Viết 1 đoạn văn mở đầu: Đi thẳng vào nhận định tổng quan về tình huống hiện tại của {$name} đối với câu hỏi/chủ đề. Bắt đầu bằng: Chào bạn, (hoặc Chào {$name}, ).]

(Gợi ý: Xâu chuỗi các lá bài thành một câu chuyện theo luồng nhân quả hoặc tiến trình trải bài (ví dụ: Nguyên nhân/Quá khứ -> Nút thắt hiện tại -> Xu hướng tương lai). 
- BẮT BUỘC: Lồng ghép tên các lá bài (và chiều xuôi/ngược) vào trong câu văn một cách mượt mà để minh chứng cho nhận định.
- CẤM TUYỆT ĐỐI: Không được dùng cấu trúc liệt kê kiểu "Lá bài 1 ở vị trí X cho thấy... Lá bài 2 nói rằng...".
- Chỉ ra sự tương tác: Lá bài này đang bổ trợ hay cản trở lá bài kia?)
[Viết 2-3 đoạn văn phân tích mạch lạc. Diễn giải những góc khuất, những rào cản đang gặp phải và xu hướng sắp tới. Văn phong khách quan, sâu sắc, thấu hiểu tâm lý.]

(Gợi ý: Dựa vào lá bài mang tính giải pháp hoặc kết quả để đúc kết hành động.)
Nếu tổng thể tốt: Có thể viết 1 câu nhẹ nhàng đúng mực, phù hợp tình huống ví dụ, chúc mứng, lời khen .v.v
Nếu tổng thể trung bình trở xuống: Có thể viết 1 câu nhẹ nhàng ngắn, lời khuyên/an ủi tâm lý nhưng đảm bảo không biến chất Tarot, không dạy đời, đạo lý.
[/AST_RESULT]

TXT;
}

function tarot_build_prompt_question(string $name, string $question, array $cards, string $spread_key, string $topic = 'question'): string {
    // Load spreads config
    static $spreads = null;
    if ($spreads === null) {
        $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
    }
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];
    
    // Format cards
    $card_lines = '';
    $orient_labels = ['upright' => 'Xuôi ↑', 'reversed' => 'Ngược ↓'];
    
    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $ol = $orient_labels[$c['orientation']] ?? '';
        $kw = implode(', ', $c['keywords']);
        
        $card_lines .= "- [{$pos_label}]: {$c['name_vi']} ({$c['name']}) — {$ol}\n";
        $card_lines .= "  Thông điệp: {$kw}\n";
        if (!empty($c['timing'])) {
            $card_lines .= "  Thời điểm: {$c['timing']}\n";
        }
        $card_lines .= "\n";
    }

    return <<<TXT
Dựa trên toàn bộ các lá bài đã rút, hãy tổng hợp insight và trả lời trực tiếp câu hỏi Tarot.

THÔNG TIN:
- Họ và tên: {$name}
- Câu hỏi: "{$question}"

CÁC LÁ BÀI:
{$card_lines}

YÊU CẦU QUY TẮC:
- KHÔNG phân tích từng lá bài riêng lẻ  
- KHÔNG giải thích ý nghĩa lá bài  
- Xưng hô “Bạn” hoặc trích xuất tên (ví dụ: “Bùi Văn Lợi” → “Lợi”), dùng nhất quán  
- KHÔNG dùng “Anh/Chị/Em/Họ/Mày/Tao”  
- TỔNG HỢP insight từ toàn bộ lá bài để trả lời câu hỏi  
- KHÔNG suy diễn ngoài dữ liệu lá bài, KHÔNG CẦN VIẾT CHO HAY. Không sử dụng ngôn ngữ dạy đời, đạo lý.

YÊU CẦU DIỄN ĐẠT:
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu  
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề  
- Trình bày mạch lạc, không dài dòng  
- Sử dụng **in đậm**, *in nghiêng* hợp lý để nhấn mạnh

YÊU CẦU OUTPUT:
1. TUYỆT ĐỐI KHÔNG dùng các gạch đầu dòng phân mục như a), b), c), d), e), f) hay 1, 2, 3, 4 để làm tiêu đề trong bài viết.
2. CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
3. Trả về đúng format [AST_RESULT][/AST_RESULT], sử dụng Markdown, định dạng văn bản dễ đọc.
4. Sử dụng in đậm in nghiêng hợp lý, không lạm dụng, (không sử dụng ---, ***, ___, thẻ hr).
5. Không in tiêu đề, chỉ có nội dung Không in những hướng dẫn, suy nghĩ nội bộ vào nội dung.

[AST_RESULT]
(Gợi ý: Nhìn lướt qua toàn bộ các lá bài và vị trí của chúng. Năng lượng chung là gì? (Tích cực, tiêu cực, mâu thuẫn, hay đang chuyển giao?). Xác định một thông điệp cốt lõi nhất bao trùm toàn bộ trải bài để làm câu mở đầu.)
[Viết 1 đoạn văn mở đầu: Đi thẳng vào nhận định tổng quan về tình huống hiện tại của {$name} đối với câu hỏi/chủ đề. Bắt đầu bằng: Chào bạn, (hoặc Chào {$name}, ).]

(Gợi ý: Xâu chuỗi các lá bài thành một câu chuyện theo luồng nhân quả hoặc tiến trình trải bài (ví dụ: Nguyên nhân/Quá khứ -> Nút thắt hiện tại -> Xu hướng tương lai). 
- BẮT BUỘC: Lồng ghép tên các lá bài (và chiều xuôi/ngược) vào trong câu văn một cách mượt mà để minh chứng cho nhận định.
- CẤM TUYỆT ĐỐI: Không được dùng cấu trúc liệt kê kiểu "Lá bài 1 ở vị trí X cho thấy... Lá bài 2 nói rằng...".
- Chỉ ra sự tương tác: Lá bài này đang bổ trợ hay cản trở lá bài kia?)
[Viết 2-3 đoạn văn phân tích mạch lạc. Diễn giải những góc khuất, những rào cản đang gặp phải và xu hướng sắp tới. Văn phong khách quan, sâu sắc, thấu hiểu tâm lý.]

(Gợi ý: Dựa vào lá bài mang tính giải pháp hoặc kết quả để đúc kết hành động.)
Nếu tổng thể tốt: Có thể viết 1 câu nhẹ nhàng đúng mục, phù hợp tình huống ví dụ, chúc mứng, lời khen .v.v
Nếu tổng thể trung bình trở xuống: Có thể viết 1 câu nhẹ nhàng ngắn, lời khuyên/an ủi tâm lý nhưng đảm bảo không biến chất Tarot, không dạy đời, đạo lý.
[/AST_RESULT]
TXT;
}