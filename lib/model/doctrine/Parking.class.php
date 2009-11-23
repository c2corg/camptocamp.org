<?php
/**
 * $Id: Parking.class.php 2529 2007-12-19 14:07:18Z alex $
 */
class Parking extends BaseParking
{
    public static function getAssociatedParkingsData($associated_docs)
    {
        $parkings = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_parking')),
                                            'Parking',
                                            array('lowest_elevation', 'public_transportation_rating', 'public_transportation_types'));

        return $parkings;
    }

    public static function getAssociatedParkings($docs, $type)
    {
        sfLoader::loadHelpers(array('Field'));
        
        $parkings = Document::getAssociatedDocuments($docs, $type, false,
                                                     array('elevation', 'lowest_elevation', 'public_transportation_rating', 'public_transportation_types'),
                                                     array('name'));

        $parkings_string = array();
        foreach ($parkings as $id => $doc)
        {
            $name = ucfirst($doc['name']);
            $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']);
            $parking = link_to($name, $url);
            if (isset($doc['lowest_elevation']) && is_scalar($doc['lowest_elevation']) && $doc['lowest_elevation'] != $doc['elevation'])
            {
                $parking .= '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
            }
            else if (isset($doc['elevation']) && is_scalar($doc['elevation']))
            {
                $parking .= '&nbsp; ' . $doc['elevation'] . __('meters');
            }
            if (isset($doc['public_transportation_types']))
            {
                $parking .= field_pt_picto_if_set($doc, true, true, ' - ');
            }
            
            $parkings_string[$id] = $parking;
        }
        
        return $parkings_string;
    }
    
    public static function filterSetElevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetLowest_elevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetPublic_transportation_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetPublic_transportation_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetPublic_transportation_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSnow_clearance_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Parking', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

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
            // "filter on regions" is the only filter activated for summits:
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
                           array('m.elevation', 'm.lowest_elevation', 'm.public_transportation_rating', 'm.public_transportation_types', 'm.snow_clearance_rating', 'm.lon', 'm.lat'));
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
