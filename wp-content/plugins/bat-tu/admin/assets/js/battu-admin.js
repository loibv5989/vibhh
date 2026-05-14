jQuery(function ($) {
    $('#battu_provider_select').on('change', function() {
        var provider = $(this).val();

        $('#battu-gemini-settings, #battu-groq-settings, #battu-mistral-settings').hide();
        $('#battu-gemini-models, #battu-groq-models, #battu-mistral-models').hide();

        if (provider === 'gemini') {
            $('#battu-gemini-settings, #battu-gemini-models').show();
        } else if (provider === 'groq') {
            $('#battu-groq-settings, #battu-groq-models').show();
        } else if (provider === 'mistral') {
            $('#battu-mistral-settings, #battu-mistral-models').show();
        }
    });

    $('#battu-test-provider').on('click', function() {
        var provider = $('#battu_provider_select').val();
        var $button = $(this);
        var $results = $('#battu-test-results');

        $button.prop('disabled', true).text('Testing...');
        $results.html('<div class="notice notice-info"><p>Testing connection...</p></div>');

        $.ajax({
            url: battuAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'battu_test_provider',
                provider: provider,
                nonce: (window.battuAdmin && window.battuAdmin.test_provider_nonce) ? window.battuAdmin.test_provider_nonce : ''
            },
            success: function(response) {
                if (response && response.success) {
                    $results.html('<div class="notice notice-success"><p>Connection successful!</p></div>');
                } else {
                    var msg = (response && response.data && response.data.message) ? response.data.message : 'Unknown error';
                    $results.html('<div class="notice notice-error"><p>Error: ' + msg + '</p></div>');
                }
            },
            error: function(xhr) {
                var msg = 'Network error occurred.';
                if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                    msg = xhr.responseJSON.data.message;
                }
                $results.html('<div class="notice notice-error"><p>Error: ' + msg + '</p></div>');
            },
            complete: function() {
                $button.prop('disabled', false).text('Test Connection');
            }
        });
    });
});
