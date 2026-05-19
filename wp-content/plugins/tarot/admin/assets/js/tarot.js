jQuery(document).ready(function($) {
    $('#bb_tarot_provider_select').on('change', function() {
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
        var $button = $(this);
        var $results = $('#test-results');

        $button.prop('disabled', true).text('Testing...');
        $results.html('');

        $.post(bbTarotAdmin.ajax_url, {
            action: 'bb_tarot_test_provider',
            provider:  $('#bb_tarot_provider_select').val(),
            nonce: bbTarotAdmin.nonce
        }, function(response) {
            $button.prop('disabled', false).text('Test Connection');
            if (response.success) {
                var html = '<div style="padding: 10px; background: #f0f0f0; border-left: 4px solid #46b450; border-radius: 4px; font-size: 13px; font-family: monospace; color: #333; white-space: pre-wrap; word-wrap: break-word;">';
                html += (response.data.response || 'OK');
                html += '</div>';
                html += '<div style="margin-top: 10px; padding: 8px; background: #fff; border: 1px solid #ddd; border-radius: 4px; font-size: 12px; color: #666;">';
                html += '<strong>Model:</strong> ' + (response.data.model || 'N/A') + ' | ';
                html += '<strong>Key:</strong> ' + (response.data.key || 'N/A') + ' | ';
                html += '<strong>Duration:</strong> ' + (response.data.duration_ms || 0) + 'ms';
                if (response.data.failed_keys && response.data.failed_keys.length > 0) {
                    html += '<br><strong style="color: #d63638;">Failed keys:</strong> ' + response.data.failed_keys.join(', ');
                }
                html += '</div>';
                $results.html(html);
            } else {
                var html = '<div style="color: red; font-weight: bold;">✗ Test failed</div>';
                html += '<div style="margin-top: 8px; padding: 8px; background: #fff0f0; border-radius: 4px; font-size: 12px; color: #d63638;">';
                var data = response && response.data ? response.data : {};
                var msg = (data && data.message) ? data.message : ((response && response.message) ? response.message : 'Unknown error');
                html += msg;
                html += '</div>';
                if (data && data.duration_ms) {
                    html += '<div style="margin-top: 5px; font-size: 13px; color: #666;">Duration: ' + data.duration_ms + 'ms</div>';
                }
                if (data && data.failed_keys && data.failed_keys.length > 0) {
                    html += '<div style="margin-top: 5px; font-size: 13px; color: #d63638;">Failed keys: ' + data.failed_keys.join(', ') + '</div>';
                }
                $results.html(html);
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

        $.post(bbTarotAdmin.ajax_url, {
            action: 'bb_tarot_create_pages',
            nonce: bbTarotAdmin.create_pages_nonce
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
