jQuery(document).ready(function ($) {
    $('.tiFyField-NumberJs').each(function() {
        var options = JSON.parse(
            decodeURIComponent($(this).data('options'))
        );
        $(this).spinner(options);
    });
});