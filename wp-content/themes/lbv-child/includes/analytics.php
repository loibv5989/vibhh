<?php

add_action('wp_enqueue_scripts', 'google_enqueue_scripts', 12);

function google_enqueue_scripts(){
    if (!is_dev()) {
//        wp_enqueue_script('adsense',
//            'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3109927831594907',
//            array(),
//            null,
//            true
//        );

        wp_enqueue_script('analytics',
            'https://www.googletagmanager.com/gtag/js?id=G-EZS4GW92SX',
            array(),
            null,
            true
        );

        add_action('wp_footer', function() {
            echo "<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-EZS4GW92SX');
</script>\n";
        }, 20);
    }
}

add_filter('script_loader_tag', 'add_async_to_google_scripts', 10, 2);
function add_async_to_google_scripts($tag, $handle) {
    if (strpos($tag, ' src=') === false) {
        return $tag;
    }

//    if ($handle === 'adsense') {
//        return str_replace('<script ', '<script async crossorigin="anonymous" ', $tag);
//    }

    if ($handle === 'analytics') {
        return str_replace('<script ', '<script async ', $tag);
    }

    return $tag;
}
