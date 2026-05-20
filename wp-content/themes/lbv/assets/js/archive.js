(function ($) {
    'use strict';

    const State = {
        paged: 1,
        maxPages: 1,
        isLoading: false,
        archiveId: 0,
        searchQuery: '',
        context: 'home',
        nonce: '',
        nonceInitialized: false,

        init: function () {
            const self = this;

            const $loadBtn = $('.load-more-btn');
            if ($loadBtn.length) {
                self.paged = parseInt($loadBtn.data('page')) || 1;
                self.maxPages = parseInt($loadBtn.data('max')) || 1;
                self.archiveId = parseInt($loadBtn.data('archive')) || 0;
                self.searchQuery = $loadBtn.data('search') || '';
                self.context = $loadBtn.data('context') || self.detectContext();
            }
        },

        fetchNonce: function () {
            const self = this;
            return $.ajax({
                url: lbvArchive.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'lbv_get_nonce'
                },
                success: function (response) {
                    if (response.success && response.data) {
                        self.nonce = response.data.nonce;
                        self.nonceInitialized = true;
                    }
                }
            });
        },

        detectContext: function () {
            if (window.location.href.indexOf('/tag/') > -1) return 'tag';
            if (window.location.href.indexOf('?s=') > -1) return 'search';
            if (document.body.classList.contains('home')) return 'home';
            return 'category';
        },

        resetPage: function () {
            this.paged = 1;
        },

        incrementPage: function () {
            this.paged++;
        },

        setLoading: function (loading) {
            this.isLoading = loading;
        },

        getArchiveId: function () {
            return this.archiveId;
        },

        getSearchQuery: function () {
            return this.searchQuery;
        },

        canLoadMore: function () {
            return !this.isLoading;
        }
    };

    const UI = {
        showLoadingButton: function ($btn) {
            $btn.addClass('loading').prop('disabled', true);
            $btn.find('.btn-content').hide();
            $btn.find('.btn-loading').show();
        },

        hideLoadingButton: function ($btn) {
            $btn.removeClass('loading').prop('disabled', false);
            $btn.find('.btn-loading').hide();
            $btn.find('.btn-content').show();
        },

        updateLoadButton: function (hasMore, $btn) {
            if (!hasMore) {
                $btn.fadeOut(300);
            } else {
                $btn.show();
            }
        },

        resetUI: function ($btn) {
            this.hideLoadingButton($btn);
        }
    };

    const PostLoader = {
        getRequestData: function (extraData) {
            const data = {
                paged: State.paged,
                archive_nonce: State.nonce,
                context: State.context,
                is_home: State.context === 'home' ? 1 : 0,
                action: 'lbv_load_posts'
            };

            if (State.context === 'search') {
                data.search_query = State.getSearchQuery();
            } else {
                data.archive_id = State.getArchiveId();
            }

            return Object.assign(data, extraData || {});
        },

        handleResponse: function (response, append, $btn, targetSelector) {
            if (!response.success || !response.data) {
                UI.resetUI($btn);
                State.setLoading(false);
                return;
            }

            const data = response.data;
            const $container = $(targetSelector || '#posts-container');

            if (append) {
                $container.append(data.posts);
            } else {
                $container.html(data.posts);
                State.resetPage();
            }

            const hasMore = data.has_more === true;

            if (data.notice) {
                this.handleNotice(data.notice, append);
            }

            UI.updateLoadButton(hasMore, $btn);
            UI.resetUI($btn);
            State.setLoading(false);
        },

        handleNotice: function (noticeHtml, append) {
            const $wrapper = $('.load-more-wrapper');
            $wrapper.find('.end-list').remove();

            if (append) {
                $wrapper.find('.load-more-btn').fadeOut(300, function () {
                    $wrapper.append(noticeHtml);
                    $wrapper.find('.end-list').hide().fadeIn(300);
                });
            } else {
                $wrapper.append(noticeHtml);
                $wrapper.find('.end-list').hide().fadeIn(300);
            }
        },

        load: function (options) {
            const opts = options || {};
            const append = opts.append !== false;
            const $btn = opts.$btn;
            const targetSelector = opts.targetSelector || '#posts-container';
            const extraData = opts.extraData || {};

            if (State.isLoading) return;

            State.setLoading(true);
            if ($btn) {
                UI.showLoadingButton($btn);
            }

            const doRequest = () => {
                if (!State.nonce) {
                    if ($btn) UI.resetUI($btn);
                    State.setLoading(false);
                    return;
                }

                $.ajax({
                    url: lbvArchive.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: this.getRequestData(extraData),
                    success: (response) => {
                        this.handleResponse(response, append, $btn, targetSelector);
                    },
                    error: (xhr) => {
                        if ($btn) {
                            UI.resetUI($btn);
                        }
                        State.setLoading(false);

                        if (xhr.responseJSON?.data?.message === 'Invalid nonce') {
                            State.fetchNonce().then(() => {
                                setTimeout(() => PostLoader.load(options), 100);
                            });
                        }
                    }
                });
            };

            if (!State.nonceInitialized) {
                State.fetchNonce().then(() => {
                    doRequest();
                });
            } else {
                doRequest();
            }
        }
    };

    const LoadMoreButton = {
        init: function () {
            $(document).on('click', '.load-more-btn', function (e) {
                e.preventDefault();

                if (!State.canLoadMore()) return;

                const $btn = $(this);
                State.incrementPage();

                PostLoader.load({
                    append: true,
                    $btn: $btn,
                    targetSelector: '#posts-container'
                });
            });

            $(document).on('click', '.load-more-btn-post', function (e) {
                e.preventDefault();

                if (State.isLoading) return;

                const $btn = $(this);
                const targetId = $btn.data('target') || '#posts-container-blog';

                let page = parseInt($btn.data('page'), 10) || 1;
                page++;
                $btn.data('page', page);

                PostLoader.load({
                    append: true,
                    $btn: $btn,
                    targetSelector: targetId,
                    extraData: {
                        context: 'home',
                        is_home: 1,
                        paged: page,
                        post_type: 'post'
                    }
                });
            });
        }
    };

    $(function () {
        State.init();
        LoadMoreButton.init();
    });

})(jQuery);