"use strict";

jQuery(document).ready(function($) {
    $(document).on('click', '[data-control="notice"] [data-toggle="notice.dismiss"]', function(e){
        e.preventDefault();

        $(this).closest('[data-control="notice"]').attr('aria-hide', 'true');
    });
});