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

    public static function addBestSummitName($routes)
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
        foreach ($routes as $key => $route)
        {
            $routes[$key]['name'] = $_b[$route['id']]['Summit'][0]['SummitI18n'][0]['name'] . ' : ' . $route['name'];
        }
        return $routes;
    }

    public static function getAssociatedRoutesData($associated_docs, $add_summit_name = false)
    {
        $routes =  Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_route')), 
                                            'Route', 
                                            array('activities', 'global_rating', 'height_diff_up', 'facing',
                                                  'engagement_rating', 'toponeige_technical_rating', 
                                                  'toponeige_exposition_rating', 'labande_ski_rating',
                                                  'labande_global_rating', 'rock_free_rating', 'geom_wkt',
                                                  'ice_rating', 'mixed_rating', 'aid_rating', 'hiking_rating'));

        
        if ($add_summit_name)
        {
            // TODO: do additional fields fetching + summit name fetching at once (one query instead of 2)
            $routes = self::addBestSummitName($routes);
        }

        // sort alphabetically by name
        foreach ($routes as $key => $row)
        {
            $name[$key] = $row['name'];
        }
        array_multisort($name, SORT_STRING, $routes);

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

    public static function filterSetDifficulties_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterGetRoute_length($value)
    {
        return round($value / 1000, 1); 
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
            // join with parkings tables only if needed 
            if (isset($criteria[0]['join_parking']))
            {
                unset($criteria[0]['join_parking']);
                
                $q->leftJoin('m.associations l2')
                  ->addWhere("l2.type IN ('pr')")
                  ->leftJoin('l2.Parking p');

                if (isset($criteria[0]['join_parking_i18n']))
                {
                    unset($criteria[0]['join_parking_i18n']);
                    $q->leftJoin('p.ParkingI18n pi');
                }
            }
            
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
        }
        elseif (c2cPersonalization::isMainFilterSwitchOn())
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

    protected static function buildFieldsList()
    {
        return array_merge(parent::buildFieldsList(), 
                           parent::buildGeoFieldsList(),
                           array('m.activities', 'm.facing', 'm.height_diff_up',
                                 'm.global_rating', 'm.engagement_rating',
                                 'm.toponeige_technical_rating',
                                 'm.toponeige_exposition_rating',
                                 'm.labande_ski_rating',
                                 'm.labande_global_rating', 'm.rock_free_rating',
                                 'm.ice_rating', 'm.mixed_rating', 'm.aid_rating',
                                 'm.hiking_rating', 'l.type', 's.elevation', 
                                 'si.name'));
    }

    public static function buildFacingRange(&$conditions, &$values, $field, $param)
    {
        $facings = explode('~', $param);
        if (count($facings) == 1)
        {
            $conditions[] = "$field = ?";
            $values[] = $facings[0];
        }
        else
        {
            $facing1 = $facings[0];
            $facing2 = $facings[1];
            
            if ($facing1 == $facing2)
            {
                $conditions[] = "$field = ?";
                $values[] = $facing1;
            }
            elseif ($facing1 > $facing2)
            {
                $conditions[] = "$field BETWEEN ? AND ?";
                $values[] = $facing2;
                $values[] = $facing1;
            }
            else
            {
                $conditions[] = "$field <= ? OR $field >= ?";
                $values[] = $facing1;
                $values[] = $facing2;
            }
        }
    }
}
