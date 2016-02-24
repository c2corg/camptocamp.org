<?php
/**
 * Model for summits
 * $Id: Summit.class.php 2529 2007-12-19 14:07:18Z alex $
 */

class Summit extends BaseSummit
{
    public static function getAssociatedSummitsData($associated_docs)
    {
        $summits = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_summit')),
                                            'Summit',
                                            array('elevation'));

        return c2cTools::sortArrayByName($summits);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetSummit_type($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMaps_info($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function buildSummitListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
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
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'summit_i18n';
        }
        else
        {
            $m = 's';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'summit';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, 'id', $join_id);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', 'ls.main_id', 'summits', 'summit_id');
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', 'lss.linked_id', 'subsummits', 'subsummit_id');
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'summits', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Istring', 'maps_info', 'mapinfo', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Lstring', 'si.description', 'descl', $join_i18n);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'sarnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'si.search_name'), ($is_module ? array('snam', 'name') : 'snam'), array($join_idi18n, $join_i18n), 'Summit');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'salt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.summit_type', 'styp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'si.culture', 'scult', $join_i18n);
            
            // book criteria
            $nb_name = Book::buildBookListCriteria($criteria, $params_list, false, 's', 'main_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 's', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (   isset($criteria[2]['join_sbook'])
                || isset($criteria[2]['join_sarticle'])
            )
            {
                $joins['join_summit'] = true;
                if (!$is_module)
                {
                    $joins['post_summit'] = true;
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
            $joins['join_summit'] = true;
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'summits', 'ract');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('snam'), array('summit_i18n', 'join_summit'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 's');

        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // summit criteria
        $has_name = Summit::buildSummitListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($criteria, $params_list, false, 'linked_id');
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array('r', 'r', 'activities'), 'act', array('route', 'join_route'));
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
        self::joinOnRegions($q);
    }
    
    public static function buildSummitPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'summit';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
            
            if (isset($joins['summit_id']))
            {
                $q->leftJoin($main_join . ' ls');
                
                if (isset($joins['summit_id_has']))
                {
                    $q->addWhere("ls.type = 'ss'");
                }
            }
            
            if (isset($joins['subsummit_id']))
            {
                $q->leftJoin($linked_join . ' lss');
                
                if (isset($joins['subsummit_id_has']))
                {
                    $q->addWhere("lss.type = 'ss'");
                }
            }
        }
        else
        {
            $m = 'ls';
            if ($is_linked)
            {
                $linked = 'Linked';
                $main_join = $m . '.MainMainAssociation';
                $linked_join = $m . '.LinkedAssociation';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
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
                    $q->leftJoin($m . '.' . $linked . 'Summit s');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'SummitI18n si');
        }
        if (isset($joins['join_sbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 's', false, $main_join, 'bs');
        }
        
        
        if (isset($joins['join_sarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 's', false, $linked_join, 'sc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with summit / book / article tables only if needed 
        if (isset($joins['join_summit']))
        {
            Summit::buildSummitPagerConditions($q, $joins, true);
        }

        // join with route tables only if needed 
        if (   isset($joins['join_parking'])
            || isset($joins['join_hut'])
            || isset($joins['join_outing'])
            || isset($joins['join_user'])
        )
        {
            $joins['join_route'] = true;
            $joins['post_route'] = true;
        }
        
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'sr');
            
            // join with hut tables only if needed 
            if (isset($joins['join_hut']))
            {
                Hut::buildHutPagerConditions($q, $joins, false, false, 'lr.MainMainAssociation', 'hr');
            }
            
            // join with parking tables only if needed 
            if (isset($joins['join_parking']))
            {
                Parking::buildParkingPagerConditions($q, $joins, false, false, 'lr.MainMainAssociation', 'pr');
            }
            
            if (isset($joins['join_user']))
            {
                $joins['join_outing'] = true;
                $joins['post_outing'] = true;
            }
            
            if (isset($joins['join_outing']))
            {
                Outing::buildOutingPagerConditions($q, $joins, false, true, 'lr.LinkedAssociation', 'ro');
                
                if (isset($joins['join_user']))
                {
                    User::buildUserPagerConditions($q, $joins, false, false, 'lo.MainMainAssociation', 'uo');
                }
            }
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'si');
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
            case 'snam': return $mi . '.search_name';
            case 'salt': return 'm.elevation';
            case 'styp': return 'm.summit_type';
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
            $data_fields_list = array('m.elevation', 'm.summit_type', 'm.lon', 'm.lat');
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

    public static function listFromRegion($region_id, $buffer, $table = 'summits', $where = '') 
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }

    // sub summits are defined by the association order
    public static function getSubSummits($id)
    {
        $query = 'SELECT m.id FROM summits m WHERE m.id IN '
               . '(SELECT a.linked_id FROM app_documents_associations a WHERE a.main_id = ? AND type = ?) '
               . 'ORDER BY m.id ASC';

        $results = sfDoctrine::connection()
                    ->standaloneQuery($query, array($id, 'ss'))
                    ->fetchAll();
        return $results;
    }
}
