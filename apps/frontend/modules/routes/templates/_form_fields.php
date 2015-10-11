<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'Button', 'ModalBox');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/routes_edit.js', 'last');

// Here document = route
echo '<div>';
display_document_edit_hidden_tags($document, array('v4_id', 'v4_app'));

if ($linked_doc)
{
    $linked_with = $linked_doc->get('id');
    $linked_name = $linked_doc->get('name');
}
else
{
    $linked_with = 0;
}
echo input_hidden_tag('summit_id', $linked_with);
echo '</div>';
// FIXME: form validation : test this value to prevent value 0 upon route creation
echo mandatory_fields_warning(array('route form warning'));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));

$prefix = $linked_name . __('&nbsp;:');
echo object_group_tag($document, 'name', array('prefix' => $prefix, 'class' => 'bfc_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true, 'na' => array(0, 8)),
                               true, null, null, '', '', 'picto_act act_');
?>
<div data-act-filter="none">
<div class="article_gauche_5050">
<?php
echo object_group_tag($document, 'max_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_group_tag($document, 'min_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_group_tag($document, 'height_diff_up', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
?>
<div data-act-filter="1 2 3 6 7">
<?php
echo object_group_tag($document, 'height_diff_down', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
?>
</div>
<div  data-act-filter="6 7">
<?php
echo object_group_tag($document, 'route_length', array('suffix' => 'kilometers', 'class' => 'short_input', 'type' => 'number', 'min' => '0', step => '.1'));

?>
</div>

<div data-act-filter="1 2 3 4 5">
<?php
echo object_group_tag($document, 'elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900', 'label_name' => 'difficulties_start_elevation'));
echo object_group_tag($document, 'difficulties_height', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
?>
</div>
<?php
echo object_group_dropdown_tag($document, 'facing', 'app_routes_facings');
echo object_group_dropdown_tag($document, 'route_type', 'mod_routes_route_types_list');
echo object_group_dropdown_tag($document, 'duration', 'mod_routes_durations_list', array('na' => array(0)), true, null, null, 'days', 2);
?>
<div data-act-filter="1 2 3 7">
<?php
echo object_group_tag($document, 'is_on_glacier', array('callback' => 'object_checkbox_tag'));
?>
</div>

<div data-act-filter="1 2 3 4 7">
<?php
echo object_group_dropdown_tag($document, 'configuration', 'mod_routes_configurations_list',
                               array('multiple' => true));
?>
</div>
</div>

<div class="article_droite_5050">
<div data-act-filter="1 2 7">
<?php
echo object_group_tag($document, 'slope', array('class' => 'long_input'));
?>
</div>

<div data-act-filter="2 3 4 5">
<?php
echo object_group_dropdown_tag($document, 'global_rating', 'app_routes_global_ratings');
echo object_group_dropdown_tag($document, 'engagement_rating', 'app_routes_engagement_ratings');
?>
</div>

<div data-act-filter="2 3 5">
<?php echo object_group_dropdown_tag($document, 'objective_risk_rating', 'app_routes_objective_risk_ratings') ?>
</div>

<div data-act-filter="2 3 4 5">
<?php echo object_group_dropdown_tag($document, 'equipment_rating', 'app_equipment_ratings_list') ?>
</div>

<div data-act-filter="3 4">
<?php
echo object_group_dropdown_tag($document, 'rock_exposition_rating', 'app_routes_rock_exposition_ratings');
echo object_group_dropdown_tag($document, 'rock_free_rating', 'app_routes_rock_free_ratings');
echo object_group_dropdown_tag($document, 'rock_required_rating', 'app_routes_rock_free_ratings');
echo object_group_dropdown_tag($document, 'aid_rating', 'app_routes_aid_ratings');
?>
</div>

<div data-act-filter="2 5">
<?php
echo object_group_dropdown_tag($document, 'ice_rating', 'app_routes_ice_ratings');
echo object_group_dropdown_tag($document, 'mixed_rating', 'app_routes_mixed_ratings');
?>
</div>

<div data-act-filter="1">
<?php
$cotometre = '&nbsp; '
           . m_link_to(__('cotometre'), '@tool?action=cotometre',
                       array('title'=> __('cotometre long')),
                       array('width' => 600));
echo object_group_dropdown_tag($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings', null, true, null, null, $cotometre);
echo object_group_dropdown_tag($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings');
echo object_group_dropdown_tag($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings');
echo object_group_dropdown_tag($document, 'labande_global_rating', 'app_routes_global_ratings');
echo object_group_dropdown_tag($document, 'sub_activities', 'mod_routes_sub_activities_list',
                               array('multiple' => true));
?>
</div>

<div data-act-filter="6">
<?php
echo object_group_dropdown_tag($document, 'hiking_rating', 'app_routes_hiking_ratings');
?>
</div>

<div data-act-filter="7">
<?php
echo object_group_dropdown_tag($document, 'snowshoeing_rating', 'app_routes_snowshoeing_ratings');
?>
</div>

</div>
<div class="clear"></div>
<?php
echo file_upload_tag('gps_data');

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'medlargetext', 'abstract' => true, 'route_line' => true, 'placeholder' => __('routes_description_default')));
echo object_group_bbcode_tag($document, 'remarks', null, array('no_img' => true));
echo object_group_bbcode_tag($document, 'gear', 'specific gear', array('class' => 'smalltext', 'placeholder' => __('gear_default'), 'no_img' => true));

$backpack_content_list = array('pack_skitouring' => '1',
                               'pack_snow_ice_mixed_easy' => '2',
                               'pack_mountain_climbing_easy' => '3',
                               'pack_rock_climbing_bolted' => '4',
                               'pack_ice' => '2 5',
                               'pack_hiking' => '6');

foreach ($backpack_content_list as $backpack_content => $activities_filter)
{
    $link_text = __($backpack_content);
    $url = getMetaArticleRoute($backpack_content, false);
    $backpack_content_links[] = '<span data-act-filter="' . $activities_filter . '">'
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

echo object_group_bbcode_tag($document, 'route_history', null, array('placeholder' => __('route_history_default')));
?>
</div>
<?php

include_partial('documents/form_history');
?>
