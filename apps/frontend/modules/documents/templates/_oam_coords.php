<?php use_helper('Field', 'OamMap', 'I18N'); ?>
<div style="float:left;">
<p class="tips">
<?php 
$lon = $document->get('lon') ? $document->get('lon') : 0;
$lat = $document->get('lat') ? $document->get('lat') : 0;

$async_map = sfConfig::get('app_async_map', false) &&
             !sfContext::getInstance()->getRequest()->getParameter('debug', false);

echo __('Regions are detected automatically according to coordinates')."\n".
    link_to_function(__('Use map'), $async_map ? 'map_load_async()' : 'map_init()').
    __(' to point location'); 
?></p>
<?php 
echo object_coord_tag($document, 'lon', '°E');
echo object_coord_tag($document, 'lat', '°N');
?>
</div>
<?php 
echo show_georef_map($lon, $lat, $document->get('module'));
?>
