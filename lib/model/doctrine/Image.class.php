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
        return floatval(sprintf('%01.6f', $dms));
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

        // get and store image dimensions and size
        $size = getimagesize($from . DIRECTORY_SEPARATOR . $filename);
        if ($size)
        {
            $image->set('width', $size[0]);
            $image->set('height', $size[1]);
        }
        $image->set('file_size', filesize($from . DIRECTORY_SEPARATOR . $filename));

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
                     ->standaloneQuery('SELECT DISTINCT filename FROM app_images_archives WHERE id = ?', array($id))
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

		            // some images come with (0,0) coordinates. Skip such cases
                // also skip obviously wrong values (it happens)
                if (($lon != 0 && $lat != 0) &&
                    (abs($lat) < 90) &&
                    (abs($lon) < 180))
                {
                    $this->set('lon', $lon);
                    $this->set('lat', $lat);
                }
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
        if (count($langs))
        {
            $params['icult'] = implode('-', $langs);
        }
        if (count($ranges))
        {
        //    $params['areas'] = implode('-', $ranges);
        }
        if (count($activities))
        {
            $params['act'] = implode('-', $activities);
        }
        
        $categories = sfConfig::get('app_images_home_categories');
        $params['icat'] = implode('-', $categories);

        $criteria = Image::buildListCriteria($params);
        
        $sort = array('orderby_params' => array(),
                      'order_params' => array(),
                      'npp'      => $max_items
                     );
        
        $sub_query_result = self::browseId('Image', $sort, $criteria, array(), 1, $max_items);
        
        $nb_results = $sub_query_result['nb_results'];
        $ids = $sub_query_result['ids'];

        if ($nb_results == 0 || empty($ids))
        {
            return array();
        }
        
        $where_ids = 'm.id' . $sub_query_result['where'];
        
        $q = Doctrine_Query::create();
        $q->select('m.id, n.culture, n.name, m.filename')
          ->from('Image m')
          ->leftJoin('m.ImageI18n n')
          ->addWhere($where_ids, $ids)
          ->orderBy('m.id DESC');

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

    public static function buildImageListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'linked_id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'i';
            $mid = 'm.id';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'image_i18n';
        }
        else
        {
            $m = 'i';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'image';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'images'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'images', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'i', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Date', 'date_time::date', array('date', 'idate'), $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            else
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Date', $m . '.date_time::date', 'idate', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'iarnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'ii.search_name'), ($is_module ? array('inam', 'name') : 'inam'), array($join_id, $join_i18n), 'Image');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'i', 'activities'), 'iact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'ialt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'i', 'categories'), 'icat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.image_type', 'ityp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ii.culture', 'icult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'i', 'main_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_iarticle']))
            {
                $joins['join_image'] = true;
                if (!$is_module)
                {
                    $joins['post_image'] = true;
                }
            }
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
        }
        if (!empty($joins))
        {
            $joins['join_image'] = true;
        }
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        $criteria[2] += $joins;
        
        return null;
    }

    public static function buildListCriteria($params_list)
    {
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'images');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('inam'), array('image_i18n', 'join_image'));
        self::buildOrderCondition($joins_order, $orderby_list, array('odate'), array('outing', 'join_outing'));
        self::buildOrderCondition($joins_order, $orderby_list, array('oid'), array('post_outing', 'join_outing'));
        self::buildOrderCondition($joins, $orderby_list, array('oid', 'odate'), array('post_outing', 'join_outing'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'i');
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, true);
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'Join', '', 'join', '');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // document criteria
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lid.main_id', 'docs', 'doc');
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ldc.linked_id', 'dtags', 'dtag');

        // summit criteria
        $has_name = Summit::buildSummitListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // hut criteria
        $has_name = Hut::buildHutListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // site criteria
        $has_name = Site::buildSiteListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // user criteria
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'hm.user_id', 'users', 'user_id'); // TODO here we should restrict to initial uploader (ticket #333)
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lou.main_id', 'ousers', array('ouser_id', 'join_outing', 'post_outing'));

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }
    
    public static function buildMainPagerConditions(&$q, $criteria)
    {
    }
    
    public static function buildImagePagerConditions(&$q, &$joins, $is_module = false, $ltype = null, $from_users = false)
    {
        $join = 'image';
        if ($is_module)
        {
            $m = 'm';
            $main_join = $m . '.associations';
        }
        else
        {
            $m = 'li';
            $join_id = $join . '_id';
            
            if (!$from_users)
            {
                $main_join = $m . '.MainMainAssociation';
                
                if (isset($joins[$join_id]))
                {
                    self::joinOnMulti($q, $joins, $join_id, "m.LinkedAssociation $m", 5);
                    
                    if (isset($joins[$join_id . '_has']))
                    {
                        $q->addWhere($m . "1.type = '$ltype'");
                    }
                }
            }
            else
            {
                $m = $m . '1';
                $main_join = $m . '.MainAssociation';
                $q->leftJoin("m.history_metadata hm")
                  ->leftJoin("hm.versions $m")
                  ->addWhere("$m.version = 1");
                
                if (isset($joins[$join . '_id_has']))
                {
                    $q->leftJoin("$m.LinkedAssociation li2")
                      ->addWhere("li2.type = '$ltype'");
                }
            }
            
            if (   isset($joins['post_' . $join])
                || isset($joins[$join])
                || isset($joins[$join . '_idi18n'])
                || isset($joins[$join . '_i18n'])
            )
            {
                if (!$from_users)
                {
                    $q->leftJoin("m.LinkedAssociation $m");
                    
                    if (   isset($joins['post_' . $join])
                        || isset($joins[$join])
                        || isset($joins[$join . '_i18n'])
                    )
                    {
                        if ($ltype)
                        {
                            $q->addWhere($m . ".type = '$ltype'");
                        }
                    }
                }
                
                if (isset($joins[$join]))
                {
                    $q->leftJoin($m . '.' . 'Image i');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . 'ImageI18n ii');
        }
        
        if (isset($joins['join_iarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'i', false, $main_join, 'ci');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        $route_join = 'm.associations';
        $route_ltype = 'ri';
        $summit_join = 'm.associations';
        $summit_ltype = 'si';
        $hut_join = 'm.associations';
        $hut_ltype = 'hi';
        $parking_join = 'm.associations';
        $parking_ltype = 'pi';
        $site_join = 'm.associations';
        $site_ltype = 'ti';
        
        self::joinOnLinkedDocMultiRegions($q, $joins);

        if (   isset($joins['doc'])
            || isset($joins['dtag']))
        {
            $q->leftJoin('m.associations lid');
            if (isset($joins['dtag']))
            {
                $q->leftJoin('lid.LinkedLinkedAssociation ldc');
            }
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, true);
        }

        // join with outing tables only if needed 
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, false, 'm.associations', 'oi');
            
            $route_join = 'lo.MainAssociation';
            $route_ltype = 'ro';
            $summit_join = 'lr.MainAssociation';
            $summit_ltype = 'sr';
            $hut_join = 'lr.MainAssociation';
            $hut_ltype = 'hr';
            $parking_join = 'lr.MainAssociation';
            $parking_ltype = 'pr';
            $site_join = 'lo.MainAssociation';
            $site_ltype = 'to';
        
            if (isset($joins['ouser_id']))
            {
                $q->leftJoin($route_join . ' lou');
            }
            
            if (   isset($joins['join_summit'])
                || isset($joins['join_hut'])
                || isset($joins['join_parking'])
            )
            {
                $joins['join_route'] = true;
                $joins['post_route'] = true;
            }
        }

        // join with route tables only if needed 
        if (isset($joins['join_route']))
        {
            Route::buildRoutePagerConditions($q, $joins, false, false, $route_join, $route_ltype);
            
            $summit_join = 'lr.MainAssociation';
            $summit_ltype = 'sr';
            $hut_join = 'lr.MainAssociation';
            $hut_ltype = 'hr';
            $parking_join = 'lr.MainAssociation';
            $parking_ltype = 'pr';
        }

        // join with summit tables only if needed 
        if (isset($joins['join_summit']))
        {
            Summit::buildSummitPagerConditions($q, $joins, false, false, $summit_join, $summit_ltype);
        }
        
        // join with hut tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, false, false, $hut_join, $hut_ltype);
        }
        
        // join with parking tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, $parking_join, $parking_ltype);
        }

        // join with site tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, false, false, $site_join, $site_ltype);
        }
        
        // join with user tables only if needed 
        if (isset($joins['user_id']))
        {
            $q->leftJoin('m.versions v')
              ->leftJoin('v.history_metadata hm')
              ->addWhere('v.version = 1');
        }
        
        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }

    public static function getSortField($orderby, $mi = 'mi')
    {
        switch ($orderby)
        {
            case 'id':   return 'm.id';
            case 'inam': return $mi . '.search_name';
            case 'act':  return 'm.activities';
            case 'icat':  return 'm.categories';
            case 'auth': return 'm.author';
            case 'range': return 'gr.linked_id';
            case 'admin': return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley': return 'gv.linked_id';
            case 'date': return 'm.date_time';
            case 'odate': return array(array('o.date', 'o.id', 'm.date_time'), array(null, null, 'asc'));
            case 'oid': return array(array('lo.main_id', 'm.date_time'), array(null, 'asc'));
            case 'ityp': return 'm.image_type';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {
        if ($main_query)
        {
            $data_fields_list = array('m.filename', 'm.date_time', 'm.image_type', 'm.lon', 'm.lat', 'm.geom_wkt');
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        $orderby_fields = array();
        if (isset($sort['orderby_params']))
        {
            $orderby = $sort['orderby_params'];
            
            if (array_intersect($orderby, array('oid', 'odate')))
            {
                $orderby_fields = array('lo.type');
            }
        }
        
        return array_merge($base_fields_list, 
                           $data_fields_list,
                           $orderby_fields);
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

    public static function addAssociatedImages(&$docs, $type)
    {
        Document::addAssociatedDocuments($docs, $type, true,
                                         array('filename', 'image_type', 'date_time'),
                                         array('name'));
    }
}
