<?php
/**
 * $Id$
 */
class Association extends BaseAssociation
{

    public static function find($main_id, $linked_id, $type = null, $strict = true)
    {
        if ($strict)
        {
            $where = 'a.main_id = ? AND a.linked_id = ?';
            $where_array = array($main_id, $linked_id);
        }
        else
        {
            $where = '(( a.main_id = ? AND a.linked_id = ? ) OR ( a.linked_id = ? AND a.main_id = ? ))';
            $where_array = array($main_id, $linked_id, $main_id, $linked_id);
        }
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }
                
        return Doctrine_Query::create()
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute()
                             ->getFirst();
    }
    
    public static function findAllAssociations($id, $types = null)
    {
        $where = '( a.main_id = ? OR a.linked_id = ? )';
        $where_array = array($id, $id);
        
        if ($types)
        {
            $where .= ' AND ( ';
            
            if (!is_array($types))
            {
                $types = array($types);
            }

            $where2 = array();

            foreach ($types as $type)
            {
                $where2[] = 'a.type = ?';
                $where_array[] = $type;
            }

            $where .= implode(' OR ', $where2 ) . ' )';
        }
                
        return Doctrine_Query::create()
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute();
    }    
    
    
    // FIXME: factorize with findAllWithBestName
    public static function findAllAssociatedDocs($ids, $fields = array('*'), $types = null)
    {
        $select = implode(', ', $fields);
        
        if (!is_array($ids))
        {
            $ids = array($ids);
        }
        elseif (!count($ids))
        {
            return array();
        }
        
        $where_array = $ids;
        $where_ids = array();
        foreach ($ids as $id)
        {
            $where_ids[] = '?';
        }
        $where_ids = implode(', ', $where_ids);
        if (count($ids) == 1)
        {
            $where_ids = '= ' . $where_ids;
        }
        else
        {
            $where_ids = 'IN ( ' . $where_ids . ' )';
        }
        
        if ($types)
        {
            if (!is_array($types))
            {
                $types = array($types);
            }

            $where2 = array();
            foreach ($types as $type)
            {
                $where2[] = 'a.type = ?';
                $where_array[] = $type;
            }

            $where = '( ' . implode(' OR ', $where2 ) . ' )';
            $where_array2 = array_merge($where_array, $where_array);
            
            $query = "SELECT $select " .
                 'FROM documents ' .
                 'WHERE id IN '. 
                 "((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id $where_ids AND $where) ".
                 "UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id $where_ids AND $where)) ".
                 'ORDER BY id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, $where_array2)
                        ->fetchAll();
        }
        else
        {
            $query = "SELECT $select " .
                 'FROM documents ' .
                 'WHERE id IN '. 
                 "((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id $where_ids) ".
                 "UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id $where_ids)) ".
                 'ORDER BY id ASC';

            $where_array2 = array_merge($where_array, $where_array);
            
            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, $where_array2)
                        ->fetchAll();
        }
                
        return $results;
    }  
    
    
    public static function findMainAssociatedDocs($ids, $fields = array('*'), $types = null, $current_doc_ids = null)
    {
        $select = implode(', ', $fields);
        
        if (!is_array($ids))
        {
            $ids = array($ids);
        }
        elseif (!count($ids))
        {
            return array();
        }
        $where_array = $ids;
        $where_ids = array();
        foreach ($ids as $id)
        {
            $where_ids[] = '?';
        }
        $where = 'a.linked_id ';
        $where_ids = implode(', ', $where_ids);
        if (count($ids) == 1)
        {
            $where .= ' = ' . $where_ids;
        }
        else
        {
            $where .= ' IN ( ' . $where_ids . ' )';
        }
        
        if (!empty($current_doc_ids))
        {
            if (!is_array($current_doc_ids))
            {
                $current_doc_ids = array($current_doc_ids);
            }

            $where2 = array();
            foreach ($current_doc_ids as $current_doc_id)
            {
                $where2[] = 'a.main_id != ?';
                $where_array[] = $current_doc_id;
            }

            $where2 = implode(' AND ', $where2 );
            $where .= " AND $where2";
        }
        
        if ($types)
        {
            if (!is_array($types))
            {
                $types = array($types);
            }

            $where2 = array();
            foreach ($types as $type)
            {
                $where2[] = 'a.type = ?';
                $where_array[] = $type;
            }

            $where .= ' AND ( ' . implode(' OR ', $where2 ) . ' )';
        }
        
        $query = "SELECT $select " .
             'FROM documents ' .
             'WHERE id IN '. 
             "(SELECT a.main_id FROM app_documents_associations a WHERE $where) ".
             'ORDER BY id ASC';

        $results = sfDoctrine::connection()
                    ->standaloneQuery($query, $where_array)
                    ->fetchAll();
                
        return $results;
    }  
    
    
    private static function computeLangRank($user_prefered_langs, $culture)
    {
        $_a = array_keys($user_prefered_langs, $culture);
        $rank = array_shift($_a);
        return ($rank === null) ? 20 : $rank; // if lang not in prefs, return a 'high' rank
    }
    
    
    // build the actual results based on the user's prefered language
    // try to select best name only if there is choice, ie if there are at least two lines with same id and different cultures.
    private static function setBestName($results, $user_prefered_langs)
    {
        $out = array();
        $i = 0;
        $previous_result_id = 0;
                
        foreach ($results as $result)
        {
            $current_id = $result['id'];
            $rank = self::computeLangRank($user_prefered_langs, $result['culture']);
            
            if ($current_id != $previous_result_id)
            {
                // take current record and add it into new array
                $out[$i] = $result;
                $best_result_culture_rank = $rank;
                $i++;
            }
            elseif ($rank < $best_result_culture_rank)
            {
                // take current record and replace previous record into new array
                $out[$i - 1] = $result;
                $best_result_culture_rank = $rank;
            }
            $previous_result_id = $current_id;
        }
        return $out;
    }


    public static function findAllWithBestName($id, $user_prefered_langs, $types = null)
    {
        // we use lon/lat instead of geom_wkt to avoid retrieving heavy routes/areas wkt
        $fields = 'm.module, m.elevation, mi.id, mi.culture, mi.name, mi.search_name, makePointWkt(m.lon, m.lat) as pointwkt';
        
        if ($types)
        {
            if (!is_array($types))
            {
                $types = array($types);
            }

            $where2 = array();
            $where_array = array($id);
            foreach ($types as $type)
            {
                $where2[] = 'a.type = ?';
                $where_array[] = $type;
            }

            $where = '( ' . implode(' OR ', $where2 ) . ' )';
            $where_array2 = array_merge($where_array, $where_array);
            
            $query = "SELECT $fields " . 
                 'FROM documents_i18n mi LEFT JOIN documents m ON mi.id = m.id ' .
                 'WHERE mi.id IN '. 
                 "((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id = ? AND $where) ".
                 "UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ? AND $where)) ".
                 'ORDER BY mi.id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, $where_array2)
                        ->fetchAll();
        }
        else
        {
            $query = "SELECT $fields " .
                 'FROM documents_i18n mi LEFT JOIN documents m ON mi.id = m.id ' .
                 'WHERE mi.id IN '. 
                 '((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id = ?) '.
                 'UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ?)) '.
                 'ORDER BY mi.id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, array($id, $id)) 
                        ->fetchAll();
        }
        
        $out = self::setBestName($results, $user_prefered_langs);
        return $out;
    }


    /*
     * retrieve docs linked with $ids (and their names)
     * $get_associated_ids: if you need the id of the element it is linked to (requires a more complex query, so left as an option)
     * $get_linked: depending on the association, the element will be main_id or linked_id
     * $current_doc_ids: docs to exclude from the results
     */
    public static function findLinkedDocsWithBestName($ids, $user_prefered_langs, $types = null, $get_associated_ids = false, $get_linked = true, $current_doc_ids = null)
    {
        if (!is_array($ids))
        {
            $ids = array($ids);
        }
        elseif (!count($ids))
        {
            return array();
        }

        if (!$get_associated_ids)
        {
            $where_array = $ids;
            $where_ids = array();
            foreach ($ids as $id)
            {
                $where_ids[] = '?';
            }
            
            if ($get_linked)
            {
                $select_id = 'linked_id';
                $where_id = 'main_id';
            }
            else
            {
                $select_id = 'main_id';
                $where_id = 'linked_id';
            }
            $where = "a.$where_id IN ( " . implode($where_ids, ', ') . " )";
            
            if ($types)
            {
                $where .= ' AND ( ';
                
                if (!is_array($types))
                {
                    $types = array($types);
                }

                $where2 = array();
                foreach ($types as $type)
                {
                    $where2[] = 'a.type = ?';
                    $where_array[] = $type;
                }

                $where .= implode(' OR ', $where2 ) . ' )';
            }

            if (!empty($current_doc_ids))
            {
                if (!is_array($current_doc_ids))
                {
                    $current_doc_ids = array($current_doc_ids);
                }
                $where_ids = array();
                foreach ($current_doc_ids as $current_doc_id)
                {
                    $where_ids[] = '?';
                    $where_array[] = $current_doc_id;
                }

                $where .= " AND a.$select_id NOT IN ( " . implode(', ', $where_ids) . " )";
            }
            
            $doc_ids = "SELECT a.$select_id FROM app_documents_associations a WHERE $where";
        }
        else
        {
            $doc_associations = self::countAll($ids, $types, $current_doc_ids);
            if (!count($doc_associations))
            {
                return array();
            }
            
            $doc_associations_norm = array();
            $where_ids = $where_array = array();
            foreach ($doc_associations as $association)
            {
                $association_norm = array();
                if (in_array($association['main_id'], $ids))
                {
                    $association_norm['parent_id'] = $association['main_id'];
                    $association_norm['id'] = $association['linked_id'];
                    $association_norm['rel_parent'] = 'main_id';
                }
                else
                {
                    $association_norm['parent_id'] = $association['linked_id'];
                    $association_norm['id'] = $association['main_id'];
                    $association_norm['rel_parent'] = 'linked_id';
                }
                $doc_associations_norm[] = $association_norm;
                $where_ids[] = '?';
                $where_array[] = $association_norm['id'];
            }
            $doc_ids = implode(', ', $where_ids);
        }

        $query = 'SELECT m.module, m.elevation, mi.id, mi.culture, mi.name, mi.search_name ' .
                 'FROM documents_i18n mi LEFT JOIN documents m ON mi.id = m.id ' .
                 'WHERE mi.id IN '. 
                 "($doc_ids) " .
                 'ORDER BY mi.id ASC';

        $results = sfDoctrine::connection()
                        ->standaloneQuery($query, $where_array)
                        ->fetchAll();

        $out = self::setBestName($results, $user_prefered_langs);
        
        if ($get_associated_ids)
        {
            foreach ($out as $key => $result)
            {
                foreach ($doc_associations_norm as $association_norm)
                {
                    if ($association_norm['id'] == $result['id'])
                    {
                        $out[$key]['parent_relation'][$association_norm['parent_id']] = $association_norm['rel_parent'] === 'linked_id' ?
                            'child' : 'parent';
                    }
                    if ($association_norm['parent_id'] == $result['id'])
                    {
                        $out[$key]['parent_relation'][$association_norm['id']] = $association_norm['rel_parent'] === 'linked_id' ?
                            'parent' : 'child';
                    }
                }
            }
        }

        return $out;
    }
    
    // Search the list of linked docs to documents
    // Return a flat and ordered list with all docs with hierarchical information
    public static function createHierarchyWithBestName($docs, $user_prefered_langs, $type = null, $current_doc_id = 0, $keep_current_doc = false, $sort_field = null, $show_sub_docs = true)
    {
        if (!count($docs))
        {
            return $docs;
        }

        $parent_ids = array();
        foreach ($docs as $doc)
        {
            $parent_ids[] = $doc['id'];
        }

        $linked_docs = self::findLinkedDocsWithBestName($parent_ids, $user_prefered_langs, $type, true, true, ($keep_current_doc ? null : $current_doc_id));

        return self::createHierarchy($docs, $linked_docs, $type, $sort_field, $show_sub_docs, $current_doc_id);
    }

    // Given a list of documents and a list of linked docs, along with parent-child relations,
    // output a flat list of the documents, but ordered and annotated with the hierarchy, like:
    // docA - level 1
    // docB - level 2, child of docA
    // docC - level 2, child of docA, [current doc]
    // docD - level 1
    // docE - level 2, child of docD
    // docF - level 3, child of docE
    //
    // No DB request is done
    public static function createHierarchy($docs, $linked_docs, $type = null, $sort_field = null, $show_sub_docs = true, $current_doc_id = 0)
    {
        if (!count($docs))
        {
            return $docs;
        }

        // internal order between docs of same level
        $order = null;
        if (empty($sort_field))
        {
            switch ($type)
            {
                case 'ss' :
                    $sort_field = 'elevation';
                    $order = SORT_DESC;
                    break;
                
                case 'pp' :
                    $sort_field = 'elevation';
                    $order = SORT_ASC;
                    break;
                
                default :
                    $sort_field = 'name';
            }
        }

        // add relation information to 1-hop docs
        foreach ($docs as $id => $doc)
        {
            $docs[$id]['link_tools'] = true; // mark it has directly linked to doc: we can display association tools to moderators
            $doc['parent_relation'] = array();
            foreach ($linked_docs as $doc2)
            {
                if (isset($doc2['parent_relation'][$doc['id']]))
                {
                    $docs[$id]['parent_relation'][$doc2['id']] = ($doc2['parent_relation'][$doc['id']] == 'parent') ? 'child' : 'parent';
                }
            }
        }
        $all_docs = array_merge($docs, $linked_docs);

        // Mark original document
        foreach ($all_docs as $key => $doc)
        {
            if ($doc['id'] == $current_doc_id)
            {
                $all_docs[$key]['is_doc'] = true;
                break;
            }
        }

        // if documents from different hierarchical levels are linked, we might have duplicates at that point
        // we should remove them
        $ids = $keys = array();
        foreach($all_docs as $key => $doc)
        {
            if ($pos = array_search($doc['id'], $ids))
            {
                $all_docs[$keys[$pos]]['parent_relation'] = isset($all_docs[$keys[$pos]]['parent_relation']) ?
                    $all_docs[$keys[$pos]]['parent_relation'] + $doc['parent_relation'] :
                    $doc['parent_relation'];
                unset($all_docs[$key]);
            }
            else
            {
                $ids[] = $doc['id'];
                $keys[] = $key;
            }
        }

        // get all docs that don't have parents and put them in the output list with level 1
        foreach($all_docs as $id => $doc)
        {
            if (!isset($doc['parent_relation']) || array_search('parent', $doc['parent_relation']) === false)
            {
                $doc['level'] = 1;
                $output[] = $doc;
                unset($all_docs[$id]);
            }
        }
        $output = c2cTools::sortArray($output, $sort_field, null, $order);

        // for each level 1 doc, get corresponding children and insert them into the list
        // repeat the same process for level 2 docs
        for ($level=2; $level<=3; $level++)
        {
            $offset = 1;
            foreach ($output as $pos => $sorted_doc)
            {
                // only go through level 2 docs on second run
                if ($level == 3 && $sorted_doc['level'] !== 2) continue;

                $sub_docs = array();
                foreach ($all_docs as $id => $doc)
                {
                    if (array_key_exists($sorted_doc['id'], $doc['parent_relation'])) // it can only be a child
                    {
                        $doc['level'] = $level;
                        $sub_docs[] = $doc;
                        unset($all_docs[$id]);
                    }
                }
                if (count($sub_docs))
                {
                    $sub_docs = c2cTools::sortArray($sub_docs, $sort_field, null, $order);
                    array_splice($output, $pos + $offset, 0, $sub_docs);
                    $offset = $offset + count($sub_docs);
                }
            }
        }

        return $output;
    }

    public static function countLinked($main_id, $type = null)
    {
        $where = 'a.main_id = ?';
        $where_array = array($main_id);
        
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }
        
        return Doctrine_Query::create()
                             ->select('COUNT(DISTINCT a.linked_id) nb_linked')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute()
                             ->getFirst()->nb_linked;  
    }

    public static function countMains($linked_id, $types = null)
    {
        $where = 'a.linked_id = ?';
        $where_array = array($linked_id);
        
        if ($types)
        {
            $types_array = is_array($types) ? $types : array($types);
            foreach ($types_array as $type)
            {
                $wherein_clause[] = '?';
                $where_array[] = $type;
            }
            $where .= ' AND a.type IN ' . '( ' . implode(', ', $wherein_clause) . ' )';
        }
        
        return Doctrine_Query::create()
                             ->select('COUNT(DISTINCT a.main_id) nb_main')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute()
                             ->getFirst()->nb_main;  
    }

    // deletes all existing associations for main_id matching type in array $types
    public static function deleteAllFor($main_id, $types)
    {
        $where = 'a.main_id = ? AND a.type IN ';
        $where_array = array($main_id);
        $wherein_clause = array();
        foreach ($types as $type)
        {
            $wherein_clause[] = '?';
            $where_array[] = $type;
        }
        $where .= '( ' . implode(', ', $wherein_clause) . ' )';

        $rows = Doctrine_Query::create()
                             ->delete('Association')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(); 
        return $rows;
    }
    
    
    // deletes all existing associations concerning a particular document id
    // FIXME: merge with previous method ?
    public static function deleteAll($id)
    {
        $where = 'a.main_id = ? OR a.linked_id = ?';
        $where_array = array($id, $id);

        $rows = Doctrine_Query::create()
                             ->delete('Association')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(); 
        return $rows;
    }
    
    public function doSaveWithValues($main_id, $linked_id, $type, $user_id)
    {
        $conn = sfDoctrine::Connection();
        try
        {
            $conn->beginTransaction();
            
            // we create the association:
            $this->main_id = $main_id;
            $this->linked_id = $linked_id;
            $this->type = $type;
            $this->save();

            // and we log this:
            $al = new AssociationLog();
            $al->main_id = $main_id;
            $al->linked_id = $linked_id;
            $al->type = $type;
            $al->user_id = $user_id;
            $al->is_creation = true;
            $al->save();

            $conn->commit();

            // send an email to moderators if a picture is associated to a book
            if ($type == 'bi')
            {
                try
                {
                    // retrieve moderators email
                    $moderator_id = sfConfig::get('app_moderator_user_id'); // currently send to topo-fr only
                    $conn->beginTransaction();
                    $rows = $conn->standaloneQuery('SELECT email FROM app_users_private_data d WHERE id = ' . $moderator_id)->fetchAll();
                    $conn->commit();
                    $email_recipient = $rows[0]['email'];
                    $mail = new sfMail();
                    $mail->setCharset('utf-8');

                    // definition of the required parameters
                    $mail->setSender(sfConfig::get('app_outgoing_emails_sender'));
                    $mail->setFrom(sfConfig::get('app_outgoing_emails_from'));
                    $mail->addReplyTo(sfConfig::get('app_outgoing_emails_reply_to'));

                    $mail->addAddress($email_recipient);

                    $mail->setSubject('New image associated to book');

                    $mail->setContentType('text/html');
                    $server = $_SERVER['SERVER_NAME'];
                    $body = "<p>A <a href=\"http://$server/images/$linked_id\">new image</a> has been associated to <a href=\"http://$server/books/$main_id\">book $main_id</a>.</p>"
                        . "<p>The image may require a copyright license. If so, please ensure that:</p>"
                        . "<ul>"
                        . "<li>the owner is correctly acknowledged in the author field;</li>"
                        . "<li>the image is not too big (max 800px width or height).</li>"
                        . "</ul>";
                    $mail->setBody($body);

                    // send the email
                    $mail->send();
                }
                catch (exception $e)
                {
                    $conn->rollback();
                    c2cTools::log("Association::doSaveWithValues($main_id, $linked_id, $type, $user_id) failed sending email for image associated to book");
                }
            }

            return true;
        }
        catch (exception $e)
        {
            $conn->rollback();
            c2cTools::log("Association::doSaveWithValues($main_id, $linked_id, $type, $user_id) failed - rollback");
            return false;
        }
    }
    
    public static function countAllLinked($main_ids, $type = null)
    {
        $where = array();
        foreach ($main_ids as $main_id)
        {
            $where[] = '?';
        }
        $where = 'a.main_id IN ( ' . implode(', ', $where) . ' )';
        $where_array = $main_ids;
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }
        
        return Doctrine_Query::create()
                             ->select('DISTINCT a.main_id, a.linked_id')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }
    
    public static function countAllMain($linked_ids, $type = null)
    {
        $where = array();
        foreach ($linked_ids as $linked_id)
        {
            $where[] = '?';
        }
        $where = 'a.linked_id IN ( ' . implode(', ', $where) . ' )';
        $where_array = $linked_ids;
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }
        
        return Doctrine_Query::create()
                             ->select('DISTINCT a.main_id, a.linked_id')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }
    
    public static function countAll($ids, $types = null, $current_doc_ids = null, $get_type = false)
    {
        $where_array = $ids;
        $where_ids = array();
        foreach ($ids as $id)
        {
            $where_ids[] = '?';
        }
        $where_ids = '( ' . implode(', ', $where_ids) . ' )';
        
        if (empty($current_doc_ids))
        {
            $where = "( a.linked_id IN $where_ids OR a.main_id IN $where_ids )";
        }
        else
        {
            if (!is_array($current_doc_ids))
            {
                $current_doc_ids = array($current_doc_ids);
            }

            $where2 = array();
            $where3 = array();
            foreach ($current_doc_ids as $current_doc_id)
            {
                $where2[] = 'a.main_id != ?';
                $where3[] = 'a.linked_id != ?';
                $where_array[] = $current_doc_id;
            }

            $where2 = implode(' AND ', $where2 );
            $where3 = implode(' AND ', $where3 );
            $where = "( ( a.linked_id IN $where_ids AND $where2 ) OR ( a.main_id IN $where_ids AND $where3 ) )";
        }
        
        $where_array = array_merge($where_array, $where_array);
        
        if ($types)
        {
            $where .= ' AND ( ';
            
            if (!is_array($types))
            {
                $types = array($types);
            }

            $where2 = array();

            foreach ($types as $type)
            {
                $where2[] = 'a.type = ?';
                $where_array[] = $type;
            }

            $where .= implode(' OR ', $where2 ) . ' )';
        }
        
        $fields = 'a.main_id, a.linked_id';
        if ($get_type)
        {
            $fields .= ', a.type';
        }
        
        return Doctrine_Query::create()
                             ->select("DISTINCT $fields")
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }
    
}
