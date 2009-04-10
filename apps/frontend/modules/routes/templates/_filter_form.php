<?php
use_helper('FilterForm');

echo javascript_tag('focus_field = \'rnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('summits/summits_short_filter');
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
include_partial('routes_filter');
echo '<br />' . georef_selector() . '<br />';
include_partial('documents/filter_sort');
