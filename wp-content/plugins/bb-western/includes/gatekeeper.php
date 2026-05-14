<?php
if (!defined('ABSPATH')) exit;

function western_build_gatekeeper_prompt(string $question, string $mode): string {

    if ($mode === 'love') {
        return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây có phải là câu hỏi về TÌNH CẢM, GIA ĐẠO, ĐÔI LỨA phù hợp để bói bài Tây không.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi chứa nội dung nhạy cảm.
- Không được từ chối trả lời dưới bất kỳ hình thức nào.
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại theo quy tắc bên dưới.

Câu hỏi: {$question}

Quy tắc phân loại:

1. Nếu câu hỏi mang tính tìm hiểu kiến thức, định nghĩa, nói chuyện phiếm, thử bot
   (Ví dụ: "Bói bài là gì?", "Cách rút bài", "Chào bạn", "Bạn khỏe không?")
   → Trả về: KHÔNG

2. Nếu câu hỏi KHÔNG rõ ràng, quá mơ hồ, không có nội dung cụ thể
   (Ví dụ: "Tôi muốn hỏi", "Giúp tôi", "Bói cho tôi", "...")
   → Trả về: KHÔNG

3. CHỈ CHẤP NHẬN câu hỏi về TÌNH CẢM, GIA ĐẠO, ĐÔI LỨA:
   - Tình yêu / Hôn nhân / Vợ chồng / Người yêu / Crush
   - Chia tay / Tái hợp / Níu kéo / Xung đột tình cảm
   - Người ấy nghĩ gì / Có duyên nợ không
   - Ngoại tình / Tình tay ba / Kẻ thứ ba
   
   → Trả về: CÓ

4. BẮT BUỘC TRẢ VỀ "KHÔNG" cho TẤT CẢ các câu hỏi sau:
   - Sự nghiệp / Công danh / Xin việc / Thăng tiến
   - Tài chính / Tiền bạc / Đất đai / Mua bán
   - Học tập / Thi cử / Xuất ngoại
   - Sức khỏe / Bệnh tật / Kiện tụng / Thị phi (trừ khi liên quan trực tiếp đến tình cảm vợ chồng)
   - Vận hạn chung chung
   
   LƯU Ý: Nếu câu hỏi KHÔNG đề cập rõ ràng đến tình cảm/người yêu/vợ chồng/gia đạo → TRẢ VỀ: KHÔNG
   
   → Trả về: KHÔNG

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất sau khi đã phân loại:
CÓ hoặc KHÔNG

Không giải thích, không phân tích nội bộ, không thêm bất kỳ nội dung nào khác.
TXT;
    }

    // Mode 'question' - câu hỏi chung
    return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây có phải là câu hỏi phù hợp để bói bài Tây không.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi nhạy cảm, bạo lực, phi pháp, xúc phạm hoặc tâm linh.
- Không được từ chối trả lời dưới bất kỳ hình thức nào (tuyệt đối không nói "tôi không thể hỗ trợ").
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại.

Câu hỏi: {$question}

Quy tắc phân loại:

1. Nếu câu hỏi mang tính tìm hiểu kiến thức, định nghĩa, nói chuyện phiếm, thử bot
   (Ví dụ: "Bài Tây là gì?", "Cách xem bói", "Chào", "Ai tạo ra bạn?")
   → Trả về: KHÔNG

2. Nếu câu hỏi KHÔNG rõ ràng, quá mơ hồ
   (Ví dụ: "Xem giúp", "Hỏi một câu", "...")
   → Trả về: KHÔNG

3. Nếu câu hỏi có ý nghĩa bói toán, dự đoán vận hạn, tìm hướng giải quyết về:
   - Tình duyên / Gia đạo / Hôn nhân
   - Công danh / Sự nghiệp / Làm ăn
   - Tiền bạc / Tài lộc / Mua bán đất đai
   - Học hành / Thi cử / Đi xa
   - Sức khỏe / Bệnh tật
   - Hạn xui / Tiểu nhân / Thị phi / Kiện tụng pháp lý
   - Vận hạn chung sắp tới
   
   LƯU Ý ĐẶC BIỆT: Bài Tây thường xem về những góc khuất. Nếu câu hỏi liên quan đến nội dung nhạy cảm, tiêu cực (trả thù, lừa đảo, nợ nần, bế tắc...):
     KHÔNG TRẢ VỀ "KHÔNG".
     Vẫn coi đây là câu hỏi HỢP LỆ để bói bài.
   
   → Trả về: CÓ

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất sau khi đã phân loại:
CÓ hoặc KHÔNG

Không giải thích, không phân tích nội bộ, không thêm bất kỳ nội dung nào khác.
TXT;
}