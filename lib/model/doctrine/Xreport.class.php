<?php

class Xreport extends BaseXreport
{
    public static function filterSetDate($value)
    {
        $year  = $value['year'];
        $month = (strlen($value['month']) == 2) ? $value['month'] : ('0' . $value['month']);
        $day   = (strlen($value['day']) == 2) ? $value['day'] : ('0' . $value['day']);
        
        return "$year-$month-$day";
    }
    
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetElevation($value)
    {   
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetNb_participants($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetNb_impacted($value)
    {   
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetSeverity($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetEvent_type($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetEvent_type($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetAuthor_status($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetActivity_rate($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetNb_outings($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetAutonomy($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetAge($value)
    {   
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetGender($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetPrevious_injuries($value)
    {   
        return self::returnPosIntOrNull($value);
    }

    public static function buildXreportListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
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
            $join_i18n = 'xreport_i18n';
        }
        else
        {
            $m = 'f';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'xreport';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'xreports'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'xreports', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'x', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Date', 'date', array('date', 'xdate'), $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'xarnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'xi.search_name'), ($is_module ? array('xnam', 'name') : 'xnam'), array($join_idi18n, $join_i18n), 'Xreport');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'xalt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'event_type'), 'xtyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.severity', 'xsev', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bool', $m . '.rescue', 'xres', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.nb_participants', 'xpar', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.nb_impacted', 'ximp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'xi.culture', 'xcult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'x', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_xarticle']))
            {
                $joins['join_xreport'] = true;
                if (!$is_module)
                {
                    $joins['post_xreport'] = true;
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
            $joins['join_xreport'] = true;
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'xreports');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('fnam'), array('xreport_i18n', 'join_xreport'));
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = $conditions;
            $criteria[1] = $values;
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'x');

        // xreport criteria
        $has_name = Xreport::buildXreportListCriteria($criteria, $params_list, true);
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
    
    public static function buildXreportPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'xreport';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
        }
        else
        {
            $m = 'lx';
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
                    $q->leftJoin($m . '.' . $linked . 'Xreport x');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'XreportI18n xi');
        }
        
        if (isset($joins['join_xarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'x', false, $linked_join, 'xc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with xreport tables only if needed 
        if (isset($joins['join_xreport']))
        {
            Xreport::buildXreportPagerConditions($q, $joins, true);
        }

        // join with outing tables only if needed 
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, false, 'm.associations', 'ox');
        }

        // join with route tables only if needed 
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, false, 'm.associations', 'rx');
        }

        // join with site tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, false, false, 'm.associations', 'tx');
        }

        // join with user tables only if needed 
        if (isset($joins['join_user']))
        {
            User::buildUserPagerConditions($q, $joins, false, false, 'm.associations', 'ux');
        }
        
        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'xi');
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
            case 'xnam': return $mi . '.search_name';
            case 'xalt': return 'm.elevation';
            case 'act':  return 'm.activities';
            case 'date': return array('m.date', 'm.id');
            case 'xpar': return 'm.nb_participants';
            case 'ximp': return 'm.nb_impacted';
            case 'xsev': return 'm.severity';
            case 'xres': return 'm.rescue';
            case 'xtyp': return 'm.event_type';
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
            $data_fields_list = array('m.elevation', 'm.activities', 'm.date', 'm.nb_participants', 'm.nb_impacted', 'm.severity', 'm.rescue', 'm.event_type', 'm.lon', 'm.lat');
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

    public static function listFromRegion($region_id, $buffer, $table = 'xreports', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
