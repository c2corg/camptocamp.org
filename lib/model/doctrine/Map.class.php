<?php
/**
 * Model for maps
 * $Id: Map.class.php 2535 2007-12-19 18:26:27Z alex $
 */

class Map extends BaseMap
{
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

    public static function browse($sort, $criteria)
    {   
        $pager = self::createPager('Map', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
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
