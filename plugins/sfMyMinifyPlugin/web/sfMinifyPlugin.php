<?php

define('SF_ROOT_DIR', realpath(dirname(__FILE__).'/../../..'));
define('SF_APP', 'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG', false);
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$webdir = sfConfig::get('sf_web_dir');

/**
 * The Files controller only "knows" HTML, CSS, and JS files. Other files
 * would only be trim()ed and sent as plain/text.
 */
$serveExtensions = array('css', 'js');

// is debug mode set?
if (isset($_GET['no/'])) {
  $debug = true;
}
else
{
  $debug = false;
}

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
      if (!file_exists($webdir . $file))
      {
        $error = true;
      }
      else
      {
        $files[$key] = $webdir . $file;
      }
    }

    if(!$error)
    {
      set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'minify'.DIRECTORY_SEPARATOR.'min'.DIRECTORY_SEPARATOR.'lib');

      // minify class loading
      require 'Minify/Loader.php';
      Minify_Loader::register();

      // Config stuff. NOte that we DON'T USE min/config.php file

      // check for URI versioning
      if (preg_match('/&[a-f0-9]{8}\/$/', $_SERVER['QUERY_STRING'])) {
        $maxAge = 31536000;
      }
      else
      {
        $maxAge = 86400;
      }

      $options = array('files' => $files,
                       'maxAge' => $maxAge,
                       'debug' => false,
                       'bubbleCssImports' => false);

      // Do not minify files with names containing .min or -min before the extension 
      // (only works with MinApp Controller)
      $options['minApp']['noMinPattern'] = '@[-\\.]min\\.(?:js|css)$@i';

      if ($debug) // debug = we don't minify. But we don't add /* line numbers */ (thus option debug = false)
      {
        $options['minifiers'] = array(Minify::TYPE_JS => '', Minify::TYPE_CSS => '');
      }

      if (sfConfig::get('sf_cache'))
      {
        $minifyCachePath = sfConfig::get('sf_config_cache_dir') . DIRECTORY_SEPARATOR . 'minify';
        if(!is_dir($minifyCachePath))
        {
          mkdir($minifyCachePath);
        }
        Minify::setCache($minifyCachePath);
      }

      // serve the file
      Minify::serve('MinApp', $options);
      exit();
    }
  }
}
$s = $_SERVER["SERVER_PROTOCOL"]." 404 Not Found";
header($s);
echo $s;
