<?php

function minify_get_head_javascripts($combine = true, $debug = false)
{
  if (!$combine)
  {
    use_helper('MyJavascriptStyleSheet');
    return include_head_javascripts($debug);
  }

  return minify_get_javascripts(array('head_first', 'head', 'head_last'), $debug); 
}

function minify_get_body_javascripts($combine = true, $debug = false)
{
  if (!$combine)
  {
    use_helper('MyJavascriptStyleSheet');
    return include_body_javascripts($debug);
  }

  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('javascripts_included', true, 'symfony/view/asset');

  // prototype is added with position='' by JavascriptHelper. We don't want it here (added in head)
  $my_already_seen = array(sfConfig::get('app_static_url').sfConfig::get('sf_prototype_web_dir').'/js/prototype' => 1);

  return minify_get_javascripts(array('first', '', 'last'), $debug, $my_already_seen);
}

function minify_get_javascripts($position_array = array('first', '', 'last'), $debug = false, $my_already_seen = array())
{
  $response = sfContext::getInstance()->getResponse();
  $already_seen = $my_already_seen;
  $minify_files = array();
  $external_files = array();
  foreach ($position_array as $position)
  {
    foreach ($response->getJavascripts($position) as $files)
    {
      if (!is_array($files))
      {
        $files = array($files);
      }

      $options = array_merge(array('type' => 'text/javascript'));
      foreach ($files as $file)
      {
        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;

        // check if the javascript is on this server // TODO better handle + what if user wants to precisely place the call??
        if (preg_match('/http(s)?:\/\//', $file))
        {
          $external_files[] = $file;
          break;
        }

        $file = javascript_path($file);

        $type = serialize($options);

        if(isset($minify_files[$type]))
        {
          array_push($minify_files[$type], $file);
        }
        else
        {
          $minify_files[$type] = array($file);
        }
      }
    }
  }

  $html = '';
  foreach ($external_files as $file)
  {
    $html .= javascript_include_tag($file);
  }
  foreach ($minify_files as $options => $files)
  {
    $options = unserialize($options);
    if ($debug)
    {
      $options['src'] = join($files, ',').'?debug';
    }
    else
    {
      $filenames = array();
      foreach ($files as $file)
      {
        $file_parts = explode('/', $file);
        array_push($filenames, end($file_parts));
      }
      $max_rev = count($filenames) ? sfSVN::getHeadRevision($filenames) : '';
      $options['src'] = join($files, ',').(!empty($max_rev) ? '?'.$max_rev : '');
    }
    $html .= content_tag('script', '', $options)."\n";
  }

  return $html;
}

function minify_include_head_javascripts($combine = true, $debug = false)
{
  echo minify_get_head_javascripts($combine, $debug);
}

function minify_include_body_javascripts($combine = true, $debug = false)
{
  echo minify_get_body_javascripts($combine, $debug);
}

function minify_get_main_stylesheets($combine = true, $debug = false)
{
  if (!$combine)
  {
    use_helper('MyJavascriptStyleSheet');
    return get_all_stylesheets();
  }

  return minify_get_stylesheets(array('first', '', 'last'), $debug); 
}

function minify_get_custom_stylesheets($combine = true, $debug = false)
{
  if (!$combine)
  {
    return;
  }

  return minify_get_stylesheets(array('custom_first', 'custom', 'custom_last'), $debug); 
}

function minify_get_stylesheets($position_array = array('first', '', 'last'), $debug = false, $my_already_seen = array())
{
  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('stylesheets_included', true, 'symfony/view/asset');

  $already_seen = $my_already_seen;
  $minify_files = array();
  foreach ($position_array as $position)
  {
    foreach ($response->getStylesheets($position) as $files => $options)
    {
      if (!is_array($files))
      {
        $files = array($files);
      }

      $options = array_merge(array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen'), $options);
      foreach ($files as $file)
      {
        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;

        $absolute = false;
        if (isset($options['absolute']))
        {
          unset($options['absolute']);
          $absolute = true;
        }

        if(!isset($options['raw_name']))
        {
          $file = stylesheet_path($file, $absolute);
        }
        else
        {
          unset($options['raw_name']);
        }

        $type = serialize($options);

        if(isset($minify_files[$type]))
        {
          array_push($minify_files[$type], $file);
        }
        else
        {
          $minify_files[$type] = array($file);
        }
      }
    }
  }

  $html = '';
  foreach($minify_files as $options => $files)
  {
    $options = unserialize($options);
    if ($debug)
    {
      $options['href'] = join($files, ',').'?debug';
    }
    else
    {
      $filenames = array();
      foreach ($files as $file)
      {
        $file_parts = explode('/', $file);
        array_push($filenames, end($file_parts));
      }
      $max_rev = count($filenames) ? sfSVN::getHeadRevision($filenames) : '';
      $options['href'] = join($files, ',').(isset($max_rev) ? "?$max_rev" : '');
    }
    $html .= tag('link', $options)."\n";
  }
  return $html;
}

function minify_include_main_stylesheets($combine = true, $debug = false)
{
  echo minify_get_main_stylesheets($combine, $debug);
}

function minify_include_custom_stylesheets($combine = true, $debug = false)
{
  echo minify_get_custom_stylesheets($combine, $debug);
}
