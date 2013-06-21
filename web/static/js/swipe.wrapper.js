// touch-friendly gallery for mobile version
// built around swipe.js

// TODO js async load?
//      when too many images could crash - to be tested
//      enable for documents embedded images?

(function(C2C) {

  C2C.swipe = function() {

    var images, swipe, overlay, background, meta, timer, img_type;

    // regsiter events for starting the swipejs based gallery
    function init() {
      // TODO use event delegation ?
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

      // temporarily disable zoom
      disableZoom();

      // depending on screen width, we use MI or BI images
      // MI are ~10-15ko, BI are ~100ko
      // TODO might need tweaking and maybe we should take pixelratio into account too
      img_type = (document.viewport.getWidth() > 400) ? 'BI' : 'MI';

      // build DOM for displaying the images
      var wrapper = Builder.node('div', { 'class': 'swipe-wrap' });
      images.each(function(o) {
        var img = o.down('img').src.replace('SI', img_type);
        wrapper.appendChild(Builder.node('div',
          Builder.node('div', { 'class': 'swipe-img', 'style': 'background-image: url(' + img + ')' })));
      });

      var links = [];
      if (img_type === 'MI') {
        links.push(Builder.node('a', C2C.swipe_i18n['Big size']), ' - ');
      }
      links.push(Builder.node('a', C2C.swipe_i18n['Original image']),
        ' - ', Builder.node('a', C2C.swipe_i18n.Informations));

      meta = Builder.node('div', { 'class': 'swipe-meta' }, [
        Builder.node('span', { 'class': 'swipe-title' }),
        Builder.node('br'),
        Builder.node('span', { 'class': 'swipe-links' }, links),
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
        location.hash = '#_'; // use dummy hash since using '' or '#' would cause page scroll to top
        stop();
      });

      // launch Swipe
      swipe = new Swipe($$('.swipe')[0], {
        startSlide: startSlide,
        disableScroll: true,
        continuous: false,
        callback: onSlideChange
      });

      // display info on first slide
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
    // and is used to update image information
    function onSlideChange(index, elt) {
      $$('.swipe-index')[0].update((index + 1) + ' / ' + swipe.getNumSlides());
      $$('.swipe-title')[0].update(images[index].title);
      var links = $$('.swipe-links a');
      var src = images[index].down('img').src;
      if (img_type === 'MI') {
        links[0].href = src.replace('SI', 'BI');
        links[1].href = src.replace('SI', '');
      } else {
        links[0].href = src.replace('SI', '');
      }
      links.last().href = images[index].href;
    }

    // show image information panel
    function showMeta() {
      window.clearTimeout(timer);
      translateY(meta, 0);
    }

    // hide image information panel
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
      enableZoom();
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

    function disableZoom() {
      $$('meta[name="viewport"]')[0].content = "width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no";
    }

    function enableZoom() {
      $$('meta[name="viewport"]')[0].content = "width=device-width";
    }

    return init();

  };

  Event.observe(window, 'dom:loaded', C2C.swipe);

})(window.C2C = window.C2C || {});
