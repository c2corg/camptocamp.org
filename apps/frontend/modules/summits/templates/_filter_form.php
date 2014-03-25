<?php
use_helper('FilterForm');

if (!isset($coords))
{
    $coords = array();
}
else
{
    $coords_raw = $sf_data->getRaw('coords');
    $coords = $coords_raw;
}

echo around_selector('sarnd', $coords);
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
