<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'hnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));

include_partial('huts_filter');
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
