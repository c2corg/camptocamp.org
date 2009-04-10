<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'pnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('parkings_filter');
echo georef_selector();
include_partial('documents/filter_sort');
