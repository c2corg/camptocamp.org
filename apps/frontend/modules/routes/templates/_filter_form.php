<?php
use_helper('FilterForm');

$is_connected = $sf_user->isConnected();

echo around_selector('parnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
?>
<br />
<?php
include_partial('summits/summits_short_filter');
$activities_raw = $sf_data->getRaw('activities');
include_partial('routes_filter', array('autofocus' => true, 'activities' => $activities_raw));
?>
<br />
<fieldset>
<?php
if (!c2cTools::mobileVersion())
{
    echo '<span data-act-filter="1 2 3 4 6 7" style="display:none">'
       , bool_selector_from_list('sub', 'mod_routes_sub_activities_list', 8)
       , '</span>';
    echo '<span data-act-filter="1 3 4 6" style="display:none">'
       , bool_selector_from_list('sub', 'mod_routes_sub_activities_list', 6)
       , '</span>';
}
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
?>
</fieldset>
<br />
<?php
if (!c2cTools::mobileVersion())
{
    echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('rcult');
    if ($is_connected)
    {
        echo __('Search in my routes') . ' ' . field_value_selector('myroutes', 'mod_routes_myroutes_list', array('filled_options' => false));
    }
    ?>
    <br />
    <?php
}
include_partial('documents/filter_sort');
