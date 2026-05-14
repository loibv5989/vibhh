# Bói Bài - Plugin Tarot WordPress

Plugin bói bài Tarot online với AI phân tích chi tiết.

## Cấu trúc Plugin

```
boi-bai/
├── admin/
│   ├── admin.php
│   └── assets/
│       ├── css/
│       │   └── boi-bai.css
│       └── js/
│           └── boi-bai.js
├── assets/
│   ├── boi-bai.css
│   └── boi-bai.js
├── includes/
│   ├── calc.php          # Tính toán và xử lý bài
│   ├── data.php          # Dữ liệu 78 lá bài Tarot
│   ├── handle.php        # AJAX handlers
│   ├── helpers.php       # Helper functions
│   ├── prompt.php        # AI prompt builders
│   ├── render-helper.php # Render functions
│   └── spreads.php       # Cấu hình các kiểu trải bài
├── template/
│   ├── landing.php       # Trang chủ hub
│   └── render.php        # Template trải bài
├── boi-bai.php          # Main plugin file
└── README.md

```

## Tính năng

- 78 lá bài Tarot đầy đủ (Major + Minor Arcana)
- 4 kiểu trải bài: 3 lá, 5 lá, 7 lá, 10 lá (Celtic Cross)
- AI phân tích chi tiết với Gemini/Groq/Mistral
- Cache kết quả để tối ưu hiệu suất
- Responsive design
- Honeypot và rate limiting bảo mật

## Shortcode

```php
[boi_bai_form mode="hub"]           // Trang chủ
[boi_bai_form mode="topic" spread="3_cards"]   // Trải bài theo chủ đề
[boi_bai_form mode="question" spread="5_cards"] // Trải bài theo câu hỏi
```

## Yêu cầu

- WordPress 5.0+
- PHP 7.4+
- Plugin Fortune Tools (để sử dụng AI providers)

## Cài đặt

1. Upload thư mục `boi-bai` vào `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress admin
3. Sử dụng shortcode `[boi_bai_form]` trong trang/bài viết

## Phát triển

Plugin được phát triển dựa trên cấu trúc tương tự plugin Thần Số Học, với các thành phần:

- **includes/**: Core logic và xử lý dữ liệu
- **template/**: Giao diện người dùng
- **assets/**: CSS và JavaScript
- **admin/**: Quản trị (placeholder cho tương lai)

## License

Proprietary - NBBLO.com
