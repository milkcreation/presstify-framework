/* global jQuery */
"use strict";

jQuery(function ($) {
  // Désactivation des actions de masquage des colonnes natives de Wordpress.
  //$('.hide-column-tog').unbind();
  $.widget('tify.tifyListTable', {
    widgetEventPrefix: 'list-table:',
    id: undefined,
    options: {

    },
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      this._initOptions();

      this._on(this.el, {
        'list-table:created-row' : function(e, args) {
          let i = 0;
          $.each(args.data, function (u,v) {
            if (typeof v !== 'string') {
              $(args.row).find('td:eq(' + (i++) + ')').attr(v.attrs).html(v.render);
            } else {
              return v;
            }
          });
        }
      });

      let ajax = {
        /**
         * @param {object} d
         * @returns {*}
         */
        data: function (d) {
          d = $.extend(d, {action: 'get_items'});

          return d;
        },
        /**
         * @param {object} json
         * @returns {*}
         */
        dataSrc: function (json) {
          $('[data-control="list-table.search"]').each(function () {
            $(this).replaceWith(json.search);
          });

          $('[data-control="list-table.pagination"]').each(function () {
            $(this).replaceWith(json.pagination);
          });

          return json.data;
        }
      };

      $.extend($.fn.dataTable.defaults, {
        // Attributs de la requête de traitement Ajax.
        ajax: $.extend(this.option('ajax') || {}, ajax),
        // Liste des colonnes.
        columns: this.option('columns') || [],
        // Désactivation du chargement Ajax à l'initialisation.
        deferLoading: this.option('deferLoading') || (this.option('options.pageLength') && this.option('total_items')) ?
            [this.option('total_items'), this.option('options.pageLength')] : null,
        // Interface.
        dom: this.option('options.dom') || 'rt',
        // Nombre d'éléments par page.
        pageLength: parseInt(this.option('options.pageLength')) || 50,
        // Traduction.
        language: this.option('language') || {},
        // Tri par défaut.
        order: this.option('options.order') || [],
        // Activation de l'indicateur de chargement.
        processing: this.option('processing') || true,
        // Activation du chargement Ajax.
        serverSide: this.option('serverSide') || true,
      });

      let self = this,
          o = {
            /**
             * A l'issue de la création d'une ligne de donnée.
             * @see https://datatables.net/reference/option/createdRow
             *
             * @param {node} row
             * @param {array} data
             * @param {int} dataIndex
             * @param {node[]} cells
             */
            createdRow: function (row, data, dataIndex, cells) {
              let dataTable = this;

              self._trigger('created-row', null, {row, data, dataIndex, cells, dataTable});
            },
            /**
             * A Chaque écriture dans la table.
             * @see https://datatables.net/reference/option/drawCallback
             *
             * @param {dataTable.Settings} settings
             */
            drawCallback: function (settings) {
              let dataTable = this;

              self._trigger('draw-callback', null, {settings, dataTable});
            },
            /**
             * Au moment de l'affichage du pied de la table (tfoot).
             * @see https://datatables.net/reference/option/footerCallback
             *
             * @param {node} tfoot
             * @param {array} data
             * @param {int} start
             * @param {int} end
             * @param {array} display
             */
            footerCallback: function (tfoot, data, start, end, display) {
              let dataTable = this;
              self._trigger('footer-callback', null, {tfoot, data, start, end, display, dataTable});
            },
            /**
             * Au moment du formatage des nombres.
             * @see https://datatables.net/reference/option/formatNumber
             *
             * @param {int} formatNumber
             */
            formatNumber: function (formatNumber) {
              let dataTable = this;
              self._trigger('format-number', null, {formatNumber, dataTable});
            },
            /**
             * Au moment de l'affichage de l'entête de la table (thead).
             * @see https://datatables.net/reference/option/headerCallback
             *
             * @param {node} thead
             * @param {array} data
             * @param {int} start
             * @param {int} end
             * @param {array} display
             */
            headerCallback: function (thead, data, start, end, display) {
              let dataTable = this;
              self._trigger('header-callback', null, {thead, data, start, end, display, dataTable});
            },
            /**
             * Au moment du formatage des informations de la table.
             * @see https://datatables.net/reference/option/infoCallback
             *
             * @param {dataTable.Settings} settings
             * @param {int} start
             * @param {int} end
             * @param {int} max
             * @param {int} total
             * @param {string} pre
             */
            infoCallback: function (settings, start, end, max, total, pre) {
              let dataTable = this;
              self._trigger('info-callback', null, {settings, start, end, max, total, pre, dataTable});
            },
            /**
             * A l'issue de l'initialisation.
             * @see https://datatables.net/reference/option/initComplete
             *
             * @param {dataTable.Settings} settings
             * @param {object} json
             */
            initComplete: function (settings, json) {
              let dataTable = this;
              self._trigger('init-complete', null, {settings, json, dataTable});

              let api = this.api();

              api.columns().every(function () {
                let column = this;

                if (column.visible()) {
                  return false;
                }
              });
              // Pagination
              $(document).on('click', '.tablenav-pages a', function (e) {
                e.preventDefault();

                let page = 0;
                if ($(this).hasClass('next-page')) {
                  page = 'next';
                } else if ($(this).hasClass('prev-page')) {
                  page = 'previous';
                } else if ($(this).hasClass('first-page')) {
                  page = 'first';
                } else if ($(this).hasClass('last-page')) {
                  page = 'last';
                }
                api.page(page).draw('page');
              });
              // Champ de recherche
              $(document).on('click', '[data-control="list-table.search"] > button', function (e) {
                e.preventDefault();

                api.search($(this).prev().val()).draw();
              });
              // Affichage/Masquage des colonnes
              $('[data-control="list-table.column.toggle"]').change(function (e) {
                e.preventDefault();

                let $this = $(this),
                    column = api.column($this.val() + ':name');

                column.visible(!column.visible());
              });
              //$.each(api.columns().visible(), function (u, v) {
              //    let name = settings()[0].aoColumns[u].name;
              //    $('.hide-column-tog[name="' + name + '-hide"]').prop('checked', v);
              //});

              // Soumission du formulaire
              /*$('form#adv-settings').submit(function (e) {
                e.preventDefault();

                let value = parseInt($('.screen-per-page', $(this)).val());

                $.post(tify.ajax_url, {
                  action: tify.listTable.action_prefix + '_per_page',
                  per_page: value
                }).done(function () {
                  $('#show-settings-link').trigger('click');
                });

                dataTable.page.len(value).draw();
              });*/
              // Filtrage
              /*$('#table-filter').submit(function (e) {
                e.preventDefault();

                filters = {};

                $.each($(this).serializeArray(), function (u, v) {
                  if (
                      (v.name === '_wpnonce') ||
                      (v.name === '_wp_http_referer') ||
                      (v.name === 's') ||
                      (v.name === 'paged')
                  ) {
                    return true;
                  }
                  filters[v.name] = v.value;
                });

                api.draw(true);
              });*/
            },
            /**
             * Pré-écriture dans la table.
             *
             * @see https://datatables.net/reference/option/preDrawCallback
             *
             * @param {dataTable.Settings} settings
             */
            preDrawCallback: function (settings) {
              let dataTable = this;
              self._trigger('pre-draw-callback', null, {settings, dataTable});
            },
            /**
             * Après la génération d'une ligne de la table, avant son affichage.
             *
             * @see https://datatables.net/reference/option/rowCallback
             *
             * @param {node} row
             * @param {array|object} data
             * @param {int} displayNum
             * @param {int} displayIndex
             * @param {int} dataIndex
             */
            rowCallback: function (row, data, displayNum, displayIndex, dataIndex) {
              let dataTable = this;

              self._trigger('row-callback', null, {row, data, displayNum, displayIndex, dataIndex, dataTable});
            },
            /**
             * @see https://datatables.net/reference/option/stateLoadCallback
             *
             * @param {dataTable.Settings} settings
             * @param {function} callback
             */
            stateLoadCallback: function (settings, callback) {
              let dataTable = this;
              self._trigger('state-load-callback', null, {settings, callback, dataTable});
            },
            /**
             * @see https://datatables.net/reference/option/stateLoadParams
             *
             * @param {dataTable.Settings} settings
             * @param {object} data
             */
            stateLoadParams: function (settings, data) {
              let dataTable = this;
              self._trigger('state-load-params', null, {settings, data, dataTable});
            },
            /**
             * @see https://datatables.net/reference/option/stateLoaded
             *
             * @param {dataTable.Settings} settings
             * @param {object} data
             */
            stateLoaded: function (settings, data) {
              let dataTable = this;
              self._trigger('state-loaded', null, {settings, data, dataTable});
            },
            /**
             * @see https://datatables.net/reference/option/stateSaveCallback
             *
             * @param {dataTable.Settings} settings
             * @param {object} data
             */
            stateSaveCallback: function (settings, data) {
              let dataTable = this;
              self._trigger('state-save-callback', null, {settings, data, dataTable});
            },
            /**
             * @see https://datatables.net/reference/option/stateSaveParams
             *
             * @param {dataTable.Settings} settings
             * @param {object} data
             */
            stateSaveParams: function (settings, data) {
              let dataTable = this;
              self._trigger('state-save-params', null, {settings, data, dataTable});
            }
          };

      this.el.dataTable(o);
    },

    // INITIALISATION
    // -------------------------------------------------------------------------------------------------------------
    // Initialisation des attributs de configuration.
    _initOptions: function () {
      $.extend(
          true,
          this.options,
          this.el.data('options') && $.parseJSON(decodeURIComponent(this.el.data('options'))) || {}
      );
    }
  });

  $(document).ready(function ($) {
    $('[data-control="list-table"]').tifyListTable();

    $(document).on('click', '[data-control="list-table.row-action"]', function (e) {
      e.preventDefault();

      let $self = $(this);

      $.ajax({
        url: $self.attr('href'),
        method: 'POST',
        type: 'json'
      }).done(function (resp) {
        console.log(resp);
      });
    });
  });
});

/*
jQuery(document).ready(function ($) {

  let i18n = tiFyUiAdminListTablePreviewItem;

  $(document).on('click', '#the-list .row-actions .preview_item a', function (e) {
    e.preventDefault();

    let item_index = url('?' + i18n.item_index_name, $(this).attr('href')),
        nonce = url('?' + i18n.nonce_action, $(this).attr('href')),
        $closest = $(this).closest('tr');

    if (!item_index) {
      return;
    }

    let $preview;

    if ($closest.next().attr('id') !== 'Item-preview--' + item_index) {
      // Création de la zone de prévisualisation
      $preview = $('#Item-previewContainer').clone(true);

      let id = 'Item-preview--' + item_index,
          data = $.extend(
              {
                'action': i18n.action,
                '_ajax_nonce': nonce
              },
              JSON.parse(
                  decodeURIComponent($('#PreviewItemAjaxData').val())
              )
          );
      data[i18n.item_index_name] = item_index;

      $preview
          .attr('id', id)
          .hide();

      $closest.after($preview);

      if (i18n.mode === 'dialog') {
        $('#' + id).dialog({
          autoOpen: false,
          draggable: false,
          width: 'auto',
          modal: true,
          resizable: false,
          closeOnEscape: true,
          position:
              {
                my: "center",
                at: "center",
                of: window
              },
          open: function () {
            $('.ui-widget-overlay').bind('click', function () {
              $('#' + id).dialog('close');
            });
          },
          create: function () {
            $('.ui-dialog-titlebar-close').addClass('ui-button');
          }
        });
      }

      // Récupération et affichage de la prévisualisation de l'élément
      $.post(
          tify.ajax_url,
          data,
          function (resp) {
            $('.Item-previewContent', $preview).html(resp);

            if (i18n.mode === 'dialog') {
              $('#' + id).dialog('open');
            }
          }
      );
    } else {
      $preview = $closest.next();
    }

    $preview.toggle();

    return false;
  });
}); */