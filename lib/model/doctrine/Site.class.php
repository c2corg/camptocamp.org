<?php
/**
 * $Id: Site.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Site extends BaseSite
{
    public static function getAssociatedSitesData($associated_docs)
    {
        $sites = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_site')),
                                            'Site',
                                            array('site_types'));

        return c2cTools::sortArrayByName($sites);
    }

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
            $conditions = $criteria[0];
            
            $conditions = self::joinOnMultiRegions($q, $conditions);
            
            // join with parkings tables only if needed 
            if (isset($conditions['join_parking']))
            {
                unset($conditions['join_parking']);
                $q->leftJoin('m.associations l')
                  ->leftJoin('l.Parking p')
                  ->addWhere("l.type = 'pt'");

                if (isset($conditions['join_parking_i18n']))
                {
                    unset($conditions['join_parking_i18n']);
                    $q->leftJoin('p.ParkingI18n pi');
                }
            }

            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
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
                           array('m.routes_quantity', 'm.elevation',
                                 'm.rock_types', 'm.site_types', 'm.lon', 'm.lat'));
    }

    public static function listFromRegion($region_id, $buffer, $table = 'sites', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }

    public static function getAssociatedBooksData($associated_docs)
    {
         $books = Document::fetchAdditionalFieldsFor(
                      array_filter($associated_docs, array('c2cTools', 'is_book')),
                      'Book',
                      array('author'));

        return $books;
    }
}
