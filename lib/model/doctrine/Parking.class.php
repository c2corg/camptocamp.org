<?php
/**
 * $Id: Parking.class.php 2529 2007-12-19 14:07:18Z alex $
 */
class Parking extends BaseParking
{
    public static function getAssociatedParkingsData($associated_docs)
    {
        $parkings = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_parking')),
                                            'Parking',
                                            array('lowest_elevation', 'public_transportation_rating', 'public_transportation_types'));

        return $parkings;
    }

    public static function addAssociatedParkings(&$docs, $type)
    {
        Document::addAssociatedDocuments($docs, $type, false,
                                         array('elevation', 'lowest_elevation', 'public_transportation_rating', 'public_transportation_types'),
                                         array('name'));
    }
    
    public static function filterSetElevation($value)
    {   
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetLowest_elevation($value)
    {   
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetPublic_transportation_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetPublic_transportation_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetPublic_transportation_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSnow_clearance_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildParkingListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id', $m = 'p')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'p';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'parking_i18n';
        }
        else
        {
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'parking';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, 'id', $join_id);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', 'lp.main_id', 'parkings', 'parking_id');
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', 'lpp.linked_id', 'subparkings', 'subparking_id');
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'parkings', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'parnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'pi.search_name'), ($is_module ? array('pnam', 'name') : 'pnam'), array($join_idi18n, $join_i18n), 'Parking');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'palt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.public_transportation_rating', 'tp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'public_transportation_types'), 'tpty', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'pi.culture', 'pcult', $join_i18n);
            
            // book criteria
            $nb_name = Book::buildBookListCriteria($criteria, $params_list, false, 'p', 'main_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'p', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (   isset($criteria[2]['join_pbook'])
                || isset($criteria[2]['join_particle'])
            )
            {
                $joins['join_parking'] = true;
                if (!$is_module)
                {
                    $joins['post_parking'] = true;
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
            $joins['join_parking'] = true;
        }
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        $criteria[2] += $joins;
        
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'parkings', 'ract');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('pnam'), array('parking_i18n', 'join_parking'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'p');

        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // parking / book / article criteria
        $has_name = Parking::buildParkingListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // hut criteria
        $has_name = Hut::buildHutListCriteria($criteria, $params_list, false, 'linked_id');
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

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }

    public static function buildMainPagerConditions(&$q, $criteria)
    {
        self::joinOnRegions($q);
    }
    
    public static function buildParkingPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null, $p = 'p')
    {
        $join = 'parking';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
            
            if (isset($joins['parking_id']))
            {
                $q->leftJoin($main_join . ' lp');
                
                if (isset($joins['parking_id_has']))
                {
                    $q->addWhere("lp.type = 'pp'");
                }
            }
            
            if (isset($joins['subparking_id']))
            {
                $q->leftJoin($linked_join . ' lpp');
                
                if (isset($joins['subparking_id_has']))
                {
                    $q->addWhere("lpp.type = 'pp'");
                }
            }
        }
        else
        {
            $m = 'lp';
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
                    $q->leftJoin($m . '.' . $linked . 'Parking p');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'ParkingI18n pi');
        }
        
        if (isset($joins['join_pbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 'p', false, $main_join, 'bp');
        }
        
        if (isset($joins['join_particle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'p', false, $linked_join, 'pc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with parking / book / article tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, true);
        }
        
        // join with huts tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'ph');
        }

        // join with routes tables only if needed 
        if (   isset($joins['join_summit'])
            || isset($joins['join_outing'])
        )
        {
            $joins['join_route'] = true;
            $joins['post_route'] = true;
        }
        
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'pr');

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
            Site::buildSitePagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'pt');
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'pi');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }

    public static function getSortField($orderby, $mi = 'mi')
    {
        switch ($orderby)
        {
            case 'id':   return 'm.id';
            case 'pnam': return $mi . '.search_name';
            case 'palt': return 'm.elevation';
            case 'tp':  return 'm.public_transportation_rating';
            case 'tpty':  return 'm.public_transportation_types';
            case 'scle':  return 'm.snow_clearance_rating';
            case 'range': return 'gr.linked_id';
            case 'admin': return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley': return 'gv.linked_id';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    } 

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.elevation', 'm.lowest_elevation', 'm.public_transportation_rating', 'm.public_transportation_types', 'm.snow_clearance_rating', 'm.lon', 'm.lat');
            $data_fields_list = array_merge($data_fields_list,
                                            parent::buildGeoFieldsList());
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
