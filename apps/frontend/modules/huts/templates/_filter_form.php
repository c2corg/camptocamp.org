<?php
use_helper('FilterForm');
echo update_on_select_change();

echo __('Name:') . ' ' . input_tag('hnam') . ' ';
echo __('shelter_type') . ' ' . field_value_selector('styp', 'mod_huts_shelter_types_list');
?>
<br />
<?php
echo __('elevation') . ' ' . elevation_selector('halt') . ' ';
echo georef_selector();
?>
<br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
