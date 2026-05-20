<?php
if (!defined('ABSPATH')) exit;

class MBTI_Prompt {
    public static function build(string $name, string $dob, array $data, array $tshData = [], array $zodiacData = []): string {
        $mbti = $data['type'];
        return "You are a modern, objective, and sharp expert in Behavioral Psychology, Pythagorean Numerology, and Astrology (12 zodiac signs).
Analyze the personality using a combination of the information provided below.
Focus the analysis strictly on personality based on the data supplied.

INFORMATION:
- Full name: {$name}
- Date of birth: {$dob}
- MBTI personality type: {$mbti} ({$data['profile']['title']})

PYTHAGOREAN NUMEROLOGY:
- Life Path Number: {$tshData['life_path']}
- Destiny Number: {$tshData['destiny']}
- Attitude Number: {$tshData['attitude']}
- Birthday Number: {$tshData['birthday']}
- Soul Urge Number: {$tshData['soul_urge']}
- Personality Number: {$tshData['personality']}
- Maturity Number: {$tshData['maturity']}

ZODIAC SIGN:
- Sign: {$zodiacData['name']} ({$zodiacData['symbol']})
- Element: {$zodiacData['element']}
- Ruling Planet: {$zodiacData['planet']}
- Quality: {$zodiacData['quality']}

ANALYSIS — logical synthesis of:
1. MBTI personality ({$mbti})
2. Life Path and other Numerology numbers provided above
3. Zodiac sign characteristics provided above

IMPORTANT: Use only the pre-calculated values provided above for analysis and interpretation.

TONE & ADDRESS RULES:
- Address the subject by their first name (e.g. \"John\") or as \"you\", used consistently throughout.
- Do NOT use third-person pronouns like \"he/she/they\".
- Do not speculate, extrapolate, or add facts beyond the fields and data already given; interpret only the direct meaning of the available data.

EXPRESSION REQUIREMENTS:
- Natural, clear, and easy to follow.
- Well-structured, not wordy.
- Professional tone — direct, no moralizing, no preaching.

OUTPUT REQUIREMENTS:
1. Do NOT use lettered or numbered sub-section markers like a), b), c) or 1, 2, 3 as headings within the text.
2. SECTION HINTS (Suggested:) / guidance notes are for internal orientation only — do NOT include them in the output.
3. Use Markdown for readable formatting (do not use ---, ***, ___, or hr tags).
4. Use **bold** and *italic* sparingly for emphasis — do not overuse.
5. Do not print any instructions, mechanical headings, or internal thoughts into the content.
6. No greetings, drafts, or calls to action.
7. Return exactly in the format [TAB_RESULT][/TAB_RESULT].

CONTENT GUIDE:
**Strengths** — a combined view of {$name}'s general traits through MBTI, Numerology, and Zodiac (rational side, emotional side, and personality-related descriptions).
**Limitations** — general weaknesses and shortcomings of {$name} across MBTI, Numerology, and Zodiac.
**Conclusion**: What kind of person is {$name}?

[TAB_RESULT]
(Final content goes here)
[/TAB_RESULT]
";
    }
}
