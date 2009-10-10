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

function loadAutoCompleteRessources()
{
    $static_base_url = sfConfig::get('app_static_url');

    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript($static_base_url . '/static/js/association.js?' . sfSVN::getHeadRevision('association.js'));
}
loadAutoCompleteRessources();

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

function c2c_form_add_multi_module($module, $id, $modules_list, $default_selected)
{
    $modules_list = array_intersect(sfConfig::get('app_modules_list'), $modules_list);
    $modules_list_i18n = array_map('__', $modules_list);
    $select_js = 'var c=this.classNames().each(function(i){$(\'type\').removeClassName(i)});this.addClassName(\'picto picto_\'+$F(this));';
    $select_modules = select_tag('dropdown_modules', options_with_classes_for_select($modules_list_i18n, array($default_selected), array(), 'picto picto_'),
                    array('onchange' => $select_js, 'class' => 'picto picto_' . $default_selected));
    $out = '<div id="doc_add">'
       . picto_tag('picto_add', __('Link an existing document')) . ' '
       . $select_modules
       . '</div>';

    $out .= observe_field('dropdown_modules', array(
        'update' => 'ac_form',
        'url' => '/documents/getautocomplete',
        'with' => "'module_id=' + value",
        'script' => 'true',
        'loading' => "Element.show('indicator')",
        'complete' => "Element.hide('indicator')"));

    $id_name = substr($module, 0, -1) . '_id';
    $out .= c2c_form_remote_add_element("$module/addassociation?$id_name=$id", 'list_associated_docs');

    $out .= '<div id="ac_form">'
          . input_hidden_tag('document_id', '0')
          . c2c_auto_complete($modules_list[$default_selected], 'document_id')
          . '</div></form>';

    return $out;
}
