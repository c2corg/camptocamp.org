<?php

/************************************************************************
 * CSS and Javascript Combinator 0.5
 * Copyright 2006 by Niels Leenheer
 * http://rakaz.nl/extra/code/combine
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

// Minor modifications by Scott Meves for use with sfCombineFilterPlugin.

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../..'));
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$cache     = true;
$minify_js = true;

$cachedir  = SF_ROOT_DIR.DIRECTORY_SEPARATOR.'cache';
$webdir    = SF_ROOT_DIR.DIRECTORY_SEPARATOR.'web';
$cssdir    = $webdir.DIRECTORY_SEPARATOR.'css';
$jsdir     = $webdir.DIRECTORY_SEPARATOR.'js';

// Determine the directory and type we should use
switch ($_GET['type']) {
  case 'css':
    $dir = $cssdir;
    break;
  case 'javascript':
    $dir = $jsdir;
    break;
  default:
    header ("HTTP/1.0 503 Not Implemented");
    exit;
}

$type = $_GET['type'];
$elements = explode(',', $_GET['files']);
if ($elements[0] == '') {
  array_shift($elements);
}

// Determine last modification date of the files
$lastmodified = 0;

$files = array();
while (list(,$element) = each($elements)) {
  $path = null;
  if (substr($element, 0, 4) == '/sf/') {
    $path = $sf_symfony_data_dir.DIRECTORY_SEPARATOR.'web'.$element;
  } else if (substr($element, 0, 3) == 'sf/') {
    $path = $sf_symfony_data_dir.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$element;
  } else if (0 === strpos($element, '/')) {
    $path = realpath($webdir.$element);
  } else {
    $path = realpath($dir.DIRECTORY_SEPARATOR.$element);
  }

  if (($type == 'javascript' && substr($path, -3) != '.js') ||
    ($type == 'css' && substr($path, -4) != '.css')) {
    header ("HTTP/1.0 403 Forbidden");
    exit;
  }

  if (!file_exists($path)) {
    header ("HTTP/1.0 404 Not Found");
    exit;
  }

  $files[] = $path;
  $lastmodified = max($lastmodified, filemtime($path));
}

// Send Etag hash
$hash = $lastmodified . '-' . md5($_GET['files']);
header ("Etag: \"" . $hash . "\"");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
  stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"')
{
  // Return visit and no modifications, so do not send anything
  header ("HTTP/1.0 304 Not Modified");
  header ('Content-Length: 0');
}
else
{

  // First time visit or files were modified
  if ($cache)
  {
    // Determine supported compression method
    $gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
    $deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');

    // Determine used compression method
    $encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');

    // Check for buggy versions of Internet Explorer
    if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
      preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
      $version = floatval($matches[1]);

      if ($version < 6)
        $encoding = 'none';

      if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
        $encoding = 'none';
    }

    // Try the cache first to see if the combined files were already generated

    $cachefile = 'cache-' . $hash . '.' . $type . ($encoding != 'none' ? '.' . $encoding : '');

    if (file_exists($cachedir . '/' . $cachefile)) {
      if ($fp = fopen($cachedir . '/' . $cachefile, 'rb')) {

        if ($encoding != 'none') {
          header ("Content-Encoding: " . $encoding);
        }

        header ("Content-Type: text/" . $type);
        header ("Content-Length: " . filesize($cachedir . '/' . $cachefile));

        fpassthru($fp);
        fclose($fp);
        exit;
      }
    }
  }

  // Get contents of the files
  $contents = '';
  reset($elements);
  foreach ($files as $path) {
    $contents .= "\n\n" . file_get_contents($path);
  }

  if ($_GET['type'] == 'javascript' && $minify_js) {
    include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'JSMin.class.php';
    try {
      $contents = JSMin::minify($contents);
    } catch (JSMinException $e) {
      ;
    }
  }


  // Send Content-Type
  header ("Content-Type: text/" . $type);

  if (isset($encoding) && $encoding != 'none')
  {
    // Send compressed contents
    $contents = gzencode($contents, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);

    header ("Content-Encoding: " . $encoding);
    header ('Content-Length: ' . strlen($contents));
    echo $contents;
  }
  else
  {
    // Send regular contents
    header ('Content-Length: ' . strlen($contents));
    echo $contents;
  }

  // Store cache
  if ($cache) {
    file_put_contents($cachedir . '/' . $cachefile, $contents);
  }
}