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

    public static function filterSetHeight_diff_up($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetHeight_diff_down($value)
    {
        return self::returnNullIfEmpty($value);
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

    public static function filterGetOuting_length($value)
    {
        return round($value / 1000, 1);
    }

    /**
     * Retrieves a list of outings ordered by effective outing date (more recent first).
     */
    public static function listLatest($max_items, $langs, $ranges, $activities)
    {
        $q = Doctrine_Query::create();
        $q->select('o.id, n.culture, n.name, o.date, o.activities')
          ->from('Outing o')
          ->leftJoin('o.OutingI18n n')
          ->leftJoin('o.geoassociations g')
          ->leftJoin('g.AreaI18n ai')
          ->leftJoin('ai.Area a')
          ->orderBy('o.date DESC, o.id DESC')
          ->limit($max_items);


        if (!empty($activities))
        {
            $q->addWhere(self::getActivitiesQueryString($activities), $activities);
        }

        if (!empty($langs))
        {
            $q->addWhere(self::getLanguagesQueryString($langs, 'n'), $langs);
        }

        if (!empty($ranges))
        {
            $q->addWhere(self::getAreasQueryString($ranges, 'g'), $ranges);
        }

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    public static function fetchAdditionalFields($objects)
    {
        if (!count($objects)) 
        {   
            return array();
        }
    
        $ids = array();
        $q = array();

        // build ids list
        foreach ($objects as $object)
        {
            $ids[] = $object['id'];
            $q[] = '?';
        }

        // db request fetching array with all requested fields
        $results = Doctrine_Query::create()
                          ->select('m.activities, m.date, v.version, hm.user_id, u.name_to_use, u.private_name, u.username, u.login_name')
                          ->from('Outing m')
                          ->leftJoin('m.versions v')
                          ->leftJoin('v.history_metadata hm')
                          ->leftJoin('hm.user_private_data u')
                          ->where('m.id IN ( '. implode(', ', $q) .' )', $ids)
                          ->addWhere('v.version = 1')
                          ->orderBy('m.date DESC')
                          ->execute(array(), Doctrine::FETCH_ARRAY);
        
        $out = array();
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
        return $out;
    }

    public static function browse($sort, $criteria)
    {
        $pager = self::createPager('Outing', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();

        self::joinOnRegions($q);

        $q->leftJoin('m.versions v')
          ->leftJoin('v.history_metadata hm')
          ->leftJoin('hm.user_private_data u')
          ->addWhere('v.version = 1');

        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            $associations = array();

            if (isset($conditions['join_route']) || 
                isset($conditions['join_summit']) ||
                isset($conditions['join_user']) ||
                isset($conditions['join_parking']))
            {
                $q->leftJoin('m.associations l');
            }

            if (isset($conditions['join_route']) || 
                isset($conditions['join_summit']) ||
                isset($conditions['join_parking']))
            {
               $q->leftJoin('l.Route r')
                  ->addWhere("l.type = 'ro'");
            }

            if (isset($conditions['join_route_i18n']))
            {
                $q->leftJoin('r.RouteI18n ri');
                unset($conditions['join_route_i18n']);
            }

            if (isset($conditions['join_summit']) ||
                isset($conditions['join_parking']))
            {
                $q->leftJoin('r.associations l2');
            }

            if (isset($conditions['join_summit']))
            {
                unset($conditions['join_summit']);
                $associations[] = 'sr';
                $q->leftJoin('l2.Summit s');
                
                if (isset($conditions['join_summit_i18n']))
                {
                    unset($conditions['join_summit_i18n']);
                    $q->leftJoin('s.SummitI18n si');
                }
            }
            
            if (isset($conditions['join_parking']))
            {
                unset($conditions['join_parking']);
                $associations[] = 'pr';
                $q->leftJoin('l2.Parking p');

                if (isset($conditions['join_parking_i18n']))
                {
                    unset($conditions['join_parking_i18n']);
                    $q->leftJoin('p.ParkingI18n pi');
                }
            }

            if (isset($conditions['join_route']))
            {
                unset($conditions['join_route']);
            }

            if (isset($conditions['join_user']))
            {
                unset($conditions['join_user']);
            }

            if (!empty($associations))
            {
                $q->addWhere("l2.type IN ('" . implode("', '", $associations) . "')");
            }
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::isMainFilterSwitchOn())
        {
            self::filterOnLanguages($q);
            self::filterOnActivities($q);
            self::filterOnRegions($q);
        }

        return $pager;
    }

    protected static function buildFieldsList()
    {
        return array_merge(parent::buildFieldsList(),
                           parent::buildGeoFieldsList(),
                           array('m.activities', 'm.date', 'm.height_diff_up',
                                 'v.version', 'hm.user_id', 'u.name_to_use', 
                                 'u.private_name', 'u.username', 'u.login_name',
                                 'm.geom_wkt'));
    }

    public static function retrieveConditions($days)
    {
        $pager = new sfDoctrinePager('Outing', 10);
        $q = $pager->getQuery();
        $q->select('m.date, m.activities, m.conditions_status, m.up_snow_elevation, m.down_snow_elevation, ' .
                   'm.access_elevation, mi.name, mi.conditions, mi.conditions_levels, mi.weather, mi.culture' .
                   'g.type, g.linked_id, ai.name')
          ->from('Outing m')
          ->leftJoin('m.OutingI18n mi')
          ->where("age(date) < interval '$days days'")
          ->orderBy('m.date DESC, m.id DESC');

        self::joinOnRegions($q);
        
        // applying user filters
        if (c2cPersonalization::isMainFilterSwitchOn())
        {
            self::filterOnLanguages($q);
            self::filterOnActivities($q);
            self::filterOnRegions($q);
        }

        return $pager;
    }
}
