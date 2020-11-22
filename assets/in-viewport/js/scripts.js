/**
 * USAGE :
 * ---------------------------------------------------------------------------------------------------------------------
 * import * as inViewport from 'presstify-framework/in-viewport/js/scripts';
 *
 * inViewport('#target', 100, '#viewport');
 */
;(function (window, factory) {
  // universal module definition
  /*jshint strict: false */ /* globals define, module, require */
  if (typeof define === 'function' && define.amd) {
    // AMD
    define(['jquery'], function (jQuery) {
      return factory(window, jQuery);
    });
  } else if (typeof module === 'object' && module.exports) {
    // CommonJS
    module.exports = factory(
        window,
        require('jquery')
    );
  } else {
    // browser global
    window.inViewport = factory(
        window,
        window.jQuery
    );
  }
})(window, function factory(window, $) {
  'use strict';

  return function ($target, threshold = 0, viewport = null) {
    let $viewport = viewport || $(window),
        offset = $target.offset();

    if (!offset) {
      return false;
    }

    if ((typeof $viewport === 'string' || (typeof $viewport === 'object' && !$viewport.jquery))) {
      $viewport = $($viewport);
    }

    if ($viewport.find($target).length) {
      let elTop = $target[0].offsetTop,
          eBottom = elTop + $target[0].clientHeight,
          viewScrollTop = $viewport[0].scrollTop,
          viewScrollBottom = viewScrollTop + $viewport[0].clientHeight;

      return viewScrollBottom > elTop && viewScrollTop < eBottom;
    } else {
      let lBound = $viewport.scrollTop(),
          uBound = lBound + $viewport.height(),
          top = offset.top + threshold,
          bottom = top + $target.outerHeight(true);

      return (top > lBound && top < uBound) ||
          (bottom > lBound && bottom < uBound) ||
          (lBound >= top && lBound <= bottom) ||
          (uBound >= top && uBound <= bottom);
    }
  };
});