<?php
use_helper('FilterForm', 'Form');
echo update_on_select_change();

// put focus on the name field on window load
echo javascript_tag('Event.observe(window, \'load\', function(){$(\'inam\').focus();});');

echo __('Name:') . ' ' . input_tag('inam');
//echo __('author') . ' ' . input_tag('auth') ;
echo __('categories') . ' ' . field_value_selector('cat', 'mod_images_categories_list', false, false, true);
echo georef_selector();
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
