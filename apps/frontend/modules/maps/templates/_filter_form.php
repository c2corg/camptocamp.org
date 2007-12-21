<?php
use_helper('FilterForm');

echo __('Name:') . ' ' . input_tag('mnam') . ' ';
echo __('Code:') . ' ' . input_tag('code');
?>
<br />
<?php
echo __('Scale:') . ' ' . field_value_selector('scal', 'mod_maps_scales_list', true) . ' ';
echo __('Editor:') . ' ' . field_value_selector('edit', 'mod_maps_editors_list', true);
include_partial('documents/filter_sort');
