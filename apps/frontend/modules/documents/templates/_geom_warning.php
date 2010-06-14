<?php 
$document = isset($document) ? $document : null;

if ($sf_user->isConnected() && $document && !$document->get('geom_wkt') && !$c2cTools::mobileVersion()): ?>
    <p class="default_text"><?php echo __('No geom info, please edit this document to add some');?></p>
    <?php
endif;
