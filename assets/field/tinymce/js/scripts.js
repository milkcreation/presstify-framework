/* global tify */
'use strict';

import jQuery from 'jquery';
import 'presstify-framework/observer/js/scripts';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';
import 'tinymce/jquery.tinymce';

jQuery(function ($) {
  if (typeof (tinyMCE) == 'undefined') {
    require('tinymce/tinymce');
    require('tinymce/themes/silver/index');
    if (tify.locale.language !== undefined) {
      try {
        require('tinymce-i18n/langs5/' + tify.locale.language);
      } catch (e) {
        console.log('Unavailable tinyMCE language ' + tify.locale.language);
      }
    }
  }

  $.widget('tify.tifyTinymce', {
    widgetEventPrefix: 'tinymce:',
    id: undefined,
    options: {},
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this._initOptions();
      this._initControls();
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
    // Initialisation des agents de control
    _initControls : function () {
      let o = this.option();

      if (typeof (tinyMCE) != 'undefined') {
        o = $.extend({}, tinyMCE.settings || {}, o);
      }

      this.el.tinymce(o);
    }
  });

  $(document).ready(function () {
    $('[data-control="tinymce"]')
        .tifyTinymce()
        .tifyObserver({
          selector: '[data-control="tinymce"]',
          func: function (i, target) {
            let o = $.parseJSON(decodeURIComponent($(this).data('options'))) || {};

            if (typeof (tinyMCE) != 'undefined') {
              o = $.extend({}, tinyMCE.settings || {}, o);
            }

            $(target).tinymce(o);
          }
        });
  });
});