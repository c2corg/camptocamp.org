<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = user
display_document_edit_hidden_tags($document, array('v4_id'));
// need the name, due to versioning and i18n in other models
echo input_hidden_tag('name', $document->getName());
echo mandatory_fields_warning();
?>

<h3><?php echo __('Information') ?></h3>

<?php
include_partial('documents/oam_coords', array('document' => $document));
?>

<h3><?php echo __('Description') ?></h3>

<?php
include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'largetext'));

include_partial('documents/form_history');
?>
