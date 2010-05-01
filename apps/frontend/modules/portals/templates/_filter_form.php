<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'wnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));

echo '<br />' . __('activities') . ' ' . activities_selector(false, true);

echo '<br />' . georef_selector();
?>
<br /><br />
<?php
include_partial('documents/filter_sort');
