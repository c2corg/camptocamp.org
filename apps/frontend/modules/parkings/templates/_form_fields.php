<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

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
echo object_group_dropdown_tag($document, 'public_transportation_rating', 'app_parkings_public_transportation_ratings');
echo object_group_dropdown_tag($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list');

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', __('road access'));
echo object_group_bbcode_tag($document, 'public_transportation_description');
echo object_group_tag($document, 'snow_clearance_comment', 'object_textarea_tag', null, array('class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'accommodation');

include_partial('documents/form_history');
