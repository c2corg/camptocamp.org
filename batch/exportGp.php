<?php
/**
 * This script retrieves summits, huts and climbing sites in France and generates CSV files used to export to other sites.
 */
define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

define('GP_DIR', SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp/');

require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP.DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

$zoom_levels = array(
'10' => 1,
'20' => 2,
'50' => 3,
'100' => 4
);
$default_zoom = 5; 

$csv = '';
$nb_summits = 0;
foreach(Doctrine_Query::create()->select('a.id')->from('Area a')->where('a.area_type = 1')->limit(3)->execute() as $area)
{
    // summits
    $sql = "SELECT s.id, n.name, s.lon, s.lat, s.elevation FROM summits s, summits_i18n n, areas a " .
           "WHERE a.id = " . $area->id . " AND s.id = n.id AND s.redirects_to IS NULL AND summit_type = 1 AND s.geom IS NOT NULL AND n.culture = 'fr' " .
           "AND intersects(a.geom, s.geom) AND a.geom && s.geom " .
           "ORDER BY s.elevation DESC, n.name ASC";
    $summits = sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
    $i = 0;
    foreach ($summits as $summit)
    {
        $nb_summits++;
        $i++;
        $zoom = $default_zoom;
        foreach ($zoom_levels as $max => $level)
        {
            if ($i <= $max) {
                $zoom = $level;
                break;
            }
        }
        $csv .= sprintf('"%s";"%s";"%s";"http://www.camptocamp.org/summits/popup/%d/fr";"%d";"%d";"%d"' . "\n",
                        $summit['name'], $summit['lon'], $summit['lat'], $summit['id'], $summit['elevation'], $zoom, $area->id);
    }
}
file_put_contents(GP_DIR . 'sommets_c2c.csv', $csv);
echo "Summits exported: $nb_summits\n";

echo "Done.\n";
