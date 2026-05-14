<?php
/**
 * Template Name: Fortune Tools
 * Template Post Type: page
 *  wp-content/plugins/fortune-tools/templates/ftn-layout.php
 */
if (!defined('ABSPATH')) exit;

get_header();
?>
<div class="fortune-page">
    <section class="fortune-hero">
        <div class="hero-badge">✨ Khám phá vận mệnh của bạn</div>
        <h1 class="ftn-h-title"><span>Vận Mệnh</span> & <span> Tình Yêu</span></h1>
        <p>Giải mã những thông điệp từ vũ trụ để thấu hiểu bản thân và tìm thấy mảnh ghép hoàn hảo cho hành trình
            hạnh phúc của riêng bạn.</p>
    </section>
    <main class="fortune-main">
        <div class="fortune-tools-container">
            <a href="<?= get_home_url() . '/than-so-hoc/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">🔢</div>
                <h3 class="tool-title">Thần số học</h3>
                <p class="tool-description">Giải mã con số định mệnh từ tên và ngày sinh của bạn</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/bat-tu/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">☯️</div>
                <h3 class="tool-title">Bát tự</h3>
                <p class="tool-description">Luận giải mệnh cục theo Can Chi, Ngũ hành và Thập thần</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/cung-hoang-dao/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">♈</div>
                <h3 class="tool-title">12 Cung hoàng đạo</h3>
                <p class="tool-description">Khám phá tính cách và vận mệnh qua cung hoàng đạo</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/tu-vi-12-con-giap/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">♊</div>
                <h3 class="tool-title">Tử vi 12 con giáp</h3>
                <p class="tool-description">Tử vi phương Đông kết hợp chiêm tinh phương Tây</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/tarot-online/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">🌙</div>
                <h3 class="tool-title">Bói bài Tarot</h3>
                <p class="tool-description">Nhận thông điệp sâu sắc từ 78 lá bài Tarot huyền bí</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/boi-bai-tay/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">♠️</div>
                <h3 class="tool-title">Bói bài Tây</h3>
                <p class="tool-description">Bộ 52 lá bài truyền thống</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/oracle-cards-online/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">🌸</div>
                <h3 class="fortune-tool-name">Oracle Cards</h3>
                <p class="fortune-tool-desc">Thông điệp từ bộ bài oracle huyền bí</p>
            </a>
            <a href="<?= get_home_url() . '/que-kinh-dich/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">🪙</div>
                <h3 class="tool-title">Quẻ Kinh Dịch</h3>
                <p class="tool-description">Gieo quẻ Kim Tiền - Lục Hào theo Thiên thời</p>
                <div class="tool-arrow">→</div>
            </a>
            <a href="<?= get_home_url() . '/trac-nghiem-tinh-cach/' ?>" class="fortune-tool-card">
                <div class="tool-icon-large">📝</div>
                <h3 class="tool-title">Trắc nghiệm tính cách</h3>
                <p class="tool-description">Bài trắc nghiệm MBTI: Tìm hiểu bản thân trong 5 phút</p>
                <div class="tool-arrow">→</div>
            </a>
        </div>
    </main>
</div>
<?php get_footer(); ?>