/* global tify */
"use strict";

import jQuery from 'jquery';
import 'tinymce/tinymce.min';
import 'tinymce/themes/silver/index';
import 'tinymce-i18n/langs5/fr_FR';
import 'tinymce/jquery.tinymce.min';

jQuery(function ($) {
  $('[data-control="tinymce"]').each(function() {
    let o = $.parseJSON(decodeURIComponent($(this).data('options'))) || {};

    $(this).tinymce(o);
  });
});