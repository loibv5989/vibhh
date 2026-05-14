<?php

if (!defined('ABSPATH')) exit;

function tarot_build_prompt_topic(string $name, string $topic, array $cards, string $spread_key): string {
    $topic_labels = [
        'love'    => 'Love / Relationships',
        'career'  => 'Career / Work',
        'finance' => 'Finance',
        'study'   => 'Study / Exams',
        'health'  => 'Health',
        'future'  => 'Future Direction',
    ];

    $topic_contexts = [
        'love'    => 'Focus on love, romantic relationships, marriage, and partnerships. Interpret the cards from the angle of emotion, feelings, and the connection between two people.',
        'career'  => 'Focus on career, work, promotion, job changes, and the work environment. Interpret the cards from the angle of profession, growth opportunities, and workplace challenges.',
        'finance' => 'Focus on finances, money, investment, business, and income. Interpret the cards from the angle of financial matters, earning opportunities, and financial risks.',
        'study'   => 'Focus on study, exams, studying abroad, knowledge, and skills. Interpret the cards from the angle of education, learning ability, and exam results.',
        'health'  => 'Focus on physical health, mental wellbeing, energy, and balance. Interpret the cards from the angle of health, warnings, and self-care advice.',
        'future'  => 'Focus on future direction, important decisions, and the road ahead. Interpret the cards from a broad angle covering trends, upcoming opportunities, and challenges.',
    ];

    $topic_label = $topic_labels[$topic] ?? $topic;
    $topic_context = $topic_contexts[$topic] ?? '';

    static $spreads = null;
    if ($spreads === null) {
        $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
    }
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    $card_lines = '';
    $orient_labels = ['upright' => 'Upright ↑', 'reversed' => 'Reversed ↓'];

    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $ol = $orient_labels[$c['orientation']] ?? '';
        $kw = implode(', ', $c['keywords']);

        $card_lines .= "- [{$pos_label}]: {$c['name_vi']} ({$c['name']}) — {$ol}\n";
        $card_lines .= "  Message: {$kw}\n";
        if (!empty($c['timing'])) {
            $card_lines .= "  Timing: {$c['timing']}\n";
        }
        $card_lines .= "\n";
    }

    return <<<TXT
Based on all the drawn cards, synthesize the insights and interpret the reading on the topic of "{$topic_label}".

INFORMATION:
- Full name: {$name}
- Topic of interest: {$topic_label}

CARDS:
{$card_lines}

TOPIC CONTEXT:
{$topic_context}

RULES:
- DO NOT analyze each card individually
- DO NOT explain card meanings
- Address the user as "you" or extract their first name (e.g., "John Smith" → "John"), use consistently
- DO NOT use gendered or informal pronouns
- SYNTHESIZE insights from all cards to answer the question
- DO NOT infer beyond the card data

STYLE REQUIREMENTS:
- Natural, clear, and easy-to-understand language
- Professional tone, get straight to the point
- Coherent and concise presentation
- Use **bold** and *italic* appropriately for emphasis

OUTPUT REQUIREMENTS:
1. NEVER use lettered or numbered section headers such as a), b), c) or 1, 2, 3 as headings in the output.
2. (Hint:) sections and instructions are for INTERNAL GUIDANCE ONLY and must NOT appear in the output.
3. Return output strictly in [AST_RESULT][/AST_RESULT] format, using Markdown for readable formatting.
4. Use bold and italic sparingly and appropriately. Do NOT use ---, ***, ___, or hr tags.
5. Do NOT print any headings or internal instructions in the output. Content only.

[AST_RESULT]
(Hint: Scan all the cards and their positions. What is the overall energy? (Positive, negative, conflicted, or in transition?) Identify the single core message that runs through the entire spread to use as the opening sentence.)
[Write 1 opening paragraph: Go straight to an overall assessment of {$name}'s current situation regarding the question or topic. Begin with: Hello, (or Hello {$name},).]

(Hint: Weave the cards into a narrative following a cause-and-effect or spread-flow structure (e.g., Root cause / Past -> Current tension -> Future trend).
- REQUIRED: Naturally embed card names (and their orientation) into the sentences as evidence for your interpretation.
- STRICTLY FORBIDDEN: Do not use a list structure like "Card 1 in position X shows... Card 2 says...".
- Point out interaction: Is this card supporting or blocking the other?)
[Write 2-3 coherent analytical paragraphs. Interpret the hidden layers, the obstacles being faced, and the trends ahead. Objective, insightful, and psychologically empathetic tone.]

(Hint: Draw on the resolution or outcome card to arrive at a closing point.)
If the overall reading is positive: Write 1 brief, fitting sentence — a congratulation or light praise appropriate to the situation.
If the overall reading is average or below: Write 1 short, gentle sentence of advice or emotional support, while keeping the Tarot reading authentic. No moralizing or life lessons.
[/AST_RESULT]

TXT;
}

function tarot_build_prompt_question(string $name, string $question, array $cards, string $spread_key, string $topic = 'question'): string {
    // Load spreads config
    static $spreads = null;
    if ($spreads === null) {
        $spreads = require TAROT_PLUGIN_DIR . 'includes/spreads.php';
    }
    $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions = $spread_config['positions'];

    // Format cards
    $card_lines = '';
    $orient_labels = ['upright' => 'Upright ↑', 'reversed' => 'Reversed ↓'];

    foreach ($positions as $pos_key => $pos_label) {
        if (!isset($cards[$pos_key])) continue;
        $c = $cards[$pos_key];
        $ol = $orient_labels[$c['orientation']] ?? '';
        $kw = implode(', ', $c['keywords']);

        $card_lines .= "- [{$pos_label}]: {$c['name_vi']} ({$c['name']}) — {$ol}\n";
        $card_lines .= "  Message: {$kw}\n";
        if (!empty($c['timing'])) {
            $card_lines .= "  Timing: {$c['timing']}\n";
        }
        $card_lines .= "\n";
    }

    return <<<TXT
Based on all the drawn cards, synthesize the insights and directly answer the Tarot question.

INFORMATION:
- Full name: {$name}
- Question: "{$question}"

CARDS:
{$card_lines}

RULES:
- DO NOT analyze each card individually
- DO NOT explain card meanings
- Address the user as "you" or extract their first name (e.g., "John Smith" → "John"), use consistently
- DO NOT use gendered or informal pronouns
- SYNTHESIZE insights from all cards to answer the question
- DO NOT infer beyond the card data. DO NOT over-polish the writing. Do not use moralizing or preachy language.

STYLE REQUIREMENTS:
- Natural, clear, and easy-to-understand language
- Professional tone, get straight to the point
- Coherent and concise presentation
- Use **bold** and *italic* appropriately for emphasis

OUTPUT REQUIREMENTS:
1. NEVER use lettered or numbered section headers such as a), b), c) or 1, 2, 3 as headings in the output.
2. (Hint:) sections and instructions are for INTERNAL GUIDANCE ONLY and must NOT appear in the output.
3. Return output strictly in [AST_RESULT][/AST_RESULT] format, using Markdown for readable formatting.
4. Use bold and italic sparingly and appropriately. Do NOT use ---, ***, ___, or hr tags.
5. Do NOT print any headings or internal instructions in the output. Content only.

[AST_RESULT]
(Hint: Quickly scan all the cards and their positions. What is the overall energy? (Positive, negative, conflicted, or in transition?) Identify the single core message that runs through the entire spread to use as the opening sentence.)
[Write 1 opening paragraph: Go straight to an overall assessment of {$name}'s current situation regarding the question or topic. Begin with: Hello, (or Hello {$name},).]

(Hint: Weave the cards into a narrative following a cause-and-effect or spread-flow structure (e.g., Root cause / Past -> Current tension -> Future trend).
- REQUIRED: Naturally embed card names (and their orientation) into the sentences as evidence for your interpretation.
- STRICTLY FORBIDDEN: Do not use a list structure like "Card 1 in position X shows... Card 2 says...".
- Point out interaction: Is this card supporting or blocking the other?)
[Write 2-3 coherent analytical paragraphs. Interpret the hidden layers, the obstacles being faced, and the trends ahead. Objective, insightful, and psychologically empathetic tone.]

(Hint: Draw on the resolution or outcome card to arrive at a closing point.)
If the overall reading is positive: Write 1 brief, fitting sentence — a congratulation or light praise appropriate to the situation.
If the overall reading is average or below: Write 1 short, gentle sentence of advice or emotional support, while keeping the Tarot reading authentic. No moralizing or life lessons.
[/AST_RESULT]
TXT;
}