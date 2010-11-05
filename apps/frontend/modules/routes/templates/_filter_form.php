<?php
use_helper('FilterForm');

echo javascript_tag('focus_field = \'rnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
include_partial('summits/summits_short_filter');
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
include_partial('routes_filter', array('activities' => $activities));
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('rcult');
?>
<br />
<?php
include_partial('documents/filter_sort');
