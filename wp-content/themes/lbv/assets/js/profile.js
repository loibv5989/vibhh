(function($) {
    'use strict';

    const SELECTORS = {
        toggleBtn: '#show-delete-form',
        form: '#delete-account-form',
        cancelBtn: '#cancel-delete',
        usernameInput: '#confirm-username-input',
        feedback: '#username-feedback',
        submitBtn: '#submit-delete',
        successMsg: '#delete-success-message',
        deleteText: '.fup-btn-delete-text',
        deleteLoading: '.fup-btn-delete-loading',
        confirmDisplay: '.fup-confirm-username code'
    };

    const STATE = {
        currentUser: ''
    };

    init();

    function init() {
        cacheCurrentUser();
        bindEvents();
    }

    function cacheCurrentUser() {
        const $toggle = $(SELECTORS.toggleBtn);

        if (!$toggle.length) return;

        STATE.currentUser = $toggle.data('username') ||
            $(SELECTORS.confirmDisplay).text().trim() || '';
    }

    function bindEvents() {
        $(SELECTORS.toggleBtn).on('click', showDeleteForm);
        $(SELECTORS.cancelBtn).on('click', hideDeleteForm);
        $(SELECTORS.usernameInput).on('input', validateUsername);
        $(SELECTORS.submitBtn).on('click', submitDelete);
    }

    function showDeleteForm(e) {
        e.preventDefault();
        $(SELECTORS.form).slideDown(150);
        $(this).hide();
        $(SELECTORS.usernameInput).focus();
    }

    function hideDeleteForm(e) {
        e.preventDefault();
        $(SELECTORS.form).slideUp(150, function () {
            $(SELECTORS.toggleBtn).show();
        });
        resetForm();
    }

    function normalize(str) {
        return (str || '').trim().replace(/\s+/g, '');
    }

    function validateUsername() {
        const inputVal = $(this).val();
        const isMatch =
            normalize(inputVal) === normalize(STATE.currentUser);

        toggleValidationState(isMatch, inputVal.length > 0);
    }

    function toggleValidationState(isValid, hasValue) {
        const $input = $(SELECTORS.usernameInput);
        const $btn = $(SELECTORS.submitBtn);
        const $feedback = $(SELECTORS.feedback);

        if (!hasValue) {
            resetValidation();
            return;
        }

        if (isValid) {
            $input.addClass('valid').removeClass('invalid');
            $btn.prop('disabled', false);
            $feedback
                .text('✓ Username matches')
                .removeClass('error')
                .addClass('success');
        } else {
            $input.addClass('invalid').removeClass('valid');
            $btn.prop('disabled', true);
            $feedback
                .text('✗ Username does not match')
                .removeClass('success')
                .addClass('error');
        }
    }

    function resetValidation() {
        $(SELECTORS.usernameInput)
            .removeClass('valid invalid')
            .val('');
        $(SELECTORS.submitBtn).prop('disabled', true);
        $(SELECTORS.feedback).empty();
    }

    function submitDelete(e) {
        e.preventDefault();

        if ($(this).prop('disabled')) return;

        setLoading(true);

        $.post(fup_ajax.ajax_url, {
            action: 'fup_delete_account',
            nonce: fup_ajax.nonce,
            confirm: 'yes',
            username: STATE.currentUser
        }).done(handleAjaxSuccess).fail(handleAjaxError);
    }

    function handleAjaxSuccess(response) {
        if (response?.success) {
            $(SELECTORS.form).slideUp(200, function () {
                $(SELECTORS.successMsg).slideDown(200);
            });

            setTimeout(() => location.reload(), 2000);
            return;
        }

        alert(response?.data?.message || 'An error occurred.');
        setLoading(false);
    }

    function handleAjaxError() {
        alert('Network error. Please try again.');
        setLoading(false);
    }

    function setLoading(isLoading) {
        $(SELECTORS.submitBtn).prop('disabled', isLoading);
        $(SELECTORS.deleteText).toggle(!isLoading);
        $(SELECTORS.deleteLoading).toggle(isLoading);
    }

    function resetForm() {
        resetValidation();
        setLoading(false);
    }

})(jQuery);

