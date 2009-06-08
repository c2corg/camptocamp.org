<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false); 

/**
 * Add the location of Minify's "lib" directory to the include_path. In
 * production this could be done via .htaccess or some other method.
 */
ini_set('include_path', $configuration->getRootDir() . '/plugins/sfMinifyPlugin/minify/lib' . PATH_SEPARATOR . ini_get('include_path'));

/**
 * The Files controller only "knows" HTML, CSS, and JS files. Other files
 * would only be trim()ed and sent as plain/text.
 */
$serveExtensions = array('css', 'js');

// serve
if (isset($_GET['f']))
{
  $filenamePattern = '/(' . implode('|', $serveExtensions).   ')$/';
  if(preg_match($filenamePattern, $_GET['f'], $matches))
  {
    $files = split(',', $_GET['f']);
    $error = false;

    foreach($files as $key => $file)
    {
      if (!file_exists(dirname(__FILE__) . $file))
      {
        $error = true;
      }
      else
      {
        $files[$key] = dirname(__FILE__) . $file;
      }
    }

    if(!$error)
    {
      require 'Minify.php';

      /**
       * Set $minifyCachePath to a PHP-writeable path to enable server-side caching
       * in all examples and tests.  
       */
      if (sfConfig::get('sf_cache'))
      {
        $minifyCachePath = sfConfig::get('sf_config_cache_dir') . DIRECTORY_SEPARATOR . 'minify';
        if(!is_dir($minifyCachePath))
        {
          mkdir($minifyCachePath);
        }
        Minify::useServerCache($minifyCachePath);
      }

      Minify::serve('Files', array('files' => $files));
      exit();
    }
  }
}
header("HTTP/1.0 404 Not Found");
echo "HTTP/1.0 404 Not Found";
