(function($) {
    'use strict';

    const SEARCH_DELAY = 300;
    const MIN_SEARCH_LENGTH = 2;

    let currentViewport = 'desktop';
    let ajax_url = lbvMain.ajax_url;
    
    const StickyHeader = {
        lastScrollTop: 0,
        ticking: false,
        $header: null,
        scrollThreshold: 100,
        scrollHandler: null,

        init: function() {
            this.$header = $('.header');
            if (!this.$header.length) return;
            this.scrollHandler = this.handleScroll.bind(this);
            window.addEventListener('scroll', this.scrollHandler, { passive: true });
        },

        handleScroll: function() {
            if (!this.ticking) {
                requestAnimationFrame(() => {
                    const scrollTop = window.scrollY || document.documentElement.scrollTop;

                    if (scrollTop <= this.scrollThreshold) {
                        this.$header.removeClass('hidden');
                    } else if (scrollTop > this.lastScrollTop) {
                        this.$header.addClass('hidden');
                    } else {
                        this.$header.removeClass('hidden');
                    }

                    this.lastScrollTop = scrollTop;
                    this.ticking = false;
                });
                this.ticking = true;
            }
        },

        destroy: function() {
            if (this.scrollHandler) {
                window.removeEventListener('scroll', this.scrollHandler);
                this.scrollHandler = null;
            }
            this.ticking = false;
            this.lastScrollTop = 0;
            this.$header = null;
        }
    };

    const Search = {
        timeout: null,
        xhr: null,
        currentRequestId: 0,

        init: function () {
            this.initDesktopSearch();
            this.initDesktopSearchButton();
            this.initMobileSearch();
        },

        initDesktopSearch: function() {
            const $input = $('#searchInput');
            const $dropdown = $('#searchBox .search-dropdown');
            const $wrapper = $('#searchBox .search-input-wrapper');

            if (!$input.length) return;

            $input.on('input', (e) => {
                this.performSearch($(e.target).val().trim(), $dropdown, $wrapper);
            });
        },

        initDesktopSearchButton: function() {
            const $searchBtn = $('#searchBtn');
            const $searchBox = $('#searchBox');
            const $searchInput = $('#searchInput');

            if (!$searchBtn.length || !$searchBox.length) return;

            $searchBtn.off('click').on('click', (e) => {
                if ($(window).width() > 768) {
                    e.stopPropagation();
                    closeAllDropdowns('search');
                    $searchBox.toggleClass('active');

                    if ($searchBox.hasClass('active')) {
                        $searchInput.trigger('focus');
                    }
                } else {
                    MobileMenu.openMobileMenu(e);
                    $searchInput.trigger('focus');
                }
            });
        },

        initMobileSearch: function() {
            const $input = $('#mobileSearchInput');
            const $dropdown = $('.mobile-search .search-dropdown');
            const $wrapper = $('.mobile-search .search-input-wrapper');

            if (!$input.length) return;

            $input.on('input', (e) => {
                this.performSearch($(e.target).val().trim(), $dropdown, $wrapper);
            });
        },

        performSearch: function(query, $dropdown, $wrapper) {
            clearTimeout(this.timeout);

            if (this.xhr && this.xhr.readyState !== 4) {
                this.xhr.abort();
            }

            query = query.trim();
            if (query.length > 30) {
                query = query.substring(0, 30);
            }

            if (query.length >= MIN_SEARCH_LENGTH) {
                const requestId = ++this.currentRequestId;

                this.timeout = setTimeout(() => {
                    this.xhr = $.ajax({
                        url: ajax_url,
                        type: 'POST',
                        timeout: 5000,
                        data: {
                            action: 'ajax_search',
                            query: query
                        },
                        beforeSend: () => {
                            this.showLoading($dropdown, $wrapper);
                        },
                        success: (response) => {
                            if (requestId === this.currentRequestId) {
                                if (response) {
                                    this.showResults($dropdown, response);
                                } else {
                                    this.showNoResults($dropdown);
                                }
                            }
                            this.xhr = null;
                        },
                        error: (xhr, status) => {
                            if (requestId === this.currentRequestId) {
                                if (status === 'timeout') {
                                    this.showError($dropdown, 'Searching too long. Please try again.');
                                } else if (status !== 'abort') {
                                    this.showError($dropdown, 'An error occurred.');
                                }
                            }
                            this.xhr = null;
                        }
                    });
                }, SEARCH_DELAY);
            } else {
                this.hideResults($dropdown, $wrapper);
            }
        },

        showLoading: function($dropdown, $wrapper) {
            $dropdown.empty().addClass('active');
            $('<div class="loading">Searching…</div>').appendTo($dropdown);
            $wrapper.addClass('has-results');
        },

        showResults: function($dropdown, html) {
            $dropdown.empty().html(html).addClass('active');
        },

        showNoResults: function($dropdown) {
            $dropdown.empty().addClass('active');
            $('<div class="no-results">No results found.</div>').appendTo($dropdown);
        },

        showError: function($dropdown, message) {
            $dropdown.empty().addClass('active');
            const $error = $('<div class="error"></div>');
            $error.text(message);
            $error.appendTo($dropdown);
        },

        hideResults: function($dropdown, $wrapper) {
            $dropdown.removeClass('active').empty();
            $wrapper.removeClass('has-results');
        },

        destroy: function() {
            clearTimeout(this.timeout);
            if (this.xhr && this.xhr.readyState !== 4) {
                this.xhr.abort();
            }
            this.xhr = null;
            this.currentRequestId = 0;
        }
    };

    const ResizeHandler = (function() {
        let resizeTimer = null;
        let isInitialized = false;

        return {
            init: function() {
                if (isInitialized) {
                    return;
                }

                $(window).on('resize.lbvResize', function() {
                    if (resizeTimer) {
                        clearTimeout(resizeTimer);
                    }

                    resizeTimer = setTimeout(function() {
                        const windowWidth = $(window).width();
                        const newViewport = windowWidth > 1024 ? 'desktop' : 'mobile';

                        if (newViewport !== currentViewport) {
                            currentViewport = newViewport;
                            resetMenuOnResize();
                            MobileMenu.init();
                            DesktopMenu.init();
                        }
                    }, 250);
                });

                isInitialized = true;
            },

            destroy: function() {
                $(window).off('resize.lbvResize');
                if (resizeTimer) {
                    clearTimeout(resizeTimer);
                }
                isInitialized = false;
            }
        };
    })();

    const DesktopMenu = {
        $navItems: null,
        $submenuToggles: null,
        $dropdownLinks: null,

        init: function() {
            if ($(window).width() <= 1024) return;

            this.cacheSelectors();
            this.bindEvents();
        },

        cacheSelectors: function() {
            this.$navItems = $('.nav-item');
            this.$submenuToggles = $('.dropdown-submenu-toggle');
            this.$dropdownLinks = $('.dropdown-item.has-submenu .dropdown-link');
        },

        bindEvents: function() {
            this.$navItems.each((index, element) => {
                const $item = $(element);
                const $svg = $item.find('.nav-link svg');

                if ($svg.length) {
                    $svg.off('click').on('click', (e) => {
                        this.handleNavSvgClick(e, $item);
                    });

                    $item.find('.nav-link').off('click').on('click', (e) => {
                        this.handleNavLinkClick(e);
                    });
                }
            });

            this.$submenuToggles.off('click').on('click', (e) => {
                this.handleSubmenuToggleClick(e);
            });

            this.$dropdownLinks.off('click').on('click', (e) => {
                e.stopPropagation();
            });
        },

        handleNavSvgClick: function(e, $item) {
            e.preventDefault();
            e.stopPropagation();

            closeAllDropdowns('nav');
            this.$navItems.not($item).removeClass('active');
            $item.toggleClass('active');
        },

        handleNavLinkClick: function(e) {
            if ($(e.target).closest('svg').length) {
                e.preventDefault();
            }
        },

        handleSubmenuToggleClick: function(e) {
            if ($(window).width() <= 1024) return;

            e.preventDefault();
            e.stopPropagation();

            const $parent = $(e.currentTarget).closest('.dropdown-item.has-submenu');
            $('.dropdown-item.has-submenu').not($parent).removeClass('active');
            $parent.toggleClass('active');
        }
    };

    const MobileMenu = {
        $menuBtn: null,
        $closeBtn: null,
        $overlay: null,
        isInitialized: false,

        init: function() {
            this.$menuBtn = $('#menu-btn');
            this.$closeBtn = $('#close-btn');

            if (!this.$menuBtn.length || !this.$closeBtn.length) {
                return;
            }

            this.$menuBtn.off('click').on('click', (e) => {
                this.openMobileMenu(e);
            });

            this.$closeBtn.off('click').on('click', (e) => {
                this.closeMobileMenu(e);
            });
        },

        createOverlay: function() {
            let $overlay = $('#mobile-menu-overlay');
            if (!$overlay.length) {
                $('body').append('<div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>');
            }
            return $overlay;
        },

        openMobileMenu: function(e) {
            if ($(window).width() > 1024) return;

            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            this.$overlay = this.createOverlay();
            this.$overlay.addClass('active');

            const scrollY = window.scrollY;
            document.body.dataset.scrollY = String(scrollY);
            document.body.style.top = `-${scrollY}px`;
            document.body.classList.add('mobile-menu-active');

            const $firstNavItemWithDropdown = $('.nav-item:has(.dropdown)').first();
            if ($firstNavItemWithDropdown.length) {
                const $firstDropdown = $firstNavItemWithDropdown.find('.dropdown').first();
                const $firstSvg = $firstNavItemWithDropdown.find('.nav-link svg').first();

                $firstDropdown.addClass('show');
                $firstSvg.addClass('rotate');
            }

            if (!this.isInitialized) {
                this.initMobileMenuEvents();
                this.isInitialized = true;
            }

            this.$overlay.off('click').on('click', (e) => {
                this.closeMobileMenu(e);
            });
        },

        closeMobileMenu: function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            this.$overlay = $('#mobile-menu-overlay');
            this.$overlay.removeClass('active');

            const scrollY = document.body.dataset.scrollY || 0;
            document.body.classList.remove('mobile-menu-active');
            document.body.style.top = '';
            window.scrollTo(0, parseInt(scrollY));

            $('.dropdown.show, .submenu.show').removeClass('show');
            $('.nav-link svg.rotate, .dropdown-submenu-toggle.rotate').removeClass('rotate');
        },

        initMobileMenuEvents: function() {
            const $svgIcon = $('.nav-item .nav-link svg');
            const $toggle = $('.dropdown-submenu-toggle');

            $svgIcon.off('click.mobileMenu').on('click.mobileMenu', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const $svg = $(e.currentTarget);
                const $navItem = $svg.closest('.nav-item');
                const $dropdown = $navItem.find('.dropdown').first();

                if (!$dropdown.length) return;

                const wasOpen = $dropdown.hasClass('show');
                $('.dropdown').removeClass('show');
                $('.nav-link svg').removeClass('rotate');

                if (!wasOpen) {
                    $dropdown.addClass('show');
                    $svg.addClass('rotate');
                }
            });

            $toggle.off('click.mobileMenu').on('click.mobileMenu', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const $btn = $(e.currentTarget);
                const $currentItem = $btn.closest('.dropdown-item.has-submenu');
                const $submenu = $currentItem.find('.submenu').first();

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

    const UserHandler = {
        $loginModal: null,
        $loginTriggers: null,

        init: function() {
            this.$loginModal = $('#lbv-user-popup-form');
            this.$loginTriggers = $('#userBtn, .mobile-login-btn, .must-log-in a, .lbv-login, .lbv-register, .user-dropdown a, .lbv-edit-artist-btn');

            this.initSocialLogin();
            this.checkLoginSuccess();
            this.initUserDropdown();
        },

        openModal: function() {
            if (this.$loginModal.hasClass('show')) return;
            this.$loginModal.css('display', 'flex');
            this.$loginModal.addClass('show');
            $('body').css('overflow', 'hidden');
        },

        closeModal: function() {
            this.$loginModal.removeClass('show');
            this.$loginModal.hide();
            $('body').css('overflow', '');
        },

        initSocialLogin: function() {
            if (!this.$loginModal.length) return;

            this.$loginTriggers.on('click', (e) => {
                if (lbvMain.is_user_logged_in && $(e.currentTarget).attr('id') === 'userBtn') {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();
                this.openModal();
            });

            $('body').on('click', '.close-popup-btn, .login-modal-overlay', (e) => {
                e.preventDefault();
                this.closeModal();
            });

            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.$loginModal.hasClass('show')) {
                    this.closeModal();
                }
            });

            $(document).on('click', '.oauth-link', () => {
                $('.oauth-link').css('pointer-events', 'none');
            });
        },

        checkLoginSuccess: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const loginStatus = urlParams.get('login');

            if (loginStatus === 'success' || loginStatus === 'error') {
                if (window.history && window.history.replaceState) {
                    const cleanUrl = window.location.pathname + window.location.search.replace(/[?&]login=(success|error)/, '').replace(/^&/, '?');
                    window.history.replaceState({}, document.title, cleanUrl || window.location.pathname);
                }
            }
        },

        initUserDropdown: function() {
            const $userBtn = $('#userBtn');
            const $userContainer = $userBtn.closest('.user-container');

            if (!$userBtn.length || !$userContainer.length) return;

            $userBtn.on('click', (e) => {
                if (lbvMain.is_user_logged_in) {
                    e.stopPropagation();
                    window.closeAllDropdowns('user');
                    $userContainer.toggleClass('active');
                }
            });
        }
    };

    function closeAllDropdowns(except = null) {
        const actions = [];

        if (except !== 'search') {
            actions.push(
                ['#searchBox', 'removeClass', 'active'],
                ['.search-dropdown', 'removeClass', 'active'],
                ['.search-input-wrapper', 'removeClass', 'has-results']
            );
            $('.search-dropdown').empty();
        }

        if (except !== 'user') {
            actions.push(['.user-container', 'removeClass', 'active']);
        }

        if (except !== 'nav') {
            actions.push(
                ['.nav-item', 'removeClass', 'active'],
                ['.dropdown-item.has-submenu', 'removeClass', 'active']
            );
        }

        actions.forEach(([selector, method, className]) => {
            $(selector)[method](className);
        });
    }

    const ClickOutside = {
        init: function() {
            $(document).on('click', (e) => {
                this.handleClickOutside(e);
            });
        },

        handleClickOutside: function(e) {
            const $target = $(e.target);

            if (!$target.closest('.search-container, .mobile-search').length) {
                $('#searchBox').removeClass('active');
                $('.search-dropdown').removeClass('active').empty();
                $('.search-input-wrapper').removeClass('has-results');
            }

            if (!$target.closest('.user-container').length) {
                $('.user-container').removeClass('active');
            }

            if (!$target.closest('.nav-item').length) {
                $('.nav-item').removeClass('active');
                $('.dropdown-item.has-submenu').removeClass('active');
            }
        }
    };

    function resetMenuOnResize() {
        const $body = $('body');
        const windowWidth = $(window).width();

        if (windowWidth > 1024) {
            $body.removeClass('mobile-menu-active');
            $('.dropdown.show, .submenu.show').removeClass('show');
            $('.nav-link svg.rotate, .dropdown-submenu-toggle.rotate').removeClass('rotate');
            $('.mobile-menu').css('display', '');
        } else {
            $('.nav-item').removeClass('active');
            $('.dropdown-item.has-submenu').removeClass('active');
        }
    }

    const ThemeToggle = {
        init: function() {
            const currentTheme = localStorage.getItem('theme') || 'light';
            const $html = $('html');
            $html.attr('data-theme', currentTheme);

            $('.dark-mode-toggle').off('click').on('click', () => {
                this.handleToggle($html);
            });
        },

        handleToggle: function($html) {
            const currentTheme = $html.attr('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            $html.attr('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
    };

    const ScrollProgress = {
        $progressBar: null,
        ticking: false,

        init: function () {
            this.createElements();
            this.bindEvents();
        },

        createElements: function () {
            const $container = $('<div>', {
                class: 'scroll-progress-container',
                html: '<div class="scroll-progress-bar" id="scrollProgressBar"></div>'
            });

            $('body').append($container);
            this.$progressBar = $('#scrollProgressBar');
        },

        bindEvents: function () {
            const self = this;

            $(window).on('scroll', function () {
                if (!self.ticking) {
                    window.requestAnimationFrame(function () {
                        self.updateProgress();
                    });
                    self.ticking = true;
                }
            });
        },

        updateProgress: function () {
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            const scrollTop = $(window).scrollTop();
            const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;

            this.$progressBar.css('width', Math.max(0, Math.min(100, scrollPercent)) + '%');
            this.ticking = false;
        }
    };

    function init() {
        ScrollProgress.init();
        Search.init();
        ResizeHandler.init();
        StickyHeader.init();
        DesktopMenu.init();
        MobileMenu.init();
        UserHandler.init();
        ThemeToggle.init();
        ClickOutside.init();
        currentViewport = $(window).width() > 1024 ? 'desktop' : 'mobile';

        window.closeAllDropdowns = closeAllDropdowns;
    }

    $(function() {
        init();
    });

})(jQuery);
