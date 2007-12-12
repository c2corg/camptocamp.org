<?php
/**
 * Batch that creates initial geographic associations (maps and areas) for each document
 * Must be launched after real data have been loaded
 *
 * @version $Id: createInitialAssociations.php 2183 2007-10-26 09:14:16Z alex $
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

// WARNING: set correct id limits below

// summits
makeAssociations(36992, 44917);

// sites, huts, parkings
makeAssociations(101594, 104161);

// maps
makeAssociations(104162, 106276, true);

function makeAssociations($id_min, $id_max, $is_map = false)
{
    $id = $id_min;
    while ($id <= $id_max)
    {
        echo "Computing associations for document $id ... \n";
        
        // if associations with areas for current doc already existed, delete them
        //$deleted = Association::deleteAllFor($id, array('dr', 'dc', 'dd', 'dm'));
        //if ($deleted) echo "Deleted $deleted already existing associations. \n";
        
        // compute new associations
        $areas = gisQuery::getAreasContaining($id);
        $maps = gisQuery::getMapsContaining($id);
                        
        // perform association with these areas.
        foreach ($areas as $area)
        {                        
            switch ($area['type']) 
            {
                case 1: // range
                    $type = 'dr';
                    break;
                case 2: // country
                    $type = 'dc';
                    break;
                case 3: // dept
                    $type = 'dd';
                    break;
            }
                            
            $a = new GeoAssociation();
            echo "[type=$type] found area : " . $area['id'] . "\n" ;
            $a->doSaveWithValues($id, $area['id'], $type); // main, linked, type
            unset($a);
        }
                        
        // perform association with these maps if current document is not a map.
        if (!$is_map)
        {
            foreach ($maps as $map)
            {
                $a = new GeoAssociation();
                echo "[type=dm] found map : " . $map['id'] . "\n" ;
                $a->doSaveWithValues($id, $map['id'], 'dm'); // main, linked, type
                unset($a);
            } 
        }
        
        $id++;
    }
}
