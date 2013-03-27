<?php
use_helper('MyMinify');
echo start_section_tag('Elevation profile', 'elevation_profile_container', 'closed');
?>
<!-- TODO make that cleaner -->
<form>
  <label><input type="radio" name="profile_mode" value="distance" checked /> Distance</label>
  <label><input type="radio" name="profile_mode" value="time" /> Time</label>
</form>
<?php
echo end_section_tag();

// FIXME d3js uses function sunsupported by ie<=8 but
// feature detecting svg support should be enough//
$script_url = minify_get_combined_files_url(array('/static/js/d3.v3.js',
                                                  '/static/js/elevation_profile.js'),
                                            (bool) sfConfig::get('app_minify_debug'));
$js = "var div = document.createElement('div');
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
  // define function for opening+change class once intializaed
  function c2c_load_elevation_profile() {
    c2c_asyncload('$script_url');
    $('elevation_profile_container_section_container').addClassName('profile_loaded');
  }
}";

// TODO
// - i18n
// - waiting UI (use beautiful waiting svg :p)
// - nav
// - not supproted -> also hide nav?
// - mobile -> no animation !
// mobile version
// is y'a pas d'altitude ??
// function()()
// gpx sans time
// check also altitude...

echo javascript_tag($js);
