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
    echo '<div class="section_subtitle" id="_conditions">' . __('conditions') . '</div>';
    $conditions_levels = $document->get('conditions_levels');
    if (!empty($conditions_levels) && count($conditions_levels))
    {
        conditions_levels_data($conditions_levels);
    }
    echo parse_links(parse_bbcode($conditions));
}

echo field_text_data_if_set($document, 'weather');
echo field_text_data_if_set($document, 'participants');
echo field_text_data_if_set($document, 'timing');
echo field_text_data_if_set($document, 'description', 'comments');
echo field_text_data_if_set($document, 'access_comments');
echo field_text_data_if_set($document, 'hut_comments');
?>
