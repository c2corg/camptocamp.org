<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

display_document_edit_hidden_tags($document, array('v4_id', 'v4_app'));
echo mandatory_fields_warning();
?>

<h3><?php echo __('Information') ?></h3>

<?php 
echo object_group_tag($document, 'author', null, '', array('class' => 'long_input'));
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input'));
echo object_datetime_tag($document, 'date_time');
echo object_group_dropdown_tag($document, 'activities', 'app_activities_list', array('multiple' => true));
echo object_group_dropdown_tag($document, 'categories', 'mod_images_categories_list', array('multiple' => true));
?>

<h3><?php echo __('Description') ?></h3>

<?php
include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));

echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext'));

include_partial('documents/form_history');
?>
