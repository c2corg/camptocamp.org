<?php use_helper('Field', 'OamMap', 'I18N'); ?>
<div class="clearing">
<div style="float:left; width: 300px;">
<?php 
echo object_group_tag($document, 'lon', null, '°E', array('class' => 'medium_input', 'onchange' => 'toggle_update_btn()'));
echo object_group_tag($document, 'lat', null, '°N', array('class' => 'medium_input', 'onchange' => 'toggle_update_btn()'));
?>
<p class="tips">
<?php 
$lon = $document->get('lon') ? $document->get('lon') : 0;
$lat = $document->get('lat') ? $document->get('lat') : 0;
echo __('Regions are detected automatically according to coordinates').' '.
    link_to_function(__('Use map'), "init_mapping($lon,$lat)").
    __(' to point location'); 
?></p>
</div>
<div id="mapping" style="float:left; width: 100%; display: none;">
<?php 
echo show_oam($lon, $lat);
?>
</div>
</div>