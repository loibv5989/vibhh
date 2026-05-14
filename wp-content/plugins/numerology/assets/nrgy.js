jQuery(function ($) {

    const UI = {
        $form: $('#nrgy-form'),
        $resultBox: $('#nrgy-result'),
        $submitBtn: $('#nrgy-submit-btn')
    };

    const Loading = {
        set(isActive) {
            if (isActive) {
                UI.$submitBtn.addClass('nrgy-loading').attr('disabled', true);
            } else {
                UI.$submitBtn.removeClass('nrgy-loading').removeAttr('disabled');
            }
        }
    };

    const Validator = {
        normalizeName(value) {
            const cleaned = (value || '').replace(/[^a-zA-Z\s]/g, '').replace(/\s+/g, ' ').trim();
            if (!cleaned) return '';
            return cleaned.toLocaleLowerCase('en-US').split(' ').filter(Boolean).map((word) => {
                return word.split('-').map((part) => {
                    return part ? (part.charAt(0).toLocaleUpperCase('en-US') + part.slice(1)) : '';
                }).join('-');
            }).join(' ');
        },

        name(inputId, errorId) {
            const $input = $('#' + inputId);
            const $error = $('#' + errorId);
            const rawValue = $input.val();
            const value = this.normalizeName(rawValue);
            $input.val(value);
            $input.removeClass('is-error');
            $error.text('');
            if (!value) {
                $error.text('Please enter a valid full name.');
                $input.addClass('is-error');
                return false;
            }
            if (value.length > 40) {
                $error.text('Full name maximum 40 characters.');
                $input.addClass('is-error');
                return false;
            }
            return value;
        },
        dob(inputId, errorId) {
            const $input = $('#' + inputId);
            const $error = $('#' + errorId);
            const value = $input.val().trim();
            $input.removeClass('is-error');
            $error.text('');
            if (!value) {
                $error.text('Please enter your date of birth.');
                $input.addClass('is-error');
                return false;
            }
            const match = value.match(/^(\d{1,2})[/\-\.\s](\d{1,2})[/\-\.\s](19\d{2}|20\d{2})$/);
            if (!match) {
                $error.text('Please enter a valid date (MM/DD/YYYY).');
                $input.addClass('is-error');
                return false;
            }
            const month = parseInt(match[1], 10);
            const day   = parseInt(match[2], 10);
            const year  = parseInt(match[3], 10);
            if (month < 1 || month > 12) {
                $error.text('Month must be between 1 and 12.');
                $input.addClass('is-error');
                return false;
            }
            if (day < 1 || day > 31) {
                $error.text('Day must be between 1 and 31.');
                $input.addClass('is-error');
                return false;
            }

            const testDate = new Date(year, month - 1, day);
            if (testDate.getFullYear() !== year || testDate.getMonth() !== month - 1 || testDate.getDate() !== day) {
                $error.text('Date ' + month + '/' + day + '/' + year + ' does not exist.');
                $input.addClass('is-error');
                return false;
            }
            return value;
        }
    };

    const Color = {
        map: {
            life_path: '#8b5cf6',
            destiny: '#a855f7',
            attitude: '#ec4899',
            birthday: '#f97316',
            soul_urge: '#10b981',
            personality: '#06b6d4',
            maturity: '#84cc16',
            balance: '#14b8a6',
            karmic_lessons: '#ef4444',
            karmic_debt: '#b91c1c',
            pinnacles: '#eab308',
            challenges: '#64748b',
            personal_year: '#f59e0b',
            personal_month: '#0ea5e9',
            personal_day: '#3b82f6'
        },
        forValue(key) {
            return this.map[key] || '#8b5cf6';
        }
    };

    const Typewriter = {
        fastMode: false,
        speedMultiplier: 1,

        typeText($element, text, speed, callback, allowHtml = false) {
            let charIndex = 0;
            const tick = () => {
                if (charIndex >= text.length) {
                    if (callback) callback();
                    return;
                }
                if (allowHtml) {
                    $element.html(text);
                    charIndex = text.length;
                } else {
                    $element.append(document.createTextNode(text[charIndex++]));
                }

                const currentSpeed = (Typewriter.fastMode ? 4 : speed) * Typewriter.speedMultiplier;
                const randomDelay = Typewriter.fastMode ? 0 : (Math.random() * 10) * Typewriter.speedMultiplier;
                setTimeout(tick, currentSpeed + randomDelay);
            };
            tick();
        },

        run(lines, onDoneCallback, isCached = false) {
            Typewriter.fastMode = false;
            Typewriter.speedMultiplier = isCached ? 0.5 : 1;

            const $cursor = $('#nrgy-chat-body .nrgy-cursor');
            let lineIndex = 0;
            const processNextLine = () => {
                if (lineIndex >= lines.length) {
                    $cursor.addClass('hidden');
                    $('.nrgy-tw-closing').remove();
                    if (onDoneCallback) onDoneCallback();
                    return;
                }
                const line = lines[lineIndex++];
                if (line.type === 'divider') {
                    $cursor.before('<div class="nrgy-tw-divider"></div>');
                    setTimeout(processNextLine, (Typewriter.fastMode ? 6 : 54) * Typewriter.speedMultiplier);
                    return;
                }
                if (line.type === 'index') {
                    const $el = $('<div class="nrgy-tw-index" data-index-key="' + line.key + '"></div>');
                    $cursor.before($el);
                    const color = Color.forValue(line.key);
                    const full = line.label + ': ';
                    const number = String(line.value);
                    const hintText = line.hint || '';

                    this.typeText($el, full, 10, () => {
                        const $num = $('<span class="nrgy-tw-num" style="color:' + color + '">' + number + '</span>');
                        $el.append($num);
                        if (hintText) {
                            const $hint = $('<span class="nrgy-tw-hint nrgy-tw-hint-text">' + hintText + '</span>');
                            $el.append($hint);
                        }
                        setTimeout(processNextLine, (Typewriter.fastMode ? 6 : 36) * Typewriter.speedMultiplier);
                    });
                    return;
                }
                const cssMap = {
                    greeting: 'nrgy-tw-greeting',
                    intro: 'nrgy-tw-intro',
                    closing: 'nrgy-tw-closing',
                    easter: 'nrgy-tw-easter',
                    block: 'nrgy-tw-block'
                };
                const cssClass = cssMap[line.type] || 'nrgy-tw-text';
                const $el = $('<div class="' + cssClass + '"></div>');
                $cursor.before($el);
                const speed = line.type === 'greeting' ? 10 : 10;
                const allowHtml = line.type === 'block' || line.type === 'easter';

                this.typeText($el, line.text, speed, () => setTimeout(processNextLine, (Typewriter.fastMode ? 7 : 67) * Typewriter.speedMultiplier), allowHtml);
            };
            processNextLine();
        },

        typeHtml($target, htmlString, baseSpeed, onComplete) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = htmlString;
            const nodes = Array.from(tempDiv.childNodes);

            $target.empty().append('<span class="nrgy-cursor">|</span>');
            const $cursor = $target.find('.nrgy-cursor');

            function typeNode(node, containerNode, doneCb) {
                containerNode.appendChild($cursor[0]);

                if (node.nodeType === Node.TEXT_NODE) {
                    let text = node.textContent;
                    if (!text.trim()) {
                        containerNode.insertBefore(document.createTextNode(text), $cursor[0]);
                        doneCb();
                        return;
                    }
                    let charIndex = 0;
                    let textNode = document.createTextNode('');
                    containerNode.insertBefore(textNode, $cursor[0]);

                    function tickText() {
                        if (charIndex >= text.length) {
                            doneCb();
                            return;
                        }
                        textNode.nodeValue += text[charIndex];
                        charIndex++;
                        setTimeout(tickText, (Typewriter.fastMode ? 1 : baseSpeed) * Typewriter.speedMultiplier);
                    }
                    tickText();

                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    const voidElements = ['AREA', 'BASE', 'BR', 'COL', 'EMBED', 'HR', 'IMG', 'INPUT', 'LINK', 'META', 'PARAM', 'SOURCE', 'TRACK', 'WBR'];
                    let el = document.createElement(node.tagName);
                    Array.from(node.attributes).forEach(attr => el.setAttribute(attr.name, attr.value));
                    containerNode.insertBefore(el, $cursor[0]);

                    if (voidElements.includes(node.tagName.toUpperCase())) {
                        doneCb();
                        return;
                    }

                    let childNodes = Array.from(node.childNodes);
                    let childIndex = 0;
                    function nextChild() {
                        if (childIndex >= childNodes.length) {
                            containerNode.appendChild($cursor[0]);
                            doneCb();
                            return;
                        }
                        el.appendChild($cursor[0]);
                        typeNode(childNodes[childIndex], el, () => {
                            childIndex++;
                            nextChild();
                        });
                    }
                    nextChild();
                } else {
                    doneCb();
                }
            }

            let nodeIndex = 0;
            function processNextNode() {
                if (nodeIndex >= nodes.length) {
                    $target[0].appendChild($cursor[0]);
                    $cursor.addClass('hidden');
                    if (onComplete) onComplete();
                    return;
                }
                $target[0].appendChild($cursor[0]);
                typeNode(nodes[nodeIndex], $target[0], () => {
                    nodeIndex++;
                    processNextNode();
                });
            }
            processNextNode();
        }
    };

    const TabHandler = {
        init() {
            $('#nrgy-result').on('click', '.nrgy-tab', function (e) {
                e.stopPropagation();
                const tabId = $(this).data('tab');
                const $wrap = $(this).closest('.nrgy-analysis-wrap');
                $wrap.find('.nrgy-tab').removeClass('active');
                $(this).addClass('active');
                $wrap.find('.nrgy-tab-pane').removeClass('active');
                $('#nrgy-tab-' + tabId).addClass('active');
            });
        }
    };

    const FormHandler = {
        init() {
            let activeUserData = null;

            $('.faq-q').on('click', function () {
                const $a = $(this).next('.faq-a');
                const $c = $(this).find('.faq-chevron');
                $a.toggleClass('open');
                $c.toggleClass('open');
            });

            $(document).on('click', '.nrgy-btn-reset', function () {
                $(this).prop('disabled', true).fadeOut(200);
                $('#nrgy-landing-wrapper').removeClass('is-focused');
                $('#nrgy-result').slideUp(300, function () {
                    $(this).empty();
                });
                $('#nrgy-form')[0].reset();
                if ($('#nrgy-form-section').length) {
                    $('html,body').animate({
                        scrollTop: $('#nrgy-form-section').offset().top - 80
                    }, 500);
                }
                activeUserData = null;
            });

            $('#nrgy-name').on('keydown', function (e) {
                const allowedKeys = [8, 9, 13, 27, 32, 37, 38, 39, 40, 45, 46];
                if (allowedKeys.includes(e.keyCode) || e.ctrlKey || e.metaKey) return true;
                const char = String.fromCharCode(e.keyCode || e.which);
                if (/^[a-zA-Z\s]$/.test(char)) return true;
                return false;
            }).on('input', function () {
                const $el = $(this), val = $el.val();
                if (val.length > 50) $el.val(val.substring(0, 50));
            }).on('blur', function () {
                const $input = $(this);
                const value = Validator.normalizeName($input.val());
                $input.val(value);
                Validator.name('nrgy-name', 'nrgy-error-name');
            });
            $('#nrgy-dob').on('blur', function () {
                Validator.dob('nrgy-dob', 'nrgy-error-dob');
            });

            UI.$form.on('submit', function (e) {
                e.preventDefault();
                const name = Validator.name('nrgy-name', 'nrgy-error-name');
                const dob = Validator.dob('nrgy-dob', 'nrgy-error-dob');
                if (!name || !dob) return;

                Loading.set(true);
                UI.$resultBox.hide().empty();
                $('.nrgy-btn-reset').fadeOut(200).prop('disabled', true);

                fetch(nrgy.api_url + 'calculate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ full_name: name, dob: dob })
                })
                    .then(response => response.json())
                    .then(res => {
                        if (!res.success) {
                            $('#nrgy-error-name').text(res.message || 'An error occurred.');
                            Loading.set(false);
                            if ($('#nrgy-landing-wrapper').hasClass('is-focused')) {
                                $('.nrgy-btn-reset').prop('disabled', false).fadeIn(400);
                            }
                            return;
                        }

                        $('#nrgy-landing-wrapper').addClass('is-focused');
                        UI.$resultBox.html(res.html).fadeIn(300);
                        $('html,body').animate({scrollTop: UI.$resultBox.offset().top - 80}, 500);

                        const rawLines = $('#nrgy-chat-body').data('lines');
                        const lines = Array.isArray(rawLines) ? rawLines : (typeof rawLines === 'string' ? JSON.parse(rawLines) : []);

                        Typewriter.run(lines, () => {
                            Loading.set(false);
                            $('#nrgy-action-next').fadeIn(400);
                            $('.nrgy-btn-reset').prop('disabled', false).fadeIn(400);
                        });
                    })
                    .catch(error => {
                        $('#nrgy-error-name').text('Connection error. Please try again.');
                        Loading.set(false);
                        if ($('#nrgy-landing-wrapper').hasClass('is-focused')) {
                            $('.nrgy-btn-reset').prop('disabled', false).fadeIn(400);
                        }
                    });

                $(document).on('click', '#btn-show-analysis', function() {
                    const $btn = $(this);
                    const $chatBody = $('#nrgy-chat-body');
                    const name = $chatBody.data('name');
                    const dob = $chatBody.data('dob');

                    $('#nrgy-error-api').hide().text('');
                    $btn.addClass('nrgy-loading').attr('disabled', true);

                    fetch(nrgy.api_url + 'analyze', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ full_name: name, dob: dob })
                    })
                        .then(response => response.json())
                        .then(res => {
                            if (res.success) {
                                $('#nrgy-action-next').slideUp(300);
                                $('#nrgy-step-1-wrap').slideUp(400, () => {
                                    $('#nrgy-chat-body').remove();
                                });
                                $('#nrgy-analysis-display').html(res.html_detail);
                                $('#nrgy-step-2-wrap').slideDown(400, () => {
                                    $('#nrgy-disclaimer').fadeIn(400);
                                    $('#nrgy-action-footer').fadeIn(400);
                                });
                            } else {
                                $btn.removeClass('nrgy-loading').removeAttr('disabled');
                                $('#nrgy-error-api').text(res.message || 'An error occurred.').show();
                            }
                        })
                        .catch(error => {
                            $btn.removeClass('nrgy-loading').removeAttr('disabled');
                            $('#nrgy-error-api').text('Connection error. Please try again.').show();
                        });
                });
            });
        }
    };

    const Animation = {
        observer: null,

        init() {
            this.createObserver();
            this.observeElements();
        },

        createObserver() {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        this.observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px'
            });
        },

        observeElements() {
            const sections = document.querySelectorAll(
                '.nrgy-landing .lp-section, .nrgy-landing .intro-card, .nrgy-landing .ind-card, .nrgy-landing .faq-item'
            );
            sections.forEach(el => this.observer.observe(el));

            const cards = document.querySelectorAll('.nrgy-landing .intro-card, .nrgy-landing .ind-card');
            cards.forEach((el, i) => {
                el.style.transitionDelay = `${i * 80}ms`;
                this.observer.observe(el);
            });
        },

        destroy() {
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
        }
    };

    const NumberSwitcher = {
        init() {
            window.nrgySwitchNum = this.switchNumber.bind(this);
        },

        switchNumber(num, btnElement) {
            document.querySelectorAll('.nrgy-num-content').forEach(function (el) {
                el.classList.remove('active');
            });
            document.querySelectorAll('.nrgy-num-tab').forEach(function (el) {
                el.classList.remove('active');
            });
            document.getElementById('nrgy-mean-' + num).classList.add('active');
            btnElement.classList.add('active');
        }
    };

    $(document).on('click', '#nrgy-btn-comment', function () {
        const $comments = $('#comments');
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

    TabHandler.init();
    FormHandler.init();
    Animation.init();
    NumberSwitcher.init();
});