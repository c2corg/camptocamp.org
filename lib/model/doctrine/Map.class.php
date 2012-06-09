<?php
/**
 * Model for maps
 * $Id: Map.class.php 2535 2007-12-19 18:26:27Z alex $
 */

class Map extends BaseMap
{
    public static function getAssociatedMapsData($maps)
    {
        if (!count($maps)) 
        {
            return array();
        }
        
        $editor_list = sfConfig::get('app_maps_editors');
        foreach ($maps as $key => $map)
        {
            $name = $editor_list[$map['editor']] . ' ' . $map['code'] . ' ' . $map['name'];
            $maps[$key]['name'] = $name;
        }
        
        return c2cTools::sortArrayByName($maps);
    }

    public static function filterSetEditor($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetScale($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetCode($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function buildListCriteria($params_list)
    {
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'maps');
        
        // orderby criteria
        $orderby = c2cTools::getRequestParameter('orderby');
        if (!empty($orderby))
        {
            $orderby = array('orderby' => $orderby);
            
            self::buildConditionItem($conditions, $values, $joins_order, $orderby, 'Order', array('mnam'), 'orderby', array('map_i18n', 'join_map'));
        }
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = $conditions;
            $criteria[1] = $values;
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'm');

        $m = 'm';
        $m2 = 'm';
        $mid = 'm.id';
        $midi18n = $mid;
        $join = null;
        $join_id = null;
        $join_idi18n = null;
        $join_i18n = null;
        $is_module = true;
        
        $nb_id = 0;
        $nb_name = 0;
        
        $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'maps'), $join_id);
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'marnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'mi.search_name'), ($is_module ? array('mnam', 'name') : 'mnam'), array($join_idi18n, $join_i18n), 'Map');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Istring', $m . '.code', 'code', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.scale', 'scal', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.editor', 'edit', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'mi.culture', 'mcult', $join_i18n);
        }
        
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        
        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }

    public static function buildMainPagerConditions(&$q, $criteria)
    {
        self::joinOnRegions($q);
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);

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
            case 'mnam': return $mi . '.search_name';
            case 'code': return 'm.code';
            case 'scal': return 'm.scale';
            case 'edit': return 'm.editor';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.code', 'm.scale', 'm.editor');
            $data_fields_list = array_merge($data_fields_list,
                                            parent::buildGeoFieldsList());
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
    }
}
