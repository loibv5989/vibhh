jQuery(function ($) {

    const DECK_SIZE     = 44;
    const SPREAD_DATA   = window.ORACLE_SPREADS || {};

    const $config = $('#trt-app-config');

    const State = {
        mode      : $config.data('mode')   || 'hub',
        spread    : $config.data('spread') || '3_cards',
        name      : '',
        topic     : '',
        question  : '',
        cardsLite : null,
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
            return str.trim()
                .replace(/\s+/g, ' ')
                .split(' ')
                .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
                .join(' ');
        },

        parseJSON(str) {
            try { return JSON.parse(str); }
            catch { return null; }
        },
    };

    const Steps = {
        show(id) {
            $('.trt-step').removeClass('active');
            $('#trt-step-' + id).addClass('active');
            $('html, body').animate({ scrollTop: 0 }, 400);
        },
    };

    const Validator = {
        _setError(errorId, msg) {
            $('#' + errorId).text(msg || '');
        },

        name(val, errorId) {
            this._setError(errorId);
            if (!val)              { this._setError(errorId, 'Please enter your name.');       return false; }
            if (val.length > 40)   { this._setError(errorId, 'Name must be 40 characters or fewer.');         return false; }
            if (/\d/.test(val))    { this._setError(errorId, 'Name must not contain numbers.');       return false; }
            return true;
        },

        topic(val, errorId) {
            this._setError(errorId);
            if (!val) { this._setError(errorId, 'Please select a topic.'); return false; }
            return true;
        },

        question(val, errorId) {
            this._setError(errorId);
            if (!val || val.trim().length < 5) {
                this._setError(errorId, 'Please enter a question (at least 5 characters).');
                return false;
            }
            return true;
        },
    };

    const Typewriter = {
        fastMode: false,

        _typeText($el, text, speed, cb) {
            let i        = 0;
            let lastTime = 0;
            const interval = this.fastMode ? 0 : speed + Math.random() * 12;

            const tick = (ts) => {
                if (i >= text.length) { if (cb) cb(); return; }
                if (ts - lastTime >= interval) {
                    $el.append(document.createTextNode(text[i++]));
                    lastTime = ts;
                }
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        },

        run(lines, onDone) {
            this.fastMode = false;
            const $cursor = $('#ast-chat-body .ast-cursor');
            const cssMap  = { greeting: 'ast-tw-greeting', intro: 'ast-tw-intro', closing: 'ast-tw-closing' };
            let idx       = 0;

            const next = () => {
                if (idx >= lines.length) {
                    $cursor.removeClass('ast-cursor-blink').fadeOut(300);
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
                    const $el = $('<span class="ast-tw-index"></span>').insertBefore($cursor);
                    this._typeText($el, line.label + ': ', 24, () => {
                        $('<span class="ast-tw-num">')
                            .css('color', line.color)
                            .text(line.value)
                            .appendTo($el);
                        setTimeout(next, this.fastMode ? 20 : 60);
                    });
                    return;
                }

                const $el = $('<span>', { class: cssMap[line.type] || 'ast-tw-text' }).insertBefore($cursor);
                this._typeText($el, line.text, line.type === 'greeting' ? 30 : 18, () => {
                    setTimeout(next, this.fastMode ? 20 : 120);
                });
            };

            next();
        },
    };

    const AIResult = {
        inject(result) {
            if (!result) return;

            $('.ast-tw-closing, #ast-chat-body .ast-cursor').fadeOut(300);

            const $c = $('#ast-final-result').empty().addClass('ast-content-loaded');

            if (result.is_cached) {
                $c.hide().html(result.html).fadeIn(500);
                $('#trt-disclaimer').fadeIn(400);
                $('.ast-action-footer').fadeIn(400);
            } else {
                $c.html(result.html);
                $('#trt-disclaimer').fadeIn(400);
                $('.ast-action-footer').fadeIn(400);
            }
        },
    };

    const Ajax = {
        drawPromise: null,

        draw() {
            this.drawPromise = $.ajax({
                url         : OracleAjax.api_url + '/draw',
                type        : 'POST',
                contentType : 'application/json',
                data        : JSON.stringify({
                    mode    : State.mode,
                    topic   : State.topic,
                    question: State.question,
                    spread  : State.spread,
                }),
            }).then(res => {
                if (!res.html) return $.Deferred().reject(res.message || 'An error occurred.').promise();
                State.cardsLite = res.cards;
                $('#trt-result-box').html(res.html);
                return res;
            }, xhr => $.Deferred().reject(xhr.responseJSON?.message || 'Connection error. Please try again.').promise());

            return this.drawPromise;
        },

        analyze() {
            return $.ajax({
                url         : OracleAjax.api_url + '/analyze',
                type        : 'POST',
                contentType : 'application/json',
                data        : JSON.stringify({
                    full_name: State.name,
                    mode     : State.mode,
                    topic    : State.topic,
                    question : State.question,
                    spread   : State.spread,
                    cards    : State.cardsLite,
                    hp_trap  : $('#trt-deep-trap').val(),
                }),
            }).then(res => {
                if (res.success === false) return $.Deferred().reject(res.message || 'An error occurred.').promise();
                return res;
            }, xhr => $.Deferred().reject(xhr.responseJSON?.message || 'Connection error. Please try again.').promise());
        },
    };

    const Deck = {
        selectedCount : 0,
        targetCount   : 3,
        positions     : [],
        picking       : false,
        _deferred     : null,

        init() {
            this.selectedCount = 0;
            this.picking       = false;
            State.cardsLite    = null;

            const spreadData   = SPREAD_DATA[State.spread];
            this.positions     = Object.keys(spreadData.positions);
            this.targetCount   = spreadData.count;

            this._buildCards();
            this._buildSlots(spreadData);
            this._resetUI();

            this._deferred = $.Deferred();
            $(document).off('click.deck').on('click.deck', '.trt-deck-card:not(.selected-card)', (e) => this._pick($(e.currentTarget)));
            setTimeout(() => this._spread(), 100);

            return this._deferred.promise();
        },

        _buildCards() {
            const html = Array.from({ length: DECK_SIZE }, (_, i) =>
                `<div class="trt-deck-card" data-index="${i}"><div class="trt-card-back-face"></div></div>`
            ).join('');
            $('#trt-deck-wrap').empty().html(html);
        },

        _buildSlots(spreadData) {
            const $wrap = $('#trt-dynamic-slots').empty()
                .removeClass()
                .addClass('trt-selected-slots trt-layout-' + State.spread);

            $.each(spreadData.positions, (key, label, idx) => {
                $wrap.append(
                    $('<div class="trt-slot">').attr('data-slot', idx)
                        .append($('<span class="trt-slot-pos">').text(label))
                );
            });
        },

        _resetUI() {
            $('#trt-deck-instruction')
                .show()
                .html(`✦ Focus on your question and choose <strong>${this.targetCount} cards</strong>`)
                .css('opacity', 1);
            $('#trt-selected-count').text('0');
            $('#trt-target-count').text(this.targetCount);
        },

        _spread() {
            const isMob  = window.innerWidth < 560;
            const rows   = isMob ? 6 : 4;
            const cardW  = isMob ? 40 : 56;
            const gapY   = isMob ? 75 : 105;
            const wrapW  = $('#trt-deck-wrap').width();

            const baseCols = Math.floor(DECK_SIZE / rows);
            const extra    = DECK_SIZE % rows;
            let idx        = 0;

            for (let r = 0; r < rows; r++) {
                const cols     = baseCols + (r < extra ? 1 : 0);
                const spacingX = Math.min((wrapW - cardW) / (cols - 1), cardW * 0.85);
                const offsetX  = -((cols - 1) * spacingX) / 2;

                for (let c = 0; c < cols; c++) {
                    const $card = $('.trt-deck-card').eq(idx);
                    const x     = offsetX + c * spacingX;
                    const y     = r * gapY;
                    const rot   = ((c - (cols - 1) / 2) / (cols - 1)) * 12;
                    const tf    = `translate(${x}px, ${y}px) rotate(${rot}deg)`;
                    const z     = r * 100 + c;

                    setTimeout(() => {
                        $card.addClass('spread').css({ transform: tf, zIndex: z });
                    }, idx * 8);

                    if (!isMob) {
                        $card.data({ tf, z });
                    }
                    idx++;
                }
            }

            $('#trt-deck-wrap').css('height', rows * gapY + 20 + 'px');

            if (!isMob) {
                this._bindHover();
            }
        },

        _bindHover() {
            $('#trt-deck-wrap')
                .off('mouseenter.deck mouseleave.deck')
                .on('mouseenter.deck', '.trt-deck-card:not(.selected-card)', function () {
                    const tf    = $(this).data('tf') || '';
                    const mPos  = tf.match(/translate\(([^,]+),\s*([^)]+)\)/);
                    const mRot  = tf.match(/rotate\(([^)]+)\)/);
                    const rot   = mRot ? mRot[1] : '0deg';
                    if (mPos) {
                        $(this).css({
                            transform : `translate(${mPos[1]}, calc(${mPos[2]} - 15px)) rotate(${rot})`,
                            zIndex    : 1000,
                        });
                    }
                })
                .on('mouseleave.deck', '.trt-deck-card:not(.selected-card)', function () {
                    $(this).css({ transform: $(this).data('tf'), zIndex: $(this).data('z') });
                });
        },

        _pick($card) {
            if (this.selectedCount >= this.targetCount || this.picking) return;
            this.picking = true;

            $card.addClass('selected-card').css({ zIndex: 9999 }).off('mouseenter mouseleave');

            const proceed = () => {
                const posKey   = this.positions[this.selectedCount];
                const cardData = State.cardsLite[posKey];
                const $slot    = $('.trt-slot').eq(this.selectedCount);

                $slot.addClass('filled').find('.trt-slot-pos').hide();

                const $card3D = $(
                    `<div class="trt-card-3d">
                        <div class="trt-face trt-face-back"><div class="trt-card-back-face"></div></div>
                        <div class="trt-face trt-face-front">
                            <div class="trt-front-name">${cardData.name}</div>
                            <div class="trt-front-orient"></div>
                        </div>
                    </div>`
                ).appendTo($slot);

                setTimeout(() => $card3D.addClass('flipped'), 50);

                this.selectedCount++;
                this.picking = false;
                $('#trt-selected-count').text(this.selectedCount);

                if (this.selectedCount === this.targetCount) {
                    $(document).off('click.deck');
                    $('#trt-deck-wrap').off('mouseenter.deck mouseleave.deck');
                    setTimeout(() => {
                        $('#trt-deck-instruction').slideUp(300);
                        this._deferred.resolve();
                    }, 1200);
                }
            };

            if (!State.cardsLite) {
                $('#trt-deck-instruction').html('✦ Connecting to the cards...').css('opacity', 0.6);

                $.when(Ajax.drawPromise).then(() => {
                    $('#trt-deck-instruction')
                        .html(`✦ Focus on your question and choose <strong>${this.targetCount} cards</strong>`)
                        .css('opacity', 1);
                    proceed();
                }).fail(() => {
                    $('#trt-deck-instruction').html('Connection error. Please reload the page.').css('color', 'red');
                    $card.removeClass('selected-card');
                    this.picking = false;
                });
            } else {
                proceed();
            }
        },
    };

    const FAQ = {
        toggle($btn) {
            const $ans  = $btn.closest('.trt-faq-item-oracle').find('.trt-faq-ans-oracle');
            const $ico  = $btn.find('.trt-faq-ico-oracle');
            const isOpen = $ans[0].style.maxHeight && $ans[0].style.maxHeight !== '0px';

            if (isOpen) {
                $ans.css({ maxHeight: '0px', paddingBottom: '0' });
                $ico.css('transform', 'rotate(0deg)');
            } else {
                $ans.css({ maxHeight: $ans[0].scrollHeight + 'px', paddingBottom: '18px' });
                $ico.css('transform', 'rotate(45deg)');
            }
        },
    };

    const AppLogic = {
        runFlow() {
            Steps.show('deck');
            Ajax.draw();

            $.when(Deck.init()).then(() => {
                if (!State.cardsLite) return;

                $('#trt-step-deck').removeClass('active');
                $('#trt-result-box').fadeIn(400);

                $('html, body').animate({
                    scrollTop: $('#trt-result-box').offset().top - 60,
                }, 500);

                /* Typewriter if there are lines */
                const lines = Utils.parseJSON($('#ast-chat-body').attr('data-lines'));
                const tw$   = $.Deferred();
                if (lines) {
                    Typewriter.run(lines, () => tw$.resolve());
                } else {
                    tw$.resolve();
                }

                $.when(tw$).then(() => {
                    $('.ast-action-footer').fadeIn(400);
                    $('#trt-detail-container').slideDown(600);
                });
            });
        },

        runDeepAnalyze() {
            const $btn = $('#trt-btn-deep-analyze').addClass('loading').prop('disabled', true);
            $('#ast-analysis-wrap').slideDown(400);

            $.when(Ajax.analyze())
                .then(result => {
                    if (result.is_cached) Typewriter.fastMode = true;
                    AIResult.inject(result);
                    $('#trt-deep-analyze-form').slideUp(300);
                })
                .fail((msg) => {
                    $('#trt-err-analyze').text(msg || 'Connection error. Please try again later.');
                    $('#ast-final-result').html('').hide();
                    $('html, body').animate({ scrollTop: $('#trt-deep-analyze-form').offset().top - 50 }, 400);
                    $btn.removeClass('loading').prop('disabled', false);
                });
        },
    };

    $(document).on('click', '.trt-back-btn', function (e) {
        const target = $(this).data('back');
        if (target) { e.preventDefault(); Steps.show(target); }
    });

    $(document).on('click', '.trt-spread-btn', function () {
        if (State.mode === 'question') {
            State.question = $('#trt-question').val().trim();
            if (!Validator.question(State.question, 'trt-err-question')) {
                const $q = $('#trt-question');
                $('html, body').animate({ scrollTop: $q.offset().top - 100 }, 300);
                $q.focus();
                return;
            }
        }
        State.spread = $(this).data('spread');
        AppLogic.runFlow();
    });

    $(document).on('click', '.trt-topic-card', function () {
        $('.trt-topic-card').removeClass('selected');
        $(this).addClass('selected');
        State.topic = $(this).data('topic');
        $('#trt-topic-val').val(State.topic);
        $('#trt-err-topic-a').text('');
        AppLogic.runFlow();
    });

    $(document).on('input', '#trt-question', function () {
        $('#trt-q-count').text($(this).val().length);
    });

    $(document).on('click', '.trt-chip', function () {
        $('#trt-question').val($(this).data('q')).trigger('input').focus();
    });

    $(document).on('blur', '#trt-deep-name', function() {
        let val = $(this).val().trim();
        if (val) {
            val = Utils.capitalizeName(val);
            $(this).val(val);
        }
    });

    $(document).on('click', '#trt-btn-deep-analyze', function () {
        if (!Auth.require()) return;

        let val = $('#trt-deep-name').val().trim();
        if (val) { val = Utils.capitalizeName(val); $('#trt-deep-name').val(val); }
        State.name = val;
        if (Validator.name(State.name, 'trt-err-deep-name')) {
            AppLogic.runDeepAnalyze();
        }
    });

    $(document).on('click', '.trt-faq-toggle', function () {
        FAQ.toggle($(this));
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

});