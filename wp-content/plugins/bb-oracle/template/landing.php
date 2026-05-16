<?php
/**
 * Template: Landing Page – Oracle Cards Online
 * @package BbOracle
 */

$oracle_topics = [
        [
                'symbol'  => '🌱',
                'name'    => 'New Beginnings',
                'group'   => 'Cycles',
                'color'   => '#4ade80',
                'mo_ta'   => 'When one chapter closes and the next hasn\'t taken shape yet. The message often asks you to move forward without needing to see the full path first.',
                'cau_hoi' => 'What do I need to let go of to make room for something new?',
        ],
        [
                'symbol'  => '🔥',
                'name'    => 'Action and Commitment',
                'group'   => 'Will',
                'color'   => '#f97316',
                'mo_ta'   => 'When you already know what needs doing but keep putting it off. The message tends to push you past the comfortable edge and remind you the time is now.',
                'cau_hoi' => 'What is actually stopping me from starting?',
        ],
        [
                'symbol'  => '🌊',
                'name'    => 'Letting Go',
                'group'   => 'Transformation',
                'color'   => '#38bdf8',
                'mo_ta'   => 'When you\'re trying to control something that\'s out of reach. The message often draws a line between what can be changed and what simply needs to be accepted.',
                'cau_hoi' => 'What am I still holding onto that I already know needs to go?',
        ],
        [
                'symbol'  => '💗',
                'name'    => 'Love and Connection',
                'group'   => 'Relationships',
                'color'   => '#f472b6',
                'mo_ta'   => 'When a relationship is at a turning point. The message often circles around honest communication, healthy boundaries, and the kind of love that starts with yourself.',
                'cau_hoi' => 'In this relationship, am I giving or receiving in a way that feels balanced?',
        ],
        [
                'symbol'  => '⚖',
                'name'    => 'Choices and Decisions',
                'group'   => 'Direction',
                'color'   => '#a78bfa',
                'mo_ta'   => 'When you\'re weighing several paths and can\'t find solid ground. The message rarely points to a specific answer — it tends to surface the core value that\'s been getting skipped in your reasoning.',
                'cau_hoi' => 'Which choice most honestly reflects who I want to be?',
        ],
        [
                'symbol'  => '🛡',
                'name'    => 'Boundaries and Self-Protection',
                'group'   => 'Relationships',
                'color'   => '#fb923c',
                'mo_ta'   => 'When you feel drained or overstepped by others. The message tends to name which boundary has been crossed and why saying no is an act of care, not selfishness.',
                'cau_hoi' => 'What limit do I keep meaning to set but haven\'t?',
        ],
        [
                'symbol'  => '✦',
                'name'    => 'Inner Healing',
                'group'   => 'Transformation',
                'color'   => '#e879f9',
                'mo_ta'   => 'When an old wound resurfaces in a new situation. The message often reframes difficult emotions as information rather than obstacles — and asks what they actually need.',
                'cau_hoi' => 'Which part of me is asking to be seen and taken care of right now?',
        ],
        [
                'symbol'  => '💼',
                'name'    => 'Career and Purpose',
                'group'   => 'Direction',
                'color'   => '#fbbf24',
                'mo_ta'   => 'When work has lost meaning or you\'re facing a professional crossroads. The message doesn\'t judge your choices — it asks what\'s really driving you beneath the surface reasons.',
                'cau_hoi' => 'What is the real reason I\'m doing this work?',
        ],
        [
                'symbol'  => '💰',
                'name'    => 'Money and Abundance',
                'group'   => 'Material',
                'color'   => '#34d399',
                'mo_ta'   => 'When money is causing anxiety or a sense of scarcity. The message rarely talks about numbers — it looks at your psychological relationship with abundance, fear of loss, and belief in your own capacity.',
                'cau_hoi' => 'Am I operating from a place of scarcity or a sense of enough?',
        ],
        [
                'symbol'  => '🌿',
                'name'    => 'Health and the Body',
                'group'   => 'Material',
                'color'   => '#86efac',
                'mo_ta'   => 'When your body is sending signals that haven\'t been heard yet. The message often connects physical symptoms to what\'s being suppressed or ignored on a psychological level.',
                'cau_hoi' => 'What is my body trying to tell me?',
        ],
        [
                'symbol'  => '◉',
                'name'    => 'Intuition and Inner Truth',
                'group'   => 'Will',
                'color'   => '#818cf8',
                'mo_ta'   => 'When your mind and your gut are pointing in different directions. The message doesn\'t take sides — it reminds you that both are sources of information, not opponents.',
                'cau_hoi' => 'What do I already know but haven\'t been willing to admit?',
        ],
        [
                'symbol'  => '🌌',
                'name'    => 'Timing and Patience',
                'group'   => 'Cycles',
                'color'   => '#94a3b8',
                'mo_ta'   => 'When things are moving slower than expected. The message often draws a distinction between delay from fear and delay because the timing isn\'t right — and how to use the waiting period with intention.',
                'cau_hoi' => 'Am I genuinely waiting, or am I avoiding?',
        ],
];

$oracle_faqs = [
        ['q' => 'What system is this 44-card Oracle deck based on?',
                'a' => 'The deck is built around universal archetypes — recurring patterns found across depth psychology, Eastern philosophy, and symbolic traditions. The 44 cards map to the inner states and external circumstances that come up most often throughout a human life cycle.'],
        ['q' => 'How often should I draw cards?',
                'a' => 'There\'s no fixed rule. That said, drawing multiple times a day on the same question usually reflects anxiety more than a genuine need for guidance. Most people find that once a day — or when a specific situation calls for reflection — keeps each draw meaningful.'],
        ['q' => 'What if the message seems unrelated to my question?',
                'a' => 'That\'s often the most useful signal. A message that feels off-topic usually points to an angle you haven\'t been ready to look at, or a deeper question underneath the surface one. Instead of dismissing it, try asking: if this card were right, what would that mean for me right now?'],
        ['q' => 'Is online Oracle different from a physical deck?',
                'a' => 'Functionally, both depend on the intention you bring and how you interpret the reading afterward. An online deck offers algorithmic randomness and is available anytime. A physical deck creates a sense of ritual and presence that some people find helps them focus. Neither is objectively better.'],
        ['q' => 'Can I draw again if I\'m not happy with the result?',
                'a' => 'Technically yes, but it usually backfires. Drawing again out of dissatisfaction means you\'re asking with expectation rather than openness. The uncomfortable card is typically the one worth sitting with the longest — not the one to replace.'],
        ['q' => 'Is my information stored anywhere?',
                'a' => 'Anything you enter — your question, your chosen topic — is only used within the current session to generate your reading. The system does not store personal content after the session ends.'],
];
?>
<div class="trt-wrap" id="trt-wrap">
    <div class="trt-step active" id="trt-step-landing">
        <div class="trt-hero">
            <div class="trt-hero-badge">✦ 44-Card Oracle Deck · Messages from Within</div>
            <h1 class="trt-hero-title"><span>Oracle Cards</span> Free Online Reading</h1>
            <p class="trt-hero-sub">Choose how many cards to draw based on what you're looking for</p>
            <div class="hero-actions">
                <button class="btn-primary" onclick="document.getElementById('trt-xem-boi-tarot').scrollIntoView({behavior:'smooth'})">Start drawing</button>
                <button class="btn-ghost" onclick="document.getElementById('trt-thong-diep').scrollIntoView({behavior:'smooth'})">About Oracle</button>
            </div>
        </div>

        <div class="trt-section-spacing">
            <h2 class="trt-label trt-section-title trt-section-subtitle">What Are Oracle Cards</h2>
            <div class="trt-oracle-about-wrap">
                <p><strong>Oracle Cards</strong> are a tool for reflection, not prediction. They surface what you already sense but haven't fully named — about a situation, a relationship, or a decision you're circling.</p>
                <ul>
                    <li><strong>No system to memorize:</strong> Unlike Tarot's fixed 78-card structure, Oracle decks have no required rules. Each card carries a self-contained message you can read directly without prior knowledge.</li>
                    <li><strong>Reflection over fortune-telling:</strong> Oracle readings work best when you bring a real question. The cards don't predict — they reframe. The value is in the angle they offer, not a yes or no answer.</li>
                    <li><strong>Works for anyone:</strong> Draw one card in the morning to orient your day, or lay out three when you're stuck on something specific. There's no wrong way to use it.</li>
                </ul>
            </div>
        </div>

        <div class="trt-section-spacing">
            <h2 class="trt-label trt-section-title trt-section-subtitle" id="trt-thong-diep">12 Message Themes</h2>
            <p class="trt-section-desc">The deck spans 12 themes that cover the situations people return to most — relationships, direction, inner states, and the in-between moments. Each theme includes an opening question to help you focus before you draw.</p>

            <div class="trt-topic-grid-layout">
                <?php foreach ($oracle_topics as $topic): ?>
                    <div class="trt-card-detail trt-topic-card-wrapper" style="--topic-color: <?= $topic['color'] ?>;">
                        <div class="trt-topic-border-left"></div>
                        <div class="trt-topic-inner-pad">
                            <div class="trt-topic-header-flex">
                                <span class="trt-topic-symbol"><?= $topic['symbol'] ?></span>
                                <div>
                                    <div class="trt-topic-name"><?= esc_html($topic['name']) ?></div>
                                    <div class="trt-topic-group"><?= esc_html($topic['group']) ?></div>
                                </div>
                            </div>
                            <p class="trt-topic-mo-ta"><?= esc_html($topic['mo_ta']) ?></p>
                            <div class="trt-topic-cau-hoi">
                                ✦ <?= esc_html($topic['cau_hoi']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="trt-section-spacing" id="trt-xem-boi-tarot">
            <h2 class="trt-label trt-faq-title trt-section-subtitle">Draw Your Cards</h2>
            <div class="trt-mode-grid">
                <a href="/oracle-cards-online/oracle-1-card/" class="trt-mode-card">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">🌟</div>
                        <div class="trt-mode-title">Daily Card — 1 Card</div>
                    </div>
                    <div class="trt-mode-desc">One card, one message for the day ahead.</div>
                    <div class="trt-mode-arrow">Start →</div>
                </a>
                <a href="/oracle-cards-online/oracle-2-cards/" class="trt-mode-card">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">✨</div>
                        <div class="trt-mode-title">Oracle 2 Cards</div>
                    </div>
                    <div class="trt-mode-desc">Situation and guidance — two energies in dialogue.</div>
                    <div class="trt-mode-arrow">Start →</div>
                </a>
                <a href="/oracle-cards-online/oracle-3-cards/" class="trt-mode-card">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">🔮</div>
                        <div class="trt-mode-title">Oracle 3 Cards</div>
                    </div>
                    <div class="trt-mode-desc">Mind · Heart · Spirit.</div>
                    <div class="trt-mode-arrow">Start →</div>
                </a>
                <a href="/oracle-cards-online/oracle-question/" class="trt-mode-card">
                    <div class="trt-spread-header">
                        <div class="trt-mode-icon">✍️</div>
                        <div class="trt-mode-title">Ask Oracle</div>
                    </div>
                    <div class="trt-mode-desc">Type a specific question and draw cards around it.</div>
                    <div class="trt-mode-arrow">Start →</div>
                </a>
            </div>
        </div>

        <div class="trt-section-spacing">
            <h3 class="trt-label trt-faq-title trt-section-subtitle">Common Questions</h3>
            <div id="trt-faq-list">
                <?php foreach ($oracle_faqs as $idx => $faq): ?>
                    <div class="trt-faq-item-oracle">
                        <button class="trt-faq-btn-oracle trt-faq-toggle">
                            <span class="trt-faq-q-text"><?= esc_html($faq['q']) ?></span>
                            <span class="trt-faq-ico-oracle">+</span>
                        </button>
                        <div class="trt-faq-ans-oracle">
                            <?= esc_html($faq['a']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>