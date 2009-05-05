<?php
/**
 * $Id: FilterFormHelper.php 2538 2007-12-20 16:08:35Z alex $
 */

use_helper('Form', 'Javascript');

function elevation_selector($fieldname, $unit = 'meters')
{
    $option_tags = options_for_select(array('0' => '',
                                            '1' => __('greater than'),
                                            '2' => __('lower than'),
                                            '3' => __('between'),
                                            '-' => __('nonwell informed'))
                                     );
    $out = select_tag($fieldname . '_sel', $option_tags,
                      array('onchange' => "update_on_select_change('$fieldname', 3)"));
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
                                            '-' => __('nonwell informed'))
                                     );
    $out = select_tag($fieldname . '_sel', $option_tags,
                      array('onchange' => "update_on_select_change('$fieldname', 3)"));
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

function update_on_select_change()
{
    return javascript_tag(
'function update_on_select_change(field, optionIndex)
{
    index = $(field + \'_sel\').options.selectedIndex;
    if (index == \'0\' || index > optionIndex)
    {
        $(field + \'_span1\').hide();
        $(field + \'_span2\').hide();
    }
    else
    {
        $(field + \'_span1\').show();
        if (index == optionIndex)
        {
            $(field + \'_span2\').show();
        }
        else
        {
            $(field + \'_span2\').hide();
        }
    }
}'
    );
}

function facings_selector($fieldname)
{
    $option_tags = options_for_select(array('0' => '',
                                            '=' => __('equal'),
                                            '~' => __('between'),
                                            '-' => __('nonwell informed'))
                                     );     
    $out = select_tag($fieldname . '_sel', $option_tags,
                      array('onchange' => "update_on_select_change('$fieldname', 2)"));
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

function activities_selector($onclick = false)
{
    $out = array();
    foreach (sfConfig::get('app_activities_list') as $activity_id => $activity)
    {
        if ($activity_id == 0) continue;
        $options = $onclick ? array('onclick' => "hide_unrelated_filter_fields($activity_id)")
                            : array();
        $label_text = '<span class="activity_' . $activity . '">' . __($activity) . '</span>';
        $out[] = '<div>' .
                 checkbox_tag('act[]', $activity_id, false, $options) 
                 . ' ' . 
                 label_for('act_' . $activity_id, $label_text)
                 . '</div>';
    }
    return '<div id="actform">' . implode(' ', $out) . '</div>';
}

function translate_sort_param($label)
{
    return str_replace(array(' :', ':'), '', __($label));
}

function field_value_selector($name, $conf, $blank = false, $keepfirst = true, $multiple = false, $size = 0)
{
    $options = array_map('__', sfConfig::get($conf));
    if (!$keepfirst)
    {
        unset($options[0]);
    }
    $options['_'] = __('nonwell informed');
    $option_tags = options_for_select($options, '',
                                      array('include_blank' => $blank));
    if ($multiple)
    {
        $select_param = array('multiple' => true);
        if ($size == 0)
        {
            $size = count($options) - 1;
        }
        $select_param['size'] = $size;
    }
    else
    {
        $select_param = array();
    }
    return select_tag($name, $option_tags, $select_param);
}

function date_selector()
{
    $option_tags = options_for_select(array('0' => '',
                                            '1' => __('greater than'),
                                            '2' => __('lower than'),
                                            '3' => __('between'))
                                     );
    $out = select_tag('date_sel', $option_tags,
                      array('onchange' => "update_on_select_change('date', 3)"));
    $out .= '<span id="date_span1" style="display:none"> ';
    $out .= input_date_tag('date', NULL, array('class' => 'medium_input',
                                               'rich' => false,
                                               'year_start' => 1990,
                                               'year_end' => date('Y')));
    $out .= '<span id="date_span2" style="display:none"> ' . __('and') . ' ';
    $out .= input_date_tag('date2', NULL, array('class' => 'medium_input',
                                                'rich' => false,
                                                'year_start' => 1990,
                                                'year_end' => date('Y')));
    $out .= '</span></span>'; 
    return $out;
}

function bool_selector($field)
{
    $out = select_tag($field, options_for_select(array('yes' => __('yes'), 'no' => __('no')),
                                                  '', array('include_blank' => true)));
    return $out;
}

function georef_selector()
{
    $out  = __('geom_wkt') . ' ';
    $out .= bool_selector('geom');
    return $out;
}
