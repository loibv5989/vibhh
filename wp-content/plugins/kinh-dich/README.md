# Kinh Dịch Quẻ Plugin

Plugin WordPress cung cấp chức năng gieo quẻ và luận giải Kinh Dịch theo hai phương pháp: Mai Hoa Thần Số và Lục Hào.

## Cài đặt

1. Upload thư mục `kinh-dich` vào thư mục `/wp-content/plugins/`
2. Kích hoạt plugin trong trang quản trị WordPress
3. Cấu hình AI Provider trong menu **Kinh Dịch** → **Cấu hình - Kinh Dịch Quẻ**
4. Cấu hình múi giờ (Timezone UTC + 7) VN trong cài đặt WordPress
## Cấu hình AI

Plugin hỗ trợ 3 nhà cung cấp AI:

- **Google Gemini** (mặc định)
- **Groq** 
- **Mistral AI**

### Các bước cấu hình:

1. Vào **Kinh Dịch** → **Cấu hình - Kinh Dịch Quẻ**
2. Chọn AI Provider từ dropdown
3. Nhập API keys (mỗi key trên một dòng)
4. Chọn model phù hợp
5. Nhấn **Test Connection** để kiểm tra kết nối
6. Lưu thay đổi

## Shortcodes

Plugin cung cấp các shortcode sau:

### `[iching_landing]`

Hiển thị trang landing giới thiệu về Kinh Dịch với 4 phương pháp gieo quẻ.

**Sử dụng:**
```html
[iching_landing]
```

### `[iching_maihoa]`

Hiển thị form gieo quẻ theo phương pháp Mai Hoa Thần Số.

**Sử dụng:**
```html
[iching_maihoa]  <!-- Mặc định: Gieo theo thời gian -->
```

**Với tham số tùy chọn:**
```html
[iching_maihoa method="maihoa_time"]    <!-- Gieo theo thời gian động tâm -->
[iching_maihoa method="maihoa_number"]  <!-- Gieo theo con số -->
[iching_maihoa method="maihoa_object"]  <!-- Gieo theo ngoại tượng -->
```

**Lưu ý:** Sử dụng `[iching_maihoa]` cho tất cả các phương pháp Mai Hoa để nhất quán.

### `[iching_luchao]`

Hiển thị form gieo quẻ theo phương pháp Lục Hào (3 đồng xu).

**Sử dụng:**
```html
[iching_luchao]
```

## Các trang mẫu

### Trang Landing

Tạo trang mới và thêm shortcode:

```html
<!-- Page Title: Kinh Dịch Quẻ -->
[iching_landing]
```

### Trang Gieo Quẻ Mai Hoa Theo Thời Gian

```html
<!-- Page Title: Gieo Quẻ Mai Hoa Thời Gian -->
[iching_maihoa]  <!-- Sẽ dùng mặc định maihoa_time -->
```

### Trang Gieo Quẻ Mai Hoa Theo Con Số

```html
<!-- Page Title: Gieo Quẻ Mai Hoa Con Số -->
[iching_maihoa method="maihoa_number"]
```

### Trang Gieo Quẻ Mai Hoa Theo Ngoại Tượng

```html
<!-- Page Title: Gieo Quẻ Mai Hoa Ngoại Tượng -->
[iching_maihoa method="maihoa_object"]
```

### Trang Gieo Quẻ Lục Hào

```html
<!-- Page Title: Gieo Quẻ Lục Hào -->
[iching_luchao]
```

## Cách hoạt động

1. **Người dùng nhập câu hỏi** và chọn phương pháp gieo quẻ
2. **Hệ thống gieo quẻ** dựa trên thời gian hoặc các tham số người dùng nhập
3. **AI phân tích và luận giải** quẻ dựa trên:
   - Câu hỏi của người dùng
   - Thông tin cá nhân (tên, giới tính)
   - Kết quả gieo quẻ (hào động, quẻ bản, quẻ biến)
   - Chủ đề câu hỏi (tình duyên, công việc, sức khỏe, etc.)
4. **Hiển thị kết quả** với luận giải chi tiết từ AI

## Các phương pháp gieo quẻ

### 1. Mai Hoa Thần Số - Thời Gian (maihoa_time)
- Dựa trên thời gian gieo quẻ (năm, tháng, ngày, giờ)
- Sử dụng "giờ động tâm" - khoảnh khắc khởi lên ý niệm muốn hỏi
- Phù hợp cho câu hỏi cần câu trả lời nhanh và chính xác

### 2. Mai Hoa Thần Số - Con Số (maihoa_number)
- Nhập dãy số bất kỳ liên quan đến câu hỏi
- Áp dụng: số seri tiền, biển số xe, số điện thoại, số trang sách
- Công cụ sẽ tự động áp dụng quy tắc Dịch lý để lập quẻ

### 3. Mai Hoa Thần Số - Ngoại Tượng (maihoa_object)
- Mượn sự vật, hiện tượng xung quanh để lập quẻ
- Nhập 2 con số: 
  - Số thứ nhất (Thượng Quái): số lượng vật tĩnh, tiếng động
  - Số thứ hai (Hạ Quái): phương hướng, thời gian
- Đây là phương pháp tinh hoa của Mai Hoa Dịch Số

### 4. Lục Hào Nạp Giáp (luchao)
- Gieo 3 đồng xu 6 lần để tạo 6 hào
- Mỗi hào có thể là dương hoặc âm, động hoặc tĩnh
- Cung cấp luận giải chi tiết với Lục Thân, Dụng Thần
- Phù hợp cho câu hỏi cần phân tích sâu sắc

## Cấu trúc kết quả

Kết quả luận giải bao gồm:

- **Quẻ bản**: Quẻ gốc từ kết quả gieo
- **Hào động**: Các hào đang biến đổi
- **Quẻ biến**: Quẻ sau khi các hào động biến đổi
- **Luận giải AI**: Phân tích chi tiết từ AI về ý nghĩa quẻ

## Tùy chỉnh

### CSS Customization

Plugin sử dụng các class CSS sau để tùy chỉnh giao diện:

```css
.iching-container { /* Container chính */ }
.iching-form { /* Form gieo quẻ */ }
.iching-result { /* Kết quả luận giải */ }
.iching-hexagram { /* Hình ảnh quẻ */ }
.iching-analysis { /* Phần luận giải AI */ }
```

### JavaScript Hooks

Plugin cung cấp các JavaScript events để tùy chỉnh:

```javascript
// Khi gieo quẻ thành công
jQuery(document).on('iching_draw_success', function(event, data) {
    console.log('Draw result:', data);
});

// Khi AI phân tích xong
jQuery(document).on('iching_analyze_success', function(event, data) {
    console.log('Analysis result:', data);
});
```

## API Keys

### Google Gemini
1. Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Tạo API key mới
3. Copy và paste vào setting

### Groq
1. Truy cập [Groq Console](https://console.groq.com/keys)
2. Tạo API key mới
3. Copy và paste vào setting

### Mistral AI
1. Truy cập [Mistral AI Console](https://console.mistral.ai/)
2. Tạo API key mới
3. Copy và paste vào setting

## Troubleshooting

### AI không phản hồi
- Kiểm tra API key có hợp lệ không
- Đảm bảo có đủ quota/quota limit
- Test connection trong admin settings

### Lỗi gieo quẻ
- Kiểm tra JavaScript console
- Đảm bảo không có plugin nào xung đột
- Verify PHP version >= 7.4

### Performance
- Sử dụng caching plugin
- Optimize images nếu có
- Consider CDN cho static assets

## Support

Nếu gặp vấn đề, vui lòng:

1. Kiểm tra WordPress Requirements:
   - PHP 7.4+
   - WordPress 5.0+
   - curl extension enabled

2. Kiểm tra plugin conflicts
3. Test với different AI providers
4. Contact support với:
   - WordPress version
   - PHP version
   - Error logs
   - Steps to reproduce

## Changelog

### Version 26.21.03 (Ngày 21/3/2026)
- Tích hợp AI providers (Gemini, Groq, Mistral)
- Admin interface cho AI settings
- Multi-API key rotation
- Connection testing functionality
- Error handling và admin notifications

## License

Plugin này được phát triển bởi loibv và được bảo vệ bởi bản quyền.

Note, nên cấu hình thêm: 
Nginx:
fastcgi_read_timeout 240;
proxy_read_timeout 240s;

PHP:
max_execution_time = 300
max_input_time = 600
