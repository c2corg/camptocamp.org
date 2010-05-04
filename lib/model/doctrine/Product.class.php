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
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $associations = array();
            
            $conditions = self::joinOnMultiRegions($q, $conditions);

            // join with parkings tables only if needed 
            if (isset($conditions['join_parking_id']) || isset($conditions['join_parking']))
            {
                $q->leftJoin('m.associations l');
                if (isset($conditions['join_parking_id']))
                {
                    unset($conditions['join_parking_id']);
                }
                
                if (isset($conditions['join_parking']))
                {
                    $q->leftJoin('l.Parking q')
                      ->addWhere("l.type = 'pf'");
                    unset($conditions['join_parking']);

                    if (isset($conditions['join_parking_i18n']))
                    {
                        $q->leftJoin('q.ParkingI18n pi');
                        unset($conditions['join_parking_i18n']);
                    }
                }
            }

            if (!empty($associations))
            {
                $q->addWhere("l.type IN ('" . implode("', '", $associations) . "')");
            }
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
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
