/* global tify */
"use strict";

jQuery(document).ready(function($) {
    $(document).on('click', '[data-control="notice"] [data-toggle="notice.accept"]', function(e){
        e.preventDefault();
        e.stopPropagation();

        let $self = $(this),
            $closest = $self.closest('[data-control="notice"]');

        let o = JSON.parse(decodeURIComponent($closest.data('options')));

        $closest.attr('aria-loading', 'true');
        $.post(tify.ajax_url, o)
            .done(function (resp) {
                if (resp.success) {
                    $closest.attr('aria-loading', 'false');
                    $closest.attr('aria-hide', 'true');
                    $self.trigger('cookie-notice:done');
                }
            })
            .always(function () {
                $self.trigger('cookie-notice:always');
            });
    });
});