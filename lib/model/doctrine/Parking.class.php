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

    public static function addAssociatedParkings(&$docs, $type)
    {
        Document::addAssociatedDocuments($docs, $type, false,
                                         array('elevation', 'lowest_elevation', 'public_transportation_rating', 'public_transportation_types'),
                                         array('name'));
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

    public static function buildParkingListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id', $m = 'p')
    {
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'p';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m2 = $m;
            $join = 'join_parking';
            $join_id = $join . '_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'Id', $mid, 'parkings', 'join_parking_id', false, $params_list);
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'id', null, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Id', $mid, 'subparkings', 'join_subparking_id', false, $params_list);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'Around', $m2 . '.geom', 'parnd', $join, false, $params_list);
            
            $has_name = self::buildConditionItem($conditions, $values, 'String', array($mid, 'pi.search_name'), ($is_module ? array('pnam', 'name') : 'pnam'), array($join_id, 'join_parking_i18n'), false, $params_list, 'Parking');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'palt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.public_transportation_rating', 'tp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'public_transportation_types'), 'tpty', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'pi.culture', 'pcult', 'join_parking_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Id', 'lpc.linked_id', 'ptags', 'join_ptag_id', false, $params_list);
        }
        
        return null;
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'pcult', 'ract');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list, 'p');

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($conditions, $values, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // hut criteria
        $has_name = Hut::buildHutListCriteria($conditions, $values, $params_list, false, 'lh.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($conditions, $values, $params_list, false, 'lr.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // summit criteria
        $has_name = Summit::buildSummitListCriteria($conditions, $values, $params_list, false, 'ls.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // site criteria
        $has_name = Site::buildSiteListCriteria($conditions, $values, $params_list, false, 'lt.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
       
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($conditions, $values, $params_list, false, 'lo.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // article criteria
        $has_name = Article::buildArticleListCriteria($conditions, $values, $params_list, false, 'p', 'lc.linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($conditions, $values, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Parking', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);

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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('parkings'))
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
    
    public static function buildParkingPagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null, $p = 'p')
    {
        if ($is_module)
        {
            $m = 'm.';
            $linked = '';
            $linked2 = '';
            
            if (   isset($conditions['join_parking_id'])
                || isset($conditions['join_parking_id_has'])
            )
            {
                $q->leftJoin($m . 'associations lp');
                
                if (isset($conditions['join_parking_id_has']))
                {
                    $q->addWhere("lp.type = 'pp'");
                    unset($conditions['join_parking_id_has']);
                }
                if (isset($conditions['join_parking_id']))
                {
                    unset($conditions['join_parking_id']);
                }
            }
            
            if (   isset($conditions['join_subparking_id'])
                || isset($conditions['join_subparking_id_has'])
            )
            {
                $q->leftJoin($m . 'LinkedAssociation lpp');
                
                if (isset($conditions['join_subparking_id_has']))
                {
                    $q->addWhere("lpp.type = 'pp'");
                    unset($conditions['join_subparking_id_has']);
                }
                if (isset($conditions['join_subparking_id']))
                {
                    unset($conditions['join_subparking_id']);
                }
            }
        }
        else
        {
            $m = 'lp.';
            if ($is_linked)
            {
                $linked = 'Linked';
                $linked2 = '';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
            }
                
            $q->leftJoin($first_join . ' lp');
            
            if (!isset($conditions['join_parking_id']) || isset($conditions['join_parking_id_has']))
            {
                $q->addWhere($m . "type = '$ltype'");
                if (isset($conditions['join_parking_id_has']))
                {
                    unset($conditions['join_parking_id_has']);
                }
            }
            if (isset($conditions['join_parking_id']))
            {
                unset($conditions['join_parking_id']);
            }
            
            if (isset($conditions['join_parking']))
            {
                $q->leftJoin($m . $linked . 'Parking ' . $p);
                unset($conditions['join_parking']);
            }
        }

        if (isset($conditions['join_parking_i18n']))
        {
            $q->leftJoin($m . $linked . 'ParkingI18n pi');
            unset($conditions['join_parking_i18n']);
        }
        
        if (isset($conditions['join_ptag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation lpc");
            unset($conditions['join_ptag_id']);
            
            if (isset($conditions['join_ptag_id_has']))
            {
                $q->addWhere("lpc.type = 'pc'");
                unset($conditions['join_ptag_id_has']);
            }
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        // join with parking tables only if needed 
        if (   isset($conditions['join_parking_i18n'])
            || isset($conditions['join_parking_id'])
            || isset($conditions['join_subparking_id'])
            || isset($conditions['join_ptag_id'])
        )
        {
            Parking::buildParkingPagerConditions($q, $conditions, true);
        }
        
        // join with huts tables only if needed 
        if (   isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            Hut::buildHutPagerConditions($q, $conditions, false, true,'m.LinkedAssociation', 'ph');
        }

        // join with routes tables only if needed 
        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_rdoc_id'])
            || isset($conditions['join_rtag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbtag_id'])
            || isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
            || isset($conditions['join_outing_id'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
        )
        {
            Route::buildRoutePagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'pr');

            if (   isset($conditions['join_summit_id'])
                || isset($conditions['join_summit'])
                || isset($conditions['join_summit_i18n'])
                || isset($conditions['join_stag_id'])
                || isset($conditions['join_sbook_id'])
                || isset($conditions['join_sbtag_id'])
            )
            {
                Summit::buildSummitPagerConditions($q, $conditions, false, false, 'lr.MainAssociation', 'sr');
            }
            
            if (   isset($conditions['join_outing_id'])
                || isset($conditions['join_outing'])
                || isset($conditions['join_outing_i18n'])
                || isset($conditions['join_otag_id'])
            )
            {
                Outing::buildOutingPagerConditions($q, $conditions, false, true, 'lr.LinkedAssociation', 'ro');
            }
        }

        // join with site tables only if needed 
        if (   isset($conditions['join_site_id'])
            || isset($conditions['join_site'])
            || isset($conditions['join_site_i18n'])
            || isset($conditions['join_tbook_id'])
            || isset($conditions['join_ttag_id'])
            || isset($conditions['join_tbtag_id'])
        )
        {
            Site::buildSitePagerConditions($q, $conditions, false, false, 'm.LinkedAssociation', 'pt');
        }

        // join with article tables only if needed 
        if (   isset($conditions['join_article_id'])
            || isset($conditions['join_article'])
            || isset($conditions['join_article_i18n'])
        )
        {
            Article::buildArticlePagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'pc');
        }

        // join with image tables only if needed 
        if (   isset($conditions['join_image_id'])
            || isset($conditions['join_image'])
            || isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $conditions, false, 'pi');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $criteria);
        }
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
