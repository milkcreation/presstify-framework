"use strict";

import jQuery from 'jquery';
import 'slick-carousel/slick/slick';

jQuery(function ($) {
    $(document).ready(function () {
        let $slider = $('[data-control="slider"]');

        $slider
            .on('init', function () {
                $(this).addClass('is-ready');
            })
            .each(function () {
                $(this).slick();
            });
    });
});
