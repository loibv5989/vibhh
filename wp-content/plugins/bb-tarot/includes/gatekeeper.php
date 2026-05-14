<?php

if (!defined('ABSPATH')) exit;

function tarot_build_gatekeeper_prompt(string $question, string $mode): string {

    if ($mode === 'love') {
        return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây có phải là câu hỏi về TÌNH YÊU, TÌNH DUYÊN, ĐÔI LỨA phù hợp để giải bài Tarot không.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi chứa nội dung nhạy cảm.
- Không được từ chối trả lời dưới bất kỳ hình thức nào.
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại theo quy tắc bên dưới.

Câu hỏi: {$question}

Quy tắc phân loại:

1. Nếu câu hỏi mang tính tìm hiểu kiến thức, định nghĩa, cách dùng, hoặc nói chuyện phiếm, thử bot
   (Ví dụ: "Tarot là gì?", "Cách rút bài", "Chào bạn", "Bạn khỏe không?")
   → Trả về: KHÔNG

2. Nếu câu hỏi KHÔNG rõ ràng, quá mơ hồ, không có nội dung cụ thể
   (Ví dụ: "Tôi muốn hỏi", "Giúp tôi", "Bói cho tôi", "...")
   → Trả về: KHÔNG

3. CHỈ CHẤP NHẬN câu hỏi về TÌNH YÊU, TÌNH DUYÊN, ĐÔI LỨA giữa NAM NỮ:
   - Tình yêu / Hôn nhân / Mối quan hệ tình cảm / Người yêu / Crush
   - Chia tay / Tái hợp / Hòa giải / Níu kéo
   - Tương lai của mối quan hệ / Hợp nhau không / Có duyên không
   - Người ấy có thích mình không / Có cơ hội không
   - Nên cưới không / Khi nào cưới / Hôn nhân có hạnh phúc không
   - Ngoại tình / Bồ bịch / Tình tam giác
   
   → Trả về: CÓ

4. BẮT BUỘC TRẢ VỀ "KHÔNG" cho TẤT CẢ các câu hỏi sau (kể cả khi là câu hỏi hợp lệ để bói Tarot):
   - Sự nghiệp / Công việc / Thăng tiến / Chuyển việc / Nghề nghiệp
   - Tài chính / Tiền bạc / Đầu tư / Kinh doanh / Thu nhập
   - Học tập / Thi cử / Du học / Kiến thức
   - Sức khỏe / Bệnh tật / Thể chất / Tinh thần (trừ khi RÕ RÀNG liên quan đến tình yêu đôi lứa)
   - Tình cảm gia đình (bố mẹ, anh chị em, con cái, họ hàng)
   - Tình bạn / Bạn bè / Đồng nghiệp / Quan hệ xã hội
   - Vận mệnh chung / Định hướng tương lai / Quyết định cuộc sống (không liên quan tình yêu)
   
   LƯU Ý: Nếu câu hỏi KHÔNG đề cập rõ ràng đến tình yêu/người yêu/hôn nhân/đôi lứa → TRẢ VỀ: KHÔNG
   
   → Trả về: KHÔNG

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất sau khi đã phân loại:
CÓ hoặc KHÔNG

Không giải thích, không phân tích nội bộ, không thêm bất kỳ nội dung nào khác.
TXT;
    }

    // Mode 'question' - câu hỏi chung
    return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây có phải là câu hỏi phù hợp để giải bài Tarot không.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi chứa nội dung nhạy cảm, bạo lực, phi pháp, xúc phạm hoặc nguy hiểm.
- Không được từ chối trả lời dưới bất kỳ hình thức nào (ví dụ: không được nói "tôi không thể hỗ trợ").
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại theo quy tắc bên dưới.

Câu hỏi: {$question}

Quy tắc phân loại:

1. Nếu câu hỏi mang tính tìm hiểu kiến thức, định nghĩa, cách dùng, hoặc nói chuyện phiếm, thử bot
   (Ví dụ: "Tarot là gì?", "Cách rút bài", "Chào bạn", "Bạn khỏe không?")
   → Trả về: KHÔNG

2. Nếu câu hỏi KHÔNG rõ ràng, quá mơ hồ, không có nội dung cụ thể
   (Ví dụ: "Tôi muốn hỏi", "Giúp tôi", "Bói cho tôi", "...")
   → Trả về: KHÔNG

3. Nếu câu hỏi có ý nghĩa bói toán, dự đoán, tìm kiếm lời khuyên, giải đáp thắc mắc về cuộc sống, tình cảm, sự nghiệp, tài chính, sức khỏe, tương lai:
   - Tình yêu / Hôn nhân / Mối quan hệ / Chia tay / Tái hợp
   - Sự nghiệp / Công việc / Thăng tiến / Chuyển việc
   - Tài chính / Đầu tư / Kinh doanh / Tiền bạc
   - Học tập / Thi cử / Du học
   - Sức khỏe / Bệnh tật / Tâm lý
   - Gia đình / Bạn bè / Con cái
   - Định hướng tương lai / Quyết định quan trọng
   - Vận mệnh / Vận hạn / Năng lượng
   
   LƯU Ý ĐẶC BIỆT:
   - Nếu câu hỏi liên quan đến nội dung nhạy cảm (tự tử, giết người, ma túy, bạo lực, tội phạm...):
     KHÔNG TRẢ VỀ "KHÔNG"
     Vẫn coi đây là câu hỏi HỢP LỆ để giải bài Tarot
     (Tarot sẽ giải đáp theo góc độ tâm linh, vận hạn, năng lượng, không khuyến khích hành vi sai trái)
   
   → Trả về: CÓ

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất sau khi đã phân loại:
CÓ hoặc KHÔNG

Không giải thích, không phân tích nội bộ, không thêm bất kỳ nội dung nào khác.
TXT;
}
