<?php use_helper('sfBBCode', 'SmartFormat');

echo ($needs_translation) ? '<div class="translatable translatable_no_label">' : '';
echo '<div class="field_value">';
echo '<p class="abstract">', parse_links(parse_bbcode_abstract($document->get('abstract'))), '</p>';

if ($document->get('description'))
{
    echo parse_links(parse_bbcode($document->get('description'), $images, $filter_image_type));
}
echo '</div>';
echo ($needs_translation) ? '</div>' : '';
