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
            <div class="hero-badge">✨ Discover your destiny</div>
            <h1 class="ftn-h-title"><span>Personality</span> & <span>Love</span></h1>
            <p>Decode messages from the universe to understand yourself and find the perfect piece for your own journey
                to happiness.</p>
        </section>
        <main class="fortune-main">
            <div class="fortune-tools-container">
                <a href="<?= get_home_url() . '/numerology/' ?>" class="fortune-tool-card">
                    <div class="tool-icon-large">🔢</div>
                    <h3 class="tool-title">Numerology</h3>
                    <p class="tool-description">Decode your destiny number from your name and date of birth</p>
                    <div class="tool-arrow">→</div>
                </a>
                <a href="<?= get_home_url() . '/zodiac/' ?>" class="fortune-tool-card">
                    <div class="tool-icon-large">♈</div>
                    <h3 class="tool-title">12 Zodiac Signs</h3>
                    <p class="tool-description">Explore personality and destiny through your zodiac sign</p>
                    <div class="tool-arrow">→</div>
                </a>
                <a href="<?= get_home_url() . '/tarot-online/' ?>" class="fortune-tool-card">
                    <div class="tool-icon-large">🌙</div>
                    <h3 class="tool-title">Tarot Reading</h3>
                    <p class="tool-description">Receive meaningful messages from 78 mystical Tarot cards</p>
                    <div class="tool-arrow">→</div>
                </a>
                <a href="<?= get_home_url() . '/playing-card-reading/' ?>" class="fortune-tool-card">
                    <div class="tool-icon-large">♠️</div>
                    <h3 class="tool-title">Playing Card Reading</h3>
                    <p class="tool-description">The classic 52-card deck</p>
                    <div class="tool-arrow">→</div>
                </a>
            </div>
        </main>
    </div>
<?php get_footer(); ?>