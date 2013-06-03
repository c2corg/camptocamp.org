(function(C2C) {

  // image slideshow stuff
  var delay = 4;

  function fadeInOut(frame, lis) {
    var next_frame = (frame == lis.length - 1) ? 0 : frame + 1;

    new Effect.Parallel([
      new Effect.Fade(lis[frame]),
      new Effect.Appear(lis[next_frame])
    ], {
      afterFinish: function() {
        fadeInOut.delay(delay, next_frame, lis);
      }
    });
  }

  C2C.init_slideshow = function() {
    var lis = this.select('.popup_slideimages li');
    fadeInOut.delay(delay, 0, lis);
  };

  C2C.init_popup = function() {
    // we might have more than one popup on the screen, so we go through
    // each of them and init if needed
    var popups = $$('.popup_content');
    
    popups.each(function(popup) {

      // do not init if already done
      if (popup.hasClassName('c2c-init')) return;

      popup.addClassName('c2c-init');

      // init slideshow
      if (popup.select('.popup_slideimages img').length > 1) {
        C2C.init_slideshow.bind(popup)();
      }

      // if the popup is used on a map, we need to make sure that
      // activities section (if present) can be toggled
      // TODO code is using ids, but this should be changed because we can have
      // more than one popup on the map
      popup.select('#routes_section_container .title2').each(function(elt) {
        elt.observe('click', function() {
          this.next().toggle();
        });
      });

    });
  };

})(window.C2C = window.C2C || {});
