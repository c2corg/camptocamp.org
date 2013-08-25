(function(C2C, $) {

  // image slideshow stuff
  var delay = 4000;

  function showNextImage(frame, lis) {
    var next_frame = (frame == lis.length - 1) ? 0 : frame + 1;

    $(lis[frame]).removeClass('popup-img-active');
    $(lis[next_frame]).addClass('popup-img-active');

    window.setTimeout(function() { showNextImage(next_frame, lis) }, delay);
  }

  C2C.init_slideshow = function() {
    var lis = $(this).find('.popup_slideimages li');
    window.setTimeout(function() { showNextImage(0, lis) }, delay);
  };

  C2C.init_popup = function() {
    // we might have more than one popup on the screen, so we go through
    // each of them and init if needed
    var popups = $('.popup_content').each(function() {
      var $this = $(this);

      // do not init if already done
      if ($this.hasClass('c2c-init')) return;

      $this.addClass('c2c-init');

      // init slideshow
      if ($this.find('.popup_slideimages img').length > 1) {
        $.proxy(C2C.init_slideshow, this)();
      }

      // if the popup is used on a map, we need to make sure that
      // activities section (if present) can be toggled
      $this.on('click', '.title2', function() {
        $(this).next().toggle();
      });

    });
  };

})(window.C2C = window.C2C || {}, jQuery);
