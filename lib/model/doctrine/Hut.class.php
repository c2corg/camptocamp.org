<?php
/**
 * $Id: Hut.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Hut extends BaseHut
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetStaffed_period($value)
    {   
        return self::returnNullIfEmpty($value);
    }   

    public static function filterSetStaffed_capacity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUnstaffed_capacity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetShelter_type($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetPhone($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUrl($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function browse($sort, $criteria)
    {   
        $pager = self::createPager('Hut', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
        }
        elseif (c2cPersonalization::isMainFilterSwitchOn())
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
                           array('m.elevation', 'm.shelter_type', 'm.activities'));
    }
}
