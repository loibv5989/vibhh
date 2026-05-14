const TuViApp = (function ($) {
    "use strict";

    const UIError = {
        show($input, message) {
            $input.addClass('is-error');
            let $msg = $input.siblings('.tuvi-input-err-msg');
            if ($msg.length === 0) {
                $input.after(`<span class="tuvi-input-err-msg">${message}</span>`);
            } else {
                $msg.text(message);
            }
        },
        clear($input) {
            $input.removeClass('is-error');
            $input.siblings('.tuvi-input-err-msg').remove();
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

    function showResultError(containerId, message) {
        const $container = $(containerId);
        if (!$container.length) return;
        $container.html('<div class="tuvi-result-box tuvi-result-bad">' + message + '</div>').show();
    }

    const DateValidator = {
        validate(value, $input = null) {
            if (!value) return { valid: false, message: 'Vui lòng nhập ngày.' };

            value = value.trim();
            const match = value.match(/^(\d{1,2})[/\-.\s](\d{1,2})[/\-.\s](\d{4})$/);

            if (!match) {
                let example = '9/8/1992';
                if ($input) {
                    const id = $input.attr('id') || '';
                    if (id === 'tuvi_date' || id === 'tuvi_start' || id === 'tuvi_end') {
                        example = `18/4/${new Date().getFullYear() + 1}`;
                    }
                }
                return { valid: false, message: `Định dạng: ngày/tháng/năm (VD: ${example})` };
            }

            const day = parseInt(match[1], 10);
            const month = parseInt(match[2], 10);
            const year = parseInt(match[3], 10);

            if (month < 1 || month > 12) return { valid: false, message: 'Tháng phải từ 1 đến 12.' };
            if (day < 1 || day > 31) return { valid: false, message: 'Ngày không hợp lệ.' };
            if (year < 1900 || year > 2100) return { valid: false, message: 'Hệ thống chỉ hỗ trợ tra cứu từ năm 1900 đến 2100.' };

            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || testDate.getMonth() !== month - 1 || testDate.getDate() !== day) {
                return { valid: false, message: `Ngày ${day}/${month} không tồn tại.` };
            }

            return { valid: true, value: `${day}/${month}/${year}` };
        },

        checkBusinessLogic($input, formattedDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const parts = formattedDate.split('/');
            const targetDate = new Date(parts[2], parts[1] - 1, parts[0]);
            const inputId = $input.attr('id') || '';
            const inputName = $input.attr('name') || '';

            if (inputId === 'tuvi_date' || inputId === 'tuvi_start' || inputId === 'tuvi_end') {
                if (targetDate < today) return { valid: false, message: 'Ngày này đã qua rồi.' };
            }
            if (inputName.includes('ngay_sinh')) {
                if (targetDate > today) return { valid: false, message: 'Vui lòng nhập ngày sinh thực tế của bạn.' };
            }
            return { valid: true };
        },

        validateDateInputs($form, checkVisibility = false) {
            let hasError = false;
            $form.find('.tuvi-input-date').each(function() {
                const $input = $(this);
                if (checkVisibility && !$input.is(':visible')) {
                    UIError.clear($input);
                    return;
                }
                const valRes = DateValidator.validate($input.val(), $input);
                if (!valRes.valid) {
                    UIError.show($input, valRes.message);
                    hasError = true;
                } else {
                    const logicRes = DateValidator.checkBusinessLogic($input, valRes.value);
                    if (!logicRes.valid) {
                        UIError.show($input, logicRes.message);
                        hasError = true;
                    } else {
                        UIError.clear($input);
                    }
                }
            });
            return hasError;
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

                const result = DateValidator.validate(val, $input);
                if (!result.valid) {
                    UIError.show($input, result.message);
                } else {
                    $input.val(result.value);
                    const logicRes = DateValidator.checkBusinessLogic($input, result.value);
                    if (!logicRes.valid) {
                        UIError.show($input, logicRes.message);
                    } else {
                        UIError.clear($input);
                    }
                }
            });
        }
    };

    const FAQ = {
        init() { this.bindEvents(); },

        bindEvents() {
            $(document).on('click', '.tuvi-faq-q', (e) => {
                this.toggle($(e.currentTarget));
            });
        },

        toggle($element) {
            const $item = $element.closest('.tuvi-faq-item');
            const $container = $element.closest('.tuvi-faq-list');
            $container.find('.tuvi-faq-item').not($item).removeClass('open');
            $item.toggleClass('open');
            $element.attr('aria-expanded', $item.hasClass('open'));
        }
    };

    const Capture = {
        init() {
            window.tuviSaveImage = this.save.bind(this);
        },

        save() {
            if (typeof html2canvas === 'undefined') {
                this.loadLibrary(this.capture.bind(this));
            } else {
                this.capture();
            }
        },

        loadLibrary(callback) {
            if (this.isLoadingLibrary) return;
            this.isLoadingLibrary = true;
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.3/html2canvas.min.js';
            script.onload = () => { this.isLoadingLibrary = false; callback(); };
            document.head.appendChild(script);
        },

        capture() {
            const element = document.querySelector('.laso-wrapper');
            const btn = document.getElementById('tuvi-download-btn');
            if (!element) return;

            let $container = $(element).closest('.tuvi-form-wrap, .tuvi-ntx-wrap, #tuvi-ht-app');
            if ($container.length === 0) {
                $container = $('.tuvi-tabs').parent();
            }
            
            const $currentTab = $container.find('.tuvi-tab.active');
            const currentTabId = $currentTab.data('tab');

            if (currentTabId !== 'la-so') {
                $container.find('.tuvi-tab').removeClass('active');
                $container.find('.tuvi-tab[data-tab="la-so"]').addClass('active');
                $container.find('.tuvi-tab-pane').removeClass('active');
                $container.find('#tuvi-tab-la-so').addClass('active');
            }

            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';

            let originalBtnHtml = '';
            if (btn) {
                originalBtnHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span>Đang tạo ảnh...</span>';
            }

            const restoreTab = () => {
                if (currentTabId && currentTabId !== 'la-so') {
                    $container.find('.tuvi-tab').removeClass('active');
                    $currentTab.addClass('active');
                    $container.find('.tuvi-tab-pane').removeClass('active');
                    $container.find(`#tuvi-tab-${currentTabId}`).addClass('active');
                }
            };

            setTimeout(() => {
                html2canvas(element, {
                    backgroundColor: currentTheme === 'dark' ? '#0d1117' : '#ffffff',
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    windowWidth: 1440,
                    ignoreElements: el => el.classList && (el.classList.contains('tuvi-wbtn') || el.classList.contains('tuvi-btn-download')),
                    onclone: (clonedDoc) => {
                        clonedDoc.documentElement.setAttribute('data-theme', currentTheme);

                        const clonedWrap = clonedDoc.querySelector('.laso-wrapper');
                        if (clonedWrap) {
                            clonedWrap.style.width = '900px';
                            clonedWrap.style.maxWidth = '900px';
                            clonedWrap.style.transform = 'none';
                            clonedWrap.style.boxShadow = 'none';
                            clonedWrap.style.containerType = 'normal';
                            clonedWrap.style.overflow = 'visible';
                            clonedWrap.style.animation = 'none';
                            clonedWrap.style.background = currentTheme === 'dark' ? '#12161e' : '#fdfbf7';

                            const cornerColor = getComputedStyle(document.documentElement).getPropertyValue('--tuvi-color-1').trim() || '#b8860b';
                            const cornerTL = clonedDoc.createElement('div'); cornerTL.style.cssText = ` position: absolute; top: 2px; left: 2px; width: 30px; height: 30px; border-top: 3px solid ${cornerColor}; border-left: 3px solid ${cornerColor}; pointer-events: none; z-index: 10; box-sizing: border-box; `;
                            clonedWrap.appendChild(cornerTL);
                            const cornerBR = clonedDoc.createElement('div'); cornerBR.style.cssText = ` position: absolute; bottom: 2px; right: 2px; width: 30px; height: 30px; border-bottom: 3px solid ${cornerColor}; border-right: 3px solid ${cornerColor}; pointer-events: none; z-index: 10; box-sizing: border-box; `;
                            clonedWrap.appendChild(cornerBR);
                        }
                        const borderColor = currentTheme === 'dark' ? '#ef5350' : '#e53935';
                        clonedDoc.querySelectorAll('.ls-cung-tieu-han').forEach(cung => {
                            cung.style.position = 'relative';
                            cung.style.overflow = 'hidden';
                            const overlay = clonedDoc.createElement('div');
                            overlay.style.cssText = ` position: absolute; top: 2px; left: 2px; right:0; bottom: 2px; border: 1px solid ${borderColor}; box-sizing: border-box; pointer-events: none; z-index: 5; `;
                            cung.appendChild(overlay);
                        });
                    },
                }).then(canvas => {
                    this.download(canvas);
                    restoreTab();
                    if (btn) { btn.disabled = false; btn.innerHTML = originalBtnHtml; }
                }).catch(error => {
                    restoreTab();
                    if (btn) { btn.disabled = false; btn.innerHTML = originalBtnHtml; }
                });
            }, 100);
        },

        download(canvas) {
            canvas.toBlob(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                const nameElement = document.querySelector('.ls-info-val');
                const userName = nameElement ? nameElement.textContent.trim() : '';
                const sanitized = userName
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9\s]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
                const domain = window.location.hostname.replace(/^www\./, '');
                const fileName = sanitized ? `${sanitized}_la-so-tu-vi_${domain}.png` : `la-so-tu-vi_${domain}_${Date.now()}.png`;
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 'image/png');
        }
    };

    const Tabs = {
        init() { this.bindEvents(); },

        bindEvents() {
            $(document).on('click', '.tuvi-tab', (e) => {
                this.switch($(e.currentTarget));
            });
        },

        switch($tab) {
            const tabId = $tab.data('tab');
            const $container = $tab.closest('.tuvi-form-wrap, .tuvi-ntx-wrap, #tuvi-ht-app');
            $container.find('.tuvi-tab').removeClass('active');
            $container.find('.tuvi-tab-pane').removeClass('active');
            $tab.addClass('active');
            $container.find(`#tuvi-tab-${tabId}`).addClass('active');
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
            this.bindEvents();
            this.bindValidation();
            this.bindAjaxSubmit();
        },

        bindAjaxSubmit() {
            $(document).on('submit', '.tuvi-form-inline', (e) => {
                e.preventDefault();
                this.submitAjax($(e.currentTarget));
            });
        },

        submitAjax($form) {
            let hasError = DateValidator.validateDateInputs($form);
            $form.find('select[required]').each(function() {
                if (!$(this).val()) {
                    UIError.show($(this), 'Vui lòng chọn thông tin này.');
                    hasError = true;
                } else {
                    UIError.clear($(this));
                }
            });

            if (hasError) { UIError.scrollToFirst($form); return; }

            const $btn = $form.find('.tuvi-btn-submit-inline');
            const originalText = $btn.text();
            $btn.prop('disabled', true).text('Đang lập lá số...');
            $form.find('.tuvi-error-inline').remove();

            const data = {
                ho_ten: $form.find('input[name="tuvi_ho_ten"]').val() || 'Đương Số',
                ngay_sinh: $form.find('input[name="tuvi_ngay_sinh"]').val(),
                gio_sinh: $form.find('[name="tuvi_gio_sinh"]').val(),
                gioi_tinh: $form.find('[name="tuvi_gioi_tinh"]').val(),
                nam_xem: $form.find('input[name="tuvi_nam_xem"]').val() || new Date().getFullYear()
            };

            $.ajax({ url: tuvi.api_url + 'calculate', method: 'POST', contentType: 'application/json', data: JSON.stringify(data) })
                .done((response) => {
                    if (response.success && response.html) {
                        this.displayResult(response);
                    } else {
                        this.showError($form, response.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                })
                .fail((xhr) => {
                    this.showError($form, xhr.responseJSON?.message || 'Lỗi kết nối, vui lòng thử lại.');
                })
                .always(() => {
                    $btn.prop('disabled', false).text(originalText);
                });
        },

        displayResult(response) {
            const $wrapper = $('.tuvi-form-wrap');
            $wrapper.find('.tuvi-result-container').remove();
            const $result = $('<div class="tuvi-result-container"></div>').html(response.html);
            $result.data('tuvi-data', { thong_tin: response.thong_tin, la_so: response.la_so, input: response.input });
            $wrapper.append($result);
            $('#tuvi-btn-deep-analyze').data('can-analyze', response.is_logged_in || false);
            scrollToResult($result);
            $('#comments').fadeIn(400);
        },

        showError($form, message) {
            $form.find('.tuvi-error-inline').remove();
            $form.append(`<div class="tuvi-error-inline">${message}</div>`);
        },

        bindValidation() {
            const nameSelector = 'input[name="tuvi_ho_ten"], input[name="tuvi_ht_ten_a"], input[name="tuvi_ht_ten_b"]';

            $(document).on('input', nameSelector, function() {
                const $input = $(this);
                let val = $input.val().replace(/[^a-zA-ZÀ-ỹ\s]/g, '');
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
                let cleaned = value.replace(/[^a-zA-ZÀ-ỹ\s]/g, '').replace(/\s+/g, ' ').trim();
                if (cleaned.length > 50) cleaned = cleaned.substring(0, 50);
                $input.val(Form.normalizeName(cleaned));
            });

            $(document).on('input', 'input[name="tuvi_gio_sinh"], input[name="tuvi_ht_gio_sinh_a"], input[name="tuvi_ht_gio_sinh_b"]', function() {
                let input = $(this).val().replace(/[^\d:]/g, '');
                if (input.length > 5) input = input.substring(0, 5);
                $(this).val(input);
                UIError.clear($(this));
            });

            $(document).on('blur', 'input[name="tuvi_gio_sinh"], input[name="tuvi_ht_gio_sinh_a"], input[name="tuvi_ht_gio_sinh_b"]', function() {
                let val = $(this).val();
                if (!val) { UIError.clear($(this)); return; }

                val = val.replace(/[^\d:]/g, '');
                if (!val.includes(':')) {
                    if (val.length === 3)      val = '0' + val.substring(0, 1) + ':' + val.substring(1, 3);
                    else if (val.length === 4)  val = val.substring(0, 2) + ':' + val.substring(2, 4);
                    else if (val.length <= 2)   val = val.padStart(2, '0') + ':00';
                } else {
                    const parts = val.split(':');
                    if (parts.length === 2) {
                        const h = parts[0] ? parts[0].padStart(2, '0') : '00';
                        const m = parts[1] ? parts[1].padEnd(2, '0').substring(0, 2) : '00';
                        val = h + ':' + m;
                    }
                }

                if (/^([01][0-9]|2[0-3]):[0-5][0-9]$/.test(val)) {
                    $(this).val(val);
                    $(this).attr('placeholder', 'VD: 14:30');
                    UIError.clear($(this));
                } else {
                    $(this).val('');
                    UIError.show($(this), 'Sai định dạng giờ (VD: 14:30)');
                }
            });
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

        bindEvents() {
            $(document).on('change', 'select', function() {if ($(this).val()) UIError.clear($(this));});

            $(document).on('click', '.tuvi-sq-btn', function() {
                const q = $(this).attr('data-q') || $(this).text();
                $('#tuvi-user-question').val(q).focus();
            });

            $(document).on('click', '#tuvi-btn-qa-cancel', function() {
                const $qaArea = $('#tuvi-qa-input-area');
                const $deepBtn = $('#tuvi-btn-deep-analyze');
                const $qaBtn = $('#tuvi-btn-qa-analyze');
                const $err = $('#tuvi-ai-actions').find('.tuvi-err-analyze');

                $qaArea.slideUp(200);
                $(this).hide();

                $deepBtn.show();
                $qaBtn.find('.tuvi-btn-text').text('Hỏi lá số');

                $('#tuvi-user-question').val('');
                $err.hide().text('');
            });

            $(document).on('click', '#tuvi-reset-btn', (e) => { e.preventDefault(); this.reset(); });

            $(document).on('click', '#tuvi-download-btn', (e) => { e.preventDefault(); window.tuviSaveImage(); });

            $(document).on('click', '#tuvi-btn-deep-analyze', async function () {
                if (!Auth.require()) return;

                const $btn = $(this);
                const $qaBtn = $('#tuvi-btn-qa-analyze');
                const $err = $('#tuvi-ai-actions').find('.tuvi-err-analyze');
                const $btnLoading = $btn.find('.tuvi-btn-loading');

                $qaBtn.hide();
                $btn.prop('disabled', true);
                $btn.find('.tuvi-btn-text').hide();
                $btnLoading.show();
                $err.hide().text('');

                const data = {
                    ho_ten: $('input[name="tuvi_ho_ten"]').val(),
                    ngay_sinh: $('input[name="tuvi_ngay_sinh"]').val(),
                    gio_sinh: $('[name="tuvi_gio_sinh"]').val(),
                    gioi_tinh: $('[name="tuvi_gioi_tinh"]').val(),
                    nam_xem: $('input[name="tuvi_nam_xem"]').val() || new Date().getFullYear(),
                };

                if (!data.ngay_sinh || !data.gio_sinh || !data.gioi_tinh) {
                    $btn.prop('disabled', false);
                    $qaBtn.show();
                    $btn.find('.tuvi-btn-text').show();
                    $btnLoading.hide();
                    return;
                }

                const loadingTexts = [
                    'Đang kết nối...', 'Khởi tạo dữ liệu...', 'Phân tích Cung Mệnh...',
                    'Phân tích Cung Tài Bạch...', 'Phân tích Cung Quan Lộc...', 'Phân tích Cung Phu Thê...',
                    'Phân tích Đại Vận...', 'Phân tích Tiểu Vận...', 'Đối chiếu dữ kiện...',
                    'Tổng hợp kết quả...', 'Luận giải lá số...', 'Kiểm tra độ chính xác...',
                    'Hoàn tất xử lý...', 'Vui lòng chờ...'
                ];

                const spinnerHtml = txt => `<span class="tuvi-spinner"></span> ${txt}`;
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
                };

                try {
                    const res = await fetch(tuvi.api_url + 'analyze', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data),
                        signal: abortController.signal
                    });

                    const response = await res.json();
                    cleanup();

                    if (response.success && response.tuvi_html) {
                        $('#tuvi-luan-giai').html(response.tuvi_html).show();
                        $('#tuvi-disclaimer').show();
                        $('html, body').animate({ scrollTop: $('#tuvi-luan-giai').offset().top - 100 }, 300);
                        $btn.hide();
                        $qaBtn.show().find('.tuvi-btn-text').text('Hỏi lá số');
                        $btn.find('.tuvi-btn-text').show();
                        $btnLoading.hide();
                    } else {
                        $btn.prop('disabled', false);
                        $qaBtn.show();
                        $btn.find('.tuvi-btn-text').show();
                        $btnLoading.hide();
                        $err.text(response.message || 'Không thể phân tích. Vui lòng thử lại.').show();
                    }
                } catch (err) {
                    cleanup();
                    $btn.prop('disabled', false);
                    $qaBtn.show();
                    $btn.find('.tuvi-btn-text').show();
                    $btnLoading.hide();
                    $err.text(err.name === 'AbortError'
                        ? 'Quá thời gian chờ. Vui lòng thử lại.'
                        : 'Lỗi kết nối. Vui lòng kiểm tra internet và thử lại.').show();
                }
            });

            $(document).on('click', '#tuvi-btn-qa-analyze', async function () {
                if (!Auth.require()) return;

                const $qaArea = $('#tuvi-qa-input-area');
                const $deepBtn = $('#tuvi-btn-deep-analyze');
                const $btn = $(this);
                const $cancelBtn = $('#tuvi-btn-qa-cancel');
                const $err = $('#tuvi-ai-actions').find('.tuvi-err-analyze');
                const $btnLoading = $btn.find('.tuvi-btn-loading');

                if ($qaArea.is(':hidden')) {
                    $deepBtn.hide();
                    $qaArea.show();
                    $btn.find('.tuvi-btn-text').text('Gửi câu hỏi');
                    $cancelBtn.show();
                    $qaArea.find('textarea').focus();
                    return;
                }
                const question = $('#tuvi-user-question').val().trim();
                if (question.length < 10) {
                    $err.text('Vui lòng đặt câu hỏi rõ nghĩa hơn.').show();
                    return;
                }

                $btn.prop('disabled', true);
                $btn.find('.tuvi-btn-text').hide();
                $btnLoading.show();
                $err.hide().text('');
                $cancelBtn.fadeOut(300);

                const data = {
                    ho_ten: $('input[name="tuvi_ho_ten"]').val(),
                    ngay_sinh: $('input[name="tuvi_ngay_sinh"]').val(),
                    gio_sinh: $('[name="tuvi_gio_sinh"]').val(),
                    gioi_tinh: $('[name="tuvi_gioi_tinh"]').val(),
                    nam_xem: $('input[name="tuvi_nam_xem"]').val() || new Date().getFullYear(),
                    user_question: question,
                    is_qa_mode: true
                };

                const loadingTexts = [
                    'Đang kết nối...', 'Phân tích câu hỏi...', 'Đối chiếu lá số...',
                    'Phân tích Chính Tinh...', 'Phân tích Phụ Tinh...', 'Tổng hợp kết quả...',
                    'Luận giải...', 'Hoàn tất xử lý...', 'Vui lòng chờ...'
                ];

                const spinnerHtml = txt => `<span class="tuvi-spinner"></span> ${txt}`;
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
                };

                try {
                    const res = await fetch(tuvi.api_url + 'analyze', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data),
                        signal: abortController.signal
                    });

                    const response = await res.json();
                    cleanup();

                    if (response.success && response.tuvi_html) {
                        $('#tuvi-luan-giai').html(response.tuvi_html).show();
                        $('#tuvi-disclaimer').show();
                        $('html, body').animate({ scrollTop: $('#tuvi-luan-giai').offset().top - 100 }, 300);
                        $qaArea.hide();
                        $btn.find('.tuvi-btn-text').text('Hỏi lá số');
                        $btn.prop('disabled', false);
                        $btn.find('.tuvi-btn-text').show();
                        $btnLoading.hide();
                    } else {
                        $btn.prop('disabled', false);
                        $btn.find('.tuvi-btn-text').show();
                        $btnLoading.hide();
                        const errorMsg = response.gatekeeper === 'KHONG'
                            ? 'Vui lòng đặt câu hỏi rõ nghĩa hơn.'
                            : (response.message || 'Không thể phân tích. Vui lòng thử lại.');
                        $err.text(errorMsg).show();
                    }
                } catch (err) {
                    cleanup();
                    $btn.prop('disabled', false);
                    $btn.find('.tuvi-btn-text').show();
                    $btnLoading.hide();
                    $err.text(err.name === 'AbortError'
                        ? 'Quá thời gian chờ. Vui lòng thử lại.'
                        : 'Lỗi kết nối. Vui lòng kiểm tra internet và thử lại.').show();
                }
            });
        },

        reset() {
            const $wrapper = $('.tuvi-form-wrap');
            const $form = $wrapper.find('form');
            if ($form.length) {
                $form[0].reset();
                $form.find('input[type="text"], input[type="date"], input[type="time"], input[type="number"]').val('');
                $form.find('select').prop('selectedIndex', 0);
            }

            $wrapper.find('.tuvi-error-inline').remove();

            const $calcCard = $wrapper.find('.tuvi-calc-card');
            if ($calcCard.length === 0) {
                window.location.href = window.location.pathname + window.location.search;
                return;
            }

            $wrapper.children().not('.tuvi-calc-card').hide();
            $calcCard.show();
            $('html, body').animate({ scrollTop: $wrapper.offset().top - 50 }, 300);
        }
    };

    const NTX = {
        init() {
            this.bindEvents();
            this.initFormToggle();
        },

        bindEvents() {
            $(document).on('submit', '.tuvi-ntx-form', (e) => {
                e.preventDefault();
                this.handleSubmit($(e.currentTarget));
            });
        },

        initFormToggle() {
            const $modeEl = $('#tuvi_mode');
            const $singleField = $('.tuvi-single-field');
            const $rangeFields = $('.tuvi-range-fields');
            if (!$modeEl.length || !$singleField.length || !$rangeFields.length) return;

            const syncMode = () => {
                if ($modeEl.val() === 'range') {
                    $singleField.hide().find('input').prop('disabled', true);
                    $rangeFields.show().find('input').prop('disabled', false);
                } else {
                    $singleField.show().find('input').prop('disabled', false);
                    $rangeFields.hide().find('input').prop('disabled', true);
                }
            };
            $modeEl.on('change', syncMode);
            syncMode();
        },

        handleSubmit($form) {
            let hasError = DateValidator.validateDateInputs($form, true);
            if (hasError) { UIError.scrollToFirst($form); return; }

            $form.find('select[required]').each(function() {
                if (!$(this).val()) {
                    UIError.show($(this), 'Vui lòng chọn thông tin này.');
                    hasError = true;
                } else {
                    UIError.clear($(this));
                }
            });

            const $timeInput = $form.find('input[name="tuvi_gio_sinh"]');
            const timeVal = $timeInput.val();
            if (timeVal && !/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(timeVal)) {
                UIError.show($timeInput, 'Vui lòng nhập định dạng 24h (VD: 14:30)');
                hasError = true;
            }

            if (hasError) { UIError.scrollToFirst($form); return; }

            const mode = $form.find('#tuvi_mode').val();
            const $btn = $form.find('button[type="submit"]');
            const $btnText = $btn.find('.tuvi-btn-text');
            const originalText = $btnText.text() || 'Tra cứu';
            $btn.prop('disabled', true);
            $btnText.text('Đang phân tích...');

            const data = {
                mode,
                purpose: $form.find('#tuvi_purpose').val(),
                ngay_sinh: $form.find('input[name="tuvi_ngay_sinh"]').val(),
                gio_sinh: timeVal,
                gioi_tinh: $form.find('select[name="tuvi_gioi_tinh"]').val()
            };

            if (mode === 'range') {
                data.start = $form.find('#tuvi_start').val();
                data.end = $form.find('#tuvi_end').val();
                data.limit = parseInt($form.find('#tuvi_limit').val()) || 10;
            } else {
                data.date = $form.find('#tuvi_date').val();
            }

            $.ajax({ url: tuvi.api_url + 'ntx', method: 'POST', contentType: 'application/json', data: JSON.stringify(data) })
                .done((response) => {
                    if (response.success) { this.displayResult(response); }
                    else { this.showError(response.message); }
                })
                .fail((xhr) => { this.showError(xhr.responseJSON?.message || 'Có lỗi hệ thống xảy ra.'); })
                .always(() => { $btn.prop('disabled', false); $btnText.text(originalText); });
        },

        displayResult(response) {
            const $container = $('#ntx-result');
            if (!$container.length) return;
            $container.html(response.html);
            $('.tuvi-tabs').removeClass('tuvi-tabs-hidden');
            $('.tuvi-tab').removeClass('active');
            $('.tuvi-tab[data-tab="chi-tiet"]').addClass('active');
            $('#tuvi-tab-chi-tiet').addClass('active');
            $('#comments').fadeIn(400);
            scrollToResult($container);
        },

        showError(message) { showResultError('#ntx-result', message); }
    };

    const HT = {
        init() { this.bindEvents(); },

        bindEvents() {
            $(document).on('submit', '.tuvi-ht-form', (e) => {
                e.preventDefault();
                this.handleSubmit($(e.currentTarget));
            });
        },

        handleSubmit($form) {
            const hasError = DateValidator.validateDateInputs($form);
            if (hasError) { UIError.scrollToFirst($form); return; }

            const $btn = $form.find('button[type="submit"]');
            const $btnText = $btn.find('.tuvi-btn-text');
            const originalText = $btnText.text() || 'Xem Hợp Tuổi';
            $btn.prop('disabled', true);
            $btnText.text('Đang phân tích...');

            const data = {
                muc_dich:    $form.find('#tuvi_ht_muc_dich').val(),
                ten_a:       $form.find('#tuvi_ht_ten_a').val(),
                ngay_sinh_a: $form.find('#tuvi_ht_ngay_sinh_a').val(),
                gio_sinh_a:  $form.find('#tuvi_ht_gio_sinh_a').val(),
                gioi_tinh_a: $form.find('#tuvi_ht_gioi_tinh_a').val(),
                ten_b:       $form.find('#tuvi_ht_ten_b').val(),
                ngay_sinh_b: $form.find('#tuvi_ht_ngay_sinh_b').val(),
                gio_sinh_b:  $form.find('#tuvi_ht_gio_sinh_b').val(),
                gioi_tinh_b: $form.find('#tuvi_ht_gioi_tinh_b').val()
            };

            $.ajax({ url: tuvi.api_url + 'hoptuoi', method: 'POST', contentType: 'application/json', data: JSON.stringify(data) })
                .done((response) => {
                    if (response.success) { this.displayResult(response); }
                    else { this.showError(response.message); }
                })
                .fail((xhr) => { this.showError(xhr.responseJSON?.message || 'Có lỗi hệ thống xảy ra.'); })
                .always(() => { $btn.prop('disabled', false); $btnText.text(originalText); });
        },

        displayResult(response) {
            const $container = $('#ht-result');
            if (!$container.length) return;
            $container.html(response.html);
            const $wrap = $container.closest('#tuvi-ht-app');
            $wrap.find('.tuvi-tabs').css('display', '');
            $wrap.find('.tuvi-tab').removeClass('active');
            $wrap.find('.tuvi-tab-pane').removeClass('active');
            $wrap.find('.tuvi-tab[data-tab="chi-tiet"]').addClass('active');
            $wrap.find('#tuvi-tab-chi-tiet').addClass('active');
            $('#comments').fadeIn(400);
            scrollToResult($container);
        },

        showError(message) { showResultError('#ht-result', message); }
    };

    const init = () => {
        DateValidator.bind('.tuvi-input-date');
        FAQ.init();
        Capture.init();
        Tabs.init();
        Form.init();
        NTX.init();
        HT.init();
    };

    return { init, FAQ, Capture, Tabs, Form, HT };

})(jQuery);

jQuery(() => { TuViApp.init(); });