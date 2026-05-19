<?php

if (!defined('ABSPATH')) exit;

class Tarot_Prompt {

    public static function tarot_gatekeeper(string $question, string $mode): string {

        if ($mode === 'love') {
            return <<<TXT
Task:
Analyze the question to determine whether it is a question about LOVE, ROMANCE, or RELATIONSHIPS suitable for a Tarot reading.

Mandatory rules:
- This is a pure text classification task, not a task to answer or comment on the content of the question.
- Always return a classification result, even if the question involves sensitive, violent, illegal, offensive, or spiritual content.
- Never refuse to classify under any circumstances. Do not say "I cannot assist with this".
- Do not apply moral judgment, add warnings, or include any commentary.
- Focus only on the meaning of the question to classify it.

Question: {$question}

Classification rules:

1. If the question is about general knowledge, definitions, small talk, or testing the bot
   (Examples: "What is Tarot?", "How do I draw cards", "Hello", "How are you?")
   → Return: NO

2. If the question is unclear or too vague to understand
   (Examples: "I want to ask something", "Help me", "Read my cards", "...")
   → Return: NO

3. ONLY ACCEPT questions related to LOVE, ROMANCE, or RELATIONSHIPS:
   - Love / Marriage / Romantic relationships / Partner / Crush
   - Breakup / Reconciliation / Getting back together / Holding on
   - Future of the relationship / Compatibility / Fate
   - Does this person like me / Do I have a chance
   - Should I get married / When will I get married / Will my marriage be happy
   - Infidelity / Affairs / Love triangle

   SPECIAL NOTE: Tarot readings often deal with difficult or sensitive situations. If the question involves sensitive or negative content (jealousy, betrayal, feeling stuck in a relationship, etc.):
     Do NOT return "NO".
     Treat it as a VALID question for a Tarot reading.

   → Return: YES

4. MUST return "NO" for all questions unrelated to romantic relationships:
   - Career / Work / Promotion / Job change
   - Finance / Money / Investment / Business
   - Study / Exams / Studying abroad
   - Health / Illness (unless clearly related to a romantic relationship)
   - Family relationships (parents, siblings, children, relatives)
   - Friendship / Friends / Colleagues / Social relationships
   - General fate / Life direction / Life decisions (unrelated to love)

   NOTE: If the question does NOT clearly mention love / a partner / marriage / romance → RETURN: NO

   → Return: NO

OUTPUT REQUIREMENT:
Return exactly one word after classifying:
YES or NO

No explanation, no reasoning, no additional content of any kind.
TXT;
        }

        return <<<TXT
Task:
Analyze the question to determine whether it is suitable for a Tarot reading.

Mandatory rules:
- This is a pure text classification task, not a task to answer or comment on the content of the question.
- Always return a classification result, even if the question involves sensitive, violent, illegal, offensive, or spiritual content.
- Never refuse to classify under any circumstances. Do not say "I cannot assist with this".
- Do not apply moral judgment, add warnings, or include any commentary.
- Focus only on the meaning of the question to classify it.

Question: {$question}

Classification rules:

1. If the question is about general knowledge, definitions, small talk, or testing the bot
   (Examples: "What is Tarot?", "How do I draw cards", "Hello", "How are you?")
   → Return: NO

2. If the question is unclear or too vague to understand
   (Examples: "I want to ask something", "Help me", "Read my cards", "...")
   → Return: NO

3. If the question relates to divination, predicting outcomes, or seeking guidance on:
   - Relationships / Family / Marriage
   - Work / Career / Business
   - Money / Assets / Transactions
   - Study / Exams / Relocation
   - Health / Illness
   - Bad luck / Enemies / Conflict / Disputes
   - General fortune in the near future

   SPECIAL NOTE: Tarot readings often deal with difficult or sensitive situations. If the question involves sensitive or negative content (revenge, debt, deception, feeling stuck, etc.):
     Do NOT return "NO".
     Treat it as a VALID question for a Tarot reading.

   → Return: YES

OUTPUT REQUIREMENT:
Return exactly one word after classifying:
YES or NO

No explanation, no reasoning, no additional content of any kind.
TXT;
    }

    private static function rules(): string {
        return <<<RULES

OUTPUT REQUIREMENTS:
- Do NOT use lettered or numbered section markers like a), b), c) or 1, 2, 3 as headings.
- Use bold/italic sparingly for emphasis, not excessively.
- FORMAT: Standard Markdown only (do not use ---, ***, ___ to create hr separators).
- Clean, readable formatting. 100% English.
- MUST return the correct format [AST_RESULT][/AST_RESULT].

PROHIBITED:
- No speculation beyond the Tarot card data provided.
- Do NOT recalculate — use only the data provided.
- Sections marked (Hint:) / guidance are FOR INTERNAL ORIENTATION ONLY and must NOT appear in the OUTPUT.
- Do NOT include drafts, internal thoughts, thinking, Constraint Checklists, Confidence Scores, validation, reasoning, notes, opening greetings, or CTAs in the OUTPUT.
- No icons or emoji in the writing.
- No em dashes —.
- No preachy, moralizing, promotional, flattering, or exaggerated language.

- No language that personifies power or uses power metaphors. Banned words/phrases: dominate, consolidate, assert dominance, overwhelm, overshadow, rise to power, break through.
- ABSOLUTELY NO hollow spiritual advice, superstition, or generic platitudes such as: "cultivate virtue", "do good deeds", "accumulate good karma", "pray", "feng shui", "you need to..."
=> Every statement MUST be grounded in a specific card, its orientation, its message, its effect, and its consequence.
   Do not state general conclusions without card data supporting them.

- Headings:
    + Headings (Markdown heading tags like ##, ###) may ONLY contain the [Topic] or a time marker.
    + ABSOLUTELY NO exclamations, evaluations, commentary, or separators like long dashes (–) or colons (:) in headings.
- Body text:
    + Do not write in a "Label: Content" two-part structure. Do NOT use colons to split a sentence into two halves — write complete, natural sentences instead, or if using a list, write each item as a direct complete sentence.
    + BANNED mechanical phrases: in-depth, comprehensive, pronounced, dissect, significant, core, intersect — in short, avoid any word that SOUNDS authoritative and expert but is hollow surface-level filler.
RULES;
    }

    public static function tarot_topic(string $name, string $topic, array $cards, string $spread_key): string {
        $topic_labels = [
            'love'    => 'Love / Relationships',
            'career'  => 'Career / Work',
            'finance' => 'Finance',
            'study'   => 'Study / Exams',
            'health'  => 'Health',
            'future'  => 'Future Direction',
        ];

        $topic_contexts = [
            'love'    => 'Focus on love, relationships, marriage, and significant life milestones. Read the cards through the lens of emotional connection, romantic attachment, signs of strain, or third-party interference.',
            'career'  => 'Focus on work, career, and dynamics within the professional environment. Read the cards through the lens of personal effort, opportunities, business partnerships, or silent rivals in the workplace.',
            'finance' => 'Focus on finances, money, and business. Read the cards through the lens of income flow, investment, cash management, or the risk of financial loss.',
            'study'   => 'Focus on study, exams, and the learning process. Read the cards through the lens of intellect, focus, personal effort, and academic outcomes.',
            'health'  => 'Focus on physical health and overall wellbeing. Read the cards through the lens of energy levels, recovery, or warnings around illness, injury, or physical strain.',
            'future'  => 'Focus on overall fortune and upcoming turning points. Read the cards through the lens of favorable or unfavorable trends, unexpected events, or sudden shifts in luck.',
        ];

        $topic_label = $topic_labels[$topic] ?? $topic;
        $topic_context = '';
        if (!empty($topic_contexts[$topic])) {
            $topic_context = "TOPIC CONTEXT:\n" . $topic_contexts[$topic];
        }

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

            $card_lines .= "- [{$pos_label}]: {$c['name']} — {$ol}\n";
            $card_lines .= "  Message: {$kw}\n";
            if (!empty($c['timing'])) {
                $card_lines .= "  Timing: {$c['timing']}\n";
            }
        }

        $rules = self::rules();

        $short_name = trim(explode(' ', trim($name))[count(explode(' ', trim($name))) - 1]);

        return <<<TXT
78-Card Tarot:
Based on the cards provided, synthesize the symbolic meaning of the full spread and interpret the reading for the given topic.

Topic: "{$topic_label}"

INFORMATION:
- Full name: {$name}

CARDS:
{$card_lines}
{$topic_context}

EXPRESSION REQUIREMENTS:
- Professional, composed tone, in keeping with Tarot tradition.
- FORM OF ADDRESS: use "{$short_name}" or "you" consistently throughout. Do not use "he/she/they/this person".
- Natural, clear, accessible language.
- Coherent and concise. No rambling.
- Avoid repeating ideas — if a blockquote is used, it must not restate the earlier interpretation word for word; rephrase or approach it from a different angle.
- Professional tone, direct, no moralizing.
- Do NOT restate the provided information — interpret directly.
- Use **bold** and *italic* to emphasize where appropriate.
{$rules}

[CONTENT GUIDE]
## Reading the cards for your topic
- Answer DIRECTLY and stay ON TOPIC. Do not drift into other areas, do not interpret each card individually.
- Lead with card evidence, follow with conclusions. Do not open with a verdict and then explain it — let the card data lead to the conclusion naturally.
- Every statement must be grounded in specific card evidence. Do NOT make vague references like "early on" or "later" without a clear time marker.
- REQUIRED: Weave card names (and their orientation) into sentences smoothly as evidence for each point.
- ABSOLUTELY NO list-style structure like "Card 1 in position X shows... Card 2 says...".
- Show interaction between cards: is this card supporting or working against that one.
- Scan all cards and their positions, identify the central card, and draw one through-line message for the entire spread.
- Write in short, tight, coherent paragraphs.
- Close with a blockquote (using >) summarizing in 4-6 sentences: overall reading, main trend, the most significant phase, and 1-2 points to watch drawn from the cards.

Examples of WRONG vs RIGHT expression:

EX1:
+ WRONG (vague, no evidence): You should focus on building your skills and gaining solid experience rather than chasing quick returns...
+ RIGHT: The Tower reversed in the challenge position shows the risk has not fully erupted yet, but The Moon upright immediately after reflects a state of uncertainty — not enough clear information to make a sound financial decision at this stage.

EX2:
+ WRONG (preachy, moralizing): You need balance and understanding to keep the relationship harmonious...
+ RIGHT: The Lovers upright at the center confirms a genuine attraction and connection, but Three of Swords reversed in the blocking position shows an old wound that hasn't fully healed — keeping both sides more guarded than open.

[AST_RESULT]
(Reading content goes here)
[/AST_RESULT]

TXT;
    }

    public static function tarot_question(string $name, string $question, array $cards, string $spread_key, string $topic = 'question'): string {
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

            $card_lines .= "- [{$pos_label}]: {$c['name']} — {$ol}\n";
            $card_lines .= "  Message: {$kw}\n";
            if (!empty($c['timing'])) {
                $card_lines .= "  Timing: {$c['timing']}\n";
            }
        }

        $rules = self::rules();
        return <<<TXT
78-Card Tarot:
Based on the cards provided, synthesize the symbolic meaning of the full spread and answer the question directly.

Question: "{$question}"

INFORMATION:
- Full name: {$name}

CARDS:
{$card_lines}

EXPRESSION REQUIREMENTS:
- Professional, composed tone, in keeping with Tarot tradition.
- FORM OF ADDRESS: use {$name} or "you" consistently throughout. Do not use "he/she/they/this person".
- Natural, clear, accessible language.
- Coherent and concise. No rambling.
- Avoid repeating ideas — if a blockquote is used, it must not restate the earlier interpretation word for word; rephrase or approach it from a different angle.
- Professional tone, direct, no moralizing.
- Do NOT restate the provided information — interpret directly.
- Use **bold** and *italic* to emphasize where appropriate.
{$rules}

[CONTENT GUIDE]
## Reading the cards for your question
- Answer DIRECTLY and stay ON TOPIC. Do not drift into other areas, do not interpret each card individually.
- Lead with card evidence, follow with conclusions. Do not open with a verdict and then explain it — let the card data lead to the conclusion naturally.
- Every statement must be grounded in specific card evidence. Do NOT make vague references like "early on" or "later" without a clear time marker.
- REQUIRED: Weave card names (and their orientation) into sentences smoothly as evidence for each point.
- ABSOLUTELY NO list-style structure like "Card 1 in position X shows... Card 2 says...".
- Show interaction between cards: is this card supporting or working against that one.
- Scan all cards and their positions, identify the central card, and draw one through-line message for the entire spread.
- Write in short, tight, coherent paragraphs.
- Close with a blockquote (using >) summarizing in 4-6 sentences: overall reading, main trend, the most significant phase, and 1-2 points to watch drawn from the cards.

Examples of WRONG vs RIGHT expression:

EX1:
+ WRONG (vague, no evidence): You should focus on building your skills and gaining solid experience rather than chasing quick returns...
+ RIGHT: The Tower reversed in the challenge position shows the risk has not fully erupted yet, but The Moon upright immediately after reflects a state of uncertainty — not enough clear information to make a sound decision at this stage.

EX2:
+ WRONG (preachy, moralizing): You need balance and understanding to keep the relationship harmonious...
+ RIGHT: The Lovers upright at the center confirms a genuine attraction and connection, but Three of Swords reversed in the blocking position shows an old wound that hasn't fully healed — keeping both sides more guarded than open.

[AST_RESULT]
(Reading content goes here)
[/AST_RESULT]
TXT;
    }
}