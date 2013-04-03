<?php
use_helper('MyMinify');
echo start_section_tag('Elevation profile', 'elevation_profile_container', 'closed');
?>
<div class="elevation_profile_loading ui-spinner">
   <span class="side side-left"><span class="fill"></span></span>
  <span class="side side-right"><span class="fill"></span></span>
</div>
<div class="elevation_profile_controls" style="display: none;">
  <div>
  <!-- TODO add some information here? like number of points, ...
       And later on, options like move the map, ... -->
  </div>
  <form class="xaxis-dimension">
    <?php echo __('x axis:') ?><br />
    <label><input type="radio" name="profile_mode" value="distance" checked /> Distance</label>
    <label><input type="radio" name="profile_mode" value="time" /> Time</label>
  </form>
</div>
<div id="elevation_profile">
</div>
<?php
echo end_section_tag();

// FIXME d3js uses functions unsupported by ie<=8 but
// feature detecting svg support should be enough//
$script_url = minify_get_combined_files_url(array('/static/js/d3.v3.js',
                                                  '/static/js/elevation_profile.js'),
                                            (bool) sfConfig::get('app_minify_debug'));
$js = "(function() { \"use strict\";
var div = document.createElement('div');
div.innerHTML = '<svg/>';
var svg_supported = (div.firstChild && div.firstChild.namespaceURI) == 'http://www.w3.org/2000/svg';
if (!svg_supported) {
  $('elevation_profile_container_tbg').hide();
} else {
  // some constants
  window.c2cprofile = {
    track: '" . url_for("@export_gpx?module=outings&id=$id&lang=$lang") . "',
    i18n: {
      yLegend: '" . __('Elevation (m)') . "',
      x1Legend: '" . __('Distance (km)') . "',
      x2Legend: '" . __('Duration (hrs)') . "',
      elevation: '" . __('Elevation') . "',
      distance: '" . __('Distance') . "',
      time: '" . __('Time') . "',
      duration: '" . __('Duration') . "',
      meters: '" . __('meters') . "',
      kilometers: '" . __('kilometers') . "'
    }
  };
  // we test if css animations are supported, else replace the spinner by
  // an animated gif
  // we don't test for css transform support (which is also needed) since every
  // browser that supports animations also supports transforms
  var animation = false;
  var props = ['animationName', 'WebkitAnimationName', 'MozAnimationName',
               'oAnimationName', 'msAnimationName'];
  var elt = $$('.ui-spinner .fill')[0];
  for (var i in props) {
    var prop = props[i];
    if (elt.style[prop] !== undefined) {
      animation = true;
      break;
    }
  }
  if (!animation) {
    elt = $$('.elevation_profile_loading')[0];
    elt.innerHTML = '" . image_tag('/static/images/indicator.gif') . "';
    elt.removeClassName('ui-spinner');
  }
  // define function for opening+change class once intializaed
  window.c2c_load_elevation_profile = function() {
    c2c_asyncload('$script_url');
    $('elevation_profile_container_section_container').addClassName('profile_loaded');
  }
}})()";

echo javascript_tag($js);
