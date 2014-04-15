// touch-friendly gallery for mobile version
// built around swipe.js

// TODO js async load?
//      enable for documents embedded images?

(function(C2C, $, window, document) {

  C2C.swipe = function() {

    var images, swipe, overlay, meta, timer, img_type, i18n, pos;

    // regsiter events for starting the swipejs based gallery
    function init() {
      // do not activate swipe gallery if touch is not supported
      // since swipe doesn't use mouse as fallback
      if (!('ontouchstart' in window) && 
          !(window.DocumentTouch && document instanceof DocumentTouch)) {
        return;
      }

      images = $('.image a[data-lightbox]');

      // gets too laggy when they are too many slides
      // TODO detect number of images that make ipad crash or find a workaround!!
      if (images.length > 30) return;

      i18n = C2C.swipe_i18n;

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

      pos = $(document).scrollTop();

      // depending on screen width, we use MI or BI images by default
      // use stored setting if any
      // MI are ~10-15ko, BI are ~100ko
      // TODO might need tweaking and maybe we should take pixelratio into account too
      img_type = (window.localStorage && localStorage.getItem('swipe-quality')) ||
                 ($(document).width() > 400 ? 'BI' : 'MI');

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
      links.push($('<a/> - <a>' + i18n.Informations + '</a>'),
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

      $('body').append(overlay).addClass('swipe-active');

      $('.swipe-close').click(function() {
        window.history.back();
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

      // use location hash or historty api in order to cancel gallery
      // if user pushes back button
      if (historyapi) {
        history.pushState('', '', '#swipe');
        $(window).on('popstate.swipe', stop);
      } else {
        location.hash = '#swipe';
        $(window).on('hashchange.swipe', function() {
          if (location.hash !== '#swipe') stop();
        });
      }
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
          links.eq(0).text(i18n['Big size']);
          links.eq(1).text(i18n['Original image']);
        } else {
          links.eq(0).text(i18n['Original image']);
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
      overlay.remove();
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
      timer = window.setTimeout(function() {
        translateY(meta, meta.outerHeight());
      }, 4000);
    }

    // stop the gallery and clean the dom
    // note: we don't cancel observe events since we are targeting
    // browsers that should do this by themselves
    function stop() {
      if (!swipe) return;
      swipe.kill();
      overlay.remove();
      $('body').removeClass('swipe-active');
      enableZoom();
      $(window).off('.swipe');
      setTimeout(function() { $(document).scrollTop(pos); }, 0);
      swipe = overlay = null;
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

  historyapi = (function () {
    // test taken from modernizr ( MIT license
    // https://github.com/Modernizr/Modernizr/blob/master/feature-detects/history.js
    var ua = navigator.userAgent;

    if ((ua.indexOf('Android 2.') !== -1 ||
        (ua.indexOf('Android 4.0') !== -1)) &&
        ua.indexOf('Mobile Safari') !== -1 &&
        ua.indexOf('Chrome') === -1) {
      return false;
    }

    return (window.history && 'pushState' in window.history);
  })();

  C2C.swipe();

})(window.C2C = window.C2C || {}, jQuery, window, document);
