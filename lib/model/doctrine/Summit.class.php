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
        self::buildPersoCriteria($conditions, $values, $params_list, 'scult', 'ract');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, true);

        // route criteria
        Route::buildRouteListCriteria(&$conditions, &$values, $params_list, false, 'lr.linked_id');
        self::buildConditionItem($conditions, $values, 'Array', array('r', 'r', 'activities'), 'act', 'join_route', false, $params_list);
 
        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.main_id');

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');
       
        // outing criteria
        Outing::buildOutingListCriteria(&$conditions, &$values, $params_list, false, 'lo.linked_id');
        
        // user criteria
        User::buildUserListCriteria(&$conditions, &$values, $params_list, false, 'lu.main_id');

        // book criteria
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 's');
        self::buildConditionItem($conditions, $values, 'List', 'lsb.main_id', 'books', 'join_sbook_id', false, $params_list);
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 'r');
        
        // image criteria
        Image::buildImageListCriteria(&$conditions, &$values, $params_list, false);

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
    
    public static function buildSummitPagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
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
            $m = 'ls.';
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
            
            $q->leftJoin($first_join . ' ls');
            
            if (isset($conditions['join_summit_id']))
            {
                unset($conditions['join_summit_id']);
                
                return;
            }
            else
            {
                $q->addWhere($m . "type = '$ltype'");
            }
            
            if (isset($conditions['join_summit']))
            {
                $q->leftJoin($m . $linked . 'Summit s');
                unset($conditions['join_summit']);
            }
        }
        
        if (isset($conditions['join_summit_i18n']))
        {
            $q->leftJoin($m . $linked . 'SummitI18n si');
            unset($conditions['join_summit_i18n']);
        }
        
        if (isset($conditions['join_stag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation lsc");
            unset($conditions['join_stag_id']);
        }
        
        if (   isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
            || isset($conditions['join_sbook'])
            || isset($conditions['join_sbook_i18n'])
        )
        {
            $q->leftJoin($main . " lsb");
            
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
                $q->leftJoin('lsb.BookI18n hsi');
                unset($conditions['join_sbook_i18n']);
            }
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        // join with summit / book tables only if needed 
        if (   isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbook'])
            || isset($conditions['join_sbook_i18n'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            Summit::buildSummitPagerConditions($q, $conditions, true);
        }

        // join with routes tables only if needed 
        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_rdoc_id'])
            || isset($conditions['join_rtag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbook'])
            || isset($conditions['join_rbook_i18n'])
            || isset($conditions['join_rbtag_id'])
            || isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbtag_id'])
            || isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
            || isset($conditions['join_outing_id'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
            || isset($conditions['join_user_id'])
            || isset($conditions['join_user'])
            || isset($conditions['join_user_i18n'])
            || isset($conditions['join_user_pd'])
            || isset($conditions['join_utag_id'])
        )
        {
            Route::buildRoutePagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'sr');
            
            // join with huts tables only if needed 
            if (   isset($conditions['join_hut_id'])
                || isset($conditions['join_hut'])
                || isset($conditions['join_hut_i18n'])
                || isset($conditions['join_htag_id'])
                || isset($conditions['join_hbook_id'])
                || isset($conditions['join_hbtag_id'])
            )
            {
                Hut::buildHutPagerConditions($q, $conditions, false, false, 'lr.MainMainAssociation', 'hr');
            }
            
            // join with parkings tables only if needed 
            if (   isset($conditions['join_parking_id'])
                || isset($conditions['join_parking'])
                || isset($conditions['join_parking_i18n'])
                || isset($conditions['join_ptag_id'])
            )
            {
                Parking::buildParkingPagerConditions($q, $conditions, false, false, 'lr.MainMainAssociation', 'pr');
            }
            
            if (   isset($conditions['join_outing_id'])
                || isset($conditions['join_outing'])
                || isset($conditions['join_outing_i18n'])
                || isset($conditions['join_otag_id'])
                || isset($conditions['join_user_id'])
                || isset($conditions['join_user'])
                || isset($conditions['join_user_i18n'])
                || isset($conditions['join_user_pd'])
                || isset($conditions['join_utag_id'])
            )
            {
                Outing::buildOutingPagerConditions($q, $conditions, false, true, 'lr.LinkedAssociation', 'ro');
                
                if (   isset($conditions['join_user_id'])
                    || isset($conditions['join_user'])
                    || isset($conditions['join_user_i18n'])
                    || isset($conditions['join_user_pd'])
                    || isset($conditions['join_utag_id'])
                )
                {
                    User::buildUserPagerConditions($q, $conditions, false, false, 'lo.MainMainAssociation', 'uo');
                }
            }
        }

        // join with image tables only if needed 
        if (   isset($conditions['join_image_id'])
            || isset($conditions['join_image'])
            || isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $conditions, false, 'si');
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
