<?php
if (!defined('ABSPATH')) exit;

function prompt_topic(string $name, string $topic, array $cards, string $spread_key): string {
    $topic_labels = [
        'love'    => 'Love / Relationships',
        'career'  => 'Career / Work',
        'finance' => 'Finance',
        'study'   => 'Study / Personal Growth',
        'health'  => 'Health / Healing',
        'future'  => 'Future Direction',
    ];
    $topic_label = $topic_labels[$topic] ?? $topic;
    return _bb_oracle_prompt($name, "Topic: {$topic_label}", $cards, $spread_key);
}

function prompt_question(string $name, string $question, array $cards, string $spread_key): string {
    return _bb_oracle_prompt($name, "Question: \"{$question}\"", $cards, $spread_key);
}

function _bb_oracle_prompt(string $name, string $context_line, array $cards, string $spread_key): string {
    $spreads = require BB_ORACLE_PLUGIN_DIR . 'data/spreads.php';
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $card_lines = '';
    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $kw = implode(', ', $c['keywords']);

        $card_lines .= "- Position [{$pos_label}]: {$c['name']}\n";
        $card_lines .= "  (Message: {$kw})\n";
        $card_lines .= "  (Light energy: {$c['light']})\n";
        $card_lines .= "  (Shadow energy: {$c['shadow']})\n";
        $card_lines .= "  (Guidance: {$c['advice']})\n\n";
    }

    return <<<TXT
Based on all the drawn cards, synthesize the insights and directly answer the Oracle reading.

INFORMATION:
- Full name: {$name}
- {$context_line}

CARDS:
{$card_lines}

RULES:
- DO NOT analyze each card individually
- DO NOT explain card meanings
- Address the user as "you" or extract their first name (e.g., "John Smith" → "John"), use consistently
- DO NOT use gendered or informal pronouns
- SYNTHESIZE insights from all cards to answer
- DO NOT infer beyond the card data. Do not over-polish the writing. No moralizing or preachy language.

STYLE REQUIREMENTS:
- Natural, clear, and easy to understand
- Professional tone, get straight to the point
- Coherent and concise
- Use **bold** and *italic* appropriately for emphasis

OUTPUT REQUIREMENTS:
1. NEVER use lettered or numbered section headers such as a), b), c) or 1, 2, 3 as headings.
2. (Hint:) sections are for INTERNAL GUIDANCE ONLY and must NOT appear in the output.
3. Return output strictly in [AST_RESULT][/AST_RESULT] format, using Markdown for readable formatting.
4. Use bold and italic sparingly. Do NOT use ---, ***, ___, or hr tags.
5. Do NOT print any headings or internal instructions in the output. Content only.

[AST_RESULT]
### Reading

(Hint: Read the overall energy across all cards. Identify the single core message that runs through {$name}'s current situation.)
[Write 1 opening paragraph: Go straight to an overall read of the situation. Begin with: Hello, (or Hello {$name},).]

(Hint: Weave the light and shadow energies of the cards into a connected thread. What internal tension is present? What is the reading pointing toward?)
[Write 2-3 analytical paragraphs. Sharp and grounded, with an undertone that supports rather than lectures.]

(Hint: Draw on the Guidance field of the cards to land on a concrete next step.)
[Write a short closing paragraph: What should {$name} focus on or do next. Solution-facing, not fear-facing.]
[/AST_RESULT]
TXT;
}