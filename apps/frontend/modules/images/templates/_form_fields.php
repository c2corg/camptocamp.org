<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

$creator = $document->getCreator();
$image_type = $document->get('image_type');
$moderator = $sf_user->hasCredential('moderator');
// do not allow to modify the license:
// * only moderators have all right
// * the creator can switch from personal to collaborative
// * other users cannot
$hide_image_type_edit = (!$moderator && $image_type == 1)
                     || (!$moderator && $sf_user->getId() != $creator['id']);
$hidden_fields = array('v4_id', 'v4_app');
if ($hide_image_type_edit)
{
    array_push($hidden_fields, 'image_type');
}
echo '<div>';
display_document_edit_hidden_tags($document, $hidden_fields);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'author', null, '', array('class' => 'long_input'));
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input'));
echo object_datetime_tag($document, 'date_time');
if (!$hide_image_type_edit)
{
    echo object_group_dropdown_tag($document, 'image_type', 'mod_images_type_list');
}
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list', array('multiple' => true),
                               false, null, '', '', 'picto_act act_');
echo object_group_dropdown_tag($document, 'categories', 'mod_images_categories_list', array('multiple' => true));

echo form_section_title('Description', 'form_desc', 'preview_desc');
if ($image_type == 1)
{
    echo file_upload_tag('image_new_version', false, 'file', true);
}
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext', 'abstract' => true));

include_partial('documents/form_history');
