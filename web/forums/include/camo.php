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
  return true;
  return (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
         !empty($_SERVER["HTTPS"]);
}

//
// Generate camo url
//
function camo_url($url)
{
  global $camo_url, $camo_key;

  $hash = hash_hmac('sha1', $url, $camo_key);
  return $hash . '?url=' . urlencode($url);
}

//
// Handle image to prevent mixed content warning
//
function handle_mixed_content($img_url)
{
  if (!https_enabled() || is_secure_url($img_url))
  {
    return $img_url;
  }
  else
  {
    // TODO check for common image services that can be translated to https
    return camo_url($img_url);
  }
}
