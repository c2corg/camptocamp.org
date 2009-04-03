<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/routes.js?' . sfSVN::getHeadRevision('routes.js'), 'last');

// Here document = route
display_document_edit_hidden_tags($document, array('v4_id', 'v4_app'));

$link_with = $linked_doc ? $linked_doc->get('id') : 0; 
echo input_hidden_tag('summit_id', $link_with); 
// FIXME: form validation : test this value to prevent value 0 upon route creation
echo mandatory_fields_warning(array('route form warning'));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true, 'onchange' => 'hide_unrelated_fields()'));
echo object_group_tag($document, 'max_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'min_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'height_diff_up', null, 'meters', array('class' => 'short_input'));
?>
<div id="ski_snow_mountain_hiking_fields">
<?php
echo object_group_tag($document, 'height_diff_down', null, 'meters', array('class' => 'short_input'));
?>
</div>

<div id="ski_snow_mountain_rock_ice_fields">
<?php
echo object_group_tag($document, 'elevation', null, __('meters'), array('class' => 'short_input'), true, 'difficulties_start_elevation');
echo object_group_tag($document, 'difficulties_height', null, __('meters'), array('class' => 'short_input'));
?>
</div>
<?php
echo object_group_dropdown_tag($document, 'facing', 'app_routes_facings');
echo object_group_dropdown_tag($document, 'route_type', 'mod_routes_route_types_list');
echo object_group_dropdown_tag($document, 'duration', 'mod_routes_durations_list', null, true, null, 'days', 2);
?>
<div id="ski_snow_mountain_fields">
<?php
echo object_group_tag($document, 'is_on_glacier', 'object_checkbox_tag');
?>
</div>

<div id="ski_snow_mountain_rock_fields">
<?php
echo object_group_dropdown_tag($document, 'configuration', 'mod_routes_configurations_list',
                               array('multiple' => true));
?>
</div>

<div id="ski_snow_fields">
<?php
echo object_group_tag($document, 'slope', null, '', array('class' => 'long_input'));
?>
</div>

<div id="snow_mountain_rock_ice_fields">
<?php
echo object_group_dropdown_tag($document, 'global_rating', 'app_routes_global_ratings');
echo object_group_dropdown_tag($document, 'engagement_rating', 'app_routes_engagement_ratings');
echo object_group_dropdown_tag($document, 'equipment_rating', 'app_equipment_ratings_list');
?>
</div>

<div id="rock_mountain_fields">
<?php
echo object_group_dropdown_tag($document, 'rock_free_rating', 'app_routes_rock_free_ratings');
echo object_group_dropdown_tag($document, 'rock_required_rating', 'app_routes_rock_free_ratings');
echo object_group_dropdown_tag($document, 'aid_rating', 'app_routes_aid_ratings');
?>
</div>

<div id="snow_ice_fields">
<?php
echo object_group_dropdown_tag($document, 'ice_rating', 'app_routes_ice_ratings');
echo object_group_dropdown_tag($document, 'mixed_rating', 'app_routes_mixed_ratings');
?>
</div>

<div id="ski_fields">
<?php
echo object_group_dropdown_tag($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings');
echo object_group_dropdown_tag($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings');
echo object_group_dropdown_tag($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings');
echo object_group_dropdown_tag($document, 'labande_global_rating', 'app_routes_global_ratings');
echo object_group_dropdown_tag($document, 'sub_activities', 'mod_routes_sub_activities_list',
                               array('multiple' => true));
?>
</div>

<div id="hiking_fields">
<?php
echo object_group_dropdown_tag($document, 'hiking_rating', 'app_routes_hiking_ratings');
?>
</div>
<?php
echo file_upload_tag('gps_data');

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'largetext'));
echo object_group_bbcode_tag($document, 'remarks');
echo object_group_tag($document, 'gear', 'object_textarea_tag', null, array('class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'external_resources');
echo object_group_bbcode_tag($document, 'route_history');

include_partial('documents/form_history');
?>
