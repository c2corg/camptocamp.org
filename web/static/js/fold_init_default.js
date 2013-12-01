/*
 * This javascript defines some functions in order to hide sections or left navigation
 * while loading the page. It should not rely on other scripts and should be kept as small as possible
 * so that we can inline it once minified (it is ok if js size < 2048 bytes)
 *
 * The logic for opening/closing the sections and saving states in pref cookie is handled by fold.js
 */

(function(C2C, document) {

  // check in the preferences cookie whether a section (identified by its index)
  // should be hidden
  C2C.shouldHide = function(position, default_opened) {
    var cookie_value = /(?:^|;)\s?fold=([tfx]{20})(?:;|$)/.exec(document.cookie);
    if (cookie_value) {
      switch (cookie_value[1].charAt(position)) {
        case 't': return false;
        case 'f': return true;
      }
    }
    // if no cookie value or value is neither f nor t
    return !default_opened;
  };

  // hide a section according to preference cookie or default value
  C2C.setSectionStatus = function(container, position, default_opened) {

    if (!C2C.shouldHide(position, default_opened)) return;

    if (container == 'nav') { // hide nav
      if (/MSIE [67].0/.exec(navigator.userAgent)) return;
     
      var content_box = document.getElementById('content_box');
      if (content_box) {
        content_box.className += ' wide';
      }
    } else if (container == 'map_container') {
      return true; // this return value will be used by fold_init_map.js
    }
  };

})(window.C2C = window.C2C || {}, document);
