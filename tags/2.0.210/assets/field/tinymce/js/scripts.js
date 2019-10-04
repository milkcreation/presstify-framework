/* global tify */
'use strict';

import jQuery from 'jquery';
import 'tinymce/tinymce.min';
import 'tinymce/themes/silver/index';
if (tify.locale.language !== undefined) {
  try {
    require('tinymce-i18n/langs5/'+ tify.locale.language);
  } catch (e) {
    console.log('Unavailable tinyMCE language ' + tify.locale.language);
  }
}
import 'tinymce/jquery.tinymce.min';

jQuery(function ($) {
  $('[data-control="tinymce"]').each(function() {
    let o = $.parseJSON(decodeURIComponent($(this).data('options'))) || {};

    $(this).tinymce(o);
  });
});