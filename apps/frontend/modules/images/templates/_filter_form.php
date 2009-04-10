<?php
use_helper('FilterForm', 'Form');

// put focus on the name field on window load
echo javascript_tag(
'field_list = new Array(\'date\');
optionIndex_list = new Array(3);
focus_field = \'inam\';'
);

echo __('Name:') . ' ' . input_tag('inam');
//echo __('author') . ' ' . input_tag('auth') ;
echo __('categories') . ' ' . field_value_selector('cat', 'mod_images_categories_list', false, false, true, 8);
echo georef_selector();
?>
<br />
<?php
echo __('Date:') . ' ' . date_selector();
?>
<br /><br />
<?php
echo  __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
