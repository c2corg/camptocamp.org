<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'Javascript', 'Escaping');
$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/parkings.js', 'last');

// Here document = parking
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_group_tag($document, 'lowest_elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_dropdown_tag($document, 'public_transportation_rating', 'app_parkings_public_transportation_ratings', array('onchange' => 'C2C.hide_parkings_unrelated_fields()'));
?>
<div id="tp_types">
<?php
// special handling for public_transportation_types. Cablecar (9) should be presented separately
echo object_group_dropdown_tag($document, 'public_transportation_types', 'app_parkings_public_transportation_types',
                               array('multiple' => true, 'na' => array('cable_car' => 9)));
?>
</div>
<?php
echo start_group_tag(), label_tag('cable_car_access'), ' <span>',
     checkbox_tag('public_transportation_types[]', 9, in_array(9, $document->getRaw('public_transportation_types')),
                  array('id' => 'cable_car_access')), '</span>', end_group_tag();

echo object_group_dropdown_tag($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list', array('onchange' => 'C2C.hide_parkings_unrelated_fields()'));

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', __('road access'));
?>
<div id="tp_desc">
<?php
echo object_group_bbcode_tag($document, 'public_transportation_description', null, array('placeholder' => __('public_transportation_description_default')));
?>
</div>
<div id="snow_desc">
<?php
echo object_group_tag($document, 'snow_clearance_comment', array('callback' => 'object_textarea_tag', 'class' => 'smalltext'));
?>
</div>
<?php
echo object_group_bbcode_tag($document, 'accommodation');

include_partial('documents/form_history');
