<?php
if (!defined('ABSPATH')) exit;

class TshLove_Prompt {

    public static function build(array $data): string {
        $n1 = $data['name1']; $lp1 = $data['lp1']; $soul1 = $data['soul1']; $att1 = $data['att1'];
        $n2 = $data['name2']; $lp2 = $data['lp2']; $soul2 = $data['soul2']; $att2 = $data['att2'];
        $pct = $data['percent'];

        return <<<TXT
Pythagorean Numerology - Love Compatibility Analysis:

[INFO]:
- {$n1}: Life Path: {$lp1} | Soul Urge: {$soul1} | Attitude: {$att1}
- {$n2}: Life Path: {$lp2} | Soul Urge: {$soul2} | Attitude: {$att2}
- Base compatibility resonance: {$pct}%

INTERPRETATION PRINCIPLES:
- DO NOT recalculate; ONLY use the provided data.
- ESSENCE IS ROOT: Analyze directly into the essence of international Pythagorean Numerology.
- NO SPECULATION: Do not invent specific details about actions, words, or real-life scenarios.

TONE REQUIREMENTS:
- ADDRESSING: Extract first names from "{$n1}" and "{$n2}" for consistency, e.g. John Michael Smith -> John; or use the phrase "both of you".
- Natural, clear, easy-to-understand language
- Professional style, straight to the point, no lecturing, no preaching
- Logical flow, no long-winded introductions

OUTPUT REQUIREMENTS:
- DO NOT use bullet prefixes like a), b), c) or 1, 2, 3 as headings
- Use bold and italic reasonably for emphasis but do not overuse.
- Use Markdown for easy-to-read formatting.
- FORMAT: Use standard Markdown (do not use ---, ***, ___).
- MUST return in the exact format [TAB_RESULT][/TAB_RESULT].

PROHIBITIONS:
- Any (Hint:)/guidance paragraphs are FOR INTERNAL THINKING DIRECTION ONLY, do not include them in the OUTPUT.
- FORBIDDEN terms: brother, sister, elder/younger sibling, or any familial honorifics.
- FORBIDDEN: draft passages, internal thoughts, thinking -> only return complete content.
- FORBIDDEN: display any meta content including but not limited to: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- These sections are for internal processing ONLY and MUST NOT appear in the final output.
- Output must contain only the complete analysis in the requested format.

[CONTENT GUIDE]
### Love Decoded
### 1. Core Index (Life Path Number)
When the Life Path {$lp1} ({$n1}) and Life Path {$lp2} ({$n2}) energies coexist, do these two fields resonate or oppose? What factor plays the anchoring role in this relationship?

### 2. Subconscious (Soul Urge Number)
Based on Soul Urge {$soul1} and {$soul2}, analyze the true deepest desires of both people in love. Is there any contradiction between how they present themselves externally and what they truly want deep inside?

### 3. Attitude Number
When facing pressure or conflict, how do the natural reflexes of Attitude {$att1} ({$n1}) and Attitude {$att2} ({$n2}) manifest? Analyze behavioral tendencies to identify the barriers most likely to cause misunderstanding in communication.

[TAB_RESULT]
(Content goes here)
[/TAB_RESULT]
TXT;
    }

}