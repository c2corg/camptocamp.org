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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'mcult');
        
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
        
        $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'maps'), $join_id);
        
        $has_name = false;
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'marnd', $join);
            
            $has_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'mi.search_name'), ($is_module ? array('mnam', 'name') : 'mnam'), array($join_idi18n, $join_i18n), 'Map');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Istring', $m . '.code', 'code', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.scale', 'scal', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.editor', 'edit', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'mi.culture', 'mcult', $join_i18n);
        }
        
        if ($has_id || $has_name)
        {
            $joins['has_id'] = true;
        }
        
        $criteria[0] += $conditions;
        $criteria[1] += $values;
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Map', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        $all = false;
        if (isset($criteria[2]['all']))
        {
            $all = $criteria[2]['all'];
        }
        
        if (!$all && !empty($criteria[0]))
        {
            self::joinOnMultiRegions($q, $criteria[2]);
            
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
        }
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('maps'))
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   

    protected static function buildFieldsList($mi = 'mi')
    {   
        return array_merge(parent::buildFieldsList($mi), 
                           parent::buildGeoFieldsList(),
                           array('m.code', 'm.scale', 'm.editor'));
    }
}
