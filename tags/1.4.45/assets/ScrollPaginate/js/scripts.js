/**
 * @see https://learn.jquery.com/plugins/stateful-plugins-with-widget-factory/
 * @see https://api.jqueryui.com/jquery.widget
 * @see https://blog.rodneyrehm.de/archives/11-jQuery-Hooks.html
 */
!(function ($, doc, win) {
    $.widget(
        'tify.tifypaginate', {
            // Définition des options par défaut
            options: {
                handler: '',
                target: ''
            },

            // Instanciation de l'élément
            _create: function () {
                var self = this;

                // Définition de l'alias court du controleur d'affichage
                this.el = this.element;

                // Initialisation des attributs de configuration du controleur
                this._initOptions();

                this.handler = $(this.options.container_id);
                this.target = !this.options.target ? this.handler.prev() : $(this.options.target);
                this.xhr = undefined;

                $(document).on('click', self.handler, function (e) {
                    if ($(this).hasClass('tiFyCoreControl-ScrollPaginate--complete')) {
                        return false;
                    }

                    var o = self.options,
                        offset = $('> *', self.target).length;

                    self.target.addClass('tiFyCoreControl-ScrollPaginateLoading tiFyCoreControl-ScrollPaginateLoading--target');
                    $(this).addClass('tiFyCoreControl-ScrollPaginateLoading tiFyCoreControl-ScrollPaginateLoading--handler');

                    self.target.trigger('tify_control.scroll_paginate.loading', $(this));

                    self.xhr = $.post(
                        tify_ajaxurl,
                        {
                            action: o.ajax_action,
                            _ajax_nonce: o.ajax_nonce,
                            options: o,
                            offset: offset
                        }
                    )
                        .done(function(data, textStatus, jqXHR){
                            self.target.append(data.html);
                            self.target.trigger('tify_control.scroll_paginate.item_added', data.html);

                            if (data.complete) {
                                self.target.addClass('tiFyCoreControl-ScrollPaginateComplete tiFyCoreControl-ScrollPaginateComplete--target');
                                $(this).addClass('tiFyCoreControl-ScrollPaginateComplete tiFyCoreControl-ScrollPaginateComplete--handler');
                            }
                        })
                        .then(function(data, textStatus, jqXHR ){
                            self.target.removeClass('tiFyCoreControl-ScrollPaginateLoading tiFyCoreControl-ScrollPaginateLoading--target');
                            $(this).removeClass('tiFyCoreControl-ScrollPaginateLoading tiFyCoreControl-ScrollPaginateLoading--handler');

                            self.target.trigger('tify_control.scroll_paginate.loaded', $(this));

                            self.xhr = undefined;
                        });
                });
            },
            _initOptions: function () {
                if (this.el.data('options')) {
                    $.extend(
                        this.options,
                        $.parseJSON(decodeURIComponent(this.el.data('options')))
                    );
                }
            }
    });
})(jQuery, document, window);
/*
var tify_scroll_paginate_xhr, tify_scroll_paginate;
!(function ($, doc, win, undefined) {
    tify_scroll_paginate = function (handler, target)
    {
        // Récupération du receveur d'éléments
        var $target = !target ? $(handler).prev() : $(target);

        // Contrôle d'existance des éléments dans le DOM
        if (!$target.length) {
            return;
        }

        $(window).scroll(function (e) {
            if ((tify_scroll_paginate_xhr === undefined) && !$target.hasClass('tiFyCoreControl-ScrollPaginateComplete') && isScrolledIntoView($(handler))) {
                $(handler).trigger('click');
            }
        });


    }

    function isScrolledIntoView($ele) {
        var offset = $ele.offset();
        if (!offset)
            return false;

        var lBound = $(window).scrollTop(),
            uBound = lBound + $(window).height(),
            top = offset.top,
            bottom = top + $ele.outerHeight(true);

        return (top > lBound && top < uBound)
            || (bottom > lBound && bottom < uBound)
            || (lBound >= top && lBound <= bottom)
            || (uBound >= top && uBound <= bottom);
    }
})(jQuery, document, window, undefined);
*/

jQuery(document).ready(function($){
    $('[aria-control="scroll_paginate"]').tifypaginate();
});