<?php
use_helper('FilterForm', 'General');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'mnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
?>
<br />
<?php
echo picto_tag('picto_maps') . __('Name:') . ' ' . input_tag('mnam');
echo __('Code:') . ' ' . input_tag('code');
?>
<br />
<?php
echo __('Scale:') . ' ' . field_value_selector('scal', 'mod_maps_scales_list', true);
echo __('Editor:') . ' ' . field_value_selector('edit', 'mod_maps_editors_list', true);
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('mcult');
include_partial('documents/filter_sort');
