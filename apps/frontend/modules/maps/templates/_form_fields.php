<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = map
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_dropdown_tag($document, 'editor', 'mod_maps_editors_list');
echo object_group_dropdown_tag($document, 'scale', 'mod_maps_scales_list');
echo object_group_tag($document, 'code', null, '', array('class' => 'long_input'));

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext', 'abstract' => true));

include_partial('documents/form_history');
