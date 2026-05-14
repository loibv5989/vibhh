jQuery(document).ready(function ($) {

    $('#bb-oracle-test-provider').on('click', function () {
        var provider = $('#bb_oracle_test_provider_select').val();
        var $btn = $(this);
        var $results = $('#bb-oracle-test-results');

        $btn.prop('disabled', true).text('Testing...');
        $results.html('');

        $.post(bbOracleAdmin.ajax_url, {
            action:   'bb_oracle_test_provider',
            nonce:    bbOracleAdmin.test_provider_nonce,
            provider: provider
        }, function (res) {
            if (res.success) {
                $results.html('<span style="color:green;">✓ Kết nối thành công!</span>');
            } else {
                $results.html('<span style="color:red;">✗ Lỗi: ' + (res.data && res.data.message ? res.data.message : 'Unknown error') + '</span>');
            }
        }).fail(function () {
            $results.html('<span style="color:red;">✗ Request failed</span>');
        }).always(function () {
            $btn.prop('disabled', false).text('Test Connection');
        });
    });

    $('#bb-oracle-create-pages').on('click', function () {
        var $btn = $(this);
        var $results = $('#bb-oracle-create-pages-results');

        $btn.prop('disabled', true).text('Đang tạo...');
        $results.html('');

        $.post(bbOracleAdmin.ajax_url, {
            action: 'bb_oracle_create_pages',
            nonce:  bbOracleAdmin.create_pages_nonce
        }, function (res) {
            if (res.success && res.data && res.data.pages) {
                var html = '<p>' + res.data.message + '</p><ul>';
                $.each(res.data.pages, function (i, page) {
                    html += '<li><strong>' + page.title + '</strong> — ' + page.status;
                    if (page.edit_url) html += ' (<a href="' + page.edit_url + '" target="_blank">Sửa</a>)';
                    html += '</li>';
                });
                html += '</ul>';
                $results.html(html);
            } else {
                $results.html('<span style="color:red;">✗ ' + (res.data && res.data.message ? res.data.message : 'Lỗi không xác định') + '</span>');
            }
        }).fail(function () {
            $results.html('<span style="color:red;">✗ Request failed</span>');
        }).always(function () {
            $btn.prop('disabled', false).text('Tạo trang');
        });
    });
});
