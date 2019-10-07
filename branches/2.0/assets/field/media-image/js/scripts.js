/* global wp */
'use strict';

import jQuery from 'jquery';
import 'presstify-framework/partial/media-library/js/scripts';

jQuery(function ($) {
  $.widget('tify.tifyMediaImage', {
    widgetEventPrefix: 'media-image:',
    id: undefined,
    options: {},
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this._initOptions();
      this._initEvents();
      this._initController();
    },
    selector: {
      input: '[data-control="media-image.input"]',
      opener: '[data-control="media-image.open"]',
      preview: '[data-control="media-image.preview"]',
      remover: '[data-control="media-image.remove"]'
    },
    // INTIALISATIONS
    // -----------------------------------------------------------------------------------------------------------------
    // Initialisation des attributs de configuration.
    _initOptions: function () {
      $.extend(
          true,
          this.options,
          this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
      );
    },
    // Initialisation des événements.
    _initEvents: function () {
      this._on(this.el, {'click [data-control="media-image.open"]': this._onClickOpen});
      this._on(this.el, {'click [data-control="media-image.remove"]': this._onClickRemove});
    },
    // Initialisation de la médiathèque.
    _initController: function () {
      let self = this;

      if (this.library === undefined) {
        this.library = $(this.el).tifyMediaLibrary({multiple: false, library: {type: 'image'}});
        $(this.el).on('media-library:select', function (e, items) {
          let item = items[0] || {};

          $(self.selector.preview, self.el).css('background-image', 'url(' + item.url + '').show();
          $(self.selector.input, self.el).val(item.id);
          self.el.attr('aria-selected', 'true');
        });
      }
    },
    // EVENEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    // Au clic sur le bouton d'ouverture de la médiathèque.
    _onClickOpen: function (e) {
      e.preventDefault();
      this.open();
    },
    // Au clic sur le bouton de suppression.
    _onClickRemove: function (e) {
      e.preventDefault();
      this.remove();
    },
    // ACCESSEURS.
    // -----------------------------------------------------------------------------------------------------------------
    // Ouverture de la médiathèque.
    open: function () {
      this.library.tifyMediaLibrary('open');
    },
    // Suppression de l'image.
    remove: function () {
      let self = this;

      self.el.attr('aria-selected', 'false');

      $(this.selector.preview, this.el).fadeOut(function () {
        $(self.selector.preview, self.el).css('background-image', '');
        $(self.selector.input, self.el).val('');
      });
    }
  });

  $(document).ready(function () {
    $(document).on('mouseenter', '[data-control="media-image"]', function() {
      $(this).tifyMediaImage();
    });
  });
});