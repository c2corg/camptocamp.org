<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'wnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));

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
