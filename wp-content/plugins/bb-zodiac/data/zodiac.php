<?php

if (!defined("ABSPATH")) {
    exit();
}

return [
    "meta" => [
        "schema_version" => "2.5",
        "updated_at" => "2026-04-14",
        "coverage" => [
            "12 cung hoàng đạo",
            "ngày sinh",
            "nguyên tố",
            "hành tinh chủ quản",
            "tính chất",
            "phân cực âm dương",
            "phân cung (decan)",
            "tương hợp 5 lớp (Góc chiếu, Nguyên tố, Đặc tính, Âm-Dương, Decan)",
            "giao đỉnh (cusp)",
        ],
        "notes" => [],
    ],
    "signs" => [
        "aries" => [
            "id" => "aries",
            "name" => "Bạch Dương",
            "symbol" => "♈",
            "element" => "Lửa",
            "planet" => "Sao Hỏa",
            "quality" => "Thống Lĩnh (Tiên phong, quyết đoán)",
            "polarity" => "Dương (Masculine)",
            "keywords" => "Quả quyết, Độc lập, Bốc đồng, Thiếu kiên nhẫn",
            "start_m" => 3,
            "start_d" => 21,
            "date_range" => ["start" => "03-21", "end" => "04-19"],
            "compatibility" => [
                "best_match" => ["leo", "sagittarius"],
                "worst_match" => ["cancer", "capricorn"],
                "karmic_match" => ["libra"],
            ],
            "decans" => [
                1 => [
                    "days" => ["03-21", "03-30"],
                    "ruler" => "Sao Hỏa",
                    "vibe" =>
                        "{Năng lượng Bạch Dương nguyên bản|Khí chất Bạch Dương thuần túy}: {mãnh liệt, tốc độ, đề cao cái tôi và sở hữu tinh thần cạnh tranh sắc bén|rực lửa, dứt khoát, tôn trọng bản ngã và luôn sẵn sàng cho mọi cuộc đua}.",
                ],
                2 => [
                    "days" => ["03-31", "04-09"],
                    "ruler" => "Mặt Trời",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Sư Tử|Pha trộn sức hút của Sư Tử}: {ấm áp, lòng tự tôn cao, thích tỏa sáng ở vị trí trung tâm và có tố chất lãnh đạo bẩm sinh|hào sảng, đầy kiêu hãnh, khao khát trở thành tâm điểm và mang năng lực dẫn dắt bẩm sinh}.",
                ],
                3 => [
                    "days" => ["04-10", "04-19"],
                    "ruler" => "Sao Mộc",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Nhân Mã|Chịu ảnh hưởng từ Nhân Mã}: {đam mê tự do, khát khao trải nghiệm, tư duy rộng mở và mang tinh thần lạc quan không giới hạn|yêu thích xê dịch, không giới hạn, nhìn xa trông rộng và sở hữu sức sống tích cực hiếm có}.",
                ],
            ],
            "horoscope_life" =>
                "{Cuộc đời Bạch Dương là chuỗi những cuộc chinh phục|Với Bạch Dương, sống là một hành trình bứt phá không ngừng}. {Bạn mang năng lượng của kẻ dẫn đầu, luôn chủ động tiến bước và không ngại đối mặt với thử thách|Bạn sở hữu ngọn lửa của người mở đường, sẵn sàng nghênh chiến mọi rào cản}.",

            "personality" => [
                "core" =>
                    "{Bạn sống nhanh, thích dẫn đầu và phản ứng trước khi do dự|Nhịp độ sống của bạn luôn ở mức tối đa, hành động nhanh hơn suy tính}; {động lực của bạn đến từ thử thách cùng cảm giác tự mình mở đường|bạn khao khát chinh phục và muốn trở thành người đầu tiên chạm tới đích}.",
                "strengths" => [
                    "quyết đoán",
                    "chủ động",
                    "nhiệt huyết",
                    "dám bắt đầu",
                    "chịu áp lực tốt",
                ],
                "weaknesses" => [
                    "nóng nảy",
                    "thiếu kiên nhẫn",
                    "dễ chán",
                    "quá thẳng",
                    "khó nhường nhịn",
                ],
                "love" =>
                    "{Trong tình cảm, bạn thích rõ ràng, chủ động và không hợp kiểu mập mờ kéo dài|Khi yêu, bạn luôn trực diện, thích nắm thế thượng phong và ghét những mối quan hệ lấp lửng}.",
                "career" =>
                    "{Hợp môi trường cạnh tranh, khởi xướng, bán hàng, kinh doanh, thể thao, vận hành hoặc quản lý dự án|Tỏa sáng trong áp lực cao với các vị trí startup, sales, leader, vận động viên hoặc điều hành thực thi}.",

                "layers" => [
                    "element" =>
                        "{Lửa: Nguyên tố Lửa làm nổi bật tính hành động, nhiệt huyết, tinh thần khởi xướng và phản ứng nhanh|Nguyên tố Lửa: Mang lại sự bùng nổ, đam mê, khả năng dẫn đầu và ra quyết định chớp nhoáng}.",
                    "planet" =>
                        "{Sao Hỏa: Tăng tính chủ động, quyết liệt, cạnh tranh và sức bật hành động|Chủ tinh Sao Hỏa: Khuếch đại sức mạnh chiến đấu, sự can đảm và năng lượng dồi dào}.",
                    "quality" =>
                        "{Thống Lĩnh (Tiên phong, quyết đoán): Tính chất Thống Lĩnh ưu tiên khởi xướng, dẫn dắt và làm chủ nhịp điệu|Nhóm Thống Lĩnh: Đặc trưng bởi khát vọng dẫn đầu, mở đường và kiểm soát cục diện}.",
                    "polarity" =>
                        "{Dương (Masculine): Phân cực Dương thiên về biểu đạt ra ngoài, hành động và tương tác chủ động|Cực Dương: Đại diện cho năng lượng hướng ngoại, sự bộc trực và mong muốn tác động lên thế giới}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 thiên về bản năng tiên phong, thích hành động ngay và ghét bị cản đường|Với Decan 1, tính tiên phong vô cùng mạnh mẽ, không thích chờ đợi và luôn tự tạo ra lối đi riêng}.",
                    2 => "{Decan 2 chú trọng hình ảnh cá nhân, nhu cầu được công nhận và năng lực dẫn dắt người khác|Decan 2 tăng khí chất, khao khát quyền lực và khả năng làm chủ đám đông}.",
                    3 => "{Decan 3 đại diện cho sự phóng khoáng, tự do và tinh thần bứt phá khỏi giới hạn cũ|Decan 3 mạnh về khám phá, yêu trải nghiệm và không chấp nhận sự gò bó}.",
                ],
                "shadow" =>
                    "{Điểm yếu là phản ứng quá nhanh nên dễ tạo va chạm trước khi kịp cân nhắc|Sự bốc đồng thường dẫn tới sai lầm, dễ gây tổn thương cho người khác khi chưa suy nghĩ thấu đáo}.",
            ],
        ],
        "taurus" => [
            "id" => "taurus",
            "name" => "Kim Ngưu",
            "symbol" => "♉",
            "element" => "Đất",
            "planet" => "Sao Kim",
            "quality" => "Kiên Định (Ổn định, vững vàng)",
            "polarity" => "Âm (Feminine)",
            "keywords" => "Bền bỉ, Thực tế, Đam mê vật chất, Cố chấp",
            "start_m" => 4,
            "start_d" => 20,
            "date_range" => ["start" => "04-20", "end" => "05-20"],
            "compatibility" => [
                "best_match" => ["virgo", "capricorn"],
                "worst_match" => ["leo", "aquarius"],
                "karmic_match" => ["scorpio"],
            ],
            "decans" => [
                1 => [
                    "days" => ["04-20", "04-29"],
                    "ruler" => "Sao Kim",
                    "vibe" =>
                        "{Năng lượng Kim Ngưu nguyên bản|Khí chất Kim Ngưu thuần túy}: {trân trọng cái đẹp, chú trọng giá trị vật chất, vô cùng kiên định nhưng đôi khi khá bảo thủ|ưa chuộng sự hưởng thụ, an toàn tài chính, vững chãi như núi nhưng rất khó bị lay chuyển}.",
                ],
                2 => [
                    "days" => ["04-30", "05-10"],
                    "ruler" => "Sao Thủy",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Xử Nữ|Sự pha trộn tính cách Xử Nữ}: {thực tế, sắc bén, tỉ mỉ và sở hữu tư duy phân tích, quản lý tài chính xuất sắc|cẩn trọng, logic, kỷ luật và giỏi trong việc điều phối nguồn lực}.",
                ],
                3 => [
                    "days" => ["05-11", "05-20"],
                    "ruler" => "Sao Thổ",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Ma Kết|Dấu ấn mạnh mẽ của Ma Kết}: {kỷ luật thép, đầy tham vọng, lý trí và làm việc với sự bền bỉ đáng kinh ngạc|nghiêm túc, tham vọng, thực dụng và kiên cường bền bỉ cho đến khi đạt mục tiêu}.",
                ],
            ],
            "horoscope_life" =>
                "{Kim Ngưu hướng tới sự ổn định và bền vững|Cuộc sống của bạn là một đồ thị tăng trưởng bền bỉ}. {Cuộc đời bạn gắn liền với việc xây dựng những giá trị thực tế và tận hưởng thành quả lao động|Bạn sinh ra để tạo dựng nền tảng vững chắc và biết cách trân trọng những tiện nghi xứng đáng}.",

            "personality" => [
                "core" =>
                    "{Bạn đi chậm nhưng chắc, thích ổn định, có gu riêng và muốn tạo ra giá trị hữu hình, bền lâu|Bạn luôn ưu tiên sự an toàn, ưa chuộng sự nhất quán, sở hữu thẩm mỹ tinh tế và khao khát biến mọi ý tưởng thành vật chất có giá trị}.",
                "strengths" => [
                    "kiên định",
                    "thực tế",
                    "bền bỉ",
                    "cảm thụ tốt",
                    "giữ nhịp ổn định",
                ],
                "weaknesses" => [
                    "cố chấp",
                    "ngại thay đổi",
                    "chậm khởi động",
                    "sở hữu cao",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần sự an toàn, đều đặn và cảm giác tin cậy để mở lòng|Khi yêu, bạn đặt niềm tin, sự ổn định lên đầu cùng sự cam kết để trao đi tình cảm hoàn toàn}.",
                "career" =>
                    "{Hợp tài chính, bất động sản, thiết kế, ẩm thực, thương mại, quản trị tài sản hoặc các việc cần độ bền|Tỏa sáng trong lĩnh vực kinh tế, kiến trúc, nghệ thuật, nhà hàng, sales, quản lý quỹ rủi ro hoặc bất kỳ vai trò nào đòi hỏi sự bền bỉ}.",

                "layers" => [
                    "element" =>
                        "{Đất: Nguyên tố Đất làm nổi bật tính thực tế, ổn định, bền bỉ và nhu cầu xây nền tảng chắc chắn|Nguyên tố Đất: Đại diện cho sự vững vàng, điềm tĩnh, khả năng tích lũy và bám rễ sâu vào thực tại}.",
                    "planet" =>
                        "{Sao Kim: Tăng thẩm mỹ, sức hút, nhu cầu gắn kết và khả năng tạo hài hòa|Chủ tinh Sao Kim: Mang lại gu nghệ thuật tinh tế, tình yêu cái đẹp và khát khao tận hưởng cuộc sống}.",
                    "quality" =>
                        "{Kiên Định (Ổn định, vững vàng): Tính chất Kiên Định ưu tiên duy trì, tập trung, bền bỉ và giữ vững hướng đi|Nhóm Kiên Định: Đặc trưng bởi sức chịu đựng dẻo dai, sự trung thành và khả năng bảo vệ thành quả lâu dài}.",
                    "polarity" =>
                        "{Âm (Feminine): Phân cực Âm thiên về hướng nội, tiếp nhận, cảm nhận và nuôi dưỡng bên trong|Cực Âm: Đại diện cho năng lượng thu hút, sự tĩnh lặng và khả năng tích tụ nguồn lực}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 chậm, chắc, yêu cái đẹp và rất khó bị lay chuyển|Thuần túy tính cách Kim Ngưu nhất nằm ở Decan 1: từ tốn, ổn định và không thể bị ép buộc thay đổi}.",
                    2 => "{Decan 2 sắc hơn về trí óc, thực dụng và biết tính toán|Tư duy kinh tế nổi trội ở Decan 2: thông minh trong quản lý và phân tích}.",
                    3 => "{Decan 3 tăng tham vọng, kỷ luật thép cùng sức chịu đựng để tích lũy thành quả lớn|Decan 3 có năng lực chịu áp lực cực lớn để đạt được vinh quang và tham vọng dài hạn}.",
                ],
                "shadow" =>
                    "{Điểm yếu là bám vào vùng an toàn quá lâu và bỏ lỡ nhịp thay đổi cần thiết|Sự bảo thủ khiến bạn mắc kẹt trong sự ổn định, ngại bứt phá khi thời cơ đã tới}.",
            ],
        ],
        "gemini" => [
            "id" => "gemini",
            "name" => "Song Tử",
            "symbol" => "♊",
            "element" => "Khí",
            "planet" => "Sao Thủy",
            "quality" => "Linh Hoạt (Đa chiều, dễ thích nghi)",
            "polarity" => "Dương (Masculine)",
            "keywords" =>
                "Thông minh, Hoạt ngôn, Thiếu kiên định, Biến hóa khó lường",
            "start_m" => 5,
            "start_d" => 21,
            "date_range" => ["start" => "05-21", "end" => "06-20"],
            "compatibility" => [
                "best_match" => ["libra", "aquarius"],
                "worst_match" => ["virgo", "pisces"],
                "karmic_match" => ["sagittarius"],
            ],
            "decans" => [
                1 => [
                    "days" => ["05-21", "05-31"],
                    "ruler" => "Sao Thủy",
                    "vibe" =>
                        "{Năng lượng Song Tử nguyên bản|Khí chất Song Tử thuần túy}: {trí tuệ sắc bén, linh hoạt, tò mò về vạn vật nhưng đôi khi thiếu đi sự tập trung chuyên sâu|nhanh trí, đa nhiệm, yêu thích khám phá thông tin mới nhưng dễ bị xao nhãng bởi những thứ mới mẻ}.",
                ],
                2 => [
                    "days" => ["06-01", "06-10"],
                    "ruler" => "Sao Kim",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Thiên Bình|Ảnh hưởng từ Thiên Bình}: {tinh tế, hòa nhã, đầy sức hút trong các mối quan hệ và nhạy bén trong việc nắm bắt tâm lý|duyên dáng, khéo léo, giỏi giao thiệp xã hội và có khả năng thấu hiểu mong muốn của người khác}.",
                ],
                3 => [
                    "days" => ["06-11", "06-20"],
                    "ruler" => "Sao Thiên Vương",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Bảo Bình|Dòng máu Bảo Bình trong Song Tử}: {tư duy độc đáo, mang tư tưởng nổi loạn ngầm, thích đi ngược lại số đông và cực kỳ sáng tạo|sáng tạo, khác biệt, độc lập, không chấp nhận khuôn mẫu và luôn có những ý tưởng đột phá}.",
                ],
            ],
            "horoscope_life" =>
                "{Song Tử sống trong dòng chảy không ngừng của thông tin và ý tưởng|Cuộc đời bạn là một chuỗi những cuộc đối thoại và khám phá trí tuệ}. {Cuộc đời bạn là hành trình khám phá thế giới qua lăng kính trí tuệ và sự giao tiếp đa chiều|Bạn sinh ra để kết nối dữ liệu qua góc nhìn đa chiều và năng lực ngôn ngữ linh hoạt}.",

            "personality" => [
                "core" =>
                    "{Bạn vận hành bằng thông tin, tò mò nhanh, thích kết nối ý tưởng và hiếm khi chỉ nhìn một phía|Bạn sống bằng dữ liệu, nắm bắt vấn đề chớp nhoáng, giỏi ráp nối các mảng thông tin rời rạc và luôn nhìn nhận mọi sự việc ở nhiều khía cạnh khác nhau}.",
                "strengths" => [
                    "thông minh",
                    "linh hoạt",
                    "giao tiếp tốt",
                    "nhanh nhạy",
                    "học nhanh",
                ],
                "weaknesses" => [
                    "thiếu nhất quán",
                    "dễ phân tán",
                    "nói nhiều hơn làm",
                    "khó đào sâu",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần đối thoại, sự mới mẻ và cảm giác đầu óc được kết nối|Khi yêu, bạn ưu tiên sự kết nối về trí tuệ, không gian trò chuyện và sự đồng điệu trong tư duy}.",
                "career" =>
                    "{Hợp truyền thông, nội dung, marketing, ngôn ngữ, giáo dục, công nghệ hoặc vai trò xử lý nhiều luồng thông tin|Tỏa sáng trong mảng báo chí, PR, quảng cáo, dịch thuật, sư phạm, IT hoặc bất kỳ công việc nào cần sự đa nhiệm}.",

                "layers" => [
                    "element" =>
                        "{Khí: Nguyên tố Khí làm nổi bật tư duy, giao tiếp, tính xã hội và khả năng kết nối ý tưởng|Nguyên tố Khí: Đại diện cho trí tuệ, ngôn từ, sự chuyển động không ngừng và lan truyền thông tin}.",
                    "planet" =>
                        "{Sao Thủy: Tăng tư duy, phân tích, ngôn ngữ và khả năng tiếp nhận thông tin|Chủ tinh Sao Thủy: Mang lại sự nhạy bén, logic cùng phản xạ cực nhanh trước mọi sự vật hiện tượng}.",
                    "quality" =>
                        "{Linh Hoạt (Đa chiều, dễ thích nghi): Tính chất Linh Hoạt ưu tiên thích nghi, xoay chuyển, học nhanh và xử lý đa luồng|Nhóm Linh Hoạt: Đặc trưng bởi sự mềm dẻo, biến hóa và khả năng ứng biến xuất thần trong mọi hoàn cảnh}.",
                    "polarity" =>
                        "{Dương (Masculine): Phân cực Dương thiên về biểu đạt ra ngoài, hành động và tương tác chủ động|Cực Dương: Đại diện cho năng lượng hướng ngoại, sự cởi mở và khao khát kết nối với đám đông}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 nhanh trí, lanh lợi và liên tục đổi hướng khám phá|Thuần tính Song Tử nhất là Decan 1: thông minh, hoạt ngôn và không ngừng tìm kiếm những điều thú vị mới}.",
                    2 => "{Decan 2 thêm độ duyên dáng cùng kỹ năng xã hội, giao tiếp mềm mại hơn|Duyên dáng hơn ở Decan 2 với khả năng ngoại giao và kết nối cộng đồng xuất sắc}.",
                    3 => "{Decan 3 sáng tạo, mang tinh thần cải tiến và tư duy khác biệt|Sáng tạo đột phá ở Decan 3, độc lập và có khả năng đổi mới không ngừng}.",
                ],
                "shadow" =>
                    "{Điểm yếu là dễ tản lực vì có quá nhiều ý tưởng chạy cùng lúc|Sự thiếu tập trung là rào cản lớn nhất khi có quá nhiều sự lựa chọn khiến bạn mất phương hướng}.",
            ],
        ],
        "cancer" => [
            "id" => "cancer",
            "name" => "Cự Giải",
            "symbol" => "♋",
            "element" => "Nước",
            "planet" => "Mặt Trăng",
            "quality" => "Thống Lĩnh (Bảo bọc, phòng thủ)",
            "polarity" => "Âm (Feminine)",
            "keywords" =>
                "Nhạy cảm, Hướng về gia đình, Cảm xúc bất ổn, Khó tha thứ",
            "start_m" => 6,
            "start_d" => 22,
            "date_range" => ["start" => "06-21", "end" => "07-22"],
            "compatibility" => [
                "best_match" => ["scorpio", "pisces"],
                "worst_match" => ["aries", "libra"],
                "karmic_match" => ["capricorn"],
            ],
            "decans" => [
                1 => [
                    "days" => ["06-21", "07-01"],
                    "ruler" => "Mặt Trăng",
                    "vibe" =>
                        "{Năng lượng Cự Giải nguyên bản|Khí chất Cự Giải thuần túy}: {cảm xúc dạt dào, nhạy cảm tột độ, hướng về gia đình và mang bản năng che chở, chăm sóc mạnh mẽ|giàu tình cảm, trực giác mạnh, trân trọng nguồn cội và luôn muốn bảo bọc những người thân yêu}.",
                ],
                2 => [
                    "days" => ["07-02", "07-11"],
                    "ruler" => "Sao Diêm Vương",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Thiên Yết|Sự pha trộn tính cách Thiên Yết}: {thâm trầm, bí ẩn, trực giác tâm linh nhạy bén và có xu hướng sở hữu, kiểm soát cao trong tình cảm|sâu sắc, có phần kín đáo, khả năng thấu thị tâm lý và thích sự gắn kết bền chặt, mang tính chiếm hữu}.",
                ],
                3 => [
                    "days" => ["07-12", "07-22"],
                    "ruler" => "Sao Hải Vương",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Song Ngư|Ảnh hưởng từ Song Ngư}: {mơ mộng, lãng mạn, giàu sự đồng cảm nhưng có xu hướng lẩn tránh hiện thực khi đối mặt với tổn thương|giàu lòng trắc ẩn, nghệ thuật, dễ thấu hiểu nỗi đau của người khác nhưng dễ rơi vào thế giới ảo mộng để phòng thủ}.",
                ],
            ],
            "horoscope_life" =>
                "{Cự Giải tìm kiếm sự an toàn trong thế giới cảm xúc|Cuộc đời bạn là hành trình xây dựng bến đỗ bình yên}. {Cuộc đời bạn gắn liền với việc xây dựng tổ ấm, nuôi dưỡng các mối quan hệ và bảo vệ những gì bạn yêu thương|Sứ mệnh của bạn là nuôi dưỡng tình thương, gắn kết những mảnh đời và che chở cho những giá trị tinh thần thiêng liêng}.",

            "personality" => [
                "core" =>
                    "{Bạn sống bằng cảm xúc, ký ức, nhu cầu an toàn nội tâm và rất nhạy với môi trường xung quanh|Tâm hồn bạn được dệt nên từ những kỷ niệm, luôn khao khát một điểm tựa vững chắc và có khả năng bắt sóng cảm xúc của người khác cực tốt}.",
                "strengths" => [
                    "chăm sóc tốt",
                    "đồng cảm",
                    "trực giác mạnh",
                    "bảo vệ người mình yêu",
                    "trung thành",
                ],
                "weaknesses" => [
                    "nhạy cảm quá mức",
                    "dễ phòng thủ",
                    "khó buông quá khứ",
                    "dao động cảm xúc",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần sự an tâm, chăm chút và cảm giác thuộc về|Khi yêu, bạn đặt sự tin tưởng lên hàng đầu cùng sự quan tâm nhỏ nhặt và cam kết gắn bó}.",
                "career" =>
                    "{Hợp y tế, giáo dục, dịch vụ khách hàng, nhà hàng, bất động sản, nhân sự hoặc công việc hậu cần|Tỏa sáng trong ngành chăm sóc, giảng dạy, hỗ trợ cộng đồng, F&B, thiết kế tổ ấm, quản trị con người hoặc bất cứ vai trò nào cần sự tận tâm}.",

                "layers" => [
                    "element" =>
                        "{Nước: Nguyên tố Nước làm nổi bật trực giác, cảm xúc, sự thấu cảm và chiều sâu nội tâm|Nguyên tố Nước: Đại diện cho sự nuôi dưỡng, dòng chảy tâm hồn, bản năng bảo bọc và sự nhạy cảm tột độ}.",
                    "planet" =>
                        "{Mặt Trăng: Tăng cảm xúc, bản năng chăm sóc và nhu cầu an toàn|Chủ tinh Mặt Trăng: Chi phối ký ức, sự dịu dàng và khao khát một tổ ấm bình yên}.",
                    "quality" =>
                        "{Thống Lĩnh (Bảo bọc, phòng thủ): Tính chất Thống Lĩnh ưu tiên bảo vệ, chủ động dẫn dắt và giữ vai trò kiểm soát an toàn|Nhóm Thống Lĩnh: Đặc trưng bởi khả năng khởi tạo vòng tròn an toàn, bảo vệ lãnh thổ và những người thân yêu một cách quyết liệt}.",
                    "polarity" =>
                        "{Âm (Feminine): Phân cực Âm thiên về hướng nội, tiếp nhận, cảm nhận và nuôi dưỡng bên trong|Cực Âm: Đại diện cho năng lượng thụ động, che chở, sự thấu cảm sâu sắc và lưu giữ kỷ niệm}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 thiên về bản năng che chở, cảm xúc rõ và nhu cầu gia đình vô cùng mạnh mẽ|Decan 1 mạnh về tính mẫu tử/phụ tử, trân trọng nguồn cội và yêu thương vô điều kiện}.",
                    2 => "{Decan 2 sâu hơn, kín hơn, trực giác mạnh và có xu hướng kiểm soát cảm xúc|Decan 2 bí ẩn và nội lực hơn; giỏi kìm nén và thấu hiểu tầng sâu tâm lý}.",
                    3 => "{Decan 3 mềm mại, giàu lòng trắc ẩn nhưng dễ lạc trong cảm xúc|Decan 3 mang tâm hồn nghệ sĩ, bao dung nhưng cũng dễ bị tổn thương bởi thực tế phũ phàng}.",
                ],
                "shadow" =>
                    "{Điểm yếu là dễ mang cảm xúc cũ vào hiện tại và phòng thủ quá mức|Quá khứ thường là xiềng xích của bạn, khiến bạn tự tạo lớp vỏ bọc quá dày}.",
            ],
        ],
        "leo" => [
            "id" => "leo",
            "name" => "Sư Tử",
            "symbol" => "♌",
            "element" => "Lửa",
            "planet" => "Mặt Trời",
            "quality" => "Kiên Định (Tỏa sáng, trung tâm)",
            "polarity" => "Dương (Masculine)",
            "keywords" => "Kiêu hãnh, Hào phóng, Áp đặt, Trọng thể diện",
            "start_m" => 7,
            "start_d" => 23,
            "date_range" => ["start" => "07-23", "end" => "08-22"],
            "compatibility" => [
                "best_match" => ["aries", "sagittarius"],
                "worst_match" => ["taurus", "scorpio"],
                "karmic_match" => ["aquarius"],
            ],
            "decans" => [
                1 => [
                    "days" => ["07-23", "08-01"],
                    "ruler" => "Mặt Trời",
                    "vibe" =>
                        "{Năng lượng Sư Tử nguyên bản|Khí chất Sư Tử thuần túy}: {rực rỡ, tự tôn cao, sinh ra để trở thành tâm điểm của sự chú ý và không chấp nhận làm cái bóng của người khác|kiêu hãnh, nồng nhiệt, tỏa sáng, luôn khao khát được ghi nhận và luôn muốn khẳng định vị thế dẫn đầu}.",
                ],
                2 => [
                    "days" => ["08-02", "08-12"],
                    "ruler" => "Sao Mộc",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Nhân Mã|Ảnh hưởng từ Nhân Mã}: {hào sảng, triết lý, đam mê sự tự do, sở hữu tầm nhìn xa trông rộng và giảm bớt sự cố chấp đặc trưng|phóng khoáng, tự tin, thông thái, yêu thích trải nghiệm mới, có khả năng hoạch định tương lai rộng mở và biết lắng nghe hơn}.",
                ],
                3 => [
                    "days" => ["08-13", "08-22"],
                    "ruler" => "Sao Hỏa",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Bạch Dương|Sức mạnh từ Bạch Dương}: {mãnh liệt, tốc độ, vô cùng quyết đoán và không bao giờ chùn bước trước bất kỳ thách thức nào|quyết đoán, hừng hực lửa chiến đấu, hành động dứt khoát và luôn đối đầu trực diện với khó khăn}.",
                ],
            ],
            "horoscope_life" =>
                "{Sư Tử sinh ra để tỏa sáng và trở thành trung tâm|Bạn mang mệnh lệnh của sự rực rỡ và dẫn dắt}. {Cuộc đời bạn là sân khấu để thể hiện bản ngã, truyền cảm hứng và khẳng định giá trị độc đáo của chính mình|Sứ mệnh của bạn là khẳng định cái tôi cá nhân, lan tỏa năng lượng tích cực và chứng minh bản lĩnh riêng biệt của mình trước thế giới}.",

            "personality" => [
                "core" =>
                    "{Bạn muốn được nhìn thấy, được ghi nhận và sống đúng với khí chất của người dẫn dắt|Bạn khao khát sự công nhận, tôn trọng và luôn hành xử như một người thủ lĩnh thực thụ}.",
                "strengths" => [
                    "tự tin",
                    "hào phóng",
                    "truyền cảm hứng",
                    "sáng tạo",
                    "có khí chất lãnh đạo",
                ],
                "weaknesses" => [
                    "tự ái",
                    "thích kiểm soát hình ảnh",
                    "dễ cái tôi",
                    "ngại bị phớt lờ",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần sự ngưỡng mộ, tôn trọng và sự rõ ràng trong cách đối xử|Khi yêu, bạn muốn được trân trọng như một vị vua/nữ hoàng với sự chung thủy tuyệt đối và những hành động lãng mạn phô trương}.",
                "career" =>
                    "{Hợp sân khấu, quản lý, thương hiệu cá nhân, sáng tạo, sự kiện, lãnh đạo nhóm hoặc vai trò đứng mũi chịu sào|Tỏa sáng trong nghệ thuật, quản trị, branding, event, entertainment, leader team hoặc bất kỳ vị trí nào cần sự chịu trách nhiệm cao nhất}.",

                "layers" => [
                    "element" =>
                        "{Lửa: Nguyên tố Lửa làm nổi bật tính hành động, nhiệt huyết, tinh thần khởi xướng và phản ứng nhanh|Nguyên tố Lửa: Mang đến sự rực rỡ, ấm áp, khát khao tỏa sáng và năng lượng sáng tạo vô tận}.",
                    "planet" =>
                        "{Mặt Trời: Tăng bản ngã, ý chí, tự tin và nhu cầu tỏa sáng|Chủ tinh Mặt Trời: Đại diện cho quyền lực, sự sống và lòng tự tôn kiêu hãnh không thể lu mờ}.",
                    "quality" =>
                        "{Kiên Định (Tỏa sáng, trung tâm): Tính chất Kiên Định ưu tiên giữ nhịp ổn định, bám mục tiêu và duy trì sức hút bền lâu|Nhóm Kiên Định: Đặc trưng bởi sự trung thành, vững vàng và khả năng duy trì ngọn lửa đam mê dài hạn}.",
                    "polarity" =>
                        "{Dương (Masculine): Phân cực Dương thiên về biểu đạt ra ngoài, hành động và tương tác chủ động|Cực Dương: Đại diện cho năng lượng bộc trực, phóng khoáng và nhu cầu khẳng định bản thân mãnh liệt trước đám đông}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 rực rỡ, tự tôn cao và rất muốn đứng ở trung tâm|Decan 1 kiêu hãnh, nồng nhiệt nhất và luôn tìm mọi cách để tỏa sáng}.",
                    2 => "{Decan 2 mở rộng tầm nhìn, bớt cứng và hào sảng hơn trong cách thể hiện|Decan 2 thông thái, hào sảng hơn và biết cách dùng trí tuệ để chinh phục lòng người}.",
                    3 => "{Decan 3 quyết liệt, có tinh thần chiến đấu và sức bật rất mạnh|Decan 3 mạnh mẽ, đầy tính hành động và không ngại va chạm để bảo vệ danh dự}.",
                ],
                "shadow" =>
                    "{Điểm yếu là khi bị tổn thương cái tôi, bạn dễ phản ứng bằng sự kiêu hãnh quá mức|Cái tôi quá lớn là gót chân Achilles của bạn, khiến bạn trở nên độc đoán và khó kiểm soát cảm xúc}.",
            ],
        ],
        "virgo" => [
            "id" => "virgo",
            "name" => "Xử Nữ",
            "symbol" => "♍",
            "element" => "Đất",
            "planet" => "Sao Thủy",
            "quality" => "Linh Hoạt (Phân tích, phục vụ)",
            "polarity" => "Âm (Feminine)",
            "keywords" => "Cầu toàn, Tỉ mỉ, Khắt khe, Tận tụy",
            "start_m" => 8,
            "start_d" => 23,
            "date_range" => ["start" => "08-23", "end" => "09-22"],
            "compatibility" => [
                "best_match" => ["taurus", "capricorn"],
                "worst_match" => ["gemini", "sagittarius"],
                "karmic_match" => ["pisces"],
            ],
            "decans" => [
                1 => [
                    "days" => ["08-23", "09-02"],
                    "ruler" => "Sao Thủy",
                    "vibe" =>
                        "{Năng lượng Xử Nữ nguyên bản|Khí chất Xử Nữ thuần túy}: {tư duy sắc bén, khả năng phân tích chi tiết đến mức hoàn hảo, cực kỳ thực tế và đề cao tính logic|cẩn trọng, logic, tỉ mỉ tột độ, coi trọng hiệu quả thực tiễn và luôn dựa trên dữ liệu xác thực}.",
                ],
                2 => [
                    "days" => ["09-03", "09-12"],
                    "ruler" => "Sao Thổ",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Ma Kết|Dấu ấn của Ma Kết}: {tham vọng lớn ẩn giấu bên trong, kỷ luật thép và coi trọng địa vị, sự nghiệp hơn là những tiểu tiết vụn vặt|nghiêm túc, bền bỉ, tự chủ cực cao và luôn nhìn về kết quả dài hạn thay vì những thứ phù phiếm}.",
                ],
                3 => [
                    "days" => ["09-13", "09-22"],
                    "ruler" => "Sao Kim",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Kim Ngưu|Sắc thái của Kim Ngưu}: {dịu dàng, thực tế, yêu cái đẹp và có xu hướng ưu tiên sự an nhàn, thích tận hưởng cuộc sống|nhã nhặn, thực dụng, có gu thẩm mỹ tinh tế và biết cách cân bằng giữa công việc và hưởng thụ cá nhân}.",
                ],
            ],
            "horoscope_life" =>
                "{Xử Nữ theo đuổi sự hoàn hảo trong từng chi tiết|Cuộc đời bạn là hành trình tinh lọc và tối ưu hóa}. {Cuộc đời bạn là hành trình cải thiện, phục vụ và tìm kiếm ý nghĩa qua sự cống hiến tỉ mỉ|Bạn sinh ra để giúp thế giới trở nên ngăn nắp hơn và khẳng định giá trị bản thân qua kết quả công việc chuẩn xác}.",

            "personality" => [
                "core" =>
                    "{Bạn nhìn thế giới bằng logic, tiêu chuẩn, khả năng sắp xếp và muốn mọi thứ đúng trật tự, có ích thật sự|Thế giới trong mắt bạn là một hệ thống dữ liệu đòi hỏi sự quy củ, chính xác và không chấp nhận sự cẩu thả hay vô nghĩa}.",
                "strengths" => [
                    "tỉ mỉ",
                    "phân tích tốt",
                    "có trách nhiệm",
                    "thực tế",
                    "tận tụy",
                ],
                "weaknesses" => [
                    "cầu toàn",
                    "khắt khe",
                    "hay soi lỗi",
                    "tự gây áp lực",
                    "khó hài lòng",
                ],
                "love" =>
                    "{Trong tình cảm, bạn thể hiện qua chăm sóc cụ thể, sự có mặt đúng lúc và những chi tiết nhỏ|Khi yêu, bạn dùng hành động thực tế để chứng minh qua sự tận tâm thầm lặng và quan tâm chu đáo đến từng nhu cầu của đối phương}.",
                "career" =>
                    "{Hợp kiểm tra chất lượng, dữ liệu, quy trình, biên tập, kế toán, vận hành, chăm sóc sức khỏe hoặc công việc đòi hỏi chính xác|Tỏa sáng trong ngành kiểm toán, phân tích hệ thống, vận hành, y tế, tài chính, quản trị rủi ro hoặc bất kỳ vai trò nào cần sự cẩn trọng tuyệt đối}.",

                "layers" => [
                    "element" =>
                        "{Đất: Nguyên tố Đất làm nổi bật tính thực tế, ổn định, bền bỉ và nhu cầu xây nền tảng chắc chắn|Nguyên tố Đất: Đại diện cho tính tổ chức, quy trình, sự cẩn trọng và năng lực phân tích chi tiết}.",
                    "planet" =>
                        "{Sao Thủy: Tăng tư duy, phân tích, ngôn ngữ và khả năng tiếp nhận thông tin|Chủ tinh Sao Thủy: Mang lại trí tuệ logic, sắc bén cùng kỹ năng xử lý dữ liệu và giải quyết vấn đề xuất sắc}.",
                    "quality" =>
                        "{Linh Hoạt (Phân tích, phục vụ): Tính chất Linh Hoạt ưu tiên quan sát, điều chỉnh, phân tích và phục vụ hiệu quả|Nhóm Linh Hoạt: Đặc trưng bởi sự tận tụy, khả năng hoàn thiện và khả năng tối ưu hóa mọi thứ đạt mức hoàn hảo}.",
                    "polarity" =>
                        "{Âm (Feminine): Phân cực Âm thiên về hướng nội, tiếp nhận, cảm nhận và nuôi dưỡng bên trong|Cực Âm: Đại diện cho năng lượng khiêm tốn, lặng lẽ, sự chu toàn và cống hiến thầm lặng}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 mạnh chất phân tích, logic và chuẩn xác theo kiểu Xử Nữ nguyên bản|Decan 1 chuẩn mực tính cách Xử Nữ nhất, vô cùng cẩn trọng và có óc quan sát sắc bén}.",
                    2 => "{Decan 2 nặng kỷ luật, tham vọng ngầm và ưu tiên thành tựu lâu dài hơn tiểu tiết|Decan 2 tham vọng và kiên cường hơn, giỏi lãnh đạo và có tầm nhìn chiến lược}.",
                    3 => "{Decan 3 mềm hơn, thực tế hơn, có gu thẩm mỹ và biết tận hưởng nhịp sống ổn định|Decan 3 tinh tế và nhạy bén thẩm mỹ hơn, biết cách làm đẹp cuộc sống bằng sự tỉ mỉ của mình}.",
                ],
                "shadow" =>
                    "{Điểm yếu là vòng lặp tự soi lỗi và tiêu chuẩn hóa mọi thứ đến mức mệt mỏi|Sự cầu toàn thái quá là gông cùm khiến bạn luôn khắt khe với chính mình và cảm thấy chưa đủ tốt}.",
            ],
        ],
        "libra" => [
            "id" => "libra",
            "name" => "Thiên Bình",
            "symbol" => "♎",
            "element" => "Khí",
            "planet" => "Sao Kim",
            "quality" => "Thống Lĩnh (Cân bằng, xã giao)",
            "polarity" => "Dương (Masculine)",
            "keywords" => "Hòa nhã, Công bằng, Thiếu chính kiến, Hay do dự",
            "start_m" => 9,
            "start_d" => 23,
            "date_range" => ["start" => "09-23", "end" => "10-22"],
            "compatibility" => [
                "best_match" => ["gemini", "aquarius"],
                "worst_match" => ["cancer", "capricorn"],
                "karmic_match" => ["aries"],
            ],
            "decans" => [
                1 => [
                    "days" => ["09-23", "10-02"],
                    "ruler" => "Sao Kim",
                    "vibe" =>
                        "{Năng lượng Thiên Bình nguyên bản|Khí chất Thiên Bình thuần túy}: {duyên dáng, lãng mạn, bài xích sự xung đột và luôn nỗ lực tìm kiếm sự cân bằng tuyệt đối|nhã nhặn, tinh tế, nghệ thuật, ghét sự thô lỗ và luôn hướng tới sự hòa hợp trong mọi mối quan hệ}.",
                ],
                2 => [
                    "days" => ["10-03", "10-12"],
                    "ruler" => "Sao Thiên Vương",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Bảo Bình|Ảnh hưởng từ Bảo Bình}: {lý trí, độc lập, sở hữu góc nhìn vượt thời đại và đôi khi bộc lộ sự xa cách, lạnh lùng trong tình cảm|thông thái, phá cách, có tư duy tiến bộ và biết cách giữ khoảng cách cá nhân để bảo vệ sự tự do trí tuệ}.",
                ],
                3 => [
                    "days" => ["10-13", "10-22"],
                    "ruler" => "Sao Thủy",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Song Tử|Sự kết hợp với Song Tử}: {hoạt ngôn, thông minh, kỹ năng giao tiếp xuất chúng nhưng tâm ý dễ thay đổi và thiếu kiên định|nhanh trí, giao tiếp khéo léo, có tài ngoại giao bậc thầy nhưng đôi khi gặp khó khăn trong việc đưa ra quyết định dứt khoát}.",
                ],
            ],
            "horoscope_life" =>
                "{Thiên Bình luôn tìm kiếm sự cân bằng và hài hòa|Cuộc đời bạn là một bài thơ về sự đối thoại và thẩm mỹ}. {Cuộc đời bạn là nghệ thuật dung hòa các mối quan hệ, công lý và vẻ đẹp trong một thế giới đầy mâu thuẫn|Sứ mệnh của bạn là trở thành người kết nối sự công bằng và tính nghệ thuật giữa những dòng chảy xung đột của cuộc sống}.",

            "personality" => [
                "core" =>
                    "{Bạn tìm sự cân bằng, cái đẹp và sự hòa hợp trong quan hệ, đồng thời rất giỏi đọc bầu không khí xung quanh|Bản chất của bạn là sự hài hòa và kết nối, luôn có khả năng thấu cảm tinh tế nhịp điệu của các mối quan hệ}.",
                "strengths" => [
                    "ngoại giao tốt",
                    "công bằng",
                    "tinh tế",
                    "thẩm mỹ cao",
                    "biết kết nối",
                ],
                "weaknesses" => [
                    "do dự",
                    "ngại xung đột",
                    "phụ thuộc đánh giá",
                    "dễ thiếu chính kiến",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần sự đẹp đẽ, đối thoại và tôn trọng lẫn nhau|Khi yêu, bạn yêu bằng cả trái tim và gu thẩm mỹ, ưu tiên sự chia sẻ văn minh và lãng mạn nhẹ nhàng}.",
                "career" =>
                    "{Hợp luật, ngoại giao, thiết kế, truyền thông, tư vấn, HR, thương lượng hoặc công việc cần cân bằng lợi ích|Tỏa sáng trong ngành tư pháp, quan hệ công chúng, nghệ thuật, marketing, tư vấn tâm lý, quản trị nhân sự hoặc bất kỳ vai trò nào đòi hỏi sự công tâm}.",

                "layers" => [
                    "element" =>
                        "{Khí: Nguyên tố Khí làm nổi bật tư duy, giao tiếp, tính xã hội và khả năng kết nối ý tưởng|Nguyên tố Khí: Đại diện cho sự khách quan, trí thức, năng lực ngoại giao và xây dựng mạng lưới quan hệ}.",
                    "planet" =>
                        "{Sao Kim: Tăng thẩm mỹ, sức hút, nhu cầu gắn kết và khả năng tạo hài hòa|Chủ tinh Sao Kim: Mang lại vẻ đẹp duyên dáng, thanh lịch, tình yêu hòa bình và gu nghệ thuật đỉnh cao}.",
                    "quality" =>
                        "{Thống Lĩnh (Cân bằng, xã giao): Tính chất Thống Lĩnh ưu tiên chủ động điều phối quan hệ, giữ vững thế cân bằng|Nhóm Thống Lĩnh: Đặc trưng bởi năng lực hòa giải, có khả năng khởi xướng các mối quan hệ xã hội một cách khéo léo}.",
                    "polarity" =>
                        "{Dương (Masculine): Phân cực Dương thiên về biểu đạt ra ngoài, hành động và tương tác chủ động|Cực Dương: Đại diện cho năng lượng tương tác, mở rộng, sự thân thiện và luôn hướng tới sự hợp tác}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 thiên về duyên dáng, hòa nhã và khao khát hòa bình|Decan 1 đại diện cho nét đẹp thuần khiết nhất của Thiên Bình, luôn muốn né tránh mọi sự tranh cãi vô ích}.",
                    2 => "{Decan 2 sắc lý trí hơn, độc lập hơn và có xu hướng suy nghĩ khác biệt|Decan 2 thông minh và cá tính hơn, luôn giữ được cái đầu lạnh trong các tình huống xã giao}.",
                    3 => "{Decan 3 hoạt ngôn, nhanh trí và giỏi xử lý tình huống xã giao|Decan 3 linh hoạt và sắc bén nhất, có khả năng xoay chuyển cục diện bằng ngôn từ}.",
                ],
                "shadow" =>
                    "{Điểm yếu là muốn làm vừa lòng nhiều phía nên dễ mất phương hướng|Nỗ lực dĩ hòa vi quý là gông cùm khiến bạn dễ rơi vào trạng thái ba phải}.",
            ],
        ],
        "scorpio" => [
            "id" => "scorpio",
            "name" => "Thiên Yết",
            "symbol" => "♏",
            "element" => "Nước",
            "planet" => "Sao Diêm Vương",
            "quality" => "Kiên Định (Sâu sắc, cực đoan)",
            "polarity" => "Âm (Feminine)",
            "keywords" => "Bí ẩn, Sâu sắc, Cảnh giác cao, Nhớ lâu thù dai",
            "start_m" => 10,
            "start_d" => 23,
            "date_range" => ["start" => "10-23", "end" => "11-21"],
            "compatibility" => [
                "best_match" => ["cancer", "pisces"],
                "worst_match" => ["leo", "aquarius"],
                "karmic_match" => ["taurus"],
            ],
            "decans" => [
                1 => [
                    "days" => ["10-23", "11-01"],
                    "ruler" => "Sao Diêm Vương",
                    "vibe" =>
                        "{Năng lượng Thiên Yết nguyên bản|Khí chất Thiên Yết thuần túy}: {bí ẩn, sâu sắc, cảm xúc rạch ròi và sở hữu khả năng tái sinh mạnh mẽ từ nghịch cảnh|thâm trầm, lôi cuốn, yêu hận rõ ràng và có nội lực bền bỉ để vượt qua mọi bão táp}.",
                ],
                2 => [
                    "days" => ["11-02", "11-11"],
                    "ruler" => "Sao Hải Vương",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Song Ngư|Pha trộn tính cách Song Ngư}: {nhạy cảm, trực giác sắc bén, dễ bị tác động bởi cảm xúc và mang tần số năng lượng tâm linh, nghệ thuật cao|giàu thấu cảm, trực giác, có tâm hồn lãng mạn thầm kín và có khả năng thấu thị những điều vô hình}.",
                ],
                3 => [
                    "days" => ["11-12", "11-21"],
                    "ruler" => "Mặt Trăng",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Cự Giải|Ảnh hưởng từ Cự Giải}: {gắn bó sâu sắc với gia đình, có bản năng bảo vệ người thân cực kỳ mạnh mẽ nhưng nội tâm đa sầu đa cảm|trân trọng nguồn cội, luôn là lá chắn cho những người yêu thương và mang một tâm hồn vô cùng phức tạp, nhạy cảm}.",
                ],
            ],
            "horoscope_life" =>
                "{Thiên Yết khám phá chiều sâu của sự sống và cái chết|Cuộc đời bạn là hành trình xuyên qua những tầng sâu của tâm hồn}. {Cuộc đời bạn là hành trình lột xác, tìm kiếm sự thật ẩn giấu và nắm giữ sức mạnh của sự tái sinh|Bạn sinh ra để tái sinh không ngừng, khám phá những bí mật của vũ trụ và làm chủ năng lượng của sự chuyển hóa}.",

            "personality" => [
                "core" =>
                    "{Bạn sống sâu, kín, ít khi lộ hết bài nhưng một khi đã tin thì kết nối rất mạnh|Bản chất của bạn là sự tập trung, bí ẩn, luôn giữ lại khoảng lặng riêng nhưng khi đã trao niềm tin thì tình cảm vô cùng sâu sắc}.",
                "strengths" => [
                    "sâu sắc",
                    "kiên cường",
                    "trực giác mạnh",
                    "chịu đựng tốt",
                    "khả năng tái sinh cao",
                ],
                "weaknesses" => [
                    "đa nghi",
                    "kiểm soát",
                    "nhớ lâu",
                    "cực đoan",
                    "khó mở lòng",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần độ sâu, sự trung thành và cảm giác được thấu hiểu tận gốc|Khi yêu, bạn đòi hỏi sự đồng điệu tuyệt đối về linh hồn, sự cam kết trọn đời và thành thật không tì vết}.",
                "career" =>
                    "{Hợp điều tra, nghiên cứu, tâm lý, tài chính rủi ro, y khoa, bảo mật, chiến lược hoặc lĩnh vực cần nhìn thấu bản chất|Tỏa sáng trong ngành cảnh sát, thám tử, tâm lý học, ngân hàng, bác sĩ phẫu thuật, quản trị rủi ro hoặc bất kỳ công việc nào cần sự tập trung cao độ}.",

                "layers" => [
                    "element" =>
                        "{Nước: Nguyên tố Nước làm nổi bật trực giác, cảm xúc, sự thấu cảm và chiều sâu nội tâm|Nguyên tố Nước: Đại diện cho thế giới tâm lý phức tạp, sức mạnh vô hình và khả năng cảm nhận năng lượng sâu sắc}.",
                    "planet" =>
                        "{Sao Diêm Vương: Tăng chiều sâu, sức mạnh chuyển hóa, kiểm soát và khả năng tái sinh|Chủ tinh Sao Diêm Vương: Đại diện cho sự tái sinh, phá hủy, kiến tạo, quyền lực ngầm và trực giác nhạy bén tột độ}.",
                    "quality" =>
                        "{Kiên Định (Sâu sắc, cực đoan): Tính chất Kiên Định ưu tiên sự bền bỉ, tập trung sâu và khó bị lay chuyển khỏi mục tiêu|Nhóm Kiên Định: Đặc trưng bởi sự kiên cường, dẻo dai cùng khả năng chịu đựng áp lực cực lớn mà không hề khuất phục}.",
                    "polarity" =>
                        "{Âm (Feminine): Phân cực Âm thiên về hướng nội, tiếp nhận, cảm nhận và nuôi dưỡng bên trong|Cực Âm: Đại diện cho năng lượng bí ẩn, thu hút, sự kín đáo và khả năng giấu kín nội tâm mãnh liệt}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 rất mạnh về chiều sâu, bản năng và khả năng tự chuyển hóa|Decan 1 là hiện thân của sự bí ẩn nhất, giàu nội lực và sức mạnh tái sinh}.",
                    2 => "{Decan 2 đậm trực giác, nghệ thuật hoặc tâm linh, cảm xúc tinh vi hơn|Decan 2 nhạy cảm, nghệ sĩ hơn, giàu lòng thấu cảm và khả năng sáng tạo}.",
                    3 => "{Decan 3 gắn bó gia đình, bảo vệ người thân và cảm xúc nội tâm dữ dội|Decan 3 ấm áp, bảo bọc hơn, trung thành tuyệt đối với những người trong vòng tròn tin cậy}.",
                ],
                "shadow" =>
                    "{Điểm yếu là dùng sự im lặng và kiểm soát như một lớp áo giáp quá dày|Sự đa nghi thái quá khiến bạn tự cô lập mình}.",
            ],
        ],
        "sagittarius" => [
            "id" => "sagittarius",
            "name" => "Nhân Mã",
            "symbol" => "♐",
            "element" => "Lửa",
            "planet" => "Sao Mộc",
            "quality" => "Linh Hoạt (Tự do, triết lý)",
            "polarity" => "Dương (Masculine)",
            "keywords" => "Lạc quan, Yêu tự do, Thiếu gắn kết, Thiếu kỷ luật",
            "start_m" => 11,
            "start_d" => 22,
            "date_range" => ["start" => "11-22", "end" => "12-21"],
            "compatibility" => [
                "best_match" => ["aries", "leo"],
                "worst_match" => ["virgo", "pisces"],
                "karmic_match" => ["gemini"],
            ],
            "decans" => [
                1 => [
                    "days" => ["11-22", "12-01"],
                    "ruler" => "Sao Mộc",
                    "vibe" =>
                        "{Năng lượng Nhân Mã nguyên bản|Khí chất Nhân Mã thuần túy}: {khát khao tự do mãnh liệt, tinh thần lạc quan mạnh mẽ và luôn hướng về bức tranh vĩ mô thay vì bận tâm tiểu tiết|yêu chuộng sự phóng khoáng tột độ, luôn nhìn vào mặt tích cực của vấn đề và quan tâm đến đại cuộc thay vì những vụn vặt}.",
                ],
                2 => [
                    "days" => ["12-02", "12-11"],
                    "ruler" => "Sao Hỏa",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Bạch Dương|Sức mạnh từ Bạch Dương}: {mạnh mẽ, quyết liệt, hành động theo đam mê và mang tinh thần chiến đấu, sức cạnh tranh vô cùng cao|quyết đoán, đầy lửa, luôn làm việc bằng tất cả trái tim và không bao giờ chùn bước trước mục tiêu đã chọn}.",
                ],
                3 => [
                    "days" => ["12-12", "12-21"],
                    "ruler" => "Mặt Trời",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Sư Tử|Vẻ rực rỡ từ Sư Tử}: {đầy kiêu hãnh, mang vầng hào quang rực rỡ, thích tỏa sáng và có khả năng truyền cảm hứng tuyệt vời cho cộng đồng|tự tin, tỏa sáng, thu hút và có tố chất trở thành người dẫn dắt tinh thần cho đám đông}.",
                ],
            ],
            "horoscope_life" =>
                "{Nhân Mã khao khát tự do và những chân lý vĩ đại|Cuộc đời bạn là một mũi tên luôn lao về phía những chân trời mới}. {Cuộc đời bạn là cuộc phiêu lưu không giới hạn, tìm kiếm ý nghĩa lớn lao qua trải nghiệm và tri thức mở rộng|Bạn sinh ra để trải nghiệm và học hỏi, khám phá những tri thức vĩ đại giữa thế gian rộng lớn này}.",

            "personality" => [
                "core" =>
                    "{Bạn hướng ra thế giới rộng lớn, thích tự do, chân lý, trải nghiệm và càng bị bó chặt càng dễ phản ứng mạnh|Bạn mang tâm hồn của kẻ lữ hành, khao khát sự thông thái, những chuyến đi và không gì làm khó bạn bằng sự gò bó}.",
                "strengths" => [
                    "lạc quan",
                    "cởi mở",
                    "thẳng thắn",
                    "ham học hỏi",
                    "khả năng truyền cảm hứng",
                ],
                "weaknesses" => [
                    "thiếu kiên trì",
                    "nóng vội",
                    "quá thẳng",
                    "ngại ràng buộc",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần không gian, sự vui vẻ và một người hiểu nhịp sống tự do của mình|Khi yêu, bạn trân trọng sự tin tưởng, tự do cá nhân, sự đồng điệu về lý tưởng sống và những chuyến phiêu lưu chung}.",
                "career" =>
                    "{Hợp giáo dục, du lịch, ngoại ngữ, truyền thông, đào tạo, phát triển thị trường hoặc các công việc cần mở rộng tầm nhìn|Tỏa sáng trong ngành sư phạm, lữ hành, dịch thuật, marketing, coaching, kinh doanh quốc tế hoặc bất kỳ vị trí nào mang tính tiên phong}.",

                "layers" => [
                    "element" =>
                        "{Lửa: Nguyên tố Lửa làm nổi bật tính hành động, nhiệt huyết, tinh thần khởi xướng và phản ứng nhanh|Nguyên tố Lửa: Mang lại nguồn năng lượng bừng sáng, nhiệt thành, khát khao mở rộng giới hạn và tinh thần phiêu lưu}.",
                    "planet" =>
                        "{Sao Mộc: Tăng mở rộng, lạc quan, niềm tin và tầm nhìn|Chủ tinh Sao Mộc: Đại diện cho sự may mắn, triết lý và khát vọng vươn tới những chân lý cao đẹp}.",
                    "quality" =>
                        "{Linh Hoạt (Tự do, triết lý): Tính chất Linh Hoạt ưu tiên thích nghi, mở rộng trải nghiệm và thay đổi nhịp hành động|Nhóm Linh Hoạt: Đặc trưng bởi sự cởi mở, không ngừng vận động, khả năng tiếp thu văn hóa mới và tư duy không biên giới}.",
                    "polarity" =>
                        "{Dương (Masculine): Phân cực Dương thiên về biểu đạt ra ngoài, hành động và tương tác chủ động|Cực Dương: Đại diện cho năng lượng tự do, phóng khoáng, sự chân thành, thẳng thắn và bộc trực}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 thuần lạc quan, tự do và thích bứt ra khỏi giới hạn|Decan 1 đại diện cho tinh thần phóng khoáng nhất và luôn tìm cách phá vỡ mọi rào cản ngăn bước chân bạn}.",
                    2 => "{Decan 2 quyết liệt hơn, hành động nhanh và có tính chiến đấu cao|Decan 2 mạnh mẽ, đầy nhiệt huyết chiến đấu và không ngại đối đầu để bảo vệ lý tưởng}.",
                    3 => "{Decan 3 nổi bật, truyền cảm hứng và thích đứng ở vị trí dẫn đường|Decan 3 rực rỡ, lôi cuốn nhất và luôn là người khơi mào cho những xu hướng mới}.",
                ],
                "shadow" =>
                    "{Điểm yếu là dễ bỏ dở sớm nếu cảm thấy hành trình mất đi sự hào hứng|Sự thiếu kiên trì là rào cản lớn nhất khi niềm vui ban đầu dần tan biến}.",
            ],
        ],
        "capricorn" => [
            "id" => "capricorn",
            "name" => "Ma Kết",
            "symbol" => "♑",
            "element" => "Đất",
            "planet" => "Sao Thổ",
            "quality" => "Thống Lĩnh (Quyền lực, hệ thống)",
            "polarity" => "Âm (Feminine)",
            "keywords" => "Tham vọng, Kỷ luật, Lạnh lùng, Chú trọng thực tế",
            "start_m" => 12,
            "start_d" => 22,
            "date_range" => ["start" => "12-22", "end" => "01-19"],
            "compatibility" => [
                "best_match" => ["taurus", "virgo"],
                "worst_match" => ["aries", "libra"],
                "karmic_match" => ["cancer"],
            ],
            "decans" => [
                1 => [
                    "days" => ["12-22", "12-31"],
                    "ruler" => "Sao Thổ",
                    "vibe" =>
                        "{Năng lượng Ma Kết nguyên bản|Khí chất Ma Kết thuần túy}: {nghiêm túc, tham vọng to lớn, làm việc theo hệ thống và kỷ luật thép, cực kỳ lý trí trong mọi quyết định|trọng trách, thực dụng, bền bỉ, tư duy quy trình và luôn đặt mục tiêu lên hàng đầu}.",
                ],
                2 => [
                    "days" => ["01-01", "01-10"],
                    "ruler" => "Sao Kim",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Kim Ngưu|Sắc thái từ Kim Ngưu}: {thực tế nhưng biết cách tận hưởng cuộc sống, trân trọng cái đẹp và đề cao sự ổn định tài chính, không hề khô khan|thực dụng nhưng không thiếu thẩm mỹ, yêu thích sự tinh tế và biết cách dùng tiền bạc để tạo ra sự thịnh vượng bền vững}.",
                ],
                3 => [
                    "days" => ["01-11", "01-19"],
                    "ruler" => "Sao Thủy",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Xử Nữ|Sự pha trộn với Xử Nữ}: {óc phân tích sắc bén, logic, đặc biệt chú trọng vào chi tiết, quy trình và đôi lúc có phần khắt khe|thông minh, cẩn trọng, tỉ mỉ, luôn đòi hỏi sự chuẩn xác tột độ và dễ trở nên cầu toàn quá mức}.",
                ],
            ],
            "horoscope_life" =>
                "{Ma Kết xây dựng đế chế của sự bền vững|Cuộc đời bạn là hành trình chinh phục những đỉnh cao đầy thử thách}. {Cuộc đời bạn là hành trình leo lên đỉnh cao thông qua kỷ luật, sự kiên trì và tầm nhìn dài hạn|Bạn sinh ra để đứng trên đỉnh vinh quang thông qua nỗ lực bền bỉ và chiến lược đúng đắn}.",

            "personality" => [
                "core" =>
                    "{Bạn tư duy theo mục tiêu, cấu trúc, thành tựu, thường chậm mở lòng nhưng rất bền bỉ khi đã xác định đường đi|Bạn sống để hiện thực hóa những tham vọng lớn, khép kín, cẩn trọng nhưng vô cùng kiên cường một khi đã chọn mục tiêu}.",
                "strengths" => [
                    "kỷ luật",
                    "tham vọng",
                    "kiên trì",
                    "thực tế",
                    "chịu trách nhiệm",
                ],
                "weaknesses" => [
                    "lạnh",
                    "khô cảm xúc",
                    "quá áp lực bản thân",
                    "thích kiểm soát",
                ],
                "love" =>
                    "{Trong tình cảm, bạn thể hiện bằng sự ổn định, trách nhiệm và cam kết lâu dài hơn là lời nói nhiều cảm xúc|Khi yêu, bạn trao đi sự cam kết chắc chắn, sự tin cậy tuyệt đối và những kế hoạch tương lai thực tế thay vì những lời hứa hão huyền}.",
                "career" =>
                    "{Hợp quản trị, tài chính, điều hành, xây dựng hệ thống, tổ chức, chiến lược hoặc vị trí đòi hỏi leo dốc lâu dài|Tỏa sáng trong vai trò CEO, quản lý quỹ, chính trị gia, ngành xây dựng, tổ chức nhân sự, quy hoạch hoặc bất kỳ công việc nào cần sức bền}.",

                "layers" => [
                    "element" =>
                        "{Đất: Nguyên tố Đất làm nổi bật tính thực tế, ổn định, bền bỉ và nhu cầu xây nền tảng chắc chắn|Nguyên tố Đất: Đại diện cho nền tảng vững vàng, vật chất, tính kỷ luật và ý thức trách nhiệm cao độ}.",
                    "planet" =>
                        "{Sao Thổ: Tăng kỷ luật, trách nhiệm, cấu trúc và sức bền|Chủ tinh Sao Thổ: Biểu tượng của thời gian, sự trưởng thành, áp lực tạo nên kim cương và những bài học cuộc đời}.",
                    "quality" =>
                        "{Thống Lĩnh (Quyền lực, hệ thống): Tính chất Thống Lĩnh ưu tiên xây hệ thống, kiểm soát cấu trúc và chịu trách nhiệm cao|Nhóm Thống Lĩnh: Đặc trưng bởi tham vọng dẫn đầu, khả năng tổ chức, quản lý và đạt được địa vị tối cao}.",
                    "polarity" =>
                        "{Âm (Feminine): Phân cực Âm thiên về hướng nội, tiếp nhận, cảm nhận và nuôi dưỡng bên trong|Cực Âm: Đại diện cho sự tập trung, điềm tĩnh, năng lực làm việc âm thầm để hướng tới kết quả lớn}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 rất nguyên bản: nghiêm túc, tham vọng và ưu tiên kết quả|Decan 1 là mẫu người thép: kỷ luật, thực dụng nhất và luôn tập trung tối đa cho thành tựu sự nghiệp}.",
                    2 => "{Decan 2 mềm hơn nhưng vẫn thực tế, biết cân bằng giữa hưởng thụ và kỷ luật|Decan 2 nhạy bén, khôn ngoan hơn, giỏi quản lý tài chính và biết cách hưởng thụ thành quả}.",
                    3 => "{Decan 3 sắc logic, kỹ tính và thiên về tối ưu hóa từng chi tiết|Decan 3 thông thái, cẩn trọng nhất và không bao giờ cho phép sự sai sót nhỏ xảy ra}.",
                ],
                "shadow" =>
                    "{Điểm yếu là tự giam mình trong tiêu chuẩn cao và áp lực phải thành công|Sự nghiêm khắc quá mức là nhà tù của chính bạn, cùng nỗi sợ thất bại khiến bạn đôi khi trở nên khắc nghiệt}.",
            ],
        ],
        "aquarius" => [
            "id" => "aquarius",
            "name" => "Bảo Bình",
            "symbol" => "♒",
            "element" => "Khí",
            "planet" => "Sao Thiên Vương",
            "quality" => "Kiên Định (Dị biệt, cộng đồng)",
            "polarity" => "Dương (Masculine)",
            "keywords" => "Sáng tạo, Phá cách, Độc lập, Bướng bỉnh",
            "start_m" => 1,
            "start_d" => 20,
            "date_range" => ["start" => "01-20", "end" => "02-18"],
            "compatibility" => [
                "best_match" => ["gemini", "libra"],
                "worst_match" => ["taurus", "scorpio"],
                "karmic_match" => ["leo"],
            ],
            "decans" => [
                1 => [
                    "days" => ["01-20", "01-29"],
                    "ruler" => "Sao Thiên Vương",
                    "vibe" =>
                        "{Năng lượng Bảo Bình nguyên bản|Khí chất Bảo Bình thuần túy}: {khác biệt, mang tư tưởng nổi loạn, có tầm nhìn vượt thời đại, luôn đi theo lối đi riêng và từ chối ràng buộc|độc đáo, tự do, phá cách, tư duy tiến bộ bậc nhất, không chấp nhận bị đóng khung và khao khát sự giải phóng trí tuệ}.",
                ],
                2 => [
                    "days" => ["01-30", "02-08"],
                    "ruler" => "Sao Thủy",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Song Tử|Sự kết nối với Song Tử}: {kỹ năng giao tiếp xuất sắc, hoạt ngôn, tư duy phản xạ cực nhanh và luôn khao khát dung nạp tri thức mới|nhanh trí, giao tiếp thông minh, nảy số tốt và không bao giờ ngừng học hỏi những điều mới lạ}.",
                ],
                3 => [
                    "days" => ["02-09", "02-18"],
                    "ruler" => "Sao Kim",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Thiên Bình|Dấu ấn từ Thiên Bình}: {duyên dáng, hòa đồng, năng lực ngoại giao tuyệt vời, đặc biệt coi trọng các mối quan hệ xã hội và sự công bằng|nhã nhặn, khéo léo, giỏi kết nối cộng đồng, trân trọng tình bạn và tính nhân văn trong các mối liên kết}.",
                ],
            ],
            "horoscope_life" =>
                "{Bảo Bình mang sứ mệnh thay đổi thế giới|Bạn sinh ra để phá vỡ những quy chuẩn cũ kỹ}. {Cuộc đời bạn là dòng chảy của tư tưởng đột phá, phá vỡ quy chuẩn và định hình tương lai theo cách riêng|Cuộc đời bạn là hành trình cách tân, thay đổi nhận thức cộng đồng và kiến tạo những giá trị mới cho ngày mai}.",

            "personality" => [
                "core" =>
                    "{Bạn nghĩ khác, đi trước, không thích bị đóng khung; luôn quan tâm đến ý tưởng lớn vì cộng đồng và ưu tiên tương lai hơn là khuôn mẫu sẵn có|Bạn sở hữu tư duy của người đến từ tương lai; hướng tới lợi ích chung của xã hội và tin vào sức mạnh của sự đổi mới}.",
                "strengths" => [
                    "độc lập",
                    "sáng tạo",
                    "tư duy hệ thống",
                    "phá cách",
                    "nhìn xa",
                ],
                "weaknesses" => [
                    "xa cách",
                    "bướng",
                    "khó đoán",
                    "thiếu nhất quán cảm xúc",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần sự tôn trọng không gian cá nhân và một người có thể trò chuyện như bạn bè trước|Khi yêu, bạn coi trọng sự tự do, thấu hiểu và một sự kết nối tri kỷ thay vì ràng buộc truyền thống}.",
                "career" =>
                    "{Hợp công nghệ, sản phẩm, nghiên cứu, đổi mới, cộng đồng, truyền thông số hoặc các dự án cần phá cách|Tỏa sáng trong IT, R&D, sáng tạo công nghệ, mạng xã hội, startup, khoa học ứng dụng hoặc bất kỳ vai trò nào đòi hỏi tư duy đột phá}.",

                "layers" => [
                    "element" =>
                        "{Khí: Nguyên tố Khí làm nổi bật tư duy, giao tiếp, tính xã hội và khả năng kết nối ý tưởng|Nguyên tố Khí: Đại diện cho lý trí sắc lạnh, tri thức, tầm nhìn vượt thời đại và ý tưởng cách tân}.",
                    "planet" =>
                        "{Sao Thiên Vương: Tăng độc lập, phá cách, tư duy mới và tinh thần khác biệt|Chủ tinh Sao Thiên Vương: Biểu tượng của sự nổi loạn, đột phá, sự khai sáng và những thay đổi bất ngờ}.",
                    "quality" =>
                        "{Kiên Định (Dị biệt, cộng đồng): Tính chất Kiên Định ưu tiên giữ bản sắc, theo đuổi ý tưởng dài hạn và kết nối cộng đồng|Nhóm Kiên Định: Đặc trưng bởi sự trung thành với lý tưởng, bảo vệ quan điểm cá nhân và hoạt động vì nhóm xã hội lớn}.",
                    "polarity" =>
                        "{Dương (Masculine): Phân cực Dương thiên về biểu đạt ra ngoài, hành động và tương tác chủ động|Cực Dương: Đại diện cho năng lượng lan tỏa, tiến bộ, sự lập dị độc đáo và khao khát thay đổi thế giới}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 rất khác biệt, tự do và khó khuất phục bởi số đông|Decan 1 dị biệt, bướng bỉnh nhất và luôn trung thành với bản sắc cá nhân độc đáo}.",
                    2 => "{Decan 2 mạnh giao tiếp, học nhanh và mở rộng kết nối trí tuệ|Decan 2 linh hoạt, thông thái hơn và giỏi lan tỏa những ý tưởng mới đến mọi người}.",
                    3 => "{Decan 3 xã giao tốt, biết cân bằng tập thể và cái đẹp trong quan hệ|Decan 3 hài hòa, duyên dáng nhất và giỏi điều phối lợi ích cộng đồng, thẩm mỹ sống}.",
                ],
                "shadow" =>
                    "{Điểm yếu là có thể tạo khoảng cách cảm xúc ngay cả khi vẫn rất quan tâm|Sự xa cách đôi khi khiến bạn trở nên lạnh lùng dù trong lòng bạn vẫn hướng về mọi người}.",
            ],
        ],
        "pisces" => [
            "id" => "pisces",
            "name" => "Song Ngư",
            "symbol" => "♓",
            "element" => "Nước",
            "planet" => "Sao Hải Vương",
            "quality" => "Linh Hoạt (Thấu cảm, hòa tan)",
            "polarity" => "Âm (Feminine)",
            "keywords" =>
                "Mơ mộng, Lãng mạn, Dễ bị tác động, Trốn tránh hiện thực",
            "start_m" => 2,
            "start_d" => 19,
            "date_range" => ["start" => "02-19", "end" => "03-20"],
            "compatibility" => [
                "best_match" => ["cancer", "scorpio"],
                "worst_match" => ["gemini", "sagittarius"],
                "karmic_match" => ["virgo"],
            ],
            "decans" => [
                1 => [
                    "days" => ["02-19", "02-29"],
                    "ruler" => "Sao Hải Vương",
                    "vibe" =>
                        "{Năng lượng Song Ngư nguyên bản|Khí chất Song Ngư thuần túy}: {mộng mơ, gắn kết tâm linh, vô cùng nhạy cảm, giàu lòng trắc ẩn nhưng cũng dễ trở thành nạn nhân của lòng tốt|giàu trí tưởng tượng, nhạy cảm tâm linh, tâm hồn dễ rung động, bao dung, thấu cảm nhưng cần học cách bảo vệ bản thân trước sự lợi dụng}.",
                ],
                2 => [
                    "days" => ["03-01", "03-10"],
                    "ruler" => "Mặt Trăng",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Cự Giải|Dòng máu Cự Giải trong Song Ngư}: {dạt dào tình cảm, bản năng chăm sóc mạnh mẽ, dễ bị tổn thương và luôn mưu cầu sự an toàn từ nền tảng gia đình|giàu tình thương, luôn muốn che chở, tâm hồn nhạy cảm và trân trọng những giá trị thuộc về tổ ấm}.",
                ],
                3 => [
                    "days" => ["03-11", "03-20"],
                    "ruler" => "Sao Diêm Vương",
                    "vibe" =>
                        "{Cộng hưởng năng lượng Thiên Yết|Sức mạnh từ Thiên Yết}: {thấu cảm sâu sắc, bí ẩn, có năng lực nhìn thấu tâm lý người khác và sở hữu một nội tâm mãnh liệt đôi khi dẫn đến sự cực đoan|sâu sắc, thâm trầm, lôi cuốn, giỏi đọc vị cảm xúc và có nguồn sức mạnh tinh thần cực lớn nên cần cân bằng giữa đam mê và lý trí}.",
                ],
            ],
            "horoscope_life" =>
                "{Song Ngư hòa tan vào vũ trụ của cảm xúc và tâm linh|Cuộc đời bạn là một bản nhạc đầy thi vị về lòng trắc ẩn}. {Cuộc đời bạn là hành trình thấu cảm, sáng tạo và tìm kiếm sự kết nối vượt ra ngoài giới hạn vật chất|Sứ mệnh của bạn là mang tình thương đến với thế gian và kết nối linh hồn giữa những thực tại hữu hình và vô hình}.",

            "personality" => [
                "core" =>
                    "{Bạn luôn cảm nhận thế giới bằng trực giác, nhìn đời qua lăng kính của cảm xúc và tin vào những kết nối vô hình|Bạn lấy trực giác làm kim chỉ nam, thường biến thế giới thành những mảng màu tâm hồn và luôn trân trọng sợi dây liên kết tâm linh giữa người với người}.",
                "strengths" => [
                    "đồng cảm",
                    "mơ mộng",
                    "sáng tạo",
                    "nhạy bén cảm xúc",
                    "giàu lòng trắc ẩn",
                ],
                "weaknesses" => [
                    "dễ lạc hướng",
                    "trốn tránh thực tế",
                    "bị tác động mạnh",
                    "thiếu ranh giới",
                ],
                "love" =>
                    "{Trong tình cảm, bạn cần sự nâng niu, an toàn, đồng điệu tinh thần và bạn thường cho đi rất nhiều|Khi yêu, bạn khao khát sự đồng điệu tuyệt đối về linh hồn, sự lãng mạn thuần khiết và sẵn sàng hy sinh vì người mình yêu}.",
                "career" =>
                    "{Hợp nghệ thuật, chữa lành, tâm lý, chăm sóc, âm nhạc, điện ảnh, nội dung sáng tạo hoặc công việc dùng cảm nhận nhiều hơn công thức|Tỏa sáng trong âm nhạc, y tế, tư vấn tâm lý, phim ảnh, viết lách, các ngành nghề nhân văn hoặc bất kỳ vai trò nào đòi hỏi sự thấu cảm cao}.",

                "layers" => [
                    "element" =>
                        "{Nước: Nguyên tố Nước làm nổi bật trực giác, cảm xúc, sự thấu cảm và chiều sâu nội tâm|Nguyên tố Nước: Đại diện cho đại dương bao la của tiềm thức, sự hòa tan ranh giới và khả năng cảm thụ nghệ thuật}.",
                    "planet" =>
                        "{Sao Hải Vương: Tăng trực giác, mơ mộng, lòng trắc ẩn và kết nối vô hình|Chủ tinh Sao Hải Vương: Biểu tượng của tâm linh, ảo ảnh, sự lãng mạn vô điều kiện và trí tưởng tượng phong phú}.",
                    "quality" =>
                        "{Linh Hoạt (Thấu cảm, hòa tan): Tính chất Linh Hoạt ưu tiên cảm nhận, thích nghi, thấu cảm và tiếp nhận tinh tế|Nhóm Linh Hoạt: Đặc trưng bởi sự xuôi dòng, dễ dàng biến đổi, khả năng chữa lành và hy sinh vì người khác}.",
                    "polarity" =>
                        "{Âm (Feminine): Phân cực Âm thiên về hướng nội, tiếp nhận, cảm nhận và nuôi dưỡng bên trong|Cực Âm: Đại diện cho năng lượng mơ màng, nhu thuận, sự êm đềm và khát khao đồng điệu linh hồn}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 rất mơ mộng, nhạy cảm và có tần số tâm linh rõ ràng|Decan 1 thuần khiết nhất: lãng mạn, nhạy cảm và mang tâm hồn của một kẻ mộng mơ thực thụ}.",
                    2 => "{Decan 2 tăng bản năng chăm sóc, gia đình và nhu cầu an toàn cảm xúc|Decan 2 ấm áp, hướng về tổ ấm hơn và luôn khao khát một bến đỗ bình yên cho trái tim}.",
                    3 => "{Decan 3 sâu sắc, bí ẩn và dễ cảm được tầng tâm lý của người khác|Decan 3 mạnh mẽ, lôi cuốn hơn và có khả năng thấu hiểu những góc khuất trong tâm hồn đối phương}.",
                ],
                "shadow" =>
                    "{Điểm yếu là dễ hòa tan vào cảm xúc và mất điểm tựa nếu thiếu ranh giới|Sự nhạy cảm quá mức khiến bạn dễ bị tổn thương và dễ lạc lối nếu không biết cách tự bảo vệ mình}.",
            ],
        ],
    ],
    "cusps" => [
        "pisces_aries" => [
            "name" => "Giao đỉnh Tái Sinh (Rebirth)",
            "date_range" => ["start" => "03-17", "end" => "03-23"],
            "blend" => "Nước x Lửa",
            "vibe" =>
                "{Sự giao thoa giữa trực giác mộng mơ của Song Ngư và ngọn lửa tiên phong, nhiệt huyết của Bạch Dương|Sự kết hợp giữa tâm hồn mộng mơ và tinh thần chiến binh cùng sức mạnh khởi đầu của cung Lửa}.",
        ],
        "aries_taurus" => [
            "name" => "Giao đỉnh Quyền Lực (Power)",
            "date_range" => ["start" => "04-17", "end" => "04-23"],
            "blend" => "Lửa x Đất",
            "vibe" =>
                "{Năng lượng bứt phá, tiên phong của Bạch Dương kết hợp cùng sự thực tế, kiên định vững vàng của Kim Ngưu|Bản lĩnh dẫn đầu của Bạch Dương đi cùng sự vững chãi, thực dụng của Kim Ngưu}.",
        ],
        "taurus_gemini" => [
            "name" => "Giao đỉnh Năng Lượng (Energy)",
            "date_range" => ["start" => "05-17", "end" => "05-23"],
            "blend" => "Đất x Khí",
            "vibe" =>
                "{Bản chất thực tế, điềm tĩnh của Kim Ngưu hòa quyện cùng trí tuệ linh hoạt, kỹ năng giao tiếp nhạy bén của Song Tử|Sự vững chãi của đất kết hợp với sự nhanh nhẹn, hoạt ngôn của khí}.",
        ],
        "gemini_cancer" => [
            "name" => "Giao đỉnh Phép Thuật (Magic)",
            "date_range" => ["start" => "06-17", "end" => "06-23"],
            "blend" => "Khí x Nước",
            "vibe" =>
                "{Trí tuệ sắc bén, đa chiều của Song Tử kết hợp với chiều sâu cảm xúc dạt dào, nhạy cảm tột độ của Cự Giải|Đầu óc linh hoạt của Song Tử chạm tới tâm hồn nhạy cảm, sâu sắc của Cự Giải}.",
        ],
        "cancer_leo" => [
            "name" => "Giao đỉnh Dao Động (Oscillation)",
            "date_range" => ["start" => "07-19", "end" => "07-25"],
            "blend" => "Nước x Lửa",
            "vibe" =>
                "{Sự hướng nội, nhạy cảm của Cự Giải tạo nên màn mâu thuẫn nhưng hòa quyện tinh tế với khát khao tỏa sáng rực rỡ của Sư Tử|Nét dịu dàng của Cự Giải là sự đối lập thú vị với bản lĩnh vương giả của Sư Tử}.",
        ],
        "leo_virgo" => [
            "name" => "Giao đỉnh Bộc Lộ (Exposure)",
            "date_range" => ["start" => "08-19", "end" => "08-25"],
            "blend" => "Lửa x Đất",
            "vibe" =>
                "{Vầng hào quang rực rỡ, đầy kiêu hãnh của Sư Tử đi kèm với sự tỉ mỉ, thực tế và cầu toàn của Xử Nữ|Khí chất vương giả của Sư Tử song hành cùng đầu óc phân tích chuẩn xác của Xử Nữ}.",
        ],
        "virgo_libra" => [
            "name" => "Giao đỉnh Cái Đẹp (Beauty)",
            "date_range" => ["start" => "09-19", "end" => "09-25"],
            "blend" => "Đất x Khí",
            "vibe" =>
                "{Năng lực phân tích logic của Xử Nữ kết hợp hoàn hảo với gu thẩm mỹ tinh tế, yêu thích sự hòa hợp của Thiên Bình|Sự chuẩn xác của Xử Nữ kết hợp tinh tế với tâm hồn nghệ sĩ của Thiên Bình}.",
        ],
        "libra_scorpio" => [
            "name" => "Giao đỉnh Kịch Tính (Drama)",
            "date_range" => ["start" => "10-19", "end" => "10-25"],
            "blend" => "Khí x Nước",
            "vibe" =>
                "{Lối sống xã giao, duyên dáng của Thiên Bình hòa trộn với sự bí ẩn, cảm xúc mãnh liệt và quyền lực ngầm của Thiên Yết|Sự duyên dáng của Thiên Bình kết hợp nội lực thâm trầm cùng sức mạnh chuyển hóa của Thiên Yết}.",
        ],
        "scorpio_sagittarius" => [
            "name" => "Giao đỉnh Cách Mạng (Revolution)",
            "date_range" => ["start" => "11-18", "end" => "11-24"],
            "blend" => "Nước x Lửa",
            "vibe" =>
                "{Chiều sâu cảm xúc, sự cảnh giác của Thiên Yết gặp gỡ tinh thần lạc quan, khát khao tự do và đam mê khám phá của Nhân Mã|Bản năng của Thiên Yết đồng hành cùng ngọn lửa tự do, tầm nhìn triết lý của Nhân Mã}.",
        ],
        "sagittarius_capricorn" => [
            "name" => "Giao đỉnh Tiên Tri (Prophecy)",
            "date_range" => ["start" => "12-18", "end" => "12-24"],
            "blend" => "Lửa x Đất",
            "vibe" =>
                "{Tầm nhìn vĩ mô, lý tưởng của Nhân Mã được hiện thực hóa thông qua kỷ luật thép và tham vọng lớn lao của Ma Kết|Lý tưởng cao đẹp của Nhân Mã được vật chất hóa bởi kỷ luật và chiến lược bền bỉ của Ma Kết}.",
        ],
        "capricorn_aquarius" => [
            "name" => "Giao đỉnh Bí Ẩn (Mystery)",
            "date_range" => ["start" => "01-16", "end" => "01-22"],
            "blend" => "Đất x Khí",
            "vibe" =>
                "{Tính quy củ, kỷ luật của Ma Kết va chạm đầy thú vị với tư duy đột phá, vượt thời đại của Bảo Bình|Sự nghiêm túc của Ma Kết kết hợp đầy kịch tính với tư tưởng cách tân của Bảo Bình}.",
        ],
        "aquarius_pisces" => [
            "name" => "Giao đỉnh Thấu Cảm (Sensitivity)",
            "date_range" => ["start" => "02-15", "end" => "02-21"],
            "blend" => "Khí x Nước",
            "vibe" =>
                "{Sự thông thái, độc lập của Bảo Bình kết nối sâu sắc với tâm hồn mộng mơ, giàu tính tâm linh của Song Ngư|Lý trí sắc lạnh của Bảo Bình giao thoa với trực giác thấu cảm của Song Ngư}.",
        ],
    ],
];
