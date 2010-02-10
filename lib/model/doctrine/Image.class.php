<?php
/**
 * $Id: Image.class.php 2542 2007-12-21 19:07:08Z alex $
 */

class Image extends BaseImage
{
    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetV4_app($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetCategories($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetCategories($value)
    {   
        return self::convertStringToArray($value);
    }
    
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetAuthor($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetCamera_name($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetExposure_time($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetFnumber($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetFocal_length($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetIso_speed($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function filterSetDate_time($value)
    {
        if (is_array($value)) // edited
        {
            $year    = $value['year'];
            $month   = $value['month'];
            $day     = $value['day'];
            $hour    = $value['hour'];
            $minute  = $value['minute'];
            $second  = $value['second'];
            
            if (empty($year) || empty($month) || empty($day) ||
                empty($hour) || empty($minute) || empty($second))
            {
                return NULL;
            }

            return date('Y-m-d H:i:s', mktime($hour, $minute, $second, $month, $day, $year));
        }
        else // from exif
        {
            return self::returnNullIfEmpty($value);
        }
    }

    /**
     * String fraction to decimal.
     * Convert 'x/y' to x/y as a decimal.  e.g.:
     * $value = 2692/100 or 21/1
     */
    public static function frac_to_dec($value) 
    {
        // explode to get x and y in an array.
        $dec = explode('/', $value);

        // confirm we are not dividing by zero
        if ((count($dec) == 2) && ($dec[1] > 0)) 
        {
            $value = $dec[0] / $dec[1];
            return ($value < 100 ? $value : ''); // avoid obviously wrong values
        }
        elseif (count($dec) == 1)
        {
            return ($value < 100 ? $value : '');
        }
        else 
        {
            return '';
        }
    }

    /**
     * Converts a degree/minute/second value to a decimal value
     *
     * @param  $dms two dimensionnal array
     * @return      decimal value
     */
    public static function convertDMSToDecimal($dms)
    {
        $degree = $dms[0];
        $minutes = $dms[1];
        $seconds = $dms[2];
        //c2cTools::log("$degree-$minutes-$seconds");
        $dms = self::frac_to_dec($degree) + self::frac_to_dec($minutes)/60 + self::frac_to_dec($seconds)/3600;
        return sprintf('%01.6f', $dms);
    }

    public static function customSave($name, $filename, $associated_doc_id, $user_id, $model, $activities = array(), $categories = array(), $image_type = 1)
    {
        $base_path = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR;
        $from = $base_path . sfConfig::get('app_images_temp_directory_name');
        $to   = $base_path . sfConfig::get('app_images_directory_name');
        
        c2cTools::log("linking image $filename to $model $associated_doc_id, with title \"$name\", user $user_id ");

        // save a new image...
        $image = new Image();
        $image->setCulture(sfContext::getInstance()->getUser()->getCulture());
        $image->set('name', $name);
        $image->set('filename', $filename);
        // here, read eventual lon, lat, elevation and other interesting fields from exif tag...
        // (nb: always after $image->set('filename', $filename))
        $image->populateWithExifDataFrom($from . DIRECTORY_SEPARATOR . $filename);
        // here, copy activities field from the linked document (if it exists):
        if (!empty($activities))
        {
            $image->set('activities', $activities);
        }
        if (!empty($categories))
        {
            $image->set('categories', $categories);
        }
        $image->set('image_type', $image_type);
        // then save:
        $image->doSaveWithMetadata($user_id, false, 'Image uploaded');
        
        c2cTools::log('associating and moving files');
        
        $image_id = $image->get('id');
        $type = c2cTools::Model2Letter($model).'i';
        
        // associate it
        $a = new Association;
        $a->doSaveWithValues($associated_doc_id, $image_id, $type, $user_id);

        // move to uploaded images directory (move the big, small and all other configured in yaml)
        Images::moveAll($filename, $from, $to);

        return $image_id;
    }

    public static function getLinkedFiles($id)
    {
        $filename_rows = sfDoctrine::Connection()
                     ->standaloneQuery('SELECT DISTINCT filename FROM app_images_archives WHERE id = '.$id)
                     ->fetchAll();

        $filenames = array();
        foreach ($filename_rows as $row)
        {
            $filenames[] = $row['filename'];
        }

        return $filenames;
    }

    /**
     * Reads exif data in Image file (if available) and hydrates object with the most important of them.
     * Also reads GPS info if set (for instance with gpscorrelate, a GPX file and a bunch of well-tagged pictures)  
     * see for instance http://freefoote.dview.net/linux_gpscorr.html
     *
     * @param  $filename_with_path
     * @param  $overwrite_geom : if true, then image lon, lat and ele will be overwritten by those taken from the Exif data (if they exist).
     */
    public function populateWithExifDataFrom($filename_with_path, $overwrite_geom = true, $overwrite_datetime = true)
    {
        // here, read eventual lon, lat and elevation in exif tag...
        if (c2cTools::getFileType($filename_with_path) == 'jpg' && $exif = exif_read_data($filename_with_path))
        // cannot use PEL to read them because still buggy (lib does not conform to php strict standards).
        {
            $lon = '';
            $lat = '';
            if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude']) 
                && isset($exif['GPSLatitudeRef']) && isset($exif['GPSLongitudeRef'])
                && $overwrite_geom) 
            {
                if (strtoupper($exif['GPSLongitudeRef']) == "W") 
                {
                    $lon = -1 * self::convertDMSToDecimal($exif['GPSLongitude']);
                }
                else 
                {
                    $lon =  self::convertDMSToDecimal($exif['GPSLongitude']);
                }
        
                if (strtoupper($exif['GPSLatitudeRef']) == "S") 
                {
                    $lat = -1 * self::convertDMSToDecimal($exif['GPSLatitude']);
                }
                else 
                {
                    $lat = self::convertDMSToDecimal($exif['GPSLatitude']);
                }
                
                $this->set('lon', $lon);
                $this->set('lat', $lat);

            }

            $ele = '';
            if (isset($exif['GPSAltitude']) && isset($exif['GPSAltitudeRef']) && $overwrite_geom) 
            {
                if ($exif['GPSAltitudeRef'] == "1") // negative elevations
                {
                    $ele = -1 * round(self::frac_to_dec($exif['GPSAltitude']));
                }
                else 
                {
                    $ele =  round(self::frac_to_dec($exif['GPSAltitude']));
                }
                
                $this->set('elevation', $ele);

            }

            $camera_name = null;
            if (isset($exif['Make']) && (strtolower($exif['Make']) != 'canon'))
            {
                $camera_name = $exif['Make'];
            }
            if (isset($exif['Model']))
            {
                if (strtolower($exif['Make']) != 'canon')
                {
                    $camera_name .= ' ' . $exif['Model'];
                }
                else
                {
                    $camera_name = $exif['Model'];
                }
            }
            $this->set('camera_name', $camera_name);

            if (isset($exif['DateTimeOriginal']) && $overwrite_datetime)
            {
                $image_date = str_replace(' ', ':', $exif['DateTimeOriginal']);
                $image_date = explode(':', $image_date);
                $image_date = mktime($image_date[3], $image_date[4], $image_date[5], $image_date[1], $image_date[2], $image_date[0]);

                $this->set('date_time', date('c', $image_date));
            }

            if (isset($exif['ExposureTime']))
            {
                $this->set('exposure_time', self::frac_to_dec($exif['ExposureTime']));
            }
            if (isset($exif['FNumber']))
            {
                $this->set('fnumber', self::frac_to_dec($exif['FNumber']));
            }
            if (isset($exif['ISOSpeedRatings']))
            {
                $iso_speed = is_array($exif['ISOSpeedRatings']) ? $exif['ISOSpeedRatings'][0] : $exif['ISOSpeedRatings'];
                $this->set('iso_speed', $iso_speed);
            }
            if (isset($exif['FocalLength']))
            {
                $this->set('focal_length', self::frac_to_dec($exif['FocalLength']));
            }

            c2cTools::log("Image::populateWithExifDataFrom found GPS data in exif tag : $lon, $lat, $ele");

        }
    }

    /**
     * Retrieves a list of images ordered by descending id.
     */
    public static function listLatest($max_items, $langs, $activities)
    {
        $categories_filter = array();
        foreach (sfConfig::get('app_images_home_categories') as $id)
        {
            $categories_filter[] = "$id = ANY (categories)";
        }
        $categories_filter = implode(' OR ', $categories_filter);

        $q = Doctrine_Query::create();
        $q->select('i.id, n.culture, n.name, n.search_name, i.filename')
          ->from('Image i')
          ->leftJoin('i.ImageI18n n')
          ->where($categories_filter) // FIXME: needs index?
          ->addWhere('i.redirects_to IS NULL')
          ->orderBy('i.id DESC')
          ->limit($max_items);

        if (!empty($activities))
        {
            $q->addWhere(self::getActivitiesQueryString($activities), $activities);
        }

        if (!empty($langs))
        {
            $q->addWhere(self::getLanguagesQueryString($langs, 'n'), $langs);
        }

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    protected static function joinOnMultiRegions($q, $conditions)
    {
        if (isset($conditions['join_area']))
        {
            $q->leftJoin('m.associations l')
              ->leftJoin('l.MainDocument d')
              ->addWhere("l.type IN ('ai', 'hi', 'pi', 'oi', 'ri', 'ti', 'si')");
            
            $conditions = Document::joinOnMulti($q, $conditions, 'join_area', 'd.geoassociations g', 3);
        }
        return $conditions;
    }
    
    public static function browse($sort, $criteria, $format = null)
    {
        $pager = self::createPager('Image', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();

        if (!empty($criteria))
        {
            // TODO: join only if area criteria is detected
            $conditions = $criteria[0];

            $conditions = self::joinOnMultiRegions($q, $conditions);

            if (isset($conditions['join_doc']))
            {
                unset($conditions['join_doc']);
                $q->leftJoin('m.associations d');
            }

            if (isset($conditions['join_user']))
            {
                unset($conditions['join_user']);
                $q->leftJoin('m.versions v')
                  ->leftJoin('v.history_metadata hm')
                  ->addWhere('v.version = 1');
            }
            
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            //self::filterOnRegions($q);
            self::filterOnActivities($q);
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
                           array('m.filename', 'm.date_time', 'm.image_type', 'm.lon', 'm.lat'));
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::filterOnActivities($q);
    }
}
