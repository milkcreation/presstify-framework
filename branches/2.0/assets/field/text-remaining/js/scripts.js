jQuery(document).ready(function ($) {
    $(document).on('keyup.text_remaining', '[aria-control="text_remaining"] [aria-control="input"]', function (e) {
        var $closest = $(this).closest('[aria-control="text_remaining"]'),
            $infos = $($closest.data('infos')),
            length = parseInt($closest.data('max') -$(this).val().length);

        $infos
            .html(textRemainingInfosHtml(length))
            .attr('aria-reached', length ? (length<0 ? 'more' : 'less') : 'exact');

    });
    $('[aria-control="text_remaining"] [aria-control="input"]').each(function(e) {
        $(this).trigger('keyup.text_remaining');
    });
});

var textRemainingInfosHtml = function (length) {
    if ((length > 1) || (length < -1)) {
        return '<b>' + length + '</b> ' + tify.fieldTextRemaining.plural;
    } else if (length === 0) {
        return tify.fieldTextRemaining.none;
    } else {
        return '<b>' + length + '</b> ' + tify.fieldTextRemaining.singular;
    }
}