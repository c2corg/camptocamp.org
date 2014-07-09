// touch-friendly gallery for mobile version
// built around swipe.js

// Note that if we simply try to build a large (like 30) list of slides
// this either gets laggy (android) or crashes (iOS/Safari)
// The idea is thus to use a limited pool of slides and to dynamically change the
// of the slides after a slide change
// This also prevents preloading too many images

(function(C2C, $, window, document) {

  C2C.swipe = function() {

    var maxSlides = 8, imagesData = [], swipe, overlay, meta, timer, i18n, pos,
        nbSlides, slideIndex, startSlide, nbImages, imageIndex, imageType, images;


    // regsiter events for starting the swipejs based gallery
    function init() {
      // do not activate swipe gallery if touch is not supported
      // since swipe doesn't use mouse as fallback
      if (!('ontouchstart' in window) && 
          !(window.DocumentTouch && document instanceof DocumentTouch)) {
        return;
      }

      i18n = C2C.swipe_i18n;

      $('a[data-lightbox]').each(function(i) {
        var $this = $(this),
            $img = $this.find('img').first();

        imagesData.push({
          href: $this.attr('href'),
          src: $img.attr('src'),
          title: $this.attr('title'),
          width: $img.data('width'),
          height: $img.data('height'),
        });

        $this.click(function(event) {
          event.preventDefault();
          start(i);
        });
      });
    }

    // prepare and start the slideshow
    function start(startImage) {
      nbImages = imagesData.length;
      imageIndex = startImage || 0;
      nbSlides = Math.min(nbImages, maxSlides);
      slideIndex = startSlide = nbSlides % 2 ? Math.floor(nbSlides / 2) : Math.floor(nbSlides / 2) - 1;

      // temporarily disable zoom
      disableZoom();

      // save scroll position
      pos = $(document).scrollTop();

      // depending on screen width, we use MI or BI images by default
      // use stored setting if any
      // MI are ~10-15ko, BI are ~100ko
      // TODO might need tweaking and maybe we should take pixelratio into account too
      imageType = (window.localStorage && localStorage.getItem('swipe-quality')) ||
                 ($(document).width() > 400 ? 'BI' : 'MI');

      // build DOM for displaying the images
      var wrapper = $('<div/>', { 'class': 'swipe-wrap' });

      for (var i = 0; i < nbSlides; i++) {
        wrapper.append($('<div><div class="swipe-img" style="background-image:url(' +
          imagesData[circleImages(imageIndex - slideIndex + i)].src.replace(/(S|M)I/, imageType) +
          ')"></div></div>'));
      }

      var links = [];
      if (imageType === 'MI') {
        links.push('<a/> - ');
      }
      links.push($('<a/> - <a>' + i18n.Informations + '</a>'),
        $('<span/>', { 'class': 'swipe-quality-switch' })
          .append(imageType == 'MI' ? 'LQ' : 'HQ')
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

      images = $('.swipe-img');

      // launch Swipe
      swipe = new Swipe($('.swipe')[0], {
        startSlide: slideIndex,
        disableScroll: true,
        continuous: true,
        callback: onSlideChange
      });

      // display info on first slide
      updateInfo(imageIndex);
      hideMeta();

      // register events
      $('.swipe-wrap')
        .on('touchstart', showMeta)
        .on('touchend', hideMeta);

      $('.swipe-close').click(function() {
        window.history.back();
      });

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

    function circleImages(index) {
      return circle(index, nbImages);
    }

    function circleSlides(index) {
      return circle(index, nbSlides);
    }

    function circle(index, size) {
      return (size + (index % size)) % size;
    }

    function onSlideChange(index) {
      var direction = index - slideIndex;
      direction = Math.abs(direction) === 1 ? direction : direction > 0 ? -1 : 1;

      slideIndex = index;
      imageIndex = circleImages(imageIndex + direction);

      updateInfo(imageIndex);

      if (nbSlides < nbImages) {
        var slideToChange = direction === 1 ? circleSlides(slideIndex - startSlide - 1) : circleSlides(slideIndex - startSlide),
            newImage = direction === 1 ? circleImages(imageIndex + nbSlides - startSlide - 1) : circleImages(imageIndex - startSlide);

        images.eq(slideToChange).css('backgroundImage', 'url(' + imagesData[newImage].src.replace('SI', imageType) + ')');
      }
    }

    // this function gets executed after a new slide is displayed
    // and is used to update image information
    function updateInfo(index) {
      var links = $('.swipe-links a'),
          img = imagesData[index];

      $('.swipe-index').text((index + 1) + ' / ' + nbImages);
      $('.swipe-title').text(img.title);

      if (img.width) {
        var width = img.width;
        var height = img.height;
        if (imageType === 'MI') {
          links.eq(0).text(imageSize(800, width, height));
          links.eq(1).text(imageSize(20000, width, height));
        } else {
          links.eq(0).text(imageSize(20000, width, height));
        }
      } else {
        if (imageType === 'MI') {
          links.eq(0).text(i18n['Big size']);
          links.eq(1).text(i18n['Original image']);
        } else {
          links.eq(0).text(i18n['Original image']);
        }
      }

      var src = img.src;
      if (imageType === 'MI') {
        links.eq(0).attr('href', src.replace('SI', 'BI'));
        links.eq(1).attr('href', src.replace('SI', ''));
      } else {
        links.eq(0).attr('href',  src.replace('SI', ''));
      }
      links.last().attr('href', img.href);
    }

    // switch quality
    function switchQuality(event) {
      event.preventDefault();
      if (window.localStorage) {
        window.localStorage.setItem('swipe-quality', imageType == 'MI' ? 'BI' : 'MI');
      }
      overlay.remove();
      start(imageIndex);
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
      setTimeout(function() { $(document).scrollTop(pos); }, 10);
      swipe = overlay = null;
    }

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

    function imageSize(max, width, height) {
      var ratio = Math.min(max/width, max/height);
      if (ratio >= 1) {
        return width + 'x' + height;
      } else {
        return Math.round(ratio*width) + 'x' + Math.round(ratio*height);
      }
    }

    return init();

  };

  var historyapi = (function () {
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
