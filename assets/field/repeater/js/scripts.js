jQuery(document).ready(function ($) {
    var jqxhr;

    // Ajout d'un élément
    $(document).on('click.tify.field.repeater.add', '[aria-control="repeater"] [aria-control="add"]', function (e) {
        e.stopPropagation();
        e.preventDefault();

        if (jqxhr !== undefined) {
            return;
        }

        // Eléments du DOM
        var $this = $(this),
            $closest = $(this).closest('[aria-control="repeater"]'),
            $items = $('[aria-control="items"]', $closest);

        // Variables
        var index = $('[aria-control="item"]', $items).length,
            o = JSON.parse(decodeURIComponent($closest.data('options')));

        jqxhr = $.post(
            tify_ajaxurl,
            {
                action: o.ajax_action,
                _ajax_nonce: o.ajax_nonce,
                index: index,
                options: o
            }
        )
            .done(function (resp) {
                if (!resp.success) {
                    alert(resp.data);
                } else {
                    $items.append(resp.data);
                }
            })
            .always(function () {
                jqxhr = undefined;
            });
    });

    // Ordonnacement des images de la galerie
    $('[aria-control="repeater"] [aria-control="items"]')
        .sortable({placeholder: 'tiFyField-RepeaterItemPlaceholder', axis: 'y'})
        .disableSelection();

    // Suppression d'un élément
    $(document).on('click.tify.field.repeater.remove', '[aria-control="repeater"] [aria-control="remove"]', function (e) {
        e.preventDefault();
        $(this).closest('[aria-control="item"]').fadeOut(function () {
            $(this).remove();
        });
    });
});