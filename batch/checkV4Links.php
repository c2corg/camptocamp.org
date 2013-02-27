<?php

// This script lists all pictures inserted in collaborative documents that are still personal pictures.

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

// config
$SERVER_NAME = 'www.camptocamp.org';

// define fields that must be tested (personal-only documents are not included)
// description field is always included
$lookup = array('Area'     => array(),
                'Article'  => array(),
                'Book'     => array(),
                'Image'    => array(),
                'Map'      => array(),
                'Summit'   => array(),
                'Site'     => array('remarks', 'pedestrian_access', 'way_back', 'external_resources', 'site_history'),
                'Parking'  => array('accommodation'),
                'Hut'      => array('pedestrian_access'),
                'Route'    => array('remarks', 'gear', 'external_resources', 'route_history'),
                'Outing'   => array('conditions', 'access_comments', 'hut_comments'),
                'User'    => array());

// map doc id => document
$docs = array();

// retrieve image ids inserted in collaborative content
// it does not exclude valid tags rejected because the picture is not associated to the document.
// testing this would cost quite a lot for a very few cases (maybe even no one)
foreach($lookup as $table => $fields)
{
    // retrieve all documents with an image tag
    $select = 'di.id, di.name, di.culture, di.description';
    foreach ($fields as $field)
    {
        $select .= ", di.$field";
    }
    $where = "(d.id = di.id) AND (di.description ~* E'http://(?:alpinisme|skirando|escalade)\\.camptocamp\\.com'";
    foreach ($fields as $field)
    {
        $where .= " OR di.$field ~* E'http://(?:alpinisme|skirando|escalade)\\.camptocamp\\.com'";
    }
    $where .= ')';
    $conn = sfDoctrine::Connection();
    try
    {
        $conn->beginTransaction();
        $documents = $conn->standaloneQuery('SELECT ' . $select . ' FROM ' . $table . 's_i18n di, ' . $table . 's d WHERE ' . $where)->fetchAll();
        $conn->commit();

        array_unshift($fields, 'description');

        foreach ($documents as $doc)
        {
            echo 'http://' . $SERVER_NAME . '/' . strtolower($table) . 's/'
                        . $doc['id'] . '/' . $doc['culture'] . ' "' . $doc['name'] . '"' . "\n";
        }
    }
    catch (Exception $e)
    {
        $conn->rollback();
        echo ("A problem occured during retrieve\n");
        throw $e;
    }
}

