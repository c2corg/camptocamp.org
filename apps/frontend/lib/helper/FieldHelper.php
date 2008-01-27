<?php
/**
 * $Id: FieldHelper.php 2455 2007-11-30 11:44:31Z alex $
 */

function loadTooltipsViewRessources()
{
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/static/js/tooltips.js', 'last');
    $response->addJavascript('/static/js/tooltips_view.js', 'last');
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

function field_data_from_list($document, $name, $config, $multiple = false)
{
    return _format_data_from_list($name, $document->getRaw($name), $config, $multiple);
}

function field_activities_data($document, $raw = false)
{
    $list = sfConfig::get('app_activities_list');
    $activities = (isset($document['activities'])) ? Document::convertStringToArray($document['activities']) :
                                                     $document->getRaw('activities');
    $html = '';
    if (!empty($activities))
    {
        foreach ($activities as $activity)
        {
            if (!isset($list[$activity]))
            {
                continue;
            }
            $activity = $list[$activity];
            $name = __($activity);
            $html .= image_tag('/static/images/picto/' . $activity . '_mini.png',
                               array('alt' => $name, 'title' => $name));
            $html .= ' ';
        }
    }

    if ($raw)
    {
        return $html;
    }

    return _format_data('activities', $html);
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
        $text .= ' ' . __($suffix);
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
        $value = '<a href="' . $value . '">' . $value . '</a>'; 
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
function field_route_ratings_data($document)
{
    return _route_ratings_sum_up(
        _filter_ratings_data($document, 'global_rating', 'app_routes_global_ratings'),
        _filter_ratings_data($document, 'engagement_rating', 'app_routes_engagement_ratings'),
        _filter_ratings_data($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings'),
        _filter_ratings_data($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings'),
        _filter_ratings_data($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings'),
        _filter_ratings_data($document, 'labande_global_rating', 'app_routes_global_ratings'),
        _filter_ratings_data($document, 'rock_free_rating', 'app_routes_rock_free_ratings'),
        _filter_ratings_data($document, 'ice_rating', 'app_routes_ice_ratings'),
        _filter_ratings_data($document, 'mixed_rating', 'app_routes_mixed_ratings'),
        _filter_ratings_data($document, 'aid_rating', 'app_routes_aid_ratings'),
        _filter_ratings_data($document, 'hiking_rating', 'app_routes_hiking_ratings')
                                 );
}

function _filter_ratings_data($document, $name, $config)
{
    $value = !empty($document[$name]) ? $document[$name] : $document->get($name, 'ESC_RAW');
    $value = _get_field_value_in_list(sfConfig::get($config), $value);
    return !empty($value) ? $value : NULL;
}

function _route_ratings_sum_up($global, $engagement, $topo_ski, $topo_exp, $labande_ski, $labande_global,
                               $rock, $ice, $mixed, $aid, $hiking)
{
    $groups = $ski1 = $ski2 = $climbing = array();

    if ($topo_ski) $ski1[] = $topo_ski;
    if ($topo_exp) $ski1[] = $topo_exp;
    if ($labande_global) $ski2[] = $labande_global;
    if ($labande_ski) $ski2[] = $labande_ski;
    if ($engagement) $climbing[] = $engagement;
    if ($global) $climbing[] = $global;
    if ($rock) $climbing[] = $rock;
    if ($ice) $climbing[] = $ice;
    if ($mixed) $climbing[] = $mixed;
    if ($aid) $climbing[] = $aid;

    $groups[] = implode('/', $ski1);
    $groups[] = implode('/', $ski2);
    $groups[] = implode('/', $climbing);
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
    li(_format_data('Document type', __($type)));
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
    if (empty($value) || $value instanceof Doctrine_Null) return '';

    if (!empty($suffix)) $suffix = ' ' . __($suffix);

    return __($name) . ' ' . $value . $suffix;
}
