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
            <div class="nrgy-hero-badge">🔮 Decode Yourself</div>
            <h1 class="nrgy-hero-title">Your Personal <span>Numerology</span><br>Map</h1>
            <p>Discover your numerology map to understand personality, tendencies, and life direction.</p>
        </div>
    </section>
    <section class="lp-section">
        <div class="lp-container">
            <div class="nrgy-calc-card">
                <form id="nrgy-form" novalidate>
                    <div class="nrgy-form-row">
                        <div class="nrgy-form-group">
                            <label for="nrgy-name">Full Name</label>
                            <input type="text" id="nrgy-name" class="nrgy-input" placeholder="e.g. Hirai Momo" autocomplete="on">
                            <span class="nrgy-error" id="nrgy-error-name"></span>
                        </div>
                        <div class="nrgy-form-group">
                            <label for="nrgy-dob">Date of Birth <span style="font-weight:400;opacity:.7">(YYYY-MM-DD)</span></label>
                            <input type="text" id="nrgy-dob" class="nrgy-input" placeholder="e.g. 1996-09-11" maxlength="10"  autocomplete="on">
                            <span class="nrgy-error" id="nrgy-error-dob"></span>
                        </div>
                    </div>
                    <button type="submit" class="nrgy-btn-submit" id="nrgy-submit-btn">
                        <span class="nrgy-btn-text">Analyze</span>
                        <span class="nrgy-btn-loading">
                        <span class="nrgy-spinner"></span> Analyzing...
                    </span>
                    </button>
                </form>
            </div>
            <div class="nrgy-result" id="nrgy-result"></div>
        </div>
    </section>
</div>
