<?php

if (!defined('ABSPATH')) exit;

class BbZodiac_Prompt {

    public static function build(string $dob, array $sign): string {
        $signName   = $sign['name']       ?? '';
        $symbol     = $sign['symbol']     ?? '';
        $element    = $sign['element']    ?? '';
        $planet     = $sign['planet']     ?? '';
        $quality    = $sign['quality']    ?? '';
        $polarity   = $sign['polarity']   ?? '';
        $keywords   = $sign['keywords']   ?? '';
        $decan      = $sign['decan']      ?? '';
        $subRuler   = $sign['sub_ruler']  ?? '';
        $decanVibe  = $sign['decan_vibe'] ?? '';
        $hasCusp    = !empty($sign['has_cusp']);
        $cuspName   = $sign['cusp_name']  ?? '';
        $cuspBlend  = $sign['cusp_blend'] ?? '';
        $cuspVibe   = $sign['cusp_vibe']  ?? '';

        $compat = $sign['compatibility'] ?? [];
        $bestMatch   = !empty($compat['best_match'])   ? implode(', ', $compat['best_match'])   : '';
        $karmicMatch = !empty($compat['karmic_match']) ? implode(', ', $compat['karmic_match']) : '';
        $worstMatch  = !empty($compat['worst_match'])  ? implode(', ', $compat['worst_match'])  : '';

        $cuspSection = $hasCusp ? "- Giao đỉnh (Cusp): {$cuspName} — {$cuspBlend} — {$cuspVibe}" : '';

        // TÍNH TOÁN TUỔI ĐỂ CHUYỂN HÓA THÀNH "TRẠNG THÁI NĂNG LƯỢNG NGẦM"
        $age = 0;
        $dobObj = DateTime::createFromFormat('d/m/Y', $dob);
        if ($dobObj) {
            $age = $dobObj->diff(new DateTime())->y;
        }

        $energyState = '';
        if ($age < 18) {
            $energyState = "Năng lượng nguyên thủy, bộc phát bản năng. Cái tôi đang trong quá trình định hình, dễ bị tác động, tính cách chòm sao bộc lộ một cách thuần túy và dữ dội nhất.";
        } elseif ($age <= 25) {
            $energyState = "Năng lượng đang va chạm với thực tế xã hội. Tính cách đặc trưng bộc lộ mạnh mẽ, ưa khám phá và thử sai nhưng đôi khi thiếu kiểm soát.";
        } elseif ($age <= 35) {
            $energyState = "Năng lượng đã trưởng thành và thực tế hóa. Biết tiết chế khuyết điểm, tập trung vào việc xây dựng nền tảng sự nghiệp và hướng tới sự ổn định.";
        } elseif ($age <= 50) {
            $energyState = "Năng lượng đạt độ chín, mở rộng và tối ưu. Đã thấu hiểu rõ bản ngã, biết cách sử dụng điểm mạnh của chòm sao một cách sắc bén, sâu sắc và điềm tĩnh.";
        } else {
            $energyState = "Năng lượng lắng đọng và chắt lọc. Vượt lên trên những bốc đồng thường thấy của chòm sao này, hướng tới giá trị cốt lõi, sự minh triết và bình yên nội tâm.";
        }

        return <<<TXT
Phân tích tính cách chuyên sâu dựa trên Chiêm tinh học.

DỮ LIỆU ĐẦU VÀO:
- Ngày sinh: {$dob}
- Cung hoàng đạo: {$signName} {$symbol}
- Nguyên tố: {$element}
- Hành tinh chủ quản: {$planet}
- Tính chất: {$quality}
- Phân cực: {$polarity}
- Từ khóa: {$keywords}
- Decan: {$decan} (Hành tinh phụ: {$subRuler}, Sắc thái: {$decanVibe})
- Tương hợp: Hợp nhất [{$bestMatch}], Duyên nghiệp [{$karmicMatch}], Khắc nhất [{$worstMatch}]
{$cuspSection}
- Trạng thái năng lượng hiện tại (Hệ quy chiếu ngầm): {$energyState}

YÊU CẦU QUY TẮC:
- KHÔNG tự tính toàn lại, chỉ phân tích dự trên dữ liệu đã cung cấp.
- Xưng hô "Bạn" đảm bảo nhất quán, KHÔNG dùng "Anh/Chị/Em".
- CHỈ sử dụng chiêm tinh học trong phân tích. Đảm bảo viết đúng bản chất của 12 cung hoàng đạo.
- Nếu có Cusp, phải đề cập đến ảnh hưởng của giao đỉnh.
- BẮT BUỘC NGẦM ĐỊNH HIỂU: Soi chiếu tính cách cung hoàng đạo qua "Trạng thái năng lượng hiện tại". 

YÊU CẦU DIỄN ĐẠT:
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu.
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề, không sử dụng ngôn ngữ dạy đời, không giảng đạo lý.
- Trình bày mạch lạc, không dài dòng.

YÊU CẦU OUTPUT:
- KHÔNG dùng gạch đầu dòng phân mục như a), b), c) hay 1, 2, 3 làm tiêu đề.
- Sử dụng in đậm, in nghiêng hợp lý để nhấn mạnh nội dung nhưng không lạm dụng
- Sử dụng Markdown, định dạng văn bản dễ đọc (không dùng ---, ***, ___, hr để tạo phân cách ).
- PHẢI TRẢ ĐÚNG format [ZDC_HTML][/ZDC_HTML].

LỆNH CẤM (TUYỆT ĐỐI TUÂN THỦ):
- TUYỆT ĐỐI KHÔNG nhắc đến số tuổi cụ thể, năm sinh, hay ám chỉ về "tuổi tác", "giai đoạn cuộc đời" trong bài phân tích. Chỉ dùng [Trạng thái năng lượng hiện tại] để tự điều chỉnh giọng văn và góc nhìn của người luận giải.
- KHÔNG sử dụng các đoạn nháp, suy nghĩ nội bộ (thinking).
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- CẤM hiển thị bất kỳ nội dung meta nào, bao gồm nhưng không giới hạn: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- Những phần này chỉ phục vụ xử lý nội bộ và KHÔNG được xuất ra kết quả cuối cùng.
- Output chỉ bao gồm nội dung phân tích hoàn chỉnh theo format yêu cầu.

[HƯỚNG DẪN NỘI DUNG]
### Giải mã tính cách
(Gợi ý: Dựa vào thông tin [DỮ LIỆU ĐẦU VÀO], viết 3-4 đoạn văn phân tích tính cách:
- Đoạn 1: Tổng quan bản ngã (Cung + Nguyên tố + Hành tinh) bộc lộ như thế nào dưới lăng kính của Trạng thái năng lượng hiện tại.
- Đoạn 2: Điểm mạnh, tiềm năng (kết hợp Decan) và cách chòm sao này phát huy năng lực một cách thực tế nhất lúc này.
- Đoạn 3: Điểm yếu đặc trưng và những góc khuất tâm lý thầm kín cần được nhận diện.
- Đoạn 4: Định hướng và lời khuyên (sự nghiệp/tình cảm) sắc sảo, phù hợp với mức độ trưởng thành của năng lượng đang có.)

[ZDC_HTML]
(Nội dung phân tích sẽ được đặt ở đây)
[/ZDC_HTML]
TXT;
    }

    public static function buildLove(array $data): string {
        // Thông tin người 1
        $n1 = $data['name1'] ?? '';
        $s1 = $data['sign1']['name'] ?? '';
        $e1 = $data['sign1']['element'] ?? '';

        // Thông tin người 2
        $n2 = $data['name2'] ?? '';
        $s2 = $data['sign2']['name'] ?? '';
        $e2 = $data['sign2']['element'] ?? '';

        // Dữ liệu 5 lớp phân tích (từ calc.php)
        $score = $data['analysis']['score'] ?? 0;
        $aspectLabel = $data['analysis']['aspect_label'] ?? '';
        $mod1 = $data['analysis']['mod_1'] ?? '';
        $mod2 = $data['analysis']['mod_2'] ?? '';
        $pol1 = $data['analysis']['pol_1'] ?? '';
        $pol2 = $data['analysis']['pol_2'] ?? '';
        $planetMatch = !empty($data['analysis']['planet_match']) ? 'Có liên kết sâu trong tiềm thức' : 'Độc lập về mặt tâm hồn';

        return <<<TXT
Bạn là Nhà Chiêm Tinh Học (Astrologer).
Nhiệm vụ: Luận giải bản đồ sao đôi (Synastry) của chúng tôi dựa trên 5 lớp phân tích nền tảng của chiêm tinh học.

[THÔNG TIN]:
- Nhân vật 1: {$n1} | Cung: {$s1} | Nguyên tố: {$e1} | Đặc tính: {$mod1} | Phân cực: {$pol1}
- Nhân vật 2: {$n2} | Cung: {$s2} | Nguyên tố: {$e2} | Đặc tính: {$mod2} | Phân cực: {$pol2}
- Lớp 1 (Góc chiếu/Khoảng cách): {$aspectLabel}
- Lớp 5 (Liên kết Hành tinh/Decan): {$planetMatch}
- Chỉ số Tương hợp Tổng thể: {$score}%

NGUYÊN TẮC LUẬN GIẢI (BẮT BUỘC):
- BẢN CHẤT LÀ GỐC: Chỉ dùng dữ liệu của 5 lớp trên để soi chiếu. Phân tích đi thẳng vào bản chất của 12 cung hoàng đạo.
- KHÔNG SUY DIỄN: Không được tạo ra chi tiết cụ thể về hành động, lời nói, hay kịch bản đời thực.
- Tổng hợp insight từ TẤT CẢ dữ liệu trên để phân tích
- Nếu có Cusp, phải đề cập đến ảnh hưởng của giao đỉnh

YÊU CẦU DIỄN ĐẠT:
- XƯNG HÔ: Gọi tên "{$n1}" và "{$n2}" hoặc dùng từ "hai bạn". CẤM dùng: anh, chị hay anh/chị, họ.
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề, không sử dụng ngôn ngữ dạy đời, không giảng đạo lý
- Trình bày mạch lạc, không dài dòng

YÊU CẦU OUTPUT:
- KHÔNG dùng gạch đầu dòng phân mục như a), b), c) hay 1, 2, 3 làm tiêu đề
- Sử dụng in đậm in nghiêng hợp lý, không lạm dụng (không dùng ---, ***, ___)
- Sử dụng Markdown, định dạng văn bản dễ đọc.
- ĐỊNH DẠNG: Dùng Markdown chuẩn (không dùng ---, ***, ___).
- PHẢI TRẢ ĐÚNG format [ZDC_HTML][/ZDC_HTML]. 

LỆNH CẤM:
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- CẤM đưa các đoạn nháp, suy nghĩ nội bộ, thinking-> chỉ trả nội dung hoàn chỉnh.
- CẤM hiển thị bất kỳ nội dung meta nào, bao gồm nhưng không giới hạn: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- Những phần này chỉ phục vụ xử lý nội bộ và KHÔNG được xuất ra kết quả cuối cùng.
- Output chỉ bao gồm nội dung phân tích hoàn chỉnh theo format yêu cầu.

[HƯỚNG DẪN NỘI DUNG]
### Giải mã tình yêu
(Gợi ý - Góc chiếu & Nguyên tố: 
Phân tích sự va chạm giữa nguyên tố {$e1} và {$e2} kết hợp với góc chiếu "{$aspectLabel}". Năng lượng nào đóng vai trò dẫn dắt, năng lượng nào thích nghi? Lực hút ban đầu giữa hai người là sự mãnh liệt kịch tính hay bình yên tự nhiên?)

(Gợi ý - Phong cách Hành động (Đặc tính & Phân cực): 
Phân tích sự kết hợp giữa Đặc tính {$mod1}-{$mod2} và Phân cực {$pol1}-{$pol2}. Khi Cái Tôi (Ego) đối đầu hoặc khi gặp vấn đề chung, cách họ phản ứng sẽ thế nào? Xu hướng mâu thuẫn là bộc phát hay tích tụ?)

(Gợi ý - Liên kết Tâm hồn (Hành tinh & Decan): 
Dựa vào trạng thái lớp 5 là "{$planetMatch}", giải mã mức độ thấu cảm, sự đồng điệu hoặc tính độc lập trong tiềm thức và tâm hồn sâu thẳm của {$n1} và {$n2}.)

(Gợi ý - Lời khuyên (Dựa trên điểm số {$score}%): 
Đưa ra nhận định chốt lại về mức độ hòa hợp {$score}% và 1-2 lời khuyên cốt lõi nhất để duy trì hoặc cân bằng mối quan hệ này dựa trên các điểm khác biệt đã phân tích.)

[ZDC_HTML]
(Nội dung phân tích sẽ được đặt ở đây)
[/ZDC_HTML]
TXT;
    }

    public static function buildNatal(string $dob, string $tob, string $pob, array $natalChart): string {
        $pob = mb_convert_case($pob, MB_CASE_TITLE, "UTF-8");
        $planets = $natalChart['planets'];
        $ascendant = $natalChart['ascendant'];
        $midheaven = $natalChart['midheaven'];
        $elementDist = $natalChart['element_distribution'];
        $tobText = $natalChart['is_exact_time'] ? $tob : '12:00';

        $planetList = '';
        foreach ($planets as $planet) {
            $extra = [];
            if (!empty($planet['quality']))  $extra[] = "Tính chất: {$planet['quality']}";
            if (!empty($planet['keywords'])) $extra[] = "Từ khóa: {$planet['keywords']}";
            $extraStr = !empty($extra) ? ' | ' . implode(' | ', $extra) : '';
            $planetList .= "- {$planet['name']}: {$planet['sign']} {$planet['degree_formatted']} ({$planet['element']}, {$planet['modality']}){$extraStr}\n";
        }

        $elementStats = '';
        $total = array_sum($elementDist);
        foreach ($elementDist as $element => $count) {
            $percent = $total > 0 ? round(($count / $total) * 100) : 0;
            $elementStats .= "- {$element}: {$count} hành tinh ({$percent}%)\n";
        }

        $houseList = '';
        if (!empty($natalChart['houses'])) {
            foreach ($natalChart['houses'] as $num => $house) {
                $planetsInHouse = !empty($house['planets']) ? implode(', ', $house['planets']) : 'Trống';
                $houseList .= "- Nhà {$num} ({$house['sign']}): {$planetsInHouse}\n";
            }
        }

        $aspectStr = '';
        if (!empty($natalChart['aspects'])) {
            foreach ($natalChart['aspects'] as $asp) {
                $aspectStr .= "- {$asp['planet1']} {$asp['aspect']} {$asp['planet2']} ({$asp['nature']})\n";
            }
        }

        $patternStr = '';
        if (!empty($natalChart['patterns'])) {
            foreach ($natalChart['patterns'] as $pat) {
                $pList = implode(', ', $pat['planets']);
                $patternStr .= "- Cấu trúc {$pat['type']}: {$pList}\n";
            }
        }

        return <<<TXT
Bạn là một Astrologer (Nhà Chiêm Tinh) chuyên nghiệp.
Nhiệm vụ: Luận giải Bản Đồ Sao Cá Nhân (Natal Chart).

[DỮ LIỆU ĐẦU VÀO]:
- Tọa độ sinh: {$pob}
- Thời gian: {$tobText} |  {$dob}
- Nguyên tố chủ đạo: {$natalChart['dominant_element']} | Đặc tính: {$natalChart['dominant_modality']}
{$elementStats}
[HÀNH TINH & CUNG MỌC]:
{$planetList}- Cung Mọc (Ascendant): {$ascendant['sign']}
- Thiên Đỉnh (MC): {$midheaven['sign']}
[CUNG NHÀ]:
{$houseList}[GÓC CHIẾU & CẤU TRÚC]:
{$aspectStr}{$patternStr}

QUY TẮC BẮT BUỘC:
- Trọng tâm, trực tiếp, đi thẳng vào bản chất vấn đề.
- KHÔNG viết văn dài dòng, KHÔNG phân tích lan man mang tính lý thuyết học thuật hay bách khoa toàn thư. Ngắn gọn, đọc là hiểu ngay ứng dụng thực tế.
- Luận giải bám sát thực tế cuộc sống, tâm lý và tính cách con người.
- KHÔNG tự đoán lại vị trí hành tinh, chỉ dùng dữ liệu đã cho.

YÊU CẦU DIỄN ĐẠT:
- XƯNG HÔ: "Bạn". KHÔNG dùng "Anh/Chị/Em".
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu.
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề, không sử dụng ngôn ngữ dạy đời, không giảng đạo lý.
- Trình bày mạch lạc, không dài dòng.

YÊU CẦU OUTPUT:
- KHÔNG dùng gạch đầu dòng phân mục như a), b), c) hay 1, 2, 3 làm tiêu đề.
- Sử dụng in đậm in nghiêng hợp lý, không lạm dụng (không dùng ---, ***, ___).
- Sử dụng các tiêu đề phụ ngắn gọn, tiêu đề phụ chỉ để để phân tách. đừng cố NHỒI NHÉT ý nghĩa nội dung vào tiêu đề. 
- TIêu đề phụ chỉ 1 vế ví dụ: Thế Giới Cảm Xúc và Giá Trị Nội Tại -> Cảm Xúc 
- ĐỊNH DẠNG: Dùng Markdown chuẩn (không dùng ---, ***, ___), định dạng văn bản dễ đọc.
- PHẢI TRẢ ĐÚNG format [ZDC_HTML][/ZDC_HTML].

LỆNH CẤM:
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- CẤM đưa các đoạn nháp, suy nghĩ nội bộ, thinking-> chỉ trả nội dung hoàn chỉnh.
- CẤM hiển thị bất kỳ nội dung meta nào, bao gồm nhưng không giới hạn: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- TUYỆT ĐỐI CẤM sử dụng icon, emoji trong bài viết.
- Những phần này chỉ phục vụ xử lý nội bộ và KHÔNG được xuất ra kết quả cuối cùng.
- Output chỉ bao gồm nội dung luận giải hoàn chỉnh theo format yêu cầu.

[HƯỚNG DẪN NỘI DUNG]
(Gợi ý: Viết thành các đoạn văn ngắn gọn, súc tích tập trung vào:
- Bản ngã, tiềm thức & vỏ bọc bên ngoài.
- Cách tư duy, hành động & tình cảm trong các mối quan hệ.
- Những thách thức bẩm sinh hoặc tiềm năng nổi bật nhất.
- Lời khuyên định hướng phát triển bản thân thiết thực.)

[ZDC_HTML]
(Nội dung phân tích sẽ được đặt ở đây)
[/ZDC_HTML]
TXT;
    }
}

