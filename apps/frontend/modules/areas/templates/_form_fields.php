<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'Field');

// Here document = route
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

if ($sf_user->hasCredential('moderator')):
    echo object_group_dropdown_tag($document, 'area_type', 'mod_areas_area_types_list');
else:
?>
<ul class="data">
  <?php li(field_data_from_list($document, 'area_type', 'mod_areas_area_types_list')) ?>
</ul>
<?php 
    echo object_input_hidden_tag($document, 'getArea_type');
endif;

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'largetext'));

include_partial('documents/form_history');
