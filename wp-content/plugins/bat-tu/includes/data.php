<?php

if (!defined('ABSPATH')) exit;

class BatTu_Data {
    private static $cache = [];

    public static function load($module = 'all') {
        if (isset(self::$cache[$module])) {
            return self::$cache[$module];
        }

        $base = self::load_file('base.php');
        $relations = self::load_file('relations.php');
        $stars = self::load_file('stars.php');
        $calendar = self::load_file('calendar.php');
        $vuong_suy = self::load_file('vuong-suy.php');

        $all = self::deep_merge($base, $relations);
        $all = self::deep_merge($all, $stars);
        $all = self::deep_merge($all, $calendar);
        $all = self::deep_merge($all, $vuong_suy);

        self::$cache['all'] = $all;

        if ($module === 'all') {
            return $all;
        }

        if (isset($all[$module])) {
            self::$cache[$module] = $all[$module];
            return $all[$module];
        }

        return [];
    }

    private static function load_file($filename) {
        $path = BATTU_PLUGIN_DIR . 'data/' . $filename;
        if (!is_file($path)) {
            return [];
        }
        $data = include $path;
        return is_array($data) ? $data : [];
    }

    private static function deep_merge($a, $b) {
        if (!is_array($a)) {
            $a = [];
        }
        if (!is_array($b)) {
            $b = [];
        }

        foreach ($b as $k => $v) {
            if (array_key_exists($k, $a) && is_array($a[$k]) && is_array($v)) {
                $a[$k] = self::deep_merge($a[$k], $v);
            } else {
                $a[$k] = $v;
            }
        }
        return $a;
    }
}
