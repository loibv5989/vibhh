<?php
if (!defined('ABSPATH')) exit;

return [
    '__meta' => [
        'schema_version' => '3.1.0',
        'dataset_type' => 'architectural_nodes',
        'source_type' => 'astrological_prose',
        'updated_at' => '2026-04-14'
    ],
    '__zodiac_map' => [
        'aries' => ['vi' => 'Bạch Dương', 'element' => 'fire'],
        'taurus' => ['vi' => 'Kim Ngưu', 'element' => 'earth'],
        'gemini' => ['vi' => 'Song Tử', 'element' => 'air'],
        'cancer' => ['vi' => 'Cự Giải', 'element' => 'water'],
        'leo' => ['vi' => 'Sư Tử', 'element' => 'fire'],
        'virgo' => ['vi' => 'Xử Nữ', 'element' => 'earth'],
        'libra' => ['vi' => 'Thiên Bình', 'element' => 'air'],
        'scorpio' => ['vi' => 'Thiên Yết', 'element' => 'water'],
        'sagittarius' => ['vi' => 'Nhân Mã', 'element' => 'fire'],
        'capricorn' => ['vi' => 'Ma Kết', 'element' => 'earth'],
        'aquarius' => ['vi' => 'Bảo Bình', 'element' => 'air'],
        'pisces' => ['vi' => 'Song Ngư', 'element' => 'water'],
    ],

    'anchors' => [
        'daily' => [
            'fire' => "{Với đặc tính mạnh mẽ của nguyên tố Lửa|Dưới tác động của luồng năng lượng Lửa trong ngày hôm nay},",
            'earth' => "{Với nền tảng vững chãi từ nguyên tố Đất|Nhịp độ hôm nay cho thấy sự tĩnh tại của nguyên tố Đất đang bao trùm},",
            'air' => "{Khi các dòng chảy của nguyên tố Khí đang luân chuyển|Sự giao thoa của nhóm Khí trong ngày hôm nay chỉ ra rằng},",
            'water' => "{Sự nhạy bén của nguyên tố Nước đang dẫn dắt trực giác của bạn|Khi dòng chảy cảm xúc của hệ Nước hòa nhịp cùng bối cảnh hôm nay},"
        ],
        'weekly' => [
            'fire' => "{Trong chu kỳ bảy ngày tới, ngọn lửa bên trong bạn sẽ được thúc đẩy mạnh mẽ|Chuỗi ngày sắp tới đánh dấu một nhịp độ dồn dập từ nguyên tố Lửa},",
            'earth' => "{Bức tranh toàn cảnh của tuần này nhấn mạnh vào sự tích lũy của nguyên tố Đất|Với sự bảo trợ của năng lượng Đất trong tuần này},",
            'air' => "{Tuần mới mở ra chu kỳ luân chuyển liên tục của nguyên tố Khí|Khi bầu không khí của tuần mới được định hình bởi tính linh hoạt của hệ Khí},",
            'water' => "{Trải dài trong tuần này là những gợn sóng tĩnh lặng nhưng sâu thẳm của hệ Nước|Đặc tính của Nước sẽ bao trùm trọn vẹn bức tranh cảm xúc của bạn trong tuần tới},"
        ],
        'monthly' => [
            'fire' => "{Xuyên suốt chu kỳ của tháng {month}, sự chủ động của bạn sẽ tìm thấy bối cảnh phù hợp để phát huy|Tháng {month} mở ra một giai đoạn rực rỡ dưới đặc tính của nguyên tố Lửa},",
            'earth' => "{Tháng {month} đánh dấu một nhịp độ chậm rãi nhưng vô cùng vững chãi mang đậm sắc thái của hệ Đất|Trong tháng {month} này, năng lượng của đất mẹ sẽ củng cố cho mọi nền tảng bạn đang xây dựng},",
            'air' => "{Bước vào tháng {month}, bạn sẽ cảm nhận rõ sự luân chuyển không ngừng của những luồng thông tin từ hệ Khí|Bức tranh của tháng {month} được dệt nên từ những ý tưởng và sự kết nối mang tần số của nguyên tố Khí},",
            'water' => "{Nhịp độ của tháng {month} mang đậm chiều sâu cảm xúc của nguyên tố Nước|Tháng {month} dẫn lối bạn bước vào một giai đoạn nội tâm tĩnh lặng dưới đặc tính của hệ Nước},"
        ]
    ],

    'daily' => [
        'fire' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Một ngày mà nhịp độ cá nhân của bạn đạt đến trạng thái bứt phá|Bạn đang bước vào một điểm rơi phong độ cực kỳ sắc bén}.",
                    'explanation' => "{Những rào cản|Các chướng ngại vật} dường như {tự động lùi bước|bị xóa nhòa} trước {ý chí|lòng quyết tâm} của bạn. Khả năng {nhìn nhận vấn đề|xử lý các tầng thông tin} diễn ra với tốc độ {đáng kinh ngạc|vượt ngoài mong đợi}.",
                    'action' => "Hãy {tận dụng thời điểm này|nắm bắt ngay cơ hội này} để {hiện thực hóa những ý tưởng đang ấp ủ|khởi động những việc bạn từng e ngại}."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Sự bộc phát có thể vô tình tạo ra những va chạm không đáng có|Sự khao khát thể hiện bản thân đang đi kèm với rủi ro về mặt tương tác}.",
                    'explanation' => "Khát vọng {tiến lên phía trước|hoàn thành mục tiêu} quá lớn khiến bạn vô tình {bỏ qua những tiểu tiết quan trọng|tạo ra sức ép lên những người đồng hành}. Sự {thiếu kiên nhẫn|nóng vội} chính là {điểm mù|lỗ hổng} lớn nhất trong ngày.",
                    'action' => "{Việc chậm lại một nhịp|Sự tĩnh tâm} và {quan sát thái độ của tập thể|lắng nghe phản hồi xung quanh} sẽ giúp bạn {tránh được những rạn nứt|bảo vệ được kết quả công việc}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Sự ấm áp tự nhiên của bạn đang tạo ra sức hút rất lớn|Bạn trở thành tâm điểm của sự gắn kết nhờ sự tích cực dồi dào}.",
                    'explanation' => "{Dù là những cuộc trò chuyện ngắn ngủi hay những tương tác sâu sắc|Mọi sự tiếp xúc trong ngày hôm nay} đều {mang lại cảm giác dễ chịu|để lại dư âm vô cùng tốt đẹp} cho những người bên cạnh bạn.",
                    'action' => "{Đừng ngần ngại|Hãy tự tin} {bày tỏ sự quan tâm chân thành|chia sẻ những suy nghĩ thực tế}, hoàn cảnh hiện tại đang {ủng hộ|tạo điều kiện} cho sự kết nối của bạn."
                ]
            ],
            'money' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 2,
                    'headline' => "{Dòng chảy vật chất duy trì ở trạng thái tĩnh lặng và cân bằng|Không có những gợn sóng quá lớn trong bức tranh tài chính của bạn hôm nay}.",
                    'explanation' => "Mọi thứ {diễn ra đúng như những gì bạn đã dự liệu|nằm trọn trong tầm kiểm soát an toàn}. Đây không phải là thời điểm {thích hợp để kỳ vọng vào sự đột biến|dành cho những quyết định mang tính bước ngoặt}.",
                    'action' => "Hãy {tập trung củng cố những nền tảng đang có|giữ nguyên nhịp độ tích lũy hiện tại} thay vì {tìm kiếm những sự xáo trộn|chạy theo những giá trị bên ngoài}."
                ]
            ]
        ],
        'earth' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Sự vững chãi từ bên trong giúp bạn làm chủ mọi cục diện|Bạn đang thể hiện sự điềm tĩnh tuyệt vời trước những biến động xung quanh}.",
                    'explanation' => "{Những công việc đòi hỏi sự cẩn trọng|Các vấn đề cần chiều sâu phân tích} sẽ được bạn {tháo gỡ một cách triệt để|xử lý với độ chính xác cao}. {Sự ngăn nắp trong tư duy|Góc nhìn thực tế} chính là {tấm khiên bảo vệ bạn|điểm tựa vững chắc nhất}.",
                    'action' => "{Hãy tiếp tục duy trì|Đừng thay đổi} {nhịp độ chậm mà chắc này|phương pháp làm việc cẩn trọng này}, vì nó đang {đưa bạn đi đúng hướng|tạo ra những giá trị bền vững}."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Sự an toàn thái quá đôi khi lại biến thành rào cản của sự phát triển|Bạn dường như đang tự nhốt mình trong những khuôn mẫu quen thuộc}.",
                    'explanation' => "Việc {cố chấp bảo vệ những quan điểm cũ|từ chối tiếp nhận những góc nhìn mới} đang làm {giảm đi tính linh hoạt|chậm lại khả năng thích nghi} của bạn trước {những chuyển biến của tập thể|những sự kiện bất ngờ}.",
                    'action' => "Một chút {cởi mở với những phương pháp mới|khoan dung với sự khác biệt} sẽ {giúp công việc trôi chảy hơn|mang lại sự nhẹ nhõm} cho tâm trí bạn."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Khoảng cách vô hình đang được tạo ra bởi sự tĩnh lặng quá mức|Có một sự nghẽn mạch trong việc truyền tải cảm xúc của bạn}.",
                    'explanation' => "Bạn có xu hướng {khép kín hơn bình thường|rút lui vào thế giới nội tâm} và mưu cầu sự yên tĩnh. Dù không có ác ý, nhưng điều này vô tình khiến {những người thân thiết|những ai quan tâm bạn} cảm thấy {bối rối|khó chạm tới bạn}.",
                    'action' => "Chỉ cần một {cử chỉ quan tâm nhỏ|lời giải thích nhẹ nhàng} cũng đủ để {xóa nhòa sự xa cách|mang lại sự an tâm} cho sợi dây liên kết giữa hai bên."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Những kế hoạch vật chất bạn gieo trồng đang bắt đầu bén rễ vững chắc|Giai đoạn tích lũy âm thầm của bạn đang mang lại những tín hiệu vô cùng khả quan}.",
                    'explanation' => "{Sự kiên nhẫn|Tư duy quản trị} của bạn cuối cùng cũng {được chứng minh là hoàn toàn đúng đắn|tạo ra sự an toàn tuyệt đối}. {Những giá trị thực tiễn|Các giới hạn ngân sách} đang {phục vụ rất tốt cho đời sống của bạn|phát huy hiệu quả bảo vệ dòng tiền}.",
                    'action' => "{Hãy cho phép bản thân|Bạn hoàn toàn có thể} {thư giãn một chút|tận hưởng một tiện nghi nhỏ} như một sự tự thưởng cho {khả năng kiểm soát này|tính kỷ luật của chính mình}."
                ]
            ]
        ],
        'air' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Khả năng liên kết thông tin của bạn đang đạt đến độ sắc bén tối đa|Bạn đóng vai trò như một chất xúc tác tuyệt vời kết nối mọi yếu tố rời rạc}.",
                    'explanation' => "{Sự nhạy bén trong ngôn từ|Khả năng thấu hiểu đa chiều} giúp bạn {chuyển hóa những mâu thuẫn thành sự đồng thuận|tìm ra lối thoát cho những bế tắc của tập thể}. Tư duy của bạn {hoạt động vô cùng hiệu quả|liên tục nảy sinh những giải pháp mới mẻ}.",
                    'action' => "{Đây là thời điểm|Hãy tận dụng ngày hôm nay} để {đưa ra những đề xuất táo bạo|bày tỏ những góc nhìn độc đáo} mà bạn từng chần chừ."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Sự quá tải về mặt dữ liệu đang khiến tâm trí bạn bị nhiễu loạn|Dòng suy nghĩ luân chuyển quá nhanh khiến bạn đánh mất đi sự tập trung cốt lõi}.",
                    'explanation' => "Cảm giác {muốn làm mọi thứ cùng lúc|bị cuốn theo nhiều luồng thông tin khác nhau} đang {bào mòn năng lượng|làm suy giảm hiệu suất} của bạn. Sự {thiếu nhất quán|phân tán} khiến các mục tiêu {trở nên dang dở|chỉ dừng lại ở mức ý tưởng}.",
                    'action' => "{Việc cần làm ngay lúc này là|Thay vì dàn trải, hãy} {viết mọi thứ ra giấy|lọc lại các ưu tiên} và {chỉ chọn duy nhất một điểm đến|tập trung vào một hành động cụ thể}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 2,
                    'headline' => "{Các mối quan hệ duy trì ở một khoảng cách văn minh và nhẹ nhàng|Bầu không khí giao tiếp mang đậm tính trí tuệ và sự cởi mở}.",
                    'explanation' => "Không có {những cảm xúc quá bi lụy hay gắn kết quá sâu|những ràng buộc ngột ngạt}, mọi người {đến với nhau bằng sự tôn trọng không gian riêng|tìm thấy niềm vui trong những câu chuyện đa chủ đề}.",
                    'action' => "Hãy {tận hưởng sự thoải mái này|duy trì sự thanh thoát trong cách đối thoại} để {nuôi dưỡng tinh thần|giữ cho bản thân luôn tươi mới}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Những tò mò nhất thời có thể cuốn trôi đi một phần tích lũy của bạn|Sự hiếu kỳ đang đe dọa các nguyên tắc chi tiêu an toàn}.",
                    'explanation' => "Sức hấp dẫn từ {những thứ mới lạ|các xu hướng bên ngoài} đang {thách thức sự lý trí|làm mờ đi ranh giới cảnh giác} của bạn. Nếu không {tỉnh táo|giữ chặt nguyên tắc}, {sự hao hụt tài chính|những khoản chi không tên} là điều khó tránh khỏi.",
                    'action' => "{Tuyệt đối cẩn trọng|Hãy lùi lại một bước} trước {những quyết định xuất phát từ cảm hứng|những lời mời gọi hấp dẫn nhưng thiếu nền tảng thực tế}."
                ]
            ]
        ],
        'water' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Sự tinh tế đang dẫn dắt bạn đi qua những vấn đề phức tạp một cách êm ái|Khả năng thấu cảm trở thành la bàn định vị xuất sắc cho bạn trong hôm nay}.",
                    'explanation' => "Bạn {cảm nhận được những biến động tinh vi nhất|đọc vị được những điều không ai nói ra} từ môi trường xung quanh. Điều này giúp bạn {né tránh được những rắc rối ngầm|đưa ra những quyết định hợp tình hợp lý}, {mang lại sự an tâm cho tập thể|tạo ra những giá trị vô hình to lớn}.",
                    'action' => "{Hãy tin tưởng vào|Đừng phớt lờ} {sự quan sát nhạy bén|cảm nhận đầu tiên của bạn}, vì chúng đang {vô cùng chính xác|chỉ ra đúng bản chất của vấn đề}."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 5,
                    'headline' => "{Ranh giới giữa sự đồng cảm và sự chịu đựng đang trở nên mờ nhạt|Cảm xúc cá nhân đang tràn bờ và ảnh hưởng đến tính khách quan của bạn}.",
                    'explanation' => "Việc {hấp thụ quá nhiều sự tiêu cực từ người khác|quá để tâm đến những đánh giá xung quanh} đang khiến bạn {mệt mỏi|đánh mất sự tự chủ}. Bức tranh tổng thể bị {bóp méo|che khuất} bởi {những nỗi lo âu vô cớ|sự nhạy cảm thái quá}.",
                    'action' => "{Bạn cần lập tức|Hãy dứt khoát} {thiết lập lại ranh giới bảo vệ bản thân|tách bạch rõ ràng giữa những tác động ngoại cảnh và tâm trí mình} trước khi {rơi vào sự hoang mang|để mọi thứ đi quá xa}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Một ngày mà sự đồng điệu chạm đến những góc khuất sâu kín nhất|Sợi dây liên kết giữa bạn và những người thân thiết trở nên bền chặt tuyệt đối}.",
                    'explanation' => "{Không cần quá nhiều lời giải thích|Chỉ qua những cử chỉ nhỏ}, sự {thấu hiểu|bao dung} vẫn {hiện diện trọn vẹn|xoa dịu những khuyết điểm}. Đây là {thời điểm của sự thấu cảm|liều thuốc tinh thần hoàn hảo} cho những rạn nứt trong quá khứ.",
                    'action' => "{Hãy để sự chân thành dẫn lối|Bạn hoàn toàn an toàn khi} {thả lỏng những lớp vỏ bọc|đón nhận sự quan tâm từ những người thực sự trân trọng bạn}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Những dao động cảm xúc có thể kéo theo sự rò rỉ về mặt vật chất|Hãy cẩn thận với cái bẫy dùng tiền bạc để xoa dịu tâm lý}.",
                    'explanation' => "{Khi tâm trạng chông chênh|Sự thương cảm đặt không đúng chỗ} rất dễ biến thành {những quyết định chi tiền thiếu lý trí|những sự nhượng bộ gây thiệt thòi cho chính bản thân bạn}.",
                    'action' => "{Hãy tạm dừng các giao dịch lớn|Từ chối dứt khoát các yêu cầu mượn mọc} cho đến khi {tâm trí bạn thực sự tĩnh lặng|bạn lấy lại được sự cân bằng}."
                ]
            ]
        ]
    ],

    'weekly' => [
        'fire' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Chuỗi ngày sắp tới đánh dấu một quỹ đạo phát triển đầy tính chủ động|Bạn nắm trong tay quyền làm chủ cục diện của cả một tuần dài}.",
                    'explanation' => "{Những khát vọng|Sự chuẩn bị kỹ lưỡng} trước đây nay đã hội tụ đủ điều kiện để {bứt tốc|chuyển hóa thành hành động cụ thể}. Bầu không khí xung quanh {mang đậm tính cạnh tranh lành mạnh|thôi thúc bạn bước ra phía trước}.",
                    'action' => "{Đừng ngần ngại|Hãy dũng cảm} {đứng ra nhận lãnh trách nhiệm|bảo vệ những quan điểm của mình}, hoàn cảnh đang {tạo đà|trải thảm} cho sự dấn thân của bạn."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 3,
                    'headline' => "{Một tuần mà các mối quan hệ vận hành theo nhịp độ sôi nổi nhưng thiếu đi chiều sâu|Sự tương tác diễn ra liên tục nhưng chủ yếu dừng ở bề mặt}.",
                    'explanation' => "{Những cuộc gặp gỡ|Các hoạt động tập thể} {diễn ra dày đặc|mang lại nhiều tiếng cười}, nhưng để tìm kiếm {một sự đồng điệu trọn vẹn|một chỗ dựa tinh thần tĩnh lặng} thì có lẽ chưa phải lúc.",
                    'action' => "{Hãy cứ hòa mình vào niềm vui chung|Tận hưởng những khoảnh khắc vui vẻ này} nhưng {đừng đặt kỳ vọng quá cao|hãy để mọi thứ diễn ra tự nhiên nhất}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Sự bứt phá mang lại những điểm sáng rõ rệt trong bức tranh tài chính|Bạn có khả năng tạo ra những khoản thu tốt nhờ tính quyết đoán}.",
                    'explanation' => "{Những quyết định nhanh gọn|Sự nhạy bén với các tín hiệu thực tế} {mang lại lợi thế dẫn đầu|giúp bạn chớp được những cơ hội cốt lõi}. Dòng tiền {đang có dấu hiệu tích cực|được khai thông một cách ngoạn mục}.",
                    'action' => "Tuy nhiên, {khi đã đạt được mục tiêu|nếu đã thu về lợi ích}, {hãy biết điểm dừng|cần thiết lập lại hàng rào bảo vệ ngân sách} để {bảo toàn kết quả|tránh việc vung tay quá trán}."
                ]
            ]
        ],
        'earth' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Bảy ngày tới là khoảng thời gian tuyệt vời để thiết lập lại trật tự cá nhân|Sự nhẫn nại của bạn đang từng bước định hình những kết quả vô cùng vững chãi}.",
                    'explanation' => "{Những dự định dài hơi|Các kế hoạch đòi hỏi sự kiên nhẫn} nay {được xử lý triệt để|đạt được bước tiến lớn} nhờ khả năng sắp xếp logic của bạn. {Sự cẩn trọng|Tốc độ chậm mà chắc} đang {phát huy tác dụng tối đa|chứng minh tính đúng đắn của nó}.",
                    'action' => "{Tiếp tục duy trì tính kỷ luật|Hãy bám sát các nguyên tắc đã đặt ra}, sự cống hiến thầm lặng của bạn đang được ghi nhận."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 3,
                    'headline' => "{Nhịp độ giao tiếp của bạn tuần này trôi qua một cách êm đềm và an toàn|Sợi dây liên kết với những người xung quanh mang lại cảm giác vô cùng bình yên}.",
                    'explanation' => "{Không có những cảm xúc trồi sụt|Thiếu vắng đi những sự lãng mạn bất ngờ}, bù lại, bạn tìm thấy {giá trị của sự chân thành|sự bảo bọc thực tế} từ những người bạn thực sự trân trọng.",
                    'action' => "{Đôi khi sự tĩnh lặng chính là câu trả lời tốt nhất|Hãy thả lỏng và đón nhận nhịp độ đều đặn này} thay vì cố gắng tìm kiếm những thay đổi khiên cưỡng."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Tuần này, thu nhập và chi tiêu của bạn được duy trì ở mức cực kỳ ổn định|Bản tính thực tế mang lại cho bạn sự sáng suốt tuyệt vời trong việc quản lý tài sản}.",
                    'explanation' => "{Những nỗ lực tích lũy|Các quyết định giữ tiền an toàn} bắt đầu {tạo ra giá trị thực tiễn|bảo vệ bạn trước những biến động bên ngoài}. Khả năng {kiểm soát dòng tiền|nhìn nhận giá trị vật chất} của bạn đang ở trạng thái tốt nhất.",
                    'action' => "{Bạn hoàn toàn có thể tự thưởng cho mình một chút tiện nghi|Đây là lúc thích hợp để lập ra các quỹ dự phòng dài hạn}, miễn là mọi thứ nằm trong khuôn khổ cho phép."
                ]
            ]
        ],
        'air' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Sẽ có vô số luồng giao tiếp mới đang chờ đón bạn trong chu kỳ bảy ngày tới|Tuần mới mở ra một không gian tư duy đa chiều và sự kết nối cực kỳ mạnh mẽ}.",
                    'explanation' => "Những {sáng kiến|ý tưởng} liên tục {tuôn trào|xuất hiện} khiến bạn {tràn đầy năng lượng|hào hứng muốn thực thi ngay lập tức}. Tư duy linh hoạt giúp bạn {giải quyết vấn đề mượt mà|làm chủ mọi tình huống đối thoại}.",
                    'action' => "{Tuy nhiên, hãy chỉ chọn ra một mục tiêu cốt lõi|Cần tập trung cao độ vào điều quan trọng nhất} để {tránh rơi vào cảnh nói nhiều làm ít|đảm bảo ý tưởng được hiện thực hóa trọn vẹn}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Sự phân tâm của bạn có thể vô tình tạo ra rào cản với những người yêu thương|Tính hời hợt nhất thời dễ khiến đối phương cảm thấy bị bỏ rơi}.",
                    'explanation' => "Mối quan tâm của bạn {đang bị phân tán bởi quá nhiều thứ mới mẻ|dành quá nhiều cho các tương tác xã giao bên ngoài}, khiến {sự tập trung cho những người thân thiết|chiều sâu trong các cuộc trò chuyện nội bộ} bị giảm sút nghiêm trọng.",
                    'action' => "{Hãy dành sự chú tâm tuyệt đối|Cần lắng nghe một cách trọn vẹn} khi ở bên cạnh họ, {đừng để tâm trí trôi dạt đi nơi khác|sự hiện diện hoàn toàn mới là điều họ cần nhất lúc này}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Sự năng động mang lại cơ hội nhưng cũng tiềm ẩn những rủi ro từ sự cả tin|Hãy hết sức cẩn thận với việc đưa ra quyết định tài chính dựa trên tin đồn}.",
                    'explanation' => "Bầu không khí {đầy rẫy những thông tin chưa kiểm chứng|chứa đựng nhiều lời mời gọi} dễ khiến bạn {mất đi sự cảnh giác|bỏ qua các bước phân tích logic}. Việc {vội vàng hành động theo số đông|thiếu chiều sâu trong đánh giá} có thể khiến bạn trả giá đắt.",
                    'action' => "{Tuyệt đối tránh những quyết định vội vã|Hãy đóng băng các kế hoạch chi tiền chớp nhoáng} cho đến khi bạn {tự mình xác thực được toàn bộ dữ liệu|tìm thấy sự minh bạch tuyệt đối}."
                ]
            ]
        ],
        'water' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Sự tinh tế dẫn lối giúp bạn xử lý mọi công việc một cách vô cùng uyển chuyển|Sự thấu cảm mang lại cho bạn khả năng kết nối hoàn hảo với tập thể}.",
                    'explanation' => "{Năng lượng của bạn tuần này đặc biệt thích hợp để|Bạn sở hữu một khả năng nội tâm giúp} {trở thành người gỡ rối cho những xung đột ngầm|tạo ra những giá trị tinh thần lớn lao}. {Không cần quá nhiều tranh luận|Sự tĩnh lặng của bạn} vẫn có sức mạnh {hòa giải|gắn kết} đáng kinh ngạc.",
                    'action' => "{Hãy để bản thân xuôi theo dòng chảy của sự thấu hiểu|Đừng cố ép buộc kết quả một cách cứng nhắc}, mọi thứ sẽ tự tìm được hướng đi tốt nhất của nó."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Đừng để những trạng thái chông chênh vô cớ nhấn chìm năng suất của bạn|Sự nhạy cảm đang trở thành một lớp sương mù che khuất tầm nhìn lý trí}.",
                    'explanation' => "Có những {cơn sóng tâm lý tiêu cực|sự bồn chồn khó gọi tên} liên tục xuất hiện, khiến bạn {mất tập trung vào các mục tiêu hiện tại|dễ đưa ra các quyết định cảm tính}. Khả năng chống chịu áp lực của bạn {đang ở mức rất thấp|đang bị thử thách nghiêm trọng}.",
                    'action' => "{Cần mạnh mẽ vượt lên cảm xúc cá nhân|Hãy neo giữ tâm trí vào những điều thực tế} để {duy trì sự chuyên nghiệp|không bị cuốn đi bởi những suy tư vô hình}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Một tuần đong đầy cảm xúc và ngập tràn sự sâu lắng đang chờ đón bạn|Những sự quan tâm thầm lặng sẽ đưa các mối quan hệ chạm đến sự thấu hiểu}.",
                    'explanation' => "{Sự đồng điệu về mặt nội tâm|Khả năng giao tiếp tinh tế} giúp bạn và những người xung quanh {nhìn nhận rõ những góc khuất chân thật nhất|xóa nhòa mọi ranh giới xa cách}. Bầu không khí {trở nên vô cùng gắn kết|mang đậm màu sắc của sự chia sẻ}.",
                    'action' => "{Hãy cứ tự do bộc lộ những suy nghĩ nội tâm|Mở rộng trái tim để đón nhận sự quan tâm}, đây là lúc bạn được bao bọc bởi những cảm xúc thuần khiết nhất."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Hãy cẩn thận với cái bẫy của việc tìm kiếm sự an ủi thông qua vật chất|Thói quen mua sắm để khỏa lấp nỗi buồn đang chực chờ nuốt chửng ngân sách của bạn}.",
                    'explanation' => "Các quyết định {chi tiêu|giữ tiền} tuần này phụ thuộc rất nhiều vào {những biến động tâm lý|khả năng tự kiểm soát cảm xúc} của bạn. Khi {sự trống trải xuất hiện|cảm thấy không an toàn}, bạn rất dễ {ném tiền vào những thứ vô nghĩa|chi tiêu một cách thiếu ý thức}.",
                    'action' => "{Nếu nhận thấy dấu hiệu bất ổn|Khi cảm thấy tâm trạng đang đi xuống}, {tuyệt đối đừng vội vàng đưa ra quyết định mua bán|hãy khóa chặt thẻ tín dụng} để bảo vệ bản thân."
                ]
            ]
        ]
    ],

    'monthly' => [
        'fire' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Tháng {month} là lúc nhịp độ phát triển của bạn thăng hoa rực rỡ nhờ sự cống hiến|Bản lĩnh cá nhân của bạn sẽ soi sáng những hướng đi mới mẻ và mang tính đột phá}.",
                    'explanation' => "{Những nỗ lực không ngừng nghỉ|Khát khao hoàn thiện} từ trước đến nay cuối cùng cũng {mang lại kết quả xứng đáng|tạo ra những thay đổi vô cùng rõ rệt}. Bạn đang sở hữu {một sự quyết tâm mạnh mẽ|sự tự tin vững vàng} để {chấp nhận những thử thách lớn hơn|bứt phá khỏi những giới hạn cũ kỹ}.",
                    'action' => "{Đây là thời điểm lý tưởng|Hãy tiến lên bằng tất cả nội lực}, đừng ngần ngại {thể hiện năng lực cốt lõi|chủ động dẫn dắt công việc} vì hoàn cảnh đang hoàn toàn ủng hộ bạn."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Sự tự tin của bạn rất thu hút nhưng sự áp đặt cái tôi lại là một rào cản lớn|Hãy cẩn thận với mong muốn luôn phải giành phần thắng trong các cuộc thảo luận}.",
                    'explanation' => "{Năng lượng rực rỡ|Sự quyết đoán thái quá} đôi khi khiến bạn {quên đi việc lắng nghe|vô tình lấn át tiếng nói} của những người xung quanh. Dù bạn {có ý tốt|muốn bảo vệ}, sự {thiếu tinh tế|kiểm soát quá mức} vẫn sẽ tạo ra sự ngột ngạt.",
                    'action' => "{Hãy nhớ rằng mọi mối quan hệ đều cần sự tôn trọng|Việc tiết chế sự cứng nhắc là yếu tố quan trọng nhất} để duy trì một sợi dây liên kết vừa nhiệt thành vừa bền bỉ."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Dòng tiền của bạn trong tháng {month} mang những dấu hiệu tăng trưởng rất khả quan|Sự chủ động nắm bắt cơ hội sẽ mang lại sự gia tăng đáng kể về mặt vật chất}.",
                    'explanation' => "{Những quyết định thực tế|Sự dứt khoát trong việc tối ưu nguồn lực} giúp bạn {thu về những lợi ích thiết thực|khai thông những khoảng tắc nghẽn bấy lâu}. Khả năng {quản lý thu nhập|chớp thời cơ} của bạn đang phát huy cực tốt.",
                    'action' => "{Thách thức lớn nhất hiện tại không phải là cách tạo ra giá trị|Tuy nhiên hãy nhớ rằng}, việc {kiềm chế sự hào phóng bốc đồng|duy trì kế hoạch chi tiêu} mới là thứ giúp bạn giữ được sự ổn định cuối cùng."
                ]
            ]
        ],
        'earth' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Toàn bộ tháng {month} là bức tranh tuyệt đẹp để bạn gặt hái thành quả của sự nhẫn nại|Những dự án bạn từng âm thầm theo đuổi nay đã phát triển vô cùng vững chắc}.",
                    'explanation' => "{Tính kỷ luật|Sự kiên định} và {chiến lược sắp xếp|tư duy hệ thống} của bạn đã {mang lại giá trị thực tiễn|chứng minh được hiệu quả rõ rệt}. Hoàn cảnh đang {mở ra một nấc thang mới|chuẩn bị giao phó cho bạn} những {vị trí quan trọng hơn|nhiệm vụ lớn lao hơn}.",
                    'action' => "{Hãy tự hào về những gì mình đã hoàn thành|Đừng ngần ngại bước lên nhận lấy sự công nhận}, vì đây là kết quả xứng đáng cho một hành trình làm việc bền bỉ."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 3,
                    'headline' => "{Chất lượng các mối quan hệ của bạn trong tháng {month} bước vào một giai đoạn vô cùng bình yên|Sự tin cậy và giá trị cốt lõi giúp bạn củng cố một nền tảng tinh thần vững chãi}.",
                    'explanation' => "{Sự ổn định|Bầu không khí an toàn} bao trùm lấy mọi tương tác, giúp bạn {cảm nhận rõ giá trị của sự cam kết|tìm thấy sự an tâm thực sự}. Dù {thiếu vắng đi những bất ngờ lớn|không có quá nhiều biến động}, sự êm đềm này lại chính là thứ bạn cần.",
                    'action' => "{Sự vững chãi là nền tảng, nhưng một chút mới mẻ sẽ là chất xúc tác|Hãy nhớ làm mới nhịp sống đều đặn hàng ngày} để các mối quan hệ không rơi vào sự khô khan nhàm chán."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Kế hoạch chi tiêu tháng {month} của bạn thể hiện một tầm nhìn xa vô cùng sắc bén|Khả năng định vị sự an toàn vật chất của bạn đang hoạt động ở mức hoàn hảo}.",
                    'explanation' => "{Việc tuân thủ ngân sách một cách nghiêm ngặt|Thói quen quản lý rủi ro khôn ngoan} giúp bạn {cảm thấy hoàn toàn tự tin|tích lũy được những nguồn lực đáng kể}. Bức tường thành tài chính của bạn {đang vững chắc hơn bao giờ hết|đang phát huy tác dụng bảo vệ tối đa}.",
                    'action' => "{Đây là tháng tốt để cân nhắc việc|Đừng ngại việc bắt đầu} {thiết lập các quỹ dự phòng mới|tái cấu trúc lại định hướng tài sản cho tương lai}."
                ]
            ]
        ],
        'air' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Xuyên suốt tháng {month}, sự nhạy bén của bạn sẽ phát huy tối đa khả năng thích nghi|Những biến động của môi trường chính là cơ hội để bạn đưa ra các giải pháp đột phá}.",
                    'explanation' => "{Tư duy linh hoạt|Góc nhìn đa chiều} giúp bạn {đón nhận sự thay đổi một cách đầy chủ động|biến mọi thách thức thành cơ hội giải quyết vấn đề}. {Mạng lưới kết nối|Các mối quan hệ làm việc} sẽ mang đến {những luồng thông tin mới|những tương tác vô cùng giá trị}.",
                    'action' => "{Cần ưu tiên tập trung mở rộng sự giao tiếp|Hãy để tư duy của bạn mở rộng nhất có thể}, vì giới hạn duy nhất lúc này chỉ là cách bạn tổ chức thông tin."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Nhu cầu tìm kiếm sự mới lạ liên tục có thể khiến các mối quan hệ trở nên hời hợt|Khả năng giao tiếp rộng của bạn đang đi kèm với sự thiếu hụt trong cam kết sâu sắc}.",
                    'explanation' => "{Bạn có thể tiếp nhận vô số những tương tác thú vị|Sự cởi mở giúp bạn thu hút rất nhiều người}, nhưng {sự cả thèm chóng chán|việc dễ dàng thay đổi trọng tâm} khiến bạn {rất khó chốt hạ một điểm dừng|khó tạo lập sự gắn bó thực sự vững chắc}.",
                    'action' => "{Nếu muốn giải quyết những xa cách vô hình|Sự thật là}, chỉ có {việc sẵn sàng nói chuyện thẳng thắn và chịu mở lòng|sự dũng cảm đối mặt với các vấn đề cốt lõi} mới giúp tháo gỡ khó khăn."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 5,
                    'headline' => "{Nhịp độ tài chính tháng {month} biến động liên tục đòi hỏi sự tính toán cực kỳ logic|Hãy tuyệt đối tránh xa các quyết định chạy theo trào lưu để mong sinh lời chớp nhoáng}.",
                    'explanation' => "{Sẽ có nhiều luồng tiền linh hoạt chảy vào|Khả năng nắm bắt xu hướng giúp bạn thu về không ít lợi ích}, nhưng {mức độ hao hụt cho các nhu cầu cá nhân cũng cực cao|sự phân tán trong chi tiêu khiến tiền bạc rời đi rất nhanh}. Sự thiếu kiên định là {rủi ro lớn nhất|chiếc bẫy hoàn hảo}.",
                    'action' => "{Việc thiếu đánh giá cẩn thận|Chỉ một giây phút bốc đồng} sẽ khiến bạn {tự làm khó bản thân về mặt ngân sách|rơi vào một tình huống thâm hụt không đáng có}."
                ]
            ]
        ],
        'water' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Tháng {month} đánh dấu một thời điểm tuyệt vời cho chiều sâu cảm xúc và năng lực thấu hiểu|Sự tinh tế của bạn sẽ tạo ra những giá trị mang tính kết nối lớn lao}.",
                    'explanation' => "{Khả năng quan sát nội tâm|Sự thấu hiểu hoàn cảnh} giúp bạn {không cần phải gồng mình phản kháng trước những thay đổi|linh hoạt xử lý vấn đề một cách đầy trí tuệ}. Bằng sự điềm tĩnh, bạn {đón nhận mọi thứ|biến sự mềm mỏng thành thế mạnh} để giữ vững vị thế.",
                    'action' => "{Đừng cố dùng lý trí khô cứng để giải mã mọi hiện tượng|Hãy tin tưởng vào đánh giá tổng quan của bản thân}, vì đôi khi cách tiếp cận nhẹ nhàng lại mang đến hiệu quả cao nhất."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Ba mươi ngày của tháng {month} là một trải nghiệm với những kết nối vô cùng sâu lắng|Bạn đang bước vào một giai đoạn gắn kết mà sự chân thành được đặt lên cao nhất}.",
                    'explanation' => "{Sự thấu hiểu tận cùng|Sợi dây liên kết chặt chẽ} giữa bạn và những người xung quanh {trở nên vô cùng sắc nét|mang đến cảm giác bình yên đến lạ thường}. Những {hiểu lầm cũ|khoảng cách trước đây} dường như {được xoa dịu trọn vẹn|được hòa tan vào sự bao dung}.",
                    'action' => "{Hãy cho phép mình tận hưởng sự dịu dàng này|Đây là lúc bạn được quyền thả lỏng}, hoàn cảnh đang tạo ra không gian an toàn nhất cho cảm xúc của bạn."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Cần nhận thức rõ về ranh giới cá nhân trước khi sự thấu cảm biến thành sự bi lụy|Đừng vì khao khát duy trì hòa khí mà chấp nhận hy sinh sự tự chủ của bản thân}.",
                    'explanation' => "{Nỗi sợ làm người khác buồn|Mong muốn làm hài lòng xung quanh} đôi khi khiến bạn {xóa nhòa đi những nguyên tắc bảo vệ chính mình|dễ dàng thỏa hiệp với những điều không xứng đáng}. Sự quan tâm {sẽ trở nên mệt mỏi|sẽ làm bạn cạn kiệt} nếu nó thiếu đi sự tỉnh táo.",
                    'action' => "{Việc nói không cũng là một cách để bảo vệ bản thân|Hãy nhớ rằng}, {bạn chỉ có thể giúp đỡ người khác|sự gắn kết chỉ bền vững} khi bạn biết trân trọng giới hạn của chính mình."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Lòng thương người không đúng chỗ có thể là điểm yếu khiến bạn gặp khó khăn về tiền bạc|Khả năng bảo vệ tài sản của bạn đang bị che lấp bởi sự cả nể sai thời điểm}.",
                    'explanation' => "{Dù kỹ năng quản lý tài chính của bạn vốn rất tốt|Bạn hoàn toàn có khả năng nhìn ra rủi ro}, nhưng khi {bị tác động bởi những lời nhờ vả|ai đó cầu xin sự giúp đỡ}, bạn lại {dễ dàng bỏ qua các nguyên tắc|chấp nhận chịu thiệt thòi về mặt vật chất}.",
                    'action' => "{Cần dứt khoát từ chối việc phải gánh vác trách nhiệm tài chính cho người khác|Hãy học cách giữ nguyên tắc trước những đòi hỏi vật chất không rõ ràng} để bảo vệ sự an toàn của ngân sách cá nhân."
                ]
            ]
        ]
    ]
];