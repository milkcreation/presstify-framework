"use strict";

jQuery(function ($) {
    $.widget('tify.tifyFileBrowser', {
        widgetEventPrefix: 'file-browser:',
        id: undefined,
        options: {},
        // Instanciation de l'élément.
        _create: function () {
            this.instance = this;

            this.el = this.element;

            this._initOptions();
            this._initEvents();
        },

        // INITIALISATION
        // -------------------------------------------------------------------------------------------------------------
        // Initialisation des événements déclenchement.
        _initEvents: function () {
            this._on(this.el, {
                'click [data-control="file-browser.link.dir"]': this._onDirClick
            });

            this._on(this.el, {
                'click [data-control="file-browser.link.file"]': this._onFileClick
            });

            this._on(this.el, {
                'submit [data-control="file-browser.form.delete"]': this._onDeleteSubmit
            });

            this._on(this.el, {
                'submit [data-control="file-browser.form.newdir"]': this._onNewdirSubmit
            });

            this._on(this.el, {
                'submit [data-control="file-browser.form.newname"]': this._onNewnameSubmit
            });
        },
        // Initialisation des attributs de configuration.
        _initOptions: function () {
            $.extend(
                true,
                this.options,
                this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
            );
        },

        // EVENEMENTS
        // -------------------------------------------------------------------------------------------------------------
        // Action au clic sur un répertoire.
        _onDirClick: function (e) {
            e.preventDefault();

            let self = this,
                el = e.currentTarget,
                ajax = $.extend(self.option('ajax'), {data: {path: $(el).data('target'), action: 'getdir'}});

            $(el).closest('[data-control="file-browser.content.item"]').addClass('selected')
                .siblings().removeClass('selected');

            $.ajax(ajax)
                .done(function (resp) {
                    $('[data-control="file-browser.breadcrumb"]', self.el).replaceWith(resp.breadcrumb);
                    $('[data-control="file-browser.content.items"]', self.el).replaceWith(resp.content);
                    $('[data-control="file-browser.sidebar"]', self.el).html(resp.sidebar);
                });
        },
        // Action au clic sur un fichier.
        _onFileClick: function (e) {
            e.preventDefault();

            let self = this,
                el = e.currentTarget,
                ajax = $.extend(self.option('ajax'), {data: {path: $(el).data('target'), action: 'getfile'}});

            $(el).closest('[data-control="file-browser.content.item"]').addClass('selected')
                .siblings().removeClass('selected');

            $.ajax(ajax)
                .done(function (resp) {
                    $('[data-control="file-browser.sidebar"]', self.el).html(resp.sidebar);
                });
        },
        // Action à la soumission de formulaire de suppression.
        _onDeleteSubmit: function (e) {
            e.preventDefault();

            let self = this,
                el = e.currentTarget,
                ajax = $.extend(self.option('ajax'), {data: 'action=delete&' + $(el).serialize()});

            $.ajax(ajax)
                .done(function (resp) {
                    $('[data-control="file-browser.breadcrumb"]', self.el).replaceWith(resp.breadcrumb);
                    $('[data-control="file-browser.content.items"]', self.el).replaceWith(resp.content);
                    $('[data-control="file-browser.sidebar"]', self.el).html(resp.sidebar);
                });
        },
        // Action à la soumission de formulaire de création de nouveau répertoire.
        _onNewdirSubmit: function (e) {
            e.preventDefault();

            let self = this,
                el = e.currentTarget,
                ajax = $.extend(self.option('ajax'), {data: 'action=newdir&' + $(el).serialize()});

            $.ajax(ajax)
                .done(function (resp) {
                    $('[data-control="file-browser.breadcrumb"]', self.el).replaceWith(resp.breadcrumb);
                    $('[data-control="file-browser.content.items"]', self.el).replaceWith(resp.content);
                });
        },
        // Action à la soumission de formulaire de création de renommage.
        _onNewnameSubmit: function (e) {
            e.preventDefault();

            let self = this,
                el = e.currentTarget,
                ajax = $.extend(self.option('ajax'), {data: 'action=newname&' + $(el).serialize()});

            $.ajax(ajax)
                .done(function (resp) {
                    $('[data-control="file-browser.breadcrumb"]', self.el).replaceWith(resp.breadcrumb);
                    $('[data-control="file-browser.content.items"]', self.el).replaceWith(resp.content);
                    $('[data-control="file-browser.sidebar"]', self.el).html(resp.sidebar);
                });
        }
    });

    $(document).ready(function ($) {
        $('[data-control="file-browser"]').tifyFileBrowser();
    });
});