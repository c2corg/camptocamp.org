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
    //$response->setParameter('javascripts_included', true, 'symfony/view/asset'); this is done in _body function
    return _include_javascripts(array('head_first', 'head', 'head_last'), $debug);
}

function include_body_javascripts($debug = false)
{
    $response = sfContext::getInstance()->getResponse();
    $response->setParameter('javascripts_included', true, 'symfony/view/asset');

    $static_base_url = sfConfig::get('app_static_url');
    $already_seen = array();

    // prototype, effects and controls are added with position='' by JavascriptHelper. We don't want it here (added in head)
    // FIXME we could probably do that a cleaner way by looking if already present in head javascripts (but this way is maybe quicker)
    $already_seen['/static/js/prototype.js'] = 1;
    $already_seen['/static/js/effects.js'] = 1;
    $already_seen['/static/js/controls.js'] = 1;

    return _include_javascripts(array('first', '', 'last'), $debug, $already_seen);
}

function include_maps_javascripts($debug = false)
{
    if (!sfConfig::get('app_async_map', true) || sfContext::getInstance()->getRequest()->getparameter('action') == 'map')
    {
        return _include_javascripts(array('maps'), $debug);
    }
}

function _include_javascripts($position_array = array('first', '', 'last'), $debug = false, $my_already_seen = array())
{
    $response = sfContext::getInstance()->getResponse();
    $static_base_url = sfConfig::get('app_static_url');

    $already_seen = $my_already_seen;
    $internal_files = array();
    $external_files = array();

    foreach ($position_array as $position)
    {
        foreach ($response->getJavascripts($position) as $files)
        {
            if (!is_array($files))
            {
                $files = array($files);
            }

            foreach ($files as $file)
            {
                // be sure to normalize files with .js at the end
                $file .= substr($file, -3) === '.js' ? '' : '.js';

                if (isset($already_seen[$file])) continue;

                $already_seen[$file] = 1;

                // check if the javascript is on this server // TODO better handle + what if user wants to precisely place the call??
                if (preg_match('/http(s)?:\/\//', $file))
                {
                    $external_files[] = $file;
                    break;
                }

                $file = javascript_path($file);

                $internal_files[] = $file;
            }
        }
    }

    $html = '';

    foreach ($external_files as $file)
    {
        $html .= javascript_include_tag($file);
    }

    foreach ($internal_files as $file)
    {
        $prefix = $debug ? '/no' : '';
        $ts = sfTimestamp::getTimestamp($file);
        if (!empty($ts))
        {
            $file = '/' . $ts . $prefix . $file;
        }
        else
        {
            $file = $prefix . $file;
        }

        $html .= javascript_include_tag($static_base_url . $file);
    }

    return $html;
}


/**
 * This one is a copy from get_stylesheets from symfony except that it also looks for custom css
 */
function get_all_stylesheets($debug = false)
{
  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('stylesheets_included', true, 'symfony/view/asset');

  $static_base_url = sfConfig::get('app_static_url');
  $already_seen = array();
  $html = '';

  foreach (array('first', '', 'last', 'print', 'custom_first', 'custom', 'custom_last') as $position)
  {
    foreach ($response->getStylesheets($position) as $files => $options)
    {
      if (!is_array($files))
      {
        $files = array($files);
      }

      foreach ($files as $file)
      {
        $file = stylesheet_path($file);

        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;
        $ts = sfTimestamp::getTimestamp($file);
        $prefix = $debug ? '/no' : '';
        $prefix = empty($ts) ? $prefix : '/' . $ts . $prefix;
        $html .= stylesheet_tag($static_base_url . $prefix . $file, $options);
      }
    }
  }

  return $html;
}
