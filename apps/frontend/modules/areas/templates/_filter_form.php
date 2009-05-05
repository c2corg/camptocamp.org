<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'anam\';');

echo '<div class="picto picto_areas"></div>';
echo __('region_name') . ' ' . input_tag('anam');
echo __('area_type') . ' ' . field_value_selector('atyp', 'mod_areas_area_types_list');
include_partial('documents/filter_sort');
