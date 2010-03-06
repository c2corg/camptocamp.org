<?php
/**
 * c2cActions must be between action in module and the base class sfActions
 * it provides new shortcuts
 *
 * @author     Mickael Kurmann <mickael.kurmann@gmail.com>
 * @version    SVN: $Id: c2cTools.class.php 2438 2007-11-28 15:57:57Z alex $
 */

class c2cTools
{
    public static function log($message = null)
    {
        if (sfConfig::get('sf_logging_enabled'))
        {
            sfContext::getInstance()->getLogger()->debug('{c2c} '.$message);
        }
    }
    
    // log arrays
    public static function alog($myarray = null)
    {
        self::log(print_r($myarray, true));
    }
    
    /**
     * A method to get file mime_type by using custom magic file
     * requires fileinfo (pecl extension, or included for php>=5.3)
     * (this because magic files do not always match what we want
     *  depending on OS and versions)
     */
    public static function getMimeType($path)
    {
        $magic_file = sfConfig::get('app_mime_file');
        $finfo = finfo_open(FILEINFO_NONE, $magic_file);

        if (!$finfo) {
            return null;
        }

        $mime =  finfo_file($finfo, $path);
        finfo_close($finfo);

        return $mime;
    }

    
    /**
     * A method to get file type (KML, KMZ, GPX, JPG, GIF, PNG ...)
     */
    public static function getFileType($path)
    {
        $type = self::getMimeType($path);
        
        self::log('c2cTools::getFileType :'.$type);
        
        switch ($type)
        {
            /*case 'text/xml':
                $xml = self::simplexmlLoadFile($path);
                return $xml->getName();*/
            case 'application/vnd.google-earth.kmz': return 'kmz';
            case 'application/vnd.google-earth.kml+xml': return 'kml';
            case 'application/gpx+xml': return 'gpx';
            case 'image/png': return 'png';
            case 'image/jpg': return 'jpg';
            case 'image/jpeg': return 'jpg';
            case 'image/gif': return 'gif';
            case 'image/svg+xml': return 'svg';
            default: return false;
        }
    }

    
    /**
     * Generate a new name for the uploaded file composed of 3 parts :
     * - Unix date (files can thus be easily listed using the upload date
     * - Random number (to assure name unicity)
     */
    public static function generateUniqueName()
    {
        srand((double) microtime() * 1000000);
        return date('U') . '_' . rand();
    }
    
    /**
     * Converts string 'Summit' into 'summits'
     */
    public static function model2module($model)
    {
        return strtolower($model).'s';
    }
    
    /**
     * Converts string 'summits' into 'Summit'
     */
    public static function module2model($module)
    {
        return ucfirst(substr($module, 0, -1));
    }

    public static function sortArray($array, $type = null, $field = null, $order = null)
    {
        if (count($array) <= 1 or $type == null)
        {
            return $array;
        }
        
        sfLoader::loadHelpers(array('General'));
        
        if ($field == null)
        {
            $field = $type;
        }
        
        switch ($type)
        {
            case 'name' :
            case 'string' :
                $sort_type = SORT_STRING;
                $sort_order = SORT_ASC;
                foreach ($array as $key => $row)
                {
                   $name[$key] = remove_accents($row[$field]);
                }
                break;
            
            default :
                $sort_type = SORT_NUMERIC;
                $sort_order = SORT_DESC;
                foreach ($array as $key => $row)
                {
                   $name[$key] = $row[$field];
                }
        }
        
        if (!empty($order))
        {
            $sort_order = $order;
        }
        
        array_multisort($name, $sort_type, $sort_order, $array);
        
        return $array;
    }
 
    public static function sortArrayByName($array, $field = 'name')
    {
        sfLoader::loadHelpers(array('General'));
        if (count($array) > 1)
        {
            foreach ($array as $key => $row)
            {
               $name[$key] = remove_accents($row[$field]);
            }
            array_multisort($name, SORT_STRING, $array);
        }
        return $array;
    }
 
    public static function is_route($a)
    {
        return ($a['module'] == 'routes');
    }
        
    public static function is_summit($a)
    {
        return ($a['module'] == 'summits');
    }
    
    public static function is_outing($a)
    {
        return ($a['module'] == 'outings');
    }
        
    public static function is_area($a)
    {
        return ($a['module'] == 'areas');
    }
    
    public static function is_map($a)
    {
        return ($a['module'] == 'maps');
    }
    
    public static function is_article($a)
    {
        return ($a['module'] == 'articles');
    }
    
    public static function is_book($a)
    {
        return ($a['module'] == 'books');
    }
    
    public static function is_hut($a)
    {
        return ($a['module'] == 'huts');
    }
    
    public static function is_parking($a)
    {
        return ($a['module'] == 'parkings');
    }    
    
    public static function is_site($a)
    {
        return ($a['module'] == 'sites');
    }
    
    public static function is_user($a)
    {
        return ($a['module'] == 'users');
    } 
    
    public static function is_image($a)
    {
        return ($a['module'] == 'images');
    }
    
    public static function is_not_image($a)
    {
        return ($a['module'] != 'images');
    }
    
    public static function is_not_route($a)
    {
        return ($a['module'] != 'routes');
    }
    
    public static function is_not_user($a)
    {
        return ($a['module'] != 'users');
    }
    
    public static function is_site_route_image($a)
    {
        return in_array($a['module'], array('sites', 'routes', 'images'));
    }

    public static function cmpDate($a, $b)
    {
        if ($a['date'] == $b['date']) 
        {
            return 0;
        }
        return ($a['date'] < $b['date']) ? 1 : -1;
    }

    public static function cmpDateTimeDesc($a, $b)
    {
        if ($a['date_time'] instanceof Doctrine_Null && !$b['date_time'] instanceof Doctrine_Null)
        {
            return 1;
        }

        if (!$a['date_time'] instanceof Doctrine_Null && $b['date_time'] instanceof Doctrine_Null)
        {
            return -1;
        }

        if ($a['date_time'] == $b['date_time'])
        {
            return ($a['id'] > $b['id']) ? 1 : -1;
        }
        return ($a['date_time'] > $b['date_time']) ? 1 : -1;
    }

    public static function is_collaborative_document($a)
    {
        return !(c2cTools::is_user($a)
                  || c2cTools::is_outing($a)
                  || (c2cTools::is_article($a) && $a['article_type'] == 2)
                  || (c2cTools::is_image($a) && $a['image_type'] == 2));
    }
    
    /**
     * Clear cache for view / history and diff after a new comment has been posted
     * rq: comment page is not cached
     */
    public static function clearCommentCache($id, $lang = null, $module = '*')
    {
        $cache_dir = sfConfig::get('sf_root_cache_dir') . '/frontend/*/template/*/all';
        $cache_dir .= (sfConfig::get('sf_no_script_name')) ? '/' : '/*/';
    
        $items = array("$module/history/id/$id/", "$module/view/id/$id/", "$module/diff/id/$id/");

        foreach ($items as $item)
        {
            $item .= ($lang == null) ? '*' : "lang/$lang/*" ;        
            c2cTools::log('{cache} removing : ' . $cache_dir . $item);
            sfToolkit::clearGlob($cache_dir . $item);
        }
    }
    
    public static function extractHighestName($objects_array, $return_search_name = false)
    {
        $highest_elevation = 0;
        $highest_object_name = '';

        $name_field = $return_search_name ? 'search_name' : 'name';
        
        foreach ($objects_array as $object)
        {
            // find highest summit name
            if ($object['elevation'] > $highest_elevation)
            {
                $highest_elevation = $object['elevation'];
                $highest_object_name = $object[$name_field];
            }
            // set default name if no summit has its elevation set
            elseif ($highest_elevation == 0)
            {
                $highest_object_name = $object[$name_field];
            }
        }
        return $highest_object_name;
    }
    
    /**
     * Converts an array of C2C region ids into an array of region ids suitable for meta-engine
     */
    public static function convertC2cRangeIdsToMetaIds($range_ids)
    {
        $out = array();
        $ranges_array = sfConfig::get('app_meta_engine_regions');
        foreach ($range_ids as $id)
        {
            if (array_key_exists($id, $ranges_array))
            {
                $out[] = $ranges_array[$id];
            }
        }
        return $out;
    }

    /**
     * Converts an array of C2C activity ids into an array of activity ids suitable for meta-engine
     */
    public static function convertC2cActivityIdsToMetaIds($activity_ids)
    {
        $out = array();
        $activities_array = sfConfig::get('app_meta_engine_activities');
        foreach ($activity_ids as $id)
        {
            if (array_key_exists($id, $activities_array))
            {
                $out[] = $activities_array[$id];
            }
        }
        return $out;
    }
    
    /**
     * Converts a Letter used in association system into a Model
     */
    public static function Letter2Model($a)
    {
        switch ($a)
        {
            case 'a':
                return 'Area';
            case 'b':
                return 'Book';
            case 'c':
                return 'Article';
            case 'h':
                return 'Hut';
            case 'i':
                return 'Image';
            case 'm':
                return 'Map';
            case 'o':
                return 'Outing';
            case 'p':
                return 'Parking';
            case 'r':
                return 'Route';
            case 's':
                return 'Summit';
            case 't':
                return 'Site';
            case 'u':
                return 'User';
            default:
                return '';
        }
    }
    
    /**
     * Converts a Letter used in association system into a module
     */
    public static function Letter2Module($a)
    {
        $model = self::Letter2Model($a);
        if (empty($model))
        {
            return '';
        }
        return self::model2module($model);
    }

    /**
     * Converts a Model into a Letter used in association system
     */
    public static function Model2Letter($a)
    {
        switch ($a)
        {
            case 'Area':
                return 'a';
            case 'Book':
                return 'b';
            case 'Article':
                return 'c';
            case 'Hut':
                return 'h';
            case 'Image':
                return 'i';
            case 'Map':
                return 'm';
            case 'Outing':
                return 'o';
            case 'Parking':
                return 'p';
            case 'Route':
                return 'r';
            case 'Summit':
                return 's';
            case 'Site':
                return 't';
            case 'User':
                return 'u';
            default:
                return '';
        }
    }

    public static function Module2Letter($a)
    {
        switch ($a)
        {
            case 'areas':
                return 'a';
            case 'books':
                return 'b';
            case 'articles':
                return 'c';
            case 'huts':
                return 'h';
            case 'images':
                return 'i';
            case 'maps':
                return 'm';
            case 'outings':
                return 'o';
            case 'parkings':
                return 'p';
            case 'routes':
                return 'r';
            case 'summits':
                return 's';
            case 'sites':
                return 't';
            case 'users':
                return 'u';
            default:
                return '';
        }
    }
    
    /**
     * Converts a couple of modules into an association type
     * returns null if the association kind does not exist
     */
    public static function Modules2Type($main, $linked)
    {
        $type_list = sfConfig::get('app_associations_types');
        $type = self::Module2Letter($main) . self::Module2Letter($linked);
        $type_reversed = self::Module2Letter($linked) . self::Module2Letter($main);
        $swap = false;

        if (!in_array($type, $type_list))
        {
            if (!in_array($type_reversed, $type_list))
            {
                return null;
            }
            else
            {
                $swap = true;
                $type = $type_reversed;
                $temp = $main;
                $main = $linked;
                $linked = $temp;
            }
        }

        $strict = ($main == $linked) ? 0 : 1;

        return array($type, $swap, $main, $linked, $strict);
    }
    
    /**
     * Converts an association type (eg: 'sr') into an array of Models
     */
    public static function Type2Models($in)
    {
        return array('main' => self::Letter2Model(substr($in,0,1)), 'linked' => self::Letter2Model(substr($in,1,1)));
    }

    /**
     * Converts an association type (eg: 'sr') into an array of modules
     */
    public static function Type2Modules($in)
    {
        return array('main' => self::Letter2Module(substr($in,0,1)), 'linked' => self::Letter2Module(substr($in,1,1)));
    }

    /**
     * Wrapper for SimpleXml object creation.
     * @param string path
     * @return SimpleXMLElement
     */
    public static function simplexmlLoadFile($path)
    {
        $string = file_get_contents($path);
        // SimpleXml is currently not compatible with XML 1.1, let's try to fool it by updating version
        $string = str_replace('<?xml version="1.1"', '<?xml version="1.0"', $string);
        return simplexml_load_string($string);
    }

    /**
     * Checks that number such as days or months are written with 2 digits.
     * @param integer
     * @return integer
     */
    public static function writeWith2Digits($nb)
    {
        if (empty($nb)) return '';

        return ($nb < 10) ? "0$nb" : $nb;
    }

    /*
     * Convert a string like '2008-06-13' into Array{ 'year' => 2008, 'month' => 6, 'day' => 13 }
     * @param string
     * @return array
     */
    public static function stringDateToArray($date)
    {
        if (!preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date, $matches))
            return $date;

        return array('year' => $matches[1], 'month' => $matches[2], 'day' => $matches[3]);
    }

    /*
     * Returns a list of users that should be notified that a document has been commented
     * for the first time.
     * Typically users associated to an outing, user that uploaded a photo, user page and
     * personal articles
     */
    public static function getUsersToNotify($doc_id)
    {
        if (!is_int((int)$doc_id)) return array();

        $result = Doctrine_Query::create()
            ->select('d.module')
            ->from('Document d')
            ->where('d.id = ?', array($doc_id))
            ->execute(array(), Doctrine::FETCH_ARRAY);
        $module = $result[0]['module'];

        // note: only personal articles are linked with users so it is ok to search users attached for each article
        if (in_array($module, array('outings', 'users', 'articles', 'images')))
        {
            if ($module == 'users')
            {
                return array($doc_id);
            }
            else if ($module == 'images')
            {
                $result = Doctrine_Query::create()
                    ->select('dv.document_id, hm.user_id')
                    ->from('DocumentVersion dv LEFT JOIN dv.history_metadata hm ')
                    ->where('dv.document_id = ? AND dv.version = ?', array($doc_id, 1))
                    ->orderBy('dv.created_at ASC')
                    ->limit(1)
                    ->execute(array(), Doctrine::FETCH_ARRAY);
                return array($result[0]['history_metadata']['user_id']);
            }
            else
            {
                switch ($module)
                {
                    case 'outings': $association_type = 'uo'; break;
                    case 'articles': $association_type = 'uc'; break;
                }
                $associations = Association::findAllAssociations($doc_id, $association_type);
                $users = array();
                foreach ($associations as $association)
                {
                    $users[] = $association['main_id'];
                }
                return $users;
            }
        }
        return array();
    }

    /**
     * Convert lat/lon to swiss coordinates
     * see http://www.swisstopo.admin.ch/internet/swisstopo/fr/home/topics/survey/sys/refsys/switzerland.parsysrelated1.31216.downloadList.77004.DownloadFile.tmp/swissprojectionfr.pdf
     */
    public static function convertLatLonToSwissCoords($lat, $lon)
    {
        $x = $lon * (M_PI / 180);
        $y = $lat * (M_PI / 180);
        $e2 = 0.006674372230614;
        $e = sqrt($e2);
        $alpha = 1.00072913843038;
        $K = 0.0030667323772751;
        $lambda0 = 0.129845224143583;
        $b0 = 0.818694358568627;
        $R = 6378815.90365;

        $Sa1 = log(tan(M_PI / 4.0 - $y / 2.0));
        $Sa2 = $e / 2.0 
             * log((1 + $e * sin($y)) 
             / (1 - $e * sin($y))); 
        $S = -$alpha * ($Sa1 + $Sa2) + $K; 
        $b = 2.0 * (atan(exp($S)) - M_PI / 4.0);
        $I = $alpha * ($x - $lambda0); 

 	    $rotI = atan(sin($I) / (sin($b0) * tan($b) + cos($b0) * cos($I))); 
        $rotB = asin(cos($b0) * sin($b) - sin($b0) * cos($b)* cos($I)); 
        $X = $R / 2.0 * log((1 + sin($rotB)) / (1 - sin($rotB))) + 200000.0; 
 	    $Y = $R * $rotI + 600000.0;
        return array(round($X), round($Y)); 
    }
}
