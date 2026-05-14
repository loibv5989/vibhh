<?php

class Post_Cleaner {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->cleaner_init();
        }
        return self::$instance;
    }

    public function cleaner_init() {
        add_filter( 'content_save_pre', [ $this, 'cleaner_content' ], 20 );
        add_filter( 'wp_insert_post_data', [ $this, 'cleaner_title' ], 20, 2 );
    }

    public function cleaner_content( $content ) {
        if ( empty( $content ) ) return $content;
        $content = str_replace([ '&nbsp;', '&#160;', "\xC2\xA0" ], ' ', $content);
        $content = preg_replace('/[ \t]+/', ' ', $content);
        $content = preg_replace_callback('/<a\b[^>]*>(.*?)<\/a>/is', [ $this, 'clean_anchor' ], $content);

        $content = preg_replace_callback(
            '/<a\b[^>]*>/is',
            function ($m) {
                return preg_replace('/\s+(data-type|data-id)=\\\\?"[^"]*\\\\?"/i', '', $m[0]);
            },
            $content
        );

        $content = preg_replace_callback(
            '/<h[1-6]\b[^>]*>/i',
            function ($m) {
                return preg_replace('/\s+id=\\\\?"[^"]*\\\\?"/i', '', $m[0]);
            },
            $content
        );

        return $content;
    }

    public function clean_anchor( $matches ) {

        $text = html_entity_decode( $matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $text = strip_tags( $text );
        $text = trim( $text );
        if ( $text === '' ) return '';
        if ( ! preg_match('/[a-zA-Z0-9\p{L}\p{N}]/u', $text) ) return '';
        return $matches[0];
    }

    public function cleaner_title( $data, $postarr ) {
        if ( ! empty( $data['post_title'] ) ) {
            $title = str_replace("\xc2\xa0", ' ', $data['post_title']);
            $title = preg_replace(
                '/[\x{00A0}\x{1680}\x{180E}\x{2000}-\x{200B}\x{202F}\x{205F}\x{3000}]+/u',
                ' ',
                $title
            );
            $title = preg_replace('/\s+/', ' ', $title);
            $data['post_title'] = trim($title);
        }

        return $data;
    }
}

Post_Cleaner::get_instance();