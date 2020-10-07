'use strict';

import jQuery from 'jquery';

// Personnalisation
jQuery(document).ready(function ($) {
    let adminBarPosition = function ($el) {
        if ($('body.admin-bar').length) {
            if (window.matchMedia('(max-width: 600px)').matches) {
                if ($(this).scrollTop() > 46) {
                    $el.css({'position': 'fixed', 'top': 0});
                } else {
                    $el.css({'position': 'absolute', 'top': 46});
                }
            } else if (window.matchMedia('(max-width: 782px)').matches) {
                $el.css({'position': 'fixed', 'top': 46});
            } else {
                $el.css({'position': 'fixed', 'top': 32});
            }
        }
    };

    $(document).ready(function ($) {
        $('[data-control="admin-bar-pos"]').each(function () {
            adminBarPosition($(this));
        });

        $(window).on('scroll resize', function () {
            $('[data-control="admin-bar-pos"]').each(function () {
                adminBarPosition($(this));
            });
        });
    });
});