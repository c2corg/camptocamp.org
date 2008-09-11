<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'unam\').focus();});');

echo __('User:') . ' ' . input_tag('unam') . ' ';
echo georef_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
