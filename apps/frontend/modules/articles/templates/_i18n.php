<?php use_helper('sfBBCode', 'SmartFormat');

echo ($needs_translation) ? '<div class="translatable"><div></div><div>' : '';//fake divs needed
echo '<p class="abstract">', parse_links(parse_bbcode_abstract($document->get('abstract'))), '</p>';

if ($document->get('description'))
{
    echo parse_links(parse_bbcode($document->get('description')));
}
echo ($needs_translation) ? '</div></div>' : '';
