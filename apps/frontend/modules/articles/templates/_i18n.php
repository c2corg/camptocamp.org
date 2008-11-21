<?php use_helper('sfBBCode', 'SmartFormat'); ?>

<p class="abstract"><?php echo $document->get('abstract') ?></p>

<?php
if ($document->get('description'))
{
    echo parse_links(parse_bbcode($document->get('description')));
}
