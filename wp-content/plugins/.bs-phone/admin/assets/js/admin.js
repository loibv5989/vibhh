jQuery(function ($) {

    var ajaxurl = bsPhoneAdmin.ajax_url;

    // Tạo trang
    $('#bs-phone-create-pages').on('click', function () {
        var $btn     = $(this).prop('disabled', true).text('Đang tạo...');
        var $results = $('#bs-phone-pages-result').html('');

        $.post(ajaxurl, {
            action: 'bs_phone_create_pages',
            nonce:  bsPhoneAdmin.create_pages_nonce,
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

    // Xóa cache
    $('#bs-phone-clear-cache').on('click', function () {
        if (!confirm('Xóa toàn bộ cache phân tích?')) return;
        $.post(ajaxurl, {
            action: 'bs_phone_clear_cache',
            nonce:  bsPhoneAdmin.clear_cache_nonce,
        }, function (res) {
            var msg = res.success ? res.data.message : (res.data || 'Lỗi');
            $('#bs-phone-cache-result').html('<p style="color:' + (res.success ? 'green' : 'red') + '">' + msg + '</p>');
        });
    });
});
