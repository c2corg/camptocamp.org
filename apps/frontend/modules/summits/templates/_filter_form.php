<?php
use_helper('FilterForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'snam\').focus(); })};');
}

echo around_selector('sarnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
?>
<br />
<br />
<?php
include_partial('summits_filter', array('autofocus' => true));
echo georef_selector();
?>
<br /><br />
<?php
$activities_raw = $sf_data->getRaw('activities');
$paragliding_tag = sfConfig::get('app_tags_paragliding');
$paragliding_tag = implode('/', $paragliding_tag);
echo  __('linked routes activities') . ' ' . activities_selector(false, false, $activities_raw, array(8 => $paragliding_tag));
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('scult');
?>
<br />
<?php
include_partial('documents/filter_sort');
