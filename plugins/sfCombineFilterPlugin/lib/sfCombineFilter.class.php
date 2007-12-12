<?php
/*
 * This file is part of the sfCombineFilter package.
 *
 * sfCombineFilter.class.php (c) 2007 Scott Meves.
 * Combine.php Copyright (c) 2006 by Niels Leenheer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This filter combines requested js and css files into a single request each.
 *
 * @package      sfCombineFilter
 * @subpackage   filter
 * @author       Scott Meves <scott@stereointeractive.com>
 *
 */
class sfCombineFilter extends sfFilter
{

  public function execute ($filterChain)
  {
    $filterChain->execute();

    if ($this->getParameter('javascripts', true)) {
      $this->getCombinedJavascripts();
    }

    if ($this->getParameter('stylesheets', true)) {
      $this->getCombinedStylesheets();
    }
  }

  protected function getCombinedJavascripts()
  {
    $response = $this->getContext()->getResponse();
    $sf_relative_url_root = $this->getContext()->getRequest()->getRelativeUrlRoot();
    $root_js_only = $this->getParameter('root_js_only', true);

    $already_seen = array();
    $combined_sources = array();

    foreach (array('first', '', 'last') as $position)
    {
      foreach ($response->getJavascripts($position) as $files => $options)
      {
        if (!is_array($files))
        {
          $files = array($files);
        }

        foreach ($files as $file)
        {
          if (isset($already_seen[$file])) continue;

          $already_seen[$file] = 1;

          if (is_array($options) && $this->isAbsolutePath($options))
          {
            continue;
          }

          $path = _compute_public_path($file, 'js', 'js');

          if ((!$root_js_only && !strpos($path, '://')) || ($root_js_only && strpos($path, $sf_relative_url_root.'/js/') === 0)) {
            $combined_sources[] = ($root_js_only ? preg_replace("/^".str_replace('/', '\/', $sf_relative_url_root.'/js/')."/i", '', $path) : $path);
            $response->getParameterHolder()->remove($file, 'helper/asset/auto/javascript'.($position ? '/'.$position : ''));
          }
        }
      }
    }

    if (count($combined_sources)) {
      $combined_sources_str = $sf_relative_url_root . '/js/packed/' . implode(',', $combined_sources);
      $response->addJavascript($combined_sources_str, '');
    }

  }

  protected function getCombinedStylesheets()
  {
    $response = $this->getContext()->getResponse();
    $sf_relative_url_root = $this->getContext()->getRequest()->getRelativeUrlRoot();
    $root_css_only = $this->getParameter('root_css_only', true);

    $already_seen = array();
    $combined_sources = array();

    foreach (array('first', '', 'last') as $position)
    {
      foreach ($response->getStylesheets($position) as $files => $options)
      {
        if (!is_array($files))
        {
          $files = array($files);
        }

        foreach ($files as $file)
        {
          if (isset($already_seen[$file])) continue;

          $already_seen[$file] = 1;

          if (is_array($options) && ($this->isInvalidMediaType($options) || $this->isAbsolutePath($options)))
          {
            continue;
          }

          $path = _compute_public_path($file, 'css', 'css');

          if ((!$root_css_only && !strpos($path, '://')) || ($root_css_only && strpos($path, $sf_relative_url_root.'/css/') === 0)) {
            $combined_sources[] = (!$root_css_only ? preg_replace("/^".str_replace('/', '\/', $sf_relative_url_root.'/css/')."/i", '', $path) : $path);
            $response->getParameterHolder()->remove($file, 'helper/asset/auto/stylesheet'.($position ? '/'.$position : ''));
          }
        }
      }
    }

    if (count($combined_sources)) {
      $combined_sources_str = $sf_relative_url_root . '/css/packed/' . implode(',', $combined_sources);
      $response->addStylesheet($combined_sources_str, '', array('raw_name'=>true));
    }
  }

  protected function isInvalidMediaType($options) {
    return isset($options['media']) && !in_array($options['media'], array('', 'all', 'screen'));
  }

  protected function isAbsolutePath($options) {
    return isset($options['absolute']) && $options['absolute'] == true;
  }

}
