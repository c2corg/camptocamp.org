<?php use_helper('sfBBCode', 'SmartFormat'); ?>

<p class="abstract"><?php echo parse_links(parse_bbcode($document->get('abstract'))) ?></p>

<?php if ($document->get('description')): ?>
<p><?php echo parse_links(parse_bbcode($document->get('description'))) ?></p>
<?php endif ?>
