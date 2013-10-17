<?php
use_helper('JavascriptQueue');

echo javascript_queue("$('#image_type').change(function() {
  var i = $(this).val();
  $('#license_collab').toggle(i == 2);
  $('#license_perso').toggle(i == 1);
  $('#license_copyright').toggle(i == 3);
});");
?>
<div id="license_collab" style="display:<?php echo ($license == 'by-sa') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-sa')); ?></div>
<div id="license_perso" style="display:<?php echo ($license == 'by-nc-nd') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-nc-nd')); ?></div>
<div id="license_copyright" style="display:<?php echo ($license == 'copyright') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'copyright')); ?></div>
