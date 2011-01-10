<?php

/**
 * This script is used to import or update regions and maps
 * @todo clear cache?
 * @todo try to reduce number of separate transactions (but seems like some things do not work anymore if...)
 *
 * KML format: use placemarks with polygon or linestring inside. Use multigeometry if
 * you have multipolygons.
 * Define the placemark's name (which will be document's name)
 *
 * this script is not bullet proof : don't try to give odd kml files or wrong arguments
 */

///////////////////////////////////////////////////////////////////////////////////
// Arguments parsing
///////////////////////////////////////////////////////////////////////////////////

function usage()
{
    echo "Usage: php " . basename(__FILE__) . " area <kml file> [<region id (0)> [<region type (1)> [<culture (fr)> [<comment>]]]]\n" .
         "   or: php " . basename(__FILE__) . " map <kml file> [<map id (0)> [<scale (1)> [<editor (1)> [<code (unknown)> [<culture (fr)> [<comment>]]]]]]\n";
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

// region comment
if (!$is_map && $argc >= 7)
{
    $comment = $argv[6];
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

// map comment
if ($is_map && $argc >= 9)
{
    $comment = $argv[8];
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

function get_coordinates($border, $is_line = false)
{
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

    return implode(', ', $vertexes);
}

$xml = simplexml_load_file($filepath);
$region = $xml->Document->Placemark;

$name = $region->name;

// detect if polygon or multipolygon
$is_multi = !empty($region->MultiGeometry);
if ($is_multi)
{
    $region = $region->MultiGeometry;
}

$polygons_list = array();
$is_line = empty($region->Polygon);
$polygons = $is_line ? $region->LineString : $region->Polygon;
for ($i = 0; $i < count($polygons); $i++) // warning: foreach won't work
{
    // retrieve polygon outer boundary points
    $outerboundary = '(' . get_coordinates($is_line ? $polygons[$i]->coordinates : 
                                                      $polygons[$i]->outerBoundaryIs->LinearRing->coordinates,
                                           $is_line) . ')';

    // Check if they are som inner rings
    if (!$is_line)
    {
        $innerboundaries = array();
        $innerrings = $polygons[$i]->innerBoundaryIs;
        for ($j=0; $j < count($innerrings); $j++)
        {
            $innerboundaries[] = '(' . get_coordinates($innerrings[$j]->LinearRing->coordinates) . ')';
        }
    }

    $polygons_list[] = $outerboundary . (isset($innerboundaries) && count($innerboundaries) ? ',' . implode(',', $innerboundaries) : '');
}

if (!$is_multi)
{
    $geom = 'POLYGON(' . $polygons_list[0] . ')';
}
else
{
    $geom = 'MULTIPOLYGON((' . implode('),(', $polygons_list) . '))';
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
    echo "Old geometry deleted\n";

    // delete all geo associations
    // find all docs associated to current area or map
    $geoassociations = GeoAssociation::findAllAssociations($document_id, null, 'both');

    $conn = sfDoctrine::Connection();
    echo "Deleting old geoassociations...\n";
    try
    {
        $conn->beginTransaction();
        $tot = count($geoassociations);
        foreach ($geoassociations as $i => $geoassociation)
        {
            echo "\r   " . ($i+1) . " out of $tot";
            $geoassociation->delete();
        }
        $conn->commit();
    }
    catch (exception $e)
    {
        $conn->rollback();
        throw $e;
    }
    echo "\n";
}

// import new geometry into database
try
{
    $conn->beginTransaction();

    $history_metadata = new HistoryMetadata();
    $history_metadata->setComment(isset($comment) ? $comment : ($is_new_document ? 'Imported new ' . ($is_map ? 'map' : 'area') : 'Updated geometry'));
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

    echo "Geometry uploaded.\n";

    if ($is_new_document)
    {
        $document_id = $doc->get('id');
    }

    // $conn->commit();
    // $conn->beginTransaction();

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

    // Some explanation for the following queries:
    // - && operator is used to limit docs to the one whose bouding boxes overlap (it uses spatial index)
    // - ST_Within and ST_Intersects are used to work on the actual geometries
    // - ST_Within apears to be faster, so we use it for 'points' (like summits, huts etc), but we have
    //   to use ST_Intersects for 'geometries' (like outings, maps etc)
    // - we only use a buffer of 200m for areas (because of boundaries imprecision), but not for maps
    // - areas are not linked together
    // - maps are not linked to outings, users, and other maps

    // todo in some cases, the bounding box of the area could be very big (US?) and the query could be far too slow
    //      especially if it is a multigeometry...

    $geom = $is_map ? '(SELECT geom FROM maps WHERE id=?)' : '(SELECT buffer(geom, 200) FROM areas WHERE id=?)';

    $query1 = 'SELECT id, module FROM documents WHERE geom && ' . $geom
            . 'AND ST_Within(geom, ' . $geom . ')'
            . "AND MODULE IN('summits', 'huts', 'sites', 'parkings', 'products', 'portals', 'images'"
            . ($is_map ? '' : ", 'users'") . ')';

    $query2 = 'SELECT id, module FROM documents WHERE geom && ' . $geom 
            . 'AND ST_Intersects(geom, ' . $geom . ') '
            . 'AND MODULE IN'
            . ($is_map ? "('routes', 'areas')"
                       : "('outings', 'routes', 'maps')");
    
    $results1 = sfDoctrine::connection()
                        ->standaloneQuery($query1, array($document_id, $document_id))
                        ->fetchAll();

    $results2 = sfDoctrine::connection()
                        ->standaloneQuery($query2, array($document_id, $document_id))
                        ->fetchAll();

    $results = array();
    foreach ($results1 as $d)
    {
        $results[] = $d;
    }
    foreach ($results2 as $d)
    {
        $results[] = $d;
    }

    echo "Create new associations (+ inherited docs)...\n";
    $tot = count($results);
    foreach ($results as $i => $d)
    {
        echo "\r   " . ($i+1) . " out of $tot";
        $a = new GeoAssociation();

        // for map - area geoassociations, links must not be dm but dr, dc, dd...
        if ($is_map && $d['module'] == 'areas')
        {
            $area = Document::find('Area', $d['id']);
            switch ($area->get('area_type'))
            {
                case 1: // range
                    $t_a_type = 'dr';
                    break;
                case 2: // country
                    $t_a_type = 'dc';
                    break;
                case 3: // dept
                    $t_a_type = 'dd';
                    break;
            }
            $a->doSaveWithValues($document_id, $d['id'], $t_a_type);
        }
        else // default case
        {
            $a->doSaveWithValues($d['id'], $document_id, $a_type);
        }

        // inherited docs: we add geoassociations for the 'inherited docs' from sites, routes and summits
        // but not if they already have a geometry (gps track)
        switch ($d['module'])
        {
            case 'sites':
            case 'routes':
                if ($is_map) break; // we do not link maps to outings
                $associated_outings = Association::findAllAssociatedDocs($d['id'], array('id', 'geom_wkt'), ($d['module'] == 'routes' ? 'ro' : 'to'));
                if (count($associated_outings))
                {
                    foreach ($associated_outings as $outing)
                    {
                        if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                        {
                             // we create geoassociation (if it already existed, it has been deleted before in the script)
                             $a = new GeoAssociation();
                             $a->doSaveWithValues($outing['id'], $document_id, $a_type);
                        }
                    }
                }
                break;
            case 'summits':
                // if summit is of type raid, we should not try to update its routes and outings summit_type=5
                $summit = Document::find('Summit', $d['id']);
                if ($summit->get('summit_type') == 5) break;

                $associated_routes = Association::findAllAssociatedDocs($d['id'], array('id', 'geom_wkt'), 'sr');
                if (count($associated_routes))
                {
                    foreach ($associated_routes as $route)
                    {
                        $i = $route['id'];
                        if (!$route['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                        {
                            $a = new GeoAssociation();
                            $a->doSaveWithValues($i, $document_id, $a_type);

                            if (!$is_map) // We do not link maps to outings
                            {
                                $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
                                if (count($associated_outings))
                                {
                                    foreach ($associated_outings as $outing)
                                    {
                                        $j = $outing['id'];
                                        if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                                        {
                                            $a = new GeoAssociation();
                                            $a->doSaveWithValues($j, $document_id, $a_type);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }
    }
    echo "\n";
   
    $conn->commit();

    if ($is_new_document)
    {
        echo 'Added new ' . ($is_map ? 'map' : 'area') . " $name\n";
    }
    else
    {
        echo 'Updated ' . ($is_map ? 'map' : 'area') . " $document_id ($name)\n";
    }
}
catch (Exception $e)
{
    $conn->rollback();
    throw $e;
}
