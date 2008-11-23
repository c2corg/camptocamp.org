<?php use_helper('Field', 'OamMap', 'I18N'); ?>
<div style="float:left;">
<p class="tips">
<?php 
$lon = $document->get('lon') ? $document->get('lon') : 0;
$lat = $document->get('lat') ? $document->get('lat') : 0;
echo __('Regions are detected automatically according to coordinates')."\n".
    link_to_function(__('Use map'), "init_mapping($lon,$lat)").
    __(' to point location'); 
?></p>
<?php 
echo object_coord_tag($document, 'lon', '°E');
echo object_coord_tag($document, 'lat', '°N');
?>
</div>
<div id="mapping" style="float:left; width: 100%; display: none;">
<?php 
echo show_oam($lon, $lat);
?>
</div>
