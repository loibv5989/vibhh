# Swiss Ephemeris WordPress Plugin

Plugin tính toán thiên văn học chính xác cao cho WordPress, sử dụng thư viện Swiss Ephemeris (Astrodienst).

---

## Yêu cầu hệ thống

- WordPress 5.0+
- PHP 8.0+ (khuyến nghị PHP 8.2)
- VPS hoặc Dedicated Server có quyền root
- Hệ điều hành Linux (Ubuntu/Debian)

---

## Cài đặt

### Bước 1: Cài đặt PHP Extension `php-sweph`

> ⚠️ Bước này yêu cầu quyền `root` trên server. **Không thể thực hiện trên Shared Hosting.**

```bash
# Cài các gói phụ thuộc
sudo apt-get update
sudo apt-get install php-dev gcc make autoconf libc-dev pkg-config

# Clone mã nguồn
git clone https://github.com/cyjoelchen/php-sweph.git
cd php-sweph

# Compile và cài vào đúng phiên bản PHP đang chạy (ví dụ PHP 8.2)
phpize8.5
./configure --with-php-config=/usr/bin/php-config8.5
make
sudo make install
```

### Bước 2: Kích hoạt Extension

Thêm dòng sau vào **cả hai** file `php.ini`:

```bash
# FPM (bắt buộc - dùng cho web/WordPress)
sudo nano /etc/php/8.2/fpm/php.ini

# CLI (tuỳ chọn - dùng cho debug qua terminal)
sudo nano /etc/php/8.2/cli/php.ini
```

Nội dung cần thêm vào cuối file:

```ini
extension=swephp.so
```

> 📝 Lưu ý: tên file là `swephp.so` (có chữ **p**), không phải `sweph.so`.

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

Kiểm tra đã load thành công chưa:

```bash
php-fpm8.2 -m | grep sweph
# Kết quả mong đợi: swephp
```

### Bước 3: Tải Dữ liệu Thiên văn (Ephemeris Data)

Swiss Ephemeris tách biệt giữa thuật toán và dữ liệu. Cần tải thêm các file `.se1` để tính toán chính xác.

```bash
# Tạo thư mục chứa data
sudo mkdir -p /var/www/swisseph/ephe
cd /var/www/swisseph/ephe

# Tải 3 file cơ bản (phủ năm 1800–2399)
wget https://github.com/aloistr/swisseph/raw/master/ephe/sepl_18.se1
wget https://github.com/aloistr/swisseph/raw/master/ephe/semo_18.se1
wget https://github.com/aloistr/swisseph/raw/master/ephe/seas_18.se1
```

| File | Nội dung |
|---|---|
| `sepl_18.se1` | Các hành tinh (Sun, Mercury → Pluto) |
| `semo_18.se1` | Mặt Trăng |
| `seas_18.se1` | Tiểu hành tinh (Chiron, Ceres...) |

### Bước 4: Cài đặt Plugin WordPress

1. Upload thư mục plugin vào `/wp-content/plugins/`
2. Vào **WordPress Admin → Plugins → Installed Plugins**
3. Kích hoạt plugin
4. Vào **Settings** của plugin, điền đường dẫn đến thư mục ephemeris data (mặc định: `/var/www/swisseph/ephe`)

---

## Cấu hình trong code

Trước khi gọi bất kỳ hàm tính toán nào, cần khai báo đường dẫn data:

```php
swe_set_ephe_path(BB_ZODIAC_PLUGIN_DIR . 'lib/ephe/');
```

---

## Hạn chế quan trọng

> ❌ **Plugin này KHÔNG hoạt động trên Shared Hosting** (Hostinger, Nhân Hòa, AZDIGI... loại gói thông thường).
>
> Lý do: `php-sweph` là C-extension cần được biên dịch và cài vào PHP ở cấp hệ thống. Shared Hosting không cấp quyền này.

| Môi trường | Hỗ trợ |
|---|---|
| Shared Hosting | ❌ Không |
| Managed WordPress Hosting | ❌ Không |
| VPS (tự quản lý) | ✅ Có |
| Dedicated Server | ✅ Có |
| Docker (tự build image) | ✅ Có |

---

## Cập nhật Extension

Chỉ cần compile lại khi:
- Nâng cấp phiên bản PHP (vd: 8.2 → 8.3)
- Chuyển sang máy chủ mới
- Cập nhật `php-sweph` lên version mới

---

## Tài liệu tham khảo

- [Swiss Ephemeris - Astrodienst](https://www.astro.com/swisseph/)
- [php-sweph GitHub](https://github.com/cyjoelchen/php-sweph)
- [Swiss Ephemeris Data Files](https://github.com/aloistr/swisseph/tree/master/ephe)