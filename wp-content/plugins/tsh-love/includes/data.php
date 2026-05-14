<?php
if (!defined('ABSPATH')) exit;

class TshLove_Data {

    public static function getLifePathHint(int $number): string {
        $hints = [
            1 => 'Đại diện cho tinh thần tiên phong; trọng tâm năng lượng đặt vào việc rèn luyện sự tự lập.',
            2 => 'Mang tần số của sự hòa hợp; bài học cốt lõi là xây dựng tính kiên nhẫn và năng lực gắn kết.',
            3 => 'Cộng hưởng với sự sáng tạo; ưu tiên việc duy trì tinh thần lạc quan và cởi mở.',
            4 => 'Từ trường của tính kỷ luật; đòi hỏi xây dựng nền tảng vững chắc và nguyên tắc rõ ràng.',
            5 => 'Đại diện cho sự linh hoạt; trọng tâm là thích nghi với thay đổi và học cách tự do trong khuôn khổ.',
            6 => 'Mang rung động của trách nhiệm; hướng năng lượng vào việc chăm sóc và duy trì sinh thái gia đình.',
            7 => 'Đại diện cho trí tuệ nội tâm; bài học lớn nhất là quan sát, học hỏi chuyên sâu và đúc kết chân lý.',
            8 => 'Tần số của thực tế và thành tựu; trọng tâm đặt vào việc quản lý nguồn lực và xây dựng cơ ngơi.',
            9 => 'Cộng hưởng với lòng nhân đạo; hướng tới sự cống hiến, vị tha và các giá trị lợi ích cộng đồng.',
            11 => 'Mang từ trường trực giác cao; đòi hỏi khả năng thấu hiểu cảm xúc và truyền cảm hứng tinh thần.',
            22 => 'Đại diện cho năng lượng kiến tạo; trọng tâm là hiện thực hóa những ý tưởng lớn thành di sản.',
            33 => 'Mang tần số chữa lành diện rộng; hướng năng lượng vào lòng trắc ẩn và phục vụ vô điều kiện.'
        ];
        return $hints[$number] ?? 'Trường năng lượng đặc biệt, mang tính độc bản cao.';
    }

    public static function getLifePathDetail(int $number): string {
        $details = [
            1 => 'Đại diện cho sự độc lập, tiên phong và khả năng lãnh đạo. Bản chất quyết đoán, có cái tôi lớn, luôn muốn tự chủ và thường giữ vai trò dẫn dắt trong các mối quan hệ.',
            2 => 'Đại diện cho sự hòa bình, thấu cảm và sự gắn kết. Bản chất dịu dàng, biết lắng nghe, luôn tìm kiếm sự an toàn, hài hòa và có xu hướng hỗ trợ từ phía sau.',
            3 => 'Mang năng lượng của sự sáng tạo, lạc quan và khả năng truyền cảm hứng. Bản chất thích giao tiếp, hoạt ngôn và lan tỏa sự tích cực cho những người xung quanh.',
            4 => 'Là con số của sự thực tế, kỷ luật và đáng tin cậy. Bản chất coi trọng sự ổn định, làm việc có kế hoạch, nền tảng vững chắc và hướng tới những cam kết an toàn.',
            5 => 'Đại diện cho sự tự do, linh hoạt và thích khám phá. Bản chất đam mê những trải nghiệm mới, bài xích sự gò bó, dễ thích nghi và luôn tạo ra luồng sinh khí mới mẻ.',
            6 => 'Là con số của trách nhiệm, tình yêu thương và hướng về gia đình. Bản chất ấm áp, có bản năng chăm sóc, bảo vệ người thân và ưu tiên hạnh phúc chung.',
            7 => 'Mang năng lượng của trí tuệ, sự sâu sắc và trực giác tốt. Bản chất cần nhiều không gian riêng tư, thích phân tích, chiêm nghiệm và tìm kiếm quy luật của mọi vấn đề.',
            8 => 'Là con số của bản lĩnh, tính thực tế và hướng tới thành tựu. Bản chất trọng vật chất, có năng lực tổ chức xuất sắc, độc lập tài chính và mang phong thái quyền lực.',
            9 => 'Đại diện cho sự bao dung, nhân đạo và lòng vị tha. Bản chất có lý tưởng sống cao đẹp, giàu lòng trắc ẩn, luôn muốn cống hiến và có dung lượng tha thứ lớn.',
            11 => '<strong>(Con số Master)</strong> Đại diện cho trực giác nhạy bén, sự thấu cảm và tâm linh. Bản chất nhạy cảm, chân thành, có lý tưởng cao đẹp và hướng đến sự hòa hợp sâu sắc.',
            22 => '<strong>(Con số Master)</strong> Đại diện cho sự kiến tạo, tầm nhìn lớn và khả năng thực thi xuất chúng. Bản chất có khả năng biến tầm nhìn thành hiện thực và xây dựng nền tảng vững chắc.',
            33 => '<strong>(Con số Master)</strong> Mang năng lượng chữa lành, hy sinh và tình yêu thương vô điều kiện. Bản chất gánh vác trách nhiệm lớn lao, bao dung và có năng lực dẫn dắt tinh thần.'
        ];
        return $details[$number] ?? 'Năng lượng đặc biệt.';
    }

    public static function getCompatibilityAnalysis(int $n1, int $n2, int $percent): array {
        if ($percent >= 85) {
            $result = [
                'summary' => "Cặp đôi số $n1 và số $n2 tạo ra một phổ năng lượng hài hòa và đồng điệu tự nhiên.",
                'pros'    => "Hai trường năng lượng có tính bổ trợ cao, dễ dàng thiết lập mục tiêu chung mà không gây biến dạng bản chất của nhau.",
                'cons'    => "Sự an toàn và thấu hiểu quá mức dễ đưa mối quan hệ vào trạng thái tĩnh, thiếu các điểm thắt để tạo đột phá.",
                'advice1' => "Năng lượng đồng pha dễ tạo ra vùng an toàn; điểm bứt phá nằm ở việc chủ động thiết lập các trải nghiệm mới mẻ.",
                'advice2' => "Sự thấu cảm tự nhiên sẽ đạt mức tối đa khi các ranh giới phòng vệ được dỡ bỏ và mong muốn sâu kín được minh bạch hóa."
            ];
        } elseif ($percent >= 70) {
            $result = [
                'summary' => "Cặp đôi số $n1 và số $n2 tạo nên một quỹ đạo nhiều tiềm năng nhưng đòi hỏi sự tinh chỉnh liên tục.",
                'pros'    => "Sự cọ xát năng lượng mang lại góc nhìn mới mẻ, lấp đầy các khoảng trống nhận thức của cả hai phía.",
                'cons'    => "Sự lệch pha trong ưu tiên cuộc sống dễ kích hoạt các phản ứng phòng vệ nếu luồng giao tiếp bị tắc nghẽn.",
                'advice1' => "Sự chênh lệch nhịp độ xử lý vấn đề yêu cầu khoảng lùi về không gian để cả hai từ từ đồng bộ tần số.",
                'advice2' => "Điểm neo giữ cấu trúc quan hệ này nằm ở biên độ lắng nghe và năng lực tiếp nhận các góc nhìn đối lập."
            ];
        } else {
            $result = [
                'summary' => "Cặp đôi số $n1 và số $n2 mang hai trường năng lượng đối kháng, tạo ra lực đẩy ngược đan xen sức hút.",
                'pros'    => "Môi trường cọ xát mạnh mẽ ép buộc cả hai bứt phá khỏi giới hạn cá nhân để đạt độ chín về mặt cảm xúc.",
                'cons'    => "Cơ chế xử lý thông tin và phản xạ trái ngược nhau dễ làm tăng sinh các xung đột mang tính triệt tiêu.",
                'advice1' => "Ranh giới cá nhân cần được phân định rạch ròi; mọi hành vi áp đặt tiêu chuẩn đều sẽ tạo ra lực phản kháng mạnh.",
                'advice2' => "Trạng thái cân bằng chỉ xuất hiện khi sự khác biệt được định dạng là quy luật tự nhiên thay vì một khuyết điểm cần sửa chữa."
            ];
        }

        if (($n1 == 5 && in_array($n2, [1, 10])) || ($n2 == 5 && in_array($n1, [1, 10]))) {
            $result['summary'] = "Cặp đôi $n1 và $n2 là sự va chạm năng lượng cường độ cao giữa xu hướng dẫn dắt và bản năng tự do.";
            $result['pros']    = "Không gian cá nhân được tôn trọng tuyệt đối. Biến số từ sự ngẫu hứng giúp duy trì lực hút vật lý và sự tò mò liên tục.";
            $result['cons']    = "Lực ly tâm sinh ra từ hai cái tôi độc lập quá lớn dễ làm đứt gãy sợi dây liên kết nếu thiếu một mỏ neo chung về mục tiêu.";

            $result['advice1'] = ($n1 == 5) ?
                "Tần số dịch chuyển liên tục và nhu cầu làm mới cao dễ dẫn đến sự xói mòn cấu trúc cam kết nếu không được định vị trong một mục tiêu dài hạn." :
                "Xung lực kiểm soát hoặc nỗ lực áp đặt ý chí thường trực tiếp kích hoạt bản năng phòng vệ và phản ứng đối kháng nhằm bảo vệ ranh giới tự do cá nhân.";

            $result['advice2'] = ($n2 == 5) ?
                "Tần số dịch chuyển liên tục và nhu cầu làm mới cao dễ dẫn đến sự xói mòn cấu trúc cam kết nếu không được định vị trong một mục tiêu dài hạn." :
                "Xung lực kiểm soát hoặc nỗ lực áp đặt ý chí thường trực tiếp kích hoạt bản năng phòng vệ và phản ứng đối kháng nhằm bảo vệ ranh giới tự do cá nhân.";
        }

        if (($n1 == 5 && $n2 == 11) || ($n1 == 11 && $n2 == 5)) {
            $result['summary'] = "Cặp đôi $n1 và $n2 là giao điểm phức tạp giữa tính dịch chuyển vật lý và chiều sâu tâm linh.";
            $result['pros']    = "Cơ chế bù trừ rõ rệt: Khí chất động của số 5 phá vỡ trạng thái tĩnh của số 11, trong khi năng lượng số 11 đóng vai trò mỏ neo cảm xúc.";
            $result['cons']    = "Biên độ dao động khác biệt: Quá trình phóng thích năng lượng hướng ngoại của số 5 dễ làm tổn thương hệ thống phản ứng nhạy cảm của số 11.";

            $result['advice1'] = ($n1 == 5) ?
                "Sự đồng bộ tần số diễn ra hiệu quả khi nhịp độ dịch chuyển nhanh được điều tiết để tương thích với trường cảm xúc sâu và hệ thống phản ứng nhạy cảm của đối phương." :
                "Trạng thái cân bằng được duy trì thông qua việc mở rộng biên độ an toàn nội tâm nhằm thích nghi với các luồng năng lượng biến động liên tục từ môi trường.";

            $result['advice2'] = ($n2 == 5) ?
                "Sự đồng bộ tần số diễn ra hiệu quả khi nhịp độ dịch chuyển nhanh được điều tiết để tương thích với trường cảm xúc sâu và hệ thống phản ứng nhạy cảm của đối phương." :
                "Trạng thái cân bằng được duy trì thông qua việc mở rộng biên độ an toàn nội tâm nhằm thích nghi với các luồng năng lượng biến động liên tục từ môi trường.";
        }

        return $result;
    }

    public static function getMatchHint(int $percent): string {
        if ($percent >= 90) return 'Hợp nhau gần như tuyệt đối. Điều kiện lý tưởng để gắn kết bền vững.';
        if ($percent >= 80) return 'Hai bạn rất hòa hợp với nhau. Dễ dàng thấu hiểu và chia sẻ mục tiêu chung.';
        if ($percent >= 70) return 'Hai bạn rất hợp nhau, nhưng cần một chút chủ động để tránh lệch nhịp.';
        if ($percent >= 60) return 'Có sự phù hợp nhưng chưa ổn định. Đòi hỏi sự nhượng bộ lớn để bù trừ năng lượng.';
        return 'Nhiều thử thách. Tồn tại sự đối kháng năng lượng.';
    }

    public static function getSoulUrgeHint(int $number): string {
        $hints = [
            1 => 'Khát khao tự chủ mãnh liệt, nhu cầu cốt lõi là được công nhận năng lực.',
            2 => 'Động lực lớn nhất là sự thấu hiểu, bình yên và thiết lập cảm giác thuộc về.',
            3 => 'Nhu cầu tỏa sáng, bộc lộ bản sắc cá nhân và mở rộng kết nối xã hội.',
            4 => 'Tiềm thức đòi hỏi một môi trường vận hành ổn định, trật tự và rõ ràng.',
            5 => 'Động lực bên trong là sự dịch chuyển và liên tục phá vỡ các giới hạn.',
            6 => 'Nhu cầu sâu thẳm là được chăm sóc người khác và nhận lại sự trân trọng.',
            7 => 'Đòi hỏi không gian tĩnh lặng, cách ly tạp âm để tự chiêm nghiệm.',
            8 => 'Khát khao đạt được các thành tựu đo lường được và sự sung túc vật chất.',
            9 => 'Mong muốn hiện thực hóa các lý tưởng cao đẹp và phụng sự cộng đồng.',
            11 => 'Khao khát sự đồng điệu tâm linh, vượt khỏi các giá trị vật chất thông thường.',
            22 => 'Động lực tạo ra những hệ thống hoặc công trình mang tầm vóc vĩ mô.',
            33 => 'Khát khao xoa dịu tổn thương, phát tỏa tình yêu thương vô điều kiện.'
        ];
        return $hints[$number] ?? 'Động lực nội tâm bí ẩn.';
    }

    public static function getAttitudeHint(int $number): string {
        $hints = [
            1 => 'Phản xạ chủ động đối đầu, bảo vệ ranh giới với lòng tự tôn cao.',
            2 => 'Phản xạ lắng nghe, nhượng bộ và thiết lập cơ chế né tránh xung đột trực diện.',
            3 => 'Sử dụng sự lạc quan hoặc hoạt ngôn để xoa dịu và bẻ lái căng thẳng.',
            4 => 'Phản xạ phân định đúng sai rạch ròi theo logic và quy tắc thực tế.',
            5 => 'Phản xạ nhanh nhạy, bốc đồng và lập tức từ chối mọi sự ràng buộc.',
            6 => 'Xu hướng tự gánh vác trách nhiệm, dễ kích hoạt bản năng lo lắng thái quá.',
            7 => 'Phản xạ im lặng, thu mình quan sát và tự rút lui để xử lý luồng thông tin.',
            8 => 'Dùng lý trí áp đảo trực tiếp, tập trung hoàn toàn vào kết quả giải quyết.',
            9 => 'Phản xạ bao dung, dễ dàng bỏ qua tiểu tiết để ưu tiên bức tranh hòa bình tổng thể.'
        ];
        return $hints[$number] ?? 'Biên độ phản ứng biến thiên, không cố định.';
    }

    public static function getRelationshipHint(int $number): string {
        $hints = [
            1 => 'Trọng tâm duy trì nằm ở việc tôn trọng tuyệt đối ranh giới cá nhân.',
            2 => 'Lực hút cốt lõi sinh ra từ sự nhạy cảm, đồng cảm và khả năng chữa lành.',
            3 => 'Điểm tựa chính là sự cởi mở trong giao tiếp và khả năng chia sẻ sở thích.',
            4 => 'Năng lượng tập trung vào việc xây dựng nền tảng tài chính và cấu trúc ổn định.',
            5 => 'Đòi hỏi việc liên tục làm mới trải nghiệm để tránh kích hoạt trạng thái ngột ngạt.',
            6 => 'Ưu tiên tối thượng cho tinh thần trách nhiệm và bản năng vun vén tổ ấm.',
            7 => 'Kết nối bền vững thông qua giao tiếp tri thức và sự tôn trọng khoảng lặng cá nhân.',
            8 => 'Giao thoa mạnh mẽ về vật chất và quyền lực khi cả hai thống nhất được mục tiêu chung.',
            9 => 'Thử thách lớn nhất là việc triệt tiêu cái tôi cá nhân để nhường chỗ cho sự bao dung.'
        ];
        return $hints[$number] ?? 'Cấu trúc năng lượng vượt quá.';
    }
}