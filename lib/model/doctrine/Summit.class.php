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

    public static function buildSummitListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 's';
            $join = 'join_summit';
            $join_id = 'join_summit_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'summits', $join_id, false, $params_list);
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
            self::buildConditionItem($conditions, $values, 'String', 'si.search_name', ($is_module ? array('snam', 'name') : 'snam'), 'join_summit_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'salt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.summit_type', 'styp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'si.culture', 'scult', 'join_summit_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lsb.main_id', 'sbooks', 'join_sbook_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lsc.linked_id', 'stags', 'join_stag_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lsbc.linked_id', 'sbtags', 'join_sbtag_id', false, $params_list);
        }
    }

    public static function buildListCriteria($params_list)
    {   
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'scult');
        if (isset($conditions['all']))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, true);

        // route criteria
        Route::buildRouteListCriteria(&$conditions, &$values, $params_list, false, 'lr.linked_id');

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.main_id');

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');

        // book criteria
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 's');
        self::buildConditionItem($conditions, $values, 'List', 'lsb.main_id', 'books', 'join_sbook_id', false, $params_list);
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 'r');

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

        if (isset($conditions['join_stag_id']))
        {
            $q->leftJoin("m.LinkedAssociation lsc");
            unset($conditions['join_stag_id']);
        }

        // join with routes tables only if needed 
        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbook'])
            || isset($conditions['join_rbook_i18n'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_ptag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbtag_id'])
        )
        {
            $q->leftJoin("m.LinkedAssociation lr");
            
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
                $q->leftJoin('lr.LinkedRoute r');
                unset($conditions['join_route']);
            }

            if (isset($conditions['join_route_i18n']))
            {
                $q->leftJoin('lr.LinkedRouteI18n ri');
                unset($conditions['join_route_i18n']);
            }
            
            if (isset($conditions['join_rtag_id']))
            {
                $q->leftJoin("lr.LinkedLinkedAssociation lrc");
                unset($conditions['join_rtag_id']);
            }

            if (isset($conditions['join_rdtag_id']))
            {
                $q->leftJoin("lr.MainAssociation lrd")
                  ->leftJoin("lrd.LinkedLinkedAssociation lrdc")
                  ->addWhere("lrd.type IN ('sr', 'hr', 'pr', 'br')");
                unset($conditions['join_rdtag_id']);
            }
            
            if (   isset($conditions['join_rbook_id'])
                || isset($conditions['join_rbook'])
                || isset($conditions['join_rbook_i18n'])
                || isset($conditions['join_rbtag_id'])
            )
            {
                $q->leftJoin("lr.MainAssociation lrb");
                
                if (isset($conditions['join_rbook_id']))
                {
                    unset($conditions['join_rbook_id']);
                }
                else
                {
                    $q->addWhere("lrb.type = 'br'");
                }
                if (isset($conditions['join_rbtag_id']))
                {
                    $q->leftJoin("lrb.LinkedLinkedAssociation lrbc");
                    unset($conditions['join_rbtag_id']);
                }
                
                if (isset($conditions['join_rbook']))
                {
                    $q->leftJoin('lrb.Book rb');
                    unset($conditions['join_rbook']);
                }

                if (isset($conditions['join_rbook_i18n']))
                {
                    $q->leftJoin('lrb.BookI18n rbi');
                    unset($conditions['join_rbook_i18n']);
                }
            }
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
            $q->leftJoin("lr.MainMainAssociation lh");
            
            if (isset($conditions['join_hut_id']))
            {
                unset($conditions['join_hut_id']);
            }
            else
            {
                $q->addWhere("lh.type = 'hr'");
            }
            
            if (isset($conditions['join_hut']))
            {
                $q->leftJoin('lh.Hut h');
                unset($conditions['join_hut']);
            }
            
            if (isset($conditions['join_hut_i18n']))
            {
                $q->leftJoin('lh.HutI18n hi');
                unset($conditions['join_hut_i18n']);
            }
            
            if (isset($conditions['join_htag_id']))
            {
                $q->leftJoin("lh.LinkedLinkedAssociation lhc");
                unset($conditions['join_htag_id']);
            }
            
            if (   isset($conditions['join_hbook_id'])
                || isset($conditions['join_hbtag_id'])
            )
            {
                $q->leftJoin("lh.MainAssociation lhb");
                
                if (isset($conditions['join_hbook_id']))
                {
                    unset($conditions['join_hbook_id']);
                }
                else
                {
                    $q->addWhere("lhb.type = 'bh'");
                }
                if (isset($conditions['join_hbtag_id']))
                {
                    $q->leftJoin("lhb.LinkedLinkedAssociation lhbc");
                    unset($conditions['join_hbtag_id']);
                }
            }
        }
        
        // join with parkings tables only if needed 
        if (   isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
        )
        {
            $q->leftJoin("lr.MainMainAssociation lp");
            
            if (isset($conditions['join_parking_id']))
            {
                unset($conditions['join_parking_id']);
            }
            else
            {
                $q->addWhere("lp.type = 'pr'");
            }
            
            if (isset($conditions['join_parking']))
            {
                $q->leftJoin('lp.Parking p');
                unset($conditions['join_parking']);
            }

            if (isset($conditions['join_parking_i18n']))
            {
                $q->leftJoin('lp.ParkingI18n pi');
                unset($conditions['join_parking_i18n']);
            }
            
            if (isset($conditions['join_ptag_id']))
            {
                $q->leftJoin("lp.LinkedLinkedAssociation lpc");
                unset($conditions['join_ptag_id']);
            }
        }
        
        // join with books tables only if needed 
        if (   isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbook'])
            || isset($conditions['join_sbook_i18n'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            $q->leftJoin('m.associations lsb');
            
            if (isset($conditions['join_sbook_id']))
            {
                unset($conditions['join_sbook_id']);
            }
            else
            {
                $q->addWhere("lsb.type = 'bs'");
            }
            if (isset($conditions['join_sbtag_id']))
            {
                $q->leftJoin("lsb.LinkedLinkedAssociation lsbc");
                unset($conditions['join_sbtag_id']);
            }
            
            if (isset($conditions['join_sbook']))
            {
                $q->leftJoin('lsb.Book sb');
                unset($conditions['join_sbook']);
            }

            if (isset($conditions['join_sbook_i18n']))
            {
                $q->leftJoin('lsb.BookI18n sbi');
                unset($conditions['join_sbook_i18n']);
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
