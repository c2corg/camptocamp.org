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
    /*
    ('Url'); // for link_to
    ('I18N'); // for __()
    ('Javascript'); // for link_to_remote
    ('Asset'); // for image_tag
    */
}

function c2c_input_auto_complete($module, $update_hidden, $display = '', $field = 'name', $size = '45')
{
    return input_auto_complete_tag($field, 
                            $display, // default value in text field 
                            "$module/autocomplete", 
                            array('size' => $size), 
                            array('after_update_element' => "function (inputField, selectedItem) { 
                                                                $('$update_hidden').value = selectedItem.id;}",
                                  'min_chars' => sfConfig::get('app_autocomplete_min_chars'), // min chars to type before ajax request
                                  'indicator' => 'indicator'));
}


function c2c_auto_complete($module, $update_hidden, $display = '', $field = null, $display_button = true )
{
    // updated field name must be customized so that there is no interference between different autocomplete forms :
    $field = ($field==null) ? $module . '_name' : $field ;
    
    $out = c2c_input_auto_complete($module, $update_hidden, $display, $field);
    $out .= ($display_button) ? submit_tag(__('Link'), array(
                                    'onclick' => "$('$field').value = '';",
                                    'class' =>  'picto action_create')) : '';
    return $out;
}


function c2c_form_remote_add_element($url,
                                    $updated_success,
                                    $updated_failure = null,
                                    $indicator = 'indicator',
                                    $removed_id = null)
{

    $updated_failure = ($updated_failure == null) ? sfConfig::get('app_ajax_feedback_div_name_failure') : $updated_failure;
    return form_remote_tag(array(
                                'update' => array(
                                            'success' => $updated_success,
                                            'failure' => $updated_failure),
                                'position' => 'bottom',
                                'url' => $url,
                                'method' => 'post',
                                'loading' => "Element.show('$indicator')",
                                'complete' => "Element.hide('$indicator');",
                                'success'  => "Element.hide('$updated_failure');" . ($removed_id == null ? '' : "$('$removed_id').hide();"),
                                'failure'  => "Element.show('$updated_failure');setTimeout('emptyFeedback(" .'"'. $updated_failure .'"'. ")', 4000);"));
}

function c2c_link_to_delete_element($link_type,
                                    $main_id,
                                    $linked_id,
                                    $main_doc = true,
                                    $strict = 1,
                                    $updated_failure = null,
                                    $indicator = 'indicator')
{
    // NB : $del_image_id is for internal use, but will be useful when we have several delete forms in same page
    $main_doc = ($main_doc) ? 'true' : 'false';
    $updated_failure = ($updated_failure == null) ? sfConfig::get('app_ajax_feedback_div_name_failure') : $updated_failure;
    return link_to(picto_tag('action_del_light', __('Delete this association')),
                         '#',
                         array('onclick' => "remLink('$link_type', $main_id, $linked_id, $main_doc, $strict); return false;"));
}

function c2c_form_add_multi_module($module, $id, $modules_list, $default_selected, $form_id = 'list_associated_docs', $hide = true)
{
    $modules_list = array_intersect(sfConfig::get('app_modules_list'), $modules_list);
    $modules_list_i18n = array_map('__', $modules_list);
    $select_js = 'var c=this.classNames().each(function(i){$(\'dropdown_modules\').removeClassName(i)});this.addClassName(\'picto picto_\'+$F(this));';
    $select_modules = select_tag('dropdown_modules', options_with_classes_for_select($modules_list_i18n, array($default_selected), array(), 'picto picto_'),
                    array('onchange' => $select_js, 'class' => 'picto picto_' . $default_selected));
    
    $form = $form_id . '_form';
    $picto_add = ($hide) ? '' : picto_tag('picto_add', __('Link an existing document')) . ' ';
    
    $out = $picto_add . $select_modules;

    $out .= observe_field('dropdown_modules', array(
        'update' => $form,
        'url' => '/documents/getautocomplete',
        'with' => "'module_id=' + value + '&form_id=$form_id'",
        'script' => 'true',
        'loading' => "Element.show('indicator')",
        'complete' => "Element.hide('indicator')"));

    $out .= c2c_form_remote_add_element("$module/addAssociation?form_id=$form_id&main_id=$id", $form_id);

    $out .= '<div id="' . $form . '" class="ac_form">'
          . input_hidden_tag($form_id . '_document_id', '0')
          . c2c_auto_complete($modules_list[$default_selected], $form_id . '_document_id')
          . '</div></form>';
    
    $out = '<div class="doc_add">'
         . $out
         . '</div>';
    
    if ($hide)
    {
        $picto_add = picto_tag('picto_add');
        $picto_rm = picto_tag('picto_rm');
        $pictos = $picto_add . $picto_rm;
        foreach ($modules_list as $module)
        {
            $pictos .= picto_tag('picto_' . $module);
        }
        
        $pictos = link_to_function($pictos, "toggleForm('$form_id')",
                                   array('class' =>'add_content',
                                         'title' => __('Link an existing document')));
        
        $out = '<div class="one_kind_association empty_content">'
             . '<div class="association_tool hide" id="' . $form_id . '_association">'
             . $pictos
             . '<ul id="' . $form_id . '"></ul>'
             . $out
             . '</div></div>';
    }

    return $out;
}
