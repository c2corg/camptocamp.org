<?php
use_helper('FilterForm');
echo update_on_select_change();

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'hnam\').focus();});');

include_partial('huts_filter');
echo '<br />' . georef_selector();
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br />
<?php
echo __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
