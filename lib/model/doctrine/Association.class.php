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
    public static function findAllAssociatedDocs($id, $fields = array('*'), $type = null)
    {
        $select = implode(', ', $fields);
        
        if ($type)
        {
            $query = "SELECT $select " .
                 'FROM documents ' .
                 'WHERE id IN '. 
                 '((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id = ? AND type = ?) '.
                 'UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ? AND type = ?)) '.
                 'ORDER BY id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, array($id, $type, $id, $type))
                        ->fetchAll();
        }
        else
        {
            $query = "SELECT $select " .
                 'FROM documents ' .
                 'WHERE id IN '. 
                 '((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id = ?) '.
                 'UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ?)) '.
                 'ORDER BY id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, array($id, $id)) 
                        ->fetchAll();
        }
                
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


    public static function findAllWithBestName($id, $user_prefered_langs, $type = null)
    {

        if ($type)
        {
            $query = 'SELECT m.module, m.elevation, mi.id, mi.culture, mi.name, mi.search_name ' . // elevation field is used to guess most important associated doc
                 'FROM documents_i18n mi LEFT JOIN documents m ON mi.id = m.id ' .
                 'WHERE mi.id IN '. 
                 '((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id = ? AND type = ?) '.
                 'UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ? AND type = ?)) '.
                 'ORDER BY mi.id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, array($id, $type, $id, $type))
                        ->fetchAll();
        }
        else
        {
            $query = 'SELECT m.module, m.elevation, mi.id, mi.culture, mi.name, mi.search_name ' .
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


    public static function findWithBestName($ids, $user_prefered_langs, $types = null, $get_associated_ids = false, $get_linked = true, $current_doc_ids = null)
    {
        if (!is_array($ids))
        {
            $ids = array($ids);
        }
        $where_array = array();

        if (!$get_associated_ids)
        {
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
            $where = "a.$where_id IN ( '" . implode($ids, "', '") . "' )";
            
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

                $where2 = array();
                foreach ($current_doc_ids as $current_doc_id)
                {
                    $where2[] = "a.$select_id != ?";
                    $where_array[] = $type;
                }

                $where .= implode(' AND ', $where2 );
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
            $doc_ids = array();
            foreach ($doc_associations as $association)
            {
                $association_norm = array();
                if (in_array($association['main_id'], $ids))
                {
                    $association_norm['parent_id'] = $association['main_id'];
                    $association_norm['id'] = $association['linked_id'];
                }
                else
                {
                    $association_norm['parent_id'] = $association['linked_id'];
                    $association_norm['id'] = $association['main_id'];
                }
                $doc_associations_norm[] = $association_norm;
                $doc_ids[] = $association_norm['id'];
            }
            $doc_ids = " '" . implode($doc_ids, "', '") . "' ";
        }

        $query = 'SELECT m.module, m.elevation, mi.id, mi.culture, mi.name, mi.search_name ' . // elevation field is used to guess most important associated doc
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
                        $out[$key]['parent_id'][] = $association_norm['parent_id'];
                    }
                }
            }
        }
        
        return $out;
    }
    
    public static function addChildWithBestName($parent_docs, $user_prefered_langs, $type = null, $current_doc_id = 0, $sort_field = null)
    {
        if (!count($parent_docs))
        {
            return $parent_docs;
        }
        
        $parent_ids = array();
        foreach ($parent_docs as $doc)
        {
            $parent_ids[] = $doc['id'];
        }
        
        $child_docs = self::findWithBestName($parent_ids, $user_prefered_langs, $type, true, true, $current_doc_id);
        
        return self::addChild($parent_docs, $child_docs, $type, $sort_field);
    }

    public static function addChild($parent_docs, $child_docs, $type = null, $sort_field = null)
    {
        if (!count($parent_docs))
        {
            return $parent_docs;
        }
        
        $parent_ids = array();
        foreach ($parent_docs as $doc)
        {
            $parent_ids[] = $doc['id'];
        }
        
        foreach ($child_docs as $key => $doc)
        {
            if (in_array($doc['id'], $parent_ids))
            {
                unset($child_docs[$key]);
            }
        }
        
        if (!count($child_docs))
        {
            return $parent_docs;
        }
        
        if (empty($sort_field))
        {
            switch ($type)
            {
                case 'ss' :
                    $sort_field = 'elevation';
                    break;
                
                default :
                    $sort_field = 'name';
            }
        }
        
        $child_docs = c2cTools::sortArray($child_docs, $sort_field);
        
        $all_docs = array();
        foreach ($parent_docs as $parent)
        {
            foreach ($child_docs as $child)
            {
                if (in_array($parent['id'], $child['parent_id']))
                {
                    if (($type == 'ss' && $parent['elevation'] < $child['elevation']) || ($type != 'ss' && count($child['parent_id']) > 1))
                    {
                        if (!isset($child['doc_set']))
                        {
                            $all_docs[] = $child;
                            $child['doc_set'] = true;
                        }
                        if (!isset($parent['doc_set']))
                        {
                            $parent['is_child'] = true;
                            $all_docs[] = $parent;
                            $parent['doc_set'] = true;
                        }
                    }
                    else
                    {
                        if (!isset($parent['doc_set']))
                        {
                            $all_docs[] = $parent;
                            $parent['doc_set'] = true;
                        }
                        if (!isset($child['doc_set']))
                        {
                            $child['is_child'] = true;
                            $all_docs[] = $child;
                            $child['doc_set'] = true;
                        }
                    }
                }
            }
            if (!isset($parent['doc_set']))
            {
                $all_docs[] = $parent;
                $parent['doc_set'] = true;
            }
        }
        
        return $all_docs;
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
                             ->select('COUNT(a.linked_id) nb_linked')
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
                             ->select('COUNT(a.main_id) nb_main')
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
            $wherein_clause[] =  '?';
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
        $where = 'a.main_id IN ( ' . "'" . implode($main_ids, "', '") . "'" . ' )';
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }
        
        return Doctrine_Query::create()
                             ->select('a.main_id, a.linked_id')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }
    
    public static function countAllMain($linked_ids, $type = null)
    {
        $where = 'a.linked_id IN ( ' . "'" . implode($linked_ids, "', '") . "'" . ' )';
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }
        
        return Doctrine_Query::create()
                             ->select('a.main_id, a.linked_id')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }
    
    public static function countAll($ids, $types = null, $current_doc_ids = null)
    {
        $where_array = array();
        $where_ids = '( ' . "'" . implode($ids, "', '") . "'" . ' )';
        
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
                $where_array[] = $current_doc_id;
            }

            $where2 = implode(' AND ', $where2 );
            $where3 = implode(' AND ', $where3 );
            $where = "( ( a.linked_id IN $where_ids AND $where2 ) OR ( a.main_id IN $where_ids AND $where3 ) )";
        }
        
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
                             ->select('a.main_id, a.linked_id')
                             ->from('Association a')
                             ->where($where, $where_array)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }
    
}
