<?php

class Portal extends BasePortal
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function buildMainPagerConditions(&$q)
    {
        self::joinOnRegions($q);
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::joinOnMultiRegions($q, $joins);
        
        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'fi');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }
    
    public static function getSortField($orderby, $mi = 'mi')
    {
        switch ($orderby)
        {
            case 'wnam': return $mi . '.search_name';
            case 'walt': return 'm.elevation';
            case 'act':  return 'm.activities';
            case 'anam': return 'ai.search_name';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.activities', 'm.lon', 'm.lat');
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
    }

    public static function listFromRegion($region_id, $buffer, $table = 'portals', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }

    public static function convertForumsIds($forums_ids)
    {
        $forums_list = array();
        $forums_ids = str_replace(' ', '', $forums_ids);
        if (empty($forums_ids))
        {
            return $forums_list;
        }
        
        $temp_list = explode('|', $forums_ids);
        foreach ($temp_list as $temp)
        {
            $temp = explode(':', $temp);
            if (count($temp >= 2))
            {
                $forums_list[$temp[0]] = explode(',', $temp[1]);
            }
        }
        return $forums_list;
    }

    public static function getLocalPortals(&$portal_list, $areas)
    {
        $areas_ids = array();
        foreach ($areas as $area)
        {
            $areas_ids[] = $area['id'];
        }
        
        $portal_ids = sfConfig::get('app_portals_id');
        foreach ($portal_ids as $id)
        {
            $def = sfConfig::get('app_portals_' . $id);
            if (isset($def['areas_only']) && count(array_uintersect($areas_ids, $def['areas_only'], 'strcmp')))
            {
                $portal_list[] = $id;
            }
        }
    }
}
