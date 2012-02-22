<?php
use_helper('FilterForm');

if (!c2cTools::mobileVersion())
{
   // put focus on the name field on dom load
   echo javascript_tag('if (!("autofocus" in document.createElement("input"))) {
   document.observe(\'dom:loaded\', function() { $(\'fnam\').focus(); })};');
}

include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => true));

include_partial('products_filter', array('autofocus' => true));
echo '<br />' . georef_selector();
?>
<br />
<?php
// FIXME : dont show tpty select due to Doctrine bug - see ticket #687
include_partial('parkings/parkings_filter', array('show_tpty' => false)) ?>
<br />
<?php echo __('filter language') . __('&nbsp;:') . ' ' . lang_selector('fcult') ?>
<br /><br />
<?php
include_partial('documents/filter_sort');
