<?php
use_helper('FilterForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'snam\').focus(); })};');
}

include_partial('summits_filter', array('autofocus' => true));
echo georef_selector();
?>
<br /><br />
<?php
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
echo around_selector('sarnd');
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo  __('linked routes activities') . ' ' . activities_selector(false, false, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('scult');
?>
<br />
<?php
include_partial('documents/filter_sort');
