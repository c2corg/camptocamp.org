<?php
/**
 * Config:
 */

//$filepath = '../tmp/kml/puna.kml';
$filepath = '../73.kml';
$region_id = 0; // new region if 0
$region_type = 1; // range
$culture = 'fr';

/**
 */
define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

define('GP_DIR', SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp/');

require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP.DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

$xml = simplexml_load_file($filepath);
$region = $xml->Document->Placemark;

$name = $region->name;

$is_line = empty($region->Polygon);
if (!$is_line)
{
    $border = $region->Polygon->outerBoundaryIs->LinearRing->coordinates;
}
else
{
    $border = $region->LineString->coordinates;
}
$border = explode(' ', trim($border));

$vertexes = array();
foreach ($border as $point) {
    $data = explode(',', $point);
    $vertexes[] = $data[0] . ' ' . $data[1];
}
if ($is_line)
{
    $vertexes[] = $vertexes[0]; // closes line to make a polygon
}

$geom = 'POLYGON((' . implode(', ', $vertexes) . '))';

$conn = sfDoctrine::Connection();

try
{
    $is_new_area = empty($region_id);
    $conn->beginTransaction();

    $history_metadata = new HistoryMetadata();
    $history_metadata->setComment($is_new_area ? 'Imported new area' : 'Updated geometry');
    $history_metadata->set('is_minor', false);
    $history_metadata->set('user_id', 2); // C2C user
    $history_metadata->save();
    
    if ($is_new_area)
    {
        // creation of a region
        $area = new Area();
    }
    else
    {
        $area = Document::find('Area', $region_id);
    }

    $area->setCulture($culture);
    $area->set('name', $name);
    $area->set('area_type', $region_type);
    $area->set('geom_wkt', $geom);
    $area->save(); // FIXME: crash in update mode

    $conn->commit();

    if ($is_new_area)
    {
        echo "Added new area $name\n";
    }
    else
    {
        echo "Updated area $name\n";
    }
}
catch (Exception $e)
{
    $conn->rollback();
    throw $e;
}
