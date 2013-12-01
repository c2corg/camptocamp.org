<?php

function m_link_to($name, $url, $html_options = array(), $modal_options = array())
{
    use_helper('Javascript');
    
    if (is_string($html_options))
    {
        $html_options = array('title' => __($html_options));
    }
    elseif (!is_array($html_options))
    {
        $html_options = array();
    }

    // modalbox specific options
    if (array_key_exists('title', $html_options))
    {
        $modal_options = array_merge($modal_options, array('title' => 'this.title'));
    }
    if (!array_key_exists('remote', $modal_options))
    {
        $modal_options['remote'] = 'this.href';
    }

    $js_options = _options_for_javascript($modal_options);

    if (!c2cTools::mobileVersion())
    {
        $html_options['onclick'] = "$.modalbox.show($js_options); return false;";
    }

    return link_to($name, $url, $html_options);
}

function loadRessources()
{
    $response = sfContext::getInstance()->getResponse();

    if (!c2cTools::mobileVersion())
    {
        $response->addJavascript('/static/js/modal.js', 'last');
        $response->addStylesheet('/static/css/modal.css', 'last');
    }
}

loadRessources();
