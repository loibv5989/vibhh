(function($) {
    'use strict';

    const STORAGE_KEY = 'lbv_contact_sent';
    const COOKIE_TIMEOUT_MINUTES = 2;

    function setCookie(name, value, minutes) {
        const d = new Date();
        d.setTime(d.getTime() + (minutes * 60 * 1000));
        document.cookie = name + "=" + value + "; expires=" + d.toUTCString() + "; path=/";
    }

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }

    function deleteCookie(name) {
        document.cookie = name + '=; Max-Age=0; path=/';
    }

    function hasSubmittedInSession() {
        return getCookie(STORAGE_KEY) === 'true';
    }

    function setSessionSubmitted(html) {
        setCookie(STORAGE_KEY, 'true', COOKIE_TIMEOUT_MINUTES);
        sessionStorage.setItem(STORAGE_KEY + '_html', html);
    }

    function getSessionHTML() {
        return sessionStorage.getItem(STORAGE_KEY + '_html');
    }

    function getFormData(form) {
        return {
            action: 'lbv_contact',
            fullname: form.fullname.value.trim(),
            email: form.email.value.trim(),
            subject: form.subject.value.trim(),
            content: form.content.value.trim(),
            security: lbvCT.nonce
        };
    }

    function showLoadingState(button) {
        document.getElementById('contact-loading').style.display = 'block';
        document.getElementById('contact-response').style.display = 'none';
        button.disabled = true;
        button.textContent = 'Sending...';
    }

    function hideLoadingState(button) {
        document.getElementById('contact-loading').style.display = 'none';
        button.disabled = false;
        button.textContent = 'Submit';
    }

    function showErrorMessage(message) {
        const responseEl = document.getElementById('contact-response');
        responseEl.style.color = 'red';
        responseEl.textContent = message;
        responseEl.style.display = 'block';
    }

    function showSuccessMessage(html) {
        $('.lbv-topct').remove();
        $('.contact-form').remove();
        $('.contact-container').show();
        $('#contact-response').html(html).show();
    }

    function handleSubmitError() {
        showErrorMessage('Failed to send message, please try again.');
    }

    function submitContactForm(form, button) {
        if (hasSubmittedInSession()) {
            const html = getSessionHTML();
            if (html) showSuccessMessage(html);
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        $.ajax({
            url: lbvCT.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: getFormData(form),
            beforeSend: function() {
                showLoadingState(button);
            },
            success: handleSubmitSuccess,
            error: handleSubmitError,
            complete: function() {
                hideLoadingState(button);
            }
        });
    }

    function handleSubmitSuccess(response) {
        if (response.success) {
            setSessionSubmitted(response.data.html);
            showSuccessMessage(response.data.html);
        } else {
            const errorMessage = response.data || 'Unknown error';
            showErrorMessage(errorMessage);
        }
    }

    function initContactForm() {
        if (hasSubmittedInSession()) {
            const html = getSessionHTML();
            if (html) showSuccessMessage(html);
            return;
        }

        const form = document.querySelector('.contact-form');
        const submitBtn = document.querySelector('.submit-btn');

        if (!form || !submitBtn) return;

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            submitContactForm(form, submitBtn);
        });
    }

    $(document).ready(initContactForm);

})(jQuery);
