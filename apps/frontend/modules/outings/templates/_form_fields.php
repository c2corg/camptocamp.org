<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'DateForm', 'Javascript', 'Escaping', 'General');

$mw_contest_enabled = sfConfig::get('app_mw_contest_enabled'); // shunt for mw contest

$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/outings_edit.js', 'last');
if ($mw_contest_enabled) $response->addJavascript('/static/js/mw.js', 'last');

echo javascript_queue("C2C.confirm_outing_date_message = '" . addslashes(__('Has this outing really been done today?')) . "';
C2C.confirm_outing_activities_message = '" . addslashes(__('Is really a multi-activity outing?')) . "';
C2C.alert_outing_paragliding_message = '" . addslashes(__('paragliding can not be selected alone')) . "';
C2C.confirm_snow_elevation_message = '" . addslashes(__('Snow elevation is correct?')) . "';");

// Here document = outing
$link_with = $linked_doc ? $linked_doc->get('id') : 0; 
echo '<div>';
echo input_hidden_tag('document_id', $link_with);
if ($new_document == false && $mw_contest_enabled == true)
{
    echo javascript_queue('C2C.mw_contest_article_id=' . sfConfig::get('app_mw_contest_id')); // for use with MW contest
}
display_document_edit_hidden_tags($document, array('v4_id', 'v4_app'));
echo '</div>';

echo mandatory_fields_warning(array(('outing form warning')));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'date', array('callback' => 'object_input_date_tag', 'year_start' => date('Y'), 'year_end' => sfConfig::get('app_date_year_min')));
?>
<div class="article_gauche_5050">
<?php
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true, 'na' => array(0)),
                               true, null, null, '', '', 'picto_act act_');
?>
</div>
<div class="article_droite_5050">
    <?php echo __('select activities according to outing') ?>
</div>
<div class="article_gauche_5050">
<?php
echo object_group_tag($document, 'partial_trip', array('callback' => 'object_checkbox_tag'));
echo object_group_tag($document, 'max_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_group_tag($document, 'height_diff_up', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
?>
<div data-act-filter="1 6 7 height_diff_down">
<?php
echo object_group_tag($document, 'height_diff_down', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
?>
</div>
<div data-act-filter="1 6 7 length">
<?php
echo object_group_tag($document, 'outing_length', array('suffix' => 'kilometers', 'class' => 'short_input', 'type' => 'number', 'min' => '0', step => '.1'));
?>
</div>
<?php
echo object_group_tag($document, 'outing_with_public_transportation', array('callback' => 'object_checkbox_tag'));
if ($mw_contest_enabled == true)
{
$mw_checked = false;
if (isset($associated_articles) && count($associated_articles))
{
    foreach($associated_articles as $article)
    {
        if ($article['id'] == sfConfig::get('app_mw_contest_id'))
        {
            $mw_checked = true;
            break;
        }
    }
}
?>
<div id="mw_contest"> 
<?php
echo __('Participate to MW %1% contest', array('%1%' => sfConfig::get('app_mw_contest_id')));
echo checkbox_tag('mw_contest_associate', 1, $mw_checked);
?>
</div>
<?php
}
echo object_group_dropdown_tag($document, 'access_status', 'mod_outings_access_statuses_list');
echo object_group_tag($document, 'access_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
?>
<div data-act-filter="1 2 5 7">
<?php
echo object_group_tag($document, 'up_snow_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_group_tag($document, 'down_snow_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
?>
</div>
</div>
<div class="article_droite_5050">
<?php
echo object_group_dropdown_tag($document, 'conditions_status', 'mod_outings_conditions_statuses_list');
?>
<div data-act-filter="1 2 3 7">
<?php
echo object_group_dropdown_tag($document, 'glacier_status', 'mod_outings_glacier_statuses_list');
?>
</div>
<div data-act-filter="1 2 5 7">
<?php
echo object_group_dropdown_tag($document, 'track_status', 'mod_outings_track_statuses_list');
?>
</div>
<?php
echo object_group_dropdown_tag($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list');
echo object_group_dropdown_tag($document, 'hut_status', 'mod_outings_hut_statuses_list');
echo object_group_dropdown_tag($document, 'lift_status', 'mod_outings_lift_statuses_list');
?>
</div>
<div class="clear"></div>
<?php
echo file_upload_tag('gps_data');

echo form_section_title('Description', 'form_desc', 'preview_desc');

?>
<div data-act-filter="1 2 3 6 7 8">
<?php
echo object_group_bbcode_tag($document, 'outing_route_desc', null, array('class' => 'smalltext', 'placeholder' => __('outing_route_desc_default')));

?>
</div>
<div data-act-filter="1 2 5 7">
<?php
// conditions levels fields:
echo start_group_tag();
echo label_tag('conditions_levels');
$conditions_levels = $document->getRaw('conditions_levels');
$level_fields = sfConfig::get('mod_outings_conditions_levels_fields');
if (empty($conditions_levels))
{
    $conditions_levels = array('0' => array());
    foreach ($level_fields as $field)
    {
        $conditions_levels[0][$field] = '';
    }
}
?>
<table id="conditions_levels_table">
  <colgroup></colgroup>
  <?php foreach ($level_fields as $field): ?>
  <colgroup id="<?php echo $field ?>"></colgroup>
  <?php endforeach ?>
  <thead>
    <tr>
      <th><?php echo link_to(picto_tag('picto_add', __('add a condition level')), '#', array('class' => 'add-condition-level')) ?></th>
      <?php foreach ($level_fields as $field): ?>
        <th><?php echo __($field) ?></th>
      <?php endforeach ?>
    </tr>
  </thead>
  <tbody id="conditions_levels_tbody">
    <?php
    foreach ($conditions_levels as $level => $data)
    {
        include_partial('conditions_level', array('fields' => $level_fields, 'level' => $level, 'data' => $data)); 
    }
    ?>
  </tbody>
</table>
<?php
echo javascript_queue("
var tbody = $('#conditions_levels_tbody');
var next_id =  tbody.find('tr').length;

$('.add-condition-level').click(function(e) {
  e.preventDefault();
  tbody.append('" . addcslashes(get_partial('conditions_level', array('fields' => $level_fields,
                                                                       'level'  => '%%var%%',
                                                                       'data'   => null)), "\0..\37\\'\"\/") .
  "'.replace(/%%var%%/g, next_id));
  next_id++;
});

tbody.on('click', '.remove-condition-level', function(e) {
  e.preventDefault();
  $(this).closest('tr').remove();
});
");
echo end_group_tag();
// end of conditions levels fields

?>
</div>
<?php

$activities = $document->getRaw('activities');
if (array_intersect($activities, array(3,4,5)) || (in_array(2, $activities) && !array_intersect($activities, array(1,6,7))))
{
    $conditions_title = 'conditions_and_equipment';
    if (array_intersect($activities, array(2,5)))
    {
        $conditions_default = 'conditions_ice_default';
    }
    else
    {
        $conditions_default = 'conditions_rock_default';
    }
}
else if (array_intersect($activities, array(1,7)))
{
    $conditions_title = null;
    $conditions_default = 'conditions_ski_default';
}
else
{
    $conditions_title = null;
    $conditions_default = 'conditions_hike_default';
}

echo object_group_bbcode_tag($document, 'conditions', $conditions_title, array('class' => 'mediumtext', 'placeholder' => __($conditions_default)), true, $conditions_title);
?>
<div data-act-filter="1 2 5 7">
<?php
echo object_group_dropdown_tag($document, 'avalanche_date', 'mod_outings_avalanche_date_edit_list', array('multiple' => true));
?>
<div id="avalanche_desc_form">
<?php
echo object_group_bbcode_tag($document, 'avalanche_desc', null, array('class' => 'smalltext', 'placeholder' => __('avalanche_desc_tooltip')));
?>
</div>
</div>
<?php
echo object_group_bbcode_tag($document, 'weather', null, array('no_img' => true));
echo object_group_bbcode_tag($document, 'participants', null, array('class' => 'smalltext', 'no_img' => true));
?>
<p class="edit-tips"><?php echo __('link contributors in view page') ?></p>
<?php
echo object_group_bbcode_tag($document, 'timing', null, array('class' => 'smalltext', 'no_img' => true));
echo object_group_bbcode_tag($document, 'description', __('comments'), array('class' => 'mediumtext', 'placeholder' => __('outings_description_default')));
echo object_group_bbcode_tag($document, 'hut_comments');
echo object_group_bbcode_tag($document, 'access_comments');

include_partial('documents/form_history');
