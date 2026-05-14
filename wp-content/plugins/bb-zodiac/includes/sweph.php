<?php
if (!defined('ABSPATH')) exit;

class BbZodiac_Sweph {

    private static array $zodiacSigns = [
        'Bạch Dương', 'Kim Ngưu', 'Song Tử', 'Cự Giải',
        'Sư Tử', 'Xử Nữ', 'Thiên Bình', 'Thiên Yết',
        'Nhân Mã', 'Ma Kết', 'Bảo Bình', 'Song Ngư'
    ];

    private static array $planetNames = [
        0 => ['name' => 'Mặt Trời', 'symbol' => '☀️', 'key' => 'sun'],
        1 => ['name' => 'Mặt Trăng', 'symbol' => '🌙', 'key' => 'moon'],
        2 => ['name' => 'Thủy Tinh', 'symbol' => '☿', 'key' => 'mercury'],
        3 => ['name' => 'Kim Tinh', 'symbol' => '♀', 'key' => 'venus'],
        4 => ['name' => 'Hỏa Tinh', 'symbol' => '♂', 'key' => 'mars'],
        5 => ['name' => 'Mộc Tinh', 'symbol' => '♃', 'key' => 'jupiter'],
        6 => ['name' => 'Thổ Tinh', 'symbol' => '♄', 'key' => 'saturn'],
        7 => ['name' => 'Thiên Vương', 'symbol' => '♅', 'key' => 'uranus'],
        8 => ['name' => 'Hải Vương', 'symbol' => '♆', 'key' => 'neptune'],
        9 => ['name' => 'Diêm Vương', 'symbol' => '♇', 'key' => 'pluto'],
    ];

    private static array $elementMap = [
        'Bạch Dương' => 'Lửa', 'Sư Tử' => 'Lửa', 'Nhân Mã' => 'Lửa',
        'Kim Ngưu' => 'Đất', 'Xử Nữ' => 'Đất', 'Ma Kết' => 'Đất',
        'Song Tử' => 'Khí', 'Thiên Bình' => 'Khí', 'Bảo Bình' => 'Khí',
        'Cự Giải' => 'Nước', 'Thiên Yết' => 'Nước', 'Song Ngư' => 'Nước',
    ];

    private static array $modalityMap = [
        'Bạch Dương' => 'Cardinal', 'Cự Giải' => 'Cardinal', 'Thiên Bình' => 'Cardinal', 'Ma Kết' => 'Cardinal',
        'Kim Ngưu' => 'Fixed', 'Sư Tử' => 'Fixed', 'Thiên Yết' => 'Fixed', 'Bảo Bình' => 'Fixed',
        'Song Tử' => 'Mutable', 'Xử Nữ' => 'Mutable', 'Nhân Mã' => 'Mutable', 'Song Ngư' => 'Mutable',
    ];

    private static array $signKeyMap = [
        'Bạch Dương' => 'aries', 'Kim Ngưu' => 'taurus', 'Song Tử' => 'gemini',
        'Cự Giải' => 'cancer', 'Sư Tử' => 'leo', 'Xử Nữ' => 'virgo',
        'Thiên Bình' => 'libra', 'Thiên Yết' => 'scorpio', 'Nhân Mã' => 'sagittarius',
        'Ma Kết' => 'capricorn', 'Bảo Bình' => 'aquarius', 'Song Ngư' => 'pisces',
    ];

    private static ?array $zodiacData = null;

    private static function getZodiacData(): array {
        if (self::$zodiacData === null) {
            self::$zodiacData = require BB_ZODIAC_PLUGIN_DIR . 'data/zodiac.php';
        }
        return self::$zodiacData;
    }

    private static function getSignInfo(string $sign): array {
        if (empty($sign) || !isset(self::$signKeyMap[$sign])) return [];
        $key = self::$signKeyMap[$sign];
        $data = self::getZodiacData();
        if (!isset($data['signs'][$key])) return [];
        $s = $data['signs'][$key];
        return [
            'ruling_planet' => $s['planet'] ?? '',
            'quality'       => $s['quality'] ?? '',
            'polarity'      => $s['polarity'] ?? '',
            'keywords'      => $s['keywords'] ?? '',
        ];
    }

    private static $cityDb = [
        // ===== VIỆT NAM (34 tỉnh thành từ 12/6/2025) =====
        // 6 Thành phố trực thuộc Trung ương
        'hà nội'           => [21.0285, 105.8542], 'hanoi'           => [21.0285, 105.8542],
        'ha noi'           => [21.0285, 105.8542],
        'hồ chí minh'      => [10.8231, 106.6297], 'ho chi minh'     => [10.8231, 106.6297],
        'hcm'              => [10.8231, 106.6297], 'tphcm'           => [10.8231, 106.6297],
        'tp hcm'           => [10.8231, 106.6297], 'tp.hcm'          => [10.8231, 106.6297],
        'tp. hcm'          => [10.8231, 106.6297], 'tp. hồ chí minh' => [10.8231, 106.6297],
        'tp hồ chí minh'   => [10.8231, 106.6297],
        'sài gòn'          => [10.8231, 106.6297], 'saigon'          => [10.8231, 106.6297],
        'hải phòng'        => [20.8449, 106.6881], 'hai phong'       => [20.8449, 106.6881],
        'đà nẵng'          => [16.0544, 108.2022], 'da nang'         => [16.0544, 108.2022],
        'cần thơ'          => [10.0452, 105.7469], 'can tho'         => [10.0452, 105.7469],
        'huế'              => [16.4637, 107.5909], 'hue'             => [16.4637, 107.5909],

        // 28 Tỉnh
        // Miền Bắc
        'cao bằng'         => [22.6657, 106.2638], 'cao bang'        => [22.6657, 106.2638],
        'lạng sơn'         => [21.8537, 106.7615], 'lang son'        => [21.8537, 106.7615],
        'quảng ninh'       => [21.0064, 107.2925], 'quang ninh'      => [21.0064, 107.2925],
        'tuyên quang'      => [21.8235, 105.2182], 'tuyen quang'     => [21.8235, 105.2182], // Tuyên Quang + Hà Giang
        'lào cai'          => [22.4809, 103.9753], 'lao cai'         => [22.4809, 103.9753], // Lào Cai + Yên Bái, TT: Yên Bái
        'lai châu'         => [22.3964, 103.4319], 'lai chau'        => [22.3964, 103.4319],
        'điện biên'        => [21.3861, 103.0175], 'dien bien'       => [21.3861, 103.0175],
        'điện biên phủ'    => [21.3861, 103.0175], 'dien bien phu'   => [21.3861, 103.0175],
        'sơn la'           => [21.3256, 103.9188], 'son la'          => [21.3256, 103.9188],
        'thái nguyên'      => [21.5942, 105.8482], 'thai nguyen'     => [21.5942, 105.8482], // Thái Nguyên + Bắc Kạn
        'phú thọ'          => [21.4000, 105.2300], 'phu tho'         => [21.4000, 105.2300], // Phú Thọ + Vĩnh Phúc + Hòa Bình
        'việt trì'         => [21.3227, 105.4019], 'viet tri'        => [21.3227, 105.4019], // TT tỉnh Phú Thọ
        'bắc ninh'         => [21.1861, 106.0763], 'bac ninh'        => [21.1861, 106.0763], // Bắc Ninh + Bắc Giang
        'hưng yên'         => [20.6464, 106.0512], 'hung yen'        => [20.6464, 106.0512], // Hưng Yên + Thái Bình
        'ninh bình'        => [20.2539, 105.9749], 'ninh binh'       => [20.2539, 105.9749], // Ninh Bình + Hà Nam + Nam Định
        'thanh hóa'        => [19.8075, 105.7765], 'thanh hoa'       => [19.8075, 105.7765],
        'nghệ an'          => [18.6796, 105.6813], 'nghe an'         => [18.6796, 105.6813],
        'vinh'             => [18.6796, 105.6813], // TT tỉnh Nghệ An
        'hà tĩnh'          => [18.3421, 105.9057], 'ha tinh'         => [18.3421, 105.9057],
        'quảng trị'        => [17.2392, 106.7319], 'quang tri'       => [17.2392, 106.7319], // Quảng Trị + Quảng Bình, TT: Đồng Hới
        'quảng ngãi'       => [15.1194, 108.7922], 'quang ngai'      => [15.1194, 108.7922], // Quảng Ngãi + Kon Tum
        'gia lai'          => [13.9833, 108.0000], 'gia lai'         => [13.9833, 108.0000], // Gia Lai + Bình Định
        'pleiku'           => [13.9833, 108.0000], // TT tỉnh Gia Lai
        'đắk lắk'          => [12.6797, 108.0500], 'dak lak'         => [12.6797, 108.0500], // Đắk Lắk + Phú Yên
        'buôn ma thuột'    => [12.6797, 108.0500], 'buon ma thuot'   => [12.6797, 108.0500],
        'khánh hòa'        => [12.2388, 109.1967], 'khanh hoa'       => [12.2388, 109.1967], // Khánh Hòa + Ninh Thuận
        'nha trang'        => [12.2388, 109.1967], // TT tỉnh Khánh Hòa
        'lâm đồng'         => [11.9404, 108.4583], 'lam dong'        => [11.9404, 108.4583], // Lâm Đồng + Đắk Nông + Bình Thuận
        'đà lạt'           => [11.9404, 108.4583], 'da lat'          => [11.9404, 108.4583],
        'đồng nai'         => [10.9574, 106.8426], 'dong nai'        => [10.9574, 106.8426], // Đồng Nai + Bình Phước
        'biên hòa'         => [10.9574, 106.8426], 'bien hoa'        => [10.9574, 106.8426],
        'vũng tàu'             => [10.3460, 107.0843], 'vung tau'            => [10.3460, 107.0843],
        'bà rịa'               => [10.3460, 107.0843], 'ba ria'              => [10.3460, 107.0843],
        'bà rịa - vũng tàu'   => [10.3460, 107.0843], 'ba ria - vung tau'   => [10.3460, 107.0843],
        'tây ninh'         => [11.3103, 106.0988], 'tay ninh'        => [11.3103, 106.0988], // Tây Ninh + Long An
        'vĩnh long'        => [10.2397, 105.9572], 'vinh long'       => [10.2397, 105.9572], // Vĩnh Long + Bến Tre + Trà Vinh
        'đồng tháp'        => [10.4900, 105.6881], 'dong thap'       => [10.4900, 105.6881], // Đồng Tháp + Tiền Giang
        'mỹ tho'           => [10.3600, 106.3600], 'my tho'          => [10.3600, 106.3600],
        'an giang'         => [10.3862, 105.4348], 'an giang'        => [10.3862, 105.4348], // An Giang + Kiên Giang
        'long xuyên'       => [10.3862, 105.4348], 'long xuyen'      => [10.3862, 105.4348],
        'cà mau'           => [9.1769, 105.1500],  'ca mau'          => [9.1769, 105.1500],  // Cà Mau + Bạc Liêu
        'bạc liêu'         => [9.2850, 105.7278], 'bac lieu'         => [9.2850, 105.7278],
        'sóc trăng'        => [9.6025, 105.9740], 'soc trang'        => [9.6025, 105.9740],
        'hậu giang'        => [9.7579, 105.6413], 'hau giang'        => [9.7579, 105.6413],
        'kiên giang'       => [10.0117, 105.0809], 'kien giang'      => [10.0117, 105.0809],
        'rạch giá'         => [10.0117, 105.0809], 'rach gia'        => [10.0117, 105.0809],
        'bình dương'       => [11.1625, 106.6524], 'binh duong'      => [11.1625, 106.6524],
        'thủ dầu một'      => [11.1625, 106.6524], 'thu dau mot'     => [11.1625, 106.6524],
        'bình phước'       => [11.7511, 106.7234], 'binh phuoc'      => [11.7511, 106.7234],
        'quảng bình'       => [17.4689, 106.5936], 'quang binh'      => [17.4689, 106.5936],
        'đồng hới'         => [17.4689, 106.5936], 'dong hoi'        => [17.4689, 106.5936],
        'quảng nam'        => [15.5394, 108.0191], 'quang nam'       => [15.5394, 108.0191],
        'tam kỳ'           => [15.5394, 108.0191], 'tam ky'          => [15.5394, 108.0191],
        'bình định'        => [13.7830, 109.2197], 'binh dinh'       => [13.7830, 109.2197],
        'quy nhơn'         => [13.7830, 109.2197], 'quy nhon'        => [13.7830, 109.2197],
        'phú yên'          => [13.0882, 109.0929], 'phu yen'         => [13.0882, 109.0929],
        'tuy hòa'          => [13.0882, 109.0929], 'tuy hoa'         => [13.0882, 109.0929],
        'kon tum'          => [14.3497, 108.0005],
        'đắk nông'         => [12.0046, 107.6876], 'dak nong'        => [12.0046, 107.6876],
        'ninh thuận'       => [11.6739, 108.8630], 'ninh thuan'      => [11.6739, 108.8630],
        'phan rang'        => [11.6739, 108.8630],
        'bình thuận'       => [11.0904, 108.0721], 'binh thuan'      => [11.0904, 108.0721],
        'phan thiết'       => [10.9289, 108.1022], 'phan thiet'      => [10.9289, 108.1022],
        'long an'          => [10.5356, 106.4102],
        'tiền giang'       => [10.4493, 106.3420], 'tien giang'      => [10.4493, 106.3420],
        'bến tre'          => [10.2415, 106.3759], 'ben tre'         => [10.2415, 106.3759],
        'trà vinh'         => [9.9350, 106.3452],  'tra vinh'        => [9.9350, 106.3452],
        'vĩnh phúc'        => [21.3609, 105.5474], 'vinh phuc'       => [21.3609, 105.5474],
        'hòa bình'         => [20.8175, 105.3376], 'hoa binh'        => [20.8175, 105.3376],
        'hà nam'           => [20.5835, 105.9228], 'ha nam'          => [20.5835, 105.9228],
        'nam định'         => [20.4200, 106.1683], 'nam dinh'        => [20.4200, 106.1683],
        'thái bình'        => [20.4500, 106.3400], 'thai binh'       => [20.4500, 106.3400],
        'bắc giang'        => [21.2820, 106.1975], 'bac giang'       => [21.2820, 106.1975],
        'bắc kạn'          => [22.1477, 105.8348], 'bac kan'         => [22.1477, 105.8348],
        'yên bái'          => [21.7051, 104.8698], 'yen bai'         => [21.7051, 104.8698],
        'hà giang'         => [22.8333, 104.9833], 'ha giang'        => [22.8333, 104.9833],
    ];

    private static function geocodeCity(string $cityName): array {
        $key = mb_strtolower(trim($cityName));
        $key = preg_replace('/,.*$/', '', $key);
        $key = trim($key);

        if (isset(self::$cityDb[$key])) {
            return ['lat' => self::$cityDb[$key][0], 'lng' => self::$cityDb[$key][1]];
        }

        $cacheKey = 'zdc_geo_' . md5($cityName);
        $cached = get_transient($cacheKey);
        if ($cached !== false) return $cached;

        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($cityName) . "&format=json&limit=1";
        $response = wp_remote_get($url, ['timeout' => 5, 'headers' => ['User-Agent' => 'BbZodiac_Plugin/1.0']]);

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                $result = ['lat' => (float)$data[0]['lat'], 'lng' => (float)$data[0]['lon']];
                set_transient($cacheKey, $result, WEEK_IN_SECONDS);
                return $result;
            }
        }

        return ['lat' => 21.0285, 'lng' => 105.8542];
    }

    private static function formatDegree(float $deg): string {
        $d = floor($deg);
        $m = floor(($deg - $d) * 60);
        $s = round((($deg - $d) * 60 - $m) * 60);
        return "{$d}°{$m}'{$s}\"";
    }

    private static function normalizeDeg(float $deg): float {
        return fmod(fmod($deg, 360) + 360, 360);
    }

    private static function getSignFromDegree(float $deg): string {
        $deg = self::normalizeDeg($deg);
        return self::$zodiacSigns[(int)floor($deg / 30) % 12];
    }

    public static function calculateBig3(string $dob, string $tob, string $pob): array {
        $full = self::calculateNatalChart($dob, $tob, $pob);
        return [
            'sun' => $full['planets']['sun']['sign'],
            'moon' => $full['planets']['moon']['sign'],
            'ascendant' => $full['ascendant']['sign'],
            'is_exact_time' => $full['is_exact_time']
        ];
    }

    public static function calculateNatalChart(string $dob, string $tob, string $pob): array {
        if (!function_exists('swe_calc_ut')) {
            throw new Exception("Thư viện swephp chưa được load vào PHP.");
        }

        swe_set_ephe_path(BB_ZODIAC_PLUGIN_DIR . 'lib/ephe/');
        $geo = self::geocodeCity($pob);

        $cleanDob = str_replace(['/', '.', ' '], '-', $dob);
        $localTime = $cleanDob . ' ' . (!empty($tob) ? $tob : '12:00');
        $wp_timezone = wp_timezone();
        $dt = DateTime::createFromFormat('d-m-Y H:i', $localTime, $wp_timezone);
        if (!$dt) {
            $dt = new DateTime($localTime, $wp_timezone);
        }
        $dt->setTimezone(new DateTimeZone('UTC'));

        $y = (int)$dt->format('Y');
        $m = (int)$dt->format('n');
        $d = (int)$dt->format('j');
        $h = (float)$dt->format('G') + ((float)$dt->format('i') / 60);

        $julianDay = swe_julday($y, $m, $d, $h, 1);

        // Tính 10 hành tinh
        $planets = [];
        foreach (self::$planetNames as $id => $info) {
            $pos = swe_calc_ut($julianDay, $id, 256);
            $deg = isset($pos['xx']) ? $pos['xx'][0] : ($pos[0] ?? 0);
            $deg = self::normalizeDeg($deg);
            $sign = self::getSignFromDegree($deg);
            
            $signInfo = self::getSignInfo($sign);
            $planets[$info['key']] = array_merge([
                'name' => $info['name'],
                'symbol' => $info['symbol'],
                'sign' => $sign,
                'degree' => $deg,
                'degree_formatted' => self::formatDegree(fmod($deg, 30)),
                'element' => $sign && isset(self::$elementMap[$sign]) ? self::$elementMap[$sign] : '',
                'modality' => $sign && isset(self::$modalityMap[$sign]) ? self::$modalityMap[$sign] : ''
            ], $signInfo);
        }

        // Tính 12 Houses (Placidus)
        $houses = swe_houses($julianDay, $geo['lat'], $geo['lng'], 'P');

        // Lấy cusps 12 houses
        $houseCusps = [];
        if (isset($houses['cusps']) && is_array($houses['cusps'])) {
            foreach ($houses['cusps'] as $i => $cusp) {
                $houseCusps[$i] = self::normalizeDeg((float)$cusp);
            }
        } elseif (isset($houses[1]) && is_array($houses[1])) {
            for ($i = 1; $i <= 12; $i++) {
                $houseCusps[$i] = self::normalizeDeg((float)($houses[1][$i - 1] ?? 0));
            }
        }

        // Tính 4 góc chính
        $ascmc = isset($houses['ascmc']) ? $houses['ascmc'] : ($houses[2] ?? []);
        $ascDeg = self::normalizeDeg((float)($ascmc[0] ?? $houseCusps[1] ?? 0));
        $mcDeg  = self::normalizeDeg((float)($ascmc[1] ?? $houseCusps[10] ?? 0));
        $icDeg  = self::normalizeDeg($mcDeg + 180);
        $dscDeg = self::normalizeDeg($ascDeg + 180);

        $ascSign = self::getSignFromDegree($ascDeg);
        $mcSign  = self::getSignFromDegree($mcDeg);
        $icSign  = self::getSignFromDegree($icDeg);
        $dscSign = self::getSignFromDegree($dscDeg);

        $ascSignInfo = self::getSignInfo($ascSign);
        $ascendant = array_merge([
            'sign'             => $ascSign,
            'degree'           => $ascDeg,
            'degree_formatted' => self::formatDegree(fmod($ascDeg, 30)),
            'element'          => isset(self::$elementMap[$ascSign]) ? self::$elementMap[$ascSign] : '',
            'modality'         => isset(self::$modalityMap[$ascSign]) ? self::$modalityMap[$ascSign] : ''
        ], $ascSignInfo);

        $midheaven = [
            'sign'             => $mcSign,
            'degree'           => $mcDeg,
            'degree_formatted' => self::formatDegree(fmod($mcDeg, 30))
        ];

        $imum_coeli = [
            'sign'             => $icSign,
            'degree'           => $icDeg,
            'degree_formatted' => self::formatDegree(fmod($icDeg, 30))
        ];

        $descendant = [
            'sign'             => $dscSign,
            'degree'           => $dscDeg,
            'degree_formatted' => self::formatDegree(fmod($dscDeg, 30))
        ];

        // Build danh sách 12 houses với sign
        $houseList = [];
        for ($i = 1; $i <= 12; $i++) {
            $cuspDeg = $houseCusps[$i] ?? 0.0;
            $houseList[$i] = [
                'cusp_degree'   => $cuspDeg,
                'cusp_formatted'=> self::formatDegree(fmod($cuspDeg, 30)),
                'sign'          => self::getSignFromDegree($cuspDeg),
                'planets'       => []
            ];
        }

        // Phân bổ hành tinh vào house
        if (!empty($houseCusps)) {
            foreach ($planets as $pKey => $planet) {
                $pDeg = $planet['degree'];
                $houseNum = 12;
                for ($i = 1; $i <= 12; $i++) {
                    $next = $i < 12 ? $i + 1 : 1;
                    $cStart = $houseCusps[$i] ?? 0.0;
                    $cEnd   = $houseCusps[$next] ?? 0.0;
                    if ($cEnd < $cStart) {
                        if ($pDeg >= $cStart || $pDeg < $cEnd) { $houseNum = $i; break; }
                    } else {
                        if ($pDeg >= $cStart && $pDeg < $cEnd) { $houseNum = $i; break; }
                    }
                }
                $planets[$pKey]['house'] = $houseNum;
                $houseList[$houseNum]['planets'][] = $planet['name'];
            }
        }

        // Stellium (≥3 hành tinh cùng house) và Empty houses
        $stelliums = [];
        $emptyHouses = [];
        foreach ($houseList as $num => $house) {
            $count = count($house['planets']);
            if ($count >= 3) $stelliums[] = ['house' => $num, 'planets' => $house['planets']];
            if ($count === 0) $emptyHouses[] = $num;
        }

        // Thống kê nguyên tố
        $elementCount = ['Lửa' => 0, 'Đất' => 0, 'Khí' => 0, 'Nước' => 0];
        $modalityCount = ['Cardinal' => 0, 'Fixed' => 0, 'Mutable' => 0];
        
        foreach ($planets as $planet) {
            if (!empty($planet['element']) && isset($elementCount[$planet['element']])) $elementCount[$planet['element']]++;
            if (!empty($planet['modality']) && isset($modalityCount[$planet['modality']])) $modalityCount[$planet['modality']]++;
        }
        if (!empty($ascendant['element']) && isset($elementCount[$ascendant['element']])) $elementCount[$ascendant['element']]++;
        if (!empty($ascendant['modality']) && isset($modalityCount[$ascendant['modality']])) $modalityCount[$ascendant['modality']]++;

        arsort($elementCount);
        arsort($modalityCount);

        // === MỨC 3: ĐIỂM ĐẶC BIỆT ===
        // Chiron (15), Lilith/Black Moon (13), Part of Fortune
        $specialPoints = [];

        $chironPos = swe_calc_ut($julianDay, 15, 256);
        $chironDeg = self::normalizeDeg((float)(isset($chironPos['xx']) ? $chironPos['xx'][0] : ($chironPos[0] ?? 0)));
        $specialPoints['chiron'] = [
            'name' => 'Chiron', 'symbol' => '⚷',
            'sign' => self::getSignFromDegree($chironDeg),
            'degree' => $chironDeg,
            'degree_formatted' => self::formatDegree(fmod($chironDeg, 30))
        ];

        $lilitDeg = 0.0;
        $lilitPos = swe_calc_ut($julianDay, 13, 256);
        $lilitDeg = self::normalizeDeg((float)(isset($lilitPos['xx']) ? $lilitPos['xx'][0] : ($lilitPos[0] ?? 0)));
        $specialPoints['lilith'] = [
            'name' => 'Lilith (Black Moon)', 'symbol' => '⚸',
            'sign' => self::getSignFromDegree($lilitDeg),
            'degree' => $lilitDeg,
            'degree_formatted' => self::formatDegree(fmod($lilitDeg, 30))
        ];

        // Part of Fortune = ASC + Moon - Sun
        $sunDeg  = $planets['sun']['degree']  ?? 0.0;
        $moonDeg = $planets['moon']['degree'] ?? 0.0;
        $pofDeg  = self::normalizeDeg($ascDeg + $moonDeg - $sunDeg);
        $specialPoints['part_of_fortune'] = [
            'name' => 'Part of Fortune', 'symbol' => '⊕',
            'sign' => self::getSignFromDegree($pofDeg),
            'degree' => $pofDeg,
            'degree_formatted' => self::formatDegree(fmod($pofDeg, 30))
        ];

        // === MỨC 3: ASPECTS ===
        $aspectDefs = [
            'Conjunction'  => ['angle' => 0,   'orb' => 8,  'symbol' => '☌', 'nature' => 'neutral'],
            'Opposition'   => ['angle' => 180, 'orb' => 8,  'symbol' => '☍', 'nature' => 'tense'],
            'Trine'        => ['angle' => 120, 'orb' => 8,  'symbol' => '△', 'nature' => 'harmonious'],
            'Square'       => ['angle' => 90,  'orb' => 7,  'symbol' => '□', 'nature' => 'tense'],
            'Sextile'      => ['angle' => 60,  'orb' => 6,  'symbol' => '⚹', 'nature' => 'harmonious'],
            'Quincunx'     => ['angle' => 150, 'orb' => 3,  'symbol' => '⚻', 'nature' => 'tense'],
        ];

        $aspectBodies = $planets;
        $aspectBodies['ascendant'] = array_merge($ascendant, ['name' => 'Ascendant', 'symbol' => 'ASC']);
        $aspectBodies['midheaven'] = array_merge($midheaven, ['name' => 'MC', 'symbol' => 'MC']);

        $aspects = [];
        $bodyKeys = array_keys($aspectBodies);
        for ($i = 0; $i < count($bodyKeys); $i++) {
            for ($j = $i + 1; $j < count($bodyKeys); $j++) {
                $k1 = $bodyKeys[$i]; $k2 = $bodyKeys[$j];
                $d1 = $aspectBodies[$k1]['degree']; $d2 = $aspectBodies[$k2]['degree'];
                $diff = abs($d1 - $d2);
                if ($diff > 180) $diff = 360 - $diff;
                foreach ($aspectDefs as $aspName => $aspDef) {
                    $orb = abs($diff - $aspDef['angle']);
                    if ($orb <= $aspDef['orb']) {
                        $aspects[] = [
                            'planet1'  => $aspectBodies[$k1]['name'],
                            'planet2'  => $aspectBodies[$k2]['name'],
                            'aspect'   => $aspName,
                            'symbol'   => $aspDef['symbol'],
                            'nature'   => $aspDef['nature'],
                            'orb'      => round($orb, 2),
                            'angle'    => round($diff, 2),
                        ];
                        break;
                    }
                }
            }
        }

        // === MỨC 3: PATTERNS ===
        $patterns = [];
        $planetDegrees = array_map(fn($p) => $p['degree'], $planets);
        $planetNames   = array_map(fn($p) => $p['name'], $planets);
        $pKeys = array_keys($planetDegrees);

        // Grand Trine: 3 hành tinh cách nhau ~120° (orb 8°)
        for ($i = 0; $i < count($pKeys); $i++) {
            for ($j = $i + 1; $j < count($pKeys); $j++) {
                for ($k = $j + 1; $k < count($pKeys); $k++) {
                    $d = [$planetDegrees[$pKeys[$i]], $planetDegrees[$pKeys[$j]], $planetDegrees[$pKeys[$k]]];
                    $diffs = [
                        abs($d[0] - $d[1]), abs($d[1] - $d[2]), abs($d[0] - $d[2])
                    ];
                    $diffs = array_map(fn($x) => $x > 180 ? 360 - $x : $x, $diffs);
                    if (max(array_map(fn($x) => abs($x - 120), $diffs)) <= 8) {
                        $patterns[] = [
                            'type'    => 'Grand Trine',
                            'planets' => [$planets[$pKeys[$i]]['name'], $planets[$pKeys[$j]]['name'], $planets[$pKeys[$k]]['name']],
                            'element' => $planets[$pKeys[$i]]['element'] ?? ''
                        ];
                    }
                }
            }
        }

        // T-Square: 2 Opposition + 1 Square vào cả hai
        foreach ($aspects as $asp1) {
            if ($asp1['aspect'] !== 'Opposition') continue;
            foreach ($aspects as $asp2) {
                if ($asp2['aspect'] !== 'Square') continue;
                $apex = null;
                if ($asp2['planet1'] === $asp1['planet1'] && $asp2['planet2'] !== $asp1['planet2']) $apex = $asp2['planet2'];
                elseif ($asp2['planet2'] === $asp1['planet1'] && $asp2['planet1'] !== $asp1['planet2']) $apex = $asp2['planet1'];
                elseif ($asp2['planet1'] === $asp1['planet2'] && $asp2['planet2'] !== $asp1['planet1']) $apex = $asp2['planet2'];
                elseif ($asp2['planet2'] === $asp1['planet2'] && $asp2['planet1'] !== $asp1['planet1']) $apex = $asp2['planet1'];
                if ($apex) {
                    $key = implode('-', array_unique([$asp1['planet1'], $asp1['planet2'], $apex]));
                    $patterns[$key] = ['type' => 'T-Square', 'planets' => [$asp1['planet1'], $asp1['planet2']], 'apex' => $apex];
                }
            }
        }

        // Yod (Finger of God): 2 Sextile + cả 2 Quincunx vào 1 apex
        foreach ($aspects as $asp1) {
            if ($asp1['aspect'] !== 'Sextile') continue;
            foreach ($aspects as $asp2) {
                if ($asp2['aspect'] !== 'Quincunx') continue;
                foreach ($aspects as $asp3) {
                    if ($asp3['aspect'] !== 'Quincunx') continue;
                    if (in_array($asp1['planet1'], [$asp2['planet1'], $asp2['planet2']]) &&
                        in_array($asp1['planet2'], [$asp3['planet1'], $asp3['planet2']])) {
                        $apex2 = $asp2['planet1'] === $asp1['planet1'] ? $asp2['planet2'] : $asp2['planet1'];
                        $apex3 = $asp3['planet1'] === $asp1['planet2'] ? $asp3['planet2'] : $asp3['planet1'];
                        if ($apex2 === $apex3) {
                            $key = implode('-', [$asp1['planet1'], $asp1['planet2'], $apex2]);
                            $patterns[$key] = ['type' => 'Yod', 'planets' => [$asp1['planet1'], $asp1['planet2']], 'apex' => $apex2];
                        }
                    }
                }
            }
        }

        $patterns = array_values($patterns);

        swe_close();

        return [
            'planets'              => $planets,
            'ascendant'            => $ascendant,
            'midheaven'            => $midheaven,
            'imum_coeli'           => $imum_coeli,
            'descendant'           => $descendant,
            'houses'               => $houseList,
            'stelliums'            => $stelliums,
            'empty_houses'         => $emptyHouses,
            'special_points'       => $specialPoints,
            'aspects'              => $aspects,
            'patterns'             => $patterns,
            'element_distribution' => $elementCount,
            'modality_distribution'=> $modalityCount,
            'dominant_element'     => array_key_first($elementCount),
            'dominant_modality'    => array_key_first($modalityCount),
            'is_exact_time'        => !empty($tob)
        ];
    }
}