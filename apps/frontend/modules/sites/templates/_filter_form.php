<?php
use_helper('FilterForm', 'General');

echo around_selector('tarnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
?>
<br />
<br />
<?php
echo picto_tag('picto_sites') . __('Name:') . ' ' . input_tag('tnam', null, array('autofocus' => 'autofocus'));
echo __('elevation') . ' ' . elevation_selector('talt');
?>
<br />
<?php
echo __('site_types') . ' ' . field_value_selector('ttyp', 'app_sites_site_types', array('keepfirst' => false, 'multiple' => true));
echo __('climbing_styles') . ' ' . field_value_selector('tcsty', 'app_climbing_styles_list', array('keepfirst' => false, 'multiple' => true));
?>
<br />
<?php
echo __('facings') . ' ' . field_value_selector('tfac', 'mod_sites_facings_list', array('keepfirst' => false, 'multiple' => true, 'size' => 5));
echo __('rock_types') . ' ' . field_value_selector('trock', 'app_rock_types_list', array('keepfirst' => false, 'multiple' => true, 'size' => 5));
?>
<br />
<?php
echo __('equipment_rating') . ' ' . range_selector('tprat', 'app_equipment_ratings_list', null, true);
?>
<br />
<?php
echo __('routes_quantity') . ' ' . elevation_selector('rqua', '');
?>
<br />
<?php
echo __('mean_height') . ' ' . elevation_selector('tmhei');
echo __('mean_rating') . ' ' . range_selector('tmrat', 'app_routes_rock_free_ratings', null, true);
?>
<br />
<?php
echo __('children_proof') . ' ' . field_value_selector('chil', 'mod_sites_children_proof_list', array('keepfirst' => false, 'multiple' => true));
echo __('rain_proof') . ' ' . field_value_selector('rain', 'mod_sites_rain_proof_list', array('keepfirst' => false, 'multiple' => true));
?>
<br />
<?php
echo georef_selector();
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('tcult');
?>
<br />
<?php
include_partial('documents/filter_sort');
