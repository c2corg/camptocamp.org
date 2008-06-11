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
     * Detects mimetype of given file.
     * Requires the PECL extension FileInfo.
     * Requires to symlink or copy misc/magic.mime file into /etc (for Linux).
     */
    public static function getMimeType($path)
    {
        $finfo = new finfo(FILEINFO_MIME);
        if (!$finfo) {
            self::log('c2cTools::getMimeType() failed opening fileinfo db file');
            return null;
        }
        return $finfo->file($path);
    }
    
    /**
     * A method to get file type (KML, KMZ, GPX, JPG, GIF, PNG ...)
     */
    public static function getFileType($path)
    {
        $type = exec("file -i -b $path"); //OS dependent
        // or
        //$type = self::getMimeType($path);
        // or
        //$type = mime_content_type($path); // deprecated, OS independent but still works

        self::log('c2cTools::getFileType :'.$type);
        
        // returns "text/xml" (GPX, KML files) or  "application/x-zip" (KMZ) or image/png ...
        switch ($type)
        {
            case 'application/x-zip': 
                // unzip and perform following test for KML detection
                //exec("unzip $finalPath");
                // FIXME: handle KMZ
                return false;
            case 'text/xml':
                $xml = self::simplexmlLoadFile($path);
                return $xml->getName();
            case 'image/png': return 'png';
            case 'image/jpg': return 'jpg';
            case 'image/jpeg': return 'jpg';
            case 'image/gif': return 'gif';
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
    
    public static function cmpDate($a, $b)
    {
        if ($a['date'] == $b['date']) 
        {
            return 0;
        }
        return ($a['date'] < $b['date']) ? 1 : -1;
    }
    
    /**
     * Clears comment cache only.
     * used after punBB comment post
     */
    public static function clearCommentCache($id, $lang = null, $module = '*')
    {
        $cache_dir = sfConfig::get('sf_root_cache_dir') . '/frontend/*/template/*/all';
        $cache_dir .= (sfConfig::get('sf_no_script_name')) ? '/' : '/*/';
    
        $items = array("$module/comment/id/$id/", "$module/view/id/$id/");

        foreach ($items as $item)
        {
            $item .= ($lang == null) ? '*' : "lang/$lang/*" ;        
            c2cTools::log('{cache} removing : ' . $cache_dir . $item);
            sfToolkit::clearGlob($cache_dir . $item);
        }
    }
    
    public static function extractHighestName($objects_array)
    {
        $highest_elevation = 0;
        $highest_object_name = '';
        
        foreach ($objects_array as $object)
        {
            // find highest summit name
            if ($object['elevation'] > $highest_elevation)
            {
                $highest_elevation = $object['elevation'];
                $highest_object_name = $object['name'];
            }
            // set default name if no summit has its elevation set
            elseif ($highest_elevation == 0)
            {
                $highest_object_name = $object['name'];
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
    
    /**
     * Converts an association type (eg: 'sr') into an array of Models
     */
    public static function Type2Models($in)
    {
        return array('main' => self::Letter2Model(substr($in,0,1)), 'linked' => self::Letter2Model(substr($in,1,1)));
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
        return ($nb < 10) ? "0$nb" : $nb;
    }
}
