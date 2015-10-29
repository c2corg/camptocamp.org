<?php

// FIXME : dirty trick
if (isset($sf_user))
{
    // we are in a template
    use_helper('General');
}
else
{
    // we are in an action
    sfLoader::loadHelpers(array('General'));
}

function loadTooltipsViewRessources()
{
    if (!c2cTools::mobileVersion())
    {
        $response = sfContext::getInstance()->getResponse();
        $response->addJavascript('/static/js/tooltips.js', 'last');
    }
}

loadTooltipsViewRessources();

function field_data($document, $name, $options = array())
{
    $value = $document->get($name);
    $title = _option($options, 'title', '');
    
    if (empty($title))
    {
        $title = $name;
    }
    
    return field_data_arg($title, $value, $options);
}

function field_data_arg($name, $value, $options = array())
{
    if (!check_is_numeric_or_text($value))
    {
        $value = '';
    }

    return _format_data($name, $value, $options);
}

function field_data_if_set($document, $name, $options = array())
{
    $value = $document->get($name);
    $title = _option($options, 'title', '');
    
    if (empty($title))
    {
        $title = $name;
    }
    
    return field_data_arg_if_set($title, $value, $options);
}

function field_data_arg_if_set($name, $value, $options = array())
{
    if (!check_is_numeric_or_text($value))
    {
        return '';
    }
    
    return _format_data($name, $value, $options);
}

function field_data_range($document, $name_min, $name_max, $options = array())
{
    $value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    
    return field_data_arg_range($name_min, $name_max, $value_min, $value_max, $options);
}

function field_data_arg_range($name_min, $name_max, $value_min, $value_max, $options = array())
{
    $range_only = _option($options, 'range_only', false);

    $name = $name_min . '_' . $name_max;
    
    $is_not_empty_value_min = check_is_numeric_or_text($value_min);
    $is_not_empty_value_max = check_is_numeric_or_text($value_max);
    
    if (($is_not_empty_value_min && $is_not_empty_value_max) || (($is_not_empty_value_min || $is_not_empty_value_max) && $range_only))
    {
        return _format_data_range($name, $value_min, $value_max, $options);
    }
    else if ($is_not_empty_value_min && !$is_not_empty_value_max)
    {
        return _format_data($name_min, $value_min, $options);
    }
    else if (!$is_not_empty_value_min && $is_not_empty_value_max)
    {
        return _format_data($name_max, $value_max, $options);
    }
    else
    {
        return _format_data($name, '');
    }
}

function field_data_range_if_set($document, $name_min, $name_max, $options = array())
{
    $value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    
    return field_data_arg_range_if_set($name_min, $name_max, $value_min, $value_max, $options);
}

function field_data_arg_range_if_set($name_min, $name_max, $value_min, $value_max, $options = array())
{
    if (empty($value_min) && empty($value_max))
    {
        return '';
    }
    
	return field_data_arg_range($name_min, $name_max, $value_min, $value_max, $options);
}

function field_data_from_list($document, $name, $config, $options = array())
{
    $title = _option($options, 'title', $name);

    return _format_data_from_list($title, $document->getRaw($name), $config, $options);
}

function field_data_from_list_if_set($document, $name, $config, $options = array())
{
    $title = _option($options, 'title', $name);
    $multiple = _option($options, 'multiple', false, false);
    $value = $document->getRaw($name);
    
    if ($multiple)
    {
        $value = is_array($value) ? $value : Document::convertStringToArray($value);
    }
    
    if (!check_list_not_empty($value, $multiple))
    {
        return '';
    }
    
    return _format_data_from_list($title, $value, $config, $options);
}

function field_data_range_from_list($document, $name_min, $name_max, $config, $options = array())
{
    $value_min = $document->getRaw($name_min);
    $value_max = $document->getRaw($name_max);
    $is_not_empty_value_min = check_is_positive($value_min);
    $is_not_empty_value_max = check_is_positive($value_max);
    $range_only = _option($options, 'range_only', false);
    $name_if_equal = _option($options, 'name_if_equal', '');
    $prefix = isset($option['prefix']) ? $option['prefix'] : '';
    $suffix = isset($option['suffix']) ? $option['suffix'] : '';
    $name = $name_min . '_' . $name_max;
    $div_id = null;
    
    if (!empty($name_if_equal) && $value_min == $value_max)
    {
        $div_id = $name;
        $name = $name_if_equal;
    }
    
    if (is_array($prefix))
    {
        $prefix_min = $prefix[0];
        $prefix_max = $prefix[1];
    }
    else
    {
        $prefix_min = $prefix_max = $prefix;
    }
    
    if (is_array($suffix))
    {
        $suffix_min = $suffix[0];
        $suffix_max = $suffix[1];
    }
    else
    {
        $suffix_min = $suffix_max = $suffix;
    }
    
    if (($is_not_empty_value_min && $is_not_empty_value_max) || (($is_not_empty_value_min || $is_not_empty_value_max) && $range_only))
    {
        return _format_data_range_from_list($name, $value_min, $value_max, $config, $options);
    }
    else
    {
        $options['prefix'] = $prefix_min;
        $options['suffix'] = $suffix_min;

        if ($is_not_empty_value_min && !$is_not_empty_value_max)
        {
            return _format_data_from_list($name_min, $value_min, $config, $options);
        }
        else if (!$is_not_empty_value_min && $is_not_empty_value_max)
        {
            return _format_data_from_list($name_max, $value_max, $config, $options);
        }
        else
        {
            return _format_data($name, '', $options);
        }
    }
}

function field_data_range_from_list_if_set($document, $name_min, $name_max, $config, $options = array())
{
    $value_min = $document->get($name_min);
    $value_max = $document->get($name_max);
    
    if (!check_is_positive($value_min) && !check_is_positive($value_max))
    {
        return '';
    }
    
    return field_data_range_from_list($document, $name_min, $name_max, $config, $options);
}

function field_picto_from_list($document, $name, $config, $options = array())
{
    return _format_picto_from_list($name, $document->getRaw($name), $config, $options);
}

function field_picto_from_list_if_set($document, $name, $config, $options = array())
{
    $multiple = _option($options, 'multiple', false, false);
    $value = $document->getRaw($name);
    
    if ($multiple)
    {
        $value = is_array($value) ? $value : Document::convertStringToArray($value);
    }
    
    if (!check_list_not_empty($value, $multiple))
    {
        return '';
    }

    return _format_picto_from_list($name, $value, $config, $options);
}

function field_activities_data($document, $options = array())
{
    $options['multiple'] = true;
    $options['picto_name'] = 'activity';
    $options['picto_separator'] = ' ';
    $options['text_separator'] = ', ';

    return field_picto_from_list($document, 'activities', 'app_activities_list', $options);
}

function field_activities_data_if_set($document, $raw = false, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'activities', 'app_activities_list',
        array('multiple' => true, 'raw' => $raw, 'picto_name' => 'activity',
        'picto_separator' => ' ', 'text_separator' => ' - ', 'prefix' => $prefix, 'suffix' => $suffix));
}

function _activities_data($activities, $picto_separator = ' ')
{
    return _format_picto_from_list('activities', $activities, 'app_activities_list',
        array('multiple' => true, 'raw' => true, 'picto_name' => 'activity',
        'picto_separator' => $picto_separator, 'text_separator' => ' - '));
}

function field_pt_picto_if_set($document, $raw = false, $prefix = '', $suffix = '', $show_if_empty = true)
{
    // special handling for cablecar, change label if it is the only selected option
    $options = array('multiple' => true, 'raw' => $raw, 'picto_name' => 'pt', 'picto_separator' => ' ',
                     'text_separator' => ', ', 'prefix' => $prefix, 'suffix' => $suffix, 'show_if_empty' => $show_if_empty);
    if (!$raw)
    {
        $value = $document->getRaw('public_transportation_types');
        if (($key = array_search("0", $value)) !== false) unset($value[$key]); // on display, changed by symfony
        if (($key = array_search("", $value)) !== false) unset($value[$key]); // on preview

        if (sizeof($value) === 1 && reset($value) === "9")
        {
            $options['label'] = 'access deserved by:';
        }
    }
    return field_picto_from_list_if_set($document, 'public_transportation_types', 'app_parkings_public_transportation_types',
        $options);
}

function _pt_picto_if_set($pt_types)
{
    return _format_picto_from_list('public_transportation_types', $pt_types, 'app_parkings_public_transportation_types',
        array('multiple' => true, 'raw' => true, 'picto_name' => 'pt',
        'picto_separator' => ' ', 'text_separator' => ', '));
}

function field_frequentation_picto_if_set($document, $raw = false, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'frequentation_status', 'mod_outings_frequentation_statuses_list',
        array('multiple' => false, 'raw' => $raw, 'picto_name' => 'freq', 'picto_separator' => ' ',
        'text_separator' => ', ', 'prefix' => $prefix, 'suffix' => $suffix));
}

function _frequentation_picto_if_set($frequentation)
{
    return _format_picto_from_list('frequentation_status', $frequentation, 'mod_outings_frequentation_statuses_list',
        array('multiple' => false, 'raw' => true, 'picto_name' => 'freq'));
}

function field_conditions_picto_if_set($document, $raw = false, $prefix = '', $suffix = '')
{
    return field_picto_from_list_if_set($document, 'conditions_status', 'mod_outings_conditions_statuses_list',
        array('multiple' => false, 'raw' => $raw, 'picto_name' => 'cond', 'picto_separator' => ' ', 'text_separator' => ', ',
        'prefix' => $prefix, 'suffix' => $suffix));
}

function _conditions_picto_if_set($conditions)
{
    return _format_picto_from_list('conditions_status', $conditions, 'mod_outings_conditions_statuses_list',
        array('multiple' => false, 'raw' => true, 'picto_name' => 'cond'));
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

function field_semantic_date_data($document, $name)
{
    use_helper('Date');
    return '<time datetime="'.$document->get($name).'">'.format_date($document->get($name), 'D').'</time>';
}

/**
 * Display the value of a boolean field
 *
 * null_equals_no : use this if a null value equals a 'no'
 * show_only_yes : use this if you only want to display the field if it is true
 */
function field_bool_data($document, $name, $options = array())
{
    $value = $document->get($name);
    return _format_bool_data($name, $value, $options);
}

function field_bool_data_from_list($document, $name, $config, $options = array())
{
    $new_items = _option($options, 'new_items', array());
    $single_value = _option($options, 'single_value', 0);

    $value = $document->getRaw($name);
    $list = sfConfig::get($config);
    if (count($new_items))
    {
        foreach ($new_items as $key => $item)
        {
            $list[$key] = $item;
        }
    }
    if ($single_value)
    {
        $single_list = array();
        $single_list[$single_value] = $list[$single_value];
        $list = $single_list;
    }
    
    if (!empty($value))
    {
        $value = is_array($value) ? $value : Document::convertStringToArray($value);
        $result = array();
        foreach ($list as $key => $item)
        {
            $value_key = (in_array($key, $value) ? 1 : 0);
            $result[] = _format_bool_data($item, $value_key, $options);
        }
        $result = implode(' ', $result);
    }
    else
    {
        $result = '';
    }
    
    return $result;
}

function _format_bool_data($name, $value, $options = array())
{
    $null_equals_no = _option($options, 'null_equals_no', false);
    $show_only_yes = _option($options, 'show_only_yes', false);

    if (is_null($value))
    {
        if ($null_equals_no)
        {
            $value = 0;
        }
        else
        {
            return '';
        }
    }

    if (!$value && $show_only_yes)
    {
        return '';
    }

    $value = (bool)$value ? __('yes') : __('no');
    $options['name_suffix'] = __('&nbsp;:');
    return _format_data($name, $value, $options);
}

function _format_data($name, $value, $options = array())
{
    $raw = _option($options, 'raw', false);
    $id = _option($options, 'id', $name);
    $prefix = _option($options, 'prefix', '');
    $suffix = _option($options, 'suffix', '');
    $name_suffix = _option($options, 'name_suffix', '');
    $microdata = _option($options, 'microdata', null);
    $show_if_empty = _option($options, 'show_if_empty', true);
    $label = _option($options, 'label', $name);
    
    $is_not_empty_value = check_is_numeric_or_text($value);
    
    if (!$is_not_empty_value)
    {
        if (!$show_if_empty)
        {
            return '';
        }

        $value = '<span class="default_text">' . __('nonwell informed') . '</span>';
        $div_class = ' default_text';
    }
    else
    {
        $div_class = '';

        if ($microdata)
        {
            is_string($microdata) ? $value = content_tag('span', $value, array('itemprop' => $microdata)) :
                                    $value = content_tag(_option($microdata, 'tag', 'span'), $value, $microdata);
        }
    }

    $text = ($raw) ? '' : content_tag('div', ucfirst(__($name)) . $name_suffix,
        array('class' => 'section_subtitle' . $div_class, 'id' => '_' . $id, 'data-tooltip' => ''));
    $text .= ' ';



    if (!empty($prefix) && $is_not_empty_value)
    {
        $text .= __($prefix);
    }
    
    $text .= $value;

    if (!empty($suffix) && $is_not_empty_value)
    {
        $text .= __($suffix);
    }

    return $text;
}

function _format_data_range($name, $value_min, $value_max, $options = array())
{
    $raw = _option($options, 'raw', false);
    $separator = _option($options, 'separator', ' / ');
    $prefix_min = _option($options, 'prefix_min', '');
    $prefix_max = _option($options, 'prefix_max', '');
    $suffix = _option($options, 'suffix', '');

    if ($raw)
    {
        $text = '';
    }
    else
    {
        $text = content_tag('div', __($name), array('class' => 'section_subtitle',
            'id' => '_'.$name, 'data-tooltip' => '')) . ' ';
    }
    
    $is_not_empty_value_min = check_is_numeric_or_text($value_min);
    $is_not_empty_value_max = check_is_numeric_or_text($value_max);
    
    if ($is_not_empty_value_min && $is_not_empty_value_max && $value_min == $value_max)
    {
        $text .= $value_min;
        
        if (!empty($suffix))
        {
            $text .= __($suffix);
        }
    }
    else
    {
        if ($is_not_empty_value_min)
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
        
        if ($is_not_empty_value_min && $is_not_empty_value_max)
        {
            $text .= __($separator);
        }

        if ($is_not_empty_value_max)
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

function _format_data_from_list($name, $value, $config, $options = array())
{
    $new_items = _option($options, 'new_items', array());
    $multiple = _option($options, 'multiple', false);
    $ifset = _option($options, 'ifset', false);

    $list = sfConfig::get($config);
    if (count($new_items))
    {
        foreach ($new_items as $key => $item)
        {
            $list[$key] = $item;
        }
    }
    
    $is_not_empty_value = !empty($value);

    if ($is_not_empty_value)
    {
        if ($multiple)
        {
            $value = is_array($value) ? $value : Document::convertStringToArray($value);
            $value_tmp = array();
            foreach ($value as $item)
            {
                $value_tmp[] = _get_field_value_in_list($list, $item);
            }
            $value = array_filter($value_tmp);
            $value = implode(', ', $value);
        }
        else
        {
            $value = _get_field_value_in_list($list, $value);
        }
    }
    else
    {
        $value = '';
    }
    
    if ($ifset && !$is_not_empty_value)
    {
        return '';
    }
    else
    {
        return _format_data($name, $value, $options);
    }
}

function _format_data_range_from_list($name, $value_min, $value_max, $config, $options = array())
{
    $list = sfConfig::get($config);
    $value = '';
    $prefix = _option($options, 'prefix', '');
    $suffix = _option($options, 'suffix', '');
    $separator = _option($options, 'separator', ' / ');

    if (is_array($prefix))
    {
        $prefix_min = $prefix[0];
        $prefix_max = $prefix[1];
    }
    else
    {
        $prefix_min = $prefix_max = $prefix;
    }
    
    if (is_array($suffix))
    {
        $suffix_min = $suffix[0];
        $suffix_max = $suffix[1];
    }
    else
    {
        $suffix_min = $suffix_max = $suffix;
    }
    
    $is_not_empty_value_min = check_is_positive($value_min);
    $is_not_empty_value_max = check_is_positive($value_max);
    
    if ($is_not_empty_value_min)
    {
        $value .= $prefix_min . _get_field_value_in_list($list, $value_min) . $suffix_min;
    }
    
    if (!$is_not_empty_value_min || !$is_not_empty_value_max || $value_min != $value_max)
    {
        if ($is_not_empty_value_min && $is_not_empty_value_max)
        {
            $value .= __($separator);
        }
    	
        if ($is_not_empty_value_max)
        {
            $value .= $prefix_max . _get_field_value_in_list($list, $value_max) . $suffix_max;
        }
    }

    return _format_data($name, $value, $options);
}

function _format_picto_from_list($name, $value, $config, $options = array())
{
    if (!empty($value))
    {
        $multiple = _option($options, 'multiple', false);
        $picto_name = _option($options, 'picto_name', '');
        $picto_separator = _option($options, 'picto_separator', '');

        $html = array();
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

        $print_separator_counter = 0;
        foreach ($value as $picto_id)
        {
            if (!$picto_id || $picto_id == '0' || !isset($list[$picto_id]))
            {
                continue;
            }

            $picto_text = __($list[$picto_id]);

            $print_sep = '';
            if ($print_separator_counter === 0)
            {
                $print_separator_counter = 1;
            }
            else
            {
                $print_sep = ' sep';
            }

            $html[] = '<span class="picto '.$picto_name.'_'.$picto_id.$print_sep.'" title="'.$picto_text.'"></span>';
            $picto_text_list[] = $picto_text;
        }
        $html = implode($picto_separator, $html);
    }
    else
    {
        $html = '';
    }

    return _format_data($name, $html, $options);
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
    $has_inserted_text = isset($options['inserted_text']) && !empty($options['inserted_text']);
    if (empty($value) && !$has_inserted_text)
    {
        return '';
    }

   return _format_text_data($name, $document->get($name), $label, $options);
}

function _format_text_data($name, $value, $label = NULL, $options = array())
{
    use_helper('sfBBCode', 'SmartFormat');

    if (empty($label))
    {
        $label = $name;
    }

    $has_value = !empty($value);
    $needs_translation = _option($options, 'needs_translation', false);
    $inserted = _option($options, 'inserted_text', '');
    $images = _option($options, 'images', null);
    $filter_image_type = _option($options, 'filter_image_type', true);
    $show_label = _option($options, 'show_label', true);
    $show_images = _option($options, 'show_images', true);
    $class = _option($options, 'class', '');
    $label_id = _option($options, 'label_id', $name);

    if (!empty($class))
    {
        $class = ' ' . $class;
    }

    if ($show_label)
    {
        $label = content_tag('div', __($label), array('class' => 'section_subtitle htext',
            'id' => '_'.$label_id, 'data-tooltip' => '')) . "\n";
    }
    else
    {
        $label = '';
    }
    
    $out = $label . $inserted;
    if ($has_value)
    {
        $lang = $needs_translation ? ' lang="' . $needs_translation . '"' : '';
        $out .= '<div class="field_value"' . $lang . '>'
              . parse_links(parse_bbcode($value, $images, $filter_image_type, $show_images))
              . '</div>';
    }

    return $out;
}

function field_url_data($document, $name, $options = array())
{
    $link_text = _option($options, 'link_text', '');
    $ifset = _option($options, 'ifset', false);
    $microdata = _option($options, 'microdata');

    $value = $document->get($name);
    if ($value)
    {
        if (empty($link_text))
        {
            $displayvalue = (strlen($value) > 50) ? substr($value, 0 , 35).' &hellip; '.substr($value, -9) : $value;
        }
        else
        {
            $displayvalue = $link_text;
        }
        $itemprop = empty($microdata) ? '' : ' itemprop="'.$microdata.'"';
        $value = '<a' . $itemprop . ' href="' . $value . '">' . $displayvalue . '</a>';
    }
    elseif ($ifset)
    {
        return '';
    }

    return  _format_data($name, $value, $options);
}

function field_url_data_if_set($document, $name, $options = array())
{
    $options['ifset'] = true;
    return field_url_data($document, $name, $options);
}

function field_phone($document, $name, $ifset = false, $options = array())
{
    use_helper('Link');

    $value = $document->get($name);
    if ($value)
    {
        $value = phone_link($value);
    }
    elseif ($ifset)
    {
        return '';
    }

    return  _format_data($name, $value, $options);
}

function field_phone_if_set($document, $name, $options)
{
    return field_phone($document, $name, true, $options);
}

function field_export($module, $id, $lang, $version = null)
{
    $route_suffix = !empty($version) ? "_version?version=$version&" : '?';
    $route_suffix .= "module=$module&id=$id&lang=$lang";
                  
    $title = 'download geo data under %1% format';
    $result = '<div class="no_print">' . content_tag('span', __('Export:'), array('class' => 'section_subtitle',
               'id' => 'geo_export', 'data-tooltip' => ''))
           . ' ' . picto_tag('action_gps') . ' ' .
           link_to('GPX', "@export_gpx$route_suffix",
                   array('title' => __($title, array('%1%' => 'GPX')), 'rel' => 'nofollow'));
    
    if (!c2cTools::mobileVersion())
    {
        $result .= ' ' . picto_tag('action_kml') . ' ' .
           link_to('KML', "@export_kml$route_suffix",
                   array('title' => __($title, array('%1%' => 'KML')), 'rel' => 'nofollow'))
           . ' ' . picto_tag('action_json') . ' ' .
           link_to('JSON', "@export_json$route_suffix",
                   array('title' => __($title, array('%1%' => 'JSON')), 'rel' => 'nofollow'));
    }
    
    $result .= '</div>';
    
    return $result;
}

function field_getdirections($id)
{
    $title = 'Use %1% to see directions to this parking';
    return '<div class="no_print">' . content_tag('span', __('Get directions:'), array('class' => 'section_subtitle htext',
               'id' => 'get_directions', 'data-tooltip' => ''))
           . ' ' .
           link_to('Google', "@getdirections?id=$id&service=gmaps",
                   array('title' => __($title, array('%1%' => 'Google Maps')),
                         'class' => 'external_link', 'rel' => 'no-follow'))
           . ' ' .
           link_to('Yahoo!', "@getdirections?id=$id&service=yahoo",
                   array('title' => __($title, array('%1%' => __('Yahoo! Maps'))),
                         'class' => 'external_link', 'rel' => 'no-follow'))
           . ' ' .
           link_to('Bing Maps', "@getdirections?id=$id&service=livesearch",
                   array('title' => __($title, array('%1%' => 'Bing Maps')),
                         'class' => 'external_link', 'rel' => 'no-follow'))
           . ' ' .
           link_to('OSRM', "@getdirections?id=$id&service=osrm",
                   array('title' => __($title, array('%1%' => __('OSRM'))),
                         'class' => 'external_link', 'rel' => 'no-follow')) . '</div>';
}

function field_coord_data_if_set($document, $name, $options = array()) 
{
    $microdata = _option($options, 'microdata', null);

    $raw_value = $document->get($name);
    if (empty($raw_value))
    {   
        return ''; 
    }

    switch ($name)
    {
        case 'lat':
            $suffix = ($raw_value < 0) ? '°S' : '°N';
            break;

        case 'lon':
            $suffix = ($raw_value < 0) ? '°W' : '°E';
            break;

        default:
            $suffix = '';
    }

    $value = abs($raw_value);
    $deg = floor($value);
    $minTemp = 60 * ($value - $deg);
    $min = floor($minTemp);
    $sec = floor(60 * 100 * ($minTemp - $min)) /100;
    $value = $deg . '° ' . $min . "' " . $sec . '" ' . str_replace('°', '', $suffix);

    return _format_data($name, $value) . ($microdata ? microdata_meta($microdata, $raw_value) : '');
}

function field_swiss_coords($document)
{
    if (!$document->get('lat') || !isset($document->associated_areas)) return '';

    $isSwiss = false;
    foreach ($document->associated_areas as $area)
    {
        if ($area['id'] == 14067) // 14067 = id of Switzerland document
        {
            $isSwiss = true;
            break;
        }
    }
    // only document located in Switzerland are concerned
    if (!$isSwiss) return '';

    list($x, $y) = c2cTools::WGS84toCH1903($document->get('lat'), $document->get('lon'));
    $value = sprintf('%d / %d [<a href="http://map.geo.admin.ch/?X=%d&amp;Y=%d&amp;zoom=6&amp;crosshair=cross">%s</a>]',
                      $y, $x, $x, $y, __('map'));
    return _format_data('swiss coords', $value);
}

function field_exposure_time_if_set($document, $name = 'exposure_time', $prefix = '1/', $suffix = 's')
{
    $value = $document->get($name);
    if (empty($value))
    {
        return '';
    }

    return _format_data($name, round(1/$value), array('prefix'=>$prefix, 'suffix'=>$suffix));
}

function field_image_details($document)
{
    $size = $document->get('file_size');
    $width = $document->get('width');
    $height = $document->get('height');

    // old images don't have these values in the db
    if ($size == null || $width == null) return '';

    $hsize = ($size >= 1048576) ? round($size / 1048576, 2) : round($size / 1024);
    return _format_data('image_details', __(($size >= 1048576) ? '%1% x %2% px, %3% Mo' : '%1% x %2% px, %3% Ko', 
                                            array('%1%' => $width,
                                                  '%2%' => $height,
                                                  '%3%' => $hsize)));
}

function field_months_data($document, $name)
{
    use_helper('DateForm');

    $months = $document->getRaw($name);

    $I18n_arr = _get_I18n_date_locales(sfContext::getInstance()->getUser()->getCulture());
    $month_names = $I18n_arr['dateFormatInfo']->getMonthNames();

    if (is_array($months))
    {
        if (count($months) == 12)
        {
            $value = __('all months');
        }
        else
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
    }
    else
    {
        $months--;
        $value = array_key_exists($months, $month_names) ? $month_names[$months] : '';
    }

    return _format_data($name, $value);
}

// This function outputs a string composed of all ratings data available for the given route.
function field_route_ratings_data($document, $show_activities = true, $add_tooltips = false, $use_esc_raw = false, $format = 'html', $avalaible_activities = null)
{
    $activities =  isset($document['activities']) ?
        Document::convertStringToArray($document['activities']) : $document->get('activities', ESC_RAW);


    return _route_ratings_sum_up(
        $format,
        $activities,
        $avalaible_activities,
        $show_activities,
        _filter_ratings_data($document, 'global_rating', 'globalRating', 'app_routes_global_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'engagement_rating', 'engagementRating', 'app_routes_engagement_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'objective_risk_rating', 'objectiveRiskRating', 'app_routes_objective_risk_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'toponeige_technical_rating', 'toponeigeTechnicalRating', 'app_routes_toponeige_technical_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'toponeige_exposition_rating', 'toponeigeExpositionRating', 'app_routes_toponeige_exposition_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'labande_ski_rating', 'labandeSkiRating', 'app_routes_labande_ski_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'labande_global_rating', 'labandeGlobalRating', 'app_routes_global_ratings', $format, $add_tooltips),
        _filter_ratings_rock($document, $format, $add_tooltips, false, null, $use_esc_raw),
        _filter_ratings_data($document, 'ice_rating', 'iceRating', 'app_routes_ice_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'mixed_rating', 'mixedRating', 'app_routes_mixed_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'aid_rating', 'aidRating', 'app_routes_aid_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'rock_exposition_rating', 'rockExpositionRating', 'app_routes_rock_exposition_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'equipment_rating', 'equipmentRating', 'app_equipment_ratings_list', $format, $add_tooltips, false, null, null, 'app_equipment_ratings_tooltips'),
        _filter_ratings_data($document, 'hiking_rating', 'hikingRating', 'app_routes_hiking_ratings', $format, $add_tooltips),
        _filter_ratings_data($document, 'snowshoeing_rating', 'snowshoeingRating', 'app_routes_snowshoeing_ratings', $format, $add_tooltips)
        );
}

function _filter_ratings_data($document, $name, $json_name, $config, $format, $add_tooltips = false, $use_raw_value = false, $raw_value_prefix = null, $alternate_name = null, $tooltip_config = null)
{
    $raw_value = !empty($document[$name]) ? $document[$name] : $document->get($name, 'ESC_RAW');
    $value = _get_field_value_in_list(sfConfig::get($config), $raw_value);

    if (empty($value))
    {
        if ($format == 'json' || $format == 'jsonkeys')
        {
            return array();
        }
        else
        {
            return null;
        }
    }
    $string_value = $use_raw_value ? $raw_value_prefix . $raw_value : $value;
    if ($format == 'json')
    {
        return array($json_name => $string_value);
    }
    elseif ($format == 'jsonkeys')
    {
        return array($json_name => $raw_value);
    }
    elseif ($add_tooltips)
    {
        if (!empty($tooltip_config))
        {
            $tooltip_value = _get_field_value_in_list(sfConfig::get($tooltip_config), $raw_value);
        }
        else
        {
            $tooltip_value = $value;
        }
        $string_value = '<span title="'.__(empty($alternate_name) ? $name : $alternate_name).' '.$tooltip_value.'">'.$string_value.'</span>';
    }
    return $string_value;
}

function _filter_ratings_rock($document, $format = 'html', $add_tooltips = false, $use_raw_value = false, $raw_value_prefix = null, $use_esc_raw = false)
{
    $rock_free_name = 'rock_free_rating';
    $rock_free_json_name = 'rockFreeRating';
    $rock_free_config = 'app_routes_rock_free_ratings';
    $rock_free_raw_value = (is_int($document[$rock_free_name])) ? $document[$rock_free_name] : 
                           ($use_esc_raw ? $document->get($rock_free_name, 'ESC_RAW') : $document->getRaw($rock_free_name));

    $rock_required_name = 'rock_required_rating';
    $rock_required_json_name = 'rockRequiredRating';
    $rock_required_config = 'app_routes_rock_free_ratings';
    $rock_required_raw_value = (is_int($document[$rock_required_name])) ? $document[$rock_required_name] :
                               ($use_esc_raw ? $document->get($rock_required_name, 'ESC_RAW') : $document->getRaw($rock_required_name));

    if ($format == 'html' || $format == 'table')
    {
        if (!check_is_positive($rock_free_raw_value)) return null;

        if (check_is_positive($rock_required_raw_value) && ($rock_required_raw_value == $rock_free_raw_value))
        {
            $alternate_name = 'rock_free_and_required_rating';
        }
        else
        {
            $alternate_name = null;
        }
        $string_rock_free_value =  _filter_ratings_data($document, $rock_free_name, $rock_free_json_name, $rock_free_config, $format, $add_tooltips, $use_raw_value, $raw_value_prefix, $alternate_name);

        if (check_is_positive($rock_required_raw_value) && ($rock_required_raw_value != $rock_free_raw_value))
        {
            $string_rock_required_value = '>' .  _filter_ratings_data($document, $rock_required_name, $rock_required_json_name, $rock_required_config, $format, $add_tooltips, $use_raw_value, $raw_value_prefix);
        }
        else
        {
            $string_rock_required_value = null;
        }

        return $string_rock_free_value . $string_rock_required_value;
    }
    elseif ($format == 'json' || $format == 'jsonkeys')
    {
        $rock_free_value =  _filter_ratings_data($document, $rock_free_name, $rock_free_json_name, $rock_free_config, $format, false, $use_raw_value, $raw_value_prefix);
        $rock_required_value = _filter_ratings_data($document, $rock_required_name, $rock_required_json_name, $rock_required_config, $format, false, $use_raw_value);
        
        return array_merge($rock_free_value, $rock_required_value);
    }
}

function _route_ratings_sum_up($format = 'html', $activities = array(), $avalaible_activities = null, $show_activities = true,
             $global, $engagement, $objective_risk, $topo_ski, $topo_exp, $labande_ski, $labande_global,
             $rock_free_and_required, $ice, $mixed, $aid, $rock_exposition, $equipment, $hiking, $snowshoeing)
{
    if ($format == 'html' || $format == 'table')
    {
        $act_filter_enable = is_array($avalaible_activities);
        $groups = $ski1 = $ski2 = $climbing1 = $climbing2 = $climbing3 = $climbing4 = $climbing5 = array();

        if ($topo_ski) $ski1[] = $topo_ski;
        if ($topo_exp) $ski1[] = $topo_exp;
        if ($labande_global) $ski2[] = $labande_global;
        if ($labande_ski) $ski2[] = $labande_ski;
        if ($global) $climbing1[] = $global;
        if ($engagement) $climbing2[] = $engagement;
        if ($objective_risk) $climbing2[] = $objective_risk;
        if ($equipment) $climbing3[] = $equipment;
        if ($rock_exposition) $climbing3[] = $rock_exposition;
        if ($aid) $climbing4[] = $aid;
        if ($rock_free_and_required) $climbing4[] = $rock_free_and_required;
        if ($ice) $climbing5[] = $ice;
        if ($mixed) $climbing5[] = $mixed;

        if ((!$act_filter_enable || array_intersect(array(1), $avalaible_activities)) && $ski_activities = array_intersect(array(1), $activities))
        {
            if ($show_activities)
            {
                $groups[] = _activities_data($ski_activities);
            }
            $groups[] = implode('/', $ski1);
            $groups[] = implode('/', $ski2);
        }
        if ((!$act_filter_enable || array_intersect(array(2,3,4,5), $avalaible_activities)) &&
            $climbing_activities = array_intersect(array(2,3,4,5), $activities))
        {
            if ($show_activities)
            {
                $groups[] = _activities_data($climbing_activities, '&nbsp;');
            }

            for ($i = 1; $i <= 5; $i++)
            {
                $groups[] = implode('/', ${'climbing' . $i});
            }
        }
        if ((!$act_filter_enable || array_intersect(array(6), $avalaible_activities)) && $hiking_activities = array_intersect(array(6), $activities))
        {
            if ($show_activities)
            {
                $groups[] = _activities_data($hiking_activities);
            }
            $groups[] = $hiking;
        }
        if ((!$act_filter_enable || array_intersect(array(7), $avalaible_activities)) && $snowshoeing_activities = array_intersect(array(7), $activities))
        {
            if ($show_activities)
            {
                $groups[] = _activities_data($snowshoeing_activities);
            }
            $groups[] = $snowshoeing;
        }
        if ($format == 'html')
        {
            return implode(' ', $groups);
        }
        else
        {
            return '<td>'
                 . implode('</td><td>', $groups)
                 . '</td>';
        }
    }
    elseif ($format == 'json' || $format == 'jsonkeys')
    {
        return array_merge($global, $engagement, $objective_risk, $topo_ski, $topo_exp, $labande_ski, $labande_global,
                           $rock_free_and_required, $ice, $mixed, $aid, $equipment, $hiking, $snowshoeing);
    }
}

function li($content, $options = array())
{
    if (!empty($content))
    {
        echo content_tag('li', $content, $options);
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

function disp_moderator($status)
{
    li(_format_data('moderator status', __($status)));
}

function conditions_levels_data($conditions_levels)
{
    $level_fields = sfConfig::get('mod_outings_conditions_levels_fields');
    
    $html = '<table id="conditions_levels_table">';
    
    foreach ($level_fields as $field)
    {
        $html .= '<colgroup id="' . $field . '"></colgroup>';
    }
    
    $html .= '<thead><tr>';
    foreach ($level_fields as $field)
    {
        $html .= '<th>' . __($field) . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    foreach ($conditions_levels as $level => $data)
    {
        $html .= '<tr>';
        foreach ($level_fields as $field)
        {
            $html .= '<td>' . $data[$field] . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    
    return $html;
}

function simple_data($name, $value, $suffix = '')
{
    if (!check_not_empty($value)) return '';

    if (!empty($suffix)) $suffix = __($suffix);

    return '<em>' . __($name) . '</em> ' . $value . $suffix;
}

function check_not_empty_doc($document, $name)
{
    $value = $document->get($name);
    return check_is_numeric($value);
}

function summarize_route($route, $show_activities = true, $add_tooltips = false, $avalaible_activities = null, $list_format = true)
{
    $max_elevation = is_scalar($route['max_elevation']) ? ($route['max_elevation'] . __('meters')) : NULL;
    
    $height_diff_up = is_scalar($route['height_diff_up']) ? ($route['height_diff_up'] . __('meters')) : NULL;
    if (is_scalar($route['difficulties_height']))
    {
        $difficulties_height = $route['difficulties_height'] . __('meters');
    }
    else
    {
        $difficulties_height = NULL;
    }

    $facing = field_data_from_list_if_set($route, 'facing', 'app_routes_facings', array('raw' => true));

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
    
    $ratings = field_route_ratings_data($route, $show_activities, $add_tooltips, false, ($list_format ? 'html' : 'table'), $avalaible_activities);
    
    if ($list_format)
    {
        $height = implode(' ', $height);

        $route_data = array($max_elevation,
                            $height,
                            $facing,
                            $ratings
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
    }
    else
    {
        $route_data = array($max_elevation,
                            $height_diff_up,
                            $difficulties_height,
                            $facing);
        $route_data = '<td>'
                    . implode('</td><td>', $route_data)
                    . '</td>'
                    . $ratings;
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


function format_book_data($books, $type, $main_id, $is_moderator = false)
{
    $type_list = $type . '_list';
    $module = 'books';
    $main_module = c2cTools::Letter2Module(substr($type,1,1));
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
        $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $book['culture'] . '&slug=' . make_slug($book['name']);
        $html .= link_to($name, $url);
        if (isset($book['author']) && trim($book['author']) != '')
        {
            $html .= ' - ' . $book['author'];
        }
        if (isset($book['publication_date']) && trim($book['publication_date']) != '')
        {
            $html .= ' - ' . $book['publication_date'];
        }
        if ($is_moderator && $main_id && !c2cTools::mobileVersion())
        {
            $html .= c2c_link_to_delete_element($type, $doc_id, $main_id, false, $strict);
        }
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

function avalanche_link($id, $name, $date = null)
{
    if ($date)
    {
        $day = format_date($date, 'yyyyMMdd');
        $archive_old_limit_list = sfConfig::get('app_areas_avalanche_archive_old_limit');
        if (isset($archive_old_limit_list[$id]) && $day <= $archive_old_limit_list[$id])
        {
            $url_list = sfConfig::get('app_areas_avalanche_archive_url_old');
        }
        else
        {
            $url_list = sfConfig::get('app_areas_avalanche_archive_url');
        }
    }
    else
    {
        $url_list = sfConfig::get('app_areas_avalanche_url');
    }

    $areas = array_keys($url_list);
    if (!in_array($id, $areas))
    {
        return '';
    }
    
    $country_url_list = sfConfig::get('app_areas_avalanche_country_url');
    $countries = array_keys($country_url_list);
    $url = $url_list[$id];
    if (in_array($url, $countries))
    {
        $suffix_list = sfConfig::get('app_areas_suffix_' . $url);
        $url = $country_url_list[$url] . $suffix_list[$id];
    }
    $url = 'http://' . $url;
    
    // Swiss bulletin
    if ($id == 14067)
    {
        $lang = strtoupper(sfContext::getInstance()->getUser()->getCulture());
        if (in_array($lang, array('CA', 'ES')))
        {
            $lang = 'EN';
        }
        elseif ($lang == 'EU')
        {
            $lang = 'FR';
        }
        
        if (!$date)
        {
            $url .= $lang;
        }
        else
        {
            use_helper('Date');
            $lang = strtolower($lang);
            $year = date('Y', $date);
            $month = date('n', $date);
            if ($month >= 10)
            {
                $year += 1;
            }
            $url = sprintf($url, $year, $lang, $day, $lang);
        }
    }
    
    return link_to($name, $url);
}

function weather_link($id, $name)
{
    $name_list = sfConfig::get('app_areas_weather_name');
    $url_list = sfConfig::get('app_areas_weather_url');
    $areas = array_keys($url_list);
    $weather_names = $urls = array();
    if (in_array($id, $areas))
    {
        $weather_name = $name_list[$id];
        $url = $url_list[$id];
        if (!is_array($weather_name))
        {
            $weather_names[] = $weather_name;
            $urls[] = $url;
        }
        else
        {
            foreach ($weather_name as $name_temp)
            {
                $weather_names[] = $name_temp;
            }
            foreach ($url as $url_temp)
            {
                $urls[] = $url_temp;
            }
        }
    }
    
    $country_name_list = sfConfig::get('app_areas_weather_country_name');
    $country_url_list = sfConfig::get('app_areas_weather_country_url');
    foreach ($country_url_list as $country => $country_url)
    {
        $suffix_list = sfConfig::get('app_areas_suffix_' . $country);
        $areas = array_keys($suffix_list);
        if (in_array($id, $areas))
        {
            $weather_names[] = $country_name_list[$country];
            $urls[] = $country_url . $suffix_list[$id];
            break;
        }
    }
    
    if (empty($weather_names))
    {
        return array('', '');
    }
    elseif (count($weather_names) == 1)
    {
        return array('', link_to($name, 'http://' . $urls[0]));
    }
    else
    {
        $title = '<span class="title_inline">' . $name . __('&nbsp;:') . '</span> ';
        $out = array();
        foreach ($weather_names as $key => $weather_name)
        {
            $weather_names[$key] = link_to(__($weather_name), 'http://' . $urls[$key]);
        }
        return array($title, implode(' ', $weather_names));
    }
}

// generate weather links with coordinate criteria
function weather_coord_link($lat, $lon, $alti, $lang)
{
    $name_list = sfConfig::get('app_areas_weather_coord_name');
    $url_list = sfConfig::get('app_areas_weather_coord_url');
    $lang_list = sfConfig::get('app_areas_weather_coord_lang');
    $weather_names = $urls = array();
    
    foreach ($name_list as $key => $weather_name)
    {
        $weather_names[] = $weather_name;
        $lang = $lang_list[$key][$lang];
        $url = $url_list[$key];
        $urls[] = str_replace(array('%lat%', '%lon%', '%alti%', '%lang%'), array($lat, $lon, $alti, $lang), $url);
    }
    
    if (empty($weather_names))
    {
        return array('', '');
    }
    else
    {
        $out = array();
        foreach ($weather_names as $key => $weather_name)
        {
            $weather_names[$key] = link_to(__($weather_name), 'http://' . $urls[$key], array('rel' => 'nofollow'));
        }
        return array('', implode(' ', $weather_names));
    }
}

// insert a microdata as meta tag
// use sparingly and when info is not displayed
function microdata_meta($itemprop, $content)
{
    return tag('meta', array('itemprop' => $itemprop, 'content' => $content));
}
