<?php
use_helper('MyMinify', 'JavascriptQueue');

$mobile = c2cTools::mobileVersion();
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
    <span class="xaxis-label"><?php echo __('x axis:') ?></span>
    <label><input type="radio" name="profile_mode" value="distance" checked /> <?php echo __('Distance'); ?></label>
    <label><input type="radio" name="profile_mode" value="time" /> <?php echo __('Time'); ?></label>
  </form>
</div>
<div id="elevation_profile">
</div>
<?php
echo end_section_tag();

// FIXME d3js uses functions unsupported by ie<=8 but
// feature detecting svg support should be enough
$script_url = minify_get_combined_files_url(array('/static/js/d3.js',
                                                  '/static/js/elevation_profile.js'),
                                            (bool) sfConfig::get('app_minify_debug'));
// notes:
// - first test is to test for support of inline svg
// - we also test if css animations are supported; else replace the spinner
//   by an animated gif. We d'ont test for css transforms support (also needed)
//   since every browser supporting animations supports transforms
// - c2c_load_elevation_profile is called once the section is opened, and
//   ensures the js is only loaded once
$js = "var div = document.createElement('div');
div.innerHTML = '<svg/>';
var svg_supported = (div.firstChild && div.firstChild.namespaceURI) == 'http://www.w3.org/2000/svg';
if (!svg_supported) {
  jQuery('#elevation_profile_container_tbg').hide();
  jQuery('#elevation_profile_nav').hide();
} else {
  window.c2cprofile = {
    track: '" . url_for("@export_gpx?module=outings&id=$id&lang=" . $sf_user->getCulture()) . "',
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
  var animation = false;
  var props = ['animationName', 'WebkitAnimationName', 'MozAnimationName'];
  var elt = jQuery('.ui-spinner .fill')[0];
  for (var i in props) {
    var prop = props[i];
    if (elt.style[prop] !== undefined) {
      animation = true;
      break;
    }
  }
  if (!animation) {
    elt = jQuery('.elevation_profile_loading')
      .html('" . image_tag('/static/images/indicator.gif') . "')
      .removeClass('ui-spinner');
  }
  window.c2c_load_elevation_profile = function() {
    c2c_asyncload('$script_url');
    jQuery('#elevation_profile_container_section_container').addClass('profile_loaded');
  }
}";

// In mobile version, we don't have the dynamic map, so we don't have c2c_asyncload defined
// Also if async map loading is disabled
// FIXME the function should be defined at top level and could be used for addthis and analytics snippets (and more...)
// + logic is somewhat not very clear...
if ($mobile || !sfConfig::get('app_async_map', false))
{
    echo javascript_tag('function c2c_asyncload(jsurl) { var a = document.createElement("script"), h = document.getElementsByTagName("head")[0];' .
        'a.async = 1; a.src = jsurl; h.appendChild(a); }');
}

echo javascript_queue($js);
