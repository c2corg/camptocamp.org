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
function m_link_to($name, $url, $html_options = array(), $modal_options = array())
{
    use_helper('Javascript');
    
    if (is_string($html_options))
    {
        $html_options = array('title' => __($html_options));
    }
    else
    {
        $html_options = array();
    }
    if (array_key_exists('title', $html_options))
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

    // scriptaculous & prototype
    $response->addJavascript('/static/js/prototype.js', 'head_first');
    //$response->addJavascript('scriptaculous.js', 'head');
    
    // FIXME: these 4 files are not loaded automatically (are they?)
    // when ModalBox is used in conjonction with sfCombineFilterPlugin or MyMinifyPlugin, so that we must add them here:
    $response->addJavascript('/static/js/builder.js', 'first');
    $response->addJavascript('/static/js/effects.js', 'head'); // needed by controls.js
    $response->addJavascript('/static/js/dragdrop.js', 'first'); // needed for sorting lists in modalboxes
    $response->addJavascript('/static/js/controls.js', 'head'); // needed in head for autocomplete in modalboxes

    if (!c2cTools::mobileVersion())
    {
        $response->addJavascript('/static/js/modalbox.js', 'last');
        $response->addStylesheet('/static/css/modalbox.css', 'last');
    }
    $response->addJavascript('/static/js/submit.js', 'last');
}

loadRessources();
