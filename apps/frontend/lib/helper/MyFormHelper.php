<?php
/**
 * Form tools
 * @version $Id: MyFormHelper.php 2483 2007-12-06 22:42:31Z alex $
 */

// FIXME dirty trick
if (function_exists('use_helper')) // template
{
    use_helper('Form', 'Object', 'Tag', 'Asset', 'Validation', 'DateForm', 'General', 'Button');
}
else // action
{
    sfLoader::loadHelpers('Form', 'Object', 'Tag', 'Asset', 'Validation', 'DateForm', 'General', 'Button');
}

function loadTooltipsEditRessources()
{
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/static/js/submit.js', 'last');
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

function object_group_tag($object, $fieldname, $options = array())
{
    $callback = _option($options, 'callback', 'object_input_tag');
    $suffix = _option($options, 'suffix', '');
    $prefix = _option($options, 'prefix', '');
    $check_mandatory = _option($options, 'check_mandatory', true);
    $label_name = _option($options, 'label_name', null);
    $label_id = _option($options, 'label_id', $label_name);
    $tips = _option($options, 'tips', '');

    $method = _convert_fieldname_to_method($fieldname);
    $mandatory = $check_mandatory && is_mandatory($fieldname);

    $no_label = ($callback == 'object_checkbox_tag');

    $out  = $mandatory 
            ? start_group_tag(sfConfig::get('app_form_input_group_class', 'form-row') . ' mandatory')
            : start_group_tag();
    $out .= label_tag($fieldname, $label_name, $mandatory, null, $label_id, $no_label);
    $out .= form_error($fieldname) . ' <span>';

    // special case where we use BFC: idea is to have the prefix on the left, and the input taking all
    // remaining space after that. This requires some extra markup. FIXME could probably be made cleaner
    if ($prefix && !$suffix && isset($options['class']) && $options['class'] == 'bfc_input')
    {
        $out .= '<span class="bfc_float_left">' . $prefix . '</span><span class="bfc_wrap">' .
                $callback($object, $method, $options) . '</span></span>';
    }
    else
    {
        if ($prefix)
        {
            $out .= $prefix . ' ';
        }

        $out .= $callback($object, $method, $options) . '</span>';
    }

    // display suffix right after the input (for example unit)
    if ($suffix)
    {
        $out .= '&nbsp;' . __($suffix);
    }

    if ($tips) // remove ????? FIXME
    {
        if ($tips === true)
        {
            $tips = '_' . $fieldname . '_short_info';
        }
        $out .= '<div class="float-tips">' . __($tips) . '</div>';
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
                            'onkeyup' => "c2corg.coords.update_degminsec('$fieldname');"));
    $out .= '</div>';
    $out .= '&nbsp;' . __($suffix) . ' &nbsp; / &nbsp; ';
    $options = array('class' => 'short_input', 'onkeyup' => "c2corg.coords.update_decimal('$fieldname');");
    $out .= input_tag($fieldname . '_deg', $deg, $options) . ' ' .  __($suffix) . ' ';
    $out .= input_tag($fieldname . '_min', $min, $options) . " ' ";
    $out .= input_tag($fieldname . '_sec', $sec, $options) . ' "';

    $out .= end_group_tag();

    return $out;
}

function object_group_dropdown_tag($object, $fieldname, $config, $options = null, $check_mandatory = true, $labelname = null, $label_id = null, $suffix = '', $default_value = '', $class_prefix = '')
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

    // values that should be excluded
    $na = _option($options, 'na', null);
    if ($na)
    {
        $choices = array_diff_ukey($choices, array_flip($na), 'strcmp');
    }
    
    if (!isset($labelname))
    {
        $labelname = $fieldname;
    }

    if (empty($label_id))
    {
        $label_id = $labelname;
    }
    
    if (count($options))
    {
        if (!isset($options['size']) && isset($options['multiple']) && $options['multiple'])
        {
            $options['size'] = count($choices);
        }
    }
    
    return start_group_tag() .
           label_tag($labelname, '', $check_mandatory && is_mandatory($fieldname), null, $label_id) .
           form_error($fieldname) . '    ' .
           select_tag($fieldname, options_with_classes_for_select($choices, $value, array(), $class_prefix), $options) .
           ($suffix ? '&nbsp;' . __($suffix) : '') .
           end_group_tag();
}

function object_group_bbcode_tag($object, $fieldname, $field_title = null, $options = null, $check_mandatory = true, $label_id = null)
{
    $mandatory = $check_mandatory && is_mandatory($fieldname);
    if (empty($label_id))
    {
        $label_id = $fieldname;
    }
    if (empty($field_title))
    {
        $field_title = $fieldname;
    }

    $out  = $mandatory 
            ? start_group_tag(sfConfig::get('app_form_input_group_class', 'form-row') . ' mandatory')
            : start_group_tag();
    $out .= label_tag($fieldname, $field_title, $mandatory, 
                      array('class' => sfConfig::get('app_form_label_class') . ' extraheight', 'id' => '_' . $label_id));
    $out .= '<div>';
    $out .= bbcode_textarea_tag($object, $fieldname, $options);
    $out .= '</div>';
    $out .= end_group_tag();

    return $out;
}

function file_upload_tag($fieldname, $filetag = null)
{
    $filetag = isset($filetag) ? $filetag: $fieldname;

    return start_group_tag() . 
           label_tag($fieldname) .
           form_error($filetag) .
           input_file_tag($filetag) . 
           end_group_tag();
}

function label_tag($id, $labelname = null, $mandatory = false, $options = null, $label_id = null, $no_label = false)
{
    if (empty($labelname))
    {
        $labelname = $id;
    }
    elseif(strpos('[', $labelname))
    {
        $tmp = explode('[', $labelname);
        $labelname = $tmp[0];
    }

    if (empty($label_id))
    {
        $label_id = $id;
    }
    
    $default_options = array('class' => sfConfig::get('app_form_label_class', 'fieldname'), 'id' => '_' . $label_id);

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

    $content = __($labelname) . $asterisk;
    if (!$no_label)
    {
        return label_for($id, $content, $default_options) . "\n    " ;
    }
    else
    {
        return content_tag('div', $content, $default_options) . "\n    " ;
    }
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
    if (isset($options['picto']))
    {
        $picto = array('picto' => $options['picto']);
        unset($options['picto']);
    }
    $options = _convert_options_to_javascript(_convert_options($options));
    $options = array_merge(array('type' => 'submit'), $options);

    if (isset($picto))
    {
        return c2c_button($options['value'], $picto, tag('input', $options));
    }
    else
    {
        return tag('input', $options);
    }
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

function button_tag($value, $options = array())
{
    return c2c_button($value, array_merge(array('type'  => 'button',
                                                'alt'   => $value), $options)) . " ";
}

function bb_button_tag($name, $value, $textarea_id, $options = array())
{
    $title = __($value . ' button title');
    //return button_tag($name, $value, array_merge($options, $onclick));
    return tag('input', array_merge(array('value' => $value,
                                          'type' => 'button',
                                          'name'  => $name,
                                          'onclick' => "C2C.insertBbcode('$value', '$textarea_id')",
                                          'title' => $title),
                                    $options)) . ' ';
}

function bbcode_toolbar_tag($document, $target_id, $options = array())
{
    use_helper('Button');

    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/static/js/bbcode.js', 'last');

    $img_tag = !isset($options['no_img']);
    $abs_tag = isset($options['abstract']);
    $line_tag = isset($options['route_line']);
    
    return start_group_tag('bbcodetoolcontainer ' . $target_id) . 
           bb_button_tag('bold', 'b', $target_id, array('style' => 'font-weight:bold')) .
           bb_button_tag('italic', 'i', $target_id, array('style' => 'font-style:italic')) .
           bb_button_tag('underline', 'u', $target_id, array('style' => 'text-decoration:underline')) .
           bb_button_tag('strike_button', 's', $target_id, array('style' => 'text-decoration:line-through')) .
           bb_button_tag('code_button', 'c', $target_id) .
           bb_button_tag('wl_button', 'wl', $target_id) .
           bb_button_tag('url_button', 'url', $target_id, array('style' => 'text-decoration:underline')) .
           ($img_tag ? bbcode_toolbar_img_tag($document, $target_id) : '') .
           ($abs_tag ? bb_button_tag('abs_button', 'abs', $target_id) : '') .
           ($line_tag ? bb_button_tag('line_button', 'L#', $target_id, array('class' => 'rlineb')) : '') . ' &nbsp; ' .
           link_to(__('Help'), getMetaArticleRoute('formatting', false, 'path')) . ' ' .
           picto_tag('picto_close', __('Reduce the text box'),
                     array('onclick' => "C2C.changeTextareaSize('$target_id', false)")) .
           picto_tag('picto_open', __('Enlarge the text box'),
                     array('onclick' => "C2C.changeTextareaSize('$target_id', true)")) .
           end_group_tag();
}

function bbcode_toolbar_img_tag($document, $target_id)
{
    $id = $document->getId();

    $options = array('title' => __('Insert image'),
                     'onclick' => "jQuery.modalbox.show(" .
                         _options_for_javascript(array('title' => 'this.title', 'width' => 710,
                                                      'remote' => "'/insertimagetag/" . $document->getModule() . "/$id/$target_id'")) .
                         '); return false;');

    if (!(isset($id) && trim($id) != ''))
    {
        $options['disabled'] = 'disabled';
    }

    $title = __('img button title');
    return tag('input', array_merge(array('value' => 'img',
                                          'title' => $title,
                                          'type' => 'button',
                                          'name'  => 'insert img'),
                                    $options)) . ' ';
}

function bbcode_textarea_tag($object, $fieldname, $options = null)
{
    $method = _convert_fieldname_to_method($fieldname);

    // we don't want no_img options to be forwarded to textarea function
    // moreover we need to define default rows and cols values for xhtml strict
    // even if they are overriden by css
    $bbcode_options = $options;
    if (isset($options['no_img'])) unset($options['no_img']);
    if (isset($options['abstract'])) unset($options['abstract']);
    if (isset($options['route_line'])) unset($options['route_line']);
    $options['rows'] = '4';
    $options['cols'] = '20';

    return bbcode_toolbar_tag($object, $fieldname, $bbcode_options) .
           object_textarea_tag($object, $method, $options);
}

function search_box_tag($id_prefix = '', $autocomplete = true)
{
    $sf_context = sfContext::getInstance();
    $list = array();
    foreach (sfConfig::get('app_modules_list') as $module)
    {
        switch ($module)
        {
            case 'documents':
                // remove "all documents" option because it load too much the server
                // $list['documents'] = __('all');
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
        if (in_array($current_module, array('documents', 'common', 'portals')))
        {
            $selected = 'summits'; // FIXME should be routes, but we use summits until performance are improved
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
    $select_js = 'var c=this.classNames().each(function(i){$(\''.$id_prefix.'type\').removeClassName(i)});this.addClassName(\'picto picto_\'+$F(this));';
    $html = select_tag('type', $options,
                       array('onchange' => $select_js,
                             'class' => 'picto picto_'.$selected,
                             'id' => $id_prefix.'type'));
    
    $input_html_options = array('class' => 'searchbox action_filter',
                                'id' => $id_prefix.'q',
                                'type' => 'search',
                                'placeholder' => __('Search'),
                                'accesskey' => 'f',
                                'title' => __('Search on c2c') . ' [alt-shift-f]');
    if ($autocomplete)
    {
        $html .= input_auto_complete_tag('q', '', '@quicksearch',
                                         $input_html_options,
                                         array('update_element' => "function (selectedItem) {
                                                  window.location = '/documents/'+selectedItem.id; }",
                                               'min_chars' => sfConfig::get('app_autocomplete_min_chars'),
                                               'with' => "'q='+$('${id_prefix}q').value+'&type='+$('${id_prefix}type').value"));
    }
    else
    {
        $html .= input_tag('q', $sf_context->getRequest()->getParameter('q'), $input_html_options);
    }
    return $html;
}

function portal_search_box_tag($params, $current_module)
{
    sfLoader::loadHelpers(array('Pagination'));
    $sf_context = sfContext::getInstance();
    
    if (is_array($params) && count($params))
    {
        $main_filter = $params['main'];
        unset($params['main']);
    }
    else
    {
        $main_filter = $params;
        $params = array();
    }
    $url_params = array();
    $criteria = unpackUrlParameters($main_filter, $url_params);
    $names = array_keys($criteria);
    $list = array();
    foreach (sfConfig::get('app_modules_list') as $module)
    {
        switch ($module)
        {
            case 'documents':
                break;
        
            default:
                if (($module != $current_module) && !in_array($module, $names))
                {
                    $key = $module;
                    if (isset($params[$module]))
                    {
                        $key .= '/' . $params[$module];
                    }
                    $list[$key] = __($module);
                }
        }
    }
    $selected = 'routes';
    $options = options_with_classes_for_select($list, $selected, array(), 'picto picto_');
    $select_js = 'var c=this.classNames().each(function(i){$(\'wtype\').removeClassName(i)});this.addClassName(\'picto picto_\'+($F(this).split(\'/\'))[0]);';
    $html = '<input type="hidden" value="' . $main_filter . '" name="params" />';
    $html .= select_tag('wtype', $options, array('onchange' => $select_js, 'class' => 'picto picto_'.$selected, 'placeholder' => __('Search'))); 
    $html .= input_tag('q', $sf_context->getRequest()->getParameter('q'), array('class' => 'searchbox'));
    $html .= c2c_submit_tag(__('Search'), 'action_filter');
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
                $tmp = explode('/', $key, 2);
                $suffix = $tmp[0];
                $option_options['class'] = $class_prefix . $suffix;
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

/*
 * providing a consistent UI for form elements accross
 * all browsers is a mess. Use following functions to get
 * buttons, input submits, etc... */
function c2c_reset_tag($value = 'Reset', $options = array())
{
    return c2c_button($value, array_merge(array('type' => 'reset', 'name' => 'reset'), _convert_options($options)));
}

function c2c_submit_tag($value = 'Submit', $options = array())
{
    return c2c_button($value, array_merge(array('type' => 'submit', 'name' => 'commit'), _convert_options_to_javascript(_convert_options($options))));
}

function c2c_button($value, $options, $btn = null)
{
    // 'picto' and 'class' options are used by the styled spans
    // other options are applied to the input
    if (array_key_exists('picto', $options))
    {
      $picto = ' c2cui_picto ' . $options['picto'];
      unset($options['picto']);
    }
    else
    {
        $picto = '';
    }
    if (array_key_exists('class', $options))
    {
        $class = 'c2cui_btn ' . $options['class'];
    }
    else
    {
        $class = 'c2cui_btn';
    }
    $options['class'] = 'c2cui_btnr';

    $btn = is_null($btn) ? tag('input', array_merge(array('value' => $value), $options))
                         : $btn;
    
    return '<span class="'. $class . '">' . $btn . '<span class="c2cui_btno">' .
           '<span class="c2cui_btnin' . $picto . '">' . $value . '</span></span></span>';
}

function my_radiobutton_tag($name, $value, $checked = false, $options = array())
{
    $id = str_replace('[]', '', $name);
    if ($value != null)
    {
        $id .= '_' . $value;
    }
    
    $html_options = array_merge(array('type' => 'radio', 'name' => $name, 'id' => get_id_from_name($id, $value), 'value' => $value), _convert_options($options));

    if ($checked)
    {
        $html_options['checked'] = 'checked';
    }

    return tag('input', $html_options);
}
