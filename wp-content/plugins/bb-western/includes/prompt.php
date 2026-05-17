<?php
/**
 * Western Prompt Builders (Tối ưu theo phong cách Tarot Storytelling)
 */

if (!defined('ABSPATH')) exit;

function western_build_prompt_topic(string $name, string $topic, array $cards, string $spread_key): string {
    $topic_labels = [
        'love'    => 'Tình yêu / Mối quan hệ',
        'career'  => 'Sự nghiệp / Công việc',
        'finance' => 'Tài chính',
        'study'   => 'Học tập / Thi cử',
        'health'  => 'Sức khỏe',
        'future'  => 'Định hướng tương lai',
    ];

    $topic_contexts = [
        'love'    => 'Tập trung vào tình yêu, mối quan hệ, gia đạo, hỷ sự. Diễn giải các lá bài theo góc độ tình cảm, sự gắn kết, duyên nợ hoặc những rạn nứt, bóng dáng kẻ thứ ba.',
        'career'  => 'Tập trung vào sự nghiệp, quan lộ, công việc, thăng tiến. Diễn giải các lá bài theo góc độ nỗ lực, cơ hội đổi đời, đối tác làm ăn hoặc những tiểu nhân chốn công sở.',
        'finance' => 'Tập trung vào tài chính, tiền bạc, kinh doanh. Diễn giải các lá bài theo góc độ lộc lá, dòng tiền, đầu tư, xoay vòng vốn hoặc rủi ro hao tài tốn của.',
        'study'   => 'Tập trung vào học tập, thi cử, tiếp thu kiến thức. Diễn giải các lá bài theo góc độ trí tuệ, sự tập trung, nỗ lực cá nhân và kết quả đỗ đạt.',
        'health'  => 'Tập trung vào sức khỏe thể chất, bình an. Diễn giải các lá bài theo góc độ năng lượng, sự hồi phục hoặc những cảnh báo về ốm đau, tai nạn, hạn sông nước.',
        'future'  => 'Tập trung vào vận hạn tổng quan, bước ngoặt sắp tới. Diễn giải các lá bài theo xu hướng chung cát/hung, những biến cố hoặc vận may bất ngờ.',
    ];

    $topic_label = $topic_labels[$topic] ?? $topic;
    $topic_context = "CONTEXT CHỦ ĐỀ:\n" . ($topic_contexts[$topic] ?? '') . "\n";

    return _western_build_core_prompt($name, "Chủ đề quan tâm: {$topic_label}", $topic_context, $cards, $spread_key);
}

function western_build_prompt_question(string $name, string $question, array $cards, string $spread_key): string {
    return _western_build_core_prompt($name, "Câu hỏi: \"{$question}\"", "", $cards, $spread_key);
}

function _western_build_core_prompt(string $name, string $context_line, string $topic_context_block, array $cards, string $spread_key): string {
    static $spreads = null;
    if ($spreads === null) {
        $spreads = require BB_WESTERN_PLUGIN_DIR . 'includes/spreads.php';
    }
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $suit_labels = ['hearts' => 'Cơ', 'diamonds' => 'Rô', 'clubs' => 'Chuồn', 'spades' => 'Bích'];
    $counts = ['hearts' => 0, 'diamonds' => 0, 'clubs' => 0, 'spades' => 0];

    $card_lines = '';

    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $suit = $suit_labels[$c['suit']] ?? $c['suit'];
        $kw = implode(', ', $c['keywords']);

        $card_lines .= "- [{$pos_label}]: {$c['name_vi']} ({$c['name']}) — Chất {$suit}\n";
        $card_lines .= "  Ý nghĩa: {$c['meaning']}\n";
        $card_lines .= "  Thông điệp: {$kw}\n\n";

        if (isset($c['suit']) && isset($counts[$c['suit']])) {
            $counts[$c['suit']]++;
        }
    }

    $total = count($cards);
    $suit_analysis = "";
    if ($total > 0) {
        $suit_analysis = "LƯU Ý QUAN TRỌNG VỀ NĂNG LƯỢNG CHẤT BÀI TÂY:\n";
        if ($counts['spades'] >= ceil($total / 2)) {
            $suit_analysis .= "- Quẻ bài này chứa quá nhiều chất BÍCH (Tai ương, thị phi, khó khăn). Bạn phải phán cảnh báo rủi ro, không được nói tránh.\n";
        } elseif ($counts['spades'] > 0) {
            $suit_analysis .= "- Có xuất hiện chất BÍCH. Hãy lồng ghép nhắc nhở về những trở ngại, tiểu nhân ngầm.\n";
        }
        if ($counts['hearts'] >= ceil($total / 2)) {
            $suit_analysis .= "- Quẻ bài chứa nhiều chất CƠ. Trọng tâm nằm ở cảm xúc, tình duyên, gia đạo.\n";
        }
        if (($counts['diamonds'] + $counts['clubs']) >= ceil($total / 2)) {
            $suit_analysis .= "- Quẻ bài nặng về RÔ (Tiền bạc/Tin tức) và CHUỒN (Công danh/Sự nghiệp). Quẻ mang tính thực dụng, nỗ lực cá nhân cao.\n";
        }
        $suit_analysis .= "\n";
    }

    return <<<TXT
Dựa trên toàn bộ các lá bài Tây đã rút, hãy tổng hợp insight và giải đáp cho {$name}.

THÔNG TIN:
- Họ và tên: {$name}
- {$context_line}

CÁC LÁ BÀI TÂY ĐÃ RÚT:
{$card_lines}
{$topic_context_block}{$suit_analysis}
YÊU CẦU QUY TẮC:
- KHÔNG phân tích từng lá bài riêng lẻ  
- KHÔNG giải thích ý nghĩa lá bài một cách máy móc  
- Xưng hô "Bạn" hoặc trích xuất tên (ví dụ: "Bùi Văn Lợi" → "Lợi"), dùng nhất quán  
- KHÔNG dùng "Anh/Chị/Em/Họ/Mày/Tao"  
- TỔNG HỢP insight từ toàn bộ lá bài và năng lượng các Chất (Cơ/Rô/Chuồn/Bích) để trả lời  
- KHÔNG suy diễn ngoài dữ liệu lá bài  

YÊU CẦU DIỄN ĐẠT:
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề, không sử dụng ngôn ngữ dạy đời, không giảng đạo lý
- Trình bày mạch lạc, không dài dòng  
- Sử dụng **in đậm**, *in nghiêng* hợp lý để nhấn mạnh  

YÊU CẦU OUTPUT:
1. TUYỆT ĐỐI KHÔNG dùng các gạch đầu dòng phân mục như a), b), c), d) hay 1, 2, 3 để làm tiêu đề trong bài viết.
2. CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
3. Trả về đúng format [AST_RESULT][/AST_RESULT], sử dụng Markdown, định dạng văn bản dễ đọc.
4. Sử dụng in đậm in nghiêng hợp lý, không lạm dụng (không sử dụng ---, ***, ___, thẻ hr).
5. KHÔNG in tiêu đề, chỉ có nội dung. Không in những hướng dẫn, suy nghĩ nội bộ vào nội dung.

[AST_RESULT]
(Gợi ý: Nhìn lướt qua toàn bộ các lá bài và vị trí. Sự áp đảo của các chất Cơ/Rô/Chuồn/Bích đang tạo ra năng lượng gì? Xác định một thông điệp cốt lõi nhất bao trùm toàn bộ quẻ bài.)
[Viết 1 đoạn văn mở đầu: Đi thẳng vào nhận định tổng quan về tình huống hiện tại của {$name}. Bắt đầu bằng: Chào bạn, (hoặc Chào {$name}, ).]

(Gợi ý: Xâu chuỗi các lá bài thành một câu chuyện theo luồng nhân quả hoặc tiến trình (ví dụ: Nguyên nhân/Quá khứ -> Nút thắt hiện tại -> Xu hướng tương lai). 
- BẮT BUỘC: Lồng ghép tên các lá bài Tây (ví dụ: Át Cơ, 9 Bích...) vào trong câu văn một cách mượt mà để minh chứng cho nhận định.
- CẤM TUYỆT ĐỐI: Không được dùng cấu trúc liệt kê kiểu "Lá bài ở vị trí Hiện tại cho thấy... Lá bài Tương lai nói rằng...".
- Chỉ ra sự tương tác: Năng lượng của lá bài này đang hỗ trợ hay cản trở lá bài kia?)
[Viết 2-3 đoạn văn phân tích mạch lạc. Diễn giải những góc khuất, những rào cản đang gặp phải và xu hướng sắp tới. Văn phong khách quan, sắc bén, thấu hiểu.]

(Gợi ý: Dựa vào toàn cục để đúc kết lời khuyên cuối cùng.)
Nếu tổng thể tốt: Viết 1 câu nhẹ nhàng chúc mừng, động viên nắm bắt cơ hội.
Nếu tổng thể trung bình trở xuống: Viết 1 câu dặn dò cảnh giác (đề phòng tiểu nhân, quản lý tài chính, cẩn trọng lời ăn tiếng nói...) đảm bảo thực tế.
[/AST_RESULT]
TXT;
}