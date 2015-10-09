<?php
use_helper('FilterForm', 'Form', 'General', 'MyForm');

echo around_selector('xarnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));

include_partial('xreports_filter', array('autofocus' => true));
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('xcult');
echo georef_selector();
?>
<br />
<?php
include_partial('documents/filter_sort');
