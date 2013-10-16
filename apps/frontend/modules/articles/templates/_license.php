<?php
use_helper('JavascriptQueue');

echo javascript_queue("$('#article_type').change(function() {
  var i = ($('#article_type').val() == 2);
  $('#license_collab').toggle(!i);
  $('#license_perso').toggle(i);
});");
?>
<div id="license_collab" style="display:<?php echo ($license == 'by-sa') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-sa')); ?></div>
<div id="license_perso" style="display:<?php echo ($license == 'by-sa') ? 'none' : 'block' ?>">
<?php include_partial('documents/license', array('license' => 'by-nc-nd')); ?></div>
