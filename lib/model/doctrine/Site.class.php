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
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetMax_height($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetMin_height($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetMean_height($value)
    {
        return self::returnNaturalIntOrNull($value);
    }

    public static function filterSetElevation($value)
    {
        return self::returnNaturalIntOrNull($value);
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

    public static function buildSiteListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 's';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'site_i18n';
        }
        else
        {
            $m = 't';
            $m2 = 's';
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'site';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, 'id', $join_id);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', 'lt.main_id', 'sites', 'site_id');
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Id', 'ltt.linked_id', 'subsites', 'subsite_id');
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'sites', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'tarnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'ti.search_name'), ($is_module ? array('tnam', 'name') : 'tnam'), array($join_idi18n, $join_i18n), 'Site');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'talt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'site_types'), 'ttyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'climbing_styles'), 'tcsty', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.equipment_rating', 'tprat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.routes_quantity', 'rqua', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.mean_height', 'tmhei', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.mean_rating', 'tmrat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'facings'), 'tfac', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'rock_types'), 'trock', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.children_proof', 'chil', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.rain_proof', 'rain', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ti.culture', 'tcult', $join_i18n);
            
            // book criteria
            $nb_name = Book::buildBookListCriteria($criteria, $params_list, false, 't', 'main_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 't', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (   isset($criteria[2]['join_tbook'])
                || isset($criteria[2]['join_tarticle'])
            )
            {
                $joins['join_site'] = true;
                if (!$is_module)
                {
                    $joins['post_site'] = true;
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
            $joins['join_site'] = true;
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'sites');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('tnam'), array('site_i18n', 'join_site'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 's');

        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // site criteria
        $has_name = Site::buildSiteListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

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
       
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // user criteria
        $has_name = User::buildUserListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // xreport criteria
        $has_name = Xreport::buildXreportListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }

    public static function buildMainPagerConditions(&$q, $criteria)
    {
        self::joinOnRegions($q);
    }
    
    public static function buildSitePagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'site';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
            
            if (isset($joins['site_id']))
            {
                $q->leftJoin($main_join . ' lt');
                
                if (isset($joins['site_id_has']))
                {
                    $q->addWhere("lt.type = 'tt'");
                }
            }
            
            if (isset($joins['subsite_id']))
            {
                $q->leftJoin($linked_join . ' ltt');
                
                if (isset($joins['subsite_id_has']))
                {
                    $q->addWhere("ltt.type = 'tt'");
                }
            }
        }
        else
        {
            $m = 'lt';
            if ($is_linked)
            {
                $linked = 'Linked';
                $main_join = $m . 'MainMainAssociation';
                $linked_join = $m . '.LinkedAssociation';
            }
            else
            {
                $linked = '';
                $main_join = $m . 'MainAssociation';
                $linked_join = $m . '.LinkedLinkedAssociation';
            }
            $join_id = $join . '_id';
            
            if (isset($joins[$join_id]))
            {
                self::joinOnMulti($q, $joins, $join_id, $first_join . " $m", 5);
                
                if (isset($joins[$join_id . '_has']))
                {
                    $q->addWhere($m . "1.type = '$ltype'");
                }
            }
            
            if (   isset($joins['post_' . $join])
                || isset($joins[$join])
                || isset($joins[$join . '_idi18n'])
                || isset($joins[$join . '_i18n'])
            )
            {
                $q->leftJoin($first_join . " $m");
                
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
                
                if (isset($joins[$join]))
                {
                    $q->leftJoin($m . '.' . $linked . 'Site t');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'SiteI18n ti');
        }
        if (isset($joins['join_tbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 't', false, $main_join, 'bt');
        }
        
        
        if (isset($joins['join_tarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 't', false, $linked_join, 'tc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);

        // join with site / book / article tables only if needed 
        if (isset($joins['join_site']))
        {
            Site::buildSitePagerConditions($q, $joins, true);
        }

        // join with summit tables only if needed 
        if (isset($joins['join_summit']))
        {
            Summit::buildSummitPagerConditions($q, $joins, false, false, 'm.associations', 'st');
        }
        
        // join with hut tables only if needed 
        if (isset($joins['join_hut']))
        {
            Hut::buildHutPagerConditions($q, $joins, false, false, 'm.associations', 'ht');
        }
        
        // join with parking tables only if needed 
        if (isset($joins['join_parking']))
        {
            Parking::buildParkingPagerConditions($q, $joins, false, false, 'm.associations', 'pt');
        }
        
        // join with outing tables only if needed 
        if (isset($joins['join_user']))
        {
            $joins['join_outing'] = true;
            $joins['post_outing'] = true;
        }

        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'to');
            
            if (isset($joins['join_user']))
            {
                User::buildUserPagerConditions($q, $joins, false, false, 'lo.MainMainAssociation', 'uo');
            }
        }

        // join with image tables only if needed 
        if (   isset($joins['join_image_id'])
            || isset($joins['join_image'])
            || isset($joins['join_image_i18n'])
            || isset($joins['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'pi');
        }

        // join with xreport tables only if needed 
        if (isset($joins['join_xreport']))
        {
            Xreport::buildXreportPagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'tx');
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
            case 'tnam': return $mi . '.search_name';
            case 'talt': return 'm.elevation';
            case 'rqua': return 'm.routes_quantity';
            case 'ttyp': return 'm.site_types';
            case 'mhei': return 'm.mean_height';
            case 'mrat': return 'm.mean_rating';
            case 'tprat': return 'm.equipment_rating';
            case 'trock': return 'm.rock_types';
            case 'range': return 'gr.linked_id';
            case 'admin': return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley': return 'gv.linked_id';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.routes_quantity', 'm.elevation',
                                 'm.rock_types', 'm.site_types', 'm.lon', 'm.lat');
            $data_fields_list = array_merge($data_fields_list,
                                            parent::buildGeoFieldsList());
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
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
