<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'snam\';');

include_partial('summits_filter');
echo georef_selector();
?>
<br /><br />
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
