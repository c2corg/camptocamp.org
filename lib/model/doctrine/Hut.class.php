<?php
/**
 * $Id: Hut.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Hut extends BaseHut
{
    public static function getAssociatedHutsData($associated_docs)
    {
        $huts = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_hut')),
                                            'Hut',
                                            array('elevation'));

        return c2cTools::sortArrayByName($huts);
    }

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

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Hut', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $conditions = $criteria[0];
            $associations = array();
            
            $conditions = self::joinOnMultiRegions($q, $conditions);

            // join with parkings tables only if needed 
            if (isset($conditions['join_parking_id']) || isset($conditions['join_parking']))
            {
                $q->leftJoin('m.associations l');
                if (isset($conditions['join_parking_id']))
                {
                    unset($conditions['join_parking_id']);
                }
                
                if (isset($conditions['join_parking']))
                {
                    $q->leftJoin('l.Parking p')
                      ->addWhere("l.type = 'ph'");
                    unset($conditions['join_parking']);

                    if (isset($conditions['join_parking_i18n']))
                    {
                        $q->leftJoin('p.ParkingI18n pi');
                        unset($conditions['join_parking_i18n']);
                    }
                }
            }

            if (!empty($associations))
            {
                $q->addWhere("l.type IN ('" . implode("', '", $associations) . "')");
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
                           array('m.elevation', 'm.shelter_type', 'm.activities', 'm.lon', 'm.lat', 'm.staffed_capacity', 'm.unstaffed_capacity', 'm.phone', 'm.url'));
    }

    public static function listFromRegion($region_id, $buffer, $table = 'huts', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
