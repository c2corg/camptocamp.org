<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'Javascript', 'Escaping');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript(sfConfig::get('app_static_url') . '/static/js/parkings.js', 'last');

?>
<script language="Javascript" type="text/javascript">
//<![CDATA[
<?php
    echo 'field_default = new Array();';
    echo "\n" . 'field_default[0] = Array(\'public_transportation_description\', "' . __('public_transportation_description_default') . '");';
?>
//]]>
</script>
<?php

// Here document = parking
display_document_edit_hidden_tags($document);
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'lowest_elevation', null, 'meters', array('class' => 'short_input'));
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_dropdown_tag($document, 'public_transportation_rating', 'app_parkings_public_transportation_ratings', array('onchange' => 'hide_parkings_unrelated_fields()'));
?>
<div id="tp_types">
<?php
echo object_group_dropdown_tag($document, 'public_transportation_types', 'app_parkings_public_transportation_types',
                               array('multiple' => true));
?>
</div>
<?php
echo object_group_dropdown_tag($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list', array('onchange' => 'hide_parkings_unrelated_fields()'));

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', __('road access'));
?>
<div id="tp_desc">
<?php
echo object_group_bbcode_tag($document, 'public_transportation_description', null, array('onfocus' => 'hideFieldDefault(0)'));
?>
</div>
<div id="snow_desc">
<?php
echo object_group_tag($document, 'snow_clearance_comment', 'object_textarea_tag', null, array('class' => 'smalltext'));
?>
</div>
<?php
echo object_group_bbcode_tag($document, 'accommodation');

include_partial('documents/form_history');
