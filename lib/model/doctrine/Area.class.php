<?php
/**
 * Model for areas
 * $Id: Area.class.php 2529 2007-12-19 14:07:18Z alex $
 */

class Area extends BaseArea
{

    // returns an array of regions 
    public static function getRegions($area_type, $user_prefered_langs, $ids = array())
    {
        sfLoader::loadHelpers(array('General'));

        $filter_type = !empty($area_type);
        $filter_ids = !empty($ids);

        $select = 'a.id, i.name';
        if (!$filter_type)
        {
            $select .= ', a.area_type';
        }

        $q = Doctrine_Query::create()
                           ->select($select)
                           ->from('Area a')
                           ->leftJoin('a.AreaI18n i');
        if ($filter_type)
        {
            $q->where('a.area_type = ?', array($area_type));
        }
        if ($filter_ids)
        {
            $condition_array = array();
            foreach ($ids as $id)
            {
                $condition_array[] = '?';
            }
            $q->where('a.id IN ( ' . implode(', ', $condition_array) . ' )', array($ids));
        }
        
        if ($filter_type || $filter_ids)
        {
            $q->orderBy('i.search_name');
        }
        else
        {
            $q->orderBy('a.area_type');
        }
        $results = $q->execute(array(), Doctrine::FETCH_ARRAY);
                             
        // build the actual results based on the user's prefered language
        $out = array();
        foreach ($results as $result)
        {
            $ref_culture_rank = 10; // fake high value
            foreach ($result['AreaI18n'] as $translation)
            {
                $tmparray = array_keys($user_prefered_langs, $translation['culture']); 
                $rank = array_shift($tmparray);
                if ($rank < $ref_culture_rank)
                {
                    $best_name = $translation['name'];
                    $ref_culture_rank = $rank;
                }
            }
            $out[$result['id']] = ucfirst($best_name);
        }
        return $out;
    }

    /**
     * Retrieve the most precise attached region level and
     * return the corresponding string
     *
     * $geo: array of attached areas with I18n already worked out
     */
    public static function getBestRegionDescription($geo, $link_to_conditions = false)
    {
        $nb_geo = count($geo);
        if ($nb_geo == 0)
        {
            return null;
        }
        elseif ($nb_geo == 1)
        {
            $id = ($geo instanceof sfOutputEscaperArrayDecorator) ? $geo->key() : key($geo);
            $regions = array($id => $geo[$id]['AreaI18n'][0]['name']);
        }
        elseif ($nb_geo > 1)
        {
            $areas = $ids = $types = $regions = $countries = array();
            foreach ($geo as $id => $g)
            {
                $area = $g['AreaI18n'][0];
                if (empty($area)) continue;
                $types[] = !empty($area['Area']['area_type']) ? $area['Area']['area_type'] : 0;
                $areas[] = $area['name'];
                $ids[] = $id;
            }
            // use ranges if any
            $rk = array_keys($types, 1);
            if ($rk)
            {
                foreach ($rk as $r)
                {
                     $regions[$ids[$r]] = $areas[$r];
                }
            }
            else
            {
                // else use dept/cantons if any
                $ak = array_keys($types, 3);
                if ($ak)
                {
                    foreach ($ak as $a)
                    {
                        $regions[$ids[$a]] = $areas[$a];
                    }
                    
                    $countries = array();
                    $ck = array_keys($types, 2);
                    foreach ($ck as $c)
                    {
                        $countries[$ids[$c]] = $areas[$c];
                    }
                }
                else
                {
                    // else use what's left (coutries)
                    $ck = array_keys($types, 2);
                    foreach ($ck as $c)
                    {
                        $regions[$ids[$c]] = $areas[$c];
                    }
                }
            }
        }
        
        if ($link_to_conditions)
        {
            foreach ($regions as $id => $region)
            {
                $regions[$id] = link_to($region, "/outings/conditions?areas=$id&date=3W&orderby=date&order=desc");
            }
        }
        
        if (isset($countries))
        {
            $regions = array_merge($regions, $countries);
        }
        
        return implode(', ', $regions);
    }

    public static function buildAreaListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'a';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'area_i18n';
        }
        else
        {
            $m = 'a';
            $m2 = $m;
            $mid = array('g' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'area';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'areas'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, 'Multilist', $mid, 'areas', $join_id, false, $params_list);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                // self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'a', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bbox', 'geom', 'bbox', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', 'geom', 'around', $join);
            }
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'ai.search_name'), ($is_module ? array('anam', 'name') : 'anam'), array($join_idi18n, $join_i18n), 'Area');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.area_type', 'atyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'a', 'activities'), 'aact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ai.culture', 'acult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'a', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_aarticle']))
            {
                $joins['join_area'] = true;
                if (!$is_module)
                {
                    $joins['post_area'] = true;
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
            $joins['join_area'] = true;
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'areas');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('anam'), array('area_i18n', 'join_area'));
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = $conditions;
            $criteria[1] = $values;
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area / article criteria
        $has_name = Area::buildAreaListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, false, 'main_id');
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

        // product criteria
        $has_name = Product::buildProductListCriteria($criteria, $params_list, false, 'main_id');
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
    }
    
    public static function buildAreaPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'area';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.geoassociations';
            $linked_join = $m . '.LinkedAssociation';
        }
        else
        {
            $m = 'lo.';
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
                        if ($ltype)
                    {
                        $q->addWhere($m . ".type = '$ltype'");
                    }
                    }
                }
                
                if (isset($joins[$join]))
                {
                    $q->leftJoin($m . '.' . $linked . 'Area a');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'AreaI18n ai');
        }
        
        if (isset($joins['join_aarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'a', false, $linked_join, 'ac');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        $route_join = 'm.MainGeoassociations';
        $route_ltype = '';
        $summit_join = 'm.MainGeoassociations';
        $summit_ltype = '';
        $hut_join = 'm.MainGeoassociations';
        $hut_ltype = '';
        $parking_join = 'm.MainGeoassociations';
        $parking_ltype = '';
        $site_join = 'm.MainGeoassociations';
        $site_ltype = '';
        $user_join = 'm.MainGeoassociations';
        $user_ltype = '';
        
        // join with area tables only if needed 
        if (isset($joins['join_area']))
        {
            Area::buildAreaPagerConditions($q, $joins, true);
        }
        
        // join with outing tables only if needed 
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, false, 'm.MainGeoassociations', '');
            
            $route_join = 'lo.MainAssociation';
            $route_ltype = 'ro';
            $summit_join = 'lr.MainAssociation';
            $summit_ltype = 'sr';
            $hut_join = 'lr.MainAssociation';
            $hut_ltype = 'hr';
            $parking_join = 'lr.MainAssociation';
            $parking_ltype = 'pr';
            $site_join = 'lo.MainAssociation';
            $site_ltype = 'to';
            $user_join = 'lu.MainAssociations';
            $user_ltype = 'uo';
        }

        // join with route tables only if needed 
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, false, $route_join, $route_ltype);
            
            $summit_join = 'lr.MainAssociation';
            $summit_ltype = 'sr';
            $hut_join = 'lr.MainAssociation';
            $hut_ltype = 'hr';
            $parking_join = 'lr.MainAssociation';
            $parking_ltype = 'pr';
        }

        // join with summit tables only if needed 
        if (isset($joins['join_summit']))
        {
            Summit::buildSummitPagerConditions($q, $joins, false, false, $summit_join, $summit_ltype);
        }
        
        // join with hut tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, false, false, $hut_join, $hut_ltype);
        }
        
        // join with parking tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, $parking_join, $parking_ltype);
        }

        // join with site tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, false, false, $site_join, $site_ltype);
        }

        // join with product tables only if needed 
        if (isset($joins['join_product']))
        {
            Product::buildProductPagerConditions($q, $joins, false, false, 'm.MainGeoassociations', '');
        }

        // join with user tables only if needed 
        if (isset($joins['join_user']))
        {
            User::buildUserPagerConditions($q, $joins, false, false, $user_join, $user_ltype);
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, '');
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
            case 'anam': return $mi . '.search_name';
            case 'atyp': return 'm.area_type';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.geom_wkt', 'm.area_type');
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
    }
    
    public static function getAssociatedAreasData($associated_areas)
    {
        $areas = Document::fetchAdditionalFieldsFor(
                                            $associated_areas,
                                            'Area',
                                            array('area_type'));

        return c2cTools::sortArrayByName($areas);
    }

    // sort the areas associated to a list of documents via the 'geoassociations' entry
    public static function sortAssociatedAreas(&$documents)
    {
        // we want to sort ranges first, then admin limits and countries
        // good thing is, corresponding association types (dr, dd, dc, dv) are
        // already alphabetically ordered
        foreach ($documents as &$document)
        {
            usort($document['geoassociations'], function ($a, $b) {
                return ($a['type'] < $b['type']) ? 1 : -1;
            });
        }
    }

}
