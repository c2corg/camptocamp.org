<?php
/**
 * Model for maps
 * $Id: Map.class.php 2535 2007-12-19 18:26:27Z alex $
 */

class Map extends BaseMap
{
    public static function getAssociatedMapsData($associated_docs)
    {
        if (!count($associated_docs)) 
        {
            return array();
        }
        
        $maps = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_map')),
                                            'Map',
                                            array('code', 'editor'));

        $editor_list = sfConfig::get('app_maps_editors');
        foreach ($maps as $key => $map)
        {
            $name = $editor_list[$map['editor']] . ' ' . $map['code'] . ' ' . $map['name'];
            $maps[$key]['name'] = $name;
        }
        
        return c2cTools::sortArrayByName($maps);
    }

    public static function filterSetEditor($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetScale($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetCode($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function browse($sort, $criteria)
    {   
        $pager = self::createPager('Map', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $conditions = $criteria[0];
            
            $conditions = self::joinOnMultiRegions($q, $conditions);
            
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
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
                           array('m.code', 'm.scale', 'm.editor'));
    }
}
