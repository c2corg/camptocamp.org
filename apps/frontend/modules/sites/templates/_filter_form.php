<?php
use_helper('FilterForm');
echo update_on_select_change();
include_partial('areas/areas_selector', array('ranges' => $ranges));

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'snam\').focus();});');

echo __('Name:') . ' ' . input_tag('snam');
echo __('elevation') . ' ' . elevation_selector('salt');
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br />
<?php
echo __('site_types') . ' ' . field_value_selector('styp', 'app_sites_site_types', false, false, true);
echo __('equipment_rating') . ' ' . range_selector('prat', 'app_equipment_ratings_list', null, true);
?>
<br />
<?php
echo __('mean_height') . ' ' . elevation_selector('mhei', '');
echo __('mean_rating') . ' ' . range_selector('mrat', 'mod_sites_rock_free_ratings_list', null, true);
?>
<br />
<?php
echo __('facings') . ' ' . field_value_selector('fac', 'mod_sites_facings_list', false, false, true);
echo __('rock_types') . ' ' . field_value_selector('rock', 'mod_sites_rock_types_list', false, false, true);
?>
<br />
<?php
echo __('children_proof') . ' ' . range_selector('chil', 'mod_sites_children_proof_list', null, true);
echo __('rain_proof') . ' ' . range_selector('rain', 'mod_sites_rain_proof_list', null, true);
?>
<br />
<?php
echo georef_selector();
?>
<br />
<?php
include_partial('documents/filter_sort');
