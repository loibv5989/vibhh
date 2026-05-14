jQuery(document).ready(function ($) {

    $('#bb-zodiac-test-provider').on('click', function () {
        var provider = $('#bb_zodiac_test_provider_select').val();
        var $btn     = $(this);
        var $results = $('#bb-zodiac-test-results');

        $btn.prop('disabled', true).text('Testing...');
        $results.html('');

        $.post(bbZodiacAdmin.ajax_url, {
            action:   'bb_zodiac_test_provider',
            nonce:    bbZodiacAdmin.test_provider_nonce,
            provider: provider
        }, function (res) {
            if (res.success) {
                $results.html('<span style="color:green;">✓ Connection successful!</span>');
            } else {
                $results.html('<span style="color:red;">✗ Error: ' + (res.data && res.data.message ? res.data.message : 'Unknown error') + '</span>');
            }
        }).fail(function () {
            $results.html('<span style="color:red;">✗ Request failed</span>');
        }).always(function () {
            $btn.prop('disabled', false).text('Test Connection');
        });
    });

    $('#bb-zodiac-create-pages').on('click', function () {
        var $btn     = $(this);
        var $results = $('#bb-zodiac-create-pages-results');

        $btn.prop('disabled', true).text('Creating...');
        $results.html('');

        $.post(bbZodiacAdmin.ajax_url, {
            action: 'bb_zodiac_create_pages',
            nonce:  bbZodiacAdmin.create_pages_nonce
        }, function (res) {
            if (res.success && res.data && res.data.pages) {
                var html = '<p>' + res.data.message + '</p><ul>';
                $.each(res.data.pages, function (i, page) {
                    html += '<li><strong>' + page.title + '</strong> — ' + page.status;
                    if (page.edit_url) html += ' (<a href="' + page.edit_url + '" target="_blank">Edit</a>)';
                    html += '</li>';
                });
                html += '</ul>';
                $results.html(html);
            } else {
                $results.html('<span style="color:red;">✗ ' + (res.data && res.data.message ? res.data.message : 'Unknown error') + '</span>');
            }
        }).fail(function () {
            $results.html('<span style="color:red;">✗ Request failed</span>');
        }).always(function () {
            $btn.prop('disabled', false).text('Create Pages');
        });
    });
});
