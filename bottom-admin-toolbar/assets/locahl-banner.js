jQuery(function ($) {
    $(document).on('click', '.bab-locahl-banner .locahl-bento-banner__close', function () {
        if (typeof babLocahlBanner === 'undefined') {
            return;
        }

        var $banner = $(this).closest('.locahl-promo-banner');

        $.post(babLocahlBanner.ajax_url, {
            action: babLocahlBanner.action,
            nonce: babLocahlBanner.nonce,
        });

        $banner.fadeOut(200, function () {
            $(this).remove();
        });
    });
});
