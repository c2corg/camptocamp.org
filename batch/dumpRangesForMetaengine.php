<?php
/**
 * Batch that dumps list of ranges to be used in metaengine.
 *
 * @version $Id: dumpRangesForMetaengine.php 2231 2007-10-31 14:22:09Z alex $
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

$sql = 'SELECT n.name, ST_AsText(ST_Transform(a.geom, 4326)) as geom_wkt, a.id FROM areas a, areas_i18n n ' .
       'WHERE a.id = n.id AND a.area_type = ? AND n.culture = ? ORDER BY n.name ASC';
$res = sfDoctrine::connection()->standaloneQuery($sql, array(1, 'fr'))->fetchAll();

$output = '';

foreach ($res as $r)
{
    $output .= sprintf("INSERT INTO regions (name, external_region_id, system_id, geom) VALUES ('%s', %d, %d, GeomFromEWKT('SRID=4326;%s'));\n\n",
                       addslashes($r['name']),
                       $r['id'],
                       sfConfig::get('app_meta_engine_c2c_id'),
                       $r['geom_wkt']);
}
$output .= "VACUUM ANALYSE;";

file_put_contents("metaengine_ranges.sql", $output);
