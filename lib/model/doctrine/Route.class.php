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
                    ->select('l.main_id, s.id, s.elevation, si.name, si.search_name') 
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
            if (isset($route['search_name']))
            {
                $routes[$key]['search_name'] = $_b[$route['id']]['Summit'][0]['SummitI18n'][0]['search_name'] . '-' . $route['search_name'];
            }
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
                                                  'labande_global_rating', 'rock_free_rating', 'geom_wkt',
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
        return self::returnNullIfEmpty($value * 1000);
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

    public static function browse($sort, $criteria)
    {
        $pager = self::createPager('Route', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
        
        self::joinOnRegions($q);

        // to get summit info:
        $q->leftJoin('m.associations l')
          ->leftJoin('l.Summit s')
          ->leftJoin('s.SummitI18n si')
          ->addWhere("l.type = 'sr'");

        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            
            $conditions = self::joinOnMultiRegions($q, $conditions);
            
            // join with huts tables only if needed 
            if (isset($conditions['join_hut']))
            {
                unset($conditions['join_hut']);
                $q->leftJoin('m.associations l2')
                  ->leftJoin('l2.Hut h')
                  ->addWhere("l2.type = 'hr'");

                if (isset($conditions['join_hut_i18n']))
                {
                    unset($conditions['join_hut_i18n']);
                    $q->leftJoin('h.HutI18n hi');
                }
            }
            
            // join with parkings tables only if needed 
            if (isset($conditions['join_parking']))
            {
                unset($conditions['join_parking']);
                $q->leftJoin('m.associations l3')
                  ->leftJoin('l3.Parking p')
                  ->addWhere("l3.type = 'pr'");

                if (isset($conditions['join_parking_i18n']))
                {
                    unset($conditions['join_parking_i18n']);
                    $q->leftJoin('p.ParkingI18n pi');
                }
            }

            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        else
        {
            $q->addWhere("l.type = 'sr'");
            
            if (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
            {
                self::filterOnActivities($q);
                self::filterOnRegions($q);
            }
            else
            {
                $pager->simplifyCounter();
            }
        }

        return $pager;
    }

    protected static function buildFieldsList()
    {
        return array_merge(parent::buildFieldsList(), 
                           parent::buildGeoFieldsList(),
                           array('m.activities', 'm.max_elevation', 'm.facing',
                                 'm.height_diff_up', 'm.difficulties_height',
                                 'm.global_rating', 'm.engagement_rating',
                                 'm.toponeige_technical_rating',
                                 'm.toponeige_exposition_rating',
                                 'm.labande_ski_rating', 'm.equipment_rating',
                                 'm.labande_global_rating', 'm.rock_free_rating',
                                 'm.ice_rating', 'm.mixed_rating', 'm.aid_rating',
                                 'm.hiking_rating', 'm.route_length', 'l.type',
                                 's.elevation', 'si.name', 'si.search_name'));
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnActivities($q);
        self::filterOnRegions($q);
    }
}
