<?php
/**
 * $Id: FilterFormHelper.php 2538 2007-12-20 16:08:35Z alex $
 */

use_helper('Form', 'MyForm', 'Javascript', 'General');

function elevation_selector($fieldname, $unit = 'meters')
{
    $option_tags = options_for_select(array('0' => '',
                                            '1' => __('greater than'),
                                            '2' => __('lower than'),
                                            '3' => __('between'),
                                            '=' => __('equal'),
                                            ' ' => __('filled in'),
                                            '-' => __('nonwell informed'))
                                     );
    $out = select_tag($fieldname . '_sel', $option_tags,
                      array('onchange' => "C2C.update_on_select_change('$fieldname', 3)"));
    $out .= '<span id="' . $fieldname . '_span1" style="display:none"> ';
    $out .= input_tag($fieldname, NULL, array('class' => 'short_input'));
    $out .= '<span id="' . $fieldname . '_span2" style="display:none"> ' . __('and') . ' ';
    $out .= input_tag($fieldname . '2', NULL, array('class' => 'short_input'));
    $out .= '</span> ' . __($unit) . '</span>'; 
    return '<span class="lineform">' . $out . '</span>';
}

function range_selector($fieldname, $config, $unit = NULL, $i18n = false)
{
    $option_tags = options_for_select(array('0' => '',
                                            '1' => __('greater than'),
                                            '2' => __('lower than'),
                                            '3' => __('between'),
                                            '=' => __('equal'),
                                            ' ' => __('filled in'),
                                            '-' => __('nonwell informed'))
                                     );
    $out = select_tag($fieldname . '_sel', $option_tags,
                      array('onchange' => "C2C.update_on_select_change('$fieldname', 3)"));
    $out .= '<span id="' . $fieldname . '_span1" style="display:none"> ';
    $out .= topo_dropdown($fieldname, $config, $i18n);
    $out .= '<span id="' . $fieldname . '_span2" style="display:none"> ' . __('and') . ' ';
    $out .= topo_dropdown($fieldname . '2', $config, $i18n);
    $out .= '</span>';
    if ($unit)
    {
        $out .= ' ' . __($unit);
    }
    $out .= '</span>'; 
    return '<span class="lineform">' . $out . '</span>';
}

function facings_selector($fieldname)
{
    $option_tags = options_for_select(array('0' => '',
                                            '~' => __('between'),
                                            '=' => __('equal'),
                                            ' ' => __('filled in'),
                                            '-' => __('nonwell informed'))
                                     );     
    $out = select_tag($fieldname . '_sel', $option_tags,
                      array('onchange' => "C2C.update_on_select_change('$fieldname', 2)"));
    $out .= '<span id="' . $fieldname . '_span1" style="display:none"> ';
    $out .= topo_dropdown($fieldname, 'app_routes_facings');
    $out .= '<span id="' . $fieldname . '_span2" style="display:none"> ' . __('and') . ' ';
    $out .= topo_dropdown($fieldname . '2', 'app_routes_facings');
    $out .= '&nbsp;' . __('(hour loop)');
    $out .= '</span></span>'; 
    return '<span class="lineform">' . $out . '</span>';
}

function topo_dropdown($fieldname, $config, $i18n = false, $keepfirst = false, $add_empty = false)
{
    $options = sfConfig::get($config);
    if ($i18n)
    {
        $options = array_map('__', $options);
    }
    if (!$keepfirst) {
        unset($options[0]);
    }
    if ($add_empty)
    {
        array_unshift($options, '');
    }
    $option_tags = options_for_select($options);
    return select_tag($fieldname, $option_tags);
}

function activities_selector($onclick = false, $use_personalization = false, $filtered_activities = array(), $unavailable_activities = array(), $merged_activities = array(), $multiple = true, $show_picto = true, $activity_config = '')
{
    $out = array();
    $col = 0;
    $col_item = 0;
    if (empty($activity_config))
    {
        $activity_config = 'app_activities_form';
    }
    $activities = sfConfig::get($activity_config);
    
    $multiple_activities = array();
    if (is_array($multiple))
    {
        if (count($multiple))
        {
            $multiple_activities = $multiple;
        }
        $multiple = false;
    }
    
    if (!$multiple)
    {
        foreach($merged_activities as $key => $value)
        {
            $activities[$key] = $value;
        }
    }
    foreach($unavailable_activities as $key => $value)
    {
        if (array_key_exists($key, $activities) && (empty($value) || !$multiple))
        {
            $activity = $activities[$key];
            unset($activities[$key]);
        }
        if (!empty($value) && !$multiple)
        {
            $activities[$key] = $activity;
        }
    }
    if ($multiple)
    {
        foreach($merged_activities as $key => $value)
        {
            $activities[$key] = $value;
        }
    }
    else
    {
        foreach($multiple_activities as $key)
        {
            $activity = $activities[$key];
            unset($activities[$key]);
            $activities[$key] = $activity;
        }
    }
    
    $item_max = count($activities) - count($multiple_activities) - 1;
    $col_item_max = ceil(($item_max + 1)/2) - 1;

    if (!count($filtered_activities) && $use_personalization)
    {
        $perso = c2cPersonalization::getInstance();
        if ($perso->isMainFilterSwitchOn()) $filtered_activities = $perso->getActivitiesFilter();
    }

    foreach ($activities as $activity_id => $activity)
    {
        if (array_key_exists($activity_id, $unavailable_activities))
        {
            $tag = explode('/', $unavailable_activities[$activity_id]);
            if (count($tag) == 2)
            {
                $param = $tag[0];
                $value = $tag[1];
                $ckeckbox = true;
            }
            else
            {
                continue;
            }
        }
        else
        {
            $param = 'act';
            $value = $activity_id;
            if (in_array($activity_id, $multiple_activities))
            {
                $ckeckbox = true;
            }
            else
            {
                $ckeckbox = $multiple;
            }
        }
        
        if ($col_item == 0)
        {
            $col_class = ($col % 2) ? 'col' : 'col_left';
            $out[] = '<div class="' . $col_class . '">';
        }
        
        $checked = in_array($activity_id, $filtered_activities) ? true : false;

        $activity_id_list = explode('-', $activity_id);
        $label_text = '';
        if ($show_picto)
        {
            foreach($activity_id_list as $id)
            {
                $label_text .= '<span class="picto activity_' . $id . '"></span>';
            }
        }
        $label_text .= __($activity);
        if ($ckeckbox)
        {
            $input_tag = checkbox_tag($param . '[]', $value, $checked);
        }
        else
        {
            $input_tag = my_radiobutton_tag($param . '[]', $value, $checked);
        }
        $out[] = $input_tag . ' ' . 
                 label_for($param . '_' . $value, $label_text);
        
        if ($col_item == $col_item_max || ($col * $col_item_max + $col_item == $item_max))
        {
            $out[] = '</div>';
            $col += 1;
            $col_item = 0;
        }
        else
        {
            $out[] = '<br />';
            $col_item += 1;
        }
    }
    if ($col_item > 0)
    {
        $out[] = '</div>';
    }
    return '<div id="actform">' . implode("\n", $out) . '</div>';
}

function translate_sort_param($label)
{
    return str_replace(array(' :', ':'), '', __($label));
}

function field_value_selector($name, $conf, $options)
{
    $blank = _option($options, 'blank', false);
    $keepfirst = _option($options, 'keepfirst', true);
    $multiple = _option($options, 'multiple', false);
    $size = c2cTools::mobileVersion() ? _option($options, 'msize', 1) : _option($options, 'size', 0);
    $filled_options = _option($options, 'filled_options', true);
    $exclude = _option($options, 'exclude', null);

    $values = array_map('__', sfConfig::get($conf));
    if ($exclude)
    {
        $exclude = is_array($exclude) ? $exclude : array($exclude);
        foreach ($exclude as $value)
        {
            unset($values[$value]);
        }
    }
    if (!$keepfirst)
    {
        unset($values[0]);
    }
    if ($filled_options)
    {
        $values[' '] = __('filled in');
        $values['_'] = __('nonwell informed');
    }
    $option_tags = options_for_select($values, '',
                                      array('include_blank' => $blank));

    if ($multiple)
    {
        $select_param = array('multiple' => true);
        if ($size == 0)
        {
            $size = count($values);
            if ($filled_options)
            {
                $size -= 2;
            }
        }
        $select_param['size'] = $size;
    }
    else
    {
        $select_param = array();
    }
    return select_tag($name, $option_tags, $select_param);
}

function around_selector($name, $coords = array(), $multiline = false)
{
    // note that all javascript is handled in geocode_autocompleter.js
    // we should separate Geocode.Autocompleter and the code specific to the selector
    // if Geocode.Autocompleter should be used elsewhere
    use_helper('AutoComplete');
    
    if (count($coords) == 2)
    {
        $manual_coords = true;
        list($lon, $lat) = $coords;
    }
    else
    {
        $manual_coords = false;
        $lon = $lat = '';
    }
    
    $options_tmp = array('0' => '',
                         '1' => __('Place'),
                         '2' => __('My position'));
    if ($manual_coords)
    {
        $options_tmp['3'] = __('Coordinates');
        $selected = '3';
        $main_display = '';
    }
    else
    {
        $selected = '';
        $main_display = ' style="display:none"';
    }
    $option_tags = options_for_select($options_tmp, $selected);

    $out = __('Around: ');
    $out .= select_tag($name . '_sel', $option_tags,
                       array('onchange' => "C2C.update_around_on_select_change('$name')"));
    $out .= input_hidden_tag($name . '_lat', $lat);
    $out .= input_hidden_tag($name . '_lon', $lon);

    $out .= '<span id="' . $name . '_span"' . $main_display . '>';

    // geocode api
    $out .= '<span id="' . $name . '_geocode" style="display:none">';
    $out .= geocode_auto_complete($name, sfConfig::get('app_autocomplete_geocode_service'));
    $out .= ' </span>';

    // browser geolocation
    $out .= '<span id="' . $name . '_geolocation_not_supported" style="display:none">';
    $out .= __('geolocation not supported') . '</span>';
    $out .= '<span id="' . $name . '_geolocation_waiting" style="display:none">';
    $out .= __('waiting for geolocation') . '</span>';
    $out .= '<span id="' . $name . '_geolocation_failed" style="display:none">';
    $out .= __('geolocation failed') . '</span>';
    $out .= '<span id="' . $name . '_geolocation_denied" style="display:none">';
    $out .= __('geolocation denied') . '</span>';

    // manual coordinates
    if ($manual_coords)
    {
        $out .= '<span id="' . $name . '_manual">'
             .  __('lon') . ' ' . $lon . ' / ' . __('lat') . ' ' . $lat . '<br/>'
             .  '</span>'
             .  input_hidden_tag($name . '_manual_lat', $lat)
             .  input_hidden_tag($name . '_manual_lon', $lon);
    }
    else
    {
        $out .= '<span id="' . $name . '_manual" style="display:none"></span>';
    }

    // range input
    $out .= '<span id="' . $name . '_range_span">';
    if ($multiline)
    {
        $out .= '<br />';
    }
    $out .= __('within km: ');
    $out .= input_tag($name . '_range', 5, array('value' => '10', 'class' => 'short_input'));
    $out .= ' ' . __('kilometers');
    $out .= '</span>';

    $out .= '</span>';    

    return '<span class="around_form">' . $out . '</span>';
}

function date_selector($include_blanks = array('month' => false, 'day' => false, 'year' => false))
{
    $option_tags = options_for_select(array('0' => '',
                                            '4' => __('for (time)'),
                                            '1' => __('greater than'),
                                            '2' => __('lower than'),
                                            '3' => __('between'),
                                            '=' => __('equal'))
                                     );
    $out = select_tag('date_sel', $option_tags,
                      array('onchange' => "C2C.update_on_select_change('date', 4)"));
    
    $out .= '<span id="date_span1" style="display:none"> ';
    $out .= my_input_date_tag('date', NULL, array('class' => 'medium_input',
                                               'include_blank_year' => $include_blanks['year'],
                                               'include_blank_month' => $include_blanks['month'],
                                               'include_blank_day' => $include_blanks['day'],
                                               'year_start' => date('Y'),
                                               'year_end' => sfConfig::get('app_date_year_min')));
    $out .= '<span id="date_span2" style="display:none"> ' . __('and') . ' ';
    $out .= my_input_date_tag('date2', NULL, array('class' => 'medium_input',
                                                'include_blank_year' => $include_blanks['year'],
                                                'include_blank_month' => $include_blanks['month'],
                                                'include_blank_day' => $include_blanks['day'],
                                                'year_start' => date('Y'),
                                                'year_end' => sfConfig::get('app_date_year_min')));
    $out .= '</span>' . __('Year and day are optional') . '</span>';
    
    $ages_values = sfConfig::get('app_ages_values');
    $ages_units = sfConfig::get('app_ages_units');
    $options = array();
    foreach ($ages_values as $key => $age_value)
    {
        $options[$key] = $age_value . ' ' . __($ages_units[$key]);
    }
    $out .= '<span id="date_span3" style="display:none"> ';
    $out .= select_tag('date3', options_for_select($options, '1W'));
    $out .= '</span>';
    
    return '<span class="dateform">' . $out . '</span>';
}

// same as input_date_tag from symfony, except we can specifiy blank for days, month, years separately
// and it proposes far less options (that we don't use)
function my_input_date_tag($name, $value = null, $options = array(), $html_options = array())
{
    $options = _parse_attributes($options);

    $context = sfContext::getInstance();

    $culture = _get_option($options, 'culture', $context->getUser()->getCulture());

    // set it back for month tag
    $options['culture'] = $culture;

    $I18n_arr = _get_I18n_date_locales($culture);

    $date_seperator = _get_option($options, 'date_seperator', $I18n_arr['date_seperator']);
    $include_blank_month  = array('include_blank' => _get_option($options, 'include_blank_month', false));
    $include_blank_day    = array('include_blank' => _get_option($options, 'include_blank_day', false));
    $include_blank_year   = array('include_blank' => _get_option($options, 'include_blank_year', false));

    $order = _get_option($options, 'order');
    $tags = array();
    if (is_array($order) && count($order) == 3)
    {
        foreach ($order as $v)
        {
            $tags[] = $v[0];
        }
    }
    else
    {
        $tags = $I18n_arr['date_order'];
    }

    $month_name = $name.'[month]';
    $m = select_month_tag($month_name, _parse_value_for_date($value, 'month', 'm'), $options + $include_blank_month, $html_options);

    $day_name = $name.'[day]';
    $d =  select_day_tag($day_name, _parse_value_for_date($value, 'day', 'd'), $options + $include_blank_day, $html_options);

    $year_name = $name.'[year]';
    $y = select_year_tag($year_name, _parse_value_for_date($value, 'year', 'Y'), $options + $include_blank_year, $html_options);

    // we have $tags = array ('m','d','y')
    foreach ($tags as $k => $v)
    {
        // $tags['m|d|y'] = $m|$d|$y
        $tags[$k] = $$v;
    }

    return implode($date_seperator, $tags);
}

function bool_selector($field)
{
    $out = select_tag($field, options_for_select(array('yes' => __('yes'), 'no' => __('no')),
                                                  '', array('include_blank' => true)));
    return $out;
}

function bool_selector_from_list($field, $config, $value)
{
    $list = sfConfig::get($config);
    $title = $list[$value];
    $out  = ucfirst(__($title)) . __('&nbsp;:') . ' ';
    $out .= select_tag($field . '[]', options_for_select(array($value => __('yes'), '!' . $value => __('no')),
                                                  '', array('include_blank' => true)),
                       array('id' => get_id_from_name($field) . '_' . $value));
    return $out;
}

function georef_selector($title = '')
{
    if($title == '')
    {
        $title = 'geom_wkt';
    }
    $out  = __($title) . ' ';
    $out .= bool_selector('geom');
    return $out;
}

function lang_selector($field)
{
    $options = array();
    foreach (sfConfig::get('app_languages_c2c') as $key => $lang)
    {
        $options[$key] = __($lang);
    }
    return select_tag($field, options_for_select($options, '', array('include_blank' => true)));
}

function filter_field($field_name, $field_form, $class='col')
{
    return '<div class="' . $class . '">'
       . __($field_name) . ' '
       . $field_form
       . '</div>';
}
