'use strict'

import jQuery from 'jquery'
import 'jquery-ui/ui/core'
import 'jquery-ui/ui/widget'
import '../../../observer/js/scripts'

jQuery(function ($) {
  $.widget('tify.tifyBurgerButton', {
    widgetEventPrefix: 'burger-button:',
    id: undefined,
    options: {},
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      this._initOptions()
      this._initEvents()
    },
    // INITIALISATIONS.
    // -----------------------------------------------------------------------------------------------------------------
    // Initialisation des attributs de configuration.
    _initOptions: function () {
      $.extend(
          true,
          this.options,
          this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
      )
    },
    // Initialisation des événements.
    _initEvents: function () {
      this.handlers = this.option('handler') || [];

      this._doActiveTrigger()

      if (this.handlers.indexOf('click') !== -1) {
        this._on(this.el, {'click': this._onClick})
      }

      if (this.handlers.indexOf('hover') !== -1) {
        this._on(this.el, {'mouseenter': this._onEnter})
        this._on(this.el, {'mouseleave': this._onLeave})
      }
    },
    // ACTIONS
    _doActiveTrigger: function () {
      if (this.active() === true) {
        this._trigger('active')
      } else {
        this._trigger('inactive')
      }
    },
    // EVENEMENTS.
    // -----------------------------------------------------------------------------------------------------------------
    _onClick: function () {
      this._trigger('clicked')
      this.toggle()
    },
    _onEnter: function () {
      this._trigger('entered')
      this.toggle()
    },
    _onLeave: function () {
      this._trigger('leaved')
      this.toggle()
    },
    // ACCESSEURS.
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Vérification du statut d'activation.
     *
     * @uses $('[data-control="burger-button"]').tifyBurgerButton('active')
     *
     * @return boolean
     */
    active: function () {
      return this.el.hasClass('is-active')
    },
    /**
     * Bascule le statut d'activation.
     *
     * @uses $('[data-control="burger-button"]').tifyBurgerButton('toggle')
     *
     * @return boolean
     */
    toggle: function () {
      this.el.toggleClass('is-active')

      this._trigger('toggle')

      this._doActiveTrigger()
    },
    /**
     * Passe le statut a inactif.
     *
     * @uses $('[data-control="burger-button"]').tifyBurgerButton('close')
     *
     * @return boolean
     */
    close: function () {
      if (this.active() === true) {
        this.el.removeClass('is-active')

        this._trigger('toggle')

        this._doActiveTrigger()
      }
    },
    /**
     * Passe le statut a actif si nécessaire.
     *
     * @uses $('[data-control="burger-button"]').tifyBurgerButton('open')
     *
     * @return boolean
     */
    open: function () {
      if (this.active() === false) {
        this.el.addClass('is-active')

        this._trigger('toggle')

        this._doActiveTrigger()
      }
    }
  })

  $(document).ready(function () {
    $('[data-control="burger-button"]').tifyBurgerButton()

    $.tify.observe('[data-control="burger-button"]', function (i, target) {
      $(target).tifyBurgerButton()
    })
  })
})