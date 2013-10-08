<?php
use_helper('Tag');

function ajax_feedback($inline = false)
{
    $afs = sfConfig::get('app_ajax_feedback_div_name_success');
    $aff = sfConfig::get('app_ajax_feedback_div_name_failure');

    $indicator = content_tag('div', __(' loading...'), array('id' => 'indicator', 'style' => 'display:none;'));

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
    $url = url_for($url);

    $js = "jQuery('#indicator').show();
jQuery.post('$url', jQuery(this).serialize())
  .always(function() { jQuery('#indicator').hide(); })
  .fail(function(data) { C2C.showFailure(data.responseText); })
  .success(function(data) { C2C.showSuccess(data); });
return false;";
  
    $options['action'] = $url;
    $options['method'] = isset($options['method']) ? $options['method'] : 'post';
    $options['onsubmit'] = $js;

    return tag('form', $options, true);
}
