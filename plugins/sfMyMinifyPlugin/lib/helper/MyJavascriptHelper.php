<?php

/**
 * These two functions are used to differentiate between scripts
 * that should be put in the head section of the document
 * and the ones that can be left at the end of the body,
 * in order to optimize performances
 * Look for include_javascripts from symfony to understand what happens here
 */

function include_head_javascripts($debug = false)
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

                if ($debug)
                {
                    $file = $file . '?debug';
                }
                else
                {
                    $rev = sfSVN::getHeadRevision($filenames);
                    if (!empty($rev))
                    {
                        $file = $file . '?' . $rev;
                    }
                }

                $html .= javascript_include_tag($file);
            }
        }
    }
    echo $html;
}

function include_body_javascripts($debug = false)
{
    $response = sfContext::getInstance()->getResponse();
    $response->setParameter('javascripts_included', true, 'symfony/view/asset');

    $already_seen = array();

    // prototype, effects and controls are added with position='' by JavascriptHelper. We don't want it here (added in head)
    // FIXME we could probably do that a cleaner way by lokking if already present in head javascripts (but this way is maybe quicker)
    $static_base_url = sfConfig::get('app_static_url');
    $already_seen[javascript_path($static_base_url . '/static/js/prototype.js')] = 1;
    $already_seen[javascript_path($static_base_url . '/static/js/effects.js')] = 1;
    $already_seen[javascript_path($static_base_url . '/static/js/controls.js')] = 1;

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

                if ($debug)
                {
                    $file = $file . '?debug';
                }
                else
                {
                    $rev = sfSVN::getHeadRevision($filenames);
                    if (!empty($rev))
                    {
                        $file = $file . '?' . $rev;
                    }
                }


                $html .= javascript_include_tag($file);
            }
        }
    }
    echo $html;
}
