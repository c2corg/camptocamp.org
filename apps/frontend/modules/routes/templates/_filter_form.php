<?php
use_helper('FilterForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'rnam\').focus(); })};');
}

echo around_selector('rarnd');
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
include_partial('summits/summits_short_filter');
$activities_raw = $sf_data->getRaw('activities');
include_partial('routes_filter', array('autofocus' => true, 'activities' => $activities_raw));
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('rcult');
?>
<br />
<?php
include_partial('documents/filter_sort');
