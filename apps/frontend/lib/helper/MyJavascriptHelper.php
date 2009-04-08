<?php

/**
 * These two functions are used to differentiate between scripts
 * that should be put in the head section of the document
 * and the ones that can be left at the end of the body,
 * in order to optimize performances
 * Look for include_javascripts from symfony to understand what happens here
 */

function include_head_javascripts()
{
    $response = sfContext::getInstance()->getResponse();
    //$response->setParameter('javascripts_included', true, 'symfony/view/asset'); this is done in _body function

    $already_seen = array();
    $html = '';

    foreach (array('head_first', 'head', 'head_last') as $position)
    {
        foreach ($response->getJavascripts($position) as $files)
        {
            if (!is_array($files))
            {
                $files = array($files);
            }

            foreach ($files as $file)
            {
                $file = javascript_path($file);

                if (isset($already_seen[$file])) continue;

                $already_seen[$file] = 1;
                $html .= javascript_include_tag($file);
            }
        }
    }
    echo $html;
}

function include_body_javascripts()
{
    $response = sfContext::getInstance()->getResponse();
    $response->setParameter('javascripts_included', true, 'symfony/view/asset');

    $already_seen = array();

    // prototype is added with position='' by JavascriptHelper. We don't want it here (added in head)
    $already_seen[javascript_path(sfConfig::get('app_static_url').sfConfig::get('sf_prototype_web_dir').'/js/prototype.js')] = 1;

    $html = '';

    foreach (array('first', '', 'last') as $position)
    {
        foreach ($response->getJavascripts($position) as $files)
        {
            if (!is_array($files))
            {
                $files = array($files);
            }

            foreach ($files as $file)
            {
                $file = javascript_path($file);

                if (isset($already_seen[$file])) continue;

                $already_seen[$file] = 1;
                $html .= javascript_include_tag($file);
            }
        }
    }
    echo $html;
}
