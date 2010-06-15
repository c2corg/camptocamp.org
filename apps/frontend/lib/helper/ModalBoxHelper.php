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

    if (!c2cTools::mobileVersion())
    {
        $html_options['onclick'] = 'Modalbox.show(this.href, ' . $js_options . '); return false;';
    }

    return link_to($name, $url, $html_options);
}

function loadRessources()
{
    $response = sfContext::getInstance()->getResponse();
    $static_base_url = sfConfig::get('app_static_url');

    // scriptaculous & prototype
    $response->addJavascript($static_base_url . '/static/js/prototype.js', 'head_first');
    //$response->addJavascript($prototype_url . 'scriptaculous.js', 'head');
    
    // FIXME: these 4 files are not loaded automatically (are they?)
    // when ModalBox is used in conjonction with sfCombineFilterPlugin or MyMinifyPlugin, so that we must add them here:
    $response->addJavascript($static_base_url . '/static/js/builder.js', 'first');
    $response->addJavascript($static_base_url . '/static/js/effects.js', 'head'); // needed by controls.js
    $response->addJavascript($static_base_url . '/static/js/dragdrop.js', 'first'); // needed for sorting lists in modalboxes
    $response->addJavascript($static_base_url . '/static/js/controls.js', 'head'); // needed in head for autocomplete in modalboxes

    if (!c2cTools::mobileVersion())
    {
        $response->addJavascript($static_base_url . '/static/js/modalbox.js', 'last');
        $response->addStylesheet($static_base_url . '/static/css/modalbox.css', 'last');
    }
    $response->addJavascript($static_base_url . '/static/js/submit.js', 'last');
}

loadRessources();
