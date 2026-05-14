<?php
if (!defined('ABSPATH')) exit;
?>

<div class="nrgy-page nrgy-landing" id="nrgy-landing-wrapper">
    <section class="nrgy-hero nrgy-toggle-content">
        <div class="nrgy-hero-svg-wrap">
            <svg width="100%" viewBox="0 0 680 400" preserveAspectRatio="xMidYMid slice">
                <use href="#svg-hero-illu"></use>
            </svg>
        </div>
        <div class="nrgy-hero-content">
            <div class="nrgy-hero-badge">✦ Pythagorean Numerology</div>
            <h1 class="nrgy-hero-title">Decode Your<br> <span>Numerology</span> and <span>Love</span><br> Compatibility</h1>
            <p>Discover your numerology map to understand personality, tendencies, and life direction. Analyze love compatibility between you and your partner.</p>
            <div class="hero-actions">
                <button class="btn-primary" onclick="document.getElementById('nrgy-tools-section').scrollIntoView({behavior:'smooth'})">Explore</button>
                <button class="btn-ghost" onclick="document.getElementById('lp-mean-section').scrollIntoView({behavior:'smooth'})">Learn More</button>
            </div>
        </div>
    </section>

    <section class="lp-section nrgy-toggle-content">
        <div class="lp-container">
            <h2 class="section-title">What is Numerology?</h2>
            <p class="section-desc">Numerology is a spiritual belief system in which numbers hold special meaning and can reflect or influence a person's life, personality, and destiny.</p>

            <div class="intro-grid">
                <div class="intro-card">
                    <h3>Principles</h3>
                    <p>Your full name and date of birth are converted into numbers using a fixed mapping system. Each number corresponds to a group of traits, forming indices used as tools for self-discovery.</p>
                </div>

                <div class="intro-card">
                    <h3>Relationship Compatibility</h3>
                    <p>In love, Numerology compares core indices (Life Path, Soul Urge, Destiny) between two people to assess energy frequency harmony. It reveals resonant points and areas requiring effort to maintain a lasting relationship.</p>
                </div>

                <div class="nrgy-sv-sc">
                    <div class="nrgy-sv-card nrgy-sv-card1">
                        <div class="nrgy-sv-icon">▲</div>
                        <div class="nrgy-sv-title">Life Path</div>
                        <div class="nrgy-sv-desc">Path of life</div>
                    </div>
                    <div class="nrgy-sv-card nrgy-sv-card2">
                        <div class="nrgy-sv-icon">❤</div>
                        <div class="nrgy-sv-title">Soul Urge</div>
                        <div class="nrgy-sv-desc">Deep desire</div>
                    </div>
                    <div class="nrgy-sv-card nrgy-sv-card3">
                        <div class="nrgy-sv-icon">⌖</div>
                        <div class="nrgy-sv-title">Destiny</div>
                        <div class="nrgy-sv-desc">Role to fulfill</div>
                    </div>
                    <div class="nrgy-sv-card nrgy-sv-card4">
                        <div class="nrgy-sv-icon">☺</div>
                        <div class="nrgy-sv-title">Personality</div>
                        <div class="nrgy-sv-desc">Outward image</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="lp-section nrgy-toggle-content" id="lp-mean-section">
        <div class="lp-container">
            <div class="section-label">Meaning of Numbers</div>
            <h2 class="section-title">Discover Core Number Energies</h2>
            <p class="section-desc">Each core number in the Pythagorean system reveals core energy, personality, natural strengths, and lessons you need to complete in life.</p>

            <div class="nrgy-meaning-nav">
                <?php foreach([1,2,3,4,5,6,7,8,9,11,22,33] as $n): ?>
                    <button class="nrgy-num-tab <?php echo $n === 1 ? 'active' : ''; ?>" onclick="nrgySwitchNum(<?php echo $n; ?>, this)">
                        <?php echo $n; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="nrgy-meaning-contents">
                <div class="nrgy-num-content active" id="nrgy-mean-1">
                    <h3>Numerology Number 1 – The Pioneer</h3>
                    <p>People with number 1 tend to be independent, proactive, and enjoy leading. You fit roles requiring quick decisions, responsibility, and creating new directions.<br><strong>Point to note:</strong> Sometimes you can become too stubborn or difficult to cooperate with if you don't manage your ego well.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-2">
                    <h3>Numerology Number 2 – The Connector</h3>
                    <p>Number 2 leans toward sensitivity, good listening, and easy cooperation. You fit environments requiring teamwork, support, and maintaining stability.<br><strong>Note:</strong> You may be easily affected by emotions or dependent on others if lacking confidence.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-3">
                    <h3>Numerology Number 3 – The Expresser</h3>
                    <p>You have good communication skills, creativity, and easily convey emotions. Jobs related to art, content, or social communication will suit you.<br><strong>Limitation:</strong> Prone to scattered energy, lacking persistence without clear goals.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-4">
                    <h3>Numerology Number 4 – The Builder</h3>
                    <p>Number 4 represents discipline, practicality, and reliability. You work with plans, fitting environments requiring stability and longevity.<br><strong>Note:</strong> Can be too rigid, resistant to change, or lacking flexibility.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-5">
                    <h3>Numerology Number 5 – The Experiencer</h3>
                    <p>You love freedom, exploration, and change. Good adaptability helps you fit dynamic environments.<br><strong>Limitation:</strong> You get bored easily, lacking stability without clear direction.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-6">
                    <h3>Numerology Number 6 – The Responsible One</h3>
                    <p>Number 6 is often associated with family, care, and high responsibility. You are trustworthy in relationships.<br><strong>Balance:</strong> Avoid overburdening or sacrificing too much for others.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-7">
                    <h3>Numerology Number 7 – The Analyst</h3>
                    <p>You tend to think deeply, enjoy understanding the essence of issues. Suitable for research, academia, or work requiring logical thinking.<br><strong>Limitation:</strong> You can become withdrawn or difficult to share with others.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-8">
                    <h3>Numerology Number 8 – The Achievement Oriented</h3>
                    <p>Number 8 is associated with goals, finance, and management. You have organizational ability and pursue material success.<br><strong>Note:</strong> Prone to achievement pressure or overemphasizing material factors.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-9">
                    <h3>Numerology Number 9 – The Community Oriented</h3>
                    <p>You tend to care about society, enjoy helping others, and have clear life ideals.<br><strong>Weakness:</strong> Prone to sentimentality or placing too high expectations on others.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-11">
                    <h3>Numerology Number 11/2 – Sensitive & Intuitive</h3>
                    <p>You possess extraordinary intuition and high sensitivity to your surroundings. Suitable for roles requiring deep understanding of people.<br><strong>Advice:</strong> Learn to maintain emotional balance to avoid energy overload.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-22">
                    <h3>Numerology Number 22/4 – Large Scale Execution</h3>
                    <p>You have the ability to turn macro ideas into specific, practical plans. Suitable for large, long-term projects.<br><strong>Note:</strong> Avoid setting too extreme goals leading to pressure or giving up halfway.</p>
                </div>
                <div class="nrgy-num-content" id="nrgy-mean-33">
                    <h3>Numerology Number 33/6 – The Servant</h3>
                    <p>Number 33 is seen as the number of the "Master Healer", associated with boundless compassion and dedication to community. You tend to devote your heart to helping and supporting others, especially in education, therapy, or social support fields.<br><strong>Point to note:</strong> You easily set too high standards for yourself or sacrifice too much leading to exhaustion. Learn to love yourself before serving humanity.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="nrgy-tools-section" class="lp-section">
        <div class="lp-container">
            <div class="nrgy-h-fo">
                <h2 class="section-title">Find answers for yourself?</h2>
            </div>
            <div class="nrgy-tools-grid">
                <div class="nrgy-tool-card">
                    <div class="nrgy-tool-icon">🔮</div>
                    <h3>Personal Numerology Lookup</h3>
                    <p>Decode your personal numerology map to understand personality, tendencies, and life direction.</p>
                    <a href="/numerology/personal/" class="nrgy-tool-btn">Start</a>
                </div>
                <div class="nrgy-tool-card">
                    <div class="nrgy-tool-icon">💗</div>
                    <h3>Love Numerology Compatibility</h3>
                    <p>Analyze compatibility and energy between two people to understand your relationship.</p>
                    <a href="/numerology/boi-tinh-yeu/" class="nrgy-tool-btn">Start</a>
                </div>
            </div>
        </div>
    </section>

    <section class="lp-section nrgy-toggle-content">
        <div class="lp-container">
            <h3 class="section-title">Frequently Asked Questions</h3>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-q">
                        <span>Why doesn't my result have a Life Path number of 10?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">According to the international Pythagorean standard, all numbers are reduced to 1 through 9 (except Master numbers 11, 22, 33). Therefore, 10 is calculated as 1 (1+0=1), instead of being kept as a variant number.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-q">
                        <span>How accurate are the results?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">Results are calculated based on a fixed conversion system from full name and date of birth. The content reflects common traits and tendencies according to this method, but is not an absolute confirmation and does not replace personal decisions.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-q">
                        <span>Is there a fee to use this?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">Completely free. We do not charge in any form, do not sell courses, and do not provide paid fortune-telling services.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-q">
                        <span>Is numerology superstition?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">Numerology is a belief system with a history of over 4,000 years. It is not empirical science, nor is it superstition in the sense of worshiping or depending on supernatural forces. We view it as a personal contemplation tool — helping you reflect on yourself and orient your thinking, not as a means of judgment or fate prediction.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-q">
                        <span>Do I need to enter my full name?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">It is recommended to use your full name as on your birth certificate to ensure consistent number conversion. Missing or changing names may lead to differences in analysis results.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-q">
                        <span>Is my data saved?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">Information is processed directly in your browser and is not stored on our system. No personal data is recorded or used for other purposes.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-q">
                        <span>Why is my result different from other websites?</span>
                        <span class="faq-chevron">▼</span>
                    </div>
                    <div class="faq-a">Different websites may apply different conversion methods or interpretation systems. There is no single global standard for numerology, so differences between sources are normal.</div>
                </div>
            </div>
        </div>
    </section>
</div>
