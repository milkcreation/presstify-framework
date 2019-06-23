"use strict";

jQuery(document).ready(function ($) {
    $('[data-control="number_js"]').each(function() {
        var options = JSON.parse(
            decodeURIComponent($(this).data('options'))
        );
        $(this).spinner(options);
    });
});