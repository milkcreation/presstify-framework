"use strict";

import jQuery from 'jquery';

jQuery(document).ready(function($) {
    $(document).on('click', '[data-control="notice"] [aria-toggle="accept"]', function(e){
        e.preventDefault();
        e.stopPropagation();

        var $closest = $(this).closest('[data-control="notice"]');

        var o = JSON.parse(decodeURIComponent($closest.data('options')));

        $closest.attr('aria-loading', 'true');
        $.post(tify.ajax_url, {
            action: o.ajax_action,
            _ajax_nonce: o.ajax_nonce,
            cookie_name: o.cookie_name,
            cookie_hash: o.cookie_hash,
            cookie_expire: o.cookie_expire
        })
            .done(function () {
                $closest.attr('aria-loading', 'false');
            })
            .always(function () {
                $closest.attr('aria-hide', 'true');
            });
    });
});