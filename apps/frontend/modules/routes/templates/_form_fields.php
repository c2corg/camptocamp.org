<?php
use_helper('Object', 'Language', 'Validation', 'MyForm','Button');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/routes.js', 'last');

javascript_tag('field_default = new Array();field_default[0] = Array(\'gear\', "' .
               __('gear_default') . '");field_default[1] = Array(\'route_history\', "' .
               __('route_history_default') . '");');

// Here document = route
echo '<div>';
display_document_edit_hidden_tags($document, array('v4_id', 'v4_app'));

$link_with = $linked_doc ? $linked_doc->get('id') : 0; 
echo input_hidden_tag('summit_id', $link_with);
echo '</div>';
// FIXME: form validation : test this value to prevent value 0 upon route creation
echo mandatory_fields_warning(array('route form warning'));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true, 'onchange' => 'hide_unrelated_fields()'),
                               false, null, null, '', '', 'picto_act act_');
?>
<div id="data_fields">
<div class="article_gauche_5050">
<?php
echo object_group_tag($document, 'max_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'min_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'height_diff_up', null, 'meters', array('class' => 'short_input'));
?>
<div id="ski_snow_mountain_hiking_fields">
<?php
echo object_group_tag($document, 'height_diff_down', null, 'meters', array('class' => 'short_input'));
?>
</div>
<div id="hiking2_fields">
<?php
echo object_group_tag($document, 'route_length', null, 'kilometers', array('class' => 'short_input'));
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
echo object_group_dropdown_tag($document, 'duration', 'mod_routes_durations_list', null, true, null, null, 'days', 2);
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
</div>

<div class="article_droite_5050">
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
</div>
<div class="clear"></div>
<?php
echo file_upload_tag('gps_data');

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext', 'abstract' => true));
echo object_group_bbcode_tag($document, 'remarks', null, array('no_img' => true));
echo object_group_bbcode_tag($document, 'gear', 'specific gear', array('class' => 'smalltext', 'onfocus' => 'hideFieldDefault(0)', 'no_img' => true));

$backpack_content_list = array('pack_ski' => 'pack_skitouring',
                               'pack_snow_easy' => 'pack_snow_ice_mixed_easy',
                               'pack_mountain_easy' => 'pack_mountain_climbing_easy',
                               'pack_rock_bolted' => 'pack_rock_climbing_bolted',
                               'pack_ice' => 'pack_ice',
                               'pack_hiking' => 'pack_hiking');

foreach ($backpack_content_list as $pack_id => $backpack_content)
{
    $link_text = __($backpack_content);
    $url = getMetaArticleRoute($backpack_content, false);
    $backpack_content_links[] = '<span id="' . $pack_id . '_fields">'
                              . link_to($link_text, $url, array('onclick' => "window.open(this.href);return false;"))
                              . '</span>';
}
$gear_tips = '<p id="usual_gear" class="edit-tips">'
           . __('do not mention usual gear') . __('&nbsp;:')
           . implode('', $backpack_content_links)
           . "</p>\n";
echo $gear_tips;

echo object_group_bbcode_tag($document, 'external_resources');
if (isset($associated_books) && count($associated_books))
{
  use_helper('Field');
  echo '<div class="extres_books"><p class="edit-tips">', __('do not duplicate linked books'), '</p>',
       format_book_data($associated_books, 'br', null, false), '</div>';
}

echo object_group_bbcode_tag($document, 'route_history', null, array('onfocus' => 'hideFieldDefault(1)'));
?>
</div>
<?php

include_partial('documents/form_history');
?>
