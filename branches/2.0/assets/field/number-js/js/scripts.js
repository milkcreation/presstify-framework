"use strict";

jQuery(document).ready(function ($) {
    $('[data-control="number-js"]').each(function() {
        let options = JSON.parse(decodeURIComponent($(this).data('options')));

        $(this).spinner(options);
    });
});