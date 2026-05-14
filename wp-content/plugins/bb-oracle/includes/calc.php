<?php

if (!defined('ABSPATH')) exit;

class BbOracle_Calc {

    private static array $allowed_spreads = ['1_card', '2_cards', '3_cards'];

    private static ?array $deck = null;

    private static function getDeck(): array {
        if (self::$deck === null) self::$deck = require BB_ORACLE_PLUGIN_DIR . 'data/data.php';
        return self::$deck;
    }

    public static function isValidSpread(string $spread_key): bool {
        return in_array($spread_key, self::$allowed_spreads, true);
    }

    public static function getSpreads(): array {
        static $spreads = null;
        if ($spreads === null) $spreads = require BB_ORACLE_PLUGIN_DIR . 'data/spreads.php';
        return $spreads;
    }

    public static function drawLite(string $spread_key = '3_cards'): array {
        $spreads     = self::getSpreads();
        $oracle_deck = self::getDeck();
        $spread      = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions   = array_keys($spread['positions']);
        $count       = $spread['count'];

        $keys = array_keys($oracle_deck);
        shuffle($keys);
        $drawn = array_slice($keys, 0, $count);

        $lite = [];
        foreach ($drawn as $i => $key) {
            $d = $oracle_deck[$key];
            $lite[$positions[$i]] = [
                'key'     => $key,
                'name_vi' => $d['name_vi'],
                'element' => $d['element'],
            ];
        }
        return $lite;
    }

    public static function hydrate(array $liteCards): array {
        $oracle_deck = self::getDeck();
        $fullCards   = [];

        foreach ($liteCards as $pos => $cardData) {
            $key = $cardData['key'] ?? null;
            if (!$key || !isset($oracle_deck[$key])) continue;
            $d = $oracle_deck[$key];
            $fullCards[$pos] = [
                'key'      => $key,
                'name_vi'  => $d['name_vi']  ?? '',
                'name_en'  => $d['name_en']  ?? '',
                'element'  => $d['element']  ?? '',
                'theme'    => $d['theme']    ?? '',
                'keywords' => $d['keywords'] ?? [],
                'light'    => $d['light']    ?? '',
                'shadow'   => $d['shadow']   ?? '',
                'advice'   => $d['advice']   ?? '',
                'fortune'  => $d['fortune']  ?? '',
                'mantra'   => $d['mantra']   ?? '',
            ];
        }
        return $fullCards;
    }

    public static function parseResponse(string $raw): array {
        $html = '';
        if (preg_match('/\[AST_RESULT\](.*?)\[\/AST_RESULT\]/s', $raw, $m)) {
            $html = self::markdownToHtml(trim($m[1]));
        } else {
            $html = self::markdownToHtml(trim($raw));
        }

        if (empty(trim(strip_tags($html)))) {
            $html = '<p>Không thể giải mã kết quả. Vui lòng thử lại.</p>';
        }

        return ['html' => $html];
    }

    public static function markdownToHtml(string $md): string {
        $md = preg_replace('/^[\-]{3,}$/m', '', $md);
        $md = preg_replace('/^\*{3,}$/m', '', $md);
        $md = preg_replace('/^_{3,}$/m', '', $md);

        if (!class_exists('Parsedown')) {
            require_once BB_ORACLE_PLUGIN_DIR . 'lib/Parsedown.php';
        }

        $Parsedown = new Parsedown();
        return $Parsedown->text($md);
    }
}
