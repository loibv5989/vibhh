<?php

if (!defined("ABSPATH")) {
    exit();
}

return [
    "meta" => [
        "schema_version" => "2.5",
        "updated_at" => "2026-04-14",
        "coverage" => [
            "12 zodiac signs",
            "birth date",
            "element",
            "ruling planet",
            "quality",
            "yin-yang polarity",
            "decans",
            "5-layer compatibility (Aspects, Elements, Qualities, Yin-Yang, Decans)",
            "cusps",
        ],
        "notes" => [],
    ],
    "signs" => [
        "aries" => [
            "id" => "aries",
            "name" => "Aries",
            "symbol" => "♈",
            "element" => "Fire",
            "planet" => "Mars",
            "quality" => "Cardinal (Pioneering, decisive)",
            "polarity" => "Yang (Masculine)",
            "keywords" => "Assertive, Independent, Impulsive, Impatient",
            "start_m" => 3,
            "start_d" => 21,
            "date_range" => ["start" => "03-21", "end" => "04-19"],
            "compatibility" => [
                "best_match" => ["leo", "sagittarius"],
                "worst_match" => ["cancer", "capricorn"],
                "karmic_match" => ["libra"],
            ],
            "decans" => [
                1 => [
                    "days" => ["03-21", "03-30"],
                    "ruler" => "Mars",
                    "vibe" =>
                        "{Pure Aries energy|Raw Aries nature}: {intense, fast, self-driven, with a sharp competitive spirit|fiery, decisive, honors the self, always ready for the next race}.",
                ],
                2 => [
                    "days" => ["03-31", "04-09"],
                    "ruler" => "Sun",
                    "vibe" =>
                        "{Leo-infused resonance|Blended with Leo's charisma}: {warm, proud, drawn to the center stage, born to lead|generous, self-assured, craves the spotlight, carries a natural authority}.",
                ],
                3 => [
                    "days" => ["04-10", "04-19"],
                    "ruler" => "Jupiter",
                    "vibe" =>
                        "{Sagittarius-influenced energy|Touched by Sagittarius}: {freedom-loving, experience-hungry, broad-minded, endlessly optimistic|restless, boundary-pushing, forward-looking, with a rare supply of positive drive}.",
                ],
            ],
            "horoscope_life" =>
                "{An Aries life is a series of conquests|For Aries, living means a nonstop push forward}. {You carry the energy of a pioneer, always stepping up and facing challenges head-on|You hold the fire of a trailblazer, ready to break through every barrier}.",

            "personality" => [
                "core" =>
                    "{You move fast, prefer to lead, and react before you hesitate|Your pace is always at maximum; action comes before overthinking}; {challenges fuel you, and the feeling of opening your own path excites you most|you crave the win and want to be the first to reach the target}.",
                "strengths" => [
                    "decisive",
                    "proactive",
                    "passionate",
                    "unafraid to start",
                    "handles pressure well",
                ],
                "weaknesses" => [
                    "quick-tempered",
                    "impatient",
                    "easily bored",
                    "too blunt",
                    "finds it hard to yield",
                ],
                "love" =>
                    "{In love, you like clarity and directness; endless ambiguity does not work for you|When you care, you show it openly, prefer to take the lead, and dislike relationships that stay unclear}.",
                "career" =>
                    "{Suits competitive environments, startups, sales, business, sports, operations, or project management|Thrives under high pressure in roles like founder, sales lead, team captain, athlete, or execution manager}.",

                "layers" => [
                    "element" =>
                        "{Fire: The Fire element highlights action, passion, initiative, and quick reactions|Fire element: Brings bursts of energy, courage, leadership, and split-second decision-making}.",
                    "planet" =>
                        "{Mars: Boosts initiative, intensity, competitiveness, and the drive to act|Ruling planet Mars: Amplifies fighting spirit, boldness, and abundant energy}.",
                    "quality" =>
                        "{Cardinal (Pioneering, decisive): The Cardinal quality prioritizes starting, leading, and setting the pace|Cardinal group: Marked by a hunger to be first, open roads, and take control of situations}.",
                    "polarity" =>
                        "{Yang (Masculine): Yang polarity leans toward outward expression, action, and active interaction|Yang pole: Represents external energy, directness, and a wish to impact the world}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 leans on pure pioneering instinct; acts immediately and hates roadblocks|With Decan 1, the pioneering streak is exceptionally strong, waiting is not an option, and you carve your own way}.",
                    2 => "{Decan 2 focuses on personal image, the need for recognition, and the power to guide others|Decan 2 adds presence, a hunger for influence, and the ability to own a crowd}.",
                    3 => "{Decan 3 stands for openness, freedom, and breaking old limits|Decan 3 is strong on exploration, loves experiences, and rejects confinement}.",
                ],
                "shadow" =>
                    "{The weak spot is reacting too fast and creating friction before weighing things carefully|Impulsiveness often leads to mistakes; it can hurt others before you think it through}.",
            ],
        ],
        "taurus" => [
            "id" => "taurus",
            "name" => "Taurus",
            "symbol" => "♉",
            "element" => "Earth",
            "planet" => "Venus",
            "quality" => "Fixed (Steady, grounded)",
            "polarity" => "Yin (Feminine)",
            "keywords" => "Patient, Practical, Sensual, Stubborn",
            "start_m" => 4,
            "start_d" => 20,
            "date_range" => ["start" => "04-20", "end" => "05-20"],
            "compatibility" => [
                "best_match" => ["virgo", "capricorn"],
                "worst_match" => ["leo", "aquarius"],
                "karmic_match" => ["scorpio"],
            ],
            "decans" => [
                1 => [
                    "days" => ["04-20", "04-29"],
                    "ruler" => "Venus",
                    "vibe" =>
                        "{Pure Taurus energy|Raw Taurus nature}: {cherishes beauty, values material security, extremely steady but sometimes quite rigid|enjoys comfort, financial safety, solid as rock, very hard to budge}.",
                ],
                2 => [
                    "days" => ["04-30", "05-10"],
                    "ruler" => "Mercury",
                    "vibe" =>
                        "{Virgo-influenced resonance|Mixed with Virgo traits}: {practical, sharp, detail-oriented, with an excellent analytical mind for finances|careful, logical, disciplined, skilled at managing resources}.",
                ],
                3 => [
                    "days" => ["05-11", "05-20"],
                    "ruler" => "Saturn",
                    "vibe" =>
                        "{Capricorn-influenced energy|A strong Capricorn stamp}: {iron discipline, ambitious, rational, works with astonishing persistence|serious, driven, pragmatic, relentlessly patient until the goal is met}.",
                ],
            ],
            "horoscope_life" =>
                "{Taurus aims for stability and what lasts|Your life is a graph of steady growth}. {You build real value and enjoy the fruit of your labor|You were born to establish solid foundations and know how to appreciate well-earned comfort}.",

            "personality" => [
                "core" =>
                    "{You go slow but sure, prefer steadiness, have your own taste, and want to create tangible, lasting worth|Safety comes first for you; you favor consistency, hold a refined sense of beauty, and long to turn every idea into something of real value}.",
                "strengths" => [
                    "persistent",
                    "practical",
                    "enduring",
                    "good sensory awareness",
                    "keeps a stable rhythm",
                ],
                "weaknesses" => [
                    "stubborn",
                    "resists change",
                    "slow to start",
                    "highly possessive",
                ],
                "love" =>
                    "{In love, you need safety, steadiness, and a feeling of trust before you open up|When you commit, trust and stability come first, and you give your feelings fully only after you feel secure}.",
                "career" =>
                    "{Suits finance, real estate, design, food, commerce, asset management, or any work that demands endurance|Shines in economics, architecture, art, restaurants, sales, risk management, or any role that requires staying power}.",

                "layers" => [
                    "element" =>
                        "{Earth: The Earth element highlights practicality, stability, persistence, and the need to build firm foundations|Earth element: Stands for steadiness, calm, the ability to accumulate, and deep roots in reality}.",
                    "planet" =>
                        "{Venus: Enhances aesthetics, attraction, the need for connection, and the ability to create harmony|Ruling planet Venus: Brings refined artistic taste, a love for beauty, and a craving to enjoy life}.",
                    "quality" =>
                        "{Fixed (Steady, grounded): The Fixed quality prioritizes maintaining, concentrating, persisting, and holding the course|Fixed group: Marked by resilient endurance, loyalty, and the ability to protect long-term achievements}.",
                    "polarity" =>
                        "{Yin (Feminine): Yin polarity leans inward, receiving, feeling, and nurturing within|Yin pole: Represents magnetic energy, stillness, and the capacity to gather resources}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is slow, solid, loves beauty, and is very difficult to move|The purest Taurus traits sit in Decan 1: unhurried, stable, and impossible to force into change}.",
                    2 => "{Decan 2 is sharper mentally, more pragmatic, and knows how to calculate|Economic thinking stands out in Decan 2: smart at managing and analyzing}.",
                    3 => "{Decan 3 increases ambition, iron discipline, and the stamina to accumulate big results|Decan 3 carries immense pressure tolerance to achieve lasting glory and long-term ambition}.",
                ],
                "shadow" =>
                    "{The weak spot is clinging to the comfort zone too long and missing necessary shifts|Stubbornness keeps you stuck in stability, reluctant to leap when the moment arrives}.",
            ],
        ],
        "gemini" => [
            "id" => "gemini",
            "name" => "Gemini",
            "symbol" => "♊",
            "element" => "Air",
            "planet" => "Mercury",
            "quality" => "Mutable (Multi-faceted, adaptable)",
            "polarity" => "Yang (Masculine)",
            "keywords" =>
                "Curious, Expressive, Inconsistent, Unpredictable",
            "start_m" => 5,
            "start_d" => 21,
            "date_range" => ["start" => "05-21", "end" => "06-20"],
            "compatibility" => [
                "best_match" => ["libra", "aquarius"],
                "worst_match" => ["virgo", "pisces"],
                "karmic_match" => ["sagittarius"],
            ],
            "decans" => [
                1 => [
                    "days" => ["05-21", "05-31"],
                    "ruler" => "Mercury",
                    "vibe" =>
                        "{Pure Gemini energy|Raw Gemini nature}: {quick mind, flexible, curious about everything but sometimes lacks deep focus|sharp, multitasking, loves new information but easily distracted by the next new thing}.",
                ],
                2 => [
                    "days" => ["06-01", "06-10"],
                    "ruler" => "Venus",
                    "vibe" =>
                        "{Libra-influenced energy|Touched by Libra}: {refined, gracious, magnetic in social settings, and perceptive about what others feel|charming, tactful, skilled socially, able to sense what people want}.",
                ],
                3 => [
                    "days" => ["06-11", "06-20"],
                    "ruler" => "Uranus",
                    "vibe" =>
                        "{Aquarius-influenced energy|Aquarius blood in Gemini}: {original mind, carries a quiet rebellion, likes to go against the crowd, wildly creative|inventive, unusual, independent, rejects molds, always has breakthrough ideas}.",
                ],
            ],
            "horoscope_life" =>
                "{Gemini lives in a constant flow of information and ideas|Your life is a series of conversations and intellectual discoveries}. {Your path explores the world through a sharp mind and multi-angle communication|You were born to link data through versatile perspectives and agile language skills}.",

            "personality" => [
                "core" =>
                    "{You run on information, quick curiosity, connecting ideas, and rarely see only one side|You live through data, grasp things fast, skillfully join scattered pieces, and always view matters from several angles}.",
                "strengths" => [
                    "smart",
                    "flexible",
                    "great communicator",
                    "quick-witted",
                    "fast learner",
                ],
                "weaknesses" => [
                    "lacks consistency",
                    "easily scattered",
                    "talks more than acts",
                    "struggles to go deep",
                ],
                "love" =>
                    "{In love, you need conversation, freshness, and a sense that your minds are connected|When you care, you prioritize intellectual connection, space for dialogue, and mental harmony}.",
                "career" =>
                    "{Suits media, content, marketing, languages, education, technology, or roles that handle multiple information streams|Shines in journalism, PR, advertising, translation, teaching, IT, or any work that requires multitasking}.",

                "layers" => [
                    "element" =>
                        "{Air: The Air element highlights thinking, communication, social nature, and linking ideas|Air element: Stands for intellect, words, constant movement, and spreading information}.",
                    "planet" =>
                        "{Mercury: Boosts thinking, analysis, language, and information intake|Ruling planet Mercury: Brings sharpness, logic, and lightning reflexes toward everything around you}.",
                    "quality" =>
                        "{Mutable (Multi-faceted, adaptable): The Mutable quality prioritizes adapting, pivoting, learning fast, and handling parallel threads|Mutable group: Marked by flexibility, changeability, and remarkable improvisation in any situation}.",
                    "polarity" =>
                        "{Yang (Masculine): Yang polarity leans toward outward expression, action, and active interaction|Yang pole: Represents extroverted energy, openness, and a strong urge to connect with others}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is quick-minded, sharp, and constantly shifts exploration direction|The purest Gemini traits are in Decan 1: clever, talkative, endlessly hunting for the next interesting thing}.",
                    2 => "{Decan 2 adds extra charm and social skill, with smoother communication|More graceful in Decan 2, with excellent diplomacy and community connection}.",
                    3 => "{Decan 3 is creative, carries a reformer spirit, and thinks differently|Breakthrough creativity in Decan 3, fiercely independent, and never stops innovating}.",
                ],
                "shadow" =>
                    "{The weak spot is scattering energy because too many ideas run at once|A lack of focus is the biggest barrier; when too many choices appear, you lose direction}.",
            ],
        ],
        "cancer" => [
            "id" => "cancer",
            "name" => "Cancer",
            "symbol" => "♋",
            "element" => "Water",
            "planet" => "Moon",
            "quality" => "Cardinal (Protective, defensive)",
            "polarity" => "Yin (Feminine)",
            "keywords" =>
                "Sensitive, Home-oriented, Moody, Holds grudges",
            "start_m" => 6,
            "start_d" => 22,
            "date_range" => ["start" => "06-21", "end" => "07-22"],
            "compatibility" => [
                "best_match" => ["scorpio", "pisces"],
                "worst_match" => ["aries", "libra"],
                "karmic_match" => ["capricorn"],
            ],
            "decans" => [
                1 => [
                    "days" => ["06-21", "07-01"],
                    "ruler" => "Moon",
                    "vibe" =>
                        "{Pure Cancer energy|Raw Cancer nature}: {overflowing feelings, extremely sensitive, home-focused, with a strong protective, nurturing instinct|emotional, intuitive, values roots, always wants to shelter loved ones}.",
                ],
                2 => [
                    "days" => ["07-02", "07-11"],
                    "ruler" => "Pluto",
                    "vibe" =>
                        "{Scorpio-influenced resonance|Blended with Scorpio traits}: {deep, mysterious, sharp psychic intuition, with a tendency toward possessiveness and emotional control|profound, somewhat secretive, psychologically perceptive, prefers tight bonds with an owning quality}.",
                ],
                3 => [
                    "days" => ["07-12", "07-22"],
                    "ruler" => "Neptune",
                    "vibe" =>
                        "{Pisces-influenced energy|Touched by Pisces}: {dreamy, romantic, full of empathy but tends to escape reality when hurt|compassionate, artistic, easily feels others' pain, but prone to retreat into fantasy for self-protection}.",
                ],
            ],
            "horoscope_life" =>
                "{Cancer seeks safety within the emotional world|Your life is a journey toward building a peaceful harbor}. {You build a home, nurture relationships, and protect what you love|Your mission is to grow love, connect souls, and shield sacred inner values}.",

            "personality" => [
                "core" =>
                    "{You live through feelings, memories, the need for inner safety, and you are highly sensitive to your surroundings|Your soul is woven from memories; you always crave a solid anchor and can pick up others' emotions extremely well}.",
                "strengths" => [
                    "caring",
                    "empathetic",
                    "strong intuition",
                    "protective of loved ones",
                    "loyal",
                ],
                "weaknesses" => [
                    "oversensitive",
                    "easily defensive",
                    "struggles to let go of the past",
                    "emotional swings",
                ],
                "love" =>
                    "{In love, you need peace of mind, attentive care, and a sense of belonging|When you commit, trust and small acts of care come first, along with a deep promise of attachment}.",
                "career" =>
                    "{Suits healthcare, education, customer service, hospitality, real estate, human resources, or support roles|Shines in caregiving, teaching, community support, F&B, home design, people management, or any position that needs heartfelt dedication}.",

                "layers" => [
                    "element" =>
                        "{Water: The Water element highlights intuition, emotion, empathy, and inner depth|Water element: Stands for nurturing, the flow of the soul, a protective instinct, and extreme sensitivity}.",
                    "planet" =>
                        "{Moon: Enhances emotions, nurturing instinct, and the need for security|Ruling planet Moon: Governs memory, gentleness, and a deep wish for a peaceful home}.",
                    "quality" =>
                        "{Cardinal (Protective, defensive): The Cardinal quality prioritizes guarding, actively leading, and holding the role of safety controller|Cardinal group: Marked by the ability to create a circle of safety and fiercely defend territory and loved ones}.",
                    "polarity" =>
                        "{Yin (Feminine): Yin polarity leans inward, receiving, feeling, and nurturing within|Yin pole: Represents passive energy, sheltering, deep empathy, and memory keeping}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 leans on protective instinct, clear emotions, and a very strong need for family|Decan 1 is strong in parental energy, cherishes roots, and loves unconditionally}.",
                    2 => "{Decan 2 is deeper, more private, has strong intuition, and a tendency to control feelings|Decan 2 is more mysterious and internally powerful; good at holding back and understanding psychological layers}.",
                    3 => "{Decan 3 is soft, full of compassion, but easily lost in emotion|Decan 3 carries an artist's soul, forgiving, but also easily wounded by harsh reality}.",
                ],
                "shadow" =>
                    "{The weak spot is carrying old emotions into the present and being too guarded|The past often chains you, causing you to build walls that are too thick}.",
            ],
        ],
        "leo" => [
            "id" => "leo",
            "name" => "Leo",
            "symbol" => "♌",
            "element" => "Fire",
            "planet" => "Sun",
            "quality" => "Fixed (Radiant, central)",
            "polarity" => "Yang (Masculine)",
            "keywords" => "Proud, Generous, Dominating, Image-conscious",
            "start_m" => 7,
            "start_d" => 23,
            "date_range" => ["start" => "07-23", "end" => "08-22"],
            "compatibility" => [
                "best_match" => ["aries", "sagittarius"],
                "worst_match" => ["taurus", "scorpio"],
                "karmic_match" => ["aquarius"],
            ],
            "decans" => [
                1 => [
                    "days" => ["07-23", "08-01"],
                    "ruler" => "Sun",
                    "vibe" =>
                        "{Pure Leo energy|Raw Leo nature}: {radiant, proud, born to be the center of attention, refuses to be anyone's shadow|majestic, warm, shining, always craves recognition and affirms a leading position}.",
                ],
                2 => [
                    "days" => ["08-02", "08-12"],
                    "ruler" => "Jupiter",
                    "vibe" =>
                        "{Sagittarius-influenced energy|Touched by Sagittarius}: {big-hearted, philosophical, freedom-loving, has a far-reaching vision, and softens the typical stubborn streak|open-minded, confident, wise, enjoys new experiences, can map out a broad future, and listens better}.",
                ],
                3 => [
                    "days" => ["08-13", "08-22"],
                    "ruler" => "Mars",
                    "vibe" =>
                        "{Aries-influenced energy|Strength from Aries}: {intense, fast, extremely decisive, never backs down from any challenge|assertive, burning with fighting fire, acts without hesitation, and confronts difficulty head-on}.",
                ],
            ],
            "horoscope_life" =>
                "{Leo is born to shine and be the center|You carry the mandate of radiance and leadership}. {Your life is a stage to express your ego, inspire others, and confirm your unique worth|Your mission is to claim your individuality, spread positive energy, and prove your distinct mettle to the world}.",

            "personality" => [
                "core" =>
                    "{You want to be seen, acknowledged, and live true to the character of a leader|You crave recognition, respect, and always behave like a true commander}.",
                "strengths" => [
                    "confident",
                    "generous",
                    "inspiring",
                    "creative",
                    "natural leader",
                ],
                "weaknesses" => [
                    "proud",
                    "image-controlling",
                    "ego-driven",
                    "hates being ignored",
                ],
                "love" =>
                    "{In love, you need admiration, respect, and clear treatment|When you care, you expect to be treasured like royalty, with absolute loyalty and grand romantic gestures}.",
                "career" =>
                    "{Suits stage, management, personal branding, creative work, events, team leadership, or roles that carry ultimate responsibility|Shines in arts, administration, branding, event planning, entertainment, team leading, or any spot that demands the highest accountability}.",

                "layers" => [
                    "element" =>
                        "{Fire: The Fire element highlights action, passion, initiative, and quick reactions|Fire element: Brings radiance, warmth, a desire to shine, and endless creative energy}.",
                    "planet" =>
                        "{Sun: Strengthens ego, will, confidence, and the need to shine|Ruling planet Sun: Represents power, life, and a proud self-esteem that cannot be dimmed}.",
                    "quality" =>
                        "{Fixed (Radiant, central): The Fixed quality prioritizes holding a steady rhythm, staying on target, and keeping attraction alive long-term|Fixed group: Marked by loyalty, firmness, and the ability to sustain a lasting flame of passion}.",
                    "polarity" =>
                        "{Yang (Masculine): Yang polarity leans toward outward expression, action, and active interaction|Yang pole: Represents bold energy, openness, and a fierce need to assert oneself before an audience}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is radiant, proud, and intensely wants the center|Decan 1 is the proudest, warmest, and always finds a way to shine}.",
                    2 => "{Decan 2 expands vision, becomes less rigid, and expresses generosity more freely|Decan 2 is wiser, more magnanimous, and knows how to win hearts through intellect}.",
                    3 => "{Decan 3 is fierce, has a fighting spirit, and very strong momentum|Decan 3 is powerful, action-packed, and not afraid of conflict to defend honor}.",
                ],
                "shadow" =>
                    "{The weak spot is that when the ego is hurt, you tend to react with excessive pride|An oversized ego is your Achilles heel, making you domineering and emotionally hard to control}.",
            ],
        ],
        "virgo" => [
            "id" => "virgo",
            "name" => "Virgo",
            "symbol" => "♍",
            "element" => "Earth",
            "planet" => "Mercury",
            "quality" => "Mutable (Analytical, service-oriented)",
            "polarity" => "Yin (Feminine)",
            "keywords" => "Perfectionist, Meticulous, Critical, Dedicated",
            "start_m" => 8,
            "start_d" => 23,
            "date_range" => ["start" => "08-23", "end" => "09-22"],
            "compatibility" => [
                "best_match" => ["taurus", "capricorn"],
                "worst_match" => ["gemini", "sagittarius"],
                "karmic_match" => ["pisces"],
            ],
            "decans" => [
                1 => [
                    "days" => ["08-23", "09-02"],
                    "ruler" => "Mercury",
                    "vibe" =>
                        "{Pure Virgo energy|Raw Virgo nature}: {sharp mind, ability to analyze details to perfection, extremely practical, values logic|careful, logical, profoundly meticulous, respects real-world effectiveness, always relies on solid data}.",
                ],
                2 => [
                    "days" => ["09-03", "09-12"],
                    "ruler" => "Saturn",
                    "vibe" =>
                        "{Capricorn-influenced energy|A Capricorn imprint}: {hidden huge ambition, iron discipline, values status and career more than trivial details|serious, persistent, highly self-controlled, always eyes long-term results instead of frivolous things}.",
                ],
                3 => [
                    "days" => ["09-13", "09-22"],
                    "ruler" => "Venus",
                    "vibe" =>
                        "{Taurus-influenced energy|A Taurus shade}: {gentle, practical, loves beauty, tends to prioritize comfort, enjoys life's pleasures|refined, pragmatic, has subtle aesthetic taste, knows how to balance work and personal enjoyment}.",
                ],
            ],
            "horoscope_life" =>
                "{Virgo pursues perfection in every detail|Your life is a journey of refinement and optimization}. {Your path is about improving, serving, and finding meaning through meticulous dedication|You were born to make the world tidier and confirm your worth through precise work results}.",

            "personality" => [
                "core" =>
                    "{You see the world through logic, standards, order, and want everything correctly arranged and truly useful|In your eyes, the world is a data system that demands rules, accuracy, and rejects sloppiness or meaninglessness}.",
                "strengths" => [
                    "meticulous",
                    "analytical",
                    "responsible",
                    "practical",
                    "dedicated",
                ],
                "weaknesses" => [
                    "perfectionist",
                    "critical",
                    "fault-finding",
                    "self-pressuring",
                    "hard to satisfy",
                ],
                "love" =>
                    "{In love, you express it through concrete care, timely presence, and small details|When you commit, you use practical actions to prove yourself through quiet dedication and attentive care for your partner's every need}.",
                "career" =>
                    "{Suits quality control, data, processes, editing, accounting, operations, healthcare, or any work that demands precision|Shines in auditing, systems analysis, logistics, medicine, finance, risk management, or any role requiring absolute carefulness}.",

                "layers" => [
                    "element" =>
                        "{Earth: The Earth element highlights practicality, stability, persistence, and the need to build firm foundations|Earth element: Stands for organization, process, caution, and detailed analytical ability}.",
                    "planet" =>
                        "{Mercury: Boosts thinking, analysis, language, and information intake|Ruling planet Mercury: Brings logical, sharp intelligence along with excellent data processing and problem-solving skills}.",
                    "quality" =>
                        "{Mutable (Analytical, service-oriented): The Mutable quality prioritizes observing, adjusting, analyzing, and serving effectively|Mutable group: Marked by dedication, completion ability, and the capacity to optimize everything to a perfect state}.",
                    "polarity" =>
                        "{Yin (Feminine): Yin polarity leans inward, receiving, feeling, and nurturing within|Yin pole: Represents modest, quiet energy, thoroughness, and silent dedication}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is strong in analysis, logic, and precision in the original Virgo way|Decan 1 models the most standard Virgo traits: extremely careful with a sharp eye for observation}.",
                    2 => "{Decan 2 is heavy on discipline, hidden ambition, and prioritizes long-term achievements over minutiae|Decan 2 is more ambitious and resilient, skilled at leadership and strategic vision}.",
                    3 => "{Decan 3 is softer, more practical, has aesthetic taste, and knows how to enjoy a stable life rhythm|Decan 3 is more refined and aesthetically sensitive, knows how to beautify life through its own meticulousness}.",
                ],
                "shadow" =>
                    "{The weak spot is the loop of self-criticism and standardizing everything to the point of exhaustion|Excessive perfectionism is a shackle that makes you relentlessly hard on yourself and feeling not good enough}.",
            ],
        ],
        "libra" => [
            "id" => "libra",
            "name" => "Libra",
            "symbol" => "♎",
            "element" => "Air",
            "planet" => "Venus",
            "quality" => "Cardinal (Balancing, social)",
            "polarity" => "Yang (Masculine)",
            "keywords" => "Diplomatic, Fair-minded, Indecisive, People-pleasing",
            "start_m" => 9,
            "start_d" => 23,
            "date_range" => ["start" => "09-23", "end" => "10-22"],
            "compatibility" => [
                "best_match" => ["gemini", "aquarius"],
                "worst_match" => ["cancer", "capricorn"],
                "karmic_match" => ["aries"],
            ],
            "decans" => [
                1 => [
                    "days" => ["09-23", "10-02"],
                    "ruler" => "Venus",
                    "vibe" =>
                        "{Pure Libra energy|Raw Libra nature}: {charming, romantic, rejects conflict, always striving for absolute balance|gracious, refined, artistic, detests rudeness, always aims for harmony in all connections}.",
                ],
                2 => [
                    "days" => ["10-03", "10-12"],
                    "ruler" => "Uranus",
                    "vibe" =>
                        "{Aquarius-influenced energy|Touched by Aquarius}: {rational, independent, has a forward-thinking viewpoint, sometimes shows emotional detachment and coolness|wise, unconventional, progressive-minded, knows how to keep personal distance to protect intellectual freedom}.",
                ],
                3 => [
                    "days" => ["10-13", "10-22"],
                    "ruler" => "Mercury",
                    "vibe" =>
                        "{Gemini-influenced energy|Blend with Gemini}: {expressive, clever, outstanding communication skills but easily changeable and inconsistent|quick-witted, tactful, a master diplomat, yet sometimes struggles to make a firm decision}.",
                ],
            ],
            "horoscope_life" =>
                "{Libra always seeks balance and harmony|Your life is a poem of dialogue and aesthetics}. {Your path is the art of reconciling relationships, justice, and beauty in a world full of contradiction|Your mission is to become the bridge between fairness and artistry amid the clashing currents of life}.",

            "personality" => [
                "core" =>
                    "{You seek balance, beauty, and harmony in relationships, and you are very good at reading the atmosphere|Your essence is harmony and connection; you always have a subtle ability to sense the rhythm of relationships}.",
                "strengths" => [
                    "diplomatic",
                    "fair",
                    "refined",
                    "strong aesthetic sense",
                    "skilled connector",
                ],
                "weaknesses" => [
                    "indecisive",
                    "avoids conflict",
                    "depends on others' opinions",
                    "easily lacks a firm stance",
                ],
                "love" =>
                    "{In love, you need beauty, dialogue, and mutual respect|When you care, you love with all your heart and taste, prioritizing civil sharing and gentle romance}.",
                "career" =>
                    "{Suits law, diplomacy, design, communications, consulting, HR, negotiation, or work that balances interests|Shines in justice, PR, art, marketing, psychological counseling, people management, or any role that demands impartiality}.",

                "layers" => [
                    "element" =>
                        "{Air: The Air element highlights thinking, communication, social nature, and linking ideas|Air element: Stands for objectivity, intellect, diplomatic skill, and building social networks}.",
                    "planet" =>
                        "{Venus: Enhances aesthetics, attraction, the need for connection, and the ability to create harmony|Ruling planet Venus: Brings graceful charm, elegance, a love of peace, and top-tier artistic taste}.",
                    "quality" =>
                        "{Cardinal (Balancing, social): The Cardinal quality prioritizes actively coordinating relationships and maintaining balance|Cardinal group: Marked by mediating ability, able to initiate social bonds with finesse}.",
                    "polarity" =>
                        "{Yang (Masculine): Yang polarity leans toward outward expression, action, and active interaction|Yang pole: Represents interactive, expansive energy, friendliness, and a constant orientation toward cooperation}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 leans on grace, harmony, and a craving for peace|Decan 1 represents the purest beauty of Libra, always wanting to dodge useless arguments}.",
                    2 => "{Decan 2 is sharper rationally, more independent, and tends to think differently|Decan 2 is smarter and more individual, keeping a cool head in social situations}.",
                    3 => "{Decan 3 is expressive, quick-witted, and good at handling social moments|Decan 3 is the most flexible and sharp, able to turn situations around with words}.",
                ],
                "shadow" =>
                    "{The weak spot is wanting to please too many sides and losing direction|The effort to maintain harmony at all costs is a cage that easily makes you wishy-washy}.",
            ],
        ],
        "scorpio" => [
            "id" => "scorpio",
            "name" => "Scorpio",
            "symbol" => "♏",
            "element" => "Water",
            "planet" => "Pluto",
            "quality" => "Fixed (Deep, extreme)",
            "polarity" => "Yin (Feminine)",
            "keywords" => "Intense, Perceptive, Guarded, Resentful",
            "start_m" => 10,
            "start_d" => 23,
            "date_range" => ["start" => "10-23", "end" => "11-21"],
            "compatibility" => [
                "best_match" => ["cancer", "pisces"],
                "worst_match" => ["leo", "aquarius"],
                "karmic_match" => ["taurus"],
            ],
            "decans" => [
                1 => [
                    "days" => ["10-23", "11-01"],
                    "ruler" => "Pluto",
                    "vibe" =>
                        "{Pure Scorpio energy|Raw Scorpio nature}: {mysterious, deep, emotions are black-and-white, owns a powerful ability to regenerate from adversity|intense, magnetic, loves and hates clearly, has resilient inner strength to overcome any storm}.",
                ],
                2 => [
                    "days" => ["11-02", "11-11"],
                    "ruler" => "Neptune",
                    "vibe" =>
                        "{Pisces-influenced resonance|Blended with Pisces traits}: {sensitive, sharp intuition, easily affected by emotions, carries a high frequency of spiritual and artistic energy|compassionate, intuitive, secretly romantic, can perceive the invisible}.",
                ],
                3 => [
                    "days" => ["11-12", "11-21"],
                    "ruler" => "Moon",
                    "vibe" =>
                        "{Cancer-influenced energy|Touched by Cancer}: {deeply attached to family, has an extremely powerful protective instinct for loved ones, but a soul full of melancholy|cherishes roots, always a shield for those they love, carries a profoundly complex and sensitive inner world}.",
                ],
            ],
            "horoscope_life" =>
                "{Scorpio explores the depths of life and death|Your life is a journey through the deeper layers of the soul}. {Your path is one of shedding skin, searching for hidden truth, and holding the power of rebirth|You were born to constantly renew, uncover the universe's secrets, and master the energy of transformation}.",

            "personality" => [
                "core" =>
                    "{You live deep, keep things private, rarely show all your cards, but once trust is given, the bond is intense|Your essence is concentration and mystery; you keep a quiet corner, but when trust is earned, feelings run very deep}.",
                "strengths" => [
                    "profound",
                    "resilient",
                    "strong intuition",
                    "high endurance",
                    "powerful regenerative ability",
                ],
                "weaknesses" => [
                    "suspicious",
                    "controlling",
                    "holds onto hurt",
                    "extremist",
                    "hard to open up",
                ],
                "love" =>
                    "{In love, you need depth, loyalty, and a sense of being understood to the core|When you commit, you demand absolute soul resonance, lifelong commitment, and unblemished honesty}.",
                "career" =>
                    "{Suits investigation, research, psychology, risk finance, medicine, security, strategy, or fields that require seeing the real essence|Shines in police work, detective roles, psychology, banking, surgery, risk management, or any job that demands extreme focus}.",

                "layers" => [
                    "element" =>
                        "{Water: The Water element highlights intuition, emotion, empathy, and inner depth|Water element: Represents a complex psychological world, invisible strength, and a profound capacity to feel energy}.",
                    "planet" =>
                        "{Pluto: Increases depth, transformative power, control, and regeneration|Ruling planet Pluto: Stands for rebirth, destruction, creation, underground power, and razor-sharp intuition}.",
                    "quality" =>
                        "{Fixed (Deep, extreme): The Fixed quality prioritizes endurance, deep focus, and being hard to move from a target|Fixed group: Marked by tenacity, supple strength, and bearing immense pressure without yielding}.",
                    "polarity" =>
                        "{Yin (Feminine): Yin polarity leans inward, receiving, feeling, and nurturing within|Yin pole: Represents mysterious, magnetic energy, secrecy, and a fierce capacity to hide the inner world}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is very strong in depth, instinct, and self-transformation|Decan 1 is the embodiment of utmost mystery, rich in inner power and regenerative strength}.",
                    2 => "{Decan 2 is full of intuition, art or spirituality, with more refined emotions|Decan 2 is more sensitive, artistic, compassionate, and creatively gifted}.",
                    3 => "{Decan 3 is attached to family, protective of loved ones, with intense inner feelings|Decan 3 is warmer, more sheltering, absolutely loyal to those within the circle of trust}.",
                ],
                "shadow" =>
                    "{The weak spot is using silence and control as overly thick armor|Excessive suspicion makes you isolate yourself}.",
            ],
        ],
        "sagittarius" => [
            "id" => "sagittarius",
            "name" => "Sagittarius",
            "symbol" => "♐",
            "element" => "Fire",
            "planet" => "Jupiter",
            "quality" => "Mutable (Freedom, philosophy)",
            "polarity" => "Yang (Masculine)",
            "keywords" => "Optimistic, Freedom-loving, Uncommitted, Undisciplined",
            "start_m" => 11,
            "start_d" => 22,
            "date_range" => ["start" => "11-22", "end" => "12-21"],
            "compatibility" => [
                "best_match" => ["aries", "leo"],
                "worst_match" => ["virgo", "pisces"],
                "karmic_match" => ["gemini"],
            ],
            "decans" => [
                1 => [
                    "days" => ["11-22", "12-01"],
                    "ruler" => "Jupiter",
                    "vibe" =>
                        "{Pure Sagittarius energy|Raw Sagittarius nature}: {fierce desire for freedom, strong optimism, always focused on the big picture instead of trivial details|loves boundless openness, always sees the bright side, cares about the grand scheme rather than minutiae}.",
                ],
                2 => [
                    "days" => ["12-02", "12-11"],
                    "ruler" => "Mars",
                    "vibe" =>
                        "{Aries-influenced energy|Strength from Aries}: {strong, decisive, passion-driven, carries a fighting spirit and very high competitiveness|assertive, fiery, works with full heart, never backs down from a chosen goal}.",
                ],
                3 => [
                    "days" => ["12-12", "12-21"],
                    "ruler" => "Sun",
                    "vibe" =>
                        "{Leo-influenced energy|Radiance from Leo}: {proud, carries a bright aura, likes to shine, can inspire the community wonderfully|confident, glowing, magnetic, has the quality to become a spiritual guide for the crowd}.",
                ],
            ],
            "horoscope_life" =>
                "{Sagittarius craves freedom and grand truths|Your life is an arrow always flying toward new horizons}. {Your path is an unlimited adventure, searching for great meaning through experience and expanded knowledge|You were born to experience and learn, exploring vast wisdom across this wide world}.",

            "personality" => [
                "core" =>
                    "{You reach out to the wider world, love freedom, truth, experience, and the more confined you feel, the stronger you react|You carry the soul of a wanderer, hungry for wisdom, journeys, and nothing troubles you like confinement}.",
                "strengths" => [
                    "optimistic",
                    "open-minded",
                    "straightforward",
                    "eager to learn",
                    "inspiring",
                ],
                "weaknesses" => [
                    "lacks persistence",
                    "hasty",
                    "too blunt",
                    "avoids commitment",
                ],
                "love" =>
                    "{In love, you need space, fun, and someone who understands your free rhythm|When you care, you value trust, personal freedom, shared ideals, and adventures together}.",
                "career" =>
                    "{Suits education, travel, languages, media, training, market development, or jobs that broaden horizons|Shines in teaching, tourism, translation, marketing, coaching, international business, or any pioneering role}.",

                "layers" => [
                    "element" =>
                        "{Fire: The Fire element highlights action, passion, initiative, and quick reactions|Fire element: Brings a blazing source of energy, enthusiasm, a desire to expand limits, and an adventurous spirit}.",
                    "planet" =>
                        "{Jupiter: Increases expansion, optimism, faith, and vision|Ruling planet Jupiter: Represents luck, philosophy, and a yearning to reach noble truths}.",
                    "quality" =>
                        "{Mutable (Freedom, philosophy): The Mutable quality prioritizes adapting, expanding experience, and shifting action pace|Mutable group: Marked by openness, constant motion, the ability to absorb new cultures, and borderless thinking}.",
                    "polarity" =>
                        "{Yang (Masculine): Yang polarity leans toward outward expression, action, and active interaction|Yang pole: Represents free, open energy, sincerity, directness, and frankness}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is purely optimistic, freedom-loving, and eager to break limits|Decan 1 represents the most open spirit, always finding ways to smash barriers blocking your steps}.",
                    2 => "{Decan 2 is more decisive, acts fast, and has high combativeness|Decan 2 is strong, full of fighting passion, unafraid of confrontation to defend ideals}.",
                    3 => "{Decan 3 stands out, inspires, and likes to be in the guiding position|Decan 3 is the brightest, most magnetic, and always ignites new trends}.",
                ],
                "shadow" =>
                    "{The weak spot is quitting early when the journey loses its excitement|A lack of persistence is the biggest barrier when the initial joy fades}.",
            ],
        ],
        "capricorn" => [
            "id" => "capricorn",
            "name" => "Capricorn",
            "symbol" => "♑",
            "element" => "Earth",
            "planet" => "Saturn",
            "quality" => "Cardinal (Authority, systems)",
            "polarity" => "Yin (Feminine)",
            "keywords" => "Ambitious, Disciplined, Reserved, Pragmatic",
            "start_m" => 12,
            "start_d" => 22,
            "date_range" => ["start" => "12-22", "end" => "01-19"],
            "compatibility" => [
                "best_match" => ["taurus", "virgo"],
                "worst_match" => ["aries", "libra"],
                "karmic_match" => ["cancer"],
            ],
            "decans" => [
                1 => [
                    "days" => ["12-22", "12-31"],
                    "ruler" => "Saturn",
                    "vibe" =>
                        "{Pure Capricorn energy|Raw Capricorn nature}: {serious, huge ambition, works by system and iron discipline, extremely rational in every decision|duty-bound, pragmatic, persistent, process-oriented, always puts goals first}.",
                ],
                2 => [
                    "days" => ["01-01", "01-10"],
                    "ruler" => "Venus",
                    "vibe" =>
                        "{Taurus-influenced energy|A Taurus shade}: {practical yet knows how to enjoy life, cherishes beauty, values financial stability, not dry at all|pragmatic but not lacking aesthetics, loves refinement, knows how to use money to create lasting prosperity}.",
                ],
                3 => [
                    "days" => ["01-11", "01-19"],
                    "ruler" => "Mercury",
                    "vibe" =>
                        "{Virgo-influenced energy|Blend with Virgo}: {sharp analytical mind, logical, particularly detail and process-focused, sometimes overly critical|clever, careful, meticulous, always demands utmost precision, can become excessively perfectionistic}.",
                ],
            ],
            "horoscope_life" =>
                "{Capricorn builds an empire of sustainability|Your life is a journey of conquering challenging peaks}. {Your path climbs to the summit through discipline, persistence, and long-term vision|You were born to stand on the peak of glory through relentless effort and correct strategy}.",

            "personality" => [
                "core" =>
                    "{You think by goals, structure, achievements; slow to open up but very persistent once the road is clear|You live to realize big ambitions; reserved, careful, yet incredibly resilient once a target is chosen}.",
                "strengths" => [
                    "disciplined",
                    "ambitious",
                    "persistent",
                    "practical",
                    "responsible",
                ],
                "weaknesses" => [
                    "cold",
                    "emotionally dry",
                    "self-pressuring",
                    "likes control",
                ],
                "love" =>
                    "{In love, you show it through stability, responsibility, and long-term commitment rather than emotional words|When you commit, you give a solid promise, absolute reliability, and practical future plans instead of empty vows}.",
                "career" =>
                    "{Suits management, finance, executive roles, system building, organization, strategy, or positions demanding a long uphill climb|Shines as CEO, fund manager, politician, construction, HR structuring, planning, or any job that requires endurance}.",

                "layers" => [
                    "element" =>
                        "{Earth: The Earth element highlights practicality, stability, persistence, and the need to build firm foundations|Earth element: Stands for solid ground, materiality, discipline, and a high sense of duty}.",
                    "planet" =>
                        "{Saturn: Increases discipline, responsibility, structure, and stamina|Ruling planet Saturn: Symbolizes time, maturity, the pressure that creates diamonds, and life's essential lessons}.",
                    "quality" =>
                        "{Cardinal (Authority, systems): The Cardinal quality prioritizes building systems, controlling structure, and bearing great responsibility|Cardinal group: Marked by ambition to lead, organizational skill, management capacity, and reaching the top position}.",
                    "polarity" =>
                        "{Yin (Feminine): Yin polarity leans inward, receiving, feeling, and nurturing within|Yin pole: Represents focus, calmness, the ability to work quietly toward major outcomes}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is very original: serious, ambitious, results-first|Decan 1 is the iron person: most disciplined, pragmatic, fully focused on career achievement}.",
                    2 => "{Decan 2 is softer but still practical, knowing how to balance enjoyment and discipline|Decan 2 is sharper, wiser, skilled at financial management, and knows how to enjoy the fruit of hard work}.",
                    3 => "{Decan 3 is razor-logical, meticulous, and optimization-focused down to each detail|Decan 3 is the wisest, most careful, never allowing a small mistake to occur}.",
                ],
                "shadow" =>
                    "{The weak spot is imprisoning yourself in high standards and the pressure to succeed|Excessive severity is your own prison, along with a fear of failure that makes you harsh at times}.",
            ],
        ],
        "aquarius" => [
            "id" => "aquarius",
            "name" => "Aquarius",
            "symbol" => "♒",
            "element" => "Air",
            "planet" => "Uranus",
            "quality" => "Fixed (Unique, community)",
            "polarity" => "Yang (Masculine)",
            "keywords" => "Innovative, Unconventional, Independent, Stubborn",
            "start_m" => 1,
            "start_d" => 20,
            "date_range" => ["start" => "01-20", "end" => "02-18"],
            "compatibility" => [
                "best_match" => ["gemini", "libra"],
                "worst_match" => ["taurus", "scorpio"],
                "karmic_match" => ["leo"],
            ],
            "decans" => [
                1 => [
                    "days" => ["01-20", "01-29"],
                    "ruler" => "Uranus",
                    "vibe" =>
                        "{Pure Aquarius energy|Raw Aquarius nature}: {different, rebellious, has a beyond-its-time vision, always follows its own path, refuses constraints|unique, free, unconventional, the most progressive mindset, cannot be boxed in, craves intellectual liberation}.",
                ],
                2 => [
                    "days" => ["01-30", "02-08"],
                    "ruler" => "Mercury",
                    "vibe" =>
                        "{Gemini-influenced energy|Linked with Gemini}: {excellent communication skills, expressive, reflex-quick thinking, constantly hungry for new knowledge|quick-witted, intelligent talker, sharp mental reflexes, never stops learning new things}.",
                ],
                3 => [
                    "days" => ["02-09", "02-18"],
                    "ruler" => "Venus",
                    "vibe" =>
                        "{Libra-influenced energy|A Libra imprint}: {charming, sociable, wonderful diplomacy, especially values social relationships and fairness|gracious, tactful, skilled at community connection, treasures friendship and humanistic bonds}.",
                ],
            ],
            "horoscope_life" =>
                "{Aquarius carries the mission to change the world|You were born to shatter old norms}. {Your life is a current of breakthrough thought, breaking standards, and shaping the future your own way|Your path is one of innovation, shifting community awareness, and building new values for tomorrow}.",

            "personality" => [
                "core" =>
                    "{You think differently, stay ahead, dislike being boxed in; always care about big ideas for the community, and prioritize the future over existing molds|You possess the mind of someone from the future; you aim for the collective good and believe in the power of change}.",
                "strengths" => [
                    "independent",
                    "creative",
                    "system thinker",
                    "unconventional",
                    "far-sighted",
                ],
                "weaknesses" => [
                    "detached",
                    "stubborn",
                    "unpredictable",
                    "emotionally inconsistent",
                ],
                "love" =>
                    "{In love, you need respect for personal space and someone you can talk to like a friend first|When you care, you value freedom, understanding, and a soulmate connection rather than traditional binds}.",
                "career" =>
                    "{Suits technology, product development, research, innovation, community building, digital media, or projects that break the mold|Shines in IT, R&D, tech creation, social media, startups, applied science, or any role demanding breakthrough thinking}.",

                "layers" => [
                    "element" =>
                        "{Air: The Air element highlights thinking, communication, social nature, and linking ideas|Air element: Stands for cool reason, knowledge, a vision beyond the times, and innovative ideas}.",
                    "planet" =>
                        "{Uranus: Increases independence, unconventionality, fresh thinking, and a different spirit|Ruling planet Uranus: Symbolizes rebellion, breakthrough, enlightenment, and sudden change}.",
                    "quality" =>
                        "{Fixed (Unique, community): The Fixed quality prioritizes keeping identity, pursuing long-term ideas, and connecting community|Fixed group: Marked by loyalty to ideals, defending personal views, and acting for larger social groups}.",
                    "polarity" =>
                        "{Yang (Masculine): Yang polarity leans toward outward expression, action, and active interaction|Yang pole: Represents spreading energy, progress, quirky uniqueness, and a desire to change the world}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is very different, freedom-loving, hard for the majority to subdue|Decan 1 is the most unique, stubborn, and forever loyal to its own distinct identity}.",
                    2 => "{Decan 2 is communication-strong, learns fast, and expands intellectual connections|Decan 2 is more flexible, wiser, good at spreading fresh ideas to everyone}.",
                    3 => "{Decan 3 socializes well, balances collective and beauty in relationships|Decan 3 is the most harmonious, graceful, skilled at mediating community interests and life aesthetics}.",
                ],
                "shadow" =>
                    "{The weak spot is creating emotional distance even while still caring deeply|Detachment sometimes makes you appear cold even though your heart is still with everyone}.",
            ],
        ],
        "pisces" => [
            "id" => "pisces",
            "name" => "Pisces",
            "symbol" => "♓",
            "element" => "Water",
            "planet" => "Neptune",
            "quality" => "Mutable (Empathetic, merging)",
            "polarity" => "Yin (Feminine)",
            "keywords" =>
                "Dreamy, Romantic, Easily influenced, Escapist",
            "start_m" => 2,
            "start_d" => 19,
            "date_range" => ["start" => "02-19", "end" => "03-20"],
            "compatibility" => [
                "best_match" => ["cancer", "scorpio"],
                "worst_match" => ["gemini", "sagittarius"],
                "karmic_match" => ["virgo"],
            ],
            "decans" => [
                1 => [
                    "days" => ["02-19", "02-29"],
                    "ruler" => "Neptune",
                    "vibe" =>
                        "{Pure Pisces energy|Raw Pisces nature}: {dreamy, spiritually connected, deeply sensitive, full of compassion but also easily becomes a victim of its own kindness|imaginative, psychically sensitive, easily moved, forgiving, empathetic but needs to learn to guard itself from being taken advantage of}.",
                ],
                2 => [
                    "days" => ["03-01", "03-10"],
                    "ruler" => "Moon",
                    "vibe" =>
                        "{Cancer-influenced resonance|Cancer blood in Pisces}: {overflowing emotion, strong nurturing instinct, easily hurt, always seeking safety in the family base|full of love, always wanting to shelter, sensitive soul, cherishes home-rooted values}.",
                ],
                3 => [
                    "days" => ["03-11", "03-20"],
                    "ruler" => "Pluto",
                    "vibe" =>
                        "{Scorpio-influenced energy|Power from Scorpio}: {profound empathy, mysterious, can see through others' psychology, owns an intense inner world that sometimes leads to extremes|deep, magnetic, mesmerizing, skilled at reading emotions, carries enormous mental strength and needs to balance passion with reason}.",
                ],
            ],
            "horoscope_life" =>
                "{Pisces merges with the universe of feeling and spirit|Your life is a poetic song of compassion}. {Your path is a journey of empathy, creativity, and seeking connections beyond material limits|Your mission is to bring love to the world and link souls between the visible and invisible realities}.",

            "personality" => [
                "core" =>
                    "{You always sense the world through intuition, see life through an emotional lens, and believe in invisible connections|You take intuition as your compass, often turning the world into shades of the soul, and always treasure the spiritual thread between people}.",
                "strengths" => [
                    "empathetic",
                    "dreamy",
                    "creative",
                    "emotionally sharp",
                    "compassionate",
                ],
                "weaknesses" => [
                    "easily lost",
                    "avoids reality",
                    "strongly affected",
                    "lacks boundaries",
                ],
                "love" =>
                    "{In love, you need gentleness, safety, spiritual harmony, and you often give a great deal|When you care, you yearn for absolute soul connection, pure romance, and are ready to sacrifice for the one you love}.",
                "career" =>
                    "{Suits art, healing, psychology, caregiving, music, film, creative content, or work that uses feeling more than formula|Shines in music, medicine, counseling, cinema, writing, humanitarian fields, or any role that demands high empathy}.",

                "layers" => [
                    "element" =>
                        "{Water: The Water element highlights intuition, emotion, empathy, and inner depth|Water element: Represents the vast ocean of the subconscious, the dissolving of boundaries, and artistic sensitivity}.",
                    "planet" =>
                        "{Neptune: Increases intuition, dreams, compassion, and invisible connections|Ruling planet Neptune: Symbolizes spirituality, illusion, unconditional romance, and a rich imagination}.",
                    "quality" =>
                        "{Mutable (Empathetic, merging): The Mutable quality prioritizes sensing, adapting, empathizing, and delicate receiving|Mutable group: Marked by going with the flow, easily transforming, healing ability, and sacrificing for others}.",
                    "polarity" =>
                        "{Yin (Feminine): Yin polarity leans inward, receiving, feeling, and nurturing within|Yin pole: Represents dreamy, yielding energy, calmness, and a thirst for soul harmony}.",
                ],
                "decan_overlays" => [
                    1 => "{Decan 1 is very dreamy, sensitive, and has a clear spiritual frequency|Decan 1 is the purest: romantic, sensitive, carrying the soul of a true dreamer}.",
                    2 => "{Decan 2 increases nurturing instinct, family orientation, and the need for emotional safety|Decan 2 is warmer, more home-oriented, always longing for a peaceful dock for the heart}.",
                    3 => "{Decan 3 is profound, mysterious, easily senses others' psychological layers|Decan 3 is stronger, more magnetic, can understand the hidden corners of another's soul}.",
                ],
                "shadow" =>
                    "{The weak spot is easily dissolving into emotions and losing footing without boundaries|Excessive sensitivity makes you easily wounded and lost if you don't know how to protect yourself}.",
            ],
        ],
    ],
    "cusps" => [
        "pisces_aries" => [
            "name" => "Cusp of Rebirth",
            "date_range" => ["start" => "03-17", "end" => "03-23"],
            "blend" => "Water x Fire",
            "vibe" =>
                "{The intersection of Pisces' dreamy intuition and Aries' fiery pioneering drive|A blend of a dreamer's soul and a warrior's spirit with the initiating power of Fire}.",
        ],
        "aries_taurus" => [
            "name" => "Cusp of Power",
            "date_range" => ["start" => "04-17", "end" => "04-23"],
            "blend" => "Fire x Earth",
            "vibe" =>
                "{Aries' breakthrough energy combined with Taurus' practical steadiness|The pioneering courage of Aries together with the solid pragmatism of Taurus}.",
        ],
        "taurus_gemini" => [
            "name" => "Cusp of Energy",
            "date_range" => ["start" => "05-17", "end" => "05-23"],
            "blend" => "Earth x Air",
            "vibe" =>
                "{Taurus' calm, practical nature fused with Gemini's flexible intellect and sharp communication|The solidness of Earth paired with the quickness and expressiveness of Air}.",
        ],
        "gemini_cancer" => [
            "name" => "Cusp of Magic",
            "date_range" => ["start" => "06-17", "end" => "06-23"],
            "blend" => "Air x Water",
            "vibe" =>
                "{Gemini's sharp, multi-faceted mind meets Cancer's deep emotional sensitivity|Gemini's agile mind touching Cancer's sensitive, profound soul}.",
        ],
        "cancer_leo" => [
            "name" => "Cusp of Oscillation",
            "date_range" => ["start" => "07-19", "end" => "07-25"],
            "blend" => "Water x Fire",
            "vibe" =>
                "{Cancer's introverted sensitivity creates a subtle contradiction yet blends with Leo's radiant shine|Cancer's gentleness is an interesting contrast to Leo's royal mettle}.",
        ],
        "leo_virgo" => [
            "name" => "Cusp of Exposure",
            "date_range" => ["start" => "08-19", "end" => "08-25"],
            "blend" => "Fire x Earth",
            "vibe" =>
                "{Leo's proud, brilliant aura comes with Virgo's meticulousness, practicality, and perfectionism|Leo's majestic presence alongside Virgo's precise analytical mind}.",
        ],
        "virgo_libra" => [
            "name" => "Cusp of Beauty",
            "date_range" => ["start" => "09-19", "end" => "09-25"],
            "blend" => "Earth x Air",
            "vibe" =>
                "{Virgo's logical analysis perfectly combined with Libra's refined aesthetics and love for harmony|Virgo's precision subtly paired with Libra's artistic soul}.",
        ],
        "libra_scorpio" => [
            "name" => "Cusp of Drama",
            "date_range" => ["start" => "10-19", "end" => "10-25"],
            "blend" => "Air x Water",
            "vibe" =>
                "{Libra's charming social style blended with Scorpio's mystery, intense emotion, and underground power|Libra's grace matched with Scorpio's deep inner strength and transformative power}.",
        ],
        "scorpio_sagittarius" => [
            "name" => "Cusp of Revolution",
            "date_range" => ["start" => "11-18", "end" => "11-24"],
            "blend" => "Water x Fire",
            "vibe" =>
                "{Scorpio's emotional depth and guarded nature meet Sagittarius' optimism, hunger for freedom, and passion for exploration|Scorpio's instinct accompanies Sagittarius' fire of freedom and philosophical vision}.",
        ],
        "sagittarius_capricorn" => [
            "name" => "Cusp of Prophecy",
            "date_range" => ["start" => "12-18", "end" => "12-24"],
            "blend" => "Fire x Earth",
            "vibe" =>
                "{Sagittarius' broad ideals are realized through Capricorn's iron discipline and huge ambition|Sagittarius' noble vision gets materialized by Capricorn's lasting strategy and discipline}.",
        ],
        "capricorn_aquarius" => [
            "name" => "Cusp of Mystery",
            "date_range" => ["start" => "01-16", "end" => "01-22"],
            "blend" => "Earth x Air",
            "vibe" =>
                "{Capricorn's rule-bound discipline collides intriguingly with Aquarius' forward-thinking breakthroughs|Capricorn's seriousness dramatically paired with Aquarius' innovative thought}.",
        ],
        "aquarius_pisces" => [
            "name" => "Cusp of Sensitivity",
            "date_range" => ["start" => "02-15", "end" => "02-21"],
            "blend" => "Air x Water",
            "vibe" =>
                "{Aquarius' wise independence connects deeply with Pisces' dreamy, highly spiritual soul|Aquarius' cool reason interfacing with Pisces' empathetic intuition}.",
        ],
    ],
];