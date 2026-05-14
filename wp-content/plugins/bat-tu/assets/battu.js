const BatTuApp = (function ($) {
    "use strict";

    const UIError = {
        show($input, message) {
            $input.addClass('is-error');
            let $msg = $input.siblings('.battu-input-err-msg');
            if ($msg.length === 0) {
                $input.after(`<span class="battu-input-err-msg">${message}</span>`);
            } else {
                $msg.text(message);
            }
        },
        clear($input) {
            $input.removeClass('is-error');
            $input.siblings('.battu-input-err-msg').remove();
        },
        scrollToFirst($form) {
            const $firstError = $form.find('.is-error').first();
            if ($firstError.length) {
                $('html, body').animate({ scrollTop: $firstError.offset().top - 120 }, 300);
                $firstError.focus();
                return true;
            }
            return false;
        }
    };

    function scrollToResult($el) {
        $('html, body').animate({ scrollTop: $el.offset().top - 20 }, 400);
    }

    const DateValidator = {
        validate(value) {
            if (!value) return { valid: false, message: 'Vui lòng nhập ngày sinh.' };
            value = value.trim();
            const match = value.match(/^(\d{1,2})[/\-.\s](\d{1,2})[/\-.\s](\d{4})$/);
            if (!match) return { valid: false, message: 'Định dạng: ngày/tháng/năm (VD: 15/8/1990)' };

            const day = parseInt(match[1], 10);
            const month = parseInt(match[2], 10);
            const year = parseInt(match[3], 10);

            if (year < 1900 || year > 2100) return { valid: false, message: 'Hỗ trợ từ 1900 đến 2100.' };
            if (month < 1 || month > 12) return { valid: false, message: 'Tháng phải từ 1 đến 12.' };
            if (day < 1 || day > 31) return { valid: false, message: 'Ngày không hợp lệ.' };

            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || testDate.getMonth() !== month - 1 || testDate.getDate() !== day) {
                return { valid: false, message: `Ngày ${day}/${month}/${year} không tồn tại.` };
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (testDate > today) return { valid: false, message: 'Ngày sinh không được là tương lai.' };

            return { valid: true, value: `${day}/${month}/${year}` };
        },

        bind(selector) {
            $(document).on('input', selector, function() {
                let val = $(this).val().replace(/[^\d/\-.\s]/g, '');
                if (val.length > 10) val = val.substring(0, 10);
                $(this).val(val);
                UIError.clear($(this));
            });

            $(document).on('blur', selector, function() {
                const $input = $(this);
                const val = $input.val();
                if (!val) return;
                const result = DateValidator.validate(val);
                if (!result.valid) UIError.show($input, result.message);
                else { $input.val(result.value); UIError.clear($input); }
            });
        }
    };

    const TimeValidator = {
        normalize(val) {
            val = val.replace(/[^\d:\s]/g, '').trim();
            if (!val.includes(':')) {
                val = val.replace(/\s+/g, '');
                if (val.length === 1)       val = '0' + val + ':00';
                else if (val.length === 2)  val = val + ':00';
                else if (val.length === 3)  val = '0' + val.charAt(0) + ':' + val.substring(1);
                else if (val.length === 4)  val = val.substring(0, 2) + ':' + val.substring(2);
            } else {
                const parts = val.split(':');
                if (parts.length === 2) {
                    const h = parts[0].trim() ? parts[0].trim().padStart(2, '0') : '00';
                    const m = parts[1].trim() ? parts[1].trim().padEnd(2, '0').substring(0, 2) : '00';
                    val = h + ':' + m;
                }
            }
            return val;
        },

        validate(value) {
            if (!value) return { valid: false, message: 'Vui lòng nhập giờ sinh.' };
            const normalized = this.normalize(value);
            if (!/^([01][0-9]|2[0-3]):[0-5][0-9]$/.test(normalized)) {
                return { valid: false, message: 'Sai định dạng 24h (VD: 14:30)' };
            }
            return { valid: true, value: normalized };
        },

        bind(selector) {
            $(document).on('input', selector, function() {
                let val = $(this).val().replace(/[^\d:\s]/g, '');
                if (val.length > 5) val = val.substring(0, 5);
                $(this).val(val);
                UIError.clear($(this));
            });

            $(document).on('blur', selector, function() {
                const $input = $(this);
                const val = $input.val();
                if (!val) return;
                const result = TimeValidator.validate(val);
                if (!result.valid) UIError.show($input, result.message);
                else { $input.val(result.value); UIError.clear($input); }
            });
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

    const Form = {
        init() {
            this.bindValidation();
            this.bindAjaxSubmit();
            this.bindAI();
        },

        bindAjaxSubmit() {
            $(document).on('submit', '#battu-form', (e) => {
                e.preventDefault();
                this.submitAjax($('#battu-form'));
            });
        },

        submitAjax($form) {
            let hasError = false;

            const $dateInput = $form.find('.battu-input-date');
            const dateRes = DateValidator.validate($dateInput.val());
            if (!dateRes.valid) { UIError.show($dateInput, dateRes.message); hasError = true; }
            else { UIError.clear($dateInput); }

            const $timeInput = $form.find('.battu-input-time');
            const timeRes = TimeValidator.validate($timeInput.val());
            if (!timeRes.valid) { UIError.show($timeInput, timeRes.message); hasError = true; }
            else { $timeInput.val(timeRes.value); UIError.clear($timeInput); }

            $form.find('select[required]').each(function() {
                if (!$(this).val()) { UIError.show($(this), 'Vui lòng chọn thông tin này.'); hasError = true; }
                else { UIError.clear($(this)); }
            });

            if (hasError) { UIError.scrollToFirst($form); return; }

            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);
            $btn.find('.battu-btn-text').hide();
            $btn.find('.battu-btn-loading').show();
            $('#battu-error').hide();

            const data = {
                ho_ten: $form.find('input[name="battu_ho_ten"]').val() || 'Đương Số',
                ngay_sinh: $form.find('input[name="battu_ngay_sinh"]').val(),
                gio_sinh: $form.find('input[name="battu_gio_sinh"]').val(),
                gioi_tinh: $form.find('[name="battu_gioi_tinh"]').val()
            };

            $.ajax({ url: battu.api_url + 'calculate', method: 'POST', contentType: 'application/json', data: JSON.stringify(data) })
                .done((response) => {
                    if (response.success && response.html) {
                        $('#battu-result').html(response.html).show();
                        $('#battu-result').data('battu-data', { input: response.input });
                        $('.battu-lp-toggle').hide();
                        setTimeout(applyResultScale, 50);
                        scrollToResult($('#battu-result'));
                        $('#comments').show()
                    } else {
                        $('#battu-error').text(response.message || 'Có lỗi xảy ra.').show();
                    }
                })
                .fail((xhr) => { $('#battu-error').text(xhr.responseJSON?.message || 'Lỗi kết nối.').show(); })
                .always(() => {
                    $btn.prop('disabled', false);
                    $btn.find('.battu-btn-text').show();
                    $btn.find('.battu-btn-loading').hide();
                });
        },

        bindValidation() {
            const nameSelector = 'input[name="battu_ho_ten"]';
            $(document).on('input', nameSelector, function() {
                const $input = $(this);
                let val = $input.val().replace(/[^a-zA-ZÀ-ỹ\s\-]/g, '');
                if (val.length > 50) val = val.substring(0, 50);
                if (val !== $input.val()) {
                    const cursorPos = this.selectionStart;
                    $input.val(val);
                    this.setSelectionRange(cursorPos, cursorPos);
                }
            });

            $(document).on('blur', nameSelector, function() {
                const $input = $(this);
                const value = $input.val();
                if (!value) return;
                let cleaned = value.replace(/[^a-zA-ZÀ-ỹ\s\-]/g, '').replace(/\s+/g, ' ').trim();
                if (cleaned.length > 50) cleaned = cleaned.substring(0, 50);
                $input.val(Form.normalizeName(cleaned));
            });

            $(document).on('change', 'select', function() { if ($(this).val()) UIError.clear($(this)); });
        },

        normalizeName(value) {
            const cleaned = (value || '').replace(/\s+/g, ' ').trim();
            if (!cleaned) return '';
            return cleaned.toLocaleLowerCase('vi-VN')
                .split(' ')
                .filter(Boolean)
                .map(word => word.split('-').map(part => part ? part.charAt(0).toLocaleUpperCase('vi-VN') + part.slice(1) : '').join('-'))
                .join(' ');
        },

        bindAI() {
            $(document).on('click', '.sq-btn', function() {
                const q = $(this).attr('data-q') || $(this).text();
                $('#battu-user-question').val(q).focus();
            });

            $(document).on('click', '#battu-btn-qa-cancel', function() {
                $('#battu-qa-input-area').slideUp(200);
                $(this).hide();
                $('#battu-btn-deep-analyze').show();
                $('#battu-btn-qa-analyze').find('.battu-btn-text').text('Hỏi Bát Tự');
                $('#battu-btn-support').show();
                $('#battu-user-question').val('');
                $('.battu-err-analyze').hide().text('');
            });

            $(document).on('click', '#battu-btn-support-cancel', function() {
                $('#battu-support-area').slideUp(200);
                $(this).hide();
                $('#battu-btn-deep-analyze').show();
                $('#battu-btn-qa-analyze').show();
                $('#battu-btn-support').show();
                $('#battu-support-question').val('');
                $('.battu-err-support').hide().text('');
                $('.battu-success-support').hide().text('');
            });

            $(document).on('click', '#battu-btn-deep-analyze', function () {
                if (!Auth.require()) return;
                const $btn = $(this);
                const $qaBtn = $('#battu-btn-qa-analyze');
                const $supportBtn = $('#battu-btn-support');
                $qaBtn.hide();
                $supportBtn.hide();
                processAnalyze($btn, false, '', $qaBtn);
            });

            $(document).on('click', '#battu-btn-qa-analyze', function () {
                if (!Auth.require()) return;
                const $qaArea = $('#battu-qa-input-area');
                const $deepBtn = $('#battu-btn-deep-analyze');
                const $supportBtn = $('#battu-btn-support');
                const $btn = $(this);
                const $cancelBtn = $('#battu-btn-qa-cancel');
                const $err = $('.battu-err-analyze');

                if ($qaArea.is(':hidden')) {
                    $deepBtn.hide();
                    $supportBtn.hide();
                    $qaArea.show();
                    $btn.find('.battu-btn-text').text('Gửi câu hỏi');
                    $cancelBtn.show();
                    $qaArea.find('textarea').focus();
                    return;
                }

                const question = $('#battu-user-question').val().trim();
                if (question.length < 10) {
                    $err.text('Vui lòng đặt câu hỏi rõ nghĩa hơn.').show();
                    return;
                }
                $cancelBtn.fadeOut(300);
                processAnalyze($btn, true, question, null);
            });

            $(document).on('click', '#battu-btn-support', function () {
                if (!Auth.require()) return;
                const $supportArea = $('#battu-support-area');
                const $deepBtn = $('#battu-btn-deep-analyze');
                const $qaBtn = $('#battu-btn-qa-analyze');
                const $btn = $(this);
                const $cancelBtn = $('#battu-btn-support-cancel');

                $deepBtn.hide();
                $qaBtn.hide();
                $btn.hide();
                $supportArea.show();
                $cancelBtn.show();

                const hoTen = $('input[name="battu_ho_ten"]').val() || '[Tên của bạn]';
                const ngaySinh = $('input[name="battu_ngay_sinh"]').val() || '[Ngày sinh của bạn]';
                const gioSinh = $('input[name="battu_gio_sinh"]').val() || '[Giờ sinh của bạn]';
                const gioiTinh = $('select[name="battu_gioi_tinh"]').val() || 'nam';
                const userEmail = $('.user-dropdown-header p').length ? $('.user-dropdown-header p').text().trim() : '';

                $('#battu-support-question').val(`Họ tên: ${hoTen}\nNgày sinh: ${ngaySinh}\nGiờ sinh: ${gioSinh}\nGiới tính: ${gioiTinh}\nEmail: ${userEmail}\nCâu hỏi: `);
                $supportArea.find('textarea').focus();
            });

            $(document).on('click', '#battu-btn-support-submit', function () {
                if (!Auth.require()) return;
                const $err = $('.battu-err-support');
                const question = $('#battu-support-question').val().trim();

                if (question.length < 10 || question.split(/\s+/).filter(w => w.length > 0).length < 20) {
                    $err.text('Vui lòng đặt câu hỏi rõ ràng hơn.').show();
                    return;
                }

                $('#battu-modal-textarea').val(question);
                $('#battu-support-modal').css('display', 'flex');
            });

            $(document).on('click', '#battu-modal-ok', function () {
                $('#battu-support-modal').hide();
                const $btn = $('#battu-btn-support-submit');
                const $err = $('.battu-err-support');
                const $success = $('.battu-success-support');

                $btn.prop('disabled', true);
                $btn.find('.battu-btn-text').hide();
                $btn.find('.battu-btn-loading').show();
                $err.hide();
                $success.hide();

                $.ajax({
                    url: battu.api_url + 'send-support',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        ho_ten: $('input[name="battu_ho_ten"]').val() || '',
                        question: $('#battu-modal-textarea').val().trim()
                    })
                })
                    .done((response) => {
                        $btn.prop('disabled', false);
                        $btn.find('.battu-btn-text').show();
                        $btn.find('.battu-btn-loading').hide();
                        if (response.success) {
                            $success.text(response.message || 'Đã gửi câu hỏi thành công.').show();
                            $('#battu-support-question').val('');
                            $('#battu-support-area').hide();
                            $('#battu-btn-support').hide();
                            $('#battu-btn-support-cancel').hide();
                            $('#battu-btn-deep-analyze').show();
                            $('#battu-btn-qa-analyze').show();
                        } else {
                            $err.text(response.message || 'Có lỗi xảy ra.').show();
                        }
                    })
                    .fail((xhr) => {
                        $btn.prop('disabled', false);
                        $btn.find('.battu-btn-text').show();
                        $btn.find('.battu-btn-loading').hide();
                        $err.text(xhr.responseJSON?.message || 'Lỗi kết nối.').show();
                    });
            });

            $(document).on('click', '#battu-modal-cancel', function () {
                $('#battu-support-modal').hide();
            });
        }
    };

    async function processAnalyze($btn, isQA, question, $otherBtn) {
        const storedData = $('#battu-result').data('battu-data');
        const data = storedData ? storedData.input : null;
        if (!data) return;

        const $err = $('.battu-err-analyze');
        const $luanGiai = $('#battu-luan-giai');
        const $btnLoading = $btn.find('.battu-btn-loading');

        $btn.prop('disabled', true);
        $btn.find('.battu-btn-text').hide();
        $btnLoading.show();
        $err.hide().text('');

        data.is_qa_mode = isQA;
        data.user_question = question;

        const loadingTexts = isQA ? [
            'Đang kết nối...', 'Phân tích câu hỏi...', 'Đối chiếu Bát Tự...',
            'Phân tích Ngũ Hành...', 'Phân tích Thập Thần...', 'Tổng hợp kết quả...',
            'Luận giải...', 'Hoàn tất xử lý...', 'Vui lòng chờ...'
        ] : [
            'Đang kết nối...', 'Khởi tạo dữ liệu...', 'Phân tích Tứ Trụ...',
            'Phân tích Ngũ Hành...', 'Phân tích Thập Thần...', 'Phân tích Đại Vận...',
            'Phân tích Thần Sát...', 'Phân tích Dụng Thần...', 'Đối chiếu dữ kiện...',
            'Tổng hợp kết quả...', 'Luận giải Bát Tự...', 'Kiểm tra độ chính xác...',
            'Hoàn tất xử lý...', 'Vui lòng chờ...'
        ];

        const spinnerHtml = txt => `<span class="battu-spinner"></span> ${txt}`;
        let loadingStep = 0;
        $btnLoading.html(spinnerHtml(loadingTexts[0]));
        const loadingInterval = setInterval(() => {
            if (loadingStep < loadingTexts.length - 1) {
                loadingStep++;
                $btnLoading.html(spinnerHtml(loadingTexts[loadingStep]));
            }
        }, 1500);

        const abortController = new AbortController();
        const timeoutId = setTimeout(() => abortController.abort(), 180000);

        const cleanup = () => {
            clearInterval(loadingInterval);
            clearTimeout(timeoutId);
            $btn.prop('disabled', false);
            $btn.find('.battu-btn-text').show();
            $btnLoading.hide();
        };

        try {
            const res = await fetch(battu.api_url + 'analyze', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                signal: abortController.signal
            });

            const response = await res.json();
            cleanup();

            if (response.success && response.battu_html) {
                $luanGiai.html(response.battu_html).show();
                $('html, body').animate({ scrollTop: $luanGiai.offset().top - 100 }, 300);
                if (!isQA) {
                    $('#battu-btn-deep-analyze').hide();
                    $('#battu-btn-qa-analyze').show().find('.battu-btn-text').text('Hỏi Bát Tự');
                    $('#battu-qa-input-area').hide();
                }
            } else {
                if ($otherBtn) $otherBtn.show();
                $err.text(response.message || 'Không thể phân tích.').show();
            }
        } catch (err) {
            cleanup();
            if ($otherBtn) $otherBtn.show();
            $err.text(err.name === 'AbortError'
                ? 'Quá thời gian chờ. Vui lòng thử lại.'
                : 'Lỗi kết nối. Vui lòng kiểm tra internet và thử lại.').show();
        }
    }

    const Tabs = {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            $(document).on('click', '.battu-tab', (e) => {
                this.switch($(e.currentTarget));
            });
        },

        switch($tab) {
            const tabId = $tab.data('tab');
            const $container = $tab.closest('.battu-tabs').parent();

            $container.find('.battu-tab').removeClass('active');
            $tab.addClass('active');

            $container.find('.battu-tab-pane').removeClass('active');
            $container.find(`#battu-tab-${tabId}`).addClass('active');
        }
    };

    function initFAQ() {
        $(document).on('click', '.battu-faq-q', function () {
            $(this).closest('.battu-faq-item').toggleClass('is-open');
        });
    }

    let _scaleObserver = null;

    function applyResultScale() {
        const container = document.querySelector('.nh-result-scale-wrap');
        const wrap      = document.querySelector('.battu-capture-wrap');

        if (!container || !wrap) return;

        const updateScale = () => {
            if (window.innerWidth > 767) {
                wrap.style.removeProperty('--battu-scale');
                container.style.height = 'auto';
                return;
            }

            let available = container.clientWidth;
            if (available === 0) available = window.innerWidth - 40;

            if (available < 900) {
                const scale = Math.max(0.3, available / 900);
                wrap.style.setProperty('--battu-scale', scale);
                container.style.height = (wrap.offsetHeight * scale + 25) + 'px';
            } else {
                wrap.style.removeProperty('--battu-scale');
                container.style.height = 'auto';
            }
        };

        updateScale();

        if (_scaleObserver) _scaleObserver.disconnect();
        _scaleObserver = new ResizeObserver(() => window.requestAnimationFrame(updateScale));
        _scaleObserver.observe(document.body);
    }

    $(document).on('click', '#battu-new-calc-btn', function () {
        $('#battu-result').html('').hide();
        $('#battu-error').hide();
        $('.battu-lp-toggle').show();
        const $form = $('#battu-form');
        if ($form.length) {
            $('html, body').animate({ scrollTop: $form.offset().top - 20 }, 400);
        }
    });

    const Capture = {
        save() {
            typeof html2canvas === 'undefined'
                ? this.loadLibrary(() => this.capture())
                : this.capture();
        },

        init() {
            $(document).on('click', '#battu-download-btn', (e) => {
                e.preventDefault();
                this.save();
            });
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
            const element = document.querySelector('.battu-capture-wrap');
            const btn     = document.getElementById('battu-download-btn');
            if (!element || !btn) return;

            const originalBtnHtml = btn.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = '<span>Đang tạo ảnh...</span>';

            html2canvas(element, {
                backgroundColor: '#ffffff',
                scale: 3,
                logging: false,
                useCORS: true,
                windowWidth: 1440,
                ignoreElements: el =>
                    el.classList && el.classList.contains('battu-action-controls'),
                onclone: (clonedDoc) => {
                    const clonedWrap = clonedDoc.querySelector('.battu-capture-wrap');
                    if (clonedWrap) {
                        clonedWrap.style.width      = '900px';
                        clonedWrap.style.maxWidth   = '900px';
                        clonedWrap.style.transform  = 'none';
                        clonedWrap.style.animation  = 'none';
                        clonedWrap.style.opacity    = '1';
                        clonedWrap.style.removeProperty('--battu-scale');
                    }
                    clonedDoc.querySelectorAll('.battu-capture-wrap, .battu-capture-wrap *').forEach(el => {
                        el.style.animation  = 'none';
                        el.style.transition = 'none';
                    });
                }
            }).then(canvas => {
                this.download(canvas);
                btn.disabled  = false;
                btn.innerHTML = originalBtnHtml;
            }).catch(error => {
                console.error('Screenshot failed:', error);
                btn.disabled  = false;
                btn.innerHTML = originalBtnHtml;
            });
        },

        download(canvas) {
            canvas.toBlob(blob => {
                const url    = URL.createObjectURL(blob);
                const a      = document.createElement('a');
                const domain = window.location.hostname.replace(/^www\./, '');
                const nameEl = document.querySelector('.battu-result-title');
                const rawName = nameEl ? nameEl.textContent.replace(/^.*—\s*/, '').trim() : '';
                const sanitized = rawName
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9\s]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
                const fileName = sanitized
                    ? `${sanitized}_bat-tu_${domain}.png`
                    : `bat-tu_${domain}_${Date.now()}.png`;
                a.href     = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 'image/png');
        }
    };

    function init() {
        DateValidator.bind('.battu-input-date');
        TimeValidator.bind('.battu-input-time');
        Capture.init();
        Form.init();
        Tabs.init();
        initFAQ();
    }

    $(document).ready(init);
    return { init, Capture, Tabs };

})(jQuery);