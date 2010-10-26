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
        
        if ($has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'pi.search_name', array('pnam', 'name'), 'join_parking_i18n', true, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'palt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.difficulties_height', 'dhei', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.public_transportation_rating', 'tp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', $m . '.public_transportation_types', 'tpty', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Item', 'pi.culture', 'pcult', 'join_parking_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lpc.linked_id', 'ptags', 'join_ptag_id', false, $params_list);
        }
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'pcult');
        if (isset($conditions['all']))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, true);

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
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        if (isset($conditions['join_parking_i18n']))
        {
            $q->leftJoin('m.ParkingI18n pi');
            unset($conditions['join_parking_i18n']);
        }

        if (isset($conditions['join_ptag_id']))
        {
            $q->leftJoin("m.LinkedAssociation lpc");
            unset($conditions['join_ptag_id']);
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
