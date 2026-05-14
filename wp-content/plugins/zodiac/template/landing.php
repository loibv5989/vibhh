<?php
/**
 * Template: Landing Page – Zodiac
 * @package Zodiac
 */
if (!defined('ABSPATH')) exit;

$signs = [
    ['id' => 'aries',       'name' => 'Aries',       'symbol' => '♈', 'date' => '21/3 – 19/4',  'element' => 'Fire',  'keywords' => 'Assertive · Independent · Impulsive'],
    ['id' => 'taurus',      'name' => 'Taurus',      'symbol' => '♉', 'date' => '20/4 – 20/5',  'element' => 'Earth', 'keywords' => 'Persistent · Sensual · Stubborn'],
    ['id' => 'gemini',      'name' => 'Gemini',      'symbol' => '♊', 'date' => '21/5 – 20/6',  'element' => 'Air',   'keywords' => 'Curious · Expressive · Unpredictable'],
    ['id' => 'cancer',      'name' => 'Cancer',      'symbol' => '♋', 'date' => '21/6 – 22/7',  'element' => 'Water', 'keywords' => 'Sensitive · Home-oriented · Moody'],
    ['id' => 'leo',         'name' => 'Leo',         'symbol' => '♌', 'date' => '23/7 – 22/8',  'element' => 'Fire',  'keywords' => 'Proud · Generous · Image-conscious'],
    ['id' => 'virgo',       'name' => 'Virgo',       'symbol' => '♍', 'date' => '23/8 – 22/9',  'element' => 'Earth', 'keywords' => 'Perfectionist · Meticulous · Dedicated'],
    ['id' => 'libra',       'name' => 'Libra',       'symbol' => '♎', 'date' => '23/9 – 22/10', 'element' => 'Air',   'keywords' => 'Diplomatic · Fair-minded · Indecisive'],
    ['id' => 'scorpio',     'name' => 'Scorpio',     'symbol' => '♏', 'date' => '23/10 – 21/11','element' => 'Water', 'keywords' => 'Intense · Perceptive · Guarded'],
    ['id' => 'sagittarius', 'name' => 'Sagittarius', 'symbol' => '♐', 'date' => '22/11 – 21/12','element' => 'Fire',  'keywords' => 'Optimistic · Freedom-loving · Uncommitted'],
    ['id' => 'capricorn',   'name' => 'Capricorn',   'symbol' => '♑', 'date' => '22/12 – 19/1', 'element' => 'Earth', 'keywords' => 'Ambitious · Disciplined · Reserved'],
    ['id' => 'aquarius',    'name' => 'Aquarius',    'symbol' => '♒', 'date' => '20/1 – 18/2',  'element' => 'Air',   'keywords' => 'Innovative · Unconventional · Independent'],
    ['id' => 'pisces',      'name' => 'Pisces',      'symbol' => '♓', 'date' => '19/2 – 20/3',  'element' => 'Water', 'keywords' => 'Dreamy · Romantic · Escapist'],
];

$elementColors = [
    'Fire'  => '#ef4444',
    'Earth' => '#84cc16',
    'Air'   => '#06b6d4',
    'Water' => '#6366f1',
];

$faqs = [
    ['q' => 'How accurate are the results?',
     'a' => 'Results are calculated based on Western astrology, combined with Decan analysis and planetary positions. The content reflects traits and tendencies according to this method — not an absolute verdict on destiny.'],
    ['q' => 'Is this service free?',
     'a' => 'Completely free. We do not charge under any form, do not sell courses, and do not provide paid fortune-telling services.'],
    ['q' => 'Is astrology superstition?',
     'a' => 'Astrology is a symbolic belief system with over 2,500 years of history. We view it as a personal reflection tool — helping you look at yourself and orient your thinking, not as a means of judgment or fate prediction.'],
    ['q' => 'What is a Decan? Why analyze it?',
     'a' => 'Each zodiac sign is divided into 3 Decans (each Decan ~10 days), and each Decan is influenced by a different sub-ruling planet. This explains why two people with the same sign can have quite different personalities.'],
    ['q' => 'What is a Cusp?',
     'a' => 'A cusp is the transitional period between two signs (~5–7 days). People born during this phase carry blended energy from both adjacent signs, creating a more complex and diverse character.'],
    ['q' => 'Is my data saved?',
     'a' => 'Information is processed directly and not stored on our system. No personal data is recorded or used for other purposes.'],
];
?>

<div class="zdc-lp fortune-page" id="zdc-landing-wrapper">
    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">♈ Discover Yourself</div>
        <h1 class="ftn-hero-title">Decode Your <span>Zodiac Sign</span></h1>
        <p>Enter your date of birth to explore your personal star map: personality, strengths, weaknesses, love, and career — analyzed in depth by Sun Sign, Decan, and planetary interactions.</p>
        <div class="zdc-hero-actions">
            <button class="zdc-btn-primary" onclick="document.getElementById('zdc-tools-section').scrollIntoView({behavior:'smooth'})">Explore</button>
            <button class="zdc-btn-ghost" onclick="document.getElementById('zdc-about-section').scrollIntoView({behavior:'smooth'})">Learn more</button>
        </div>
    </section>
    <section id="zdc-about-section" class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <h2 class="zdc-section-title">What is Astrology?</h2>
            <p class="zdc-section-desc">Western astrology is a system that studies the relationship between celestial positions at your birth and your personality traits and behavioral tendencies. Unlike simple horoscopes that only use 12 signs, this system digs deeper into Decans, Cusps, and Numerology to create a highly personalized portrait.</p>
            <div class="zdc-intro-grid">
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">🌟</div>
                        <h3>Sun Sign</h3>
                    </div>
                    <p>Determined by date of birth, it reflects your core identity, how you present yourself to the world, and what you aspire to become.</p>
                </div>
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">🔬</div>
                        <h3>Decan – 10-Day Variation</h3>
                    </div>
                    <p>Each sign is divided into 3 Decans, each influenced by a different sub-ruling planet. This explains why two people with the same sign can differ in personality.</p>
                </div>
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">🌒</div>
                        <h3>Moon & Rising Sign</h3>
                    </div>
                    <p>The Moon represents your inner emotional world, while the Rising Sign (Ascendant) determines how you approach the world and the first impression you leave on others.</p>
                </div>
                <div class="zdc-intro-card">
                    <div class="zdc-intro-card-header">
                        <div class="zdc-intro-icon">⚡</div>
                        <h3>Cusp</h3>
                    </div>
                    <p>If you were born during the transition between two signs, you carry a unique blended energy — more complex but also much more interesting.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <div class="zdc-section-label">12 Zodiac Signs</div>
            <h2 class="zdc-section-title">Which sign are you?</h2>
            <p class="zdc-section-desc">Click on your sign to see birth dates and characteristic energy — or enter your date of birth below for an in-depth analysis.</p>
            <div class="zdc-signs-grid">
                <?php foreach ($signs as $s):
                    $color = $elementColors[$s['element']] ?? '#7c3aed';
                ?>
                <div class="zdc-sign-card" data-element="<?= esc_attr($s['element']) ?>" style="--sign-color:<?= $color ?>">
                    <div class="zdc-sign-symbol"><?= $s['symbol'] ?></div>
                    <div class="zdc-sign-name"><?= esc_html($s['name']) ?></div>
                    <div class="zdc-sign-date"><?= esc_html($s['date']) ?></div>
                    <div class="zdc-sign-element"><?= esc_html($s['element']) ?></div>
                    <div class="zdc-sign-keywords"><?= esc_html($s['keywords']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="zdc-element-legend">
                <span class="zdc-el-badge" style="--el-color:#ef4444">🔥 Fire — Aries · Leo · Sagittarius</span>
                <span class="zdc-el-badge" style="--el-color:#84cc16">🌱 Earth — Taurus · Virgo · Capricorn</span>
                <span class="zdc-el-badge" style="--el-color:#06b6d4">🌬️ Air — Gemini · Libra · Aquarius</span>
                <span class="zdc-el-badge" style="--el-color:#6366f1">💧 Water — Cancer · Scorpio · Pisces</span>
            </div>
        </div>
    </section>
    <section id="zdc-tools-section" class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <div class="zdc-section-label">Tools</div>
            <h2 class="zdc-section-title">Astrology Toolkit</h2>
            <p class="zdc-section-desc">Choose the right approach to decode the messages from the universe tailored for you.</p>
            <div class="zdc-tools-grid">
                <a href="<?= esc_url(home_url('/cung-hoang-dao/tinh-cach/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">🔮</div>
                        <h3>Your True Personality</h3>
                    </div>
                    <p>Based on date of birth → identify sign → analyze personality, strengths, weaknesses, love, and career.</p>
                    <div class="zdc-tool-examples">Aries: dynamic, quick-tempered · Virgo: perfectionist, detail-oriented</div>
                    <span class="zdc-tool-cta">Start →</span>
                </a>

                <a href="<?= esc_url(home_url('/cung-hoang-dao/tinh-yeu/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">💘</div>
                        <h3>Love Match (Compatible – Challenging)</h3>
                    </div>
                    <p>Compare 2 zodiac signs to see emotional compatibility, common ground, and potential conflicts.</p>
                    <div class="zdc-tool-examples">Leo matches Sagittarius · Cancer matches Pisces</div>
                    <span class="zdc-tool-cta">Start →</span>
                </a>

                <a href="<?= esc_url(home_url('/cung-hoang-dao/tu-vi/')) ?>" class="zdc-tool-card">
                    <div class="zdc-tool-header">
                        <div class="zdc-tool-icon">🗓️</div>
                        <h3>Daily Horoscope</h3>
                    </div>
                    <p>Predictions by day / week / month: career, finance, and love — continuously updated according to celestial cycles.</p>
                    <div class="zdc-tool-examples">Daily · Weekly · Monthly forecasts</div>
                    <span class="zdc-tool-cta">Start →</span>
                </a>

            </div>
        </div>
    </section>
    <section class="zdc-lp-section zdc-lp-toggle">
        <div class="zdc-lp-container">
            <h3 class="zdc-section-title">Frequently Asked Questions</h3>
            <div class="zdc-faq-list">
                <?php foreach ($faqs as $faq): ?>
                <div class="zdc-faq-item">
                    <div class="zdc-faq-q">
                        <span><?= esc_html($faq['q']) ?></span>
                        <span class="zdc-faq-chevron">▼</span>
                    </div>
                    <div class="zdc-faq-a"><?= esc_html($faq['a']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>
