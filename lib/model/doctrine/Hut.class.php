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

    public static function buildHutListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'h';
            $join = 'join_summit';
            $join_id = 'join_summit_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'huts', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, 'h', 'activities'), 'act', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'hi.search_name', ($is_module ? array('hnam', 'name') : 'hnam'), 'join_hut_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'h', 'activities'), 'hact', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'halt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.shelter_type', 'htyp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.is_staffed', 'hsta', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.staffed_capacity', 'hscap', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.unstaffed_capacity', 'hucap', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.has_unstaffed_matress', 'hmat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.has_unstaffed_blanket', 'hbla', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.has_unstaffed_gas', 'hgas', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.has_unstaffed_wood', 'hwoo', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'hi.culture', 'hcult', 'join_hut_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lhb.main_id', 'hbooks', 'join_hbook_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lhc.linked_id', 'htags', 'join_htag_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lhbc.linked_id', 'hbtags', 'join_hbtag_id', false, $params_list);
        }
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'rcult');
        if (isset($conditions['all']))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, true);

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');
    }
    
    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Hut', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

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
            self::buildPagerConditions($q, $conditions, $criteria[1]);
        }
        elseif (!$all && c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
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

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $criteria);
        }
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
