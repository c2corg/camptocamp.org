<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'fnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));

include_partial('products_filter');
echo '<br />' . georef_selector();
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br /><br />
<?php
include_partial('documents/filter_sort');
