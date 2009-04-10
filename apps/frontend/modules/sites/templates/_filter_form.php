<?php
use_helper('FilterForm');

// put focus on the name filed on window load
echo javascript_tag('focus_field = \'snam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));
?>
<br />
<?php
echo __('Name:') . ' ' . input_tag('snam');
echo __('elevation') . ' ' . elevation_selector('salt');
?>
<br />
<?php
echo __('site_types') . ' ' . field_value_selector('styp', 'app_sites_site_types', false, false, true);
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
echo __('mean_rating') . ' ' . range_selector('mrat', 'mod_sites_rock_free_ratings_list', null, true);
?>
<br />
<?php
echo __('children_proof') . ' ' . field_value_selector('chil', 'mod_sites_children_proof_list', false, false, true);
echo __('rain_proof') . ' ' . field_value_selector('rain', 'mod_sites_rain_proof_list', false, false, true);
?>
<br />
<?php
echo __('facings') . ' ' . field_value_selector('fac', 'mod_sites_facings_list', false, false, true, 5);
echo __('rock_types') . ' ' . field_value_selector('rock', 'mod_sites_rock_types_list', false, false, true, 5);
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br />
<?php
echo georef_selector();
?>
<br />
<?php
include_partial('documents/filter_sort');
