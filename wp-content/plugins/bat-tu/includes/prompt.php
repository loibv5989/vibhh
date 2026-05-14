<?php

if (!defined('ABSPATH')) {
    exit;
}

class Battu_Prompt {

    public static function gatekeeper(string $question): string {
        return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây là một câu hỏi kiến thức thông thường hay là câu hỏi có ý nghĩa luận giải, dự đoán sự việc, xem hung cát liên quan đến lá số Bát Tự Tứ Trụ.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi chứa nội dung nhạy cảm, bạo lực, phi pháp, xúc phạm hoặc nguy hiểm.
- Không được từ chối trả lời dưới bất kỳ hình thức nào.
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại theo quy tắc bên dưới.

Câu hỏi: {$question}

Quy tắc phân loại:
Nếu câu hỏi mang tính tìm hiểu kiến thức chung, định nghĩa, cách dùng, nói chuyện phiếm, từ ngữ vô nghĩa:
(Ví dụ: "Bát Tự là gì?", "Nhật Chủ là gì?", "Thập Thần có ý nghĩa gì?", "Chào bạn")
 → Trả về: KHONG

- Cấu trúc lá số / Cường nhược / Dụng thần / Kỵ thần / Tổng quan lá số → TONGQUAN
- Tính cách / Tâm lý / Điểm mạnh điểm yếu / Nóng tính / Cứng đầu / Hướng nội hướng ngoại / Tư duy / Cảm xúc / Bản năng → TINHCACH
- Công việc / Sự nghiệp / Học hành / Thi cử / Thăng chức / Kiện tụng / Tù tội / Nghề nghiệp phù hợp → CONGVIEC
- Tiền bạc / Tài lộc / Đầu tư / Kinh doanh / Mua bán nhà đất xe cộ / Cổ phiếu / Crypto / Vay mượn → TAILOC
- Tình duyên / Hôn nhân / Tình yêu / Vợ chồng / Người yêu / Phu thê / Ngoại tình / Ly hôn → TINHCAM
- Sức khỏe / Bệnh tật / Tai nạn / Tính mạng / Bình an / Thọ yểu → SUKKHOE
- Cha mẹ / Con cái / Mang thai / Sinh đẻ / Anh chị em / Gia đạo / Lục thân → GIADAO
- Năm nay / Tháng này / Đại vận / Vận hạn / Lưu niên / Tương lai gần / Hung cát năm nay → VANHAN
- Xuất ngoại / Di cư / Định cư nước ngoài / Chuyển nhà / Đi xa / Dịch chuyển / Du học → XUATHANH
- Hợp tác / Đối tác kinh doanh / Kết nghĩa / Bạn bè / Quan hệ xã hội / Tin người được không → QUANHE
- Pháp lý / Tranh chấp / Kiện tụng / Hợp đồng / Mâu thuẫn với cơ quan nhà nước → PHAPLY
- Tâm linh / Vong linh / Cô đơn tâm linh / Bị trùng / Hay gặp điều kỳ lạ → TAMLINH

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất sau khi đã phân loại:
KHONG / TONGQUAN / TINHCACH / CONGVIEC / TAILOC / TINHCAM / SUKKHOE / GIADAO / VANHAN / XUATHANH / QUANHE / PHAPLY / TAMLINH
Không giải thích, không phân tích nội bộ. Không thêm bất kỳ nội dung nào khác.
TXT;
    }

    private static function rules(): string {
        return <<<RULES

YÊU CẦU OUTPUT:
- KHÔNG dùng gạch đầu dòng phân mục như a), b), c) hay 1, 2, 3 làm tiêu đề.
- Sử dụng in đậm/in nghiêng hợp lý để nhấn mạnh nội dung nhưng không lạm dụng.
- ĐỊNH DẠNG: Dùng Markdown chuẩn (không dùng ---, ***, ___ để tạo thẻ hr phân tách).
- Định dạng văn bản dễ đọc. 100% tiếng Việt, nếu có thuật ngữ tiếng anh/trung quốc phải kèm bản dịch tiếng Việt trong ngoặc.
- PHẢI TRẢ ĐÚNG format [AST_RESULT][/AST_RESULT].

LỆNH CẤM:
- Cấm suy diễn ngoài dữ liệu lá số Bát Tự đã cung cấp.
- CẤM tự tính toán lại => chỉ sử dụng dữ liệu đã cung cấp.
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- CẤM đưa các đoạn nháp, suy nghĩ nội bộ, thinking, Constraint Checklist, Confidence Score, validation, reasoning, notes, chào hỏi mở đầu, CTA vào nội dung OUTPUT.
- CẤM sử dụng icon, emoji trong bài viết.
- CẤM dùng dấu en dash dài –.
- CẤM mọi ngôn ngữ/ từ ngữ dạy đời, đạo lý, PR, tâng bốc, cường điệu.

- CẤM dùng ngôn ngữ nhân cách hóa hoặc ẩn dụ quyền lực, các từ/cụm bị cấm: thống trị, củng cố, vị thế, chi phối, lấn át, áp đảo, chiếm ưu thế, thăng hoa, trỗi dậy, bứt phá.
- CẤM TUYỆT ĐỐI đưa ra các lời khuyên tâm linh sáo rỗng, mê tín, chung chung như: "tu tâm dưỡng tính", "làm việc thiện", "tích phúc", "tích đức", "phóng sinh", "cúng bái", "phong thủy".
=> Mọi nhận định PHẢI xuất phát từ cơ chế sinh khắc cụ thể trong lá số: chỉ rõ Thập Thần nào, hành nào, tác động ra sao, hệ quả. 
   Không phát biểu kết luận chung nếu không có dữ liệu lá số dẫn đến kết luận đó.
   
- Tiêu đề:
    + Tiêu đề (các thẻ Heading Markdown như ##, ###) CHỈ ĐƯỢC PHÉP chứa đúng [Chủ Đề] hoặc mốc thời gian.
    + CẤM TUYỆT ĐỐI thêm các cụm từ cảm thán, đánh giá, nhận xét, hoặc các dấu phân cách như gạch ngang dài (–), dấu hai chấm (:) vào tiêu đề.
- Nội dung:
    + Cấm viết theo kiểu 2 vế 'Tiêu đề: Nội dung'. CẤM dùng dấu hai chấm để chia vế câu -> Hãy viết thành các câu văn hoàn chỉnh tự nhiên, hoặc nếu là list thì cũng phải viết list câu trực tiếp.
    + CẤM: Các cụm từ máy móc như: chuyên sâu, toàn diện, rõ rệt, bóc tách, đáng kể, cốt lõi, giao thoa -> Tóm lại, cấm dùng từ CÓ VẺ TRỊNH TRỌNG, khái quát cao, và nghe rất "chuyên gia" nhưng sáo rỗng bề nổi.

RULES;
    }

    private static function format_tu_tru(array $tu_tru): string {
        $tru_labels = [
            'nam'   => 'Trụ Năm',
            'thang' => 'Trụ Tháng',
            'ngay'  => 'Trụ Ngày',
            'gio'   => 'Trụ Giờ',
        ];

        $hanh_map = [
            'kim' => 'Kim', 'moc' => 'Mộc', 'thuy' => 'Thủy',
            'hoa' => 'Hỏa', 'tho' => 'Thổ',
        ];

        $than_map = [
            'Thực' => 'Thực Thần', 'Thương' => 'Thương Quan',
            'T.Tài' => 'Thiên Tài', 'C.Tài' => 'Chính Tài',
            'Sát' => 'Thất Sát', 'Quan' => 'Chính Quan',
            'Kiêu' => 'Thiên Ấn', 'Ấn' => 'Chính Ấn',
            'Tỷ' => 'Tỷ Kiên', 'Kiếp' => 'Kiếp Tài',
            'NHẬT CHỦ' => 'Nhật Chủ',
        ];

        $str = '';
        foreach ($tru_labels as $key => $label) {
            if (empty($tu_tru[$key])) continue;
            $t = $tu_tru[$key];

            $can_el = $hanh_map[$t['can_element']] ?? $t['can_element'];
            $chi_el = $hanh_map[$t['chi_element']] ?? $t['chi_element'];
            $than_short = $than_map[$t['thap_than_short']] ?? $t['thap_than_short'];

            $str .= "• {$label}: {$t['can_name']} {$t['chi_name']}\n";
            $str .= "  - Thiên Can: {$t['can_name']} (hành {$can_el}), Thập Thần: {$than_short}\n";
            $str .= "  - Địa Chi: {$t['chi_name']} (hành {$chi_el}), Trường Sinh: {$t['truong_sinh']}\n";

            if (!empty($t['tang_can'])) {
                $tc_parts = array_map(
                    function($tc) use ($than_map) {
                        $tc_than = $than_map[$tc['thap_than_short']] ?? $tc['thap_than_short'];
                        return "{$tc['can']}({$tc_than},{$tc['truong_sinh']})";
                    },
                    $t['tang_can']
                );
                $str .= "  - Tàng Can: " . implode(', ', $tc_parts) . "\n";
            }

            if (!empty($t['than_sat'])) {
                $str .= "  - Thần Sát: " . implode(', ', $t['than_sat']) . "\n";
            }

            if (!empty($t['nap_am'])) {
                $str .= "  - Nạp Âm: {$t['nap_am']}\n";
            }
        }

        return $str;
    }

    private static function format_dai_van(array $dai_van, int $current_year): array {
        $van_trinh = $dai_van['van_trinh'] ?? [];
        $dai_van_hien_tai = 'Chưa xác định';

        $hanh_map = [
            'kim' => 'Kim', 'moc' => 'Mộc', 'thuy' => 'Thủy',
            'hoa' => 'Hỏa', 'tho' => 'Thổ',
        ];

        foreach ($van_trinh as $v) {
            $nam_bat_dau = (int)($v['nam_bat_dau'] ?? 0);
            if ($current_year >= $nam_bat_dau && $current_year <= $nam_bat_dau + 9) {
                $tuoi_ket_thuc = (int)$v['tuoi'] + 9;
                $nam_ket_thuc  = $nam_bat_dau + 9;
                $dai_van_hien_tai = "{$v['can_name']} {$v['chi_name']} ({$v['tuoi']}-{$tuoi_ket_thuc} tuổi, {$nam_bat_dau}-{$nam_ket_thuc})";
                break;
            }
        }

        $van_str = '';
        foreach ($van_trinh as $v) {
            $can_el = $hanh_map[$v['can_element']] ?? $v['can_element'];
            $van_str .= "  - {$v['nam_bat_dau']} (tuổi {$v['tuoi']}): {$v['can_name']} {$v['chi_name']}";
            if (!empty($v['can_element'])) $van_str .= " [{$can_el}]";
            $van_str .= "\n";
        }

        return [
            'dai_van_hien_tai' => $dai_van_hien_tai,
            'van_str'          => $van_str,
        ];
    }

    public static function build(array $formatted): string {
        $tt  = $formatted['thong_tin'];
        $tu  = $formatted['tu_tru'];
        $dv  = $formatted['dai_van'];

        $current_year = (int)date('Y');
        $tuoi         = $current_year - (int)substr($tt['ngay_sinh'] ?? '', 0, 4) + 1;

        $tu_tru_str   = self::format_tu_tru($tu);
        $dv_data      = self::format_dai_van($dv, $current_year);
        $rules        = self::rules();

        $gioi_tinh = $tt['gioi_tinh'] ?? 'Không rõ';

        $than_info = $formatted['than_vuong_nhuoc'] ?? [];
        $dung_info = $formatted['dung_than'] ?? [];
        $than_str = ($than_info['ket_qua'] ?? '') . ($than_info['muc_do'] ? ' (' . $than_info['muc_do'] . ')' : '');
        $dung_str = implode(', ', $dung_info['dung_than'] ?? []);
        $ky_str = implode(', ', $dung_info['ky_than'] ?? []);

        return <<<PROMPT
Dựa trên hệ thống Tử Bình Bát Tự cổ học kết hợp phương pháp luận giải hiện đại của Thiệu Vĩ Hoa.
Hãy luận giải chi tiết lá số Bát Tự sau đây:

Họ và tên: {$tt['ho_ten']} (Tuổi: {$tuoi})
Giới tính: {$gioi_tinh}
Ngày sinh Dương lịch: {$tt['ngay_sinh']}, Giờ sinh: {$tt['gio_sinh']}
Nhật Chủ: {$tt['nhat_chu']} (hành {$tt['nhat_chu_hanh']})
Nhật Trụ: {$tt['nhat_tru']}
Vượng suy Nhật Chủ: {$tt['vuong_suy']} (mùa {$tt['mua']})
Thân: {$than_str}
Dụng Thần: {$dung_str}
Kỵ Thần: {$ky_str}
Nạp Âm năm sinh: {$tt['nap_am_nam']}

TỨ TRỤ CHI TIẾT:
{$tu_tru_str}
THÔNG TIN ĐẠI VẬN:
- Khởi vận: {$dv['tuoi_khoi_van']} tuổi
- Chiều hành vận: {$dv['chieu_hanh_van']}
- Đại Vận hiện tại: {$dv_data['dai_van_hien_tai']}
- Toàn bộ Đại Vận:
{$dv_data['van_str']}

QUY TẮC ĐẠI VẬN:
- Đại Vận đi theo chiều {$dv['chieu_hanh_van']}, tuyệt đối không tự suy diễn lại chiều vận.
- Các Đại Vận phải được đọc đúng theo thứ tự đã cung cấp bên dưới.
- Không tự đảo thứ tự Đại Vận.

YÊU CẦU DIỄN ĐẠT:
- Giọng điệu chuyên nghiệp, trang trọng, chuẩn huyền học bát tự - tứ trụ 
- XƯNG HÔ: là Bạn (có thể dùng Tên người dùng, sử dụng 1 cách tự nhiên) đảm bảo xuyên suốt. Cấm dùng "Anh/Chị/Em", người này, họ.
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu.
- Trình bày mạch lạc, không dài dòng.
- Tránh diễn đạt lặp ý, ví dụ blockquote (nếu có) không được viết lại y nguyên diễn giải trước đó, phải diễn giải trực quan hơn hoặc phương pháp khác để không bị lặp template.
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề, không sử dụng ngôn ngữ dạy đời.
- KHÔNG viết lại các thông tin đã cung cấp -> Hãy luận giải trực tiếp.
- Nếu họ tên là Đương Số, hoặc tên không có dấu Tiếng Việt hoặc biệt danh -> Nghĩa là người dùng không muốn công khai -> xưng hô là Bạn.
- Nếu tuổi nhỏ hơn 10, tức cha mẹ / người thân đang xem hộ -> xưng hô là Bé, ví dụ: Bé có Nhật Chủ...
{$rules}

[HƯỚNG DẪN NỘI DUNG]
( Viết thành các đoạn văn ngắn gọn, súc tích, mạch lạc. Tuyệt đối bám sát các tiêu chí sau:

## Luận Giải Lá Số Bát Tự của {$tt['ho_ten']}
### Nhật Chủ {$tt['nhat_chu']} {$tt['nhat_chu_hanh']}
Mở đầu bằng một câu hình tượng ngắn về bản chất của Nhật Chủ (ví dụ: Mậu Thổ là đất dày, núi cao; Giáp Mộc là cây cổ thụ...) trước khi luận cường/nhược. 
Sau đó luận ngắn gọn lý do Thân nhược/vượng dựa trên mùa sinh và tác động từ các trụ còn lại. Kết phần này bằng một block gạch đầu dòng tiêu đề "Điểm mạnh của trụ này" liệt kê các yếu tố trợ lực cụ thể (thiên can, địa chi, tàng can nổi bật).

### Tính cách
Mở đầu bằng một bảng 2 cột: cột "Mặt" (Nổi trội / Tư duy / Quan hệ / Điểm yếu) và cột "Biểu hiện" tóm tắt ngắn. Sau bảng, viết 2-3 đoạn văn phân tích sâu hơn dựa trên các Thập Thần nổi bật, các điểm đặc biệt (tự hình, hợp, xung) nếu có.
Sau các đoạn phân tích chính, viết thêm một đoạn blockquote (dùng ký hiệu >) phân tích vai trò của Thực Thần và Kiếp Tài trong việc định hình tính cách (năng khiếu biểu đạt, thẩm mỹ, tư duy độc lập...) nếu có trong lá số.

### Sự nghiệp & Tài lộc
(Luận xu hướng nghề nghiệp, năng lực cạnh tranh, con đường công danh dựa trên Quan Sát, Thực Thương, cấu cục đặc biệt nếu có.)
(Luận khả năng kiếm tiền, tích lũy, hay hao tài dựa trên Tài Tinh, mối quan hệ với Nhật Chủ và Thực Thương. Chỉ rõ tiền đến từ đâu, dễ mất ở đâu.)
(Sau phần luận chính, viết thêm một đoạn blockquote (dùng ký hiệu >) phân tích cấu trúc Thực Thần sinh Tài nếu có: hệ quả (thân nhược tài vượng, hao tài vì người khác, cạnh tranh ngầm từ Kiếp Tài...).)

### Tình cảm hôn nhân
(Với nam luận theo Tài Tinh, với nữ luận theo Quan Sát. Luận đặc điểm người phối ngẫu, cung Thê, các điểm xung hình nếu có. Nêu hướng hóa giải cụ thể theo cơ chế sinh khắc, không dùng lời khuyên chung chung.)
(Sau phần luận chính, viết thêm một đoạn blockquote (dùng ký hiệu >) nêu cụ thể hướng hóa giải dựa trên cơ chế sinh khắc trong lá số (ví dụ: dùng Ấn Tinh Hỏa để hóa giải áp lực từ Tài Tinh Thủy).)

### Đại vận hiện tại {$dv_data['dai_van_hien_tai']}
Chỉ phân tích 3-5 Đại Vận quan trọng có tương tác rõ với Dụng Thần/Kỵ Thần. Ưu tiên Đại Vận hiện tại. Trong Đại Vận hiện tại, nếu có Lưu Niên nổi bật (Dụng Thần hoặc Kỵ Thần cùng xuất hiện mạnh) thì chỉ rõ năm cụ thể và lý do. Không liệt kê tuần tự toàn bộ các mốc thập niên.
KHÔNG trình bày dạng danh sách đầy đủ theo từng thập niên, Ưu tiên chiều sâu phân tích hơn số lượng Đại Vận.

Kết bài bằng một đoạn blockquote (dùng ký hiệu >) tóm tắt chân dung người này trong 4-6 câu: tính cách cốt lõi, quỹ đạo cuộc đời tổng thể, giai đoạn quan trọng nhất, và 1-2 điểm lưu ý.
)

Một số ví dụ SAI/ĐÚNG về diễn đạt và chuyên môn:
VD1:
+ SAI (Chung chung không có dẫn chứng): Bạn nên tập trung vào việc phát triển năng lực chuyên môn và tích lũy kinh nghiệm vững chắc hơn là tìm kiếm lợi nhuận nhanh chóng...
+ ĐÚNG: Nhật Chủ nhược, Thiên Tài Nhâm Thủy lại là Kỵ Thần, thân yếu không gánh được tài, nên các hướng đi đòi hỏi vốn lớn, đầu tư đầu cơ hoặc kinh doanh tự do sẽ dễ hao hơn sinh. 
Bính Hỏa Thiên Ấn là Dụng Thần, con đường phù hợp hơn là làm công ăn lương, phát triển chuyên môn kỹ thuật hoặc tư vấn, nơi Ấn Tinh được dùng trực tiếp.

VD2:
+ SAI (dạy đời, đạo lý): Bạn cần cân bằng và sự thấu hiểu để duy trì mối quan hệ hòa hợp ...
+ ĐÚNG: Tài Tinh là Kỵ Thần, Nhật Chủ nhược, điểm này thường dẫn đến việc người vợ có cá tính mạnh hoặc áp lực tài chính từ hôn nhân đè lên bạn. 
Quý Thủy Chính Tài tàng trong Thìn nhật trụ cho thấy tình cảm có nhưng bị khuất, không dễ bộc lộ, dễ xảy ra tình trạng hai bên không nói thẳng dẫn đến tích tụ mâu thuẫn. 
Giai đoạn Dụng Thần Hỏa Thổ vượng trong vận mới đủ lực gánh Tài, hôn nhân mới ổn định hơn.

[AST_RESULT]
(Nội dung luận giải được đặt ở đây)
[/AST_RESULT]
PROMPT;
    }

    public static function build_qa(array $formatted, string $user_question, string $category): string {
        $tt  = $formatted['thong_tin'];
        $tu  = $formatted['tu_tru'];
        $dv  = $formatted['dai_van'];

        $current_year = (int)date('Y');
        $tuoi         = $current_year - (int)substr($tt['ngay_sinh'] ?? '', 0, 4) + 1;

        $tu_tru_str = self::format_tu_tru($tu);
        $dv_data    = self::format_dai_van($dv, $current_year);
        $rules      = self::rules();

        $gioi_tinh = $tt['gioi_tinh'] ?? 'Không rõ';

        $than_info = $formatted['than_vuong_nhuoc'] ?? [];
        $dung_info = $formatted['dung_than'] ?? [];
        $than_str = ($than_info['ket_qua'] ?? '') . ($than_info['muc_do'] ? ' (' . $than_info['muc_do'] . ')' : '');
        $dung_str = implode(', ', $dung_info['dung_than'] ?? []);
        $ky_str = implode(', ', $dung_info['ky_than'] ?? []);

        $category_map = [
            'TONGQUAN'  => 'Tổng quan',
            'TINHCACH'  => 'Tính cách',
            'CONGVIEC'  => 'Công việc / Sự nghiệp',
            'TAILOC'    => 'Tài lộc',
            'TINHCAM'   => 'Tình duyên',
            'SUKKHOE'   => 'Sức khỏe',
            'GIADAO'    => 'Gia đạo',
            'VANHAN'    => 'Vận hạn',
            'XUATHANH'  => 'Xuất ngoại / Di chuyển',
            'QUANHE'    => 'Quan hệ / Hợp tác',
            'PHAPLY'    => 'Pháp lý / Tranh chấp',
            'TAMLINH'   => 'Tâm linh',
        ];

        $category_vi = $category_map[$category] ?? $category;

        $topic_focus = '';
        if ($category === 'TONGQUAN') {
            $topic_focus = "TẬP TRUNG VÀO: Trực tiếp trả lời câu hỏi dựa trên Cấu trúc Tứ Trụ gốc, Nhật Chủ vượng/suy, và các Thần Sát/Thập Thần có sẵn. CHỈ phân tích Đại Vận/Lưu Niên nếu người dùng có hỏi kèm yếu tố thời gian.";
        } elseif ($category === 'TINHCAM') {
            $topic_focus = "TẬP TRUNG VÀO: Phu/Thê Tinh (nam luận Tài Tinh, nữ luận Quan Sát), Cung Phu Thê (Chi ngày). Bỏ qua phân tích tài chính/công việc nếu không liên quan trực tiếp đến câu hỏi.";
        } elseif ($category === 'TAILOC') {
            $topic_focus = "TẬP TRUNG VÀO: Tài Tinh, mối quan hệ Nhật Chủ với Tài, Thực Thương sinh Tài hay không. Đánh giá luồng tiền, khả năng tụ tài hay hao tài để trả lời đúng trọng tâm.";
        } elseif ($category === 'CONGVIEC') {
            $topic_focus = "TẬP TRUNG VÀO: Quan Sát, Thực Thương, Ấn Tinh liên quan đến sự nghiệp. Đánh giá công danh, thăng tiến, môi trường cạnh tranh để trả lời đúng trọng tâm.";
        } elseif ($category === 'SUKKHOE') {
            $topic_focus = "TẬP TRUNG VÀO: Ngũ hành thiếu hụt, mất cân bằng mạnh trong Tứ Trụ. Lưu ý: Chỉ cảnh báo xu hướng sức khỏe khách quan, KHÔNG ĐƯỢC PHÁN QUYẾT TÍNH MẠNG SỐNG CHẾT.";
        } elseif ($category === 'GIADAO') {
            $topic_focus = "TẬP TRUNG VÀO: Mối quan hệ lục thân tương ứng với câu hỏi (Trụ Năm: ông bà/cha mẹ, Trụ Tháng: cha mẹ/anh em, Trụ Giờ: con cái).";
        } elseif ($category === 'VANHAN') {
            $topic_focus = "TẬP TRUNG VÀO: Tương tác của Đại Vận hiện tại ({$dv_data['dai_van_hien_tai']}) và Ngũ hành năm nay với Tứ Trụ gốc (xung, hợp, hóa, sinh, khắc). Chỉ rõ hung cát.";
        } elseif ($category === 'XUATHANH') {
            $topic_focus = "TẬP TRUNG VÀO: Dịch Mã, Thiên Di cung (trụ giờ), các hành động/hành Kim/Thủy liên quan đến di chuyển. Đánh giá lá số có hợp xuất ngoại, thay đổi môi trường sống hay không.";
        } elseif ($category === 'QUANHE') {
            $topic_focus = "TẬP TRUNG VÀO: Tỷ Kiên, Kiếp Tài (quan hệ ngang hàng, bạn bè, đối tác), Thực Thương (khả năng giao tiếp, tạo kết nối). Đánh giá mức độ tin người, hợp tác được hay dễ bị lợi dụng.";
        } elseif ($category === 'PHAPLY') {
            $topic_focus = "TẬP TRUNG VÀO: Quan Sát (áp lực pháp lý, quyền lực), Kiếp Tài (tranh chấp, mâu thuẫn). Đánh giá lá số có dễ vướng kiện tụng, tranh chấp hay không và giai đoạn nào rủi ro nhất.";
        } elseif ($category === 'TAMLINH') {
            $topic_focus = "TẬP TRUNG VÀO: Các điểm hình xung đặc biệt, hành Thủy vượng, Kiếp Tài nhiều. Chỉ luận theo cơ chế ngũ hành, KHÔNG đưa ra lời khuyên tâm linh, cúng bái, phong thủy.";
        }

        return <<<PROMPT
Dựa trên hệ thống Tử Bình Bát Tự cổ học kết hợp phương pháp luận giải hiện đại của Thiệu Vĩ Hoa.
Hãy đọc lá số Bát Tự và trả trả lời câu hỏi sau đây:

CÂU HỎI: "{$user_question}"
CHỦ ĐỀ: {$category_vi}
{$topic_focus}

Họ và tên: {$tt['ho_ten']} (Tuổi: {$tuoi})
Giới tính: {$gioi_tinh}
Ngày sinh Dương lịch: {$tt['ngay_sinh']}, Giờ sinh: {$tt['gio_sinh']}
Nhật Chủ: {$tt['nhat_chu']} (hành {$tt['nhat_chu_hanh']})
Nhật Trụ: {$tt['nhat_tru']}
Vượng suy Nhật Chủ: {$tt['vuong_suy']} (mùa {$tt['mua']})
Thân: {$than_str}
Dụng Thần: {$dung_str}
Kỵ Thần: {$ky_str}
Nạp Âm năm sinh: {$tt['nap_am_nam']}

TỨ TRỤ CHI TIẾT:
{$tu_tru_str}
THÔNG TIN ĐẠI VẬN:
- Khởi vận: {$dv['tuoi_khoi_van']} tuổi
- Chiều hành vận: {$dv['chieu_hanh_van']}
- Đại Vận hiện tại: {$dv_data['dai_van_hien_tai']}
- Toàn bộ Đại Vận:
{$dv_data['van_str']}

YÊU CẦU DIỄN ĐẠT:
- XƯNG HÔ: là Bạn (có thể dùng Tên người dùng, sử dụng 1 cách tự nhiên) đảm bảo xuyên suốt. Cấm dùng "Anh/Chị/Em", người này, họ.
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu.
- Trình bày mạch lạc, không dài dòng.
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề.
- Tránh diễn đạt lặp ý, ví dụ blockquote (nếu có) không được viết lại y nguyên diễn giải trước đó, phải diễn giải trực quan hơn hoặc phương pháp khác để không bị lặp template.
- KHÔNG viết lại các thông tin đã cung cấp -> Hãy luận giải trực tiếp.
- Nếu họ tên là Đương Số, hoặc tên không có dấu Tiếng Việt hoặc biệt danh -> Nghĩa là người dùng không muốn công khai -> xưng hô là Bạn.
- Nếu tuổi nhỏ hơn 10, tức cha mẹ / người thân đang xem hộ -> xưng hô là Bé, ví dụ: Bé có Nhật Chủ...

{$rules}

[HƯỚNG DẪN NỘI DUNG]
## Giáp đáp câu hỏi của {$tt['ho_ten']} từ lá số Bát tự
- Trả lời TRỰC TIẾP và ĐÚNG TRỌNG TÂM vào câu hỏi: "{$user_question}" đúng chủ đề {$category_vi}. Không phân tích lan man sang các chủ đề khác.
- Ưu tiên đưa dẫn chứng lên trước, kết luận ra sau. Không mở đầu bằng nhận định rồi mới giải thích —> hãy dẫn dữ liệu lá số trước, để dữ liệu tự dẫn đến kết luận. 
- Mọi nhận định phải có dẫn chứng cụ thể từ lá số. CẤM nói chung chung kiểu "giai đoạn đầu", "về sau", "khi còn trẻ" mà không kèm mốc thời gian rõ ràng. 
Khi đề cập đến giai đoạn thời gian PHẢI chỉ rõ: tên Đại Vận, khoảng tuổi, năm bắt đầu. Ví dụ: "giai đoạn 10-29 tuổi (Đại Vận Tân Mùi và Canh Ngọ)" thay vì "giai đoạn đầu đời".

- Lập luận theo 3 lớp:
  + Hiện trạng trong lá số (Thập Thần nào, ở trụ nào, tàng hay lộ)
  + Cơ chế sinh khắc dẫn đến điều gì (hành nào tác động, theo chiều nào)
  + Hệ quả từ những cung/vận đó trong cuộc sống (biểu hiện cụ thể, không trừu tượng)

- Nếu câu hỏi liên quan đến diễn biến theo thời gian (tài lộc, tình cảm, sự nghiệp, sức khỏe, vận hạn) ->kết thúc bằng một đoạn blockquote (dùng ký hiệu >) phân tích Đại Vận có liên quan trực tiếp: so sánh vận trước/sau nếu có sự thay đổi rõ, chỉ rõ năm/tuổi cụ thể. 
- Nếu câu hỏi về tính cách, cấu trúc lá số hoặc đặc điểm cố định trong bản mệnh thì KHÔNG cần blockquote đại vận.
- CẤM kết luận bằng câu trấn an chung chung kiểu "không phải lá số vô duyên", "vẫn có thể thành công", "không đến nỗi tệ" nếu không có dữ liệu lá số dẫn đến kết luận đó. Kết luận phải là nhận định kỹ thuật, không phải lời an ủi.
- Nếu lá số và vấn đề của câu hỏi trái ngược nhau, hãy xem "XỬ LÝ MÂU THUẪN GIỮA CÂU HỎI VÀ LÁ SỐ".

- XỬ LÝ MÂU THUẪN GIỮA CÂU HỎI VÀ LÁ SỐ:
Nếu người dùng mô tả trái ngược với bề nổi của lá số (VD: Tài Tinh mạnh nhưng đang phá sản, hoặc Quan Sát yếu nhưng đang thăng tiến tốt),
TUYỆT ĐỐI KHÔNG phủ nhận xủa người dùng. Hãy thực hiện tuần tự:
    1. Phá cục: Kiểm tra xem các Thần Sát xấu, xung phá, hình hại có đang tác động vào trụ liên quan không.
    2. Lực cản: Xem Nhật Chủ quá nhược hoặc quá vượng có gây mất cân bằng không.
    3. Đại Vận: Nếu cấu cục gốc tốt nhưng hiện tại tồi, giải thích dựa trên Đại Vận đang tác động.
    4. Chỉ khi đã rà soát 3 bước trên mà vẫn không thấy điểm hợp lý:
    Mới khách quan trả lời rằng "Sự kiện này hiện không thể hiện rõ trên cấu trúc chính của lá số", sau đó tập trung vào phân tích điểm mạnh/yếu gốc.
    KHÔNG ngụy tạo lý do, không định hướng.

Một số ví dụ SAI/ĐÚNG về diễn đạt và chuyên môn:
VD1:
+ SAI (không đưa thời gian cụ thể): Bạn có xu hướng tình duyên lận đận ở giai đoạn đầu... 
+ ĐÚNG (Chỉ rõ giai đoạn tuổi): Bạn có tình duyên trắc trở trong giai đoạn 10-29 tuổi... 

VD2:
+ SAI (kết luận trước mới đưa dẫn chứng): Bạn có tình duyên trắc trở vì Tài Tinh tàng ẩn ... 
+ ĐÚNG (Dẫn chứng từ lá số rồi dữ liệu tự dẫn đến kết luận): Lá số có Nhâm Thủy Thiên Tài lộ ở trụ tháng và Quý Thủy Chính Tài tàng trong chi ngày Mậu Thìn - Tài Tinh ...

[AST_RESULT]
(Nội dung luận giải được đặt ở đây)
[/AST_RESULT]
PROMPT;
    }
}