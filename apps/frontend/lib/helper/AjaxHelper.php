<?php
/**
 * Ajax Helper
 * @version $Id: AjaxHelper.php 2216 2007-10-29 15:34:07Z jbaubort $
 */
use_helper('Tag', 'Javascript');

function ajax_feedback($inline = false)
{
    $afs = sfConfig::get('app_ajax_feedback_div_name_success');
    $aff = sfConfig::get('app_ajax_feedback_div_name_failure');

    $indicator = content_tag('div', __(' loading...'), array('id' => 'indicator', 'style' => 'display: none;'));

    $style = ($inline) ? sfConfig::get('app_ajax_feedback_div_style_inline') : sfConfig::get('app_ajax_feedback_div_style_absolute');

    $ajax_success_feedback = content_tag('div', '', array('id' => $afs,
                                                          'class' => $style,
                                                          'style' => 'display:none;'));

    $ajax_failure_feedback = content_tag('div', '', array('id' => $aff,
                                                          'class' => $style,
                                                          'style' => 'display:none;'));

    return $indicator.$ajax_success_feedback.$ajax_failure_feedback;
}

function c2c_form_remote_tag($url, $options = array())
{
    $afs = sfConfig::get('app_ajax_feedback_div_name_success');
    $aff = sfConfig::get('app_ajax_feedback_div_name_failure');

    return form_remote_tag(array(
            'update'   => array('success' => $afs,
                                'failure' => $aff),
            'url'      => $url,
            'loading'  => "Element.show('indicator')",
            'complete' => "Element.hide('indicator'); ",
            'success'  => "Element.show('$afs'); Element.hide('$aff');" . visual_effect('highlight', $afs),
            'failure'  => "Element.hide('$afs'); Element.show('$aff');" . visual_effect('highlight', $aff),
            'script' => true,
    ), $options);
}
