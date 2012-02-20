<?php
use_helper('FilterForm', 'AutoComplete');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load // TODO change it
   echo javascript_tag('document.observe(\'dom:loaded\', function() {
   if (!("autofocus" in document.createElement("input"))) { $(\'hnam\').focus(); }});');
}

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
echo around_selector('harnd');
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
