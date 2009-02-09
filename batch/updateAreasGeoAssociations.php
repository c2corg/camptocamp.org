<?php
/**
 * Updates geo associations of documents contained in parameter areas.
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

// ids of areas containing objects of whom geoassociations must be updated:
$areas_ids = array(369, 379);

$i = 0;
foreach ($areas_ids as $id)
{
    // find all doc associated to current area
    $associated_docs = GeoAssociation::findAllAssociations($id, null, 'linked');
    foreach ($associated_docs as $doc)
    {
        $doc_id = $doc->get('main_id');

        if (!$document = Document::find('Document', $doc_id, array('module'))) continue;

        // no maps are linked to outings, users and other maps
        $linkToMaps = !in_array($document->get('module'), array('outings', 'users', 'maps'));


        gisQuery::createGeoAssociations($doc_id, true, $linkToMaps);
        
        // TODO: handle "inherited" geo associations such as those of routes and outings

        $i++;
    }
}
echo "$i documents updated\n";
