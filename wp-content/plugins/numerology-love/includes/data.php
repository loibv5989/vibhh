<?php
if (!defined('ABSPATH')) exit;

class TshLove_Data {

    public static function getLifePathHint(int $number): string {
        $hints = [
            1  => 'Represents the pioneering spirit; core energy focuses on building independence.',
            2  => 'Carries the frequency of harmony; the central lesson is developing patience and the ability to connect.',
            3  => 'Resonates with creativity; the priority is staying optimistic and open.',
            4  => 'The energy of discipline; requires building a solid foundation and clear principles.',
            5  => 'Represents flexibility; the focus is adapting to change and finding freedom within structure.',
            6  => 'Carries the vibration of responsibility; energy flows toward nurturing and maintaining family.',
            7  => 'Represents inner wisdom; the greatest lesson is deep observation, learning, and distilling truth.',
            8  => 'The frequency of practicality and achievement; the focus is managing resources and building lasting outcomes.',
            9  => 'Resonates with humanity; oriented toward contribution, selflessness, and community values.',
            11 => 'Carries a strong intuitive energy; requires emotional sensitivity and the ability to inspire.',
            22 => 'Represents builder energy; the focus is turning big ideas into lasting legacy.',
            33 => 'Carries a wide-reaching healing frequency; energy flows toward compassion and unconditional service.'
        ];
        return $hints[$number] ?? 'A distinctive energy field — uniquely individual.';
    }

    public static function getLifePathDetail(int $number): string {
        $details = [
            1  => 'Represents independence, pioneering spirit, and leadership. Decisive by nature, with a strong sense of self — always wants autonomy and tends to take the lead in relationships.',
            2  => 'Represents peace, empathy, and deep connection. Gentle by nature, a good listener, always seeking safety and harmony, with a tendency to support from behind the scenes.',
            3  => 'Carries the energy of creativity, optimism, and inspiration. Communicative by nature, expressive, and naturally uplifting to those around them.',
            4  => 'The number of practicality, discipline, and reliability. Values stability, works with purpose, builds on solid ground, and gravitates toward secure commitments.',
            5  => 'Represents freedom, adaptability, and a love of exploration. Drawn to new experiences, resists confinement, adjusts easily, and always brings fresh energy.',
            6  => 'The number of responsibility, love, and family. Warm by nature, with a strong instinct to care for and protect loved ones — the wellbeing of others comes first.',
            7  => 'Carries the energy of intellect, depth, and sharp intuition. Needs private space, enjoys analysis and reflection, and searches for the underlying patterns in everything.',
            8  => 'The number of strength, practicality, and drive for achievement. Values material results, has strong organizational ability, pursues financial independence, and carries a commanding presence.',
            9  => 'Represents compassion, humanity, and selflessness. Lives by high ideals, is deeply empathetic, always wants to give back, and has a great capacity for forgiveness.',
            11 => '<strong>(Master Number)</strong> Represents sharp intuition, empathy, and spiritual depth. Sensitive and sincere, holds high ideals, and seeks profound harmony in all connections.',
            22 => '<strong>(Master Number)</strong> Represents vision, large-scale building, and outstanding execution. Has the ability to turn big ideas into reality and lay foundations that last.',
            33 => '<strong>(Master Number)</strong> Carries the energy of healing, sacrifice, and unconditional love. Takes on great responsibility, deeply forgiving, and has a natural gift for spiritual leadership.'
        ];
        return $details[$number] ?? 'A distinctive energy field.';
    }

    public static function getCompatibilityAnalysis(int $n1, int $n2, int $percent): array {
        if ($percent >= 85) {
            $result = [
                'summary' => "Numbers $n1 and $n2 create a naturally harmonious and resonant energy.",
                'pros'    => "The two energy fields complement each other well, making it easy to set shared goals without distorting who either person is.",
                'cons'    => "Too much comfort and understanding can push the relationship into a static state, with no friction to spark growth.",
                'advice1' => "Aligned energy creates a comfort zone easily; the breakthrough comes from actively seeking new experiences together.",
                'advice2' => "Natural empathy reaches its full potential when defenses come down and deeper desires are brought into the open."
            ];
        } elseif ($percent >= 70) {
            $result = [
                'summary' => "Numbers $n1 and $n2 form a pairing with real potential, but it needs ongoing fine-tuning.",
                'pros'    => "The friction between these energies introduces new perspectives and fills in each other's blind spots.",
                'cons'    => "Differences in life priorities can trigger defensive reactions when communication breaks down.",
                'advice1' => "Different paces of processing require both people to step back and give the other room to recalibrate.",
                'advice2' => "What holds this relationship together is the willingness to listen and sit with opposing viewpoints."
            ];
        } else {
            $result = [
                'summary' => "Numbers $n1 and $n2 bring opposing energies that create both resistance and attraction.",
                'pros'    => "Strong friction forces both people to push past personal limits and reach a greater emotional maturity.",
                'cons'    => "Opposite ways of processing and reacting can produce conflicts that wear both sides down.",
                'advice1' => "Personal boundaries need to be clearly defined; any attempt to impose standards will create a strong pushback.",
                'advice2' => "Balance only appears when differences are treated as natural, not as flaws that need to be fixed."
            ];
        }

        if (($n1 == 5 && in_array($n2, [1, 10])) || ($n2 == 5 && in_array($n1, [1, 10]))) {
            $result['summary'] = "Numbers $n1 and $n2 are a high-intensity collision between a drive to lead and an instinct for freedom.";
            $result['pros']    = "Personal space is fully respected. The unpredictability of this pairing keeps the physical pull and curiosity alive.";
            $result['cons']    = "Two strong independent egos can push apart if there is no shared anchor — no common goal to hold things in place.";

            $result['advice1'] = ($n1 == 5) ?
                "A constant need for movement and novelty can erode the structure of commitment if it is not anchored to a long-term direction." :
                "Any impulse to control or impose will directly trigger a defensive response — the other person will push back to protect their sense of freedom.";

            $result['advice2'] = ($n2 == 5) ?
                "A constant need for movement and novelty can erode the structure of commitment if it is not anchored to a long-term direction." :
                "Any impulse to control or impose will directly trigger a defensive response — the other person will push back to protect their sense of freedom.";
        }

        if (($n1 == 5 && $n2 == 11) || ($n1 == 11 && $n2 == 5)) {
            $result['summary'] = "Numbers $n1 and $n2 meet at a complex intersection of physical restlessness and spiritual depth.";
            $result['pros']    = "A clear balancing dynamic: the active energy of 5 breaks 11 out of stillness, while 11 acts as an emotional anchor.";
            $result['cons']    = "Different amplitudes: the outward energy release of 5 can easily overwhelm the sensitive emotional system of 11.";

            $result['advice1'] = ($n1 == 5) ?
                "Synchronization works best when the fast-moving pace is adjusted to meet the deep emotional field and sensitive responses of the other person." :
                "Balance is maintained by expanding the inner sense of safety to accommodate the constant flow of shifting energy from the environment.";

            $result['advice2'] = ($n2 == 5) ?
                "Synchronization works best when the fast-moving pace is adjusted to meet the deep emotional field and sensitive responses of the other person." :
                "Balance is maintained by expanding the inner sense of safety to accommodate the constant flow of shifting energy from the environment.";
        }

        return $result;
    }

    public static function getMatchHint(int $percent): string {
        if ($percent >= 90) return 'Nearly perfect compatibility. Ideal conditions for a lasting bond.';
        if ($percent >= 80) return 'Highly compatible. Understanding each other and sharing common goals comes naturally.';
        if ($percent >= 70) return 'A strong match, but staying in sync takes some active effort.';
        if ($percent >= 60) return 'Compatible but inconsistent. Requires significant compromise to balance each other out.';
        return 'A challenging combination. Opposing energies are at play.';
    }

    public static function getSoulUrgeHint(int $number): string {
        $hints = [
            1  => 'A strong drive for autonomy; the core need is to have ability recognized.',
            2  => 'The deepest motivation is understanding, inner peace, and a sense of belonging.',
            3  => 'A need to stand out, express individuality, and expand social connections.',
            4  => 'The subconscious demands a stable, ordered, and clearly structured environment.',
            5  => 'The inner drive is constant movement and the urge to break through limitations.',
            6  => 'The deepest need is to care for others and receive genuine appreciation in return.',
            7  => 'Requires quiet, uninterrupted space to observe and reflect without outside noise.',
            8  => 'A strong desire for measurable achievement and material security.',
            9  => 'The need to bring high ideals to life and serve something larger than oneself.',
            11 => 'A longing for spiritual resonance, beyond ordinary material values.',
            22 => 'Driven to build systems or structures of significant scale and impact.',
            33 => 'A deep desire to ease pain and radiate unconditional love.'
        ];
        return $hints[$number] ?? 'An inner drive that is complex and not easily defined.';
    }

    public static function getAttitudeHint(int $number): string {
        $hints = [
            1 => 'Active reflex to confront and protect personal boundaries with high self-regard.',
            2 => 'A listening and yielding reflex, with a built-in tendency to avoid direct conflict.',
            3 => 'Uses optimism or expressive communication to defuse and redirect tension.',
            4 => 'A reflex to define right and wrong through logic and practical rules.',
            5 => 'Quick and impulsive reflex, with an immediate rejection of anything that feels restrictive.',
            6 => 'Tends to take on responsibility, with an easily triggered instinct to worry.',
            7 => 'A quiet reflex — withdraws inward to observe and process information independently.',
            8 => 'Uses direct rational force, staying fully focused on solving the problem and getting results.',
            9 => 'A forgiving reflex, willing to overlook small issues in favor of the larger picture of peace.'
        ];
        return $hints[$number] ?? 'A variable response range with no fixed pattern.';
    }

    public static function getRelationshipHint(int $number): string {
        $hints = [
            1 => 'The key to sustaining this relationship is absolute respect for personal boundaries.',
            2 => 'The core attraction comes from sensitivity, empathy, and the ability to heal.',
            3 => 'The main anchor is open communication and the ability to share interests freely.',
            4 => 'Energy centers on building financial stability and a consistent daily structure.',
            5 => 'Requires constant renewal of shared experiences to prevent the feeling of being trapped.',
            6 => 'Top priority goes to a strong sense of responsibility and the instinct to build a home.',
            7 => 'Lasting connection comes through intellectual exchange and respect for each other\'s quiet time.',
            8 => 'Strong alignment on material goals and shared power when both agree on a common direction.',
            9 => 'The greatest challenge is letting go of ego to make room for genuine acceptance of the other.'
        ];
        return $hints[$number] ?? 'An energy structure that extends beyond the standard range.';
    }
}