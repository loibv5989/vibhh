<?php
if (!defined('ABSPATH')) exit;

return [

    // =========================================================
    // 1. TẦNG 1: THẦN SỐ HỌC — Số chủ đạo (Life Path)
    // Bản chất cốt lõi của con người
    // =========================================================
    'lifepath' => [
        1 => [
            'keyword'   => 'Tiên phong',
            'archetype' => 'The Hero',
            'essence'   => 'Độc lập, ý chí mạnh, cần dẫn đầu. Cái tôi rõ ràng, không chịu phụ thuộc.',
            'shadow'    => 'Sợ yếu đuối, sợ bị kiểm soát, dễ trở nên độc đoán hoặc tự cô lập khi căng thẳng.',
            'persona'   => 'Luôn thể hiện sự mạnh mẽ, quyết đoán — nhưng bên trong có thể đang mệt mỏi vì không cho phép mình yếu.',
            'growth'    => 'Học cách nhận sự giúp đỡ và chấp nhận sự phụ thuộc lành mạnh.',
        ],
        2 => [
            'keyword'   => 'Kết nối',
            'archetype' => 'The Lover',
            'essence'   => 'Nhạy cảm, hòa giải, cần quan hệ. Sống trong mối liên kết với người khác.',
            'shadow'    => 'Mất ranh giới cá nhân, dễ tan biến vào người khác, sợ xung đột đến mức đánh mất chính mình.',
            'persona'   => 'Người dễ chịu, luôn nhường — nhưng bên trong đang tích tụ giận dữ không được nói ra.',
            'growth'    => 'Học cách giữ bản sắc riêng trong khi kết nối. Khám phá phần cứng rắn bị che giấu.',
        ],
        3 => [
            'keyword'   => 'Biểu đạt',
            'archetype' => 'The Jester / Creator',
            'essence'   => 'Sáng tạo, ngôn từ, lạc quan. Cần được nghe và được thấy.',
            'shadow'    => 'Sợ bị phán xét, lạc đường trong phân tán, dùng sự vui vẻ để che giấu nỗi đau.',
            'persona'   => 'Bề ngoài sôi nổi, hài hước — nhưng che đậy sự trống rỗng hoặc nỗi sợ không được chấp nhận.',
            'growth'    => 'Đối diện với chiều sâu cảm xúc thay vì lảng tránh bằng sự vui vẻ bề mặt.',
        ],
        4 => [
            'keyword'   => 'Kiến tạo',
            'archetype' => 'The Builder / Ruler',
            'essence'   => 'Kỷ luật, hệ thống, trách nhiệm. Cần cấu trúc và sự ổn định.',
            'shadow'    => 'Cứng nhắc, sợ thay đổi, kiểm soát thái quá mọi thứ xung quanh.',
            'persona'   => 'Người đáng tin cậy, chăm chỉ — nhưng khao khát được thoát ra ngoài khuôn khổ.',
            'growth'    => 'Buông bỏ kiểm soát và tin tưởng vào sự biến chuyển tự nhiên của cuộc sống.',
        ],
        5 => [
            'keyword'   => 'Tự do',
            'archetype' => 'The Explorer',
            'essence'   => 'Biến đổi, phiêu lưu, đa dạng. Không chịu bị gò bó bởi khuôn mẫu.',
            'shadow'    => 'Bất ổn định, chạy trốn khỏi cam kết, sợ cô đơn nhưng lại tự tạo ra nó.',
            'persona'   => 'Người tự do, không bị ràng buộc — nhưng bên trong rất sợ bị bỏ lại một mình.',
            'growth'    => 'Tìm tự do trong chiều sâu, không chỉ trong chiều rộng. Cam kết với điều thực sự có ý nghĩa.',
        ],
        6 => [
            'keyword'   => 'Nuôi dưỡng',
            'archetype' => 'The Caregiver',
            'essence'   => 'Yêu thương, trách nhiệm, gia đình. Sống để chăm sóc và bảo vệ.',
            'shadow'    => 'Bao đồng, kiểm soát dưới danh nghĩa yêu thương, oán giận âm thầm vì hy sinh không được ghi nhận.',
            'persona'   => 'Người luôn cho đi — nhưng bên trong rất mệt và cần được ai đó chăm sóc lại.',
            'growth'    => 'Học cách nhận lại. Phân biệt giữa yêu thương thực sự và nhu cầu kiểm soát.',
        ],
        7 => [
            'keyword'   => 'Chiều sâu',
            'archetype' => 'The Sage / Hermit',
            'essence'   => 'Phân tích, tâm linh, hướng nội. Tìm kiếm sự thật bên dưới bề mặt.',
            'shadow'    => 'Cô lập, ngạo mạn trí tuệ, dùng tri thức như lớp giáp tránh sự thân mật.',
            'persona'   => 'Người hiền triết, bí ẩn — nhưng khao khát được kết nối thực sự.',
            'growth'    => 'Tin tưởng người khác và mở lòng ra ngoài thế giới tri thức.',
        ],
        8 => [
            'keyword'   => 'Quyền năng',
            'archetype' => 'The Ruler / Warrior',
            'essence'   => 'Tham vọng, thực thi, tài chính. Cần kết quả hữu hình và sự công nhận.',
            'shadow'    => 'Tham lam, dùng quyền lực để kiểm soát, sợ thất bại nên không bao giờ dừng lại.',
            'persona'   => 'Người thành công, mạnh mẽ — nhưng bên trong rất sợ mình không đủ giỏi.',
            'growth'    => 'Định nghĩa lại thành công phi vật chất. Tìm lại phần mềm mại bị chôn vùi.',
        ],
        9 => [
            'keyword'   => 'Viên mãn',
            'archetype' => 'The Humanitarian',
            'essence'   => 'Từ bi, cộng đồng, hoàn kết chu kỳ. Nhìn thấy bức tranh toàn cục.',
            'shadow'    => 'Ảo tưởng, từ chối thực tế cụ thể, dễ trở thành người tử vì đạo.',
            'persona'   => 'Người vị tha, rộng lượng — nhưng mệt mỏi vì mang gánh nặng của cả thế giới.',
            'growth'    => 'Học cách buông bỏ và hoàn kết. Nhận thức rằng không phải ai cũng cần được cứu.',
        ],
        11 => [
            'keyword'   => 'Trực giác Master',
            'archetype' => 'The Mystic / Visionary',
            'essence'   => 'Nhạy bén tâm linh, tầm nhìn xa, năng lượng dẫn dắt người khác ở tầng sâu.',
            'shadow'    => 'Lo âu cao độ, cảm giác quá tải vì hấp thụ năng lượng người khác, tự phá hoại.',
            'persona'   => 'Người truyền cảm hứng — nhưng thường xuyên hoài nghi về giá trị chính mình.',
            'growth'    => 'Sống cùng với độ nhạy cảm cao thay vì chống lại nó. Bảo vệ năng lượng cá nhân.',
            'master'    => true,
        ],
        22 => [
            'keyword'   => 'Kiến trúc sư Master',
            'archetype' => 'The Magician / Builder',
            'essence'   => 'Tầm nhìn lớn, khả năng hiện thực hóa, xây dựng di sản lâu dài.',
            'shadow'    => 'Áp lực bóp nghẹt, sợ thất bại vì tiêu chuẩn quá cao, có thể tê liệt hoàn toàn.',
            'persona'   => 'Người có khả năng vĩ đại — nhưng luôn chiến đấu với câu hỏi "mình có đủ tầm không?".',
            'growth'    => 'Chia nhỏ tầm nhìn lớn thành từng bước nhỏ. Chấp nhận sự không hoàn hảo.',
            'master'    => true,
        ],
    ],

    // =========================================================
    // 2. TẦNG 2: JUNG — Ý nghĩa số điện thoại (Phone Core)
    // Năng lượng vô thức biểu lộ / Công cụ giao tiếp
    // =========================================================
    'phone_core' => [
        1 => [
            'vibe'          => 'Khởi xướng, cá nhân, ý chí',
            'unconscious'   => 'Vô thức đang cần khẳng định bản thân, cần được nhìn nhận như một cá thể độc lập.',
            'communication' => 'Phát ra tín hiệu của một người quyết đoán, rõ ràng, không thích sự vòng vo.',
        ],
        2 => [
            'vibe'          => 'Kết nối, hòa giải, thấu cảm',
            'unconscious'   => 'Vô thức đang tìm kiếm sự liên kết, cần được cảm thấy an toàn trong các mối quan hệ.',
            'communication' => 'Tạo cảm giác dễ chịu, mềm mỏng, thu hút những người cần sự lắng nghe.',
        ],
        3 => [
            'vibe'          => 'Biểu đạt, sáng tạo, lan tỏa',
            'unconscious'   => 'Vô thức đang cần được nghe, được nhìn thấy — có một tiếng nói bên trong muốn được bày tỏ.',
            'communication' => 'Sinh động, thu hút, dễ để lại ấn tượng và tạo sự thân thiện bề mặt.',
        ],
        4 => [
            'vibe'          => 'Ổn định, hệ thống, bền vững',
            'unconscious'   => 'Vô thức đang tìm kiếm nền tảng, cần cảm giác kiểm soát và trật tự trong cuộc sống.',
            'communication' => 'Tạo vỏ bọc của sự nghiêm túc, chuyên nghiệp và đáng tin cậy tuyệt đối.',
        ],
        5 => [
            'vibe'          => 'Tự do, biến đổi, cơ hội',
            'unconscious'   => 'Vô thức đang phá vỡ giới hạn — có một xung lực muốn thoát ra khỏi những gì quen thuộc.',
            'communication' => 'Mở ra sự cởi mở, thích nghi nhanh và sẵn sàng đón nhận những kết nối bất ngờ.',
        ],
        6 => [
            'vibe'          => 'Nuôi dưỡng, trách nhiệm, gắn kết',
            'unconscious'   => 'Vô thức đang cần cho đi và nhận lại tình yêu thương, khao khát một chốn thuộc về.',
            'communication' => 'Ấm áp, chu đáo, tạo cảm giác an toàn như gia đình cho đối tác giao tiếp.',
        ],
        7 => [
            'vibe'          => 'Trí tuệ, phân tích, chiều sâu',
            'unconscious'   => 'Vô thức đang tìm kiếm sự thật, không chấp nhận bề mặt, cần không gian tĩnh lặng.',
            'communication' => 'Tạo khoảng cách an toàn, giao tiếp có chọn lọc, ít nhưng chất lượng.',
        ],
        8 => [
            'vibe'          => 'Thực thi, quyền lực, tài chính',
            'unconscious'   => 'Vô thức đang muốn kiểm soát kết quả, khao khát thiết lập địa vị và vật chất.',
            'communication' => 'Dứt khoát, hiệu quả, nhắm thẳng đến hành động và lợi ích cụ thể.',
        ],
        9 => [
            'vibe'          => 'Bao quát, cộng đồng, cống hiến',
            'unconscious'   => 'Vô thức mang ý thức về điều rộng lớn hơn, cần những kết nối mang tính ý nghĩa.',
            'communication' => 'Tạo sự rộng lượng, nhân văn, thu hút những mối quan hệ mang tính chia sẻ.',
        ],
        11 => [
            'vibe'          => 'Trực giác, tâm linh, dẫn dắt',
            'unconscious'   => 'Vô thức đang hoạt động ở tần số nhạy bén, thu hút những kết nối có ý nghĩa sâu xa.',
            'communication' => 'Giao tiếp dựa trên trực giác, để lại dư âm lâu dài, dễ nắm bắt tâm lý người khác.',
            'master'        => true,
        ],
        22 => [
            'vibe'          => 'Tầm nhìn lớn, kiến tạo, di sản',
            'unconscious'   => 'Vô thức đang hoạt động ở quy mô vĩ mô, sẵn sàng cho những trách nhiệm khổng lồ.',
            'communication' => 'Có sức nặng, mang tính định hướng chiến lược và xây dựng lâu dài.',
            'master'        => true,
        ],
    ],

    // =========================================================
    // 3. JUNG — Phân loại Nhóm Năng Lượng & Đối Cực (Shadow)
    // =========================================================
    'energy_groups' => [
        'yang_action'   => ['numbers' => [1, 3, 5],  'name' => 'Năng lượng Dương (Hành động, Hướng ngoại)'],
        'yin_structure' => ['numbers' => [2, 4, 6],  'name' => 'Năng lượng Âm (Cấu trúc, Hướng nội)'],
        'depth_wisdom'  => ['numbers' => [7, 9, 11], 'name' => 'Chiều sâu (Trí tuệ, Tâm linh)'],
        'power_legacy'  => ['numbers' => [8, 22],    'name' => 'Quyền năng (Di sản, Kiến tạo lớn)'],
    ],

    'shadow_pairs' => [
        1  => [9, 22],
        9  => [1],
        2  => [8],
        8  => [2],
        3  => [7],
        7  => [3],
        4  => [5],
        5  => [4],
        6  => [11],
        11 => [6],
        22 => [1]
    ],

    'lp_phone_map' => [
        'identical' => [
            'jung_concept' => 'Nhất quán (Ego Alignment)',
            'description'  => 'Bản chất cốt lõi và năng lượng giao tiếp đang đi cùng một hướng. Bạn không dùng số điện thoại để che giấu hay đóng kịch; bạn đang dùng nó làm một chiếc loa phóng thanh để khẳng định con người thật của mình.',
        ],
        'harmonic' => [
            'jung_concept' => 'Mặt nạ xã hội (The Persona)',
            'description'  => 'Hai con số cộng hưởng ở cùng một trường năng lượng, nhưng số điện thoại là phiên bản "được xã hội chấp nhận hơn". Bạn đang dùng lớp Persona này để bảo vệ những đặc tính cốt lõi và nhạy cảm nhất bên trong.',
        ],
        'compensatory' => [
            'jung_concept' => 'Sự trỗi dậy của Bóng tối (The Shadow)',
            'description'  => 'Đây là sự đối cực thú vị. Phần nội tâm bị kìm nén (Shadow) đang gào thét tìm cách bộc lộ ra ngoài. Bên ngoài bạn tỏ ra một đằng, nhưng vô thức đã tự động chọn công cụ giao tiếp ngược lại để tự cân bằng sự thiếu hụt tâm lý.',
        ],
        'neutral' => [
            'jung_concept' => 'Cơ chế Tích hợp (Integration)',
            'description'  => 'Vô thức đang đi vay mượn một công cụ hữu ích để bù đắp kỹ năng còn thiếu. Số điện thoại đóng vai trò như một kỹ năng mềm bổ trợ, giúp bạn linh hoạt hơn trong các tình huống xã hội mà không làm mất đi bản sắc gốc.',
        ],
    ],

    // =========================================================
    // 4. JUNG — 12 Nguyên mẫu Hoàng Đạo (Archetypes)
    // =========================================================
    'zodiac_archetypes' => [
        'aries'       => [
            'archetype'      => 'Chiến Binh (The Warrior)',
            'core'           => 'Hành động, chinh phục, không sợ hãi.',
            'resonant_cores' => [1, 5, 9],
            'tension_cores'  => [4, 7]
        ],
        'taurus'      => [
            'archetype'      => 'Người Kiến Tạo (The Creator)',
            'core'           => 'Tích lũy, xây dựng nền tảng vật chất bền vững.',
            'resonant_cores' => [4, 6, 8],
            'tension_cores'  => [1, 5]
        ],
        'gemini'      => [
            'archetype'      => 'Kẻ Biến Hình (The Trickster)',
            'core'           => 'Giao tiếp linh hoạt, tò mò, đa nhân cách.',
            'resonant_cores' => [3, 5, 7],
            'tension_cores'  => [4, 6]
        ],
        'cancer'      => [
            'archetype'      => 'Người Chăm Sóc (The Caregiver)',
            'core'           => 'Bảo vệ, nuôi dưỡng, kết nối bằng cảm xúc.',
            'resonant_cores' => [2, 6, 9],
            'tension_cores'  => [1, 8]
        ],
        'leo'         => [
            'archetype'      => 'Quân Vương (The Ruler)',
            'core'           => 'Tỏa sáng, lãnh đạo, khao khát sự công nhận.',
            'resonant_cores' => [1, 3, 5],
            'tension_cores'  => [2, 7]
        ],
        'virgo'       => [
            'archetype'      => 'Học Giả (The Sage)',
            'core'           => 'Phân tích, chi tiết, theo đuổi sự hoàn mỹ.',
            'resonant_cores' => [4, 6, 8],
            'tension_cores'  => [3, 5]
        ],
        'libra'       => [
            'archetype'      => 'Người Hòa Giải (The Lover)',
            'core'           => 'Cân bằng, thẩm mỹ, duy trì sự hài hòa.',
            'resonant_cores' => [2, 6, 9],
            'tension_cores'  => [1, 8]
        ],
        'scorpio'     => [
            'archetype'      => 'Nhà Giả Kim (The Magician)',
            'core'           => 'Chuyển hóa, tái sinh, đào sâu vào góc khuất.',
            'resonant_cores' => [4, 7, 9],
            'tension_cores'  => [2, 3]
        ],
        'sagittarius' => [
            'archetype'      => 'Kẻ Khám Phá (The Explorer)',
            'core'           => 'Tự do, tìm kiếm chân lý và trải nghiệm mới.',
            'resonant_cores' => [3, 5, 9],
            'tension_cores'  => [4, 6]
        ],
        'capricorn'   => [
            'archetype'      => 'Kiến Trúc Sư (The Builder)',
            'core'           => 'Kỷ luật, cấu trúc, thành tựu xã hội.',
            'resonant_cores' => [4, 8, 22],
            'tension_cores'  => [3, 5]
        ],
        'aquarius'    => [
            'archetype'      => 'Kẻ Nổi Loạn (The Rebel)',
            'core'           => 'Phá vỡ quy tắc, độc đáo, hướng tới viễn cảnh tương lai.',
            'resonant_cores' => [4, 7, 11],
            'tension_cores'  => [2, 6]
        ],
        'pisces'      => [
            'archetype'      => 'Kẻ Mộng Mơ (The Mystic)',
            'core'           => 'Trực giác, thấu cảm, vượt thoát thực tại.',
            'resonant_cores' => [2, 7, 9],
            'tension_cores'  => [1, 8]
        ]
    ],

    // =========================================================
    // 5. TẦNG 3: XÃ HỘI HỌC — Tín hiệu Xã hội & Vô thức Tập thể
    // =========================================================
    'vip_patterns' => [
        'ultra' => [
            'rule'    => 'Lặp ≥ 6 chữ số (0988888888)',
            'signal'  => 'Tuyên ngôn địa vị tối thượng. Số này có giá trị thị trường rất cao, chủ nhân biết rõ điều đó.',
            'persona' => 'Persona được đầu tư lớn nhất — hình ảnh quyền lực, độ tín nhiệm tài chính và thành công là ưu tiên số một trong giao tiếp.',
            'lp_map'  => [
                8  => 'Cộng hưởng hoàn hảo — bản chất và hình ảnh cùng hướng đến quyền năng và thành tựu tuyệt đối.',
                1  => 'Cộng hưởng mạnh — tiên phong cả trong con người lẫn cách định vị cá nhân.',
                22 => 'Tầm nhìn lớn, hình ảnh lớn — nhất quán vĩ mô ở cả hai tầng ý thức và công cụ.',
                2  => 'Đối lập thú vị — bản chất mỏng manh, cần kết nối nhưng đang khoác lên mình tấm áo quyền lực áp đảo.',
                6  => 'Đối lập thú vị — người mang bản chất nuôi dưỡng lại đang chọn mặt nạ của địa vị và sức mạnh.',
                9  => 'Căng thẳng ngầm — sự từ bi, cống hiến đang ẩn sâu dưới một hình ảnh phô trương quyền lực vật chất.'
            ]
        ],
        'high' => [
            'rule'    => 'Lặp 4-5 chữ số hoặc đối xứng toàn bộ',
            'signal'  => 'Sự lựa chọn có chủ đích và đòi hỏi tài chính. Chủ nhân đặc biệt quan tâm đến việc số điện thoại nói lên điều gì.',
            'persona' => 'Hình ảnh cá nhân/doanh nghiệp được bọc lót kỹ lưỡng. Muốn khẳng định vị thế nhưng không phô trương thái quá.',
            'lp_map'  => [
                8  => 'Phản chiếu chân thực tham vọng ổn định và sự chú trọng vào kết quả thực tế.',
                4  => 'Củng cố nền tảng, tạo ra sự tin cậy tuyệt đối trong các tương tác.',
                3  => 'Mặt nạ hoàn hảo để thu hút sự chú ý, phục vụ cho sự lan tỏa danh tiếng cá nhân.',
                7  => 'Mâu thuẫn tinh tế — người thích tĩnh lặng nhưng lại chọn công cụ thu hút ánh nhìn.'
            ]
        ],
        'memorable' => [
            'rule'    => 'Sảnh tiến ≥ 4 số, lặp nhịp nhàng',
            'signal'  => 'Ưu tiên sự dễ tiếp cận, dễ nhớ và thân thiện hơn là khoe khoang địa vị vật chất.',
            'persona' => 'Persona hướng đến sự kết nối mạng lưới rộng. Muốn xóa bỏ khoảng cách với người đối diện.',
            'lp_map'  => [
                3  => 'Bổ trợ xuất sắc cho bản năng giao tiếp rộng mở và khao khát được lắng nghe.',
                5  => 'Thích ứng hoàn hảo với lối sống linh hoạt, kết nối bất chấp ranh giới.',
                6  => 'Củng cố hình ảnh một người luôn sẵn sàng hỗ trợ, dễ gần và đáng tin cậy.',
                1  => 'Đối cực ngầm — bản chất độc lập, độc đoán nhưng lại chọn vỏ bọc thân thiện, dễ gần.'
            ]
        ]
    ],

    'collective_unconscious' => [
        '68' => [
            'label'   => 'Lộc Phát',
            'culture' => 'Mã hóa âm thanh Hán-Việt: Lục Bát → Lộc Phát',
            'signal'  => 'Chủ nhân đang phát tín hiệu văn hóa về thịnh vượng — thuộc về cộng đồng tin vào may mắn.',
            'jung'    => 'Collective Unconscious: Đây không hẳn là mê tín cá nhân, mà là vô thức tập thể của một nền văn hóa Á Đông đang lên tiếng qua lựa chọn này.'
        ],
        '86' => [
            'label'   => 'Phát Lộc',
            'culture' => 'Mã hóa: Bát Lục → Phát Lộc',
            'signal'  => 'Phát tín hiệu ưu tiên hành động tạo ra kết quả vật chất trước khi thụ hưởng.',
            'jung'    => 'Cộng hưởng với vô thức tập thể về chu kỳ nhân quả: Gieo nỗ lực thực thi, gặt hái thành tựu.'
        ],
        '39' => [
            'label'   => 'Thần Tài (Nhỏ)',
            'culture' => 'Tam Cửu: Sinh sôi lâu dài',
            'signal'  => 'Khao khát sự phồn thịnh bền vững — tín hiệu của người làm ăn mong muốn lộc lá hanh thông.',
            'jung'    => 'Niềm tin vào sự bảo hộ vô hình — phần tâm linh của vô thức tập thể luôn tìm kiếm sự che chở.'
        ],
        '79' => [
            'label'   => 'Thần Tài (Lớn)',
            'culture' => 'Thất Cửu: Trí tuệ và Viên mãn',
            'signal'  => 'Tín hiệu của người không chỉ cầu tài lộc mà còn coi trọng kết quả lớn, vững chắc.',
            'jung'    => 'Cộng hưởng với nguyên mẫu Sage (Hiền Triết) kết hợp với mong cầu trần tục trong vô thức tập thể.'
        ],
        '99' => [
            'label'   => 'Trường Cửu',
            'culture' => 'Cửu Cửu: Mãi mãi, trường tồn',
            'signal'  => 'Khao khát sự bền vững và lâu dài — không chỉ là thành công ngắn hạn mà là di sản.',
            'jung'    => 'Phản ánh nỗi khao khát vô thức của nhân loại về sự bất tử và trường tồn với thời gian.'
        ],
        '38' => [
            'label'   => 'Ông Địa (Nhỏ)',
            'culture' => 'Tam Bát: Sinh phát, giữ đất',
            'signal'  => 'Tín hiệu của sự trấn trạch, ưu tiên sự bình an ở nền tảng cốt lõi.',
            'jung'    => 'Vô thức tìm kiếm sự an toàn từ Đất Mẹ (Archetype của sự ổn định vật lý).'
        ],
        '78' => [
            'label'   => 'Ông Địa (Lớn)',
            'culture' => 'Thất Bát: Giữ của cải lớn',
            'signal'  => 'Khẳng định năng lực giữ tài sản, điền sản vững chắc.',
            'jung'    => 'Nhu cầu thiết lập ranh giới lãnh thổ an toàn ở tầng vô thức.'
        ]
    ],

    // =========================================================
    // 6. POPULAR ENDINGS (JUNG + CULTURAL FLAGS)
    // =========================================================
    'endings' => [
        '11' => ['energy' => 1, 'amplify' => 'Song Nhất', 'jung' => 'Năng lượng tiên phong được khuếch đại gấp đôi. Vô thức đang cần khẳng định bản sắc mạnh mẽ, hoặc đang chiến đấu quyết liệt với phần phụ thuộc bị kìm nén.'],
        '22' => ['energy' => 2, 'amplify' => 'Song Nhị', 'jung' => 'Nhu cầu kết nối và hòa hợp được nhân đôi. Trạng thái nội tâm đang khao khát tìm kiếm sự cân bằng và bến đỗ an toàn trong các mối quan hệ.'],
        '33' => ['energy' => 3, 'amplify' => 'Song Tam', 'jung' => 'Tiếng nói sáng tạo đang gào thét. Có điều gì đó bên trong đang bức thiết cần được biểu đạt, nếu không sẽ tìm cách thoát ra theo hướng tiêu cực.'],
        '44' => ['energy' => 4, 'amplify' => 'Song Tứ', 'jung' => 'Nhu cầu kiểm soát được đẩy lên cực đại. Bạn đang dùng kỷ luật thép để thiết lập nền tảng, hoặc dùng nó như lớp khiên để trốn tránh những cảm xúc hỗn loạn.'],
        '55' => ['energy' => 5, 'amplify' => 'Song Ngũ', 'jung' => 'Xung lực tự do đang rất mạnh. Vô thức đang lên dây cót cho quá trình đập bỏ khuôn mẫu. Một sự thay đổi lớn đang hoặc sắp diễn ra.'],
        '66' => ['energy' => 6, 'amplify' => 'Song Lục', 'jung' => 'Năng lượng trách nhiệm được nhân đôi. Có thể bạn đang gánh vác quá nhiều cho người khác, và vô thức nhắc nhở bạn cần học cách nhận lại.'],
        '77' => ['energy' => 7, 'amplify' => 'Song Thất', 'jung' => 'Chiều sâu nội tâm được khuếch đại. Bạn đang ở giai đoạn suy xét, rút lui khỏi đám đông để bắt đầu một hành trình hướng nội đáng kể.'],
        '88' => ['energy' => 8, 'amplify' => 'Song Bát', 'jung' => 'Áp lực thành công và chu kỳ tài chính nhân đôi. Bạn đang chiến đấu với khao khát quyền lực hoặc nỗi sợ thiếu thốn sâu thẳm trong Shadow.'],
        '99' => ['energy' => 9, 'amplify' => 'Song Cửu', 'jung' => 'Năng lượng hoàn kết ở đỉnh. Đang ở cuối một chu kỳ lớn, hoặc đang mang một gánh nặng tâm lý muốn buông bỏ.', 'cultural_ref' => '99'],

        '68' => ['energy' => 5, 'amplify' => 'Lộc Phát', 'jung' => 'Năng lượng tự do (5) và trách nhiệm (6) va chạm. Nội tâm đang tìm cách vật chất hóa và duy trì các ý tưởng sáng tạo.', 'cultural_ref' => '68'],
        '86' => ['energy' => 5, 'amplify' => 'Phát Lộc', 'jung' => 'Khao khát hành động tạo kết quả (8) kết hợp nhu cầu duy trì an toàn (6). Vô thức hướng tới sự ổn định sau khi đã đạt thành tựu.', 'cultural_ref' => '86'],
        '39' => ['energy' => 3, 'amplify' => 'Thần Tài', 'jung' => 'Biểu đạt sáng tạo (3) kết nối với tầm nhìn rộng mở (9). Khao khát kết quả tạo ra tiếng vang và ảnh hưởng lâu dài.', 'cultural_ref' => '39'],
        '79' => ['energy' => 7, 'amplify' => 'Thất Cửu', 'jung' => 'Nội tâm sâu sắc (7) khát khao sự viên mãn (9). Đòi hỏi mọi quyết định phải có ý nghĩa sâu xa thay vì chỉ là bề nổi.', 'cultural_ref' => '79'],
        '38' => ['energy' => 2, 'amplify' => 'Ông Địa', 'jung' => 'Sự nhạy bén (3) bị khóa lại bởi kỷ luật thực tế (8). Vô thức ưu tiên sự phòng thủ và giữ gìn nền móng an toàn.', 'cultural_ref' => '38'],
        '78' => ['energy' => 6, 'amplify' => 'Ông Địa', 'jung' => 'Sự cẩn trọng tuyệt đối (7) đi kèm tham vọng (8). Nhu cầu thiết lập ranh giới bảo vệ chặt chẽ những gì thuộc về mình.', 'cultural_ref' => '78'],

        '19' => ['energy' => 1, 'amplify' => 'Cá nhân – Cộng đồng', 'jung' => 'Cặp Shadow điển hình: Số 1 (cái tôi) và số 9 (hòa tan) giằng co. Nội tâm đang xử lý căng thẳng giữa nhu cầu ích kỷ cá nhân và sự hy sinh cho người khác.'],
        '91' => ['energy' => 1, 'amplify' => 'Cộng đồng – Cá nhân', 'jung' => 'Đang dần lấy lại bản sắc cá nhân, mong muốn khẳng định lại ranh giới cái tôi sau một khoảng thời gian dài sống vì người khác.'],
        '45' => ['energy' => 9, 'amplify' => 'Kiểm soát – Tự do', 'jung' => 'Cặp Shadow cấu trúc (4) và hỗn loạn (5). Nội tâm đang xung đột cực độ giữa việc muốn mọi thứ trong tầm kiểm soát và khao khát được buông xả hoàn toàn.'],

        '123' => ['energy' => 6, 'amplify' => 'Tiến Số', 'jung' => 'Ý thức đang chuyển động theo nhịp tích lũy: Từ hành động (1) qua kết nối (2) đến bộc lộ (3). Giai đoạn phát triển tuần tự lành mạnh.'],
        '789' => ['energy' => 6, 'amplify' => 'Tiến Viên Mãn', 'jung' => 'Từ trí tuệ (7) qua quyền năng (8) đến viên mãn (9). Đang đi qua các tầng trưởng thành sâu sắc nhất của nhận thức.'],
        '321' => ['energy' => 6, 'amplify' => 'Thu Về', 'jung' => 'Quá trình hướng nội: Từ biểu đạt (3) trở về cá nhân (1). Vô thức đang gom năng lượng lại để tự chữa lành hoặc nghỉ ngơi.'],

        '00' => ['energy' => 0, 'amplify' => 'Vô Cực Kép', 'jung' => 'Năng lượng trống — trạng thái trước khi bắt đầu. Tiềm năng chưa được định hình, vô thức đang ở trạng thái chờ đợi để tái thiết lập toàn bộ.'],

        '111' => ['energy' => 3, 'amplify' => 'Tam Nhất', 'jung' => 'Bản sắc cá nhân khuếch đại 3 lần. Đang ở giai đoạn khẳng định mạnh mẽ, hoặc đang dùng nó làm vỏ bọc để chống lại khủng hoảng bản sắc sâu thẳm.'],
        '555' => ['energy' => 6, 'amplify' => 'Tam Ngũ', 'jung' => 'Biến động đỉnh điểm. Một chu kỳ đang sụp đổ để nhường chỗ cho điều hoàn toàn mới. Đây là dấu hiệu Synchronicity mạnh nhất.'],
        '999' => ['energy' => 9, 'amplify' => 'Tam Cửu', 'jung' => 'Hoàn kết tuyệt đối. Một chương lớn của cuộc đời đang khép lại, vô thức đã sẵn sàng dọn dẹp để bước sang một thế giới quan mới.', 'cultural_ref' => '99']
    ],

    // =========================================================
    // 7. FREQUENCY ANALYSIS — Phân tích tần suất chữ số
    // =========================================================
    'digit_energy' => [
        0 => ['name' => 'Vô cực', 'energy' => 'Tiềm năng chưa định hình, khoảng trống, làm lại từ đầu.'],
        1 => ['name' => 'Khởi nguyên', 'energy' => 'Tính độc lập, chủ động khởi tạo và cái tôi mạnh mẽ.'],
        2 => ['name' => 'Đôi kết', 'energy' => 'Sự mềm mỏng, khả năng lắng nghe và nhu cầu kết nối.'],
        3 => ['name' => 'Tam sáng tạo', 'energy' => 'Khả năng ngôn từ, sức biểu đạt và tính hướng ngoại.'],
        4 => ['name' => 'Tứ nền tảng', 'energy' => 'Tính kỷ luật, giới hạn an toàn và tư duy hệ thống.'],
        5 => ['name' => 'Ngũ tự do', 'energy' => 'Tính linh hoạt, sự tò mò và khao khát thay đổi.'],
        6 => ['name' => 'Lục gia đình', 'energy' => 'Trách nhiệm, tính bao bọc và nhu cầu xây dựng tổ ấm.'],
        7 => ['name' => 'Thất trí tuệ', 'energy' => 'Nhu cầu riêng tư, khả năng phân tích và chiều sâu tri thức.'],
        8 => ['name' => 'Bát thịnh vượng', 'energy' => 'Khao khát vật chất, quyền lực và tính thực thi cao.'],
        9 => ['name' => 'Cửu viên mãn', 'energy' => 'Trí tuệ tổng hợp, sự cho đi và cái nhìn vĩ mô.'],
    ],

];