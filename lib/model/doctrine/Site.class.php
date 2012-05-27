<?php
/**
 * $Id: Site.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Site extends BaseSite
{
    public static function getAssociatedSitesData($associated_docs)
    {
        $sites = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_site')),
                                            'Site',
                                            array('site_types'));

        return c2cTools::sortArrayByName($sites);
    }

    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetV4_type($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetClimbing_styles($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetClimbing_styles($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetRock_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetRock_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSite_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetSite_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetFacings($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetFacings($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetBest_periods($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetBest_periods($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetRoutes_quantity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMin_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMean_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMin_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMean_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetEquipment_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetChildren_proof($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetRain_proof($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildSiteListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 's';
            $midi18n = $mid;
            $join = null;
            $join_id = 'site_id';
            $join_idi18n = null;
            $join_i18n = 'site_i18n';
        }
        else
        {
            $m = 's';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'site';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'sites'), null);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', $mid, 'subsites', 'subsite_id');
        }
        else
        {
            $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'sites', $join_id);
        }
        
        $has_name = false;
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'tarnd', $join);
            
            $has_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'ti.search_name'), ($is_module ? array('tnam', 'name') : 'tnam'), array($join_idi18n, $join_i18n), 'Site');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'talt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'site_types'), 'ttyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'climbing_styles'), 'tcsty', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.equipment_rating', 'tprat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.routes_quantity', 'rqua', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.mean_height', 'mhei', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.mean_rating', 'mrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'facings'), 'tfac', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'rock_types'), 'trock', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.children_proof', 'chil', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.rain_proof', 'rain', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ti.culture', 'tcult', $join_i18n);
            
            // book criteria
            $has_name = Book::buildBookListCriteria($criteria, $params_list, false, 't', 'linked_id');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            
            // article criteria
            $has_name = Article::buildArticleListCriteria($criteria, $params_list, false, 't', 'linked_id');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            
            if (   isset($criteria[2]['join_tbook'])
                || isset($criteria[2]['join_tarticle'])
            )
            {
                $joins['join_site'] = true;
                if (!$is_module)
                {
                    $joins['post_site'] = true;
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
            $joins['join_site'] = true;
        }
        if ($is_module && ($has_id || $has_name))
        {
            $joins['has_id'] = true;
        }
        $criteria[2] = $criteria[2] + $joins;
        
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'tcult');
        
        // orderby criteria
        $orderby = c2cTools::getRequestParameter('orderby');
        if (!empty($orderby))
        {
            $orderby = array('orderby' => $orderby);
            
            self::buildConditionItem($conditions, $values, $joins_order, $orderby, 'Order', 'tnam', 'orderby', array('site_i18n', 'join_site'));
        }
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 's');

        // site criteria
        $has_name = Site::buildSiteListCriteria($criteria, $params_list, true);
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

        // hut criteria
        $has_name = Hut::buildHutListCriteria($criteria, $params_list, false, 'main_id');
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
       
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // user criteria
        $has_name = User::buildUserListCriteria($criteria, $params_list, false, 'main_id');
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
        $pager = self::createPager('Site', self::buildFieldsList(), $sort);
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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('sites'))
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildSitePagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'site';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            
            if (isset($joins['site_id_has']))
            {
                $q->leftJoin($m . '.associations lt')
                  ->addWhere("lt.type = 'tt'");
            }
            
            if (isset($joins['subsite_id_has']))
            {
                $q->leftJoin($m . 'LinkedAssociation ltt')
                  ->addWhere("ltt.type = 'tt'");
            }
        }
        else
        {
            $m = 'lt';
            if ($is_linked)
            {
                $linked = 'Linked';
                $main_join = $m . 'MainMainAssociation';
                $linked_join = $m . '.LinkedAssociation';
            }
            else
            {
                $linked = '';
                $main_join = $m . 'MainAssociation';
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
                    $q->leftJoin($m . '.' . $linked . 'Site t');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'SiteI18n ti');
        }
        if (isset($joins['join_tbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 't', false, $main_join, 'bt');
        }
        
        
        if (isset($joins['join_tarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 't', false, $linked_join, 'tc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::joinOnMultiRegions($q, $joins);

        // join with site / book / article tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, true);
        }

        // join with summit tables only if needed 
        if (isset($joins['join_summit']))
        {
            Summit::buildSummitPagerConditions($q, $joins, false, false, 'm.associations', 'st');
        }
        
        // join with hut tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, false, false, 'm.associations', 'ht');
        }
        
        // join with parking tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, 'm.associations', 'pt');
        }
        
        // join with outing tables only if needed 
        if (isset($joins['join_user']))
        {
            $joins['join_outing'] = true;
            $joins['post_outing'] = true;
        }

        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'to');
            
            if (isset($joins['join_user']))
            {
                User::buildUserPagerConditions($q, $joins, false, false, 'lo.MainMainAssociation', 'uo');
            }
        }

        // join with image tables only if needed 
        if (   isset($joins['join_image_id'])
            || isset($joins['join_image'])
            || isset($joins['join_image_i18n'])
            || isset($joins['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'pi');
        }
        
        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $criteria);
        }
    }

    protected static function buildFieldsList($mi = 'mi')
    {   
        return array_merge(parent::buildFieldsList($mi), 
                           parent::buildGeoFieldsList(),
                           array('m.routes_quantity', 'm.elevation',
                                 'm.rock_types', 'm.site_types', 'm.lon', 'm.lat'));
    }

    public static function listFromRegion($region_id, $buffer, $table = 'sites', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }

    public static function getAssociatedBooksData($associated_docs)
    {
         $books = Document::fetchAdditionalFieldsFor(
                      array_filter($associated_docs, array('c2cTools', 'is_book')),
                      'Book',
                      array('author'));

        return $books;
    }
}
