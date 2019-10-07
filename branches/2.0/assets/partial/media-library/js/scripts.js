/* global wp */
'use strict';

import jQuery from 'jquery';
import 'presstify-framework/observer/js/scripts';

jQuery(function ($) {
  $.widget('tify.tifyMediaLibrary', {
    widgetEventPrefix: 'media-library:',
    id: undefined,
    options: {},
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this._initOptions();
      this._initEvents();
      this._initLibrary();
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
      this._on(this.el, {'click [data-control="media-library.open"]': this._onOpen});
    },
    // Initialisation des événements.
    _initLibrary: function () {
      let self = this,
          o = this.option();

      this.library = wp.media(o);

      this.library.on('select', function () {
        let selection = self.library.state().get('selection'),
            items = [];

        selection.map(function (attachment) {
          items.push(attachment.toJSON());
        });
        self._trigger('select', null, [items]);
      });
    },
    // EVENENEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    _onOpen: function (e) {
      e.preventDefault();
      this.open();
    },
    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------
    open: function () {
      this.library.open();
    }
  });

  $(document).ready(function () {
    $('[data-control="media-library"]')
        .tifyMediaLibrary()
        .tifyObserver({
          selector: '[data-control="media-library"]',
          func: function (i, target) {
            $(target).tifyMediaLibrary();
          }
        });
  });
});