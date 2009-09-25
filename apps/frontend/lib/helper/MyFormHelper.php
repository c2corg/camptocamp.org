<?php
/**
 * Form tools
 * @version $Id: MyFormHelper.php 2483 2007-12-06 22:42:31Z alex $
 */

use_helper('Form', 'Object', 'Tag', 'Asset', 'Validation', 'DateForm', 'General', 'Button');


function loadTooltipsEditRessources()
{
    $static_base_url = sfConfig::get('app_static_url');

    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript($static_base_url . '/static/js/tooltips.js?' . sfSVN::getHeadRevision('tooltips.js'), 'last');
    $response->addJavascript($static_base_url . '/static/js/tooltips_edit.js?' . sfSVN::getHeadRevision('tooltips_edit.js'), 'last');
    $response->addJavascript($static_base_url . '/static/js/submit.js?' . sfSVN::getHeadRevision('submit.js'), 'last');
}

loadTooltipsEditRessources();

function _get_mandatory_fields()
{
    $module = sfContext::getInstance()->getModuleName();
    $config_file = sfConfig::get('sf_app_module_dir') .
                   DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR .
                   sfConfig::get('sf_app_module_validate_dir_name') .
                   DIRECTORY_SEPARATOR . 'edit.yml';
    $config = sfYaml::load($config_file);

    $mandatory_fields = array();
    if (!empty($config) && !empty($config['fields']))
    {
        foreach ($config['fields'] as $fieldname => $field_data)
        {
            if (isset($field_data['required']))
            {
                $mandatory_fields[] = $fieldname;
            }
        }
    }

    return $mandatory_fields;
}

function is_mandatory($fieldname)
{
    global $module_mandatory_fields;

    if (is_null($module_mandatory_fields))
    {
        $module_mandatory_fields = _get_mandatory_fields();
    }

    return in_array($fieldname, $module_mandatory_fields);
}

function start_group_tag($group_class = null, $id = null)
{
    if (is_null($group_class))
    {
        $group_class = sfConfig::get('app_form_input_group_class', 'form-row');
    }
    
    $group_class = "class=\"$group_class\"" ;
    $id = !empty($id) ? "id=\"$id\" " : '';
    
    return "<div $group_class $id>\n    ";
}

function end_group_tag()
{
    return "\n</div>\n";
}

function fieldset_tag($legend)
{
    return '<fieldset><legend>' . __($legend) . '</legend>';
}

function end_fieldset_tag()
{
    return '</fieldset>';
}

/**
 * Converts some_name into getSomeName
 */
function _convert_fieldname_to_method($fieldname)
{
    $components = explode('_', $fieldname);
    $components = array_map('ucfirst', $components);
    return 'get' . implode('', $components);
}

function group_tag($label, $fieldname, $callback = 'input_tag', $value = null, $options = null)
{
    if ($callback == 'checkbox_tag')
    {
        if (is_array($value) && count($value) == 2)
        {
            $checked = $value[0];
            $value = $value[1];
        }
        else
        {
            $checked = $value;
            $value = true;
        }
        $tag = $callback($fieldname, $value, $checked, $options);
    }
    else
    {
        $tag = $callback($fieldname, $value, $options);
    }

    return start_group_tag() .
           label_tag($fieldname, $label) .
           form_error($fieldname) . "    $tag" .
           end_group_tag();
}

function object_group_tag($object, $fieldname, $callback = null, $suffix = '', $options = null, $check_mandatory = true, $label = null)
{
    $method = _convert_fieldname_to_method($fieldname);
    $mandatory = $check_mandatory && is_mandatory($fieldname);

    if (empty($callback))
    {
        $callback = 'object_input_tag';
    }

    $out  = $mandatory 
            ? start_group_tag(sfConfig::get('app_form_input_group_class', 'form-row') . ' mandatory')
            : start_group_tag();
    $out .= label_tag($fieldname, $label, $mandatory, null, $label);
    $out .= form_error($fieldname) . ' <div style="display:inline">' . $callback($object, $method, $options) . '</div>';
    if ($suffix)
    {
        $out .= '&nbsp;' . __($suffix);
    }
    $out .= end_group_tag();

    return $out;
}

function object_coord_tag($object, $fieldname, $suffix)
{
    $method = _convert_fieldname_to_method($fieldname);
    $degdec = $object->$method();
    if (is_null($degdec))
    {
        $deg = $min = $sec = NULL;
    }
    else
    {
        if ($degdec < 0)
        {
            $sign = -1;
            $degdec = -1 * $degdec;
        }
        else
        {
            $sign = 1;
        }
        $deg = floor($degdec);
        $minTemp = 60 * ($degdec - $deg);
        $min = floor($minTemp);
        $sec = floor(60 * 100 * ($minTemp - $min)) /100;
        $deg *= $sign;
        $degdec *= $sign;
    }
    
    $mandatory = is_mandatory($fieldname);
    $out  = $mandatory 
            ? start_group_tag(sfConfig::get('app_form_input_group_class', 'form-row') . ' mandatory')
            : start_group_tag();
    $out .= label_tag($fieldname, '', $mandatory);
    $out .= form_error($fieldname) . ' <div style="display:inline">';
    $out .= input_tag($fieldname, $degdec,
                      array('class' => 'medium_input',
                            'onkeyup' => "update_degminsec('$fieldname');toggle_update_btn()"));
    $out .= '</div>';
    $out .= '&nbsp;' . __($suffix) . ' &nbsp; / &nbsp; ';
    $options = array('class' => 'short_input', 'onkeyup' => "update_decimal_coord('$fieldname');toggle_update_btn()");
    $out .= input_tag($fieldname . '_deg', $deg, $options) . ' ' .  __($suffix) . ' ';
    $out .= input_tag($fieldname . '_min', $min, $options) . " ' ";
    $out .= input_tag($fieldname . '_sec', $sec, $options) . ' "';

    $out .= end_group_tag();

    return $out;
}

function object_group_dropdown_tag($object, $fieldname, $config, $options = null, $check_mandatory = true, $labelname = null, $suffix = '', $default_value = '', $class_prefix = '')
{
    $value = null;
    if (!is_null($object))
    {
        $value = $object->get($fieldname, ESC_RAW);
        if ($value == null && strval($default_value) != '')
        {
            $value = $default_value;
        }
    }
    $choices = array_map('__', sfConfig::get($config));
    if (!isset($labelname))
    {
        $labelname = $fieldname;
    }
    
    return start_group_tag() .
           label_tag($labelname, '', $check_mandatory && is_mandatory($fieldname)) .
           form_error($fieldname) . '    ' .
           select_tag($fieldname, options_with_classes_for_select($choices, $value, array(), $class_prefix), $options) .
           ($suffix ? '&nbsp;' . __($suffix) : '') .
           end_group_tag();
}

function object_group_bbcode_tag($object, $fieldname, $field_title = null, $options = null, $check_mandatory = true)
{
    $mandatory = $check_mandatory && is_mandatory($fieldname);
    if (empty($field_title))
    {
        $field_title = $fieldname;
    }

    $out  = $mandatory 
            ? start_group_tag(sfConfig::get('app_form_input_group_class', 'form-row') . ' mandatory')
            : start_group_tag();
    $out .= label_tag($fieldname, $field_title, $mandatory, 
                      array('class' => sfConfig::get('app_form_label_class') . ' extraheight', 'id' => '_' . $fieldname));
    $out .= '<div>';
    $out .= bbcode_textarea_tag($object, $fieldname, $options);
    $out .= '</div>';
    $out .= end_group_tag();

    return $out;
}

function file_upload_tag($fieldname, $mandatory = false, $filetag = 'file', $form_error = false)
{
    return start_group_tag() . 
           label_tag($fieldname, '', $mandatory) .
           (($form_error) ? form_error($filetag) : '') .
           input_file_tag($filetag) . 
           end_group_tag();
}

function label_tag($id, $label = null, $mandatory = false, $options = null, $lfor = null)
{
    if (empty($label))
    {
        $label = $id;
    }
    elseif(strpos('[', $label))
    {
        $tmp = explode('[', $label);
        $label = $tmp[0];
    }

    $for_temp = $id;
    if (!empty($lfor))
    {
        $id = $lfor;
    }
    
    $default_options = array('class' => sfConfig::get('app_form_label_class', 'fieldname'), 'id' => '_' . $id);

    if (!is_null($options))
    {
        if (array_key_exists('class', $options))
        {
            $default_options = $options;
        }
        else
        {
            $default_options = array_merge($options, $default_options);
        }
    }

    $asterisk = ($mandatory) ? '<em class="mandatory_asterisk">*</em>' : '';

    return label_for($for_temp, __($label) . $asterisk, $default_options) . "\n    " ;
}

function global_form_errors_tag($show_field = true)
{
    $toReturn = '';
    $request = sfContext::getInstance()->getRequest();

    if ($request->hasErrors())
    {
        $toReturn = "<div class='" . sfConfig::get('app_form_global_error_class', 'global_form_error') . "'>" .
                    '<p>' . __('The data you entered seems to be incorrect. ' .
                            'Please correct the following errors and resubmit:') . 
                    '</p>' . 
                    '<ul>';

        foreach($request->getErrors() as $name => $error)
        {
            $toReturn .= '<li>' . ($show_field ? __($name)  . ' ' : '') . __($error) . '</li>';
        }

        $toReturn .= '</ul></div>';
    }
    
    return $toReturn;
}

function submit_tag_without_name($options)
{
    $options = _convert_options_to_javascript(_convert_options($options));
    $options = array_merge(array('type' => 'submit'), $options);
    
    return tag('input', $options);
}

function submit_tag_disabled_if($condition, $options)
{
    if ($condition)
    {
        $options['disabled'] = 'disabled';
    }
    
    return submit_tag_without_name($options);
}

function compare_submit($condition, $html_options)
{
    if ($condition != 1)
    {
        return '<p class="diff_button">' . submit_tag_without_name($html_options) . '</p>';
    }
}

function display_document_edit_hidden_tags($document, $additional_fields = array())
{
    echo object_input_hidden_tag($document, 'getId');
    echo input_hidden_tag('lang', $document->getCulture());
    echo input_hidden_tag('revision', $document->getVersion());

    foreach ($additional_fields as $field)
    {
        echo input_hidden_tag($field, $document->get($field));
    }
}

function button_tag($name, $value, $options = array())
{
    return tag('input', array_merge(array('type'  => 'button',
                                          'name'  => $name,
                                          'value' => $value,
                                          'title' => __($value . ' button title'),
                                          'alt'   => $name), $options)) . " ";
}

function bb_button_tag($name, $value, $textarea_id, $options = array())
{
    $onclick = array('onclick' => "storeCaret('$value', '$textarea_id')");
    return button_tag($name, $value, array_merge($options, $onclick));
}

function bbcode_toolbar_tag($document, $target_id)
{
    $static_base_url = sfConfig::get('app_static_url');
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript($static_base_url . '/static/js/bbcode.js?' . sfSVN::getHeadRevision('bbcode.js'), 'last');

    return start_group_tag('bbcodetoolcontainer ' . $target_id) . 
           bb_button_tag('bold', 'b', $target_id, array('style' => 'font-weight:bold')) .
           bb_button_tag('italic', 'i', $target_id, array('style' => 'font-style:italic')) .
           bb_button_tag('underline', 'u', $target_id, array('style' => 'text-decoration:underline')) .
           bb_button_tag('insert url', 'url', $target_id, array('style' => 'text-decoration:underline')) .
           bbcode_toolbar_img_tag($document, $target_id) .
           bb_button_tag('insert wikilink', 'wl', $target_id) . ' ' .
           link_to(__('Help'), getMetaArticleRoute('formatting', false, 'path')) . ' ' .
           picto_tag('picto_close', __('Reduce the text box'),
                     array('onclick' => "changeTextareaSize('$target_id', false)")) .
           picto_tag('picto_open', __('Enlarge the text box'),
                     array('onclick' => "changeTextareaSize('$target_id', true)")) .
           end_group_tag();
}

function bbcode_toolbar_img_tag($document, $target_id)
{
    return button_tag('insert img', 'img',
                      array('title' => __('Insert image'),
                            'onclick' => "Modalbox.show('/insertimagetag/" . $document->getModule() . '/'
                                                        . $document->getId() . "/$target_id', "
                                                        . _options_for_javascript(array('title' => 'this.title', 'width' => 710))
                                                        . "); return false;"));
}

function bbcode_textarea_tag($object, $fieldname, $options = null)
{
    $method = _convert_fieldname_to_method($fieldname);
    return bbcode_toolbar_tag($object, $fieldname) .
           object_textarea_tag($object, $method, $options);
}

function search_box_tag()
{
    $sf_context = sfContext::getInstance();
    $list = array();
    foreach (sfConfig::get('app_modules_list') as $module)
    {
        switch ($module)
        {
            case 'documents':
                $list['documents'] = __('all');
                break;
        
            case 'sites':
                $list['sites'] = __('sites short');
                break;

            default:
                $list[$module] = __($module);
        }
    }
    $list['forums'] = __('forums');
    $selected = $sf_context->getRequest()->getParameter('type');
    if (empty($selected))
    {
        $current_module = $sf_context->getModuleName();
        if ($current_module == 'documents' || $current_module == 'common')
        {
            $selected = 'routes';
        }
        else if(empty($current_module))
        {
            $selected = 'forums';
        }
        else
        {
            $selected = $current_module;
        }
    }
    $options = options_with_classes_for_select($list, $selected, array(), 'picto picto_');
    $select_js = 'var c=this.classNames().each(function(i){$(\'type\').removeClassName(i)});this.addClassName(\'picto picto_\'+$F(this));';
    $html = select_tag('type', $options, array('onchange' => $select_js, 'class' => 'picto picto_'.$selected)); 
    $html .= input_tag('q', $sf_context->getRequest()->getParameter('q'), array('class' => 'searchbox action_filter'));
    return $html;
}

function tips_tag($message, $string_parameters = null)
{
    return content_tag('p', __($message, $string_parameters), array('class' => 'tips'));
}

function radiobutton_tag_selected_if($radio_name, $value, $value_to_compare_with)
{
    $are_equal = ($value == $value_to_compare_with);
    return radiobutton_tag($radio_name, $value, $are_equal);
}

function checkbox_list($list_name, $checkboxes_array, $compare_array, $label_after = true, $i18n = true, $list_class = 'checkbox_list', $nokey = false, $picto='')
{
    //$toReturn = link_to_function('do not filter', "$$('#$list_name input[type=checkbox]').invoke('disable'); $('$list_name').hide(); this.hide();");
    
    $toReturn = '<ol class="' . $list_class . '" id="' . $list_name . '">';
    
    if ($checkboxes_array[0] == null) 
    { 
        unset($checkboxes_array[0]);
    }

    foreach ($checkboxes_array as $key => $checkbox)
    {
        $value_to_use = ($nokey) ? $checkbox : $key;
        
        $checked = in_array($value_to_use, $compare_array);
        $options = ($checked) ? ' class="checked"' : '';
        
        $toReturn .= "<li$options>";

        $label_toReturn = label_for($list_name . '_' . $checkbox, $i18n ? __($checkbox) : $checkbox);
        if ($picto != '')
        {
            $label_toReturn = picto_tag($picto . '_' . $value_to_use) . $label_toReturn;
        }

    	$checkbox_toReturn = checkbox_tag($list_name . '[]', $value_to_use, $checked,
                                  array('id' => $list_name . '_' . $checkbox, 'onclick' => "javascript:if(this.parentNode.className == 'checked'){this.parentNode.className = '';}else{this.parentNode.className = 'checked';}"));
        
        if ($label_after)
        {
            $toReturn .= $checkbox_toReturn . ' ' . $label_toReturn;
        }
        else
        {
            $toReturn .= $label_toReturn . ' ' . $checkbox_toReturn;
        }

    	$toReturn .= '</li>';
    }
    
    return $toReturn .= '</ol>';
}

function checkbox_nokey_list($list_name, $checkboxes_array, $compare_array, $label_after = true, $i18n = true, $list_class = 'checkbox_list')
{
    return checkbox_list($list_name, $checkboxes_array, $compare_array, $label_after, $i18n, $list_class, true);
}

function mandatory_fields_warning($warnings = array())
{
    $out =  '<ul class="mandatory_fields_warning">';
    foreach ($warnings as $warning)
    {
        $out .= '<li>' . __($warning) . '</li>';
    }
    return $out . '<li>' . __('mandatory_fields') . '</li></ul>';
}

function object_months_list_tag($document, $fieldname, $multiple = true)
{
    $I18n_arr = _get_I18n_date_locales(sfContext::getInstance()->getUser()->getCulture());
    $months = $I18n_arr['dateFormatInfo']->getMonthNames();

    $value = $document->getRaw($fieldname);
    if (is_array($value))
    {
        $options = '';
        foreach ($months as $month_id => $month_name)
        {
            $option_options = array('value' => ++$month_id);
            if (in_array($month_id, $value))
            {
                $option_options['selected'] = 'selected';
            }
            $options .= content_tag('option', $month_name, $option_options) . "\n";
        }
    }
    else
    {
        $select_options = array();
        foreach ($months as $month_id => $month_name)
        {
            $select_options[$month_id + 1] = $month_name;
        }
        $options = options_for_select($select_options, $value);
    }

    $html_options = array();
    if ($multiple)
    {
        $html_options['multiple'] = true;
    }

    return start_group_tag() .
           label_tag($fieldname) .
           select_tag($fieldname, $options, $html_options) .
           end_group_tag();
}

function object_datetime_tag($document, $fieldname)
{
    $date = $document->get('date_time');
    if ($date == null) $date = '';

    $out  = start_group_tag();
    $out .= label_tag($fieldname, '');
    $out .= form_error($fieldname) . ' <div style="display:inline">'
            . select_datetime_tag('date_time', $date,
                                  array('include_second' => true, 'include_blank' => true,
                                        'year_start' => 1990, 'year_end' => date('Y')))
            . '</div>';
    $out .= end_group_tag();

    return $out;
}

function form_section_title($title, $section_id, $preview_id = '')
{
    $out = '<h3 class="title" id="' . $section_id . '">';
    $out .= '<a href="#' . $preview_id . '">';
    $out .= __($title);
    $out .= '<span class="goto_preview tips" style="display:none;">[' . __('Go to preview') . ']</span>';
    $out .= '</a></h3>';
    
    return $out;
}

/**
 * This function is similar to option_for_select from symfony, except that it allows you
 * to specify a class for the options (which is class_prefix+value)
 */
function options_with_classes_for_select($options = array(), $selected = '', $html_options = array(), $class_prefix = '')
{
    $html_options = _parse_attributes($html_options);

    if (is_array($selected))
    {
        $selected = array_map('strval', array_values($selected));
    }

    $html = '';

    if ($value = _get_option($html_options, 'include_custom'))
    {
        $html .= content_tag('option', $value, array('value' => ''))."\n";
    }
    else if (_get_option($html_options, 'include_blank'))
    {
        $html .= content_tag('option', '', array('value' => ''))."\n";
    }

    foreach ($options as $key => $value)
    {
        if (is_array($value))
        {
            $html .= content_tag('optgroup', options_with_classes_for_select($value, $selected, $html_options), array('label' => $key), $class_prefix)."\n";
        }
        else
        {
            $option_options = array('value' => $key);
            if (!empty($class_prefix))
            {
                $option_options['class'] = $class_prefix . $key;
            }

            if (
                (is_array($selected) && in_array(strval($key), $selected, true))
                ||
                (strval($key) == strval($selected))
            )
            {
                $option_options['selected'] = 'selected';
            }

            $html .= content_tag('option', $value, $option_options)."\n";
        }
    }

    return $html;
}
