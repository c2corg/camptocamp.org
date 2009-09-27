<?php
/**
 * Autocomplete tools
 * @version $Id:$
 */

// FIXME : dirty trick
if (isset($sf_user)) 
{
    // we are in a template 
    use_helper('Javascript','Tag','Url','I18N','Asset', 'Form', 'General');
}
else
{
    // we are in an action
    sfLoader::loadHelpers(array('Tag','Url','I18N','Asset', 'Form', 'Javascript', 'General'));
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

function c2c_input_auto_complete($module, $update_hidden, $display = '', $field = 'name', $size = '50')
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
    return ' ' . link_to(picto_tag('action_delete', __('Delete this association')),
                         '#',
                         array('onclick' => "remLink('$link_type', $main_id, $linked_id, $main_doc, $strict); return false;"));
}
