<?php
if (!defined('ABSPATH')) exit;
?>
<div class="fortune-page" id="zdc-tinh-yeu-wrapper">

    <section class="ftn-hero">
        <div class="ftn-hero-badge">Star Sign Compatibility</div>
        <h1 class="ftn-hero-title">Love Match <span>12 Zodiac Signs</span></h1>
        <p>Enter your info and your partner's to let the system automatically find your zodiac signs and discover your natural attraction.</p>
    </section>

    <div class="ftn-calc-card">
        <form id="zdc-love-form" novalidate>
            <input type="text" id="zdc-love-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-love-name1">Full name (Male)</label>
                    <input type="text" id="zdc-love-name1" class="ftn-input" placeholder="e.g. John" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-name1"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-love-dob1">Date of birth (d/m/y)</label>
                    <input type="text" id="zdc-love-dob1" class="ftn-input" placeholder="e.g. 15/04/1999" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-dob1"></span>
                </div>
            </div>
            <div class="ftn-form-row" style="margin-top:15px;">
                <div class="ftn-form-group">
                    <label for="zdc-love-name2">Full name (Female)</label>
                    <input type="text" id="zdc-love-name2" class="ftn-input" placeholder="e.g. Sarah" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-name2"></span>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-love-dob2">Date of birth (d/m/y)</label>
                    <input type="text" id="zdc-love-dob2" class="ftn-input" placeholder="e.g. 20/10/2000" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-love-dob2"></span>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-love-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">Analyze</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Analyzing...</span>
            </button>
        </form>
    </div>

    <div class="ftn-result" id="zdc-love-result"></div>

    <div class="ftn-btn-right">
        <span class="ftn-btn-reset zdc-love-btn-reset" style="display:none;">← Back</span>
    </div>

</div>
