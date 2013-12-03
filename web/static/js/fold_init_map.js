/*
 * This javascript defines some functions in order to hide sections or left navigation
 * while loading the page. It should not rely on other scripts and should be kept as small as possible
 * so that we can inline it once minified (it is ok if js size < 2048 bytes)
 *
 * The logic for opening/closing the sections and saving states in pref cookie is handled by fold.js
 * For now, maps are the only not-home sections that must be potentially hidden, so this code is called from
 * the map partial
 * Should we enable more sections, it would be better to move this code with the the other fold_init files
 */

(function(C2C, document) {

  // override existing setSectionStatus function
  var orig = C2C.setSectionStatus;

  // hide a section according to preference cookie or default value
  C2C.setSectionStatus = function(container, position, default_opened) {

    if (orig(container, position, default_opened)) { // true only if we have to hide the map section
      var alt_down = C2C.section_open;
      var div = document.getElementById(container + '_section_container');
      div.style.display = 'none';
      div.title = alt_down;

      var img = document.getElementById(container + '_toggle');
      img.className = img.className.replace('picto_close', 'picto_open');
      img.alt = '+';img.title = alt_down;

      document.getElementById('tip_' + container).innerHTML = '[' + alt_down + ']';
    }
  };

})(window.C2C = window.C2C || {}, document);
