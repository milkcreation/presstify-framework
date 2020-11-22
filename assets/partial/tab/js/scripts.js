'use strict'

import jQuery from 'jquery'
import 'jquery-ui/ui/core'
import 'jquery-ui/ui/widget'

jQuery(function ($) {
  $.widget('tify.tifyTab', {
    widgetEventPrefix: 'tab:',
    id: undefined,
    xhr: undefined,
    options: {},
    control: {
      pane: '[data-control="tab.content.pane"]',
      nav: '[data-control="tab.nav"]',
      navItem: '[data-control="tab.nav.item"]',
      navLink: '[data-control="tab.nav.link"]'
    },
    shown: false,
    // INITIALISATIONS.
    // -----------------------------------------------------------------------------------------------------------------
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this

      this.el = this.element

      this._initOptions()
      this._initEvents()
      this._initControls()
    },
    // INITIALISATION
    // -----------------------------------------------------------------------------------------------------------------
    // Initialisation des attributs de configuration.
    _initOptions: function () {
      $.extend(
          true,
          this.options,
          this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
      )
    },
    _initEvents: function () {
      let clickKey = 'click ' + this.control.navLink,
          events = {}
      events[clickKey] = this._onClick

      this._on(this.el, events)
    },
    _initControls: function () {
      let c = this.control

      $(c.pane, this.el).each(function () {
        $(this).attr('aria-hidden', 'true')
      })

      let active = this.option('active')
      if(active) {
        this.show(active)
      }

      if(!this.shown) {
        let $navLink = $('>' + c.nav + ':first > ' + c.navItem + ':first > ' + c.navLink, this.el)
        if ($navLink.length) {
          this.show($navLink.data('name'))
        }
      }
    },
    // EVENEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    _onClick: function (e) {
      e.preventDefault()

      let name = $(e.target).data('name')

      let ajax = this.option('ajax')
      if (ajax) {
        let _ajax = $.extend(true, ajax, {data: {active: name}});

        $.ajax(_ajax)
      }

      this.show(name)
    },
    // ACTIONS
    // -----------------------------------------------------------------------------------------------------------------
    _doShowNavLink($navLink, children) {
      $navLink.addClass('active')

      let $pane = $($navLink.attr('href'))

      this._doShowPane($pane, children)
    },
    _doShowPane($pane, children) {
      if ($pane.length) {
        $pane.addClass('active').attr('aria-hidden', 'false')

        if (children) {
          let c = this.control,
              $navLink = $('>' + c.nav + ' > ' + c.navItem + ' > ' + c.navLink + '.active', $pane)

          if (!$navLink.length) {
            $navLink = $('>' + c.nav + ' > ' + c.navItem + ':first > ' + c.navLink, $pane)
          }

          this._doShowNavLink($navLink, children)
        }
      }
    },
    _doShowParentsPane($pane) {
      let c = this.control,
          href = '#' + $pane.attr('id'),
          $navLink = $(c.navLink + '[href="' + href + '"]', this.el)

      if ($navLink.length) {
        this._doShowNavLink($navLink)

        let $parentPane = $navLink.closest(c.pane);
        if ($parentPane.length) {
          this._doShowParentsPane($parentPane)
        }
      }
    },
    _doHideNavLink($navLink, deactivate) {
      if (deactivate) {
        $navLink.removeClass('active')
      }

      let $pane = $($navLink.attr('href'))

      this._doHidePane($pane, deactivate)
    },
    _doHidePane($pane, deactivate) {
      if ($pane.length) {
        if (deactivate) {
          $pane.removeClass('active')
        }

        $pane.attr('aria-hidden', 'false')

        let self = this,
            c = this.control,
            $navLinks = $('>' + c.nav + ' > ' + c.navItem + ' > ' + c.navLink, $pane)

        if ($navLinks.length) {
          $navLinks.each(function () {
            self._doHideNavLink($(this))
          })
        }
      }
    },
    // ACCESSEURS
    // -----------------------------------------------------------------------------------------------------------------
    show(name) {
      this._trigger('show')

      let self = this,
          $navLink = $(this.control.navLink + '[data-name="' + name + '"]', this.el)

      if ($navLink.length) {
        let c = this.control,
            $closest = $navLink.closest(c.navItem),
            $siblings = $closest.siblings(),
            $parentPane = $navLink.closest(c.pane);

        this._doShowNavLink($navLink, true)

        if ($siblings.length) {
          $siblings.each(function () {
            let $siblingNavLink = $(c.navLink, $(this))
            if ($siblingNavLink.length) {
              self._doHideNavLink($siblingNavLink, true)
            }
          })
        }

        if ($parentPane.length) {
          this._doShowParentsPane($parentPane)
        }

        this.shown = true
        this._trigger('shown')
      } else {
        this.shown = false
      }
    }
  })

  $(document).ready(function () {
    $('[data-control="tab"]').tifyTab()
  })
})