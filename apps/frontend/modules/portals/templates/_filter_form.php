<?php
use_helper('FilterForm');

$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => false));

$activities_raw = $sf_data->getRaw('activities');
echo '<br />' . __('activities') . ' ' . activities_selector(false, false, $activities_raw);

echo '<br />' . georef_selector();
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('wcult');
?>
<br /><br />
<?php
include_partial('documents/filter_sort');
