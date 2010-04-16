<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'pnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
include_partial('parkings_filter');
?>
<br />
<?php
echo georef_selector();
?>
<br />
<?php
include_partial('documents/filter_sort');
