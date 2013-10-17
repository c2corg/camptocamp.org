<?php
use_helper('FilterForm', 'AutoComplete');

echo around_selector('harnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
include_partial('huts_filter', array('autofocus' => true));
echo '<br />' . georef_selector();
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br /><br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, true, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('hcult');
?>
<br />
<?php
include_partial('documents/filter_sort');
