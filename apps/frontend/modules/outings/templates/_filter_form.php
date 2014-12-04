<?php
use_helper('FilterForm', 'General', 'MyForm');

$is_connected = $sf_user->isConnected();

echo around_selector('sarnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => true));
?>
<br />
<br />
<?php
echo '<div class="fieldname">' . picto_tag('picto_outings') . __('name') . ' </div>' . input_tag('onam', null, array('autofocus' => 'autofocus'));
echo georef_selector('With GPS track:');
$activities_raw = $sf_data->getRaw('activities');
include_partial('routes_filter', array('activities' => $activities_raw));
include_partial('summits/summits_short_filter');
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
echo __('outing_with_public_transportation') . ' ' . bool_selector('owtp');
?>
<br /><br />
<?php
echo __('Date') . __('&nbsp;:') . ' ' . date_selector(array('month' => true, 'year' => true, 'day' => true));
?>
<div data-act-filter="1 2 5 7" style="display:none">
<?php
echo __('avalanche_infos') . ' ' . select_tag('avdate', options_for_select(array('2-3-4-5' => __('yes'), '1' => __('no'), ' ' => __('filled in'), '-' => __('nonwell informed')),
                                                                   '', array('include_blank' => true)));
?>
</div>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('ocult');
if ($is_connected)
{
    echo label_for('myoutings', __('Search in my outings')) . ' ' . checkbox_tag('myoutings', 1, false);
}
?>
<br />
<?php
include_partial('documents/filter_sort', array('orderby_default' => 'date', 'order_default' => 'desc'));

echo label_for('cond', __('Show conditions'), array('title' => __('show conditions of the outings'))) . ' ' . checkbox_tag('cond', 1, false) . ' &nbsp; nbsp; ';
echo label_for('format', __('Comments')) . ' ' . checkbox_tag('format', 'full', false);
?>
<br /><br />
<?php
