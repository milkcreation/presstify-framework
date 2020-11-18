'use strict';

import jQuery from 'jquery';
import '../../../observer/js/scripts';

jQuery(function ($) {
  $(document).on('click', '[data-control="notice"] [data-toggle="notice.dismiss"]', function (e) {
    e.preventDefault();

    $(this).closest('[data-control="notice"]').attr('aria-hidden', 'true');
  });

  $('[data-control="notice"][data-timeout]').each(function () {
    let $el = $(this),
        time = parseInt($el.data('timeout')) || 0;

    if (time !== 0) {
      setTimeout(function () {
        $el.attr('aria-hidden', 'true');
      }, time);
    }
  });

  $.tify.observe('[data-control="notice"]', function (i, target) {
    let $el = $(target),
        time = parseInt($el.data('timeout')) || 0;

    if (time !== 0) {
      setTimeout(function () {
        $el.attr('aria-hidden', 'true');
      }, time);
    }
  });
});