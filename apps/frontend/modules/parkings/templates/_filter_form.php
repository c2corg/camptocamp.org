<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'pnam\').focus();});');

echo update_on_select_change();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('parkings_filter');
echo '<br />' . georef_selector();
include_partial('documents/filter_sort');
