'use strict';

import jQuery from 'jquery';
import 'jquery-ui/ui/unique-id';

// Personnalisation
jQuery(document).ready(function ($) {
    let elements = {};

    let adminBarPosition = function ($el) {
        if (!$('body.admin-bar').length) {
            return false;
        }

        let id = $el[0].id || $el.uniqueId()[0].id,
            inf;
        const floating = ['absolute', 'fixed'];

        if (elements[id] === undefined) {
            elements[id] = {
                'pos': $el.css('position') || 'static'
            };
        }

        inf = elements[id];

        if (floating.indexOf(inf.pos) === 1) {
            inf.top = inf.top ?? $el.position().top;

            if (window.matchMedia('(max-width: 600px)').matches) {
                if ($(this).scrollTop() > 46) {
                    $el.css({'position': inf.pos, 'top': inf.top});
                } else {
                    $el.css({'position': 'absolute', 'top': inf.top + 46});
                }
            } else if (window.matchMedia('(max-width: 782px)').matches) {
                $el.css({'position': inf.pos, 'top': inf.top + 46});
            } else {
                $el.css({'position': inf.pos, 'top': inf.top + 32});
            }
        } else {
            inf.top = inf.top ?? parseInt($el.css('marginTop'));

            if (window.matchMedia('(max-width: 600px)').matches) {
                $el.css({'marginTop': inf.top});
            } else if (window.matchMedia('(max-width: 782px)').matches) {
                $el.css({'marginTop': inf.top + 46});
            } else {
                $el.css({'marginTop': inf.top + 32});
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