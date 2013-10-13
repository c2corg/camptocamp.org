<?php
use_helper('JavascriptQueue');

echo javascript_queue("jQuery('#article_type').change(function() {
  var i = (jQuery('#article_type').val() == 2);
  jQuery('#license_collab').toggle(!i);
  jQuery('#license_perso').toggle(i);
});");
?>
<div id="license_collab" style="display:<?php echo ($license == 'by-sa') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-sa')); ?></div>
<div id="license_perso" style="display:<?php echo ($license == 'by-sa') ? 'none' : 'block' ?>">
<?php include_partial('documents/license', array('license' => 'by-nc-nd')); ?></div>
