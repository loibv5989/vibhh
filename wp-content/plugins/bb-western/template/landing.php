<?php
if (!defined('ABSPATH')) exit;
?>
<div class="trt-wrap" id="trt-wrap">
    <div id="trt-app-config" data-spread="3_cards" style="display:none;"></div>
    <script>
        window.WESTERN_SPREADS = <?= json_encode($spreads_config, JSON_UNESCAPED_UNICODE) ?>;
    </script>

    <div class="trt-step active" id="trt-step-hub">
        <div class="trt-hero">
            <div class="trt-hero-badge">✦ 52 Playing Cards · Standard Deck</div>
            <h1 class="trt-hero-title"><span>Playing Card Reading</span> Online<br> Free</h1>
            <p class="trt-hero-sub">Focus on your question, pick a topic, then draw your cards</p>
        </div>
        <div class="trt-topic-section">
            <p class="trt-label">What area would you like insight on?</p>
            <p class="trt-topic-hint">✦ Close your eyes, think about what's on your mind, then pick a topic</p>
            <div class="trt-topic-grid">
                <button class="trt-topic-card" data-topic="love">
                    <div class="trt-topic-card-back">
                        <div class="trt-topic-card-inner">
                            <span class="trt-topic-icon">❤️</span>
                            <span class="trt-topic-label">Love</span>
                        </div>
                    </div>
                </button>
                <button class="trt-topic-card" data-topic="career">
                    <div class="trt-topic-card-back">
                        <div class="trt-topic-card-inner">
                            <span class="trt-topic-icon">💼</span>
                            <span class="trt-topic-label">Career</span>
                        </div>
                    </div>
                </button>
                <button class="trt-topic-card" data-topic="finance">
                    <div class="trt-topic-card-back">
                        <div class="trt-topic-card-inner">
                            <span class="trt-topic-icon">💰</span>
                            <span class="trt-topic-label">Finance</span>
                        </div>
                    </div>
                </button>
                <button class="trt-topic-card" data-topic="study">
                    <div class="trt-topic-card-back">
                        <div class="trt-topic-card-inner">
                            <span class="trt-topic-icon">📚</span>
                            <span class="trt-topic-label">Studies</span>
                        </div>
                    </div>
                </button>
                <button class="trt-topic-card" data-topic="health">
                    <div class="trt-topic-card-back">
                        <div class="trt-topic-card-inner">
                            <span class="trt-topic-icon">🌿</span>
                            <span class="trt-topic-label">Health</span>
                        </div>
                    </div>
                </button>
                <button class="trt-topic-card" data-topic="future">
                    <div class="trt-topic-card-back">
                        <div class="trt-topic-card-inner">
                            <span class="trt-topic-icon">🔮</span>
                            <span class="trt-topic-label">Future</span>
                        </div>
                    </div>
                </button>
            </div>
            <span class="trt-error" id="trt-err-hub-topic"></span>
        </div>
        <div class="trt-ls">
            <h2 class="trt-ls-h">What Is Playing Card Reading</h2>
            <div class="trt-ls-prose">
                <p>Playing card reading uses a standard <strong>52-card</strong> deck — the familiar four suits of Hearts, Diamonds, Clubs, and Spades — to interpret situations, relationships, and questions about everyday life.</p>
                <p>Tarot draws from 15th-century European occultism and uses 78 specially designed cards. Playing card reading developed separately through Vietnamese folk tradition, where each suit and card value carries a specific meaning. These meanings passed down through generations. While no single official standard exists, the core rules are widely shared and recognized.</p>
                <p>The main difference from other methods is how <strong>direct</strong> playing card readings tend to be. Each card gives a concrete message, with less symbolism than Tarot. It suits people who want a straight answer rather than an open-ended interpretation.</p>
            </div>
        </div>
        <div class="trt-ls">
            <h2 class="trt-ls-h">What Each Suit Covers</h2>
            <p class="trt-ls-lead">Each of the four suits governs a different area of life. When several cards from the same suit appear in one spread, that area is likely the dominant theme.</p>
            <div class="trt-suit-grid">
                <div class="trt-suit-card">
                    <div class="trt-suit-wrap">
                        <div class="trt-suit-sym">♥</div>
                        <div class="trt-suit-name">Hearts</div>
                    </div>
                    <div class="trt-suit-domain">Emotions · Family · Good News</div>
                    <div class="trt-suit-desc">Hearts covers emotional life and close relationships: love, marriage, family, and personal milestones. Many Hearts cards in a spread point to emotions as the driving force behind the situation.</div>
                </div>
                <div class="trt-suit-card">
                    <div class="trt-suit-wrap">
                        <div class="trt-suit-sym">♦</div>
                        <div class="trt-suit-name">Diamonds</div>
                    </div>
                    <div class="trt-suit-domain">Money · Messages · Travel</div>
                    <div class="trt-suit-desc">Diamonds relates to money, finances, paperwork, and movement. It often appears when the question involves income, transactions, contracts, or trips away from home.</div>
                </div>
                <div class="trt-suit-card">
                    <div class="trt-suit-wrap">
                        <div class="trt-suit-sym">♣</div>
                        <div class="trt-suit-name">Clubs</div>
                    </div>
                    <div class="trt-suit-domain">Career · Ambition · Effort</div>
                    <div class="trt-suit-desc">Clubs governs work, reputation, and results built through personal effort. Several Clubs cards often signal a busy period, a career opportunity, or a decision point in someone's professional life.</div>
                </div>
                <div class="trt-suit-card">
                    <div class="trt-suit-wrap">
                        <div class="trt-suit-sym">♠</div>
                        <div class="trt-suit-name">Spades</div>
                    </div>
                    <div class="trt-suit-domain">Obstacles · Conflict · Warnings</div>
                    <div class="trt-suit-desc">Spades carries the heaviest energy: setbacks, rivals, illness, and challenges that are hard to avoid. It is not an absolute sign of bad luck. Spades points to risks that are present and worth paying attention to.</div>
                </div>
            </div>
        </div>
        <div class="trt-ls">
            <h2 class="trt-ls-h">The Four Spreads</h2>
            <div class="trt-spread-rows">
                <div class="trt-sr-item">
                    <div class="trt-sr-left">
                        <div class="trt-sr-num">3</div>
                        <div class="trt-sr-lbl">cards</div>
                    </div>
                    <div class="trt-sr-body">
                        <div class="trt-sr-title">Past · Present · Future</div>
                        <div class="trt-sr-desc">The most common spread. Three cards show where a situation started, where it stands now, and where it's likely heading. Works well for most questions, especially when you want a clear answer quickly.</div>
                    </div>
                </div>
                <div class="trt-sr-item">
                    <div class="trt-sr-left">
                        <div class="trt-sr-num">5</div>
                        <div class="trt-sr-lbl">cards</div>
                    </div>
                    <div class="trt-sr-body">
                        <div class="trt-sr-title">Situation · Challenge · Advice · Outside Influence · Outcome</div>
                        <div class="trt-sr-desc">More layered than the 3-card spread. The five positions also show what outside forces are at play (other people, circumstances) and what action makes sense given everything in the spread.</div>
                    </div>
                </div>
                <div class="trt-sr-item">
                    <div class="trt-sr-left">
                        <div class="trt-sr-num">7</div>
                        <div class="trt-sr-lbl">cards</div>
                    </div>
                    <div class="trt-sr-body">
                        <div class="trt-sr-title">Horseshoe Spread</div>
                        <div class="trt-sr-desc">The most detailed spread in the deck. Seven positions cover past, present, hidden factors, obstacles, surroundings, advice, and outcome. Best used for complex situations where you need the full picture before making a significant decision.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="trt-ls">
            <h3 class="trt-ls-h">Common Questions</h3>
            <div class="trt-faq">
                <details class="trt-faq-item">
                    <summary class="trt-faq-q">How is this different from Tarot?</summary>
                    <div class="trt-faq-a">Tarot uses a 78-card deck with its own symbol system (Major Arcana, Minor Arcana) rooted in Western occultism. Playing card reading uses a standard 52-card deck with a meaning system built through Vietnamese folk tradition. The language and tone of playing card readings tend to be more direct and specific than Tarot.</div>
                </details>
                <details class="trt-faq-item">
                    <summary class="trt-faq-q">Do Spades always mean something bad?</summary>
                    <div class="trt-faq-a">No. Spades signal risk, obstacles, and the need for caution, but the meaning of any card depends on its position in the spread and the surrounding cards. A Spades card in the "advice" position reads very differently from the same card in the "outcome" position. The cards point to what's at play, not what's fixed.</div>
                </details>
                <details class="trt-faq-item">
                    <summary class="trt-faq-q">How should I phrase my question?</summary>
                    <div class="trt-faq-a">Specific questions about your actual situation tend to give clearer results than vague ones. Instead of "Will I be lucky?", try "Should I take the job offer I'm considering?" or "Where does this relationship stand right now?" Open questions give broader readings; specific ones give tighter answers.</div>
                </details>
                <details class="trt-faq-item">
                    <summary class="trt-faq-q">How often can I ask the same question?</summary>
                    <div class="trt-faq-a">Repeating the same question multiple times in one day rarely adds anything useful. The cards reflect the energy at the moment you draw them, and that takes time to shift. If a specific event has happened or you've taken new action, that's a reasonable time to ask again.</div>
                </details>
                <details class="trt-faq-item">
                    <summary class="trt-faq-q">Can I trust the AI interpretations?</summary>
                    <div class="trt-faq-a">The AI reads based on each card's established meaning, its position in the spread, and the overall pattern of suits. It's not random. The results are a reference point, a way to notice angles you might have missed. What you do with that is your call.</div>
                </details>
            </div>
        </div>
    </div>

    <div class="trt-step" id="trt-step-spread">
        <div class="trt-step-header">
            <button class="trt-back-btn" data-back="hub">← Back</button>
            <span class="trt-step-label" id="trt-spread-label">Choose Your Spread</span>
        </div>

        <div class="trt-unified-top">
            <p class="trt-unified-hint">✦ Focus on your question, then choose your spread and shuffle</p>

            <div class="trt-spread-selector">
                <button class="trt-spread-opt active" data-spread="3_cards" data-count="3">3 Cards</button>
                <button class="trt-spread-opt" data-spread="5_cards" data-count="5">5 Cards</button>
                <button class="trt-spread-opt" data-spread="7_cards" data-count="7">7 Cards</button>
            </div>
            <span class="trt-error" id="trt-err-spread"></span>

            <div class="trt-stack-wrap" id="trt-stack-wrap">
                <div class="trt-stack-card"><div class="trt-card-back-pattern"></div></div>
                <div class="trt-stack-card"><div class="trt-card-back-pattern"></div></div>
                <div class="trt-stack-card"><div class="trt-card-back-pattern"></div></div>
            </div>
        </div>

        <div class="trt-user-question" aria-hidden="true">
            <label for="trt-user-label">Enter your answer?</label>
            <input type="text" id="trt-user-question-trap" name="trt-user-question" tabindex="-1" autocomplete="off">
        </div>

        <button class="trt-shuffle-btn" id="trt-btn-unified-shuffle">
            <span class="trt-btn-text">✦ Shuffle Cards ✦</span>
            <span class="trt-btn-loading"><span class="trt-spinner"></span> Shuffling...</span>
        </button>
    </div>

    <div class="trt-step" id="trt-step-deck">
        <p class="trt-deck-instruction" id="trt-deck-instruction">✦ Focus on your question and choose your cards</p>
        <div class="trt-deck-wrap" id="trt-deck-wrap"></div>
        <div class="trt-selected-slots" id="trt-dynamic-slots"></div>
        <div class="trt-deck-counter">Selected: <span id="trt-selected-count">0</span>/<span id="trt-target-count">0</span></div>
    </div>

    <div id="trt-result-box" style="display:none"></div>

    <div id="trt-card-modal" class="trt-card-modal" style="display:none">
        <div class="trt-card-modal-backdrop"></div>
        <div class="trt-card-modal-body" id="trt-modal-body">
            <div class="trt-card-modal-overlay"></div>
            <button class="trt-card-modal-close" aria-label="Close">&times;</button>
            <div class="trt-card-modal-content" id="trt-modal-content"></div>
        </div>
    </div>
</div>