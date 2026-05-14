<?php

defined( 'ABSPATH' ) || exit;

function render_ads($client, $slot) {
    if (is_dev()) {
        return '';
    }

    return '<ins class="adsbygoogle" style="display:block" data-ad-client="'. esc_attr($client) .'"  data-ad-slot="'. esc_attr($slot) .'"  data-ad-format="auto"  data-full-width-responsive="true"></ins>
    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
}

add_shortcode('home_after_ranking', 'home_after_ranking');
function home_after_ranking(){
    if (is_dev()) {
        return '';
    }

    $ads = '<div class="lbv-g-block">' . render_ads('ca-pub-3109927831594907', '2163098755') . '</div>';
    return $ads;
}

add_shortcode('home_after_loadmore', 'home_after_loadmore');
function home_after_loadmore(){
    if (is_dev()) {
        return '';
    }

    $ads = '<div class="lbv-g-block">' . render_ads('ca-pub-3109927831594907', '1476703040') . '</div>';
    return $ads;
}

add_shortcode('sidebar_bottom', 'sidebar_bottom');
function sidebar_bottom(){
    if (is_dev()) {
        return '';
    }

    $ads = '<div class="lbv-g-block">' . render_ads('ca-pub-3109927831594907', '2549192351') . '</div>';

    return $ads;
}


add_shortcode('after_content', 'after_content');
function after_content(){
    if (is_dev()) {
        return '';
    }

    $ads = '<div class="lbv-g-block">' . render_ads('ca-pub-3109927831594907', '2215069645') . '</div>';
    return $ads;
}

function add_content_to_specific_positions($content) {
    $post_type = ['post'];

    if (!is_singular($post_type) || is_dev()) {
        return $content;
    }

    $word_count = str_word_count( strip_tags($content) );

    $parts = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    $positions = [3];

    if ($word_count > 700) {
        $positions[] = 10;
    }

    if ($word_count > 1000) {
        $positions[] = 15;
    }

    $client = 'ca-pub-3109927831594907';

    $extra_contents = [
        3  => '<div class="lbv-g-block">' . render_ads($client, '7876474998') . '</div>',
        10 => '<div class="lbv-g-block">' . render_ads($client, '3093037049') . '</div>',
        15 => '<div class="lbv-g-block">' . render_ads($client, '9466873709') . '</div>',
    ];

    $positions = array_values(array_unique($positions));
    sort($positions, SORT_NUMERIC);

    foreach ($positions as $position) {
        $closingIndex = ($position - 1) * 2 + 1;

        if (isset($parts[$closingIndex]) && isset($extra_contents[$position])) {
            $parts[$closingIndex] .= $extra_contents[$position];
        }
    }

    $new_content = implode('', $parts);

    return $new_content;
}

//add_filter('the_content', 'add_content_to_specific_positions', 12);
