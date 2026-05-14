jQuery(function ($) {
    const apiUrl = ichingData.api_url;

    const State = {
        name: '', gender: '', topic: 'general', question: '',
        lite: null, tossCount: 0, resultHtml: '', payload: {},
        isLoggedIn: false
    };

    const scrollTo = (selector, offset = 30) =>
        $('html, body').animate({ scrollTop: $(selector).offset().top - offset }, 400);

    const postJson = (endpoint, body, signal) =>
        fetch(apiUrl + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
            ...(signal ? { signal } : {})
        });

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
            return str.trim().replace(/\s+/g, ' ').toLocaleLowerCase('vi-VN')
                .split(' ').filter(Boolean)
                .map(w => w.split('-').map(p => p ? p.charAt(0).toLocaleUpperCase('vi-VN') + p.slice(1) : '').join('-'))
                .join(' ');
        },
    };

    const Steps = {
        show(id) {
            $('.ich-step').removeClass('active');
            $('#ich-step-' + id).addClass('active');
            const $wrap = $('.ich-step-header');
            if ($wrap.length) scrollTo('.ich-step-header');
        }
    };

    const makeValidator = (fn) => (val, errorId, ...args) => {
        const $e = $('#' + errorId);
        $e.text('');
        return fn($e, val, ...args);
    };

    const Validator = {
        name: makeValidator(($e, val) => {
            if (!$e.length) return true;
            if (!val) return $e.text('Vui lòng nhập họ và tên.'), false;
            if (val.length > 40 || /\d/.test(val)) return $e.text('Họ tên không hợp lệ.'), false;
            return true;
        }),
        question: makeValidator(($e, val) => {
            if (!val || val.trim().length < 5) return $e.text('Vui lòng nhập câu hỏi đủ ý.'), false;
            if (val.length > 500) return $e.text('Câu hỏi quá dài, hãy mô tả trọng tâm vào vấn đề chính của bạn.'), false;
            return true;
        }),
        gender: makeValidator(($e, val) => {
            if (!val) return $e.text('Vui lòng chọn giới tính.'), false;
            return true;
        }),
        maihoa_number: makeValidator(($e, val) => {
            if (!val || !/^\d+$/.test(val)) return $e.text('Vui lòng nhập một dãy số hợp lệ.'), false;
            if (val.length > 50) return $e.text('Dãy số quá dài. Vui lòng nhập tối đa 50 chữ số.'), false;
            return true;
        }),
        maihoa_object: makeValidator(($e, v1, v2) => {
            if (!v1 || !v2 || v1 <= 0 || v2 <= 0) return $e.text('Vui lòng nhập cả 2 số lớn hơn 0.'), false;
            return true;
        }),
        maihoa_time: makeValidator(($e, val) => {
            if (!val) return $e.text('Vui lòng nhập thời điểm.'), false;

            const regex = /^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2})$/;
            const match = val.trim().match(regex);
            if (!match) return $e.text('Sai định dạng. Hãy nhập theo mẫu: DD/MM/YYYY HH:MM'), false;

            const d = parseInt(match[1], 10);
            const m = parseInt(match[2], 10);
            const y = parseInt(match[3], 10);
            const h = parseInt(match[4], 10);
            const min = parseInt(match[5], 10);

            if (h > 23 || min > 59) {
                return $e.text('Giờ hoặc phút không hợp lệ.'), false;
            }

            const date = new Date(y, m - 1, d, h, min);
            if (
                date.getFullYear() !== y ||
                date.getMonth() !== m - 1 ||
                date.getDate() !== d
            ) {
                return $e.text('Ngày tháng này không tồn tại.'), false;
            }

            const now = new Date();

            const inputDayOnly = new Date(y, m - 1, d);
            const todayOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            if (inputDayOnly > todayOnly) {
                return $e.text('Ngày đó còn chưa đến.'), false;
            }

            if (inputDayOnly.getTime() === todayOnly.getTime()) {
                if (date > now) {
                    return $e.text('Thời điểm này trong ngày hôm nay chưa diễn ra.'), false;
                }
            }
            const limitDate = new Date(now.getTime() - 24 * 60 * 60 * 1000);
            if (date < limitDate) {
                return $e.text('Đã qua 1 ngày: ý niệm ban đầu có thể đã biến, quẻ không còn phản ánh đúng động tâm lúc đầu. Chỉ nên lập quẻ khi tâm còn giữ nguyên, chưa bị tác động hay thay đổi quyết định.'), false;
            }

            return true;
        })
    };

    const HAO_HINT = {
        6: { label: 'Hào Âm Động',    color: '#ff6b6b' },
        7: { label: 'Hào Dương',       color: '#d4af37' },
        8: { label: 'Hào Âm',          color: '#8b9dc3' },
        9: { label: 'Hào Dương Động',  color: '#ff6b6b' }
    };

    const COIN_FACES = {
        6: ['tails', 'tails', 'tails'],
        7: ['heads', 'heads', 'tails'],
        8: ['tails', 'tails', 'heads'],
        9: ['heads', 'heads', 'heads']
    };

    const Coins = {
        toss() {
            if (State.tossCount >= 6) return;

            const $btn = $('#ich-btn-toss').addClass('loading').prop('disabled', true);
            const $coins = $('.ich-coin').removeClass('heads tails spinning');

            ['#ich-coin-1', '#ich-coin-2', '#ich-coin-3'].forEach((id, i) =>
                setTimeout(() => $(id).addClass('spinning'), i * 150)
            );

            setTimeout(() => {
                $coins.removeClass('spinning');

                const sum   = State.lite.tosses[State.tossCount];
                const faces = COIN_FACES[sum].slice().sort(() => Math.random() - 0.5);
                $coins.each((i, el) => $(el).addClass(faces[i]));

                const isYang     = sum === 7 || sum === 9;
                const isChanging = sum === 6 || sum === 9;
                const changingMark = isChanging
                    ? `<span class="ich-change-mark">${isYang ? '○' : '×'}</span>` : '';
                const lineHtml = `<div class="ich-line ${isYang ? 'ich-yang' : 'ich-yin'} ${isChanging ? 'ich-changing' : ''}"></div>${changingMark}`;

                $(`.ich-line-slot[data-index="${State.tossCount}"] .ich-line-draw`)
                    .html(lineHtml).hide().fadeIn(400);

                const hint = HAO_HINT[sum];
                if (hint) {
                    $('#ich-toss-result-hint')
                        .html(`Tổng: <strong>${sum}</strong> → <span style="color:${hint.color};font-weight:700">${hint.label}</span>`)
                        .hide().fadeIn(300);
                }

                State.tossCount++;

                if (State.tossCount < 6) {
                    $('#ich-toss-num').text(State.tossCount + 1);
                    $btn.removeClass('loading').prop('disabled', false)
                        .find('.ich-btn-text').text('Tung Lần ' + (State.tossCount + 1));
                } else {
                    $('#ich-toss-num').text(6);
                    $('#ich-toss-action').slideUp(300);
                    $('#ich-toss-result-hint').html('<strong style="color:var(--lbv-color-1)">Đã lập quẻ xong! Đang phân tích...</strong>');
                    setTimeout(() => AppLogic.runAnalysis(), 800);
                }
            }, 1500);
        }
    };

    const Typewriter = {
        fastMode: false,
        run(lines, onDone) {
            const $cursor = $('#ast-chat-body .ast-cursor');
            let idx = 0;

            const next = () => {
                if (idx >= lines.length) {
                    $cursor.hide();
                    return onDone && onDone();
                }

                const line = lines[idx++];

                if (line.type === 'divider') {
                    $cursor.before('<br>');
                    return setTimeout(next, this.fastMode ? 20 : 90);
                }

                const $el = $(`<span class="${line.type === 'greeting' ? 'ast-tw-greeting' : 'ast-tw-text'}"></span>`);
                $cursor.before($el);

                let i = 0;
                const txt = line.text;
                const delay = this.fastMode ? 0 : 25;
                const nextDelay = this.fastMode ? 20 : 100;

                const tick = () => {
                    if (i >= txt.length) {
                        if (idx < lines.length && lines[idx].type !== 'divider') $el.after('<br>');
                        return setTimeout(next, nextDelay);
                    }
                    $el.append(txt[i++]);
                    setTimeout(tick, delay);
                };
                tick();
            };
            next();
        }
    };

    const AIResult = {
        tryInject(result) {
            if (!result) return;
            $('.ast-tw-closing, .ast-cursor').fadeOut(300);
            $('#ast-analysis-wrap').fadeIn(400);
            $('.ich-analysis-header').show();

            const $c = $('#ast-final-result');
            $c.empty().addClass('ast-content-loaded')
                .css('opacity', 0).html(result.html)
                .animate({ opacity: 1 }, 500, () => $('#ich-disclaimer').fadeIn(400));
        }
    };

    const AppLogic = {
        async startDraw() {
            const $btn = $('#ich-btn-submit-form').addClass('loading').prop('disabled', true);
            try {
                const res  = await postJson('draw', State.payload);
                const data = await res.json();
                if (data.success) {
                    State.lite       = data.data.lite;
                    State.resultHtml = data.data.html;
                    State.isLoggedIn = data.is_logged_in || false;
                    State.payload.mode.startsWith('maihoa') ? AppLogic.runAnalysis() : Steps.show('toss');
                } else {
                    $('#ich-err-question').text(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                }
            } finally {
                $btn.removeClass('loading').prop('disabled', false);
            }
        },

        async runAnalysis() {
            $('.ich-step').removeClass('active').hide();
            $('#ich-result-box').html(State.resultHtml).fadeIn(400);
            MaiHoaScale.init();
            $('html, body').animate({ scrollTop: $('#ich-wrap').offset().top - 30 }, 500);

            const lines = JSON.parse($('#ast-chat-body').attr('data-lines'));
            await new Promise(res => Typewriter.run(lines, res));

            $('#ich-btn-deep-analyze').data('can-analyze', State.isLoggedIn || false);
            $('#ich-detail-content, #ich-deep-analysis-form').fadeIn(600);
            $('.ich-reload, #ich-btn-comment').fadeIn(400);
        }
    };

    const MaiHoaScale = {
        observer: null,
        init() {
            const panel = document.querySelector('.ich-detail-panel[data-panel="lapque"]');
            const wrap  = document.querySelector('.lhq-wrap');

            if (!panel || !wrap) return;

            const updateScale = () => {
                if (window.innerWidth > 767) {
                    wrap.style.removeProperty('--mh-scale');
                    panel.style.height = 'auto';
                    return;
                }

                let currentWidth = panel.clientWidth;
                if (currentWidth === 0) currentWidth = window.innerWidth - 40;

                if (currentWidth < 700) {
                    const scale = Math.max(0.42, currentWidth / 700);
                    wrap.style.setProperty('--mh-scale', scale);
                    panel.style.height = (wrap.offsetHeight * scale + 25) + 'px';
                } else {
                    wrap.style.removeProperty('--mh-scale');
                    panel.style.height = 'auto';
                }
            };

            updateScale();

            if (this.observer) this.observer.disconnect();

            this.observer = new ResizeObserver(() => window.requestAnimationFrame(updateScale));
            this.observer.observe(document.body);
        }
    };

    const LhqCapture = {
        save() {
            typeof html2canvas === 'undefined'
                ? this.loadLibrary(() => this.capture())
                : this.capture();
        },

        loadLibrary(callback) {
            if (this.isLoadingLibrary) return;
            this.isLoadingLibrary = true;

            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.3/html2canvas.min.js';
            script.onload = () => {
                this.isLoadingLibrary = false;
                callback();
            };
            document.head.appendChild(script);
        },

        capture() {
            const element = document.querySelector('.lhq-wrap');
            const btn     = document.getElementById('lhq-btn-save');
            if (!element || !btn) return;

            const originalBtnHtml = btn.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = '<span>Đang tạo ảnh...</span>';

            const originalStyles = this.overrideStyles();

            html2canvas(element, {
                backgroundColor: '#ffffff',
                scale: 3,
                logging: false,
                useCORS: true,
                windowWidth: 1440,
                ignoreElements: el =>
                    el.classList && (el.classList.contains('lhq-save-row') || el.classList.contains('lhq-btn-save')),
                onclone: (clonedDoc) => {
                    const clonedWrap = clonedDoc.querySelector('.lhq-wrap');
                    if (clonedWrap) {
                        clonedWrap.style.width = '900px';
                        clonedWrap.style.maxWidth = '900px';

                        clonedWrap.style.transform = 'none';
                        clonedWrap.style.removeProperty('--mh-scale');
                    }
                }
            }).then(canvas => {
                this.restoreStyles(originalStyles);
                const resizedCanvas = this.resizeToWidth(canvas, 2560);
                this.download(resizedCanvas);
                btn.disabled  = false;
                btn.innerHTML = originalBtnHtml;
            }).catch(error => {
                console.error('Screenshot failed:', error);
                this.restoreStyles(originalStyles);
                btn.disabled  = false;
                btn.innerHTML = originalBtnHtml;
            });
        },

        resizeToWidth(canvas, targetWidth) {
            const aspectRatio = canvas.height / canvas.width;
            const targetHeight = Math.round(targetWidth * aspectRatio);

            const resized = document.createElement('canvas');
            resized.width = targetWidth;
            resized.height = targetHeight;
            const ctx = resized.getContext('2d');

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, targetWidth, targetHeight);
            ctx.drawImage(canvas, 0, 0, targetWidth, targetHeight);

            return resized;
        },

        overrideStyles() {
            const STYLE_PROPS = [
                ['background',       'background',       '#ffffff'],
                ['background-color', 'backgroundColor',  '#ffffff'],
                ['color',            'color',             '#000000'],
                ['border-color',     'borderColor',       '#e0e0e0'],
                ['box-shadow',       'boxShadow',         'none']
            ];

            const originalStyles = [];
            document.querySelectorAll('.lhq-wrap, .lhq-wrap *').forEach(el => {
                const computed = window.getComputedStyle(el);
                const saved = { element: el };
                STYLE_PROPS.forEach(([computed_prop, style_prop, fallback]) => {
                    saved[style_prop] = el.style[style_prop];
                    if (this.isProblematic(computed.getPropertyValue(computed_prop)))
                        el.style[style_prop] = fallback;
                });
                originalStyles.push(saved);
            });
            return originalStyles;
        },

        restoreStyles(styles) {
            const STYLE_KEYS = ['background', 'backgroundColor', 'color', 'borderColor', 'boxShadow'];
            styles.forEach(saved => {
                STYLE_KEYS.forEach(k => { saved.element.style[k] = saved[k]; });
            });
        },

        isProblematic: value => value.includes('color-mix') || value.includes('oklch'),

        download(canvas) {
            canvas.toBlob(blob => {
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                const domain = window.location.hostname.replace(/^www\./, '');
                const mode = $('#ich-wrap').data('method') || 'luchao';
                const methodText = mode.startsWith('maihoa') ? 'maihoa' : 'luc-hao';
                a.href     = url;
                a.download = domain + '_que-kinh-dich_' + methodText + '.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 'image/png');
        }
    };

    const LandingComponents = {
        init() {
            this.initTabs();
            this.initHexagrams();
            this.initFAQ();
        },

        initTabs() {
            const $tabNav = $('#ich-tab-nav');
            if (!$tabNav.length) return;
            $tabNav.on('click', e => {
                const $btn = $(e.target).closest('.ich-tab-btn');
                if (!$btn.length) return;
                const target = $btn.data('tab');
                $('.ich-tab-btn').removeClass('active');
                $('.ich-tab-panel').removeClass('active');
                $btn.addClass('active');
                $(`#tab-${target}`).addClass('active');
            });
        },

        initHexagrams() {
            $(document).on('click', '.ich-hex-row', function () {
                const $row = $(this), isOpen = $row.hasClass('open');
                $('.ich-hex-row.open').not($row).removeClass('open');
                $row.toggleClass('open', !isOpen);
            });
        },

        initFAQ() {
            $(document).on('click', '.ich-faq-q', function () {
                const $item = $(this).closest('.ich-faq-item'), isOpen = $item.hasClass('open');
                $('.ich-faq-item.open').not($item).removeClass('open');
                $item.toggleClass('open', !isOpen);
            });
        }
    };

    const Events = {
        init() {
            this.bindNameInput();
            this.bindChips();
            this.bindCommentToggle();
            this.bindDetailTabs();
            this.bindSubmitForm();
            this.bindToss();
            this.bindDeepAnalyze();
            this.bindBackBtn();
            this.bindSaveCapture();
        },

        bindNameInput() {
            $(document).on('keydown', '#ich-name', function (e) {
                const allowedKeys = [8, 9, 13, 27, 32, 37, 38, 39, 40, 45, 46];
                if (allowedKeys.includes(e.keyCode) || e.ctrlKey || e.metaKey) return true;
                if (/^[a-zA-ZÀ-ỹ\s]$/.test(String.fromCharCode(e.keyCode || e.which))) return true;
                e.preventDefault();
                return false;
            });

            $(document).on('input', '#ich-name', function () {
                const $el = $(this), val = $el.val();
                if (val.length > 50) $el.val(val.substring(0, 50));
            });

            $(document).on('blur', '#ich-name', function () {
                const val = $(this).val();
                if (!val) return;
                let cleaned = val.replace(/[^a-zA-ZÀ-ỹ\s-]/g, '').replace(/\s+/g, ' ').trim();
                if (cleaned.length > 50) cleaned = cleaned.substring(0, 50);
                $(this).val(Utils.capitalizeName(cleaned));
            });
        },

        bindChips() {
            $(document).on('click', '.ich-chip', function () {
                $('#ich-question').val($(this).data('q')).trigger('input');
            });
        },

        bindCommentToggle() {
            $(document).on('click', '#ich-btn-comment', function () {
                const $comments = $('#comments');
                if (!$comments.length) return;
                const isVisible = $comments.is(':visible');
                if (isVisible) {
                    $comments.slideUp(300);
                    $(this).removeClass('active');
                } else {
                    $comments.slideDown(400, () => scrollTo('#comments', 20));
                    $(this).addClass('active');
                }
            });
        },

        bindDetailTabs() {
            $(document).on('click', '.ich-detail-tab', function () {
                const $tab   = $(this);
                const target = $tab.data('tab');
                const $wrap  = $tab.closest('#ich-detail-content');
                $wrap.find('.ich-detail-tab').removeClass('active');
                $tab.addClass('active');
                $wrap.find('.ich-detail-panel').removeClass('active')
                    .filter(`[data-panel="${target}"]`).addClass('active');
            });
        },

        bindSubmitForm() {
            $(document).on('click', '#ich-btn-submit-form', function () {
                State.topic    = $('#ich-topic').val() || 'general';
                State.question = $('#ich-question').val().trim();
                const mode     = $('#ich-wrap').data('method') || 'luchao';

                if (!Validator.question(State.question, 'ich-err-question')) return;

                State.payload = {
                    question: State.question,
                    mode,
                    topic:   State.topic,
                    hp_trap: $('#ich-hp-trap').val()
                };

                if (mode === 'maihoa_number') {
                    const num = $('#ich-number-input').val().trim();
                    if (!Validator.maihoa_number(num, 'ich-err-number')) return;
                    State.payload.number = num;
                } else if (mode === 'maihoa_object') {
                    const v1 = $('#ich-object-1').val().trim();
                    const v2 = $('#ich-object-2').val().trim();
                    if (!Validator.maihoa_object(v1, 'ich-err-object', v2)) return;
                    State.payload.obj1 = v1;
                    State.payload.obj2 = v2;
                } else if (mode === 'maihoa_time') {
                    const time = $('#ich-time-input').val().trim();
                    if (!Validator.maihoa_time(time, 'ich-err-time')) return;
                    State.payload.time = time;
                }

                AppLogic.startDraw();
            });
        },

        bindToss() {
            $(document).on('click', '#ich-btn-toss', () => Coins.toss());
        },

        bindDeepAnalyze() {
            $(document).on('click', '#ich-btn-deep-analyze', async function () {
                if (!Auth.require()) return;

                const mode = $('#ich-wrap').data('method') || 'luchao';

                if (mode.startsWith('maihoa')) {
                    State.name   = 'Bạn';
                    State.gender = '';
                } else {
                    State.name   = Utils.capitalizeName($('#ich-name').val() || '');
                    State.gender = $('#ich-gender').val();
                    if (!Validator.name(State.name, 'ich-err-info')) return;
                    if (!Validator.gender(State.gender, 'ich-err-info')) return;
                }

                const $btn        = $(this).addClass('loading').prop('disabled', true);
                const $btnLoading = $btn.find('.ich-btn-loading');
                $('#ast-analysis-wrap').slideDown(400);

                const loadingTexts = mode === 'luchao' ? [
                    'Đang kết nối ...', 'Khởi tạo dữ liệu...', 'Phân tích Dụng Thần...',
                    'Phân tích Phục Thần...', 'Phân tích Ngũ Hành...', 'Phân tích Nguyệt Lệnh...',
                    'Phân tích Nhật Kiến...', 'Phân tích Trạng Thái...', 'Phân tích Hào Động...',
                    'Phân tích Thế - Ứng...', 'Đối chiếu dữ kiện...', 'Tổng hợp kết quả...',
                    'Luận giải quẻ...', 'Kiểm tra độ chính xác...', 'Hoàn tất xử lý...', 'Vui lòng chờ...'
                ] : [
                    'Đang kết nối ...', 'Khởi tạo dữ liệu...', 'Phân tích quẻ Thể...',
                    'Phân tích quẻ Dụng...', 'Phân tích Ngũ Hành...', 'Phân tích Sinh Khắc...',
                    'Đối chiếu dữ kiện...', 'Tổng hợp kết quả...', 'Luận giải quẻ...',
                    'Kiểm tra độ chính xác...', 'Hoàn tất xử lý...', 'Vui lòng chờ...'
                ];

                const spinnerHtml = txt => `<span class="ich-spinner"></span> ${txt}`;
                let loadingStep = 0;
                $btnLoading.html(spinnerHtml(loadingTexts[0]));
                const loadingInterval = setInterval(() => {
                    if (loadingStep < loadingTexts.length - 1) {
                        loadingStep++;
                        $btnLoading.html(spinnerHtml(loadingTexts[loadingStep]));
                    }
                }, 1500);

                State.abortController = new AbortController();
                const timeoutId  = setTimeout(() => State.abortController.abort(), 180000);

                const cleanup = () => {
                    clearInterval(loadingInterval);
                    clearTimeout(timeoutId);
                    $btnLoading.html(spinnerHtml('Đang luận giải...'));
                    $btn.removeClass('loading').prop('disabled', false);
                };

                const showError = (msg) => {
                    $('#ich-err-analyze').text(msg);
                    $('#ast-final-result').html('').hide();
                    scrollTo('#ich-deep-analysis-form', 50);
                };

                try {
                    const res = await postJson('analyze', {
                        name: State.name, gender: State.gender, mode,
                        topic: State.topic, question: State.question, lite: State.lite
                    }, State.abortController.signal);

                    if (!res.ok) {
                        cleanup();
                        showError('Máy chủ lỗi. Vui lòng thử lại sau.');
                        return;
                    }

                    const data = await res.json();
                    cleanup();

                    if (data.success) {
                        AIResult.tryInject(data.data);
                        $('#ich-deep-analysis-form').slideUp();
                    } else {
                        showError(data.message);
                    }
                } catch (err) {
                    cleanup();
                    showError(err.name === 'AbortError'
                        ? 'Vui lòng thử lại sau ít phút.'
                        : 'Mất kết nối. Vui lòng kiểm tra internet và thử lại.');
                }
            });
        },

        bindBackBtn() {
            $(document).on('click', '.ich-back-btn', function () {
                if (State.abortController) {
                    State.abortController.abort();
                    State.abortController = null;
                }

                const target = $(this).data('back');

                if (target === 'input') {
                    Object.assign(State, { lite: null, tossCount: 0, resultHtml: '' });

                    $('.ich-line-draw').empty();
                    $('.ich-coin').removeClass('heads tails spinning');
                    $('#ich-toss-num').text(1);
                    $('#ich-toss-action').show();
                    $('#ich-toss-result-hint').empty();
                    $('#ich-btn-toss').removeClass('loading').prop('disabled', false)
                        .find('.ich-btn-text').text('Tung Lần 1');
                    $('#ich-result-box').empty().hide();
                }

                Steps.show(target);
            });
        },

        bindSaveCapture() {
            $(document).on('click', '.lhq-btn-save', function (e) {
                e.preventDefault();
                const $lapQueTab = $('[data-tab="lapque"]');
                if ($lapQueTab.length && !$lapQueTab.hasClass('active')) {
                    $lapQueTab.trigger('click');
                    setTimeout(() => LhqCapture.save(), 350);
                    return;
                }
                LhqCapture.save();
            });
        }
    };

    const App = {
        init() {
            LandingComponents.init();
            Events.init();
        }
    };

    App.init();
});