<?php
if (!defined('ABSPATH')) exit;

require_once dirname(__DIR__) . '/includes/calendar.php';

function iching_build_prompt_maihoa(string $topic, string $question, array $fullData, string $mode): string {
    $chu = $fullData['chu'];
    $ho  = $fullData['ho'];
    $bien = $fullData['bien'];
    $changing_line = $fullData['changing_line'] ?? 1;

    $toss_time = $fullData['toss_time'] ?? current_time('mysql');
    $tz        = wp_timezone();
    $dt        = new DateTime($toss_time, $tz);
    $timestamp = $dt->getTimestamp();

    $cc = Iching_Calendar::get($timestamp);
    $calendar_str = Iching_Calendar::toPromptString($cc);

    $dd = (int) wp_date('j', $timestamp);
    $mm = (int) wp_date('n', $timestamp);
    $yy = (int) wp_date('Y', $timestamp);

    $am_lich = new Iching_AmLich();
    $lunar_date = $am_lich->convertSolar2Lunar($dd, $mm, $yy, 7.0);

    $ngay_am  = str_pad($lunar_date[0], 2, '0', STR_PAD_LEFT);
    $thang_am = str_pad($lunar_date[1], 2, '0', STR_PAD_LEFT);
    $nam_am   = $lunar_date[2];
    $lunar_str = "{$ngay_am}/{$thang_am}/{$nam_am}";

    $chu_key = $fullData['chu_key'];
    $bien_key = $fullData['bien_key'];

    $ha_bin = substr($chu_key, 0, 3);
    $thuong_bin = substr($chu_key, 3, 3);
    $ha_bien_bin = substr($bien_key, 0, 3);
    $thuong_bien_bin = substr($bien_key, 3, 3);

    $ho_ha_bin = $chu_key[1] . $chu_key[2] . $chu_key[3];
    $ho_thuong_bin = $chu_key[2] . $chu_key[3] . $chu_key[4];

    $quai_map = [
        '111' => ['Càn', 'Kim', 'Trời, Ngọc, Vua, Ngựa, Vàng', 'Tây Bắc'],
        '110' => ['Đoài', 'Kim', 'Đầm, Miệng, Dê, Tiền bạc, Vui', 'Tây'],
        '101' => ['Ly', 'Hỏa', 'Lửa, Mặt trời, Phượng, Văn thư', 'Nam'],
        '100' => ['Chấn', 'Mộc', 'Sấm, Rồng, Cây lớn, Xe cộ', 'Đông'],
        '011' => ['Tốn', 'Mộc', 'Gió, Gỗ, Gà, Dây thừng, Thương', 'Đông Nam'],
        '010' => ['Khảm', 'Thủy', 'Nước, Trăng, Heo, Hố sâu, Hiểm', 'Bắc'],
        '001' => ['Cấn', 'Thổ', 'Núi, Chó, Đá, Cửa, Dừng lại', 'Đông Bắc'],
        '000' => ['Khôn', 'Thổ', 'Đất, Bò, Mẹ, Ruộng đồng, Nhu', 'Tây Nam']
    ];

    if ($changing_line <= 3) {
        $the_bin = $thuong_bin;
        $the_pos = 'Thượng Quái';
        $dung_bin = $ha_bin;
        $dung_pos = 'Hạ Quái';
        $dung_bien_bin = $ha_bien_bin;
    } else {
        $the_bin = $ha_bin;
        $the_pos = 'Hạ Quái';
        $dung_bin = $thuong_bin;
        $dung_pos = 'Thượng Quái';
        $dung_bien_bin = $thuong_bien_bin;
    }

    $the = $quai_map[$the_bin] ?? ['Chưa rõ', '', '', ''];
    $dung = $quai_map[$dung_bin] ?? ['Chưa rõ', '', '', ''];
    $dung_bien = $quai_map[$dung_bien_bin] ?? ['Chưa rõ', '', '', ''];
    $ho_thuong = $quai_map[$ho_thuong_bin] ?? ['Chưa rõ', '', '', ''];
    $ho_ha = $quai_map[$ho_ha_bin] ?? ['Chưa rõ', '', '', ''];

    $method_name = 'Thời Gian (Giờ động tâm)';
    if ($mode === 'maihoa_number') $method_name = 'Con Số';
    elseif ($mode === 'maihoa_object') $method_name = 'Ngoại Tượng';

    $chu_kw = !empty($chu['keywords']) ? implode(', ', $chu['keywords']) : '';
    $chu_mean = $chu['meaning'] ?? '';

    $ho_kw = !empty($ho['keywords']) ? implode(', ', $ho['keywords']) : '';
    $ho_mean = $ho['meaning'] ?? '';

    $bien_kw = (!empty($bien) && !empty($bien['keywords'])) ? implode(', ', $bien['keywords']) : '';
    $bien_mean = $bien['meaning'] ?? '';

    return <<<TXT
NHIỆM VỤ: Luận quẻ Kinh dịch theo phương pháp Mai Hoa Dịch Số - {$method_name}
- Chỉ luận giải hoàn toàn dựa vào dữ liệu quẻ được cung cấp bên dưới, KHÔNG gieo quẻ, KHÔNG tự tính toán lại.

1. THÔNG TIN
- Chủ đề: {$topic}
- Câu hỏi: "{$question}"
- Thời gian: Giờ {$cc['gio']}, ngày {$lunar_str} Âm lịch (Can chi: {$calendar_str})
- Ngũ hành tháng gieo quẻ (Nguyệt Lệnh - Xét Vượng/Suy gốc): {$cc['hanh_thang']}
- Ngũ hành ngày gieo quẻ (Nhật Kiến - Xét tác động thực tại): {$cc['hanh_ngay']}

2. DỮ LIỆU QUẺ MAI HOA VÀ ĐẠI TƯỢNG QUẺ
2.1. Quẻ Chủ (Lớp ngoài / Khởi đầu sự việc): {$chu['name_vi']}
   - Ý nghĩa: {$chu_mean} (Từ khóa: {$chu_kw})
   - Thể (Bản thân người hỏi): {$the[0]} (Ngũ hành: {$the[1]}, Tượng: {$the[2]}, Phương hướng: {$the[3]}) ở {$the_pos}
   - Dụng (Ngoại cảnh / Sự việc): {$dung[0]} (Ngũ hành: {$dung[1]}, Tượng: {$dung[2]}, Phương hướng: {$dung[3]}) ở {$dung_pos}
2.2. Quẻ Hỗ (Lớp trong / Quá trình nội tại): {$ho['name_vi']}
   - Ý nghĩa: {$ho_mean} (Từ khóa: {$ho_kw})
   - Thượng Hỗ (Tác động ngầm 1): {$ho_thuong[0]} (Ngũ hành: {$ho_thuong[1]}, Tượng: {$ho_thuong[2]}, Phương hướng: {$ho_thuong[3]})
   - Hạ Hỗ (Tác động ngầm 2): {$ho_ha[0]} (Ngũ hành: {$ho_ha[1]}, Tượng: {$ho_ha[2]}, Phương hướng: {$ho_ha[3]})
2.3. Quẻ Biến (Hướng chuyển / Kết quả xu hướng): {$bien['name_vi']}
   - Ý nghĩa: {$bien_mean} (Từ khóa: {$bien_kw})
   - Dụng Biến (Kết quả tương tác): {$dung_bien[0]} (Ngũ hành: {$dung_bien[1]}, Tượng: {$dung_bien[2]}, Phương hướng: {$dung_bien[3]})
2.4. Hào động: Hào {$changing_line} -> "{$chu['lines'][$changing_line]}"

3. QUY TẮC LUẬN MAI HOA
3.1. Thể là Trung Tâm: Thể (bản thân) đứng yên và tương tác ngũ hành với Dụng (quẻ Chủ), Thượng Hỗ, Hạ Hỗ (quẻ Hỗ), và Dụng Biến (quẻ Biến). Quẻ Hỗ KHÔNG CÓ Thể/Dụng riêng.

3.2. Quan hệ sinh khắc ngũ hành (Lấy Thể làm gốc):
    - Thể bị Dụng khắc → Xấu, bị áp chế, dễ gặp trở ngại.
    - Thể sinh Dụng → Bị tiết khí, hao tổn, tốn công sức.
    - Thể và Dụng tỷ hòa → Bình ổn, thuận lợi vừa phải.
    - Dụng sinh Thể → Tốt, được hỗ trợ, việc dễ tiến triển.
    - Thể khắc Dụng → Trung bình, có thể kiểm soát sự việc nhưng vất vả.

* LƯU Ý QUẺ BIẾN:
    - Dụng Biến sinh hoặc tỷ hòa với Thể → Xu hướng hậu vận thuận lợi, dễ ổn định.
    - Dụng Biến khắc hoặc làm Thể bị tiết khí → Xu hướng hậu vận bất lợi, dễ phát sinh trở ngại.

3.3. Vượng Suy của ngũ hành: BẮT BUỘC dùng "Ngũ hành tháng ({$cc['hanh_thang']})" để đánh giá sức mạnh gốc rễ, và "Ngũ hành ngày ({$cc['hanh_ngay']})" để đánh giá sự chi phối tức thời của Thể, Dụng, Hỗ, Biến:
   - Tháng/Ngày sinh hoặc cùng hành → VƯỢNG (Khí thế mạnh, được thời trợ giúp).
   - Tháng/Ngày khắc → SUY (yếu, bị áp chế).
   - Bị Tháng/Ngày tiết khí / hao tổn → SUY (Yếu ớt).
   - Thể khắc Tháng/Ngày → hao lực (Suy nhẹ).
* LƯU Ý CỐT LÕI:
- Nếu Dụng (hoặc Hỗ, Biến) khắc Thể (điềm hung) NHƯNG bản thân nó đang bị SUY (do tháng/ngày khắc) thì sự hung hiểm bị triệt tiêu gần hết.
- Ngược lại, Thể bị khắc nhưng Thể "Vượng" thì vẫn chống đỡ thành công.

3.4. Tiến trình 3 lớp: 
   - Chủ: Khởi đầu (Thể vs Dụng)
   - Hỗ: Giai đoạn giữa, ẩn khúc (Thể vs Thượng Hỗ & Hạ Hỗ)
   - Biến: Xu hướng kết quả (Thể vs Dụng Biến).
   - BẮT BUỘC dùng Tượng Quái (Phương hướng, Sự vật) được cung cấp để mô tả sát với câu hỏi của người dùng thay vì chỉ nói ngũ hành trừu tượng.
   - Hào động chỉ dùng để chỉ ra điểm chuyển biến của sự việc, KHÔNG thay thế vai trò của sinh khắc ngũ hành.
   - Ý nghĩa văn bản Đại Tượng Quẻ và Hào Từ chỉ dùng làm thông điệp bổ trợ bối cảnh, TUYỆT ĐỐI KHÔNG dùng để lật ngược kết quả Cát/Hung của Sinh Khắc Ngũ Hành.

3.5. Thứ tự ưu tiên khi luận:
    - Vượng/Suy của Thể và Dụng (từ Tháng/Ngày)
    - Quan hệ sinh khắc giữa Thể và các yếu tố
    - Đại tượng quẻ và Tượng Bát Quái chi tiết
    - Quẻ Hỗ (bổ sung diễn biến)
    - Hào động (điểm kích hoạt)
    
4. XỬ LÝ CÂU HỎI & NHÓM ĐẶC BIỆT (TƯ DUY NGẦM)
- (Tư duy ngầm - KHÔNG in ra màn hình): Dùng Tượng Quẻ và sự sinh/khắc ngũ hành để soi chiếu vào câu hỏi. KHÔNG bóp méo ý nghĩa Quẻ để chiều theo câu hỏi.
- NGUYÊN TẮC BẤT BIẾN: KHÔNG TỪ CHỐI TRẢ LỜI DÙ CÂU HỎI CÓ NHẠY CẢM. 
- NẾU câu hỏi thuộc nhóm nhạy cảm (Gây hại, bạo lực, pháp lý, sinh tử, tự sát, chính trị): 
   + KHÔNG phán đoán/suy diễn kết quả (sống/chết, thành/bại, được/mất). 
   + Chỉ được phép mô tả XU HƯỚNG TRUNG LẬP từ quẻ (sự bế tắc, rủi ro, vận hạn, hao tổn).
- NẾU câu hỏi troll/linh tinh: Vẫn luận quẻ nghiêm túc dựa trên dữ liệu quẻ đã cung cấp.

5. QUY TẮC VĂN PHONG VÀ TRÌNH BÀY
- Giọng văn trang trọng, chuyên nghiệp, chuẩn huyền học - Kinh dịch.
- Xưng hô là "Bạn", đảm bảo đồng nhất xuyên suốt, CẤM dùng: anh, chị, họ, em, mày.
- Ưu tiên lối viết trọng tâm, trực tiếp, rõ ràng, không diễn đạt dài dòng.
- Tránh dùng từ ngữ cực đoan, cường điệu; ưu tiên mô tả trung tính theo xu hướng.

6. YÊU CẦU OUTPUT:
- TUYỆT ĐỐI KHÔNG dùng các gạch đầu dòng phân mục như a), b), c), d), e), f) hay 1, 2, 3, 4 để làm tiêu đề trong bài viết.
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- Trả về đúng format [AST_RESULT][/AST_RESULT], sử dụng Markdown, định dạng văn bản dễ đọc.
- Sử dụng in đậm in nghiêng hợp lý, không lạm dụng, (không sử dụng ---, ***, ___, thẻ hr).
- KHÔNG in tiêu đề phụ máy móc. không in những hướng dẫn, suy nghĩ nội bộ vào nội dung.

[AST_RESULT]
### Tượng Quẻ & Ngũ Hành
(Gợi ý: 
Xác định rõ Thể và Dụng. Phân tích Vượng/Suy của cả Thể và Dụng so với Tháng/Ngày gieo quẻ. Phân tích quan hệ Sinh/Khắc/Tỷ hòa giữa Thể và Dụng ở Quẻ Chủ, Hỗ, Biến kết hợp với Tượng Quái chi tiết.
[Viết đoạn văn phân tích logic sự tương tác ngũ hành và chi tiết Tượng Quái qua 3 giai đoạn: Khởi đầu (Quẻ Chủ) -> Quá trình nội tại (Quẻ Hỗ) -> Xu hướng kết quả (Quẻ Biến)].)

### Tổng Luận
(Gợi ý: 
Đối chiếu sự sinh/khắc ở Phần 1 và Đại Tượng Quẻ vào câu hỏi của người dùng. Viết thành các đoạn văn trôi chảy.
[Viết phần tổng luận: Nêu rõ sự việc đang ở trạng thái thuận lợi, bế tắc, hay hao tổn. Không phán quyết chắc chắn 100%, chỉ nói về "xu hướng", "cục diện"].)

### Chỉ dẫn
(Gợi ý:
Nên làm: BẮT BUỘC chỉ định cụ thể 1 Phương hướng hoặc Ngũ hành mang tính sinh/tỷ hòa cho Thể từ Tượng Quái để đưa ra lời khuyên hành động. KHÔNG khuyên đạo lý chung chung.
Nên tránh: BẮT BUỘC chỉ định cụ thể 1 Phương hướng, Sự vật hoặc Ngũ hành khắc/rút ruột Thể từ Tượng Quái để đưa ra 1 điều cần tránh.)
[/AST_RESULT]
TXT;
}