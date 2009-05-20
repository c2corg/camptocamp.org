<?php
use_helper('Javascript');

echo javascript_tag("Event.observe($('article_type'), 'change', function() {
    if ($('article_type').value == 2) {
        $('license_collab').style.display = 'none';
        $('license_perso').style.display = 'block';
    } else {
        $('license_collab').style.display = 'block';
        $('license_perso').style.display = 'none';
    }
});");
?>
<div id="license_collab" style="display:<?php echo ($license == 'by-sa') ? 'block' : 'none' ?>">
<?php include_partial('documents/license', array('license' => 'by-sa')); ?></div>
<div id="license_perso" style="display:<?php echo ($license == 'by-sa') ? 'none' : 'block' ?>">
<?php include_partial('documents/license', array('license' => 'by-nc-nd')); ?></div>
