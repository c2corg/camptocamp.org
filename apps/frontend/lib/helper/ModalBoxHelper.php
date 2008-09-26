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
    // Prototype & scriptaculous
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/scriptaculous');
    
    // FIXME: these 4 files are not loaded automatically 
    // when ModalBox is used in conjonction with sfCombineFilterPlugin, so that we must add them here:
    /*
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/builder');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/dragdrop'); // needed for sorting lists in modalboxes
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/controls'); // needed for autocomplete in modalboxes
    */

    $response->addJavascript('/sfModalBoxPlugin/js/modalbox', 'last');
    $response->addJavascript('/static/js/submit', 'last');
    $response->addStylesheet('/sfModalBoxPlugin/css/modalbox');
}

loadRessources();
