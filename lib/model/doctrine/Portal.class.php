<?php

class Portal extends BasePortal
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Portal', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

        $conditions = array();
        $all = false;
        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            if (isset($joins['all']))
            {
                $all = $conditions['all'];
                unset($conditions['all']);
            }
        }
        
        if (!$all && !empty($conditions))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            self::joinOnMultiRegions($q, $conditions);

            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
    /*    elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('portals')
        {
            self::filterOnRegions($q);
        }  */
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
                           array('m.activities', 'm.lon', 'm.lat'));
    }

    public static function listFromRegion($region_id, $buffer, $table = 'portals', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }

    public static function convertForumsIds($forums_ids)
    {
        $forums_list = array();
        $forums_ids = str_replace(' ', '', $forums_ids);
        if (empty($forums_ids))
        {
            return $forums_list;
        }
        
        $temp_list = explode('|', $forums_ids);
        foreach ($temp_list as $temp)
        {
            $temp = explode(':', $temp);
            if (count($temp >= 2))
            {
                $forums_list[$temp[0]] = explode(',', $temp[1]);
            }
        }
        return $forums_list;
    }

    public static function getLocalPortals(&$portal_list, $areas)
    {
        $areas_ids = array();
        foreach ($areas as $area)
        {
            $areas_ids[] = $area['id'];
        }
        
        $portal_ids = sfConfig::get('app_portals_id');
        foreach ($portal_ids as $id)
        {
            $def = sfConfig::get('app_portals_' . $id);
            if (isset($def['areas_only']) && count(array_uintersect($areas_ids, $def['areas_only'], 'strcmp')))
            {
                $portal_list[] = $id;
            }
        }
    }
}
