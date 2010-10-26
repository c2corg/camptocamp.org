<?php
/**
 * Model for outings
 * $Id: Outing.class.php 2542 2007-12-21 19:07:08Z alex $
 */

class Outing extends BaseOuting
{
    public static function filterSetActivities($value)
    {
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetMax_elevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetHeight_diff_up($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetHeight_diff_down($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetOuting_length($value)
    {
        return self::returnNullIfEmpty(round($value * 1000));
    }

    public static function filterGetOuting_length($value)
    {
        return self::returnNullIfEmpty(round($value / 1000, 1)); 
    }

    public static function filterSetHut_status($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetFrequentation_status($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetConditions_status($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetAccess_status($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetAccess_elevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUp_snow_elevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetDown_snow_elevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetTrack_status($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetV4_id($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetV4_app($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetDate($value)
    {
        $year  = $value['year'];
        $month = (strlen($value['month']) == 2) ? $value['month'] : ('0' . $value['month']);
        $day   = (strlen($value['day']) == 2) ? $value['day'] : ('0' . $value['day']);
        
        return "$year-$month-$day";
    }

    /**
     * Retrieves a list of outings ordered by effective outing date (more recent first).
     */
    public static function listLatest($max_items, $langs, $ranges, $activities, $params = array())
    {
        $q = Doctrine_Query::create();
        $q->select('m.id, n.culture, n.name, m.date, m.activities, m.max_elevation, g0.linked_id, a.area_type, ai.name, ai.culture')
          ->from('Outing m')
          ->leftJoin('m.OutingI18n n')
          ->leftJoin('m.geoassociations g0')
          ->leftJoin('g0.AreaI18n ai')
          ->leftJoin('ai.Area a')
          ->addWhere('m.redirects_to IS NULL')
          ->orderBy('m.date DESC, m.id DESC')
          ->limit($max_items);


        self::filterOnActivities($q, $activities, 'm', 'o');
        self::filterOnLanguages($q, $langs, 'n');
        self::filterOnRegions($q, $ranges, 'g2');
        
        if (!empty($params))
        {
            $criteria = self::buildListCriteria($params);
            if (!empty($criteria))
            {
                self::buildPagerConditions($q, $criteria[0], $criteria[1]);
            }
        }

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    public static function buildOutingListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'o';
            $join = 'join_outing';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'outings', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        
        if ($has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, 'o', 'activities'), 'act', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Date', 'date', 'date', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'oi.search_name', ($is_module ? array('onam', 'name') : 'onam'), 'join_outing_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'o', 'activities'), 'oact', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.max_elevation', 'oalt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.height_diff_up', 'odif', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.outing_length', 'olen', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Date', 'date', $m . '.odate', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.outing_with_public_transportation', 'owtp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.partial_trip', 'ptri', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.frequentation_status', 'ofreq', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.conditions_status', 'ocond', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.glacier_status', 'oglac', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.track_status', 'otrack', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.access_status', 'opark', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.lift_status', 'olift', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.hut_status', 'ohut', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'oi.culture', 'ocult', 'join_outing_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'loc.linked_id', 'otags', 'join_otag_id', false, $params_list);
        }
    }

    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for enabling/disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'ocult');
        if (isset($conditions['all']))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);
        
        // outing criteria
        Outing::buildOutingListCriteria(&$conditions, &$values, $params_list, true);

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, false, 'ls.main_id');
        self::buildConditionItem($conditions, $values, 'Order', array('lat', 'lon'), 'orderby', 'join_summit', false, $params_list);

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.main_id');

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');

        // route criteria
        Route::buildRouteListCriteria(&$conditions, &$values, $params_list, false, 'lr.main_id');
        self::buildConditionItem($conditions, $values, 'Order', sfConfig::get('mod_outings_sort_route_criteria'), 'orderby', 'join_route', false, $params_list);

        // site criteria
        $has_id = self::buildConditionItem($conditions, $values, 'List', 'lt.main_id', 'sites', 'join_site_id', false, $params_list);
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, 'String', 'ti.search_name', 'tnam', 'join_site_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 't.elevation', 'talt', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 't.site_types', 'ttyp', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 't.climbing_styles', 'tcsty', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 't.equipment_rating', 'prat', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 't.routes_quantity', 'rqua', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 't.mean_height', 'mhei', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', 't.mean_rating', 'mrat', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 't.facings', 'tfac', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', 't.rock_types', 'trock', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 't.children_proof', 'chil', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 't.rain_proof', 'rain', 'join_site', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'ltc.linked_id', 'ttags', 'join_ttag_id', false, $params_list);
        }

        // book criteria
        self::buildConditionItem($conditions, $values, 'List', 'ltb.main_id', 'tbooks', 'join_tbook_id', false, $params_list);
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 'r');
        self::buildConditionItem($conditions, $values, 'List', 'lrbc.linked_id', 'btags', 'join_rbtag_id', false, $params_list);
        self::buildConditionItem($conditions, $values, 'List', 'ltbc.linked_id', 'tbtags', 'join_tbtag_id', false, $params_list);
        
        // user criteria
        $has_id =    self::buildConditionItem($conditions, $values, 'Multilist', array('u', 'main_id'), 'user', 'join_user_id', false, $params_list)
                  || self::buildConditionItem($conditions, $values, 'Multilist', array('u', 'main_id'), 'users', 'join_user_id', false, $params_list);
        if (!$has_id)
        {
            self::buildConditionItem($conditions, $values, 'String', 'ui.search_name', 'unam', 'join_user_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'u.category', 'ucat', 'join_user', false, $params_list);
        }

        // image criteria
        $has_id = self::buildConditionItem($conditions, $values, 'List', 'lic.main_id', 'itags', 'join_itag_id', false, $params_list);

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public static function browse($sort, $criteria, $format = null)
    {
        $field_list = self::buildOutingFieldsList($format, $sort);
        $pager = self::createPager('Outing', $field_list, $sort);
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
            self::filterOnLanguages($q);
            self::filterOnActivities($q);
            self::filterOnRegions($q);
            
            if ($format == 'cond')
            {
                $default_max_age = sfConfig::get('mod_outings_recent_conditions_limit', '15D');
                $q->addWhere("age(date) < interval '$default_max_age'");
            }
        }
        elseif ($format == 'cond')
        {
            $default_max_age = sfConfig::get('mod_outings_recent_conditions_limit', '15D');
            $q->addWhere("age(date) < interval '$default_max_age'");
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
        
        if (isset($conditions['join_outing_i18n']))
        {
            $q->leftJoin('m.OutingI18n oi');
            unset($conditions['join_outing_i18n']);
        }

        if (isset($conditions['join_otag_id']))
        {
            $q->leftJoin("m.LinkedAssociation loc");
            unset($conditions['join_otag_id']);
        }

        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_oversummit'])
            || isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbook'])
            || isset($conditions['join_rbook_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_ptag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbtag_id'])
        )
        {
            $q->leftJoin("m.associations lr");
            
            if (isset($conditions['join_route_id']))
            {
                unset($conditions['join_route_id']);
            }
            else
            {
                $q->addWhere("lr.type = 'ro'");
            }
            
            if (isset($conditions['join_route']))
            {
                $q->leftJoin('lr.Route r');
                unset($conditions['join_route']);
            }

            if (isset($conditions['join_route_i18n']))
            {
                $q->leftJoin('lr.RouteI18n ri');
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
            }
        }

        if (   isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_oversummit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            $q->leftJoin("lr.MainAssociation ls");
            
            if (isset($conditions['join_summit_id']))
            {
                unset($conditions['join_summit_id']);
            }
            else
            {
                $q->addWhere("ls.type = 'sr'");
            }
            
            if (isset($conditions['join_summit']) || isset($conditions['join_oversummit']))
            {
                $q->leftJoin('ls.Summit s');
                if (isset($conditions['join_summit']))
                {
                    unset($conditions['join_summit']);
                }
                
                if (isset($conditions['join_oversummit']))
                {
                    $q->leftJoin('s.associations lss')
                      ->leftJoin('lss.Summit s1')
                      ->addWhere("lss.type = 'ss'");
                    unset($conditions['join_oversummit']);
                }
            }
            
            if (isset($conditions['join_summit_i18n']))
            {
                $q->leftJoin('ls.SummitI18n si');
                unset($conditions['join_summit_i18n']);
            }
            
            if (isset($conditions['join_stag_id']))
            {
                $q->leftJoin("ls.LinkedLinkedAssociation lsc");
                unset($conditions['join_stag_id']);
            }
            
            if (   isset($conditions['join_sbook_id'])
                || isset($conditions['join_sbtag_id'])
            )
            {
                $q->leftJoin("ls.MainAssociation lsb");
                
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
            }
        }
        
        if (   isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            $q->leftJoin("lr.MainAssociation lh");
            
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
        
        if (   isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
        )
        {
            $q->leftJoin("lr.MainAssociation lp");
            
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

        if (   isset($conditions['join_site_id'])
            || isset($conditions['join_site'])
            || isset($conditions['join_site_i18n'])
            || isset($conditions['join_tbook_id'])
            || isset($conditions['join_ttag_id'])
            || isset($conditions['join_tbtag_id'])
        )
        {
            $q->leftJoin("m.associations lt");
            
            if (isset($conditions['join_site_id']))
            {
                unset($conditions['join_site_id']);
            }
            else
            {
                $q->addWhere("lt.type = 'to'");
            }
            
            if (isset($conditions['join_site']))
            {
                $q->leftJoin('lt.Site t');
                unset($conditions['join_site']);
            }

            if (isset($conditions['join_site_i18n']))
            {
                $q->leftJoin('lt.SiteI18n ti');
                unset($conditions['join_site_i18n']);
            }
            
            if (isset($conditions['join_ttag_id']))
            {
                $q->leftJoin("lt.LinkedLinkedAssociation ltc");
                unset($conditions['join_ttag_id']);
            }
            
            if (   isset($conditions['join_tbook_id'])
                || isset($conditions['join_tbtag_id'])
            )
            {
                $q->leftJoin("lt.MainAssociation ltb");
                
                if (isset($conditions['join_tbook_id']))
                {
                    unset($conditions['join_tbook_id']);
                }
                else
                {
                    $q->addWhere("ltb.type = 'bt'");
                }
                if (isset($conditions['join_tbtag_id']))
                {
                    $q->leftJoin("ltb.LinkedLinkedAssociation ltbc");
                    unset($conditions['join_tbtag_id']);
                }
            }
        }
        
        if (   isset($conditions['join_rbook'])
            || isset($conditions['join_rbook_i18n'])
        )
        {
            if (isset($conditions['join_rbook']))
            {
                $q->leftJoin('lrb.Book b');
                unset($conditions['join_rbook']);
            }

            if (isset($conditions['join_rbook_i18n']))
            {
                $q->leftJoin('lrb.BookI18n bi');
                unset($conditions['join_rbook_i18n']);
            }
        }

        $conditions = self::joinOnMulti($q, $conditions, 'join_user_id', 'm.associations u', 4);

        if (   isset($conditions['join_user'])
            || isset($conditions['join_user_i18n'])
        )
        {
            $q->leftJoin("m.associations lu")
              ->addWhere("lu.type = 'uo'");
            
            if (isset($conditions['join_user']))
            {
                $q->leftJoin('lu.User u');
                unset($conditions['join_user']);
            }

            if (isset($conditions['join_user_i18n']))
            {
                $q->leftJoin('lu.UserI18n ui');
                unset($conditions['join_user_i18n']);
            }
        }

        if (isset($conditions['join_itag_id']))
        {
            $q->leftJoin("m.LinkedAssociation li")
              ->leftJoin("li.MainMainAssociation lic")
              ->addWhere("li.type = 'oi'");
            unset($conditions['join_itag_id']);
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $criteria);
        }
    }

    protected static function buildOutingFieldsList($format = null, $sort)
    {
        $outings_fields_list = array('m.activities', 'm.date',
                                     'm.height_diff_up', 'm.max_elevation',
                                     'v.version', 'hm.user_id', 'u.topo_name', 
                                     'm.geom_wkt', 'm.conditions_status', 'm.frequentation_status');
        
        $conditions_fields_list = (in_array($format, array('cond', 'full'))) ?
                                  array('m.up_snow_elevation', 'm.down_snow_elevation', 'm.access_elevation',
                                        'mi.conditions', 'mi.conditions_levels', 'mi.weather', 'mi.timing')
                                  : array();
        
        $full_fields_list = ($format == 'full') ?
                            array('m.partial_trip', 'm.min_elevation', 'm.height_diff_down', 'm.outing_length', 'm.outing_with_public_transportation',
                                  'm.access_status', 'm.glacier_status', 'm.track_status', 'm.hut_status', 'm.lift_status',
                                  'mi.participants', 'mi.timing', 'mi.access_comments', 'mi.hut_comments', 'mi.description')
                            : array();
        
        $extra_fields = array();
        if (isset($sort['orderby_param']))
        {
            $orderby = $sort['orderby_param'];
            
            if (in_array($orderby, sfConfig::get('mod_outings_sort_route_criteria')))
            {
                switch ($orderby)
                {
                    case 'fac':  $extra_fields[] = 'r.facing'; break;
                    case 'ralt': $extra_fields[] = 'r.elevation'; break;
                    case 'dhei': $extra_fields[] = 'r.difficulties_height'; break;
                    case 'grat': $extra_fields[] = 'r.global_rating'; break;
                    case 'erat': $extra_fields[] = 'r.engagement_rating'; break;
                    case 'prat': $extra_fields[] = 'r.equipment_rating'; break;
                    case 'frat': $extra_fields[] = 'r.rock_free_rating'; break;
                    case 'arat': $extra_fields[] = 'r.aid_rating'; break;
                    case 'irat': $extra_fields[] = 'r.ice_rating'; break;
                    case 'mrat': $extra_fields[] = 'r.mixed_rating'; break;
                    case 'trat': $extra_fields[] = 'r.toponeige_technical_rating'; break;
                    case 'expo': $extra_fields[] = 'r.toponeige_exposition_rating'; break;
                    case 'lrat': $extra_fields[] = 'r.labande_global_rating'; break;
                    case 'srat': $extra_fields[] = 'r.labande_ski_rating'; break;
                    case 'hrat': $extra_fields[] = 'r.hiking_rating'; break;
                    default: break;
                }
            }
            elseif (in_array($orderby, array('lat', 'lon')))
            {
                $extra_fields = array('s.lat', 's.lon');
            }
        }
        
        return array_merge(parent::buildFieldsList(),
                           parent::buildGeoFieldsList(),
                           $outings_fields_list,
                           $conditions_fields_list,
                           $full_fields_list,
                           $extra_fields);
    }

    public static function retrieveConditions($days)
    {
        $pager = new sfDoctrinePager('Outing', 10);
        $q = $pager->getQuery();
        $q->select('m.date, m.activities, m.conditions_status, m.up_snow_elevation, m.down_snow_elevation, ' .
                   'm.access_elevation, mi.name, mi.search_name, mi.conditions, mi.conditions_levels, mi.weather, mi.culture' .
                   'g0.type, g0.linked_id, ai.name, m.max_elevation')
          ->from('Outing m')
          ->leftJoin('m.OutingI18n mi')
          ->where("m.redirects_to IS NULL AND age(date) < interval '$days days'")
          ->orderBy('m.date DESC, m.id DESC');

        self::joinOnRegions($q);
        
        // applying user filters
        if (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            self::filterOnLanguages($q);
            self::filterOnActivities($q);
            self::filterOnRegions($q);
        }

        return $pager;
    }

    /**
     * Retrieves an array of array(document_id, culture) of recently CREATED outings in a given mean time (in seconds).
     */
    public static function listRecentInTime($mean_time)
    {
        $sql = 'SELECT d.document_id, d.culture, d.documents_versions_id, a.search_name  FROM app_documents_versions d ' .
               'LEFT JOIN outings_i18n a ON (d.document_id = a.id AND d.culture = a.culture) ' .
               "WHERE d.version = 1 AND (AGE(NOW(), d.created_at) < ( $mean_time * interval '1 second')) " .
               'ORDER BY d.documents_versions_id DESC';

        $outings = array();
        foreach (sfDoctrine::connection()->standaloneQuery($sql)->fetchAll() as $outing)
        {
            $id = $outing['document_id'];
            $outings[$id] = $outing; // if outing is available in several cultures, oldest one is the one
        }

        if (!empty($outings))
        {
            // remove outings having culture version already transmitted (older than $mean_time)
            $ids = implode(',', array_keys($outings));
            $sql = "select distinct document_id from app_documents_versions where document_id in ($ids) and AGE(NOW(), created_at) > ( $mean_time * interval '1 second' )";
            foreach (sfDoctrine::connection()->standaloneQuery($sql)->fetchAll() as $result)
            {
                $id = $result['document_id'];
                unset($outings[$id]);
            }
        }

        return $outings;
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::joinOnI18n($q, $model);
        self::filterOnLanguages($q);
        self::filterOnActivities($q);
        self::filterOnRegions($q);
    }

    public static function fetchAdditionalFields($objects, $images_count = false)
    {
        if (!count($objects)) 
        {   
            return array();
        }
    
        $ids = array();
        $q = array();

        // build ids list
        foreach ($objects as $object)
        {
            $ids[] = $object['id'];
            $q[] = '?';
        }

        // db request fetching array with all requested fields
        $results = Doctrine_Query::create()
                          ->select('m.activities, m.date, m.geom_wkt, v.version, hm.user_id, u.topo_name')
                          ->from('Outing m')
                          ->leftJoin('m.versions v')
                          ->leftJoin('v.history_metadata hm')
                          ->leftJoin('hm.user_private_data u')
                          ->where('m.id IN ( '. implode(', ', $q) .' )', $ids)
                          ->addWhere('v.version = 1')
                          ->orderBy('m.date DESC')
                          ->execute(array(), Doctrine::FETCH_ARRAY);
        
        $out = array();
        // merge array 'results' into array '$objects' on the basis of same 'id' key
        foreach ($objects as $object)
        {
            $id = $object['id'];
            foreach ($results as $result)
            {
                if ($result['id'] == $id)
                {
                    $out[] = array_merge($object, $result);
                }
            }
        }

        if ($images_count)
        {
            $image_links = Association::countAllLinked($ids, 'oi');
            $image_counts = array();
            foreach ($image_links as $image_link)
            {
                $main_id = $image_link['main_id'];
                if (isset($image_counts[$main_id]))
                {
                    $image_counts[$main_id]++;
                }
                else
                {
                    $image_counts[$main_id] = 1;
                }
            }
            foreach ($out as &$outing)
            {
                if (isset($image_counts[$outing['id']]))
                {
                    $outing['nb_images'] = $image_counts[$outing['id']];
                }
            }
            
        }

        return $out;
    }

    public static function getAssociatedRoutesData($outings)
    {
        if (count($outings) == 0)
        {
            return $outings;
        }
        
        $outing_ids = array();
        foreach ($outings as $key => $outing)
        {
            $outing_ids[] = $outing['id'];
            $outings[$outing['id']] = $outing;
            unset($outings[$key]);
        }
        
        $ro_associations = Association::countAllMain($outing_ids, 'ro');

        if (count($ro_associations) == 0) return $outings;

        $route_ids = array();
        foreach ($ro_associations as $ro)
        {
            $route_id = $ro['main_id'];
            $outing_id = $ro['linked_id'];
            
            $route_ids[] = $route_id;
            $outings[$outing_id]['linked_routes'] = (isset($outings[$outing_id]['linked_routes'])) ?
                                                    array_merge($outings[$outing_id]['linked_routes'], array($route_id)) :
                                                    array($route_id);
        }
        $route_ids = array_unique($route_ids);

        $outing_fields = array ('max_elevation',
                                'height_diff_up');
        $route_ski_fields = array ('toponeige_technical_rating',
                                   'toponeige_exposition_rating',
                                   'labande_ski_rating',
                                   'labande_global_rating');
        $route_climbing_fields = array ('global_rating',
                                        'engagement_rating',
                                        'rock_free_rating',
                                        'rock_required_rating',
                                        'ice_rating',
                                        'mixed_rating',
                                        'aid_rating',
                                        'equipment_rating');
        $route_hiking_fields = array ('hiking_rating');
        $route_fields = array_merge($route_ski_fields, $route_climbing_fields, $route_hiking_fields);
        $routes =  Document::findIn('Route', $route_ids);

        foreach ($outings as &$outing)
        {
            foreach ($outing_fields as $field)
            {
                if (!$outing[$field] instanceof Doctrine_Null)
                {
                    $outing[$field.'_set'] = true;
                }
            }

            $route_activities = array();
            foreach ($routes as $route)
            {
                if (!isset($outing['linked_routes'])) continue;
                if (!in_array($route['id'], $outing['linked_routes'])) continue;

                $route_activities = array_merge($route_activities, Document::convertStringToArray($route['activities']));

                // if height_diff_up or max_elevation not in outing, get values from routes
                foreach ($outing_fields as $field)
                {
                    if (!isset($outing[$field.'_set']) &&
                        (($outing[$field] instanceof Doctrine_Null) || ($route[$field] > $outing[$field])))
                    {
                        $outing[$field] = $route[$field];
                    }
                }
                foreach ($route_fields as $field)
                {
                    $field_value = $route[$field];
                    if (!isset($outing[$field]) ||
                        (isset($field_value) && $field_value > $outing[$field]))
                    {
                        $outing[$field] = $field_value;
                    }
                }
            }

            $activities_to_show = array_intersect(Document::convertStringToArray($outing['activities']), $route_activities);
            if (count($activities_to_show) == 0) $activities_to_show = $route_activities;

            if (!count(array_intersect($activities_to_show, array(1)))) foreach($route_ski_fields as $field) $outing[$field] = null;
            if (!count(array_intersect($activities_to_show, array(2, 3, 4, 5)))) foreach($route_climbing_fields as $field) $outing[$field] = null;
            if (!count(array_intersect($activities_to_show, array(6)))) foreach($route_hiking_fields as $field) $outing[$field] = null;
        }

        return $outings;
    }
}
