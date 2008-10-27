<?php
/**
 * This script retrieves summits, huts and climbing sites in France and generates CSV files used to communicate with IGN's Geoportail.
 */
define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

define('GP_DIR', SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp/');

require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP.DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

$region_id = 1234;
$buffer = 10;

// summits
$summits = Summit::listFromRegion($region_id, $buffer);
$csv = '';
foreach ($summits as $summit)
{
    $csv .= sprintf('"%s";"%s";"%s";"http://www.camptocamp.org/summits/geoportail/%d/fr";"%d"' . "\n",
                    $summit['name'], $summit['lon'], $summit['lat'], $summit['id'], $summit['elevation']);
}
file_put_contents(GP_DIR . 'sommets_c2c.csv', $csv);
$nb_summits = count($summits);
echo "Summits exported: $nb_summits\n";

// huts
$huts = Hut::listFromRegion($region_id, $buffer);
$csv = '';
foreach ($huts as $hut)
{
    $csv .= sprintf('"%s";"%s";"%s";"http://www.camptocamp.org/huts/geoportail/%d/fr";"%d"' . "\n",
                    $hut['name'], $hut['lon'], $hut['lat'], $hut['id'], $hut['elevation']);
}
file_put_contents(GP_DIR . 'refuges_c2c.csv', $csv);
$nb_huts = count($huts);
echo "Huts exported: $nb_huts\n";

// climbing sites
$sites = Site::listFromRegion($region_id, $buffer);
$csv = '';
foreach ($sites as $site)
{
    $csv .= sprintf('"%s";"%s";"%s";"http://www.camptocamp.org/sites/geoportail/%d/fr";"%d"' . "\n",
                    $site['name'], $site['lon'], $site['lat'], $site['id'], $site['elevation']);
}
file_put_contents(GP_DIR . 'sites_c2c.csv', $csv);
$nb_sites = count($sites);
echo "Sites exported: $nb_sites\n";

echo "Done.\n";
