<?php
use_helper('FilterForm');

echo update_on_select_change();
include_partial('areas/areas_selector', array('ranges' => $ranges));

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'rnam\').focus();});');
?>
<br />
<?php
include_partial('summits_filter');
include_partial('parkings/parkings_filter');
include_partial('routes_filter');
echo '<br />' . georef_selector();
include_partial('documents/filter_sort');
