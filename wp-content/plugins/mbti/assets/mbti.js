jQuery(function ($) {
    'use strict';

    let currentStep  = 1;
    let storedAnswers = {};
    const totalSteps = $('.mbti-step').length;

    const UI = {
        $form:        $('#mbti-form'),
        $result:      $('#mbti-result'),
        $progressBar: $('#mbti-progress-bar'),
        $errorMsg:    $('#mbti-error-msg'),
        $nextBtn:     $('#mbti-next'),
        $prevBtn:     $('#mbti-prev'),
        $submitBtn:   $('#mbti-submit'),
    };

    const normalizeName = v =>
        (v || '').replace(/\s+/g, ' ').trim()
            .toLocaleLowerCase('vi-VN').split(' ').filter(Boolean)
            .map(w => w.split('-').map(p => p ? p.charAt(0).toLocaleUpperCase('vi-VN') + p.slice(1).toLocaleLowerCase('vi-VN') : '').join('-'))
            .join(' ');

    const Validator = {
        name(value) {
            const val = value.trim();
            if (!val)           return { valid: false, msg: 'Vui lòng nhập họ và tên.' };
            if (/\d/.test(val)) return { valid: false, msg: 'Họ tên không được chứa số.' };
            return { valid: true, value: normalizeName(val) };
        },

        dob(value) {
            const val = value.trim();
            if (!val) return { valid: false, msg: 'Vui lòng nhập ngày sinh.' };

            const match = val.match(/^(\d{1,2})[\/\-\.\s](\d{1,2})[\/\-\.\s](19\d{2}|20\d{2})$/);
            if (!match) return { valid: false, msg: 'Ngày sinh sai định dạng (VD: 15/12/1999).' };

            const day   = parseInt(match[1], 10);
            const month = parseInt(match[2], 10);
            const year  = parseInt(match[3], 10);

            if (month < 1 || month > 12) return { valid: false, msg: 'Tháng phải từ 1 đến 12.' };
            if (day < 1 || day > 31) return { valid: false, msg: 'Ngày không hợp lệ.' };

            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || testDate.getMonth() !== month - 1 || testDate.getDate() !== day) {
                return { valid: false, msg: `Ngày ${day}/${month} không tồn tại.` };
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (testDate > today) {
                return { valid: false, msg: 'Vui lòng nhập ngày sinh thực tế của bạn.' };
            }

            return { valid: true, value: val };
        },
    };

    const AIResult = {
        renderHTML($container, htmlContent, callback) {
            $container.html(htmlContent);
            if (callback) callback();
        },
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

    function showError(msg) {
        UI.$errorMsg.text(msg).show();
    }

    function clearError() {
        UI.$errorMsg.text('').hide();
    }

    function setSubmitLoading($btn, loading) {
        $btn.find('.ftn-btn-text').toggle(!loading);
        $btn.find('.ftn-btn-loading').toggle(loading);
        $btn.prop('disabled', loading);
    }

    function allAnsweredInStep(step) {
        let answered = true;
        $(`.mbti-step[data-step="${step}"] .mbti-radios`).each(function () {
            if ($(this).find('input[type="radio"]:checked').length === 0) {
                answered = false;
                return false;
            }
        });
        return answered;
    }

    function updateUI() {
        $('.mbti-step').hide();
        $(`.mbti-step[data-step="${currentStep}"]`).fadeIn(300);

        const percent = Math.round((currentStep / totalSteps) * 100);
        UI.$progressBar.css('width', percent + '%').text(percent + '%');

        UI.$prevBtn.toggle(currentStep > 1);
        UI.$nextBtn.toggle(currentStep < totalSteps);
        UI.$submitBtn.toggle(currentStep === totalSteps);

        clearError();
    }

    function scrollToForm() {
        $('html,body').animate({ scrollTop: UI.$form.offset().top - 50 }, 300);
    }

    UI.$nextBtn.on('click', function () {
        if (!allAnsweredInStep(currentStep)) {
            showError('Vui lòng trả lời tất cả các câu hỏi để tiếp tục.');
            return;
        }
        currentStep++;
        updateUI();
        scrollToForm();
    });

    UI.$prevBtn.on('click', function () {
        currentStep--;
        updateUI();
        scrollToForm();
    });

    UI.$form.on('submit', function (e) {
        e.preventDefault();

        if (!allAnsweredInStep(totalSteps)) {
            showError('Vui lòng hoàn thành các câu hỏi cuối cùng.');
            return;
        }

        storedAnswers = {};
        $('input[type="radio"]:checked').each(function () {
            storedAnswers[$(this).attr('name')] = $(this).val();
        });

        setSubmitLoading(UI.$submitBtn, true);
        clearError();

        fetch(MbtiRest.rest_url + '/calculate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ answers: storedAnswers }),
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                UI.$form.slideUp(300);
                UI.$result.html(res.html).fadeIn(400);
                $('.ast-action-footer').fadeIn(400);
                $('html,body').animate({ scrollTop: UI.$result.offset().top - 30 }, 500);
            } else {
                showError(res.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                setSubmitLoading(UI.$submitBtn, false);
            }
        })
        .catch(() => {
            showError('Lỗi kết nối. Vui lòng thử lại.');
            setSubmitLoading(UI.$submitBtn, false);
        });
    });

    $(document).on('submit', '#mbti-ai-form', function (e) {
        e.preventDefault();

        const $form   = $(this);
        const $aiBtn  = $('#ai-submit-btn');
        if (!Auth.require()) return;

        const $aiErr  = $('#ai-error-msg');

        const vName = Validator.name($('#ai-name').val());
        const vDob  = Validator.dob($('#ai-dob').val());

        if (!vName.valid) { $aiErr.text(vName.msg).show(); return; }
        if (!vDob.valid)  { $aiErr.text(vDob.msg).show();  return; }

        setSubmitLoading($aiBtn, true);
        $aiErr.hide();

        fetch(MbtiRest.rest_url + '/analyze', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                full_name: vName.value,
                dob: vDob.value,
                answers: storedAnswers,
            }),
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                $form.slideUp(300);
                $('.ftn-upsell-box p').hide();

                const $finalResult = $('#ai-final-result');
                $finalResult.slideDown(300);

                AIResult.renderHTML($finalResult, res.html);
            } else {
                $aiErr.text(res.message || 'Có lỗi xảy ra.').show();
                setSubmitLoading($aiBtn, false);
            }
        })
        .catch(() => {
            $aiErr.text('Lỗi kết nối. Vui lòng thử lại.').show();
            setSubmitLoading($aiBtn, false);
        });
    });

    $(document).on('blur', '#ai-name', function () {
        const $i = $(this);
        const v = $i.val();
        if (v) {
            const cleaned = v.replace(/[^a-zA-ZÀ-ỹ\s]/g, '').replace(/\s+/g, ' ').trim();
            $i.val(normalizeName(cleaned));
        }
    });

    $(document).on('click', '.ftn-tab', function () {
        const $tab    = $(this);
        const $box    = $tab.closest('.ftn-result-box');
        const target  = $tab.data('tab');

        $tab.siblings('.ftn-tab').removeClass('active');
        $tab.addClass('active');

        $box.find('.ftn-tab-pane').removeClass('active');
        $box.find('#pane-' + target).addClass('active');
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
