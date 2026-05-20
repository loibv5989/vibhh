<?php

if (!defined('ABSPATH')) exit;

class MBTI_Data {

    public static function getQuestions(): array {
        $questions = [
                ['id'=>'q1', 'axis'=>'EI','dir'=> 1,'text'=>'Sau một buổi tiệc đông người, bạn thường thấy hào hứng hơn là kiệt sức.'],
                ['id'=>'q2', 'axis'=>'EI','dir'=>-1,'text'=>'Bạn chọn một buổi tối yên tĩnh ở nhà thay vì đến một sự kiện có nhiều người lạ.'],
                ['id'=>'q3', 'axis'=>'EI','dir'=> 1,'text'=>'Khi đứng trong một nhóm toàn người chưa quen, bạn là người cất lời làm quen trước.'],
                ['id'=>'q4', 'axis'=>'EI','dir'=>-1,'text'=>'Sau vài giờ giao tiếp liên tục, bạn cảm thấy cần rút lui để nạp lại năng lượng một mình.'],
                ['id'=>'q5', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn thấy việc trở thành tâm điểm chú ý trong một cuộc trò chuyện nhóm là điều khá tự nhiên.'],
                ['id'=>'q6', 'axis'=>'EI','dir'=>-1,'text'=>'Bạn chỉ thực sự thoải mái chia sẻ suy nghĩ riêng tư với một vài người bạn thân thiết.'],
                ['id'=>'q7', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn sẵn sàng nhận lời mời tham gia một hoạt động xã hội dù lịch cá nhân đã khá kín.'],
                ['id'=>'q8', 'axis'=>'EI','dir'=>-1,'text'=>'Trong một cuộc họp, bạn thường nghe hết ý kiến của mọi người rồi mới phát biểu.'],
                ['id'=>'q9', 'axis'=>'EI','dir'=> 1,'text'=>'Bạn cảm thấy năng lượng tăng lên khi được làm việc trong một không gian mở, nhiều người qua lại.'],
                ['id'=>'q10','axis'=>'EI','dir'=>-1,'text'=>'Bạn thích làm việc trong phòng riêng hoặc góc khuất hơn là ngồi giữa văn phòng đông đúc.'],
                ['id'=>'q11','axis'=>'EI','dir'=> 1,'text'=>'Khi gặp một vấn đề khó, phản xạ đầu tiên của bạn là gọi điện trao đổi với ai đó ngay.'],
                ['id'=>'q12','axis'=>'EI','dir'=>-1,'text'=>'Trước khi hỏi ý kiến người khác, bạn thường tự mình suy nghĩ thấu đáo trước đã.'],

                ['id'=>'q13','axis'=>'SN','dir'=> 1,'text'=>'Khi được giới thiệu một ý tưởng mới, câu đầu tiên bạn hỏi thường là về số liệu hoặc cách vận hành cụ thể.'],
                ['id'=>'q14','axis'=>'SN','dir'=>-1,'text'=>'Bạn thấy phấn khích hơn khi bàn về viễn cảnh 5 năm tới hơn là liệt kê những việc cần làm ngày mai.'],
                ['id'=>'q15','axis'=>'SN','dir'=> 1,'text'=>'Trong công việc, bạn tin vào quy trình đã được kiểm chứng hơn là một cách làm mới chưa ai thử.'],
                ['id'=>'q16','axis'=>'SN','dir'=>-1,'text'=>'Một ý tưởng thú vị khiến bạn mất ngủ, dù nó chưa có bằng chứng thực tế nào xác nhận.'],
                ['id'=>'q17','axis'=>'SN','dir'=> 1,'text'=>'Bạn thích một bản hướng dẫn có từng bước rõ ràng hơn là một lời gợi ý chung chung.'],
                ['id'=>'q18','axis'=>'SN','dir'=>-1,'text'=>'Khi đọc một bài báo, bạn thường tự hỏi về ẩn ý phía sau hơn là chỉ tiếp nhận thông tin trên mặt chữ.'],
                ['id'=>'q19','axis'=>'SN','dir'=> 1,'text'=>'Bạn đánh giá cao đồng nghiệp đưa ra dữ liệu cụ thể hơn là người kể một câu chuyện truyền cảm hứng.'],
                ['id'=>'q20','axis'=>'SN','dir'=>-1,'text'=>'Bạn dễ dàng kết nối những sự kiện tưởng như không liên quan thành một bức tranh có ý nghĩa.'],
                ['id'=>'q21','axis'=>'SN','dir'=> 1,'text'=>'Khi học một kỹ năng mới, bạn muốn thực hành ngay thay vì đọc lý thuyết nền tảng trước.'],
                ['id'=>'q22','axis'=>'SN','dir'=>-1,'text'=>'Bạn thường bị cuốn vào những câu hỏi kiểu như "điều gì sẽ xảy ra nếu..." trong lúc đang làm việc khác.'],
                ['id'=>'q23','axis'=>'SN','dir'=> 1,'text'=>'Bạn chọn một công cụ quen thuộc, đáng tin cậy thay vì thử nghiệm một công cụ mới có nhiều tính năng hứa hẹn.'],
                ['id'=>'q24','axis'=>'SN','dir'=>-1,'text'=>'Bạn cảm thấy bị bó buộc nếu phải làm theo một quy trình quá chi tiết và lặp đi lặp lại.'],

                ['id'=>'q25','axis'=>'TF','dir'=> 1,'text'=>'Khi một đồng nghiệp trình bày kế hoạch có lỗ hổng, bạn chỉ ra lỗi đó ngay lập tức.'],
                ['id'=>'q26','axis'=>'TF','dir'=>-1,'text'=>'Khi một đồng nghiệp trình bày kế hoạch có lỗ hổng, bạn chọn cách góp ý nhẹ nhàng để tránh làm họ nản lòng.'],
                ['id'=>'q27','axis'=>'TF','dir'=> 1,'text'=>'Bạn thấy việc giữ đúng nguyên tắc quan trọng hơn là tạo ra một ngoại lệ để ai đó không bị tổn thương.'],
                ['id'=>'q28','axis'=>'TF','dir'=>-1,'text'=>'Bạn có thể linh hoạt bỏ qua quy tắc nếu điều đó giúp giữ được bầu không khí hòa hợp trong nhóm.'],
                ['id'=>'q29','axis'=>'TF','dir'=> 1,'text'=>'Trong một cuộc tranh luận, bạn thấy bị thuyết phục bởi lập luận sắc bén hơn là câu chuyện cảm động.'],
                ['id'=>'q30','axis'=>'TF','dir'=>-1,'text'=>'Bạn thấy khó chịu khi ai đó nói quá thẳng thắn khiến người khác bị tổn thương trước mặt mọi người.'],
                ['id'=>'q31','axis'=>'TF','dir'=> 1,'text'=>'Khi phải chọn giữa hai ứng viên, bạn nghiêng về người có hồ sơ năng lực vượt trội, dù người kia dễ mến hơn.'],
                ['id'=>'q32','axis'=>'TF','dir'=>-1,'text'=>'Bạn thường mất nhiều thời gian cân nhắc một quyết định vì lo nó có thể ảnh hưởng tiêu cực đến người khác.'],
                ['id'=>'q33','axis'=>'TF','dir'=> 1,'text'=>'Bạn đánh giá một ý tưởng dựa trên tính hiệu quả và khả thi trước khi xét đến việc nó có được lòng mọi người hay không.'],
                ['id'=>'q34','axis'=>'TF','dir'=>-1,'text'=>'Khi nghe kể về một hoàn cảnh khó khăn, bạn dễ hình dung mình trong hoàn cảnh đó và thấy xúc động.'],
                ['id'=>'q35','axis'=>'TF','dir'=> 1,'text'=>'Bạn thấy việc nói thẳng giúp đôi bên hiểu nhau nhanh hơn, dù lời nói có thể gây khó chịu ban đầu.'],
                ['id'=>'q36','axis'=>'TF','dir'=>-1,'text'=>'Bạn sẵn sàng lùi một bước trong tranh luận để bảo vệ mối quan hệ, ngay cả khi bạn biết mình đúng.'],

                ['id'=>'q37','axis'=>'JP','dir'=> 1,'text'=>'Trước một chuyến đi chơi, bạn thích có lịch trình từng ngày rõ ràng hơn là để mọi thứ tùy hứng.'],
                ['id'=>'q38','axis'=>'JP','dir'=>-1,'text'=>'Bạn thấy một ngày cuối tuần không có kế hoạch gì mới thực sự là một ngày nghỉ trọn vẹn.'],
                ['id'=>'q39','axis'=>'JP','dir'=> 1,'text'=>'Bạn thường hoàn thành bài tập hoặc dự án trước hạn để đầu óc được nhẹ nhõm.'],
                ['id'=>'q40','axis'=>'JP','dir'=>-1,'text'=>'Bạn tạo ra sản phẩm tốt nhất khi áp lực thời gian dồn đến những giờ cuối cùng.'],
                ['id'=>'q41','axis'=>'JP','dir'=> 1,'text'=>'Bạn cảm thấy bực bội khi ai đó hủy kế hoạch đã định vào phút chót.'],
                ['id'=>'q42','axis'=>'JP','dir'=>-1,'text'=>'Bạn dễ dàng thay đổi kế hoạch cuối tuần nếu có một lựa chọn thú vị hơn bất ngờ xuất hiện.'],
                ['id'=>'q43','axis'=>'JP','dir'=> 1,'text'=>'Bạn thấy yên tâm hơn khi mọi thứ trong phòng làm việc được sắp xếp đúng chỗ quy định.'],
                ['id'=>'q44','axis'=>'JP','dir'=>-1,'text'=>'Bạn thích để ngỏ nhiều lựa chọn cùng lúc thay vì chốt một hướng duy nhất từ sớm.'],
                ['id'=>'q45','axis'=>'JP','dir'=> 1,'text'=>'Bạn thấy khó chịu khi làm việc trong môi trường không có quy trình rõ ràng.'],
                ['id'=>'q46','axis'=>'JP','dir'=>-1,'text'=>'Bạn cảm thấy những quy tắc cứng nhắc kìm hãm sự sáng tạo và thích ứng của mình.'],
                ['id'=>'q47','axis'=>'JP','dir'=> 1,'text'=>'Bạn thường lên danh sách việc cần làm và cảm thấy hài lòng khi được tick từng mục đã xong.'],
                ['id'=>'q48','axis'=>'JP','dir'=>-1,'text'=>'Bạn thường bắt đầu ngày làm việc mà không cần một danh sách cố định, việc gì đến thì xử lý trước.'],
            ];
        $shuffled = $questions;
        shuffle($shuffled);
        return $shuffled;
    }

    public static function getProfiles(): array {
        static $profiles = null;
        if ($profiles !== null) return $profiles;

        $profiles = [
            'ENFP'=>[
                'title'     =>'Người truyền cảm hứng',
                'short'     =>'Nhiệt huyết, sáng tạo và luôn nhìn thấy khả năng ở con người và ý tưởng.',
                'tong_quan' =>'Bạn bị thu hút bởi những ý tưởng mới, những con người thú vị và những trải nghiệm chưa từng có. Bạn nhìn thế giới qua lăng kính của những khả năng và kết nối. Năng lượng của bạn đến từ việc khám phá và chia sẻ những khám phá đó với người khác.',
                'diem_manh' =>['Nhanh chóng kết nối với người lạ','Nhìn thấy tiềm năng ở nơi người khác bỏ qua','Thích nghi tốt với thay đổi','Truyền được cảm hứng tự nhiên'],
                'diem_yeu'  =>['Dễ mất hứng thú với công việc lặp lại','Khó từ chối, dễ ôm đồm quá nhiều thứ','Có xu hướng trì hoãn những việc thiếu cảm hứng','Đôi khi quá lạc quan mà bỏ qua rủi ro thực tế'],
                'su_nghiep' =>'Bạn hợp với môi trường đề cao sáng tạo và sự linh hoạt. Những công việc lặp đi lặp lại hoặc quá cứng nhắc sẽ nhanh chóng rút cạn năng lượng của bạn. Bạn làm tốt nhất khi được tự do thử nghiệm ý tưởng mới và làm việc cùng những người cởi mở.',
                'tinh_yeu'  =>'Bạn tìm kiếm sự đồng điệu về giá trị sống và một người sẵn sàng cùng bạn khám phá thế giới. Bạn hào phóng với tình cảm của mình, nhưng đôi khi khó giữ được nhịp ổn định lâu dài nếu mối quan hệ trở nên quá quen thuộc.',
            ],
            'ENTJ'=>[
                'title'     =>'Người lãnh đạo',
                'short'     =>'Quyết đoán, có tầm nhìn xa và khả năng tổ chức bẩm sinh.',
                'tong_quan' =>'Bạn nhìn thế giới như một hệ thống có thể được tối ưu hóa. Bạn không né tránh quyết định khó và luôn có xu hướng tiến về phía trước. Người khác thường nhìn vào bạn để tìm định hướng khi mọi thứ trở nên hỗn loạn.',
                'diem_manh' =>['Tầm nhìn chiến lược rõ ràng','Khả năng ra quyết định nhanh và dứt khoát','Giỏi tổ chức con người và nguồn lực','Không dễ bị dao động bởi ý kiến trái chiều'],
                'diem_yeu'  =>['Có thể tỏ ra áp đặt và thiếu kiên nhẫn với người chậm hơn','Đôi khi bỏ qua cảm xúc của người khác khi theo đuổi mục tiêu','Khó chấp nhận thất bại hoặc bị chỉ trích','Dễ rơi vào trạng thái kiệt sức vì ôm quá nhiều trách nhiệm'],
                'su_nghiep' =>'Bạn tỏa sáng trong các vai trò lãnh đạo, quản lý dự án hoặc điều hành tổ chức. Bạn cần một môi trường đủ lớn để thử thách tầm nhìn của mình. Những vị trí không có quyền quyết định sẽ khiến bạn cảm thấy bị trói buộc.',
                'tinh_yeu'  =>'Bạn không giỏi thể hiện cảm xúc bằng lời nói, nhưng lại thể hiện sự cam kết qua hành động và trách nhiệm với mối quan hệ. Bạn cần một người đủ mạnh mẽ để không bị lấn át, nhưng cũng đủ tinh tế để hiểu những điều bạn không nói ra.',
            ],
            'ENTP'=>[
                'title'     =>'Người tranh luận',
                'short'     =>'Nhanh trí, thích phản biện và không ngừng tìm kiếm ý tưởng mới.',
                'tong_quan' =>'Bạn sống trong thế giới của những ý tưởng và khả năng. Bạn thích tháo rời một vấn đề ra thành từng mảnh, xoay nó ở nhiều góc độ, và đặt câu hỏi về mọi giả định. Với bạn, một cuộc tranh luận không phải là xung đột — đó là cách bạn học hỏi.',
                'diem_manh' =>['Tư duy linh hoạt và nhạy bén trước vấn đề mới','Không ngại thách thức hiện trạng','Giỏi nhìn ra kết nối giữa những lĩnh vực không liên quan','Có năng lượng sáng tạo dồi dào khi khởi động dự án'],
                'diem_yeu'  =>['Dễ chán khi dự án bước vào giai đoạn triển khai chi tiết','Có thể tranh luận chỉ vì thích tranh luận, không phải để tìm giải pháp','Đôi khi bỏ qua cảm xúc của người khác khi tập trung vào logic','Khó duy trì kỷ luật với những thói quen dài hạn'],
                'su_nghiep' =>'Bạn hợp với môi trường khởi nghiệp, tư vấn chiến lược, hoặc bất kỳ nơi nào cần giải quyết vấn đề mới mẻ liên tục. Công việc lặp lại hoặc quá nhiều thủ tục hành chính là cơn ác mộng với bạn.',
                'tinh_yeu'  =>'Bạn bị hấp dẫn bởi những người có thể theo kịp nhịp suy nghĩ của mình. Một cuộc trò chuyện kích thích trí tuệ quan trọng với bạn hơn những cử chỉ lãng mạn sáo rỗng. Thách thức của bạn là giữ được sự ổn định khi mối quan hệ bước vào giai đoạn bình lặng.',
            ],
            'INTJ'=>[
                'title'     =>'Nhà chiến lược',
                'short'     =>'Độc lập, phân tích sâu và có tầm nhìn dài hạn rõ ràng.',
                'tong_quan' =>'Bạn không thích sự mơ hồ. Bạn muốn hiểu mọi thứ vận hành như thế nào và làm sao để cải thiện nó. Bạn thoải mái với việc ở một mình trong thời gian dài để suy nghĩ, lập kế hoạch và xây dựng hệ thống. Đám đông ồn ào không phải là nơi bạn tìm thấy năng lượng.',
                'diem_manh' =>['Tư duy phân tích và chiến lược sắc bén','Làm việc độc lập hiệu quả cao','Khả năng lập kế hoạch dài hạn và dự đoán rủi ro','Kiên định với mục tiêu đã chọn'],
                'diem_yeu'  =>['Có thể tỏ ra lạnh lùng và khó tiếp cận','Đôi khi quá cầu toàn, trì hoãn vì chưa đạt chuẩn','Khó chia sẻ cảm xúc và dễ bị hiểu lầm là thiếu quan tâm','Thiếu kiên nhẫn với những người làm việc kém hiệu quả'],
                'su_nghiep' =>'Bạn hợp với các lĩnh vực đòi hỏi tư duy hệ thống như chiến lược, nghiên cứu, công nghệ, hoặc quy hoạch. Bạn làm việc tốt nhất khi có không gian riêng và quyền tự chủ cao. Môi trường quản lý vi mô sẽ khiến bạn nhanh chóng rời đi.',
                'tinh_yeu'  =>'Bạn không dễ mở lòng, nhưng khi đã cam kết thì cực kỳ chung thủy. Bạn cần một người hiểu rằng sự im lặng của bạn không phải là thiếu quan tâm. Bạn thể hiện tình cảm qua việc giải quyết vấn đề cho người mình yêu, không phải qua lời nói.',
            ],
            'INTP'=>[
                'title'     =>'Nhà tư duy',
                'short'     =>'Tò mò, thích phân tích và luôn đào sâu vào bản chất vấn đề.',
                'tong_quan' =>'Bạn dành nhiều thời gian trong đầu mình — phân tích, đặt câu hỏi, và xây dựng những mô hình để hiểu thế giới. Bạn không quan tâm nhiều đến quy tắc xã hội nếu chúng không hợp lý. Với bạn, một ý tưởng hay có giá trị hơn một cuộc trò chuyện xã giao.',
                'diem_manh' =>['Khả năng phân tích logic và nhìn thấy lỗ hổng trong lập luận','Sáng tạo trong việc xây dựng lý thuyết và giải pháp','Không bị áp lực đám đông chi phối','Trung thực về mặt trí tuệ'],
                'diem_yeu'  =>['Dễ bị cuốn vào phân tích đến mức không hành động','Khó duy trì các mối quan hệ xã hội thường xuyên','Có thể bỏ qua cảm xúc của chính mình và người khác','Thiếu kiên nhẫn với những quy trình không hiệu quả'],
                'su_nghiep' =>'Bạn phù hợp với nghiên cứu, lập trình, phân tích dữ liệu, hoặc bất kỳ lĩnh vực nào cho phép bạn tự do khám phá ý tưởng. Môi trường có quá nhiều họp hành và thủ tục sẽ bào mòn năng lượng của bạn.',
                'tinh_yeu'  =>'Bạn không giỏi đọc tín hiệu xã hội và đôi khi tỏ ra vụng về trong chuyện tình cảm. Nhưng bạn chân thành và không thích trò chơi tâm lý. Bạn cần một người tôn trọng không gian riêng của bạn và không đòi hỏi sự lãng mạn theo khuôn mẫu.',
            ],
            'INFJ'=>[
                'title'     =>'Người cố vấn',
                'short'     =>'Sâu sắc, thấu cảm và luôn tìm kiếm ý nghĩa trong mọi điều.',
                'tong_quan' =>'Bạn sống với một thế giới nội tâm phong phú mà ít người thấy được. Bạn cảm nhận cảm xúc của người khác gần như là của chính mình. Bạn không hứng thú với những cuộc trò chuyện hời hợt — bạn muốn hiểu điều gì thực sự đang diễn ra bên dưới bề mặt.',
                'diem_manh' =>['Thấu hiểu người khác ở mức độ sâu sắc','Có tầm nhìn dài hạn và khả năng kết nối các ý tưởng phức tạp','Kiên định với giá trị cá nhân','Giỏi giúp người khác phát triển tiềm năng'],
                'diem_yeu'  =>['Dễ kiệt sức vì hấp thụ quá nhiều cảm xúc từ người khác','Có xu hướng cầu toàn và tự phê bình khắc nghiệt','Khó mở lòng, ít người thực sự hiểu được họ','Dễ bị tổn thương bởi những xung đột hoặc chỉ trích'],
                'su_nghiep' =>'Bạn tìm thấy ý nghĩa trong các lĩnh vực như tư vấn tâm lý, giáo dục, nghệ thuật, hoặc hoạt động xã hội. Bạn cần một công việc phù hợp với giá trị cốt lõi của mình — nếu không, bạn sẽ cảm thấy trống rỗng dù bề ngoài vẫn thành công.',
                'tinh_yeu'  =>'Bạn khao khát một kết nối sâu sắc, nơi cả hai thực sự nhìn thấy và thấu hiểu nhau. Bạn không quan tâm đến những mối quan hệ qua đường. Nhưng bạn cũng dễ bị tổn thương nếu đối phương không đáp lại được chiều sâu cảm xúc mà bạn đặt vào.',
            ],
            'INFP'=>[
                'title'     =>'Người hòa giải',
                'short'     =>'Nhạy cảm, sáng tạo và sống theo hệ giá trị cá nhân mạnh mẽ.',
                'tong_quan' =>'Bạn nhìn thế giới qua lăng kính của những giá trị và ý nghĩa. Bạn quan tâm sâu sắc đến con người và luôn muốn làm điều gì đó có ý nghĩa. Vẻ ngoài điềm tĩnh của bạn đôi khi che giấu một ngọn lửa nội tâm mạnh mẽ về những điều bạn tin tưởng.',
                'diem_manh' =>['Giàu lòng trắc ẩn và thấu hiểu','Sáng tạo trong việc thể hiện ý tưởng và cảm xúc','Trung thành với giá trị cá nhân','Cởi mở và không phán xét người khác'],
                'diem_yeu'  =>['Dễ bị tổn thương bởi chỉ trích và xung đột','Có xu hướng lý tưởng hóa, dễ thất vọng khi thực tế không như mong đợi','Khó đưa ra quyết định vì sợ làm tổn thương người khác','Có thể chìm đắm trong cảm xúc tiêu cực kéo dài'],
                'su_nghiep' =>'Bạn hợp với các lĩnh vực sáng tạo như viết lách, thiết kế, nghệ thuật, hoặc các công việc mang tính nhân văn như tư vấn, giáo dục, phi lợi nhuận. Bạn cần thấy công việc của mình có ý nghĩa — tiền bạc không đủ để giữ bạn ở lại.',
                'tinh_yeu'  =>'Bạn yêu sâu đậm và lãng mạn theo cách chân thành nhất. Bạn đặt nhiều kỳ vọng vào mối quan hệ và đôi khi thất vọng khi đối phương không đáp ứng được lý tưởng của bạn. Bạn cần một người nhìn thấy được thế giới nội tâm của bạn và trân trọng nó.',
            ],
            'ENFJ'=>[
                'title'     =>'Người dẫn dắt',
                'short'     =>'Ấm áp, truyền cảm hứng và có khả năng khơi dậy điều tốt nhất ở người khác.',
                'tong_quan' =>'Bạn gần như có một "ăng-ten" bẩm sinh để đọc được cảm xúc và nhu cầu của người khác. Bạn không chỉ hiểu họ — bạn muốn giúp họ trở thành phiên bản tốt nhất của chính mình. Điều này khiến bạn trở thành một nhà lãnh đạo tự nhiên, dù bạn không cố ý.',
                'diem_manh' =>['Khả năng thấu hiểu và kết nối với người khác xuất sắc','Truyền cảm hứng và tạo động lực cho tập thể','Tận tâm và có trách nhiệm cao với những người mình dẫn dắt','Giỏi hòa giải mâu thuẫn và xây dựng sự đồng thuận'],
                'diem_yeu'  =>['Dễ ôm đồm vấn đề của người khác đến mức kiệt sức','Có xu hướng bỏ qua nhu cầu của bản thân','Khó chấp nhận khi không thể giúp được ai đó','Đôi khi quá tập trung vào sự hòa hợp mà tránh né quyết định khó'],
                'su_nghiep' =>'Bạn tỏa sáng trong giáo dục, nhân sự, truyền thông, hoặc bất kỳ vai trò nào cho phép bạn làm việc trực tiếp với con người. Bạn cần một môi trường coi trọng sự phát triển con người, không chỉ là con số.',
                'tinh_yeu'  =>'Bạn dành nhiều tâm sức cho mối quan hệ và mong đợi sự cam kết tương tự từ đối phương. Bạn tinh tế trong việc chăm sóc người mình yêu, nhưng đôi khi bạn quên mất rằng mình cũng cần được chăm sóc.',
            ],
            'ISTJ'=>[
                'title'     =>'Người trách nhiệm',
                'short'     =>'Kỷ luật, đáng tin cậy và làm việc dựa trên nguyên tắc rõ ràng.',
                'tong_quan' =>'Bạn coi trọng sự thật, trật tự và độ tin cậy. Khi bạn đã cam kết làm điều gì đó, bạn sẽ làm đến cùng. Bạn không thích sự bất ngờ hay thay đổi vào phút chót. Người khác tìm đến bạn khi họ cần một người giữ lời.',
                'diem_manh' =>['Đáng tin cậy tuyệt đối, luôn làm đúng cam kết','Làm việc có hệ thống và tổ chức cao','Kiên nhẫn và tỉ mỉ với chi tiết','Ra quyết định dựa trên sự kiện và logic'],
                'diem_yeu'  =>['Cứng nhắc, khó thích nghi với thay đổi đột ngột','Có thể bỏ qua bức tranh lớn vì quá tập trung vào chi tiết','Khó thể hiện cảm xúc và đôi khi bị coi là lạnh lùng','Khó chấp nhận cách làm khác biệt với quy trình chuẩn'],
                'su_nghiep' =>'Bạn hợp với các lĩnh vực đòi hỏi độ chính xác cao như kế toán, luật, hành chính, hoặc quản lý vận hành. Bạn làm việc tốt nhất trong môi trường có quy trình rõ ràng và kỳ vọng được xác định cụ thể.',
                'tinh_yeu'  =>'Bạn không phải mẫu người lãng mạn kiểu phim ảnh. Bạn thể hiện tình yêu qua sự ổn định, trách nhiệm và những hành động thiết thực hàng ngày. Đối phương có thể yên tâm rằng bạn sẽ luôn ở đó khi họ cần.',
            ],
            'ISFJ'=>[
                'title'     =>'Người bảo vệ',
                'short'     =>'Chu đáo, tận tâm và luôn âm thầm chăm lo cho người khác.',
                'tong_quan' =>'Bạn có một khả năng đặc biệt trong việc nhận ra khi ai đó cần giúp đỡ — thường là trước khi chính họ nhận ra. Bạn không tìm kiếm sự công nhận cho những gì mình làm. Với bạn, việc chăm sóc người khác là một lẽ tự nhiên.',
                'diem_manh' =>['Tận tâm và chu đáo trong từng việc nhỏ','Ghi nhớ chi tiết về người khác một cách đáng kinh ngạc','Kiên nhẫn và sẵn sàng hỗ trợ','Trung thành và đáng tin cậy trong mọi mối quan hệ'],
                'diem_yeu'  =>['Có xu hướng gánh vác quá nhiều mà không lên tiếng','Khó nói "không" khi người khác nhờ vả','Dễ bị tổn thương khi nỗ lực của mình không được ghi nhận','Ngại thay đổi và né tránh xung đột'],
                'su_nghiep' =>'Bạn phù hợp với y tế, chăm sóc sức khỏe, giáo dục, hoặc dịch vụ khách hàng. Bạn cần một nơi mà sự tận tâm của bạn được đánh giá cao, và nơi bạn thấy được tác động trực tiếp của công việc mình làm lên con người.',
                'tinh_yeu'  =>'Bạn là kiểu người sẽ nhớ món ăn yêu thích, ngày kỷ niệm, và những điều nhỏ nhặt mà người khác dễ bỏ qua. Bạn cần một đối phương nhìn thấy và trân trọng những điều thầm lặng bạn làm, thay vì coi đó là điều hiển nhiên.',
            ],
            'ESTJ'=>[
                'title'     =>'Người điều hành',
                'short'     =>'Thực tế, quyết đoán và giỏi biến kế hoạch thành hành động.',
                'tong_quan' =>'Bạn tin vào công việc chăm chỉ, kỷ luật và làm đúng trách nhiệm của mình. Bạn thấy rõ điều gì cần phải được làm và không ngại là người đứng ra đảm nhận. Với bạn, hiệu quả và kết quả đo lường được quan trọng hơn những ý tưởng xa vời.',
                'diem_manh' =>['Khả năng tổ chức và quản lý con người xuất sắc','Làm việc chăm chỉ và kỷ luật cao','Ra quyết định nhanh và dứt khoát','Trung thành với giá trị truyền thống và cam kết'],
                'diem_yeu'  =>['Có thể tỏ ra cứng nhắc và bảo thủ','Khó linh hoạt khi kế hoạch cần thay đổi','Đôi khi quá chú trọng vào quy trình mà bỏ qua yếu tố con người','Thiếu kiên nhẫn với những người thiếu kỷ luật'],
                'su_nghiep' =>'Bạn hợp với các vị trí quản lý, điều hành, giám sát hoặc bất kỳ nơi nào cần thiết lập trật tự. Bạn phát triển tốt trong tổ chức có phân cấp rõ ràng và tiêu chí đánh giá minh bạch.',
                'tinh_yeu'  =>'Bạn coi trọng sự trung thực và ổn định trong tình yêu. Bạn không phải là người hay nói lời ngọt ngào, nhưng bạn xây dựng một nền tảng vững chắc cho mối quan hệ. Bạn cần một người hiểu rằng sự cam kết của bạn thể hiện qua hành động.',
            ],
            'ESFJ'=>[
                'title'     =>'Người kết nối',
                'short'     =>'Hòa đồng, chu đáo và giỏi tạo ra sự gắn kết trong cộng đồng.',
                'tong_quan' =>'Bạn là người mọi người gọi khi cần tổ chức một buổi tụ họp, hoặc khi họ cần một người lắng nghe. Bạn có bản năng tự nhiên về việc làm cho người khác cảm thấy được chào đón. Sự hòa hợp trong nhóm là ưu tiên của bạn.',
                'diem_manh' =>['Khả năng giao tiếp và tạo thiện cảm tự nhiên','Rất chu đáo và nhớ được những điều quan trọng với người khác','Giỏi tổ chức sự kiện và kết nối con người','Làm việc chăm chỉ để đảm bảo mọi người đều được chăm sóc'],
                'diem_yeu'  =>['Quá phụ thuộc vào sự công nhận và chấp thuận của người khác','Khó nói thẳng sự thật vì sợ làm mất lòng','Có xu hướng can thiệp quá sâu vào chuyện của người khác','Dễ bị tổn thương khi nỗ lực kết nối bị từ chối'],
                'su_nghiep' =>'Bạn phù hợp với các lĩnh vực như tổ chức sự kiện, nhân sự, chăm sóc khách hàng, hoặc giáo dục. Bạn cần một môi trường coi trọng kỹ năng con người và nơi bạn có thể thấy được "chất keo" gắn kết tập thể mà mình tạo ra.',
                'tinh_yeu'  =>'Bạn dành rất nhiều công sức để chăm lo cho đối phương và mong đợi sự trân trọng đáp lại. Bạn cần những lời xác nhận bằng lời nói — sự im lặng khiến bạn bất an. Bạn sẽ tỏa sáng khi ở bên người biết đón nhận và đáp trả tình cảm.',
            ],
            'ISFP'=>[
                'title'     =>'Nghệ sĩ',
                'short'     =>'Tinh tế, nhạy cảm với cái đẹp và sống trọn vẹn trong từng khoảnh khắc.',
                'tong_quan' =>'Bạn cảm nhận thế giới qua giác quan và cảm xúc nhiều hơn là qua phân tích. Bạn không cần nhiều lời để hiểu một tác phẩm nghệ thuật, một bản nhạc, hay tâm trạng của người đối diện. Bạn sống nhẹ nhàng và không thích áp đặt.',
                'diem_manh' =>['Nhạy cảm thẩm mỹ và sáng tạo tự nhiên','Sống chân thành, không giả tạo','Linh hoạt và dễ thích nghi với hoàn cảnh','Tôn trọng không gian và tự do của người khác'],
                'diem_yeu'  =>['Khó lập kế hoạch dài hạn','Dễ bị tổn thương bởi chỉ trích','Ngại đối đầu và thường rút lui khi có xung đột','Có thể bị hiểu lầm là thiếu quan tâm vì ít bộc lộ bằng lời'],
                'su_nghiep' =>'Bạn hợp với các lĩnh vực sáng tạo như thiết kế, nghệ thuật, thời trang, âm nhạc, hoặc bất kỳ công việc nào cho phép bạn thể hiện gu thẩm mỹ riêng. Bạn cần tự do, không thích bị quản lý vi mô.',
                'tinh_yeu'  =>'Bạn thể hiện tình yêu qua những cử chỉ tinh tế: một món quà được chọn kỹ, một trải nghiệm được sắp xếp riêng. Bạn không giỏi nói về cảm xúc nhưng lại giỏi tạo ra những khoảnh khắc mà cả hai sẽ nhớ mãi.',
            ],
            'ISTP'=>[
                'title'     =>'Người thực hành',
                'short'     =>'Điềm tĩnh, thực tế và giỏi xử lý khủng hoảng bằng hành động.',
                'tong_quan' =>'Bạn ít nói, nhưng đôi mắt bạn luôn quan sát. Bạn hiểu mọi thứ vận hành như thế nào bằng cách tự tay tháo ra và lắp lại. Trong khủng hoảng, bạn là người giữ bình tĩnh và tìm ra giải pháp thực tế trong khi người khác còn đang hoảng loạn.',
                'diem_manh' =>['Khả năng xử lý khủng hoảng xuất sắc','Thực tế và logic trong giải quyết vấn đề','Khéo léo với công cụ và kỹ thuật','Không bị chi phối bởi cảm xúc hay áp lực xã hội'],
                'diem_yeu'  =>['Khó kết nối cảm xúc và dễ bị coi là lạnh lùng','Dễ chán với những cam kết dài hạn','Không giỏi giao tiếp xã hội và các nghi thức','Có thể bỏ qua cảm xúc của người khác khi tập trung vào giải pháp'],
                'su_nghiep' =>'Bạn hợp với các lĩnh vực kỹ thuật, cơ khí, công nghệ thông tin, hoặc bất kỳ công việc nào cần kỹ năng thực hành và giải quyết vấn đề thực tế. Bạn thích được tự do làm việc theo cách của mình.',
                'tinh_yeu'  =>'Bạn không thích những mối quan hệ quá phức tạp về mặt cảm xúc. Bạn cần một người tôn trọng sự độc lập của bạn và không đòi hỏi bạn phải liên tục thể hiện cảm xúc. Tình yêu với bạn là cùng nhau trải nghiệm cuộc sống, không phải cùng nhau phân tích nó.',
            ],
            'ESTP'=>[
                'title'     =>'Người hành động',
                'short'     =>'Táo bạo, năng động và giỏi nắm bắt thời cơ trong tích tắc.',
                'tong_quan' =>'Bạn sống trong hiện tại một cách trọn vẹn nhất. Khi có cơ hội, bạn nắm lấy. Khi có rủi ro, bạn xử lý tại chỗ. Bạn học bằng cách làm, và bạn tin vào kết quả thực tế hơn là lý thuyết. Cuộc sống với bạn là một chuỗi những trải nghiệm để tận hưởng.',
                'diem_manh' =>['Phản xạ nhanh nhạy trước tình huống bất ngờ','Dũng cảm chấp nhận rủi ro','Giỏi đàm phán và thuyết phục','Lôi cuốn và hài hước trong giao tiếp'],
                'diem_yeu'  =>['Dễ hành động bốc đồng mà không tính toán hậu quả','Khó kiên nhẫn với những dự án dài hạn','Có thể bỏ qua cảm xúc của người khác khi theo đuổi mục tiêu','Không giỏi lập kế hoạch và dễ chán với thói quen'],
                'su_nghiep' =>'Bạn tỏa sáng trong kinh doanh, bán hàng, thể thao, hoặc bất kỳ môi trường nhịp độ nhanh nào cần phản ứng tức thì. Bạn không hợp với công việc bàn giấy lặp đi lặp lại.',
                'tinh_yeu'  =>'Bạn là mẫu người hấp dẫn, vui tính và luôn mang đến bất ngờ. Nhưng bạn cũng dễ cảm thấy bị trói buộc. Bạn cần một người hiểu được nhu cầu tự do của bạn và sẵn sàng cùng bạn phiêu lưu, thay vì cố gắng giữ bạn lại.',
            ],
            'ESFP'=>[
                'title'     =>'Người truyền năng lượng',
                'short'     =>'Vui vẻ, tự nhiên và lan tỏa năng lượng tích cực ra xung quanh.',
                'tong_quan' =>'Bạn bước vào một căn phòng và không khí thay đổi. Bạn có khả năng khiến người khác cười, khiến họ cảm thấy thoải mái, và khiến những khoảnh khắc bình thường trở nên đáng nhớ. Với bạn, cuộc sống là để trải nghiệm và chia sẻ.',
                'diem_manh' =>['Năng lượng tích cực và lôi cuốn tự nhiên','Giỏi thích nghi và ứng biến trong mọi tình huống xã hội','Hào phóng về thời gian và tình cảm','Sống trọn vẹn trong hiện tại'],
                'diem_yeu'  =>['Khó lập kế hoạch dài hạn và dễ trì hoãn','Có thể quá phụ thuộc vào sự chú ý của người khác','Dễ buồn chán và tìm kiếm sự kích thích mới liên tục','Khó đối mặt với những vấn đề nghiêm trọng đòi hỏi suy nghĩ sâu'],
                'su_nghiep' =>'Bạn hợp với các lĩnh vực như giải trí, truyền thông, dịch vụ, hoặc bất kỳ nơi nào cho phép bạn tương tác với con người và mang lại niềm vui. Công việc cô lập hoặc quá nhiều giấy tờ sẽ khiến bạn kiệt sức.',
                'tinh_yeu'  =>'Bạn yêu bằng cả trái tim và muốn mỗi ngày bên nhau đều là một kỷ niệm đẹp. Bạn cần một người trân trọng sự ấm áp của bạn, nhưng cũng giúp bạn đôi khi chậm lại và suy nghĩ về tương lai thay vì chỉ sống cho hôm nay.',
            ],
        ];

        return $profiles;
    }
}