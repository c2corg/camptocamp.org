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

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Map', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        $conditions = array();
        $all = false;
        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            if (isset($conditions['all']))
            {
                $all = $conditions['all'];
                unset($conditions['all']);
            }
        }
        
        if (!$all && !empty($conditions))
        {
            $conditions = self::joinOnMultiRegions($q, $conditions);
            
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
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

    protected static function buildFieldsList()
    {   
        return array_merge(parent::buildFieldsList(), 
                           parent::buildGeoFieldsList(),
                           array('m.code', 'm.scale', 'm.editor'));
    }
}
