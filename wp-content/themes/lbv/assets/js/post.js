(function ($) {
    'use strict';

    let ajax_url = lbvPost.ajax_url;
    
    const StickySidebar = {
        $sidebar: null,
        lastScrollTop: 0,

        init: function () {
            this.$sidebar = $('.sidebar-area');
            if (!this.$sidebar.length) return;

            this.bindEvents();
        },

        isDesktop: function () {
            return window.matchMedia('(min-width: 1025px)').matches;
        },

        handleScroll: function () {
            if (!StickySidebar.isDesktop()) return;

            const scrollTop = $(window).scrollTop();
            const topPosition = (scrollTop > 50 && scrollTop > StickySidebar.lastScrollTop) ? '20px' : '100px';

            StickySidebar.$sidebar.css('top', topPosition);
            StickySidebar.lastScrollTop = scrollTop;
        },

        handleResize: function () {
            if (!StickySidebar.isDesktop()) {
                StickySidebar.$sidebar.css('top', '');
            }
        },

        bindEvents: function () {
            $(window).on('scroll', {passive: true}, this.handleScroll);
            $(window).on('resize', {passive: true}, this.handleResize);
        }
    };

    const ImageGallery = {
        initialDesktopItems: 6,
        initialMobileItems: 4,

        init: function (container, imageSelector) {
            const $container = $(container);
            if (!$container.length) return;

            const magnificItems = this.buildImageItems(container, imageSelector);
            this.bindClickEvent($container, imageSelector, magnificItems);
            this.initLoadMore();
        },

        buildImageItems: function (container, imageSelector) {
            const items = [];

            $(container + ' ' + imageSelector).each(function () {
                const imgSrc = $(this).attr('data-src') || $(this).attr('src');
                const modifiedImgSrc = imgSrc.replace(/-\d+x\d+/, '');

                items.push({
                    src: modifiedImgSrc,
                    title: '',
                    type: 'image'
                });
            });

            return items;
        },

        bindClickEvent: function ($container, imageSelector, magnificItems) {
            $container.on('click', imageSelector, function () {
                const index = $container.find(imageSelector).index(this);
                const currentItems = magnificItems.slice(index).concat(magnificItems.slice(0, index));

                $.magnificPopup.open({
                    items: currentItems,
                    type: 'image',
                    tLoading: 'Loading image #%curr%...',
                    mainClass: 'mfp-img-mobile',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                    },
                    image: {
                        tError: 'could not be loaded.'
                    },
                    index: 0
                });
            });
        },

        initLoadMore: function () {
            const self = this;
            const galleries = document.querySelectorAll('.wp-block-gallery.has-nested-images');

            galleries.forEach(gallery => {
                const figures = Array.from(gallery.querySelectorAll(':scope > figure.wp-block-image, :scope > .wp-block-gallery__wrapper > figure.wp-block-image'));

                if (figures.length === 0) return;

                function isMobile() {
                    return window.innerWidth <= 782;
                }

                // Kiểm tra xem có cần load more không
                const limit = isMobile() ? self.initialMobileItems : self.initialDesktopItems;
                if (figures.length <= limit) return;

                const loadMoreBtn = document.createElement('button');
                loadMoreBtn.className = 'gallery-load-more';
                const lang = document.documentElement.lang;
                loadMoreBtn.textContent = (lang === 'vi-VN') ? 'Tải thêm' : 'Load More';


                loadMoreBtn.addEventListener('mouseenter', () => {
                    loadMoreBtn.style.opacity = '0.8';
                });

                loadMoreBtn.addEventListener('mouseleave', () => {
                    loadMoreBtn.style.opacity = '1';
                });

                function hideExtraImages() {
                    const limit = isMobile() ? self.initialMobileItems : self.initialDesktopItems;

                    figures.forEach((fig, index) => {
                        if (index >= limit) {
                            fig.style.display = 'none';
                        } else {
                            fig.style.display = 'block';
                        }
                    });

                    if (figures.length > limit) {
                        if (!gallery.nextElementSibling || !gallery.nextElementSibling.classList.contains('gallery-load-more')) {
                            gallery.parentNode.insertBefore(loadMoreBtn, gallery.nextSibling);
                        }
                        loadMoreBtn.style.display = 'block';
                    } else {
                        loadMoreBtn.style.display = 'none';
                    }
                }

                loadMoreBtn.addEventListener('click', () => {
                    figures.forEach(fig => {
                        fig.style.display = 'block';
                    });
                    loadMoreBtn.style.display = 'none';
                });

                hideExtraImages();

                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        if (loadMoreBtn.style.display !== 'none') {
                            hideExtraImages();
                        }
                    }, 250);
                });
            });
        }
    };

    const FAQRankMath = {
        init: function () {
            const $faq = $('.rank-math-question');
            if (!$faq.length) return;

            this.bindEvents($faq);
        },

        bindEvents: function ($faq) {
            $faq.on('click', function () {
                const $item = $(this).closest('.rank-math-list-item');
                const $answer = $item.find('.rank-math-answer');

                $('.rank-math-list-item')
                    .not($item)
                    .removeClass('active')
                    .find('.rank-math-answer')
                    .slideUp();

                $item.toggleClass('active');
                $answer.slideToggle();
            });
        }
    };

    const CopyProtection = {
        init: function () {
            if (document.getElementById('wpadminbar')) return;

            this.initCopyEvent();
            this.initCodeCopyButtons();
        },

        initCopyEvent: function () {
            $(document).on('copy', function (event) {
                const target = event.target;

                if (target && (target.closest('.wp-block-code') || target.closest('#comments')) || lbvPost.is_admin) {
                    return;
                }

                const copiedText = window.getSelection().toString();

                if (copiedText.length > 0) {
                    event.preventDefault();
                    const copiedData = copiedText + ' (Source cited from ' + document.URL + ')';
                    event.originalEvent.clipboardData.setData('text/plain', copiedData);
                }
            });
        },

        initCodeCopyButtons: function () {
            $('pre.wp-block-code').each(function () {
                const $pre = $(this);
                $pre.css('position', 'relative');

                $pre.one('mouseenter', function () {
                    const $btn = $('<button>', {
                        class: 'copy-btn',
                        text: 'Copy',
                        css: {
                            position: 'absolute',
                            top: '5px',
                            right: '5px',
                            zIndex: 10
                        }
                    });

                    $pre.append($btn);

                    $btn.on('click', function () {
                        let allText = '';

                        $pre.find('code').each(function () {
                            allText += $(this).text() + '\n';
                        });

                        allText = allText.trim();

                        navigator.clipboard.writeText(allText).then(function () {
                            $btn.text('Copied!');
                            setTimeout(function () {
                                $btn.text('Copy');
                            }, 1500);
                        });
                    });
                });
            });
        }
    };

    const SidebarPopular = {
        isLoading: false,
        isInitialized: false,

        init: function () {
            if (this.isInitialized) return;
            this.isInitialized = true;
            this.bindEvents();
        },

        bindEvents: function () {
            const self = this;

            $(document).off('click.popularPosts').on('click.popularPosts', '.prev-popular, .next-popular', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                if (self.isLoading) return;
                self.handleNavigation(e);
            });
        },

        handleNavigation: function (e) {
            const self = this;
            const $nav = $('.popular-posts-nav');
            if (!$nav.length) return;

            const currentPage = parseInt($nav.attr('data-current-page')) || 1;
            const maxPages = parseInt($nav.attr('data-max-pages')) || 1;
            const referencePostId = $nav.attr('data-reference-post-id') || self.getFirstPostId();
            const isPrev = $(e.currentTarget).hasClass('prev-popular');
            const targetPage = isPrev ? currentPage - 1 : currentPage + 1;

            if (targetPage < 1 || targetPage > maxPages) return;

            self.loadPosts(targetPage, referencePostId, $nav);
        },

        loadPosts: function (targetPage, referencePostId, $nav) {
            const self = this;
            self.isLoading = true;

            const $container = $('#popular-posts-container');
            const $buttons = $('.prev-popular, .next-popular');
            const postType = $nav.attr('data-post-type') || self.getCurrentPostType();
            const is_profile = $nav.attr('data-profile');

            let currentLang = document.documentElement.lang || '';
            if (currentLang.indexOf('-') > -1) {
                currentLang = currentLang.split('-')[0];
            }

            $buttons.prop('disabled', true);
            $container.css('opacity', '0.6');

            const restUrl = lbvPost.rest_url + 'popular-posts';

            $.ajax({
                url: restUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    page: targetPage,
                    reference_post_id: referencePostId,
                    post_type: postType,
                    is_profile: is_profile,
                    lang: currentLang,
                    timestamp: new Date().getTime()
                },
                success: function (response) {
                    if (response && response.html) {
                        $container.html(response.html);

                        $nav.attr({
                            'data-current-page': response.current_page,
                            'data-max-pages': response.max_pages
                        });

                        if (response.reference_post_id) {
                            $nav.attr('data-reference-post-id', response.reference_post_id);
                        }

                        self.updateButtons(response.current_page, response.max_pages);
                    }
                },
                error: function (xhr) {
                    $buttons.prop('disabled', false);
                },
                complete: function () {
                    $container.css('opacity', '1');
                    self.isLoading = false;
                }
            });
        },

        getCurrentPostType: function() {
            const bodyClasses = $('body').attr('class');
            const match = bodyClasses.match(/single-(\w+)/);
            if (match) return match[1];

            return window.current_post_type || 'post';
        },

        updateButtons: function (currentPage, maxPages) {
            $('.prev-popular').prop('disabled', currentPage <= 1);
            $('.next-popular').prop('disabled', currentPage >= maxPages);
        },

        getFirstPostId: function () {
            const $firstPost = $('#popular-posts-container .popular-post-item').first();
            return $firstPost.length ? $firstPost.data('post-id') : 0;
        }
    };

    const ScrollToTop = {
        $button: null,
        isScrolling: false,

        init: function () {
            this.createButton();
            this.bindEvents();
        },

        createButton: function () {
            const $button = $('<a>', {
                id: 'scrollToTop',
                href: '#site-header',
                class: 'scroll-to-top',
                'aria-label': 'Scroll to top',
                html: `
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15"></polyline>
                        <polyline points="18 20 12 14 6 20"></polyline>
                    </svg>
                `
            });

            $('body').append($button);
            this.$button = $button;
        },

        bindEvents: function () {
            const self = this;

            $(window).on('scroll', function () {
                self.toggleVisibility();
            });

            this.$button.on('click', function (e) {
                e.preventDefault();
                self.scrollToTop();
            });
        },

        toggleVisibility: function () {
            const scrollTop = $(window).scrollTop();

            if (scrollTop > 300) {
                this.$button.addClass('show');
            } else {
                this.$button.removeClass('show');
            }
        },

        scrollToTop: function () {
            if (this.isScrolling) return;

            this.isScrolling = true;
            const self = this;

            $('html, body').animate({
                scrollTop: 0
            }, 50, function () {
                self.isScrolling = false;
            });
        }
    };

    const TableOfContent = {
        content: null,
        headings: [],

        init: function () {
            this.content = document.querySelector(".entry-content");
            this.headings = this.content ? this.content.querySelectorAll("h2, h3, h4") : [];

            if (this.headings.length > 0) {
                this.handleTOC();
            }
        },

        handleTOC: function () {
            const self = this;

            const overlay = document.getElementById("toc-overlay");
            const closeBtn = document.querySelector(".toc-close");
            const toc = document.getElementById("table-of-content");
            const grid = document.querySelector(".container");
            const toggleIcon = document.getElementById("toc-toggle-icon");
            const tocList = document.getElementById("toc-list");

            if (!toc || !grid) return;

            document.querySelectorAll('.toc-wrapper').forEach(function (el) {
                el.removeAttribute('style');
            });

            let rAF;
            const withRAF = (fn) => {
                if (rAF) cancelAnimationFrame(rAF);
                rAF = requestAnimationFrame(fn);
            };

            const getHeaderHeight = () => {
                const header = document.querySelector("header");
                return header ? header.getBoundingClientRect().height : 60;
            };

            function getAllMeasurements() {
                const gridRect = grid.getBoundingClientRect();
                const tocRect = toc.getBoundingClientRect();
                const toggleIconRect = toggleIcon ? toggleIcon.getBoundingClientRect() : {width: 40};
                const headerHeight = getHeaderHeight();
                const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
                const windowWidth = window.innerWidth;

                return {
                    gridRect,
                    tocRect,
                    toggleIconRect,
                    headerHeight,
                    scrollTop,
                    windowWidth
                };
            }

            function updateTOCPosition(measurements) {
                const {gridRect, tocRect, windowWidth} = measurements;

                if (windowWidth > 768) {
                    const tocLeftPosition = gridRect.left - tocRect.width;
                    toc.style.left = `${Math.max(20, tocLeftPosition)}px`;
                } else {
                    toc.style.left = '0';
                }
            }

            function updateToggleButton(measurements) {
                const {gridRect, toggleIconRect, windowWidth} = measurements;
                const toggleWrapper = document.querySelector(".toc-toggle-wrapper");

                if (toggleWrapper && windowWidth > 768) {
                    const leftPosition = gridRect.left - toggleIconRect.width;
                    toggleWrapper.style.left = `${Math.max(20, leftPosition)}px`;
                }

                if (!toggleIcon) return;

                if (self.headings.length > 0) {
                    toggleIcon.style.display = "block";
                    toggleIcon.style.visibility = "visible";
                    toggleIcon.style.opacity = toc.classList.contains("visible") ? "0" : "1";
                    toggleIcon.style.transition = "opacity 0.3s ease-in-out, visibility 0.3s ease-in-out";
                } else {
                    toggleIcon.style.display = "none";
                    toggleIcon.style.visibility = "hidden";
                    toggleIcon.style.opacity = "0";
                }
            }

            function updateAllLayout() {
                const measurements = getAllMeasurements();
                updateTOCPosition(measurements);
                updateToggleButton(measurements);
            }

            function showTOC() {
                const measurements = getAllMeasurements();
                const {scrollTop} = measurements;

                toc.classList.add("visible");

                if (window.innerWidth > 768) {
                    if (scrollTop > 50) {
                        toc.style.top = '60px';
                    } else {
                        toc.style.top = '100px';
                    }
                }

                if (toggleIcon) {
                    toggleIcon.style.visibility = "hidden";
                    toggleIcon.style.opacity = "0";
                }

                updateTOCPosition(measurements);

                const {gridRect} = measurements;
                if (gridRect.left < 250 && overlay) {
                    overlay.classList.add("visible");
                    overlay.style.visibility = "visible";
                    overlay.style.opacity = "1";
                }

                updateToggleButton(measurements);
            }

            function hideTOC() {
                const measurements = getAllMeasurements();

                toc.classList.remove("visible");
                toc.style.left = "-400px";

                if (overlay) {
                    overlay.classList.remove("visible");
                    overlay.style.visibility = "hidden";
                    overlay.style.opacity = "0";
                }

                if (toggleIcon) {
                    toggleIcon.style.display = "block";
                    toggleIcon.style.visibility = "visible";
                    toggleIcon.style.opacity = "1";
                }

                updateToggleButton(measurements);
            }

            let isManualScroll = false;

            if (tocList) {
                tocList.addEventListener("click", (e) => {
                    const a = e.target.closest("a");
                    if (!a) return;

                    isManualScroll = true;
                    tocList.querySelectorAll("a.active").forEach(el => el.classList.remove("active"));
                    a.classList.add("active");
                    setTimeout(() => {
                        isManualScroll = false;
                    }, 1000);

                    if (window.innerWidth <= 768) hideTOC();
                });
            }

            if (tocList) {
                tocList.addEventListener("click", (e) => {
                    const toggleBtn = e.target.closest(".toc-toggle");
                    if (!toggleBtn) return;

                    e.preventDefault();
                    e.stopPropagation();

                    const li = toggleBtn.closest(".oil-level-2");
                    if (li) {
                        li.classList.toggle("collapsed");
                    }
                });
            }

            const observer = new IntersectionObserver((entries) => {
                if (isManualScroll) return;
                entries.forEach(entry => {
                    const id = entry.target.getAttribute("id");
                    if (entry.isIntersecting && id && tocList) {
                        tocList.querySelectorAll("a.active").forEach(el => el.classList.remove("active"));
                        const link = tocList.querySelector(`a[href="#${CSS.escape(id)}"]`);
                        if (link) link.classList.add("active");
                    }
                });
            }, {rootMargin: "0px 0px -50% 0px", threshold: 0});

            Array.prototype.forEach.call(self.headings, (h) => {
                if (!h.id) {
                    h.id = (h.textContent || "section")
                        .trim().toLowerCase()
                        .replace(/\s+/g, "-")
                        .replace(/[^a-z0-9\-]/g, "");
                }
                observer.observe(h);
            });

            if (toggleIcon) {
                toggleIcon.addEventListener("click", () => {
                    const isHidden = !toc.classList.contains("visible");
                    if (isHidden) showTOC(); else hideTOC();
                });
            }

            if (overlay) overlay.addEventListener("click", hideTOC);
            if (closeBtn) closeBtn.addEventListener("click", hideTOC);

            window.addEventListener("load", () => {
                withRAF(updateAllLayout);
            });

            window.addEventListener("resize", () => {
                withRAF(updateAllLayout);
            });

            window.addEventListener("scroll", () => {
                withRAF(handleScroll);
            });

            let lastScrollTop = 0;

            function handleScroll() {
                const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;

                if (Math.abs(scrollTop - lastScrollTop) < 5) return;

                if (window.innerWidth > 768) {
                    const footer = document.querySelector('footer');

                    if (footer) {
                        const footerRect = footer.getBoundingClientRect();
                        const tocRect = toc.getBoundingClientRect();
                        const windowHeight = window.innerHeight;

                        const maxTocBottom = footerRect.top - 50;
                        const tocHeight = tocRect.height;

                        const maxAllowedTop = maxTocBottom - tocHeight;

                        let desiredTop;
                        if (scrollTop > 50 && scrollTop > lastScrollTop) {
                            desiredTop = 60;
                        } else {
                            desiredTop = 100;
                        }

                        if (footerRect.top < windowHeight) {
                            toc.style.top = `${Math.min(desiredTop, maxAllowedTop)}px`;
                        } else {
                            toc.style.top = `${desiredTop}px`;
                        }
                    } else {
                        if (scrollTop > 50 && scrollTop > lastScrollTop) {
                            toc.style.top = '60px';
                        } else {
                            toc.style.top = '100px';
                        }
                    }
                }

                lastScrollTop = scrollTop;
                updateAllLayout();
            }
        }
    };

    const PostComment = {
        config: {
            minLength: 3,
            maxLength: 300,
            nameMinLength: 2,
            nameMaxLength: 50,
            emailRegex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(?![0-9]+$)[a-zA-Z]{2,}$/,
            scrollOffset: 100,
            scrollDuration: 300,
            loginTrigger: '.user-dropdown .user-dropdown-item:first',
        },

        selectors: {
            commentForm: '#commentform',
            commentField: '#comment',
            submitButton: 'input#submit',
            replyLink: '.comment-reply-link',
            editButton: '.edit-comment-btn',
            loadMoreButton: '#load-more-comments',
            commentList: '.comment-list',
            commentError: '.comment_error',
            replyTitle: '#reply-title',
            respond: '#respond',
            comments: '#comments',
            loggedInAs: '.logged-in-as',
        },

        nonce: '',

        init: function () {
            this.bindEvents();
            this.initScrollToComment();
            this.initNofollowLinks();
            this.initLoginTrigger();
        },

        fetchNonce: function() {
            const self = this;
            return $.ajax({
                url: ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'lbv_get_comment_nonce'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        self.nonce = response.data.nonce;
                    }
                }
            });
        },

        initLoginTrigger: function() {
            if (!lbvPost.is_logged_in) {
                const self = this;

                $(this.selectors.commentField + ', ' + this.selectors.submitButton).on('click focus', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const $loginTrigger = $(self.config.loginTrigger);
                    if ($loginTrigger.length) {
                        $loginTrigger.trigger('click');
                    }

                    $(this).blur();
                    return false;
                });
            }
        },

        bindEvents: function () {
            $(document)
                .on('click', this.selectors.replyLink, this.handleReply.bind(this))
                .on('click', this.selectors.editButton, this.handleEdit.bind(this))
                .on('click', this.selectors.submitButton, this.handleSubmit.bind(this))
                .on('click', this.selectors.loadMoreButton, this.handleLoadMore.bind(this))
                .on('input', this.selectors.commentField, this.handleInput.bind(this));
        },

        handleReply: function (e) {
            e.preventDefault();

            const $link = $(e.currentTarget);
            const commentId = $link.data('commentid');
            const $respond = $(this.selectors.respond);
            const $comment = $('#comment-' + commentId);

            $('#comment_parent').val(commentId);
            $respond.removeAttr('data-editing data-edit-comment-id');
            $(this.selectors.commentField).val('');

            $(this.selectors.replyTitle).html(
                'Reply <small><a rel="nofollow" id="cancel-comment-reply-link" href="#respond">Cancel</a></small>'
            );

            $comment.after($respond);
            this.scrollTo($respond);
            $(this.selectors.commentField).focus();

            $('#cancel-comment-reply-link').off('click').on('click', (e) => {
                e.preventDefault();
                this.cancelReply();
            });
        },

        handleEdit: function (e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const commentId = $button.data('commentid');
            const isEditing = $button.data('editing');
            const $comment = $('#comment-' + commentId);
            const $form = $(this.selectors.respond);

            const rawHtml = $comment.find('.comment-content p').html();
            const content = rawHtml ? rawHtml.split('<a')[0].trim() : '';

            $(this.selectors.commentField).val(content);
            $('#comment_ID').val(commentId);
            $('#comment_parent').val(0);

            $comment.after($form);
            $form.attr({
                'data-editing': isEditing,
                'data-edit-comment-id': commentId
            });

            $(this.selectors.replyTitle).html(
                'Edit comment <small><a rel="nofollow" id="cancel-comment-edit-link" href="#respond">Cancel</a></small>'
            );

            this.scrollTo($form);
            $(this.selectors.commentField).focus();

            $('#cancel-comment-edit-link').off('click').on('click', (e) => {
                e.preventDefault();
                this.cancelEdit();
            });
        },

        cancelEdit: function () {
            const $respond = $(this.selectors.respond);
            const $commentList = $(this.selectors.commentList);

            $('#comment_parent').val('0');
            $('#comment_ID').val('');
            $(this.selectors.commentField).val('');

            $respond.removeAttr('data-editing data-edit-comment-id');
            $(this.selectors.replyTitle).html('Write a comment');

            if ($commentList.length) {
                $commentList.after($respond);
            } else {
                $(this.selectors.comments).append($respond);
            }

            this.scrollTo($respond);
        },

        cancelReply: function () {
            const $respond = $(this.selectors.respond);
            const $loadMoreBtn = $(this.selectors.loadMoreButton);

            $('#comment_parent').val('0');
            $(this.selectors.commentField).val('');
            $respond.removeAttr('data-editing data-edit-comment-id');
            $(this.selectors.replyTitle).html('Write a comment');

            if ($loadMoreBtn.length) {
                $loadMoreBtn.after($respond);
            } else {
                const $lbvComments = $('.lbv-comments');
                if ($lbvComments.length) {
                    $lbvComments.after($respond);
                } else {
                    $(this.selectors.comments).append($respond);
                }
            }

            this.scrollTo($respond);
        },

        handleInput: function (e) {
            const $field = $(e.currentTarget);
            const length = $field.val().length;
            const {minLength, maxLength} = this.config;

            if (length >= minLength && length <= maxLength) {
                $field.css('border', '');
            }
        },

        handleSubmit: function (e) {
            e.preventDefault();

            if (!this.nonce) {
                this.fetchNonce().then(() => {
                    this.handleSubmit(e);
                });
                return false;
            }

            const $commentField = $(this.selectors.commentField);
            const $replyTitle = $(this.selectors.replyTitle);
            const textareaValue = $commentField.val().trim();

            this.removeErrors();

            if (!this.validateComment(textareaValue, $commentField, $replyTitle)) {
                return false;
            }

            if (!lbvPost.is_logged_in && !this.validateGuestUser($replyTitle)) {
                return false;
            }

            this.submitComment($(e.currentTarget));
            return false;
        },

        validateComment: function (value, $field, $replyTitle) {
            const {minLength, maxLength} = this.config;

            if (!value) {
                this.showError($field, $replyTitle, 'Please enter a comment.');
                return false;
            }

            if (value.length < minLength) {
                this.showError($field, $replyTitle, `Comment is too short (minimum ${minLength} characters).`);
                return false;
            }

            if (value.length > maxLength) {
                this.showError($field, $replyTitle, `Comment is too long (maximum ${maxLength} characters).`);
                return false;
            }

            return true;
        },

        validateGuestUser: function ($replyTitle) {
            const authorVal = $.trim($('#author').val());
            const emailVal = $.trim($('#email').val());
            const {nameMinLength, nameMaxLength, emailRegex} = this.config;

            if (!authorVal) {
                this.showError(null, $replyTitle, 'Please enter your name.');
                return false;
            }

            if (authorVal.length < nameMinLength) {
                this.showError(null, $replyTitle, 'Name is too short.');
                return false;
            }

            if (authorVal.length > nameMaxLength) {
                this.showError(null, $replyTitle, 'Name is too long.');
                return false;
            }

            if (!emailVal) {
                this.showError(null, $replyTitle, 'Please enter your email.');
                return false;
            }

            if (!emailRegex.test(emailVal)) {
                this.showError(null, $replyTitle, 'Invalid email address.');
                return false;
            }

            return true;
        },

        submitComment: function ($submitBtn) {
            const self = this;
            const $form = $(this.selectors.commentForm);
            const parentId = $form.find('#comment_parent').val();
            const $respond = $(this.selectors.respond);
            const isEditing = $respond.attr('data-editing') === '1';
            const tempId = 'temp-' + Date.now();
            const commentContent = $form.find('#comment').val();

            $submitBtn.prop('disabled', true).val('Sending...');

            let $tempComment = null;
            let $editingComment = null;

            if (isEditing) {
                const editCommentId = $respond.attr('data-edit-comment-id');
                $editingComment = $('#comment-' + editCommentId);

                if ($editingComment.length) {
                    $editingComment.data('original-content', $editingComment.find('.comment-content').html());
                    $editingComment.find('.comment-content').html(this.formatCommentContent(commentContent));
                    $editingComment.addClass('updating');
                }
            } else {
                $tempComment = this.createTempComment(tempId, commentContent, parentId);

                if (parentId && parentId !== '0') {
                    $('#comment-' + parentId).after($tempComment);
                } else {
                    const $commentList = $(this.selectors.commentList);

                    if ($commentList.length) {
                        $commentList.prepend($tempComment);
                    } else {
                        let $lbvComments = $('.lbv-comments');

                        if (!$lbvComments.length) {
                            $lbvComments = $('<div class="lbv-comments"><div class="comments-title">1 comment</div><ul class="comment-list"></ul></div>');
                            $respond.before($lbvComments);
                        }

                        $lbvComments.find('.comment-list').prepend($tempComment);
                    }
                }

                if ($tempComment && $tempComment.length) {
                    $('html, body').animate({
                        scrollTop: $tempComment.offset().top - this.config.scrollOffset
                    }, this.config.scrollDuration);
                }
            }

            const formData = $form.serialize() +
                '&action=lbv_comment' +
                '&nonce=' + self.nonce +
                '&data_editing=' + (isEditing ? '1' : '0') +
                '&edit_comment_id=' + ($respond.attr('data-edit-comment-id') || '0') +
                '&temp_id=' + tempId;

            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: (response) => {
                    if (response.success === false) {
                        if (isEditing) {
                            self.handleEditError({responseJSON: response}, $editingComment);
                        } else {
                            self.handleNewCommentError({responseJSON: response}, $tempComment);
                        }
                        return;
                    }

                    if (isEditing) {
                        self.handleEditSuccess(response, $editingComment);
                    } else {
                        self.handleNewCommentSuccess(response, $tempComment, tempId);
                    }
                },
                error: (jqXHR) => {
                    if (jqXHR.responseJSON?.data === 'Security check failed.') {
                        self.fetchNonce().then(() => {
                            setTimeout(() => self.submitComment($submitBtn), 100);
                        });
                    } else {
                        if (isEditing) {
                            self.handleEditError(jqXHR, $editingComment);
                        } else {
                            self.handleNewCommentError(jqXHR, $tempComment);
                        }
                    }
                },
                complete: () => {
                    $submitBtn.prop('disabled', false).val('Post comment');
                }
            });
        },

        createTempComment: function(tempId, content, parentId) {
            const user = lbvPost.current_user || {
                name: 'Guest',
                avatar: 'https://secure.gravatar.com/avatar/?s=32&d=mm',
                id: 0
            };

            const depthClass = parentId && parentId !== '0' ? 'depth-2' : 'depth-1';
            const parentClass = !parentId || parentId === '0' ? 'parent' : '';

            const html = `
        <li id="temp-comment-${tempId}" class="comment ${depthClass} ${parentClass} temp-comment pending-comment" data-temp-id="${tempId}">
            <div class="comment-body">
                <div class="comment-author vcard">
                    <img src="${user.avatar}" alt="${user.name}" class="avatar avatar-32" width="32" height="32">
                </div>
                <div class="comment-content-wrapper">
                    <div class="comment-bubble">
                        <div class="comment-author-name">
                            <strong class="fn">${user.name}</strong>
                            <span class="pending-indicator">Posting...</span>
                        </div>
                        <div class="comment-content">${this.formatCommentContent(content)}</div>
                    </div>
                    <div class="comment-meta"><time>Just now</time></div>
                </div>
            </div>
        </li>
    `;
            return $(html);
        },

        formatCommentContent: function(content) {
            return '<p>' + content.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>') + '</p>';
        },

        handleNewCommentSuccess: function(response, $tempComment, tempId) {
            if (response.success) {
                if (response.data.comment_html) {
                    const $realComment = $(response.data.comment_html);
                    $tempComment.replaceWith($realComment);

                    $realComment.addClass('just-posted');
                    setTimeout(() => $realComment.removeClass('just-posted'), 3000);

                    $(this.selectors.commentField).val('');
                    this.moveFormToOriginalPosition();

                } else if (response.data.moderation_notice) {
                    $tempComment.find('.pending-indicator').text('Pending moderation');
                    $tempComment.removeClass('pending-comment').addClass('moderation-comment');

                    this.showNotice(response.data.moderation_notice, 'info');
                    $(this.selectors.commentField).val('');

                    this.moveFormToOriginalPosition();
                }

                if (response.data.total_comments) {
                    this.updateCommentCount(response.data.total_comments);
                }
            }
        },

        moveFormToOriginalPosition: function() {
            const $respond = $(this.selectors.respond);
            const $loadMoreBtn = $(this.selectors.loadMoreButton);

            if ($loadMoreBtn.length) {
                $loadMoreBtn.after($respond);
            } else {
                const $commentList = $(this.selectors.commentList);
                if ($commentList.length) {
                    $commentList.parent().after($respond);
                } else {
                    $(this.selectors.comments).append($respond);
                }
            }

            $(this.selectors.replyTitle).html('Write a comment');
        },

        handleNewCommentError: function(jqXHR, $tempComment) {
            if ($tempComment && $tempComment.length) {
                $tempComment.addClass('comment-error');

                setTimeout(() => {
                    $tempComment.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 500);
            }

            let errorMsg = 'Failed to post comment. Please try again.';

            if (jqXHR.responseJSON) {
                if (jqXHR.responseJSON.data) {
                    errorMsg = jqXHR.responseJSON.data;
                } else if (jqXHR.responseJSON.message) {
                    errorMsg = jqXHR.responseJSON.message;
                }
            }

            this.showNotice(errorMsg, 'error');
            $(this.selectors.submitButton).prop('disabled', false).val('Post comment');
        },

        handleEditSuccess: function(response, $editingComment) {
            if (response.success) {
                $editingComment.removeClass('updating');

                if (response.data.comment_edit) {
                    $editingComment.addClass('just-edited');
                    setTimeout(() => $editingComment.removeClass('just-edited'), 2000);
                }

                if (response.data.moderation_notice) {
                    this.showNotice(response.data.moderation_notice, 'info');
                }

                $(this.selectors.commentField).val('');
                this.cancelEdit();
            }
        },

        handleEditError: function(jqXHR, $editingComment) {
            const originalContent = $editingComment.data('original-content');

            if (originalContent) {
                $editingComment.find('.comment-content').html(originalContent);
            }

            $editingComment.removeClass('updating').addClass('edit-error');

            setTimeout(() => {
                $editingComment.removeClass('edit-error');
            }, 3000);

            let errorMsg = 'Failed to update comment.';
            if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
                errorMsg = jqXHR.responseJSON.data;
            }

            this.showNotice(errorMsg, 'error');
        },

        showNotice: function(message, type) {
            const $notice = $(`<div class="lbv-notice lbv-notice-${type}">${message}</div>`);

            $(this.selectors.replyTitle).after($notice);

            setTimeout(() => {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },

        updateCommentCount: function (total) {
            if (total > 0) {
                const text = total === 1 ? 'comment' : 'comment';
                $('.comments-title').text(`${total} ${text}`);
            }
        },

        handleLoadMore: function (e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const cpage = parseInt($button.attr('data-cpage'), 10) - 1;
            const postId = parseInt($button.attr('data-post'));

            $.ajax({
                url: ajax_url,
                type: 'POST',
                data: {
                    action: 'lbv_load_more',
                    post_id: postId,
                    cpage: cpage,
                },
                beforeSend: () => $button.text('Loading...'),
                success: (response) => {
                    if (response.success) {
                        $(this.selectors.commentList).append(response.data.html);

                        if (cpage <= 1) {
                            $button.replaceWith(
                                '<div class="end-list"><span>You\'ve reached the end of the list!</span></div>'
                            );
                        } else {
                            $button.attr('data-cpage', cpage).text('Load comments');
                        }

                        this.initNofollowLinks();
                    } else {
                        $button.remove();
                    }
                }
            });
        },

        initScrollToComment: function () {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#comment-')) {
                const $target = $(hash);
                if ($target.length) {
                    this.scrollTo($target);
                }
            }
        },

        initNofollowLinks: function () {
            $(this.selectors.loggedInAs).remove();

            const hostname = window.location.hostname;

            $(this.selectors.commentList + ' a').each(function () {
                const $link = $(this);
                const href = $link.attr('href');

                if (href && !href.includes(hostname) && !href.startsWith('#')) {
                    $link.attr({
                        'rel': 'ugc nofollow noopener',
                        'target': '_blank'
                    });
                }
            });

            if (!lbvPost.is_logged_in) {
                $('.comment-reply-login').remove();
            }
        },

        scrollTo: function ($element) {
            if (!$element.length) return;

            $('html, body').animate({
                scrollTop: $element.offset().top - this.config.scrollOffset
            }, this.config.scrollDuration);
        },

        removeErrors: function () {
            $(this.selectors.commentError).remove();
            $(this.selectors.commentField).css('border', '');
        },

        showError: function ($field, $replyTitle, message) {
            if ($field) {
                $field.css('border', '1px solid #ff0000');
            }
            $replyTitle.after(`<p class="comment_error">${message}</p>`);
        }
    };

    const TableMobile = {

        init: function () {
            this.addDataLabels();
        },

        addDataLabels: function () {
            const wpTables = document.querySelectorAll('.wp-block-table table');

            if (wpTables.length === 0) {
                setTimeout(() => this.addDataLabels(), 100);
                return;
            }

            wpTables.forEach(table => {
                const thead = table.querySelector('thead');
                if (!thead) return;

                const headers = Array.from(thead.querySelectorAll('th, td'))
                    .map(cell => cell.innerText?.trim() || cell.textContent?.trim() || '')
                    .filter(Boolean);

                if (!headers.length) return;

                const tbody = table.querySelector('tbody');
                if (!tbody) return;

                tbody.querySelectorAll('tr').forEach(row => {
                    Array.from(row.cells).forEach((cell, i) => {
                        if (headers[i]) {
                            cell.setAttribute('data-label', headers[i]);
                        }
                    });
                });
            });
        }

    };

    const App = {
        init: function () {
            $(function () {
                TableMobile.init();
                StickySidebar.init();
                FAQRankMath.init();
                ImageGallery.init('.entry-content', '.wp-block-image img, .idol-featured img, .idol-group img');
                CopyProtection.init();
                SidebarPopular.init();
                ScrollToTop.init();
                TableOfContent.init();
                PostComment.init();
            });
        }
    };

    App.init();

})(jQuery);

