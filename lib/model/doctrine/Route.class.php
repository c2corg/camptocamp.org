<?php
/**
 * Model for routes
 * $Id: Route.class.php 2529 2007-12-19 14:07:18Z alex $
 */

class Route extends BaseRoute
{
    public static function findHighestAssociatedSummitCoords($routes)
    {
        if (!count($routes))
        {
            return array();
        }
    
        // list all route ids
        $list = array();
        foreach ($routes as $r)
        {
            $list[] = $r['id'];
        }
        
        // request on associations table, type='sr', linked IN (route ids), join all associated summits (main), with elevation
        $results = Doctrine_Query::create()
                    ->select('l.main_id, s.id, s.elevation, s.lon, s.lat') 
                    ->from('Association l') // to display associated summit (name + elevation) with route.
                    ->leftJoin('l.Summit s') // to get the summits elevation in order to determine which one is highest
                    ->where("l.type = 'sr' AND l.linked_id IN ( ". implode(', ', $list) .' )')
                    ->execute(array(), Doctrine::FETCH_ARRAY);

        // foreach items, keep only the highest one ($item[Summit][0][elevation] )
        $elevation = 0;
        $lon = 0;
        $lat = 0;
        foreach ($results as $key => $result)
        {
            if ($result['Summit'][0]['elevation'] > $elevation)
            {
                $lon = $result['Summit'][0]['lon'];
                $lat = $result['Summit'][0]['lat'];
                $elevation = $result['Summit'][0]['elevation'];
            }
        }
        return array('lon' => $lon, 'lat' => $lat, 'ele' => $elevation);
    }

    public static function addBestSummitName($routes, $separator = ': ', $summit_name = null)
    {
        if (!count($routes))
        {
            return array();
        }
    
        // list all route ids
        $list = array();
        foreach ($routes as $r)
        {
            $list[] = $r['id'];
        }
        
        // request on associations table, type='sr', linked IN (route ids), join all associated summits (main), with elevation
        $results = Doctrine_Query::create()
                    ->select('l.main_id, s.id, s.elevation, si.name') 
                    ->from('Association l') // to display associated summit (name + elevation) with route.
                    ->leftJoin('l.Summit s') // to get the summits elevation in order to determine which one is highest
                    ->leftJoin('s.SummitI18n si') // to get the best name
                    ->where("l.type = 'sr' AND l.linked_id IN ( ". implode(', ', $list) .' )')
                    ->execute(array(), Doctrine::FETCH_ARRAY);
        
        // foreach items having the same [linked_id] (=route), keep only the highest one ($item[Summit][0][elevation] )
        $_a = array();
        foreach ($results as $key => $result)
        {
            if (!array_key_exists($result['linked_id'], $_a))
            {
                $_a[$result['linked_id']] = array('elevation' => $result['Summit'][0]['elevation'], 'item_nb' => $key);
            }
            else
            {
                // there already exists an associated summit with this route => find highest
                if ($result['Summit'][0]['elevation'] > $_a[$result['linked_id']]['elevation'])
                {
                    $_a[$result['linked_id']] = array('elevation' => $result['Summit'][0]['elevation'], 'item_nb' => $key);
                }
            }
        }

        // remove unnecessary results (those with weak summits)
        $_b = array();
        foreach ($_a as $key => $keep)
        {
           $_b[$key] =  $results[$keep['item_nb']];
        }
        
        // extract best name of summits
        foreach ($_b as $key => $_bb)
        {
            $_b[$key]['Summit'] = Language::getTheBest($_bb['Summit'], 'Summit', array(), 'id', false);
        }

        // merge highest summit name into array of associated routes names.
        // if $summit_name is given, do not add summit
        // if there is no associated summit, do nothing
        foreach ($routes as $key => $route)
        {
            if (!isset($route['name'])) $route['name'] = '';
            if ((!empty($summit_name) && ($summit_name == $_b[$route['id']]['Summit'][0]['id']))
                || (empty($_b[$route['id']]['Summit'][0]['SummitI18n'][0]['name'])))
            {
                $routes[$key]['add_summit_name'] = false;
                $routes[$key]['name'] = $route['name'];
            }
            else
            {
                $routes[$key]['add_summit_name'] = true;
                $routes[$key]['name'] = $_b[$route['id']]['Summit'][0]['SummitI18n'][0]['name'] . $separator . $route['name'];
            }

            $routes[$key]['full_name'] = $_b[$route['id']]['Summit'][0]['SummitI18n'][0]['name'] . '-' . $route['name'];
        }
        return $routes;
    }

    public static function getAssociatedRoutesData($associated_docs, $separator = ': ', $summit_name = null)
    {
        $routes =  Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_route')), 
                                            'Route', 
                                            array('activities', 'global_rating', 'height_diff_up', 'difficulties_height',
                                                  'facing', 'engagement_rating', 'toponeige_technical_rating', 
                                                  'toponeige_exposition_rating', 'labande_ski_rating',
                                                  'labande_global_rating', 'rock_free_rating', 'rock_required_rating', 'geom_wkt',
                                                  'ice_rating', 'mixed_rating', 'aid_rating', 'hiking_rating',
                                                  'max_elevation', 'equipment_rating', 'duration'));

        // TODO: do additional fields fetching + summit name fetching at once (one query instead of 2)
        $routes = self::addBestSummitName($routes, $separator, $summit_name);

       if (empty($routes))
           return $routes;

        // sort alphabetically by name
        if (empty($summit_name))
        {
            foreach ($routes as $key => $row)
            {
                $name[$key] = mb_strtolower($row['name'], "UTF-8");
            }
            array_multisort($name, SORT_STRING, $routes);
        }
        else
        {
           foreach ($routes as $key => $row)
            {
                $add_summit_name[$key] = $row['add_summit_name'];
                $name[$key] = mb_strtolower($row['name'], "UTF-8");
            }
            array_multisort($add_summit_name, $name, SORT_STRING, $routes);
        }

        return $routes;
    }

    public static function filterSetActivities($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetActivities($value)
    {
        return self::convertStringToArray($value);
    }
    
    public static function filterSetConfiguration($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetConfiguration($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSub_activities($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetSub_activities($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetHeight_diff_up($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetHeight_diff_down($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMin_elevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_elevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetDifficulties_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetRoute_length($value)
    {
        return self::returnNullIfEmpty(round($value * 1000));
    }

    public static function filterGetRoute_length($value)
    {
        return self::returnNullIfEmpty(round($value / 1000, 1)); 
    }

    public static function filterSetFacing($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetDuration($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetRoute_type($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetGlobal_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetEngagement_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetEquipment_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetToponeige_technical_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetToponeige_exposition_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetLabande_ski_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetLabande_global_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetIce_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMixed_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetRock_free_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetRock_required_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetAid_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetHiking_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildRouteListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'r';
            $join = 'join_route';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'routes', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        
        if ($has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, 'r', 'activities'), 'act', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'ri.search_name', array('rnam', 'name'), 'join_route_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Mstring', array('mi.search_name', 'si.search_name'), 'srnam', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'r', 'activities'), 'ract', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.max_elevation', 'malt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.height_diff_up', 'hdif', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'ralt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.difficulties_height', 'dhei', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'r', 'configuration'), 'conf', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Facing', $m . '.facing', 'fac', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.route_type', 'rtyp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.equipment_rating', 'prat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.duration', 'time', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.toponeige_technical_rating', 'trat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.toponeige_exposition_rating', 'expo', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.labande_global_rating', 'lrat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.labande_ski_rating', 'srat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.ice_rating', 'irat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.mixed_rating', 'mrat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.rock_free_rating', 'frat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.rock_required_rating', 'rrat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.aid_rating', 'arat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.global_rating', 'grat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.engagement_rating', 'erat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.hiking_rating', 'hrat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.route_length', 'rlen', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'r', 'sub_activities'), 'sub', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Bool', $m . '.is_on_glacier', 'glac', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Item', 'ri.culture', 'rcult', 'join_route_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lrb.main_id', 'rbooks', 'join_rbook_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lrc.linked_id', 'rtags', 'join_rtag_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lrdc.linked_id', 'rdtags', 'join_rdtag_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lrbc.linked_id', 'rbtags', 'join_rbtag_id', false, $params_list);
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

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, false, 'ls.main_id');

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.main_id');

        // parking criteria
        self::buildConditionItem($conditions, $values, 'Config', '', 'haspark', 'join_hasparking', false, $params_list);
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');

        // route criteria
        Route::buildRouteListCriteria(&$conditions, &$values, $params_list, true);

        // book criteria
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 'r');
        self::buildConditionItem($conditions, $values, 'List', 'lrb.main_id', 'books', 'join_rbook_id', false, $params_list);
       
        // outing criteria
        Outing::buildOutingListCriteria(&$conditions, &$values, $params_list, false, 'lo.linked_id');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public static function browse($sort, $criteria, $format = null)
    {
        $pager = self::createPager('Route', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
        
        self::joinOnRegions($q);

        // to get summit info:
        $q->leftJoin('m.associations lsname')
          ->leftJoin('lsname.Summit sname')
          ->leftJoin('sname.SummitI18n snamei')
          ->addWhere("lsname.type = 'sr'");

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
            self::filterOnActivities($q);
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
        
        if (isset($conditions['join_route_i18n']))
        {
            $q->leftJoin('m.RouteI18n ri');
            unset($conditions['join_route_i18n']);
        }

        if (isset($conditions['join_rtag_id']))
        {
            $q->leftJoin("m.LinkedAssociation lrc");
            unset($conditions['join_rtag_id']);
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
            $q->leftJoin("m.associations ls");
            
            if (isset($conditions['join_summit_id']))
            {
                unset($conditions['join_summit_id']);
            }
            else
            {
                $q->addWhere("ls.type = 'sr'");
            }
            
            if (isset($conditions['join_summit']))
            {
                $q->leftJoin('ls.Summit s');
                if (isset($conditions['join_summit']))
                {
                    unset($conditions['join_summit']);
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
        
        // join with huts tables only if needed 
        if (   isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            $q->leftJoin('m.associations lh');
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
        if (isset($conditions['join_hasparking']))
        {
            if ($conditions['join_hasparking'])
            {
                $is_null = 'IS NOT NULL';
            }
            else
            {
                $is_null = 'IS NULL';
            }
            $q->leftJoin('m.associations l4')
              ->addWhere("l4.type = 'pr' AND l4 $is_null");
            
            unset($conditions['join_hasparking']);
        }
        elseif (   isset($conditions['join_parking_id'])
                || isset($conditions['join_parking'])
                || isset($conditions['join_parking_i18n'])
                || isset($conditions['join_ptag_id'])
        )
        {
            $q->leftJoin('m.associations lp');
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
        if (   isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbook'])
            || isset($conditions['join_rbook_i18n'])
            || isset($conditions['join_rbtag_id'])
        )
        {
            $q->leftJoin('m.associations lrb');
            
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
        
        // join with outings tables only if needed 
        if (   isset($conditions['join_outing_id'])
                || isset($conditions['join_outing'])
                || isset($conditions['join_outing_i18n'])
                || isset($conditions['join_otag_id'])
        )
        {
            $q->leftJoin('m.LinkedAssociation lo');
            if (isset($conditions['join_outing_id']))
            {
                unset($conditions['join_outing_id']);
            }
            else
            {
                $q->addWhere("lo.type = 'ro'");
            }
            
            if (isset($conditions['join_outing']))
            {
                $q->leftJoin('lo.Outing o');
                unset($conditions['join_outing']);
            }

            if (isset($conditions['join_outing_i18n']))
            {
                $q->leftJoin('lo.OutingI18n oi');
                unset($conditions['join_outing_i18n']);
            }
            
            if (isset($conditions['join_otag_id']))
            {
                $q->leftJoin("lo.MainAssociation loc");
                unset($conditions['join_otag_id']);
            }
        }

        if (isset($conditions['join_itag_id']))
        {
            $q->leftJoin("m.LinkedAssociation li")
              ->leftJoin("li.MainMainAssociation lic")
              ->addWhere("li.type = 'ri'");
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
                           array('m.activities', 'm.max_elevation', 'm.facing',
                                 'm.height_diff_up', 'm.difficulties_height',
                                 'm.global_rating', 'm.engagement_rating', 'm.equipment_rating',
                                 'm.toponeige_technical_rating', 'm.toponeige_exposition_rating',
                                 'm.labande_ski_rating', 'm.labande_global_rating',
                                 'm.rock_free_rating', 'm.rock_required_rating',
                                 'm.ice_rating', 'm.mixed_rating', 'm.aid_rating',
                                 'm.hiking_rating', 'm.route_length', 'l.type',
                                 's.elevation', 's.lon', 's.lat', 'si.name', 'si.search_name'));
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnActivities($q);
        self::filterOnRegions($q);
    }
}
