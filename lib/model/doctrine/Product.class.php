<?php

class Product extends BaseProduct
{
    public static function filterSetElevation($value)
    {   
        return self::returnNaturalIntOrNull($value);
    }
    
    public static function filterSetProduct_type($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetProduct_type($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetUrl($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function buildProductListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
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
            $join_i18n = 'product_i18n';
        }
        else
        {
            $m = 'f';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'product';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'products'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'products', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'farnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'fi.search_name'), ($is_module ? array('fnam', 'name') : 'fnam'), array($join_idi18n, $join_i18n), 'Product');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'falt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'product_type'), 'ftyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'fi.culture', 'fcult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'f', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_farticle']))
            {
                $joins['join_product'] = true;
                if (!$is_module)
                {
                    $joins['post_product'] = true;
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
            $joins['join_product'] = true;
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'products');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('fnam'), array('product_i18n', 'join_product'));
        
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
        self::buildAreaCriteria($criteria, $params_list, 'p');

        // product criteria
        $has_name = Product::buildProductListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($criteria, $params_list, false, 'main_id', 'q');
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
    
    public static function buildProductPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'product';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
        }
        else
        {
            $m = 'lf';
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
                    $q->leftJoin($m . '.' . $linked . 'Product f');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'ProductI18n fi');
        }
        
        if (isset($joins['join_farticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'f', false, $linked_join, 'fc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with product tables only if needed 
        if (isset($joins['join_product']))
        {
            Product::buildProductPagerConditions($q, $joins, true);
        }

        // join with parkings tables only if needed 
        if (isset($joins['join_hut']))
        {
            $joins['join_parking'] = true;
            $joins['post_parking'] = true;
        }
        
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, 'm.associations', 'pf', 'q');
        
            // join with huts tables only if needed 
            if (isset($joins['join_hut']))
            {
                Hut::buildHutPagerConditions($q, $joins, false, true, 'lp.LinkedLinkedAssociation', 'ph');
            }
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'fi');
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
            case 'fnam': return $mi . '.search_name';
            case 'falt': return 'm.elevation';
            case 'ftyp': return 'm.product_type';
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
            $data_fields_list = array('m.elevation', 'm.product_type', 'm.lon', 'm.lat', 'm.url');
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

    public static function listFromRegion($region_id, $buffer, $table = 'products', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
