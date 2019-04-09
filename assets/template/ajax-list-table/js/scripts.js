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

      let ajax = {
        /**
         * @param {object} d
         * @returns {*}
         */
        data: function (d) {
          d = $.extend(d, {action: 'get_items'});
          // Ajout dynamique d'arguments passés dans la requête ajax de récupération d'éléments.
          // if ($('#ajaxDatatablesData').val()) {
          //    let ajax_data = JSON.parse(decodeURIComponent($('#ajaxDatatablesData').val()));
          //    d = $.extend(d, ajax_data);
          // }
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
  });
});