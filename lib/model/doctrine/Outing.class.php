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
    public static function listLatest($max_items, $langs, $ranges, $activities, $params = array(), $linked_areas = true, $orderby_date = true)
    {
        if (count($langs))
        {
            $params['ocult'] = implode('-', $langs);
        }
        if (count($ranges))
        {
            $params['areas'] = implode('-', $ranges);
        }
        if (count($activities))
        {
            $params['act'] = implode('-', $activities);
        }
        
        if ($orderby_date)
        {
            $orderby = array('date');
            $order = array('desc');
        }
        else
        {
            $orderby = array();
            $order = array();
        }
        
        if (isset($params['orderby']))
        {
            $orderby_tmp = array($params['orderby']);
            if ($orderby_tmp == 'id')
            {
                $orderby_date = false;
                $orderby = array();
            }
            unset($params['orderby']);
        }
        if (isset($params['order']))
        {
            $order = array($params['order']);
            unset($params['order']);
        }
        
        $criteria = Outing::buildListCriteria($params);
        
        $sort = array('orderby_params' => $orderby,
                      'order_params' => $order,
                      'npp'      => $max_items
                     );
        
        $sub_query_result = self::browseId('Outing', $sort, $criteria, array(), 1, $max_items);
        
        $nb_results = $sub_query_result['nb_results'];
        $ids = $sub_query_result['ids'];
        if (empty($nb_results) || empty($ids))
        {
            return array();
        }
        
        $where_ids = 'm.id' . $sub_query_result['where'];
        
        $fields = 'm.id, n.culture, n.name, m.date, m.activities, m.max_elevation';
        if ($linked_areas)
        {
            $fields .= ', g0.linked_id, a.area_type, ai.name, ai.culture';
        }
        
        $q = Doctrine_Query::create();
        $q->select($fields)
          ->from('Outing m')
          ->leftJoin('m.OutingI18n n')
          ->addWhere($where_ids, $ids);
       
        if ($linked_areas)
        {
            $q->leftJoin('m.geoassociations g0')
              ->leftJoin('g0.AreaI18n ai')
              ->leftJoin('ai.Area a');
        }
        
        if ($orderby_date)
        {
            $q->orderBy('m.date DESC, m.id DESC');
        }
        else
        {
            $q->orderBy('m.id DESC');
        }
        
        $outings = $q->execute(array(), Doctrine::FETCH_ARRAY);
        
        $outings = Outing::fetchAdditionalFields($outings, false, true);
        
        return $outings;
    }

    public static function buildOutingListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = '';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'outing_i18n';
        }
        else
        {
            $m = 'o';
            $m2 = 'o.';
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'outing';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'outings'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'outings', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'o', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Date', 'date', array('date', 'odate'), $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', null, 'geom', $join);
            }
            else
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Date', $m . '.date', 'odate', $join);
            }
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'oi.search_name'), ($is_module ? array('onam', 'name') : 'onam'), array($join_idi18n, $join_i18n), 'Outing');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'o', 'activities'), 'oact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.max_elevation', 'oalt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.height_diff_up', 'odif', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.height_diff_down', 'oddif', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Relative', array($m2 . 'height_diff_down', $m2 . 'height_diff_up'), 'odudif', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.outing_length', 'olen', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.outing_with_public_transportation', 'owtp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.partial_trip', 'ptri', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.frequentation_status', 'ofreq', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.conditions_status', 'ocond', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.glacier_status', 'oglac', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.track_status', 'otrack', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.access_status', 'opark', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.lift_status', 'olift', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.hut_status', 'ohut', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $m . '.geom_wkt', 'ogeom', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'oi.culture', 'ocult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'o', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_oarticle']))
            {
                $joins['join_outing'] = true;
                if (!$is_module)
                {
                    $joins['post_outing'] = true;
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
            $joins['join_outing'] = true;
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

        // criteria for enabling/disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'outings');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('onam'), array('outing_i18n', 'join_outing'));
        self::buildOrderCondition($joins_order, $orderby_list, array('lat', 'lon'), array('summit', 'join_summit'));
            
        //FIXME : Don't work : $outings_sort_route_criteria = sfConfig::get('app_outings_sort_route_criteria');
        $outings_sort_route_criteria = array('fac', 'time', 'ralt', 'dhei', 'grat', 'erat', 'prat', 'frat', 'arat', 'irat', 'mrat', 'trat', 'sexpo', 'lrat', 'srat', 'hrat', 'wrat');
        self::buildOrderCondition($joins_order, $orderby_list, $outings_sort_route_criteria, array('route', 'join_route'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'o');
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // outing / article criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, true);
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

        // route criteria
        $has_name = Route::buildRouteListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // site criteria
        $has_name = Site::buildSiteListCriteria($criteria, $params_list, false, 'main_id');
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
        self::joinOnRegions($q);
    }
    
    public static function buildOutingPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'outing';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
        }
        else
        {
            $m = 'lo';
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
                    $q->leftJoin($m . '.' . $linked . 'Outing o');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'OutingI18n oi');
        }
        
        if (isset($joins['join_oarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'o', false, $linked_join, 'oc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with outing tables only if needed 
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, true);
        }

        // join with route tables only if needed 
        if (   isset($joins['join_summit'])
            || isset($joins['join_hut'])
            || isset($joins['join_parking'])
        )
        {
            $joins['join_route'] = true;
            $joins['post_route'] = true;
        }
        
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, false, 'm.associations', 'ro');

            if (isset($joins['join_summit']))
            {
                Summit::buildSummitPagerConditions($q, $joins, false, false, 'lr.MainAssociation', 'sr');
            }
            
            if (isset($joins['join_hut']))
            {
                Hut::buildHutPagerConditions($q, $joins, false, false, 'lr.MainAssociation', 'hr');
            }
            
            if (isset($joins['join_parking']))
            {
                Parking::buildParkingPagerConditions($q, $joins, false, false, 'lr.MainAssociation', 'pr');
            }
        }

        // join with site tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, false, false, 'm.associations', 'to');
        }

        // join with user tables only if needed 
        if (isset($joins['join_user']))
        {
            User::buildUserPagerConditions($q, $joins, false, false, 'm.associations', 'uo');
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'oi');
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
            case 'onam': return $mi . '.search_name';
            case 'act':  return 'm.activities';
            case 'alt':  return 'm.max_elevation';
            case 'date': return array('m.date', 'm.id');
            case 'hdif': return 'm.height_diff_up';
            case 'ddif': return 'm.height_diff_down';
            case 'cond': return 'm.conditions_status';
            case 'geom': return 'm.geom_wkt';
            case 'time': return 'r.duration';
            case 'fac':  return 'r.facing';
            case 'ralt': return 'r.elevation';
            case 'dhei': return 'r.difficulties_height';
            case 'grat': return 'r.global_rating';
            case 'erat': return 'r.engagement_rating';
            case 'prat': return 'r.equipment_rating';
            case 'frat': return 'r.rock_free_rating';
            case 'arat': return 'r.aid_rating';
            case 'irat': return 'r.ice_rating';
            case 'mrat': return 'r.mixed_rating';
            case 'trat': return 'r.toponeige_technical_rating';
            case 'sexpo': return 'r.toponeige_exposition_rating';
            case 'lrat': return 'r.labande_global_rating';
            case 'srat': return 'r.labande_ski_rating';
            case 'hrat': return 'r.hiking_rating';
            case 'wrat': return 'r.snowshoeing_rating';
            case 'range': return 'gr.linked_id';
            case 'admin': return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley': return 'gv.linked_id';
            case 'lat': return 's.lat';
            case 'lon': return 's.lon';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {
        if ($main_query)
        {
            $outings_fields_list = array('m.activities', 'm.date',
                                         'm.height_diff_up', 'm.max_elevation',
                                         'v.version', 'hm.user_id', 'u.topo_name', 
                                         'm.geom_wkt', 'm.conditions_status', 'm.frequentation_status');
            
            $conditions_fields_list = (array_intersect($format, array('cond', 'full'))) ?
                                      array('m.up_snow_elevation', 'm.down_snow_elevation', 'm.access_elevation',
                                            'mi.conditions', 'mi.conditions_levels', 'mi.weather', 'mi.timing')
                                      : array();
            
            $full_fields_list = (in_array('full', $format)) ?
                                array('m.partial_trip', 'm.min_elevation', 'm.height_diff_down', 'm.outing_length', 'm.outing_with_public_transportation',
                                      'm.access_status', 'm.glacier_status', 'm.track_status', 'm.hut_status', 'm.lift_status',
                                      'mi.participants', 'mi.timing', 'mi.access_comments', 'mi.hut_comments', 'mi.description')
                                : array();
            
            $data_fields_list = array_merge(parent::buildGeoFieldsList(),
                                            $outings_fields_list,
                                            $conditions_fields_list,
                                            $full_fields_list);
        }
        else
        {
            $data_fields_list = array();
        }
        
        $orderby_fields = array();
        if (isset($sort['orderby_params']))
        {
            $orderby = $sort['orderby_params'];
            
            //FIXME : Don't work : $outings_sort_route_criteria = sfConfig::get('app_outings_sort_route_criteria');
            $outings_sort_route_criteria = array('fac', 'time', 'ralt', 'dhei', 'grat', 'erat', 'prat', 'frat', 'arat', 'irat', 'mrat', 'trat', 'sexpo', 'lrat', 'srat', 'hrat', 'wrat');
            
            if (array_intersect($orderby, $outings_sort_route_criteria))
            {
                $orderby_fields[] = 'lr.type'; // if we don't include it, doctrine blocks (chain of join?)
            }
            elseif (array_intersect($orderby, array('lat', 'lon')))
            {
                $orderby_fields = array('lr.type', 'ls.type');
            }
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        return array_merge($base_fields_list,
                           $data_fields_list,
                           $orderby_fields);
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

    public static function fetchAdditionalFields($objects, $add_fields = true, $add_images_count = false)
    {
        if (!count($objects)) 
        {   
            return array();
        }
    
        $ids = array();
        $q = array();
        $out = array();

        // build ids list
        foreach ($objects as $object)
        {
            $ids[] = $object['id'];
            $q[] = '?';
        }

        if ($add_fields)
        {
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
        }
        else
        {
            $out = $objects;
        }

        if ($add_images_count)
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
                                        'objective_risk_rating',
                                        'rock_free_rating',
                                        'rock_required_rating',
                                        'ice_rating',
                                        'mixed_rating',
                                        'aid_rating',
                                        'rock_exposition_rating',
                                        'equipment_rating');
        $route_hiking_fields = array ('hiking_rating');
        $route_snowshoeing_fields = array ('snowshoeing_rating');
        $route_fields = array_merge($route_ski_fields, $route_climbing_fields, $route_hiking_fields, $route_snowshoeing_fields);
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
            if (!count(array_intersect($activities_to_show, array(7)))) foreach($route_snowshoeing_fields as $field) $outing[$field] = null;
        }

        return $outings;
    }
}
