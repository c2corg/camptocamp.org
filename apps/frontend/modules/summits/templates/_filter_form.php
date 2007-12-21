<?php
use_helper('FilterForm');

echo update_on_select_change();
include_partial('summits_filter');
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
