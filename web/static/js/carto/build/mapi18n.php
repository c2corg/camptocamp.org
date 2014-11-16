<?php
/**
 * Batch that creates the js files with c2c map specific i18n
 *
 * This files are then used by jsbuild to build lang-(fr|it|de|en|eu|es|ca).js files
 */

$culture = $argv[1];

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../../../../'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

sfLoader::loadHelpers('I18N');

$I18N = sfContext::getInstance()->getI18N();

$I18N->setMessageSourceDir(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'i18n', $culture);

?>
OpenLayers.Util.extend(OpenLayers.Lang.<?php echo $culture ?>, {
  //"Map data": "<?php echo __('Map data')?>",
  //"Search": "<?php echo __('Search')?>",
  //"Help": "<?php echo __('Help')?>",
  //"no item selected": "<?php echo __('no item selected on map')?>",
  "Expand map": "<?php echo __('Expand map')?>",
  //"Reduce map": "<?php echo __('Reduce map')?>",
  //"longitude / latitude: ": "<?php echo __('longitude / latitude: ')?>",
  "c2c data": "<?php echo __('c2c map data')?>",
  "summits": "<?php echo __('summits')?>",
  "parkings": "<?php echo __('parkings')?>",
  "huts": "<?php echo __('huts')?>",
  "sites": "<?php echo __('sites')?>",
  "users": "<?php echo __('users')?>",
  "images": "<?php echo __('images')?>",
  "routes": "<?php echo __('routes')?>",
  "outings": "<?php echo __('outings')?>",
  "ranges": "<?php echo __('ranges')?>",
  "maps": "<?php echo __('maps')?>",
  "areas": "<?php echo __('areas')?>",
  "countries": "<?php echo __('countries')?>",
  "admin boundaries": "<?php echo __('admin_limits')?>",
  "pass": "<?php echo __('pass')?>",
  "lake": "<?php echo __('lake')?>",
  "valley": "<?php echo __('valley')?>",
  "public_transportations": "<?php echo __('PT access points')?>",
  "other access": "<?php echo __('other access')?>",
  "camping area": "<?php echo __('camping area')?>",
  "gite": "<?php echo __('gite')?>",
  "products": "<?php echo __('products')?>",
  //"Backgrounds": "<?php echo __('backgrounds')?>",
  "Gmaps physical": "<?php echo __('relief')?>",
  "Gmaps hybrid": "<?php echo __('hybrid')?>",
  //"Normal": "<?php echo __('Google maps')?>",
  "OpenStreetMap": "<?php echo __('OpenStreetMap')?>",
  "IGN maps": "<?php echo __('IGN maps')?>",
  "IGN orthos": "<?php echo __('IGN orthos')?>",
  "Swisstopo maps": "<?php echo __('Swisstopo maps')?>",
  //"Clear": "<?php echo __('Clear')?>",
  //"max extent": "<?php echo __('max extent')?>",
  //"pan": "<?php echo __('pan')?>",
  //"zoom box": "<?php echo __('zoom in')?>",
  //"previous": "<?php echo __('previous map')?>",
  //"next": "<?php echo __('next map')?>",
  //"See next item": "<?php echo __('next page')?>",
  //"See previous item": "<?php echo __('previous page')?>",
  //"length measure": "<?php echo __('length measure')?>",
  //"My position": "<?php echo __('My position')?>",
  //"Measure": "<?php echo __('Distance')?>",
  //"map query": "<?php echo __('map query')?>",
  "Go to...": "<?php echo __('Go to...')?>",
  //"Choose layer...": "<?php echo __('Choose...')?>",
  //"close": "<?php echo __('close')?>",
  //"name": "<?php echo __('name')?>",
  //"elevation": "<?php echo __('elevation')?>",
  //"Recenter": "<?php echo __('recenter')?>",
  //"permalink": "<?php echo __('permalink')?>",
  //"Permalink.openlink": "<?php echo __('Permalink.openlink')?>",
  "Please wait...": "<?php echo __(' loading...')?>",
  //"${nb_items} items. Click to show info": "<?php echo __('${nb_items} items. Click to show info')?>",
  "${item}. Click to show info": "<?php echo __('${item}. Click to show info')?>",
  //"Map URL": "<?php echo __('Map URL')?>",
  "More...": "<?php echo __('More...')?>",
  "Georef Tool": "<?php echo __('Georef Tool')?>",
  "Click on the map to locate item": "<?php echo __('Click on the map to locate item')?>",
  "Reset georef": "<?php echo __('Reset georef')?>",
  "Cancel changes": "<?php echo __('Cancel georef changes')?>"
});
