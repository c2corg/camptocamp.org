<?php
/**
 * Batch that creates initial geographic associations (maps and areas) for each document
 * Must be launched after real data have been loaded
 *
 * @version $Id: createInitialAssociations.php 2021 2007-10-10 15:39:53Z fvanderbiest $
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

// Next Step : retrieve highest summits associated (in fact there should always be only one) with routes, and transfer summit geoassociations to route. 

// WARNING: set correct id limits
// WARNING 2: routes must be consecutive.
$id = 44918;
$id_max = 57877;

while ($id <= $id_max)
{
    echo "Computing associations for route $id ... \n";
    
    // if associations with areas for current doc already existed, delete them
    //$deleted = GeoAssociation::deleteAllFor($id, array('dr', 'dc', 'dd', 'dm'));
    //if ($deleted) echo "Deleted $deleted already existing geoassociations. \n";
    
    // get all associated regions (3+maps) with this summit:
    
    //$summit_id is the id of associated summit with route $id
    $associated_docs = Association::findAllAssociatedDocs($id, array('id', 'module'), 'sr'); // summit-route
    foreach ($associated_docs as $doc)
    {
        if ($doc['module'] == 'summits') $summit_id = $doc['id'];
    }
    
    $associations = GeoAssociation::findAllAssociations($summit_id, array('dr', 'dc', 'dd', 'dm'));
    // replicate them with route_id instead of summit_id:
    foreach ($associations as $ea)
    {
        $a = new GeoAssociation();
        $a->doSaveWithValues($id, $ea->get('linked_id'), $ea->get('type'));
        echo "Created association with " . $ea->get('linked_id') . " \n";
        unset($a);
    }
    $id++;
}
