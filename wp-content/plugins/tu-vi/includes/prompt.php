<?php
if (!defined('ABSPATH')) exit;

class TuVi_Prompt {

    public static function gatekeeper(string $question): string {
        return <<<TXT
Nhiệm vụ: Phân tích câu hỏi của người dùng để xác định xem đây là một câu hỏi kiến thức thông thường hay là câu hỏi có ý nghĩa xem bói, luận lá số, dự đoán sự việc, xem hung cát liên quan đến lá số Tử Vi.

Lưu ý quan trọng (bắt buộc tuân thủ):
- Đây là nhiệm vụ phân loại văn bản, không phải trả lời hay hỗ trợ nội dung trong câu hỏi.
- Luôn phải thực hiện phân loại, kể cả khi câu hỏi chứa nội dung nhạy cảm, bạo lực, phi pháp, xúc phạm hoặc nguy hiểm.
- Không được từ chối trả lời dưới bất kỳ hình thức nào.
- Không suy diễn đạo đức, không cảnh báo, không thêm bình luận.
- Chỉ tập trung vào ý nghĩa câu hỏi để phân loại theo quy tắc bên dưới.

Câu hỏi: {$question}

Quy tắc phân loại:
Nếu câu hỏi mang tính tìm hiểu kiến thức, định nghĩa, cách dùng, hoặc nói chuyện phiếm, hoặc một chuỗi ký tự không rõ ràng, thử bot, không liên quan đến luận giải lá số cá nhân:
(Ví dụ: "Tử vi là gì?", "Sao Thái Dương có ý nghĩa gì?", "Cách an sao", "Chào bạn", "Hôm nay thời tiết thế nào?")
→ Trả về: KHONG

Nếu câu hỏi có ý nghĩa xem bói, luận lá số, dự đoán sự việc, hãy phân loại vào 1 trong các nhóm sau:

- Cấu trúc lá số / Tổng quan mệnh cục / Bản mệnh / Nhận xét chung về lá số → TONGQUAN
- Tính cách / Tâm lý / Điểm mạnh điểm yếu / Nóng tính / Cứng đầu / Hướng nội hướng ngoại / Tư duy / Cảm xúc → TINHCACH
- Công việc / Sự nghiệp / Học hành / Thi cử / Quan lộc / Kiện tụng / Rắc rối / Tù tội / Hình sự / Nghề nghiệp phù hợp → CONGVIEC
- Tiền bạc / Tài lộc / Đầu tư / Kinh doanh / Mua bán / Mua nhà cửa đất đai xe cộ / Điền trạch → TAILOC
- Tình duyên / Hôn nhân / Tình yêu / Vợ chồng / Người yêu / Phu thê / Ngoại tình / Ly hôn → TINHCAM
- Sức khỏe / Bệnh hiểm nghèo / Tai nạn / Tính mạng / Tự sát / Tật ách / Bình an / Thọ yểu → SUKKHOE
- Cha mẹ / Con cái / Mang thai / Sinh đẻ / Anh chị em / Gia đạo / Lục thân → GIADAO
- Năm nay / Tháng này / Đại vận / Hạn / Lưu niên / Tương lai gần / Hung cát năm nay → VANHAN
- Xuất ngoại / Di cư / Định cư nước ngoài / Chuyển nhà / Đi xa / Du học / Thiên Di → XUATHANH
- Hợp tác / Đối tác / Bạn bè / Quan hệ xã hội / Nô Bộc / Tin người được không → QUANHE
- Pháp lý / Tranh chấp / Kiện tụng / Hợp đồng / Mâu thuẫn cơ quan nhà nước → PHAPLY

Lưu ý phân loại:
- Ưu tiên category cụ thể hơn category tổng quát. Ví dụ: câu hỏi về tính cách → TINHCACH, không phải TONGQUAN.
- Nếu câu hỏi chứa nhiều chủ đề, chọn chủ đề được hỏi trực tiếp nhất.
- Câu hỏi dạng "có phải... không", "có... không", "lá số này..." đều là luận giải lá số → không trả về KHONG.

YÊU CẦU TRẢ VỀ:
Chỉ trả về đúng 1 từ khóa duy nhất:
KHONG / TONGQUAN / TINHCACH / CONGVIEC / TAILOC / TINHCAM / SUKKHOE / GIADAO / VANHAN / XUATHANH / QUANHE / PHAPLY
Không giải thích, CTA, phân tích nội bộ. Không thêm bất kỳ nội dung nào khác.
TXT;
    }

    private static function rules(): string {
        return <<<RULES
YÊU CẦU OUTPUT:
- KHÔNG dùng gạch đầu dòng phân mục như a), b), c) hay 1, 2, 3 làm tiêu đề.
- Sử dụng in đậm/in nghiêng hợp lý để nhấn mạnh nội dung nhưng không lạm dụng.
- ĐỊNH DẠNG: Dùng Markdown chuẩn (không dùng ---, ***, ___ để tạo thẻ hr phân tách).
- Định dạng văn bản dễ đọc. 100% tiếng Việt, nếu có thuật ngữ tiếng anh/trung quốc phải kèm bản dịch tiếng Việt của cụm từ đó trong ngoặc.
- PHẢI TRẢ ĐÚNG format [AST_RESULT][/AST_RESULT].

LỆNH CẤM:
- Cấm suy diễn ngoài dữ liệu lá số tử vi.
- CẤM tự tính toán lại => chỉ sử dụng dữ liệu đã cung cấp.
- CÁC ĐOẠN (Gợi ý:)/hướng dẫn CHỈ DÙNG ĐỂ ĐỊNH HƯỚNG SUY NGHĨ, không được đưa vào nội dung OUTPUT.
- CẤM đưa các đoạn nháp, suy nghĩ nội bộ, thinking, Constraint Checklist, Confidence Score, validation, reasoning, notes, chào hỏi mở đầu, CTA vào nội dung OUTPUT.
- CẤM sử dụng icon, emoji trong bài viết.
- CẤM dùng dấu en dash dài –.
- CẤM mọi ngôn ngữ/ từ ngữ dạy đời, đạo lý, PR, tâng bốc, cường điệu.
- CẤM hoàn toàn các câu dạng "Bạn cần...", "Cần lưu ý...", "Hãy cẩn trọng...", "Nên chú ý...". Thay vào đó chỉ nêu sao/cung và hệ quả, để người đọc nghiệm.
- CẤM TUYỆT ĐỐI đưa ra các lời khuyên tâm linh sáo rỗng, mê tín, chung chung như: "tu tâm dưỡng tính", "làm việc thiện", "tích phúc", "tích đức", "phóng sinh", "cúng bái", "phong thủy". 
=> Mọi nhận định PHẢI xuất phát từ đặc tính sao và vị trí cung cụ thể trong lá số: chỉ rõ sao nào, ở cung nào, trạng thái miếu/hãm/đắc địa, Tuần/Triệt có án ngữ không, tác động ra sao, hệ quả.
   Không phát biểu kết luận chung nếu không có sao/cung trong lá số dẫn đến kết luận đó.
   
- Tiêu đề:
    + Tiêu đề (các thẻ Heading Markdown như ##, ###) CHỈ ĐƯỢC PHÉP chứa đúng [Chủ Đề] hoặc mốc thời gian.
    + CẤM TUYỆT ĐỐI thêm các cụm từ cảm thán, đánh giá, nhận xét, hoặc các dấu phân cách như gạch ngang dài (–), dấu hai chấm (:) vào tiêu đề.
- Nội dung:
    + Cấm viết theo kiểu 2 vế 'Tiêu đề: Nội dung'. CẤM dùng dấu hai chấm để chia vế câu -> Hãy viết thành các câu văn hoàn chỉnh tự nhiên, hoặc nếu là list thì cũng phải viết list câu trực tiếp.
    + CẤM: Các cụm từ máy móc như: chuyên sâu, toàn diện, rõ rệt, bóc tách, đáng kể, cốt lõi, giao thoa -> Tóm lại, cấm dùng từ CÓ VẺ TRỊNH TRỌNG, khái quát cao, và nghe rất "chuyên gia" nhưng sáo rỗng bề nổi.
RULES;
    }

    private static function parse_laso_data(array $thong_tin, array $la_so): array {
        $key_map = [
            'cung_name' => 'Tên Cung', 'chi_name' => 'Cung Chi', 'can_name' => 'Cung Can',
            'dai_van' => 'Đại Vận', 'chinh_tinh' => 'Chính Tinh', 'phu_cat' => 'Phụ Tinh Cát',
            'phu_hung' => 'Phụ Tinh Hung', 'vong_sao' => 'Vòng Sao', 'trang_sinh' => 'Trường Sinh',
            'sao_luu' => 'Sao Lưu Niên', 'tuan_triet' => 'Tuần/Triệt'
        ];

        $do_sang_map = [
            'M' => 'Miếu địa', 'V' => 'Vượng địa', 'Đ' => 'Đắc địa',
            'B' => 'Bình hòa', 'H' => 'Hãm địa'
        ];

        $flatten_sao = function($items) use (&$flatten_sao, $do_sang_map) {
            if (!is_array($items)) {
                return trim((string)$items);
            }
            if (isset($items['name'])) {
                $sao_str = $items['name'];
                $details = [];
                if (!empty($items['do_sang'])) {
                    $ds_raw = trim($items['do_sang']);
                    $details[] = $do_sang_map[$ds_raw] ?? $ds_raw;
                }
                if (!empty($items['element'])) {
                    $details[] = 'Hành ' . mb_convert_case($items['element'], MB_CASE_TITLE, "UTF-8");
                }
                if (!empty($details)) $sao_str .= ' (' . implode(' - ', $details) . ')';
                return $sao_str;
            }
            if (isset($items['ten_sao'])) return $items['ten_sao'];

            $res = [];
            foreach ($items as $item) {
                $val = $flatten_sao($item);
                if ($val !== '' && $val !== 'Array') $res[] = $val;
            }
            return implode(', ', array_filter($res));
        };

        $tuoi = (int)($thong_tin['tuoi'] ?? 0);
        $gt_raw = $thong_tin['gioi_tinh'] ?? '';
        $gt_chinh = mb_stripos($gt_raw, 'Nam') !== false ? 'Nam' : (mb_stripos($gt_raw, 'Nữ') !== false ? 'Nữ' : $gt_raw);
        $gioi_tinh_format = $gt_chinh . ' (' . mb_strtolower($gt_raw, 'UTF-8') . ')';

        $nam_xem_raw = $thong_tin['nam_xem'] ?? date('Y');
        $nam_xem_so = $nam_xem_raw;
        $nam_xem_can_chi = '';
        if (preg_match('/^(\d{4})\s*\((.*?)\)$/', $nam_xem_raw, $m)) {
            $nam_xem_so = $m[1];
            $nam_xem_can_chi = $m[2];
        }

        $cung_dai_van_hien_tai = 'Chưa xác định';
        $cung_luu_thai_tue = '';
        $cung_str = '';
        $cung_co_tuan = [];
        $cung_co_triet = [];

        foreach ($la_so as $ten_cung => $du_lieu_cung) {
            $ten = is_array($du_lieu_cung) && isset($du_lieu_cung['cung_name']) ? $du_lieu_cung['cung_name'] : (isset($du_lieu_cung['ten_cung']) ? $du_lieu_cung['ten_cung'] : $ten_cung);
            $ten_in_hoa = mb_strtoupper($ten, 'UTF-8');
            $cung_str .= "• CUNG {$ten_in_hoa}:\n";

            if (is_array($du_lieu_cung)) {
                $dv_start = (int)($du_lieu_cung['dai_van'] ?? -1);
                if ($dv_start >= 0 && $tuoi >= $dv_start && $tuoi < $dv_start + 10) {
                    $cung_dai_van_hien_tai = $ten_in_hoa . " (Từ {$dv_start} tuổi)";
                }

                if (isset($du_lieu_cung['sao_luu']) && is_array($du_lieu_cung['sao_luu'])) {
                    if (in_array('L.Thái Tuế', $du_lieu_cung['sao_luu'])) $cung_luu_thai_tue = $ten_in_hoa;
                }

                foreach ($du_lieu_cung as $thuoc_tinh => $gia_tri) {
                    if ($thuoc_tinh === 'cung_name' || $thuoc_tinh === 'ten_cung') continue;

                    if ($thuoc_tinh === 'is_tieu_han') {
                        if ($gia_tri) $cung_str .= "  - Ghi chú: LÀ CUNG TIỂU HẠN CỦA NĂM XEM\n";
                        continue;
                    }
                    if ($thuoc_tinh === 'thang_trong_cung') {
                        if (!empty($gia_tri)) {
                            $cung_str .= "  - Nguyệt Hạn (Tháng Âm Lịch): Các tháng " . implode(', ', (array)$gia_tri) . " trong năm\n";
                        }
                        continue;
                    }

                    $gia_tri_str = $flatten_sao($gia_tri);
                    if ($gia_tri_str === '') continue;

                    if (mb_stripos($gia_tri_str, 'Tuần') !== false) $cung_co_tuan[] = $ten_in_hoa;
                    if (mb_stripos($gia_tri_str, 'Triệt') !== false) $cung_co_triet[] = $ten_in_hoa;

                    $thuoc_tinh_vi = $key_map[$thuoc_tinh] ?? $thuoc_tinh;
                    $cung_str .= "  - {$thuoc_tinh_vi}: {$gia_tri_str}\n";
                }
            } else {
                if (mb_stripos($du_lieu_cung, 'Tuần') !== false) $cung_co_tuan[] = $ten_in_hoa;
                if (mb_stripos($du_lieu_cung, 'Triệt') !== false) $cung_co_triet[] = $ten_in_hoa;
                $cung_str .= "  - {$du_lieu_cung}\n";
            }
            $cung_str .= "";
        }

        $luu_tu_hoa_arr = [];
        if (!empty($thong_tin['tu_hoa_nam_xem'])) {
            foreach ($thong_tin['tu_hoa_nam_xem'] as $th) {
                $sao_name = mb_strtoupper($th['sao_name'], 'UTF-8');
                $cung_name = mb_strtoupper($th['cung_name'], 'UTF-8');
                $luu_tu_hoa_arr[] = "  + Sao {$sao_name} {$th['label']} nhập cung {$cung_name} ({$th['cung_chi']})";
            }
        }

        $tuan_str = !empty($cung_co_tuan) ? implode(' và ', array_unique($cung_co_tuan)) : "Không bị ảnh hưởng";
        $triet_str = !empty($cung_co_triet) ? implode(' và ', array_unique($cung_co_triet)) : "Không bị ảnh hưởng";
        $luu_tu_hoa_str = !empty($luu_tu_hoa_arr) ? implode("\n", $luu_tu_hoa_arr) : "  + Không có dữ liệu Lưu Tứ Hóa";

        return [
            'tuoi' => $tuoi,
            'gt_raw' => $gt_raw,
            'gioi_tinh_format' => $gioi_tinh_format,
            'nam_xem_so' => $nam_xem_so,
            'nam_xem_can_chi' => $nam_xem_can_chi,
            'cung_dai_van_hien_tai' => $cung_dai_van_hien_tai,
            'cung_luu_thai_tue' => $cung_luu_thai_tue,
            'luu_tu_hoa_str' => $luu_tu_hoa_str,
            'cung_str' => $cung_str,
            'tuan_str' => $tuan_str,
            'triet_str' => $triet_str
        ];
    }

    public static function build(array $thong_tin, array $la_so): string {
        $p_data = self::parse_laso_data($thong_tin, $la_so);

        $ngay_duong  = $thong_tin['ngay_duong'] ?? 'Không rõ';
        $ngay_am     = $thong_tin['ngay_am'] ?? 'Không rõ';
        $gio_am      = $thong_tin['gio_am'] ?? 'Không rõ';
        $nam_can_chi = $thong_tin['nam_can_chi'] ?? '';
        $chu_menh    = $thong_tin['chu_menh'] ?? 'Không rõ';
        $chu_than    = $thong_tin['chu_than'] ?? 'Không rõ';

        $rules = self::rules();

        return <<<PROMPT
Dựa trên dữ liệu cổ học trong Tử Vi Đẩu Số Toàn Thư của Trần Đoàn (Trần Hi Di), cùng hệ thống chú giải truyền thừa như Lâm Canh Phàm.
Hãy luận giải chi tiết lá số tử vi sau đây:

Họ và tên: {$thong_tin['ho_ten']} (Tuổi: {$p_data['tuoi']})
Giới tính: {$p_data['gioi_tinh_format']}
Ngày sinh Dương lịch: {$ngay_duong}
Ngày sinh Âm lịch: {$ngay_am} Năm {$nam_can_chi}
Giờ sinh: Giờ {$gio_am}
Mệnh: {$thong_tin['nam_nap_am']} - Cục: {$thong_tin['cuc_name']}
Âm Dương: {$thong_tin['am_duong_ly']} - Mệnh Cục: {$thong_tin['menh_cuc_ly']}
Chủ Mệnh: {$chu_menh}
Chủ Thân: {$chu_than}
Hậu vận: {$thong_tin['than_cu']}

THÔNG TIN VẬN HẠN:
Năm xem: {$p_data['nam_xem_so']} - Can Chi: {$p_data['nam_xem_can_chi']}
- Đại vận hiện tại: Cung {$p_data['cung_dai_van_hien_tai']}
- Cung chứa Lưu Thái Tuế (Trọng điểm năm): Cung {$p_data['cung_luu_thai_tue']}
- Biến động Tứ Hóa Lưu Niên:
{$p_data['luu_tu_hoa_str']}

LƯU Ý QUAN TRỌNG VỀ TUẦN/TRIỆT:
- Tuần Không đang án ngữ tại: {$p_data['tuan_str']}.
- Triệt Không đang án ngữ tại: {$p_data['triet_str']}.
(Bắt buộc phải đánh giá sự cản trở, làm bế tắc hoặc giảm sút năng lượng của Tuần, Triệt khi luận giải các cung này).

CHI TIẾT 12 CUNG TRÊN LÁ SỐ:
{$p_data['cung_str']}

YÊU CẦU DIỄN ĐẠT:
- Giọng điệu chuyên nghiệp, trang trọng, chuẩn huyền học Tử Vi.
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

## Luận Giải Lá Số Tử Vi của {$thong_tin['ho_ten']}

### Bản Mệnh {$thong_tin['chu_menh']} {$thong_tin['nam_nap_am']}
Mở đầu bằng một câu hình tượng ngắn về bản chất của sao chủ Mệnh (ví dụ: Thiên Tướng là sao ấn thụ, mang phong thái đường hoàng và trọng nguyên tắc; Tử Vi là đế tinh, thiên về lãnh đạo và tự tôn...) trước khi luận tính cách.
Sau đó luận ngắn gọn Mệnh-Thân kết hợp ra sao. Luận Mệnh Cục, Âm Dương và chiều hành hạn (thuận/nghịch) có ý nghĩa gì với đường đời tổng thể: xác định tiền vận hay hậu vận thuận hơn, mệnh vất vả hay nhàn. 
Kết phần này bằng một block gạch đầu dòng tiêu đề "Điểm mạnh của bản mệnh" liệt kê các yếu tố trợ lực cụ thể (cát tinh, Thần Sát nổi bật trong cung Mệnh).

### Tính Cách
Mở đầu bằng một bảng 2 cột: cột "Mặt" (Nổi trội / Tư duy / Quan hệ / Điểm yếu) và cột "Biểu hiện" tóm tắt ngắn. 
Sau bảng, viết thêm 2–3 đoạn phân tích kỹ hơn về con người và số mệnh dựa trên chính tinh cung Mệnh, Chủ Thân, các sao nổi bật và ảnh hưởng của Tuần, Triệt hay hung tinh hãm địa nếu có.
Sau các đoạn phân tích chính, viết thêm một đoạn blockquote (dùng ký hiệu >) phân tích sự kết hợp Mệnh-Thân trong việc định hình tính cách (ví dụ: Mệnh cương nghị nhưng Thân linh hoạt tạo ra người vừa có nguyên tắc vừa biết ứng biến...).

### Công Danh & Tài Lộc
Mở đầu bằng luận quỹ đạo cuộc đời tổng thể dựa trên Mệnh Cục, Âm Dương và chiều hành hạn (thuận/nghịch): xác định tiền vận hay hậu vận thuận hơn. 
Sau đó luận cung Quan Lộc là trọng tâm sự nghiệp: chính tinh, Tuần/Triệt nếu có, hệ quả về nghề nghiệp phù hợp và con đường thăng tiến. Không kết luận chung chung nếu không có sao dẫn đến kết luận đó.
Luận cung Tài Bạch và Điền Trạch: kiểu tích lũy hay hao tán, tiền đến từ đâu, dễ mất ở đâu. Nếu Thân cư Tài Bạch thì nêu ý nghĩa hậu vận.
Sau phần luận chính, viết thêm một đoạn blockquote (dùng ký hiệu >) phân tích tương tác giữa cung Quan Lộc và Tài Bạch nếu có điểm đặc biệt (ví dụ: Triệt Quan Lộc nhưng Tài Bạch có Lộc Tồn, công danh trắc trở nhưng tài lộc tự thân vẫn tích lũy được...).

### Tình Duyên
Luận cung Phu Thê là trọng tâm: chính tinh mô tả đặc điểm người phối ngẫu, các sao hung ảnh hưởng hôn nhân (Địa Kiếp, Hóa Kỵ, Tham Lang hãm...), xung hình nếu có.
Sau phần luận chính, viết thêm một đoạn blockquote (dùng ký hiệu >) nêu cụ thể hướng hóa giải dựa trên đặc tính sao trong lá số (ví dụ: Hóa Quyền trong Phu Thê gây tranh giành quyền kiểm soát, người nào chủ động nhường bước trước sẽ giữ được hôn nhân ổn định hơn).

### Gia Đạo
Luận các cung Phụ Mẫu, Tử Tức, Huynh Đệ, Điền Trạch theo thứ tự quan trọng. Mỗi cung chỉ nêu điểm nổi bật nhất (1-2 sao chủ đạo và hệ quả), không liệt kê toàn bộ sao. 
Riêng cung Tử Tức cần luận rõ: con cái đến sớm hay muộn, quan hệ cha mẹ - con cái thuận hay trắc trở, dựa trên chính tinh và trạng thái miếu/hãm của sao trong cung.

### Vận Hạn {$p_data['nam_xem_so']} ({$p_data['nam_xem_can_chi']})
- Quy ước ký hiệu: Tiền tố "L." (Ví dụ: L.Lộc Tồn, L.Đà La) là viết tắt của "Sao Lưu Niên" chỉ di chuyển trong năm xem hạn. 
KHÔNG được nhầm lẫn với sao gốc của năm sinh.

Phân tích theo đúng 3 lớp, mỗi lớp là một đoạn văn riêng:

**Đại Vận** Luận cung Đại Vận hiện tại: tên cung, tuổi bắt đầu, sao chủ đạo và xu hướng hung/cát của cả giai đoạn 10 năm. Chỉ nêu sao tác động rõ, không liệt kê hết.
**Tiểu Hạn** Luận cung Tiểu Hạn năm xem. Nếu cung bị Tuần/Triệt thì phân tích tác động cụ thể. Nêu 1-2 sao lưu niên nổi bật nhất và hệ quả.
**Lưu Tứ Hóa** Lần lượt luận 4 Hóa (Lộc, Quyền, Khoa, Kỵ): nhập vào cung nào, cung đó đang có sao gốc gì, tương tác ra sao. Ưu tiên Hóa Kỵ và Hóa Lộc. 
Nếu cung nhập bị Tuần/Triệt thì nêu rõ hiệu lực bị giảm.

Kết phần Vận Hạn bằng 1-2 câu tóm tắt hung cát tổng thể của năm, không dùng danh sách lời khuyên.

> Kết bài bằng một đoạn blockquote (dùng ký hiệu >) tóm tắt chân dung người này trong 4-6 câu, đảm bảo không trùng lặp ý y nguyên với các câu trước: 
tính cách cốt lõi từ Mệnh-Thân, quỹ đạo cuộc đời tổng thể (xác định mệnh vất vả hay nhàn), giai đoạn quan trọng nhất, và 1-2 điểm cần lưu ý xuất phát từ cấu trúc lá số.
)

Một số ví dụ SAI/ĐÚNG về diễn đạt và chuyên môn:
VD1:
+ SAI: Bạn cần kiên trì, không nản lòng trước những thử thách ban đầu.
+ ĐÚNG: Triệt án ngữ cung Quan Lộc, sự nghiệp không đi theo tuyến thẳng qua hệ thống tổ chức được, phù hợp hơn với môi trường tự do hoặc tự kinh doanh.

VD2:
+ SAI: Bạn cần cẩn trọng, bao dung và tránh sự đa nghi trong hôn nhân.
+ ĐÚNG: Địa Kiếp hãm địa trong cung Phu Thê, Hóa Quyền đồng cung — hai bên dễ tranh giành quyền kiểm soát, đây là điểm căng thẳng thường trực trong hôn nhân, không phải mâu thuẫn nhất thời.

VD3:
+ SAI: Năm nay có nhiều biến động, cần giữ tâm lý vững vàng, cẩn trọng giấy tờ hợp đồng.
+ ĐÚNG: Tiểu Hạn rơi vào cung Điền Trạch bị Triệt, L.Tang Môn đồng cung — kế hoạch liên quan nhà cửa, tài sản trong năm dễ bị chặn giữa chừng, có biến cố liên quan người thân lớn tuổi.
)

[AST_RESULT]
(Nội dung luận giải được đặt ở đây)
[/AST_RESULT]
PROMPT;
    }

    public static function build_qa(array $thong_tin, array $la_so, string $user_question, string $category): string {
        $p_data = self::parse_laso_data($thong_tin, $la_so);

        $ngay_duong = $thong_tin['ngay_duong'] ?? 'Không rõ';
        $ngay_am = $thong_tin['ngay_am'] ?? 'Không rõ';
        $gio_am = $thong_tin['gio_am'] ?? 'Không rõ';
        $nam_can_chi = $thong_tin['nam_can_chi'] ?? '';
        $chu_menh = $thong_tin['chu_menh'] ?? 'Không rõ';
        $chu_than = $thong_tin['chu_than'] ?? 'Không rõ';

        $category_map = [
            'TONGQUAN' => 'Tổng quan bản mệnh',
            'TINHCACH' => 'Tính cách',
            'CONGVIEC' => 'Công việc',
            'TAILOC'   => 'Tài lộc',
            'TINHCAM'  => 'Tình duyên',
            'SUKKHOE'  => 'Sức khỏe',
            'GIADAO'   => 'Gia đạo',
            'VANHAN'   => 'Vận hạn',
            'XUATHANH' => 'Xuất ngoại / Di chuyển',
            'QUANHE'   => 'Quan hệ / Hợp tác',
            'PHAPLY'   => 'Pháp lý / Tranh chấp',
        ];
        $category_vi = $category_map[$category] ?? $category;

        $topic_focus = "";
        if ($category === 'TONGQUAN') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Mệnh, Thân, Mệnh Cục, Âm Dương, Chủ Mệnh/Chủ Thân. Trả lời trực tiếp câu hỏi dựa trên cấu trúc bản mệnh gốc. CHỈ phân tích Đại Vận/Lưu Niên nếu câu hỏi có kèm yếu tố thời gian.";
        } elseif ($category === 'TINHCACH') {
            $topic_focus = "TẬP TRUNG VÀO: Chính tinh cung Mệnh, Chủ Mệnh, Chủ Thân, các sao định hình tính cách (Kình Dương, Đà La, Hóa Kỵ, Văn Xương, Văn Khúc...). Bỏ qua phân tích tài lộc/hôn nhân/sự nghiệp nếu không liên quan trực tiếp.";
        } elseif ($category === 'TINHCAM') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Phu Thê, Tử Tức, Đào Hoa, Hồng Loan, và các sao chủ về tình cảm. Bỏ qua phân tích chi tiết về tiền bạc/công việc nếu không liên quan mật thiết.";
        } elseif ($category === 'TAILOC') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Tài Bạch, Điền Trạch, Phúc Đức, Hóa Lộc, Lộc Tồn. Đánh giá luồng tiền, khả năng tụ tài hay hao tài.";
        } elseif ($category === 'CONGVIEC') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Quan Lộc, Mệnh, Thân, Hóa Quyền. Đánh giá sự nghiệp, thăng tiến, áp lực đồng nghiệp.";
        } elseif ($category === 'SUKKHOE') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Tật Ách, Mệnh, và các hung tinh/sát tinh. Lưu ý: Chỉ cảnh báo xu hướng sức khỏe khách quan, KHÔNG ĐƯỢC PHÁN QUYẾT TÍNH MẠNG SỐNG CHẾT.";
        } elseif ($category === 'GIADAO') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Phụ Mẫu, Huynh Đệ, Tử Tức và Điền Trạch. Luận theo cung liên quan trực tiếp đến câu hỏi.";
        } elseif ($category === 'VANHAN') {
            $topic_focus = "TẬP TRUNG VÀO: Đại vận hiện tại (Cung {$p_data['cung_dai_van_hien_tai']}), Lưu Thái Tuế (Cung {$p_data['cung_luu_thai_tue']}), Tứ Hóa Lưu Niên và các sao Lưu.";
        } elseif ($category === 'XUATHANH') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Thiên Di, Quan Lộc, và các sao liên quan đến di chuyển (Thiên Mã, Lưu Hà...). Đánh giá lá số có hợp xuất ngoại, thay đổi môi trường sống hay không.";
        } elseif ($category === 'QUANHE') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Nô Bộc, Huynh Đệ. Đánh giá mức độ tin người, hợp tác được hay dễ bị lợi dụng, quan hệ bạn bè đối tác ra sao.";
        } elseif ($category === 'PHAPLY') {
            $topic_focus = "TẬP TRUNG VÀO: Cung Quan Lộc, Tật Ách, và các sao pháp lý (Thiên Hình, Thiên La, Địa Võng). Đánh giá lá số có dễ vướng kiện tụng, tranh chấp không.";
        } else {
            $topic_focus = "TẬP TRUNG VÀO: Trực tiếp trả lời câu hỏi dựa trên dữ liệu lá số đã cung cấp.";
        }

        $rules = self::rules();

        return <<<PROMPT
Dựa trên dữ liệu cổ học trong Tử Vi Đẩu Số Toàn Thư của Trần Đoàn (Trần Hi Di), cùng hệ thống chú giải truyền thừa như Lâm Canh Phàm.
Hãy đọc lá số tử vi và trả trả lời câu hỏi sau đây:

CÂU HỎI: "{$user_question}"
CHỦ ĐỀ: {$category_vi}
{$topic_focus}

Họ và tên: {$thong_tin['ho_ten']} (Tuổi: {$p_data['tuoi']})
Giới tính: {$p_data['gioi_tinh_format']}
Ngày sinh Dương lịch: {$ngay_duong}
Ngày sinh Âm lịch: {$ngay_am} Năm {$nam_can_chi}
Giờ sinh: Giờ {$gio_am}
Mệnh: {$thong_tin['nam_nap_am']} - Cục: {$thong_tin['cuc_name']}
Âm Dương: {$thong_tin['am_duong_ly']} - Mệnh Cục: {$thong_tin['menh_cuc_ly']}
Chủ Mệnh: {$chu_menh} - Chủ Thân: {$chu_than}
Hậu vận: {$thong_tin['than_cu']}

THÔNG TIN VẬN HẠN:
Năm xem: {$p_data['nam_xem_so']} - Can Chi: {$p_data['nam_xem_can_chi']}
- Đại vận hiện tại: Cung {$p_data['cung_dai_van_hien_tai']}
- Cung chứa Lưu Thái Tuế (Trọng điểm năm): Cung {$p_data['cung_luu_thai_tue']}
- Biến động Tứ Hóa Lưu Niên:
{$p_data['luu_tu_hoa_str']}

LƯU Ý QUAN TRỌNG VỀ TUẦN/TRIỆT:
- Tuần Không đang án ngữ tại: {$p_data['tuan_str']}.
- Triệt Không đang án ngữ tại: {$p_data['triet_str']}.
(Bắt buộc phải đánh giá sự cản trở, làm bế tắc hoặc giảm sút năng lượng của Tuần, Triệt khi luận giải các cung này).

CHI TIẾT 12 CUNG TRÊN LÁ SỐ:
{$p_data['cung_str']}

YÊU CẦU DIỄN ĐẠT:
- XƯNG HÔ: là Bạn (có thể dùng Tên người dùng, sử dụng 1 cách tự nhiên) đảm bảo xuyên suốt. Cấm dùng "Anh/Chị/Em", người này, họ.
- Ngôn ngữ tự nhiên, rõ ràng, dễ hiểu.
- Trình bày mạch lạc, không dài dòng.
- Văn phong chuyên nghiệp, đi thẳng vào vấn đề.
- KHÔNG viết lại các thông tin đã cung cấp -> Hãy luận giải trực tiếp.
- Tránh diễn đạt lặp ý, ví dụ blockquote (nếu có) không được viết lại y nguyên diễn giải trước đó, phải diễn giải trực quan hơn hoặc phương pháp khác để không bị lặp template.

{$rules}

[HƯỚNG DẪN NỘI DUNG]
## Giáp đáp câu hỏi của {$thong_tin['ho_ten']} từ lá số Tử vi
- Trả lời TRỰC TIẾP và ĐÚNG TRỌNG TÂM vào câu hỏi: "{$user_question}" đúng chủ đề {$category_vi}. Không phân tích lan man sang các chủ đề khác.
- Ưu tiên đưa dẫn chứng lên trước, kết luận ra sau. Không mở đầu bằng nhận định rồi mới giải thích. Nêu tên sao, vị trí cung, trạng thái (miếu/hãm, Tuần/Triệt) trước, để dữ liệu tự dẫn đến kết luận.
- Mọi nhận định phải có dẫn chứng cụ thể từ lá số. CẤM nói chung chung kiểu "giai đoạn đầu", "về sau", "khi còn trẻ" mà không kèm mốc thời gian rõ ràng. Khi đề cập đến giai đoạn thời gian PHẢI chỉ rõ tên Đại Vận, cung, khoảng tuổi. Ví dụ: "giai đoạn 33-42 tuổi (Đại Vận cung Tử Tức)" thay vì "giai đoạn sau này".

- Lập luận theo 3 lớp:
  + Hiện trạng trong lá số (sao nào, ở cung nào, trạng thái miếu/hãm/đắc địa, có Tuần/Triệt không)
  + Tác động của sao đó lên cung (sinh, khắc, hội chiếu, hung/cát ra sao)
  + Hệ quả trong cuộc sống (biểu hiện cụ thể, không trừu tượng)

- Nếu câu hỏi liên quan đến diễn biến theo thời gian (tài lộc, tình cảm, sự nghiệp, sức khỏe, vận hạn) -> kết thúc bằng một đoạn blockquote (dùng ký hiệu >) phân tích Đại Vận có liên quan trực tiếp: so sánh vận trước/sau nếu có sự thay đổi rõ, chỉ rõ cung/tuổi cụ thể.
- Nếu câu hỏi về tính cách, bản mệnh hoặc đặc điểm cố định trong lá số thì KHÔNG cần blockquote đại vận.

- CẤM kết luận bằng câu trấn an chung chung kiểu "không phải lá số xấu", "vẫn có thể thành công", "không đến nỗi tệ" nếu không có sao/cung dẫn đến kết luận đó. Kết luận phải là nhận định từ lá số, không phải lời an ủi.
- Quy ước ký hiệu: Tiền tố "L." (Ví dụ: L.Lộc Tồn, L.Đà La) là sao Lưu Niên, chỉ di chuyển trong năm xem. KHÔNG nhầm với sao gốc năm sinh.

- Nếu lá số và vấn đề của câu hỏi trái ngược nhau, hãy xem "XỬ LÝ MÂU THUẪN GIỮA CÂU HỎI VÀ LÁ SỐ".

- XỬ LÝ MÂU THUẪN GIỮA CÂU HỎI VÀ LÁ SỐ:
Nếu người dùng mô tả trái ngược với bề nổi của lá số (VD: Cung Tài Bạch đẹp nhưng người dùng than đang phá sản, hoặc Cung Quan Lộc xấu nhưng người dùng báo đang làm chủ),
TUYỆT ĐỐI KHÔNG phủ nhận của người dùng. Hãy thực hiện tuần tự:
    1. Phá cách: Kiểm tra các hung tinh/sát tinh (Không, Kiếp, Kình, Đà, Hỏa, Linh, Phá Toái, Đại Hao) có hội chiếu vào cung đang xét không.
    2. Lực cản: Xem Tuần Không hoặc Triệt Không có đang án ngữ và làm bế tắc cung đó không.
    3. Đại vận/Lưu niên: Nếu cung gốc tốt nhưng hiện tại tồi, giải thích dựa trên Đại Vận hiện tại hoặc Lưu Niên năm nay.
    4. Chỉ khi đã rà soát 3 bước trên mà vẫn không thấy điểm hợp lý:
    Mới khách quan trả lời rằng "Sự kiện này hiện không thể hiện rõ trên cấu trúc chính của lá số", sau đó tập trung vào phân tích điểm mạnh/yếu gốc.
    KHÔNG ngụy tạo lý do, không định hướng.

Một số ví dụ SAI/ĐÚNG về diễn đạt:
VD1:
+ SAI (không đưa thời gian cụ thể): Bạn có xu hướng tài lộc trắc trở ở giai đoạn đầu...
+ ĐÚNG (chỉ rõ giai đoạn): Tài lộc trắc trở trong giai đoạn 33-42 tuổi (Đại Vận cung Tử Tức)...

VD2:
+ SAI (kết luận trước, dẫn chứng sau): Bạn có hôn nhân trắc trở vì Tham Lang hãm địa...
+ ĐÚNG (dẫn chứng trước, kết luận tự nhiên): Tham Lang hãm địa trong cung Phu Thê, Địa Kiếp đồng cung -> hôn nhân dễ nảy sinh bất ổn, không phẳng lặng.

VD3:
+ SAI (lời an ủi chung chung): Năm nay có nhiều biến động nhưng Bạn vẫn có thể vượt qua nếu cẩn thận.
+ ĐÚNG (nhận định từ lá số): Tiểu Hạn rơi vào cung Điền Trạch bị Triệt, L.Tang Môn đồng cung -> kế hoạch dễ bị chậm hoặc trì trệ, có biến cố liên quan người thân lớn tuổi.

[AST_RESULT]
(Nội dung luận giải được đặt ở đây)
[/AST_RESULT]
PROMPT;
    }
}