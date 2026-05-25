(function ($) {
    'use strict';

    const SEARCH_DELAY      = 300;
    const MIN_SEARCH_LENGTH = 2;
    const RESIZE_DELAY      = 250;
    const BREAKPOINT_MOBILE = 768;
    const BREAKPOINT_DESKTOP = 1024;

    const ajax_url = lbvMain.ajax_url;
    let currentViewport = 'desktop';

    const abortXhr = (xhr) => {
        if (xhr && xhr.readyState !== 4) xhr.abort();
    };

    const ajaxPost = (action, extraData = {}) =>
        $.ajax({ url: ajax_url, type: 'POST', data: { action, ...extraData } });

    const restoreBodyScroll = () => {
        const scrollY = document.body.dataset.scrollY || 0;
        document.body.classList.remove('mobile-menu-active');
        document.body.style.top = '';
        window.scrollTo(0, parseInt(scrollY));
    };

    const closeAllDropdowns = (except = null) => {
        const actions = [];

        if (except !== 'search') {
            actions.push(
                ['#searchBox',             'removeClass', 'active'],
                ['.search-dropdown',       'removeClass', 'active'],
                ['.search-input-wrapper',  'removeClass', 'has-results']
            );
            $('.search-dropdown').empty();
        }
        if (except !== 'user')         actions.push(['.user-container',         'removeClass', 'active']);
        if (except !== 'nav') {
            actions.push(
                ['.nav-item',                    'removeClass', 'active'],
                ['.dropdown-item.has-submenu',   'removeClass', 'active']
            );
        }

        actions.forEach(([sel, method, cls]) => $(sel)[method](cls));
    };

    const StickyHeader = {
        lastScrollTop:   0,
        ticking:         false,
        $header:         null,
        scrollThreshold: 100,
        scrollHandler:   null,

        init() {
            this.$header = $('.header');
            if (!this.$header.length) return;

            this.scrollHandler = this.handleScroll.bind(this);
            window.addEventListener('scroll', this.scrollHandler, { passive: true });
        },

        handleScroll() {
            if (this.ticking) return;

            requestAnimationFrame(() => {
                const scrollTop = window.scrollY || document.documentElement.scrollTop;

                if (scrollTop <= this.scrollThreshold || scrollTop < this.lastScrollTop) {
                    this.$header.removeClass('hidden');
                } else {
                    this.$header.addClass('hidden');
                }

                this.lastScrollTop = scrollTop;
                this.ticking = false;
            });
            this.ticking = true;
        },

        destroy() {
            if (this.scrollHandler) {
                window.removeEventListener('scroll', this.scrollHandler);
            }
            Object.assign(this, { scrollHandler: null, ticking: false, lastScrollTop: 0, $header: null });
        }
    };

    const Search = {
        timeout:          null,
        xhr:              null,
        currentRequestId: 0,

        init() {
            this._bindInput('#searchInput',       '#searchBox .search-dropdown',    '#searchBox .search-input-wrapper');
            this._bindInput('#mobileSearchInput', '.mobile-search .search-dropdown', '.mobile-search .search-input-wrapper');
            this._bindSearchBtn();
        },

        _bindInput(inputSel, dropdownSel, wrapperSel) {
            const $input = $(inputSel);
            if (!$input.length) return;

            const $dropdown = $(dropdownSel);
            const $wrapper  = $(wrapperSel);

            $input.on('input', (e) => this.performSearch($(e.target).val().trim(), $dropdown, $wrapper));
        },

        _bindSearchBtn() {
            const $btn   = $('#searchBtn');
            const $box   = $('#searchBox');
            const $input = $('#searchInput');
            const $home  = $('.s-home');

            if (!$btn.length || !$box.length) return;

            $btn.off('click').on('click', (e) => {
                if (document.body.classList.contains('home')) {
                    $home.trigger('focus');
                    return;
                }

                if ($(window).width() > BREAKPOINT_MOBILE) {
                    e.stopPropagation();
                    closeAllDropdowns('search');
                    $box.toggleClass('active');
                    if ($box.hasClass('active')) $input.trigger('focus');
                } else {
                    MobileMenu.openMobileMenu(e);
                    $input.trigger('focus');
                }
            });
        },

        performSearch(query, $dropdown, $wrapper) {
            clearTimeout(this.timeout);
            abortXhr(this.xhr);

            query = query.trim().substring(0, 30);

            if (query.length < MIN_SEARCH_LENGTH) {
                this.hideResults($dropdown, $wrapper);
                return;
            }

            const requestId = ++this.currentRequestId;

            this.timeout = setTimeout(() => {
                this.xhr = $.ajax({
                    url:     ajax_url,
                    type:    'POST',
                    timeout: 5000,
                    data:    { action: 'ajax_search', query },

                    beforeSend: () => this.showLoading($dropdown, $wrapper),

                    success: (response) => {
                        if (requestId === this.currentRequestId) {
                            response ? this.showResults($dropdown, response)
                                : this.showNoResults($dropdown);
                        }
                        this.xhr = null;
                    },

                    error: (xhr, status) => {
                        if (requestId === this.currentRequestId && status !== 'abort') {
                            this.showError($dropdown, status === 'timeout'
                                ? 'Searching too long. Please try again.'
                                : 'An error occurred.');
                        }
                        this.xhr = null;
                    }
                });
            }, SEARCH_DELAY);
        },

        _setDropdown($dropdown, content, $wrapper, wrapperClass) {
            $dropdown.empty().addClass('active');
            if (content) $dropdown.html(content);
            if ($wrapper) $wrapper.toggleClass('has-results', !!wrapperClass);
        },

        showLoading($dropdown, $wrapper) {
            this._setDropdown($dropdown, '<div class="loading">Searching…</div>', $wrapper, true);
        },

        showResults($dropdown, html) {
            $dropdown.empty().html(html).addClass('active');
        },

        showNoResults($dropdown) {
            this._setDropdown($dropdown, '<div class="no-results">No results found.</div>');
        },

        showError($dropdown, message) {
            this._setDropdown($dropdown, $('<div class="error">').text(message)[0].outerHTML);
        },

        hideResults($dropdown, $wrapper) {
            $dropdown.removeClass('active').empty();
            $wrapper.removeClass('has-results');
        },

        destroy() {
            clearTimeout(this.timeout);
            abortXhr(this.xhr);
            Object.assign(this, { xhr: null, currentRequestId: 0 });
        }
    };

    const DesktopMenu = {
        init() {
            if ($(window).width() <= BREAKPOINT_DESKTOP) return;

            const $navItems        = $('.nav-item');
            const $submenuToggles  = $('.dropdown-submenu-toggle');
            const $dropdownLinks   = $('.dropdown-item.has-submenu .dropdown-link');

            $navItems.each((_, el) => {
                const $item = $(el);
                const $svg  = $item.find('.nav-link svg');
                if (!$svg.length) return;

                $svg.off('click').on('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    closeAllDropdowns('nav');
                    $navItems.not($item).removeClass('active');
                    $item.toggleClass('active');
                });

                $item.find('.nav-link').off('click').on('click', (e) => {
                    if ($(e.target).closest('svg').length) e.preventDefault();
                });
            });

            $submenuToggles.off('click').on('click', (e) => {
                if ($(window).width() <= BREAKPOINT_DESKTOP) return;
                e.preventDefault();
                e.stopPropagation();
                const $parent = $(e.currentTarget).closest('.dropdown-item.has-submenu');
                $('.dropdown-item.has-submenu').not($parent).removeClass('active');
                $parent.toggleClass('active');
            });

            $dropdownLinks.off('click').on('click', (e) => e.stopPropagation());
        }
    };

    const MobileMenu = {
        $menuBtn:      null,
        $closeBtn:     null,
        $overlay:      null,
        isInitialized: false,

        init() {
            this.$menuBtn  = $('#menu-btn');
            this.$closeBtn = $('#close-btn');
            if (!this.$menuBtn.length || !this.$closeBtn.length) return;

            this.$menuBtn.off('click').on('click',  (e) => this.openMobileMenu(e));
            this.$closeBtn.off('click').on('click', (e) => this.closeMobileMenu(e));
        },

        _getOverlay() {
            if (!$('#mobile-menu-overlay').length) {
                $('body').append('<div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>');
            }
            return $('#mobile-menu-overlay');
        },

        openMobileMenu(e) {
            if ($(window).width() > BREAKPOINT_DESKTOP) return;
            if (e) { e.preventDefault(); e.stopPropagation(); }

            this.$overlay = this._getOverlay().addClass('active');

            const scrollY = window.scrollY;
            document.body.dataset.scrollY = String(scrollY);
            document.body.style.top = `-${scrollY}px`;
            document.body.classList.add('mobile-menu-active');

            const $firstItem = $('.nav-item:has(.dropdown)').first();
            if ($firstItem.length) {
                $firstItem.find('.dropdown').first().addClass('show');
                $firstItem.find('.nav-link svg').first().addClass('rotate');
            }

            if (!this.isInitialized) {
                this._initMobileMenuEvents();
                this.isInitialized = true;
            }

            this.$overlay.off('click').on('click', (e) => this.closeMobileMenu(e));
        },

        closeMobileMenu(e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }

            this.$overlay = $('#mobile-menu-overlay');
            this.$overlay.removeClass('active');

            restoreBodyScroll();

            $('.dropdown.show, .submenu.show').removeClass('show');
            $('.nav-link svg.rotate, .dropdown-submenu-toggle.rotate').removeClass('rotate');
        },

        _initMobileMenuEvents() {
            $('.nav-item .nav-link svg').off('click.mobileMenu').on('click.mobileMenu', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const $svg      = $(e.currentTarget);
                const $dropdown = $svg.closest('.nav-item').find('.dropdown').first();
                if (!$dropdown.length) return;

                const wasOpen = $dropdown.hasClass('show');
                $('.dropdown').removeClass('show');
                $('.nav-link svg').removeClass('rotate');

                if (!wasOpen) {
                    $dropdown.addClass('show');
                    $svg.addClass('rotate');
                }
            });

            $('.dropdown-submenu-toggle').off('click.mobileMenu').on('click.mobileMenu', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const $btn     = $(e.currentTarget);
                const $submenu = $btn.closest('.dropdown-item.has-submenu').find('.submenu').first();
                if (!$submenu.length) return;

                const wasOpen = $submenu.hasClass('show');
                $('.submenu').removeClass('show');
                $('.dropdown-submenu-toggle').removeClass('rotate');

                if (!wasOpen) {
                    $submenu.addClass('show');
                    $btn.addClass('rotate');
                }
            });
        }
    };

    const resetMenuOnResize = () => {
        if ($(window).width() > BREAKPOINT_DESKTOP) {
            $('body').removeClass('mobile-menu-active');
            $('.dropdown.show, .submenu.show').removeClass('show');
            $('.nav-link svg.rotate, .dropdown-submenu-toggle.rotate').removeClass('rotate');
            $('.mobile-menu').css('display', '');
        } else {
            $('.nav-item').removeClass('active');
            $('.dropdown-item.has-submenu').removeClass('active');
        }
    };

    const ResizeHandler = (() => {
        let resizeTimer    = null;
        let isInitialized  = false;

        return {
            init() {
                if (isInitialized) return;

                $(window).on('resize.lbvResize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        const newViewport = $(window).width() > BREAKPOINT_DESKTOP ? 'desktop' : 'mobile';
                        if (newViewport !== currentViewport) {
                            currentViewport = newViewport;
                            resetMenuOnResize();
                            MobileMenu.init();
                            DesktopMenu.init();
                        }
                    }, RESIZE_DELAY);
                });

                isInitialized = true;
            },

            destroy() {
                $(window).off('resize.lbvResize');
                clearTimeout(resizeTimer);
                isInitialized = false;
            }
        };
    })();

    const UserHandler = {
        $loginModal:    null,
        $loginTriggers: null,

        init() {
            this.$loginModal    = $('#lbv-user-popup-form');
            this.$loginTriggers = $('#userBtn, .mobile-login-btn, .must-log-in a, .lbv-login, .lbv-register, .user-dropdown a, .lbv-edit-artist-btn');

            this._initSocialLogin();
            this._cleanLoginParam();
            this._initUserDropdown();
        },

        openModal() {
            if (this.$loginModal.hasClass('show')) return;
            this.$loginModal.css('display', 'flex').addClass('show');
            $('body').css('overflow', 'hidden');
        },

        closeModal() {
            this.$loginModal.removeClass('show').hide();
            $('body').css('overflow', '');
        },

        _initSocialLogin() {
            if (!this.$loginModal.length) return;

            this.$loginTriggers.on('click', (e) => {
                if (lbvMain.is_user_logged_in && $(e.currentTarget).attr('id') === 'userBtn') return;
                e.preventDefault();
                e.stopPropagation();
                this.openModal();
            });

            $('body').on('click', '.close-popup-btn, .login-modal-overlay', (e) => {
                e.preventDefault();
                this.closeModal();
            });

            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.$loginModal.hasClass('show')) this.closeModal();
            });

            $(document).on('click', '.oauth-link', () => {
                $('.oauth-link').css('pointer-events', 'none');
            });
        },

        _cleanLoginParam() {
            const status = new URLSearchParams(window.location.search).get('login');
            if ((status === 'success' || status === 'error') && window.history?.replaceState) {
                const clean = window.location.pathname +
                    window.location.search.replace(/[?&]login=(success|error)/, '').replace(/^&/, '?');
                window.history.replaceState({}, document.title, clean || window.location.pathname);
            }
        },

        _initUserDropdown() {
            const $userBtn       = $('#userBtn');
            const $userContainer = $userBtn.closest('.user-container');
            if (!$userBtn.length || !$userContainer.length) return;

            $userBtn.on('click', (e) => {
                if (!lbvMain.is_user_logged_in) return;
                e.stopPropagation();
                window.closeAllDropdowns('user');
                $userContainer.toggleClass('active');
            });
        }
    };

    const ThemeToggle = {
        init() {
            const theme = localStorage.getItem('theme') ||
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            const $html = $('html').attr('data-theme', theme);

            $('.dark-mode-toggle').off('click').on('click', () => {
                const next = $html.attr('data-theme') === 'dark' ? 'light' : 'dark';
                $html.attr('data-theme', next);
                localStorage.setItem('theme', next);
            });
        }
    };

    const ClickOutside = {
        init() {
            $(document).on('click', (e) => {
                const $t = $(e.target);

                if (!$t.closest('.search-container, .mobile-search').length) {
                    $('#searchBox').removeClass('active');
                    $('.search-dropdown').removeClass('active').empty();
                    $('.search-input-wrapper').removeClass('has-results');
                }
                if (!$t.closest('.user-container').length)          $('.user-container').removeClass('active');
                if (!$t.closest('.nav-item').length) {
                    $('.nav-item').removeClass('active');
                    $('.dropdown-item.has-submenu').removeClass('active');
                }
            });
        }
    };

    const PlaceholderTyping = {
        keywords:           [],
        $input:             null,
        keywordIndex:       0,
        charIndex:          0,
        typing:             true,
        placeholderTimeout: null,
        isPageVisible:      true,
        isRemoving:         false,
        visibilityHandler:  null,

        init() {
            this.$input = $('.search-page-input');
            if (!this.$input.length) return;

            this.keywords = $('.post-content.idol-content h3.post-title')
                .slice(0, 10).map(function() { return $(this).text().trim(); }).get();

            if (!this.keywords.length) return;

            this.$input
                .off('focus').on('focus', () => this._onFocus())
                .off('input').on('input',  () => this._onInput())
                .off('blur').on('blur',    () => this._onBlur());

            this.visibilityHandler = this._onVisibilityChange.bind(this);
            document.addEventListener('visibilitychange', this.visibilityHandler);

            this.typing = true;
            this._type();
        },

        _stop() {
            clearTimeout(this.placeholderTimeout);
            this.typing = false;
        },

        _onFocus() {
            this._stop();
            this.$input.attr('placeholder', '');
        },

        _onInput() {
            if (this.$input.val().length > 0) {
                this._stop();
                this.$input.attr('placeholder', '');
            }
        },

        _onBlur() {
            if (!this.$input.val().length && this.isPageVisible) {
                clearTimeout(this.placeholderTimeout);
                Object.assign(this, { typing: true, charIndex: 0, isRemoving: false });
                this._type();
            }
        },

        _onVisibilityChange() {
            this.isPageVisible = !document.hidden;

            if (this.isPageVisible && !this.$input.val().length) {
                if (this.typing) {
                    this.isRemoving ? this._erase() : this._type();
                } else {
                    Object.assign(this, { typing: true, isRemoving: false, charIndex: 0 });
                    this._type();
                }
            }
        },

        _guard() {
            return this.typing && this.keywords.length && this.isPageVisible && this.$input;
        },

        _type() {
            if (!this._guard()) return;
            this.isRemoving = false;

            const current = this.keywords[this.keywordIndex];
            this.$input.attr('placeholder', current.slice(0, this.charIndex));
            this.charIndex++;

            const delay = this.charIndex > current.length ? 1200 : 120;
            const next  = this.charIndex > current.length ? () => this._erase() : () => this._type();

            this.placeholderTimeout = setTimeout(() => {
                if (this.isPageVisible && this.typing) next();
            }, delay);
        },

        _erase() {
            if (!this._guard()) return;
            this.isRemoving = true;

            const current = this.keywords[this.keywordIndex];
            this.charIndex--;
            this.$input.attr('placeholder', current.slice(0, this.charIndex));

            if (this.charIndex > 0) {
                this.placeholderTimeout = setTimeout(() => {
                    if (this.isPageVisible && this.typing) this._erase();
                }, 45);
            } else {
                this.keywordIndex = (this.keywordIndex + 1) % this.keywords.length;
                this.placeholderTimeout = setTimeout(() => {
                    if (this.isPageVisible && this.typing) {
                        this.charIndex = 0;
                        this._type();
                    }
                }, 400);
            }
        },

        destroy() {
            this._stop();
            this.$input.off('focus input blur');
            if (this.visibilityHandler) {
                document.removeEventListener('visibilitychange', this.visibilityHandler);
            }
            Object.assign(this, { $input: null, keywords: [], visibilityHandler: null });
        }
    };

    const ScrollProgress = {
        $progressBar: null,
        ticking:      false,

        init() {
            $('body').append(
                $('<div>', { class: 'scroll-progress-container',
                    html: '<div class="scroll-progress-bar" id="scrollProgressBar"></div>' })
            );
            this.$progressBar = $('#scrollProgressBar');

            $(window).on('scroll', () => {
                if (this.ticking) return;
                window.requestAnimationFrame(() => this._update());
                this.ticking = true;
            });
        },

        _update() {
            const scrollTop  = $(window).scrollTop();
            const maxScroll  = $(document).height() - $(window).height();
            const pct        = Math.max(0, Math.min(100, (scrollTop / maxScroll) * 100));
            this.$progressBar.css('width', pct + '%');
            this.ticking = false;
        }
    };

    const State = {
        paged:       1,
        maxPages:    1,
        isLoading:   false,
        archiveId:   0,
        searchQuery: '',
        context:     'home',

        init() {
            const $btn = $('.load-more-btn');
            if (!$btn.length) return;

            this.paged       = parseInt($btn.data('page'))    || 1;
            this.maxPages    = parseInt($btn.data('max'))     || 1;
            this.archiveId   = parseInt($btn.data('archive')) || 0;
            this.searchQuery = $btn.data('search')            || '';
            this.context     = $btn.data('context')           || this._detectContext();
        },

        _detectContext() {
            const href = window.location.href;
            if (href.includes('/tag/'))  return 'tag';
            if (href.includes('?s='))    return 'search';
            if (document.body.classList.contains('home')) return 'home';
            return 'category';
        },

        resetPage()          { this.paged = 1; },
        incrementPage()      { this.paged++; },
        setLoading(loading)  { this.isLoading = loading; },
        canLoadMore()        { return !this.isLoading; }
    };

    const UI = {
        showLoadingButton($btn) {
            $btn.addClass('loading').prop('disabled', true);
            $btn.find('.btn-content').hide();
            $btn.find('.btn-loading').show();
        },

        hideLoadingButton($btn) {
            $btn.removeClass('loading').prop('disabled', false);
            $btn.find('.btn-loading').hide();
            $btn.find('.btn-content').show();
        },

        updateLoadButton(hasMore, $btn) {
            hasMore ? $btn.show() : $btn.fadeOut(300);
        },

        resetUI($btn) { this.hideLoadingButton($btn); }
    };

    const PostLoader = {
        _buildData(extra = {}) {
            const data = {
                paged:   State.paged,
                context: State.context,
                is_home: State.context === 'home' ? 1 : 0,
                ...extra
            };
            if (State.context === 'search') {
                data.search_query = State.searchQuery;
            } else {
                data.archive_id = State.archiveId;
            }
            return data;
        },

        _handleResponse(data, append, $btn, targetSelector) {
            if (!data?.posts) {
                UI.resetUI($btn);
                State.setLoading(false);
                return;
            }

            const $container = $(targetSelector || '#posts-container');
            if (append) {
                $container.append(data.posts);
            } else {
                $container.html(data.posts);
                State.resetPage();
            }

            if (data.notice) this._handleNotice(data.notice, append);
            UI.updateLoadButton(data.has_more === true, $btn);
            UI.resetUI($btn);
            State.setLoading(false);
        },

        _handleNotice(noticeHtml, append) {
            const $wrapper = $('.load-more-wrapper');
            $wrapper.find('.end-list').remove();

            const appendNotice = () => {
                $wrapper.append(noticeHtml);
                $wrapper.find('.end-list').hide().fadeIn(300);
            };

            if (append) {
                $wrapper.find('.load-more-btn').fadeOut(300, appendNotice);
            } else {
                appendNotice();
            }
        },

        load(options = {}) {
            const { append = true, $btn, targetSelector = '#posts-container', extraData = {} } = options;

            if (State.isLoading) return;
            State.setLoading(true);
            if ($btn) UI.showLoadingButton($btn);

            $.ajax({
                url:      lbvMain.rest_url + 'load-posts',
                type:     'GET',
                dataType: 'json',
                data:     this._buildData(extraData),
                success:  (response) => this._handleResponse(response, append, $btn, targetSelector),
                error:    (xhr) => {
                    if ($btn) UI.resetUI($btn);
                    State.setLoading(false);
                    console.error('Lỗi khi tải bài viết qua REST API:', xhr);
                }
            });
        }
    };

    const LoadMoreButton = {
        init() {
            $(document).on('click', '.load-more-btn', (e) => {
                e.preventDefault();
                if (!State.canLoadMore()) return;

                State.incrementPage();
                PostLoader.load({ append: true, $btn: $(e.currentTarget), targetSelector: '#posts-container' });
            });

            $(document).on('click', '.load-more-btn-post', (e) => {
                e.preventDefault();
                if (State.isLoading) return;

                const $btn  = $(e.currentTarget);
                const page  = (parseInt($btn.data('page'), 10) || 1) + 1;
                $btn.data('page', page);

                PostLoader.load({
                    append:       true,
                    $btn,
                    targetSelector: $btn.data('target') || '#posts-container-blog',
                    extraData:    { context: 'home', is_home: 1, paged: page, post_type: 'post' }
                });
            });
        }
    };

    const init = () => {
        currentViewport = $(window).width() > BREAKPOINT_DESKTOP ? 'desktop' : 'mobile';

        ScrollProgress.init();
        Search.init();
        ResizeHandler.init();
        StickyHeader.init();
        DesktopMenu.init();
        MobileMenu.init();
        UserHandler.init();
        ThemeToggle.init();
        ClickOutside.init();
        PlaceholderTyping.init();

        window.closeAllDropdowns = closeAllDropdowns;
    };

    $(function () {
        init();
        State.init();
        LoadMoreButton.init();
    });

})(jQuery);