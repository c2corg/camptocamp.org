<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = map
display_document_edit_hidden_tags($document);
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));
?>

<h3><?php echo __('Information') ?></h3>

<?php
echo object_group_dropdown_tag($document, 'editor', 'mod_maps_editors_list');
echo object_group_dropdown_tag($document, 'scale', 'mod_maps_scales_list');
echo object_group_tag($document, 'code', null, '', array('class' => 'long_input'));
?>

<h3><?php echo __('Description') ?></h3>

<?php
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext'));

include_partial('documents/form_history');
