<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$elevation = $document->get('max_elevation');
if (!empty($elevation)) {
    $title .= " - $elevation&nbsp;m";
}
$route = "@document_by_id_lang_slug?module=outings&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'outings', $route);

$image = make_thumbnail_slideshow($associated_images);

if (!$raw && $image)
{
    echo insert_popup_js();
}

?>
<div class="popup_desc"><?php
if ($image) {
    echo $image;
}
?>
<ul class="data">
<?php
$activities = $document->getRaw('activities');
li(field_activities_data($document));
li(field_bool_data($document, 'partial_trip'));
li(field_data_range_if_set($document, 'min_elevation', 'max_elevation', array('separator' => 'elevation separator', 'suffix' => 'meters')));
li(field_data_range_if_set($document, 'height_diff_up', 'height_diff_down', array('separator' => 'height diff separator', 'prefix_min' => '+',
        'prefix_max' => '-', 'suffix' => 'meters', 'range_only' => true)));
li(field_bool_data($document, 'outing_with_public_transportation'));
$access_elevation = field_data_if_set($document, 'access_elevation', array('suffix' => 'meters'));
if (empty($access_elevation))
{
    li(field_data_from_list_if_set($document, 'access_status', 'mod_outings_access_statuses_list'));
}
else
{
    $access_status = field_data_from_list_if_set($document, 'access_status', 'mod_outings_access_statuses_list', array('raw' => true, 'prefix' => ' - '));
    li($access_elevation . $access_status);
}
if (array_intersect(array(1,2,5), $activities)) // ski, snow or ice_climbing
{
    li(field_data_range_if_set($document, 'up_snow_elevation', 'down_snow_elevation', array('separator' => 'elevation separator',
            'suffix' => 'meters')));
}
li(field_data_from_list_if_set($document, 'conditions_status', 'mod_outings_conditions_statuses_list'));
li(field_data_from_list_if_set($document, 'glacier_status', 'mod_outings_glacier_statuses_list'));
if (array_intersect(array(1,2,5), $activities)) // ski, snow or ice_climbing
{
    li(field_data_from_list_if_set($document, 'track_status', 'mod_outings_track_statuses_list'));
}
li(field_data_from_list_if_set($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list'));
li(field_data_from_list_if_set($document, 'hut_status', 'mod_outings_hut_statuses_list'));
li(field_data_from_list_if_set($document, 'lift_status', 'mod_outings_lift_statuses_list'));
?>
</ul>
</div>
<?php

echo javascript_tag('C2C.init_popup();');
