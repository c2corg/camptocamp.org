<?php
/**
 * $Id: Parking.class.php 2529 2007-12-19 14:07:18Z alex $
 */
class Parking extends BaseParking
{
    public static function getAssociatedParkingsData($associated_docs)
    {
        $parkings = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_parking')),
                                            'Parking',
                                            array('lowest_elevation', 'public_transportation_rating', 'public_transportation_types'));

        return $parkings;
    }

    public static function addAssociatedParkings(&$docs, $type) // le & obligatoire ?????? a virer plutot
    {
        Document::addAssociatedDocuments($docs, $type, false,
                                         array('elevation', 'lowest_elevation', 'public_transportation_rating', 'public_transportation_types'),
                                         array('name'));
    }
    
    public static function filterSetElevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetLowest_elevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetPublic_transportation_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetPublic_transportation_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetPublic_transportation_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSnow_clearance_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildParkingListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'p';
            $join = 'join_parking';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'parkings', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'pi.search_name', ($is_module ? array('pnam', 'name') : 'pnam'), 'join_parking_i18n', true, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'palt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.difficulties_height', 'dhei', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.public_transportation_rating', 'tp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', $m . '.public_transportation_types', 'tpty', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'pi.culture', 'pcult', 'join_parking_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lpc.linked_id', 'ptags', 'join_ptag_id', false, $params_list);
        }
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'pcult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, true);

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.linked_id');

        // route criteria
        Route::buildRouteListCriteria(&$conditions, &$values, $params_list, false, 'lr.linked_id');

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, false, 'ls.main_id');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }


    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Parking', self::buildFieldsList(), $sort);
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
            // "filter on regions" is the only filter activated for summits:
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildParkingPagerConditions(&$q, &$conditions, $ltype, $is_linked = false)
    {
        if ($is_module)
        {
            $m = 'm.';
            $linked = '';
            $linked2 = '';
            $main = $m . 'associations';
        }
        else
        {
            $m = 'lp.';
            if ($is_linked)
            {
                $linked = 'Linked';
                $linked2 = '';
                $main = $m . 'MainMainAssociation';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
                $main = $m . 'MainAssociation';
            }
                
            if (isset($conditions['join_parking_id']))
            {
                unset($conditions['join_parking_id']);
            }
            else
            {
                $q->addWhere($m . "type = '$ltype'");
            }
            
            if (isset($conditions['join_parking']))
            {
                $q->leftJoin($m . $linked . 'Parking p');
                unset($conditions['join_parking']);
            }
        }

        if (isset($conditions['join_parking_i18n']))
        {
            $q->leftJoin($m . $linked . 'ParkingI18n pi');
            unset($conditions['join_parking_i18n']);
        }
        
        if (isset($conditions['join_ptag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation lpc");
            unset($conditions['join_ptag_id']);
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        // join with parking tables only if needed 
        if (   isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
        )
        {
            Parking::buildParkingPagerConditions($q, $conditions, true);
        }
        
        // join with huts tables only if needed 
        if (   isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            $q->leftJoin('m.LinkedAssociation lh');
            
            Hut::buildHutPagerConditions($q, $conditions, false, true, 'ph');
        }

        // join with routes tables only if needed 
        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_rdoc_id'])
            || isset($conditions['join_rtag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbtag_id'])
            || isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            $q->leftJoin("m.LinkedAssociation lr");
            
            Route::buildRoutePagerConditions($q, $conditions, false, true, 'pr');

            if (   isset($conditions['join_summit_id'])
                || isset($conditions['join_summit'])
                || isset($conditions['join_summit_i18n'])
                || isset($conditions['join_stag_id'])
                || isset($conditions['join_sbook_id'])
                || isset($conditions['join_sbtag_id'])
            )
            {
                $q->leftJoin("lr.MainAssociation ls");
                
                Summit::buildSummitPagerConditions($q, $conditions, false, false, 'sr');
            }
        }

        if (isset($conditions['join_itag_id']))
        {
            $q->leftJoin("m.LinkedAssociation li")
              ->leftJoin("li.MainMainAssociation lic")
              ->addWhere("li.type = 'pi'");
            unset($conditions['join_itag_id']);
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
                           array('m.elevation', 'm.lowest_elevation', 'm.public_transportation_rating', 'm.public_transportation_types', 'm.snow_clearance_rating', 'm.lon', 'm.lat'));
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
