jQuery(function ($) {
    $('#tuvi_provider_select').on('change', function() {
        var provider = $(this).val();

        $('#tuvi-gemini-settings, #tuvi-groq-settings, #tuvi-mistral-settings').hide();
        $('#tuvi-gemini-models, #tuvi-groq-models, #tuvi-mistral-models').hide();

        if (provider === 'gemini') {
            $('#tuvi-gemini-settings, #tuvi-gemini-models').show();
        } else if (provider === 'groq') {
            $('#tuvi-groq-settings, #tuvi-groq-models').show();
        } else if (provider === 'mistral') {
            $('#tuvi-mistral-settings, #tuvi-mistral-models').show();
        }
    });

    $('#tuvi-test-provider').on('click', function() {
        var provider = $('#tuvi_provider_select').val();
        var $button = $(this);
        var $results = $('#tuvi-test-results');

        $button.prop('disabled', true).text('Testing...');
        $results.html('<div class="notice notice-info"><p>Testing connection...</p></div>');

        $.ajax({
            url: tuviAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'tuvi_test_provider',
                provider: provider,
                nonce: (window.tuviAdmin && window.tuviAdmin.test_provider_nonce) ? window.tuviAdmin.test_provider_nonce : ''
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
