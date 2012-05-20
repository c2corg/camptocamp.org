<?php
/**
 * $Id: Article.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Article extends BaseArticle
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetCategories($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetCategories($value)
    {   
        return self::convertStringToArray($value);
    }

    /**
     * Retrieves a list of articles ordered by descending id (~date of creation).
     */
    public static function listLatest($max_items, $langs, $activities, $params = array())
    {
        $q = Doctrine_Query::create();
        $q->select('m.id, n.culture, n.name, n.abstract')
          ->from('Article m')
          ->leftJoin('m.ArticleI18n n')
          ->leftJoin('m.versions d ON m.id = d.document_id AND d.version = 1 AND n.culture = d.culture')
          ->addWhere('NOT (100 = ANY (a.categories))')
          ->addWhere('m.redirects_to IS NULL')
          ->orderBy('d.created_at DESC, m.id DESC')
          ->limit($max_items);

        self::filterOnActivities($q, $activities, 'm', 'a');
        self::filterOnLanguages($q, $langs, 'n');
        
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

    public static function buildArticleListCriteria(&$conditions, &$values, $params_list, $is_module = false, $prefix = '', $mid = 'm.id')
    {
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'a';
            $join = null;
            $join_id = null;
            $join_i18n = 'join_article_i18n';
        }
        else
        {
            $m = $prefix . 'c';
            $m2 = $m;
            $join = 'join_' . $prefix . 'article';
            $join_id = $join . '_id';
            $join_i18n = $join . '_i18n';
        }
        
        $has_id = false;
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, 'List', $mid, 'id', null, false, $params_list);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'activities'), 'act', $join, false, $params_list);
            }
            $has_name = self::buildConditionItem($conditions, $values, 'String', array($mid, $prefix . 'ci.search_name'), ($is_module ? array('cnam', 'name') : $prefix . 'cnam'), array($join_id, $join_i18n), false, $params_list, 'Article');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'activities'), $prefix . 'cact', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'categories'), $prefix . 'ccat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m2 . '.article_type', $prefix . 'ctyp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $prefix . 'ci.culture', $prefix . 'ccult', $join_i18n, false, $params_list);
        }
        
        return null;
    }

    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'ccult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list, 'a');
        
        // article criteria
        $has_name = Article::buildArticleListCriteria($conditions, $values, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // linked document criteria
        self::buildConditionItem($conditions, $values, 'List', 'd.main_id', 'cdocs', 'join_doc', false, $params_list);

        // summit criteria
        $has_name = Summit::buildSummitListCriteria($conditions, $values, $params_list, false, 'ls.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // hut criteria
        $has_name = Hut::buildHutListCriteria($conditions, $values, $params_list, false, 'lh.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // parking criteria
        $has_name = Parking::buildParkingListCriteria($conditions, $values, $params_list, false, 'lp.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($conditions, $values, $params_list, false, 'lr.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // site criteria
        $has_name = Site::buildSiteListCriteria($conditions, $values, $params_list, false, 'lt.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($conditions, $values, $params_list, false, 'lo.main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // book criteria
        $has_name = Book::buildBookListCriteria($conditions, $values, $params_list, false, 'c', 'lcb.main_id');
        self::buildConditionItem($conditions, $values, 'Id', 'lcb.main_id', 'books', 'join_cbook_id', false, $params_list);
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
        
        // user criteria
        self::buildConditionItem($conditions, $values, 'Multilist', array('u', 'main_id'), 'users', 'join_user_id', false, $params_list);

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }
    
    public static function browse($sort, $criteria, $format = null)
    {
        $field_list = self::buildFieldsList();
        $pager = self::createPager('Article', $field_list, $sort);
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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('articles'))
        {
            self::filterOnActivities($q);
            self::filterOnLanguages($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildArticlePagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        if ($is_module)
        {
            $m = 'm.';
            $linked = '';
        }
        else
        {
            $m = 'lc.';
            if ($is_linked)
            {
                $linked = 'Linked';
            }
            else
            {
                $linked = '';
            }
                
            $q->leftJoin($first_join . ' lc');
            
            if (!isset($conditions['join_article_id']) || isset($conditions['join_article_id_has']))
            {
                $q->addWhere($m . "type = '$ltype'");
                if (isset($conditions['join_article_id_has']))
                {
                    unset($conditions['join_article_id_has']);
                }
            }
            if (isset($conditions['join_article_id']))
            {
                unset($conditions['join_article_id']);
            }
            
            if (isset($conditions['join_article']))
            {
                $q->leftJoin($m . $linked . 'Article c');
                unset($conditions['join_article']);
            }
        }

        if (isset($conditions['join_article_i18n']))
        {
            $q->leftJoin($m . $linked . 'ArticleI18n ci');
            unset($conditions['join_article_i18n']);
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $route_join = 'm.associations';
        $route_ltype = 'rc';
        $summit_join = 'm.associations';
        $summit_ltype = 'sc';
        $hut_join = 'm.associations';
        $hut_ltype = 'hc';
        $parking_join = 'm.associations';
        $parking_ltype = 'pc';
        $site_join = 'm.associations';
        $site_ltype = 'tc';
        
        $conditions = self::joinOnLinkedDocMultiRegions($q, $conditions);

        $conditions = self::joinOnMulti($q, $conditions, 'join_user_id', 'm.associations u', 4);

        if (isset($conditions['join_doc']))
        {
            $q->leftJoin('m.associations d');
            unset($conditions['join_doc']);
        }

        // join with article tables only if needed 
        if (   isset($conditions['join_article_i18n'])
        )
        {
            Article::buildArticlePagerConditions($q, $conditions, true);
        }
        
        // join with book tables only if needed 
        if (   isset($conditions['join_cbook_id'])
            || isset($conditions['join_cbook'])
            || isset($conditions['join_cbook_i18n'])
        )
        {
            $q->leftJoin('m.associations lcb');
            
            if (!isset($conditions['join_cbook_id']) || isset($conditions['join_cbook_id_has']))
            {
                $q->addWhere("lcb.type = 'bc'");
                if (isset($conditions['join_cbook_id_has']))
                {
                    unset($conditions['join_cbook_id_has']);
                }
            }
            if (isset($conditions['join_cbook_id']))
            {
                unset($conditions['join_cbook_id']);
            }
            
            if (isset($conditions['join_cbook']))
            {
                $q->leftJoin('lcb.Book cb');
                unset($conditions['join_cbook']);
            }

            if (isset($conditions['join_cbook_i18n']))
            {
                $q->leftJoin('lcb.BookI18n cbi');
                unset($conditions['join_cbook_i18n']);
            }
        }

        // join with outings tables only if needed 
        if (   isset($conditions['join_outing_id'])
            || isset($conditions['join_outing_id_has'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
        )
        {
            Outing::buildOutingPagerConditions($q, $conditions, false, false, 'm.associations', 'oc');
            
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
        
            if (isset($conditions['join_ouser_id']))
            {
                $q->leftJoin($route_join . ' lou');
                unset($conditions['join_ouser_id']);
            }
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
            Route::buildRoutePagerConditions($q, $conditions, false, false, $route_join, $route_ltype);
            
            $summit_join = 'lr.MainAssociation';
            $summit_ltype = 'sr';
            $hut_join = 'lr.MainAssociation';
            $hut_ltype = 'hr';
            $parking_join = 'lr.MainAssociation';
            $parking_ltype = 'pr';
        }

        if (   isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
        )
        {
            Summit::buildSummitPagerConditions($q, $conditions, false, false, $summit_join, $summit_ltype);
        }
        
        if (   isset($conditions['join_hut_id'])
            || isset($conditions['join_hut'])
            || isset($conditions['join_hut_i18n'])
            || isset($conditions['join_hbook_id'])
            || isset($conditions['join_htag_id'])
            || isset($conditions['join_hbtag_id'])
        )
        {
            Hut::buildHutPagerConditions($q, $conditions, false, false, $hut_join, $hut_ltype);
        }
        
        if (   isset($conditions['join_parking_id'])
            || isset($conditions['join_parking'])
            || isset($conditions['join_parking_i18n'])
            || isset($conditions['join_ptag_id'])
        )
        {
            Parking::buildParkingPagerConditions($q, $conditions, false, false, $parking_join, $parking_ltype);
        }

        if (   isset($conditions['join_site_id'])
            || isset($conditions['join_site'])
            || isset($conditions['join_site_i18n'])
            || isset($conditions['join_tbook_id'])
            || isset($conditions['join_ttag_id'])
            || isset($conditions['join_tbtag_id'])
        )
        {
            Site::buildSitePagerConditions($q, $conditions, false, false, $site_join, $site_ltype);
        }
        
        $q->addWhere(implode(' AND ', $conditions), $criteria);
    }

    protected static function buildFieldsList()
    {   
        return array_merge(parent::buildFieldsList(), 
                           array('m.categories', 'm.activities', 'm.article_type'));
    } 

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnI18n($q, $model);
        self::filterOnActivities($q);
        self::filterOnLanguages($q);
    }
}
