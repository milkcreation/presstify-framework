"use strict";

import jQuery from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';
import 'jquery-ui/ui/widgets/sortable';
import 'presstify-framework/field/suggest/js/scripts';

jQuery(function ($) {
  $.widget('tify.tifyMetaboxRelatedPost', {
    widgetEventPrefix: 'metabox-related-post:',
    options: {},

    // INITIALISATION
    // -------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this._initOptions();
      this._initSuggest();
      this._initSortable();
      this._initItems();
    },

    // Initialisation des attributs de configuration.
    _initOptions: function () {
      $.extend(
          true,
          this.options,
          this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
      );
    },

    // Initialisation de l'ordonnancement des éléments.
    _initSuggest: function () {
      let self = this;

      $('[data-control="suggest"]', this.el).on('suggest:select', function () {
        let o = $.extend(true, {}, self.option('ajax') || {}, {
          data: {
            index: $('[data-control="metabox.related-post.item"]', self.el).length,
            post_id: $(this).val()
          }
        });

        $.ajax(o).done(function (resp) {
          if (resp.success) {
            let $item = $(resp.data).appendTo($('[data-control="metabox.related-post.items"]', self.el));
            self._setItem($item);
          }
        });
      });
    },

    // Initialisation de l'ordonnancement des éléments.
    _initSortable: function () {
      let self = this,
          o = $.extend({
            axis: 'y',
            handle: '[data-control="metabox.related-post.item.sort"]'
          }, self.option('sortable') || {}, {
            update: function () {
              self._doUpdateItemsOrder();
            }
          });

      $('[data-control="metabox.related-post.items"]', self.el).sortable(o).disableSelection();
    },

    // Initialisation de la liste des éléments.
    _initItems: function () {
      let self = this;

      $('[data-control="metabox.related-post.item"]', self.el).each(function () {
        self._setItem($(this));
      });
    },

    // DEFINITIONS.
    // -------------------------------------------------------------------------------------------------------------
    // Définition d'un élément.
    _setItem: function ($item) {
      let self = this;

      self._onItemMetasToggle($item);
      self._onItemRemove($item);
    },

    // ACTIONS.
    // -------------------------------------------------------------------------------------------------------------
    // Mise à jour des indicateurs d'ordre d'affichage.
    _doUpdateItemsOrder: function () {
      let self = this;

      $('[data-control="metabox.related-post.item.order"]', self.el).each(function (i) {
        $(this).val(i + 1);
      });
    },

    // EVENEMENTS.
    // -------------------------------------------------------------------------------------------------------------
    // Bascule d'affichage des métadonnées.
    _onItemMetasToggle: function ($item) {
      $('[data-control="metabox.related-post.item.metas-toggle"]', $item).on(
          'mouseenter mouseleave',
          function (e) {
            e.preventDefault();

            if ($item.attr('aria-metas') === 'true') {
              $item.attr('aria-metas', 'false')
            } else {
              $item.attr('aria-metas', 'true')
            }
          });
    },
    // Suppression d'un élément.
    _onItemRemove: function ($item) {
      let self = this;

      $('[data-control="metabox.related-post.item.remove"]', $item).on('click', function (e) {
        e.preventDefault();

        $item.fadeOut(function () {
          $(this).remove();
          self._doUpdateItemsOrder();
        });
      });
    },
  });

  $(document).ready(function () {
    $('[data-control="metabox.related-post"]').tifyMetaboxRelatedPost();
  });
});