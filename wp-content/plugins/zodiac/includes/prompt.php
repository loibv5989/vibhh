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

        $age = 0;
        $dobObj = DateTime::createFromFormat('d/m/Y', $dob);
        if ($dobObj) {
            $age = $dobObj->diff(new DateTime())->y;
        }

        $energyState = '';
        if ($age < 18) {
            $energyState = "Raw energy, instinctive expression. The ego is still forming and easily influenced. The sign's traits manifest in their purest, most unfiltered form.";
        } elseif ($age <= 25) {
            $energyState = "Energy colliding with social reality. Core traits are prominent, driven by exploration and experimentation, but impulse control is inconsistent.";
        } elseif ($age <= 35) {
            $energyState = "Energy is grounded and practical. Weaknesses are being managed. Focus shifts to building stability and a career foundation.";
        } elseif ($age <= 50) {
            $energyState = "Energy is at peak maturity. The sign's strengths are applied with precision and calm. Self-awareness is deep and consistent.";
        } else {
            $energyState = "Energy is settled and refined. The sign's typical impulsiveness has been transcended. Orientation moves toward core values, wisdom, and inner stillness.";
        }

        return <<<TXT
Personality analysis based on Western Astrology.

INPUT:
- Date of birth: {$dob}
- Sign: {$signName} {$symbol}
- Element: {$element}
- Ruling planet: {$planet}
- Modality: {$quality}
- Polarity: {$polarity}
- Keywords: {$keywords}
- Decan: {$decan} (Sub-ruler: {$subRuler}, Vibe: {$decanVibe})
- Compatibility: Best [{$bestMatch}] | Karmic [{$karmicMatch}] | Worst [{$worstMatch}]
{$cuspSection}
- Energy state: {$energyState}

RULES:
- Use only the INPUT data above. Do not recalculate anything.
- Address the person as "You" throughout. No other pronouns.
- Use only Western Astrology. Be accurate about the nature of all 12 signs.
- If Cusp data is present, include its influence in the analysis.
- Use [Energy state] to calibrate the voice, tone, and depth of the analysis — not to mention age or life stage explicitly.

OUTPUT RULES:
- Do not use lettered or numbered section prefixes (a), b), 1., 2., etc.).
- Bold and italic for emphasis only — do not overuse.
- Standard Markdown. No ---, ***, ___, or hr tags.
- Return content inside [ZDC_HTML][/ZDC_HTML] only.

PROHIBITIONS:
- Never mention age, birth year, or any reference to life stage in the output.
- Never output drafts, reasoning, internal notes, Constraint Checklist, Confidence Score, or any meta content.
- Hint paragraphs below are internal guidance only — never include them in output.

[CONTENT GUIDE]
### Personality Decoded
(Hint: Write 3-4 paragraphs:
- Para 1: The ego — Sign + Element + Planet — as filtered through the current Energy state.
- Para 2: Strengths and potential, combined with Decan influence, and how they best manifest right now.
- Para 3: Core weaknesses and psychological blind spots that need recognition.
- Para 4: Focused direction for career and love, calibrated to the current energy level.)

[ZDC_HTML]
(Content goes here)
[/ZDC_HTML]
TXT;
    }

    public static function buildLove(array $data): string {
        $n1 = $data['name1'] ?? '';
        $s1 = $data['sign1']['name'] ?? '';
        $e1 = $data['sign1']['element'] ?? '';

        $n2 = $data['name2'] ?? '';
        $s2 = $data['sign2']['name'] ?? '';
        $e2 = $data['sign2']['element'] ?? '';

        $score       = $data['analysis']['score']        ?? 0;
        $aspectLabel = $data['analysis']['aspect_label'] ?? '';
        $mod1        = $data['analysis']['mod_1']        ?? '';
        $mod2        = $data['analysis']['mod_2']        ?? '';
        $pol1        = $data['analysis']['pol_1']        ?? '';
        $pol2        = $data['analysis']['pol_2']        ?? '';
        $planetMatch = !empty($data['analysis']['planet_match']) ? 'Deep subconscious link' : 'Independent in spirit';

        return <<<TXT
You are a professional Astrologer.
Task: Interpret a synastry chart based on 5 astrological analysis layers.

INPUT:
- Person 1: {$n1} | Sign: {$s1} | Element: {$e1} | Modality: {$mod1} | Polarity: {$pol1}
- Person 2: {$n2} | Sign: {$s2} | Element: {$e2} | Modality: {$mod2} | Polarity: {$pol2}
- Layer 1 (Aspect): {$aspectLabel}
- Layer 5 (Planet & Decan link): {$planetMatch}
- Compatibility score: {$score}%

RULES:
- Use only the 5-layer INPUT data above. Do not speculate or add external details.
- Analyze from the core nature of the signs involved — no invented scenarios or dialogue.
- Synthesize all layers. Do not skip any.
- If Cusp data is present, include its influence.
- Address by name: use "{$n1}", "{$n2}", or "you two". No gendered pronouns (he, she, they) or formal address terms.

OUTPUT RULES:
- Do not use lettered or numbered section prefixes (a), b), 1., 2., etc.).
- Bold and italic for emphasis only — do not overuse.
- Standard Markdown. No ---, ***, or ___.
- Return content inside [ZDC_HTML][/ZDC_HTML] only.

PROHIBITIONS:
- Never output drafts, reasoning, internal notes, Constraint Checklist, Confidence Score, or any meta content.
- Hint paragraphs below are internal guidance only — never include them in output.

[CONTENT GUIDE]
### Love Decoded
(Hint - Aspect & Element:
How do {$e1} and {$e2} interact under the aspect "{$aspectLabel}"? Which energy leads, which adapts? Is the initial pull intense and dramatic, or steady and natural?)

(Hint - Modality & Polarity:
How does the {$mod1}/{$mod2} modality pairing and {$pol1}/{$pol2} polarity dynamic play out under pressure or conflict? Does tension tend to explode or accumulate?)

(Hint - Soul Link:
Given layer 5 status "{$planetMatch}", what is the subconscious resonance between {$n1} and {$n2}? Deep empathy, mirroring, or independence?)

(Hint - Closing:
Assess the {$score}% compatibility score. Give 1-2 concrete actions to maintain or rebalance this connection based on the differences above.)

[ZDC_HTML]
(Content goes here)
[/ZDC_HTML]
TXT;
    }
}