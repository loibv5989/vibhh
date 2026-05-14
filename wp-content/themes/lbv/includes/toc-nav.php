<?php

defined('ABSPATH') || exit;

class LBV_Table_Of_Contents {

    private static $instance = null;

    private $post_types = array('idol', 'group', 'photo', 'actor', 'v_star', 'post');

    private $heading_levels = array(2, 3, 4);
    private $used_ids = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('the_content', array($this, 'add_ids_to_headings'), 12);
    }

    private function is_valid_page() {
        return is_single() && is_singular($this->post_types);
    }

    private function extract_headings($content) {
        $pattern = '/<h([' . implode('', $this->heading_levels) . '])(.*?)>(.*?)<\/h[' . implode('', $this->heading_levels) . ']>/i';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        return $matches;
    }

    private function generate_unique_id($text) {
        $slug = remove_accents($text);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        $base_id = $slug;
        $id = $base_id;
        $counter = 1;

        while (in_array($id, $this->used_ids)) {
            $id = $base_id . '-' . $counter++;
        }

        $this->used_ids[] = $id;
        return $id;
    }

    private function render_toggle_button($level, $has_children) {
        if ($level !== 2 || !$has_children) {
            return '';
        }

        return '<button class="toc-toggle" aria-label="' . __('Toggle submenu', 'lbv') . '">' .
            '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>' .
            '</svg></button>';
    }

    private function get_clean_heading_text($html) {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Chuẩn hóa apostrophe và quotes
        $text = str_replace(
            ["'", "\x{2018}", "\x{2019}", "\x{8217}", '’', '‘', '"', "\x{201C}", "\x{201D}"],
            ["'", "'", "'", "'", "'", '"', '"', '"'],
            $text
        );

        $text = str_replace(['–', '—', '‐', '‑'], '-', $text);

        // Giữ lại: chữ cái, số, space, gạch ngang, apostrophe, quotes, dấu hai chấm, dấu chấm, dấu phẩy
        $text = preg_replace('/[^\p{L}\p{N}\p{P}\s\-]/u', '', $text);

        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        return $text;
    }


    private function build_toc_items($matches) {
        $html = '';
        $prev_level = 2;
        $first_item = true;

        foreach ($matches as $index => $heading) {
            $level = (int)$heading[1];
            $text = $this->get_clean_heading_text($heading[3]);
            $id = $this->generate_unique_id($text);

            if (!$first_item) {
                if ($level > $prev_level) {
                    $html .= '<ul>';
                } elseif ($level < $prev_level) {
                    for ($i = $prev_level; $i > $level; $i--) {
                        $html .= '</li></ul>';
                    }
                    $html .= '</li>';
                } else {
                    $html .= '</li>';
                }
            }

            $has_children = isset($matches[$index + 1]) && (int)$matches[$index + 1][1] > $level;
            $li_class = 'oil-level-' . $level;
            $li_class .= ($level === 2 && $has_children) ? ' has-children' : '';

            $toggle_button = $this->render_toggle_button($level, $has_children);

            $html .= sprintf(
                '<li class="%s">%s<a href="#%s">%s</a>',
                esc_attr($li_class),
                $toggle_button,
                esc_attr($id),
                esc_html($text)
            );

            $prev_level = $level;
            $first_item = false;
        }

        $html .= '</li>';
        return $html;
    }

    public function create() {

        if (!$this->is_valid_page()) {
            return '';
        }

        $post = get_post();
        $content = apply_filters('the_content', $post->post_content);
        $matches = $this->extract_headings($content);

        if (empty($matches)) {
            return '';
        }

        $this->used_ids = array();
        $html  = '<nav class="toc-wrapper nav-toc" aria-label="Table of Contents" style="position:absolute; top:0; left:-9999px; width:0; height:0; overflow:hidden; clip:rect(0 0 0 0); clip-path:inset(50%);">';

        $html .= '<div class="toc-toggle-wrapper">';
        $html .= '<button id="toc-toggle-icon" title="' . __('Open TOC', 'lbv') . '">';
        $html .= '<span></span><span></span><span></span>';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '<div id="table-of-content" class="table-of-content">';
        $html .= '<header class="toc-header">';
        $html .= '<span class="toc-title">' . __('Table of Contents', 'lbv') . '</span>';
        $html .= '<button class="toc-close">×</button>';
        $html .= '</header>';

        $html .= '<div class="toc-body"><ul id="toc-list">';
        $html .= $this->build_toc_items($matches);
        $html .= '</ul></div>';

        $html .= '</div>';
        $html .= '<div id="toc-overlay" class="toc-overlay"></div>';
        $html .= '</nav>';

        return $html;
    }

    public function add_ids_to_headings($content) {
        if ( is_admin() && ! wp_doing_ajax() ) return $content;
        if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) return $content;

        if (!$this->is_valid_page()) {
            return $content;
        }

        $matches = $this->extract_headings($content);
        if (empty($matches)) {
            return $content;
        }

        $this->used_ids = array();

        foreach ($matches as $heading) {
            $level = (int)$heading[1];

            $text = $this->get_clean_heading_text($heading[3]);
            $id = $this->generate_unique_id($text);

            $attributes = preg_replace('/\s+id="[^"]*"/i', '', $heading[2]);

            $new_heading = sprintf(
                '<h%d%s id="%s">%s</h%d>',
                $level,
                $attributes,
                $id,
                $heading[3],  // Giữ HTML gốc cho display
                $level
            );

            $content = str_replace($heading[0], $new_heading, $content);
        }

        return $content;
    }

}

function lbv_table_of_contents() {
    return LBV_Table_Of_Contents::get_instance()->create();
}

LBV_Table_Of_Contents::get_instance();
