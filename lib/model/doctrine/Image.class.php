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
            $year    = empty($value['year']) ? 0 : $value['year'];
            $month   = empty($value['month']) ? 0 : $value['month'];
            $day     = empty($value['day']) ? 0 : $value['day'];
            $hour    = empty($value['hour']) ? 0 : $value['hour'];
            $minute  = empty($value['minute']) ? 0 : $value['minute'];
            $second  = empty($value['second']) ? 0 : $value['second'];
 
            if ((!$year || !$month || !$day) ||
                (!$year && !$month && !$day && !$hour && !$minute && !$second))
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
        $image->set('has_svg', Images::hasSVG($filename, $from));
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
    public static function listLatest($max_items, $langs, $ranges, $activities, $params = array())
    {
        $categories_filter = array();
        foreach (sfConfig::get('app_images_home_categories') as $id)
        {
            $categories_filter[] = "$id = ANY (categories)";
        }
        $categories_filter = implode(' OR ', $categories_filter);

        $q = Doctrine_Query::create();
        $q->select('m.id, n.culture, n.name, m.filename')
          ->from('Image m')
          ->leftJoin('m.ImageI18n n')
          ->where($categories_filter) // FIXME: needs index?
          ->addWhere('m.redirects_to IS NULL')
          ->orderBy('m.id DESC')
          ->limit($max_items);

        self::filterOnActivities($q, $activities, 'm', 'i');
        self::filterOnLanguages($q, $langs, 'n');
        self::filterOnRegions($q, $ranges, 'g2');
        
        if (!empty($params))
        {
            $criteria = self::buildListCriteria($params);
            if (!empty($criteria))
            {
                self::buildPagerConditions($q, $criteria[0], $criteria[1]);
            }
        }

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    protected static function filterOnRegions($q, $areas = null, $alias = 'g2')
    {
        if  (is_null($areas))
        {
            $areas = c2cPersonalization::getInstance()->getPlacesFilter();
        }
        if (!empty($areas))
        {
            $q->leftJoin('m.associations l')
              ->leftJoin('l.MainGeoassociations ' . $alias)
              ->addWhere(self::getAreasQueryString($areas, $alias), $areas);
            c2cTools::log('filtering on regions');
        }
    }

    public static function buildImageListCriteria(&$conditions, &$values, $params_list, $is_module = false)
    {
        if ($is_module)
        {
            $m = 'm';
            $mid = 'm.id';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 'i';
            $mid = 'li.linked_id';
            $join = 'join_image';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'images', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, 'i', 'activities'), 'act', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Date', 'date_time', 'date', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'ii.search_name', ($is_module ? array('inam', 'name') : 'inam'), 'join_image_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'i', 'activities'), 'iact', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'ialt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'i', 'categories'), 'icat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Item', $m . '.image_type', 'ityp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Date', $m . '.date_time', 'idate', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'ii.culture', 'icult', 'join_image_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'lic.main_id', 'itags', 'join_itag_id', false, $params_list);
        }
    }

    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'icult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);
        
        // image criteria
        Images::buildImageListCriteria(&$conditions, &$values, $params_list, true);
        self::buildConditionItem($conditions, $values, 'List', 'lic.main_id', 'documents', 'join_itag_id', false, $params_list);

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, false, 'ls.main_id');

        // route criteria
        Route::buildRouteListCriteria(&$conditions, &$values, $params_list, false, 'lr.main_id');

        // site criteria
        Site::buildSiteListCriteria(&$conditions, &$values, $params_list, false, 'lt.main_id');
        
        // outing criteria
        Outing::buildOutingListCriteria(&$conditions, &$values, $params_list, false, 'lo.main_id');
        
        // user criteria
        self::buildConditionItem($conditions, $values, 'List', 'hm.user_id', 'user', 'join_user', false, $params_list); // TODO here we should restrict to initial uploader (ticket #333)
        self::buildConditionItem($conditions, $values, 'List', 'hm.user_id', 'users', 'join_user', false, $params_list); // TODO here we should restrict to initial uploader (ticket #333)

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }
    
    public static function browse($sort, $criteria, $format = null)
    {
        $pager = self::createPager('Image', self::buildFieldsList(), $sort);
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
            self::buildPagerConditions($q, $conditions, $criteria[1]);
        }
        elseif (!$all && c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            self::filterOnActivities($q);
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }
    
    public static function buildImagePagerConditions(&$q, &$conditions, $is_module = false, $ltype)
    {
        if ($is_module)
        {
            $m = 'm.';
            $main = $m . 'associations';
        }
        else
        {
            $m = 'li.';
            $main = $m . 'MainMainAssociation';
            
            $q->leftJoin("m.LinkedAssociation li");
            
            if (isset($conditions['join_image_id']))
            {
                unset($conditions['join_image_id']);
                
                return;
            }
            else
            {
                $q->addWhere($m . "type = '$ltype'");
            }
            
            if (isset($conditions['join_image']))
            {
                $q->leftJoin($m . 'Image i');
                unset($conditions['join_image']);
            }
        }

        if (isset($conditions['join_image_i18n']))
        {
            $q->leftJoin($m . 'ImageI18n ii');
            unset($conditions['join_image_i18n']);
        }
        
        if (isset($conditions['join_itag_id']))
        {
            $q->leftJoin($main . ' lic');
            unset($conditions['join_itag_id']);
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnLinkedDocMultiRegions($q, $conditions);

        // join with image tables only if needed 
        if (   isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id'])
        )
        {
            Image::buildImagePagerConditions($q, $conditions, true);
        }

        if (   isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            $q->leftJoin("m.associations ls");
            
            Summit::buildSummitPagerConditions($q, $conditions, false, false, 'si');
        }

        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_rdoc_id'])
            || isset($conditions['join_rtag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbook'])
            || isset($conditions['join_rbook_i18n'])
            || isset($conditions['join_rbtag_id'])
        )
        {
            $q->leftJoin("m.associations lr");
            
            Route::buildRoutePagerConditions($q, $conditions, false, false, 'ri');
        }

        if (   isset($conditions['join_site_id'])
            || isset($conditions['join_site'])
            || isset($conditions['join_site_i18n'])
            || isset($conditions['join_tbook_id'])
            || isset($conditions['join_ttag_id'])
            || isset($conditions['join_tbtag_id'])
        )
        {
            $q->leftJoin("m.associations lt");
            
            Site::buildSitePagerConditions($q, $conditions, false, false, 'ti');
        }
        
        // join with outings tables only if needed 
        if (   isset($conditions['join_outing_id'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
        )
        {
            Outing::buildOutingPagerConditions($q, $conditions, false, false, 'm.associations', 'oi');
        }

        if (isset($conditions['join_user']))
        {
            $q->leftJoin('m.versions v')
              ->leftJoin('v.history_metadata hm')
              ->addWhere('v.version = 1');
             unset($conditions['join_user']);
        }
        
        $q->addWhere(implode(' AND ', $conditions), $criteria);
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

    /** This function is used to validate an uploaded image.
     * It is quite similar to myImageValidator, except
     * that we cannot validate images one by one (when several ones
     * are uploaded at the same time). Thus, we have some custom
     * validation mechanism here
     */
    public static function validate_image(&$value, &$error, $i)
    {
        // file upload check
        if ($value['error'][$i])
        {
            $error = 'file failed to upload';
            return false;
        }

        $validation = sfConfig::get('app_images_validation');

        if ($value['size'][$i] > $validation['weight'])
        {
            $error = 'file is too big';
            return false;
        }

        // type check
        // FIXME with symfony 1.0, the type is the one given by the browser
        // we prefer to use or own mime type checker (this is what is done in further
        // versions of symfony, using system file check)
        $mime_type = c2cTools::getMimeType($value['tmp_name'][$i]);
        if (!in_array($mime_type, $validation['mime_types']))
        {
            $error = 'file has incorrect type';
            return false;
        }
        if ($mime_type != 'image/svg+xml')
        {
            list($width, $height) = getimagesize($value['tmp_name'][$i]);
        }
        else
        {
            // are there any script?
            if (SVG::hasScript($value['tmp_name'][$i]))
            {
                $error = 'file cannot contain scripts';
                return false;
            }

            // dimensions
            $dimensions = SVG::getSize($value['tmp_name'][$i]);
            if ($dimensions === false)
            {
                $error = 'file is malformed SVG';
                return false;
            }
            else
            {
                list($width, $height) = $dimensions;
            }
        }
        // height/width check
        if ($width > $validation['max_size']['width'] ||
            $height > $validation['max_size']['height'])
        {
            $error = 'file is too large';
            return false;
        }
        if ($width < $validation['min_size']['width'] ||
            $height < $validation['min_size']['height'])
        {
            $error = 'min_dim_error';
            return false;
        }

        return true;
    }
}
