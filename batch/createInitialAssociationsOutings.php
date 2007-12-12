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

// Next Step : retrieve routes associated (in fact there should always be only one) with outings, and transfer route geoassociations to outing. 

// WARNING: set correct id limits:
// WARNING 2: outings must be consecutive
$id = 57878;
$id_max = 101593;

while ($id <= $id_max)
{
    echo "Computing associations for outing $id ... \n";
    
    // if associations with areas for current doc already existed, delete them
    //$deleted = GeoAssociation::deleteAllFor($id, array('dr', 'dc', 'dd', 'dm'));
    //if ($deleted) echo "Deleted $deleted already existing geoassociations. \n";
    
    $doc_id = 0;
    
    //$route_id is the id of associated route with outing $id
    $associated_docs = Association::findAllAssociatedDocs($id, array('id', 'module'), 'ro');
    if (!empty($associated_docs))
    {
        foreach ($associated_docs as $doc)
        {
            if ($doc['module'] == 'routes') $doc_id = $doc['id'];
        }
    }
    else
    {
        $associated_docs = Association::findAllAssociatedDocs($id, array('id', 'module'), 'to');
        foreach ($associated_docs as $doc)
        {
            if ($doc['module'] == 'sites') $doc_id = $doc['id'];
        }
    }
    
    if ($doc_id)
    {
        $associations = GeoAssociation::findAllAssociations($doc_id, array('dr', 'dc', 'dd'));
        // replicate them with outing_id instead of route_id:
        // Note: map associations are not transfered
        foreach ($associations as $ea)
        {
            $a = new GeoAssociation();
            $a->doSaveWithValues($id, $ea->get('linked_id'), $ea->get('type'));
            echo "Created association with " . $ea->get('linked_id') . " \n";
            unset($a);
        }
        unset($ea);
    }
    $id++;
    
    //echo 'Memory consumption: ' . number_format(memory_get_usage()) . " bytes\n";
    
}

