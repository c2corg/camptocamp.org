<?php
use_helper('sfBBCode', 'SmartFormat', 'Field');

$activities = $document->getRaw('activities');
$outing_route_desc = $document->get('outing_route_desc');
$conditions = $document->get('conditions');
$conditions_levels = $document->get('conditions_levels');
$avalanche_date = $document->getRaw('avalanche_date');
$avalanche_desc = $document->get('avalanche_desc');
$weather = $document->get('weather');
$timing = $document->get('timing');
$access_comments = $document->get('access_comments');
$hut_comments = $document->get('hut_comments');

$has_outing_route_desc = !empty($outing_route_desc);
$has_conditions = !empty($conditions);
$has_weather_or_timing = (!empty($weather) || !empty($timing));
$has_access_or_hut = (!empty($access_comments) || !empty($hut_comments));
$has_avalanche_date = !empty($avalanche_date) && count($avalanche_date) && !array_intersect(array(0, 1), $avalanche_date);
$has_avalanche_desc = $has_avalanche_date && !empty($avalanche_desc);

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

if ($has_outing_route_desc || $has_conditions || $has_conditions_levels || $has_avalanche_date)
{
    $outing_route_desc_string = field_text_data_if_set($document, 'outing_route_desc', null, array('needs_translation' => $needs_translation, 'images' => $images, 'filter_image_type' => false));
    
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
    
    $avalanche_title_level = 0;
    $conditions_sub_title = '';
    $avalanche_string = '';
    if ($has_avalanche_date)
    {
        $avalanche_title_level = 3;
        if ($has_conditions)
        {
            if (preg_match('{^(\s*\n|)(\#{2,6})}s', $conditions, $match))
            {
                $avalanche_title_level = strlen($matches[2]);
            }
            else
            {
                $conditions_sub_title = '<h' . $avalanche_title_level . ' id="conditions2" class="htext"><a href="#conditions2">' . __('cond short') . '</a></h' . $avalanche_title_level . '>';
            }
        }
        
        $avalanche_desc_string = '';
        if ($has_avalanche_desc)
        {
            $avalanche_desc_string .= parse_links(parse_bbcode($avalanche_desc, $images, false));
        }
        
        $avalanche_title_class = 'htext' . ($has_conditions_levels ? '' : ' hfirst');
        $avalanche_title = '<h' . $avalanche_title_level . ' id="avalanche_info" class="' . $avalanche_title_class . '"><a href="#avalanche_info">' . __('avalanche_info_title') . '</a></h' . $avalanche_title_level . '>';
        $avalanche_date_string = '<p>'
                               . c2cTools::multibyte_ucfirst(get_paginated_value_from_list($avalanche_date, 'mod_outings_avalanche_date_list'))
                               . '</p>';

        $avalanche_string = $avalanche_title
                          . $avalanche_date_string
                          . $avalanche_desc_string;
    }
    
    $conditions_string = '';
    if ($has_conditions)
    {
        $conditions_string = $conditions_sub_title
                           . parse_links(parse_bbcode($conditions, $images, false));
    }
    
    if ($has_conditions_levels)
    {
        if ($has_conditions || $has_avalanche_date)
        {
            $conditions_string = '<div class="col_left col_66">'
                               . $avalanche_string
                               . $conditions_string
                               . '</div>';
        }
        $conditions_string = $outing_route_desc_string
                           . $conditions_title
                           . $conditions_levels_string
                           . $conditions_string
                           . '</div>'
                           . $other_conditions;
    }
    else
    {
        $conditions_string = '<div class="col_left col_66 hfirst">'
                           . $outing_route_desc_string
                           . $conditions_title
                           . $avalanche_string
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
