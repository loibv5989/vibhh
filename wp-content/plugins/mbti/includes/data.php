<?php
if (!defined('ABSPATH')) exit;

class MBTI_Data {
    public static function getQuestions(): array {
        static $questions = null;

        if ($questions === null) {
            $questions = [
                ['id'=>'q1', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn thường cảm thấy tràn đầy năng lượng sau khi tham gia các hoạt động đông người.'],
                ['id'=>'q2', 'axis'=>'EI','dir'=>-1,'text'=>'Bạn thường thích dành thời gian một mình ở nhà hơn là tham gia các hoạt động xã hội.'],
                ['id'=>'q3', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn khá dễ dàng bắt chuyện với những người mới quen.'],
                ['id'=>'q4', 'axis'=>'EI','dir'=>-1,'text'=>'Bạn không dễ mở lòng với người mới quen và chỉ có một vài người bạn thật sự thân thiết.'],
                ['id'=>'q5', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn thường chủ động bắt đầu cuộc trò chuyện trong nhóm.'],
                ['id'=>'q6', 'axis'=>'EI','dir'=>-1,'text'=>'Sau khi giao tiếp với nhiều người trong thời gian dài, bạn cần thời gian một mình để hồi phục năng lượng.'],
                ['id'=>'q7', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn cảm thấy khá thoải mái khi trở thành tâm điểm chú ý trong một nhóm lớn.'],
                ['id'=>'q8', 'axis'=>'EI','dir'=>-1,'text'=>'Bạn thường suy nghĩ kỹ trước khi chia sẻ ý kiến với người khác.'],
                ['id'=>'q9', 'axis'=>'SN','dir'=> 1,'text'=>'Bạn thích thông tin thực tế và có dữ liệu rõ ràng hơn là các giả thuyết trừu tượng.'],
                ['id'=>'q10','axis'=>'SN','dir'=>-1,'text'=>'Bạn thường nghĩ về những khả năng trong tương lai hơn là tập trung hoàn toàn vào hiện tại.'],
                ['id'=>'q11','axis'=>'SN','dir'=> 1,'text'=>'Bạn chú ý nhiều đến những gì đang diễn ra trước mắt hơn là các ý tưởng xa vời.'],
                ['id'=>'q12','axis'=>'SN','dir'=>-1,'text'=>'Bạn thích tìm hiểu ý nghĩa sâu xa đằng sau một sự việc.'],
                ['id'=>'q13','axis'=>'SN','dir'=> 1,'text'=>'Bạn tin vào kinh nghiệm thực tế đã trải qua hơn là linh cảm.'],
                ['id'=>'q14','axis'=>'SN','dir'=>-1,'text'=>'Khi giải quyết vấn đề, bạn thường tìm cách tiếp cận mới và sáng tạo.'],
                ['id'=>'q15','axis'=>'SN','dir'=> 1,'text'=>'Bạn thích hướng dẫn chi tiết từng bước hơn là chỉ dẫn chung chung.'],
                ['id'=>'q16','axis'=>'SN','dir'=>-1,'text'=>'Bạn dễ hứng thú với những câu hỏi lớn về triết học hoặc ý nghĩa cuộc sống.'],
                ['id'=>'q17','axis'=>'TF','dir'=> 1,'text'=>'Khi đưa ra quyết định, bạn thường ưu tiên logic và tính hợp lý.'],
                ['id'=>'q18','axis'=>'TF','dir'=>-1,'text'=>'Bạn dễ đồng cảm và thường đặt mình vào vị trí của người khác để hiểu họ.'],
                ['id'=>'q19','axis'=>'TF','dir'=> 1,'text'=>'Bạn coi trọng sự thẳng thắn hơn việc giữ hòa khí bằng những lời nói tránh né.'],
                ['id'=>'q20','axis'=>'TF','dir'=>-1,'text'=>'Bạn thường cân nhắc cảm xúc của người khác khi đưa ra quyết định.'],
                ['id'=>'q21','axis'=>'TF','dir'=> 1,'text'=>'Trong tranh luận, lập luận logic quan trọng hơn việc làm hài lòng người nghe.'],
                ['id'=>'q22','axis'=>'TF','dir'=>-1,'text'=>'Bạn dễ xúc động khi nghe những câu chuyện khó khăn của người khác.'],
                ['id'=>'q23','axis'=>'TF','dir'=> 1,'text'=>'Bạn đánh giá ý tưởng dựa trên hiệu quả và tính khả thi hơn là cảm giác cá nhân.'],
                ['id'=>'q24','axis'=>'TF','dir'=>-1,'text'=>'Bầu không khí hòa hợp trong nhóm đôi khi quan trọng hơn việc chọn phương án tối ưu.'],
                ['id'=>'q25','axis'=>'JP','dir'=> 1,'text'=>'Bạn thích lên kế hoạch rõ ràng trước khi bắt đầu công việc.'],
                ['id'=>'q26','axis'=>'JP','dir'=>-1,'text'=>'Bạn thích sự linh hoạt và không thoải mái khi bị ràng buộc bởi quá nhiều quy tắc.'],
                ['id'=>'q27','axis'=>'JP','dir'=> 1,'text'=>'Bạn thường hoàn thành công việc sớm để tránh áp lực vào phút cuối.'],
                ['id'=>'q28','axis'=>'JP','dir'=>-1,'text'=>'Bạn làm việc tập trung nhất khi deadline đang đến gần.'],
                ['id'=>'q29','axis'=>'JP','dir'=> 1,'text'=>'Bạn thích mọi thứ được sắp xếp gọn gàng và có trật tự.'],
                ['id'=>'q30','axis'=>'JP','dir'=>-1,'text'=>'Bạn thích giữ nhiều lựa chọn mở trước khi đưa ra quyết định cuối cùng.'],
                ['id'=>'q31','axis'=>'JP','dir'=> 1,'text'=>'Bạn cảm thấy khó chịu khi kế hoạch thay đổi đột ngột.'],
                ['id'=>'q32','axis'=>'JP','dir'=>-1,'text'=>'Bạn thích khám phá nhiều hướng khác nhau trước khi chọn một con đường cụ thể.'],
            ];
        }

        $shuffled = $questions;
        shuffle($shuffled);
        return $shuffled;
    }

    public static function getProfiles(): array {
        static $profiles = null;
        if ($profiles !== null) return $profiles;

        $profiles = [
            'ENFP'=>[
                'title'     =>'Người truyền cảm hứng (The Campaigner)',
                'tong_quan' =>'Bạn mang trong mình nguồn năng lượng dồi dào, sự sáng tạo và luôn cởi mở với những trải nghiệm mới. Bạn dễ dàng kết nối với mọi người và thường là người mang lại bầu không khí tích cực cho môi trường xung quanh.',
                'su_nghiep' =>'Bạn sẽ phát huy tối đa năng lực trong những môi trường cần ý tưởng mới, sự linh hoạt và kỹ năng giao tiếp tốt. Tuy nhiên, đôi khi bạn cần rèn luyện thêm sự kiên trì để có thể theo đuổi những mục tiêu dài hạn.',
                'tinh_yeu'  =>'Trong chuyện tình cảm, bạn là người chân thành và vô cùng nhiệt thành. Bạn luôn thích cùng đối phương khám phá những trải nghiệm mới mẻ và chia sẻ các giá trị sống cốt lõi.',
            ],
            'ENTJ'=>[
                'title'     =>'Người lãnh đạo (The Commander)',
                'tong_quan' =>'Bạn là người cực kỳ quyết đoán và luôn có một tầm nhìn rõ ràng. Bạn thích tổ chức mọi thứ xung quanh mình theo hướng logic, hiệu quả và luôn hướng tới những mục tiêu lớn lao.',
                'su_nghiep' =>'Bạn sinh ra để phù hợp với các vai trò lãnh đạo, điều hành hoặc quản lý dự án. Bạn rất giỏi lập kế hoạch và định hướng nhóm. Tuy nhiên, đôi khi bạn cần chậm lại một chút để chú ý hơn đến cảm xúc của cộng sự.',
                'tinh_yeu'  =>'Trong mối quan hệ, bạn coi trọng sự tôn trọng lẫn nhau và tinh thần cùng phát triển. Thay vì những lời nói lãng mạn sáo rỗng, bạn thường thể hiện sự quan tâm thông qua những hành động vun vén thiết thực.',
            ],
            'ENTP'=>[
                'title'     =>'Người tranh luận (The Debater)',
                'tong_quan' =>'Bạn sở hữu tư duy nhanh nhạy, tính tò mò bẩm sinh và niềm đam mê khám phá những ý tưởng mới. Bạn thích đặt câu hỏi, phản biện và luôn tìm ra nhiều góc nhìn độc đáo cho cùng một vấn đề.',
                'su_nghiep' =>'Bạn sẽ tỏa sáng rực rỡ trong môi trường sáng tạo như khởi nghiệp, marketing hoặc tư vấn chiến lược. Thách thức lớn nhất của bạn thường là việc phải duy trì sự tập trung với các dự án kéo dài quá lâu.',
                'tinh_yeu'  =>'Bạn bị thu hút bởi những mối quan hệ có sự giao thoa về mặt trí tuệ và trao đổi ý tưởng. Bạn luôn muốn tìm kiếm một người có thể cùng mình tranh luận và khám phá thế giới.',
            ],
            'INTJ'=>[
                'title'     =>'Nhà chiến lược (The Architect)',
                'tong_quan' =>'Bạn là người có tư duy phân tích sâu sắc và tầm nhìn chiến lược xa rộng. Bạn không thích sự hời hợt, mà luôn muốn xây dựng các hệ thống và kế hoạch rõ ràng để chinh phục mục tiêu.',
                'su_nghiep' =>'Bạn có khả năng làm việc độc lập cực kỳ xuất sắc và thích tự mình giải quyết các vấn đề phức tạp. Bạn rất phù hợp với các lĩnh vực đòi hỏi tư duy phân tích, quy hoạch và chiến lược vĩ mô.',
                'tinh_yeu'  =>'Mặc dù không phải lúc nào bạn cũng bộc lộ cảm xúc dạt dào ra bên ngoài, nhưng sâu thẳm bên trong, bạn vô cùng coi trọng sự chân thành và sự đồng điệu về mặt trí tuệ với đối phương.',
            ],
            'INTP'=>[
                'title'     =>'Nhà tư duy (The Logician)',
                'tong_quan' =>'Bạn luôn mang trong mình sự tò mò và khát khao tìm hiểu cách thế giới này vận hành. Bạn thích phân tích cặn kẽ các vấn đề và đắm chìm trong việc khám phá những giả thuyết mới lạ.',
                'su_nghiep' =>'Bạn đặc biệt phù hợp với các lĩnh vực nghiên cứu, khoa học, công nghệ hoặc phân tích dữ liệu. Bạn sẽ phát huy năng lực tốt nhất khi được trao cho một không gian suy nghĩ hoàn toàn độc lập.',
                'tinh_yeu'  =>'Trong tình cảm, bạn là người chân thành, thẳng thắn và không thích sự vòng vo. Bạn mong muốn xây dựng những mối quan hệ đơn giản nhưng lại chứa đựng sự thấu hiểu sâu sắc.',
            ],
            'INFJ'=>[
                'title'     =>'Người cố vấn (The Advocate)',
                'tong_quan' =>'Bạn mang trong mình một thế giới nội tâm vô cùng sâu sắc và sự thấu cảm tuyệt vời. Bạn thường xuyên suy ngẫm về ý nghĩa thực sự của cuộc sống và luôn đề cao những giá trị nhân văn.',
                'su_nghiep' =>'Bạn sẽ tìm thấy tiếng gọi của mình trong các lĩnh vực như giáo dục, tâm lý học, nghệ thuật hoặc các công việc mang lại giá trị thiết thực cho cộng đồng và xã hội.',
                'tinh_yeu'  =>'Bạn không tìm kiếm những mối quan hệ hời hợt. Đối với bạn, tình yêu phải là sự kết nối sâu sắc, chân thành và thấu hiểu nhau từ tận trong tâm hồn.',
            ],
            'INFP'=>[
                'title'     =>'Người hòa giải (The Mediator)',
                'tong_quan' =>'Bạn là một tâm hồn nhạy cảm, giàu sức sáng tạo và sở hữu một hệ giá trị cá nhân vô cùng kiên định. Bạn sống tử tế, hướng thiện và luôn mong muốn mang lại sự hòa bình.',
                'su_nghiep' =>'Bạn thường phát huy xuất sắc khả năng của mình trong các lĩnh vực nghệ thuật, viết lách, thiết kế hoặc những công việc cho phép bạn thể hiện sự nhân văn và lý tưởng sống.',
                'tinh_yeu'  =>'Trong chuyện tình cảm, bạn yêu một cách chân thành và sâu đậm. Bạn luôn khao khát một mối quan hệ mà ở đó, cả hai có thể thấu hiểu trọn vẹn những rung động tinh tế nhất của nhau.',
            ],
            'ENFJ'=>[
                'title'     =>'Người dẫn dắt (The Protagonist)',
                'tong_quan' =>'Bạn mang đến sự ấm áp, dễ dàng thấu hiểu và kết nối với mọi người. Đặc biệt, bạn có một khả năng thiên bẩm trong việc truyền cảm hứng và khơi dậy tiềm năng của người khác.',
                'su_nghiep' =>'Bạn sẽ là một ngôi sao sáng trong các lĩnh vực yêu cầu sự tương tác con người như giáo dục, truyền thông, nhân sự hoặc các vai trò lãnh đạo, dẫn dắt đội nhóm.',
                'tinh_yeu'  =>'Khi yêu, bạn là người vô cùng tinh tế, biết quan tâm và luôn sẵn sàng hy sinh, vun vén để chăm sóc cho đối phương cũng như bảo vệ mối quan hệ của cả hai.',
            ],
            'ISTJ'=>[
                'title'     =>'Người trách nhiệm (The Logistician)',
                'tong_quan' =>'Bạn là đại diện cho sự thực tế, đáng tin cậy và đề cao tính kỷ luật. Một khi đã cam kết, bạn luôn thể hiện tinh thần trách nhiệm cao độ để hoàn thành công việc đến cùng.',
                'su_nghiep' =>'Bạn làm việc vô cùng cẩn thận, có tổ chức và nguyên tắc. Bạn đặc biệt phù hợp với các môi trường chuyên nghiệp, yêu cầu sự quy chuẩn và độ chính xác cao.',
                'tinh_yeu'  =>'Bạn không quá màu mè trong tình yêu. Thay vào đó, bạn thể hiện tình cảm của mình thông qua việc tạo ra sự ổn định, an toàn và những cam kết gắn bó lâu dài.',
            ],
            'ISFJ'=>[
                'title'     =>'Người bảo vệ (The Defender)',
                'tong_quan' =>'Bạn vô cùng chu đáo, tận tâm và luôn hướng sự quan tâm sâu sắc đến những người xung quanh. Bạn thích âm thầm chăm sóc và bảo vệ những người mà bạn yêu thương.',
                'su_nghiep' =>'Bạn sẽ cảm thấy hạnh phúc và phát huy tốt nhất trong các lĩnh vực mang tính hỗ trợ cộng đồng như y tế, chăm sóc sức khỏe, giáo dục hoặc dịch vụ khách hàng.',
                'tinh_yeu'  =>'Trong tình cảm, bạn là một người bạn đời tuyệt vời: vô cùng chung thủy, biết lắng nghe và luôn tinh tế ghi nhớ những nhu cầu, sở thích nhỏ nhất của đối phương.',
            ],
            'ESTJ'=>[
                'title'     =>'Người điều hành (The Executive)',
                'tong_quan' =>'Bạn thích mọi thứ được sắp xếp và tổ chức một cách rõ ràng, thực tế. Bạn là người làm việc dựa trên nguyên tắc, tôn trọng trật tự và luôn hướng tới sự hiệu quả tối đa.',
                'su_nghiep' =>'Với khả năng quản lý xuất sắc, bạn rất phù hợp với các vị trí điều hành, giám sát hoặc tổ chức dự án, nơi bạn có thể thiết lập kỷ luật và thúc đẩy tiến độ.',
                'tinh_yeu'  =>'Bạn đánh giá cao sự trung thực và tính trách nhiệm trong mối quan hệ. Tình yêu đối với bạn là sự đồng hành bền vững, cùng nhau xây dựng một cuộc sống ổn định và nề nếp.',
            ],
            'ESFJ'=>[
                'title'     =>'Người kết nối (The Consul)',
                'tong_quan' =>'Bạn là người hòa đồng, thân thiện và luôn mang lại cảm giác dễ gần. Bạn đặc biệt quan tâm đến cảm xúc của đám đông và luôn nỗ lực duy trì sự hòa hợp trong cộng đồng.',
                'su_nghiep' =>'Bạn sẽ phát huy tối đa thế mạnh trong các công việc đòi hỏi kỹ năng giao tiếp tốt, chăm sóc con người hoặc tổ chức sự kiện, nơi sự duyên dáng của bạn được tỏa sáng.',
                'tinh_yeu'  =>'Trong tình yêu, bạn vô cùng ân cần và chu đáo. Bạn luôn cố gắng thấu hiểu, chiều chuộng đối phương và nỗ lực hết mình để tạo ra một sự gắn kết bền chặt, ấm áp.',
            ],
            'ISFP'=>[
                'title'     =>'Nghệ sĩ (The Adventurer)',
                'tong_quan' =>'Bạn mang một tâm hồn nhẹ nhàng, tinh tế và có sự rung cảm sâu sắc với cái đẹp. Bạn thích sống trọn vẹn trong hiện tại và tận hưởng cuộc sống theo nhịp độ của riêng mình.',
                'su_nghiep' =>'Bạn rất phù hợp với các lĩnh vực sáng tạo tự do như nghệ thuật, thiết kế, thời trang... nơi bạn không bị gò bó và có thể thoải mái thể hiện cá tính riêng.',
                'tinh_yeu'  =>'Bạn ít khi dùng những lời hoa mỹ sáo rỗng. Thay vào đó, bạn thể hiện tình cảm chân thành của mình bằng những hành động chăm sóc tinh tế và những khoảnh khắc lãng mạn bình dị.',
            ],
            'ISTP'=>[
                'title'     =>'Người thực hành (The Virtuoso)',
                'tong_quan' =>'Bạn là người thực tế, điềm tĩnh và có óc quan sát nhạy bén. Bạn đặc biệt thích tự tay tìm hiểu, tháo lắp và khám phá cách thức hoạt động của mọi thứ xung quanh.',
                'su_nghiep' =>'Với sự khéo léo và khả năng xử lý tình huống linh hoạt, bạn rất phù hợp với các lĩnh vực kỹ thuật, công nghệ, cơ khí hoặc những công việc đòi hỏi kỹ năng thực hành cao.',
                'tinh_yeu'  =>'Bạn không thích sự ràng buộc ngột ngạt hay những "drama" tình cảm. Bạn ưu tiên những mối quan hệ tự nhiên, thoải mái, nơi cả hai có thể cùng nhau chia sẻ những trải nghiệm thực tế.',
            ],
            'ESTP'=>[
                'title'     =>'Người hành động (The Entrepreneur)',
                'tong_quan' =>'Bạn là một người năng động, táo bạo và luôn bị thu hút bởi những trải nghiệm mới mẻ, kịch tính. Bạn nhạy bén với thời cơ và thích giải quyết vấn đề ngay tại trận.',
                'su_nghiep' =>'Bạn sẽ tỏa sáng rực rỡ trong các môi trường nhịp độ nhanh, áp lực cao và cần sự phản xạ tốt như kinh doanh, bán hàng, đầu tư rủi ro hoặc thể thao.',
                'tinh_yeu'  =>'Chuyện tình cảm đối với bạn là một hành trình thú vị. Bạn hài hước, quyến rũ và luôn biết cách mang lại niềm vui, tiếng cười cũng như những trải nghiệm bất ngờ cho đối phương.',
            ],
            'ESFP'=>[
                'title'     =>'Người truyền năng lượng (The Entertainer)',
                'tong_quan' =>'Bạn luôn tỏa ra sự vui vẻ, nhiệt huyết và cởi mở. Bạn giống như một "nam châm" thu hút sự chú ý, luôn biết cách khuấy động không khí và mang lại niềm vui cho mọi người.',
                'su_nghiep' =>'Bạn cực kỳ phù hợp với các lĩnh vực liên quan đến biểu diễn nghệ thuật, truyền thông, giải trí hoặc dịch vụ, nơi bạn có thể sử dụng sức hút tự nhiên của mình để tỏa sáng.',
                'tinh_yeu'  =>'Bạn ngọt ngào, ấm áp và luôn muốn biến mỗi ngày bên nhau thành những khoảnh khắc đáng nhớ. Bạn thích được chia sẻ niềm vui và tận hưởng trọn vẹn từng phút giây hạnh phúc bên đối phương.',
            ],
        ];

        return $profiles;
    }
}
