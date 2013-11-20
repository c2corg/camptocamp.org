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

// note: d3js uses functions unsupported by ie<=8 but
// feature detecting svg support should be enough
$script_url = minify_get_combined_files_url(array('/static/js/d3.min.js',
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
  $('#elevation_profile_container_tbg').hide();
  $('#elevation_profile_nav').hide();
} else {
  C2C.elevation_profile_data = {
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
  var elt = $('.ui-spinner .fill')[0];
  for (var i in props) {
    var prop = props[i];
    if (elt.style[prop] !== undefined) {
      animation = true;
      break;
    }
  }
  if (!animation) {
    elt = $('.elevation_profile_loading')
      .html('" . image_tag('/static/images/indicator.gif') . "')
      .removeClass('ui-spinner');
  }
  load_elevation_profile = function() {
    $.ajax({
      url: '$script_url',
      dataType: 'script',
      cache: true
    }).done(function() {
      $('#elevation_profile_container_section_container').addClass('profile_loaded');
    });
  }

  var section = $('#elevation_profile_container_section_container');
  $('#elevation_profile_container').click(function() {
    if (!section.hasClass('profile_loaded')) {
      load_elevation_profile();
    }
  });

}";

echo javascript_queue($js);
