"use strict";

import jQuery from 'jquery';
import 'spectrum/lib/spectrum';

jQuery(function ($) {
    $(document).on('tify_field.colorpicker.init', function (event, obj) {
        let options = $.parseJSON(
            decodeURIComponent(
                $(obj).data('options')
            )
        );

        options = $.extend({change: function(color) { $(obj).val(color.toHexString()); }}, options);
        $(obj).spectrum(options);
    });

    $('.tiFyField-colorpicker').each(function () {
        $(document).trigger('tify_field.colorpicker.init', $(this));
    });
});