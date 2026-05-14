jQuery(function ($) {

    const UI = {
        $form:      $('#phone-form'),
        $resultBox: $('#phone-result'),
        $submitBtn: $('#phone-submit'),
        _twTimers:  [],
    };

    // ─── Validator ────────────────────────────────────────────────────────────
    const Validator = {
        check(inputId, errorId, type) {
            const $input = $('#' + inputId);
            const $error = $('#' + errorId);
            const val    = $input.val().trim();
            $input.removeClass('is-error');
            $error.text('');

            if (!val) {
                $error.text('Không được để trống.');
                $input.addClass('is-error');
                return false;
            }

            if (type === 'phone') {
                // Chỉ chấp nhận 10 số, bắt đầu bằng 0
                const clean = val.replace(/[\s\.\-\(\)]/g, '');
                if (!/^0[1-9][0-9]{8}$/.test(clean)) {
                    $error.text('Số điện thoại phải có 10 số và bắt đầu bằng 0 (VD: 0987654321).');
                    $input.addClass('is-error');
                    return false;
                }
                return clean;
            }

            if (type === 'dob' && !/^(\d{1,2})[\/\-\.\s](\d{1,2})[\/\-\.\s](19\d{2}|20\d{2})$/.test(val)) {
                $error.text('Sai định dạng (VD: 15/12/1989).');
                $input.addClass('is-error');
                return false;
            }

            return val;
        },

        clearAll() {
            $('.ftn-input').removeClass('is-error');
            $('.ftn-error').text('');
            $('#phone-error-msg').hide().text('');
        },
    };

    // ─── Typewriter ───────────────────────────────────────────────────────────
    const Typewriter = {
        fastMode: false,

        cancelAll() {
            UI._twTimers.forEach(id => clearTimeout(id));
            UI._twTimers = [];
        },

        _setTimeout(fn, delay) {
            const id = setTimeout(fn, delay);
            UI._twTimers.push(id);
            return id;
        },

        typeText($element, text, speed, callback) {
            let charIndex = 0;
            const tick = () => {
                if (charIndex >= text.length) {
                    if (callback) callback();
                    return;
                }
                $element.append(document.createTextNode(text[charIndex++]));
                const d = Typewriter.fastMode ? 4 : speed + (Math.random() * 15);
                Typewriter._setTimeout(tick, d);
            };
            tick();
        },

        run(lines, onDoneCallback) {
            Typewriter.fastMode = false;
            const $cursor = $('#ftn-chat-body .ftn-cursor');
            let lineIndex = 0;

            const processNextLine = () => {
                if (lineIndex >= lines.length) {
                    $cursor.addClass('ftn-cursor-hidden');
                    if (onDoneCallback) onDoneCallback();
                    return;
                }
                const line = lines[lineIndex++];

                if (line.type === 'divider') {
                    $cursor.before('<div class="ftn-tw-divider"></div>');
                    Typewriter._setTimeout(processNextLine, Typewriter.fastMode ? 10 : 120);
                    return;
                }

                if (line.type === 'index') {
                    const $el    = $('<div class="ftn-tw-index" data-index-key="' + line.key + '"></div>');
                    const $label = $('<span class="ftn-tw-label"></span>');
                    $el.append($label);
                    if (line.hint) $el.append('<span class="ftn-tw-hint ftn-tw-hint-text"> ' + line.hint + '</span>');
                    $cursor.before($el);
                    Typewriter.typeText($label, line.label + ': ', 15, () => {
                        const numClass = line.numClass ? ' ' + line.numClass : '';
                        $el.append('<span class="ftn-tw-num' + numClass + '">' + line.value + '</span>');
                        Typewriter._setTimeout(processNextLine, Typewriter.fastMode ? 10 : 80);
                    });
                    return;
                }

                const cssMap  = { greeting: 'ftn-tw-greeting', intro: 'ftn-tw-intro', closing: 'ftn-tw-closing' };
                const cssClass = cssMap[line.type] || 'ftn-tw-text';
                const $el      = $('<div class="' + cssClass + '"></div>');
                $cursor.before($el);
                const speed = line.type === 'greeting' ? 15 : 22;
                Typewriter.typeText($el, line.text, speed, () => {
                    Typewriter._setTimeout(processNextLine, Typewriter.fastMode ? 10 : 150);
                });
            };

            processNextLine();
        },
    };

    // ─── Submit ───────────────────────────────────────────────────────────────
    UI.$form.on('submit', function (e) {
        e.preventDefault();

        Validator.clearAll();

        const name  = Validator.check('p-name',  'p-error-name',  'text');
        const dob   = Validator.check('p-dob',   'p-error-dob',   'dob');
        const phone = Validator.check('p-phone', 'p-error-phone', 'phone');

        if (!name || !dob || !phone) return;

        // Hủy animation cũ nếu đang chạy
        Typewriter.cancelAll();

        UI.$submitBtn.addClass('fortune-loading').attr('disabled', true);
        UI.$submitBtn.find('.ftn-btn-text').hide();
        UI.$submitBtn.find('.ftn-btn-loading').show();

        UI.$resultBox.hide().empty();

        const unlockButton = () => {
            UI.$submitBtn.removeClass('fortune-loading').removeAttr('disabled');
            UI.$submitBtn.find('.ftn-btn-loading').hide();
            UI.$submitBtn.find('.ftn-btn-text').show();
        };

        $.ajax({
            url:         PhoneRest.rest_url + '/calculate',
            type:        'POST',
            contentType: 'application/json',
            // Gửi phone đã normalize (10 số bắt đầu 0)
            data: JSON.stringify({ full_name: name, dob: dob, phone: phone }),
            success: function (res) {
                if (!res.success) {
                    $('#phone-error-msg').text(res.message || 'Lỗi xử lý.').show();
                    unlockButton();
                    return;
                }

                UI.$resultBox.html(res.html).fadeIn(300);
                $('html,body').animate({ scrollTop: UI.$resultBox.offset().top - 80 }, 500);

                if (res.tabs_html) {
                    const $tmp = $('<div>').html($.trim(res.tabs_html));
                    $('#ftn-tab-phan-tich').html($tmp.find('#static-tab-phan-tich').html() || $tmp.html());
                }

                const $chatBody = $('#ftn-chat-body');
                if ($chatBody.length) {
                    const lines = JSON.parse($chatBody.attr('data-lines'));
                    Typewriter.run(lines, unlockButton);
                } else {
                    unlockButton();
                }
            },
            error: function () {
                $('#phone-error-msg').text('Không thể kết nối để tính toán dữ liệu.').show();
                unlockButton();
            },
        });
    });

    // ─── Tabs ─────────────────────────────────────────────────────────────────
    $('#phone-result').on('click', '.ftn-tab', function () {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $(this).closest('.ftn-analysis-wrap').find('.ftn-tab-pane').removeClass('active');
        $('#ftn-tab-' + $(this).data('tab')).addClass('active');
    });
});
