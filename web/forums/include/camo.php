<?php
/*****************************************************************************
 * This file regroups functions for handling non-http images when running
 * camptocamp on https. The goal is to prevent mixed content warnings.
 ****************************************************************************/

if (!defined('PUN_ROOT'))
    exit('The constant PUN_ROOT must be defined and point to a valid PunBB installation root directory.');

@include PUN_ROOT.'config.php';

if (!defined('PUN'))
    exit('The file \'config.php\' doesn\'t exist or is corrupt. Please run <a href="install.php">install.php</a> to install PunBB first.');

//
// Check if a url is available on https
//
function is_secure_url($url)
{
  return (substr($url, 0, 8) === 'https://' || substr($url, 0, 2) === '//');
}

//
// Check if we are running on https
//
function https_enabled()
{
  return (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
         !empty($_SERVER["HTTPS"]);
}

//
// Check if camo enabled
//
function camo_enabled()
{
  global $use_camo;

  return (bool) $use_camo;
}

//
// Hexencode data
//
function hexencode($data)
{
  $ascii = unpack("C*", $data);
  $retval = '';
  foreach ($ascii as $v) {
    $retval .= sprintf("%02x", $v);
  }
  return $retval;
}

//
// Generate camo url
//
function camo_url($url)
{
  global $camo_url, $camo_key;

  $hash = hash_hmac('sha1', $url, $camo_key);
  return $camo_url . $hash . '/' . hexencode($url);
}

//
// Translate some http urls to https one for some known providers
// that are often used on camptocamp.org
//
function known_https_hosting($url)
{
  $url = preg_replace(array(
    '/^http:\/\/((media|www)\.koreus\.com)/',
    '/^http:\/\/((img[0-9]{0,4}\.)?\.imageshack\.us)/',
    '/^http:\/\/(upload\.wikimedia\.org)/',
    '/^http:\/\/((i\.)?imgur\.com)/',
    '/^http:\/\/(pix\.toile-libre\.org)/',
    '/^http:\/\/(farm[0-9]\.static\.flickr.com)/',
    '/^http:\/\/(lh[0-9]\.ggpht\.com)/',
    '/^http:\/\/(ppcdn\.500px\.org)/',
    '/^http:\/\/(((www|s)\.)?camptocamp\.org)/'
  ), '//$1', $url, 1, $count);

  return (!$count) ? false : $url;
}

//
// Handle image to prevent mixed content warning
//
function handle_mixed_content($img_url)
{
  if (!camo_enabled() || !https_enabled() || is_secure_url($img_url))
  {
    return $img_url;
  }
  else
  {
    $known_url = known_https_hosting($img_url);
    return $known_url ? $known_url : camo_url($img_url);
  }
}
