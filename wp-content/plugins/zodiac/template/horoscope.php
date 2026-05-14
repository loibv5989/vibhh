<?php
if (!defined('ABSPATH')) exit;

$signs = [
        'aries'       => '♈ Aries (21/3 - 19/4)',
        'taurus'      => '♉ Taurus (20/4 - 20/5)',
        'gemini'      => '♊ Gemini (21/5 - 20/6)',
        'cancer'      => '♋ Cancer (21/6 - 22/7)',
        'leo'         => '♌ Leo (23/7 - 22/8)',
        'virgo'       => '♍ Virgo (23/8 - 22/9)',
        'libra'       => '♎ Libra (23/9 - 22/10)',
        'scorpio'     => '♏ Scorpio (23/10 - 21/11)',
        'sagittarius' => '♐ Sagittarius (22/11 - 21/12)',
        'capricorn'   => '♑ Capricorn (22/12 - 19/1)',
        'aquarius'    => '♒ Aquarius (20/1 - 18/2)',
        'pisces'      => '♓ Pisces (19/2 - 20/3)'
];
?>
<div class="fortune-page" id="zdc-tu-vi-wrapper">

    <section class="ftn-hero zdc-lp-toggle">
        <div class="ftn-hero-badge">Horoscope</div>
        <h1 class="ftn-hero-title">Daily <span>Horoscope</span></h1>
        <p>Predictions by day / week / month: career, finance, and love based on your zodiac sign.</p>
    </section>
    <div class="ftn-calc-card zdc-lp-toggle">
        <form id="zdc-tuvi-form" novalidate>
            <input type="text" id="zdc-tuvi-cbsp" name="zdc_cbsp" class="zdc-decoy-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="ftn-form-row">
                <div class="ftn-form-group">
                    <label for="zdc-tuvi-sign">Your zodiac sign</label>
                    <select id="zdc-tuvi-sign" class="ftn-input">
                        <option value="" disabled selected>-- Select zodiac sign --</option>
                        <?php foreach ($signs as $id => $label): ?>
                            <option value="<?= esc_attr($id) ?>"><?= esc_html($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ftn-form-group">
                    <label for="zdc-tuvi-period">View forecast for</label>
                    <select id="zdc-tuvi-period" class="ftn-input">
                        <option value="daily">Today</option>
                        <option value="weekly">This week</option>
                        <option value="monthly">This month</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="ftn-btn-submit" id="zdc-tuvi-submit-btn" style="width:100%;margin-top:20px">
                <span class="ftn-btn-text">View horoscope</span>
                <span class="ftn-btn-loading"><span class="ftn-spinner"></span> Connecting...</span>
            </button>
            <p id="zdc-error-tuvi" style="color:var(--lbv-color-2); text-align:center; margin-top:10px; font-size:14px;"></p>
        </form>
    </div>
    <div class="ftn-result" id="zdc-tuvi-result" style="display:none;"></div>
    <div class="ftn-btn-right">
        <span class="ftn-btn-reset zdc-tuvi-btn-reset" style="display:none;">← Change sign</span>
    </div>
</div>
