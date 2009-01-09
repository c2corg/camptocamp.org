<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = parking
display_document_edit_hidden_tags($document);
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));
?>

<h3><?php echo __('Information') ?></h3>

<?php
echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'lowest_elevation', null, 'meters', array('class' => 'short_input'));
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_dropdown_tag($document, 'public_transportation_rating', 'mod_parkings_public_transportation_ratings_list');
echo object_group_dropdown_tag($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list');
?>

<h3><?php echo __('Description') ?></h3>

<?php
echo object_group_bbcode_tag($document, 'description', __('road access'));
echo object_group_bbcode_tag($document, 'public_transportation_description');
echo object_group_tag($document, 'snow_clearance_comment', 'object_textarea_tag', null, array('class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'accommodation');

include_partial('documents/form_history');
