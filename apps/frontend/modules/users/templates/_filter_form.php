<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'unam\';');

echo __('User:') . ' ' . input_tag('unam');
echo __('category') . ' ' . field_value_selector('cat', 'mod_users_category_list', false, false, true);
echo georef_selector();
?>
<br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
