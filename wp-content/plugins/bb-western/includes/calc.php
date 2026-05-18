<?php
/**
 * Western Calculation Class
 */

if (!defined('ABSPATH')) exit;

class Western_Calc {
    private static array  $allowed_spreads = ['3_cards', '5_cards', '7_cards'];
    private static ?array $data_cache      = null;
    private static ?array $spreads_cache   = null;

    public static function isValidSpread(string $spread_key): bool {
        return in_array($spread_key, self::$allowed_spreads, true);
    }

    private static function getData(): array {
        if (self::$data_cache === null) {
            self::$data_cache = require BB_WESTERN_PLUGIN_DIR . 'data/data.php';
        }
        return self::$data_cache;
    }


    public static function getSpreads(): array {
        if (self::$spreads_cache === null) {
            self::$spreads_cache = require BB_WESTERN_PLUGIN_DIR . 'includes/spreads.php';
        }
        return self::$spreads_cache;
    }

    public static function drawLite(string $spread_key = '3_cards'): array {
        $western_deck = self::getData();
        $spreads      = self::getSpreads();

        $spread    = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions = array_keys($spread['positions']);
        $count     = $spread['count'];

        $keys = array_keys($western_deck);
        shuffle($keys);
        $drawn = array_slice($keys, 0, $count);

        $lite = [];
        foreach ($drawn as $i => $key) {
            $d = $western_deck[$key];
            $pos_key = $positions[$i];
            $lite[$pos_key] = [
                'key'  => $key,
                'name' => $d['name'],
                'suit' => $d['suit'],
            ];
        }
        return $lite;
    }

    public static function drawShuffled(string $spread_key = '3_cards'): array {
        $western_deck = self::getData();
        $spreads      = self::getSpreads();

        $spread    = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions = array_keys($spread['positions']);
        $count     = $spread['count'];

        $keys = array_keys($western_deck);
        shuffle($keys);

        $shuffled = [];
        foreach ($keys as $key) {
            $d = $western_deck[$key];
            $shuffled[] = [
                'key'  => $key,
                'name' => $d['name'],
                'suit' => $d['suit'],
            ];
        }

        $lite = [];
        foreach (array_slice($keys, 0, $count) as $i => $key) {
            $d = $western_deck[$key];
            $pos_key = $positions[$i];
            $lite[$pos_key] = [
                'key'  => $key,
                'name' => $d['name'],
                'suit' => $d['suit'],
            ];
        }

        return ['shuffled_deck' => $shuffled, 'cards' => $lite];
    }

    private static function parseRank(string $key): string {
        $map = [
            'ace' => 'A', 'two' => '2', 'three' => '3', 'four' => '4',
            'five' => '5', 'six' => '6', 'seven' => '7', 'eight' => '8',
            'nine' => '9', 'ten' => '10', 'jack' => 'J', 'queen' => 'Q', 'king' => 'K',
        ];
        $parts = explode('_', $key);
        return $map[$parts[0] ?? ''] ?? '?';
    }

    public static function hydrate(array $liteCards, string $topic = ''): array {
        $western_deck = self::getData();

        $fullCards = [];
        foreach ($liteCards as $pos => $cardData) {
            $key = $cardData['key'] ?? null;
            if (!$key || !isset($western_deck[$key])) continue;

            $d = $western_deck[$key];

            $meaning = $d['upright'] ?? '';
            if ($topic && !empty($d['meanings_by_topic'][$topic])) {
                $meaning = $d['meanings_by_topic'][$topic];
            }

            $fullCards[$pos] = [
                'key'      => $key,
                'name'     => $d['name']    ?? '',
                'suit'     => $d['suit']    ?? '',
                'rank'     => self::parseRank($key),
                'upright'  => $d['upright'] ?? '',
                'keywords' => $d['keywords_upright'] ?? [],
                'meaning'  => $meaning,
            ];
        }
        return $fullCards;
    }

    public static function parseResponse(string $raw): array {
        $html  = '';

        if (preg_match('/\[AST_RESULT\](.*?)\[\/AST_RESULT\]/s', $raw, $m)) {
            $html = self::markdownToHtml(trim($m[1]));
        } else {
            $html = self::markdownToHtml(trim($raw));
        }
        if (empty(trim(strip_tags($html)))) {
            $html = '<p>Unable to decode the result. Please try again.</p>';
        }
        return ['html' => $html];
    }

    public static function markdownToHtml(string $md): string {
        if (str_contains($md, '[AST_RESULT]') && str_contains($md, '[/AST_RESULT]')) {
            preg_match('/\[AST_RESULT\]([\s\S]*?)\[\/AST_RESULT\]/', $md, $matches);
            if (!empty($matches[1])) {
                $md = trim($matches[1]);
            }
        }

        $md = preg_replace('/^[\-]{3,}$/m', '', $md);
        $md = preg_replace('/^\*{3,}$/m', '', $md);
        $md = preg_replace('/^_{3,}$/m', '', $md);

        if (!class_exists('Parsedown')) {
            require_once BB_WESTERN_PLUGIN_DIR . 'lib/Parsedown.php';
        }
        $parsedown = new Parsedown();

        return $parsedown->text($md);
    }
}
