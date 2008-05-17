<?php
/**
 * Model for areas
 * $Id: Area.class.php 2529 2007-12-19 14:07:18Z alex $
 */

class Area extends BaseArea
{

    // returns an array of regions 
    public static function getRegions($area_type, $user_prefered_langs)
    {
        $filter = !empty($area_type);

        $select = 'a.id, i.name';
        if (!$filter)
        {
            $select .= ', a.area_type';
        }

        $q = Doctrine_Query::create()
                           ->select($select)
                           ->from('Area a')
                           ->leftJoin('a.AreaI18n i');
        if ($filter)
        {
            $q->where('a.area_type = ?', array($area_type));
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

    public static function browse($sort, $criteria)
    {   
        $pager = self::createPager('Area', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
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
}
