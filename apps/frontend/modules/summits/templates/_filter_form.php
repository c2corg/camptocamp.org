<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'snam\';');

include_partial('summits_filter');
echo georef_selector();
?>
<br /><br />
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo  __('linked routes activities') . ' ' . activities_selector(false, false, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('scult');
?>
<br />
<?php
include_partial('documents/filter_sort');
