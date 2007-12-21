<?php
use_helper('FilterForm');
echo __('region_name') . ' ' . input_tag('anam') . ' ';
echo __('area_type') . ' ' . field_value_selector('atyp', 'mod_areas_area_types_list');
include_partial('documents/filter_sort');
