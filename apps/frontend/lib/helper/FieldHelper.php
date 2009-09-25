<?php
/**
 * $Id: FieldHelper.php 2455 2007-11-30 11:44:31Z alex $
 */

function loadTooltipsViewRessources()
{
    $static_base_url = sfConfig::get('app_static_url');

    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript($static_base_url . '/static/js/tooltips.js?' . sfSVN::getHeadRevision('tooltips.js'), 'last');
    $response->addJavascript($static_base_url . '/static/js/tooltips_view.js?' . sfSVN::getHeadRevision('tooltips_view.js'), 'last');
}

loadTooltipsViewRessources();

function field_data($document, $name, $prefix = '', $suffix = '', $title = '')
{
    $value = $document->get($name);
    
    if (empty($title))
    {
        $title = $name;
    }
    
    return field_data_arg($title, $value, $prefix, $suffix);
}

function field_data_arg($name, $value, $prefix = '', $suffix = '')
{
    if (empty($value))
    {
        $value = '';
    }

    return _format_data($name, $value, false, $prefix, $suffix);
}

function field_data_if_set($document, $name, $prefix = '', $suffix = '', $title = '')
{
    $value = $document->get($name);
    
    if (empty($title))
    {
        $title = $name;
    }
    
    return field_data_arg_if_set($title, $value, $prefix, $suffix);
}

function field_data_arg_if_set($name, $value, $prefix = '', $suffix = '')
{
    if (!check_not_empty($value))
    {
        return '';
    }
    
    return _format_data($name, $value, false, $prefix, $suffix);
}

function field_data_range($document, $name_min, $name_max, $separator = ' / ', $prefix_min = '', $prefix_max = '', $suffix = '', $range_only = false)
{
	$value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    
    return field_data_arg_range($name_min, $name_max, $value_min, $value_max, $separator, $prefix_min, $prefix_max, $suffix, $range_only);
}

function field_data_arg_range($name_min, $name_max, $value_min, $value_max, $separator = ' / ', $prefix_min = '', $prefix_max = '', $suffix = '', $range_only = false)
{
    $name = $name_min . '_' . $name_max;
    if ((!empty($value_min) && !empty($value_max)) || ((!empty($value_min) || !empty($value_max)) && $range_only))
    {
        return _format_data_range($name, $value_min, $value_max, false, $separator, $prefix_min, $prefix_max, $suffix);
    }
	else if (!empty($value_min) && empty($value_max))
	{
		return _format_data($name_min, $value_min, false, '', $suffix);
	}
	else if (empty($value_min) && !empty($value_max))
	{
		return _format_data($name_max, $value_max, false, '', $suffix);
	}
    else
    {
        return _format_data($name, '');
    }
}

function field_data_range_if_set($document, $name_min, $name_max, $separator = ' / ', $prefix_min = '', $prefix_max = '', $suffix = '', $range_only = false)
{
	$value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    
    return field_data_arg_range_if_set($name_min, $name_max, $value_min, $value_max, $separator, $prefix_min, $prefix_max, $suffix, $range_only);
}

function field_data_arg_range_if_set($name_min, $name_max, $value_min, $value_max, $separator = ' / ', $prefix_min = '', $prefix_max = '', $suffix = '', $range_only = false)
{
    if (empty($value_min) && empty($value_max))
    {
        return '';
    }
    
	return field_data_arg_range($name_min, $name_max, $value_min, $value_max, $separator, $prefix_min, $prefix_max, $suffix, $range_only);
}

function field_data_from_list($document, $name, $config, $multiple = false, $raw = false, $prefix = '', $suffix = '')
{
    return _format_data_from_list($name, $document->getRaw($name), $config, $multiple, $raw, $prefix, $suffix);
}

function field_data_from_list_if_set($document, $name, $config, $multiple = false, $raw = false, $prefix = '', $suffix = '')
{
    $value = (isset($document[$name])) ? $document[$name] : $document->getRaw($name);
    if (!check_not_empty($value) || $value == '0')
    {
        return '';
    }
    if ($multiple)
    {
        $value = is_array($value) ? $value : Document::convertStringToArray($value);
        if (empty($value))
        {
            return '';
        }
    }

    return _format_data_from_list($name, $value, $config, $multiple, $raw, $prefix, $suffix);
}

function field_data_range_from_list($document, $name_min, $name_max, $separator = ' / ', $config, $range_only = false, $raw = false, $prefix = '', $suffix = '')
{
    $name = $name_min . '_' . $name_max;
	$value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    if ((!empty($value_min) && !empty($value_max)) || ((!empty($value_min) || !empty($value_max)) && $range_only))
    {
        return _format_data_range_from_list($name, $value_min, $value_max, $separator, $config, $raw, $prefix, $suffix);
    }
	else if (!empty($value_min) && empty($value_max))
	{
		return _format_data_from_list($name_min, $value_min, $config, false, $raw, $prefix, $suffix);
	}
	else if (empty($value_min) && !empty($value_max))
	{
		return _format_data_from_list($name_max, $value_max, $config, false, $raw, $prefix, $suffix);
	}
    else
    {
        return _format_data($name, '', $raw, $prefix, $suffix);
    }
}

function field_data_range_from_list_if_set($document, $name_min, $name_max, $separator = ' / ', $config, $range_only = false, $raw = false, $prefix = '', $suffix = '')
{
    $value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    if (empty($value_min) && empty($value_max))
    {
        return '';
    }
    
	return field_data_range_from_list($document, $name_min, $name_max, $separator, $config, $range_only, $raw, $prefix, $suffix);
}

function field_picto_from_list($document, $name, $config, $multiple = false, $raw = false, $printspan = false, $picto_name = '', $separator = ' - ', $prefix = '', $suffix = '')
{
    return _format_picto_from_list($name, $document->getRaw($name), $config, $multiple, $raw, $printspan, $picto_name, $separator, $prefix, $suffix);
}

function field_picto_from_list_if_set($document, $name, $config, $multiple = false, $raw = false, $printspan = false, $picto_name = '', $separator = ' - ', $prefix = '', $suffix = '')
{
    $value = (isset($document[$name])) ? $document[$name] : $document->getRaw($name);
    if (!check_not_empty($value))
    {
        return '';
    }
    return _format_picto_from_list($name, $value, $config, $multiple, $raw, $printspan, $picto_name, $separator, $prefix, $suffix);
}

function field_activities_data($document, $raw = false, $printspan = true, $prefix = '', $suffix = '')
{
    return field_picto_from_list($document, 'activities', 'app_activities_list', true, $raw, $printspan, 'activity', ' - ', $prefix, $suffix);
}

function field_activities_data_if_set($document, $raw = false, $printspan = true, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'activities', 'app_activities_list', true, $raw, $printspan, 'activity', ' - ', $prefix, $suffix);
}

function _activities_data($activities, $printspan = false)
{
    return _format_picto_from_list('activities', $activities, 'app_activities_list', true, true, $printspan, 'activity', ' - ');
}

function field_pt_picto_if_set($document, $raw = false, $printspan = true, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'public_transportation_types', 'app_parkings_public_transportation_types', true, $raw, $printspan, 'pt', ', ', $prefix, $suffix);
}

function _pt_picto_if_set($pt_types, $printspan = false)
{
    return _format_picto_from_list('public_transportation_types', $pt_types, 'app_parkings_public_transportation_types', true, true, $printspan, 'pt', ', ');
}

function field_frequentation_picto_if_set($document, $raw = false, $printspan = true, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list', false, $raw, $printspan, 'freq', ', ', $prefix, $suffix);
}

function _frequentation_picto_if_set($frequentation, $printspan = false)
{
    return _format_picto_from_list('frequentation_status', $frequentation, 'mod_outings_frequentation_statuses_list', false, true, $printspan, 'freq');
}

function field_conditions_picto_if_set($document, $raw = false, $printspan = true, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'conditions_status', 'mod_outings_conditions_statuses_list', false, $raw, $printspan, 'cond', ', ', $prefix, $suffix);
}

function _conditions_picto_if_set($conditions, $printspan = false)
{
    return _format_picto_from_list('conditions_status', $conditions, 'mod_outings_conditions_statuses_list', false, true, $printspan, 'cond');
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

function field_bool_data($document, $name, $show_no = false, $prefix = '', $suffix = '')
{
    $value = $document->get($name);
    if (is_null($value))
    {
        if ($show_no)
        {
            $value = 0;
        }
        else
        {
            return '';
        }
    }
    $value = (bool)$value ? 'yes' : 'no';
    $value = __($value);
    return _format_data($name, $value, false, $prefix, $suffix);
}

function _format_data($name, $value, $raw = false, $prefix = '', $suffix = '')
{
    if (empty($value))
    {
        $empty_value = true;
        $value = '<span class="default_text">' . __('nonwell informed') . '</span>';
        $div_class = ' default_text';
    }
    else
    {
        $empty_value = false;
        $div_class = '';
    }
    
    if ($raw)
    {
        $text = '';
    }
    else
    {
        $text = '<div class="section_subtitle' . $div_class . '" id="_' . $name .'">' . __($name) . '</div> ';
    }

    if (!empty($prefix) && !$empty_value)
    {
        $text .= __($prefix);
    }
    
    $text .= $value;

    if (!empty($suffix) && !$empty_value)
    {
        $text .= __($suffix);
    }

    return $text;
}

function _format_data_range($name, $value_min, $value_max, $raw = false, $separator = ' / ', $prefix_min = '', $prefix_max = '', $suffix = '')
{
    if ($raw)
    {
        $text = '';
    }
    else
    {
        $text = '<div class="section_subtitle" id="_'. $name .'">' . __($name) . '</div> ';
    }

    if (!empty($value_min) && !empty($value_max) && $value_min == $value_max)
    {
        $text .= $value_min;
        
        if (!empty($suffix))
        {
            $text .= __($suffix);
        }
    }
    else
    {
        if (!empty($value_min))
        {
            if (!empty($prefix_min))
            {
                $text .= __($prefix_min);
            }
            
            $text .= $value_min;
        	
            if (!empty($suffix))
            {
                $text .= __($suffix);
            }
        }
        
        if (!empty($value_min) && !empty($value_max))
        {
            $text .= __($separator);
        }

        if (!empty($value_max))
        {
            if (!empty($prefix_max))
            {
                $text .= __($prefix_max);
            }
        	
        	$text .= $value_max;
        	
            if (!empty($suffix))
            {
                $text .= __($suffix);
            }
        }
    }

    return $text;
}

function _format_data_from_list($name, $value, $config, $multiple = false, $raw = false, $prefix = '', $suffix = '')
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
            $value = array_filter($value);
            $value = implode(', ', $value);
        }
        else
        {
            $value = _get_field_value_in_list($list, $value);
        }
    } else {
        $value = '';
    }

    return _format_data($name, $value, $raw, $prefix, $suffix);
}

function _format_data_range_from_list($name, $value_min, $value_max, $separator = ' / ', $config, $raw = false, $prefix = '', $suffix = '')
{
    $list = sfConfig::get($config);
    $value = '';
    
	if (!empty($value_min))
    {
        $value .= _get_field_value_in_list($list, $value_min);
    }
    
    if (empty($value_min) || empty($value_max) || $value_min != $value_max)
    {
        if (!empty($value_min) && !empty($value_max))
        {
            $value .= __($separator);
        }
    	
        if (!empty($value_max))
        {
            $value .= _get_field_value_in_list($list, $value_max);
        }
    }

    return _format_data($name, $value, $raw, $prefix, $suffix);
}

function _format_picto_from_list($name, $value, $config, $multiple = false, $raw = false, $printspan = false, $picto_name = '', $separator = ' - ', $prefix = '', $suffix = '')
{
    if (!empty($value))
    {
        $html = '';
        $picto_text_list = array();
        $list = sfConfig::get($config);
        if ($multiple)
        {
            $value = is_array($value) ? $value : Document::convertStringToArray($value);
        }
        else
        {
            $value = is_array($value) ? array(reset($value)) : array($value);
        }
        
        foreach ($value as $picto_id)
        {
            if (!$picto_id || $picto_id == '0' || !isset($list[$picto_id]))
            {
                continue;
            }
            $picto_text = __($list[$picto_id]);
            $html .= ' <span class="picto '.$picto_name.'_'.$picto_id.'" title="'.$picto_text.'"></span>';
            $picto_text_list[] = $picto_text;
        }
        $html = trim($html);
        
        if (!empty($html) && $printspan)
        {
            $html = $html.'<span class="printonly">'.implode($separator, $picto_text_list).'</span>';
        }
    }
    else
    {
        $html = '';
    }

    return _format_data($name, $html, $raw, $prefix, $suffix);
}

function _get_field_value_in_list($list, $key)
{
    if (empty($key) || !is_scalar($key))
    {
        return '';
    }
    return (!empty($list[$key]) ? __($list[$key]) : '');
}

function field_text_data($document, $name, $label = NULL, $options = NULL)
{
    return _format_text_data($name, $document->get($name), $label, $options);
}

function field_text_data_if_set($document, $name, $label = NULL, $options = NULL)
{
    $value = $document->get($name);
    if (empty($value))
    {
        return '';
    }

   return _format_text_data($name, $document->get($name), $label, $options);
}

function _format_text_data($name, $value, $label = NULL, $options)
{
    use_helper('sfBBCode', 'SmartFormat');

    if (empty($label))
    {
        $label = $name;
    }

    $translatable = _option($options, 'needs_translation', false);
    $inserted = _option($options, 'inserted_text', '');
    $images = _option($options, 'images', null);
    $filter_image_type = _option($options, 'filter_image_type', true);
    $show_label = _option($options, 'show_label', true);

    if ($show_label)
    {
        $label = '<div class="section_subtitle htext" id="_' . $name .'">' . __($label) . "</div>\n";
    }
    else
    {
        $label = '';
    }
    
    return (($translatable) ? '<div class="translatable">' : '')
           . $label
           . $inserted
           . '<div class="field_value">'
           . parse_links(parse_bbcode($value, $images, $filter_image_type)).'</div>'.(($translatable) ? '</div>' : '');
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

    return  _format_data($name, $value, false, $prefix, $suffix);
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
                   array('title' => __($title, array('%1%' => 'GPX')), 'rel' => 'nofollow'))
           . ' ' .
           link_to('KML', "@export_kml?module=$module&id=$id&lang=$lang",
                   array('title' => __($title, array('%1%' => 'KML')), 'rel' => 'nofollow'))
           . ' ' .
           link_to('JSON', "@export_json?module=$module&id=$id&lang=$lang",
                   array('title' => __($title, array('%1%' => 'JSON')), 'rel' => 'nofollow'));
}

function field_getdirections($id)
{
    $title = 'Use %1% to see directions to this parking';
    return '<span class="section_subtitle" id="get_directions">' . __('Get directions:') . '</span>'
           . ' ' .
           link_to('Google', "@getdirections?id=$id&service=gmaps",
                   array('title' => __($title, array('%1%' => 'Google Maps')),
                         'class' => 'external_link'))
           . ' ' .
           link_to('Yahoo!', "@getdirections?id=$id&service=yahoo",
                   array('title' => __($title, array('%1%' => __('Yahoo! Maps'))),
                         'class' => 'external_link'))
           . ' ' .
           link_to('Live Search', "@getdirections?id=$id&service=livesearch",
                   array('title' => __($title, array('%1%' => 'Live Search Maps')),
                         'class' => 'external_link'));
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
    return _format_data($name, $value, false, '', '');
}

function field_exposure_time_if_set($document, $name = 'exposure_time', $prefix = '1/', $suffix = 's')
{
    $value = $document->get($name);
    if (empty($value))
    {
        return '';
    }

    return _format_data($name, round(1/$value), false, $prefix, $suffix);
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
    $activities =  isset($document['activities']) ?
        Document::convertStringToArray($document['activities']) : $document->get('activities', ESC_RAW);
    
    $rock_free = _filter_ratings_data($document, 'rock_free_rating', 'app_routes_rock_free_ratings', false, true, '');
    $rock_required = _filter_ratings_data($document, 'rock_required_rating', 'app_routes_rock_free_ratings', false, true, '');
    if ($rock_free && $rock_required)
    {
        if ($rock_free == $rock_required)
        {
            $rock_free_name = 'rock_free_and_required_rating';
            $rock_required = null;
        }
        else
        {
            $rock_free_name = 'rock_free_rating';
        }
    }
    else
    {
        $rock_free_name = '';
    }

    return _route_ratings_sum_up(
        _filter_ratings_data($document, 'global_rating', 'app_routes_global_ratings', $add_tooltips),
        _filter_ratings_data($document, 'engagement_rating', 'app_routes_engagement_ratings', $add_tooltips),
        _filter_ratings_data($document, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings', $add_tooltips),
        _filter_ratings_data($document, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings', $add_tooltips),
        _filter_ratings_data($document, 'labande_ski_rating', 'app_routes_labande_ski_ratings', $add_tooltips),
        _filter_ratings_data($document, 'labande_global_rating', 'app_routes_global_ratings', $add_tooltips),
        _filter_ratings_data($document, $rock_free_name, 'app_routes_rock_free_ratings', $add_tooltips, false, null, $rock_free),
        _filter_ratings_data($document, 'rock_required_rating', 'app_routes_rock_free_ratings', $add_tooltips, false, null, $rock_required),
        _filter_ratings_data($document, 'ice_rating', 'app_routes_ice_ratings', $add_tooltips),
        _filter_ratings_data($document, 'mixed_rating', 'app_routes_mixed_ratings', $add_tooltips),
        _filter_ratings_data($document, 'aid_rating', 'app_routes_aid_ratings', $add_tooltips),
        _filter_ratings_data($document, 'equipment_rating', 'app_equipment_ratings_list', $add_tooltips, true, 'P'),
        _filter_ratings_data($document, 'hiking_rating', 'app_routes_hiking_ratings', $add_tooltips),
        $activities,
        $show_activities
        );
}

function _filter_ratings_data($document, $name, $config, $add_tooltips = false, $use_raw_value = false, $raw_value_prefix = null, $raw_value = null)
{
    if (is_null($raw_value))
    {
        $raw_value = !empty($document[$name]) ? $document[$name] : $document->get($name, 'ESC_RAW');
    }
    $value = _get_field_value_in_list(sfConfig::get($config), $raw_value);

    if (empty($value))
    {
        return null;
    }
    $string_value = $use_raw_value ? $raw_value_prefix . $raw_value : $value;
    if ($add_tooltips)
    {
        $string_value = '<span title="'.__($name).' '.$value.'">'.$string_value.'</span>';
    }
    return $string_value;
}

function _route_ratings_sum_up($global, $engagement, $topo_ski, $topo_exp, $labande_ski, $labande_global,
                               $rock_free, $rock_required, $ice, $mixed, $aid, $equipment, $hiking, $activities = array(), $show_activities = true)
{
    $groups = $ski1 = $ski2 = $main_climbing = $climbing = array();

    if ($topo_ski) $ski1[] = $topo_ski;
    if ($topo_exp) $ski1[] = $topo_exp;
    if ($labande_global) $ski2[] = $labande_global;
    if ($labande_ski) $ski2[] = $labande_ski;
    if ($global) $main_climbing[] = $global;
    if ($engagement) $main_climbing[] = $engagement;
    if ($equipment) $main_climbing[] = $equipment;
    if ($aid) $climbing[] = $aid;
    if ($rock_free && $rock_required)
    {
        if ($rock_free != $rock_required)
        {
            $climbing[] = $rock_free . '(' . $rock_required . ')';
        }
        else
        {
            $climbing[] = $rock_free;
        }
    }
    elseif ($rock_free)
    {
        $climbing[] = $rock_free;
    }
    elseif ($rock_required)
    {
        $climbing[] = $rock_required;
    }
    if ($ice) $climbing[] = $ice;
    if ($mixed) $climbing[] = $mixed;

    if ($ski_activities = array_intersect(array(1), $activities))
    {
        if ($show_activities)
        {
            $groups[] = _activities_data($ski_activities);
        }
        $groups[] = implode('/', $ski1);
        $groups[] = implode('/', $ski2);
    }
    if ($climbing_activities = array_intersect(array(2,3,4,5), $activities))
    {
        if ($show_activities)
        {
            $groups[] = _activities_data($climbing_activities);
        }
        $groups[] = implode('/', $main_climbing);
        $groups[] = implode('/', $climbing);
    }
    if ($hiking_activities = array_intersect(array(6), $activities))
    {
        if ($show_activities)
        {
            $groups[] = _activities_data($hiking_activities);
        }
        $groups[] = $hiking;
    }
    return implode(' ', $groups);
}

function li($string, $separator = false)
{
    if (!empty($string))
    {
        if ($separator)
        {
            $options = " class=\"separator\"";
        }
        else
        {
            $options = "";
        }
        
        echo "<li$options>$string</li>\n";
    }
}

function disp_doc_type($type)
{
    li(_format_data('document_type', __($type)));
}

function disp_nickname($nick)
{
    li(_format_data('nick_name', __($nick)));
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

    if (!empty($suffix)) $suffix = __($suffix);

    return '<em>' . __($name) . '</em> ' . $value . $suffix;
}

function check_not_empty($value)
{
    return (!$value instanceof Doctrine_Null && !empty($value));
}

function summarize_route($route, $show_activities = true, $add_tooltips = false)
{
    $max_elevation = is_scalar($route['max_elevation']) ? ($route['max_elevation'] . __('meters')) : NULL;
    
    $height_diff_up = is_scalar($route['height_diff_up']) ? ($route['height_diff_up'] . __('meters')) : NULL;
    if (($height_diff_up != NULL) && is_scalar($route['difficulties_height']))
    {
        $difficulties_height = $route['difficulties_height'] . __('meters');
    }
    else
    {
        $difficulties_height = NULL;
    }
    
    $facing = field_data_from_list_if_set($route, 'facing', 'app_routes_facings', false, true);

    if ($add_tooltips)
    {
        if (!empty($max_elevation))
        {
            $max_elevation = '<span title="' . __('max_elevation') . ' ' . $max_elevation . '">' . $max_elevation . '</span>';
        }
        if (!empty($height_diff_up))
        {
            $height_diff_up = '<span title="' . __('height_diff_up') . ' ' . $height_diff_up . '">' . $height_diff_up . '</span>';
        }
        if (!empty($difficulties_height))
        {
            $difficulties_height = '<span title="' . __('difficulties_height') . ' ' . $difficulties_height . '">' . $difficulties_height . '</span>';
        }
        if (!empty($facing))
        {
            $facing = '&nbsp;<span title="' . __('facing') . ' ' . $facing . '">' . $facing . '</span> ';
        }
    }
    
    $height = array();
    if (!empty($height_diff_up))
    {
        $height_diff_up = '+' . $height_diff_up;
        $height[] = $height_diff_up;
    }
    if (!empty($difficulties_height))
    {
        $difficulties_height = '(' . $difficulties_height . ')';
        $height[] = $difficulties_height;
    }
    $height = implode(' ', $height);
    
    $route_data = array($max_elevation,
                        $height,
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
        $route_data = implode('&nbsp; ', $route_data);
    }

    return $route_data;
}

function get_activity_classes($activities)
{
    if (empty($activities))
    {
        return '';
    }

    $alist = sfConfig::get('app_activities_list');
    
    foreach ($activities as &$activity)
    {
        $activity = $alist[$activity];
    }

    return ' ' . implode(" ", $activities);
    
}


function format_book_data($books, $type, $main_id, $is_moderator = false, $needs_add_display = false)
{
    // NOTE: this is mostly copied from association_plus... could this be refactored?

    $type_list = $type . '_list';
    $module = 'books';
    $strict = 1;
    $html = '<div class="association_content_inside_field">';

    foreach ($books as $book)
    {
        $doc_id = $book['id'];
        $idstring = $type . '_' . $doc_id;
        $class = 'linked_elt';
        $html .= '<div class="' . $class . '" id="' . $idstring . '">'
               . '<div class="assoc_img picto_' . $module . '" title="' . ucfirst(__($module)) . '"></div>';
        $name = ucfirst($book['name']);
        $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $book['culture'] . '&slug=' . formate_slug($book['search_name']);
        $html .= link_to($name, $url);
        if (isset($book['author']) && trim($book['author']) != '')
        {
            $html .= ' - ' . $book['author'];
        }
        if ($is_moderator)
        {
            $html .= ' ' . c2c_link_to_delete_element($type, $doc_id, $main_id, false, $strict);
        }
        $html .= '</div>';
    }
    // display plus sign and autocomplete form
    if ($needs_add_display)
    {
        if (count($books) == 0)
        {
            $html .= '<div id="book_default_list_icon" class="assoc_img picto_' . $module . '" title="' . ucfirst(__($module)) . '"></div>';
        }
        $html .= '<div id=' . $type_list . '></div>';
        $form = $type . '_ac_form';
        $add = $type . '_add';
        $minus = $type . '_hide_form';
        $html .= c2c_form_remote_add_element("documents/addRemoveAssociation?linked_id=$main_id&mode=add&type=$type&icon=books", $type_list, null, 'indicator', 'book_default_list_icon');
        $html .= input_hidden_tag('main_' . $type . '_id', '0'); // 0 corresponds to no document
        $html .= '<div class="add_assoc">'
               . '    <div id="' . $type . '_add">'
               . '        ' . link_to_function(picto_tag('picto_add', __('Link an existing document')),
                                                         "showForm('$form', '$add', '$minus')",
                                                         array('class' => 'add_content'))
               . '    </div>'
               . '    <div id="' . $type . '_hide_form" style="display: none">'
               . '        ' . link_to_function(picto_tag('picto_rm', __('hide form')),
                                               "hideForm('$form', '$add', '$minus')",
                                               array('class'=>'add_content'))
               . '    </div>'
               . '    <div id="' . $type . '_ac_form" style="display: none;">'
               . c2c_auto_complete($module, 'main_' . $type . '_id')
               . '   </div></div></form>';
    }
    $html .= '</div>';
    return $html;
}

function _option(&$options, $name, $default = null)
{
  if (empty($options)) return $default;

  if (array_key_exists($name, $options))
  {
    $value = $options[$name];
    unset($options[$name]);
  }
  else
  {
    $value = $default;
  }

  return $value;
}
