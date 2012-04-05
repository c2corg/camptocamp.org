<?php 
$document = isset($document) ? $document : null;

if ($sf_user->isConnected() && $document && !$document->get('geom_wkt') && !c2cTools::mobileVersion())
{
    if (!isset($message))
    {
        $message = 'No geom info, please edit this document to add some';
    }
    ?>
    <p class="default_text"><?php echo __($message);?></p>
    <?php
}
