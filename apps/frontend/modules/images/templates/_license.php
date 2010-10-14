<?php
use_helper('Javascript');

echo javascript_tag("if ($('image_type') != null) {
Event.observe($('image_type'), 'change', function() {
    if ($('image_type').value == 2) {
        $('license_collab').style.display = 'none';
        $('license_perso').style.display = 'block';
        $('license_copyright').style.display = 'none';
    } else if ($('image_type').value == 1) {
        $('license_collab').style.display = 'block';
        $('license_perso').style.display = 'none';
        $('license_copyright').style.display = 'none';
    } else {
        $('license_collab').style.display = 'none';
        $('license_perso').style.display = 'none';
        $('license_copyright').style.display = 'block';
    }
});}");
?>
<div id="license_collab" style="display:<?php echo ($license == 'by-sa') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-sa')); ?></div>
<div id="license_perso" style="display:<?php echo ($license == 'by-nc-nd') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-nc-nd')); ?></div>
<div id="license_copyright" style="display:<?php echo ($license == 'copyright') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'copyright')); ?></div>
