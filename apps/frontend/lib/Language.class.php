<?php
/**
 * Languages names tools
 * @version $Id: Language.class.php 2416 2007-11-23 15:48:36Z fvanderbiest $
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
    public static function parseListItems($array, $modelName)
    {
        $langs = sfContext::getInstance()->getUser()->getPreferedLanguageList();

        $parsed_array = self::getTheBest($array, $modelName, $langs);
        // once we have selected the best lang to display this item name, 
        // we build search string for comments (eg: 333_fr)
        $_str = array();
    
        foreach($parsed_array as $key => $item)
        {
            // build ids strings to get nb of comments
            $_str[$item['id']] = $item['id'] . '_' . $item[$modelName.'I18n'][0]['culture'];
            // extract best name for associated regions
            $parsed_array[$key]['geoassociations'] = self::getTheBest($item['geoassociations'], 'Area', $langs, 'linked_id'); 
        }
        
        // get nb of comments for all items
        $pun_msgs = Punbb::getNbMessages($_str);
        // merge this info into $parsed_array
        foreach ($pun_msgs as $pun_msg)
        {
            $id = substr($pun_msg['subject'], 0, strpos($pun_msg['subject'], '_'));
            foreach($parsed_array as $key => $item)
            {
                if ($key == $id)
                {
                    $parsed_array[$key]['nb_comments'] = $pun_msg['count'];
                }
            }
        }
        
        // Count all images linked
        $image_links = Association::countAllLinked(array_flip($_str), c2cTools::Model2Letter($modelName).'i');
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
        // parse our array
        foreach($array as $item)
        {
            if ((get_class($item[$model][0][$key]) != 'Doctrine_Null') && ($item[$model][0][$key] > $ref_ele))
            {
                $out = $item;
                $ref_ele = $item[$model][0][$key];
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
                // go through all language, and select the best
                foreach($iI18n as $itemI18n)
                {
                    // detect language position
                    $lang_pos = array_search($itemI18n['culture'], $langs);

                    // test if language is prefered over the older
                    if($lang_pos < $old_lang)
                    {
                        // save language position
                        $old_lang = $lang_pos;
                        unset($parsed_array[$i_id][$modelName . 'I18n']);
                        // [0] to respect doctrine format
                        $parsed_array[$i_id][$modelName . 'I18n'][0] = $itemI18n;
                    }
                }
            }
        }
        return $parsed_array;
    }
}
