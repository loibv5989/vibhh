<?php
/**
 * Western Prompt Builders
 */

if (!defined('ABSPATH')) exit;

function western_build_prompt_question(string $question, array $cards, string $spread_key, string $topic = ''): string {
    $topic_context = '';
    if ($topic) {
        $topic_contexts = [
            'love'    => 'Focus on love, relationships, family, and milestones. Read the cards through the lens of emotional connection, romantic ties, or signs of strain and third-party interference.',
            'career'  => 'Focus on career, professional growth, and workplace dynamics. Read the cards through the lens of effort, opportunity, business partnerships, or rivals in the workplace.',
            'finance' => 'Focus on finances, money, and business. Read the cards through the lens of income flow, investment, cash management, or risks of financial loss.',
            'study'   => 'Focus on studies, exams, and learning. Read the cards through the lens of intellect, concentration, personal effort, and academic results.',
            'health'  => 'Focus on physical health and wellbeing. Read the cards through the lens of energy levels, recovery, or warnings around illness, accidents, or physical strain.',
            'future'  => 'Focus on overall fortune and upcoming turning points. Read the cards through the lens of general favorable or unfavorable trends, unexpected events, or sudden good fortune.',
        ];
        if (!empty($topic_contexts[$topic])) {
            $topic_context = "TOPIC CONTEXT:\n" . $topic_contexts[$topic] . "\n";
        }
    }
    return _western_build_core_prompt("Question: \"{$question}\"", $topic_context, $cards, $spread_key);
}

function _western_build_core_prompt(string $context_line, string $topic_context_block, array $cards, string $spread_key): string {
    static $spreads = null;
    if ($spreads === null) {
        $spreads = require BB_WESTERN_PLUGIN_DIR . 'includes/spreads.php';
    }
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $suit_labels = ['hearts' => 'Hearts', 'diamonds' => 'Diamonds', 'clubs' => 'Clubs', 'spades' => 'Spades'];
    $counts = ['hearts' => 0, 'diamonds' => 0, 'clubs' => 0, 'spades' => 0];

    $card_lines = '';

    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $suit = $suit_labels[$c['suit']] ?? $c['suit'];
        $kw = implode(', ', $c['keywords']);

        $card_lines .= "- [{$pos_label}]: {$c['name']} — {$suit}\n";
        $card_lines .= "  Meaning: {$c['meaning']}\n";
        $card_lines .= "  Message: {$kw}\n\n";

        if (isset($c['suit']) && isset($counts[$c['suit']])) {
            $counts[$c['suit']]++;
        }
    }

    $total = count($cards);
    $suit_analysis = "";
    if ($total > 0) {
        $suit_analysis = "IMPORTANT NOTE ON SUIT ENERGY:\n";
        if ($counts['spades'] >= ceil($total / 2)) {
            $suit_analysis .= "- This spread is heavily weighted toward SPADES (setbacks, conflict, difficulty). Flag the risks clearly — do not soften them.\n";
        } elseif ($counts['spades'] > 0) {
            $suit_analysis .= "- SPADES are present. Weave in references to underlying obstacles or quiet rivals.\n";
        }
        if ($counts['hearts'] >= ceil($total / 2)) {
            $suit_analysis .= "- The spread is heavy in HEARTS. Emotions, romantic ties, and family matters are the central theme.\n";
        }
        if (($counts['diamonds'] + $counts['clubs']) >= ceil($total / 2)) {
            $suit_analysis .= "- The spread leans toward DIAMONDS (Money/News) and CLUBS (Ambition/Career). The reading is practical and driven by personal effort.\n";
        }
        $suit_analysis .= "\n";
    }

    return <<<TXT
Based on all the drawn cards, synthesize the insights and deliver a reading.

DETAILS:
- {$context_line}

CARDS DRAWN:
{$card_lines}
{$topic_context_block}{$suit_analysis}
RULES:
- DO NOT analyze each card individually
- DO NOT explain card meanings in a mechanical, list-like way
- Address the person as "bạn" consistently
- DO NOT use the person's name anywhere in the response
- DO NOT use formal gendered address forms
- Synthesize insights from all cards and suit energies (Hearts / Diamonds / Clubs / Spades) to answer the question
- DO NOT speculate beyond what the cards show

WRITING STYLE:
- Professional, direct, and to the point — no moralizing, no lecturing
- Clear and concise, no unnecessary filler
- Use **bold** and *italic* where appropriate for emphasis

OUTPUT REQUIREMENTS:
1. DO NOT use section markers like a), b), c) or 1, 2, 3 as headings within the text.
2. The (Suggestion:) guidance blocks below are for internal orientation only — do not include them in the output.
3. Return output strictly within [AST_RESULT][/AST_RESULT] tags, using Markdown for readable formatting.
4. Use bold and italic sparingly — do not use ---, ***, ___, or hr tags.
5. DO NOT include any heading or title. Output body text only. Do not print internal notes or guidance into the response.

[AST_RESULT]
(Suggestion: Scan all cards and their positions. What energy do the dominant suits — Hearts, Diamonds, Clubs, Spades — create together? Identify the single core message that runs across the whole spread.)
[Write 1 opening paragraph: Go straight to an overall read of the current situation. Open with: Hi,]

(Suggestion: Connect the cards into a narrative using cause and effect or a timeline — for example: Root cause / Past → Current tension → Likely direction ahead.
- REQUIRED: Weave card names (e.g. Ace of Hearts, 9 of Spades) naturally into sentences as evidence for each point.
- STRICTLY FORBIDDEN: Do not use structures like "The card in the Present position shows... The Future card says...".
- Note interaction: Is one card's energy supporting or working against another?)
[Write 2-3 clear analytical paragraphs. Cover hidden angles, current obstacles, and likely direction. Keep the tone objective, sharp, and grounded.]

(Suggestion: Draw on the full spread to close with one final piece of advice.)
If the overall reading is positive: Write one brief, encouraging line — acknowledge the opportunity and what to do with it.
If the overall reading is mixed or negative: Write one grounded caution — watch for rivals, manage finances carefully, or stay measured in how you communicate.
[/AST_RESULT]
TXT;
}