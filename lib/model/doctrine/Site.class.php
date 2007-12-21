<?php
/**
 * $Id: Site.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Site extends BaseSite
{
    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetV4_type($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetClimbing_styles($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetClimbing_styles($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetRock_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetRock_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSite_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetSite_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetFacings($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetFacings($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetBest_periods($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetBest_periods($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetRoutes_quantity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMin_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMean_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMin_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMean_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetEquipment_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetChildren_proof($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetRain_proof($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function browse($sort, $criteria)
    {   
        $pager = self::createPager('Site', self::buildFieldsList(), $sort);
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

        return $pager;
    }   

    protected static function buildFieldsList()
    {   
        return array_merge(parent::buildFieldsList(), 
                           parent::buildGeoFieldsList(),
                           array('m.routes_quantity', 'm.elevation',
                                 'm.rock_types', 'm.site_types'));
    }
}
