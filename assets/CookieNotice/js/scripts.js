jQuery(document).ready(function($) {
    $(document).on('click', '[data-cookie_notice][data-handle]', function(e){
        e.preventDefault();
        var $handler = $(this);

        // Définition du conteneur
        if (!$($(this).data('cookie_notice')).length)
        {
            return;
        } else {
            var $closest = $($(this).data('cookie_notice'));
        }

        var o = JSON.parse(
                decodeURIComponent($closest.data('options'))
            ),
            handle = $(this).data('handle');

        switch(handle) {
            case 'valid' :
                $.post(
                    tify_ajaxurl,
                    {
                        action: o.ajax_action,
                        _ajax_nonce: o.ajax_nonce,
                        cookie_name: o.cookie_name,
                        cookie_hash: o.cookie_hash,
                        cookie_expire: o.cookie_expire
                    }
                )
                    .done(function(resp) {
                        $handler.trigger('tify_control.cookie_notice.validated');
                    })
                    .always(function () {
                        $closest.fadeOut(function () {
                            $(this).remove();
                        });
                    });
                break;
            case 'close' :
                $handler.trigger('tify_control.cookie_notice.closed');
                $closest.fadeOut(function () {
                    $(this).remove();
                });
                break;
        }
    });
});