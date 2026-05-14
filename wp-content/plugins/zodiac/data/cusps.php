<?php
if (!defined('ABSPATH')) exit;

return [
    '__meta' => [
        'schema_version' => '3.1.0',
        'dataset_type' => 'architectural_nodes',
        'source_type' => 'astrological_prose',
        'updated_at' => '2026-04-14'
    ],
    '__zodiac_map' => [
        'aries' => ['en' => 'Aries', 'element' => 'fire'],
        'taurus' => ['en' => 'Taurus', 'element' => 'earth'],
        'gemini' => ['en' => 'Gemini', 'element' => 'air'],
        'cancer' => ['en' => 'Cancer', 'element' => 'water'],
        'leo' => ['en' => 'Leo', 'element' => 'fire'],
        'virgo' => ['en' => 'Virgo', 'element' => 'earth'],
        'libra' => ['en' => 'Libra', 'element' => 'air'],
        'scorpio' => ['en' => 'Scorpio', 'element' => 'water'],
        'sagittarius' => ['en' => 'Sagittarius', 'element' => 'fire'],
        'capricorn' => ['en' => 'Capricorn', 'element' => 'earth'],
        'aquarius' => ['en' => 'Aquarius', 'element' => 'air'],
        'pisces' => ['en' => 'Pisces', 'element' => 'water'],
    ],

    'anchors' => [
        'daily' => [
            'fire' => "{With the Fire element's bold energy today|Under the influence of Fire's drive today},",
            'earth' => "{Grounded by the Earth element's steady rhythm|Today's pace shows the Earth element's stillness enveloping you},",
            'air' => "{As Air currents flow and shift|The Air element's interaction today points out that},",
            'water' => "{Water's sharp sensitivity guides your intuition now|As the emotional currents of Water align with today's backdrop},"
        ],
        'weekly' => [
            'fire' => "{Over the next seven days, your inner fire is strongly fueled|The coming days mark an intense pace from the Fire element},",
            'earth' => "{This week's big picture emphasizes the Earth element's accumulation|With Earth energy's patronage this week},",
            'air' => "{The new week opens a cycle of constant movement from the Air element|As the week's atmosphere is shaped by Air's flexibility},",
            'water' => "{Across this week, there are quiet but deep ripples from the Water element|Water's quality will saturate your entire emotional landscape next week},"
        ],
        'monthly' => [
            'fire' => "{Throughout {month}'s cycle, your initiative will find suitable context to shine|{month} opens a vibrant phase under the Fire element's quality},",
            'earth' => "{month} marks a slow but extremely solid pace bearing the Earth element's signature|In this month of {month}, Earth's energy will reinforce every foundation you are building},",
            'air' => "{Stepping into {month}, you will clearly feel the nonstop flow of information from the Air element|{month}'s picture is woven from ideas and connections carrying the Air element's frequency},",
            'water' => "{month}'s rhythm carries the Water element's emotional depth|{month} leads you into a quiet inner phase under Water's quality},"
        ]
    ],

    'daily' => [
        'fire' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{A day where your personal rhythm reaches a breakthrough state|You are stepping into an extremely sharp peak of performance}.",
                    'explanation' => "{Obstacles|Barriers} seem to {fade back automatically|get erased} in front of your {willpower|determination}. Your ability to {see through problems|process information layers} happens at {astonishing speed|beyond expectation}.",
                    'action' => "{Seize this moment|Grasp this opportunity immediately} to {actualize the ideas you've been cherishing|start things you once feared}."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{A sudden outburst may inadvertently create unnecessary friction|The craving to express yourself is coupled with interaction risks}.",
                    'explanation' => "An overpowering desire to {move forward|achieve goals} makes you inadvertently {neglect important details|put pressure on your companions}. {Lack of patience|Being too hasty} is your biggest {blind spot|vulnerability} today.",
                    'action' => "{Slowing down a beat|Becoming still} and {observing the group's attitude|listening to feedback around you} will help you {avoid cracks|protect your work results}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Your natural warmth is creating great magnetism|You become the center of connection thanks to abundant positivity}.",
                    'explanation' => "{Whether short chats or deep interactions|Every encounter today} {brings a pleasant feeling|leaves a very good lingering impression} on those beside you.",
                    'action' => "{Do not hesitate|Be confident} to {express sincere care|share realistic thoughts}, the current situation is {favoring|supporting} your connection."
                ]
            ],
            'money' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 2,
                    'headline' => "{The material flow stays quiet and balanced|There are no big ripples in your financial picture today}.",
                    'explanation' => "Everything {unfolds exactly as you anticipated|lies well within safe control}. This is not the moment {suited for expecting sudden spikes|for game-changing decisions}.",
                    'action' => "{Focus on reinforcing existing foundations|Keep the current accumulation pace} instead of {seeking disruptions|chasing external values}."
                ]
            ]
        ],
        'earth' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Inner steadiness helps you master every situation|You are showing wonderful calmness in the face of surrounding fluctuations}.",
                    'explanation' => "{Tasks requiring caution|Issues needing deep analysis} will be {thoroughly untangled|handled with high precision} by you. {Neat thinking|A practical viewpoint} is your {shield|most solid anchor}.",
                    'action' => "{Continue to maintain|Do not change} {this slow but sure pace|this careful working method}, because it is {leading you in the right direction|creating sustainable value}."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Excessive safety sometimes becomes a barrier to growth|You seem to be locking yourself inside familiar molds}.",
                    'explanation' => "{Stubbornly defending old views|Refusing to accept new perspectives} is {reducing flexibility|slowing your adaptability} to {collective changes|unexpected events}.",
                    'action' => "A bit of {openness to new methods|tolerance for differences} will {make work flow smoother|bring relief} to your mind."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{An invisible distance is created by excessive stillness|There is a blockage in your emotional transmission}.",
                    'explanation' => "You tend to {close up more than usual|retreat into your inner world} and seek quietness. Though without bad intent, this inadvertently makes {close ones|those who care about you} feel {confused|unable to reach you}.",
                    'action' => "Just a {small caring gesture|gentle explanation} is enough to {erase the distance|bring reassurance} to the bond between both sides."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{The material plans you planted are beginning to take firm root|Your quiet accumulation phase is bringing very positive signals}.",
                    'explanation' => "{Your patience|Your management mindset} finally {proves completely correct|creates absolute safety}. {Practical values|Budget limits} are {serving your life well|effectively protecting cash flow}.",
                    'action' => "{Allow yourself|You can totally} {relax a little|enjoy a small comfort} as a reward for {this control ability|your own discipline}."
                ]
            ]
        ],
        'air' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Your ability to link information is reaching maximum sharpness|You act as a great catalyst connecting all scattered elements}.",
                    'explanation' => "{Verbal sharpness|Multi-dimensional understanding} helps you {turn conflicts into consensus|find a way out of collective deadlocks}. Your thinking {operates very efficiently|constantly generates fresh solutions}.",
                    'action' => "{This is the moment|Take advantage of today} to {put forward bold proposals|express unique viewpoints} you once hesitated about."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Data overload is making your mind noisy|Thoughts moving too fast make you lose core focus}.",
                    'explanation' => "The feeling of {wanting to do everything at once|being swept by many different information streams} is {eroding your energy|reducing your performance}. {Lack of consistency|Scattered attention} leaves goals {unfinished|stuck at the idea stage}.",
                    'action' => "{What needs doing right now is|Instead of spreading thin,} {write everything down|filter priorities} and {pick only one destination|focus on one specific action}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 2,
                    'headline' => "{Relationships keep a polite and light distance|The communication atmosphere carries intellect and openness}.",
                    'explanation' => "There are no {overly sentimental emotions or too-deep attachments|stifling bonds}, people {come together with respect for personal space|find joy in multi-topic conversations}.",
                    'action' => "{Enjoy this comfort|Maintain this lightness in dialogue} to {nourish the spirit|keep yourself feeling fresh}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Momentary curiosities can wash away part of your savings|Curiosity is threatening safe spending principles}.",
                    'explanation' => "The pull of {new things|external trends} is {testing your reason|blurring caution lines}. Without {staying alert|holding principles tight}, {financial leaks|unnamed expenses} are hard to avoid.",
                    'action' => "{Be absolutely careful|Step back} before {decisions coming from sudden inspiration|tempting offers lacking practical foundation}."
                ]
            ]
        ],
        'water' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Subtlety leads you through complex issues smoothly today|Empathy becomes an excellent compass for you today}.",
                    'explanation' => "You {sense the most delicate shifts|read what no one says aloud} from the surrounding environment. This helps you {dodge hidden troubles|make reasonable decisions}, {bringing reassurance to the group|creating great invisible value}.",
                    'action' => "{Trust|Do not ignore} {your sharp observation|your first feeling}, as they are {extremely accurate|pointing to the real nature of things}."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 5,
                    'headline' => "{The line between empathy and endurance is blurring|Personal emotions are overflowing and affecting your objectivity}.",
                    'explanation' => "{Absorbing too much negativity from others|caring too much about outside judgments} is making you {tired|lose self-control}. The overall picture gets {distorted|obscured} by {groundless worries|excessive sensitivity}.",
                    'action' => "{You need to immediately|Resolutely} {reset personal protection boundaries|clearly separate external impacts from your own mind} before {falling into confusion|things go too far}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{A day where harmony touches the deepest hidden corners|The link between you and loved ones becomes absolutely strong}.",
                    'explanation' => "{Without needing many words|Only through small gestures}, {understanding|tolerance} still {exists fully|soothes flaws}. This is {a moment of empathy|perfect spiritual medicine} for past rifts.",
                    'action' => "{Let sincerity lead|You are completely safe to} {drop your guard|accept care from those who truly value you}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Emotional swings can cause material leaks|Be careful of the trap of using money to soothe psychology}.",
                    'explanation' => "{When your mood wobbles|Misplaced sympathy} easily turns into {irrational spending decisions|concessions that harm your own position}.",
                    'action' => "{Pause large transactions|Firmly refuse lending requests} until {your mind is truly calm|you regain balance}."
                ]
            ]
        ]
    ],

    'weekly' => [
        'fire' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{The coming days mark a development path full of initiative|You hold the reins to master the whole week's situation}.",
                    'explanation' => "{Past ambitions|Careful preparation} have now converged with enough conditions to {speed up|turn into concrete action}. The surrounding atmosphere {carries healthy competition|urges you to step forward}.",
                    'action' => "{Do not hesitate|Be brave} to {take on responsibility|stand up for your views}, the situation is {giving momentum|paving the way} for your commitment."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 3,
                    'headline' => "{A week where relationships run at a lively pace but lack depth|Interaction happens continuously but mostly stays on the surface}.",
                    'explanation' => "{Meetings|Group activities} {happen thick and fast|bring much laughter}, but for seeking {a complete meeting of souls|a quiet spiritual anchor}, perhaps it is not yet time.",
                    'action' => "{Join the shared joy|Enjoy these happy moments} but {do not set expectations too high|let everything unfold naturally}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{The breakthrough brings clear bright spots to the financial picture|You can generate good income thanks to decisiveness}.",
                    'explanation' => "{Quick decisions|Sharpness to real signals} {bring the advantage of leading|help you grab core opportunities}. Cash flow {is showing positive signs|gets unblocked spectacularly}.",
                    'action' => "However, {once the goal is reached|if benefits have been gained}, {know the stopping point|set up a protective fence for the budget} to {preserve results|avoid overspending}."
                ]
            ]
        ],
        'earth' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{The next seven days are a wonderful time to re-establish personal order|Your patience is gradually shaping very solid results}.",
                    'explanation' => "{Long-term plans|Plans requiring patience} are now {handled thoroughly|achieving big progress} thanks to your logical arrangement ability. {Caution|The slow but sure pace} is {maximizing its effect|proving its correctness}.",
                    'action' => "{Continue maintaining discipline|Stick to the principles set}, your quiet dedication is being recognized."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 3,
                    'headline' => "{Your communication pace this week passes smoothly and safely|The bond with those around you brings a very peaceful feeling}.",
                    'explanation' => "{There are no emotional ups and downs|Sudden romantic surprises are missing}, in return, you find {the value of sincerity|practical support} from those who truly appreciate you.",
                    'action' => "{Sometimes stillness is the best answer|Relax and accept this steady rhythm} instead of trying to force change."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{This week, your income and spending are maintained at an extremely stable level|Your practical nature brings you wonderful clarity in managing assets}.",
                    'explanation' => "{Accumulation efforts|Safe saving decisions} begin to {create real value|protect you from external fluctuations}. Your ability to {control cash flow|perceive material value} is at its best.",
                    'action' => "{You can fully treat yourself to a little comfort|This is a suitable time to set up long-term reserve funds}, as long as everything stays within allowed limits."
                ]
            ]
        ],
        'air' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{Countless new communication streams are awaiting you in the next seven days|The new week opens a multi-dimensional thinking space and very strong connections}.",
                    'explanation' => "{Initiatives|Ideas} continuously {flow|appear} making you {full of energy|excited to act immediately}. Flexible thinking helps you {solve issues smoothly|master every dialogue situation}.",
                    'action' => "{However, pick only one core goal|Need to focus intensely on the most important thing} to {avoid falling into talking much but doing little|ensure ideas are fully realized}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Your distraction may inadvertently create barriers with loved ones|Momentary superficiality easily makes the other person feel abandoned}.",
                    'explanation' => "Your attention {is being scattered by too many new things|is invested too much in outside social exchanges}, causing {focus on close ones|depth in internal conversations} to seriously decrease.",
                    'action' => "{Give absolute attention|Need to listen fully} when beside them, {do not let your mind drift elsewhere|your full presence is what they need most right now}."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Dynamism brings opportunity but also risks from gullibility|Be very careful with financial decisions based on rumors}.",
                    'explanation' => "The atmosphere {full of unverified information|containing many tempting offers} easily makes you {lose caution|skip logical analysis steps}. {Acting hastily following the crowd|Lacking depth in evaluation} can make you pay a high price.",
                    'action' => "{Absolutely avoid rushed decisions|Freeze flash spending plans} until you {verify all data yourself|find absolute transparency}."
                ]
            ]
        ],
        'water' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Subtlety leads the way, helping you handle all tasks gracefully|Empathy brings you the ability to connect perfectly with the collective}.",
                    'explanation' => "{Your energy this week is especially suitable for|You possess an inner ability to} {be the untangler of hidden conflicts|create great spiritual value}. {Without much debate|Your stillness} still has amazing {mediating|connecting} power.",
                    'action' => "{Let yourself flow with understanding|Do not force results rigidly}, everything will find its best direction on its own."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Do not let groundless restlessness drown your productivity|Sensitivity is becoming a fog obscuring rational vision}.",
                    'explanation' => "There are {waves of negative psychology|unnamed restlessness} constantly appearing, making you {lose focus on current goals|easily make emotional decisions}. Your stress tolerance {is at a very low level|is being seriously tested}.",
                    'action' => "{Need to strongly rise above personal feelings|Anchor your mind to practical matters} to {maintain professionalism|avoid being swept away by invisible worries}."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{A week full of emotion and profound depth awaits you|Quiet care will bring relationships to understanding}.",
                    'explanation' => "{Inner harmony|Subtle communication ability} helps you and those around you {clearly see the most honest hidden corners|erase all distance boundaries}. The atmosphere {becomes deeply connected|carries the color of sharing}.",
                    'action' => "{Feel free to express inner thoughts|Open your heart to accept care}, this is the moment you are wrapped in the purest emotions."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Be careful of the trap of seeking comfort through material things|The habit of shopping to fill sadness is waiting to swallow your budget}.",
                    'explanation' => "{Spending|Saving} decisions this week depend heavily on {psychological fluctuations|your emotional self-control ability}. When {emptiness appears|you feel insecure}, you easily {throw money at meaningless things|spend unconsciously}.",
                    'action' => "{If you notice instability signs|When feeling your mood going down}, {absolutely do not rush into buying or selling|lock up your credit cards} to protect yourself."
                ]
            ]
        ]
    ],

    'monthly' => [
        'fire' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{In {month}, your growth pace blossoms brilliantly thanks to dedication|Your personal mettle will illuminate new, breakthrough directions}.",
                    'explanation' => "{Relentless efforts|The thirst for improvement} up to now finally {bring worthy results|create very clear changes}. You are possessing {strong determination|solid confidence} to {accept bigger challenges|break out of old limits}.",
                    'action' => "{This is the ideal moment|Step forward with all inner strength}, do not hesitate to {show core ability|take the lead in work} because circumstances fully support you."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Your confidence is very attractive but ego imposition is a big barrier|Be careful with the desire to always win arguments}.",
                    'explanation' => "{Brilliant energy|Excessive assertiveness} sometimes makes you {forget to listen|inadvertently overpower the voices} of those around you. Even if you {mean well|want to protect}, {lack of subtlety|excessive control} will still create suffocation.",
                    'action' => "{Remember that every relationship needs respect|Moderating rigidity is the most important factor} to maintain a bond both passionate and lasting."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Your cash flow in {month} carries very encouraging growth signs|Taking initiative to seize opportunities will bring significant material increase}.",
                    'explanation' => "{Practical decisions|Decisiveness in optimizing resources} helps you {gain tangible benefits|unblock long-standing bottlenecks}. Your ability to {manage income|seize timing} is working extremely well.",
                    'action' => "{The biggest challenge now is not how to create value|However remember that}, {restraining impulsive generosity|maintaining the spending plan} is what ultimately keeps you stable."
                ]
            ]
        ],
        'earth' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{The entire month of {month} is a beautiful picture for you to reap the fruits of patience|Projects you once silently pursued have now grown very solidly}.",
                    'explanation' => "{Discipline|Consistency} and {arrangement strategy|systematic thinking} have {brought real value|proved clear effectiveness}. The situation is {opening a new step|preparing to entrust you with} {more important positions|bigger tasks}.",
                    'action' => "{Be proud of what you have accomplished|Do not hesitate to step up and receive recognition}, as this is the deserved result of a persistent work journey."
                ]
            ],
            'love' => [
                [
                    'tone' => 'neutral',
                    'intensity' => 3,
                    'headline' => "{The quality of your relationships in {month} enters a very peaceful phase|Trust and core values help you reinforce a solid spiritual foundation}.",
                    'explanation' => "{Stability|A safe atmosphere} covers all interactions, helping you {clearly feel the value of commitment|find true peace of mind}. Even though {big surprises are missing|there are not many fluctuations}, this calmness is exactly what you need.",
                    'action' => "{Steadiness is the foundation, but a little novelty will be the catalyst|Remember to refresh the daily rhythm} so relationships do not fall into dry boredom."
                ]
            ],
            'money' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Your spending plan for {month} shows very sharp long-term vision|Your ability to position material safety is operating at a perfect level}.",
                    'explanation' => "{Strictly following the budget|Wise risk management habits} helps you {feel completely confident|accumulate significant resources}. Your financial wall {is stronger than ever|is exerting maximum protective effect}.",
                    'action' => "{This is a good month to consider|Do not be afraid to start} {setting up new reserve funds|restructuring asset orientation for the future}."
                ]
            ]
        ],
        'air' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{Throughout {month}, your sharpness will maximize adaptability|Environmental fluctuations are opportunities for you to offer breakthrough solutions}.",
                    'explanation' => "{Flexible thinking|A multi-dimensional view} helps you {welcome change proactively|turn every challenge into a problem-solving opportunity}. {Connection networks|Work relationships} will bring {new information streams|very valuable interactions}.",
                    'action' => "{Prioritize expanding communication|Let your thinking open as wide as possible}, because the only limit now is how you organize information."
                ]
            ],
            'love' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{The need to constantly seek novelty can make relationships shallow|Your wide communication ability comes with a lack of deep commitment}.",
                    'explanation' => "{You may take in countless interesting interactions|Openness helps you attract many people}, but {quickly getting bored|easily shifting focus} makes you {very hard to settle on one point|difficult to establish a truly solid bond}.",
                    'action' => "{If you want to resolve invisible distance|The truth is}, only {a readiness to talk directly and dare to open up|courage to face core issues} can untie the knot."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 5,
                    'headline' => "{Your financial pace in {month} fluctuates continuously demanding very logical calculation|Absolutely stay away from decisions chasing trends hoping for quick profits}.",
                    'explanation' => "{There will be many flexible income streams flowing in|The ability to catch trends brings you not little benefits}, but {the level of leakage for personal needs is also extremely high|scattered spending makes money leave very fast}. Lack of consistency is {the biggest risk|the perfect trap}.",
                    'action' => "{Lacking careful evaluation|Just one impulsive moment} will put you {in a difficult budget situation|into an unnecessary deficit situation}."
                ]
            ]
        ],
        'water' => [
            'career' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 5,
                    'headline' => "{month} marks a wonderful time for emotional depth and understanding ability|Your subtlety will create great connective value}.",
                    'explanation' => "{Inner observation ability|Contextual understanding} helps you {not have to brace yourself against changes|flexibly handle issues with full intelligence}. With calmness, you {welcome everything|turn gentleness into strength} to keep standing firm.",
                    'action' => "{Do not try to decode every phenomenon with dry logic|Trust your overall assessment}, because sometimes a gentle approach brings the highest effectiveness."
                ]
            ],
            'love' => [
                [
                    'tone' => 'optimistic',
                    'intensity' => 4,
                    'headline' => "{The thirty days of {month} are an experience of deeply profound connections|You are entering a bonding phase where sincerity is placed highest}.",
                    'explanation' => "{Utter understanding|The tight bond} between you and those around you {becomes very sharp|brings an unusually peaceful feeling}. {Old misunderstandings|Past distances} seem to {be completely soothed|dissolve into tolerance}.",
                    'action' => "{Allow yourself to enjoy this tenderness|This is the moment you have the right to relax}, the situation is creating the safest space for your emotions."
                ],
                [
                    'tone' => 'caution',
                    'intensity' => 3,
                    'headline' => "{Need to clearly recognize personal boundaries before empathy turns into self-pity|Do not sacrifice autonomy just because of a craving to maintain harmony}.",
                    'explanation' => "{Fear of making others sad|The desire to please those around you} sometimes makes you {blur self-protection principles|easily compromise on unworthy things}. Care {will become tiring|will drain you} if it lacks clarity.",
                    'action' => "{Saying no is also a way to protect yourself|Remember that}, {you can only help others|a bond is only sustainable} when you respect your own limits."
                ]
            ],
            'money' => [
                [
                    'tone' => 'caution',
                    'intensity' => 4,
                    'headline' => "{Misplaced kindness can be the weakness causing financial difficulty|Your asset protection ability is being obscured by misplaced obligingness}.",
                    'explanation' => "{Although your financial management skills are originally very good|You fully can see risks}, but when {impacted by requests|someone begs for help}, you {easily drop principles|accept material losses}.",
                    'action' => "{Need to firmly refuse carrying financial responsibility for others|Learn to hold principles against unclear material demands} to protect personal budget safety."
                ]
            ]
        ]
    ]
];