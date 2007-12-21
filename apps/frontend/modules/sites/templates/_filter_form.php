<?php
use_helper('FilterForm');
echo update_on_select_change();

echo __('Name:') . ' ' . input_tag('snam') . ' ';
echo __('elevation') . ' ' . elevation_selector('salt');
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
