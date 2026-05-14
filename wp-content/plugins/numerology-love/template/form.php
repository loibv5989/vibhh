<?php
if (!defined('ABSPATH')) exit;
?>
<div class="tsh-love-page">
    <section class="ftn-hero">
        <div class="ftn-hero-badge">Love Compatibility - Numerology</div>
        <h1 class="ftn-hero-title">Numerology Love <br><span>Are You Two Compatible?</span></h1>
        <p>See how well your life paths and core values align through Life Path Numbers.</p>
    </section>
    <div class="ftn-calc-card">
        <form id="numm-form" novalidate>
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="numm-name1">Full Name (Male)</label>
                    <input type="text" id="numm-name1" class="ftn-input" placeholder="e.g. Ding Yuxi" autocomplete="on">
                    <span class="ftn-error" id="numm-error-name1"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="numm-dob1">Date of Birth (DD/MM/YYYY)</label>
                    <input type="text" id="numm-dob1" class="ftn-input" placeholder="e.g. 20/07/1995" autocomplete="on">
                    <span class="ftn-error" id="numm-error-dob1"></span>
                </div>
            </div>
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="numm-name2">Full Name (Female)</label>
                    <input type="text" id="numm-name2" class="ftn-input" placeholder="e.g. Yu Shuxin" autocomplete="on">
                    <span class="ftn-error" id="numm-error-name2"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="numm-dob2">Date of Birth (DD/MM/YYYY)</label>
                    <input type="text" id="numm-dob2" class="ftn-input" placeholder="e.g. 18/12/1995" autocomplete="on">
                    <span class="ftn-error" id="numm-error-dob2"></span>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="numm-submit-btn">
                <span class="ftn-btn-text">Analyze</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Calculating...</span>
            </button>
        </form>
    </div>
    <div class="ftn-result" id="numm-result"></div>
</div>

