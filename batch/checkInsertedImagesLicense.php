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
                'Route'    => array('remarks', 'gear', 'external_resources', 'route_history'));

// map doc id => inserted pictures
$images_for_doc = array();
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
    $where = "(d.id = di.id) AND (di.description ~* E'^\\\\[img'";
    foreach ($fields as $field)
    {
        $where .= " OR di.$field ~* E'^\\\\[img'";
    }
    $where .= ')';
    if ($table == 'Article')
    {
        $where .= ' AND d.article_type=1'; // collaborative articles
    }
    else if ($table =='Image')
    {
        $where .= ' AND d.image_type=1'; // collaborative pictures
    }
    $conn = sfDoctrine::Connection();
    try
    {
        echo ('SELECT ' . $select . ' FROM ' . $table . 's_i18n di, ' . $table . 's d WHERE ' . $where . "\n");
        $conn->beginTransaction();
        $documents = $conn->standaloneQuery('SELECT ' . $select . ' FROM ' . $table . 's_i18n di, ' . $table . 's d WHERE ' . $where)->fetchAll();
        $conn->commit();

        array_unshift($fields, 'description');

        echo ('-> ' . count($documents) . "\n");
        foreach ($documents as $doc)
        {
            $inserted_images = array();

            foreach ($fields as $field) {
                $c = preg_match_all('#\[img=(\s*)([0-9]*)([\w\s]*)\](.*?)\[/img\]\n?#ise',
                                     $doc[$field], $matches, PREG_SET_ORDER);
                for ($i = 0; $i < $c; $i++)
                {
                    array_push($inserted_images, $matches[$i][2]);
                }

                $c = preg_match_all('#\[img=(\s*)([0-9]*)([\w\s]*)\/\]\n?#ise',
                                     $doc[$field], $matches, PREG_SET_ORDER);
                for ($i = 0; $i < $c; $i++)
                {
                    array_push($inserted_images, $matches[$i][2]);
                }
            }

            if(count($inserted_images))
            {
                $docid = $doc['id'] . $doc['culture'];
                $doc['type'] = strtolower($table);
                $images_for_doc[$docid] = $inserted_images;
                $docs[$docid] = $doc;
            }
        }
    }
    catch (Exception $e)
    {
        $conn->rollback();
        echo ("A problem occured during retrieve\n");
        throw $e;
    }
}

// now we have retrieved all images inserted in collaborative documents, get those that are collaborative (and removed bad references)
$all_image_ids = array();
foreach ($images_for_doc as $id => $inserted_images)
{
    $all_image_ids = array_merge($all_image_ids, $inserted_images);
}
$all_image_ids = array_unique($all_image_ids);

// map imgid => image info for images that cause problem
$retrieved_images_ids = array();

echo ('==> ' . count($all_images_ids) . "\n");
if (count($all_images_ids) == 0) {
  echo ("No image found!\n");
  exit;
}
$conn = sfDoctrine::Connection();
try
{
    $conn->beginTransaction();
    $documents = $conn->standaloneQuery('SELECT i.id, a2.user_id, a3.topo_name FROM images i LEFT JOIN app_documents_versions a ON i.id = a.document_id LEFT JOIN app_history_metadata a2 ON a.history_metadata_id = a2.history_metadata_id LEFT JOIN app_users_private_data a3 ON a2.user_id = a3.id WHERE (i.image_type = 2 AND a.version = 1 AND i.id IN (' . implode(',', $all_image_ids) . '))')->fetchAll();
    $conn->commit();

    foreach ($documents as $doc)
    {
        $retrieved_image_ids[$doc['id']] = $doc;
    }
}
catch (Exception $e)
{
    $conn->rollback();
    echo ("A problem occured during retrieve\n");
    throw $e;
}

// list problems per document, then build map user => problems

// map user id => image ids
$images_for_user = array();
// map user id => user info
$users = array();

echo "# LIST OF DOCUMENTS WITH PROBLEMS\n";
foreach ($docs as $docid => $doc)
{
    $warning_ids = array();
    foreach ($images_for_doc[$docid] as $id) // FIXME
    {
        if ($retrieved_image_ids[$id] != null)
        {
            // ok, there is a problem with this id
            $warning_ids[] = $id;
        }
    }
    if (count($warning_ids))
    {
        echo 'http://' . $SERVER_NAME . '/' . strtolower($doc['type']) . 's/'
                . $doc['id'] . '/' . $doc['culture'] . ' "' . $doc['name'] . '"' . "\n";
        foreach ($warning_ids as $id)
        {
              $user_id = $retrieved_image_ids[$id]['user_id'];
              $user_name = $retrieved_image_ids[$id]['topo_name'];

              echo '  http://' . $SERVER_NAME . '/users/' . $id . ' (' . $user_name . ')' . "\n";

              // store in map user id => image ids
              $new_array = array();
              if (array_key_exists($user_id, $images_for_user))
              {
                  $new_array = $images_for_user[$user_id];
              }
              $new_array[] = $id;
              $images_for_user[$user_id] = $new_array;

              // store map user id => user info if needed
              if (!isset($users[$user_id]))
              {
                  $users[$user_id] = $user_name;
              }
        }
    }
}

// list problems per user

echo "\n#LIST OF USERS WITH PROBLEMS\n";
foreach ($images_for_user as $user_id => $image_ids)
{
    echo $users[$user_id] . ' - ' . 'http://' . $SERVER_NAME . '/users/' . $user_id . "\n";
    $image_ids = array_unique($image_ids);
    foreach ($image_ids as $id)
    {
        // TODO: enhance and provide documents where it is inserted with a warning
        echo '  ' . 'http://' . $SERVER_NAME . '/images/' . $id . "\n";
    }
}
