jQuery(function ($) {

    const Steps = {
        show(id) {
            $('.trt-step').removeClass('active');
            $('#trt-step-' + id).addClass('active');
            window.scrollTo({top: 0, behavior: 'smooth'});
        }
    };

    const $config = $('#trt-app-config');

    const State = {
        spread: $config.data('spread') || '3_cards',
        topic: '', question: '', cardsLite: null, shuffledDeck: null, pickedCards: []
    };

    const Auth = {
        isLoggedIn: () => $('.lbv-logout').length > 0,
        require() {
            if (!this.isLoggedIn()) {
                $('.user-dropdown .user-dropdown-item:first').trigger('click');
                return false;
            }
            return true;
        },
    };

    const Validator = {
        question(val, errorId) {
            const $e = $('#' + errorId);
            $e.text('');
            if (!val || val.trim().length < 5) {
                $e.text('Please enter a question.');
                return false;
            }
            return true;
        }
    };

    const AIResult = {
        tryInject(result) {
            if (!result) return;

            const $c = $('#ast-final-result');
            $c.empty().addClass('ast-content-loaded');

            $c.html(result.html);
            $('#trt-disclaimer').fadeIn(400);
        }
    };

    const Ajax = {
        drawPromise: null,
        draw() {
            this.drawPromise = new Promise((resolve, reject) => {
                $.ajax({
                    url: WesternAjax.api_url + 'draw',
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-WP-Nonce': WesternAjax.nonce
                    },
                    data: JSON.stringify({
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread
                    }),
                    success: res => {
                        if (res.success) {
                            State.cardsLite = res.cards;
                            State.shuffledDeck = res.shuffled_deck;
                            resolve(res);
                        } else {
                            reject(res.message || 'An error occurred.');
                        }
                    },
                    error: () => reject('Connection error. Please try again.')
                });
            });
            return this.drawPromise;
        },
        analyze() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: WesternAjax.api_url + 'analyze',
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-WP-Nonce': WesternAjax.nonce
                    },
                    data: JSON.stringify({
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread,
                        cards: State.cardsLite,
                        hp_trap: $('#trt-deep-trap').val()
                    }),
                    success: res => res.success ? resolve(res) : reject(res.message || 'An error occurred.'),
                    error: () => reject('Connection error. Please try again.')
                });
            });
        },
        reveal() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: WesternAjax.api_url + 'reveal',
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-WP-Nonce': WesternAjax.nonce
                    },
                    data: JSON.stringify({
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread,
                        picked: State.pickedCards
                    }),
                    success: res => res.success ? resolve(res) : reject(res.message || 'An error occurred.'),
                    error: () => reject('Connection error. Please try again.')
                });
            });
        }
    };

    const Deck = {
        selectedCount: 0,
        targetCount: 3,
        resolvePick: null,
        positions: [],
        init() {
            this.selectedCount = 0;
            State.cardsLite = null;
            State.shuffledDeck = null;
            State.pickedCards = [];

            const spreadData = window.WESTERN_SPREADS[State.spread];
            this.positions = Object.keys(spreadData.positions);
            this.targetCount = spreadData.count;

            const $wrap = $('#trt-deck-wrap').empty();
            const html = Array.from({length: 52}, (_, i) =>
                `<div class="trt-deck-card" data-index="${i}"><div class="trt-card-back-face"></div></div>`
            ).join('');
            $wrap.html(html);

            const $slotsWrap = $('#trt-dynamic-slots').empty();
            $slotsWrap.removeClass().addClass('trt-selected-slots trt-layout-' + State.spread);

            Object.values(spreadData.positions).forEach((label, idx) => {
                $slotsWrap.append(`<div class="trt-slot" data-slot="${idx}"><span class="trt-slot-pos">${label}</span></div>`);
            });

            $('#trt-deck-instruction').show().html(`✦ Focus on your question and pick <strong>${this.targetCount} cards</strong>`).css('opacity', 1);
            $('#trt-selected-count').text('0');
            $('#trt-target-count').text(this.targetCount);

            return new Promise(resolve => {
                this.resolvePick = resolve;
                $(document).off('click.deck').on('click.deck', '.trt-deck-card:not(.selected-card)', (e) => this.pick($(e.currentTarget)));
                setTimeout(() => this.spread(), 100);
            });
        },
        spread() {
            const w = window.innerWidth;
            const isMob    = w < 768;
            const isTablet = w >= 768 && w < 1024;
            const cardW = isMob ? 38 : isTablet ? 46 : 56;
            const gapY  = isMob ? 70 : isTablet ? 88 : 105;
            const wrapW = $('#trt-deck-wrap').width();

            const rowDefs = isMob
                ? [8, 8, 8, 7, 7, 7, 7]
                : isTablet
                    ? [11, 11, 10, 10, 10]
                    : [13, 13, 13, 13];
            const rows = rowDefs.length;
            let currentIdx = 0;
            for (let r = 0; r < rows; r++) {
                const colsInRow = rowDefs[r];
                const spacingX = Math.min((wrapW - cardW) / (colsInRow - 1), cardW * 0.85);
                const offsetX = -((colsInRow - 1) * spacingX) / 2;
                for (let c = 0; c < colsInRow; c++) {
                    const $card = $('.trt-deck-card').eq(currentIdx);
                    const x = offsetX + c * spacingX;
                    const y = r * gapY;
                    const rot = ((c - (colsInRow - 1) / 2) / (colsInRow - 1)) * 12;
                    const tf = `translate(${x}px, ${y}px) rotate(${rot}deg)`;
                    const z = r * 100 + c;
                    setTimeout(() => {
                        $card.addClass('spread').css({transform: tf, zIndex: z});
                    }, currentIdx * 8);
                    if (!isMob) {
                        $card.hover(
                            function () {
                                if (!$(this).hasClass('selected-card')) $(this).css({
                                    transform: `translate(${x}px, ${y - 15}px) rotate(${rot}deg)`,
                                    zIndex: 1000
                                });
                            },
                            function () {
                                if (!$(this).hasClass('selected-card')) $(this).css({transform: tf, zIndex: z});
                            }
                        );
                    }
                    currentIdx++;
                }
            }
            $('#trt-deck-wrap').css('height', (rows * gapY + 20) + 'px');
        },
        async pick($card) {
            if (this.selectedCount >= this.targetCount) return;
            $card.addClass('selected-card').css({zIndex: 9999}).off('mouseenter mouseleave');

            if (!State.shuffledDeck) {
                $('#trt-deck-instruction').html('✦ Connecting to the cards...').css('opacity', 0.6);
                try {
                    await Ajax.drawPromise;
                } catch (e) {
                    return;
                }
                $('#trt-deck-instruction').html(`✦ Focus on your question and pick <strong>${this.targetCount} cards</strong>`).css('opacity', 1);
            }

            const clickedIndex = parseInt($card.data('index'));
            const cardData = State.shuffledDeck[clickedIndex];
            const posKey = this.positions[this.selectedCount];

            if (!State.cardsLite) State.cardsLite = {};
            State.cardsLite[posKey] = {
                key: cardData.key,
                name: cardData.name,
                suit: cardData.suit
            };
            if (!State.pickedCards) State.pickedCards = [];
            State.pickedCards.push({
                key: cardData.key,
                name: cardData.name,
                suit: cardData.suit,
                rank: cardData.rank
            });

            const $slot = $('.trt-slot').eq(this.selectedCount);
            $slot.addClass('filled');
            $slot.find('.trt-slot-pos').hide();

            const suitSymbols = { hearts: '♥', diamonds: '♦', clubs: '♣', spades: '♠' };
            const suitColor = ['hearts', 'diamonds'].includes(cardData.suit) ? '#c0392b' : '#2c3e50';
            const sym = suitSymbols[cardData.suit] || '';
            const rank = cardData.rank || '';

            const html3D = `
<div class="trt-card-3d">
    <div class="trt-face trt-face-back"><div class="trt-card-back-face"></div></div>
    <div class="trt-face trt-face-front" style="color:${suitColor}">
        <div class="trt-card-corner trt-card-tl"><span class="trt-card-rank">${rank}</span><span class="trt-card-sym">${sym}</span></div>
        <div class="trt-card-center">${sym}</div>
        <div class="trt-card-corner trt-card-br"><span class="trt-card-rank">${rank}</span><span class="trt-card-sym">${sym}</span></div>
    </div>
</div>`;
            const $card3D = $(html3D).appendTo($slot);
            setTimeout(() => {
                $card3D.addClass('flipped');
            }, 50);

            this.selectedCount++;
            $('#trt-selected-count').text(this.selectedCount);

            if (this.selectedCount === this.targetCount) {
                $(document).off('click.deck');
                $('.trt-deck-card').off('mouseenter mouseleave');
                setTimeout(() => {
                    $('#trt-deck-instruction').slideUp(300);
                    if (this.resolvePick) this.resolvePick();
                }, 1200);
            }
        }
    };

    const AppLogic = {
        async runFlow() {
            Steps.show('deck');
            Ajax.draw().catch(e => { });
            await Deck.init();

            if (!State.cardsLite || !State.pickedCards || State.pickedCards.length === 0) return;

            try {
                const revealResult = await Ajax.reveal();
                $('#trt-step-deck').removeClass('active');
                $('#trt-result-box').html(revealResult.html).fadeIn(400);
                $('html,body').animate({scrollTop: $('#trt-result-box').offset().top - 60}, 500);

                $('.ast-action-footer').fadeIn(400);
                $('#trt-detail-container').slideDown(600);

                const $comments = $('#comments, .comments-area, #wp-comments').first();
                if ($comments.length) {
                    $comments.slideDown(400);
                    $('#ast-btn-comment').addClass('active');
                }
            } catch (e) {
                $('#trt-deck-instruction').html('Connection error. Please try again.').css('opacity', 1).show();
            } finally {
                $('#trt-btn-unified-shuffle').removeClass('loading').attr('disabled', false);
            }
        },

        async runDeepAnalyze() {
            const $btn = $('#trt-btn-deep-analyze').addClass('loading').prop('disabled', true);
            $('#ast-analysis-wrap').slideDown(400);

            const loadingTexts = [
                'Connecting...', 'Initializing...', 'Analyzing cards...',
                'Interpreting context...', 'Matching meanings...', 'Compiling results...',
                'Interpreting...', 'Finalizing...', 'Please wait...'
            ];
            let textIdx = 0;
            const $loadingSpan = $btn.find('.trt-btn-loading');
            const textInterval = setInterval(() => {
                textIdx++;
                if (textIdx >= loadingTexts.length) {
                    clearInterval(textInterval);
                    return;
                }
                $loadingSpan.html('<span class="trt-spinner"></span> ' + loadingTexts[textIdx]);
            }, 500);

            try {
                const analyzeResult = await Ajax.analyze();
                clearInterval(textInterval);
                AIResult.tryInject(analyzeResult);

                $('#trt-deep-analyze-form').slideUp(300);

            } catch (error) {
                clearInterval(textInterval);
                $('#trt-err-analyze').text(error || 'Connection error. Please try again later.');
                $('#ast-analysis-wrap').hide();
                $('html, body').animate({ scrollTop: $('#trt-deep-analyze-form').offset().top - 50 }, 400);
                $btn.removeClass('loading').prop('disabled', false);
            }
        }
    };

    $(document).on('click', '.trt-back-btn', function (e) {
        const target = $(this).data('back');
        if (target) {
            e.preventDefault();
            Steps.show(target);
        }
    });

    $(document).on('click', '.ast-tab', function () {
        const tab = $(this).data('tab');
        $('.ast-tab').removeClass('active');
        $('.ast-tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#tab-' + tab).addClass('active');
    });

    $('.trt-spread-btn, .trt-spread-opt').on('click', function() {
        State.spread = $(this).data('spread');
        $('.trt-spread-btn').removeClass('selected');
        $('.trt-spread-opt').removeClass('active');
        $(this).addClass('selected active');
        $('#trt-err-spread').text('');
    });

    $(document).on('click', '.trt-topic-card', function () {
        const topic = $(this).data('topic');

        $('.trt-topic-card').removeClass('selected');
        $(this).addClass('selected');

        $('#trt-err-hub-topic').text('');
        State.topic = topic;
        $('#trt-spread-label').text('🃏 Choose Your Spread');
        Steps.show('spread');
    });

    $('#trt-deep-question').on('input', function () {
        const $count = $(this).closest('.trt-input-section').find('.trt-char-count span');
        $count.text($(this).val().length);
    });

    $(document).on('click', '.trt-chip', function () {
        const $ta = $('#trt-deep-question').filter(':visible');
        if ($ta.length) $ta.val($(this).data('q')).trigger('input').focus();
    });

    $('#trt-btn-unified-shuffle').on('click', function () {
        const $btn = $(this);

        if (!State.spread) {
            $('#trt-err-spread').text('Please select a spread.');
            return;
        }

        $btn.addClass('loading').attr('disabled', true);
        $('#trt-stack-wrap').addClass('trt-shuffling');
        setTimeout(() => {
            $('#trt-stack-wrap').removeClass('trt-shuffling');
            AppLogic.runFlow();
        }, 1200);
    });

    $(document).on('click', '#trt-btn-deep-analyze', function() {
        if (!Auth.require()) return;

        State.question = $('#trt-deep-question').val().trim();

        if (!Validator.question(State.question, 'trt-err-deep-question')) {
            $('html, body').animate({scrollTop: $('#trt-deep-question').offset().top - 100}, 300);
            return;
        }

        AppLogic.runDeepAnalyze();
    });

    $(document).on('click', '#ast-btn-comment', function () {
        const $comments = $('#comments, .comments-area, #wp-comments').first();
        if (!$comments.length) return;

        if ($comments.is(':visible')) {
            $comments.slideUp(300);
            $(this).removeClass('active');
        } else {
            $comments.slideDown(400, function () {
                $('html, body').animate({scrollTop: $comments.offset().top - 20}, 400);
            });
            $(this).addClass('active');
        }
    });

    function openCardModal($cardDetail) {
        const $body = $('#trt-modal-body');
        $body.find('.trt-cd-visual').remove();

        const $bgCard = $cardDetail.find('.trt-cd-visual').clone();
        $bgCard.find('.trt-cv-hint').remove();
        $bgCard.addClass('trt-modal-bg-card');
        $body.prepend($bgCard);

        const suitGradients = {
            hearts:   'linear-gradient(160deg, #be1a1a, #6b0000)',
            diamonds: 'linear-gradient(160deg, #b45a00, #5a2800)',
            clubs:    'linear-gradient(160deg, #035c35, #012415)',
            spades:   'linear-gradient(160deg, #2d2b8f, #0d0b40)',
        };
        const suit = $cardDetail.find('.trt-cd-visual').data('suit') || '';
        $body.css('background', suitGradients[suit] || 'linear-gradient(160deg, #2a2a2a, #111)');

        const $content = $cardDetail.find('.trt-cd-content').clone();
        $content.css('display', 'flex');
        $('#trt-modal-content').html($content);

        $('#trt-card-modal').fadeIn(200).css('display', 'flex');
        $('body').css('overflow', 'hidden');
    }

    function closeCardModal() {
        $('#trt-card-modal').fadeOut(150);
        $('#trt-modal-body').css('background', '');
        $('body').css('overflow', '');
    }

    $(document).on('click', '.trt-cd-visual', function () {
        openCardModal($(this).closest('.trt-card-detail'));
    });

    $(document).on('click', '.trt-card-modal-backdrop, .trt-card-modal-close', function () {
        closeCardModal();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeCardModal();
    });
});