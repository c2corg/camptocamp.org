<?php
//ex : http://c2c.org/sfMinifyPlugin.php?files=/sf/sf_web_debug/js/main.js,/static/js/fold.js

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false); // FIXME : what's the use ? set to false for prod ?

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// declare minify constants
define('MINIFY_ENCODING', sfConfig::get('app_sf_minify_plugin_encoding', sfConfig::get('sf_charset')));
define('MINIFY_USE_CACHE', sfConfig::get('app_sf_minify_plugin_use_cache', sfConfig::get('sf_cache')));

if(null != $base_dir = sfConfig::get('app_sf_minify_plugin_base_dir'))
{
	define('MINIFY_BASE_DIR', $base_dir);	
}
if(null != $cache_dir = sfConfig::get('app_sf_minify_plugin_cache_dir'))
{
	define('MINIFY_CACHE_DIR', $cache_dir);
}
if(null != $max_files = sfConfig::get('app_sf_minify_plugin_max_files'))
{
	define('MINIFY_MAX_FILES', $max_files);
}
if(null != $rewrite_css_urls = sfConfig::get('app_sf_minify_plugin_rewrite_css_urls'))
{
	define('MINIFY_REWRITE_CSS_URLS', $rewrite_css_urls);
}

require(dirname(__FILE__).'/../plugins/sfMinifyPlugin/minify/minify.php');

// start minify
Minify::handleRequest();
?>
