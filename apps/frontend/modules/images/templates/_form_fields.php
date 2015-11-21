<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

$image_type = $document->get('image_type');
$hidden_fields = array('v4_id', 'v4_app');

echo '<div>';
display_document_edit_hidden_tags($document, $hidden_fields);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

echo object_group_tag($document, 'author', array('class' => 'long_input'));
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_tag($document, 'elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_datetime_tag($document, 'date_time');

include_component('images', 'form_fields_image_type', array('document' => $document, 'moderator' => $sf_user->hasCredential('moderator')));

echo object_group_dropdown_tag($document, 'activities', 'app_activities_list', array('multiple' => true),
                               false, null, null, '', '', 'picto_act act_');
echo object_group_dropdown_tag($document, 'categories', 'mod_images_categories_list', array('multiple' => true),
                               false, null, 'image_categories');

echo form_section_title('Description', 'form_desc', 'preview_desc');
echo file_upload_tag('image_new_version');
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext', 'abstract' => true));

include_partial('documents/form_history');
