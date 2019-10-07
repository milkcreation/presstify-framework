'use strict';

import jQuery from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widget';

jQuery(function ($) {
  $.widget('tify.tifyObserver', {
    widgetEventPrefix: 'observer:',
    id: undefined,
    options: {},
    // Instanciation de l'élément.
    _create: function () {
      this.instance = this;

      this.el = this.element;

      let func = this.option('func'),
          selector = this.option('selector'),
          observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
              if (mutation.type === 'childList') {
                if (mutation.addedNodes.length >= 1) {
                  for (var i = 0; i < mutation.addedNodes.length; i++) {
                    let $el = $(mutation.addedNodes[i]);
                    if ($el.is(selector)) {
                      $el.each(func);
                    } else {
                      $el.find(selector).each(func);
                    }
                  }
                }
              }
            });
          }),
          observerConfig = {attributes: true, childList: true, characterData: true, subtree: true},
          targetNode = document.body;

      observer.observe(targetNode, observerConfig);
    }
  });
});