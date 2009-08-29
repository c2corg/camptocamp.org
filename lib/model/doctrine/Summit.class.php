<?php
/**
 * Model for summits
 * $Id: Summit.class.php 2529 2007-12-19 14:07:18Z alex $
 */

class Summit extends BaseSummit
{
    public static function getAssociatedSummitsData($associated_docs)
    {
        $summits = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_summit')),
                                            'Summit',
                                            array('elevation'));

        return c2cTools::sortArrayByName($summits);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetSummit_type($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMaps_info($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function browse($sort, $criteria)
    {
        $pager = self::createPager('Summit', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
        
        self::joinOnRegions($q);

        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $conditions = $criteria[0];
            
            self::joinOnMultiRegions($q, $conditions);
            
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            // "filter on regions" is the only filter activated for summits:
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
                           array('m.elevation', 'm.summit_type', 'm.lon', 'm.lat'));
    }

    public static function listFromRegion($region_id, $buffer, $table = 'summits', $where = '') 
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
