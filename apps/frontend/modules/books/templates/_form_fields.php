<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = book
display_document_edit_hidden_tags($document);
echo mandatory_fields_warning();
?>

<h3><?php echo __('Information') ?></h3>

<?php
echo object_group_tag($document, 'author', null, '', array('class' => 'long_input'));
echo object_group_tag($document, 'editor', null, '', array('class' => 'long_input'));
echo object_group_tag($document, 'isbn', null, '', array('class' => 'long_input'));
echo object_group_tag($document, 'url', null, '', array('class' => 'long_input'));
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list', array('multiple' => true));
echo object_group_dropdown_tag($document, 'langs', 'app_languages_c2c', array('multiple' => true));
echo object_group_dropdown_tag($document, 'book_types', 'mod_books_book_types_list', array('multiple' => true));
?>

<h3><?php echo __('Description') ?></h3>

<?php
include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));

echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'largetext'));

include_partial('documents/form_history');
?>
