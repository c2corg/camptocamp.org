<?php

if ($argc < 2)
{
    echo "Usage: php $argv[0] <kml file> [<region id> [<region type> [<culture>]]\n";
    exit;
}

if (!file_exists($argv[1]))
{
    echo "Please provide a valid kml file\n";
    exit;
} 
$filepath = $argv[1];

// id of the region to update, or 0 if it's a new region
if ($argc >= 3)
{
    if (!is_numeric($argv[2]))
    {
        echo "Invalid region id\n";
        exit;
    }
    $region_id =  intval($argv[2]);
}
else
{
    $region_id = 0;
}

// region type
if ($argc >= 4)
{
    if (!is_numeric($argv[3]) || intval($argv[3]) > 3 || intval($argv[2]) < 1)
    {
        echo "Invalid region type\n";
        exit;
    }
    $region_type = $argv[3];
}
else
{
    $region_type = 1;
}

// culture
if ($argc >= 5)
{
    switch ($argv[4])
    {
        case 'fr':
        case 'it':
        case 'en':
        case 'de':
        case 'es':
        case 'eu':
        case 'ca': break;
        default:
            echo "Invalid culture";
            break;
    }
    $culture = $argv[4];
}
else
{
    $culture = 'fr';
}

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

define('GP_DIR', SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp/');

require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP.DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

// Construct geometry from kml
$xml = simplexml_load_file($filepath);
$region = $xml->Document->Placemark;

$name = $region->name;

// detect if polygon or multipolygon
$is_multi = !empty($region->MultiGeometry);
if ($is_multi)
{
    $region = $region->MultiGeometry;
}

$vertexes_list = array();
$is_line = empty($region->Polygon);
$polygons = $is_line ? $region->LineString : $region->Polygon;
for ($i = 0; $i < count($polygons); $i++) // warning: foreach won't work
{
    if (!$is_line)
    {
        $border = $polygons[$i]->outerBoundaryIs->LinearRing->coordinates;
    }
    else
    {
        $border = $polygons[$i]->coordinates;
    }
    $border = preg_split("/[\s]+/", trim($border));

    $vertexes = array();
    foreach ($border as $point)
    {
        $data = explode(',', $point);
        $vertexes[] = $data[0] . ' ' . $data[1];
    }
    if ($is_line)
    {
        $vertexes[] = $vertexes[0]; // closes line to make a polygon
    }

    $vertexes_list[] = $vertexes;
}

foreach ($vertexes_list as &$vertexes)
{
    $vertexes = implode(', ', $vertexes);
}

if (!$is_multi)
{
    $geom = 'POLYGON((' . $vertexes_list[0] . '))';
}
else
{
    $geom = 'MULTIPOLYGON(((' . implode(')),((', $vertexes_list) . ')))';
}

// Update database
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
        $area->setCulture($culture);
        $area->set('name', $name);
        $area->set('area_type', $region_type);
    }
    else
    {
        // FIXME
        // reset geom before updating it (workaround to avoid update crash)
        //$query = "UPDATE app_areas_archives set geom = NULL WHERE id = $region_id and is_latest_version";
        //sfDoctrine::connection()->standaloneQuery($query);

        $area = Document::find('Area', $region_id);
    }

    $area->set('geom_wkt', $geom);
    $area->save();

    $conn->commit();

    if ($is_new_area)
    {
        echo "Added new area $name\n";
    }
    else
    {
        echo "Updated area $region_id ($name)\n";
    }
}
catch (Exception $e)
{
    $conn->rollback();
    throw $e;
}
