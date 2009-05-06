<?php
use_helper('FilterForm', 'General');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'anam\';');

echo picto_tag('picto_areas') . __('region_name') . ' ' . input_tag('anam');
echo __('area_type') . ' ' . field_value_selector('atyp', 'mod_areas_area_types_list');
include_partial('documents/filter_sort');
