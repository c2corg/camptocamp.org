<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'DateForm', 'Javascript', 'Escaping', 'General');

$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/xreports_edit.js', 'last');

echo javascript_queue("C2C.confirm_xreport_date_message = '" . addslashes(__('Has this xreport really been done today?')) . "';
C2C.confirm_xreport_activities_message = '" . addslashes(__('Is really a multi-activity xreport?')) . "';");

$mobile_version = c2cTools::mobileVersion();

// Here document = xreport
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', array('class' => 'long_input'));

echo form_section_title('Accident infos', 'form_info', 'preview_info');

echo object_group_tag($document, 'date', array('callback' => 'object_input_date_tag', 'year_start' => date('Y'), 'year_end' => sfConfig::get('app_date_year_min')));

echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true, 'na' => array(0)),
                               true, null, null, '', '', 'picto_act act_');

include_partial('documents/oam_coords', array('document' => $document));

echo '<br />';
echo object_group_tag($document, 'elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));

echo object_group_bbcode_tag($document, 'place', null, array('class' => 'smalltext', 'placeholder' => __('place_default')));

echo    '<div class="col_left">'
      , object_group_dropdown_tag($document, 'event_type', 'mod_xreports_event_type_list', array('multiple' => true, 'na' => array(0)))
      , '</div>'
      , '<div class="col col_50 tips">'
      , '<p>' , __('_event_type_info') , '</p>'
      , ( !$mobile_version ? '<p>' . __('unselect dropdown tip') . '</p>' : '' )
      , '</div>'
;

echo '<div id="is_avalanche">';
echo object_group_dropdown_tag($document, 'avalanche_level', 'mod_xreports_avalanche_level_list');
echo object_group_dropdown_tag($document, 'avalanche_slope', 'mod_xreports_avalanche_slope_list');
echo '</div>';

echo object_group_tag($document, 'nb_participants', array('class' => 'short_input', 'type' => 'number', 'min' => '1', 'max' => '10000', 'default_value' => 1));
echo    '<div class="col_left">'
      , object_group_tag($document, 'nb_impacted', array('class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '10000'))
      , '</div>'
      , '<div class="col col_50 tips">'
      , __('_nb_impacted_info')
      , '</div>'
;

echo '<div id="is_impacted">';
echo    '<div class="col_left">'
      , object_group_dropdown_tag($document, 'severity', 'mod_xreports_severity_list', null, true, null, null, '', 1)
      , '</div>'
      , '<div class="col col_50 tips">'
      , __('_severity_info')
      , '</div>'
;
echo '</div>';

echo object_group_tag($document, 'rescue', array('callback' => 'object_checkbox_tag'));

echo form_section_title('Accident description', 'form_desc', 'preview_desc');
echo object_group_bbcode_tag($document, 'description', 'xreport_description', array('class' => 'medlargetext', 'placeholder' => __('xreport_description_default')), true, 'xreport_description');

echo form_section_title('Accident factors', 'form_factors', 'preview_factors');
?>
<p class="big_tips"><?php echo __('Accident factors intro') ?></p>
<?php

echo object_group_bbcode_tag($document, 'route_study', null, array('class' => 'smalltext', 'placeholder' => __('route_study_default')));
echo object_group_bbcode_tag($document, 'conditions', 'xreport_conditions', array('class' => 'smalltext', 'placeholder' => __('xreport_conditions_default')), true, 'xreport_conditions');
echo object_group_bbcode_tag($document, 'training', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('training_default')));
echo object_group_bbcode_tag($document, 'motivations', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('motivations_default')));
echo object_group_bbcode_tag($document, 'group_management', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('group_management_default')));
echo object_group_bbcode_tag($document, 'risk', 'xreport_risk', array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('xreport_risk_default')), true, 'xreport_risk');
echo object_group_bbcode_tag($document, 'time_management', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('time_management_default')));
echo object_group_bbcode_tag($document, 'safety', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('safety_default')));
echo object_group_bbcode_tag($document, 'reduce_impact', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('reduce_impact_default')));
echo object_group_bbcode_tag($document, 'increase_impact', null, array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('increase_impact_default')));
echo object_group_bbcode_tag($document, 'modifications', 'xreport_modifications', array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('xreport_modifications_default')), true, 'xreport_modifications');
echo object_group_bbcode_tag($document, 'other_comments', 'xreport_other_comments', array('no_img' => true, 'class' => 'smalltext', 'placeholder' => __('xreport_other_comments_default')), true, 'xreport_other_comments');

echo form_section_title('Accident profil', 'form_profil', 'preview_profil');

echo object_group_dropdown_tag($document, 'author_status', 'mod_xreports_author_status_list');
?>
<p class="big_tips"><?php echo __('The following infos are visible only by the moderators') ?></p>
<?php
echo object_group_dropdown_tag($document, 'activity_rate', 'mod_xreports_activity_rate_list');
echo object_group_dropdown_tag($document, 'nb_outings', 'mod_xreports_nb_outings_list', null, true, 'nb_outings_per_year');
echo object_group_dropdown_tag($document, 'autonomy', 'mod_xreports_autonomy_list');
echo object_group_tag($document, 'age', array('class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '130', 'suffix' => __('years')));
echo object_group_dropdown_tag($document, 'gender', 'mod_xreports_gender_list');
echo object_group_dropdown_tag($document, 'previous_injuries', 'mod_xreports_previous_injuries_list');


include_partial('documents/form_history');
?>
