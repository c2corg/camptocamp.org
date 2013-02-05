<?php
use_helper('Javascript');
$lang = $sf_user->getCulture();

if ($debug) {
    include_partial('documents/map_cgxp_include_debug');
} else {
    use_stylesheet('/static/js/carto/build/app.css', 'last');
    use_javascript('/static/js/carto/build/app.js', 'maps');
}
use_stylesheet('/static/js/carto/viewer.css', 'last');
use_javascript('/static/js/carto/build/lang-fr.js', 'maps');
use_javascript('/static/js/carto/viewer.js', 'maps');


//include_partial('documents/map_i18n');
?>

<!--
<div id='map1' style='width:700px;height:400px;'></div>
<script type="text/javascript">
var map = new c2corg.Map({
      div: 'map1', // id of the div element to put the map in
          addLayerSwitcher: true,
              addMiniMap: true,
                  //zoom: 13,
                      //center: [767198, 5766524],
                          layers: ['summits']
});
map.addMarker({
      position: [767298, 5766624],
          size: [16, 16],
              icon: "http://s.camptocamp.org/static/images/modules/parkings_mini.png"
});
//map.addCustomLayer('gpx', 'GPX Layer', '/c2corg/wsgi/proj/gpx/282634.gpx');
</script>
-->

<div id="mapPort">
  <div id="mapLoading"><img src="<?php echo $app_static_url ?>/static/images/indicator.gif" alt="" /> <?php echo __('Map is loading...') ?></div>
</div>
