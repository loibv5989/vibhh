(function ($) {
    'use strict';

    let ajax_url = lbvPost.ajax_url;

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

        fetchNonce: function () {
            const self = this;
            return $.ajax({
                url: ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'lbv_get_comment_nonce'
                },
                success: function (response) {
                    if (response.success && response.data) {
                        self.nonce = response.data.nonce;
                    }
                }
            });
        },

        initLoginTrigger: function () {
            if (!lbvPost.is_logged_in) {
                const self = this;

                $(this.selectors.commentField + ', ' + this.selectors.submitButton).on('click focus', function (e) {
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

        createTempComment: function (tempId, content, parentId) {
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

        formatCommentContent: function (content) {
            return '<p>' + content.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>') + '</p>';
        },

        handleNewCommentSuccess: function (response, $tempComment, tempId) {
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

        moveFormToOriginalPosition: function () {
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

        handleNewCommentError: function (jqXHR, $tempComment) {
            if ($tempComment && $tempComment.length) {
                $tempComment.addClass('comment-error');

                setTimeout(() => {
                    $tempComment.fadeOut(300, function () {
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

        handleEditSuccess: function (response, $editingComment) {
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

        handleEditError: function (jqXHR, $editingComment) {
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

        showNotice: function (message, type) {
            const $notice = $(`<div class="lbv-notice lbv-notice-${type}">${message}</div>`);

            $(this.selectors.replyTitle).after($notice);

            setTimeout(() => {
                $notice.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 5000);
        },

        updateCommentCount: function (total) {
            if (total > 0) {
                const text = total === 1 ? 'comment' : 'comments';
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

    $(function () {
        PostComment.init();
    });

})(jQuery);
