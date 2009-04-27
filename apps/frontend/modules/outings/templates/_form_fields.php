<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'DateForm', 'Javascript', 'Escaping');

$static_base_url = sfConfig::get('app_static_url');

$response = sfContext::getInstance()->getResponse();
$response->addJavascript($static_base_url . '/static/js/outings.js?' . sfSVN::getHeadRevision('outings.js'), 'last');

echo javascript_tag("var confirm_outing_date_message = '" . addslashes(__('Has this outing really been done today?')) . "';
var outing_date_already_tested = false;");

// Here document = outing
$link_with = $linked_doc ? $linked_doc->get('id') : 0; 
echo input_hidden_tag('document_id', $link_with); 

display_document_edit_hidden_tags($document, array('v4_id', 'v4_app'));

echo mandatory_fields_warning(array(('outing form warning')));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'date', 'object_input_date_tag', '', array('year_start' => 1990, 'year_end' => date('Y')));
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true, 'onchange' => 'hide_outings_unrelated_fields()'));
echo object_group_tag($document, 'max_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'height_diff_up', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'height_diff_down', null, 'meters', array('class' => 'short_input'));
?>
<div id="outings_length">
<?php
echo object_group_tag($document, 'outing_length', null, 'kilometers', array('class' => 'short_input'));
?>
</div>
<?php
echo object_group_tag($document, 'partial_trip', 'object_checkbox_tag');
echo object_group_dropdown_tag($document, 'conditions_status', 'mod_outings_conditions_statuses_list');
?>
<div id="outings_glacier">
<?php
echo object_group_dropdown_tag($document, 'glacier_status', 'mod_outings_glacier_statuses_list');
?>
</div>
<?php
echo object_group_dropdown_tag($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list');
echo object_group_tag($document, 'outing_with_public_transportation', 'object_checkbox_tag');
echo object_group_dropdown_tag($document, 'access_status', 'mod_outings_access_statuses_list');
echo object_group_tag($document, 'access_elevation', null, 'meters', array('class' => 'short_input'));
?>
<div id="outings_track">
<?php
echo object_group_tag($document, 'up_snow_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'down_snow_elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_dropdown_tag($document, 'track_status', 'mod_outings_track_statuses_list');
?>
</div>
<?php
echo object_group_dropdown_tag($document, 'hut_status', 'mod_outings_hut_statuses_list');
echo object_group_dropdown_tag($document, 'lift_status', 'mod_outings_lift_statuses_list');
echo file_upload_tag('gps_data');

echo form_section_title('Description', 'form_desc', 'preview_desc');

?>
<div id="outings_conditions_levels">
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
<script type="text/javascript">
//<![CDATA[
var conditions_levels_fields = new Array(<?php echo "'" . implode("','", $level_fields) . "'" ?>);
var conditions_levels_next_id = <?php echo count($conditions_levels) ?>;

function addConditionLevel()
{
    new_line = '<?php echo addcslashes(get_partial('conditions_level', array('fields' => $level_fields,
                                                                                    'level'  => 'level_var', 
                                                                                    'data'   => NULL)), "\0..\37\\'\"\/") ?>';
    new_line = new_line.gsub('level_var', conditions_levels_next_id);
    new Insertion.Bottom('conditions_levels_tbody', new_line);
    conditions_levels_next_id++;
}
//]]>
</script>
<table id="conditions_levels_table">
  <colgroup></colgroup>
  <?php foreach ($level_fields as $field): ?>
  <colgroup id="<?php echo $field ?>"></colgroup>
  <?php endforeach ?>
  <thead>
    <tr>
      <th><?php echo link_to_function(image_tag($static_base_url . '/static/images/picto/add.png',
                                                array('alt' => '+', 'title' => __('add a condition level'))), 
                                      'addConditionLevel()') ?></th>
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
echo end_group_tag();
?>
</div>
<?php
// end of conditions levels fields

echo object_group_bbcode_tag($document, 'conditions', null, array('class' => 'mediumtext'));
echo object_group_bbcode_tag($document, 'weather');
echo object_group_tag($document, 'participants', 'object_textarea_tag', null, array('class' => 'smalltext'));
?>
<p class="edit-tips"><?php echo __('link contributors in view page') ?></p>
<?php
echo object_group_tag($document, 'timing', 'object_textarea_tag', null, array('class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'description', __('comments'), array('class' => 'mediumtext'));
echo object_group_bbcode_tag($document, 'hut_comments');
echo object_group_bbcode_tag($document, 'access_comments');

include_partial('documents/form_history');
