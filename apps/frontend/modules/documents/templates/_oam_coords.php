<?php use_helper('Field', 'OamMap', 'I18N'); ?>
<div style="float:left;">
<p class="tips">
<?php 
$lon = $document->get('lon') ? $document->get('lon') : 0;
$lat = $document->get('lat') ? $document->get('lat') : 0;

$async_map = sfConfig::get('app_async_map', false) &&
             !sfContext::getInstance()->getRequest()->getParameter('debug', false);

// load or toggle map when clicking on the link
// - presence of mapLoading div shows that map has not been created yet
// - if map_load_async is defined, the map js should be retrieved asynchonously
// - the delay is to ensure that div is opened and ready for initiating the map
$map_init = $async_map ? 'map_load_async()' : 'map_init()';
$js = "
if (document.getElementById('mapLoading')) {
  $map_init;
} else {
  var elt = document.getElementById('georef_container');
  elt.style.display = (elt.style.display === 'none') ? '' : 'none';
}";

echo __('Regions are detected automatically according to coordinates'), " ",
     link_to_function(__('Use map'), $js) , __(' to point location'); 
?></p>
<?php 
echo object_coord_tag($document, 'lon', '°E');
echo object_coord_tag($document, 'lat', '°N');
?>
</div>
<?php 
echo show_georef_map($lon, $lat, $document->get('module'));
?>
