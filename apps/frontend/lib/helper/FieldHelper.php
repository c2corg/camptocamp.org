<?php
/**
 * $Id: FieldHelper.php 2455 2007-11-30 11:44:31Z alex $
 */

function loadTooltipsViewRessources()
{
    $static_base_url = sfConfig::get('app_static_url');

    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript($static_base_url . '/static/js/tooltips.js', 'last');
    $response->addJavascript($static_base_url . '/static/js/tooltips_view.js', 'last');
}

loadTooltipsViewRessources();

function field_data($document, $name, $prefix = '', $suffix = '')
{
    return _format_data($name, $document->get($name), $prefix, $suffix);
}

function field_data_if_set($document, $name, $prefix = '', $suffix = '')
{
    $value = $document->get($name);
    if (empty($value))
    {
        return '';
    }

    return _format_data($name, $value, $prefix, $suffix);
}

function field_data_from_list($document, $name, $config, $multiple = false, $raw = false)
{
    return _format_data_from_list($name, $document->getRaw($name), $config, $multiple, $raw);
}

function field_activities_data($document, $raw = false)
{
    $activities = (isset($document['activities'])) ? Document::convertStringToArray($document['activities']) :
                                                     $document->getRaw('activities');
    $html = _activities_data($activities);

    if ($raw)
    {
        return $html;
    }

    return _format_data('activities', $html);
}

function field_activities_data_if_set($document, $raw = false)
{
    $activities = (isset($document['activities'])) ? Document::convertStringToArray($document['activities']) :
                                                     $document->getRaw('activities');
    $html = _activities_data($activities);

    if (empty($html) || ($raw))
    {
        return $html;
    }

    return _format_data('activities', $html);
}

function _activities_data($activities)
{
    $html = '';
    if (!empty($activities))
    {
        $list = sfConfig::get('app_activities_list');
        $static_base_url = sfConfig::get('app_static_url');
        foreach ($activities as $activity)
        {
            if (!isset($list[$activity]))
            {
                continue;
            }
            $activity = $list[$activity];
            $name = __($activity);
            $html .= image_tag($static_base_url . '/static/images/picto/' . $activity . '_mini.png',
                               array('alt' => $name, 'title' => $name));
            $html .= ' ';
        }
    }
    return $html;
}

function field_date_data($document, $name)
{
    return _format_data($name, field_raw_date_data($document, $name));
}

function field_raw_date_data($document, $name)
{
    use_helper('Date');
    return format_date($document->get($name), 'D');
}

function field_bool_data($document, $name)
{
    $value = $document->get($name);
    if (is_null($value))
    {
        return '';
    }
    $value = (bool)$value ? 'yes' : 'no';
    $value = __($value);
    return _format_data($name, $value);
}

function _format_data($name, $value, $prefix = '', $suffix = '')
{
    $text = '<div class="section_subtitle" id="_'. $name .'">' . __($name) . '</div> ';

    if (!empty($prefix) && !empty($value))
    {
        $text .= __($prefix) . ' ';
    }

    $text .= $value;

    if (!empty($suffix) && !empty($value))
    {
        $text .= __($suffix);
    }

    return $text;
}


function field_data_from_list_if_set($document, $name, $config, $multiple = false, $raw = false)
{
    $value = (isset($document[$name])) ? $document[$name] : $document->getRaw($name);
    if (empty($value))
    {
        return '';
    }
    return _format_data_from_list($name, $value, $config, $multiple, $raw);
}

function _format_data_from_list($name, $value, $config, $multiple = false, $raw = false)
{
    $list = sfConfig::get($config);
    if (!empty($value))
    {
        if ($multiple)
        {
            $value = is_array($value) ? $value : Document::convertStringToArray($value);
            foreach ($value as &$item)
            {
                $item = _get_field_value_in_list($list, $item);
            }
    
            $value = implode(', ', $value);
        }
        else
        {
            $value = _get_field_value_in_list($list, $value);
        }
    } else {
        $value = '';
    }

    if ($raw)
    {
        return $value;
    }

    return _format_data($name, $value);
}

function _get_field_value_in_list($list, $key)
{
    if (empty($key) || !is_scalar($key))
    {
        return '';
    }
    return (!empty($list[$key]) ? __($list[$key]) : '');
}

function field_text_data($document, $name, $label = NULL)
{
    return _format_text_data($name, $document->get($name), $label);
}

function field_text_data_if_set($document, $name, $label = NULL)
{
    $value = $document->get($name);
    if (empty($value))
    {
        return '';
    }

    return  _format_text_data($name, $value, $label);
}

function _format_text_data($name, $value, $label = NULL)
{
    use_helper('sfBBCode', 'SmartFormat');

    if (empty($label))
    {
        $label = $name;
    }

    return '<div class="section_subtitle" id="_'. $name .'">' . __($label) . "</div>\n" .
           parse_links(parse_bbcode($value));
}

function field_url_data($document, $name, $prefix = '', $suffix = '', $ifset = false)
{
    $value = $document->get($name);
    if ($value)
    {
        $displayvalue = (strlen($value) > 50) ? substr($value, 0 , 35).' &hellip; '.substr($value, -9) : $value;
        $value = '<a href="' . $value . '">' . $displayvalue . '</a>'; 
    }
    elseif ($ifset)
    {
        return '';
    }

    return  _format_data($name, $value, $prefix, $suffix);
}

function field_url_data_if_set($document, $name, $prefix = '', $suffix = '')
{
    return field_url_data($document, $name, $prefix, $suffix, true);
}

function field_export($module, $id, $lang)
{
    $title = 'download geo data under %1% format';
    return '<span class="section_subtitle" id="geo_export">' . __('Export:') . '</span>'
           . ' ' .
           link_to('GPX', "@export_gpx?module=$module&id=$id&lang=$lang",
                   array('title' => __($title, array('%1%' => 'GPX'))))
           . ' ' .
           link_to('KML', "@export_kml?module=$module&id=$id&lang=$lang",
                   array('title' => __($title, array('%1%' => 'KML'))))
           . ' ' .
           link_to('JSON', "@export_json?module=$module&id=$id&lang=$lang",
                   array('title' => __($title, array('%1%' => 'JSON'))));
}

function field_coord_data_if_set($document, $name) 
{
    $value = $document->get($name);
    if (empty($value))
    {   
        return ''; 
    }

    switch ($name)
    {
        case 'lat':
            $suffix = ($value < 0) ? '°S' : '°N';
            break;

        case 'lon':
            $suffix = ($value < 0) ? '°W' : '°E';
            break;

        default:
            $suffix = '';
    }

    $value = abs($value);
    $deg = floor($value);
    $minTemp = 60 * ($value - $deg);
    $min = floor($minTemp);
    $sec = floor(60 * 100 * ($minTemp - $min)) /100;
    $value = $deg . '° ' . $min . "' " . $sec . '" ' . str_replace('°', '', $suffix);
    return _format_data($name, $value, '', '');
}

function field_exposure_time_if_set($document, $name = 'exposure_time', $prefix = '1/', $suffix = 's')
{
    $value = $document->get($name);
    if (empty($value))
    {
        return '';
    }

    return _format_data($name, round(1/$value), $prefix, $suffix);
}

function field_months_data($document, $name)
{
    use_helper('DateForm');

    $months = $document->getRaw($name);

    $I18n_arr = _get_I18n_date_locales(sfContext::getInstance()->getUser()->getCulture());
    $month_names = $I18n_arr['dateFormatInfo']->getMonthNames();

    if (is_array($months))
    {
        $value = array();
        foreach ($months as $month)
        {
            $month--;
            if (!array_key_exists($month, $month_names))
            {
                continue;
            }
            $value[] = $month_names[$month];
        }
        $value = implode(', ', $value);
    }
    else
    {
        $months--;
        $value = array_key_exists($months, $month_names) ? $month_names[$months] : '';
    }

    return _format_data($name, $value);
}

// This function outputs a string composed of all ratings data available for the given route.
function field_route_ratings_data($document, $show_activities = true, $add_tooltips = false)
{
    $activities = $show_activities ? (isset($document['activities']) ?
        Document::convertStringToArray($document['activities']) : $document->getRaw('activities')) : array();

    return _route_ratings_sum_up(
        _filter_ratings_data($document, 'global_rating', 'app_routes_global_ratings', $add_tooltips),
        _filter_ratings_data($document, 'engagement_rating', 'app_routes_engagement_ratings', $add_tooltips),
        _filter_ratings_data($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings', $add_tooltips),
        _filter_ratings_data($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings', $add_tooltips),
        _filter_ratings_data($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings', $add_tooltips),
        _filter_ratings_data($document, 'labande_global_rating', 'app_routes_global_ratings', $add_tooltips),
        _filter_ratings_data($document, 'rock_free_rating', 'app_routes_rock_free_ratings', $add_tooltips),
        _filter_ratings_data($document, 'ice_rating', 'app_routes_ice_ratings', $add_tooltips),
        _filter_ratings_data($document, 'mixed_rating', 'app_routes_mixed_ratings', $add_tooltips),
        _filter_ratings_data($document, 'aid_rating', 'app_routes_aid_ratings', $add_tooltips),
        _filter_ratings_data($document, 'hiking_rating', 'app_routes_hiking_ratings', $add_tooltips),
        $activities
        );
}

function _filter_ratings_data($document, $name, $config, $add_tooltips = false)
{
    $value = !empty($document[$name]) ? $document[$name] : $document->get($name, 'ESC_RAW');
    $value = _get_field_value_in_list(sfConfig::get($config), $value);

    if (empty($value))
    {
        return null;
    }
    return ($add_tooltips) ? '<span title="'.__($name).' '.$value.'">'.$value.'</span>' : $value;

    return !empty($value) ? $value : NULL;
}

function _route_ratings_sum_up($global, $engagement, $topo_ski, $topo_exp, $labande_ski, $labande_global,
                               $rock, $ice, $mixed, $aid, $hiking, $activities = array())
{
    $groups = $ski1 = $ski2 = $climbing = array();

    if ($topo_ski) $ski1[] = $topo_ski;
    if ($topo_exp) $ski1[] = $topo_exp;
    if ($labande_global) $ski2[] = $labande_global;
    if ($labande_ski) $ski2[] = $labande_ski;
    if ($global) $climbing[] = $global;
    if ($engagement) $climbing[] = $engagement;
    if ($rock) $climbing[] = $rock;
    if ($ice) $climbing[] = $ice;
    if ($mixed) $climbing[] = $mixed;
    if ($aid) $climbing[] = $aid;

    $groups[] = _activities_data(array_intersect(array(1), $activities));
    $groups[] = implode('/', $ski1);
    $groups[] = implode('/', $ski2);
    $groups[] = _activities_data(array_intersect(array(2,3,4,5), $activities));
    $groups[] = implode('/', $climbing);
    $groups[] = _activities_data(array_intersect(array(6), $activities));
    $groups[] = $hiking;
    return implode(' ', $groups);
}

function li($string)
{
    if (!empty($string))
    {
        echo "<li>$string</li>\n";
    }
}

function disp_doc_type($type)
{
    li(_format_data('document_type', __($type)));
}

function conditions_levels_data($conditions_levels)
{
    $level_fields = sfConfig::get('mod_outings_conditions_levels_fields');
    
    echo '<table id="conditions_levels_table">';
    
    foreach ($level_fields as $field)
    {
        echo '<colgroup id="' . $field . '"></colgroup>';
    }
    
    echo '<thead><tr>';
    foreach ($level_fields as $field)
    {
        echo '<th>' . __($field) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($conditions_levels as $level => $data)
    {
        echo '<tr>';
        foreach ($level_fields as $field)
        {
            echo '<td>' . $data[$field] . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function simple_data($name, $value, $suffix = '')
{
    if (!check_not_empty($value)) return '';

    if (!empty($suffix)) $suffix = ' ' . __($suffix);

    return '<em>' . __($name) . '</em> ' . $value . $suffix;
}

function check_not_empty($value)
{
    return (!$value instanceof Doctrine_Null && !empty($value));
}

function summarize_route($route, $show_activities = true, $add_tooltips = false)
{
    $height_diff_up = is_scalar($route['height_diff_up']) ? ($route['height_diff_up'] . __('meters')) : NULL;
    if (($height_diff_up != NULL) && is_scalar($route['difficulties_height']))
    {
        $height_diff_up .= ' (' . $route['difficulties_height'] . __('meters') . ')';
    }
    $facing = field_data_from_list_if_set($route, 'facing', 'app_routes_facings', false, true);

    if ($add_tooltips)
    {
        $height_diff_up = '<span title="' . __('height_diff_up') . ' ' . $height_diff_up . '">' . $height_diff_up . '</span>';
        $facing = '<span title="' . __('facing') . ' ' . $facing . '">' . $facing . '</span>';
    }

    $route_data = array($height_diff_up,
                        $facing,
                        field_route_ratings_data($route, $show_activities, $add_tooltips)
                        );

    foreach ($route_data as $key => $value)
    {
        $value = trim($value);
        if (empty($value)) unset($route_data[$key]);
    }

    if (empty($route_data))
    {
        $route_data = '';
    }
    else
    {
        array_unshift($route_data, '');
        $route_data = implode(' - ', $route_data);
    }

    return $route_data;
}
