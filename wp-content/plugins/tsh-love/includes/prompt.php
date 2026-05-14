<?php
if (!defined('ABSPATH')) exit;

class TshLove_Prompt {

    public static function build(array $data): string {
        $n1 = $data['name1']; $lp1 = $data['lp1']; $soul1 = $data['soul1']; $att1 = $data['att1'];
        $n2 = $data['name2']; $lp2 = $data['lp2']; $soul2 = $data['soul2']; $att2 = $data['att2'];
        $pct = $data['percent'];

        return <<<TXT
Thần số học Pitago - Phân tích kết quả tương hợp:

[THÔNG TIN]:
- {$n1}: Số Chủ Đạo: {$lp1} | Số Linh Hồn: {$soul1} | Số Thái Độ: {$att1}
- {$n2}: Số Chủ Đạo: {$lp2} | Số Linh Hồn: {$soul2} | Số Thái Độ: {$att2}
- Mức độ đồng điệu nền tảng: {$pct}%

NGUYÊN TẮC LUẬN GIẢI:
- KHÔNG tự tính toán lại; Chỉ sử dụng dữ liệu được cung cấp.
- BẢN CHẤT LÀ GỐC: Phân tích đi thẳng vào bản chất của Thần Số Học Pitago quốc tế.
- KHÔNG SUY DIỄN: Không được tạo ra chi tiết cụ thể về hành động, lời nói, hay kịch bản đời thực.

YÊU CẦU DIỄN ĐẠT:
- XƯNG HÔ: Trích xuất tên của 2 người "{$n1}" và "{$n2}", ví dụ: Bùi Văn Lợi -> Lợi; đảm bảo đồng nhất; hoặc dùng từ "hai bạn".
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề, không sử dụng ngôn ngữ dạy đời, không giảng đạo lý
- Trình bày mạch lạc, không dẫn dắt dài dòng

YÊU CẦU OUTPUT:
- KHÔNG dùng gạch đầu dòng phân mục như a), b), c) hay 1, 2, 3 làm tiêu đề
- Sử dụng in đậm in nghiêng hợp lý để nhấn mạnh nhưng không lạm dụng.
- Sử dụng Markdown, định dạng văn bản dễ đọc.
- ĐỊNH DẠNG: Dùng Markdown chuẩn (không dùng ---, ***, ___).
- PHẢI TRẢ ĐÚNG format [TAB_RESULT][/TAB_RESULT]. 

LỆNH CẤM:
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- CẤM dùng/xưng hô: anh, chị hay anh/chị, họ.
- CẤM đưa các đoạn nháp, suy nghĩ nội bộ, thinking-> chỉ trả nội dung hoàn chỉnh.
- CẤM hiển thị bất kỳ nội dung meta nào, bao gồm nhưng không giới hạn: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- Những phần này chỉ phục vụ xử lý nội bộ và KHÔNG được xuất ra kết quả cuối cùng.
- Output chỉ bao gồm nội dung phân tích hoàn chỉnh theo format yêu cầu.

[HƯỚNG DẪN NỘI DUNG]
### Giải mã tình yêu
### 1. Chỉ số cốt lõi (Số Chủ Đạo)
Khi quỹ đạo Số {$lp1} ({$n1}) và Số {$lp2} ({$n2}) song hành, hai trường năng lượng này cộng hưởng hay đối kháng? Yếu tố nào đóng vai trò neo giữ mối quan hệ?

### 2. Tiềm thức (Số Linh Hồn)
Dựa vào Linh Hồn {$soul1} và {$soul2}, phân tích khát khao thực sự của hai người trong tình yêu. Có sự mâu thuẫn nào giữa cách họ thể hiện ra bên ngoài và những gì sâu thẳm bên trong họ thực sự mong muốn không?

### 3. Chỉ Số Thái Độ
Khi đối mặt với áp lực hoặc mâu thuẫn, phản xạ tự nhiên của Thái độ {$att1} ({$n1}) và Thái độ {$att2} ({$n2}) sẽ bộc lộ như thế nào? Phân tích xu hướng hành xử của hai người để tìm ra những rào cản dễ gây hiểu lầm nhất trong giao tiếp.

[TAB_RESULT]
(Nội dung được đặt ở đây)
[/TAB_RESULT]
TXT;
    }

}