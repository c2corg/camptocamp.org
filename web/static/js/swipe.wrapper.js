(function(C2C) {

// TODO this
  C2C.swipe = {

    init: function() {
      var images = $$('.image a[data-lightbox]');

      $$('.image a[data-lightbox]').invoke('observe', 'click', function(e) {
        e.stop();
        C2C.swipe.start();
      });
    },

    start: function() {
      console.log('start');
      var images = $$('.image a[data-lightbox]');

      var wrapper = Builder.node('div', { 'class': 'swipe-wrap' });
      images.each(function(o) {
        wrapper.appendChild(Builder.node('div', {},
          Builder.node('div', { 'class': 'swipe-img', 'style': 'background-image: url(' + o.href + ')' })));
      });
      var body = $$('body')[0];
      body.appendChild(Builder.node('div', { 'class': 'swipe-overlay' },
        Builder.node('div', { 'class': 'swipe' },
          wrapper)));
      window.swipe = new Swipe($$('.swipe')[0]);
    }

  };

  Event.observe(window, 'dom:loaded', C2C.swipe.init);

})(window.C2C = window.C2C || {});
