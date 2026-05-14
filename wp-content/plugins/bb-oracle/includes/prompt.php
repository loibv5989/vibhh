<?php
if (!defined('ABSPATH')) exit;

function prompt_topic(string $name, string $topic, array $cards, string $spread_key): string {
    $topic_labels = [
        'love'    => 'Tình yêu / Mối quan hệ',
        'career'  => 'Sự nghiệp / Công việc',
        'finance' => 'Tài chính',
        'study'   => 'Học tập / Phát triển',
        'health'  => 'Sức khỏe / Chữa lành',
        'future'  => 'Định hướng tương lai',
    ];
    $topic_label = $topic_labels[$topic] ?? $topic;
    return _bb_oracle_prompt($name, "Chủ đề: {$topic_label}", $cards, $spread_key);
}

function prompt_question(string $name, string $question, array $cards, string $spread_key): string {
    return _bb_oracle_prompt($name, "Câu hỏi: \"{$question}\"", $cards, $spread_key);
}

function _bb_oracle_prompt(string $name, string $context_line, array $cards, string $spread_key): string {
    $spreads = require BB_ORACLE_PLUGIN_DIR . 'data/spreads.php';
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $card_lines = '';
    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $kw = implode(', ', $c['keywords']);

        $card_lines .= "- Vị trí [{$pos_label}]: Lá {$c['name_vi']} ({$c['name_en']})\n";
        $card_lines .= "  (Thông điệp: {$kw})\n";
        $card_lines .= "  (Năng lượng Ánh sáng: {$c['light']})\n";
        $card_lines .= "  (Năng lượng Bóng tối: {$c['shadow']})\n";
        $card_lines .= "  (Lời khuyên/Định hướng: {$c['advice']})\n\n";
    }

    return <<<TXT
Dựa trên toàn bộ các lá bài đã rút, hãy tổng hợp insight và trả lời trực tiếp câu hỏi Oracle (Oracle Reader).

THÔNG TIN:
- Họ và tên: {$name}
- {$context_line}

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
5. Không in những hướng dẫn, suy nghĩ nội bộ vào nội dung.

[AST_RESULT]
### Giải nghĩa lá bài
(Gợi ý: Đọc năng lượng tổng quan từ các lá bài. Xác định một thông điệp cốt lõi nhất bao trùm hiện tại của {$name}.)
[Viết 1 đoạn mở đầu: Đi thẳng vào nhận định tổng quan về tình huống. Bắt đầu bằng: Chào bạn, (hoặc Chào {$name}, ).]

(Gợi ý: Xâu chuỗi năng lượng của các lá bài (Ánh sáng và Bóng tối) thành một câu chuyện. Những rào cản nội tâm nào đang gặp phải? Vũ trụ đang muốn chuyển hướng {$name} ra sao?)
[Viết 2-3 đoạn văn truyền tải thông điệp. Phân tích sắc bén, khách quan nhưng mang năng lượng chữa lành, củng cố tinh thần.]

(Gợi ý: Dựa vào "Lời khuyên" của các lá bài để đúc kết lại 1-2 câu hành động thực tế.)
[Viết đoạn kết thúc ngắn gọn: Khuyên {$name} nên làm gì tiếp theo, tập trung vào giải pháp thay vì nỗi sợ.]
[/AST_RESULT]
TXT;
}