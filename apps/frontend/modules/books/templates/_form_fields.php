<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = book
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'author', array('class' => 'long_input'));
echo object_group_tag($document, 'editor', array('class' => 'long_input'));
echo object_group_tag($document, 'isbn', array('class' => 'long_input', 'maxlength' => '17', 'label_name' => 'isbn_or_issn'));
echo object_group_tag($document, 'url', array('class' => 'long_input', 'type' => 'url'));
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list', array('multiple' => true),
                               false, null, null, '', '', 'picto_act act_');
echo object_group_tag($document, 'publication_date', array('class' => 'medium_input'));
echo object_group_tag($document, 'nb_pages', array('class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '30000'));
echo object_group_dropdown_tag($document, 'langs', 'app_languages_book', array('multiple' => true));
echo object_group_dropdown_tag($document, 'book_types', 'mod_books_book_types_list', array('multiple' => true));

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext', 'abstract' => true));

include_partial('documents/form_history');
?>
