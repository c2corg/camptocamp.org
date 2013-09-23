// touch-friendly gallery for mobile version
// built around swipe.js

// TODO js async load?
//      enable for documents embedded images?

(function(C2C, $) {

  C2C.swipe = function() {

    var images, swipe, overlay, background, meta, timer, img_type;

    // regsiter events for starting the swipejs based gallery
    function init() {
      // do not activate swipe gallery if touch is not supported
      if (!('ontouchstart' in window) && 
          !(window.DocumentTouch && document instanceof DocumentTouch)) {
        return;
      }

      images = $('.image a[data-lightbox]');

      // gets too laggy when they are too many slides
      if (images.length > 30) return;

      images.each(function(index) {
        $(this).click(function(event) {
          event.preventDefault();
          start(index);
        });
      });
    }

    // prepare and start the slideshow
    function start(startSlide) {
      startSlide = startSlide || 0;

      // temporarily disable zoom
      disableZoom();

      // depending on screen width, we use MI or BI images by default
      // use stored setting if any
      // MI are ~10-15ko, BI are ~100ko
      // TODO might need tweaking and maybe we should take pixelratio into account too
      img_type = (window.localStorage && localStorage.getItem('swipe-quality')) ||
                 ((document.viewport.getWidth() > 400) ? 'BI' : 'MI');

      // build DOM for displaying the images
      var wrapper = $('<div/>', { 'class': 'swipe-wrap' });

      images.each(function() {
        var img = $(this).find('img')[0].src.replace('SI', img_type);
        wrapper.append($('<div><div class="swipe-img" style="background-image:url(' + img + ')"></div></div>'));
      });

      var links = [];
      if (img_type === 'MI') {
        links.push('<a/> - ');
      }
      links.push($('<a/> - <a>' + swipe_i18n.Informations + '</a>'),
        $('<span/>', { 'class': 'swipe-quality-switch' })
          .append(img_type == 'MI' ? 'LQ' : 'HQ')
          .click(switchQuality));


      meta = $('<div/>', { 'class': 'swipe-meta' }).append(
        $('<span/>', { 'class': 'swipe-title' }),
        $('<br>'),
        $('<span/>', { 'class': 'swipe-links' }).append(links),
        $('<span/>', { 'class': 'swipe-index' })
      );

      overlay = $('<div/>', { id: 'swipe', 'class': 'swipe-overlay' }).append(
        $('<div/>', { 'class': 'swipe' }).append(wrapper),
        meta,
        $('<div/>', { 'class': 'swipe-close' })
      );

      background = $('<div/>', { 'class': 'swipe-background' });

      $('body').append(background, overlay);

      // position the overlay divs correctly on screen
      background.height($('#holder').height());
      overlay.css('top', $(document).scrollTop());

      $('.swipe-close').click(function() {
        location.hash = '#_'; // use dummy hash since using '' or '#' would cause page scroll to top
        stop();
      });

      // launch Swipe
      swipe = new Swipe($('.swipe')[0], {
        startSlide: startSlide,
        disableScroll: true,
        continuous: false,
        callback: onSlideChange
      });

      // display info on first slide
      onSlideChange(startSlide);
      hideMeta();

      // register events
      $('.swipe-wrap')
        .on('touchstart', showMeta)
        .on('touchend', hideMeta);

      // prevent page scroll when touching the information panel
      // this shouldn't prevent the click event
      meta.on('touchmove', function(event) {
        event.preventDefault();
      });

      // use location hash in order to cancel gallery
      // if user pushes back button
      // TODO use history api once better supported in the mobile world
      location.hash = '#swipe';
      $(window).on('hashchange', function() {
        if (location.hash !== '#swipe') {
          stop();
        }
      });
    }

    // this function gets executed after a new slide is displayed
    // and is used to update image information
    function onSlideChange(index, elt) {
      $('.swipe-index').text((index + 1) + ' / ' + swipe.getNumSlides());
      $('.swipe-title').text(images.get(index).title);
      var links = $('.swipe-links a');
      var img = images.eq(index).find('img').first();

      if (img.data('width')) {
        var width = img.data('width');
        var height = img.data('height');
        if (img_type === 'MI') {
          links.eq(0).text(imagesize(800, width, height));
          links.eq(1).text(imagesize(20000, width, height));
        } else {
          links.eq(0).text(imagesize(20000, width, height));
        }
      } else {
        if (img_type === 'MI') {
          links.eq(0).text(swipe_i18n['Big size']);
          links.eq(1).text(swipe_i18n['Original image']);
        } else {
          links.eq(0).text(swipe_i18n['Original image']);
        }
      }

      var src = images.eq(index).find('img')[0].src;
      if (img_type === 'MI') {
        links.eq(0).attr('href', src.replace('SI', 'BI'));
        links.eq(1).attr('href', src.replace('SI', ''));
      } else {
        links.eq(0).attr('href',  src.replace('SI', ''));
      }
      links.last().attr('href', images.get(index).href);
    }

    // switch quality
    function switchQuality(event) {
      event.preventDefault();
      if (window.localStorage) {
        localStorage.setItem('swipe-quality', img_type == 'MI' ? 'BI' : 'MI');
      }
      $$('.swipe-background, .swipe-overlay').invoke('remove');
      start(swipe.getPos());
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
        translateY(meta, meta.height());
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
      var style = elt.get(0).style;
      style.webkitTransform = 'translate(0,' + dist + 'px)' + 'translateZ(0)'; // enable GPU
      style.msTransform =
      style.MozTransform =
      style.OTransform = 'translateY(' + dist + 'px)';
    }

    function disableZoom() {
      $('meta[name="viewport"]').attr('content', 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }

    function enableZoom() {
      $('meta[name="viewport"]').attr('content', 'width=device-width');
    }

    function imagesize(max, width, height) {
      var ratio = Math.min(max/width, max/height);
      if (ratio >= 1) {
        return width + 'x' + height;
      } else {
        return Math.round(ratio*width) + 'x' + Math.round(ratio*height);
      }
    }

    return init();

  };

  $(C2C.swipe);

})(window.C2C = window.C2C || {});
