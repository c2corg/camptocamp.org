<?php

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

// config
$TOPO_MODERATOR_USER_ID = 108544; // 315
$DRY_RUN = true;
$DEBUG = true;

// some variables for summary
$stat_tags = 0;
$stat_docs_with_tags = 0;
$stat_invalid_references = 0;
$stat_tags_per_doc = array();
$stat_associations_required = 0;

// define fields that must be tested
// description field is always included
$lookup = array('Area'     => array(),
                'Article'  => array(),
                'Book'     => array(),
                'Image'    => array(),
                'Map'      => array(),
                'Summit'   => array(),
                'User'     => array(),
                'Outing'   => array('weather', 'conditions', 'hut_comments', 'access_comments'),
                'Site'     => array('remarks', 'pedestrian_access', 'way_back', 'site_history'),
                'Parking'  => array('accommodation'),
                'Hut'      => array('pedestrian_access'),
                'Route'    => array('remarks', 'gear', 'external_resources', 'route_history'));

foreach($lookup as $table => $fields)
{
    switch($table)
    {
        case 'Area':    $association_type = 'ai'; break;
        case 'Article': $association_type = 'ci'; break;
        case 'Book':    $association_type = 'bi'; break;
        case 'Image':   $association_type = 'ii'; break;
        case 'Map':     $association_type = 'mi'; break;
        case 'Summit':  $association_type = 'si'; break;
        case 'User':    $association_type = 'ui'; break;
        case 'Outing':  $association_type = 'oi'; break;
        case 'Site':    $association_type = 'ti'; break;
        case 'Parking': $association_type = 'pi'; break;
        case 'Hut':     $association_type = 'hi'; break;
        case 'Route':   $association_type = 'ri'; break;
    }

    // retrieve all documents with an image tag
    $select = 'di.id, di.culture, di.description';
    foreach ($fields as $field)
    {
        $select .= ", di.$field";
    }
    $where = "description ILIKE '%[/img]%'";
    foreach ($fields as $field)
    {
        $where .= " OR $field ILIKE '%[/img]%'";
    }
    $query =  Doctrine_Query::create()
        ->select($select)
        ->from($table . "I18n di")
        ->where($where);
    $documents = $query->execute();

    $stat_docs_with_tags += count($documents);

    array_unshift($fields, 'description');

    // a document correspond to an ID + a culture
    // for each of these, do appropriate associations, and update valid image tags
    foreach ($documents as $doc)
    {
        if($DEBUG)
        {
            echo 'Updating doc ' . $doc['id'] . ' (' . $doc['culture'] . ') http://'.$_SERVER['SERVER_NAME']. '/documents/' . $doc['id'] . '/' . $doc['culture'] . "\n";
        }

        $tags = array();
        $tags_for_field = array();
        foreach ($fields as $field) {
            $tags_for_field[$field] = array();
            $c = preg_match_all('#\[img\|?((?<=\|)\w*|)\](\s*)([0-9_]*?)\.(jpg|jpeg|png|gif)(\s*)\[/img\]#ise',
                                 $doc[$field], $matches, PREG_SET_ORDER);
            for ($i = 0; $i < $c; $i++)
            {
                $l = count($tags);
                $tags[$l][0] = $matches[$i][0];
                $tags[$l][1] = $matches[$i][3] . '.' . $matches[$i][4];
                $tags[$l][2] = $matches[$i][1];
                $tags[$l][3] = '';
                array_push($tags_for_field[$field], $l);
            }

            $c = preg_match_all('#\[img=(\s*)([0-9_]*?)\.(jpg|jpeg|png|gif)(\s*)\|?((?<=\|)\w*|)\](.*?)\[/img\]#ise',
                                 $doc[$field],$matches, PREG_SET_ORDER);
            for ($i = 0; $i < $c; $i++)
            {
                $l = count($tags);
                $tags[$l][0] = $matches[$i][0];
                $tags[$l][1] = $matches[$i][2] . '.' . $matches[$i][3];
                $tags[$l][2] = $matches[$i][5];
                $tags[$l][3] = $matches[$i][6];
                array_push($tags_for_field[$field], $l);
            }
        }

        if(empty($tags))
        {
            if($DEBUG)
            {
                echo "  No valid tag found in doc\n";
            }
            continue;
        }

        // get image ids corresponding to filenames, create required associations
        $image_ids = array();
        foreach($tags as $tag)
        {
            if (empty($tag[1]) || !empty($image_ids[$tag[1]]))
            {
                continue;
            }
            // get image id corresponding to filename
            $query =  Doctrine_Query::create()
                ->select('i.id')
                ->from('Image i')
                ->where('filename = \'' . $tag[1] . "'");
            $image_data = $query->execute()->getFirst();
            if(!(empty($image_data)))
            {
                // does the relation already exists, or should it be created?
                $association = Association::find($doc['id'], $image_data['id'], $association_type, true);
                if(empty($association))
                {
                    // create association
                    if($DEBUG)
                    {
                        echo '  Create association with image ' . $image_data['id'] . "\n";
                    }
                    if(!$DRY_RUN)
                    {
                        $asso = new Association();
                        $asso->doSaveWithValues($doc['id'], $image_data['id'], $association_type, $TOPO_MODERATOR_USER_ID);
                    }
                    $stat_associations_required++;
                }
                $image_ids[$tag[1]] = $image_data['id'];
            }
            else
            {
                // no corresponding id, the tag is incorrect and must not be modified. but a warning should be notified
                $stat_docs_with_invalid_references[] = $doc['id'] . ' (' . $doc['culture'] . ') http://'.$_SERVER['SERVER_NAME']. '/documents/' . $doc['id'] . '/' . $doc['culture'] . "\n";
            }
        }

        // replace image tags
        $conn = sfDoctrine::Connection();
        $db_doc = Document::find($table, $doc['id']);
        if (!$DRY_RUN)
        {
            $conn->beginTransaction();

            $history_metadata = new HistoryMetadata();
            $history_metadata->setComment('Updated image tags');
            $history_metadata->set('is_minor', true);
            $history_metadata->set('user_id', $TOPO_MODERATOR_USER_ID);
            $history_metadata->save();

            $db_doc->setCulture($doc['culture']);
        }
        foreach ($fields as $field)
        {
            $tag_data = $tags_for_field[$field];
            $text = $doc[$field];
            foreach ($tag_data as $tag_idx)
            {
                $references_to_modify++;
                $tag = $tags[$tag_idx];
                // do not care about images with invalid reference
                if (empty($image_ids[$tag[1]]))
                {
                    continue;
                }
                $image_id = $image_ids[$tag[1]];
                $replacement = '[img=' . $image_id;
                if (empty($tag[2]))
                {
                    $tag[2] = 'right';
                }
                $replacement .= ' ' . $tag[2];
                $replacement .= ']' . $tag[3] . '[/img]';
                $text = str_replace($tag[0], $replacement, $text);
                if($DEBUG)
                {
                    echo '  ' . $tag[0] . ' -> ' . $replacement . ' http://'.$_SERVER['SERVER_NAME']. '/images/' . $image_id . "\n";
                }
                if (!$DRY_RUN)
                {
                    $db_doc->set($field, $text);
                }
            }
        }
        if (!$DRY_RUN)
        {
            $db_doc->save();
            $conn->commit();
        }

        $tags_for_doc = count($tags);
        $stat_tags += $tags_for_doc;
        $c = $stat_tags_per_doc[$tags_for_doc];
        if (empty($c))
        {
            $stat_tags_per_doc[$tags_for_doc] = 1;
        }
        else
        {
            $stat_tags_per_doc[$tags_for_doc] = $c + 1;
        }
    }
}

echo "\n** Summary **\n";
echo 'Found ' . $stat_tags . ' tags among ' . $stat_docs_with_tags . " potential documents with inline images\n";
echo "Tags per document field:\n";
foreach ($stat_tags_per_doc as $tag_count => $doc_count)
{
    echo "  $tag_count tag(s) => $doc_count document(s)\n";
}
echo "$stat_associations_required associations were created\n";
if (count($stat_docs_with_invalid_references) > 0)
{
    echo 'Found ' . count($stat_docs_with_invalid_references) . " invalid references to images in tags:\n";
    foreach ($stat_docs_with_invalid_references as $d)
    {
        echo "  $d\n";
    }
}

