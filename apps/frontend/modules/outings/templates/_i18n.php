<?php
use_helper('sfBBCode', 'SmartFormat', 'Field');

$conditions = $document->get('conditions');
$conditions_levels = $document->getRaw('conditions_levels');

// hide condition levels if ski, snow or ice_climbing are not among outing activities
if (!array_intersect(array(1,2,5), $document->getRaw('activities')))
{
    $conditions_levels = NULL;
}

$other_conditions = '';
if (!empty($associated_areas))
{
    $area_type_list = sfConfig::get('app_areas_area_types');
    unset($area_type_list[0]);
    $area_type = '';
    foreach ($area_type_list as $key => $area_type_temp)
    {
        $area_ids = array();
        foreach ($associated_areas as $area)
        {
            if ($area['area_type'] != $key)
            {
                continue;
            }
            $area_ids[] = $area['id'];
        }
        if (!empty($area_ids))
        {
            $area_type = $area_type_temp;
            break;
        }
    }
    
    if (!empty($area_ids))
    {
        use_helper('Date');
        $link_text = __('The other conditions the same day in the same ' . $area_type);
        $date = format_date($document->get('date'), 'yyyyMMdd');
        $url = "outings/conditions?areas=" . implode('-', $area_ids) . "&date=$date";
        $other_conditions = '<p class="tips no_print">' . link_to($link_text, $url) . "</p>\n";
    }
}

echo '<div class="col_left col_66">';
if (!empty($conditions) || !empty($conditions_levels))
{
    if ($needs_translation) echo '<div class="translatable">';
    echo '<div class="section_subtitle htext" id="_conditions">' . __('conditions') . '</div><div class="field_value">';
    $conditions_levels = $document->get('conditions_levels');
    if (!empty($conditions_levels) && count($conditions_levels))
    {
        conditions_levels_data($conditions_levels);
    }
    echo parse_links(parse_bbcode($conditions, $images, false));
    echo $other_conditions;
    echo '</div>';
    if ($needs_translation) echo '</div>';
}
elseif(!empty($other_conditions))
{
    echo '<div class="section_subtitle field_text no_print" id="_conditions">' . __('conditions') . '</div><div class="field_value">';
    echo $other_conditions;
    echo '</div>';
}
echo '</div>';
echo '<div class="col_right col_33">';
echo field_text_data_if_set($document, 'weather', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'timing', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo '</div>';
echo '<div class="col_left col_66">';
echo field_text_data_if_set($document, 'access_comments', null, array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
echo '</div>';
echo '<div class="col_left col_66">';
echo field_text_data_if_set($document, 'hut_comments', null, array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
echo '</div>';
echo field_text_data_if_set($document, 'description', 'comments', array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
