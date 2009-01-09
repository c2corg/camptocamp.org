<?php
use_helper('Object', 'Language', 'Validation', 'MyForm', 'DateForm');

// Here document = site
display_document_edit_hidden_tags($document, array('v4_id', 'v4_type'));
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));
echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));
?>

<h3><?php echo __('Information') ?></h3>

<?php
include_partial('documents/oam_coords', array('document' => $document));
echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'routes_quantity', null, '', array('class' => 'short_input'));
echo object_group_dropdown_tag($document, 'max_rating', 'mod_sites_rock_free_ratings_list');
echo object_group_dropdown_tag($document, 'min_rating', 'mod_sites_rock_free_ratings_list');
echo object_group_dropdown_tag($document, 'mean_rating', 'mod_sites_rock_free_ratings_list');
echo object_group_tag($document, 'max_height', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'min_height', null, 'meters', array('class' => 'short_input'));
echo object_group_tag($document, 'mean_height', null, 'meters', array('class' => 'short_input'));
echo object_group_dropdown_tag($document, 'equipment_rating', 'app_equipment_ratings_list');
echo object_group_dropdown_tag($document, 'climbing_styles', 'mod_sites_climbing_styles_list',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'rock_types', 'mod_sites_rock_types_list',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'site_types', 'app_sites_site_types',
                               array('multiple' => true));
echo object_group_dropdown_tag($document, 'children_proof', 'mod_sites_children_proof_list');
echo object_group_dropdown_tag($document, 'rain_proof', 'mod_sites_rain_proof_list');
echo object_group_dropdown_tag($document, 'facings', 'mod_sites_facings_list', array('multiple' => true));
echo object_months_list_tag($document, 'best_periods');
?>

<h3><?php echo __('Description') ?></h3>

<?php
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'mediumtext'));
echo object_group_bbcode_tag($document, 'remarks');
echo object_group_bbcode_tag($document, 'pedestrian_access');
echo object_group_bbcode_tag($document, 'way_back');

include_partial('documents/form_history');
?>
