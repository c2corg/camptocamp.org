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
        return self::returnNullIfEmpty($value * 1000);
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
    public static function listLatest($max_items, $langs, $ranges, $activities)
    {
        $q = Doctrine_Query::create();
        $q->select('o.id, n.culture, n.name, n.search_name, o.date, o.activities, o.max_elevation, g.linked_id, a.area_type, ai.name, ai.culture')
          ->from('Outing o')
          ->leftJoin('o.OutingI18n n')
          ->leftJoin('o.geoassociations g')
          ->leftJoin('g.AreaI18n ai')
          ->leftJoin('ai.Area a')
          ->addWhere('o.redirects_to IS NULL')
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
            $q->leftJoin('o.geoassociations g2')
              ->addWhere(self::getAreasQueryString($ranges, 'g2'), $ranges);
        }

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    public static function fetchAdditionalFields($objects, $images_count = false)
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
                          ->select('m.activities, m.date, m.geom_wkt, v.version, hm.user_id, u.topo_name')
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

        if ($images_count)
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

    public static function browse($sort, $criteria, $show_conditions = false)
    {
        $pager = self::createPager('Outing', self::buildFieldsList($show_conditions), $sort);
        $q = $pager->getQuery();

        self::joinOnRegions($q);

        $q->leftJoin('m.versions v')
          ->leftJoin('v.history_metadata hm')
          ->leftJoin('hm.user_private_data u')
          ->addWhere('v.version = 1');

        if (!empty($criteria))
        {
            $conditions = $criteria[0];

            $conditions = self::joinOnMultiRegions($q, $conditions);
            
            if (isset($conditions['join_route']) || 
                isset($conditions['join_summit']) ||
                isset($conditions['join_hut']) ||
                isset($conditions['join_parking']))
            {
                $q->leftJoin('m.associations l');
            }

            if (isset($conditions['join_route']) || 
                isset($conditions['join_summit']) ||
                isset($conditions['join_oversummit']) ||
                isset($conditions['join_hut']) ||
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

            if (isset($conditions['join_summit']) || isset($conditions['join_oversummit']))
            {
                unset($conditions['join_summit']);
                $q->leftJoin('r.associations l2')
                  ->leftJoin('l2.Summit s')
                  ->addWhere("l2.type = 'sr'");
                
                if (isset($conditions['join_summit_i18n']))
                {
                    unset($conditions['join_summit_i18n']);
                    $q->leftJoin('s.SummitI18n si');
                }
            }
            
            if (isset($conditions['join_oversummit']))
            {
                unset($conditions['join_oversummit']);
                $q->leftJoin('s.associations l22')
                  ->leftJoin('l22.Summit s2')
                  ->addWhere("l22.type = 'ss' AND s2.elevation > s.elevation");
            }
            
            if (isset($conditions['join_hut']))
            {
                unset($conditions['join_hut']);
                $q->leftJoin('r.associations l3')
                  ->leftJoin('l3.Hut h')
                  ->addWhere("l3.type = 'hr'");

                if (isset($conditions['join_hut_i18n']))
                {
                    unset($conditions['join_hut_i18n']);
                    $q->leftJoin('h.HutI18n hi');
                }
            }
            
            if (isset($conditions['join_parking']))
            {
                unset($conditions['join_parking']);
                $q->leftJoin('r.associations l4')
                  ->leftJoin('l4.Parking p')
                  ->addWhere("l4.type = 'pr'");

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

            if (isset($conditions['join_site']))
            {
                unset($conditions['join_site']);
                $q->leftJoin('m.associations l5');
            }

            $join_id = 0;
            while(isset($conditions['join_user']) && ($join_id <= 4))
            {
                $join_id += 1;
                unset($conditions['join_user']);
                $q->leftJoin("m.associations u$join_id");
            }

            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            self::filterOnLanguages($q);
            self::filterOnActivities($q);
            self::filterOnRegions($q);
            
            if ($show_conditions)
            {
                $default_max_age = sfConfig::get('mod_outings_recent_conditions_limit', '15D');
                $q->addWhere("age(date) < interval '$default_max_age'");
            }
        }
        else if ($show_conditions)
        {
            $default_max_age = sfConfig::get('mod_outings_recent_conditions_limit', '15D');
            $q->addWhere("age(date) < interval '$default_max_age'");
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }

    protected static function buildFieldsList($show_conditions = false)
    {
        $outings_fields_list = array('m.activities', 'm.date', 'm.height_diff_up',
                                    'v.version', 'hm.user_id', 'u.topo_name', 
                                    'm.geom_wkt', 'm.conditions_status', 'm.max_elevation');
        
        $conditions_fields_list = ($show_conditions) ? array('m.up_snow_elevation', 'm.down_snow_elevation', 'm.access_elevation',
                                            'mi.conditions', 'mi.conditions_levels', 'mi.weather')
                                                     : array();
        
        return array_merge(parent::buildFieldsList(),
                           parent::buildGeoFieldsList(),
                           $outings_fields_list,
                           $conditions_fields_list);
    }

    public static function retrieveConditions($days)
    {
        $pager = new sfDoctrinePager('Outing', 10);
        $q = $pager->getQuery();
        $q->select('m.date, m.activities, m.conditions_status, m.up_snow_elevation, m.down_snow_elevation, ' .
                   'm.access_elevation, mi.name, mi.search_name, mi.conditions, mi.conditions_levels, mi.weather, mi.culture' .
                   'g.type, g.linked_id, ai.name, m.max_elevation')
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

        if (count($ro_associations) == 0) return;
        
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
                                        'rock_free_rating',
                                        'ice_rating',
                                        'mixed_rating',
                                        'aid_rating',
                                        'equipment_rating');
        $route_hiking_fields = array ('hiking_rating');
        $route_fields = array_merge($route_ski_fields, $route_climbing_fields, $route_hiking_fields);
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
        }

        return $outings;
    }
}
