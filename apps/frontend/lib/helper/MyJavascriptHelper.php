<?php

/**
 * These two functions are used to differentiate between scripts
 * that should be put in the head section of the document
 * and the ones that can be left at the end of the body,
 * in order to optimize performances
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
    return $html;
}

function include_body_javascripts()
{
    include_javascripts();
}
