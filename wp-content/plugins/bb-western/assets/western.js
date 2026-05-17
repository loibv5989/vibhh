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
        mode: $config.data('mode') || 'hub',
        spread: $config.data('spread') || '3_cards',
        name: '', topic: '', question: '', cardsLite: null, resultHtml: ''
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

    const Utils = {
        capitalizeName(str) {
            return str.trim().replace(/\s+/g, ' ').split(' ').map(word =>
                word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
            ).join(' ');
        },
    };


    const Validator = {
        name(val, errorId) {
            const $e = $('#' + errorId);
            $e.text('');
            if (!val) {
                $e.text('Vui lòng nhập họ và tên.');
                return false;
            }
            if (val.length > 40) {
                $e.text('Họ tên tối đa 40 ký tự.');
                return false;
            }
            if (/\d/.test(val)) {
                $e.text('Họ tên không được chứa số.');
                return false;
            }
            return true;
        },
        topic(val, errorId) {
            const $e = $('#' + errorId);
            $e.text('');
            if (!val) {
                $e.text('Vui lòng chọn chủ đề.');
                return false;
            }
            return true;
        },
        question(val, errorId) {
            const $e = $('#' + errorId);
            $e.text('');
            if (!val || val.trim().length < 5) {
                $e.text('Vui lòng nhập câu hỏi (tối thiểu 5 ký tự).');
                return false;
            }
            return true;
        }
    };

    const AIResult = {
        injectHints(hints) {
            Object.entries(hints).forEach(([key, hint]) => {
                $('[data-hint-key="' + key + '"]')
                    .stop(true, true).css('display', '')
                    .text(' — ' + hint);
            });
        },
        tryInject(result) {
            if (!result) return;

            const $c = $('#ast-final-result');
            $c.empty().addClass('ast-content-loaded');

            if (!result.is_cached) {
                $c.html(result.html);
            } else {
                $c.css('opacity', 0).html(result.html).animate({ opacity: 1 }, 500);
            }
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
                        full_name: State.name,
                        mode: State.mode,
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread
                    }),
                    success: res => {
                        if (res.success) {
                            State.cardsLite = res.cards;
                            State.resultHtml = res.html;
                            resolve(res);
                        } else {
                            reject(res.message || 'Đã có lỗi xảy ra.');
                        }
                    },
                    error: () => reject('Lỗi kết nối. Vui lòng thử lại.')
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
                        full_name: State.name,
                        mode: State.mode,
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread,
                        cards: State.cardsLite,
                        hp_trap: $('#trt-deep-trap').val()
                    }),
                    success: res => res.success ? resolve(res) : reject(res.message || 'Đã có lỗi xảy ra.'),
                    error: () => reject('Lỗi kết nối. Vui lòng thử lại.')
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

            $('#trt-deck-instruction').show().html(`✦ Tập trung vào câu hỏi của bạn và chọn <strong>${this.targetCount} lá bài</strong>`).css('opacity', 1);
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
            // Desktop  (≥1024): 4 hàng × 13 — mỗi hàng 1 chất
            // Tablet   (768-1023): 5 hàng — 11, 11, 10, 10, 10
            // Mobile   (<768):    7 hàng — 8, 8, 8, 7, 7, 7, 7
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

            if (!State.cardsLite) {
                $('#trt-deck-instruction').html('✦ Đang kết nối với các lá bài...').css('opacity', 0.6);
                try {
                    await Ajax.drawPromise;
                } catch (e) {
                    return;
                }
                $('#trt-deck-instruction').html(`✦ Tập trung vào câu hỏi của bạn và chọn <strong>${this.targetCount} lá bài</strong>`).css('opacity', 1);
            }

            const posKey = this.positions[this.selectedCount];
            const cardData = State.cardsLite[posKey];
            const $slot = $('.trt-slot').eq(this.selectedCount);
            $slot.addClass('filled');
            $slot.find('.trt-slot-pos').hide();

            const html3D = `
<div class="trt-card-3d">
    <div class="trt-face trt-face-back"><div class="trt-card-back-face"></div></div>
    <div class="trt-face trt-face-front">
    <div class="trt-front-name">${cardData.name_vi}</div>
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

            if (!State.cardsLite) return;

            $('#trt-step-deck').removeClass('active');
            $('#trt-result-box').html(State.resultHtml).fadeIn(400);
            $('html,body').animate({scrollTop: $('#trt-result-box').offset().top - 60}, 500);

            $('.ast-action-footer').fadeIn(400);
            $('#trt-detail-container').slideDown(600);
        },

        async runDeepAnalyze() {
            const $btn = $('#trt-btn-deep-analyze').addClass('loading').prop('disabled', true);
            $('#ast-analysis-wrap').slideDown(400);

            try {
                const analyzeResult = await Ajax.analyze();
                AIResult.tryInject(analyzeResult);

                $('#trt-deep-analyze-form').slideUp(300);

            } catch (error) {
                $('#trt-err-analyze').text(error || 'Lỗi kết nối. Vui lòng thử lại sau.');
                $('#ast-analysis-wrap').html('').hide();
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

    $('.trt-spread-btn').on('click', function() {
        State.spread = $(this).data('spread');
        Steps.show('input-b');
    });

    $(document).on('click', '.trt-topic-card', function () {
        $('.trt-topic-card').removeClass('selected');
        $(this).addClass('selected');
        State.topic = $(this).data('topic');
        $('#trt-topic-val').val(State.topic);
        $('#trt-err-topic-a').text('');

        AppLogic.runFlow();
    });

    $('#trt-question').on('input', function () {
        $('#trt-q-count').text($(this).val().length);
    });

    $(document).on('click', '.trt-chip', function () {
        $('#trt-question').val($(this).data('q')).trigger('input').focus();
    });

    $('#trt-btn-submit-b').on('click', function () {
        State.question = $('#trt-question').val().trim();

        if (!Validator.question(State.question, 'trt-err-question')) {
            $('html, body').animate({
                scrollTop: $('#trt-question').offset().top - 100
            }, 300);
            return;
        }
        $(this).addClass('loading').attr('disabled', true);
        AppLogic.runFlow();
    });

    $(document).on('blur', '#trt-deep-name', function() {
        let val = $(this).val();
        if (val) {
            val = Utils.capitalizeName(val);
            $(this).val(val);
        }
    });

    $(document).on('click', '#trt-btn-deep-analyze', function() {
        if (!Auth.require()) return;

        let val = $('#trt-deep-name').val();
        if (val) {
            val = Utils.capitalizeName(val);
            $('#trt-deep-name').val(val);
        }
        State.name = val;

        if (Validator.name(State.name, 'trt-err-deep-name')) {
            AppLogic.runDeepAnalyze();
        }
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

        // Gradient background theo chất bài
        const suitGradients = {
            hearts:   'linear-gradient(160deg, #be1a1a, #6b0000)',
            diamonds: 'linear-gradient(160deg, #b45a00, #5a2800)',
            clubs:    'linear-gradient(160deg, #035c35, #012415)',
            spades:   'linear-gradient(160deg, #2d2b8f, #0d0b40)',
        };
        const suit = $cardDetail.find('.trt-cd-visual').data('suit') || '';
        $body.css('background', suitGradients[suit] || 'linear-gradient(160deg, #2a2a2a, #111)');

        const $content = $cardDetail.find('.trt-cd-content').clone();
        $content.css('display', 'block');
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