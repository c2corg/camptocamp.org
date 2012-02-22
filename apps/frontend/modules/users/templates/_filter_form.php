<?php
use_helper('FilterForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'unam\').focus(); })};');
}

echo __('User:') . ' ' . input_tag('unam', null, array('autofocus' => 'autofocus'));
echo select_tag('nam_type',
                options_for_select(array('unam'=>__('topoguide name only'), 'ufnam'=>__('forum name only'), 'utfnam'=>__('forum and topoguide names')), 'unam'),
                array('onchange'=>'$(\'unam\').name = this.value'));
?>
<br />
<?php
echo __('category') . ' ' . field_value_selector('ucat', 'mod_users_category_list', false, false, true);
echo georef_selector();
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, true, $activities_raw);
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
include_partial('documents/filter_sort');
