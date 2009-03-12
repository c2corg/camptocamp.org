<?php

/**
 * @package sfModalBoxPlugin
 *
 * @author Mickael Kurmann <mickael.kurmann@gmail.com>
 * @since  22 Apr 2007
 * @version 1.0.0
 *
 */

/**
 * Enable to use Modalbox script : http://okonet.ru/projects/modalbox/
 *
 * *
 * @author Gerald Estadieu <gestadieu@gmail.com>
 * @since  15 Apr 2007
 *
 */
function m_link_to($name, $url, $html_options, $modal_options = array())
{
    use_helper('Javascript');
    
    if(array_key_exists('title', $html_options))
    {
        $modal_options = array_merge($modal_options, array('title' => 'this.title'));
    }
    
    $js_options = _options_for_javascript($modal_options);

    $html_options['onclick'] = "Modalbox.show(this.href, " . $js_options . "); return false;";

    return link_to($name, $url, $html_options);
}


function loadRessources()
{
    $static_base_url = sfConfig::get('app_static_url');
    $prototype_url = $static_base_url . sfConfig::get('sf_prototype_web_dir') . '/js/';

    // Prototype & scriptaculous
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript($prototype_url . 'prototype.js');
    $response->addJavascript($prototype_url . 'scriptaculous.js');
    
    // FIXME: these 4 files are not loaded automatically (are they?)
    // when ModalBox is used in conjonction with sfCombineFilterPlugin, so that we must add them here:
    /*
    $response->addJavascript($prototype_url . 'builder.js');
    $response->addJavascript($prototype_url . 'effects.js');
    $response->addJavascript($prototype_url . 'dragdrop.js'); // needed for sorting lists in modalboxes
    $response->addJavascript($prototype_url . 'controls.js'); // needed for autocomplete in modalboxes
    */

    $response->addJavascript($static_base_url . '/sfModalBoxPlugin/js/modalbox.js', 'last');
    $response->addJavascript($static_base_url . '/static/js/submit.js?' . sfSVN::getHeadRevision('submit.js'), 'last');
    $response->addStylesheet($static_base_url . '/sfModalBoxPlugin/css/modalbox.css');
}

loadRessources();
