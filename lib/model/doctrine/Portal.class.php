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

    public static function buildListCriteria($params_list)
    {   
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'wcult');
        
        // orderby criteria
        $orderby = c2cTools::getRequestParameter('orderby');
        if (!empty($orderby))
        {
            $orderby = array('orderby' => $orderby);
            
            self::buildConditionItem($conditions, $values, $joins_order, $orderby, 'Order', array('wnam'), 'orderby', array('portal_i18n', 'join_portal'));
        }
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = $conditions;
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'w');
        
        // portal criteria
        $m = 'm';
        $m2 = 'p';
        $midi18n = $mid;
        $join = null;
        $join_id = null;
        $join_idi18n = null;
        $join_i18n = 'portal_i18n';
        
        $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'portals'), $join_id);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'warnd', $join);
            
            $has_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'wi.search_name'), ($is_module ? array('wnam', 'name') : 'wnam'), array($join_idi18n, $join_i18n), 'Portal');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'walt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'activities'), 'act', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'wi.culture', 'wcult', $join_i18n);
        }
        
        if ($is_module && ($has_id || $has_name))
        {
            $joins['has_id'] = true;
        }
            
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] += $conditions;
        $criteria[1] += $values;
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
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
