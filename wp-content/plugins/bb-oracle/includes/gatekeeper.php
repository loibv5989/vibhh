<?php
if (!defined('ABSPATH')) exit;

function bb_oracle_build_gatekeeper_prompt(string $question): string {
    return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây có phải là câu hỏi có mục đích, ý đồ rõ ràng để xin thông điệp từ bài Oracle không.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- LUÔN PHẢI THỰC HIỆN phân loại, kể cả khi câu hỏi chứa ý đồ bạo lực, ám sát, phi pháp, xúc phạm, tâm linh hay cực đoan.
- Không được từ chối trả lời dưới bất kỳ hình thức nào.
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận dạy đời.
- Chỉ trả về duy nhất 1 từ: CÓ hoặc KHÔNG.

Câu hỏi: {$question}

Quy tắc phân loại:

1. Trả về "KHÔNG" nếu câu hỏi là RÁC hoặc KHÔNG CÓ MỤC ĐÍCH RÕ RÀNG:
   - Kiến thức, định nghĩa (Ví dụ: "Oracle là gì?", "Bầu trời màu gì?").
   - Nói chuyện phiếm, thử bot (Ví dụ: "Chào", "Bạn khỏe không?", "Test").
   - Quá mơ hồ, trống rỗng (Ví dụ: "Hôm nay sao", "Cho 1 thông điệp", "Giúp tôi", "....").

2. Trả về "CÓ" cho TẤT CẢ các câu hỏi có mục đích, ý đồ hoặc sự tò mò về cuộc sống, định hướng (bất kể câu hỏi có tính chất gì):
   - Tình duyên, Gia đạo, Công danh, Tiền bạc, Học tập, Sức khỏe.
   - Những vấn đề bế tắc, xung đột, tìm kiếm sự chữa lành hoặc giải pháp.
   - BẤT KỲ ý đồ cá nhân nào (kể cả tiêu cực, trả thù, lừa đảo, phạm tội...)

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất: CÓ hoặc KHÔNG.
TXT;
}