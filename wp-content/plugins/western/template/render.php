<?php

if (!defined('ABSPATH')) exit;

function western_render(string $topic, array $cards, string $spread_key = '3_cards'): string {
    $spreads        = Western_Calc::getSpreads();
    $spread_config  = $spreads[$spread_key] ?? $spreads['3_cards'];
    $positions      = $spread_config['positions'];

    $suit_labels  = ['hearts' => 'Hearts ♥', 'diamonds' => 'Diamonds ♦', 'clubs' => 'Clubs ♣', 'spades' => 'Spades ♠'];
    $suit_colors  = ['hearts' => '#dc2626', 'diamonds' => '#dc2626', 'clubs' => '#1f2937', 'spades' => '#1f2937'];
    $suit_symbols = ['hearts' => '♥', 'diamonds' => '♦', 'clubs' => '♣', 'spades' => '♠'];

    ob_start(); ?>

    <div id="trt-detail-container" style="display:none">
        <p class="trt-spread-hint">Tap a card to see its details</p>
        <div class="trt-card-spread" style="--card-count:<?= count($cards) ?>">
            <?php
            $rotations = [-4, 0, 4, -2, 2, -3, 3];
            $idx = 0;
            foreach ($positions as $pos_key => $pos_label):
                if (!isset($cards[$pos_key])) continue;
                $c      = $cards[$pos_key];
                $suit   = $c['suit'] ?? '';
                $pcolor = $suit_colors[$suit] ?? '#888';
                $kw     = implode(', ', $c['keywords'] ?? []);
                $rot    = $rotations[$idx % count($rotations)];
                $idx++;
                ?>
                <?php $sym = $suit_symbols[$suit] ?? ''; $rank = $c['rank'] ?? ''; ?>
                <div class="trt-card-detail" data-card-name="<?= esc_attr($c['name']) ?>">
                    <div class="trt-cd-visual trt-cd-<?= esc_attr($suit) ?> trt-cd-rank-<?= esc_attr($rank) ?>" role="button" tabindex="0" data-suit="<?= esc_attr($suit) ?>" style="--card-color:<?= esc_attr($pcolor) ?>;--card-rot:<?= $rot ?>deg">
                        <div class="trt-cv-corner trt-cv-corner-top">
                            <span class="trt-cv-rank"><?= esc_html($rank) ?></span>
                            <span class="trt-cv-suit-top"><?= esc_html($sym) ?></span>
                        </div>
                        <span class="trt-cv-suit-big"><?= esc_html($sym) ?></span>
                        <div class="trt-cv-corner trt-cv-corner-bot">
                            <span class="trt-cv-suit-bot"><?= esc_html($sym) ?></span>
                        </div>
                    </div>
                    <span class="trt-cd-pos-label"><?= esc_html($pos_label) ?></span>
                    <div class="trt-cd-content">
                        <div class="trt-cd-header">
                            <span class="trt-cd-pos" style="color:<?= esc_attr($pcolor) ?>"><?= esc_html($pos_label) ?></span>
                            <span class="trt-cd-name"><?= esc_html($c['name']) ?></span>
                            <?php if (!empty($suit)): ?>
                                <span class="trt-badge-minor" style="color:<?= esc_attr($suit_colors[$suit] ?? 'inherit') ?>"><?= esc_html($suit_labels[$suit] ?? $suit) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="trt-cd-body">
                            <p><?= esc_html($c['meaning'] ?? '') ?></p>
                            <?php if (!empty($kw)): ?><p style="font-size:.8rem">✦ Message: <em><?= esc_html($kw) ?></em></p><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
        </div>


        <?php
        $allow_ai = get_option('western_allow_ai', '0');
        if ($allow_ai === '1'):  ?>
            <div id="trt-deep-analyze-form">
                <div class="trt-input-section">
                    <div class="trt-input-trap" aria-hidden="true">
                        <input type="text" id="trt-deep-trap" name="trt-deep-trap" tabindex="-1" autocomplete="off">
                    </div>
                    <label class="trt-label" for="trt-deep-question">Enter your question for the card interpretation?</label>
                    <textarea id="trt-deep-question" class="trt-input trt-textarea" placeholder="Ex: Where is this relationship heading?..." maxlength="300" rows="3"></textarea>
                    <div class="trt-char-count"><span id="trt-deep-q-count">0</span>/300</div>
                    <span class="trt-error" id="trt-err-deep-question"></span>

                    <?php
                    $topic_chips = [
                        'love' => [
                                'Will this relationship work out?',
                                'Is there someone new I should pay attention to?',
                                'Am I really with the right person?',
                                'How does my partner feel about me right now?',
                                'How can I improve things in my relationship?',
                                'Should I tell this person how I feel?'
                        ],
                        'career' => [
                                'Will I get promoted soon?',
                                'Is it time for me to change jobs?',
                                'Does my boss actually support my growth?',
                                'Will this project succeed?',
                                'Is starting my own business a good idea right now?',
                                'How can I get noticed at work?'
                        ],
                        'finance' => [
                                'Will this investment actually pay off?',
                                'Should I save money or spend it now?',
                                'Is there a real financial opportunity ahead?',
                                'Can I recover from this loss?',
                                'Should I take this loan or wait?',
                                'How can I improve my finances?'
                        ],
                        'study' => [
                                'Will I pass this exam?',
                                'Is this the right major for me?',
                                'Am I studying in the right way?',
                                'Will I get into the school I want?',
                                'Should I keep pushing or take a break?',
                                'What is holding back my progress in school?'
                        ],
                        'health' => [
                                'Will my health get better soon?',
                                'Should I see a doctor about this?',
                                'Is my current routine helping me?',
                                'Why have I been feeling so tired lately?',
                                'Should I change my diet or exercise routine?',
                                'Will I recover quickly?'
                        ],
                        'future' => [
                                'What opportunity is coming my way?',
                                'Should I take this risk or play it safe?',
                                'Is a big change coming soon?',
                                'What should I prepare for next?',
                                'Will things start going better for me?',
                                'What path should I focus on now?'
                        ],
                    ];

                    $topic_labels = [
                            'love' => ['Relationship', 'Someone new', 'Right person', "Partner's feelings", 'Improve things', 'Say how I feel'],
                            'career' => ['Promotion', 'Change jobs', 'Boss support', 'Project success', 'Start business', 'Get noticed'],
                            'finance' => ['Investment', 'Save or spend', 'Opportunity', 'Recover loss', 'Loan or wait', 'Improve finances'],
                            'study' => ['Pass exam', 'Choose major', 'Study method', 'Get into school', 'Keep pushing', 'Progress block'],
                            'health' => ['Get better', 'See a doctor', 'Routine help', 'Feeling tired', 'Diet or exercise', 'Recover quickly'],
                            'future' => ['Opportunity', 'Risk or safe', 'Big change', 'Prepare next', 'Things improve', 'Focus now'],
                    ];
                    $current_chips  = $topic_chips[$topic] ?? $topic_chips['future'];
                    $current_labels = $topic_labels[$topic] ?? $topic_labels['future'];
                    ?>
                    <div class="trt-chips">
                        <?php foreach ($current_chips as $idx => $q): ?>
                            <button type="button" class="trt-chip" data-q="<?= esc_attr($q) ?>"><?= esc_html($current_labels[$idx]) ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="trt-submit-btn" id="trt-btn-deep-analyze">
                    <span class="trt-btn-text">Read My Cards</span>
                    <span class="trt-btn-loading"><span class="trt-spinner"></span> Reading...</span>
                </button>
                <span class="trt-error trt-error-analyze" id="trt-err-analyze"></span>
            </div>
        <?php endif; ?>

        <div id="ast-analysis-wrap" style="display:none;">
            <div id="ast-final-result" style="margin-top:20px;">
                <div class="ast-skeleton ast-sk-title"></div>
                <div class="ast-skeleton ast-sk-line"></div>
                <div class="ast-skeleton ast-sk-line ast-sk-short"></div>
                <div class="ast-skeleton ast-sk-line"></div>
            </div>
        </div>
    </div>
    <div class="ast-action-footer" style="display:none;">
        <span id="ast-btn-comment" class="ast-btn-comment">Discuss</span>
        <span class="ast-reload" onclick="window.location.reload()">↺ New Spread</span>
    </div>

    <p class="trt-disclaimer" id="trt-disclaimer" style="display:none;">
        ✦ This reading is for reference only, based on the Western Oracle system. What you do next is entirely your own choice and effort.
    </p>

    <?php return ob_get_clean();
}