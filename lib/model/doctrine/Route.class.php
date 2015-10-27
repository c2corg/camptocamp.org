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

    public static function addBestSummitName($routes, $separator = ': ', $summit_id = null, $summit_name = '')
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
        $_a = $_c = array();
        foreach ($results as $key => $result)
        {
            if (!array_key_exists($result['linked_id'], $_a))
            {
                $_a[$result['linked_id']] = array('elevation' => $result['Summit'][0]['elevation'], 'item_key' => $key);
            }
            else
            {
                // there already exists an associated summit with this route => find highest
                $candidate_so_far = $_a[$result['linked_id']];

                if ($result['Summit'][0]['elevation'] == $candidate_so_far['elevation'])
                {
                    // OK this is a rare case but our summits have the same height. Which one should we keep?
                    // Apart from very odd cases that probably don't exist, we have here one summit and one sub-summit
                    // we need some extra db request to check if they are linked, and how 
                    // As we are considering a list of routes, we do some caching in order to avoid repeating db requests
                    $cid = $results[$candidate_so_far['item_key']]['main_id'];
                    $nid = $result['main_id'];

                    $isparent = isset($_c[$nid][$cid]) ? $_c[$nid][$cid] : Association::find($nid, $cid, 'ss', true);
                    if ($isparent)
                    {
                        $_a[$result['linked_id']] = array('elevation' => $result['Summit'][0]['elevation'], 'item_key' => $key);
                        $_c[$nid][$cid] = true;
                        $_c[$cid][$nid] = false;
                    }
                    else
                    {
                        $_c[$nid][$cid] = false;
                        $_c[$cid][$nid] = true;
                    }
                }
                else if ($result['Summit'][0]['elevation'] > $candidate_so_far['elevation'])
                {
                    $_a[$result['linked_id']] = array('elevation' => $result['Summit'][0]['elevation'], 'item_key' => $key);
                }
            }
        }

        // remove unnecessary results (those with weak summits)
        $_b = array();
        foreach ($_a as $key => $keep)
        {
           $_b[$key] =  $results[$keep['item_key']];
        }
        
        // extract best name of summits
        foreach ($_b as $key => $_bb)
        {
            $_b[$key]['Summit'] = Language::getTheBest($_bb['Summit'], 'Summit', array(), 'id', false);
        }

        // merge highest summit name into array of associated routes names.
        // if $summit_id is given, do not add summit
        // if $summit_name is given, there are sub-summits, then $summit_name is removed from each route name which start with "$summit_name - "
        // if there is no associated summit, do nothing
        if (!empty($summit_name))
        {
            $has_sub_summits = true;
            $summit_name_prefix = $summit_name . ' - ';
            $summit_name_length = strlen($summit_name_prefix);
        }
        else
        {
            $has_sub_summits = false;
        }
        
        foreach ($routes as $key => $route)
        {
            $route_summit_name = $_b[$route['id']]['Summit'][0]['SummitI18n'][0]['name'];
            $routes[$key]['full_name'] = $route_summit_name . '-' . $route['name'];

            if (!isset($route['name'])) $route['name'] = '';
            if ((!empty($summit_id) && ($summit_id == $_b[$route['id']]['Summit'][0]['id']))
                || (empty($route_summit_name)))
            {
                $routes[$key]['add_summit_name'] = false;
                $routes[$key]['name'] = $route['name'];
            }
            else
            {
                $routes[$key]['add_summit_name'] = true;
                if ($has_sub_summits && (strpos($route_summit_name, $summit_name_prefix) === 0))
                {
                    $route_summit_name = c2cTools::multibyte_ucfirst(substr($route_summit_name, $summit_name_length));
                }
                $routes[$key]['name'] = $route_summit_name . $separator . $route['name'];
            }
        }
        return $routes;
    }

    public static function getAssociatedRoutesData($associated_docs, $separator = ': ', $summit_id = null, $summit_name = '')
    {
        sfLoader::loadHelpers(array('General'));
        
        $routes =  Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_route')), 
                                            'Route', 
                                            array('activities', 'global_rating', 'height_diff_up', 'difficulties_height',
                                                  'facing', 'engagement_rating', 'objective_risk_rating', 'toponeige_technical_rating', 
                                                  'toponeige_exposition_rating', 'labande_ski_rating', 'labande_global_rating', 'rock_free_rating',
                                                  'rock_required_rating', 'geom_wkt', 'ice_rating', 'mixed_rating', 'aid_rating',
                                                  'rock_exposition_rating', 'hiking_rating', 'snowshoeing_rating',
                                                  'max_elevation', 'equipment_rating', 'duration'));

        // TODO: do additional fields fetching + summit name fetching at once (one query instead of 2)
        $routes = self::addBestSummitName($routes, $separator, $summit_id, $summit_name);

       if (empty($routes))
           return $routes;

        // sort alphabetically by name
        if (empty($summit_id))
        {
            foreach ($routes as $key => $row)
            {
                $name[$key] = remove_accents($row['name']);
            }
            array_multisort($name, SORT_STRING, $routes);
        }
        else
        {
           foreach ($routes as $key => $row)
            {
                $add_summit_name[$key] = $row['add_summit_name'];
                $name[$key] = remove_accents($row['name']);
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
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetHeight_diff_down($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetMin_elevation($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetMax_elevation($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetDifficulties_height($value)
    {
        return self::returnNaturalIntOrNull($value);
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

    public static function filterSetObjective_risk_rating($value)
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

    public static function filterSetRock_exposition_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetHiking_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetSnowshoeing_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildRouteListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = $joins_summit = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = '';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'route_i18n';
        }
        else
        {
            $m = 'r';
            $m2 = 'r.';
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'route';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'routes'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'routes', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'r', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', null, 'geom', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Relative', array($m2 . 'elevation', 'p.elevation'), 'pappr', 'join_parking');
                
                $nb_name = self::buildConditionItem($conditions, $values, $joins_summit, $params_list, 'Mstring', array(array('ls.main_id', 'si.search_name'), array($midi18n, 'ri.search_name')), 'srnam', array(array('summit_idi18n', 'summit_i18n'), array($join_idi18n, $join_i18n)), array('Summit', 'Route'));
                if ($nb_name === 'no_result')
                {
                    return $nb_name;
                }
                elseif ($nb_name[0]['nb_result'] == 0)
                {
                    $nb_id += $nb_name[1]['nb_result'];
                }
                
                if (isset($joins_summit['summit_idi18n']) || isset($joins_summit['summit_i18n']))
                {
                    $joins_summit['join_summit'] = true;
                }
                if (isset($joins_summit['route_i18n']))
                {
                    $joins['route_i18n'] = true;
                }
                
                // nousers
                $user_groups = c2cTools::getArrayElement($params_list, 'nousers');
                if (!is_null($user_groups))
                {
                    $user_groups = explode(' ', $user_groups);
                    $user_ids = array();
                    $route_ids = array();
                    $first_group = true;
                    foreach ($user_groups as $user_group)
                    {
                        $conditions_temp = array("a.type = 'ro'", "lu.type = 'uo'");
                        $values_temp = array();
                        self::buildListCondition($conditions_temp, $values_temp, 'lu.main_id', $user_group);
                        $where = implode(' AND ', $conditions_temp);
                        
                        $routes = Doctrine_Query::create()
                         ->select('DISTINCT a.main_id')
                         ->from('Association a')
                         ->leftJoin('a.MainMainAssociation lu')
                         ->where($where, $values_temp)
                         ->execute(array(), Doctrine::FETCH_ARRAY);
                        
                        if (count($routes))
                        {
                            $route_group_ids = array();
                            foreach ($routes as $route)
                            {
                                $route_group_ids[] = $route['main_id'];
                            }
                            $route_group_ids = array_unique($route_group_ids);
                            if ($first_group)
                            {
                                $route_ids = $route_group_ids;
                            }
                            else
                            {
                                $route_ids = array_intersect($route_ids, $route_group_ids);
                            }
                        }
                        
                        $first_group = false;
                    }
                    
                    if (count($route_ids))
                    {
                        $params_list['noroutes'] = '!' . implode('!', $route_ids);
                        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, 'noroutes', $join_id);
                    }
                }
            }
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'ri.search_name'), ($is_module ? array('rnam', 'name') : 'rnam'), array($join_idi18n, $join_i18n), 'Route');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'r', 'activities'), 'ract', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.max_elevation', 'malt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.min_elevation', 'minalt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.height_diff_up', 'hdif', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.height_diff_down', 'ddif', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Relative', array($m2 .  'height_diff_down', $m2 . 'height_diff_up'), 'dudif', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'ralt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.difficulties_height', 'dhei', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Relative', array($m2 . 'height_diff_up', $m2 . 'difficulties_height'), 'rhappr', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Relative', array($m2 . 'elevation', $m2 . 'min_elevation'), 'rappr', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'r', 'configuration'), 'conf', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Facing', $m . '.facing', 'fac', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.route_type', 'rtyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.equipment_rating', 'prat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.duration', 'time', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.toponeige_technical_rating', 'trat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.toponeige_exposition_rating', 'sexpo', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.labande_global_rating', 'lrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.labande_ski_rating', 'srat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.ice_rating', 'irat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.mixed_rating', 'mrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.rock_free_rating', 'frat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.rock_required_rating', 'rrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.aid_rating', 'arat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.rock_exposition_rating', 'rexpo', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.global_rating', 'grat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.engagement_rating', 'erat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.objective_risk_rating', 'orrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.hiking_rating', 'hrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.snowshoeing_rating', 'wrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.route_length', 'rlen', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'r', 'sub_activities'), 'sub', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.is_on_glacier', 'glac', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ri.culture', 'rcult', $join_i18n);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $m . '.geom_wkt', 'rgeom', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lrd.main_id', 'rdocs', 'rdoc');
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lrdc.linked_id', 'rdtags', 'rdtag');
            
            // book criteria
            $nb_name = Book::buildBookListCriteria($criteria, $params_list, false, 'r', 'main_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'r', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (   isset($criteria[2]['join_rbook'])
                || isset($criteria[2]['join_rarticle'])
            )
            {
                $joins['join_route'] = true;
                if (!$is_module)
                {
                    $joins['post_route'] = true;
                }
            }
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
        }
        if (!empty($joins))
        {
            $joins['join_route'] = true;
        }
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        $criteria[2] += $joins + $joins_summit;
        
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'routes', 'act', array(8));
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('rnam'), array('route_i18n', 'join_route', 'summit_i18n', 'join_summit'));
        self::buildOrderCondition($joins_order, $orderby_list, array('lat', 'lon'), array('summit', 'join_summit'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'r');

        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // route / book / article criteria
        $has_name = Route::buildRouteListCriteria($criteria, $params_list, true);
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

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }

    public static function buildMainPagerConditions(&$q, $criteria)
    {
        $joins = $criteria[2];
        
        self::joinOnRegions($q);

        // to get summit info:
        if (!isset($joins['merged']) || $joins['merged'] == 'null')
        {
            $q->leftJoin('m.associations lsname')
              ->leftJoin('lsname.Summit sname')
              ->leftJoin('sname.SummitI18n snamei')
              ->addWhere("lsname.type = 'sr'");
        }
    }
    
    public static function buildRoutePagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'route';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
        }
        else
        {
            $m = 'lr';
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
                    $q->leftJoin($m . '.' . $linked . 'Route r');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'RouteI18n ri');
        }
        
        if (   isset($joins['rdoc'])
            || isset($joins['rdtag'])
        )
        {
            $q->leftJoin($main_join . " lrd");
            
            if (isset($joins['rdtag']))
            {
                $q->leftJoin("lrd.LinkedLinkedAssociation lrdc")
                  ->addWhere("lrd.type IN ('sr', 'hr', 'pr', 'br')");
            }
        }
        
        if (isset($joins['join_rbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 'r', false, $main_join, 'br');
        }
        
        if (isset($joins['join_rarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'r', false, $linked_join, 'rc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with route / book / article tables only if needed 
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, true);
        }

        // join with summit tables only if needed 
        if (isset($joins['join_summit']))
        {
            Summit::buildSummitPagerConditions($q, $joins, false, false, 'm.associations', 'sr');
        }
        
        // join with hut tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, false, false, 'm.associations', 'hr');
        }
        
        // join with parking tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, 'm.associations', 'pr');
        }
        
        // join with outings tables only if needed 
        if (isset($joins['join_user']))
        {
            $joins['join_outing'] = true;
            $joins['post_outing'] = true;
        }
        
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'ro');
            
            if (isset($joins['join_user']))
            {
                User::buildUserPagerConditions($q, $joins, false, false, 'lo.MainMainAssociation', 'uo');
            }
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'ri');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }

    public static function getSortField($orderby, $mi = 'mi')
    {
        $si = ($mi == 'mi') ? 'snamei' : 'si';
        $s = ($mi == 'mi') ? 'sname' : 's';
        switch ($orderby)
        {
            case 'rnam':    return array("$si.search_name", "$mi.search_name");
            case 'act':     return 'm.activities';
            case 'range':   return 'gr.linked_id';
            case 'admin':   return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley':  return 'gv.linked_id';
            case 'maxa':    return 'm.max_elevation';
            case 'fac':     return 'm.facing';
            case 'hdif':    return 'm.height_diff_up';
            case 'ddif':    return 'm.height_diff_down';
            case 'time':    return 'm.duration';
            case 'ralt':    return 'm.elevation';
            case 'dhei':    return 'm.difficulties_height';
            case 'grat':    return 'm.global_rating';
            case 'erat':    return 'm.engagement_rating';
            case 'orrat':   return 'm.objective_risk_rating';
            case 'prat':    return 'm.equipment_rating';
            case 'frat':    return 'm.rock_free_rating';
            case 'rrat':    return 'm.rock_required_rating';
            case 'arat':    return 'm.aid_rating';
            case 'rexpo':   return 'm.rock_exposition_rating';
            case 'irat':    return 'm.ice_rating';
            case 'mrat':    return 'm.mixed_rating';
            case 'trat':    return 'm.toponeige_technical_rating';
            case 'sexpo':   return 'm.toponeige_exposition_rating';
            case 'lrat':    return 'm.labande_global_rating';
            case 'srat':    return 'm.labande_ski_rating';
            case 'hrat':    return 'm.hiking_rating';
            case 'wrat':    return 'm.snowshoeing_rating';
            case 'rlen':    return 'm.route_length';
            case 'geom':    return 'm.geom_wkt';
            case 'lat':     return "$s.lat";
            case 'lon':     return "$s.lon";
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {
        if ($main_query)
        {
            $routes_fields_list = array('m.activities', 'm.max_elevation', 'm.facing',
                                 'm.height_diff_up', 'm.difficulties_height',
                                 'm.global_rating', 'm.engagement_rating', 'm.objective_risk_rating',
                                 'm.equipment_rating', 'm.toponeige_technical_rating', 'm.toponeige_exposition_rating',
                                 'm.labande_ski_rating', 'm.labande_global_rating',
                                 'm.rock_free_rating', 'm.rock_required_rating',
                                 'm.ice_rating', 'm.mixed_rating', 'm.aid_rating', 'm.rock_exposition_rating',
                                 'm.hiking_rating', 'm.snowshoeing_rating',
                                 'm.route_length',
                                 'lsname.type', // we don't need this, but if we make JOIN chains, and we don't include every element of the chain, doctrine blocks
                                 'sname.elevation', 'sname.lon', 'sname.lat',
                                 'snamei.name', 'snamei.search_name');
            
            $full_fields_list = array();
            $full_i18n_fields_list = array();
            
            if (in_array('full', $format))
            {
                $full_fields_list = array('m.height_diff_down', 'm.route_type', 'm.min_elevation', 'm.duration', 'm.slope', 'm.configuration', 'm.is_on_glacier', 'm.sub_activities');
                
                if (!in_array('notext', $format))
                {
                    $full_i18n_fields_list = array('mi.description', 'mi.remarks', 'mi.gear', 'mi.external_resources', 'mi.route_history');
                }
            }
            
            $data_fields_list = array_merge(parent::buildGeoFieldsList(),
                                            $routes_fields_list,
                                            $full_fields_list,
                                            $full_i18n_fields_list);
        }
        else
        {
            $data_fields_list = array();
        }
        
        $orderby_fields = array();
        if (isset($sort['orderby_params']))
        {
            $orderby = $sort['orderby_params'];
            
            if (!$main_query)
            {
                if (in_array('rnam', $orderby))
                {
                    $orderby_fields[] = 'ls.type';
                }
            }
        }
            
        return array_merge(parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields),
                           $data_fields_list,
                           $orderby_fields);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnActivities($q);
        self::filterOnRegions($q);
    }
    
    // Get orderby parameter to order on rating according to possible activities
    public static function getDefaultRatingOrderby($param)
    {
        $activities = c2cTools::getPossibleActivities($param);
        $activities = array_diff($activities, array(8));
        $orderby = '';
        if (count($activities))
        {
            if (!array_diff($activities, array(1)))
            {
                $orderby = 'trat';
            }
            elseif (!array_diff($activities, array(2, 3, 4, 5)))
            {
                $orderby = 'grat';
            }
            elseif (!array_diff($activities, array(6)))
            {
                $orderby = 'hrat';
            }
            elseif (!array_diff($activities, array(7)))
            {
                $orderby = 'srat';
            }
        }
        
        return $orderby;
    }
}
