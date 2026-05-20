<?php

if (!defined('ABSPATH')) exit;

add_filter('wp_robots', function ($robots) {
    $url = home_url($_SERVER['REQUEST_URI']);
    if (strpos($url, '/wp-admin') !== false ||
        strpos($url, '/wp-login.php') !== false ||
        strpos($url, '?edit-artist=1') !== false) {
        $robots = array('noindex' => true, 'nofollow' => true);
    }
    return $robots;
});

add_filter('rank_math/frontend/robots', function ($robots) {
    if (!empty($_GET['edit-artist']) && $_GET['edit-artist'] === '1') {
        $robots['index'] = 'noindex';
        $robots['follow'] = 'nofollow';
    }
    return $robots;

});
