/* global tify */

"use strict";

/**
 * @param {{ajax_url:string}} tify
 */
!(function ($) {
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
            // Liste des attributs de configuration par défaut.
            options: {
                autocomplete: false,
                classes: {
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
                    selectionItemRemove: 'FieldSelectJs-selectionItemRemove',
                    selectionItemSort: 'FieldSelectJs-selectionItemSort',
                    trigger: 'FieldSelectJs-trigger',
                    triggerHandler: 'FieldSelectJs-triggerHandler'
                },
                disabled: false,
                duplicate: false,
                id: '',
                max: -1,
                multiple: false,
                picker: {
                    appendTo: '',
                    class: '',
                    delta: {
                        top: 0,
                        left: 0,
                        width: 0
                    },
                    filter: false,
                    loader: '',
                    more: '+',
                    placement: 'clever'
                },
                removable: true,
                sortable: {},
                source: {},
                trigger: {
                    arrow: true,
                    class: ''
                }
            },

            // Instanciation de l'élément.
            _create: function () {
                this.instance = this;

                this.el = this.element;

                this.flags = {
                    hasArrow: true,
                    hasAutocomplete: false,
                    hasFilter: false,
                    hasSelection: false,
                    hasSource: false,
                    isComplete: false,
                    isDisabled: true,
                    isDuplicable: false,
                    isMultiple: false,
                    isOpen: false,
                    isRemovable: true,
                    isSortable: false,
                    onAutocomplete:false,
                    page: 1,
                    cache: undefined
                };

                this.items = [];

                this.selected = [];

                this._initOptions();
                this._initFlags();
                this._initControls();
                this._initItems();

                if (this.flags.isDisabled) {
                    this._doDisable();
                } else {
                    this._doEnable();
                }
            },

            // INITIALISATION
            // ---------------------------------------------------------------------------------------------------------
            // Initialisation des attributs de configuration.
            _initOptions: function () {
                $.extend(
                    true,
                    this.options,
                    $.parseJSON(decodeURIComponent(this.el.data('options')))
                );
            },

            // Initialisation des indicateurs d'état.
            _initFlags: function () {
                this.flags.hasAutocomplete = !!this.option('autocomplete');
                this.flags.hasSource = (this.option('source') !== false);
                this.flags.hasArrow = this.option('trigger.arrow');
                this.flags.isDisabled = !!this.option('disabled');
                this.flags.isMultiple = !!this.option('multiple');
                this.flags.isDuplicable = !!(this.option('duplicate') && this.flags.isMultiple);
                this.flags.isRemovable = !!(this.option('removable') && this.flags.isMultiple);
                this.flags.isSortable = (this.option('sortable') !== false) && this.flags.isMultiple;
            },

            // Initialisation des agents de contrôle.
            _initControls: function () {
                this._initControlElement();
                this._initControlHandler();
                this._initControlTrigger();
                this._initControlPicker();
                this._initControlSelection();
                if (this.flags.hasAutocomplete) {
                    this._initControlAutocomplete();
                }
            },

            // Initialisation du controleur d'autocompletion.
            _initControlAutocomplete: function () {
                this.autocompleteInput = $('<input data-control="select-js.autocomplete" autocomplete="off"/>')
                    .prependTo(this.trigger)
                    .addClass(this.option('classes.autocompleteInput'));

                if (typeof this.option('autocomplete') !== 'object') {
                    this.option('autocomplete', {});
                }

                this.flags.hasAutocomplete = true;
                this.el.attr('aria-autocomplete', true);

                if (this.flags.isMultiple) {
                    this.selection.insertAfter(this.trigger);
                }
            },

            // Initialisation du controleur principal.
            _initControlElement: function () {
                this.el.attr('aria-selection', this.flags.hasSelection);
                this.el.attr('aria-multiple', this.flags.isMultiple);
                this.el.attr('aria-sortable', this.flags.isSortable);
                this.el.attr('aria-duplicable', this.flags.isDuplicable);
                this.el.attr('aria-arrow', this.flags.hasArrow);
            },

            // Initialisation du controleur de traitement.
            _initControlHandler: function () {
                this.handler = $('[data-control="select-js.handler"]', this.el)
                    .addClass(this.option('classes.handler'));
            },

            // Initialisation des éléments de listes.
            _initItems: function () {
                let self = this;

                if ($('option', this.handler).length) {
                    self._selectedSet(this.handler.val());

                    $('option', this.handler).each(function (i, el) {
                        let $el = $(el);

                        self._setItem(i, $el.val(), $el.text());

                        $el.remove();
                    });
                }

                this.items.forEach(function(item, index) {
                    self._pickerAddItem(index);

                    if (self._selectedHas(item.value)) {
                        self._handlerAddItem(index);
                        self._selectionAddItem(index);
                        self._pickerAddSelected(index);
                    }
                });

                this.handler.val(this.selected);
            },

            // Initialisation du controleur de la liste de selection.
            _initControlPicker: function () {
                this.picker = $('<div data-control="select-js.picker"/>')
                    .appendTo(this.el)
                    .addClass(this.option('classes.picker'))
                    .attr('aria-multiple', this.flags.isMultiple)
                    .attr('aria-duplicable', this.flags.isDuplicable);

                this.pickerItems = $('<ul data-control="select-js.picker.items"/>')
                    .appendTo(this.picker)
                    .addClass(this.option('classes.pickerItems'));

                let $appendTo = $(this.option('picker.appendTo'));
                if (!$appendTo.length) {
                    $appendTo = $('body');
                }
                this.picker
                    .appendTo($appendTo)
                    .addClass(this.option('picker.class'));

                if (this.option('picker.filter')) {
                    this.flags.hasFilter = true;
                    this.picker.attr('aria-filter', true);

                    this.pickerFilter = $('<input data-control="select-js.picker.filter" autocomplete="off"/>')
                        .prependTo(this.picker)
                        .addClass(this.option('classes.pickerFilter'));
                }

                this.pickerLoader = $('<div data-control="select-js.picker.loader"/>')
                    .html(this.option('picker.loader'))
                    .prependTo(this.picker)
                    .addClass(this.option('classes.pickerLoader'));

                if (this.flags.hasSource) {
                    this.pickerMore = $('<a href="#" data-control="select-js.picker.more"/>')
                        .html(this.option('picker.more'))
                        .prependTo(this.picker)
                        .addClass(this.option('classes.pickerMore'));

                    this.picker.attr('aria-complete', false);
                }
            },

            // Initialisation du controleur de liste des éléments sélectionnés.
            _initControlSelection: function () {
                let self = this;

                this.selection = $('<ul data-control="select-js.selection"/>')
                    .appendTo(this.trigger)
                    .addClass(this.option('classes.selection'));

                if (this.flags.isSortable) {
                    let sortable = $.extend(
                        {
                            handle: '[data-control="select-js.selection.item.sort"]',
                            containment: 'parent',
                            update: function () {
                                self._doSort();
                            }
                        },
                        this.option('sortable')
                    );
                    this.option('sortable', sortable);

                    this.sortable = this.selection.sortable(sortable);
                } else {
                    this.sortable = undefined;
                }
            },

            // Initialisation du controleur de déclenchement d'affichage de la liste selection.
            _initControlTrigger: function () {
                this.trigger = $('<div data-control="select-js.trigger"/>')
                    .appendTo(this.el)
                    .addClass(this.option('classes.trigger'));

                this.triggerHandler = $('<a href="#" data-control="select-js.trigger.handler"/>')
                    .appendTo(this.trigger)
                    .addClass(this.option('classes.triggerHandler'));
            },

            // SETTER
            // ---------------------------------------------------------------------------------------------------------
            // Définition d'un élément du controleur de traitement.
            _setItem: function (index, value, content) {
                let item = {
                    content: content,
                    handler:undefined,
                    index: index,
                    picker: undefined,
                    selection: undefined,
                    value: value
                };
                item.handler = this._setItemHandler(null, item);
                item.picker = this._setItemPicker(null, item);
                item.selection = this._setItemSelection(null, item);

                this.items[index] = item;
            },

            // Définition d'un élément du controleur de traitement.
            _setItemHandler: function (content, item) {
                return $('<option/>')
                    .attr('value', item.value)
                    .attr('data-index', item.index)
                    .text(content ? content : item.content);
            },

            // Définition d'un élément du controleur de la liste de selection.
            _setItemPicker: function (content, item) {
                return $('<li data-control="select-js.picker.item"/>')
                    .attr('data-content', item.content)
                    .attr('data-index', item.index)
                    .attr('data-value', item.value)
                    .addClass(this.option('classes.pickerItem'))
                    .html(content ? content : item.content);
            },

            // Définition d'un élément du controleur de la liste des éléments sélectionnés.
            _setItemSelection: function (content, item) {
                let $selectionItem = $('<li data-control="select-js.selection.item"/>')
                    .attr('data-content', item.content)
                    .attr('data-index', item.index)
                    .attr('data-value', item.value)
                    .attr('aria-removable', this.flags.isRemovable)
                    .attr('aria-sortable', this.flags.isSortable)
                    .addClass(this.option('classes.selectionItem'))
                    .html(content ? content : item.content);

                if (this.flags.isRemovable) {
                    $('<a href="#" data-control="select-js.selection.item.remove"/>')
                        .appendTo($selectionItem)
                        .addClass(this.option('classes.selectionItemRemove'))
                        .text('×');
                }

                if (this.flags.isSortable) {
                    if (this.option('sortable.handle')) {
                        $('<span data-control="select-js.selection.item.sort"/>')
                            .appendTo($selectionItem)
                            .addClass(this.option('classes.selectionItemSort'))
                            .text('...');
                    }
                }

                return $selectionItem;
            },

            // Définition de la requête de récupération des éléments complète.
            _setQueryItemsComplete: function () {
                this.flags.isComplete = true;
                this.picker.attr('aria-complete', true);
                this.flags.page = 1;

                this._offPickerMoreQueryItems();
            },

            // GETTER
            // ---------------------------------------------------------------------------------------------------------
            // Récupération d'un élément.
            _getItem: function (i) {
                return this.items[i];
            },

            // Récupération de l'indice d'un élément selon sa valeur
            _getItemIndex: function(value) {
                let index = this.items.findIndex(function(item) {
                   return item.value === value;
                });

                if(index > -1) {
                    return index;
                } else {
                    return this.items.length;
                }
            },

            // Arguments de requête Ajax de récupération des éléments.
            _getQueryArgs: function () {
                return {
                    page: this.flags.page,
                    per_page: this.option('source.query_args.per_page') || 20,
                    term: this.flags.hasAutocomplete ? this.autocompleteInput.val().toString() : ''
                };
            },

            // MODIFIER
            // ---------------------------------------------------------------------------------------------------------
            // Ajout d'un élément au controleur de traitement.
            _handlerAddItem(index) {
                this.items[index].handler.appendTo(this.handler);
            },

            // Suppression d'un élément du controleur de traitement.
            _handlerFlushItems() {
                this.handler.empty();
            },

            // Suppression d'un élément du controleur de traitement.
            _handlerRemoveItem(index) {
                this.items[index].handler.remove();
            },

            // Ajout d'une selection à la liste de sélection.
            _pickerAddItem(index) {
                let $item = this.items[index].picker.appendTo(this.pickerItems);

                this._onPickerItemClick($item);
            },

            // Ajout d'une selection à la liste de sélection.
            _pickerAddSelected(index) {
                this.items[index].picker.attr('aria-selected', true);
            },

            // Suppression d'une selection à la liste de sélection.
            _pickerRemoveSelected(index) {
                this.items[index].picker.attr('aria-selected', false);
            },

            // Vidage de l'ensemble des sélections de la liste de sélection.
            _pickerFlushSelected() {
                $.each(this.items, function (u, v) {
                    v.picker.attr('aria-selected', false);
                });
            },

            // Ajout de la sélection d'un élement.
            _selectedAdd: function (value) {
                let index = this.selected.indexOf(value.toString());

                if (index === -1) {
                    this.selected.push(value.toString());
                }
            },

            // Vérification si un élément est selectionné.
            _selectedHas: function (value) {
                return this.selected.indexOf(value.toString()) !== -1;
            },

            // Définition de la liste des éléments selectionnés.
            _selectedSet: function (value) {
                this.selected = value.toString().split(',');
            },

            // Mise à jour de la sélection d'un élement.
            _selectedUpdate: function (value) {
                let index = this.selected.indexOf(value.toString());

                if (index === -1) {
                    this.selected.push(value.toString());
                    return 1;
                } else {
                    this.selected.splice(index, 1);
                    return 0;
                }
            },

            // Suppression de la sélection d'un élement.
            _selectedRemove: function (value) {
                let index = this.selected.indexOf(value.toString());

                if (index > -1) {
                    this.selected.splice(index, 1);
                }
            },

            // Ajout d'une selection à la liste des éléments sélectionnés.
            _selectionAddItem(index) {
                let $item = this.items[index].selection;

                $item.appendTo(this.selection);

                if (this.flags.isRemovable) {
                    this._onSelectionItemRemoveClick($item);
                }
            },

            // Vidage de l'ensemble des sélections de la liste des éléments sélectionnés.
            _selectionFlushItems() {
                this.selection.empty();
            },

            // Suppression d'une selection de la liste des éléments sélectionnés.
            _selectionRemoveItem(index) {
                this.items[index].selection.remove();
            },

            // ACTIONS
            // ---------------------------------------------------------------------------------------------------------
            // Récupération de la liste des éléments via Ajax.
            _doAjaxQuery: function () {
                let self = this;

                if (this.flags.hasSource) {
                    if (this.xhr !== undefined) {
                        this.xhr.abort();
                    }

                    self._doPickerLoaderShow();

                    let query_args = $.extend(
                        this.option('source.query_args'),
                        this._getQueryArgs()
                    );
                    this.option('source.query_args', query_args);

                    this.xhr = $.ajax({
                        url: tify.ajax_url,
                        data: self.option('source'),
                        method: 'post'
                    }).done(function (data) {
                        if (data.length) {
                            $.each(data, function (u, attrs) {
                                let value = attrs.value.toString(),
                                    index = self._getItemIndex(value);

                                if(self.items.length === index){
                                    self._setItem(index, attrs.value.toString(), attrs.content);
                                }
                                self._pickerAddItem(index);
                            });

                            if (data.length < self.option('source.query_args.per_page')) {
                                self._setQueryItemsComplete();
                            } else {
                                self._doPageIncrease();
                            }
                        } else {
                            self._setQueryItemsComplete();
                        }
                    }).always(function () {
                        self._doPickerLoaderHide();
                        self.xhr = undefined;
                    });
                }
            },

            // Récupération des données en cache
            _doCacheRestore: function () {
                if (this.flags.cache !== undefined) {
                    this.flags.isComplete = this.flags.cache.complete || false;
                    this.flags.page = this.flags.cache.page || 1;
                }
            },

            // Modification d'un élément dans la liste de selection.
            _doChange: function (index) {
                let item = this._getItem(index);

                if (this.flags.isMultiple) {
                    if (this._selectedUpdate(item.value)) {
                        this._handlerAddItem(item.index);
                        this._selectionAddItem(item.index);
                        this._pickerAddSelected(item.index);
                    } else {
                        this._handlerRemoveItem(item.index);
                        this._selectionRemoveItem(item.index);
                        this._pickerRemoveSelected(item.index);
                    }

                    if (this.flags.onAutocomplete) {
                        this._doClose();
                    }
                } else {
                    this._selectedSet(item.value);
                    this._handlerFlushItems();
                    this._handlerAddItem(item.index);
                    this._selectionFlushItems();
                    this._selectionAddItem(item.index);
                    this._pickerFlushSelected();
                    this._pickerAddSelected(item.index);

                    this._doClose();
                }
                this._doPickerPosition();
                this._doHighlight(item.value);

                this.handler.val(this.selected);
            },

            // Fermeture de la liste de selection.
            _doClose: function () {
                this._offOutsideClick();

                this.flags.isOpen = false;
                this.el.attr('aria-open', false);
                this.picker.attr('aria-open', false);
            },

            // Désactivation du controleur.
            _doDisable: function () {
                this._offTriggerHandlerClick();

                this.flags.isDisabled = true;
                this.el.attr('aria-disabled', true);
                this.handler.prop('disabled', true);
            },

            // Activation du controleur.
            _doEnable: function () {
                this.flags.isDisabled = false;
                this.el.attr('aria-disabled', false);
                this.handler.prop('disabled', false);

                this._onTriggerHandlerClick();

                if (this.flags.hasFilter) {
                    this._onPickerFilterKeyup();
                }

                if (this.flags.hasAutocomplete) {
                    this._onAutocomplete();
                }
            },

            // Mise en avant des éléments dans la liste des éléments sélectionnés.
            _doHighlight: function (value) {
                $('[data-control="select-js.selection.item"][data-value="' + value + '"]', this.selection)
                    .attr('aria-highlight', true)
                    .one(
                        'webkitAnimationEnd oanimationend msAnimationEnd animationend',
                        function () {
                            $(this).attr('aria-highlight', false);
                        }
                    );
            },

            // Augmentation de la pagination.
            _doPageIncrease: function () {
                this.flags.page++;
            },

            // Masquage de l'indicateur de préchargement.
            _doPickerLoaderHide: function () {
                this.picker.attr('aria-loader', false);
            },

            // Affichage de l'indicateur de préchargement.
            _doPickerLoaderShow: function () {
                this.picker.attr('aria-loader', true);
            },

            // Positionnement de la liste de selection dans le DOM.
            _doPickerPosition: function () {
                let offset = {},
                    placement = this.option('picker.placement');

                $.extend(
                    offset,
                    this.trigger.offset(),
                    {width: this.trigger.outerWidth()}
                );

                if (this.option('picker.delta.top')) {
                    offset.top += this.option('picker.delta.top');
                }
                if (this.option('picker.delta.left')) {
                    offset.left += this.option('picker.delta.left');
                }
                if (this.option('picker.delta.width')) {
                    offset.width += this.option('picker.delta.width');
                }

                if (placement === 'clever') {
                    placement = ((this.window.outerHeight() + this.window.scrollTop()) < offset.top + this.picker.outerHeight()) ?
                        'top' : 'bottom';
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

            // Ouverture de la liste de selection.
            _doOpen: function () {
                this._onOutsideClick();

                if (!this.flags.isComplete) {
                    this._onPickerMoreQueryItems();
                }

                this.flags.isOpen = true;
                this.el.attr('aria-open', true);
                this.picker.attr('aria-open', true);

                this._doPickerPosition();

                if (!this.flags.isComplete && !this.flags.onAutocomplete) {
                    this._doAjaxQuery();
                }

                if (this.flags.hasFilter) {
                    this.pickerFilter.focus();
                }
            },

            // Suppression d'un élément de la liste de selection.
            _doRemove: function (index) {
                let item = this._getItem(index);

                this._selectedRemove(item.value);
                this._handlerRemoveItem(item.index);
                this._selectionRemoveItem(item.index);
                this._pickerRemoveSelected(item.index);

                this.handler.val(this.selected);
            },

            // Réordonnancement d'un élément de la liste de selection.
            _doSort: function () {
                let self = this;

                self._handlerFlushItems();
                $('[data-control="select-js.selection.item"]', self.selection).each(function () {
                    self._handlerAddItem($(this).data('index'));
                });
            },

            //EVENTS
            // ---------------------------------------------------------------------------------------------------------
            // Activation de la saisie par autocompletion.
            _onAutocomplete: function () {
                let self = this;

                this.autocompleteInput
                    .focus(function() {
                        if(self.flags.onAutocomplete && !self.pickerItems.is(':empty')) {
                            self._doOpen();
                        } else {
                            self._doClose();
                        }

                        $(this).on('keypress.select-js.autocomplete.' + self.instance.uuid, function () {
                            if ($(this).val()) {
                                if (self.flags.cache === undefined) {
                                    self.flags.cache = { complete: self.flags.isComplete, page: self.flags.page };
                                }
                                self.flags.isComplete = false;
                                self.flags.page = 1;
                                self.flags.onAutocomplete = true;

                                self.pickerItems.empty();

                                self._doPickerLoaderShow();
                                self._doOpen();

                                if (self.timeout !== undefined) {
                                    clearTimeout(self.timeout);
                                }
                                if (self.xhr !== undefined) {
                                    self.xhr.abort();
                                }

                                self.timeout = setTimeout(function () {
                                    self._doAjaxQuery();
                                    self.xhr.done(function (data) {
                                        if (!data.length) {
                                            self._doClose();
                                        }
                                    });
                                }, 1000);
                            } else {
                                self.flags.onAutocomplete = false;

                                self._doCacheRestore();

                                self.items.forEach(function (item) {
                                    self._pickerAddItem(item.index);
                                });
                            }
                        });
                    })
                    .focusout(function() {
                        self._doCacheRestore();

                        $(this).off('keyup.select-js.autocomplete.' + self.instance.uuid);
                    });
            },

            // Activation du filtrage de la liste de selection.
            _onPickerFilterKeyup: function () {
                let self = this;

                this.pickerFilter.on('keyup.select-js.picker.filter.' + this.instance.uuid, function () {
                    let term = $(this).val().toString();

                    $('[data-control="select-js.picker.item"]', self.pickerItems).each(function () {
                        let regex = new RegExp(term, 'i');

                        if ($(this).data('content').match(regex)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            },

            // Activation du clic sur les élements de la liste de sélection.
            _onPickerItemClick: function ($pickerItem) {
                let self = this;

                $pickerItem.on(
                    'click.select-js.picker.item.' + this.instance.uuid,
                    function (e) {

                        if ($(this).is(':not([aria-disabled="true"])')) {
                            e.preventDefault();

                            self._doChange($(this).data('index'));
                        }
                    }
                );
            },

            // Activation de la récupération d'éléments supplémentaires dans la liste de selection.
            _onPickerMoreQueryItems: function () {
                let self = this;

                this.pickerItems.on('scroll.select-js.picker.items.' + this.instance.uuid, function () {
                    if (self.xhr === undefined) {
                        if (($(this).prop('scrollHeight') - $(this).innerHeight() - $(this).scrollTop()) < 20) {
                            self._doAjaxQuery();
                        }
                    }
                });

                this.pickerMore.on('click.select-js.picker.more.' + this.instance.uuid, function (e) {
                    e.preventDefault();

                    if (self.xhr === undefined) {
                        self._doAjaxQuery();
                    }
                });
            },

            // Désactivation de la récupération d'éléments supplémentaires dans la liste de selection.
            _offPickerMoreQueryItems: function () {
                this.pickerItems.off('scroll.select-js.picker.items.' + this.instance.uuid);
                this.pickerMore.off('click.select-js.picker.more.' + this.instance.uuid);
            },

            // Activation de la suppression d'un éléments séléctionnés.
            _onSelectionItemRemoveClick: function ($selectionItem) {
                let self = this;

                $selectionItem.find('[data-control="select-js.selection.item.remove"]').on(
                    'click.select-js.selection.item.remove.' + this.instance.uuid, function (e) {
                        e.preventDefault();

                        self._doRemove($(this).closest('[data-control="select-js.selection.item"]').data('index'));
                    });
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

            // Désactivation du clic sur le controleur d'affichage de la liste de sélection.
            _offTriggerHandlerClick: function () {
                this.triggerHandler.off('click.select-js.trigger.handler.' + this.instance.uuid);
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

            // ACCESSOR
            // ---------------------------------------------------------------------------------------------------------
            /**
             * Ajout d'une valeur à la liste de selection.
             *
             * @param value
             *
             * @uses $(selector).tifyselect('add', {value});
             */
            add: function (value) {
                $('[data-control=""][data-value="' + value + '"]', self.pickerItems).trigger('click');
            },

            /**
             * Fermeture de la liste de selection.
             *
             * @uses $(selector).tifyselect('close');
             */
            close: function () {
                this._doClose();
            },

            /**
             * Destruction du controleur.
             *
             * @uses $(selector).tifyselect('destroy');
             */
            destroy: function () {
                this.el.remove();
                this.picker.remove();
            },

            /**
             * Désactivation du controleur.
             *
             * @uses $(selector).tifyselect('disable');
             */
            disable: function () {
                this._doDisable();
            },

            /**
             * Activation du controleur.
             *
             * @uses $(selector).tifyselect('enable');
             */
            enable: function () {
                this._doEnable();
            },

            /**
             * Ouverture de la liste de selection.
             *
             * @uses $(selector).tifyselect('open');
             */
            open: function () {
                this._doOpen();
            },

            /**
             * Récupération de l'instance du widget d'ordonnancement des éléments.
             *
             * @uses $(selector).tifyselect('sortable');
             */
            sortable: function () {
                return this.sortable;
            }
        }
    );

    $(document).ready(function ($) {
        $('[data-control="select-js"]').tifyselect();

        $(document).on('mouseenter.field.select-js', '[data-control="select-js"]', function () {
            $(this).each(function () {
                $(this).tifyselect();
            });
        });
    });
})(jQuery);