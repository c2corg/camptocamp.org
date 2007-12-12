<?php
/**
 * Model for areas
 * $Id: Area.class.php 1842 2007-09-26 12:11:42Z fvanderbiest $
 */

class Area extends BaseArea
{

    // returns an array of regions 
    public static function getRegions($area_type, $user_prefered_langs)
    {
        $results = Doctrine_Query::create()
                             ->select('a.id, i.name')
                             ->from('Area a')
                             ->leftJoin('a.AreaI18n i')
                             ->where('a.area_type = ?', array($area_type))
                             ->execute(array(), Doctrine::FETCH_ARRAY);
                             
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
            $out[$result['id']] = $best_name;
        }
        return $out;
    }
    
}
