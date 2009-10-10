<?php
use_helper('FilterForm', 'General');

// put focus on the name filed on window load
echo javascript_tag('focus_field = \'snam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));
?>
<br />
<?php
echo picto_tag('picto_sites') . __('Name:') . ' ' . input_tag('tnam');
echo __('elevation') . ' ' . elevation_selector('talt');
?>
<br />
<?php
echo __('site_types') . ' ' . field_value_selector('ttyp', 'app_sites_site_types', false, false, true);
echo __('climbing_styles') . ' ' . field_value_selector('tcsty', 'app_climbing_styles_list', false, false, true);
?>
<br />
<?php
echo __('facings') . ' ' . field_value_selector('tfac', 'mod_sites_facings_list', false, false, true, 5);
echo __('rock_types') . ' ' . field_value_selector('trock', 'app_rock_types_list', false, false, true, 5);
?>
<br />
<?php
echo __('equipment_rating') . ' ' . range_selector('prat', 'app_equipment_ratings_list', null, true);
?>
<br />
<?php
echo __('routes_quantity') . ' ' . elevation_selector('rqua', '');
?>
<br />
<?php
echo __('mean_height') . ' ' . elevation_selector('mhei');
echo __('mean_rating') . ' ' . range_selector('mrat', 'app_routes_rock_free_ratings', null, true);
?>
<br />
<?php
echo __('children_proof') . ' ' . field_value_selector('chil', 'mod_sites_children_proof_list', false, false, true);
echo __('rain_proof') . ' ' . field_value_selector('rain', 'mod_sites_rain_proof_list', false, false, true);
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
include_partial('documents/filter_sort');
