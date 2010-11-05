<?php
use_helper('FilterForm');

// put focus on the name field on window load
echo javascript_tag('focus_field = \'wnam\';');

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));

echo '<br />' . __('activities') . ' ' . activities_selector(false, true, $activities);

echo '<br />' . georef_selector();
?>
<br />
<?php
echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('wcult');
?>
<br /><br />
<?php
include_partial('documents/filter_sort');
