<?php use_helper('sfBBCode', 'SmartFormat');

echo parse_links(parse_bbcode_abstract($document->get('abstract')));

if ($document->get('description'))
{
    echo parse_links(parse_bbcode($document->get('description')));
}
