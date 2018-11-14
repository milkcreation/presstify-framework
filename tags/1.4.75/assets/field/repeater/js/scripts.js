jQuery(document).ready(function ($) {
    var jqxhr;

    // Ajout d'un élément.
    $('[aria-control="repeater"]').each(function() {
        let $closest = $(this),
            id = $closest.attr('aria-id'),
            o = JSON.parse(decodeURIComponent($closest.data('options')));

        $(document).on('click.tify.field.repeater.add', '[aria-id="' + id + '"] [aria-control="add"]', function (e) {
            e.stopPropagation();
            e.preventDefault();

            if (jqxhr !== undefined) {
                return;
            }

            let $items = $('[aria-control="items"]', $closest),
                index = $('[aria-control="item"]', $items).length;

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

        // Ordonnacement des images de la galerie.
        $('[aria-id="' + id + '"][aria-sortable="true"] [aria-control="items"]')
            .sortable(o.sortable)
            .disableSelection();

        // Suppression d'un élément.
        $(document).on('click.tify.field.repeater.remove', '[aria-id="' + id + '"] [aria-control="remove"]', function (e) {
            e.preventDefault();
            $(this).closest('[aria-control="item"]').fadeOut(function () {
                $(this).remove();
            });
        });
    });
});