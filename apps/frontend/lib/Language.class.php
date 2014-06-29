<?php
/**
 * Languages names tools
 * @version $Id: Language.class.php 2532 2007-12-19 16:01:01Z alex $
 */

class Language
{
    /**
     * Get all SF application languages names.
     * @return array
     */
    public static function getAll()
    {
        return sfConfig::get('app_languages_c2c');
    }
    
    /**
     * PunBB uses different languages names. Thus a matching is required with SF.
     * @param string short version of the language
     * @return string PunBB equivalent language name
     */
    public static function translateForPunBB($language_code)
    {
        $punBBLangues = self::getPunBBLanguages();
        return $punBBLangues[$language_code];
    }
    
    /**
     * Returns PunBB languages names.
     * @return array
     */
    public static function getPunBBLanguages()
    {
        return sfConfig::get('app_languages_punbb');
    }

    // gets the best lang for each item + for the associated range(s) !
    public static function parseListItems($array, $modelName, $count_images = true)
    {
        $langs = sfContext::getInstance()->getUser()->getPreferedLanguageList();

        $parsed_array = self::getTheBest($array, $modelName, $langs);
        // once we have selected the best lang to display this item name, 
        // we build search string for comments (eg: 333_fr)
        $_str = array();
        
        // extract independant country list
        $countries = array();
    
        foreach($parsed_array as $key => $item)
        {
            // build ids strings to get nb of comments
            $_str[$item['id']] = $item['id'] . '_' . $item[$modelName.'I18n'][0]['culture'];
            // extract best name for associated regions
            if (isset($item['geoassociations']))
            {
                $geo_associations = $item['geoassociations'];
                $parsed_array[$key]['geoassociations'] = self::getTheBest($geo_associations, 
                                                                          'Area', $langs, 'linked_id');
                if (count($geo_associations) > 1)
                {
                    foreach ($geo_associations as $geo_association)
                    {
                        if ($geo_association['type'] == 'dc')
                        {
                            $countries[$geo_association['linked_id']] = true;
                        }
                    }
                }
            }
        }
 
        // if all docs are in the same country, country data are removed from list
        if (count($countries) == 1)
        {
            $country_id = key($countries);
            foreach($parsed_array as $key => $item)
            {
                if (isset($item['geoassociations']))
                {
                    if (isset($item['geoassociations'][$country_id]))
                    {
                        unset($parsed_array[$key]['geoassociations'][$country_id]);
                    }
                }
            }
        }
        
        // get nb of comments for all items
        $pun_msgs = PunbbComm::getNbComments($_str);
        // merge this info into $parsed_array
        foreach ($pun_msgs as $pun_msg)
        {
            $id = substr($pun_msg['subject'], 0, strpos($pun_msg['subject'], '_'));
            foreach($parsed_array as $key => $item)
            {
                if ($key == $id)
                {
                    $parsed_array[$key]['nb_comments'] = $pun_msg['nb_comments'];
                }
            }
        }
        
        if ($count_images && $modelName != 'Image')
        {
            // Count all images linked
            $image_links = Association::countAllLinked(array_keys($_str), c2cTools::Model2Letter($modelName).'i');
            // merge this info into $parsed_array
            foreach ($image_links as $image_link)
            {
                $main_id = $image_link['main_id'];
                if (isset($parsed_array[$main_id]['nb_images']))
                {
                    $parsed_array[$main_id]['nb_images']++;
                }
                else
                {
                    $parsed_array[$main_id]['nb_images'] = 1;
                }
            }
        }
    
        if ($modelName == 'Route')
        //  TODO: when doing searches on documents, there might be routes in the results,
        // yet the model name is not "route", and we need to add best summit name to included routes in results.
        {
            // find highest associated summit
            foreach($parsed_array as $key => $item)
            {
                $parsed_array[$key]['associations'] = self::getTheHighest($item['associations'], 'Summit');
                // once this is done, find his best name
                $parsed_array[$key]['associations'][0]['Summit'] = self::getTheBest($parsed_array[$key]['associations'][0]['Summit'], 'Summit', $langs, '', false);
            }
        }
        return $parsed_array;
    }
    
    public static function getTheHighest($array, $model, $key = 'elevation')
    {
        // init base vars
        $ref_ele = 0;
        $out = $array[0];
        if (count($array) > 1)
        {
            // parse our array
            foreach($array as $item)
            {
                if ((!$item[$model][0][$key] instanceof Doctrine_Null) && ($item[$model][0][$key] > $ref_ele))
                {
                    $out = $item;
                    $ref_ele = $item[$model][0][$key];
                }
            }
        }
        return array(0 => $out);
    }

    
    public static function getTheBest($array, $modelName, $langs = array(), $key = 'id', $modify_key = true)
    {
        if (empty($langs))
        {
            $langs = sfContext::getInstance()->getUser()->getPreferedLanguageList();
        }
        // init base vars
        $parsed_array = array();

        // parse our array
        foreach($array as $item)
        {
            if (isset($item['type']) && ($item['type'] == 'dm')) continue;
            // associations with maps : do not handle this item 
            // we do not want maps to display in lists.

            $iI18n = $item[$modelName . 'I18n'];
            $i_id = ($modify_key) ? $item[$key] : 0; // 'linked_id' upon second call for geoassociations

            $parsed_array[$i_id] = $item;
            $old_lang = 200;

            // if there is more than one translation
            if (count($iI18n) > 1)
            {
                unset($parsed_array[$i_id][$modelName . 'I18n']);
                
                // go through all language, and select the best
                foreach($iI18n as $itemI18n)
                {
                    // detect language position
                    $lang_pos = array_search($itemI18n['culture'], $langs);
                    if ($lang_pos === false)
                        $lang_pos = 10;

                    // test if language is prefered over the older
                    if($lang_pos < $old_lang)
                    {
                        // save language position
                        $old_lang = $lang_pos;
                        
                        // [0] to respect doctrine format
                        $parsed_array[$i_id][$modelName . 'I18n'][0] = $itemI18n;
                    }
                }
            }
        }
        return $parsed_array;
    }

    // almost same as above, but for the case when areas are
    // in [geoassociations] (because request used joins for
    // geoassociations table)
    public static function getTheBestForAssociatedAreas($array)
    {
        $langs = sfContext::getInstance()->getUser()->getPreferedLanguageList();
        $parsed_array = array();

        foreach ($array as $document_id => $document)
        {
            $parsed_array[$document_id] = $document;
            $document_geoassociations = $document['geoassociations'];
            $parsed_array[$document_id]['geoassociations'] = array();

            foreach ($document_geoassociations as $geoassociation)
            {
                $iI18n = $geoassociation['AreaI18n'];
                $i_id = $geoassociation['linked_id'];

                $parsed_array[$document_id]['geoassociations'][$i_id] = $geoassociation;
                $old_lang = 200;
                $area_type = null;

                if (count($iI18n) > 1)
                {
                    foreach($iI18n as $itemI18n)
                    {
                        // for an unknown reason, area_type is not always set for
                        // every i18n version, so store if not null
                        if (!empty($itemI18n['Area'])) $area_type = $itemI18n['Area'];

                        $lang_pos = array_search($itemI18n['culture'], $langs);
                        if ($lang_pos === false) $lang_pos = 10;
                        if ($lang_pos < $old_lang)
                        {
                            $old_lang = $lang_pos;
                            unset($parsed_array[$document_id]['geoassociations'][$i_id]['AreaI18n']);
                            $parsed_array[$document_id]['geoassociations'][$i_id]['AreaI18n'][0] = $itemI18n;
                        }
                    }
                    $parsed_array[$document_id]['geoassociations'][$i_id]['AreaI18n'][0]['Area'] = $area_type;
                }
            }
        }
        return $parsed_array;
    }
}
