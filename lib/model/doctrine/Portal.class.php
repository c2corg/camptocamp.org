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

    public static function buildMainPagerConditions(&$q, $criteria)
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'portals');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('wnam'), array('portal_i18n', 'join_portal'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'w');
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // portal criteria
        $m = 'm';
        $m2 = 'p';
        $mid = 'm.id';
        $midi18n = $mid;
        $join = null;
        $join_id = null;
        $join_idi18n = null;
        $join_i18n = 'portal_i18n';
        $is_module = true;
        
        $nb_id = 0;
        $nb_name = 0;
        
        $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'portals'), $join_id);
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'warnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'wi.search_name'), ($is_module ? array('wnam', 'name') : 'wnam'), array($join_idi18n, $join_i18n), 'Portal');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'walt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'activities'), 'act', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'wi.culture', 'wcult', $join_i18n);
        }
        
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'wi');
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
            case 'id':   return 'm.id';
            case 'wnam': return $mi . '.search_name';
            case 'walt': return 'm.elevation';
            case 'act':  return 'm.activities';
            case 'range': return 'gr.linked_id';
            case 'admin': return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley': return 'gv.linked_id';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.activities', 'm.lon', 'm.lat');
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
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

    public static function getRelatedPortals(&$portal_list, $areas, $routes, $activities = array())
    {
        if (count($routes))
        {
            $use_route_activities = (empty($activities));
            $has_ice_route = false;
            $has_steep_route = false;
            $has_ta_route = false;
            $has_alpibig_route = false;
            $has_raid_route = false;
            
            $route_activities = $activities;
            foreach ($routes as $route)
            {
                if ($use_route_activities)
                {
                    $route_activities = $route['activities'];
                    if (!is_array($route_activities))
                    {
                        $route_activities = Document::convertStringToArray($route_activities);
                    }
                }
                
                if (   array_intersect(array(2, 5), $route_activities)
                    && !$route['ice_rating'] instanceof Doctrine_Null && $route['ice_rating'] > 0)
                {
                    $has_ice_route = true;
                }
                
                if (   in_array(1, $route_activities)
                    && !$route['toponeige_technical_rating'] instanceof Doctrine_Null && $route['toponeige_technical_rating'] >= 10)
                {
                    $has_steep_route = true;
                }
                
                if (   in_array(4, $route_activities)
                    && !$route['global_rating'] instanceof Doctrine_Null && $route['global_rating'] >= 4
                    && !$route['equipment_rating'] instanceof Doctrine_Null && $route['equipment_rating'] >= 8)
                {
                    $has_ta_route = true;
                }
                
                if (   array_intersect(array(2, 3), $route_activities)
                    && !$route['global_rating'] instanceof Doctrine_Null && $route['global_rating'] >= 18
                    && !$route['engagement_rating'] instanceof Doctrine_Null && $route['engagement_rating'] >= 4
                    && !$route['difficulties_height'] instanceof Doctrine_Null && $route['difficulties_height'] >= 300)
                {
                    $has_alpibig_route = true;
                }
                
                if (!$route['duration'] instanceof Doctrine_Null && $route['duration'] >= 6)
                {
                    $has_raid_route = true;
                }
            }
            
            if ($has_ice_route)
            {
                $portal_list[] = 'ice';
            }
            if ($has_steep_route)
            {
                $portal_list[] = 'steep';
            }
            if ($has_ta_route)
            {
                $portal_list[] = 'ta';
            }
            if ($has_alpibig_route)
            {
                $portal_list[] = 'alpibig';
            }
            if ($has_raid_route)
            {
                $portal_list[] = 'raid';
            }
        }
        
        Portal::getLocalPortals($portal_list, $areas);
    }
}
