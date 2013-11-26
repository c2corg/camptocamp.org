<?php
use_helper('sfBBCode', 'SmartFormat', 'Field');

$conditions = $document->get('conditions');
$conditions_levels = $document->get('conditions_levels');
$weather = $document->get('weather');
$timing = $document->get('timing');
$access_comments = $document->get('access_comments');
$hut_comments = $document->get('hut_comments');
$has_conditions = !empty($conditions);
$has_weather_or_timing = (!empty($weather) || !empty($timing));
$has_access_or_hut = (!empty($access_comments) || !empty($hut_comments));
$activities = $document->getRaw('activities');

// hide condition levels if ski, snow, ice_climbing or snowshoeing are not among outing activities
if (!array_intersect(array(1,2,5,7), $activities))
{
    $conditions_levels = NULL;
}
$has_conditions_levels = (!empty($conditions_levels) && count($conditions_levels));

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
        $other_conditions = '<p class="tips">' . link_to($link_text, $url) . "</p>\n";
    }
}

if ($has_conditions || $has_conditions_levels)
{
    $condition_name = 'conditions';
    if ($has_conditions && (array_intersect($activities, array(3,4,5)) || (in_array(2, $activities) && !array_intersect($activities, array(1,6,7)))))
    {
        $condition_name = 'conditions_and_equipment';
    }
    $lang = $needs_translation ? ' lang="' . $needs_translation . '"' : '';
    $conditions_title = content_tag('div', __($condition_name), array('class' => 'section_subtitle htext',
            'id' => '_'.$condition_name, 'data-tooltip' => ''))
                        . '<div class="field_value"' . $lang . '>';
    
    if ($has_conditions_levels)
    {
        $conditions_levels_string = conditions_levels_data($conditions_levels);
    }
    
    $conditions_string = '';
    if ($has_conditions)
    {
        $conditions_string = parse_links(parse_bbcode($conditions, $images, false));
    }
    
    if ($has_conditions_levels)
    {
        if (!empty($conditions_string))
        {
            $conditions_string = '<div class="col_left col_66">'
                               . $conditions_string
                               . '</div>';
        }
        $conditions_string = $conditions_title
                           . $conditions_levels_string
                           . $conditions_string
                           . '</div>'
                           . $other_conditions;
    }
    else
    {
        $conditions_string = $conditions_title
                           . $conditions_string;
        $conditions_string = '<div class="col_left col_66 hfirst">'
                           . $conditions_string
                           . $other_conditions
                           . '</div></div>';
    }
    
    echo $conditions_string;
}
elseif(!empty($other_conditions))
{
    echo $other_conditions;
}

$col_weather_or_timing = ($has_weather_or_timing && ($has_conditions || (!$has_conditions_levels && !empty($other_conditions) && $has_access_or_hut) || $has_access_or_hut));
if ($has_weather_or_timing)
{
    if ($col_weather_or_timing)
    {
        $class = 'col_right col_33 hfirst';
    }
    else
    {
        $class = 'col_left';
    }
    echo '<div class="' . $class . '">';
    echo field_text_data_if_set($document, 'weather', null, array('needs_translation' => $needs_translation, 'show_images' => false));
    echo field_text_data_if_set($document, 'timing', null, array('needs_translation' => $needs_translation, 'show_images' => false));
    echo '</div>';
}

if ($has_access_or_hut)
{
    echo '<div class="col_left col_66">';
    echo field_text_data_if_set($document, 'access_comments', null, array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
    echo field_text_data_if_set($document, 'hut_comments', null, array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
    echo '</div>';
}
echo '<div class="clearer"></div>';
echo field_text_data_if_set($document, 'description', 'comments', array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
