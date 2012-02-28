<?php
use_helper('FilterForm', 'AutoComplete');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'hnam\').focus(); })};');
}

echo around_selector('harnd');
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
include_partial('huts_filter', array('autofocus' => true));
echo '<br />' . georef_selector();
?>
<br />
<?php
include_partial('parkings/parkings_filter');
?>
<br /><br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, true, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('hcult');
?>
<br />
<?php
include_partial('documents/filter_sort');
