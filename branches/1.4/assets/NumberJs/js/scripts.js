jQuery(document).ready(function ($) {
    let initNumberJs = function($el) {
        let options = JSON.parse(
            decodeURIComponent($el.data('options'))
        );
        $el.spinner(options);
    };

    $('.tiFyField-numberJs').each(function() {
        initNumberJs($(this));
    });

    $(document).on('mouseenter', '.tiFyField-numberJs', function() {
        $(this).each(function() {
            initNumberJs($(this));
        });
    });
});