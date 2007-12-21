<?php
use_helper('FilterForm', 'Form');
echo update_on_select_change();

echo __('Name:') . ' ' . input_tag('inam') . ' ';
//echo __('author') . ' ' . input_tag('auth') . ' ';
echo __('categories') . ' ' . field_value_selector('cat', 'mod_images_categories_list', true);
?>
<br />
<?php
echo __('Date:') . ' ' . date_selector();
?>
<br />
<?php
echo  __('activities') . ' ' . activities_selector();
include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('documents/filter_sort');
