<?php
use_helper('FilterForm', 'General');

$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
?>
<br />
<br />
<?php
echo picto_tag('picto_maps') . __('Name:') . ' ' . input_tag('mnam', null, array('autofocus' => 'autofocus'));
echo __('Code:') . ' ' . input_tag('code');
?>
<br />
<?php
echo __('Scale:') . ' ' . field_value_selector('scal', 'mod_maps_scales_list', array('blank' => true));
echo __('Editor:') . ' ' . field_value_selector('edit', 'mod_maps_editors_list', array('blank' => true));
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('mcult');
include_partial('documents/filter_sort');
