<?php
/**
 * Autocomplete tools
 * @version $Id:$
 */

// FIXME : dirty trick
if (isset($sf_user))
{
    // we are in a template 
    use_helper('Javascript','Tag','Url','I18N','Asset', 'Viewer', 'MyForm', 'Form', 'General');
}
else
{
    // we are in an action
    sfLoader::loadHelpers(array('Tag','Url','I18N','Asset', 'Viewer', 'MyForm', 'Form', 'Javascript', 'General'));
}

function c2c_input_auto_complete($module, $update_hidden, $field_prefix = '', $display = '', $size = '45')
{
    if ($module == 'users')
    {
        $placeholder = __('ID or name/nickname');
    }
    else
    {
        $placeholder = __('Keyword or ID');
    }
    return input_auto_complete_tag($module . '_name', 
                            $display, // default value in text field 
                            "$module/autocomplete", 
                            array('size' => $size, 'id' => $field_prefix.'_'.$module.'_name', 'placeholder' => $placeholder), 
                            array('after_update_element' => "function (inputField, selectedItem) {\$('$update_hidden').value = selectedItem.id;}",
                                  'min_chars' => sfConfig::get('app_autocomplete_min_chars'), // min chars to type
                                  'indicator' => 'indicator'));
}

function c2c_auto_complete($module, $update_hidden, $field_prefix = '', $display = '', $display_button = true)
{
    // updated field name can be customized so that there is no interference
    // between different autocomplete forms by using field_prefix
    $field = $field_prefix . '_' . $module . '_name';

    $out = c2c_input_auto_complete($module, $update_hidden, $field_prefix, $display);
    $out .= ($display_button) ? c2c_submit_tag(__('Link'), array(
                                    'class' => 'samesize',
                                    'onclick' => "$('$field').value = '';",
                                    'picto' =>  'action_create')) : '';
    return $out;
}

/*
 * service = nominatim or geonames
 */
function geocode_auto_complete($name, $service)
{
    $mobile_version = c2cTools::mobileVersion();
    $context = sfContext::getInstance();

    $response = $context->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/controls');
    // following script will automatically intanciate Geocode.Autocompleter
    $response->addJavascript('/static/js/geocode_autocompleter');

    $service_class = ($service === 'nominatim') ? ' nominatim' : ' geonames';

    $out = input_tag($name, '', array('class' => 'geocode_auto_complete' . $service_class,
                                      'placeholder' => __('enter place name'),
                                      'data-noresult' => __('no results')));
    if ($mobile_version)
    {
        $out .= content_tag('span', '<br />'.__('autocomplete_help'), array('class' => 'mobile_auto_complete_background'));
        $out .= content_tag('span', 'X', array('class' => 'mobile_auto_complete_escape'));
    }
    $out .= content_tag('span', '' , array('id' => $name.'_auto_complete', 'class' => 'auto_complete'));
    
    return $out;
}

function c2c_form_remote_add_element($url, $updated_success, $indicator = 'indicator', $removed_id = null)
{
    $updated_failure = sfConfig::get('app_ajax_feedback_div_name_failure');
    return form_remote_tag(array('update' => array('success' => $updated_success, 'failure' => $updated_failure),
                                 'position' => 'bottom',
                                 'url' => $url,
                                 'method' => 'post',
                                 'loading' => "Element.show('$indicator')",
                                 'complete' => "Element.hide('$indicator')",
                                 'success'  => "Element.hide('$updated_failure');if($('{$updated_success}_rsummits_name')){".
                                               "$('{$updated_success}_rsummits_name').value='';$('{$updated_success}_associated_routes').hide();}".
                                               ($removed_id == null ? '' : "$('$removed_id').hide();"),
                                 'failure'  => "C2C.showFailure()"));
}

function c2c_link_to_delete_element($link_type, $main_id, $linked_id, $main_doc = true,
                                    $strict = 1, $updated_failure = null, $indicator = 'indicator',
                                    $tips = null)
{
    $response = sfContext::getInstance()->getResponse()->addJavascript('/static/js/rem_link.js', 'last');
    // NB : $del_image_id is for internal use, but will be useful when we have several delete forms in same page
    $main_doc = ($main_doc) ? 'true' : 'false';
    $tips = ($tips == null) ? 'Delete this association' : $tips;
    return link_to(picto_tag('action_del_light', __($tips)), '#',
                         array('onclick' => "C2C.remLink('$link_type', $main_id, $linked_id, $main_doc, $strict); return false;"));
}

/**
 * Create a form that allow to link the current document with several kinds of other docs
 *
 * @param module current document module
 * @param id current document id
 * @param modules_list list of modules available for association
 * @param default_selected default selected module in the dropdown list
 * @param field_prefix used to prevent to have ids conflict when multiple forms
 * @param $hide if true, display button to hide/show the form + some text
 * @param $indicator, the ID of the HTML object used to display indications on the ajax status (Loading, Success, ...)
 * @param $removed_id, the ID of the HTML object to hide
 */
function c2c_form_add_multi_module($module, $id, $modules_list, $default_selected, $field_prefix = 'list_associated_docs', $hide = true, $indicator = 'indicator', $removed_id = null)
{
    $modules_list = array_intersect(sfConfig::get('app_modules_list'), $modules_list);
    $modules_list_i18n = array_map('__', $modules_list);
    $select_js = 'var c=this.classNames().each(function(i){$(\'dropdown_modules\').removeClassName(i)});this.addClassName(\'picto picto_\'+$F(this));';
    $select_modules = select_tag('dropdown_modules', options_with_classes_for_select($modules_list_i18n, array($default_selected), array(), 'picto picto_'),
                                 array('onchange' => $select_js, 'class' => 'picto picto_' . $default_selected));
    
    $picto_add = ($hide) ? '' : picto_tag('picto_add', (in_array('users', $modules_list) ? __('Link an existing user or document') : __('Link an existing document'))) . ' ';
    
    $out = $picto_add . $select_modules;

    // update form when user changes the document type
    $out .= observe_field('dropdown_modules',
                          array('update' => $field_prefix . '_form',
                                'url' => "/$module/getautocomplete",
                                'with' => "'module_id=' + value + '&field_prefix=$field_prefix'",
                                'script' => 'true',
                                'loading' => "Element.show('indicator')",
                                'complete' => "Element.hide('indicator')"));

    // form start
    $out .= c2c_form_remote_add_element("$module/addAssociation?main_id=$id", $field_prefix, $indicator, $removed_id);

    // default form content
    $out .= '<div id="' . $field_prefix . '_form' . '" class="ac_form">'
          . input_hidden_tag('document_id', '0', array('id' => $field_prefix . '_document_id'))
          . input_hidden_tag('document_module', $modules_list[$default_selected], array('id' => $field_prefix . '_document_module'))
          . c2c_auto_complete($modules_list[$default_selected], $field_prefix . '_document_id', $field_prefix, '')
          . '</div></form>';

    // this is where the linked docs will be displayed after ajax
    $out = '<div class="doc_add">' . $out . '</div>';
    
    if ($hide)
    {
        $picto_add_rm = '<span class="assoc_img picto_add" title="' . __('show form') . '"></span>'
                   . '<span class="assoc_img picto_rm" title="' . __('hide form') . '"></span>';
        $picto_add_rm = link_to_function($picto_add_rm, "C2C.toggleForm('${field_prefix}_form')");
        
        $title = '<div id="_association_tool" class="section_subtitle extra">' . (in_array('users', $modules_list) ? __('Link an existing user or document') : __('Link an existing document')) . __('&nbsp;:') . '</div> ';
        
        $pictos = ' ';
        foreach ($modules_list as $module)
        {
            $pictos .= picto_tag('picto_' . $module, __($module));
        }
        $pictos = link_to_function($pictos, "C2C.toggleForm('${field_prefix}_form')");
        $pictos = '<div class="short_data">' . $pictos . '</div>';
        
        $out = '<div class="one_kind_association empty_content">'
             . '<div class="association_tool hide" id="' . $field_prefix . '_form_association">'
             . $picto_add_rm
             . $title
             . $pictos
             . '<ul id="' . $field_prefix . '"><li style="display:none"></li></ul>'
             . $out
             . '</div></div>';
    }

    return $out;
}
