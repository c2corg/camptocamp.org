<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

// Here document = article
display_document_edit_hidden_tags($document);
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));
?>

<h3><?php echo __('Information') ?></h3>

<?php
echo object_group_dropdown_tag($document, 'categories', 'mod_articles_categories_list',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'article_type', 'mod_articles_article_types_list');
?>

<h3><?php echo __('Description') ?></h3>

<?php
echo object_group_tag($document, 'abstract', 'object_textarea_tag', null, array('class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'description', __('article body'), array('class' => 'largetext'));

include_partial('documents/form_history');
?>
