/* globals tify */
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

      $.extend($.fn.dataTable.defaults, {
        // Liste des colonnes.
        columns: tify.listTable.columns,
        // Nombre d'éléments par page.
        iDisplayLength: parseInt(tify.listTable.per_page),
        // Tri par défaut.
        order: [],
        // Traduction.
        language: tify.listTable.language,
        // Interface.
        dom: 'rt'
      });

      let filters = {},
          o = $.extend({
            // Activation de l'indicateur de chargement.
            processing: true,
            // Activation du chargement Ajax.
            serverSide: true,
            // Désactivation du chargement Ajax à l'initialisation.
            deferLoading: [
              tify.listTable.total_items,
              tify.listTable.per_page
            ],
            // Attributs de la requête de traitement Ajax.
            ajax: {
              url: tify.listTable.xhr,
              data: function (d) {
                d = $.extend(d, filters, {action: 'get_items'});
                // Ajout dynamique d'arguments passés dans la requête ajax de récupération d'éléments.
                // if ($('#ajaxDatatablesData').val()) {
                //    let ajax_data = JSON.parse(decodeURIComponent($('#ajaxDatatablesData').val()));
                //    d = $.extend(d, ajax_data);
                // }
                return d;
              },
              dataType: 'json',
              method: 'POST',
              dataSrc: function (json) {
                if (!$('.search-box').length) {
                  $(json.search_form).insertBefore('.tablenav.top');
                }

                $('.tablenav-pages').each(function () {
                  $(this).replaceWith(json.pagination);
                });

                return json.data;
              }
            },
            // Au moment du traitement.
            drawCallback: function () {
              // DEBUG
              // let dataTable = this.api();
              // console.log(dataTable.params());
            },
            // Initialisation.
            initComplete: function () {
              let dataTable = this.api();

              dataTable.columns().every(function () {
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
                dataTable.page(page).draw('page');
              });
              // Champ de recherche
              $(document).on('click', '.search-box #search-submit', function (e) {
                e.preventDefault();

                dataTable.search($(this).prev().val()).draw();
              });
              // Affichage/Masquage des colonnes
              $('.hide-column-tog').change(function (e) {
                e.preventDefault();

                let $this = $(this),
                    column = dataTable.column($this.val() + ':name');

                column.visible(!column.visible());
              });
              //$.each(this.api().columns().visible(), function (u, v) {
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

                dataTable.draw(true);
              });*/
            }
          }, tify.listTable.options);

          this.el.dataTable(o);
      }
  });

  $(document).ready(function ($) {
    $('[data-control="list-table.ajax"]').tifyListTable();
  });
});