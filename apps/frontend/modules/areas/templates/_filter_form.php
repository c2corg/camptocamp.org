<?php
use_helper('FilterForm', 'General');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('document.observe(\'dom:loaded\', function() {
   if (!("autofocus" in document.createElement("input"))) { $(\'anam\').focus(); }});');
}

echo picto_tag('picto_areas') . __('region_name') . ' ' . input_tag('anam', null, array('autofocus' => 'autofocus'));
echo __('area_type') . ' ' . field_value_selector('atyp', 'mod_areas_area_types_list');
?>
<br />
<?php echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('acult'); ?>
<br />
<?php
include_partial('documents/filter_sort');
