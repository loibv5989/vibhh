<?php
if (!defined('ABSPATH')) exit;

class Tarot_Calc {

    public static function isValidSpread(string $spread_key): bool {

        static $spreads = null;
        if ($spreads === null) {
            $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
        }

        return array_key_exists($spread_key, $spreads);
    }

    private static function getData(): array {
        $major   = require TAROT_PLUGIN_DIR . 'data/major.php';
        $minor_1 = require TAROT_PLUGIN_DIR . 'data/minor_1.php';
        $minor_2 = require TAROT_PLUGIN_DIR . 'data/minor_2.php';

        return array_merge($major, $minor_1, $minor_2);
    }


    public static function drawLite(string $spread_key = '3_cards'): array {
        static $spreads = null;
        $tarot_deck = self::getData();
        if ($spreads === null) $spreads = require __DIR__ . '/spreads.php';

        $spread = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions = array_keys($spread['positions']);
        $count = $spread['count'];

        $keys = array_keys($tarot_deck);
        shuffle($keys);
        $drawn = array_slice($keys, 0, $count);

        $lite = [];
        foreach ($drawn as $i => $key) {
            $d = $tarot_deck[$key];
            $pos_key = $positions[$i];
            $lite[$pos_key] = [
                'key'         => $key,
                'orientation' => rand(0, 1) ? 'upright' : 'reversed',
                'name'        => $d['name']
            ];
        }
        return $lite;
    }

    public static function drawShuffled(): array {
        $tarot_deck = self::getData();

        $keys = array_keys($tarot_deck);
        shuffle($keys);

        $shuffled = [];
        foreach ($keys as $key) {
            $d = $tarot_deck[$key];
            $shuffled[] = [
                'key'         => $key,
                'orientation' => rand(0, 1) ? 'upright' : 'reversed',
                'name'        => $d['name'],
            ];
        }

        return ['shuffled_deck' => $shuffled];
    }

    public static function hydrate(array $liteCards): array {
        $tarot_deck = self::getData();

        $fullCards = [];

        foreach ($liteCards as $pos => $cardData) {
            $key = $cardData['key'] ?? null;
            $orient = $cardData['orientation'] ?? 'upright';

            if (!in_array($orient, ['upright', 'reversed'], true)) {
                $orient = 'upright';
            }

            if (!$key || !isset($tarot_deck[$key])) {
                continue;
            }

            $d = $tarot_deck[$key];
            
            $image_filename = strtolower(str_replace('_', '', $key)) . '.jpg';
            $image_url = TAROT_PLUGIN_URL . 'images/' . $image_filename;

            $fullCards[$pos] = [
                'key'               => $key,
                'orientation'       => $orient,
                'name'              => $d['name'] ?? '',
                'arcana'            => $d['arcana'] ?? '',
                'suit'              => $d['suit'] ?? null,
                'element'           => $d['element'] ?? '',
                'astro_type'        => $d['astro_type'] ?? '',
                'astro_name'        => $d['astro_name'] ?? '',
                'upright'           => $d['upright'] ?? '',
                'reversed'          => $d['reversed'] ?? '',
                'keywords_upright'  => $d['keywords_upright'] ?? [],
                'keywords_reversed' => $d['keywords_reversed'] ?? [],
                'meaning'           => $d[$orient] ?? '',
                'keywords'          => $d['keywords_' . $orient] ?? [],
                'hint'              => $d['hint_' . $orient] ?? '',
                'source_deck'       => $d['source_deck'] ?? '',
                'description'       => $d['description'] ?? '',
                'themes'            => $d['themes'] ?? [],
                'timing'            => $d['timing'] ?? '',
                'related_cards'     => $d['related_cards'] ?? [],
                'image_url'         => $image_url,
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

        return ['hints' => [], 'html' => $html];
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
            require_once TAROT_PLUGIN_DIR . 'lib/Parsedown.php';
        }

        $Parsedown = new Parsedown();
        return $Parsedown->text($md);
    }
}