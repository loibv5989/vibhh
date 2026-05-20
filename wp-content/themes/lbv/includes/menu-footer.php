<?php

defined('ABSPATH') || exit;

class Lbv_Walker_Footer_Menu extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth = 0, $args = null) {
        return;
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        return;
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if ($depth > 0) return;

        $badge = get_post_meta($item->ID, '_menu_item_badge', true);

        $output .= '<li>';

        $output .= '<a href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';

        if (!empty($badge)) {
            $output .= '<span class="badge badge-new">' . esc_html($badge) . '</span>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth > 0) return;
        $output .= '</li>';
    }
}
