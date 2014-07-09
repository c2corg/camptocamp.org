<?php

/**
 * This script is used to import or update regions and maps
 * @todo clear cache?
 *
 * KML format: use placemarks with polygon or linestring inside. Use multigeometry if
 * you have multipolygons.
 * Define the placemark's name (which will be document's name)
 *
 * this script is not bullet proof : don't try to give odd kml files or wrong arguments
 */

// TODO we should not delete routes and outings if they intersect the olddiff unless they do not intersect the new geom
// check for example a route that is attached to more than one summit not for this script)
// TODO check if france fullwipe is ok?

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       true);

define('GP_DIR', SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp/');

require_once(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP.DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

// needed for doctrine connection to work
$context = sfContext::getInstance();

$conn = sfDoctrine::Connection();

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

function info($msg)
{
    echo $msg;
    ob_flush();
    flush();
}

function usage()
{
    echo "Usage: php " . basename(__FILE__) . " validate <kml file>\n" .
         "   or: php " . basename(__FILE__) . " create area <kml file> [<region type (1)> [<culture (fr)> [<comment>]]]\n" .
         "   or: php " . basename(__FILE__) . " create map <kml file> [<scale (1)> [<editor (1)> [<code (unknown)> [<culture (fr)> [<comment>]]]]\n" .
         "   or: php " . basename(__FILE__) . " update area <kml file> <area id> [fullwipe|keepassociations]\n" .
         "   or: php " . basename(__FILE__) . " update map <kml file> <map id> [fullwipe|keepassociations]\n";
    exit;
}

if ($argc < 3) usage();

switch ($argv[1])
{
    case 'validate':
        validate_kml_geometry();
        break;
    case 'create':
        create_new_document();
        break;
    case 'update':
        update_document();
        break;
}
exit;

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

function validate_kml_geometry()
{
    global $argv, $argc;

    if ($argc != 3 || !file_exists($argv[2]))
    {
        info("Kml file does not exist\n\n");
        usage();
    }

    $newgeomtext = text_geometry_from_file($argv[2]);

    if (validate_geometry($newgeomtext))
    {
        info("Geometry is valid\n");
    }
    else
    {
        info("Geometry is invalid\n");
    }
}

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

function create_new_document()
{
    global $argv, $argc, $conn, $comment, $is_map, $is_new_document, $culture, $name, $map_editor,
           $map_scale, $map_code, $region_type, $newgeomtext, $no_oldgeom, $prgmsg;

    if ($argc < 4) usage();

    if ($argv[2] != 'area' && $argv[2] != 'map') usage();
    $is_map = ($argv[2] === 'map');

    if (!file_exists($argv[3]))
    {
        info("Kml file does not exist\n\n");
        usage();
    }

    // region type
    if (!$is_map && $argc >= 5)
    {
        if (!is_numeric($argv[4]) || intval($argv[4]) > 3 || intval($argv[4]) < 1)
        {
            info("Invalid region type\n\n");
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
                info("Invalid culture\n\n");
                usage();
        }
        $culture = $argv[5];
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
            info("Invalid map scale\n\n");
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
            info("Invalid map editor\n\n");
            usage();
        }
        $map_editor = intval($argv[5]);
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
    if ($is_map)
    {
        if ($argc >= 8)
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
                    info("Invalid culture\n\n");
                    usage();
            }
            $culture = $argv[7];
        }
        else
        {
            $culture = 'fr';
        }
    }

    // map comment
    if ($is_map && $argc >= 9)
    {
        $comment = $argv[8];
    }

    $no_oldgeom = true;
    $is_new_document = true;

    info("Create geometry from kml file...\n");

    $newgeomtext = text_geometry_from_file($argv[3]);

    $newgeom = geometry_from_text($newgeomtext);

    info("Validating geometry...\n");

    // check that the new geometry is valid
    if (!validate_geometry($newgeom))
    {
        die ("The new geometry is invalid. Aborting...\n");
    }

    import_new_geometry();
}

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

function update_document()
{
    global $argv, $argc, $conn, $comment, $is_map, $is_new_document, $culture, $name, $map_editor, $newgeom,
           $map_scale, $map_code, $region_type, $newgeomtext, $no_oldgeom, $oldgeom, $document_id, $prgmsg,
           $fullwipe, $keepassociations;

    if ($argc < 5) usage();

    if ($argv[2] != 'area' && $argv[2] != 'map') usage();
    $is_map = ($argv[2] === 'map');

    if (!file_exists($argv[3]))
    {
        info("Kml file does not exist\n\n");
        usage();
    }

    if (!is_numeric($argv[4]))
    {
        info("Invalid region or map id\n\n");
        usage();
    }

    $document_id = intval($argv[4]);

    $fullwipe = ($argc === 6 && $argv[5] === 'fullwipe');
    $keepassociations = ($argc === 6 && $argv[5] === 'keepassociations');

    $is_new_document = false;

    info("Create geometry from kml file...\n");

    $newgeomtext = text_geometry_from_file($argv[3]);

    $newgeom = geometry_from_text($newgeomtext);

    info("Validating geometry...\n");

    // check that the new geometry is valid
    if (!validate_geometry($newgeom))
    {
        die ("The new geometry is invalid. Aborting...\n");
    }

    // we need to first delete old geometry and old geaoassociations
    $oldgeom = $conn->standaloneQuery('SELECT geom FROM ' . ($is_map ? 'maps' : 'areas') . ' WHERE id=?', array($document_id))
                    ->fetchAll();

    // check that document exists
    if (!count($oldgeom))
    {
        die("Specified {$argv[2]} ($document_id) does not exist\n");
    }

    // Output warning if document has no geometry
    $oldgeom = $oldgeom[0]['geom'];
    if (is_null($oldgeom))
    {
        $no_oldgeom = true;
        info("Warning: specified {$argv[2]} ($document_id) has no geometry...\n");
    }
    else
    {
        $no_oldgeom = false;
    }

    // first, remove geometry in a separate transaction if we are to update it
    // no better way found...
    if (!$no_oldgeom)
    {
        info("Deleting old geometry...\n");
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
        info("Old geometry deleted\n");
    }

    // then delete geoassociations
    // We only delete geoassociations where it is needed
    // ie the parts of the old geometry that does not intersect with the new one
    // If document has no previous geometry, we make sure to delete geoassociations
    // rq: we only use buffer for areas, not for maps
    if ($keepassociations)
    {
        // do nothing
    }
    else if ($no_oldgeom || $fullwipe)
    {
        $geoassociations = GeoAssociation::findAllAssociations($document_id, null, 'both');

        $prgmsg = "Delete all old geoassociations...";
        info($prgmsg);
        try
        {
            $conn->beginTransaction();
            $tot = count($geoassociations);
            foreach ($geoassociations as $i => $geoassociation)
            {
                progression($i, $tot);
                $geoassociation->delete();
            }
            $conn->commit();
            if (isset($deleted)) associations_result($deleted, false);
        }
        catch (exception $e)
        {
            $conn->rollback();
            throw $e;
        }
        info("\n");
    }
    else
    {
        $deletegeom = $conn->standaloneQuery("SELECT ST_Difference('$oldgeom', '$newgeom')")
                           ->fetchAll();
        $deletegeomb = $conn->standaloneQuery("SELECT ST_Difference(buffer('$oldgeom', 200), buffer('$newgeom', 200))")
                            ->fetchAll();
        $deletegeom = $deletegeom[0]['st_difference'];
        $deletegeomb = $deletegeomb[0]['st_difference'];

        // for maps, we don't use buffer at all
        if ($is_map)
        {
            $deletegeomb = $deletegeom;
        }

        $queries = array();
        // point geometry
        $queries[] = array("SELECT id, module FROM documents WHERE geom && '$deletegeomb' "
                             . "AND ST_Within(geom, '$deletegeomb') "
                             . "AND module IN('summits', 'huts', 'sites', 'parkings', 'products', 'portals', 'images'"
                             . ($is_map ? '' : ", 'users'") . ')',
                           array());

        $queries[] = array("SELECT id, module FROM documents WHERE geom && '$deletegeomb' "
                             . "AND ST_Intersects(geom, '$deletegeomb') AND module"
                             . ($is_map ? "='routes'" : " IN('routes', 'outings')"), // TODO if not new or fullwipe, we also need to check it doesnot intersect with the whole new geom with buffer
                           array());

        // for maps areas associations, we always compute 'full wipe', without buffer
        $queries[] = array("SELECT id, module FROM documents WHERE geom && '$oldgeom' "
                             . "AND ST_Intersects(geom, '$oldgeom') AND module='"
                             . ($is_map ? 'areas' : 'maps') . "'",
                          array($document_id, $document_id));

        $results_a = array();
        foreach ($queries as $query)
        {
            $results_a[] = sfDoctrine::connection()
                                   ->standaloneQuery($query[0], $query[1])
                                   ->fetchAll();
        }

        $results = array();
        foreach ($results_a as $results_set)
        {
            foreach ($results_set as $d)
            {
                $results[] = $d;
            }
        }

        $tot = count($results);
        $prgmsg = "Delete obsolete geoassociations...";
        info($prgmsg);
        try
        {
            $conn->beginTransaction();

            foreach ($results as $i => $d)
            {
                progression($i, $tot);
                $geoassociation = GeoAssociation::find($document_id, $d['id'], null, false);
                if ($geoassociation !== false)
                {
                    $geoassociation->delete();
                    $deleted[$d['module']] = isset($deleted[$d['module']]) ? $deleted[$d['module']] + 1 : 1;
                }

                // for routes and outings, we need to check that they are not intersecting the new geom
                // because they shouldn't be unlinked in that case
                // (it's quite unconvenient, but no best way found)
                if (in_array($d['module'], array('outings', 'routes')))
                {
                   $query = "SELECT ST_Intersects(geom, ST_Buffer('$newgeom', 200)) FROM "
                          . $d['module'] . " WHERE id=?";
                   $result = sfDoctrine::connection()
                                     ->standaloneQuery($query, array($d['id']))
                                     ->fetchAll();
                   $result = $result[0]['st_intersects'];
                   if ($result)
                   {
                       continue;
                   }
                }

                // inherited docs: we delete geoassociations for the 'inherited docs' from sites, routes and summits
                // but not if they already have a geometry (gps track)
                switch ($d['module'])
                {
                    case 'sites':
                    case 'routes':
                        if ($is_map) break; // maps are not linked to outings
                        $associated_outings = Association::findAllAssociatedDocs($d['id'], array('id', 'geom_wkt'),
                                                                                 ($d['module'] === 'routes' ? 'ro' : 'to'));
                        if (count($associated_outings))
                        {
                            foreach ($associated_outings as $outing)
                            {
                                if (!$outing['geom_wkt'])
                                {
                                    $geoassociation = GeoAssociation::find($document_id, $outing['id'], null, false);
                                    if ($geoassociation !== false)
                                    {
                                        $geoassociation->delete();
                                        $deleted['outings'] = isset($deleted['outings']) ? $deleted['outings'] + 1 : 1;
                                    }
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
                                if (!$route['geom_wkt'])
                                {
                                    $geoassociation = GeoAssociation::find($i, $document_id, null, false);
                                    if ($geoassociation !== false)
                                    {
                                        $geoassociation->delete();
                                        $deleted['routes'] = isset($deleted['routes']) ? $deleted['routes'] + 1 : 1;
                                    }

                                    if (!$is_map) // maps are not linked to outings
                                    {
                                        $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
                                        if (count($associated_outings))
                                        {
                                            foreach ($associated_outings as $outing)
                                            {
                                                $j = $outing['id'];
                                                if (!$outing['geom_wkt'])
                                                {
                                                    $geoassociation = GeoAssociation::find($j, $document_id, null, false);
                                                    if ($geoassociation !== false)
                                                    {
                                                        $geoassociation->delete();
                                                        $deleted['outings'] = isset($deleted['outings']) ? $deleted['outings'] + 1 : 1;
                                                    }
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
            info("\n");
            $conn->commit();
            if (isset($deleted)) associations_result($deleted, false);
        }
        catch (exception $e)
        {
            $conn->rollback();
            throw $e;
        }
    }

    // import new geometry into database and create geoassociations
    import_new_geometry();
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function import_new_geometry()
{
    global $conn, $comment, $is_map, $is_new_document, $culture, $name, $map_editor, $map_scale, $map_code, $keepassociations,
           $region_type, $newgeomtext, $no_oldgeom, $document_id, $a_type, $fullwipe, $oldgeom, $newgeom, $prgmsg;

    info("Importing the new geometry...\n");

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
            if (!$is_map)
            {
                $region_type = $doc->get('area_type');
            }
        }

        $doc->set('geom_wkt', $newgeomtext);
        $doc->save();

        info("Geometry uploaded.\n");

        if ($keepassociations)
        {
            exit;
        }

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

        // if it is a new document, we create geoassociations for the whole geometry
        // but if its an updated one, we only create geoassociations for the part of the
        // new geometry that does not intersect with the old one
        if ($is_new_document || $no_oldgeom || $fullwipe)
        {
            // retrieve geom from the database
            $geomquery = '(SELECT geom FROM '.($is_map ? 'maps' : 'areas').' WHERE id=?)';
            $geomqueryb = '(SELECT buffer(geom, 200) FROM '.($is_map ? 'maps' : 'areas').' WHERE id=?)';
            $queryparam = array($document_id, $document_id);
        }
        else
        {
            $queryparam = array();
            $creategeom = $conn->standaloneQuery("SELECT ST_Difference('$newgeom', '$oldgeom')")
                               ->fetchAll();
            $creategeomb = $conn->standaloneQuery("SELECT ST_Difference(buffer('$newgeom', 200), buffer('$oldgeom', 200))")
                                ->fetchAll();
            $creategeom = $creategeom[0]['st_difference'];
            $creategeomb = $creategeomb[0]['st_difference'];
            $geomquery = "'$creategeom'";
            $geomqueryb = "'$creategeomb'";
        }

        // for maps, we don't use buffer at all
        if ($is_map)
        {
            $geomqueryb = $geomquery;
        }

        $queries = array();

        // point geometry
        $queries[] = array("SELECT id, module FROM documents WHERE geom && $geomqueryb "
                             . "AND ST_Within(geom, $geomqueryb) "
                             . "AND module IN('summits', 'huts', 'sites', 'parkings', 'products', 'portals', 'images'"
                             . ($is_map ? '' : ", 'users'") . ')',
                           $queryparam);

        // multipoint geometry
        $queries[] = array("SELECT id, module FROM documents WHERE geom && $geomqueryb "
                             . "AND ST_Intersects(geom, $geomqueryb) AND module"
                             . ($is_map ? "='routes'" : " IN('routes', 'outings')"),
                           $queryparam);

        // for maps areas associations, we always compute 'full wipe', without buffer
        $geomquery = '(SELECT geom FROM '.($is_map ? 'maps' : 'areas').' WHERE id=?)';
        $queries[] = array("SELECT id, module FROM documents WHERE geom && $geomquery "
                             . "AND ST_Intersects(geom, $geomquery) AND module='"
                             . ($is_map ? 'areas' : 'maps') ."'",
                           array($document_id, $document_id));

        $results_a = array();
        foreach ($queries as $query)
        {
            $results_a[] = $conn->standaloneQuery($query[0], $query[1])
                                 ->fetchAll();
        }

        $results = array();
        foreach ($results_a as $results_set)
        {
            foreach ($results_set as $d)
            {
                $results[] = $d;
            }
        }

        $prgmsg = "Create new associations...";
        info($prgmsg);
        $tot = count($results);
        foreach ($results as $i => $d)
        {
            progression($i, $tot);

            // Apparently in some cases, we ar trying to create associations that
            // already exist, so we check first
            if (!GeoAssociation::find($document_id, $d['id'], null, false))
            {
                $a = new GeoAssociation();

                $created[$d['module']] = isset($created[$d['module']]) ? $created[$d['module']] + 1 : 1;

                // for map - area geoassociations, links must not be dm but dr, dc, dd...
                if ($is_map && $d['module'] === 'areas')
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
            }

            // inherited docs: we add geoassociations for the 'inherited docs' from sites, routes and summits
            // but not if they already have a geometry (gps track)
            switch ($d['module'])
            {
                case 'sites':
                case 'routes':
                    if ($is_map) break; // we do not link maps to outings
                    $associated_outings = Association::findAllAssociatedDocs($d['id'], array('id', 'geom_wkt'), ($d['module'] === 'routes' ? 'ro' : 'to'));
                    if (count($associated_outings))
                    {
                        foreach ($associated_outings as $outing)
                        {
                            if (!$outing['geom_wkt'] && // proof that there is no pre-existing geoassociation due to a GPX upload
                                GeoAssociation::find($outing['id'], $document_id, $a_type) === false) // we need to check because we can have
                                                                                                      // one-to-many associations
                                                                                                      // e.g. 1 route, 2 summits
                            {
                                 // we create geoassociation (if it already existed, it has been deleted before in the script)
                                 $a = new GeoAssociation();
                                 $a->doSaveWithValues($outing['id'], $document_id, $a_type);

                                 $created['outings'] = isset($created['outings']) ? $created['outings'] + 1 : 1;
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
                            if (!$route['geom_wkt'] &&
                                GeoAssociation::find($i, $document_id, $a_type) === false)
                            {
                                $a = new GeoAssociation();
                                $a->doSaveWithValues($i, $document_id, $a_type);

                                $created['routes'] = isset($created['routes']) ? $created['routes'] + 1 : 1;

                                if (!$is_map) // We do not link maps to outings
                                {
                                    $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
                                    if (count($associated_outings))
                                    {
                                        foreach ($associated_outings as $outing)
                                        {
                                            $j = $outing['id'];
                                            if (!$outing['geom_wkt'] && // proof that there is no pre-existing geoassociation due to a GPX upload
                                                GeoAssociation::find($j, $document_id, $a_type) === false)
                                            {
                                                $a = new GeoAssociation();
                                                $a->doSaveWithValues($j, $document_id, $a_type);

                                                $created['outings'] = isset($created['outings']) ? $created['outings'] + 1 : 1;
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
        info("\n");
   
        $conn->commit();
        if (isset($created)) associations_result($created);

        if ($is_new_document)
        {
            info('Added new ' . ($is_map ? 'map' : 'area') . " $name ($document_id)\n");
        }
        else
        {
            info('Updated ' . ($is_map ? 'map' : 'area') . " ($document_id)\n");
        }
    }
    catch (Exception $e)
    {
        $conn->rollback();
        throw $e;
    }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

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

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function text_geometry_from_file($filepath)
{
    global $name;

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

        // Check if they are some inner rings
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
        return 'POLYGON(' . $polygons_list[0] . ')';
    }
    else
    {
        return 'MULTIPOLYGON((' . implode('),(', $polygons_list) . '))';
    }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function geometry_from_text($geomtext)
{
    global $conn;

    $geom = $conn->standaloneQuery("SELECT Transform(GeometryFromText('$geomtext', 4326), 900913)")
                 ->fetchAll();

    return $geom[0][0];
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function validate_geometry($geom)
{
    global $conn;

    $geomvalid = $conn->standaloneQuery("SELECT ST_isValid('$geom')")
                      ->fetchAll();

    return $geomvalid[0]['st_isvalid'];
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function progression($i, $tot)
{
    global $prgmsg;

    info("\r$prgmsg  " . (int) (($i + 1) * 100.0 / $tot) . '%');
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function associations_result($r, $creation = true)
{
    info($creation ? 'Association of ' : 'Deassociation of ');
    foreach ($r as $module => $count)
    {
        info("$count $module ");
    }
    info("\n");
}
