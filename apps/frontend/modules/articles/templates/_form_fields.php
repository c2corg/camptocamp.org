<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

$creator = $document->getCreator();
// do not allow to modify the article type:
// * only moderators have all right
// * the creator can switch from personal to collaborative
// * other users cannot
$hide_article_type_edit = !$new_document && ((!$sf_user->hasCredential('moderator') && $document->get('article_type') == 1)
                                          || (!$sf_user->hasCredential('moderator') && $sf_user->getId() != $creator['id']));
$hidden_fields = array();
echo '<div>';
if ($hide_article_type_edit)
{
    array_push($hidden_fields, 'article_type');
}
display_document_edit_hidden_tags($document, $hidden_fields);
echo '</div>';
echo mandatory_fields_warning(array(('article form warning')));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_dropdown_tag($document, 'categories', 'mod_articles_categories_list',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                               array('multiple' => true),
                               false, null, null, '', '', 'picto_act act_');
if (!$hide_article_type_edit)
{
    echo object_group_dropdown_tag($document, 'article_type', 'mod_articles_article_types_list');
}

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'abstract', null, array('no_img' => true, 'class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'description', __('article body'), array('class' => 'largetext'));

include_partial('documents/form_history');
?>
