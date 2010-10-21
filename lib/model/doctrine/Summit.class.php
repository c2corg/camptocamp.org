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

    public static function buildSummitListCriteria(&$conditions, &$values, $params_list, $is_module = false)
    {
        if ($is_module)
        {
            $m = 'm';
            $mid = 'm.id';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 's';
            $mid = 'l2.main_id'
            $join = 'join_summit';
            $join_id = 'join_summit_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'summits', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', 'm.id', 'id', $join_id, false, $params_list);
        }
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'si.search_name', ($is_module ? array('snam', 'name') : 'snam'), 'join_summit_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'salt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.summit_type', 'styp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'si.culture', 'scult', 'join_summit_i18n', false, $params_list);
        }
    }

    public static function buildListCriteria($params_list)
    {   
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildConditionItem($conditions, $values, 'Config', '', 'all', 'all', false, $params_list);
        if (isset($conditions['all']) && $conditions['all'])
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, true);

        // route criteria
        $has_id = self::buildConditionItem($conditions, $values, 'List', 'l.main_id', 'routes', 'join_route_id', false, $params_list);
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, 'String', 'ri.search_name', 'rnam', 'join_route_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 'r.activities', 'act', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.max_elevation', 'malt', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.height_diff_up', 'hdif', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.elevation', 'ralt', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.difficulties_height', 'dhei', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 'r.configuration', 'conf', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Facing', 'r.facing', 'fac', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'r.route_type', 'rtyp', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.equipment_rating', 'prat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.duration', 'time', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.toponeige_technical_rating', 'trat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.toponeige_exposition_rating', 'expo', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.labande_global_rating', 'lrat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.labande_ski_rating', 'srat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.ice_rating', 'irat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.mixed_rating', 'mrat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.rock_free_rating', 'frat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.rock_required_rating', 'rrat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.aid_rating', 'arat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.global_rating', 'grat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.engagement_rating', 'erat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'r.hiking_rating', 'hrat', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 'r.sub_activities', 'sub', 'join_route', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', 'r.is_on_glacier', 'glac', 'join_route', false, $params_list);
        }

        // hut criteria
        $has_id = self::buildConditionItem($conditions, $values, 'List', 'l3.main_id', 'huts', 'join_hut_id', false, $params_list);
        $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', 'l3.main_id', 'hut', 'join_hut_id', false, $params_list);
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, 'String', 'hi.search_name', 'hnam', 'join_hut_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'h.elevation', 'halt', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', 'h.is_staffed', 'hsta', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'h.shelter_type', 'htyp', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'h.staffed_capacity', 'hscap', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'h.unstaffed_capacity', 'hucap', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', 'h.has_unstaffed_matress', 'hmat', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', 'h.has_unstaffed_blanket', 'hbla', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', 'h.has_unstaffed_gas', 'hgas', 'join_hut', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', 'h.has_unstaffed_wood', 'hwoo', 'join_hut', false, $params_list);
        }

        // parking criteria
        $has_id = self::buildConditionItem($conditions, $values, 'List', 'l4.main_id', 'parkings', 'join_parking_id', false, $params_list);
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, 'String', 'pi.search_name', 'pnam', 'join_parking_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 'p.elevation', 'palt', 'join_parking', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'p.public_transportation_rating', 'tp', 'join_parking', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty', 'join_parking', false, $params_list);
        }

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }
    
    public static function browse($sort, $criteria, $format = null)
    {
        $pager = self::createPager('Summit', self::buildFieldsList(), $sort);
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

        if (isset($conditions['join_summit_i18n']))
        {
            $q->leftJoin('m.SummitI18n si');
            unset($conditions['join_summit_i18n']);
        }

        if (isset($conditions['join_route_id']) || 
            isset($conditions['join_route']) || 
            isset($conditions['join_route_i18n']) || 
            isset($conditions['join_hut_id']) ||
            isset($conditions['join_hut']) ||
            isset($conditions['join_hut_i18n']) ||
            isset($conditions['join_parking_id']) ||
            isset($conditions['join_parking']) ||
            isset($conditions['join_parking_i18n']))
        {
            $q->leftJoin("m.LinkedAssociation l");
            
            if (isset($conditions['join_route_id']))
            {
                unset($conditions['join_route_id']);
            }
            else
            {
                $q->addWhere("l.type = 'sr'");
            }
            
            if (isset($conditions['join_route']))
            {
                $q->leftJoin('l.LinkedRoute r');
                unset($conditions['join_route']);
            }

            if (isset($conditions['join_route_i18n']))
            {
                $q->leftJoin('l.LinkedRouteI18n ri');
                unset($conditions['join_route_i18n']);
            }
        }
        
        if (isset($conditions['join_hut_id']) || isset($conditions['join_hut']) || isset($conditions['join_hut_i18n']))
        {
            $q->leftJoin("l.MainMainAssociation l3");
            
            if (isset($conditions['join_hut_id']))
            {
                unset($conditions['join_hut_id']);
            }
            else
            {
                $q->addWhere("l3.type = 'hr'");
            }
            
            if (isset($conditions['join_hut']))
            {
                $q->leftJoin('l3.Hut h');
                unset($conditions['join_hut']);
            }
            
            if (isset($conditions['join_hut_i18n']))
            {
                $q->leftJoin('l3.HutI18n hi');
                unset($conditions['join_hut_i18n']);
            }
        }
        
        if (isset($conditions['join_parking_id']) || isset($conditions['join_parking']) || isset($conditions['join_parking_i18n']))
        {
            $q->leftJoin("l.MainMainAssociation l4");
            
            if (isset($conditions['join_parking_id']))
            {
                unset($conditions['join_parking_id']);
            }
            else
            {
                $q->addWhere("l4.type = 'pr'");
            }
            
            if (isset($conditions['join_parking']))
            {
                $q->leftJoin('l4.Parking p');
                unset($conditions['join_parking']);
            }

            if (isset($conditions['join_parking_i18n']))
            {
                $q->leftJoin('l4.ParkingI18n pi');
                unset($conditions['join_parking_i18n']);
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
    
    public static function getSubSummits($id, $elevation)
    {
        $query = 'SELECT m.id, m.elevation '
               . 'FROM summits m '
               . 'WHERE m.id IN '
               . '((SELECT a.main_id FROM app_documents_associations a WHERE a.linked_id = ? AND type = ?) '
               . 'UNION (SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ? AND type = ?)) '
               . 'AND m.elevation < ? '
               . 'ORDER BY m.id ASC';

        $results = sfDoctrine::connection()
                    ->standaloneQuery($query, array($id, 'ss', $id, 'ss', $elevation))
                    ->fetchAll();
        return $results;
    }
}
