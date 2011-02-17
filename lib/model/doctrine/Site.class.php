<?php
/**
 * $Id: Site.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Site extends BaseSite
{
    public static function getAssociatedSitesData($associated_docs)
    {
        $sites = Document::fetchAdditionalFieldsFor(
                                            array_filter($associated_docs, array('c2cTools', 'is_site')),
                                            'Site',
                                            array('site_types'));

        return c2cTools::sortArrayByName($sites);
    }

    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetV4_type($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetClimbing_styles($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetClimbing_styles($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetRock_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetRock_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetSite_types($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetSite_types($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetFacings($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetFacings($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetBest_periods($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetBest_periods($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetRoutes_quantity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMin_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMean_height($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMax_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMin_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMean_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetEquipment_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetChildren_proof($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetRain_proof($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildSiteListCriteria(&$conditions, &$values, $params_list, $is_module = false, $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $m2 = 's';
            $join = null;
            $join_id = null;
        }
        else
        {
            $m = 's';
            $m2 = $m;
            $join = 'join_site';
            $join_id = 'join_site_id';
        }
        
        $has_id = self::buildConditionItem($conditions, $values, 'Id', $mid, 'sites', $join_id, false, $params_list);
        if ($is_module)
        {
            $has_id = $has_id || self::buildConditionItem($conditions, $values, 'List', $mid, 'id', $join_id, false, $params_list);
        }
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', 'ti.search_name', ($is_module ? array('tnam', 'name') : 'tnam'), 'join_site_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.elevation', 'talt', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'site_types'), 'ttyp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'climbing_styles'), 'tcsty', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.equipment_rating', 'tprat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.routes_quantity', 'rqua', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.mean_height', 'mhei', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Compare', $m . '.mean_rating', 'mrat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'facings'), 'tfac', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'rock_types'), 'trock', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.children_proof', 'chil', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.rain_proof', 'rain', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'ti.culture', 'tcult', 'join_site_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'Id', 'ltb.main_id', 'tbooks', 'join_tbook_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'ltc.linked_id', 'ttags', 'join_ttag_id', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'ltbc.linked_id', 'tbtags', 'join_tbtag_id', false, $params_list);
        }
    }

    public static function buildListCriteria($params_list)
    {   
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'tcult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list, 's');

        // site criteria
        Site::buildSiteListCriteria(&$conditions, &$values, $params_list, true);

        // summit criteria
        Summit::buildSummitListCriteria(&$conditions, &$values, $params_list, false, 'ls.main_id');

        // hut criteria
        Hut::buildHutListCriteria(&$conditions, &$values, $params_list, false, 'lh.main_id');

        // parking criteria
        Parking::buildParkingListCriteria(&$conditions, &$values, $params_list, false, 'lp.main_id');
       
        // outing criteria
        Outing::buildOutingListCriteria(&$conditions, &$values, $params_list, false, 'lo.linked_id');
        
        // user criteria
        User::buildUserListCriteria(&$conditions, &$values, $params_list, false, 'lu.main_id');

        // book criteria
        Book::buildBookListCriteria(&$conditions, &$values, $params_list, false, 't');
        self::buildConditionItem($conditions, $values, 'Id', 'ltb.main_id', 'books', 'join_tbook_id', false, $params_list);
        
        // image criteria
        Image::buildImageListCriteria(&$conditions, &$values, $params_list, false);

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Site', self::buildFieldsList(), $sort);
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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn(false, true, false))
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildSitePagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        if ($is_module)
        {
            $m = 'm.';
            $linked = '';
            $linked2 = '';
            $main = $m . 'associations';
        }
        else
        {
            $m = 'lt.';
            if ($is_linked)
            {
                $linked = 'Linked';
                $linked2 = '';
                $main = $m . 'MainMainAssociation';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
                $main = $m . 'MainAssociation';
            }
            
            $q->leftJoin($first_join . ' lt');
            
            if (!isset($conditions['join_site_id']) || isset($conditions['join_site_id_has']))
            {
                $q->addWhere($m . "type = '$ltype'");
                if (isset($conditions['join_site_id_has']))
                {
                    unset($conditions['join_site_id_has']);
                }
            }
            if (isset($conditions['join_site_id']))
            {
                unset($conditions['join_site_id']);
                
                return;
            }
            
            if (isset($conditions['join_site']))
            {
                $q->leftJoin($m . $linked . 'Site s');
                unset($conditions['join_site']);
            }
        }
        
        if (isset($conditions['join_site_i18n']))
        {
            $q->leftJoin($m . $linked . 'SiteI18n ti');
            unset($conditions['join_site_i18n']);
        }
        
        if (isset($conditions['join_ttag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation ltc");
            unset($conditions['join_ttag_id']);
        }
        
        if (   isset($conditions['join_tbook_id'])
            || isset($conditions['join_tbtag_id'])
            || isset($conditions['join_tbook'])
            || isset($conditions['join_tbook_i18n'])
        )
        {
            $q->leftJoin($main . " ltb");
            
            if (!isset($conditions['join_tbook_id']) || isset($conditions['join_tbook_id_has']))
            {
                $q->addWhere("ltb.type = 'bt'");
                if (isset($conditions['join_tbook_id_has']))
                {
                    unset($conditions['join_tbook_id_has']);
                }
            }
            if (isset($conditions['join_tbook_id']))
            {
                unset($conditions['join_tbook_id']);
            }
            if (isset($conditions['join_tbtag_id']))
            {
                $q->leftJoin("ltb.LinkedLinkedAssociation ltbc");
                unset($conditions['join_tbtag_id']);
            }
            
            if (isset($conditions['join_tbook']))
            {
                $q->leftJoin('ltb.Book tb');
                unset($conditions['join_tbook']);
            }

            if (isset($conditions['join_tbook_i18n']))
            {
                $q->leftJoin('ltb.BookI18n tbi');
                unset($conditions['join_tbook_i18n']);
            }
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);

        if (   isset($conditions['join_site_i18n'])
            || isset($conditions['join_tbook_id'])
            || isset($conditions['join_tbook'])
            || isset($conditions['join_tbook_i18n'])
            || isset($conditions['join_ttag_id'])
            || isset($conditions['join_tbtag_id'])
        )
        {
            Site::buildSitePagerConditions($q, $conditions, true);
        }

        if (   isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            Summit::buildSummitPagerConditions($q, $conditions, false, false, 'm.associations', 'st');
        }
        
        // join with huts tables only if needed 
        if (   isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            Hut::buildHutPagerConditions($q, $conditions, false, false, 'm.associations', 'ht');
        }
        
        // join with parkings tables only if needed 
        if (   isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
        )
        {
            Parking::buildParkingPagerConditions($q, $conditions, false, false, 'm.associations', 'pt');
        }
        
        // join with outings tables only if needed 
        if (   isset($conditions['join_outing_id'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
            || isset($conditions['join_user_id'])
            || isset($conditions['join_user'])
            || isset($conditions['join_user_i18n'])
            || isset($conditions['join_user_pd'])
            || isset($conditions['join_utag_id'])
        )
        {
            Outing::buildOutingPagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'to');
            
            if (   isset($conditions['join_user_id'])
                || isset($conditions['join_user'])
                || isset($conditions['join_user_i18n'])
                || isset($conditions['join_user_pd'])
                || isset($conditions['join_utag_id'])
            )
            {
                User::buildUserPagerConditions($q, $conditions, false, false, 'lo.MainMainAssociation', 'uo');
            }
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
                           array('m.routes_quantity', 'm.elevation',
                                 'm.rock_types', 'm.site_types', 'm.lon', 'm.lat'));
    }

    public static function listFromRegion($region_id, $buffer, $table = 'sites', $where = '')
    {
        return parent::listFromRegion($region_id, $buffer, $table, $where);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }

    public static function getAssociatedBooksData($associated_docs)
    {
         $books = Document::fetchAdditionalFieldsFor(
                      array_filter($associated_docs, array('c2cTools', 'is_book')),
                      'Book',
                      array('author'));

        return $books;
    }
}
