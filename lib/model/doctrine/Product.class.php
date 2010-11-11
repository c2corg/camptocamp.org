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
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'f';
            $join = 'join_product';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'products', $join_id, false, $params_list);
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
            self::buildConditionItem($conditions, $values, 'String', 'fi.search_name', ($is_module ? array('fnam', 'name') : 'fnam'), 'join_product_i18n', true, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'falt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'f', 'product_type'), 'tpty', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'fi.culture', 'fcult', 'join_product_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lfc.linked_id', 'ftags', 'join_ftag_id', false, $params_list);
        }
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
        self::buildAreaCriteria($conditions, $values, $params_list);

        // parking criteria
        Product::buildProductListCriteria(&$conditions, &$values, $params_list, true);

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.linked_id');
        
        // image criteria
        Image::buildImageListCriteria(&$conditions, &$values, $params_list, false);

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
        elseif (!$all && c2cPersonalization::getInstance()->isMainFilterSwitchOn())
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
            
            if (isset($conditions['join_product_id']))
            {
                unset($conditions['join_product_id']);
                
                return;
            }
            else
            {
                $q->addWhere($m . "type = '$ltype'");
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
            Parking::buildParkingPagerConditions($q, $conditions, false, false, 'm.associations', 'pf');
        
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
