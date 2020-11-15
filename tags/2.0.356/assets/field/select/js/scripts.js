'use strict';

import jQuery from 'jquery';

jQuery(document).ready(function () {
    /* * /
    $('.FieldSelect').on({
        "change": function () {
            $(this).blur();
        },
        'focus': function () {
            console.log("displayed");
        },
        "blur": function () {
            console.log("not displayed");
        },
        "keyup": function (e) {
            if (e.keyCode === 27) {
                console.log("displayed");
            }
        }
    });
    /**/

    /* * /
    $(".FieldSelect-trigger").click(function () {
        $('.FieldSelect').each(function () {
            $(this).trigger('mousedown');
        });
    });
    /**/
});