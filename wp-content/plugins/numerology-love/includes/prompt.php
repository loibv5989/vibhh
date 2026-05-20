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
- Foundational harmony score: {$pct}%

INTERPRETATION PRINCIPLES:
- DO NOT recalculate; use only the data provided above.
- GO TO THE CORE: Analyze directly into the essence of Pythagorean Numerology.
- NO SPECULATION: Do not invent specific details about actions, words, or real-life scenarios.

TONE REQUIREMENTS:
- ADDRESSING: use the names of the two people "{$n1}" and "{$n2}", consistently and naturally; or use the phrase "the two of you".
- Natural, clear, easy to understand.
- Professional and direct — no lecturing, no moralizing.
- Logical flow, no lengthy lead-ins.

OUTPUT REQUIREMENTS:
- DO NOT use lettered or numbered prefixes like a), b), c) or 1, 2, 3 as section headings.
- Use bold and italic sparingly for emphasis only.
- Use Markdown for readable formatting.
- FORMAT: Standard Markdown only — do not use ---, ***, or ___.
- MUST wrap the result in [TAB_RESULT][/TAB_RESULT].

PROHIBITIONS:
- Any (Hint:) or guidance lines are for internal thinking only — do not include them in the output.
- FORBIDDEN: gendered or formal address terms such as "he", "she", "Mr.", "Ms.", or any equivalent.
- FORBIDDEN: drafts, internal thoughts, or reasoning steps — return only the final complete content.
- FORBIDDEN: any meta content including but not limited to: Constraint Checklist, Confidence Score, validation notes, or reasoning blocks.
- These are for internal processing only and must not appear in the output.
- Output must contain only the complete analysis in the requested format.

[CONTENT GUIDE]            
### Love Decoded
### 1. Core Numbers (Life Path)
When Life Path {$lp1} ({$n1}) and Life Path {$lp2} ({$n2}) come together, do these two energy fields resonate or conflict? What holds this relationship together?

### 2. Inner World (Soul Urge)
Based on Soul Urge {$soul1} and {$soul2}, what does each person truly want in love? Is there a gap between how they show up externally and what they actually need inside?

### 3. Attitude Number
When tension or conflict arises, how do the natural responses of Attitude {$att1} ({$n1}) and Attitude {$att2} ({$n2}) show up? Identify the behavioral patterns most likely to cause misunderstanding between them.

[TAB_RESULT]
(Content goes here)
[/TAB_RESULT]
TXT;
    }

}