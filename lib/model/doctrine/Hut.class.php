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
            $m2 = 'h';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'h';
            $m2 = $m;
            $join = 'join_hut';
            $join_id = 'join_hut_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'Id', $mid, 'huts', $join_id, false, $params_list);
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
            self::buildConditionItem($conditions, $values, 'Around', $m2 . '.geom', 'harnd', $join, false, $params_list);
            $has_name = self::buildConditionItem($conditions, $values, 'String', array('hi.search_name', $mid), ($is_module ? array('hnam', 'name') : 'hnam'), 'join_hut', false, $params_list, 'Hut');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
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
            self::buildConditionItem($conditions, $values, 'Id', 'lhb.main_id', 'hbooks', 'join_hbook_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Id', 'lhc.linked_id', 'htags', 'join_htag_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Id', 'lhbc.linked_id', 'hbtags', 'join_hbtag_id', false, $params_list);
        }
        
        return null;
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'hcult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list, 'h');

        // hut criteria
        $has_name = Hut::buildHutListCriteria($conditions, $values, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($conditions, $values, $params_list, false, 'lr.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($conditions, $values, $params_list, false, 'lp.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // summit criteria
        $has_name = Summit::buildSummitListCriteria($conditions, $values, $params_list, false, 'ls.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // site criteria
        $has_name = Site::buildSiteListCriteria($conditions, $values, $params_list, false, 'lt.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
       
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($conditions, $values, $params_list, false, 'lo.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // book criteria
        $has_name = Book::buildBookListCriteria($conditions, $values, $params_list, false, 'h', 'lhb.main_id');
        self::buildConditionItem($conditions, $values, 'Id', 'lhb.main_id', 'books', 'join_hbook_id', false, $params_list);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($conditions, $values, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('huts'))
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildHutPagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
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
            $m = 'lh.';
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
            
            $q->leftJoin($first_join . ' lh');
            
            if (!isset($conditions['join_hut_id']) || isset($conditions['join_hut_id_has']))
            {
                $q->addWhere($m . "type = '$ltype'");
                if (isset($conditions['join_hut_id_has']))
                {
                    unset($conditions['join_hut_id_has']);
                }
            }
            if (isset($conditions['join_hut_id']))
            {
                unset($conditions['join_hut_id']);
            }
            
            if (isset($conditions['join_hut']))
            {
                $q->leftJoin($m . $linked . 'Hut h');
                unset($conditions['join_hut']);
            }
        }
        
        if (isset($conditions['join_hut_i18n']))
        {
            $q->leftJoin($m . $linked . 'HutI18n hi');
            unset($conditions['join_hut_i18n']);
        }
        
        if (isset($conditions['join_htag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation lhc");
            unset($conditions['join_htag_id']);
            
            if (isset($conditions['join_htag_id_has']))
            {
                $q->addWhere("lhc.type = 'hc'");
                unset($conditions['join_htag_id_has']);
            }
        }
        
        if (   isset($conditions['join_hbook_id'])
            || isset($conditions['join_hbtag_id'])
            || isset($conditions['join_hbook'])
            || isset($conditions['join_hbook_i18n'])
        )
        {
            Book::buildBookPagerConditions($q, $conditions, false, 'h', false, $main, 'bh');
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        // join with hut / book tables only if needed 
        if (   isset($conditions['join_hut_i18n'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_hbook'])
            || isset($conditions['join_hbook_i18n'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            Hut::buildHutPagerConditions($q, $conditions, true);
        }

        // join with parkings tables only if needed 
        if (   isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
        )
        {
            Parking::buildParkingPagerConditions($q, $conditions, false, false, 'm.associations', 'ph');
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
            || isset($conditions['join_outing_id'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
        )
        {
            Route::buildRoutePagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'hr');

            if (   isset($conditions['join_summit_id'])
                || isset($conditions['join_summit'])
                || isset($conditions['join_summit_i18n'])
                || isset($conditions['join_stag_id'])
                || isset($conditions['join_sbook_id'])
                || isset($conditions['join_sbtag_id'])
            )
            {
                Summit::buildSummitPagerConditions($q, $conditions, false, false, 'lr.MainAssociation', 'sr');
            }
            
            if (   isset($conditions['join_outing_id'])
                || isset($conditions['join_outing'])
                || isset($conditions['join_outing_i18n'])
                || isset($conditions['join_otag_id'])
            )
            {
                Outing::buildOutingPagerConditions($q, $conditions, false, true, 'lr.LinkedAssociation', 'ro');
            }
        }

        // join with site tables only if needed 
        if (   isset($conditions['join_site_id'])
            || isset($conditions['join_site'])
            || isset($conditions['join_site_i18n'])
            || isset($conditions['join_tbook_id'])
            || isset($conditions['join_ttag_id'])
            || isset($conditions['join_tbtag_id'])
        )
        {
            Site::buildSitePagerConditions($q, $conditions, false, false, 'm.LinkedAssociation', 'ht');
        }

        // join with article tables only if needed 
        if (   isset($conditions['join_article_id'])
            || isset($conditions['join_article'])
            || isset($conditions['join_article_i18n'])
        )
        {
            Article::buildArticlePagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'hc');
        }

        // join with image tables only if needed 
        if (   isset($conditions['join_image_id'])
            || isset($conditions['join_image'])
            || isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $conditions, false, 'hi');
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

    // return the 'ghost' summit of the hut (False if there is no)
    // the ghost summit is used to have routes that have a hut as goal
    public function getGhostSummit()
    {
        return Doctrine_Query::create()
               ->from('Summit s')
               ->leftJoin('s.LinkedAssociation a')
               ->addWhere("a.type = 'sh'")
               ->addWhere('a.linked_id = ?', array($this->getID()))
               ->execute()
               ->getFirst();
    }
}
