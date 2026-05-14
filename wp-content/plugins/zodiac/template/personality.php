<?php
/**
 * Template: Zodiac Personality Analysis
 * @package Zodiac
 */
if (!defined('ABSPATH')) exit;
?>
<div class="fortune-page" id="zdc-tinh-cach-wrapper">
    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">Personality</div>
        <h1 class="ftn-hero-title">Decode Your <span>Personality</span> by Zodiac Sign</h1>
        <p>Based on your date of birth, the system analyzes your personality, strengths, weaknesses, love life, and career according to your Zodiac Sign and Decan.</p>
    </section>
    <div class="ftn-calc-card zdc-lp-toggle">
        <form id="zdc-form" novalidate>
            <input type="text" id="zdc-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-dob">Date of birth (DD/MM/YYYY)</label>
                    <input type="text" id="zdc-dob" class="ftn-input" placeholder="e.g. 15/12/1999" autocomplete="on">
                    <span class="ftn-error" id="zdc-error-dob"></span>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">Analyze</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Analyzing...</span>
            </button>
        </form>
    </div>
    <div class="ftn-result" id="zdc-result"></div>
</div>
