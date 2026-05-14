jQuery(function ($) {

    const COLOR_MAP = {lp1: '#8b5cf6', lp2: '#10b981', match: '#ef4444', rel: '#e11d48'};
    const REST = ThsLove.rest_url;

    const $form = $('#numm-form');
    const $resultBox = $('#numm-result');
    const $submitBtn = $('#numm-submit-btn');
    const analyzWrap = '#numm-analyze-btn-wrap';


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

    const Spinner = (() => {
        const timers = {};

        const start = ($btn, $label, messages, key) => {
            let i = 0;
            $btn.addClass('fortune-loading').attr('disabled', true);
            $label.html('<span class="ftn-spinner"></span> ' + messages[0]);
            timers[key] = setInterval(() => {
                if (++i < messages.length) $label.html('<span class="ftn-spinner"></span> ' + messages[i]);
                else clearInterval(timers[key]);
            }, 800);
        };

        const stop = ($btn, $label, defaultText, key) => {
            $btn.removeClass('fortune-loading').removeAttr('disabled');
            clearInterval(timers[key]);
            $label.html('<span class="ftn-spinner"></span> ' + defaultText);
        };

        return {
            setSubmit(on) {
                const $l = $submitBtn.find('.ftn-btn-loading');
                on ? start($submitBtn, $l, [
                    'Calculating...', 'Calculating Life Path...',
                        'Calculating Soul Urge...', 'Calculating Attitude...',
                        'Compiling...', 'Displaying result...'
                    ], 'submit') : stop($submitBtn, $l, 'Calculating...', 'submit');
            },
            setAnalyze(on) {
                const $btn = $('#numm-analyze-btn');
                const $l = $btn.find('.ftn-analyze-loading');
                on ? start($btn, $l, [
                    'Analyzing indexes...', 'Analyzing energy...',
                        'Analyzing resonance...', 'Analyzing differences...',
                        'Compiling...', 'Displaying result...'
                    ], 'analyze') : stop($btn, $l, 'Analyzing...', 'analyze');
            },
        };
    })();

    const Validator = {
        name(inputId, errorId) {
            const $i = $('#' + inputId), $e = $('#' + errorId), v = $i.val().trim();
            $i.removeClass('is-error');
            $e.text('');
            if (!v) {
                $e.text('Please enter a name.');
                $i.addClass('is-error');
                return false;
            }
            return v;
        },
        dob(inputId, errorId) {
            const $i = $('#' + inputId), $e = $('#' + errorId);
            let v = $i.val().trim();
            $i.removeClass('is-error');
            $e.text('');
            if (!v) {
                $e.text('Please enter date of birth.');
                $i.addClass('is-error');
                return false;
            }
            
            v = v.replace(/[\/\.\s]+/g, '-');
            
            const m = v.match(/^(19\d{2}|20\d{2})-(\d{1,2})-(\d{1,2})$/);
            if (!m) {
                $e.text('Invalid date (e.g. 1999-12-15).');
                $i.addClass('is-error');
                return false;
            }
            const y = parseInt(m[1], 10), mo = parseInt(m[2], 10), d = parseInt(m[3], 10);
            if (mo < 1 || mo > 12 || d < 1 || d > 31) {
                $e.text('Month or day does not exist.');
                $i.addClass('is-error');
                return false;
            }
            const testDate = new Date(y, mo - 1, d);
            if (testDate.getFullYear() !== y || testDate.getMonth() !== mo - 1 || testDate.getDate() !== d) {
                $e.text('Date ' + y + '-' + mo + '-' + d + ' does not exist.');
                $i.addClass('is-error');
                return false;
            }
            const formatted = y + '-' + String(mo).padStart(2, '0') + '-' + String(d).padStart(2, '0');
            $i.val(formatted);
            return formatted;
        },
    };

    const normalizeName = v =>
        (v || '').replace(/\s+/g, ' ').trim()
            .toLowerCase().split(' ').filter(Boolean)
            .map(w => w.split('-').map(p => p ? p.charAt(0).toUpperCase() + p.slice(1).toLowerCase() : '').join('-'))
            .join(' ');

    const validateAll = () => !!(
        Validator.name('numm-name1', 'numm-error-name1') &&
        Validator.dob('numm-dob1', 'numm-error-dob1') &&
        Validator.name('numm-name2', 'numm-error-name2') &&
        Validator.dob('numm-dob2', 'numm-error-dob2')
    );

    const Typewriter = {
        fastMode: false,

        typeText($el, text, speed, cb) {
            let i = 0, html = '', inTag = false;
            const $span = $('<span></span>');
            $el.append($span);
            $('#numm-chat-body .ftn-cursor').removeClass('ftn-cursor-hidden').appendTo($el);

            const tick = () => {
                if (i >= text.length) {
                    $('#numm-chat-body .ftn-cursor').addClass('ftn-cursor-hidden');
                    cb && cb();
                    return;
                }
                const c = text[i++];
                html += c;
                if (c === '<') inTag = true;
                if (c === '>') {
                    inTag = false;
                    tick();
                    return;
                }
                if (inTag) {
                    tick();
                    return;
                }
                $span.html(html);
                setTimeout(tick, Typewriter.fastMode ? 5 : speed + Math.random() * 15);
            };
            tick();
        },

        run(lines, onDone) {
            Typewriter.fastMode = false;
            let idx = 0;
            const $body = $('#numm-chat-body');
            const cssMap = {
                greeting: 'ftn-tw-greeting',
                intro: 'ftn-tw-intro',
                closing: 'ftn-tw-closing',
                block: 'ftn-tw-block'
            };

            const next = () => {
                if (idx >= lines.length) {
                    onDone && onDone();
                    return;
                }
                const line = lines[idx++];
                const fast = Typewriter.fastMode;

                if (line.type === 'divider') {
                    $body.append('<div class="ftn-tw-divider"></div>');
                    setTimeout(next, fast ? 5 : 40);
                    return;
                }
                if (line.type === 'block') {
                    $('<div class="ftn-tw-block" style="opacity:0;transform:translateY(5px)"></div>')
                        .appendTo($body).html(line.text).animate({opacity: 1}, 400);
                    setTimeout(next, fast ? 5 : 300);
                    return;
                }
                if (line.type === 'index') {
                    const $el = $('<div class="ftn-tw-index" data-index-key="' + line.key + '"></div>').appendTo($body);
                    const color = COLOR_MAP[line.key] || '#f59e0b';
                    this.typeText($el, line.label + ': ', 20, () => {
                        $el.append('<span class="ftn-tw-num" style="color:' + color + '">' + line.value + '</span>');
                        if (line.hint) $el.append('<span class="ftn-tw-hint ftn-tw-hint-text" style="display:inline-block"> — <strong>' + line.hint + '</strong></span>');
                        setTimeout(next, fast ? 5 : 30);
                    });
                    return;
                }
                const $el = $('<div class="' + (cssMap[line.type] || 'ftn-tw-text') + '"></div>').appendTo($body);
                this.typeText($el, line.text, 2, () => setTimeout(next, fast ? 60 : 60));
            };
            next();
        },
    };

    const AIResult = {
        inject(data) {
            $('.ftn-tw-closing, .ftn-cursor').fadeOut(300);
            const $ai = $('<div>').html($.trim(data.tabs_html)).find('#numm-ai-content');
            if ($ai.length) {
                $(analyzWrap).hide();
                $('#numm-ai-content').html($ai.html());
            }
        },
    };

    let baseData = null, calcData = null;

    const onNameBlur = function () {
        const id = $(this).attr('id');
        Validator.name(id, id.replace('numm-', 'numm-error-'));
        const v = $(this).val();
        if (v) $(this).val(normalizeName(v.replace(/[^a-zA-Z\s]/g, '').replace(/\s+/g, ' ').trim()));
    };

    const onDobBlur = function () {
        const id = $(this).attr('id');
        Validator.dob(id, id.replace('numm-', 'numm-error-'));
    };

    const onFieldEnter = function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        $(this).trigger('blur');
        const id = $(this).attr('id');
        const ok = id.includes('name') ? Validator.name(id, id.replace('numm-', 'numm-error-'))
            : Validator.dob(id, id.replace('numm-', 'numm-error-'));
        if (ok && validateAll()) $form.trigger('submit');
    };

    const onSubmit = function (e) {
        e.preventDefault();
        const name1 = Validator.name('numm-name1', 'numm-error-name1');
        const dob1 = Validator.dob('numm-dob1', 'numm-error-dob1');
        const name2 = Validator.name('numm-name2', 'numm-error-name2');
        const dob2 = Validator.dob('numm-dob2', 'numm-error-dob2');
        if (!name1 || !dob1 || !name2 || !dob2) return;

        Spinner.setSubmit(true);
        $resultBox.hide().empty();
        baseData = {name1, dob1, name2, dob2};

        $.ajax({
            url: REST + '/calculate', type: 'POST',
            data: JSON.stringify(baseData), contentType: 'application/json',
            success(res) {
                if (!res.success) {
                    $('#numm-error-name1').text(res.message || 'An error occurred.');
                    Spinner.setSubmit(false);
                    return;
                }
                $resultBox.html(res.html).fadeIn(300);
                $('html,body').animate({scrollTop: $resultBox.offset().top - 80}, 500);

                Typewriter.run(JSON.parse($('#numm-chat-body').attr('data-lines')), () => {
                    $('.ftn-tw-closing, .ftn-cursor').fadeOut(300);
                    $('.comments-area, #comments').fadeIn(400);

                    const hasFatalError = res.calc_data && res.calc_data.blocks && res.calc_data.blocks.some(b =>
                        ['future', 'infant', 'under14', 'over90', 'same_name'].includes(b.type)
                    );

                    if (!hasFatalError) $(analyzWrap).fadeIn(400);

                    calcData = res.calc_data;
                    Spinner.setSubmit(false);
                });
            },
            error() {
                $('#numm-error-name1').text('Connection error.');
                Spinner.setSubmit(false);
            },
        });
    };

    const onAnalyzeClick = function () {
        if (!Auth.require() || !baseData) return;

        Spinner.setAnalyze(true);
        $(analyzWrap).find('.ftn-err-analyze').text('');

        const onError = msg => {
            Spinner.setAnalyze(false);
            $(analyzWrap).find('.ftn-err-analyze').text(msg);
            $('html,body').animate({scrollTop: $(analyzWrap).offset().top - 50}, 400);
        };

        $.ajax({
            url: REST + '/analyze', type: 'POST',
            data: JSON.stringify(baseData), contentType: 'application/json',
            success(res) {
                res.success ? AIResult.inject(res) : onError(res.message || 'Unknown error. Please try again.');
            },
            error() {
                onError('Connection error. Please try again later.');
            },
        });
    };

    const onTabClick = function (e) {
        e.stopPropagation();
        const id = $(this).data('tab');
        const $wrap = $(this).closest('.ftn-analysis-wrap');
        $wrap.find('.ftn-tab').removeClass('active');
        $(this).addClass('active');
        $wrap.find('.ftn-tab-pane').removeClass('active');
        $('#numm-tab-' + id).addClass('active');
    };

    $('#numm-name1, #numm-name2').on('blur', onNameBlur);
    $('#numm-dob1,  #numm-dob2').on('blur', onDobBlur);
    $('#numm-name1, #numm-name2, #numm-dob1, #numm-dob2').on('keydown', onFieldEnter);
    $form.on('submit', onSubmit);
    $resultBox.on('click', '#numm-analyze-btn', onAnalyzeClick);
    $resultBox.on('click', '.ftn-tab', onTabClick);

});