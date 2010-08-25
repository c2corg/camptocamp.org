<?php

/**
 * This script is used to import or update regions and maps
 * @todo  auto (re)compute geo-associations + factorize
 * @todo  clear cache
 *
 * KML format: use placemarks with polygon or linestring inside. Use multigeometry if
 * you have multipolygons. Donut geometry : open question...
 * Define the placemark's name (which will be document's name)
 *
 * this script is not bullet proof : don't try to give odd kml files or wrong arguments
 */

///////////////////////////////////////////////////////////////////////////////////
// Arguments parsing
///////////////////////////////////////////////////////////////////////////////////

function usage()
{
    echo "Usage: php $argv[0] area <kml file> [<region id (0)> [<region type (1)> [<culture (fr)>]]]\n" .
         "   or: php $argv[0] map <kml file> [<map id (0)> [<scale (1)> [<editor (1)> [<code (unknown)> [<culture (fr)>]]]]]\n";
    exit;
}

if ($argc < 3) usage();

if ($argv[1] != 'area' && $argv[1] != 'map') usage();
$is_map = ($argv[1] == 'map');

if (!file_exists($argv[2]))
{
    echo "Kml file does not exist\n\n";
    usage();
} 
$filepath = $argv[2];

// id of the map/region to update, or 0 if it's a new document
if ($argc >= 4)
{
    if (!is_numeric($argv[3]))
    {
        echo "Invalid region or map id\n\n";
        usage();
    }
    $document_id = intval($argv[3]);
}
else
{
    $document_id = 0;
}

// region type
if (!$is_map && $argc >= 5)
{
    if (!is_numeric($argv[4]) || intval($argv[4]) > 3 || intval($argv[4]) < 1)
    {
        echo "Invalid region type\n\n";
        usage();
    }
    $region_type = $argv[4];
}
else
{
    $region_type = 1;
}

// region culture
if (!$is_map && $argc >= 6)
{
    switch ($argv[5])
    {
        case 'fr':
        case 'it':
        case 'en':
        case 'de':
        case 'es':
        case 'eu':
        case 'ca': break;
        default:
            echo "Invalid culture\n\n";
            usage();
    }
    $culture = $argv[5];
}
else
{
    $culture = 'fr';
}

// map scale
if ($is_map && $argc >= 5)
{
    if (!is_numeric($argv[4]) || intval($argv[4]) > 3 || intval($argv[4]) < 1)
    {
        echo "Invalid map scale\n\n";
        usage();
    }
    $map_scale =  intval($argv[4]);
}
else
{
    $map_scale = 1;
}

// map editor
if ($is_map && $argc >= 6)
{
    if (!is_numeric($argv[5]))
    {
        echo "Invalid map editor\n\n";
        usage();
    }
    $map_editor =  intval($argv[5]);
}
else
{
    $map_editor = 0;
}

// map code
if ($is_map && $argc >= 7)
{
    $map_code = $argv[6];
}
else
{
    $map_code = 'unknown';
}

// map culture
if ($is_map && $argc >= 8)
{
    switch ($argv[7])
    {
        case 'fr':
        case 'it':
        case 'en':
        case 'de':
        case 'es':
        case 'eu':
        case 'ca': break;
        default:
            echo "Invalid culture\n\n";
            usage();
    }
    $culture = $argv[7];
}
else
{
    $culture = 'fr';
}

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

define('GP_DIR', SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp/');

require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP.DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

///////////////////////////////////////////////////////////////////////////////////
// Construct geometry from kml file
///////////////////////////////////////////////////////////////////////////////////

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

///////////////////////////////////////////////////////////////////////////////////
// Update database
///////////////////////////////////////////////////////////////////////////////////

$conn = sfDoctrine::Connection();

$is_new_document = empty($document_id);

// first, remove geometry in a separate transaction if we are to update it
// no better way found...
if (!$is_new_document)
{
    // first, remove geometry in a separate transaction if we are to update it
    // no better way found...
    try
    {
        $conn->beginTransaction();

        $history_metadata = new HistoryMetadata();
        $history_metadata->setComment('Delete geometry before update');
        $history_metadata->set('is_minor', false);
        $history_metadata->set('user_id', 2); // C2C user
        $history_metadata->save();

        $area = Document::find($is_map ? 'Map' : 'Area', $document_id);
        $area->set('geom_wkt', null);
        $area->save();

        $conn->commit();
    }
    catch (Exception $e)
    {
        $conn->rollback();
        throw $e;
    }

    // delete all geo associations TODO
    // find all docs associated to current area or map
    $geoassociations = GeoAssociation::findAllAssociations($document_id, null, 'linked');

    $conn = sfDoctrine::Connection();
    try
    {
        $conn->beginTransaction();
        foreach ($geoassociations as $geoassociation)
        {
            $geoassociation->delete();
        }
        $conn->commit();
    }
    catch (exception $e)
    {
        $conn->rollback(); // TODO
    }
}


// import new geometry into database
try
{
    $conn->beginTransaction();

    $history_metadata = new HistoryMetadata();
    $history_metadata->setComment($is_new_document ? 'Imported new ' . ($is_map ? 'map' : 'area') : 'Updated geometry');
    $history_metadata->set('is_minor', false);
    $history_metadata->set('user_id', 2); // C2C user
    $history_metadata->save();

    if ($is_new_document)
    {
        $doc = $is_map ? new Map() : new Area();
        $doc->setCulture($culture);
        $doc->set('name', $name);
        if ($is_map)
        {
            $doc->set('editor', $map_editor);
            $doc->set('scale', $map_scale);
            $doc->set('code', $map_code);
        }
        else
        {
            $doc->set('area_type', $region_type);
        }
    }
    else
    {
        $doc = Document::find($is_map ? 'Map' : 'Area', $document_id);
        $name = $doc->get('name');
    }

    $doc->set('geom_wkt', $geom);
    $doc->save();

    if ($is_new_document)
    {
        $document_id = $doc->get('id');
    }

    $conn->commit();

    $conn->beginTransaction(); // FIXME why do we have o change transaction..

    if ($is_map)
    {
        $a_type = 'dm';
    }
    else
    {
        switch ($region_type)
        {
            case 1: // range
                $a_type = 'dr';
                break;
            case 2: // country
                $a_type = 'dc';
                break;
            case 3: // dept
                $a_type = 'dd';
                break;
        }
    }

    $query = 'SELECT id, module FROM documents WHERE intersects(buffer(documents.geom, 200), (SELECT Force_2d(geom) FROM '
           . ($is_map ? 'maps' : 'areas') . ' WHERE ' . ($is_map ? 'maps' : 'areas') . '.id = ?))'
           . ($is_map ? "AND module NOT IN('outings', 'maps', 'users')" : "AND module NOT IN('areas')");
    // rq: no maps are linked to outings, users and other maps ; areas are not linked together

    $results = sfDoctrine::connection()
                        ->standaloneQuery($query, array($document_id))
                        ->fetchAll();

    foreach ($results as $d)
    {
        $a = new GeoAssociation();
        // check but area - maps links are of dr, dc, dd etc... and not dm (teh other way...) // TODO TODO TODO TODO
        $a->doSaveWithValues($d['id'], $document_id, $a_type);

        // inherited docs
        // FIXME factorize with refreshGeoassociations functions (but protected in sfActions...)
        switch ($d['module'])
        {
            case 'sites':
            case 'routes':
                $associated_outings = Association::findAllAssociatedDocs($d['id'], array('id', 'geom_wkt'), ($d['module'] == 'routes' ? 'ro' : 'to'));
                if (count($associated_outings))
                {
                    $geoassociations = GeoAssociation::findAllAssociations($d['id'], null, 'main');
                    // we create new associations :
                    //  (and delete old associations before creating the new ones)
                    //  (and do not create outings-maps associations)
                    foreach ($associated_outings as $outing)
                    {
                        $i = $outing['id'];
                        if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                        {
                            // replicate geoassoces from doc $id to outing $i and delete previous ones
                            // (because there might be geoassociations created by this same process)
                            $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, false);
                        }
                    }
                }
                break;
            case 'summits':
                // TODO summits raid
                $associated_routes = Association::findAllAssociatedDocs($d['id'], array('id', 'geom_wkt'), 'sr');
                if (count($associated_routes))
                {
                    $geoassociations = GeoAssociation::findAllAssociations($d['id'], null, 'main');
                    // we create new associations :
                    //  (and delete old associations before creating the new ones)
                    //  (and do not create outings-maps associations)
                    foreach ($associated_routes as $route)
                    {
                        $i = $route['id'];
                        if (!$route['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                        {
                            // replicate geoassoces from doc $id to outing $i and delete previous ones
                            // (because there might be geoassociations created by this same process)
                            $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, true);
                            c2cTools::log("created $nb_created geo associations for route N° $i");
                            $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
                            if (count($associated_outings))
                            {
                                $geoassociations2 = GeoAssociation::findAllAssociations($i, null, 'main');
                                // we create new associations :
                                //  (and delete old associations before creating the new ones)
                                //  (and do not create outings-maps associations)
                                foreach ($associated_outings as $outing)
                                {
                                    $j = $outing['id'];
        
                                    if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                                    {
                                        // replicate geoassoces from doc $id to outing $i and delete previous ones
                                        // (because there might be geoassociations created by this same process)
                                        $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations2, $j, true, false);
                                    }
                                }
                            }
                        }
                    }
            }
        }
    }
   
    $conn->commit();

    if ($is_new_document)
    {
        echo "Added new map/area $name\n";
    }
    else
    {
        echo "Updated map/area $document_id ($name)\n";
    }
}
catch (Exception $e)
{
    $conn->rollback();
    throw $e;
}
