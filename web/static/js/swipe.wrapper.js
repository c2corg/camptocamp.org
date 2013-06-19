// touch-friendly gallery for mobile version
// built around swipe.js

// TODO best image candidate? tablet != smartphones
//      js async load
//      when too many images could crash - to be tested
//      i18N

(function(C2C) {

  C2C.swipe = function() {

    var images, swipe, overlay, background, meta, timer;

    // regsiter events for starting the swipejs based gallery
    function init() {
      // TODO use event delegation ?
      // TODO embedded images
      images = $$('.image a[data-lightbox]');

      images.each(function(o, i) {
        o.observe('click', function(e) {
          e.stop();
          start(i);
        });
      });
    }

    // prepare and start the slideshow
    function start(startSlide) {
      startSlide = startSlide || 0;

      // build DOM for displaying the images
      var wrapper = Builder.node('div', { 'class': 'swipe-wrap' });
      images.each(function(o) {
        var img = o.down('img').src.replace('SI', 'MI');
        wrapper.appendChild(Builder.node('div',
          Builder.node('div', { 'class': 'swipe-img', 'style': 'background-image: url(' + img + ')' })));
      });

      meta = Builder.node('div', { 'class': 'swipe-meta' }, [
        Builder.node('span', { 'class': 'swipe-title' }),
        Builder.node('br'),
        Builder.node('span', { 'class': 'swipe-links' }, [
          Builder.node('a', 'Image originale'),
          ' - ',
          Builder.node('a', 'Informations')
        ]),
        Builder.node('span', { 'class': 'swipe-index' })
      ]);

      overlay = Builder.node('div', { 'class': 'swipe-overlay' }, [
        Builder.node('div', { 'class': 'swipe' }, wrapper),
        meta,
        Builder.node('div', { 'class': 'swipe-close' })
      ]);

      background = Builder.node('div', { 'class': 'swipe-background' });

      var body = $$('body')[0];
      body.appendChild(background);
      body.appendChild(overlay);

      // position the overlay divs correctly on screen
      background.style.height = $('holder').getHeight() + 'px';
      overlay.style.top = document.viewport.getScrollOffsets()[1] + 'px';

      $$('.swipe-close')[0].observe('click', function() {
        location.hash = '';
        stop();
      });

      // launch Swipe
      swipe = new Swipe($$('.swipe')[0], {
        startSlide: startSlide,
        disableScroll: true,
        continuous: false,
        callback: onSlideChange
      });

      // display info on first page
      onSlideChange(startSlide);
      hideMeta();

      // register events
      $$('.swipe-wrap')[0]
        .observe('touchstart', showMeta)
        .observe('touchend', hideMeta);

      // use location hash in order to cancel gallery
      // if user pushes back button
      // TODO use history api once better supported in the mobile world
      location.hash = '#swipe';
      Event.observe(window, 'hashchange', function() {
        if (location.hash !== '#swipe') {
          stop();
        }
      });
    }

    // this function gets executed after a new slide is displayed
    function onSlideChange(index, elt) {
      $$('.swipe-index')[0].update((index + 1) + ' / ' + swipe.getNumSlides());
      $$('.swipe-title')[0].update(images[index].title);
      var links = $$('.swipe-links a');
      links[0].href = images[index].down('img').src.replace('SI', '');
      links[1].href = images[index].href;
    }

    function showMeta() {
      window.clearTimeout(timer);
      translateY(meta, 0);
    }

    function hideMeta() {
      window.clearTimeout(timer);
      timer = (function() {
        translateY(meta, meta.getHeight());
      }).delay(4);
    }

    // stop the gallery and clean the dom
    // note: we don't cancel observe events since we are targeting
    // browsers that should do this by themselves
    function stop() {
      if (!swipe) return;
      swipe.kill();
      overlay.remove();
      background.remove();
      swipe = overlay = background = null;
    }

    // adapted from swipe.js
    function translateY(elt, dist) {
      var style = elt.style;
      style.webkitTransform = 'translate(0,' + dist + 'px)' + 'translateZ(0)'; // enable GPU
      style.msTransform =
      style.MozTransform =
      style.OTransform = 'translateY(' + dist + 'px)';
  }

    return init();

  };

  Event.observe(window, 'dom:loaded', C2C.swipe);

})(window.C2C = window.C2C || {});
