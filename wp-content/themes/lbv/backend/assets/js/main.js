jQuery(document).ready(function($) {
    'use strict';

    $('.lbv-section-title').on('click', function() {
        var submenu = $(this).next('.lbv-submenu');
        if (submenu.length) {
            submenu.slideToggle(200);
            $(this).toggleClass('open');
        }
    });

    function showNotification(type, message) {
        var notificationClass = type === 'success' ? 'notice-success' : 'notice-error';
        var notification = $('<div class="notice ' + notificationClass + ' is-dismissible"><p>' + message + '</p></div>');
        $('.lbv-admin-wrapper').prepend(notification);
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    function saveLogo(field, id, wrapper, button) {
        button.text('Saving...').prop('disabled', true);

        $.ajax({
            url: lbvAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'lbv_save_logo',
                nonce: lbvAdmin.nonce,
                field: field,
                id: id
            },
            success: function(response) {
                button.text('Upload').prop('disabled', false);

                if (response.success) {
                    showNotification('success', 'Logo saved successfully!');
                } else {
                    showNotification('error', response.data.message || 'Failed to save logo');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                button.text('Upload').prop('disabled', false);
                showNotification('error', 'AJAX error occurred');
            }
        });
    }

    $('.lbv-upload-logo').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var field = button.data('field');
        var wrapper = button.closest('.lbv-field-wrapper');

        var mediaUploader = wp.media({
            title: 'Select or Upload Logo',
            button: { text: 'Use this image' },
            library: { type: 'image' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            wrapper.find('.lbv-logo-url-display').val(attachment.url);
            wrapper.find('.lbv-logo-preview-box').html(
                '<img src="' + attachment.url + '" alt="Logo" class="lbv-logo-image">'
            );
            wrapper.find('.lbv-logo-id').val(attachment.id);
            wrapper.find('.lbv-remove-logo').prop('disabled', false);
            saveLogo(field, attachment.id, wrapper, button);
        });

        mediaUploader.open();
    });

    $('.lbv-remove-logo').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var field = button.data('field');
        var wrapper = button.closest('.lbv-field-wrapper');

        button.text('Removing...').prop('disabled', true);

        $.ajax({
            url: lbvAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'lbv_remove_logo',
                nonce: lbvAdmin.nonce,
                field: field
            },
            success: function(response) {
                if (response.success) {
                    wrapper.find('.lbv-logo-url-display').val('');
                    wrapper.find('.lbv-logo-preview-box').html(
                        '<div class="lbv-no-logo-placeholder">' +
                        '<span class="dashicons dashicons-format-image"></span>' +
                        '<p>No logo selected</p>' +
                        '</div>'
                    );
                    wrapper.find('.lbv-logo-id').val('');

                    button.text('Remove').prop('disabled', true);
                    showNotification('success', 'Logo removed successfully!');
                } else {
                    button.text('Remove').prop('disabled', false);
                    showNotification('error', response.data.message || 'Failed to remove logo');
                }
            },
            error: function(xhr, status, error) {
                button.text('Remove').prop('disabled', false);
                showNotification('error', 'AJAX error occurred');
            }
        });
    });

    $('#lbv-save-oauth-settings').on('click', function(e) {
        e.preventDefault();

        var data = {
            action: 'lbv_save_oauth',
            nonce: lbvAdmin.nonce,
            git_client_id: $('input[name="lbv_options[git_client_id]"]').val(),
            git_client_secret: $('input[name="lbv_options[git_client_secret]"]').val(),
            google_client_id: $('input[name="lbv_options[google_client_id]"]').val(),
            google_client_secret: $('input[name="lbv_options[google_client_secret]"]').val()
        };

        $.post(lbvAdmin.ajax_url, data, function(response) {
            if (response.success) {
                alert(response.data.message);
            } else {
                alert(response.data.message);
            }
        });
    });

    // Toggle password visibility
    $('.lbv-toggle-password').on('click', function() {
        var $btn = $(this);
        var $input = $btn.siblings('.lbv-password-input');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $btn.find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-hidden');
        } else {
            $input.attr('type', 'password');
            $btn.find('.dashicons').removeClass('dashicons-hidden').addClass('dashicons-visibility');
        }
    });
});
