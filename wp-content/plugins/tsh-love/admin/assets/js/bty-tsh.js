jQuery(function ($) {
    // Provider switching
    $('#bty_tsh_provider_select').on('change', function() {
        var provider = $(this).val();
        
        // Hide all provider-specific sections
        $('#gemini-settings, #groq-settings, #mistral-settings').hide();
        $('#gemini-models, #groq-models, #mistral-models').hide();
        
        // Show selected provider sections
        if (provider === 'gemini') {
            $('#gemini-settings, #gemini-models').show();
        } else if (provider === 'groq') {
            $('#groq-settings, #groq-models').show();
        } else if (provider === 'mistral') {
            $('#mistral-settings, #mistral-models').show();
        }
    });

    // Test provider connection
    $('#test-provider').on('click', function() {
        var provider = $('#bty_tsh_provider_select').val();
        var $button = $(this);
        var $results = $('#test-results');
        
        $button.prop('disabled', true).text('Testing...');
        $results.html('<div class="notice notice-info"><p>Testing connection...</p></div>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bty_tsh_test_provider',
                provider: provider,
                nonce: btyTshAdmin.test_provider_nonce
            },
            success: function(response) {
                if (response.success) {
                    $results.html('<div class="notice notice-success"><p>Connection successful!</p></div>');
                } else {
                    $results.html('<div class="notice notice-error"><p>Error: ' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $results.html('<div class="notice notice-error"><p>Network error occurred.</p></div>');
            },
            complete: function() {
                $button.prop('disabled', false).text('Test Connection');
            }
        });
    });

    // Create pages
    $('#bty-tsh-create-pages').on('click', function () {
        var $btn     = $(this).prop('disabled', true).text('Đang tạo...');
        var $results = $('#bty-tsh-pages-result').html('');

        $.post(ajaxurl, {
            action: 'bty_tsh_create_pages',
            nonce:  btyTshAdmin.create_pages_nonce,
        }, function (res) {
            if (res.success) {
                $results.html('<div class="notice notice-success"><p>✅ ' + res.data.message + '</p></div>');
            } else {
                $results.html('<div class="notice notice-error"><p>❌ ' + (res.data.message || 'Lỗi') + '</p></div>');
            }
        }).always(function () {
            $btn.prop('disabled', false).text('Tạo Trang Mẫu');
        });
    });
});
