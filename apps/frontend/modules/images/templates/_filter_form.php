<?php
use_helper('FilterForm', 'Form', 'General', 'MyForm');

$is_connected = $sf_user->isConnected();

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'inam\').focus(); })};');
}
echo around_selector('iarnd');
$ranges_raw = $sf_data->getRaw('ranges');
$selected_areas_raw = $sf_data->getRaw('selected_areas');
include_partial('areas/areas_selector', array('ranges' => $ranges_raw, 'selected_areas' => $selected_areas_raw, 'use_personalization' => false));
?>
<br />
<div class="fieldgroup">
<?php
echo '<div class="fieldname">' . picto_tag('picto_images') . __('name') . ' </div>' . input_tag('inam', null, array('autofocus' => 'autofocus'));
//echo '<br /><br /><div class="fieldname">' . __('author') . ' </div>' . input_tag('auth') ;
echo '<br /><br /><div class="fieldname">' . __('image_type') . ' </div>' . topo_dropdown('ityp', 'mod_images_type_list', true, false, true);
echo '<br /><br />' . georef_selector();
?>
</div>
<?php
echo __('categories') . ' ' . field_value_selector('icat', 'mod_images_categories_list', array('keepfirst' => false, 'multiple' => true, 'size' => 8));
?>
<br />
<?php
echo __('Date:') . ' ' . date_selector(array('month' => true, 'year' => true, 'day' => true));
?>
<br />
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, false, $activities_raw);
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('icult');
if ($is_connected)
{
    echo label_for('myimages', __('Search in my images')) . ' ' . checkbox_tag('myimages', 1, false);
}
?>
<br />
<br />
<?php
include_partial('documents/filter_sort');
