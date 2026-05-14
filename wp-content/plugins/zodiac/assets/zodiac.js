jQuery(function ($) {

    const Color = {
        map: {
            sign_name: '#8b5cf6',
            element: '#10b981',
            planet: '#f97316',
            quality: '#06b6d4',
            polarity: '#64748b',
            decan: '#06b6d4',
            sub_ruler: '#f59e0b',
            decan_vibe: '#0ea5e9',
            compat_best: '#16a34a',
            compat_karmic: '#8b5cf6',
            compat_worst: '#dc2626',
            cusp_name: '#ec4899',
            cusp_blend: '#f43f5e',
            cusp_vibe: '#a855f7',

            sign1: '#3b82f6',
            sign1_planet: '#f59e0b',
            sign1_quality: '#06b6d4',
            sign1_decan: '#7c3aed',
            sign2: '#ec4899',
            sign2_planet: '#f59e0b',
            sign2_quality: '#06b6d4',
            sign2_decan: '#7c3aed',
            pair_aspect: '#8b5cf6',
            pair_element: '#10b981',
            pair_quality: '#0ea5e9',
            pair_polarity: '#d946ef',
            pair_planet: '#f59e0b',
            pair_best: '#16a34a',
            pair_karmic: '#f59e0b',
            pair_worst: '#ef4444',
            match: '#ef4444'
        },
        forValue(key) {
            return this.map[key] || '#8b5cf6';
        }
    };

    const Validator = {
        normalizeName(value) {
            const cleaned = (value || '').replace(/[^a-zA-ZÀ-ỹ\s]/g, '').replace(/\s+/g, ' ').trim();
            if (!cleaned) return '';
            return cleaned.toLocaleLowerCase('vi-VN').split(' ').filter(Boolean).map((word) => word.split('-').map((part) => part ? (part.charAt(0).toLocaleUpperCase('vi-VN') + part.slice(1)) : '').join('-')).join(' ');
        },
        name(inputId, errorId) {
            const $input = $('#' + inputId);
            const $error = $('#' + errorId);
            const value = this.normalizeName($input.val());
            $input.val(value);
            $input.removeClass('is-error');
            $error.text('');
            if (!value) {
                $error.text('Please enter your name.');
                $input.addClass('is-error');
                return false;
            }
            return value;
        },
        dob(inputId, errorId) {
            const $input = $('#' + inputId);
            const $error = $('#' + errorId);
            let value = ($input.val() || '').trim();
            $input.removeClass('is-error');
            $error.text('');
            if (!value) {
                $error.text('Please enter your date of birth.');
                $input.addClass('is-error');
                return false;
            }
            value = value.replace(/[\-\.\s]+/g, '/');
            const match = value.match(/^(\d{1,2})\/(\d{1,2})\/(1[8-9]\d{2}|20\d{2})$/);
            if (!match) {
                $error.text('Please enter a real birth year (e.g. 15/12/1999).');
                $input.addClass('is-error');
                return false;
            }
            const day = parseInt(match[1], 10);
            const month = parseInt(match[2], 10);
            const year = parseInt(match[3], 10);
            if (month < 1 || month > 12) {
                $error.text('Invalid month.');
                $input.addClass('is-error');
                return false;
            }
            if (day < 1 || day > 31) {
                $error.text('Invalid day.');
                $input.addClass('is-error');
                return false;
            }
            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || testDate.getMonth() !== month - 1 || testDate.getDate() !== day) {
                $error.text('Date ' + day + '/' + month + ' does not exist.');
                $input.addClass('is-error');
                return false;
            }
            const formatted = String(day).padStart(2, '0') + '/' + String(month).padStart(2, '0') + '/' + year;
            $input.val(formatted);
            return formatted;
        },
        dobToISO(dob) {
            const parts = dob.split('/');
            if (parts.length !== 3) return dob;
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
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

    const ZodiacTypewriter = {
        fastMode: false,
        speedMultiplier: 0.67,

        typeText($element, text, speed, callback, isInstantHtml) {
            if (isInstantHtml) {
                $element.html(text);
                if (callback) callback();
                return;
            }

            let charIndex = 0;
            let currentHTML = '';
            let isInsideTag = false;
            const $textContainer = $('<span></span>');
            $element.append($textContainer);

            const tick = () => {
                if (charIndex >= text.length) {
                    if (callback) callback();
                    return;
                }
                const char = text[charIndex++];
                currentHTML += char;

                if (char === '<') isInsideTag = true;
                if (char === '>') {
                    isInsideTag = false;
                    tick();
                    return;
                }
                if (isInsideTag) {
                    tick();
                    return;
                }

                $textContainer.html(currentHTML);
                const currentSpeed = (ZodiacTypewriter.fastMode ? 4 : speed) * ZodiacTypewriter.speedMultiplier;
                const randomDelay = ZodiacTypewriter.fastMode ? 0 : (Math.random() * 10) * ZodiacTypewriter.speedMultiplier;
                setTimeout(tick, currentSpeed + randomDelay);
            };
            tick();
        },
        run($container, lines, onDoneCallback, isCached) {
            ZodiacTypewriter.fastMode = false;
            ZodiacTypewriter.speedMultiplier = isCached ? 0.33 : 0.67;

            let $cursor = $container.find('.ftn-cursor');
            if (!$cursor.length) {
                $cursor = $('<span class="ftn-cursor">|</span>');
                $container.append($cursor);
            }
            $cursor.show().removeClass('hidden ftn-cursor-hidden');

            let lineIndex = 0;
            const processNextLine = () => {
                if (lineIndex >= lines.length) {
                    $cursor.addClass('hidden ftn-cursor-hidden').hide();
                    $container.find('.ftn-tw-closing').remove();
                    if (onDoneCallback) onDoneCallback();
                    return;
                }
                const line = lines[lineIndex++];

                if (line.type === 'divider') {
                    $cursor.before('<div class="ftn-tw-divider"></div>');
                    setTimeout(processNextLine, (ZodiacTypewriter.fastMode ? 5 : 60) * ZodiacTypewriter.speedMultiplier);
                    return;
                }

                if (line.type === 'index') {
                    const $el = $('<div class="ftn-tw-index" data-index-key="' + line.key + '"></div>');
                    $cursor.before($el);
                    const full = line.label + ': ';
                    ZodiacTypewriter.typeText($el, full, 5, () => {
                        $el.append('<span class="ftn-tw-num" style="color:' + Color.forValue(line.key) + '">' + String(line.value) + '</span>');
                        if (line.hint) $el.append('<span class="ftn-tw-hint ftn-tw-hint-text"> ' + line.hint + '</span>');
                        setTimeout(processNextLine, (ZodiacTypewriter.fastMode ? 5 : 40) * ZodiacTypewriter.speedMultiplier);
                    }, false);
                    return;
                }

                const cssMap = {
                    greeting: 'ftn-tw-greeting',
                    intro: 'ftn-tw-intro',
                    closing: 'ftn-tw-closing',
                    easter: 'ftn-tw-easter',
                    block: 'ftn-tw-block',
                    section: 'ftn-tw-section'
                };
                const $el = $('<div class="' + (cssMap[line.type] || 'ftn-tw-text') + '"></div>');
                $cursor.before($el);

                const speed = line.type === 'greeting' ? 5 : (line.type === 'section' ? 5 : 22);
                ZodiacTypewriter.typeText($el, line.text, speed, () => {
                    setTimeout(processNextLine, (ZodiacTypewriter.fastMode ? 5 : 70) * ZodiacTypewriter.speedMultiplier);
                }, false);
            };
            processNextLine();
        }
    };

    const AIResult = {
        injectLove(aiData) {
            $('.ftn-tw-closing, #zdc-love-chat-body .ftn-cursor').fadeOut(300);
            const $tempDiv = $('<div>').html($.trim(aiData.tabs_html || ''));
            const $newChatAI = $tempDiv.find('#zdc-love-tab-chat-ai');

            if ($newChatAI.length > 0 && $newChatAI.html().trim()) {
                const $chatPane = $('#zdc-love-tab-chat');
                $chatPane.find('#zdc-love-tab-chat-ai-content').remove();

                const $container = $('<div id="zdc-love-tab-chat-ai-content" style="display:none; padding-top:15px;"></div>');
                $chatPane.find('.ftn-chat-wrap').after($container);

                $container.html($newChatAI.html()).slideDown(400);
            }
        }
    };

    const TabHandler = {
        init() {
            $(document).on('click', '.ftn-analysis-wrap .ftn-tab', function (e) {
                e.stopPropagation();
                const tabId = $(this).data('tab');
                const $wrap = $(this).closest('.ftn-analysis-wrap');
                $wrap.find('.ftn-tab').removeClass('active');
                $(this).addClass('active');
                $wrap.find('.ftn-tab-pane').removeClass('active');

                if ($wrap.attr('id') === 'zdc-love-analysis-wrap') {
                    $('#zdc-love-tab-' + tabId).addClass('active');
                } else {
                    $('#ftn-tab-' + tabId).addClass('active');
                }
            });
        }
    };

    const FormHandler = {
        init() {
            const $form = $('#zdc-form');
            const $resultBox = $('#zdc-result');
            const $submitBtn = $('#zdc-submit-btn');
            let activeSignData = null, activeDob = null;

            if (!$form.length) return;

            $('#zdc-dob').on('blur', function () {
                Validator.dob('zdc-dob', 'zdc-error-dob');
            });

            $form.on('submit', function (e) {
                e.preventDefault();
                const dob = Validator.dob('zdc-dob', 'zdc-error-dob');
                if (!dob) return;

                $submitBtn.addClass('zdc-loading').attr('disabled', true);
                $resultBox.hide().empty();
                $('.zdc-btn-reset').fadeOut(200).prop('disabled', true);
                activeDob = dob;

                $.post(ZodiacAjax.api_url + 'calc', {dob: dob, zdc_cbsp: $('#zdc-cbsp').val() || ''}, function (res) {
                    if (!res.success) {
                        $('#zdc-error-dob').text(res.data.message || 'An error has occurred.');
                        $submitBtn.removeClass('zdc-loading').removeAttr('disabled');
                        return;
                    }
                    activeSignData = res.data.sign_data;
                    activeDob = res.data.dob;

                    $resultBox.html(res.data.html).fadeIn(300);
                    $('html,body').animate({scrollTop: $resultBox.offset().top - 80}, 500);

                    const lines = JSON.parse($('#zdc-chat-body').attr('data-lines') || '[]');

                    ZodiacTypewriter.run($('#zdc-chat-body'), lines, () => {
                        $submitBtn.removeClass('zdc-loading').removeAttr('disabled');

                        $('#zdc-tab-chi-tiet-html').slideDown(400);
                        $('#zdc-action-footer, #zdc-disclaimer').fadeIn(400);
                    }, false);
                }).fail(function () {
                    $('#zdc-error-dob').text('Connection error. Please try again.');
                    $submitBtn.removeClass('zdc-loading').removeAttr('disabled');
                });
            });

            $(document).on('click', '.zdc-btn-reset:not(.zdc-love-btn-reset, .zdc-tuvi-btn-reset)', function () {
                $('#zdc-action-footer').fadeOut(200);
                $resultBox.slideUp(300, function () {
                    $(this).empty();
                });
                $form[0].reset();
                $('html,body').animate({scrollTop: $form.offset().top - 80}, 500);
                activeSignData = null;
                activeDob = null;
            });
        }
    };

    const LoveHandler = {
        init() {
            const $loveForm = $('#zdc-love-form');
            if (!$loveForm.length) return;
            const $wrapper = $('#zdc-tinh-yeu-wrapper'), $result = $('#zdc-love-result'),
                $submit = $('#zdc-love-submit-btn');
            let activeLoveData = null;

            $('#zdc-love-name1, #zdc-love-name2').on('blur', function () {
                Validator.name($(this).attr('id'), $(this).attr('id').replace('zdc-love-', 'zdc-error-love-'));
            });
            $('#zdc-love-dob1, #zdc-love-dob2').on('blur', function () {
                Validator.dob($(this).attr('id'), $(this).attr('id').replace('zdc-love-', 'zdc-error-love-'));
            });

            const loveValidateAll = () => !!(
                Validator.name('zdc-love-name1', 'zdc-error-love-name1') &&
                Validator.dob('zdc-love-dob1', 'zdc-error-love-dob1') &&
                Validator.name('zdc-love-name2', 'zdc-error-love-name2') &&
                Validator.dob('zdc-love-dob2', 'zdc-error-love-dob2')
            );

            $('#zdc-love-name1, #zdc-love-name2, #zdc-love-dob1, #zdc-love-dob2').on('keydown', function (e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                $(this).trigger('blur');
                const id = $(this).attr('id');
                const ok = id.includes('name')
                    ? Validator.name(id, id.replace('zdc-love-', 'zdc-error-love-'))
                    : Validator.dob(id, id.replace('zdc-love-', 'zdc-error-love-'));
                if (ok && loveValidateAll()) $loveForm.trigger('submit');
            });

            $loveForm.on('submit', function (e) {
                e.preventDefault();
                const nameA = Validator.name('zdc-love-name1', 'zdc-error-love-name1');
                const dobA = Validator.dob('zdc-love-dob1', 'zdc-error-love-dob1');
                const nameB = Validator.name('zdc-love-name2', 'zdc-error-love-name2');
                const dobB = Validator.dob('zdc-love-dob2', 'zdc-error-love-dob2');
                if (!nameA || !dobA || !nameB || !dobB) return;

                $submit.addClass('zdc-loading').attr('disabled', true);
                $result.hide().empty();
                $('.zdc-love-btn-reset').fadeOut(150).prop('disabled', true);

                $.post(ZodiacAjax.api_url + 'love', {
                    name_a: nameA, dob_a: Validator.dobToISO(dobA), name_b: nameB, dob_b: Validator.dobToISO(dobB), zdc_cbsp: $('#zdc-love-cbsp').val() || ''
                }, function (res) {
                    if (!res.success) {
                        $('#zdc-error-love-name1').text(res.data?.message || 'An error has occurred.');
                        $submit.removeClass('zdc-loading').removeAttr('disabled');
                        return;
                    }
                    activeLoveData = {nameA, dobA: Validator.dobToISO(dobA), nameB, dobB: Validator.dobToISO(dobB), calcData: res.data.calc_data || null};
                    $wrapper.addClass('is-focused');
                    $result.html(res.data.html).fadeIn(300);
                    $('html,body').animate({scrollTop: $result.offset().top - 80}, 500);

                    const lines = JSON.parse($('#zdc-love-chat-body').attr('data-lines') || '[]');
                    const hasDeep = $('#zdc-love-deep-analysis-form').length > 0;

                    ZodiacTypewriter.run($('#zdc-love-chat-body'), lines, () => {
                        $submit.removeClass('zdc-loading').removeAttr('disabled');
                        if (hasDeep && !(res.data.calc_data?.blocks?.length > 0)) {
                            $('#zdc-love-deep-analysis-form').slideDown(350);
                        }
                        $('#zdc-love-result .zdc-action-footer').fadeIn(350);
                    }, false);
                }).fail(function () {
                    $('#zdc-error-love-name1').text('Connection error. Please try again.');
                    $submit.removeClass('zdc-loading').removeAttr('disabled');
                });
            });

            $result.on('click', '#zdc-love-btn-deep-analyze', function () {
                if (!Auth.require()) return;
                if (!activeLoveData?.calcData) return;
                const $btn = $(this);
                $btn.addClass('zdc-loading').attr('disabled', true);
                $('.zdc-love-btn-reset').fadeOut(150).prop('disabled', true);

                $.post(ZodiacAjax.api_url + 'love-analyze', {
                    name_a: activeLoveData.nameA,
                    dob_a: activeLoveData.dobA,
                    name_b: activeLoveData.nameB,
                    dob_b: activeLoveData.dobB,
                    calc_data: activeLoveData.calcData,
                    zdc_cbsp: $('#zdc-love-cbsp-deep').val() || ''
                }, function (resAI) {
                    if (resAI.success) {
                        $('#zdc-love-deep-analysis-form').slideUp(300);
                        AIResult.injectLove(resAI.data);
                    } else {
                        $('#zdc-love-deep-analysis-form').find('.zdc-err-analyze').text(resAI.data?.message || 'Unknown error.');
                        $('#zdc-love-tab-chat-ai-content').html('').hide();
                        $('html, body').animate({scrollTop: $('#zdc-love-deep-analysis-form').offset().top - 50}, 400);
                    }
                }).fail(function () {
                    $('#zdc-love-deep-analysis-form').find('.zdc-err-analyze').text('Connection error. Please try again.');
                    $('#zdc-love-tab-chat-ai-content').html('').hide();
                    $('html, body').animate({scrollTop: $('#zdc-love-deep-analysis-form').offset().top - 50}, 400);
                }).always(function () {
                    $btn.removeClass('zdc-loading').removeAttr('disabled');
                    $('#zdc-love-result .zdc-action-footer').fadeIn(350);
                });
            });

            $(document).on('click', '.zdc-love-btn-reset', function () {
                $('#zdc-love-result .zdc-action-footer').fadeOut(150);
                $loveForm[0].reset();
                $wrapper.removeClass('is-focused');
                $result.slideUp(250, function () {
                    $(this).empty();
                });
                $('[id^="zdc-error-love-"]').text('');
                $('#zdc-love-deep-analysis-form').find('.zdc-err-analyze').text('');
                activeLoveData = null;
                $('html,body').animate({scrollTop: $loveForm.offset().top - 80}, 450);
            });
        }
    };

    const TuviHandler = {
        init() {
            const $form = $('#zdc-tuvi-form');
            if (!$form.length) return;

            $form.on('submit', function (e) {
                e.preventDefault();
                const sign = $('#zdc-tuvi-sign').val();
                const period = $('#zdc-tuvi-period').val();
                const $btn = $('#zdc-tuvi-submit-btn');
                const $error = $('#zdc-error-tuvi');

                $error.text('');

                if (!sign || !period) {
                    $error.text('Please select a sign and period to connect the energy.');
                    return;
                }

                $btn.addClass('zdc-loading').attr('disabled', true);
                $('#zdc-tuvi-result').hide().empty();
                $('.zdc-tuvi-btn-reset').fadeOut(150);

                $.post(ZodiacAjax.api_url + 'tuvi', {
                    sign,
                    period,
                    avoid_domain: localStorage.getItem(`zdc_tuvi_avoid_${sign}_${period}`) || '',
                    zdc_cbsp: $('#zdc-tuvi-cbsp').val() || ''
                }, function (res) {
                    if (res.success) {
                        $('#zdc-tu-vi-wrapper').addClass('is-focused');
                        $('#zdc-tuvi-result').html(res.data.html).show();
                        $('html,body').animate({scrollTop: $('#zdc-tuvi-result').offset().top - 80}, 500);

                        const lines = JSON.parse($('#zdc-tuvi-chat-body').attr('data-lines') || '[]');

                        ZodiacTypewriter.run($('#zdc-tuvi-chat-body'), lines, () => {
                            $btn.removeClass('zdc-loading').removeAttr('disabled');
                            $('#zdc-tuvi-html').slideDown(600);
                            $('#zdc-tuvi-result .zdc-action-footer, .zdc-disclaimer').fadeIn(600);
                        }, false);
                    } else {
                        $btn.removeClass('zdc-loading').removeAttr('disabled');
                        $error.text(res.data.message || 'Interrupted. Please try again.');
                    }
                }).fail(function () {
                    $btn.removeClass('zdc-loading').removeAttr('disabled');
                    $error.text('Unable to connect. Please come back later.');
                });
            });

            $(document).on('click', '.zdc-tuvi-btn-reset', function () {
                $('#zdc-tuvi-result .zdc-action-footer').fadeOut(150);
                $('#zdc-tu-vi-wrapper').removeClass('is-focused');
                $('#zdc-tuvi-result').slideUp(300, function () {
                    $(this).empty();
                });
                $('#zdc-error-tuvi').text('');
                $('html,body').animate({scrollTop: $('#zdc-tuvi-form').offset().top - 80}, 450);
            });

            $(document).on('click', '.zdc-energy-btn', function() {
                const $btn = $(this);
                const $container = $btn.closest('.zdc-energy-feedback');
                const type = $btn.data('type');
                const $options = $btn.closest('.zdc-feedback-options');
                const primaryDomain = $options.data('primary');
                const sign = $options.data('sign');
                const period = $options.data('period');

                $container.find('.zdc-energy-btn').attr('disabled', true).addClass('zdc-faded');
                $btn.removeClass('zdc-faded').addClass('zdc-resonated');

                if (type === 'low') {
                    localStorage.setItem(`zdc_tuvi_avoid_${sign}_${period}`, primaryDomain);
                } else {
                    localStorage.removeItem(`zdc_tuvi_avoid_${sign}_${period}`);
                }
            });
        },
    };

    const NatalHandler = {
        init() {
            const $form = $('#zdc-natal-form');
            if (!$form.length) return;

            $('#zdc-natal-dob').on('blur', function () {
                Validator.dob('zdc-natal-dob', 'zdc-error-natal-dob');
            });

            const validateTime = (val) => {
                if (!val) return true;
                return /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(val.trim());
            };

            let activeNatal = null;

            $form.on('submit', function (e) {
                e.preventDefault();
                const dob = Validator.dob('zdc-natal-dob', 'zdc-error-natal-dob');
                const tob = $('#zdc-natal-tob').val().trim();
                const pob = $('#zdc-natal-pob').val().trim();
                const cbsp = $('#zdc-natal-cbsp').val() || '';

                let hasError = false;
                if (!dob) hasError = true;
                if (tob && !validateTime(tob)) {
                    $('#zdc-error-natal-tob').text('Invalid time');
                    hasError = true;
                } else {
                    $('#zdc-error-natal-tob').text('');
                }
                if (!pob) {
                    $('#zdc-error-natal-pob').text('Please enter place of birth.');
                    hasError = true;
                } else {
                    $('#zdc-error-natal-pob').text('');
                }
                if (hasError) return;

                const $btn = $('#zdc-natal-submit-btn');
                $btn.addClass('zdc-loading').attr('disabled', true);
                $('#zdc-natal-result').hide().empty();
                $('.zdc-natal-btn-reset').fadeOut(150);

                $.post(ZodiacAjax.api_url + 'natal', {
                    dob: dob, tob: tob, pob: pob, zdc_cbsp: cbsp
                }, function (res) {
                    if (res.success) {
                        activeNatal = {
                            dob: res.data.dob,
                            tob: res.data.tob,
                            pob: res.data.pob,
                            natal_chart: res.data.natal_chart
                        };
                        $('#zdc-ban-do-sao-wrapper').addClass('is-focused');
                        $('#zdc-natal-result').html(res.data.html).show();
                        $('html,body').animate({scrollTop: $('#zdc-natal-result').offset().top - 80}, 500);

                        if (res.data.natal_chart) NatalWheel.setData(res.data.natal_chart);

                        const lines = JSON.parse($('#zdc-natal-chat-body').attr('data-lines') || '[]');
                        ZodiacTypewriter.run($('#zdc-natal-chat-body'), lines, () => {
                            $btn.removeClass('zdc-loading').removeAttr('disabled');
                            $('#zdc-natal-deep-form').fadeIn(300);
                            $('#zdc-natal-result .zdc-action-footer, .zdc-disclaimer').fadeIn(300);
                        }, false);
                    } else {
                        alert(res.data.message || 'An error has occurred.');
                        $btn.removeClass('zdc-loading').removeAttr('disabled');
                    }
                }).fail(function () {
                    $btn.removeClass('zdc-loading').removeAttr('disabled');
                    alert('Server connection error.');
                });
            });

            $(document).on('click', '#zdc-natal-btn-analyze', function (e) {
                e.preventDefault();
                if (!Auth.require()) return;
                if (!activeNatal) return;

                const $btn = $(this);
                $btn.addClass('zdc-loading').attr('disabled', true);
                $('.zdc-natal-btn-reset').fadeOut(150);

                $.post(ZodiacAjax.api_url + 'natal-analyze', {
                    dob: activeNatal.dob,
                    tob: activeNatal.tob,
                    pob: activeNatal.pob,
                    natal_chart: activeNatal.natal_chart,
                    zdc_cbsp: $('#zdc-natal-cbsp-deep').val() || ''
                }, function (resAI) {
                    $btn.removeClass('zdc-loading').removeAttr('disabled');
                    $('.zdc-natal-btn-reset').fadeIn(150);

                    if (resAI.success) {
                        $('#zdc-natal-deep-form').slideUp(300);
                        if (resAI.data.is_cached) {
                            ZodiacTypewriter.fastMode = true;
                        }
                        $('#zdc-natal-html').html(resAI.data.zdc_html).slideDown(400);
                    } else {
                        $('#zdc-natal-deep-form').find('.zdc-err-analyze').text(resAI.data.message || 'Unknown error.');
                        $('#zdc-natal-html').html('').hide();
                        $('html, body').animate({scrollTop: $('#zdc-natal-deep-form').offset().top - 50}, 400);
                    }
                }).fail(function () {
                    $btn.removeClass('zdc-loading').removeAttr('disabled');
                    $('.zdc-natal-btn-reset').fadeIn(150);
                    $('#zdc-natal-deep-form').find('.zdc-err-analyze').text('Connection error. Please try again later.');
                    $('#zdc-natal-html').html('').hide();
                    $('html, body').animate({scrollTop: $('#zdc-natal-deep-form').offset().top - 50}, 400);
                });
            });

            $(document).on('click', '.zdc-natal-btn-reset', function () {
                $('#zdc-natal-result .zdc-action-footer').fadeOut(150);
                $('#zdc-ban-do-sao-wrapper').removeClass('is-focused');
                $('#zdc-natal-result').slideUp(250, function () {
                    $(this).empty();
                });
                $('#zdc-natal-form')[0].reset();
                $('#zdc-natal-deep-form').find('.zdc-err-analyze').text('');
                activeNatal = null;
                $('html,body').animate({scrollTop: $('#zdc-natal-form').offset().top - 80}, 450);
            });
        }
    };

    const LandingHandler = {
        init() {
            $(document).on('click', '.zdc-faq-q', function () {
                const $item = $(this).closest('.zdc-faq-item');
                const $container = $(this).closest('.zdc-faq-list');

                $container.find('.zdc-faq-item')
                    .not($item)
                    .removeClass('open');

                $item.toggleClass('open');

                const isOpen = $item.hasClass('open');
                $(this).attr('aria-expanded', isOpen);
            });
        }
    };

    $(document).on('click', '#zdc-btn-comment', function () {
        const $comments = $('#comments');
        if (!$comments.length) return;
        if ($comments.is(':visible')) {
            $comments.slideUp(300);
            $(this).removeClass('active');
        } else {
            $comments.slideDown(400, function () {
                $('html,body').animate({scrollTop: $comments.offset().top - 20}, 400);
            });
            $(this).addClass('active');
        }
    });

    const PobAutocomplete = {
        cities: [
            'Hà Nội', 'Hồ Chí Minh', 'Hải Phòng', 'Đà Nẵng', 'Cần Thơ', 'Huế',
            'Cao Bằng', 'Lạng Sơn', 'Quảng Ninh', 'Tuyên Quang', 'Lào Cai', 'Lai Châu',
            'Điện Biên', 'Sơn La', 'Thái Nguyên', 'Phú Thọ', 'Bắc Ninh', 'Hưng Yên',
            'Ninh Bình', 'Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị',
            'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa', 'Ninh Thuận',
            'Bình Thuận', 'Gia Lai', 'Kon Tum', 'Đắk Lắk', 'Đắk Nông', 'Lâm Đồng',
            'Bình Phước', 'Bình Dương', 'Đồng Nai', 'Tây Ninh', 'Bà Rịa - Vũng Tàu',
            'Long An', 'Tiền Giang', 'Bến Tre', 'Trà Vinh', 'Vĩnh Long', 'Đồng Tháp',
            'An Giang', 'Kiên Giang', 'Hậu Giang', 'Sóc Trăng', 'Bạc Liêu', 'Cà Mau',
            'Vĩnh Phúc', 'Hòa Bình', 'Hà Nam', 'Nam Định', 'Thái Bình', 'Bắc Giang',
            'Bắc Kạn', 'Yên Bái', 'Hà Giang', 'Nha Trang', 'Đà Lạt', 'Vũng Tàu',
            'Buôn Ma Thuột', 'Quy Nhơn', 'Vinh', 'Pleiku', 'Việt Trì', 'Thái Bình',
        ],

        normalize(str) {
            return str.toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd').replace(/Đ/g, 'D');
        },

        highlight(text, query) {
            const norm = this.normalize(text);
            const nq = this.normalize(query);
            const idx = norm.indexOf(nq);
            if (idx === -1) return $('<span>').text(text);
            return $('<span>')
                .append(document.createTextNode(text.slice(0, idx)))
                .append($('<strong>').text(text.slice(idx, idx + query.length)))
                .append(document.createTextNode(text.slice(idx + query.length)));
        },

        init() {
            const $input = $('#zdc-natal-pob');
            const $dropdown = $('#zdc-pob-dropdown');
            if (!$input.length) return;

            let activeIdx = -1;

            const close = () => {
                $dropdown.removeClass('open').empty();
                activeIdx = -1;
            };

            const open = (query) => {
                const nq = this.normalize(query.trim());
                if (!nq) {
                    close();
                    return;
                }
                const matches = this.cities.filter(c => this.normalize(c).includes(nq)).slice(0, 8);
                if (!matches.length) {
                    close();
                    return;
                }
                $dropdown.empty();
                matches.forEach((city, i) => {
                    $('<div class="zdc-pob-option" role="option">')
                        .attr('data-idx', i)
                        .append(this.highlight(city, query.trim()))
                        .on('mousedown', () => {
                            $input.val(city);
                            close();
                        })
                        .appendTo($dropdown);
                });
                $dropdown.addClass('open');
                activeIdx = -1;
            };

            $input.on('input', e => open(e.target.value));
            $input.on('focus', e => {
                if (e.target.value) open(e.target.value);
            });
            $input.on('blur', () => setTimeout(close, 150));

            $input.on('keydown', e => {
                const $opts = $dropdown.find('.zdc-pob-option');
                if (!$dropdown.hasClass('open') || !$opts.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIdx = Math.min(activeIdx + 1, $opts.length - 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIdx = Math.max(activeIdx - 1, 0);
                } else if (e.key === 'Enter' && activeIdx >= 0) {
                    e.preventDefault();
                    $input.val($opts.eq(activeIdx).text());
                    close();
                } else if (e.key === 'Escape') {
                    close();
                } else {
                    return;
                }
                $opts.removeClass('active').eq(activeIdx).addClass('active');
            });

            $(document).on('click', e => {
                if (!$(e.target).closest('.zdc-pob-wrap').length) close();
            });
        }
    };

    PobAutocomplete.init();

    // ── Natal Chart Wheel ──
    const NatalWheel = {
        elementColors: {'Fire': '#e8543a', 'Earth': '#8B6914', 'Air': '#4a9e6b', 'Water': '#3a7fc1'},
        planetSymbols: {
            'Sun': '☀',
            'Moon': '☽',
            'Mercury': '☿',
            'Venus': '♀',
            'Mars': '♂',
            'Jupiter': '♃',
            'Saturn': '♄',
            'Uranus': '♅',
            'Neptune': '♆',
            'Pluto': '♇'
        },
        signSymbols: ['♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓'],
        signElements: ['Fire', 'Earth', 'Air', 'Water', 'Fire', 'Earth', 'Air', 'Water', 'Fire', 'Earth', 'Air', 'Water'],

        draw(data) {
            const canvas = document.getElementById('zdc-natal-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const W = canvas.width, H = canvas.height;
            const cx = W / 2, cy = H / 2;
            const R = W * 0.46;
            const r1 = R * 0.82;
            const r2 = R * 0.72;
            const r3 = R * 0.58;
            const r4 = R * 0.48;
            ctx.clearRect(0, 0, W, H);

            const ascDeg = data.ascendant.degree;
            const toRad = deg => (deg - ascDeg + 180) * Math.PI / 180;

            for (let i = 0; i < 12; i++) {
                const color = this.elementColors[this.signElements[i]];
                ctx.beginPath();
                ctx.moveTo(cx, cy);
                ctx.arc(cx, cy, R, toRad(i * 30), toRad(i * 30 + 30));
                ctx.closePath();
                ctx.fillStyle = color + '33';
                ctx.fill();
                ctx.strokeStyle = '#555';
                ctx.lineWidth = 0.5;
                ctx.stroke();
                const midRad = toRad(i * 30 + 15);
                const symR = (R + r1) / 2;
                ctx.font = 'bold ' + Math.round(R * 0.07) + 'px serif';
                ctx.fillStyle = color;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(this.signSymbols[i], cx + symR * Math.cos(midRad), cy + symR * Math.sin(midRad));
            }
            ctx.beginPath();
            ctx.arc(cx, cy, r1, 0, 2 * Math.PI);
            ctx.strokeStyle = '#888';
            ctx.lineWidth = 1;
            ctx.stroke();
            ctx.beginPath();
            ctx.arc(cx, cy, r3, 0, 2 * Math.PI);
            ctx.strokeStyle = '#aaa';
            ctx.lineWidth = 0.5;
            ctx.stroke();

            // Houses
            const houses = data.houses;
            const hKeys = Object.keys(houses);
            hKeys.forEach((k, h) => {
                const hd = parseFloat(houses[k].cusp_degree);
                const hr = toRad(hd);
                const isAngle = (h === 0 || h === 3 || h === 6 || h === 9);
                ctx.beginPath();
                ctx.moveTo(cx + r3 * Math.cos(hr), cy + r3 * Math.sin(hr));
                ctx.lineTo(cx + r1 * Math.cos(hr), cy + r1 * Math.sin(hr));
                ctx.strokeStyle = isAngle ? '#c0392b' : '#aaa';
                ctx.lineWidth = isAngle ? 1.5 : 0.6;
                ctx.stroke();
                const nextK = hKeys[(h + 1) % hKeys.length];
                const nextHd = parseFloat(houses[nextK].cusp_degree);
                const midHDeg = hd + ((nextHd > hd ? nextHd - hd : nextHd + 360 - hd) / 2);
                const hnR = (r2 + r3) / 2;
                ctx.font = Math.round(R * 0.055) + 'px sans-serif';
                ctx.fillStyle = '#999';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(k, cx + hnR * Math.cos(toRad(midHDeg)), cy + hnR * Math.sin(toRad(midHDeg)));
            });

            // Aspect lines
            const degMap = {};
            data.planets.forEach(p => {
                degMap[p.name] = p.degree;
            });
            degMap['Ascendant'] = data.ascendant.degree;
            degMap['MC'] = data.midheaven.degree;
            data.aspects.forEach(asp => {
                const d1 = degMap[asp.planet1], d2 = degMap[asp.planet2];
                if (d1 === undefined || d2 === undefined) return;
                ctx.beginPath();
                ctx.moveTo(cx + r4 * Math.cos(toRad(d1)), cy + r4 * Math.sin(toRad(d1)));
                ctx.lineTo(cx + r4 * Math.cos(toRad(d2)), cy + r4 * Math.sin(toRad(d2)));
                ctx.strokeStyle = asp.nature === 'harmonious' ? '#27ae6088' : asp.nature === 'tense' ? '#c0392b88' : '#88888866';
                ctx.lineWidth = 0.8;
                ctx.stroke();
            });

            // Planets
            const pRadius = r3 * 0.82;
            const hitAreas = [];
            data.planets.forEach(p => {
                const pr = toRad(p.degree);
                const px = cx + pRadius * Math.cos(pr);
                const py = cy + pRadius * Math.sin(pr);
                const col = this.elementColors[p.element] || '#888';
                const dotR = R * 0.025;
                ctx.beginPath();
                ctx.arc(px, py, dotR, 0, 2 * Math.PI);
                ctx.fillStyle = col;
                ctx.fill();
                ctx.beginPath();
                ctx.moveTo(cx + r3 * Math.cos(pr), cy + r3 * Math.sin(pr));
                ctx.lineTo(cx + r1 * 0.97 * Math.cos(pr), cy + r1 * 0.97 * Math.sin(pr));
                ctx.strokeStyle = col + 'aa';
                ctx.lineWidth = 0.5;
                ctx.stroke();
                ctx.font = 'bold ' + Math.round(R * 0.065) + 'px serif';
                ctx.fillStyle = col;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(this.planetSymbols[p.name] || p.symbol || '?', px, py - R * 0.045);
                hitAreas.push({
                    x: px,
                    y: py,
                    r: dotR + 8,
                    label: (this.planetSymbols[p.name] || '') + ' ' + p.name + ' — ' + p.sign
                });
            });

            // ASC / MC labels + hit areas
            [['ASC', data.ascendant.degree, '#c0392b', 'Ascendant — ' + data.ascendant.sign],
                ['MC', data.midheaven.degree, '#8e44ad', 'Midheaven — ' + data.midheaven.sign]].forEach(item => {
                const lr = toRad(item[1]);
                const lx = cx + r1 * 1.07 * Math.cos(lr), ly = cy + r1 * 1.07 * Math.sin(lr);
                ctx.font = 'bold ' + Math.round(R * 0.055) + 'px sans-serif';
                ctx.fillStyle = item[2];
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(item[0], lx, ly);
                hitAreas.push({x: lx, y: ly, r: 16, label: item[0] + ': ' + item[3]});
            });

            // Center
            ctx.beginPath();
            ctx.arc(cx, cy, r3 * 0.38, 0, 2 * Math.PI);
            ctx.fillStyle = '#1a1a2e';
            ctx.fill();
            ctx.strokeStyle = '#444';
            ctx.lineWidth = 1;
            ctx.stroke();
            ctx.font = 'bold ' + Math.round(R * 0.07) + 'px serif';
            ctx.fillStyle = '#e0c97f';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('✦', cx, cy);

            canvas._hitAreas = hitAreas;
        },

        initTooltip() {
            const canvas = document.getElementById('zdc-natal-canvas');
            const tooltip = document.getElementById('zdc-wheel-tooltip');
            if (!canvas || !tooltip) return;
            canvas.addEventListener('mousemove', e => {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                const mx = (e.clientX - rect.left) * scaleX;
                const my = (e.clientY - rect.top) * scaleY;
                const hit = (canvas._hitAreas || []).find(a => Math.hypot(mx - a.x, my - a.y) <= a.r);
                if (hit) {
                    tooltip.textContent = hit.label;
                    tooltip.style.display = 'block';
                    tooltip.style.left = (e.clientX - rect.left + 12) + 'px';
                    tooltip.style.top = (e.clientY - rect.top - 30) + 'px';
                    canvas.style.cursor = 'pointer';
                } else {
                    tooltip.style.display = 'none';
                    canvas.style.cursor = 'crosshair';
                }
            });
            canvas.addEventListener('mouseleave', () => {
                tooltip.style.display = 'none';
            });
        },

        setData(rawData) {
            const data = Object.assign({}, rawData);
            if (data.planets && !Array.isArray(data.planets)) data.planets = Object.values(data.planets);
            if (data.special_points && !Array.isArray(data.special_points)) data.special_points = Object.values(data.special_points);
            if (data.aspects && !Array.isArray(data.aspects)) data.aspects = Object.values(data.aspects);
            if (!data.planets) data.planets = [];
            if (!data.aspects) data.aspects = [];
            if (!data.special_points) data.special_points = [];
            if (!data.houses) data.houses = {};
            this._data = data;
            this.initTooltip();
            const pane = document.getElementById('ftn-tab-zdc-natal-wheel');
            if (!pane) return;
            let drawn = false;
            const obs = new MutationObserver(() => {
                if (pane.classList.contains('active') && !drawn) {
                    drawn = true;
                    setTimeout(() => this.draw(this._data), 50);
                }
                if (!pane.classList.contains('active')) drawn = false;
            });
            obs.observe(pane, {attributes: true, attributeFilter: ['class']});
        },

        init() {
        }
    };

    NatalWheel.init();
    TabHandler.init();
    FormHandler.init();
    LoveHandler.init();
    TuviHandler.init();
    NatalHandler.init();
    LandingHandler.init();
});