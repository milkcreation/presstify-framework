'use strict';

import jQuery from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';
import 'jquery-ui/ui/position';
import 'jquery-ui/ui/widgets/menu';
import 'jquery-ui/ui/widgets/autocomplete';
import * as inViewport from 'presstify-framework/in-viewport/js/scripts';

jQuery(function ($) {
  // Attribution de la valeur à l'élément.
  let _hook = $.valHooks.input;

  $.valHooks.input = {
    get: function (elem) {
      if (typeof $(elem).tifySuggest('instance') === 'undefined') {
        return _hook && _hook.get && _hook.get(elem) || undefined;
      }
      return $(elem).data('value') || $(elem).prop('value');
    },
    set: function (elem, value) {
      if (typeof $(elem).tifySuggest('instance') === 'undefined') {
        return _hook && _hook.set && _hook.set(elem, value) || undefined;
      }
      $(elem).data('value', value);
    }
  };

  $.widget('tify.tifySuggest', {
    widgetEventPrefix: 'suggest:',
    options: {
      classes: {
        alt: 'FieldSuggest-alt',
        input: 'FieldSuggest-input',
        item: 'FieldSuggest-pickerItem',
        items: 'FieldSuggest-picker',
        more: 'FieldSuggest-more',
        reset: 'FieldSuggest-reset',
        spinner: 'FieldSuggest-spinner',
        wrap: 'FieldSuggest-wrap',
      },
    },
    control: {
      alt: 'suggest.alt',
      input: 'suggest',
      more: 'suggest.more',
      reset: 'suggest.reset',
      spinner: 'suggest.spinner',
      wrap: 'suggest.wrap',
    },
    more: undefined,
    items: [],
    closable: true,

    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this.flags = {
        isAlt: true,
        isReset: true,
        isSpinner: true
      };

      this._initOptions();
      this._initFlags();
      this._initControls();
      this._initEvents();
    },
    // INITIALISATIONS.
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
      this.flags.isAlt = !!this.option('alt');
      this.flags.isReset = !!this.option('reset');
      this.flags.isSpinner = !!this.option('spinner');
    },
    // Initialisation des agents de contrôle.
    _initControls: function () {
      let self = this,
          ajax = this.option('ajax') || undefined,
          o = self.option('autocomplete');

      this.wrap = $(this.el).closest('[data-control="' + this.control.wrap + '"]');
      if (!this.wrap.length) {
        this.wrap = $(this.el).wrap('<div data-control="' + this.control.wrap + '"/>').parent();
      }
      this.wrap.addClass(this.option('classes.wrap')).attr('aria-loaded', 'false').attr('aria-selected', 'false');

      if (this.flags.isAlt) {
        this.alt = $('[data-control="' + this.control.alt + '"]', this.wrap);
        if (!this.alt.length) {
          this.alt = $('<input type="hidden" data-control="' + this.control.alt + '"/>').appendTo(this.wrap);
        }

        let value = this.option('alt');
        if (typeof value === 'string') {
          this.alt.val(value);
          this.wrap.attr('aria-selected', 'true');
        }

        let name = this.el.attr('name') || undefined;
        if (name !== undefined) {
          this.alt.attr('name', name);
        }

        this.alt.addClass(this.option('classes.alt'));
      }

      if (this.flags.isReset) {
        let $reset = $('[data-control="' + this.control.reset + '"]', this.wrap);
        if (!$reset.length) {
          $reset = $('<span data-control="' + this.control.reset + '"/>').appendTo(this.wrap);
        }
        $reset.addClass(this.option('classes.reset'));
      }

      if (this.flags.isSpinner) {
        let $spinner = $('[data-control="' + this.control.spinner + '"]', this.wrap);
        if (!$spinner.length) {
          $spinner = $('<span data-control="' + this.control.spinner + '"/>').appendTo(this.wrap);
        }
        $spinner.addClass(this.option('classes.spinner'));
      }

      if (this.uiautocomplete === undefined) {
        if (ajax && !o.source) {
          $.extend(o, {
            source: function (request, response) {
              let _ajax = $.extend(true, {}, ajax, {data: {_term: request.term}});

              if (self.more !== undefined) {
                _ajax = $.extend(true, _ajax, {data: self.more.data || {}});
              }

              self.wrap.attr('aria-loaded', 'true').attr('aria-selected', 'false');

              $.ajax(_ajax).done(function (resp) {
                if (resp.success) {
                  let items = resp.data.items;

                  if (!self.more) {
                    self.items = [];
                  }

                  $.each(items, function (u, v) {
                    self.items.push(v);
                  });

                  if (typeof resp.data.more === 'object' && resp.data.more !== null) {
                    self.more = resp.data.more;
                  } else {
                    self.more = undefined;
                  }

                  response(self.items || []);
                } else {
                  self.more = undefined;
                }
              }).always(function () {
                self.wrap.attr('aria-loaded', 'false');
              });
            }
          });
        }

        this.uiautocomplete = this.el.autocomplete(o || {});

        let handler = this.uiautocomplete.data('ui-autocomplete');
        handler._renderMenu = function (ul, items) {
          let that = this;

          $.each(items, function (index, item) {
            that._renderItemData(ul, item, index);
          });
          ul.addClass(self.option('classes.items'));

          if (self.more !== undefined) {
            let value = that.term || '';

            ul.addClass('hasMore');

            let $moreLink = $('<li data-control="' + self.control.more + '"/>')
                .html(self.more.html || '+')
                .appendTo(ul)
                .data('ui-autocomplete-item', {more: true, value: value})
                .addClass(self.option('classes.more'))
                .click(function () {
                  self._doloadMore($(this), ul);
                });

            ul.off('scroll').on('scroll', function () {
              self._onScrollLoadMore($moreLink, ul);
            });
            self._onScrollLoadMore($moreLink, ul);
          } else {
            $('<li data-control="' + self.control.more + '"/>').remove();
            ul.removeClass('hasMore');
            ul.off('scroll');
          }
        };

        handler._renderItemData = function (ul, item, index) {
          let render = (item.render !== undefined) ? item.render : item.label;
          item.value = $('<div/>').html(item.value).text();

          return $("<li>")
              .attr("data-index", index)
              .attr("data-value", item.value)
              .addClass(self.option('classes.item') + ' ' + self.option('classes.item') + '--' + index)
              .html(render)
              .appendTo(ul)
              .data("ui-autocomplete-item", item);
        };

        handler.close = function () {
          if (self.closable) {
            self.el.autocomplete('widget').hide();
          } else {
            self.el.autocomplete('widget').show();
          }
        }

        this.uiautocomplete
            .on('autocompletefocus', function (e) {
              e.preventDefault();

              self.el.autocomplete('widget').show();
            })
            .on('autocompletesearch', function () {
              self.resetted = false;
            })
            .on('autocompleteselect', function (e, ui) {
              e.preventDefault();

              if (ui.item.more) {
                self.closable = false;

                return false;
              } else if (self.resetted === false) {
                self.value(self.flags.isAlt ? ui.item.alt || ui.item.value : ui.item.value);
              }

              let $link = $(ui.item.label).find('a');
              if ($link.length) {
                window.location.href = $link.attr('href');
              }

              self.more = undefined;

              return true;
            });

        // Délégation d'appel des événements d'autocomplete.
        // @see https://api.jqueryui.com/autocomplete/#events
        // ex. $('[data-control="suggest"]').on('suggest:select', function (e, file, resp) {
        //    console.log(resp);
        // });
        let events = ['change', 'close', 'create', 'focus', 'open', 'response', 'search', 'select'];

        events.forEach(function (eventname) {
          self.uiautocomplete.on('autocomplete' + eventname, function (e, ui) {
            self._trigger(eventname, e, ui);
          });
        });
      }
    },
    // Initialisation des événements déclenchement.
    _initEvents: function () {
      this._on(this.wrap, {'click [data-control="suggest.reset"]': this.reset});
      this._on(this.uiautocomplete, {'keyup': this._onKeyUp});
    },
    // ACTIONS
    // -----------------------------------------------------------------------------------------------------------------
    _doloadMore: function (more, ul) {
      let loader = this.more.loader;

      if (loader !== false) {
        more.html(loader || '<span class="ThemeSpinner" />');
      }
      ul.off('scroll');

      this._trigger('more');
      this.uiautocomplete.autocomplete('search');
      this.uiautocomplete.data('ui-autocomplete').menu.element.show();
    },
    // EVENEMENTS.
    // -----------------------------------------------------------------------------------------------------------------
    _onKeyUp: function () {
      this.more = undefined;

      if (this.wrap.attr('aria-selected') === 'true') {
        this.reset();
      }
    },
    _onScrollLoadMore: function (more, ul) {
      if (inViewport(more, 0, ul)) {
        this._doloadMore(more, ul);
      }
    },
    // ACCESSEURS.
    // -----------------------------------------------------------------------------------------------------------------
    // Suppression de la valeur.
    reset: function () {
      this.resetted = true;

      this._trigger('reset');

      this.value('');
    },
    // Définition ou récupération de la valeur.
    value: function (value) {
      if (value !== undefined) {
        this.el.prop('value', value);
        this.el.data('value', value);

        if (this.flags.isAlt) {
          this.alt.val(value);
        }

        if (value) {
          this.wrap.attr('aria-selected', 'true');
        } else {
          this.wrap.attr('aria-selected', 'false');
        }
      }

      return this.el.data('value');
    }
  });

  $(document).ready(function () {
    $('[data-control="suggest"]').tifySuggest();

    $(document).on('focus', '[data-control="suggest"]', function () {
      $(this).tifySuggest();
    });
  });
});