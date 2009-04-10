<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag(
'field_list = new Array(\'salt\');
optionIndex_list = new Array(3);
focus_field = \'snam\';'
);

include_partial('summits_filter');
echo georef_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
