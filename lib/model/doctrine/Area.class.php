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

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Area', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
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
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
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
                           array('m.geom_wkt', 'm.area_type'));
    }
    
    public static function getAssociatedAreasData($associated_areas)
    {
        $areas = Document::fetchAdditionalFieldsFor(
                                            $associated_areas,
                                            'Area',
                                            array('area_type'));

        return c2cTools::sortArrayByName($areas);
    }

}
