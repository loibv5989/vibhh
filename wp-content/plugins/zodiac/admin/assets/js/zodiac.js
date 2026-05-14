jQuery(document).ready(function ($) {

    $('#zodiac-test-provider').on('click', function () {
        var provider = $('#zodiac_test_provider_select').val();
        var $btn     = $(this);
        var $results = $('#zodiac-test-results');

        $btn.prop('disabled', true).text('Testing...');
        $results.html('');

        $.post(zodiacAdmin.ajax_url, {
            action:   'zodiac_test_provider',
            nonce:    zodiacAdmin.test_provider_nonce,
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

    $('#zodiac-create-pages').on('click', function () {
        var $btn     = $(this);
        var $results = $('#zodiac-create-pages-results');

        $btn.prop('disabled', true).text('Creating...');
        $results.html('');

        $.post(zodiacAdmin.ajax_url, {
            action: 'zodiac_create_pages',
            nonce:  zodiacAdmin.create_pages_nonce
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
