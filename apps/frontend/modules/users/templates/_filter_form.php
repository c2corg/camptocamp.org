<?php
use_helper('FilterForm');

echo __('User:') . ' ' . input_tag('unam') . ' ';
echo georef_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
