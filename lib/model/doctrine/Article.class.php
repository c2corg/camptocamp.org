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

    public static function buildArticleListCriteria(&$criteria, &$params_list, $is_module = false, $prefix = '', $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'a';
            $midi18n = $mid;
            $join = null;
            $join_idi18n = null;
            $join_i18n = 'article_i18n';
        }
        else
        {
            $m = $prefix . 'c';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = $prefix . 'article';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $has_id = false;
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, 'id', null);
        }
        else // tags are detected here
        {
            $has_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, $prefix . 'tags', $prefix . 'tag');
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'activities'), 'act', $join);
            }
            $has_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, $prefix . 'ci.search_name'), ($is_module ? array('cnam', 'name') : $prefix . 'cnam'), array($join_idi18n, $join_i18n), 'Article');
            if ($has_name === 'no_result')
            {
                return $has_name;
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'activities'), $prefix . 'cact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'categories'), $prefix . 'ccat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m2 . '.article_type', $prefix . 'ctyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $prefix . 'ci.culture', $prefix . 'ccult', $join_i18n);
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = $criteria[0] + $conditions;
            $criteria[1] = $criteria[1] + $values;
        }
        if (!empty($joins))
        {
            $joins['join_' . $prefix . 'article'] = true;
            $criteria[2] = $criteria[2] + $joins;
        }
        
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
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'ccult');
        
        // orderby criteria
        $orderby = c2cTools::getRequestParameter('orderby');
        if (!empty($orderby))
        {
            $orderby = array('orderby' => $orderby);
            
            self::buildConditionItem($conditions, $values, $joins_order, $orderby, 'Order', 'cnam', 'orderby', array('article_i18n', 'join_article'));
        }
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'a');
        
        // article criteria
        $has_name = Article::buildArticleListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // linked document criteria
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lcd.main_id', 'cdocs', 'cdoc');

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

        // book criteria
        $has_name = Book::buildBookListCriteria($criteria, $params_list, false, 'c', 'main_id');
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
        
        // user criteria
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', array('u', 'main_id'), 'users', 'user_id');

        $criteria[0] = $criteria[0] + $conditions;
        $criteria[1] = $criteria[1] + $values;
        $criteria[2] = $criteria[2] + $joins;
        $criteria[3] = $criteria[3] + $joins_order;
        return $criteria;
    }
    
    public static function browse($sort, $criteria, $format = null)
    {
        $field_list = self::buildFieldsList();
        $pager = self::createPager('Article', $field_list, $sort);
        $q = $pager->getQuery();
    
        $all = false;
        if (isset($criteria[2]['all']))
        {
            $all = $criteria[2]['all'];
        }
        
        if (!$all && !empty($criteria[0]))
        {
            self::buildPagerConditions($q, $criteria);
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
    
    public static function buildArticlePagerConditions(&$q, $joins, $is_module = false, $prefix = '', $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = $prefix . 'article';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
        }
        else
        {
            $m = 'l' . $prefix . 'c';
            if ($is_linked)
            {
                $linked = 'Linked';
            }
            else
            {
                $linked = '';
            }
            $join_tag = $prefix . 'tag';
                
            if (isset($joins[$join_tag]))
            {
                self::joinOnMulti($q, $joins, $join_tag, $first_join . " $m", 5);
                
                if (isset($joins[$join_tag . '_has']))
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
                    $q->leftJoin($m . '.' . $linked . 'Article ' . $prefix . 'c');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'ArticleI18n ' . $prefix . 'ci');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
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
        
        self::joinOnLinkedDocMultiRegions($q, $joins);

        self::joinOnMulti($q, $joins, 'user_id', 'm.associations u', 4);

        if (isset($joins['cdoc']))
        {
            $q->leftJoin('m.associations lcd');
        }

        // join with article tables only if needed 
        if (isset($joins['join_article']))
        {
            Article::buildArticlePagerConditions($q, $joins, true);
        }
        
        // join with book tables only if needed 
        if (isset($joins['join_cbook']))
        {
            Book::buildBookPagerConditions($q, $joins, false, 'c', false, $main_join, 'bc');
        }

        // join with outing tables only if needed 
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, false, 'm.associations', 'oc');
            
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

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'ci');
        }
        
        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
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
