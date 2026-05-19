<?php

if (!defined('ABSPATH')) exit;

class Western_Prompt {

    public static function gatekeeper_western(string $question): string {

        return <<<TXT
Task:
Analyze the question to determine whether it is suitable for a Western card reading.

Mandatory rules:
- This is a pure text classification task, not a task to answer or comment on the question's content.
- Always return a classification result, even when the question involves sensitive, violent, illegal, offensive, or spiritual content.
- Refusing to classify is not allowed under any circumstances. Do not say "I cannot assist with this."
- Do not impose moral judgment, add warnings, or make any comments.
- Focus only on the meaning of the question for classification purposes.

Question: {$question}

Classification rules:

1. If the question asks about general knowledge, definitions, small talk, or bot testing
   (Examples: "What is card reading?", "How does fortune telling work?", "Hello", "Who created you?")
   → Return: NO

2. If the question is unclear or too vague to understand
   (Examples: "Check something for me", "I have a question", "...")
   → Return: NO

3. If the question involves fortune telling, predicting outcomes, or seeking guidance about:
   - Romance / Family / Marriage
   - Work / Career / Business
   - Money / Assets / Real estate transactions
   - Study / Exams / Relocation
   - Health / Illness
   - Bad luck / Enemies / Conflicts / Legal disputes
   - Overall fortune for the near future

   SPECIAL NOTE: Card readings often address difficult or hidden matters. If the question involves sensitive or negative content (revenge, debt, deception, feelings of being stuck, etc.):
     Do NOT return "NO".
     Treat it as a VALID question for a card reading.

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
- DO NOT use section markers such as a), b), c) or 1, 2, 3 as headings.
- Use bold/italic appropriately to emphasize content but do not overuse.
- FORMAT: Use standard Markdown (do not use ---, ***, ___ to create hr separator tags).
- Text formatting must be easy to read. 100% in Vietnamese; if English terms are used, include a Vietnamese translation in parentheses.
- MUST RETURN the exact format [AST_RESULT][/AST_RESULT].

PROHIBITIONS:
- Prohibited: speculating beyond the card data provided.
- PROHIBITED: recalculating anything => use only the data already provided.
- HINT sections (Gợi ý:) / guidance notes are FOR INTERNAL ORIENTATION ONLY and must NOT appear in the OUTPUT.
- PROHIBITED: including drafts, internal thoughts, thinking, Constraint Checklists, Confidence Scores, validation, reasoning, notes, opening greetings, or CTAs in the OUTPUT.
- PROHIBITED: using icons or emojis anywhere in the content.
- PROHIBITED: using the long en dash –.
- PROHIBITED: all preachy, moralistic, promotional, flattering, or exaggerated language.

- PROHIBITED: personifying or power-metaphor language; banned words/phrases include: dominate, consolidate, position, control, overpower, overwhelm, gain the upper hand, transcend, rise up, break through.
- STRICTLY PROHIBITED: hollow spiritual advice, superstitious or generic statements such as: "cultivate virtue", "do good deeds", "accumulate merit", "accumulate virtue", "release animals", "make offerings", "feng shui", "you need to..."
=> Every observation MUST trace back to a specific card, suit, message, its effect, and its consequence.
   Do not state a general conclusion unless there is card data leading to that conclusion.

- Headings:
    + Headings (Markdown heading tags such as ##, ###) MAY ONLY contain the exact [Topic] or a time marker.
    + STRICTLY PROHIBITED: adding exclamatory phrases, evaluations, commentary, or separators such as long dashes (–) or colons (:) to headings.
- Body content:
    + Prohibited: writing in a "Heading: Content" two-part structure. PROHIBITED: using colons to split a sentence into two halves -> Write complete, natural sentences instead; if using a list, write each item as a direct full sentence.
    + PROHIBITED: mechanical phrases such as: in-depth, comprehensive, pronounced, dissect, significant, core, intersection -> In short, banned words that SOUND FORMAL, broadly abstract, and very "expert-like" but are hollow on the surface.
RULES;
    }

    public static function western_prompt(string $question, array $cards, string $spread_key, string $topic = ''): string {
        $topic_context = '';
        if ($topic) {
            $topic_contexts = [
                'love'    => 'Focus on love, relationships, family, and life milestones. Interpret the cards through the lens of emotional connection, emotional bonds, or signs of strain and third-party interference.',
                'career'  => 'Focus on work, career, and relationships in the workplace. Interpret the cards through the lens of personal effort, opportunities, business partnerships, or workplace rivals.',
                'finance' => 'Focus on finances, money, and business. Interpret the cards through the lens of income streams, investments, cash management, or the risk of financial loss.',
                'study'   => 'Focus on studying, exams, and the learning process. Interpret the cards through the lens of intellect, focus, personal effort, and academic outcomes.',
                'health'  => 'Focus on physical health and overall health condition. Interpret the cards through the lens of energy levels, recovery processes, or warnings about illness, accidents, or physical strain.',
                'future'  => 'Focus on overall destiny and upcoming turning points. Interpret the cards through the lens of favorable or unfavorable trends, unexpected events, or sudden good fortune.',
            ];
            if (!empty($topic_contexts[$topic])) {
                $topic_context = "TOPIC CONTEXT:\n" . $topic_contexts[$topic];
            }
        }

        static $spreads = null;
        if ($spreads === null) {
            $spreads = require WESTERN_PLUGIN_DIR . 'includes/spreads.php';
        }

        $spread_config = $spreads[$spread_key] ?? $spreads['3_cards'];
        $positions = $spread_config['positions'];

        $suit_labels = ['hearts' => 'Cơ', 'diamonds' => 'Rô', 'clubs' => 'Chuồn', 'spades' => 'Bích'];
        $rank_labels = ['A' => 'Át', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', 'J' => 'Bồi', 'Q' => 'Đầm', 'K' => 'Già'];
        $counts = ['hearts' => 0, 'diamonds' => 0, 'clubs' => 0, 'spades' => 0];

        $card_lines = '';

        foreach ($positions as $pos_key => $pos_label) {
            if (!isset($cards[$pos_key])) continue;
            $c = $cards[$pos_key];
            $suit = $suit_labels[$c['suit']] ?? $c['suit'];
            $rank = $rank_labels[$c['rank']] ?? $c['rank'];
            $kw = implode(', ', $c['keywords']);

            $card_lines .= "- [{$pos_label}]: {$rank} {$suit} - {$c['name']}\n";
            $card_lines .= "  Meaning: {$c['meaning']}\n";
            $card_lines .= "  Message: {$kw}\n\n";

            if (isset($c['suit']) && isset($counts[$c['suit']])) {
                $counts[$c['suit']]++;
            }
        }

        $total = count($cards);
        $suit_analysis = "\n";
        if ($total > 0) {
            $suit_analysis = "NOTES ON SUIT ENERGY:\n";
            if ($counts['spades'] >= ceil($total / 2)) {
                $suit_analysis .= "- The spread is heavily weighted toward SPADES (obstacles, conflict, difficulty). Point out risks clearly without softening them.\n";
            } elseif ($counts['spades'] > 0) {
                $suit_analysis .= "- SPADES are present. Weave in references to underlying obstacles or silent rivals.\n";
            }
            if ($counts['hearts'] >= ceil($total / 2)) {
                $suit_analysis .= "- The spread leans toward HEARTS. Emotions, romantic feelings, and family matters are the central theme.\n";
            }
            if (($counts['diamonds'] + $counts['clubs']) >= ceil($total / 2)) {
                $suit_analysis .= "- The spread leans toward DIAMONDS (Money/News) and CLUBS (Ambition/Work). Interpretation should be practical and driven by personal effort.\n";
            }
        }

        $rules = self::rules();

        return <<<TXT
52-card Western Tarot Reading:
Based on the cards provided, synthesize the symbolic meaning of all cards and answer the question.

Question: "{$question}"

THE CARDS:
{$card_lines}
{$topic_context}{$suit_analysis}

EXPRESSION REQUIREMENTS:
- Tone: professional, formal, consistent with the tradition of 52-card Western cartomancy.
- ADDRESS THE QUERENT AS: "bạn" (you), consistently throughout; prohibited: "Anh/Chị/Em/người này/họ".
- Language: natural, clear, and easy to understand.
- Presentation: coherent, not long-winded.
- Avoid repeating the same idea; for example, a blockquote (if used) must not restate the preceding interpretation verbatim — it must offer a more direct angle or use a different expression to avoid template repetition.
- Writing style: professional, gets straight to the point, does not use preachy language.
- DO NOT restate the information already provided -> Interpret directly.
- Use **bold**, *italic* to emphasize content.
{$rules}

[CONTENT GUIDE]
## Answering the question from your cards
- Answer DIRECTLY and stay ON POINT with the question and the topic provided. Do not drift into unrelated topics, do not interpret each card.
- Prioritize presenting evidence first, conclusions second. Do not open with a judgment and then explain it -> Lead with card data and let the data drive the conclusion.
- Every observation must be backed by specific card evidence. PROHIBITED: vague statements like "early phase", "later on", "when younger" without a clearly stated time marker.
- The 52-card Western deck is used to read fortune, interpret psychology, and identify future trends.
- Each suit — Hearts, Diamonds, Clubs, Spades — carries its own layer of symbolic meaning.
- Each rank/card, each position in the spread, and the interactions between cards all contribute to the overall message.
- Scan all cards and their positions, identify which suit is dominant, which card is the focal point, and from that draw a single core message running through the entire spread.
- Write in short, concise, coherent paragraphs.
- Close with a blockquote paragraph (using the > symbol) summarizing this person's profile in 4-6 sentences: core personality, overall tendency, the most important phase, and 1-2 points to watch.

A few WRONG/RIGHT examples of expression and expertise:

Example 1:
+ WRONG (generic, no evidence): You should focus on developing your professional skills and building solid experience rather than seeking quick profits...
+ RIGHT: The 2 of Clubs/Spades/Diamonds/Hearts is dominant, while the Diamond card appears in the challenge position. This reflects a phase that calls for prioritizing practicality, maintaining a steady pace, and avoiding impulsive decisions about money or work.

Example 2:
+ WRONG (preachy, moralistic): You need balance and understanding to maintain a harmonious relationship...
+ RIGHT: The central love card appears in the focal position, but accompanied by Spades cards in the blocking positions, indicating that feelings are real but being suppressed by pressure, doubt, or distance — making it hard to express them directly, and leading both parties to keep more inside than they say aloud.

[AST_RESULT]
(Interpreted content goes here)
[/AST_RESULT]

TXT;
    }
}