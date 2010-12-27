<?php use_helper('Field', 'OamMap', 'I18N'); ?>
<div style="float:left;">
<p class="tips">
<?php 
$lon = $document->get('lon') ? $document->get('lon') : 0;
$lat = $document->get('lat') ? $document->get('lat') : 0;
echo __('Regions are detected automatically according to coordinates')."\n".
    link_to_function(__('Use map'), "c2corg.docGeoref.init($lon,$lat)").
    __(' to point location'); 
?></p>
<?php 
echo object_coord_tag($document, 'lon', '°E');
echo object_coord_tag($document, 'lat', '°N');
?>
</div>
<?php 
echo show_georef_map($lon, $lat, $sf_params->get('lang'), $document->get('module'));
?>
