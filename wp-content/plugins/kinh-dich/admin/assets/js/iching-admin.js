jQuery(document).ready(function($) {
    $('#iching_provider_select').on('change', function() {
        var provider = $(this).val();
        
        $('#gemini-settings, #groq-settings, #mistral-settings').hide();
        $('#gemini-models, #groq-models, #mistral-models').hide();
        
        if (provider === 'gemini') {
            $('#gemini-settings, #gemini-models').show();
        } else if (provider === 'groq') {
            $('#groq-settings, #groq-models').show();
        } else if (provider === 'mistral') {
            $('#mistral-settings, #mistral-models').show();
        }
    });

    $('#test-provider').on('click', function() {
        var provider = $('#iching_provider_select').val();
        var $button = $(this);
        var $results = $('#test-results');
        
        $button.prop('disabled', true).text('Testing...');
        $results.html('');
        
        $.post(ichingAdmin.ajax_url, {
            action: 'iching_test_provider',
            provider: provider,
            nonce: ichingAdmin.nonce
        }, function(response) {
            $button.prop('disabled', false).text('Test Connection');
            
            if (response.success) {
                $results.html('<div style="color: green;">✓ Connection successful!</div>');
            } else {
                $results.html('<div style="color: red;">✗ ' + response.data.message + '</div>');
            }
        }).fail(function() {
            $button.prop('disabled', false).text('Test Connection');
            $results.html('<div style="color: red;">✗ Connection failed</div>');
        });
    });

    $('#create-pages-btn').on('click', function() {
        var $button = $(this);
        var $results = $('#create-pages-results');
        
        $button.prop('disabled', true).text('Đang tạo trang...');
        $results.html('');
        
        $.post(ichingAdmin.ajax_url, {
            action: 'iching_create_pages',
            nonce: ichingAdmin.create_pages_nonce
        }, function(response) {
            $button.prop('disabled', false).text('Tạo tất cả trang Kinh Dịch');
            
            if (response.success) {
                $results.html('<div style="color: green;">✓ ' + response.data.message + '</div>');
                if (response.data.pages) {
                    var pagesList = '<ul style="margin-top: 10px; padding-left: 20px;">';
                    response.data.pages.forEach(function(page) {
                        pagesList += '<li><a href="' + page.edit_url + '" target="_blank">' + page.title + '</a> (' + page.status + ')</li>';
                    });
                    pagesList += '</ul>';
                    $results.append(pagesList);
                }
            } else {
                $results.html('<div style="color: red;">✗ ' + response.data.message + '</div>');
            }
        }).fail(function() {
            $button.prop('disabled', false).text('Tạo tất cả trang Kinh Dịch');
            $results.html('<div style="color: red;">✗ Có lỗi xảy ra</div>');
        });
    });
});
