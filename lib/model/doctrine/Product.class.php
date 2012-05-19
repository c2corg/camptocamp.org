<?php

class Product extends BaseProduct
{
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

    public static function buildProductListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'p';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'f';
            $m2 = $m;
            $join = 'join_product';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'Id', $mid, 'products', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'Around', $m2 . '.geom', 'farnd', $join, false, $params_list);
            
            $has_name = self::buildConditionItem($conditions, $values, 'String', array('fi.search_name', $mid), ($is_module ? array('fnam', 'name') : 'fnam'), array($join, 'join_product_i18n'), false, $params_list, 'Product');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'falt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'product_type'), 'ftyp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'fi.culture', 'fcult', 'join_product_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Id', 'lfc.linked_id', 'ftags', 'join_ftag_id', false, $params_list);
        }
        
        return null;
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'fcult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list, 'p');

        // product criteria
        $has_name = Product::buildProductListCriteria($conditions, $values, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($conditions, $values, $params_list, false, 'lp.main_id', 'q');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // hut criteria
        $has_name = Hut::buildHutListCriteria($conditions, $values, $params_list, false, 'lh.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // article criteria
        $has_name = Article::buildArticleListCriteria($conditions, $values, $params_list, false, 'f', 'lc.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($conditions, $values, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Product', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('products'))
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildProductPagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        if ($is_module)
        {
            $m = 'm.';
            $linked = '';
            $linked2 = '';
            $main = $m . 'associations';
        }
        else
        {
            $m = 'lf.';
            if ($is_linked)
            {
                $linked = 'Linked';
                $linked2 = '';
                $main = $m . 'MainMainAssociation';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
                $main = $m . 'MainAssociation';
            }
                
            $q->leftJoin($first_join . ' lf');
            
            if (!isset($conditions['join_product_id']) || isset($conditions['join_product_id_has']))
            {
                $q->addWhere($m . "type = '$ltype'");
                if (isset($conditions['join_product_id_has']))
                {
                    unset($conditions['join_product_id_has']);
                }
            }
            if (isset($conditions['join_product_id']))
            {
                unset($conditions['join_product_id']);
            }
            
            if (isset($conditions['join_product']))
            {
                $q->leftJoin($m . $linked . 'Product f');
                unset($conditions['join_product']);
            }
        }

        if (isset($conditions['join_product_i18n']))
        {
            $q->leftJoin($m . $linked . 'ProductI18n fi');
            unset($conditions['join_product_i18n']);
        }
        
        if (isset($conditions['join_ftag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation lfc");
            unset($conditions['join_ftag_id']);
            
            if (isset($conditions['join_ftag_id_has']))
            {
                $q->addWhere("lfc.type = 'fc'");
                unset($conditions['join_ftag_id_has']);
            }
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        // join with parking tables only if needed 
        if (   isset($conditions['join_product_i18n'])
            || isset($conditions['join_ftag_id'])
        )
        {
            Product::buildProductPagerConditions($q, $conditions, true);
        }

        // join with parkings tables only if needed 
        if (   isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
            || isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            Parking::buildParkingPagerConditions($q, $conditions, false, false, 'm.associations', 'pf', 'q');
        
            // join with huts tables only if needed 
            if (   isset($conditions['join_hut_id'])
                || isset($conditions['join_hut'])
                || isset($conditions['join_hut_i18n'])
                || isset($conditions['join_htag_id'])
                || isset($conditions['join_hbook_id'])
                || isset($conditions['join_hbtag_id'])
            )
            {
                Hut::buildHutPagerConditions($q, $conditions, false, true, 'lp.LinkedLinkedAssociation', 'ph');
            }
        }

        // join with article tables only if needed 
        if (   isset($conditions['join_article_id'])
            || isset($conditions['join_article'])
            || isset($conditions['join_article_i18n'])
        )
        {
            Article::buildArticlePagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'fc');
        }

        // join with image tables only if needed 
        if (   isset($conditions['join_image_id'])
            || isset($conditions['join_image'])
            || isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $conditions, false, 'fi');
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
                           array('m.elevation', 'm.product_type', 'm.lon', 'm.lat', 'm.url'));
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
