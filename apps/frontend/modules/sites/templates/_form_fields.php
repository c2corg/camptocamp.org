<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'DateForm');

// Here document = site
echo '<div>';
display_document_edit_hidden_tags($document, array('v4_id', 'v4_type'));
echo '</div>';
echo mandatory_fields_warning(array(('site form warning')));

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', array('class' => 'long_input'));

echo form_section_title('Information', 'form_info', 'preview_info');

include_partial('documents/oam_coords', array('document' => $document));
?>
<div class="article_gauche_5050">
<?php
echo object_group_tag($document, 'elevation', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0', 'max' => '8900'));
echo object_group_tag($document, 'routes_quantity', array('class' => 'short_input', 'type' => 'number', 'min' => '1'));
echo object_group_dropdown_tag($document, 'max_rating', 'app_routes_rock_free_ratings');
echo object_group_dropdown_tag($document, 'min_rating', 'app_routes_rock_free_ratings');
echo object_group_dropdown_tag($document, 'mean_rating', 'app_routes_rock_free_ratings');
echo object_group_tag($document, 'max_height', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
echo object_group_tag($document, 'min_height', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
echo object_group_tag($document, 'mean_height', array('suffix' => 'meters', 'class' => 'short_input', 'type' => 'number', 'min' => '0'));
echo object_group_dropdown_tag($document, 'equipment_rating', 'app_equipment_ratings_list');
echo object_group_dropdown_tag($document, 'climbing_styles', 'app_climbing_styles_list',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'children_proof', 'mod_sites_children_proof_list');
echo object_group_dropdown_tag($document, 'rain_proof', 'mod_sites_rain_proof_list');
echo object_group_dropdown_tag($document, 'facings', 'mod_sites_facings_list', array('multiple' => true));
?>
</div>
<div class="article_droite_5050">
<?php
echo object_group_dropdown_tag($document, 'site_types', 'app_sites_site_types',
                               array('multiple' => true, 'na' => array(0)));
echo object_group_dropdown_tag($document, 'rock_types', 'app_rock_types_list',
                               array('multiple' => true));
echo object_months_list_tag($document, 'best_periods');
?>
</div>
<div class="clear"></div>
<?php

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext', 'abstract' => true, 'route_line' => true));
echo object_group_bbcode_tag($document, 'remarks');
echo object_group_bbcode_tag($document, 'pedestrian_access');
echo object_group_bbcode_tag($document, 'way_back');
echo object_group_bbcode_tag($document, 'external_resources');
if (isset($associated_books) && count($associated_books))
{
  use_helper('Field');
  echo '<div class="extres_books"><p class="edit-tips">', __('do not duplicate linked books'), '</p>',
       format_book_data($associated_books, 'bt', null, false), '</div>';
}
echo object_group_bbcode_tag($document, 'site_history');

include_partial('documents/form_history');
?>
