<?php

defined('ABSPATH') || exit;

class Lbv_Walker_Nav_Menu extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '<div class="dropdown">';
        } else {
            $output .= '<div class="submenu">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</div>';
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {

        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes);

        $is_current = in_array('current-menu-item', $classes)
            || in_array('current-menu-parent', $classes)
            || in_array('current-menu-ancestor', $classes)
            || in_array('current_page_item', $classes);

        $active_class = $is_current ? ' lbv-current-active' : '';

        if ($depth === 0) {

            $output .= '<div class="nav-item' . $active_class . '">';
            $output .= '<a class="nav-link' . $active_class . '" href="' . esc_url($item->url) . '">';
            $output .= esc_html($item->title);

            if ($has_children) {
                $output .= '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
                $output .= '</svg>';
            }

            $output .= '</a>';
        }

        elseif ($depth === 1) {

            $item_class = 'dropdown-item';

            if ($has_children) {
                $item_class .= ' has-submenu';
            }

            if ($is_current) {
                $item_class .= ' lbv-current-active';
            }

            $output .= '<div class="' . esc_attr($item_class) . '">';

            if ($has_children) {
                $output .= '<a href="' . esc_url($item->url) . '" class="dropdown-link">';
                $output .= '<span>' . esc_html($item->title) . '</span>';
                $output .= '</a>';

                $output .= '<button class="dropdown-submenu-toggle" aria-label="Toggle submenu">';
                $output .= '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                $output .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>';
                $output .= '</svg>';
                $output .= '</button>';
            }

            else {
                $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            }

        }

        else {
            $link_class = 'submenu-item' . $active_class;
            $output .= '<a href="' . esc_url($item->url) . '" class="' . esc_attr($link_class) . '">';
            $output .= esc_html($item->title);
            $output .= '</a>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 0 || $depth === 1) {
            $output .= '</div>';
        }
    }
}
