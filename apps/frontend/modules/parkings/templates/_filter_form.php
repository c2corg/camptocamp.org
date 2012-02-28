<?php
use_helper('FilterForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'pnam\').focus(); })};');
}

echo around_selector('parnd');
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));
include_partial('parkings_filter', array('autofocus' => true));
?>
<br />
<?php echo georef_selector(); ?>
<br />
<?php echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('pcult') ?>
<br />
<?php include_partial('documents/filter_sort');
