<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'hnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));

include_partial('huts_filter');
echo '<br />' . georef_selector();
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br /><br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('documents/filter_sort');
