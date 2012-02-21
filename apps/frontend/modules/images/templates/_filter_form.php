<?php
use_helper('FilterForm', 'Form', 'General', 'MyForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'inam\').focus(); }});');
}
?>
<div class="fieldgroup">
<?php
echo '<div class="fieldname">' . picto_tag('picto_images') . __('name') . ' </div>' . input_tag('inam', null, array('autofocus' => 'autofocus'));
//echo '<br /><br /><div class="fieldname">' . __('author') . ' </div>' . input_tag('auth') ;
echo '<br /><br /><div class="fieldname">' . __('image_type') . ' </div>' . topo_dropdown('ityp', 'mod_images_type_list', true, false, true);
echo '<br /><br />' . georef_selector();
?>
</div>
<?php
echo __('categories') . ' ' . field_value_selector('icat', 'mod_images_categories_list', false, false, true, 8);
?>
<br />
<?php
echo __('Date:') . ' ' . date_selector(array('month' => true, 'year' => true, 'day' => true));
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('icult');
?>
<br />
<?php
$activities_raw = $sf_data->getRaw('activities');
echo __('activities') . ' ' . activities_selector(false, false, $activities_raw);
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));
echo around_selector('iarnd');
include_partial('documents/filter_sort');
