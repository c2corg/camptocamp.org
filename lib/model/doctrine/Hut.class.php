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

    public static function buildHutListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'h';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'article_i18n';
        }
        else
        {
            $m = 'h';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'hut';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'huts'), $join_id);
        }
        else
        {
            $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'huts', $join_id);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'h', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'harnd', $join);
            
            $has_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'hi.search_name'), ($is_module ? array('hnam', 'name') : 'hnam'), array($join_idi18n, $join_i18n), 'Hut');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'h', 'activities'), 'hact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'halt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.shelter_type', 'htyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.is_staffed', 'hsta', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.staffed_capacity', 'hscap', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.unstaffed_capacity', 'hucap', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.has_unstaffed_matress', 'hmat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.has_unstaffed_blanket', 'hbla', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.has_unstaffed_gas', 'hgas', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.has_unstaffed_wood', 'hwoo', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'hi.culture', 'hcult', $join_i18n);
            
            // book criteria
            $has_name = Book::buildBookListCriteria($criteria, $params_list, false, 'h', 'linked_id');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            
            // article criteria
            $has_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'h', 'linked_id');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            
            if (   isset($criteria[2]['join_hbook'])
                || isset($criteria[2]['join_harticle'])
            )
            {
                $joins['join_hut'] = true;
                if (!$is_module)
                {
                    $joins['post_hut'] = true;
                }
            }
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = $criteria[0] + $conditions;
            $criteria[1] = $criteria[1] + $values;
        }
        if (!empty($joins))
        {
            $joins['join_hut'] = true;
            $criteria[2] = $criteria[2] + $joins;
        }
        
        return null;
    }
    
    public static function buildListCriteria($params_list)
    {
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'hcult');
        
        // orderby criteria
        $orderby = c2cTools::getRequestParameter('orderby');
        if (!empty($orderby))
        {
            $orderby = array('orderby' => $orderby);
            
            self::buildConditionItem($conditions, $values, $joins_order, $orderby, 'Order', 'hnam', 'orderby', array('hut_i18n', 'join_hut'));
        }
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'h');

        // hut / book / article criteria
        $has_name = Hut::buildHutListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // summit criteria
        $has_name = Summit::buildSummitListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // site criteria
        $has_name = Site::buildSiteListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
       
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] = $criteria[0] + $conditions;
        $criteria[1] = $criteria[1] + $values;
        $criteria[2] = $criteria[2] + $joins;
        $criteria[3] = $criteria[3] + $joins_order;
        return $criteria;
    }
    
    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Hut', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

        $all = false;
        if (isset($criteria[2]['all']))
        {
            $all = $criteria[2]['all'];
        }
        
        if (!$all && !empty($criteria[0]))
        {
            self::buildPagerConditions($q, $criteria);
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
    
    public static function browse2($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Hut', 'm.id', $sort, false);
        $q = $pager->getQuery();
    
        $all = false;
        if (isset($criteria[2]['all']))
        {
            $all = $criteria[2]['all'];
        }
        
        if (!$all && !empty($criteria[0]))
        {
            self::buildPagerConditions($q, $criteria);
        }
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('huts'))
        {
            self::joinOnRegions($q);
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildHutPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'hut';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $linked2 = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
        }
        else
        {
            $m = 'lh';
            if ($is_linked)
            {
                $linked = 'Linked';
                $main_join = $m . '.MainMainAssociation';
                $linked_join = $m . '.LinkedAssociation';
            }
            else
            {
                $linked = '';
                $main_join = $m . '.MainAssociation';
                $linked_join = $m . '.LinkedLinkedAssociation';
            }
            $join_id = $join . '_id';
            
            if (isset($joins[$join_id]))
            {
                self::joinOnMulti($q, $joins, $join_id, $first_join . " $m", 5);
                
                if (isset($joins[$join_id . '_has']))
                {
                    $q->addWhere($m . "1.type = '$ltype'");
                }
            }
            
            if (   isset($joins['post_' . $join])
                || isset($joins[$join])
                || isset($joins[$join . '_idi18n'])
                || isset($joins[$join . '_i18n'])
            )
            {
                $q->leftJoin($first_join . " $m");
                
                if (   isset($joins['post_' . $join])
                    || isset($joins[$join])
                    || isset($joins[$join . '_i18n'])
                )
                {
                    if ($ltype)
                    {
                        $q->addWhere($m . ".type = '$ltype'");
                    }
                }
                
                if (isset($joins[$join]))
                {
                    $q->leftJoin($m . '.' . $linked . 'Hut h');
                }
            }
        }
        
        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'HutI18n hi');
        }
        
        if (isset($joins['join_hbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 'h', false, $main_join, 'bh');
        }
        
        if (isset($joins['join_harticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'h', false, $linked_join, 'hc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::joinOnMultiRegions($q, $joins);
        
        // join with hut / book / article tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, true);
        }

        // join with parking tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, 'm.associations', 'ph');
        }

        // join with route tables only if needed 
        if (   isset($joins['join_summit'])
            || isset($joins['join_outing'])
        )
        {
            $joins['join_route'] = true;
            $joins['post_route'] = true;
        }
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'hr');

            if (isset($joins['join_summit']))
            {
                Summit::buildSummitPagerConditions($q, $joins, false, false, 'lr.MainAssociation', 'sr');
            }
            
            if (isset($joins['join_outing']))
            {
                Outing::buildOutingPagerConditions($q, $joins, false, true, 'lr.LinkedAssociation', 'ro');
            }
        }

        // join with site tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, false, false, 'm.LinkedAssociation', 'ht');
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'hi');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
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
