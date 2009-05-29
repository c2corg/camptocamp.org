<?php
use_helper('sfBBCode', 'SmartFormat', 'Field');

$conditions = $document->get('conditions');
$conditions_levels = $document->getRaw('conditions_levels');

// hide condition levels if ski, snow or ice_climbing are not among outing activities
if (!array_intersect(array(1,2,5), $document->getRaw('activities')))
{
    $conditions_levels = NULL;
}

if (!empty($conditions) || !empty($conditions_levels))
{
    if ($needs_translation) echo '<div class="translatable">';
    echo '<div class="section_subtitle field_text" id="_conditions">' . __('conditions') . '</div><div>';
    $conditions_levels = $document->get('conditions_levels');
    if (!empty($conditions_levels) && count($conditions_levels))
    {
        conditions_levels_data($conditions_levels);
    }
    echo parse_links(parse_bbcode($conditions)).'</div>';
    if ($needs_translation) echo '</div>';
}

echo field_text_data_if_set($document, 'weather', null, $needs_translation);
echo field_text_data_if_set($document, 'participants', null, $needs_translation);
echo field_text_data_if_set($document, 'timing', null, $needs_translation);
echo field_text_data_if_set($document, 'description', 'comments', null, $needs_translation);
echo field_text_data_if_set($document, 'access_comments', null, $needs_translation);
echo field_text_data_if_set($document, 'hut_comments', null, $needs_translation);
