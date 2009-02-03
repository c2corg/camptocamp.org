<?php
/**
 * $Id$
 */
class GeoAssociation extends BaseGeoAssociation
{
// FIXME: this is a pure duplicate of class Association 
// => find a solution with inheritance to prevent code duplication
// or do some cleaning in here, because not everything will be useful.

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
                             ->from('GeoAssociation a')
                             ->where($where, $where_array)
                             ->execute()
                             ->getFirst();
    }
    
    public static function findAllAssociations($id, $types = null, $where_is_id = 'both')
    {
        switch ($where_is_id)
        {
            case 'both' :
                $where = '( a.main_id = ? OR a.linked_id = ? )';
                $where_array = array($id, $id);
                break;
            case 'main' :
                $where = '( a.main_id = ? )';
                $where_array = array($id);
                break;
            case 'linked' :
                $where = '( a.linked_id = ? )';
                $where_array = array($id);
                break;
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
                             ->from('GeoAssociation a')
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
                 '((SELECT a.main_id FROM app_geo_associations a WHERE a.linked_id = ? AND type = ?) '.
                 'UNION (SELECT a.linked_id FROM app_geo_associations a WHERE a.main_id = ? AND type = ?)) '.
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
                 '((SELECT a.main_id FROM app_geo_associations a WHERE a.linked_id = ?) '.
                 'UNION (SELECT a.linked_id FROM app_geo_associations a WHERE a.main_id = ?)) '.
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
        return ($rank == null) ? 20 : $rank; // if lang not in prefs, return a 'high' rank
    }

    public static function findAllWithBestName($id, $user_prefered_langs, $type = null)
    {

        if ($type)
        {
            $query = 'SELECT m.module, m.elevation, mi.id, mi.culture, mi.name, mi.search_name ' . // elevation field is used to guess most important associated doc
                 'FROM documents_i18n mi LEFT JOIN documents m ON mi.id = m.id ' .
                 'WHERE mi.id IN '. 
                 '((SELECT a.main_id FROM app_geo_associations a WHERE a.linked_id = ? AND type = ?) '.
                 'UNION (SELECT a.linked_id FROM app_geo_associations a WHERE a.main_id = ? AND type = ?)) '.
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
                 '((SELECT a.main_id FROM app_geo_associations a WHERE a.linked_id = ?) '.
                 'UNION (SELECT a.linked_id FROM app_geo_associations a WHERE a.main_id = ?)) '.
                 'ORDER BY mi.id ASC';

            $results = sfDoctrine::connection()
                        ->standaloneQuery($query, array($id, $id)) 
                        ->fetchAll();
        }
        
        
        // build the actual results based on the user's prefered language
        // try to select best name only if there is choice, ie if there are at least two lines with same id and different cultures.
        
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
                             ->from('GeoAssociation a')
                             ->where($where, $where_array)
                             ->execute()
                             ->getFirst()->nb_linked;  
    }

    public static function countMains($linked_id, $type = null)
    {
        $where = 'a.linked_id = ?';
        $where_array = array($linked_id);
        
        
        if ($type)
        {
            $where .= ' AND a.type = ?';
            $where_array[] = $type;
        }       
        
        return Doctrine_Query::create()
                             ->select('COUNT(a.main_id) nb_main')
                             ->from('GeoAssociation a')
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
                             ->delete('GeoAssociation')
                             ->from('GeoAssociation a')
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
                             ->delete('GeoAssociation')
                             ->from('GeoAssociation a')
                             ->where($where, $where_array)
                             ->execute(); 
        return $rows;
    }
    
    public function doSaveWithValues($main_id, $linked_id, $type)
    {
        try
        {
            $this->main_id = $main_id;
            $this->linked_id = $linked_id;
            $this->type = $type;
            $this->save();
            
            return true;
        }
        catch (exception $e)
        {
            c2cTools::log("GeoAssociation::doSaveWithValues($main_id, $linked_id, $type) failed");
            return false;
        }
    }
    
    // replicates geo associations from $geoassociations to document $id
    // returns the number of geo associations created
    public static function replicateGeoAssociations($geoassociations, $id, $delete_old = true, $replicate_maps_associations = true)
    {
        $nb_created = 0;
        if ($delete_old)
        {
            self::deleteAllFor($id, array('dr', 'dc', 'dm', 'dd'));
        }
        foreach ($geoassociations as $ea)
        {
            if (!(($ea->get('type') == 'dm') && (!$replicate_maps_associations)))
            {
                $a = new GeoAssociation();
                if ($a->doSaveWithValues($id, $ea->get('linked_id'), $ea->get('type')))
                {
                    $nb_created++;
                }
            }
        }
        return $nb_created;
    }
}
