<?php

if (!defined('ABSPATH')) exit;

class Zodiac_Prompt {

    public static function build(string $dob, array $sign): string {
        $signName   = $sign['name']       ?? '';
        $symbol     = $sign['symbol']     ?? '';
        $element    = $sign['element']    ?? '';
        $planet     = $sign['planet']     ?? '';
        $quality    = $sign['quality']    ?? '';
        $polarity   = $sign['polarity']   ?? '';
        $keywords   = $sign['keywords']   ?? '';
        $decan      = $sign['decan']      ?? '';
        $subRuler   = $sign['sub_ruler']  ?? '';
        $decanVibe  = $sign['decan_vibe'] ?? '';
        $hasCusp    = !empty($sign['has_cusp']);
        $cuspName   = $sign['cusp_name']  ?? '';
        $cuspBlend  = $sign['cusp_blend'] ?? '';
        $cuspVibe   = $sign['cusp_vibe']  ?? '';

        $compat = $sign['compatibility'] ?? [];
        $bestMatch   = !empty($compat['best_match'])   ? implode(', ', $compat['best_match'])   : '';
        $karmicMatch = !empty($compat['karmic_match']) ? implode(', ', $compat['karmic_match']) : '';
        $worstMatch  = !empty($compat['worst_match'])  ? implode(', ', $compat['worst_match'])  : '';

        $cuspSection = $hasCusp ? "- Cusp: {$cuspName} — {$cuspBlend} — {$cuspVibe}" : '';

        // Calculate age to transform into "Implicit Energy State"
        $age = 0;
        $dobObj = DateTime::createFromFormat('d/m/Y', $dob);
        if ($dobObj) {
            $age = $dobObj->diff(new DateTime())->y;
        }

        $energyState = '';
        if ($age < 18) {
            $energyState = "Primitive energy, instinctive outbursts. The ego is still forming, easily influenced, the zodiac character expresses itself in the purest and most intense way.";
        } elseif ($age <= 25) {
            $energyState = "Energy is colliding with social reality. Distinctive traits manifest strongly, eager to explore and trial-and-error but sometimes lacking control.";
        } elseif ($age <= 35) {
            $energyState = "Energy has matured and become practical. Knows how to curb weaknesses, focuses on building a career foundation and heads toward stability.";
        } elseif ($age <= 50) {
            $energyState = "Energy reaches ripeness, expands and optimizes. Has thoroughly understood the true self, knows how to use the zodiac strengths sharply, deeply, and calmly.";
        } else {
            $energyState = "Energy settles and distills. Rising above the usual impulsiveness of this sign, heading toward core values, wisdom, and inner peace.";
        }

        return <<<TXT
In-depth personality analysis based on Astrology.

INPUT DATA:
- Date of birth: {$dob}
- Zodiac sign: {$signName} {$symbol}
- Element: {$element}
- Ruling planet: {$planet}
- Quality: {$quality}
- Polarity: {$polarity}
- Keywords: {$keywords}
- Decan: {$decan} (Sub-ruler: {$subRuler}, Vibe: {$decanVibe})
- Compatibility: Best match [{$bestMatch}], Karmic match [{$karmicMatch}], Worst match [{$worstMatch}]
{$cuspSection}
- Current energy state (Implicit reference frame): {$energyState}

RULES:
- DO NOT recalculate, only analyze based on the provided data.
- Use "You" consistently, DO NOT use "Brother/Sister".
- ONLY use astrology in the analysis. Ensure you write accurately about the nature of the 12 zodiac signs.
- If there is a Cusp, mention the influence of the cusp.
- MANDATORY IMPLICIT UNDERSTANDING: Illuminate zodiac personality through the "Current energy state".

EXPRESSION REQUIREMENTS:
- Natural, clear, easy-to-understand language.
- Professional tone, straight to the point, no preachy or lecturing language.
- Coherent presentation, not wordy.

OUTPUT REQUIREMENTS:
- DO NOT use bullet-section headings like a), b), c) or 1, 2, 3.
- Use bold and italics reasonably to emphasize content but do not overuse.
- Use Markdown, easy-to-read text formatting (do not use ---, ***, ___, hr for separation).
- MUST RETURN exactly in format [ZDC_HTML][/ZDC_HTML].

ABSOLUTE PROHIBITIONS:
- NEVER mention specific age, year of birth, or allude to "age", "life stage" in the analysis. Only use [Current energy state] to self-adjust the voice and perspective of the interpreter.
- DO NOT use draft passages or internal thinking.
- GUIDANCE paragraphs (Hint:) are ONLY FOR ORIENTING THOUGHT, not to be included in OUTPUT content.
- FORBID displaying any meta content, including but not limited to: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- These parts only serve internal processing and MUST NOT be output in the final result.
- Output only includes complete analysis content in the requested format.

[CONTENT GUIDE]
### Personality Decoding
(Hint: Based on [INPUT DATA], write 3-4 paragraphs analyzing personality:
- Paragraph 1: Overview of the ego (Sign + Element + Planet) as revealed through the lens of the Current energy state.
- Paragraph 2: Strengths, potential (combined with Decan) and how this sign best manifests its abilities at this moment.
- Paragraph 3: Characteristic weaknesses and hidden psychological corners that need to be recognized.
- Paragraph 4: Direction and advice (career/love) that is sharp and appropriate to the maturity level of the current energy.)

[ZDC_HTML]
(Analysis content will be placed here)
[/ZDC_HTML]
TXT;
    }

    public static function buildLove(array $data): string {
        // Person 1 info
        $n1 = $data['name1'] ?? '';
        $s1 = $data['sign1']['name'] ?? '';
        $e1 = $data['sign1']['element'] ?? '';

        // Person 2 info
        $n2 = $data['name2'] ?? '';
        $s2 = $data['sign2']['name'] ?? '';
        $e2 = $data['sign2']['element'] ?? '';

        // 5-layer analysis data (from calc.php)
        $score = $data['analysis']['score'] ?? 0;
        $aspectLabel = $data['analysis']['aspect_label'] ?? '';
        $mod1 = $data['analysis']['mod_1'] ?? '';
        $mod2 = $data['analysis']['mod_2'] ?? '';
        $pol1 = $data['analysis']['pol_1'] ?? '';
        $pol2 = $data['analysis']['pol_2'] ?? '';
        $planetMatch = !empty($data['analysis']['planet_match']) ? 'Deep subconscious link' : 'Independent in spirit';

        return <<<TXT
You are an Astrologer.
Task: Interpret our synastry chart based on 5 foundational astrological analysis layers.

[INFO]:
- Person 1: {$n1} | Sign: {$s1} | Element: {$e1} | Modality: {$mod1} | Polarity: {$pol1}
- Person 2: {$n2} | Sign: {$s2} | Element: {$e2} | Modality: {$mod2} | Polarity: {$pol2}
- Layer 1 (Aspect/Distance): {$aspectLabel}
- Layer 5 (Planet & Decan link): {$planetMatch}
- Overall Compatibility Score: {$score}%

INTERPRETATION PRINCIPLES (MANDATORY):
- ESSENCE IS ROOT: Only use data from the 5 layers above for illumination. Analysis goes straight to the essence of the 12 zodiac signs.
- NO SPECULATION: Do not create specific details about actions, words, or real-life scenarios.
- Synthesize insights from ALL the data above for analysis.
- If there is a Cusp, mention the influence of the cusp.

EXPRESSION REQUIREMENTS:
- ADDRESSING: Use the names "{$n1}" and "{$n2}" or the word "you two". FORBIDDEN: brother, sister, or any third-person pronouns.
- Natural, clear, easy-to-understand language.
- Professional tone, straight to the point, no preachy or lecturing language.
- Coherent presentation, not wordy.

OUTPUT REQUIREMENTS:
- DO NOT use bullet-section headings like a), b), c) or 1, 2, 3 as titles.
- Use bold and italics reasonably, do not overuse (do not use ---, ***, ___).
- Use Markdown, easy-to-read text formatting.
- FORMAT: Standard Markdown (do not use ---, ***, ___).
- MUST RETURN exactly in format [ZDC_HTML][/ZDC_HTML].

PROHIBITIONS:
- GUIDANCE paragraphs (Hint:) are ONLY FOR ORIENTING THOUGHT, not to be included in OUTPUT content.
- FORBID draft passages, internal thinking, thinking-> only return complete content.
- FORBID displaying any meta content, including but not limited to: Constraint Checklist, Confidence Score, validation, reasoning, notes.
- These parts only serve internal processing and MUST NOT be output in the final result.
- Output only includes complete analysis content in the requested format.

[CONTENT GUIDE]
### Love Decoding
(Hint - Aspect & Element:
Analyze the clash between element {$e1} and {$e2} combined with the aspect "{$aspectLabel}". Which energy leads, which adapts? Is the initial attraction dramatic and intense or calm and natural?)

(Hint - Action Style (Modality & Polarity):
Analyze the combination of Modality {$mod1}-{$mod2} and Polarity {$pol1}-{$pol2}. When the Ego confronts or when facing common issues, how do they react? Is the conflict tendency explosive or accumulative?)

(Hint - Soul Link (Planet & Decan):
Based on layer 5 status "{$planetMatch}", decode the level of empathy, resonance, or independence in the subconscious and deep soul of {$n1} and {$n2}.)

(Hint - Advice (Based on score {$score}%):
Give a final assessment on the {$score}% compatibility level and 1-2 core advice to maintain or balance this relationship based on the analyzed differences.)

[ZDC_HTML]
(Analysis content will be placed here)
[/ZDC_HTML]
TXT;
    }
}

