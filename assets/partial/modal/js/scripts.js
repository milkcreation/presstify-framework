/* global tify */
'use strict';

import jQuery from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';
import 'bootstrap/js/dist/modal';

jQuery(function ($) {
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

    $(document).on('click', '[data-control="modal-trigger"]', function (e) {
      e.preventDefault();

      $($(this).data('target') + '[data-control="modal"]').modal('show');
    });
  });
});

