<?php
use_helper('FilterForm');
echo update_on_select_change();

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'snam\').focus();});');

echo __('Name:') . ' ' . input_tag('snam') . ' ';
echo __('elevation') . ' ' . elevation_selector('salt') . ' ';
?>
<br />
<?php
echo __('site_types') . ' ' . field_value_selector('styp', 'app_sites_site_types', false, false, true) . ' ';
echo georef_selector();
?>
<br />
<?php
echo __('equipment_rating') . ' ' . range_selector('prat', 'app_equipment_ratings_list', null, true);
?>
<br />
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
