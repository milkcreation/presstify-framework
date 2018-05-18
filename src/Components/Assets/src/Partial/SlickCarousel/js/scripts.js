jQuery(document).ready(function ($) {
    $('[aria-control="slick_carousel"]').on('init', function (event, slick) {
        $(this).addClass('is-ready');
    });
    $('[aria-control="slick_carousel"]').each(function () {
        $(this).slick();
    });
});