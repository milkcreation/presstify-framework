"use strict";

!(function ($, doc, win) {
    // Attribution de la valeur à l'élément.
    let _hook = $.valHooks.div;
    $.valHooks.div = {
        get: function (elem) {
            if (typeof $(elem).tifyselect('instance') === 'undefined') {
                return _hook && _hook.get && _hook.get(elem) || undefined;
            }
            return $(elem).data('value');
        },
        set: function (elem, value) {
            if (typeof $(elem).tifyselect('instance') === 'undefined') {
                return _hook && _hook.set && _hook.set(elem, value) || undefined;
            }
            $(elem).data('value', value);
        }
    };
    $.widget(
        'tify.tifyselect', {
            // Compatibilité v1 des classes.
            compat: {
                autocompleteInput: 'FieldSelectJs-autocomplete',
                handler: 'FieldSelectJs-handler',
                picker: 'FieldSelectJs-picker',
                pickerFilter: 'FieldSelectJs-pickerFilter',
                pickerLoader: 'FieldSelectJs-pickerLoader',
                pickerItem: 'FieldSelectJs-pickerItem',
                pickerItems: 'FieldSelectJs-pickerItems',
                pickerMore: 'FieldSelectJs-pickerMore',
                selection: 'FieldSelectJs-selection',
                selectionItem: 'FieldSelectJs-selectionItem',
                trigger: 'FieldSelectJs-trigger',
                triggerHandler: 'FieldSelectJs-triggerHandler'
            },

            // Liste des attributs de configuration.
            options: {
                autocomplete: false,
                disabled: false,
                duplicate: false,
                id: '',
                max: -1,
                multiple: false,
                picker: {
                    class: '',
                    placement: 'clever',
                    appendTo: '',
                    delta: {
                        top: 0,
                        left: 0,
                        width: 0
                    },
                    adminbar: true,
                    filter: false,
                    loader: '',
                    more: '+'
                },
                removable: true,
                sortable: {},
                source: {},
                trigger: {
                    class: '',
                    arrow: true
                }
            },

            // Instanciation de l'élément.
            _create: function () {
                this.instance = this;
                this.el = this.element;

                this.flags = {
                    hasSelection: false,
                        isDisabled: true,
                        isMultiple: false,
                        isDuplicable: false,
                        isRemovable: true,
                        isSortable: false,
                        isOpen: false,
                        isComplete: true,
                        page: 1,
                        hasArrow: true,
                        hasFilter: false,
                        hasSource: false,
                        hasAutocomplete: false
                };

                this.init = {
                    autocomplete: false,
                    el: false,
                    flags: false,
                    handler: false,
                    items:false,
                    options: false,
                    picker: false,
                    selection: false,
                    trigger: false
                };
                this.items = [];
                this.selected = [];

                this._initOptions();
                this._initFlags();
                this._initControls();
                this._initItems();

                if (this.flags.isDisabled) {
                    this._onDisable();
                } else {
                    this._onEnable();
                }
            },

            // INIT
            // ---------------------------------------------------------------------------------------------------------
            // Initialisation des attributs de configuration.
            _initOptions: function () {
                if (!this.init.options) {
                    this.options = $.extend(
                        true,
                        this.options,
                        $.parseJSON(decodeURIComponent(this.el.data('options')))
                    );

                    this.init.options = true;
                }
            },

            // Initialisation des indicateurs d'état.
            _initFlags: function () {
                if (!this.init.flags) {
                    this.flags.isDisabled = !!this.options.disabled;
                    this.flags.isMultiple = !!this.options.multiple;
                    this.flags.isDuplicable = !!(this.options.duplicate && this.flags.isMultiple);
                    this.flags.isRemovable = !!(!this.flags.isDisabled && this.options.removable);
                    this.flags.isSortable = (this.options.sortable !== false) &&
                        this.flags.isMultiple && !this.flags.isDisabled;
                    this.flags.isOpen = false;
                    this.flags.isComplete = false;
                    this.flags.page = 1;
                    this.flags.hasSource = (this.options.source !== false);
                    this.flags.hasArrow = this.options.trigger.arrow;

                    this.init.flags = true;
                }
            },

            // Initialisation des agents de contrôle.
            _initControls: function () {
                this._initControlElement();
                this._initControlHandler();
                this._initControlTrigger();
                this._initControlPicker();
                this._initControlSelection();
                if (this.options.autocomplete) {
                    this._initControlAutocomplete();
                }
            },

            // Initialisation du controleur d'autocompletion.
            _initControlAutocomplete: function () {
                if (!this.init.autocomplete) {
                    this.autocompleteInput = $('<input data-control="select-js.autocomplete" autocomplete="off"/>')
                        .prependTo(this.trigger)
                        .addClass(this.compat.autocompleteInput);

                    if (typeof this.options.autocomplete !== 'object') {
                        this.options.autocomplete = {};
                    }

                    this.flags.hasAutocomplete = true;
                    this.el.attr('aria-autocomplete', true);

                    if (this.flags.isMultiple) {
                        this.selection.insertAfter(this.trigger);
                    }

                    this.init.autocomplete = true;
                }
            },

            // Initialisation du controleur principal.
            _initControlElement: function () {
                if (!this.init.el) {
                    this.el.attr('aria-selection', this.flags.hasSelection);
                    this.el.attr('aria-multiple', this.flags.isMultiple);
                    this.el.attr('aria-sortable', this.flags.isSortable);
                    this.el.attr('aria-duplicable', this.flags.isDuplicable);
                    this.el.attr('aria-arrow', this.flags.hasArrow);

                    this.init.el = true;
                }
            },

            // Initialisation du controleur de traitement.
            _initControlHandler: function () {
                if (!this.init.handler) {
                    this.handler = $('[data-control="select-js.handler"]', this.el)
                        .addClass(this.compat.handler);

                    this.init.handler = true;
                }
            },

            // Initialisation des éléments de listes.
            _initItems: function () {
                let self = this;

                this._setSelected(this.handler.val());

                $('option', this.handler).each(function (i, el) {
                    let value = $(el).val(),
                        attrs = {index: i, value: value, content: $(el).text()};
                    attrs.picker = self._setControlPickerItem(null, attrs);
                    attrs.selection = self._setControlSelectionItem(null, attrs);

                    self.items[i] = attrs;

                    if (self._isSelected(value)) {
                        self._selectionAddSelected(i);
                        self._pickerAddSelected(i);
                    }
                });
            },

            // Initialisation du controleur de la liste de selection.
            _initControlPicker: function () {
                if (!this.init.picker) {
                    this.picker = $('<div data-control="select-js.picker"/>')
                        .appendTo(this.el)
                        .addClass(this.compat.picker)
                        .attr('aria-multiple', this.flags.isMultiple)
                        .attr('aria-duplicable', this.flags.isDuplicable);

                    this.pickerItems = $('<ul data-control="select-js.picker.items"/>')
                        .appendTo(this.picker)
                        .addClass(this.compat.pickerItems);

                    let $appendTo = $(this.options.picker.appendTo);
                    if (!$(this.options.picker.appendTo).length) {
                        $appendTo = $('body');
                    }
                    this.picker
                        .appendTo($appendTo)
                        .addClass(this.options.picker.class);

                    if (this.options.picker.filter) {
                        this.flags.hasFilter = true;
                        this.picker.attr('aria-filter', true);

                        this.pickerFilter = $('<input data-control="select-js.picker.filter" autocomplete="off"/>')
                            .prependTo(this.picker)
                            .addClass(this.compat.pickerFilter);
                    }

                    this.pickerLoader = $('<div data-control="select-js.picker.loader"/>')
                        .html(this.options.picker.loader)
                        .prependTo(this.picker)
                        .addClass(this.compat.pickerLoader);

                    if (this.flags.hasSource) {
                        this.pickerMore = $('<a href="#" data-control="select-js.picker.more"/>')
                            .html(this.options.picker.more)
                            .prependTo(this.picker)
                            .addClass(this.compat.pickerMore);

                        this.picker.attr('aria-complete', false);
                    }

                    this.init.picker = true;
                }
            },

            // Initialisation du controleur de liste des éléments sélectionnés.
            _initControlSelection: function () {
                if (!this.init.selection) {
                    this.selection = $('<ul data-control="select-js.selection"/>')
                        .appendTo(this.trigger)
                        .addClass(this.compat.selection);

                    if (this.flags.isSortable) {
                        this.options.sortable = $.extend(
                            {
                                handle: '[aria-handle="sort"]',
                                containment: 'parent',
                                update: function () {
                                    this._reset();
                                }
                            },
                            this.options.sortable
                        );

                        this.sortable = this.selection.sortable(this.options.sortable);
                        this.selection.disableSelection();
                    } else {
                        this.sortable = undefined;
                    }

                    this.init.selection = true;
                }
            },

            // Initialisation du controleur de déclenchement d'affichage de la liste selection.
            _initControlTrigger: function () {
                if (!this.init.trigger) {
                    this.trigger = $('<div data-control="select-js.trigger"/>')
                        .appendTo(this.el)
                        .addClass(this.compat.trigger);

                    this.triggerHandler = $('<a href="#" data-control="select-js.trigger.handler"/>')
                        .appendTo(this.trigger)
                        .addClass(this.compat.triggerHandler);

                    this.init.trigger = true;
                }
            },

            // SETTER
            // ---------------------------------------------------------------------------------------------------------
            // Définition d'un élément du controleur de traitement.
            _setControlHandlerItem: function (content, attrs) {
                return $('<option/>')
                    .appendTo(this.handler)
                    .attr('value', attrs.value)
                    .text(content);
            },

            // Définition d'un élément du controleur de la liste de selection.
            _setControlPickerItem: function (content, attrs) {
                let pickerItem = $('<li data-control="select-js.picker.item"/>')
                    .appendTo(this.pickerItems)
                    .addClass(this.compat.pickerItem)
                    .html(content ? content : attrs.content);

                this._onPickerItemClick(pickerItem);

                return pickerItem;
            },

            // Définition d'un élément du controleur de la liste des éléments sélectionnés.
            _setControlSelectionItem: function (content, attrs) {
                return $('<li data-control="select-js.selection-item"/>')
                    .attr('data-index', attrs.index)
                    .addClass(this.compat.selectionItem)
                    .html(content ? content : attrs.content);
            },

            // GETTER
            // ---------------------------------------------------------------------------------------------------------
            // Récupération d'un élément.
            _getItem: function (i) {
                return this.items[i];
            },

            // Arguments de requête Ajax de récupération des éléments.
            _getQueryArgs: function () {
                return {
                    page: this.flags.page,
                    term: this.flags.hasAutocomplete ? this.autocompleteInput.val().toString() : ''
                };
            },

            // SELECTED
            // ---------------------------------------------------------------------------------------------------------
            // Vérification si un élément est selectionné.
            _isSelected: function (v) {
                return this.selected.indexOf(v.toString()) !== -1;
            },

            // Définition de la liste des éléments selectionnés.
            _setSelected: function (v) {
                this.selected = v.toString().split(',');
            },

            // Mise à jour de la sélection d'un élement.
            _updateSelected: function (v) {
                let index = this.selected.indexOf(v.toString());

                if (index === -1) {
                    this.selected.push(v.toString());
                    return 1;
                } else {
                    this.selected.splice(index, 1);
                    return 0;
                }
            },

            // SELECTION
            // ---------------------------------------------------------------------------------------------------------
            // Ajout d'une selection à la liste des éléments sélectionnés.
            _selectionAddSelected(i) {
                this.items[i].selection.appendTo(this.selection);
            },

            // Suppression d'une selection de la liste des éléments sélectionnés.
            _selectionRemoveSelected(i) {
                this.items[i].selection.remove();
            },

            // Vidage des sélections de la liste des éléments sélectionnés.
            _selectionFlushSelected() {
                this.selection.empty();
            },

            // PICKER
            // ---------------------------------------------------------------------------------------------------------
            // Ajout d'une selection à la liste de sélection.
            _pickerAddSelected(i) {
                this.items[i].picker.attr('aria-selected', true);
            },

            // Suppression d'une selection à la liste de sélection.
            _pickerRemoveSelected(i) {
                this.items[i].picker.attr('aria-selected', false);
            },

            // Vidage des sélections de la liste de sélection.
            _pickerFlushSelected() {
                $.each(this.items, function (u, v) {
                    v.picker.attr('aria-selected', false);
                });
            },

            // ACTIONS
            // ---------------------------------------------------------------------------------------------------------
            // Ajout d'un élément dans la liste de selection.
            _doChange: function (item) {
                if (this.flags.isMultiple) {
                    if (this._updateSelected(item.value)) {
                        this._selectionAddSelected(item.index);
                        this._pickerAddSelected(item.index);
                    } else {
                        this._selectionRemoveSelected(item.index);
                        this._pickerRemoveSelected(item.index);
                    }
                } else {
                    this._setSelected(item.value);
                    this._selectionFlushSelected();
                    this._selectionAddSelected(item.index);
                    this._pickerFlushSelected();
                    this._pickerAddSelected(item.index);
                    this._doClose();
                }

                this.handler.val(this.selected);

                //self._onChange(item);
            },

            // Ouverture de la liste de selection.
            _doOpen: function () {
                this._onOutsideClick();

                this.flags.isOpen = true;
                this.el.attr('aria-open', true);
                this.picker.attr('aria-open', true);

                this._doPickerPosition();

                this._doQueryItems();

                //self._onOpen();
            },

            // Fermeture de la liste de selection.
            _doClose: function () {
                this._offOutsideClick();

                this.flags.isOpen = false;
                this.el.attr('aria-open', false);
                this.picker.attr('aria-open', false);

                //self._onClose();
            },

            // Positionnement de la liste de selection dans le DOM.
            _doPickerPosition: function () {
                let offset = $.extend(
                    {},
                    this.trigger.offset(),
                    {width: this.trigger.outerWidth()}
                    ),
                    placement = this.options.picker.placement;

                if (this.options.picker.delta.top) {
                    offset.top += this.options.picker.delta.top;
                }
                if (this.options.picker.delta.left) {
                    offset.left += this.options.picker.delta.left;
                }
                if (this.options.picker.delta.width) {
                    offset.width += this.options.picker.delta.width;
                }

                if (placement === 'clever') {
                    if ($(win).outerHeight() + $(win).scrollTop() < offset.top + this.picker.outerHeight()) {
                        placement = 'top';
                    } else {
                        placement = 'bottom';
                    }
                }

                switch (placement) {
                    case 'top' :
                        offset.top -= this.picker.outerHeight() - 1;
                        break;
                    case 'bottom' :
                        offset.top += this.trigger.outerHeight() - 1;
                        break;
                }

                this.picker.css(offset);
            },

            // Récupération de la liste des éléments.
            _doQueryItems: function () {
                let self = this;

                if (this.flags.hasSource && !this.flags.isComplete) {
                    if (this.xhr !== undefined) {
                        this.xhr.abort();
                    }

                    //this.pickerLoader.show();
                    this.options.source.query_args = $.extend(
                        this.options.source.query_args,
                        this._getQueryArgs()
                    );

                    this.xhr = $.ajax({
                        url: tify.ajax_url,
                        data: this.options.source,
                        method: 'post'
                    }).done(function (data) {
                        if (data.length) {
                            $.each(data, function (u, attrs) {
                                attrs = $.extend({index: self.items.length}, attrs, {value: attrs.value.toString()});
                                self._setControlHandlerItem(attrs.content, attrs);
                                attrs.picker = self._setControlPickerItem(attrs.picker, attrs);
                                attrs.selection = self._setControlSelectionItem(attrs.selection, attrs);
                                self.items[attrs.index] = attrs;
                            });
                            self.flags.page++;
                        } else {
                            self.flags.isComplete = true;
                            self.picker.attr('aria-complete', true);
                            self.flags.page = 1;
                        }
                    }).always(function () {
                        //self.pickerLoader.hide();
                        self.xhr = undefined;
                    });
                }
            },

            //EVENTS
            // ---------------------------------------------------------------------------------------------------------
            // Définition de la liste des événements à l'activation.
            _onEnable: function () {
                this.flags.isDisabled = false;
                this.el.attr('aria-disabled', false);
                this.handler.prop('disabled', false);

                this._onTriggerHandlerClick();

                /*
                // Activation de la suppression d'élément dans la liste des éléments selectionnés
                if (self.flags.isRemovable) {
                    $('> li > [aria-handle="remove"]', self.selection).on('click.tify_select.selected_remove.' +
                        self.instance.uuid,
                        function () {
                            let item = self._getAttrs($(this).closest('li'));
                            self._remove(item);
                        });
                }

                // Masque la liste des éléments selectionnés si elle est vide
                if (!this._count()) {
                    this.flags.hasSelection = false;
                    this.el.attr('aria-selection', false);
                    if (!self.flags.isMultiple && self.flags.hasAutocomplete) {
                        this.autocompleteInput.show();
                    }
                    this.selection.hide();
                }

                // Activation du champs de saisie par autocomplétion
                if (self.flags.hasAutocomplete) {
                    self.autocompleteInput.on('keyup.tify_select.autocomplete.' +
                        self.instance.uuid, function () {
                        // Fermeture de la liste de selection
                        self._close();

                        // Interruption si la condition de valeur n'est pas remplie
                        if (!$(this).val()) {
                            return;
                        }

                        // Suppression des éléments de la liste de selection
                        self.pickerItems.empty();

                        // Initialisation des indicateur d'état
                        self.flags.isComplete = false;
                        self.flags.page = 1;

                        // Réinitialisation de la requête de récupération des éléments de la liste de selection
                        if (self.timeout !== undefined) {
                            clearTimeout(self.timeout);
                        }
                        if (self.xhr !== undefined) {
                            self.xhr.abort();
                        }

                        // Lancement de la requête de récupération des éléments de la liste de selection
                        self.timeout = setTimeout(function () {
                            self._getPickerItems();
                            self.xhr.done(function (data) {
                                if (data.length) {
                                    self._open();
                                }
                            });
                        }, 1000);
                    });
                }
                */
            },

            // Evénements à la désactivation du controleur.
            _onDisable: function () {
                this.flags.isDisabled = true;
                this.el.attr('aria-disabled', true);
                this.handler.prop('disabled', true);

                this.triggerHandler.off('click.select-js.trigger.handler.' + this.instance.uuid);

                $('[data-control="select-js.picker.item"]')
                    .off('click.select-js.picker.item.' + this.instance.uuid);

                /*
                if (this.flags.isRemovable) {
                    $('> li > [aria-handle="remove"]', self.selection)
                        .off('click.tify_select.selected_remove.' + self.instance.uuid);
                }

                // Désactivation du champs de saisie par autocomplétion
                if (self.flags.hasAutocomplete) {
                    self.autocompleteInput
                        .off('keyup.tify_select.autocomplete.' + self.instance.uuid);
                }*/
            },

            // Activation du clic sur les élements de la liste de sélection.
            _onPickerItemClick: function ($pickerItem) {
                let self = this;

                $pickerItem.on(
                    'click.select-js.picker.item.' + this.instance.uuid,
                    function (e) {
                        if ($(this).is(':not([aria-disabled="true"])')) {
                            e.preventDefault();

                            let item = self._getItem($(this).index());

                            self._doChange(item);
                        }
                    }
                );
            },

            // Activation du clic sur le controleur d'affichage de la liste de sélection.
            _onTriggerHandlerClick: function () {
                let self = this;

                this.triggerHandler.on('click.select-js.trigger.handler.' + this.instance.uuid, function (e) {
                    e.preventDefault();

                    if (self.flags.isOpen) {
                        self._doClose();
                    } else {
                        self._doOpen();
                    }
                });
            },

            // Activation du clic en dehors de la liste de sélection.
            _onOutsideClick: function () {
                let self = this;

                this.document.on('click.select-js.outside.' + this.instance.uuid, function (e) {
                    if (!$(e.target).closest(self.el).length && !$(e.target).closest(self.picker).length) {
                        self._doClose();
                    }
                });
            },

            // Désactivation du clic en dehors de la liste de sélection.
            _offOutsideClick: function () {
                this.document.off('click.select-js.outside.' + this.instance.uuid);
            },




            // OLD
            // ---------------------------------------------------------------------------------------------------------

            /**
             * Suppression d'un élément de la liste des éléments selectionnés.
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _remove: function (item) {
                let self = this;

                // Mise à jour de la liste d'élement sélectionné
                self._get(item.index).remove();

                // Mise à jour du selecteur de gestion de traitement
                $('> [data-index="' + item.index + '"]', self.handler).remove();

                // Mise à jour de l'élément dans la liste de selection
                if (!$('> [data-value="' + item.value + '"]', self.selection).length) {
                    $('> [data-value="' + item.value + '"]', self.pickerItems).attr('aria-selected', false);
                }

                // Définition de la liste des événements suite à la suppression d'un élément dans la liste de selection
                self._onRemove(item);
            },

            /**
             * Récupération d'un élément de la liste de selection à partir de son indexe
             *
             * @param index
             *
             * @returns {*|HTMLElement}
             * @private
             */
            _get: function (index) {
                return $('> li[data-index="' + index + '"]', this.selection);
            },

            /**
             * Suppression de tous les éléments de la liste des éléments selectionnés
             *
             * @private
             */
            _removeAll: function () {
                let self = this;

                self._all().each(function () {
                    let item = self._getAttrs($(this));

                    // Mise à jour de la liste d'élement sélectionné
                    self._get(item.index).remove();

                    // Mise à jour du selecteur de gestion de traitement
                    $('> [data-index="' + item.index + '"]', self.handler).remove();

                    // Mise à jour de l'élément dans la liste de selection
                    if (!$('> [data-value="' + item.value + '"]',
                        self.selection).length) {
                        $('> [data-value="' + item.value + '"]', self.pickerItems).attr('aria-selected', false);
                    }
                });

                // Mise à jour de la valeur du selecteur de gestion de traitement
                self.el.val(self.handler.val());
            },

            /**
             * Réinitialisation de la liste des éléments sélectionnés
             *
             * @private
             */
            _reset: function () {
                let self = this;

                // Nettoyage des entrées du selecteur de gestion de traitement
                $('option', self.handler).remove();

                // Traitement des éléments selectionnés
                self._all().each(function () {
                    let item = self._getAttrs($(this));

                    self._setRemovable(item);
                    self._setSortable(item);
                    self._addHandlerItem(item);
                    self._setPicker(item);
                });

                // Mise à jour de la valeur du controleur d'affichage
                self.el.val(self.handler.val());
            },

            /**
             * Récupération des éléments courants dans la liste des éléments selectionnés
             *
             * @private
             */
            _all: function () {
                return $('> li', this.selection);
            },

            /**
             * Calcul du nombre d'éléments courants dans la liste des éléments selectionnés
             *
             * @private
             */
            _count: function () {
                return this._all().length;
            },

            /**
             * Indication de mise en avant d'un élément dans la liste des éléments sélectionnés
             *
             * @param item Attributs de l'élément
             *
             * @private
             */
            _highlight: function (item) {
                let self = this;

                $('> [data-value="' + item.value + '"]', self.selection).attr('aria-highlight', true).one(
                    'webkitAnimationEnd oanimationend msAnimationEnd animationend',
                    function () {
                        $(this).attr('aria-highlight', false);
                    }
                );
            },

            /**
             * Activation du controle de suppression à un élément de la liste des éléments selectionnés
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _setRemovable: function (item) {
                let self = this;

                if (self.flags.isRemovable) {
                    $('<span aria-handle="remove">×</span>')
                        .prependTo(self._get(item.index))
                        .on(
                            'click.tify_select.selected_remove.' + self.instance.uuid,
                            function () {
                                let item = self._getAttrs($(this).closest('li'));
                                self._remove(item);
                            });
                }
            },

            /**
             * Activation du controle d'ordonnancement à un élément de la liste des éléments selectionnés
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _setSortable: function (item) {
                if (!this.flags.isSortable) {
                } else if (this.options.sortable.handle) {
                    this._get(item.index).prepend('<span aria-handle="sort">...</span>');
                } else {
                    this._get(item.index).attr('aria-handle', 'sort');
                }
            },

            /**
             * Définition de la liste des événements à l'ouverture de la liste de selection
             *
             * @private
             */
            _onOpen: function () {
                let self = this;

                // Activation du positionnement de la liste de selection au scroll dans la fenêtre du navigateur
                $(win).on('scroll.tify_select.picker_position.' + self.instance.uuid,
                    function () {
                        let offset = self._getPickerOffset();

                        self.picker.css(offset);
                    });

                // Activation de la récupération de la liste des éléments au scroll dans la liste de selection
                self.pickerItems.on(
                    'scroll.tify_select.picker_list.' + self.instance.uuid,
                    function () {
                        if (self.xhr !== undefined) {
                            return;
                        } else if (($(this).prop('scrollHeight') - $(this).innerHeight()) -
                            $(this).scrollTop() < 20) {
                            self._getPickerItems();
                        }
                    });

                // Activation de la récupération de la liste des éléments supplémentaire au clique sur le lien d'ajout de la liste de selection
                if (self.pickerMore !== undefined) {
                    self.pickerMore.on(
                        'click.tify_select.picker_list.' + self.instance.uuid,
                        function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            if (self.xhr !== undefined) {
                                return;
                            }
                            self._getPickerItems();
                        });
                }

                // Activation du champs de saisie de filtrage de la liste de selection
                if (self.flags.hasFilter) {
                    self.pickerFilter.focus();

                    self.pickerFilter
                        .on('keyup.tify_select.picker_filter.' + self.instance.uuid,
                            function () {
                                let term = $(this).val();

                                $('> li', self.pickerItems).each(function () {
                                    let regex = new RegExp(term, 'i');

                                    if ($(this).data('label').match(regex)) {
                                        $(this).show();
                                    } else {
                                        $(this).hide();
                                    }
                                });
                            }
                        )
                        .on('click.tify_select.picker_filter.' + self.instance.uuid,
                            function (e) {
                                e.stopPropagation();
                            }
                        );
                }
            },

            /**
             * Définition de la liste des événements à la fermeture de la liste de selection
             *
             * @private
             */
            _onClose: function () {
                let self = this;

                // Désactivation du clic en dehors du declencheurs
                $(doc).off('click.tify_select.outside' + self.instance.uuid);

                // Désactivation du positionnement de la liste de selection au scroll dans la fenêtre du navigateur
                $(win).off('scroll.tify_select.picker_position.' + self.instance.uuid);

                // Désactivation de la récupération de la liste des éléments au scroll dans la liste de selection
                self.pickerItems.off('scroll.tify_select.picker_list.' +
                    self.instance.uuid);

                // Désactivation du champs de saisie de filtrage de la liste de selection
                if (self.flags.hasFilter) {
                    self.pickerFilter.off('keyup.tify_select.picker_filter.' +
                        self.instance.uuid);
                }
            },

            /**
             * Définition de la liste des événements à l'ajout d'un élément dans la liste de selection
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _onAdd: function (item) {
                let self = this;

                if (item.new) {
                    // Activation du controle de suppression de l'élément
                    self._setRemovable(item);

                    // Activation du controle d'ordonnancement de l'élément
                    self._setSortable(item);

                    // Mise à jour du statut de selection de l'élément dans la liste de selection
                    self._setPicker(item);

                    // Mise à jour de l'éléments dans le controleur de traitement
                    self._addHandlerItem(item);
                }

                // Modification du statut de selection
                self.flags.hasSelection = true;
                self.el.attr('aria-selection', true);

                // Masque le champ de saisie d'autocompletion
                if (!self.flags.isMultiple && self.flags.hasAutocomplete) {
                    self.autocompleteInput.hide();
                }

                // Affiche la liste des éléments selectionnés
                self.selection.show();

                // Fermeture de la liste de selection
                self._close();

                // Indication de mise en avant de l'élément dans la liste des éléments sélectionnés
                self._highlight(item);

                // Mise à jour de la valeur du controleur
                self.el.val(self.handler.val());

                // Déclenchement des événements
                self._trigger('add');
            },

            /**
             * Définition de la liste des événements à l'ajout d'un élément dans la liste de selection
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _onRemove: function (item) {
                // Masque la liste des éléments selectionnés si elle est vide
                if (!this._count()) {
                    this.flags.hasSelection = false;
                    this.el.attr('aria-selection', false);
                    if (!this.flags.isMultiple) {
                        this.autocompleteInput.show();
                    }
                    this.selection.hide();
                }

                // Mise à jour de la valeur du controleur
                this.el.val(this.handler.val());

                // Déclenchement des événements
                this._trigger('remove');
            },

            /**
             * Définition d'un élément.
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _setItem: function (item) {
                let self = this;

                self.items[item.index] = item;

                if (typeof self.items[item.index].picker_render === 'undefined') {
                    self.items[item.index].picker_render =
                        $('> [data-index="' + item.index + '"]', self.pickerItems).length
                            ? $('> [data-index="' + item.index + '"]', self.pickerItems)[0]
                            : '<li ' +
                            'data-label="' + $('<div>').text(item.label).html() + '" ' +
                            'data-value="' + item.value + '" ' +
                            'data-index="' + item.index + '" ' +
                            '>' +
                            item.label +
                            '</li>';
                }

                if (typeof self.items[item.index].selected_render === 'undefined') {
                    self.items[item.index].selected_render =
                        $('> [data-index="' + item.index + '"]', self.selection).length
                            ? $('> [data-index="' + item.index + '"]', self.selection)[0]
                            : '<li ' +
                            'data-label="' + $('<div>').text(item.label).html() + '" ' +
                            'data-value="' + item.value + '" ' +
                            'data-index="' + item.index + '" ' +
                            '>' +
                            item.label +
                            '</li>';
                }
            },

            /**
             * Ajout d'un élément dans la liste de selection
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _addPickerItem: function (item) {
                let self = this;

                // Bypass - l'élément est déjà  présent
                if ($('> [data-value="' + item.value + '"]',
                    self.pickerItems).length) {
                    return;
                }

                // Ajout de l'élément dans la liste des éléments sélectionnés
                $(self.items[item.index].picker_render)
                    .appendTo(self.pickerItems).on(
                    'click.select-js.picker_item_select.' + self.instance.uuid,
                    function () {
                        self._addSelectedItem(item);
                    }
                );

                // Mise à jour du statut de selection de l'élément dans la liste de selection
                self._setPicker(item);
            },

            /**
             * Définition des attribut d'un l'élément dans la liste de selection
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _setPicker: function (item) {
                let self = this;

                if ($('> [data-value="' + item.value + '"]', self.selection).length) {
                    $('> [data-value="' + item.value + '"]', self.pickerItems).attr('aria-selected', true);
                } else {
                    $('> [data-value="' + item.value + '"]', self.pickerItems).attr('aria-selected', false);
                }
            },

            /**
             * Ajout d'un élément dans le selecteur de gestion de traitement
             *
             * @param item Attributs de l'élément {
             *      @var mixed value Valeur de retour
             *      @var string label Intitulé de qualification
             *      @var string select Rendu dans la liste des éléments selectionnés
             *      @var string picker Rendu dans la liste de selection
             * }
             *
             * @private
             */
            _addHandlerItem: function (item) {
                if (!$('[data-index="' + item.index + '"]', this.handler).length) {
                    $('<option value="' + item.value + '" data-index="' +
                        item.index + '" selected>' + item.label + '</option>').appendTo(this.handler);
                }
            },

            /**
             * Récupération de l'instance du widget d'ordonnancement des éléments.
             *
             * @uses $(selector).tifyselect('sortable');
             */
            sortable: function () {
                if (this.flags.isSortable) {
                    return this.sortable;
                }
            },

            /**
             * Ouverture de la liste de selection.
             *
             * @uses $(selector).tifyselect('open');
             */
            open: function () {
                if (!this.flags.isDisabled) {
                    this._open();
                }
            },

            /**
             * Ajout d'une valeur à la liste de selection.
             *
             * @param value
             *
             * @uses $(selector).tifyselect('add', {value});
             */
            add: function (value) {
                let self = this;
                $('> [data-value="' + value + '"]', self.pickerItems).trigger('click');

            },

            /**
             * Fermeture de la liste de selection.
             *
             * @uses $(selector).tifyselect('close');
             */
            close: function () {
                if (!this.flags.isDisabled) {
                    this._close();
                }
            },

            /**
             * Activation du controleur.
             *
             * @uses $(selector).tifyselect('enable');
             */
            enable: function () {
                if (this.flags.isDisabled) {
                    this._enable();
                }
            },

            /**
             * Désactivation du controleur.
             *
             * @uses $(selector).tifyselect('disable');
             */
            disable: function () {
                if (!this.flags.isDisabled) {
                    this._disable();
                }
            },

            /**
             * Destruction du controleur.
             *
             * @uses $(selector).tifyselect('destroy');
             */
            destroy: function () {
                this.el.remove();
                this.picker.remove();
            }
        });
})(jQuery, document, window);

jQuery(document).ready(function ($) {
    $('[data-control="select-js"]').tifyselect();

    $(document).on('mouseenter.field.select-js', '[data-control="select-js"]', function () {
        $(this).each(function () {
            $(this).tifyselect();
        });
    });
});