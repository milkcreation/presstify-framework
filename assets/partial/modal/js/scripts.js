'use strict';

import jQuery from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';
import 'bootstrap/js/src/modal';

jQuery(function ($) {
  $.widget('tify.tifyModal', {
    widgetEventPrefix: 'modal:',
    options: {
      classes:{
        body: 'modal-body',
        close: 'modal-close',
        content: 'modal-content',
        dialog: 'modal-dialog',
        footer: 'modal-footer',
        header: 'modal-header',
      }
    },
    control: {
      body: 'modal.content.body',
      close: 'modal.close',
      content: 'modal.content',
      dialog: 'modal.dialog',
      footer: 'modal.content.footer',
      header: 'modal.content.header',
    },
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this.flags = {
        hasBody: true,
        hasClose: true,
        hasFooter: true,
        hasHeader: true,
        isAnimated: true
      };

      this._initOptions();
      this._initFlags();
      this._initControls();
      this._initEvents();
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
    // Initialisation des indicateurs d'état.
    _initFlags: function () {
      this.flags.hasBody = !!this.option('body');
      this.flags.hasClose = !!this.option('close');
      this.flags.hasFooter = !!this.option('footer');
      this.flags.hasHeader = !!this.option('header');
      this.flags.isAnimated = !!this.option('animated');
    },
    // Initialisation des agents de contrôle.
    _initControls: function () {
      this.el.addClass('modal');

      /*if (this.flags.isAnimated) {
        this.el.addClass('fade');
      }*/

      this.el.modal(this.option('engine') || {});

      let $dialog = $('[data-control="' + this.control.dialog + '"]', this.el);
      if (!$dialog.length) {
        $dialog = $('<div data-control="' + this.control.dialog + '"/>').appendTo(this.el);
      }
      $dialog.addClass(this.option('classes.dialog'));
      if (this.option('size')) {
        $dialog.addClass(this.option('size'));
      }

      let $content = $('[data-control="' + this.control.content + '"]', $dialog);
      if (!$content.length) {
        $content = $('<div data-control="' + this.control.content + '"/>').appendTo($dialog);
      }
      $content.addClass(this.option('classes.content'));

      if (this.flags.hasClose) {
        let $close = $('[data-control="' + this.control.close + '"]', $content);
        if (!$close.length) {
          $close = $('<button type="button" data-control="' + this.control.close + '"/>').appendTo($content);
        }
        $close.addClass(this.option('classes.close'));
      }

      if (this.flags.hasHeader) {
        let $header = $('[data-control="' + this.control.header + '"]', $content);
        if (!$header.length) {
          $header = $('<div data-control="' + this.control.header + '"/>').appendTo($content);
        }
        $header.addClass(this.option('classes.header'));
      }

      if (this.flags.hasBody) {
        let $body = $('[data-control="' + this.control.body + '"]', $content);
        if (!$body.length) {
          $body = $('<div data-control="' + this.control.body + '"/>').appendTo($content);
        }
        $body.addClass(this.option('classes.body'));
      }

      if (this.flags.hasFooter) {
        let $footer = $('[data-control="' + this.control.footer + '"]', $content);
        if (!$footer.length) {
          $footer = $('<div data-control="' + this.control.footer + '"/>').appendTo($content);
        }
        $footer.addClass(this.option('classes.footer'));
      }
    },
    // Initialisation des événements.
    _initEvents: function () {
      this._on(this.el, {'click [data-control="modal.open"]': this._onClickOpen});
      this._on(this.el, {'click [data-control="modal.close"]': this._onClickClose});
    },
    // EVENENEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    // Clique sur le bouton d'ouverture
    _onClickOpen: function (e) {
      e.preventDefault();
      this.open();
    },
    // Clique sur le bouton de fermeture
    _onClickClose: function (e) {
      e.preventDefault();
      this.close();
    },
    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------
    // Fermeture de la modale.
    close: function () {
      this.el.modal('hide');
    },
    // Ouverture de la modale.
    open: function () {
      this.el.modal('show');
    },
    // Bascule d'affichage de la modale.
    toggle: function () {
      this.el.modal('toggle');
    },
    // Mise à jour de la position de la modale.
    update: function () {
      this.el.modal('handleUpdate');
    },
    // Destruction de la modale.
    destroy: function () {
      this.el.modal('dispose');
    }
  });

  $(document).ready(function () {
    $('[data-control="modal"]').tifyModal();
    $.tify.observe('[data-control="modal"]', function (i, target) {
      $(target).tifyModal();
    });

    $(document).on('click', '[data-control="modal.trigger"]', function (e) {
      e.preventDefault();
      $('[data-id="' + $(this).data('target') + '"][data-control="modal"]').tifyModal('open');
    });
  });
  /*
  $(document).ready(function () {
    $('[data-control="modal"]')
        .modal()
        .on('shown.bs.modal', function () {
          let $modal = $(this),
            o = $.parseJSON(decodeURIComponent($modal.data('options')));

          if (tify[o.id] === undefined) {
            tify[o.id] = {};
          }

          if ($('.modal-content', $modal).length) {
            tify[o.id].original = $('.modal-content', $modal).html();
          }

          if (o.ajax) {
            if (tify[o.id].content === undefined) {
              $.ajax(o.ajax).done(function (resp) {
                if (resp.success) {
                  tify[o.id].content = resp.data;
                  $('.modal-content', $modal).html(resp.data);
                }
              });
            } else {
              $('.modal-content', $modal).html(tify[o.id].content);
            }
          }
        })
        .on('hidden.bs.modal', function () {
          let $modal = $(this),
              o = $.parseJSON(decodeURIComponent($modal.data('options')));

          if (o.ajax) {
            if (tify[o.id].original !== undefined) {
              $('.modal-content', $modal).html(tify[o.id].original);
            } else {
              $('.modal-content', $modal).empty();
            }
          }
        });
  });*/
});