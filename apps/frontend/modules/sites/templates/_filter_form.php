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
echo __('site_types') . ' ' . field_value_selector('styp', 'app_sites_site_types') . ' ';
echo georef_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
