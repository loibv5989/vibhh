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
        name: '', topic: '', question: '', cardsLite: null, resultHtml: '', hints: null
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

    const Typewriter = {
        fastMode: false,
        typeText($el, text, speed, cb) {
            let i = 0;
            const tick = () => {
                if (i >= text.length) {
                    if (cb) cb();
                    return;
                }
                $el.append(document.createTextNode(text[i++]));
                setTimeout(tick, this.fastMode ? 0 : speed + Math.random() * 12);
            };
            tick();
        },
        run(lines, onDone) {
            this.fastMode = false;
            const $cursor = $('#ast-chat-body .ast-cursor');
            let idx = 0;
            const next = () => {
                if (idx >= lines.length) {
                    $cursor.fadeOut(300);
                    if (onDone) onDone();
                    return;
                }
                const line = lines[idx++];
                if (line.type === 'divider') {
                    $cursor.before('\n\n');
                    setTimeout(next, this.fastMode ? 20 : 90);
                    return;
                }
                if (line.type === 'index') {
                    const $el = $('<span class="ast-tw-index"></span>');
                    $cursor.before($el);
                    this.typeText($el, line.label + ': ', 24, () => {
                        $el.append($('<span class="ast-tw-num" style="color:' + line.color + '">' + line.value + '</span>'));
                        $el.append($('<span class="ast-hint-skeleton ast-tw-hint" data-hint-key="' + line.key + '"></span>'));
                        setTimeout(next, this.fastMode ? 20 : 60);
                    });
                    return;
                }
                const cssMap = {greeting: 'ast-tw-greeting', intro: 'ast-tw-intro', closing: 'ast-tw-closing'};
                const $el = $('<span class="' + (cssMap[line.type] || 'ast-tw-text') + '"></span>');
                $cursor.before($el);
                this.typeText($el, line.text, line.type === 'greeting' ? 30 : 18, () => {
                    setTimeout(next, this.fastMode ? 20 : 120);
                });
            };
            next();
        }
    };

    const AIResult = {
        injectHints(hints) {
            Object.entries(hints).forEach(([key, hint]) => {
                $('[data-hint-key="' + key + '"]')
                    .stop(true, true).css('display', '')
                    .text(' — ' + hint)
                    .removeClass('ast-hint-skeleton')
                    .addClass('ast-tw-hint-text');
            });
            $('.ast-hint-skeleton').fadeOut(200);
        },
        tryInject(result) {
            if (!result) return;

            $('.ast-tw-closing, #ast-chat-body .ast-cursor').fadeOut(300);

            if (result.hints && Object.keys(result.hints).length) {
                this.injectHints(result.hints);
            } else {
                $('.ast-hint-skeleton').fadeOut(200);
            }

            const $c = $('#ast-final-result');
            $c.empty().addClass('ast-content-loaded');

            if (!result.is_cached) {
                $c.html(result.html);
            } else {
                $c.css('opacity', 0).html(result.html).animate({ opacity: 1 }, 500);
            }
            $('#trt-disclaimer').fadeIn(400);
            $('.ast-action-footer').fadeIn(400);
        }
    };

    const Ajax = {
        drawPromise: null,
        draw() {
            this.drawPromise = new Promise((resolve, reject) => {
                $.ajax({
                    url: TarotAjax.api_url + 'draw', type: 'POST',
                    data: {
                        mode: State.mode,
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread
                    },
                    success: res => {
                        if (res && res.success) {
                            State.cardsLite = res.cards;
                            State.resultHtml = res.html;
                            State.hints = res.hints || null;
                            resolve(res);
                        } else {
                            reject(res?.message || 'Đã có lỗi xảy ra.');
                        }
                    },
                    error: xhr => reject(xhr?.responseJSON?.message || 'Lỗi kết nối. Vui lòng thử lại.')
                });
            });
            return this.drawPromise;
        },
        analyze() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: TarotAjax.api_url + 'analyze', type: 'POST',
                    data: {
                        full_name: State.name,
                        mode: State.mode,
                        topic: State.topic,
                        question: State.question,
                        spread: State.spread,
                        cards: JSON.stringify(State.cardsLite),
                        hp_trap: $('#trt-deep-trap').val(),
                    },
                    success: res => (res && res.success) ? resolve(res) : reject(res?.message || 'Đã có lỗi xảy ra.'),
                    error: xhr => reject(xhr?.responseJSON?.message || 'Lỗi kết nối. Vui lòng thử lại.')
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

            const spreadData = window.TAROT_SPREADS[State.spread];
            this.positions = Object.keys(spreadData.positions);
            this.targetCount = spreadData.count;

            const $wrap = $('#trt-deck-wrap').empty();
            const html = Array.from({length: 78}, (_, i) =>
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
            const isMob = window.innerWidth < 560;
            const rows = isMob ? 6 : 4;
            const cardW = isMob ? 40 : 56;
            const gapY = isMob ? 75 : 105;
            const wrapW = $('#trt-deck-wrap').width();
            const total = 78;
            const baseCols = Math.floor(total / rows);
            const extra = total % rows;
            let currentIdx = 0;
            for (let r = 0; r < rows; r++) {
                const colsInRow = baseCols + (r < extra ? 1 : 0);
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
            const orientClass = cardData.orientation === 'reversed' ? 'is-reversed' : '';
            const html3D = `
<div class="trt-card-3d">
    <div class="trt-face trt-face-back"><div class="trt-card-back-face"></div></div>
    <div class="trt-face trt-face-front ${orientClass}">
    <div class="trt-front-name">${cardData.name_vi}</div>
<div class="trt-front-orient">${cardData.orientation === 'upright' ? '↑ Xuôi' : '↓ Ngược'}</div>
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
            Ajax.draw().catch(e => {});
            await Deck.init();

            if (!State.cardsLite) return;

            $('#trt-step-deck').removeClass('active');
            $('#trt-result-box').html(State.resultHtml).fadeIn(400);
            $('html,body').animate({scrollTop: $('#trt-result-box').offset().top - 60}, 500);

            const linesText = $('#ast-chat-body').attr('data-lines');
            if (linesText) {
                const lines = JSON.parse(linesText);
                await new Promise(res => Typewriter.run(lines, res));
            }

            if (State.hints) {
                AIResult.injectHints(State.hints);
            }

            $('.ast-action-footer').fadeIn(400);

            $('#trt-detail-container').slideDown(600);
        },

        async runDeepAnalyze() {
            const $btn = $('#trt-btn-deep-analyze').addClass('loading').prop('disabled', true);

            $('#ast-analysis-wrap').slideDown(400);

            try {
                const analyzeResult = await Ajax.analyze();
                if (analyzeResult.is_cached) Typewriter.fastMode = true;

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

    $(document).on('click', '.trt-spread-btn', function() {
        State.spread = $(this).data('spread');
        let nextStep = $(this).data('next') || 'input-b';
        Steps.show(nextStep);
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

    $('#trt-question-love').on('input', function () {
        $('#trt-q-count-love').text($(this).val().length);
    });

    $(document).on('click', '#trt-step-input-love .trt-chip', function () {
        $('#trt-question-love').val($(this).data('q')).trigger('input').focus();
    });

    $('#trt-btn-submit-love').on('click', function () {
        State.question = $('#trt-question-love').val().trim();
        State.topic = 'love';
        State.mode = 'love';

        if (!Validator.question(State.question, 'trt-err-question-love')) {
            $('html, body').animate({
                scrollTop: $('#trt-question-love').offset().top - 100
            }, 300);
            return;
        }

        $(this).addClass('loading').attr('disabled', true);
        AppLogic.runFlow();
    });

    $(document).on('click', '#ast-btn-comment', function () {
        const $comments = $('#comments');
        if (!$comments.length) return;
        if ($comments.is(':visible')) {
            $comments.slideUp(300);
            $(this).removeClass('active');
        } else {
            $comments.slideDown(400, function () {
                $('html, body').animate({ scrollTop: $comments.offset().top - 20 }, 400);
            });
            $(this).addClass('active');
        }
    });
});